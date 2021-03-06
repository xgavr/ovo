<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\Bid;
use Application\Entity\Order;
use Application\Entity\Goods;
use Application\Entity\Cart;
use User\Entity\User;
use Application\Entity\Client;

/**
 * Description of OrderService
 *
 * @author Daddy
 */
class OrderManager
{
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
  
    private $authService;
    
    /**
     * Менеджер лога.
     * @var Application\Service\LogManager 
     */
    private $logManager;    
    
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $authService, $logManager)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->logManager = $logManager;
    }
    
    public function addNewBid($order, $data, $flushnow=true)
    {
        $bid = new Bid();
        $bid->setNum($data['num']);
        $bid->setPrice($data['price']);
        $bid->setReserved(0);
        $currentDate = date('Y-m-d H:i:s');        
        $bid->setDateCreated($currentDate);
        
        if ($data['good'] instanceof Goods){
            $bid->setGood($data['good']);            
        } else {
            $good = $this->entityManager->getRepository(Goods::class)
                        ->findOneById($data['good']);        
            $bid->setGood($good);
        }    
        
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
        $bid->setUser($currentUser);  
        
        $bid->setOrder($order);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($bid);
        
        // Применяем изменения к базе данных.
        if ($flushnow){
            $this->entityManager->flush(); 
        }    
        $this->logManager->creation([], $bid, $order);
    }
    
    public function addNewOrder($data) 
    {
        // Создаем новую сущность.
        $order = new Order();
        if (isset($data['comment'])){
            $order->setComment($data['comment']);
        }    
        $order->setTotal(round(0, 2));
        
        if ($data['client'] instanceof Client){
            $order->setClient($data['client']);            
        } else {
            $client = $this->entityManager->getRepository(Client::class)
                        ->findOneById($data['client']);        
            $order->setClient($client);
        }    
        
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
        $order->setUser($currentUser);
        
        $order->setStatus(Order::STATUS_NEW);
        
        $currentDate = date('Y-m-d H:i:s');        
        $order->setDateCreated($currentDate);
        
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($order);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();

        $this->logManager->creation([], $order);
        
        return $order;
    }   
    
    public function updateOrderTotal($order)
    {
        $result = $this->entityManager->getRepository(Bid::class)
                ->getOrderNum($order);
        
        $order->setTotal($result[0]['total']);
        
        $this->entityManager->persist($order);
        // Применяем изменения к базе данных.
        $this->entityManager->flush();

        $this->logManager->update([], $order);
    }
    
    /*
     * Обновить заказ
     * @param Application\Entity\Order
     * @param array
     * 
     */
    public function updateOrder($order, $data) 
    {
        $order->setComment($data['comment']);

        $this->entityManager->persist($order);
        // Применяем изменения к базе данных.
        $this->entityManager->flush();

        $this->logManager->update([], $order);
    }    

    /*
     * Обновить статус заказа
     * @param Application\Entity\Order
     * @param integer
     */
    public function updateStatus($order, $status) 
    {
        $order->setStatus($status);

        $this->entityManager->persist($order);
        // Применяем изменения к базе данных.
        $this->entityManager->flush($order);

        $this->logManager->update([], $order);
    }    
    
    /*
     * Удалить заказ
     * @param Application\Entity\Order
     * 
     */
    public function removeOrder($order) 
    {   
        $bids = $this->entityManager->getRepository(Order::class)
                    ->findBidOrder($order)->getResult();
        
        foreach ($bids as $bid){
            $this->entityManager->remove($bid);
        }
        
        $this->entityManager->remove($order);
        
        $this->entityManager->flush();

        $this->logManager->remove([], $order);
    }    

    /*
     * @var Application\Entity\Clent $client
     * @var Application\Entity\Cart $carts
     * 
     */
    public function checkoutClient($client)
    {
        
        $carts = $this->entityManager->getRepository(Cart::class)
                    ->findClientCart($client)->getResult();
        
        $order = null;
        if (count($carts)){         
            $orderData = ['client' => $client];
            $order = $this->addNewOrder($orderData);

            foreach ($carts as $cart){
                $bidData = [
                    'num' => $cart->getNum(),
                    'price' => $cart->getPrice(),
                    'good' => $cart->getGood(),
                ];

                $this->addNewBid($order, $bidData, false);

                $this->entityManager->remove($cart);
            }

            $this->entityManager->flush();
            
            $this->updateOrderTotal($order);
        }
        
        return $order;
    }
    
    /*
     * Проверка количества заказанного у поставщика 
     * @param Application\Entity\Order
     */
    public function checkReserved($order)
    {
        $bids = $order->getBid();
        foreach ($bids as $bid){
            $bid->checkReserved();            
        }
        $this->entityManager->flush();
        
        return;
    }
}
