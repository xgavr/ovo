<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Goods;
use Application\Entity\Rawprice;
use Application\Entity\Image;
use Application\Entity\Producer;
use Application\Entity\GoodsGroup;
use Application\Filter\MorphyFilter;
/**
 * Description of GoodsRepository
 *
 * @author Daddy
 */
class GoodsRepository extends EntityRepository{

    public function findAllGoods()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('g, p')
            ->from(Goods::class, 'g')            
            ->join("g.producer", 'p', 'WITH') 
            ->orderBy('g.name')
                ;

        return $queryBuilder->getQuery();
    }
    
    public function searchByName($search){

        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('g, p')
            ->from(Goods::class, 'g')
            ->join("g.producer", 'p', 'WITH') 
            ->where('g.name like :search')    
            ->orderBy('g.name')
            ->setParameter('search', '%' . $search . '%')
                ;
        return $queryBuilder->getQuery();
    }
    
    public function paramsSearch($params)
    {
        
        $entityManager = $this->getEntityManager();

        $config = $entityManager->getConfiguration();
        $config->addCustomStringFunction('match_against', 'DoctrineExtensions\Query\MySq\MatchAgainst');
        
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('g, p')
            ->from(Goods::class, 'g')
            ->join("g.producer", 'p', 'WITH') 
            ->orderBy('g.name')
                ;
        
        if ($params['search'] && strlen($params['search']) > 3){
            
            $morphyFilter = new MorphyFilter();
            $search = $morphyFilter->filter($params['search']);
            
            $queryBuilder->andWhere('match (g.tags) against (:search) > 0')
                            ->setParameter('search', $search)
                ;
        }

        if (is_array($params['producer'])){
            $queryBuilder->andWhere($queryBuilder->expr()->in('g.producer', ':producer'))
                            ->setParameter('producer', $params['producer'])
                                    ;
        }

        if (is_array($params['group'])){
            $queryBuilder->andWhere($queryBuilder->expr()->in('g.group', ':group'))
                            ->setParameter('group', $params['group'])
                                    ;
        }
//        var_dump($queryBuilder->getQuery()->getSQL()); exit;

        return $queryBuilder->getQuery();
    }
    
    public function searchNameForSearchAssistant($search)
    {        
        return $this->searchByName($search)->getResult();
    }  
    
    /*
     * @var Apllication\Entity\Goods $good
     */
    public function findGoodRawprice($good)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Rawprice::class, 'c')
            ->where('c.good = ?1')    
            ->orderBy('c.id')
            ->setParameter('1', $good->getId())    
                ;
//        var_dump($queryBuilder->getQuery()->getSQL()); exit;

        return $queryBuilder->getQuery()->getResult();
    }
    
    /*
     * @var Apllication\Entity\Goods $good
     */
    public function findGoodsImage($good)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Image::class, 'c')
            ->where('c.good = ?1')    
            ->orderBy('c.id')
            ->setParameter('1', $good->getId())    
                ;
        //var_dump($queryBuilder->getQuery()->getSQL()); exit;

        return $queryBuilder->getQuery()->getResult();
    }
    
    
    public function getMaxPrice($good)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(Rawprice::class, 'r')
            ->select('MAX(r.price) as price')
            ->where('r.good = ?1')    
            ->groupBy('r.good')
            ->setParameter('1', $good->getId())
                ;
        return $queryBuilder->getQuery()->getResult();
        
    }
    
    
    public function findAllActiveGroup()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('g')
            ->from(GoodsGroup::class, 'g')
            ->orderBy('g.name')
                ;

        return $queryBuilder->getQuery();
    }    
    
    
           
}
