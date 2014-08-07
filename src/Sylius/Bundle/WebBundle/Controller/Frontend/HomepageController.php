<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Gecko\LegemdaryBundle\Entity\LegemdaryPost;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class HomepageController extends Controller
{
    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction()
    { 

    	//$this->get('legem.send.mailer')->sendBidRequest($this->getUser());
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }
    
    public function comingSoonAction()
    {
    	return $this->render('SyliusWebBundle:Frontend/Homepage:comingSoon.html.twig');
    }
    
    public function bidRequestAction()
    {
    	$currentCart = $this->container->get('sylius.cart_provider')->getCart();
    	$eventDispatcher = $this->container->get('event_dispatcher');
    	$eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_INITIALIZE, new CartEvent($currentCart));
    	
    	return $this->render('SyliusWebBundle:Frontend/Homepage:bidRequest.html.twig');
    }
    
    public function getPricingMatrixAction()
    {
    	$repository = $this->getDoctrine()->getRepository('GeckoLegemdaryBundle:DiamondPrice');
    	$prices = $repository->findAll();
    	
    	$pricingMatrix = array();
    	foreach($prices as $price)
    	{
    		$ctKey = 'ct'.$price->getCaratTable();
    		$clKey = $price->getClarity();
    		$coKey = $price->getColor();
    		
    		if(!isset($pricingMatrix[$ctKey]))
    			$pricingMatrix[$ctKey] = array();
    		
    		if(!isset($pricingMatrix[$ctKey][$clKey]))
    			$pricingMatrix[$ctKey][$clKey] = array();
    		
    		$pricingMatrix[$ctKey][$clKey][$coKey] = $price->getPriceGuidance();
    	}
    	
    	return JsonResponse::create($pricingMatrix);
    }
    
    public function blogAction()
    {
    	$repository = $this->getDoctrine()->getRepository('GeckoLegemdaryBundle:LegemdaryPost');
    	
    	$posts = $repository->findAll();
    	
    	return $this->render('SyliusWebBundle:Frontend/Blog:blog.html.twig', array(
    		'posts' => $posts
    	));
    }
    
    public function blogPostAction($id)
    {
    	$repository = $this->getDoctrine()->getRepository('GeckoLegemdaryBundle:LegemdaryPost');
    	 
    	$post = $repository->find($id);
    	
    	return $this->render('SyliusWebBundle:Frontend/Blog:blogPost.html.twig', array(
    		'post' => $post
    	));
    }
}
