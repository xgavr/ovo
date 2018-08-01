<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Filter\Basename;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Rawprice
 * @ORM\Entity(repositoryClass="\Application\Repository\RawRepository")
 * @ORM\Table(name="raw")
 * @author Daddy
 */
class Raw {
    
     // Supplier status constants.
    const STATUS_LOAD       = 4; //В процессе загрузки
    const STATUS_ACTIVE       = 1; // Active raw.
    const STATUS_RETIRED      = 2; // Retired raw.
    const STATUS_PARSE       = 5; //Разбирается
    const STATUS_PARSED       = 3; //Разобран
    
           
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="name")   
     */
    protected $name;

    /**
     * @ORM\Column(name="filename")   
     */
    protected $filename;

    /**
     * @ORM\Column(name="rows")   
     */
    protected $rows;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;
    

    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Supplier", inversedBy="raw") 
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;
    
    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\Rawprice", mappedBy="raw")
    * @ORM\JoinColumn(name="id", referencedColumnName="raw_id")
     */
    private $rawprice;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->rawprice = new ArrayCollection();
    }
    
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }         

    public function getFilename() 
    {
        return $this->filename;
    }

    public function getBasename() 
    {
        $basenameFilter = new Basename();
        return $basenameFilter->filter($this->getFilename());
    }

    public function setFilename($filename) 
    {
        $this->filename = $filename;
    }     

    public function getName() 
    {
        return $this->name;
    }

    public function setName($name) 
    {
        $this->name = $name;
    }     

    public function getRows() 
    {
        return $this->rows;
    }

    public function setRows($rows) 
    {
        $this->rows = $rows;
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
            self::STATUS_ACTIVE => 'Новый',
            self::STATUS_RETIRED => 'Удалить',
            self::STATUS_PARSED => 'Разобран',
            self::STATUS_PARSE => 'Разбирается',
            self::STATUS_LOAD => 'Загружается',
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
    
    public function getStatusName($status)
    {
        $list = self::getStatusList();
        if (isset($list[$status]))
            return $list[$status];
        
        return 'Unknown';        
    }
        
    public function getStatusActive()
    {
        return self::STATUS_ACTIVE;
    }        
    
    public function getStatusRetired()
    {
        return self::STATUS_RETIRED;
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
     * Возвращает связанный supplier.
     * @return \Application\Entity\Supplier
     */    
    public function getSupplier() 
    {
        return $this->supplier;
    }

    /**
     * Задает связанный supplier.
     * @param \Application\Entity\Supplier $supplier
     */    
    public function setSupplier($supplier) 
    {
        $this->supplier = $supplier;
        $supplier->addRaw($this);
    }    
    
    /**
     * Returns the array of contacts assigned to this.
     * @return array
     */
    public function getRawprice()
    {
        return $this->rawprice;
    }
        
    /**
     * Assigns.
     */
    public function addRawprice($rawprice)
    {
        $this->rawprice[] = $rawprice;
    }
    
}
