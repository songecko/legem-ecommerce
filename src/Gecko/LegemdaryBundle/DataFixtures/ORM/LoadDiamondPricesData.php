<?php

namespace Gecko\LegemdaryBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\DataFixture;
use Gecko\LegemdaryBundle\Entity\DiamondPrice;

class LoadDiamondPricesData extends DataFixture
{	
    public function load(ObjectManager $manager)
    {
    	$caratTables = array(1, 2, 3, 4, 5, 6);
    	$colors = array('D', 'E', 'F', 'G', 'H', 'I', 'J');
    	$claritys = array('IF', 'VVS1', 'VVS2', 'VS1', 'VS2');
    	
    	foreach ($caratTables as $caratTable)
    	{
	        foreach ($claritys as $clarity) 
	        {
	        	foreach ($colors as $color)
	        	{
	            	$diamondPrice = new DiamondPrice();
	            	$diamondPrice->setCaratTable($caratTable);
	            	$diamondPrice->setClarity($clarity);
	            	$diamondPrice->setColor($color);
	            	$diamondPrice->setPriceGuidance($this->faker->numberBetween(10, 500));
	            	
		            $manager->persist($diamondPrice);
	        	}
	        }
	    }
	    
        $manager->flush();
    }
    
    public function getOrder()
    {
    	return 8;
    }
}
