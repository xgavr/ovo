<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;

/**
 * Description of Rawprice
 * @ORM\Entity(repositoryClass="\Application\Repository\RawRepository")
 * @ORM\Table(name="rawprice")
 * @author Daddy
 */
class Rawprice {
           
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="rawdata")   
     */
    protected $rawdata;

    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
    
    /**
     * @ORM\Column(name="article")   
     */
    protected $article;

    /**
     * @ORM\Column(name="producer")   
     */
    protected $producer;

    /**
     * @ORM\Column(name="country")   
     */
    protected $country;

    /**
     * @ORM\Column(name="goodname")   
     */
    protected $goodname;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\Raw", inversedBy="rawprice") 
     * @ORM\JoinColumn(name="raw_id", referencedColumnName="id")
     */
    private $raw;
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\UnknownProducer", inversedBy="rawprice") 
     * @ORM\JoinColumn(name="unknown_producer_id", referencedColumnName="id")
     */
    private $unknownProducer;
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Goods", inversedBy="rawprice") 
     * @ORM\JoinColumn(name="good_id", referencedColumnName="id")
     */
    private $good;
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     
    
    public function getArticle() 
    {
        return $this->article;
    }

    public function setArticle($article) 
    {
        $this->article = (string) $article;
    }     
    
    public function getProducer() 
    {
        return $this->producer;
    }

    public function setProducer($producer) 
    {
        $this->producer = (string) $producer;
    }     
    
    public function getCountry() 
    {
        return $this->country;
    }

    public function setCountry($country) 
    {
        $this->country = (string) $country;
    }     
    
    public function getGoodname() 
    {
        return $this->goodname;
    }

    public function setGoodname($goodname) 
    {
        $this->goodname = (string) $goodname;
    }     
    
    public function getDescription() 
    {
        return $this->description;
    }

    public function setDescription($description) 
    {
        $this->description = (string) $description;
    }     

    public function getImage() 
    {
        return $this->image;
    }

    public function setImage($image) 
    {
        $this->image = (string) $image;
    }     

    public function getPrice() 
    {
        return $this->price;
    }

    public function setPrice($price) 
    {
        $this->price = (float) $price;
    }     
    
    public function getCurrency() 
    {
        return $this->currency;
    }

    public function setCurrency($currency) 
    {
        $this->currency = (string) $currency;
    }     

    public function getRate() 
    {
        return $this->rate;
    }

    public function setRate($rate) 
    {
        $this->rate = (float) $rate;
    }     

    public function getRest() 
    {
        return $this->rest;
    }

    public function setRest($rest) 
    {
        $this->rest = (string) $rest;
    }     

    public function getUnit() 
    {
        return $this->unit;
    }

    public function setUnit($unit) 
    {
        $this->unit = $unit;
    }     

    public function getRawdata() 
    {
        return $this->rawdata;
    }

    public function getRawdataAsArray() 
    {
        return Json::decode($this->rawdata);
    }

    public function setRawdata($rawdata) 
    {
        $this->rawdata = $rawdata;
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
     * Возвращает связанный raw.
     * @return \Application\Entity\Raw
     */    
    public function getRaw() 
    {
        return $this->raw;
    }

    /**
     * Задает связанный raw.
     * @param \Application\Entity\Raw $raw
     */    
    public function setRaw($raw) 
    {
        $this->raw = $raw;
        $raw->addRawprice($this);
    }     
    
    /*
     * Возвращает связанный raw.
     * @return \Application\Entity\UnknownProducer
     */
    
    public function getUnknownProducer() 
    {
        return $this->unknownProducer;
    }

    /**
     * Задает связанный raw.
     * @param \Application\Entity\UnknownProducer $unknownProducer
     */    
    public function setUnknownProducer($unknownProducer) 
    {
        $this->unknownProducer = $unknownProducer;
        $unknownProducer->addRawprice($this);
    }
    
    /*
     * Возвращает связанный raw.
     * @return \Application\Entity\Goods
     */
    
    public function getGood() 
    {
        return $this->good;
    }

    /**
     * Задает связанный good.
     * @param \Application\Entity\Goods $good
     */    
    public function setGood($good) 
    {
        $this->good = $good;
        if ($good){
            $good->addRawprice($this);
        }    
    }     
}
