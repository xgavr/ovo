<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Filter\ToNull;

/**
 * Description of Pricelist
 * @ORM\Entity(repositoryClass="\Application\Repository\SupplierRepository")
 * @ORM\Table(name="price_settings")
 * @author Daddy
 */
class Pricesettings {
    
     // Supplier status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.
    
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
     * @ORM\Column(name="article")   
     */
    protected $article;
    
    
    /**
     * @ORM\Column(name="iid")   
     */
    protected $iid;
    
    /**
     * @ORM\Column(name="producer")   
     */
    protected $producer;
    
    /**
     * @ORM\Column(name="country")   
     */
    protected $country;
    
    /**
     * @ORM\Column(name="title")   
     */
    protected $title;
    
    /**
     * @ORM\Column(name="description")   
     */
    protected $description;
    
    /**
     * @ORM\Column(name="image")   
     */
    protected $image;
    
    /**
     * @ORM\Column(name="price")   
     */
    protected $price;
    
    /**
     * @ORM\Column(name="currency")   
     */
    protected $currency;

    /**
     * @ORM\Column(name="rate")   
     */
    protected $rate;

    /**
     * @ORM\Column(name="rest")   
     */
    protected $rest;
    
    /**
     * @ORM\Column(name="unit")   
     */
    protected $unit;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;    

    /**
     * @ORM\Column(name="status")  
     */
    protected $status;    
       
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Supplier", inversedBy="price_settings") 
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;    
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getName() 
    {
        return $this->name;
    }

    public function setName($name) 
    {
        $this->name = $name;
    }     

    public function getArticle() 
    {
        return $this->article;
    }

    public function setArtice($article) 
    {
        $toNullFilter = new ToNull();
        $this->article = $toNullFilter->filter($article);
    }     


    public function getIid() 
    {
        return $this->iid;
    }

    public function setIid($iid) 
    {
        $toNullFilter = new ToNull();
        $this->iid = $toNullFilter->filter($iid);
    }     

    public function getProducer() 
    {
        return $this->producer;
    }

    public function setProducer($producer) 
    {
        $toNullFilter = new ToNull();
        $this->producer = $toNullFilter->filter($producer);
    }     

    public function getCountry() 
    {
        return $this->country;
    }

    public function setCountry($country) 
    {
        $toNullFilter = new ToNull();
        $this->country = $toNullFilter->filter($country);
    }     

    public function getTitle() 
    {
        return $this->title;
    }

    public function setTitle($title) 
    {
        $toNullFilter = new ToNull();
        $this->title = $toNullFilter->filter($title);
    }     

    public function getDescription() 
    {
        return $this->description;
    }

    public function setDescription($description) 
    {
        $toNullFilter = new ToNull();
        $this->description = $toNullFilter->filter($description);
    }     

    public function getImage() 
    {
        return $this->image;
    }

    public function setImage($image) 
    {
        $toNullFilter = new ToNull();
        $this->image = $toNullFilter->filter($image);
    }     

    public function getPrice() 
    {
        return $this->price;
    }

    public function setPrice($price) 
    {
        $toNullFilter = new ToNull();
        $this->price = $toNullFilter->filter($price);
    }     

    public function getCurrency() 
    {
        return $this->currency;
    }

    public function setCurrency($currency) 
    {
        $toNullFilter = new ToNull();
        $this->currency = $toNullFilter->filter($currency);
    }     

    public function getRate() 
    {
        return $this->rate;
    }

    public function setRate($rate) 
    {
        $toNullFilter = new ToNull();
        $this->rate = $toNullFilter->filter($rate);
    }     

    public function getRest() 
    {
        return $this->rest;
    }

    public function setRest($rest) 
    {
        $toNullFilter = new ToNull();
        $this->rest = $toNullFilter->filter($rest);
    }     

    public function getUnit() 
    {
        return $this->unit;
    }

    public function setUnit($unit) 
    {
        $toNullFilter = new ToNull();
        $this->unit = $toNullFilter->filter($unit);
    }     

    public function getDateCreated() 
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
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
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
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
        $supplier->addPricesettings($this);
    }    
        
}
