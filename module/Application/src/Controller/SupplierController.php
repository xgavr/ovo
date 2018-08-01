<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Supplier;
use Application\Entity\Contact;
use Application\Form\SupplierForm;
use Application\Form\ContactForm;
use Application\Form\UploadPriceForm;
use Zend\View\Model\JsonModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class SupplierController extends AbstractActionController
{
    
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Менеджер.
     * @var Application\Service\SupplierManager 
     */
    private $supplierManager;    
    
    /**
     * Менеджер.
     * @var Application\Service\ContactManager 
     */
    private $contactManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $supplierManager, $contactManager) 
    {
        $this->entityManager = $entityManager;
        $this->supplierManager = $supplierManager;
        $this->contactManager = $contactManager;
    }    
    
    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        
        $query = $this->entityManager->getRepository(Supplier::class)
                    ->findAllSupplier();
                
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'supplier' => $paginator,
            'supplierManager' => $this->supplierManager
        ]);  
    }
    
    public function addAction() 
    {     
        // Создаем форму.
        $form = new SupplierForm($this->entityManager);
        
        // Проверяем, является ли пост POST-запросом.
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            // Заполняем форму данными.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Получаем валидированные данные формы.
                $data = $form->getData();
                
                // Используем менеджер supplier для добавления нового good в базу данных.                
                $this->supplierManager->addNewSupplier($data);
                
                // Перенаправляем пользователя на страницу "supplier".
                return $this->redirect()->toRoute('supplier', []);
            }
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form
        ]);
    }   
    
   public function editAction()
   {
        // Создаем форму.
        $form = new SupplierForm($this->entityManager);
    
        // Получаем ID tax.    
        $supplierId = $this->params()->fromRoute('id', -1);
    
        // Находим существующий supplier в базе данных.    
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);  
        	
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(401);
            return;                        
        } 
        
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
                $this->supplierManager->updateSupplier($supplier, $data);
                
                // Перенаправляем пользователя на страницу "supplier".
                return $this->redirect()->toRoute('supplier', []);
            }
        } else {
            $data = [
               'name' => $supplier->getName(),
               'address' => $supplier->getAddress(),
               'info' => $supplier->getInfo(),
               'status' => $supplier->getStatus(),
               'contract' => $supplier->getContract(),
            ];
            
            $form->setData($data);
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form,
            'supplier' => $supplier
        ]);  
    }    
    
   public function editFormAction()
   {
        // Создаем форму.
        $form = new SupplierForm($this->entityManager);
    
        // Получаем ID tax.    
        $supplierId = $this->params()->fromRoute('id', -1);
    
        // Находим существующий пост в базе данных.    
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);  
        	
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(401);
            return;                        
        } 
        
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
                $this->supplierManager->updateSupplier($supplier, $data);

                return new JsonModel(
                   ['ok']
                );           
                
            }
        } else {
            $data = [
               'name' => $supplier->getName(),
               'status' => $supplier->getStatus(),
               'contract' => $supplier->getContract(),
            ];
            
            $form->setData($data);
        }
        
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'supplier' => $supplier,
        ]);                

    }    
    
    public function deleteAction()
    {
        $supplierId = $this->params()->fromRoute('id', -1);
        
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);        
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->supplierManager->removeSupplier($supplier);
        
        // Перенаправляем пользователя на страницу "rb/tax".
        return $this->redirect()->toRoute('supplier', []);
    }    

    public function deleteContactAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $supplierId = $contact->getSupplier()->getId();
        
        $this->contactManager->removeContact($contact);
        
        // Перенаправляем пользователя на страницу "supplier/view".
        return $this->redirect()->toRoute('supplier', ['action' => 'view', 'id' => $supplierId]);
    }    

    public function viewAction() 
    {       
        $supplierId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($supplierId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax supplier ID
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);
        
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $forLegals = $supplier->getLegalContacts();

        if (!count($forLegals)){
            $data['full_name'] = $data['name'] = $supplier->getName();
            $data['status'] = Contact::STATUS_LEGAL;
            $this->contactManager->addNewContact($supplier, $data);
        }
        
        $raws = $this->entityManager->getRepository(\Application\Entity\Raw::class)
            ->findAllRaw(null, $supplier)
            ->getResult();        
                
        // Render the view template.
        return new ViewModel([
            'supplier' => $supplier,
            'legalContact' => $supplier->getLegalContact(),
            'supplierManager' => $this->supplierManager,
            'lastPrice' => $this->supplierManager->getLastPriceFile($supplier),
            'arxPrice' => $this->supplierManager->getArxPriceFile($supplier),
            'raws' => $raws,
        ]);
    }    
    
    public function contactFormAction()
    {
        $supplierId = (int)$this->params()->fromRoute('id', -1);
        
        if ($supplierId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);
        
        $contactId = (int)$this->params()->fromQuery('contact', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        $form = new ContactForm($this->entityManager);

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($contact){
                    $this->contactManager->updateContact($contact, $data);
                } else {
                    $this->contactManager->addNewContact($supplier, $data);
                }    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            if ($contact){
                $form->setData([
                    'name' => $contact->getName(), 
                    'description' => $contact->getDescription(),
                    'status' => $contact->getStatus(),
                ]);
            }    
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'supplier' => $supplier,
            'contact' => $contact,
        ]);                        
    }
    
    
    public function uploadPriceAction()
    {
        $supplierId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($supplierId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax supplier ID
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);
        
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $form = new UploadPriceForm($this->supplierManager->getPriceFolder($supplier));

        if($this->getRequest()->isPost()) {
            
            $data = array_merge_recursive(
                $this->params()->fromPost(),
                $this->params()->fromFiles()
            );            
            
//            var_dump($data); exit;
            // Заполняем форму данными.
            $form->setData($data);
            if($form->isValid()) {
                                
                // Получаем валадированные данные формы.
                $data = $form->getData();
              
                // Снова перенаправляем пользователя на страницу "view".
                return $this->redirect()->toRoute('raw', ['action'=>'index']);
            } else {
                var_dump($form->getMessages());
            }
            
        }
        
        return new ViewModel([
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }
    
    public function managerFormAction()
    {
        $supplierId = (int)$this->params()->fromRoute('id', -1);
        
        if ($supplierId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $supplier = $this->entityManager->getRepository(Supplier::class)
                ->findOneById($supplierId);
        
        if ($supplier == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $managerId = (int)$this->params()->fromQuery('manager', -1);
        
        // Validate input parameter
        if ($managerId>0) {
            $manager = $this->entityManager->getRepository(Contact::class)
                    ->findOneById($managerId);
        } else {
            $manager = null;
        }
        
        $form = new ContactForm();

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($manager){
                    $this->contactManager->updateContact($manager, $data);                    
                } else{
                    $this->contactManager->addNewContact($supplier, $data);
                }    
                
                return new JsonModel(
                   ['ok']
                );           
            }
        } else {
            if ($manager){
                $data = [
                    'name' => $manager->getName(),  
                    'description' => $manager->getDescription(),  
                    'status' => $manager->getStatus(),  
                ];
                $form->setData($data);
            }    
        }        
        
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'manager' => $manager,
            'supplier' => $supplier,
        ]);                
    }
    
    public function deleteManagerAction()
    {
        $contactId = (int) $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
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
        
        $supplier = $contact->getSupplier();

        $this->contactManager->removeContact($contact);
        
        // Перенаправляем пользователя на страницу "legal".
        return $this->redirect()->toRoute('supplier', ['action' => 'view', 'id' => $supplier->getId()]);
    }
    
    public function deleteManagerFormAction()
    {
        $contactId = $this->params()->fromRoute('id', -1);
        
        $contact = $this->entityManager->getRepository(Contact::class)
                ->findOneById($contactId);
        
        if ($contact == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->contactManager->removeContact($contact);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }    
    
    
}
