<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Log
 * @ORM\Entity(repositoryClass="\Application\Repository\LogRepository")
 * @ORM\Table(name="log")
 * @author Daddy
 */
class Log {
        
     // Status constants.
    const STATUS_NEW       = 1; //
    const STATUS_UPDATE    = 2; // 
    const STATUS_DELETE    = 3; // 
    const STATUS_EMAIL     = 4; // лог отправлен/получен email связанное с объектом
    const STATUS_SMS       = 5; // лог отправлен смс связанное с объектом
    const STATUS_ATTACH    = 99; // лог свзянного с объектом прочего действия
   
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;

    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    
    /**
     * @ORM\Column(name="model")   
     */
    protected $model;

    /**
     * @ORM\Column(name="parent_model")   
     */
    protected $parentModel;

    /**
     * @ORM\Column(name="model_id")   
     */
    protected $modelId;

    /**
     * @ORM\Column(name="parent_model_id")   
     */
    protected $parentModelId;

    /**
     * @ORM\Column(name="model_data")   
     */
    protected $modelData;

    /**
     * @ORM\Column(name="message")   
     */
    protected $message;

    /**
     * @ORM\Column(name="attachment")   
     */
    protected $attachment;

    /**
     * @ORM\Column(name="extra_ip")   
     */
    protected $extraIp;

    
    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="log") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {
    }
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getModel() 
    {
        return $this->model;
    }

    public function setModel($model) 
    {
        $this->model = $model;
    }     

    public function getParentModel() 
    {
        return $this->parentModel;
    }

    public function setParentModel($parentModel) 
    {
        $this->parentModel = $parentModel;
    }     

    public function getModelId() 
    {
        return $this->modelId;
    }

    public function setModelId($modelId) 
    {
        $this->modelId = $modelId;
    }     

    public function getParentModelId() 
    {
        return $this->parentModelId;
    }

    public function setParentModelId($parentModelId) 
    {
        $this->parentModelId = $parentModelId;
    }     

    public function getModelData() 
    {
        return $this->modelData;
    }

    public function setModelData($modelData) 
    {
        $this->modelData = $modelData;
    }     

    public function getMessage() 
    {
        return $this->message;
    }

    public function setMessage($message) 
    {
        $this->message = $message;
    }     

    public function getExtraIp() 
    {
        return $this->extraIp;
    }

    public function setExtraIp($extraIp) 
    {
        $this->extraIp = $extraIp;
    }     

    /*
     * @return array
     */
    public function getAttachment() 
    {
        return $this->attachment;
    }

    public function setAttachment($attachment) 
    {
        $this->attachment = $attachment;
    }     

    /**
     * Returns status.
     * @return int     
     */
    public function getStatus() 
    {
        return $this->status;
    }

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList() 
    {
        return [
            self::STATUS_NEW => 'Создание',
            self::STATUS_UPDATE => 'Обновление',
            self::STATUS_DELETE => 'Удаление',
            self::STATUS_EMAIL => 'Отправлено письмо',
            self::STATUS_SMS => 'Отправлено смс',
            self::STATUS_ATTACH => 'Прочее',
        ];
    }    
    
    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];
        
        return 'Unknown';
    }    
    
    /**
     * Sets status.
     * @param int $status     
     */
    public function setStatus($status) 
    {
        $this->status = $status;
    }   
    
    /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated     
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }    
            
    /*
     * Возвращает связанный user.
     * @return \User\Entity\User
     */
    
    public function getUser() 
    {
        return $this->user;
    }

    /**
     * Задает связанный user.
     * @param \User\Entity\User $user
     */    
    public function setUser($user) 
    {
        $this->user = $user;
    }     
        
}
