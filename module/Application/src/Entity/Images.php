<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Images
 * @ORM\Entity(repositoryClass="\Application\Repository\GoodsRepository")
 * @ORM\Table(name="image")
 * @author Daddy
 */
class Images {
    
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
     * @ORM\Column(name="path")   
     */
    protected $path;

    /*
    * @ORM\ManyToOne(targetEntity="Application\Entity\Goods", inversedBy="images")
    * @ORM\JoinColumn(name="good_id", referencedColumnName="id")    
    * 
    */
    protected $good;
    
     
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

    public function getPath() 
    {
        return $this->path;
    }

    public function setPath($path) 
    {
        $this->path = $path;
    }     
    
    /*
     * Возвращает связанный товар.
     * @return \Application\Entity\Goods
     */
    public function getGood() 
    {
        return $this->good;
    }
    
    /**
     * Задает связанный товар.
     * @param \Application\Entity\Goods $good
     */
    public function setGood($good) 
    {
        $this->good = $good;
        $good->addImage($this);
    }    
}
