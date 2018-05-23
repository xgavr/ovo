<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Goods;
use Application\Entity\GoodsGroup;
use Application\Entity\Producer;
use Application\Entity\Supplier;
use Application\Form\GoodsForm;
use Application\Form\GoodsGroupForm;
use Application\Form\GoodSettingsForm;
use Application\Filter\MorphyFilter;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class GoodsController extends AbstractActionController
{
    
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Менеджер товаров.
     * @var Application\Service\GoodsManager 
     */
    private $goodsManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $goodsManager) 
    {
        $this->entityManager = $entityManager;
        $this->goodsManager = $goodsManager;

    }    
    
    public function settingsAction()
    {
        $form = new GoodSettingsForm($this->entityManager);
    
        $settings = $this->goodsManager->getSettings();
        
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
                $this->goodsManager->setSettings($data);
                
                // Перенаправляем пользователя на страницу "goods".
                return $this->redirect()->toRoute('goods', []);
            }
        } else {
            $form->setData($settings);
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form,
        ]);  
        
    }
    
    public function indexAction()
    {
        $producers = $this->entityManager->getRepository(Producer::class)
                ->findAllActiveProducer()->getResult();
        
        $groups = $this->entityManager->getRepository(Goods::class)
                ->findAllActiveGroup()->getResult();
        
        $suppliers = $this->entityManager->getRepository(Supplier::class)
                ->findAllActiveSupplier()->getResult();
        
        return new ViewModel([
            'producers' => $producers,
            'groups' => $groups,
            'suppliers' => $suppliers,
        ]);          
    }
    
    public function indexContentAction()
    {
        
        $q = $this->params()->fromQuery('search', '');
        $producer = $this->params()->fromQuery('producer');
        $group = $this->params()->fromQuery('group');
        $supplier = $this->params()->fromQuery('supplier');
        $offset = $this->params()->fromQuery('offset');
        $limit = $this->params()->fromQuery('limit');
        
        $params = [
            'search' => $q,
            'producer' => $producer,
            'group' => $group,
            'supplier' => $supplier,
        ];
        
        $query = $this->entityManager->getRepository(Goods::class)
                    ->paramsSearch($params);            
        
        $total = count($query->getResult(2));
        
        if ($offset) $query->setFirstResult( $offset );
        if ($limit) $query->setMaxResults( $limit );
        
        $result = $query->getResult(2);
        
        return new JsonModel([
            'total' => $total,
            'rows' => $result,            
        ]);          
    }
    
    
    public function addAction() 
    {     
        // Создаем форму.
        $form = new GoodsForm($this->entityManager);
        
        // Проверяем, является ли пост POST-запросом.
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            // Заполняем форму данными.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Получаем валидированные данные формы.
                $data = $form->getData();
                
                // Используем менеджер goods для добавления нового good в базу данных.                
                $this->goodsManager->addNewGoods($data);
                
                // Перенаправляем пользователя на страницу "goods".
                return $this->redirect()->toRoute('goods', []);
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
        $form = new GoodsForm($this->entityManager);
    
        // Получаем ID tax.    
        $goodsId = $this->params()->fromRoute('id', -1);
    
        // Находим существующий пост в базе данных.    
        $goods = $this->entityManager->getRepository(Goods::class)
                ->findOneById($goodsId);  
        	
        if ($goods == null) {
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
                $this->goodsManager->updateGoods($goods, $data);
                
                // Перенаправляем пользователя на страницу "goods".
                return $this->redirect()->toRoute('goods', []);
            }
        } else {
            $data = [
               'name' => $goods->getName(),
               'code' => $goods->getCode(),
               'producer' => $goods->getProducer(),
               'tax' => $goods->getTax(),
               'available' => $goods->getAvailable(),
               'description' => $goods->getDescription(),
            ];
            
            $form->setData($data);
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form,
            'goods' => $goods
        ]);  
    }    
    
    public function deleteAction()
    {
        $goodsId = $this->params()->fromRoute('id', -1);
        
        $goods = $this->entityManager->getRepository(Goods::class)
                ->findOneById($goodsId);        
        if ($goods == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->goodsManager->removeGood($goods);
        
        // Перенаправляем пользователя на страницу "goods".
        return $this->redirect()->toRoute('goods', []);
    }    
    
    public function deleteAllAction()
    {
        $this->goodsManager->removeAllGoods();
        
        return new JsonModel([
            'ok'
        ]);
    }
    

    public function viewAction() 
    {       
        $goodsId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($goodsId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax by ID
        $goods = $this->entityManager->getRepository(Goods::class)
                ->findOneById($goodsId);
        
        if ($goods == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        // Render the view template.
        return new ViewModel([
            'goods' => $goods,
        ]);
    }
    
    public function groupsAction()
    {
        $groups = $this->entityManager->getRepository(GoodsGroup::class)
               ->findBy([], []);
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'groups' => $groups,
            'goodsManager' => $this->goodsManager
        ]);  
        
    }
    
    public function groupFormAction()
    {
        $groupId = (int)$this->params()->fromRoute('id', -1);
        
        $group = null;
        if ($groupId>0) {
            $group = $this->entityManager->getRepository(GoodsGroup::class)
                    ->findOneById($groupId);
        }
        
        $form = new GoodsGroupForm($this->entityManager);

        if ($this->getRequest()->isPost()) {
            
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($group){
                    $this->goodsManager->updateGroup($group, $data);
                } else {
                    $this->goodsManager->addNewGroup($data);
                }    
                
                return new JsonModel(
                   ['ok']
                );           
            } else {
                var_dump($form->getMessages());
            }
        } else {
            if ($group){
                $data = [
                    'name' => $group->getName(),
                    'description' => $group->getDescription(),
                ];
                $form->setData($data);
            }  
        }    
        $this->layout()->setTemplate('layout/terminal');
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'group' => $group,
        ]);                        
    }
    
    public function deleteGroupFormAction()
    {
        $groupId = $this->params()->fromRoute('id', -1);
        
        $group = $this->entityManager->getRepository(GoodsGroup::class)
                ->findOneById($groupId);
        
        if ($group == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->goodsManager->removeGroup($group);
        
        return new JsonModel(
           ['ok']
        );           
        
        exit;
    }
    
    public function testMorphyAction()
    {
        $morphyFilter = new MorphyFilter();
        echo $morphyFilter->filter('Рулон для стерилизации 50мм*200м, ЕвроТайп');
        exit;
    }
    
    public function updateTagsAction()
    {
        $this->goodsManager->updateGoodsTags();
        return $this->redirect()->toRoute('goods', []);
    }
    
}
