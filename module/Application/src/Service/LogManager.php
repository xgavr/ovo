<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Application\Entity\Log;
use User\Entity\User;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Json\Json;

/**
 * Description of AuthService
 *
 * @author Daddy
 */
class LogManager {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $authService;    
    
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }
    
    /*
     * Преобразование данных объекта в строку
     * @param stdClass $entity
     * @return string
     */
    protected function toJson($entity)
    {
        $data = [];
        $methods = get_class_methods($entity);
        foreach ($methods as $func){
            if (substr($func, 0, 3) == 'get'){
                $data[lcfirst(substr($func, 3))] = $entity->$func();
            }
        }
        
//        $query = $this->entityManager->getRepository(get_class($entity))
//                ->createQueryBuilder('e')
//                ->where('e.id = ?1')
//                ->setParameter('1', $entity->getId())
//                ->getQuery()
//                ;
//
//        $data = $query->getResult(2);
        
        if (count($data)){
            return Json::encode($data);
        }
        
        return;
    }
    
    /*
     * Подготовка данных в зависимости от статуса
     * @param array $data
     * @param stdClass $entity
     * @return string 
     */
    protected function statusData($data, $entity)
    {
        switch ($data['status']){
            case Log::STATUS_NEW:
            case Log::STATUS_UPDATE: return $this->toJson($entity); 
            default: return;    
        }
        
        return;
    }
    
    /*
     * Подготовка вложений лога
     * @param array $attachment
     * @return string
     */
    protected function attachment($attachment)
    {
        if (is_array($attachment)){
            return Json::encode($attachment);
        }
        return;
    }
    
    /*
     * Добавление новой записи в лог
     * @param array $data
     * @param stdClass $entity
     * 
     */
    
    public function log($data, $entity, $parentEntity = null)
    {
        $log = new Log();

        $log->setStatus($data['status']);
        $log->setMessage($data['message']);
        $log->setAttachment($this->attachment($data['attachment']));
        
        $log->setModel(get_class($entity));
        $log->setModelId($entity->getId());
        
        if ($parentEntity){
            $log->setParentModel(get_class($parentEntity));
            $log->setParentModelId($parentEntity->getId());            
        }
        
        $log->setModelData($this->statusData($data, $entity));
        
        $currentDate = date('Y-m-d H:i:s');
        $log->setDateCreated($currentDate);        
        
        $currentUser = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
        if ($currentUser){
            $log->setUser($currentUser);
        }    
        
        $remoteAddress = new RemoteAddress();
        $log->setExtraIp($remoteAddress->getIpAddress());
        
        $this->entityManager->persist($log);
        $this->entityManager->flush($log);
    }
    
    /*
     * Лог создания нового объекта 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function creation($data, $entity, $parentEntity = null)
    {
        $data['status'] = Log::STATUS_NEW;
        $this->log($data, $entity, $parentEntity);
    }

    /*
     * Лог обновления объекта 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function update($data, $entity, $parentEntity = null)
    {
        $data['status'] = Log::STATUS_UPDATE;
        $this->log($data, $entity, $parentEntity);
    }

    /*
     * Лог удаления объекта 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function remove($data, $entity, $parentEntity = null)
    {
        $data['status'] = Log::STATUS_DELETE;
        $this->log($data, $entity, $parentEntity);
    }

    /*
     * Лог отправлено почтовое сообщение 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function email($data, $entity, $parentEntity = null)
    {
        $data['status'] = Log::STATUS_EMAIL;
        $this->log($data, $entity, $parentEntity);
    }

    /*
     * Лог отправлено sms сообщение 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function sms($data, $entity, $parentEntity = null)
    {
        $data['status'] = Log::STATUS_SMS;
        $this->log($data, $entity, $parentEntity);
    }

}
