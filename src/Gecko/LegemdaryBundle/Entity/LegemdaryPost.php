<?php

namespace Gecko\LegemdaryBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Post
 */
class LegemdaryPost
{
    private $id;
    private $title;
    private $text;
    private $createdAt;
    private $updatedAt;
	private $image;
    private $file;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
    
    public function getText()
    {
    	return $this->text;
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

	public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
    
    public function hasFile()
    {
    	return null !== $this->file;
    }
    
    public function getFile()
    {
    	return $this->file;
    }
    
    public function setFile(File $file)
    {
    	$this->file = $file;
    
    	if ($this->image) {
    		$this->setUpdatedAt(new \DateTime('now'));
    	}
    }
}
