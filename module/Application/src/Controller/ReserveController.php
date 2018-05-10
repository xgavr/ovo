<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Order;
use Application\Entity\Reserve;
use Application\Entity\BidReserve;
use Application\Entity\Bid;
use Application\Entity\Rawprice;
use Application\Entity\Supplier;
use Company\Entity\Office;
use Zend\View\Model\JsonModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class ReserveController extends AbstractActionController
{
    
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Менеджер товаров.
     * @var Application\Service\ReserveManager 
     */
    private $reserveManager;    
    
    /**
     * Менеджер бланков.
     * @var Blank\Service\BlankManager 
     */
    private $blankManager;    
    
        
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $reserveManager, $blankManager) 
    {
        $this->entityManager = $entityManager;
        $this->reserveManager = $reserveManager;
        $this->blankManager = $blankManager;
    }    
    
    public function workAction()
    {
        return new ViewModel([
        ]);          
        
    }
    
    public function addWorkAction()
    {
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            $supplier = $this->entityManager->getRepository(Supplier::class)
                    ->findOneById($data['supplier']);
            
            $bid = $this->entityManager->getRepository(Bid::class)
                    ->findOneById($data['id']);
            
            if ($supplier){
                $data['supplier'] = $supplier;
                $data['bid'] = $bid;
                if (!$data['reserve']) {
                    $data['reserve'] = $data['num'];
                } 
                $this->reserveManager->addWork($data);                        
            }    
        }
                                
        return new JsonModel([
            'bid' => $data['id'],
        ]);        
        
    }

    public function workContentAction()
    {
        
        $q = $this->params()->fromQuery('search', '');
        $offset = $this->params()->fromQuery('offset');
        $limit = $this->params()->fromQuery('limit');
        
        $query = $this->entityManager->getRepository(BidReserve::class)
                    ->findToReserve(null, $q);            
        
        
        if ($offset) $query->setFirstResult( $offset );
        if ($limit) $query->setMaxResults( $limit );
        
        $result = $query->getResult();
        $total = count($result);
        $bids = [];
        foreach ($result as $row){
            $bid = [
                'id' => $row->getId(),
                'num' => $row->getNum(),
                'reserved' => $row->getReserved(),
            ]; 
            if ($row->getGood()){
                
                $rawprice = $this->entityManager->getRepository(Rawprice::class)
                    ->findMinPriceRawprice($row->getGood());
                
                $bid['good_id'] = $row->getGood()->getId();
                $bid['name'] = $row->getGood()->getName();
                $bid['code'] = $row->getGood()->getCode();
                $bid['producer'] = $row->getGood()->getProducer()->getName();
                $bid['price'] = $rawprice->getPrice();
                $bid['supplier_id'] = $rawprice->getRaw()->getSupplier()->getId();
                $bid['supplier'] = $rawprice->getRaw()->getSupplier()->getName();
            };    
            $bids[] = $bid;
        }
        
        return new JsonModel([
            'total' => $total,
            'rows' => $bids,
        ]);          
    }
    
    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        
        $query = $this->entityManager->getRepository(Reserve::class)
                        ->findAllReserve();

        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'reserves' => $paginator,
            'reserveManager' => $this->reserveManager
        ]);  
    }
    
    public function addAction() 
    {     
        // Создаем форму.
        $form = new ReserveForm($this->entityManager);
        
        // Проверяем, является ли пост POST-запросом.
        if ($this->getRequest()->isPost()) {
            
            // Получаем POST-данные.
            $data = $this->params()->fromPost();
            
            // Заполняем форму данными.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Получаем валидированные данные формы.
                $data = $form->getData();
                
                // Используем менеджер order для добавления нового good в базу данных.                
                $this->reserveManager->addNewReserve($data);
                
                // Перенаправляем пользователя на страницу "order".
                return $this->redirect()->toRoute('reserve', []);
            }
        }
        
        // Визуализируем шаблон представления.
        return new ViewModel([
            'form' => $form
        ]);
    }   
        
    public function deleteAction()
    {
        $reserveId = $this->params()->fromRoute('id', -1);
        
        $reserve = $this->entityManager->getRepository(Reserve::class)
                ->findOneById($reserveId);        
        if ($reserve == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->reserveManager->removeReserve($reserve);
        
        // Перенаправляем пользователя на страницу "order".
        return $this->redirect()->toRoute('reserve', []);
    }    

    public function viewAction() 
    {       
        $reserveId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($reserveId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the reserve ID
        $reserve = $this->entityManager->getRepository(Reserve::class)
                ->findOneById($reserveId);
        
        if ($reserve == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->reserveManager->updateReserveTotal($reserve);
      
        $bids = $this->entityManager->getRepository(Reserve::class)
                    ->findBidReserve($reserve)->getResult();
        
        // Render the view template.
        return new ViewModel([
            'reserve' => $reserve,
            'bids' => $bids,
        ]);
    } 
    
    public function checkoutAction()
    {
        $orderId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($orderId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax order ID
        $order = $this->entityManager->getRepository(Order::class)
                ->findOneById($orderId);
        
        if ($order == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->reserveManager->checkout($order);
        
        // Перенаправляем пользователя на страницу "order".
        return $this->redirect()->toRoute('order', ['action' => 'view', 'id' => $order->getId()]);
        
    }
    
    public function printAction()
    {
        $orderId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($orderId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the tax order ID
        $order = $this->entityManager->getRepository(Order::class)
                ->findOneById($orderId);
        
        if ($order == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
      
        $bids = $this->entityManager->getRepository(Order::class)
                    ->findBidOrder($order)->getResult();
        
        $offices = $this->entityManager->getRepository(Office::class)
                    ->findAll([]);
        
        $office = $offices[0];
        
        $data = [
            'firmName' => $office->getLegalContact()->getActiveLegal()->getName(),
            'firmAddress' => $office->getLegalContact()->getActiveLegal()->getAddress(),
            'firmInn' => $office->getLegalContact()->getActiveLegal()->getInn(),
            'firmKpp' => $office->getLegalContact()->getActiveLegal()->getKpp(),
            'firmRs' => $office->getLegalContact()->getActiveLegal()->getActiveBankAccount()->getRs(),
            'firmBik' => $office->getLegalContact()->getActiveLegal()->getActiveBankAccount()->getBik(),
            'firmBank' => $office->getLegalContact()->getActiveLegal()->getActiveBankAccount()->getName(),
            'firmKs' => $office->getLegalContact()->getActiveLegal()->getActiveBankAccount()->getKs(),
            'invoiceId' => $order->getId(),
            'invoiceDate' => $order->getDateCreated(),
            'clientName' => $order->getClient()->getLegalContact()->getActiveLegal()->getName(),
            'clientConsignee' => $order->getClient()->getLegalContact()->getActiveLegal()->getName(),
            'itemsTotal' => count($bids),
            'total_without_tax' => $order->getTotal(),
            'taxTotal' => 'Без НДС',
            'total' => $order->getTotal(),
            'chief' => $office->getLegalContact()->getActiveLegal()->getHead(),
            'chiefAccount' => $office->getLegalContact()->getActiveLegal()->getChiefAccount(),
            'items' =>[],
        ];
        
        foreach ($bids as $bid){
            $data['items'][] = [
                'goodName' => $bid->getGood()->getName(),
                'unit' => '',
                'quantity' => $bid->getNum(),
                'price' => $bid->getPrice(),
                'total' => $bid->getPrice()*$bid->getNum(),
            ];
        }
        
        $filename = $this->blankManager->invoice($data);
        $output_filename = 'Счет на оплату №'.$order->getId().'.xls';
        
        if (file_exists($filename)){
         
            $response = new \Zend\Http\Response\Stream();
            $response->setStream(fopen($filename, 'r'));
            $response->setStatusCode(200);
            $response->setStreamName($output_filename);
            $headers = new \Zend\Http\Headers();
            $headers->addHeaders(array(
                'Content-Disposition' => 'attachment; filename="'.$output_filename.'"',
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => filesize($filename),
                'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
                'Cache-Control' => 'must-revalidate',
                'Pragma' => 'public'
            ));

            $response->setHeaders($headers);
            
//            unlink($filename);
            return $response;
        }    
        return $this->redirect()->toRoute('order', ['action' => 'view', 'id' => $order->getId()]);
    }
    
}
