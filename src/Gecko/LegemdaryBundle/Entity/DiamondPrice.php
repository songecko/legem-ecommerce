<?php

namespace Gecko\LegemdaryBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 */
class DiamondPrice
{
    protected $id;
    protected $caratTable;
    protected $clarity;
    protected $color;
    protected $priceGuidance;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
    	$this->createdAt = new DateTime('now');
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setCaratTable($caratTable)
    {
        $this->caratTable = $caratTable;

        return $this;
    }

    public function getCaratTable()
    {
        return $this->caratTable;
    }

    public function setClarity($clarity)
    {
        $this->clarity = $clarity;

        return $this;
    }
    
    public function getClarity()
    {
    	return $this->clarity;
    }
    
    public function setColor($color)
    {
    	$this->color = $color;
    
    	return $this;
    }
    
    public function getColor()
    {
    	return $this->color;
    }
    
    public function setPriceGuidance($priceGuidance)
    {
    	$this->priceGuidance = $priceGuidance;
    
    	return $this;
    }
    
    public function getPriceGuidance()
    {
    	return $this->priceGuidance;
    }
    
    public function setCreatedAt($createdAt)
    {
    	$this->createdAt = $createdAt;
    
    	return $this;
    }
    
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
    
    public function setUpdatedAt($updatedAt)
    {
    	$this->updatedAt = $updatedAt;
    
    	return $this;
    }
    
    public function getUpdatedAt()
    {
    	return $this->updatedAt;
    }
}
