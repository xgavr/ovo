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
            ->setParameter('2', Contact::STATUS_LEGAL)    
                ;
        
        if ($parent instanceof \Application\Entity\Supplier ){
            $queryBuilder->where('c.supplier = :parentId');
        } elseif ($parent instanceof \Application\Entity\Client){
            $queryBuilder->where('c.client = :parentId');            
        } elseif ($parent instanceof \Company\Entity\Office){
            $queryBuilder->where('c.office = :parentId');            
        } elseif ($parent instanceof \User\Entity\User) {
            $queryBuilder->where('c.user = :parentId');            
        } else {
            throw new \Exception('Неверный тип родительской сущности');
        }
        
        $queryBuilder->setParameters(['status' => Contact::STATUS_LEGAL, 'parentId' => $parent->getId()]); 
        
        return $queryBuilder->getQuery()->getResult();
    }        

}