<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Reserve;
use Application\Entity\BidReserve;
/**
 * Description of OrderRepository
 *
 * @author Daddy
 */
class ReserveRepository extends EntityRepository{

    public function findAllReserve($user=null)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('o')
            ->from(Reserve::class, 'o')
            ->orderBy('o.id')
                ;
        
        if ($user){
            $queryBuilder->join("o.supplier", 'c', 'WITH')
                    ->where('c.manager = ?1')
                    ->setParameter('1', $user)
                    ;
        }
        
        return $queryBuilder->getQuery();
    }       
    
    /*
     * @var Apllication\Entity\Supplier
     */
    public function findSupplierReserve($supplier)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Reserve::class, 'c')
            ->where('c.supplier = ?1')    
            ->orderBy('c.id')
            ->setParameter('1', $supplier->getId())    
                ;

        return $queryBuilder->getQuery();
    }       
    

    /*
     * @var Apllication\Entity\Reserve
     */
    public function getReserveNum($reserve)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(BidReserve::class, 'r')
            ->select('SUM(r.num) as num, SUM(r.num*r.price) as total')
            ->where('r.reserve = ?1')    
            ->groupBy('r.reserve')
            ->setParameter('1', $reserve->getId())
                ;
        return $queryBuilder->getQuery()->getResult();
        
    }
    
    /*
     * @var Apllication\Entity\Reserve
     */
    public function findBidReserve($reserve)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(BidReserve::class, 'c')
            ->where('c.order = ?1')    
            ->orderBy('c.id')
            ->setParameter('1', $reserve->getId())    
                ;

        return $queryBuilder->getQuery();
    }        
    
    /*
     * @var Application\Entyti\Goods
     */
    public function getBidInGood($good)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(BidReserve::class, 'r')
            ->where('r.good = :goodId')    
            ->setParameters(['goodId' => $good->getId()])
                ;
        return $queryBuilder->getQuery()->getResult();
    }
    
    
}
