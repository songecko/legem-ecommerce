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
