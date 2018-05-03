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
 * Description of App
 * @ORM\Entity(repositoryClass="\Application\Repository\ReserveRepository")
 * @ORM\Table(name="orders")
 * @author Daddy
 */
class Reserve {
    
    // Константы.
    const STATUS_NEW    = 10; // Новый.
    const STATUS_CONFIRMED   = 20; // Подтвержден.
    const STATUS_PAID   = 30; // Оплачен.
    const STATUS_SHIPPED   = 40; // Отгружен.
    const STATUS_CANCELED  = -10; // Отменен.
        
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;    
    
    /**
     * @ORM\Column(name="total")  
     */
    protected $total;    
    
    /**
     * @ORM\Column(name="comment")  
     */
    protected $comment;    
    
    /**
     * @ORM\Column(name="status")  
     */
    protected $status;    

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Supplier", inversedBy="reserves") 
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected $supplier;
    
    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="reserve") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
        
    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\BidReserve", mappedBy="reserve")
    * @ORM\JoinColumn(name="id", referencedColumnName="reserve_id")
     */
    private $bid;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->bid = new ArrayCollection();
    }
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getDateCreated() 
    {
        return $this->dateCreated;
    }

    public function getDateCreatedFormat($format) 
    {
        return date($format, strtotime($this->dateCreated));
    }

    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }     

    public function getTotal() 
    {
        return $this->total;
    }

    public function setTotal($total) 
    {
        $this->total = $total;
    }     
    
    public function getComment() 
    {
        return $this->comment;
    }

    public function setComment($comment) 
    {
        $this->comment = $comment;
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
            self::STATUS_NEW => 'Новый',
            self::STATUS_CANCELED => 'Отменен',
            self::STATUS_CONFIRMED => 'Подтвержден',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_SHIPPED => 'Отгружен',
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
        $supplier->addReserve($this);
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
 
    /**
     * Returns the array of bid assigned to this.
     * @return array
     */
    public function getBid()
    {
        return $this->bid;
    }
        
    /**
     * Assigns.
     * @param Application\Entity\BidReserve $bid
     */
    public function addBid($bid)
    {
        $this->bid[] = $bid;
    }
            
}
