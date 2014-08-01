<?php

namespace Gecko\LegemdaryBundle\Entity;

use Sylius\Component\Core\Model\OrderItemInterface;

class Diamond
{
	protected $id;
	protected $orderItem;
	
	protected $carat = 0.4;
	protected $color = "D";
	protected $clarity = "IF";
	protected $cut = "IDEAL";
	
	protected $createdAt;
	protected $updatedAt;
	
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->createdAt = new \DateTime();
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setOrderItem(OrderItemInterface $orderItem)
	{
		$this->orderItem = $orderItem;
	
		return $this;
	}
	
	public function getOrderItem()
	{
		return $this->orderItem;
	}
	
	public function setCarat($carat)
	{
		$this->carat = $carat;
	
		return $this;
	}
	
	public function getCarat()
	{
		return $this->carat;
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
	
	public function setClarity($clarity)
	{
		$this->clarity = $clarity;
	
		return $this;
	}
	
	public function getClarity()
	{
		return $this->clarity;
	}
	
	public function setCut($cut)
	{
		$this->cut = $cut;
	
		return $this;
	}
	
	public function getCut()
	{
		return $this->cut;
	}
	
	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	
		return $this;
	}
	
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	public function setUpdatedAt(\DateTime $updatedAt)
	{
		$this->updatedAt = $updatedAt;
	
		return $this;
	}
	
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
}