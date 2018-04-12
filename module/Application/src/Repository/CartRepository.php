<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Cart;

/**
 * Description of CartRepository
 *
 * @author Daddy
 */
class CartRepository extends EntityRepository{

    /*
     * 
     */
    public function findAllCart()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Cart::class, 'c')
            ->orderBy('c.good_id')
                ;

        return $queryBuilder->getQuery();
    }        

    /*
     * @var Apllication\Entity\Client
     */
    public function findClientCart($client)
    {
        if ($client){
            $entityManager = $this->getEntityManager();

            $queryBuilder = $entityManager->createQueryBuilder();

            $queryBuilder->select('c')
                ->from(Cart::class, 'c')
                ->where('c.client = ?1')    
                ->orderBy('c.id')
                ->setParameter('1', $client->getId())    
                    ;

            return $queryBuilder->getQuery();
        }
        
        return;
    }   
    
    public function getClientNum($client)
    {
        if ($client){
            
            $entityManager = $this->getEntityManager();

            $queryBuilder = $entityManager->createQueryBuilder();

            $queryBuilder->select('r')
                ->from(Cart::class, 'r')
                ->select('SUM(r.num) as num, SUM(r.num*r.price) as total')
                ->where('r.client = ?1')    
                ->groupBy('r.client')
                ->setParameter('1', $client->getId())
                    ;
            return $queryBuilder->getQuery()->getResult();
        }
        
        return;
    }
    
    /*
     * @var Application\Entyti\Goods
     */
    public function getCartInGood($good)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(Cart::class, 'r')
            ->where('r.good = :goodId')    
            ->setParameters(['goodId' => $good->getId()])
                ;
        return $queryBuilder->getQuery()->getResult();
    }
    
    /*
     * @var Application\Entity\Client @client
     * @var int $goodId
     */
    public function getGoodInClientCart($client, $goodId)
    {
        if ($client){
            $entityManager = $this->getEntityManager();

            $queryBuilder = $entityManager->createQueryBuilder();

            $queryBuilder->select('r')
                ->from(Cart::class, 'r')
                ->select('SUM(r.num) as num, SUM(r.num*r.price) as total')
                ->where('r.client = :clientId and r.good = :goodId')    
                ->groupBy('r.client')
                ->setParameters(['clientId' => $client->getId(), 'goodId' => $goodId])
                    ;
            return $queryBuilder->getQuery()->getResult();
        }
        
        return;
    }
    
}
