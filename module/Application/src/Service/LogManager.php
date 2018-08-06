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
        $values = get_object_vars($entity);
        foreach ($values as $value){
            if (is_object($value)){
                $value = $this->getStrData($value);
            }            
        }
        
        return Json::encode($values);
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
    
    public function log($data, $entity)
    {
        $log = new Log();

        $log->setStatus($data['status']);
        $log->setMessage($data['message']);
        $log->setAttachment($this->attachment($data['attachment']));
        
        $log->setModel(get_class($entity));
        $log->setModelId($entity->getId());
        
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
    public function createure($data, $entity)
    {
        $data['status'] = Log::STATUS_NEW;
        $this->log($data, $entity);
    }

    /*
     * Лог обновления объекта 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function update($data, $entity)
    {
        $data['status'] = Log::STATUS_UPDATE;
        $this->log($data, $entity);
    }

    /*
     * Лог удаления объекта 
     * @param array $data
     * @param stdClass $entity
     * 
     */
    public function remove($data, $entity)
    {
        $data['status'] = Log::STATUS_DELETE;
        $this->log($data, $entity);
    }
}
