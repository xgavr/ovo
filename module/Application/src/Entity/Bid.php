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
 * Description of Bid
 * @ORM\Entity(repositoryClass="\Application\Repository\OrderRepository")
 * @ORM\Table(name="bid")
 * @author Daddy
 */
class Bid {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="price")   
     */
    protected $price;

    /**
     * @ORM\Column(name="num")   
     */
    protected $num;

    /**
     * @ORM\Column(name="reserved")   
     */
    protected $reserved;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;    

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Goods", inversedBy="bid") 
     * @ORM\JoinColumn(name="good_id", referencedColumnName="id")
     */
    private $good;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="bid") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Order", inversedBy="bid") 
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\BidReserve", inversedBy="bids")
     * @ORM\JoinTable(name="bid_bid_reserve",
     *      joinColumns={@ORM\JoinColumn(name="bid_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="bid_reserve_id", referencedColumnName="id")}
     *      )
     */
   private $bidReserves;
    
   
   public function __construct() {
      $this->bidReserves = new ArrayCollection();
   }
   
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getPrice() 
    {
        return $this->price;
    }

    public function setPrice($price) 
    {
        $this->price = $price;
    }     

    public function getNum() 
    {
        return $this->num;
    }

    public function setNum($num) 
    {
        $this->num = $num;
    }     
    
    public function getReserved() 
    {
        return $this->reserved;
    }

    public function setReserved($reserved) 
    {
        $this->reserved = $reserved;
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
     * Возвращает связанный good.
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
    
    /*
     * Возвращает связанный order.
     * @return \Application\Entity\Order
     */
    
    public function getOrder() 
    {
        return $this->order;
    }

    /**
     * Задает связанный order.
     * @param \Application\Entity\Order $order
     */    
    public function setOrder($order) 
    {
        $this->order = $order;
        $order->addBid($this);
    }     

    public function getBidReserves() 
    {
      return $this->bidReserves;
    }

    /**
     * Добавляет новый bidReserve к этому bid.
     * @param $bidReserve
     */   
    public function addBidReserve($bidReserve) 
    {
        $this->bidReserves[] = $bidReserve;
    }       
    
    // Удаляет связь между этим bidReserve и заданным bid.
    public function removeBidReserveAssociation($bidReserve) 
    {
        $this->bidReseves->removeElement($bidReserve);
    }    
    
}
