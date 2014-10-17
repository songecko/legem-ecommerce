<?php

namespace Gecko\LegemdaryBundle\Entity;

use FOS\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\File\File;

class DiamondBid extends Diamond
{
	protected $diamondBidRequest;
	protected $vendor;
	protected $price;
	protected $pdf;
	protected $pdfFile;
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setDiamondBidRequest(DiamondBidRequest $diamondBidRequest)
	{
		$this->diamondBidRequest = $diamondBidRequest;
		
		return $this;
	}
	
	public function getDiamondBidRequest()
	{
		return $this->diamondBidRequest;
	}
	
	public function setVendor(User $vendor)
	{
		$this->vendor = $vendor;
	
		return $this;
	}
	
	public function getVendor()
	{
		return $this->vendor;
	}
	
	public function setPrice($price)
	{
		$this->price = $price;
	
		return $this;
	}
	
	public function getPrice()
	{
		return $this->price;
	}
	
	public function getPriceFee()
	{
		return ($this->getPrice() * 5)/100;
	}
	
	public function getPricePlusFee()
	{
		return $this->getPrice()+$this->getPriceFee();
	}
	
	public function setPdf($pdf)
	{
		$this->pdf = $pdf;
	
		return $this;
	}
	
	public function getPdf()
	{
		return $this->pdf;
	}
	
	public function hasPdf()
	{
		return null !== $this->pdf;
	}
	
	public function setPdfFile(File $pdfFile)
	{
		$this->pdfFile = $pdfFile;
	
		if ($this->pdf) {
			$this->setUpdatedAt(new \DateTime('now'));
		}
		
		return $this;
	}
	
	public function getPdfFile()
	{
		return $this->pdfFile;
	}
}