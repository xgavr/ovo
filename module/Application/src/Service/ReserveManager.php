<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\BidReserve;
use Application\Entity\Bid;
use Application\Entity\Order;
use Application\Entity\Reserve;
use Application\Entity\Goods;
use Application\Entity\Rawprice;
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
        $bidReserve = new BidReserve();
        $bidReserve->setNum($data['reserve']);
        $bidReserve->setPrice($data['price']);
        $currentDate = date('Y-m-d H:i:s');        
        $bidReserve->setDateCreated($currentDate);
        
        if ($data['good'] instanceof Goods){
            $bidReserve->setGood($data['good']);            
        } else {
            $good = $this->entityManager->getRepository(Goods::class)
                        ->findOneById($data['good']);        
            $bidReserve->setGood($good);
        }    
                
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
        $bidReserve->setUser($currentUser);  
        
        $bidReserve->setReserve($reserve);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($bidReserve);
        
        if ($data['bid'] instanceof Bid){
            $bidReserve->addBid($data['bid']);
        }    
        // Применяем изменения к базе данных.
        if ($flushnow){
            //$this->entityManager->flush();
            $this->updateReserveTotal($reserve);
        }   
        
        return $bidReserve;
    }
    
    public function addNewReserve($data, $flush = true) 
    {
        // Создаем новую сущность.
        $reserve = new Reserve();
        $reserve->setComment($data['comment']);
        $reserve->setTotal(round(0, 2));
        
        if ($data['supplier'] instanceof Supplier){
            $reserve->setSupplier($data['supplier']);            
        } else {
            $reserve = $this->entityManager->getRepository(Supplier::class)
                        ->findOneById($data['supplier']);        
            $reserve->setClient($supplier);
        }    
        
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
        $reserve->setUser($currentUser);
        
        $reserve->setStatus(Order::STATUS_NEW);
        
        $currentDate = date('Y-m-d H:i:s');        
        $reserve->setDateCreated($currentDate);
        
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($reserve);
        
        // Применяем изменения к базе данных.
        if ($flush){
            $this->entityManager->flush();
        }    
        
        return $reserve;
    }   
    
    public function getReserve($supplier, $flush = true)
    {
        $reserve = $this->entityManager->getRepository(Reserve::class)
                ->findOneBy(['supplier' => $supplier, 'status' => Reserve::STATUS_NEW]);
        
        if ($reserve){
           return $reserve; 
        }
        
        return $this->addNewReserve(['supplier' => $supplier], $flush);
    }
    
    public function updateReserveTotal($reserve, $flush = true)
    {
        $result = $this->entityManager->getRepository(BidReserve::class)
                ->getReserveNum($reserve);
        
        $reserve->setTotal($result[0]['total']);
        
        $this->entityManager->persist($reserve);
        // Применяем изменения к базе данных.
        if ($flush){
            $this->entityManager->flush();
        }    
    }
    
    public function updateReserve($reserve, $data) 
    {
        $order->setComment($data['comment']);

        $result = $this->entityManager->getRepository(BidReserve::class)
                ->getReserveNum($reserve);
        
        $reserve->setTotal($result[0]['total']);

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
     * @var array $params
     * @var Application\Entity\Supplier $supplier
     * @var Application\Entity\Bid $bid
     * @var Application\Entity\Goods $good
     * @float reserve
     * @float price
     */
    public function addWork($params, $flush = true)
    {
        if ($params['supplier']){
            $reserve = $this->getReserve($params['supplier'], $flush);
            if ($reserve){
                $this->addNewBid($reserve, 
                        [
                            'reserve' => $params['reserve'],
                            'good' => $params['good'],
                            'price' => $params['price'],
                            'bid' => $params['bid'],
                        ], 
                        $flush
                   );
                
                if (!$flush){
                    $this->updateReserveTotal($reserve, $flush);
                }
            }
        }
        
        return;
    }
    
    /*
     * @var Application\Entity\Order $order
     * 
     */
    public function checkout($order = null)
    {
        
        $bids = $this->entityManager->getRepository(BidReserve::class)
                    ->findToReserve($order)->getResult();
        
        if (count($bids)){         

            foreach ($bids as $bid){
                
                if ($bid->getNum() > $bid->getReserved()){
                    $rawprice = $this->entityManager->getRepository(Rawprice::class)
                        ->findMinPriceRawprice($bid->getGood());

                    if ($rawprice){
                        $this->addWork([
                            'supplier' => $rawprice->getRaw()->getSupplier(),
                            'reserve' => $bid->getNum() - $bid->getReserved(),
                            'good' => $bid->getGood(),
                            'price' => $rawprice->getPrice(),
                            'bid' => $bid,
                        ], false
                        );
                    }    
                }    
            }

            $this->entityManager->flush();
            
        }
        
        return;
    }
    
}
