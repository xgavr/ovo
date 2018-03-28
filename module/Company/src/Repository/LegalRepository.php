<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Company\Repository;

use Doctrine\ORM\EntityRepository;
use Company\Entity\Legal;
/**
 * Description of SupplierRepository
 *
 * @author Daddy
 */
class LegalRepository extends EntityRepository
{

    /*
     * Поиск юрлийа по инн и кпп
     * $inn string
     * $kpp string
     * $resultMode null or 2 - array
     */
    public function findOneByInnKpp($inn, $kpp = null, $resultMode = null)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('r')
            ->from(Legal::class, 'r')
            ->where('r.inn = ?1')    
            ->setParameter('1', $inn)
//            ->orderBy(['id DESC'])    
                ;

        if ($kpp){
            $queryBuilder
                ->andWhere('r.kpp = ?2')    
                ->setParameter('2', $kpp)
                ;
            
        }        
        
        $query = $queryBuilder->getQuery();
        
        if ($resultMode){
            return $queryBuilder->getQuery()->getResult($resultMode);        
        } else {
            return $queryBuilder->getQuery()->getOneOrNullResult();                    
        }    
    }
}