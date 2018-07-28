<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\Contact;
use Doctrine\Common\Collections\Criteria;


/**
 * Description of Customer
 * @ORM\Entity(repositoryClass="\Application\Repository\SupplierRepository")
 * @ORM\Table(name="supplier")
 * @author Daddy
 */
class Supplier {
        
     // Supplier status constants.
    const STATUS_ACTIVE       = 1; // Active supplier.
    const STATUS_RETIRED      = 2; // Retired supplier.
    
    const CONTRACT_YES = 1; //Контрактный поставщик
    const CONTRACT_NO = 2; //Не контрактный поставщик
   
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
     * @ORM\Column(name="info")   
     */
    protected $info;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;

    /** 
     * @ORM\Column(name="contract")  
     */
    protected $contract;

    /**
     * @ORM\Column(name="address")   
     */
    protected $address;

    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
    
    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\Contact", mappedBy="supplier")
    * @ORM\JoinColumn(name="id", referencedColumnName="supplier_id")
     */
    private $contacts;
    
    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\Raw", mappedBy="supplier")
    * @ORM\JoinColumn(name="id", referencedColumnName="supplier_id")
     */
    private $raw;

    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\Pricesettings", mappedBy="supplier")
    * @ORM\JoinColumn(name="id", referencedColumnName="supplier_id")
     */
    private $pricesettings;    
    
    /**
    * @ORM\OneToMany(targetEntity="Application\Entity\Reserve", mappedBy="supplier")
    * @ORM\JoinColumn(name="id", referencedColumnName="supplier_id")
     */
    private $reserves;

    
    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->contacts = new ArrayCollection();
        $this->raw = new ArrayCollection();
        $this->pricesettings = new ArrayCollection();
        $this->reserves = new ArrayCollection();
        
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
        $this->name = $name;
    }     

    public function getInfo() 
    {
        return $this->info;
    }

    public function setInfo($info) 
    {
        $this->info = $info;
    }     

    public function getAddress() 
    {
        return $this->address;
    }

    public function setAddress($address) 
    {
        $this->address = $address;
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
    
    /**
     * Returns contract.
     * @return int     
     */
    public function getContract() 
    {
        return $this->contract;
    }

    /**
     * Returns possible contracts as array.
     * @return array
     */
    public static function getContractList() 
    {
        return [
            self::CONTRACT_YES => 'Контрактный',
            self::CONTRACT_NO => 'Не контрактный'
        ];
    }    
    
    /**
     * Returns user contract as string.
     * @return string
     */
    public function getContractAsString()
    {
        $list = self::getContractList();
        if (isset($list[$this->contract]))
            return $list[$this->contract];
        
        return 'Unknown';
    }    

    /**
     * Sets contract.
     * @param int $contract     
     */
    public function setContract($contract) 
    {
        $this->contract = $contract;
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
        
    /**
     * Returns the array of contacts assigned to this.
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts;
    }
        
    /**
     * Assigns.
     */
    public function addContact($contact)
    {
        $this->contacts[] = $contact;
    }
    
    /**
     * Returns the array of for legal contacts assigned to this.
     * @return array
     */
    public function getLegalContacts()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("status", Contact::STATUS_LEGAL));
        return $this->getContacts()->matching($criteria);
    }
        
    /**
     * Returns the array of for first legal contact assigned to this.
     * @return array
     */
    public function getLegalContact()
    {
        $contacts = $this->getLegalContacts();
        return $contacts[0];
    }
        
    /**
     * Returns the array of for other contacts assigned to this.
     * @return array
     */
    public function getOtherContacts()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->neq("status", Contact::STATUS_LEGAL));
        return $this->getContacts()->matching($criteria);
    }
    
    /**
     * Returns the array of contacts assigned to this.
     * @return array
     */
    public function getRaw()
    {
        return $this->raw;
    }
        
    /**
     * Assigns.
     */
    public function addRaw($raw)
    {
        $this->raw[] = $raw;
    }
    
    /**
     * Returns the array of contacts assigned to this.
     * @return array
     */
    public function getPricesettings()
    {
        return $this->pricesettings;
    }
        
    /**
     * Assigns.
     */
    public function addPricesettings($pricesettings)
    {
        $this->pricesettings[] = $pricesettings;
    }
    
    /**
     * Returns the array of reserve assigned to this.
     * @return array
     */
    public function getReserves()
    {
        return $this->reserves;
    }
        
    /**
     * Assigns.
     */
    public function addReserve($reserve)
    {
        $this->reserves[] = $reserve;
    }
        
    
}
