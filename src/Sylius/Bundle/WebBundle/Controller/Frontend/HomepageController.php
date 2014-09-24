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
use Sylius\Bundle\WebBundle\Form\ContactFormType;

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
    	
    	$users = $this->get('sylius.repository.user')->findByRole('ROLE_SYLIUS_VENDOR');
    	foreach ($users as $user)
    	{
    		$this->get('legem.send.mailer')->sendToVendorsBidRequestedEmail($user);
    	}
    	
    	return $this->render('SyliusWebBundle:Frontend/Homepage:bidRequest.html.twig');
    }
    
    public function diamondEducationAction(Request $request)
    {
		return $this->render('SyliusWebBundle:Frontend/Homepage:diamondEducation.html.twig');    	
	}
	
	public function diamondGradingAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:diamondGrading.html.twig');
	}
	
	public function ringStylesAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:ringStyles.html.twig');
	}
	
	public function diamondCertificatesAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:diamondCertificates.html.twig');
	}
	
	public function helpAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:help.html.twig');
	}
	
	public function howItWorksAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:howItWorks.html.twig');
	}
	
	public function ourStoryAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:ourStory.html.twig');
	}
	
	public function ourSocialCommitmentAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:ourSocialCommitment.html.twig');
	}
	
	public function ringSizerAction(Request $request)
	{
		return $this->render('SyliusWebBundle:Frontend/Homepage:ringSizer.html.twig');
	}
	
	public function contactAction(Request $request)
	{
		$form = $this->createForm(new ContactFormType());
		
		if ($request->isMethod('POST')) 
		{
			$form->bind($request);
		
			if ($form->isValid()) 
			{
				$this->get('legem.send.mailer')->sendContactEmail($form->getData());
		
				$request->getSession()->getFlashBag()->add('success', 'Your email has been sent succesfully!');
				return $this->redirect($this->generateUrl('sylius_contact'));
			}
		}
		
		return $this->render('SyliusWebBundle:Frontend/Homepage:contact.html.twig', array(
			'form' => $form->createView()
		));
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
