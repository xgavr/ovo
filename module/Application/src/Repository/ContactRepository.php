<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Contact;
/**
 * Description of SupplierRepository
 *
 * @author Daddy
 */
class ContactRepository extends EntityRepository
{
    /*
     * 
     */
    public function findRecordForLegal($parent)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Contact::class, 'c')
            ->where('c.status = :status')
                ;
        
        if ($parent instanceof \Application\Entity\Supplier ){
            $queryBuilder->andWhere('c.supplier = :parentId');
        } elseif ($parent instanceof \Application\Entity\Client){
            $queryBuilder->andWhere('c.client = :parentId');            
        } elseif ($parent instanceof \Company\Entity\Office){
            $queryBuilder->andWhere('c.office = :parentId');            
        } elseif ($parent instanceof \User\Entity\User) {
            $queryBuilder->andWhere('c.user = :parentId');            
        } else {
            throw new \Exception('Неверный тип родительской сущности');
        }
        
        $queryBuilder->setParameters(['status' => Contact::STATUS_LEGAL, 'parentId' => $parent->getId()]); 
        
        return $queryBuilder->getQuery()->getResult();
    }        

    public function findRecordForOther($parent)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Contact::class, 'c')
            ->where('c.status != :status')
                ;
        
        if ($parent instanceof \Application\Entity\Supplier ){
            $queryBuilder->andWhere('c.supplier = :parentId');
        } elseif ($parent instanceof \Application\Entity\Client){
            $queryBuilder->andWhere('c.client = :parentId');            
        } elseif ($parent instanceof \Company\Entity\Office){
            $queryBuilder->andWhere('c.office = :parentId');            
        } elseif ($parent instanceof \User\Entity\User) {
            $queryBuilder->andWhere('c.user = :parentId');            
        } else {
            throw new \Exception('Неверный тип родительской сущности');
        }
        
        $queryBuilder->setParameters(['status' => Contact::STATUS_LEGAL, 'parentId' => $parent->getId()]); 
        
        return $queryBuilder->getQuery()->getResult();
    }        

}