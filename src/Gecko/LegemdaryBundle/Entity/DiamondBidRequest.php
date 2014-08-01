<?php

namespace Gecko\LegemdaryBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class DiamondBidRequest extends Diamond
{
	protected $diamondBids;
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->diamondBids = new ArrayCollection();
	}
	
	public function setDiamondBids(Collection $diamondBids)
	{
		$this->diamondBids = $diamondBids;
	
		return $this;
	}
	
	public function getDiamondBids()
	{
		return $this->diamondBids;
	}
	
	public function clearDiamondBids()
	{
		$this->diamondBids->clear();
	
		return $this;
	}
	
	public function countDiamondBids()
	{
		return $this->diamondBids->count();
	}
	
	public function addDiamondBid(DiamondBid $diamondBid)
	{
		if ($this->hasDiamondBid($diamondBid)) {
			return $this;
		}
	
		$diamondBid->setDiamondBidRequest($this);
		$this->diamondBids->add($diamondBid);
	
		return $this;
	}
	
	public function removeDiamondBid(DiamondBid $diamondBid)
	{
		if ($this->hasDiamondBid($diamondBid)) {
			$diamondBid->setDiamondBidRequest(null);
			$this->diamondBids->removeElement($diamondBid);
		}
	
		return $this;
	}
	
	public function hasDiamondBid(DiamondBid $diamondBid)
	{
		return $this->diamondBids->contains($diamondBid);
	}
}