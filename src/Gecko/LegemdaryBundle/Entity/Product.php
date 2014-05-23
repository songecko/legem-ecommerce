<?php

namespace Gecko\LegemdaryBundle\Entity;

use Sylius\Component\Core\Model\Product as BaseProduct;
use Symfony\Component\HttpFoundation\File\File;

class Product extends BaseProduct
{
    /**
     * Video description
     *
     * @var string
     */
    protected $videoFile;
    protected $video;
    

    public function hasVideoFile()
    {
    	return null !== $this->videoFile;
    }
    
    public function getVideoFile()
    {
    	return $this->videoFile;
    }
    
    public function setVideoFile(File $videoFile)
    {
    	$this->videoFile = $videoFile;
    
    	if ($this->video) {
    		$this->setUpdatedAt(new \DateTime('now'));
    	}
    }
    
    public function getVideo()
    {
    	return $this->video;
    }
    
    public function setVideo($video)
    {
    	$this->video = $video;
    
    	return $this;
    }
}
