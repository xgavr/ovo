<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\Bid;
use Application\Entity\BidReserve;
use Application\Entity\Order;
use Application\Entity\Reserve;
use Application\Entity\Goods;
use Application\Entity\Cart;
use User\Entity\User;
use Application\Entity\Supplier;

/**
 * Description of OrderService
 *
 * @author Daddy
 */
class ReserveManager
{
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
  
    private $authService;
    
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }
    
    public function addNewBid($reserve, $data, $flushnow=true)
    {
        $bid = new BidReserve();
        $bid->setNum($data['num']);
        $bid->setPrice($data['price']);
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
    }
    
    public function addNewReserve($data) 
    {
        // Создаем новую сущность.
        $order = new Reserve();
        $order->setComment($data['comment']);
        $order->setTotal(round(0, 2));
        
        if ($data['supplier'] instanceof Supplier){
            $order->setSupplier($data['supplier']);            
        } else {
            $client = $this->entityManager->getRepository(Supplier::class)
                        ->findOneById($data['supplier']);        
            $order->setClient($supplier);
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
        
        return $order;
    }   
    
    public function updateReserveTotal($reserve)
    {
        $result = $this->entityManager->getRepository(BidReserve::class)
                ->getReserveNum($reserve);
        
        $reserve->setTotal($result[0]['total']);
        
        $this->entityManager->persist($reserve);
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updateReserve($reserve, $data) 
    {
        $order->setComment($data['comment']);

        $this->entityManager->persist($reserve);
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }    
    
    public function removeReserve($reserve) 
    {   
        $bids = $this->entityManager->getRepository(Reserve::class)
                    ->findBidReserve($reserve)->getResult();
        
        foreach ($bids as $bid){
            $this->entityManager->remove($bid);
        }
        
        $this->entityManager->remove($reserve);
        
        $this->entityManager->flush();
    }    

    /*
     * @var Application\Entity\Supplier $client
     * @var Application\Entity\Order $order
     * 
     */
    public function checkout($order = null)
    {
        
        $carts = $this->entityManager->getRepository(Bid::class)
                    ->findToReserve($order)->getResult();
        
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
    
}
