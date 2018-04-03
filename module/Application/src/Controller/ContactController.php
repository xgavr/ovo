<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Contact;
use Application\Entity\Client;
use Application\Entity\Supplier;
use Application\Entity\Phone;
use Application\Entity\Email;
use User\Entity\User;
use Application\Form\ContactForm;
use Application\Form\PhoneForm;
use Application\Form\EmailForm;
use Zend\View\Model\JsonModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class ContactController extends AbstractActionController
{
    
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Менеджер товаров.
     * @var Application\Service\ContactManager 
     */
    private $contactManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $contactManager) 
    {
        $this->entityManager = $entityManager;
        $this->contactManager = $contactManager;
    }    
    
    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        
        $query = $this->entityManager->getRepository(Contact::class)
                    ->findAllContact();
                
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'contact' => $paginator,
            'contactManager' => $this->contactManager
        ]);  
    }
    
    public function addAction() 
    {     
        // Создаем форму.
        $form = new ContactForm($this->entityManager);
        
        // Проверяем, является ли пост POST-запросом.
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            // Заполняем форму данными.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Получаем валидированные данные формы.
                $data = $form->getData();
                
                // Используем менеджер contact для добавления нового good в базу данных.                
                $this->contactManager->addNewContact($data);
                
                // Перенаправляем пользователя на страницу "contact".
                return $this->redirect()->toRoute('contact', []);
            }
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form
        ]);
    }   
    
   public function editAction()
   {

        // Получаем ID tax.    
        $contactId = $this->params()->fromRoute('id', -1);
        
        // Находим существующий contact в базе данных.    
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);  
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(401);
            return;                        
        } 
        
        $client = $supplier = $user = null;
        if ($contact->getClient()){
            $client = $this->entityManager->getRepository(Client::class)
                    ->findOneById($contact->getClient()->getId());
        }
        if ($contact->getSupplier()){
            $supplier = $this->entityManager->getRepository(Supplier::class)
                    ->findOneById($contact->getSupplier()->getId());
        }
        if ($contact->getUser()){
            $user = $this->entityManager->getRepository(User::class)
                    ->findOneById($contact->getUser()->getId());
        }

        // Создаем форму.
        $form = new ContactForm($this->entityManager, $user);
    
            	
        // Проверяем, является ли пост POST-запросом.
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            // Заполняем форму данными.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Получаем валидированные данные формы.
                $data = $form->getData();
                
                // Используем менеджер постов, чтобы добавить новый пост в базу данных.                
                $this->contactManager->updateContact($contact, $data);
                
                // Перенаправляем пользователя на страницу "contact".
                if ($client){
                    return $this->redirect()->toRoute('client', ['action' => 'view', 'id' => $client->getId()]);
                } elseif ($supplier){
                    return $this->redirect()->toRoute('supplier', ['action' => 'view', 'id' => $supplier->getId()]);
                } elseif ($user){
                    return $this->redirect()->toRoute('user', ['action' => 'view', 'id' => $user->getId()]);
                } else {
                    return $this->redirect()->toRoute('contact', []);
                }    
            }
        } else {
            $data = [
               'name' => $contact->getName(),
               'description' => $contact->getDescription(),
               'status' => $contact->getStatus(),
            ];
            
            if ($contact->getEmail()){
                $data['email'] = $contact->getEmail()->getName();
            }
            
            if ($contact->getPhone()){
                $data['phone'] = $contact->getPhone()->getName();
            }
            
            $form->setData($data);
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form,
            'contact' => $contact,
            'client' => $client,
            'supplier'  => $supplier,
            'user' => $user,
        ]);  
    }    
    
    public function deleteAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->removeContact($contact);
        
        // Перенаправляем пользователя на страницу "rb/tax".
        return $this->redirect()->toRoute('contact', []);
    }    

    public function viewAction() 
    {       
        $contactId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($contactId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax contact ID
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        // Render the view template.
        return new ViewModel([
            'contact' => $contact,
        ]);
    }      
    
    public function phoneFormAction()
    {
        $contactId = (int)$this->params()->fromRoute('id', -1);
        
        if ($contactId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $phoneId = (int)$this->params()->fromQuery('phone', -1);
        
        // Validate input parameter
        if ($phoneId>0) {
            $phone = $this->entityManager->getRepository(Phone::class)
                    ->findOneById($phoneId);
        } else {
            $phone = null;
        }        
        $form = new PhoneForm($this->entityManager, $phone);

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($phone){
                    $this->contactManager->updatePhone($phone, ['phone' => $data['name'], 'comment' => $data['comment']]);                    
                } else {
                    $this->contactManager->addPhone($contact, ['phone' => $data['name'], 'comment' => $data['comment']], true);
                }    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            if ($phone){
                $data = [
                    'name' => $phone->getName(),  
                    'comment' => $phone->getComment(),  
                ];
                $form->setData($data);
            }  
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'contact' => $contact,
            'phone' => $phone,
        ]);                
        
    }
    
    public function deletePhoneFormAction()
    {
        $phoneId = $this->params()->fromRoute('id', -1);
        
        $phone = $this->entityManager->getRepository(Phone::class)
                ->findOneById($phoneId);
        
        if ($phone == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->removePhone($phone);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }
    
    public function emailFormAction()
    {
        $contactId = (int)$this->params()->fromRoute('id', -1);
        
        if ($contactId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $emailId = (int)$this->params()->fromQuery('email', -1);
        
        // Validate input parameter
        if ($emailId>0) {
            $email = $this->entityManager->getRepository(Email::class)
                    ->findOneById($emailId);
        } else {
            $email = null;
        }        
        $form = new EmailForm($this->entityManager, $email);

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($email){
                    $this->contactManager->updateEmail($email, ['email' => $data['name']]);                    
                } else {
                    $this->contactManager->addEmail($contact, $data['name'], true);
                }    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            if ($email){
                $data = [
                    'name' => $email->getName(),  
                ];
                $form->setData($data);
            }  
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'contact' => $contact,
            'email' => $email,
        ]);                
        
    }

    public function deleteEmailFormAction()
    {
        $emailId = $this->params()->fromRoute('id', -1);
        
        $email = $this->entityManager->getRepository(Email::class)
                ->findOneById($emailId);
        
        if ($email == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->removeEmail($email);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }
    
    public function telegrammFormAction()
    {
        $contactId = (int)$this->params()->fromRoute('id', -1);
        
        if ($contactId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $form = new ContactForm();

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $data['name'] = $contact->getName();
            $data['status'] = $contact->getStatus();
            $form->setData($data);

            if ($form->isValid()) {

                $this->contactManager->updateMessengers($contact, $data);                    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            $form->setData(['telegramm' => $contact->getTelegramm()]);
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'contact' => $contact,
        ]);                
        
    }

    public function deleteTelegrammFormAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->updateMessengers($contact, ['telegramm' => '']);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }

    public function addressFormAction()
    {
        $contactId = (int)$this->params()->fromRoute('id', -1);
        
        if ($contactId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $form = new ContactForm();

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $data['name'] = $contact->getName();
            $data['status'] = $contact->getStatus();
            $form->setData($data);

            if ($form->isValid()) {

                $this->contactManager->updateAddress($contact, $data);                    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            $form->setData(['address' => $contact->getAddress(), 'addressSms' => $contact->getAddressSms()]);
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'contact' => $contact,
        ]);                
        
    }

    public function deleteAddressFormAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->updateAddress($contact, ['address' => '', 'addressSms' => $contact->getAddressSms()]);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }

    public function deleteAddressSmsFormAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->updateAddress($contact, ['addressSms' => '', 'address' => $contact->getAddress()]);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }
}
