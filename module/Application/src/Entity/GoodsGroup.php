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
 * Description of Producer
 * @ORM\Entity(repositoryClass="\Application\Repository\GoodsRepository")
 * @ORM\Table(name="goods_group")
 * @author Daddy
 */
class GoodsGroup {
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
     * @ORM\Column(name="description")   
     */
    protected $description;
    
   /**
    * @ORM\OneToMany(targetEntity="\Application\Entity\Goods", mappedBy="group")
    * @ORM\JoinColumn(name="id", referencedColumnName="goods_group_id")
   */
   private $goods;


   public function __construct() {
      $this->goods = new ArrayCollection();
   }


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
        $this->name = trim($name);
    }     

    public function getDescription() 
    {
        return $this->description;
    }

    public function setDescription($description) 
    {
        $this->description = $description;
    }     


    /**
     * Возвращает goods для этого group.
     * @return array
     */   
   public function getGoods() {
      return $this->goods;
   }    
   
    /**
     * Добавляет новый goods к этому group.
     * @param $goods
     */   
    public function addGood($good) 
    {
        $this->goods[] = $good;
    }   
    
}
