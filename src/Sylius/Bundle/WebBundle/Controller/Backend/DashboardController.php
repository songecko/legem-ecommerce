<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Backend dashboard controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DashboardController extends Controller
{
    /**
     * Backend dashboard display action.
     */
    public function mainAction()
    {
        $orderRepository = $this->get('sylius.repository.order');
        $userRepository  = $this->get('sylius.repository.user');

        return $this->render('SyliusWebBundle:Backend/Dashboard:main.html.twig', array(
            'orders_count'        => $orderRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'orders'              => $orderRepository->findBy(array(), array('updatedAt' => 'desc'), 5),
            'users'               => $userRepository->findBy(array(), array('id' => 'desc'), 5),
            'registrations_count' => $userRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales'               => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales_confirmed'     => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime(), OrderInterface::STATE_CONFIRMED),
        ));
    }
    
    public function pricingMatrixAction(Request $request)
    {
    	$updatedPricingMatrix = null;
    	if($request->isMethod('POST'))
    	{
    		$updatedPricingMatrix = $request->get('pricingMatrix');
    	}
    	
    	$repository = $this->getDoctrine()->getRepository('GeckoLegemdaryBundle:DiamondPrice');
    	$prices = $repository->findAll();
    	
    	$pricingMatrix = array();
    	foreach($prices as $price)
    	{
    		$ctKey = $price->getCaratTable();
    		$clKey = $price->getClarity();
    		$coKey = $price->getColor();
    		
    		if(!isset($pricingMatrix[$ctKey]))
    			$pricingMatrix[$ctKey] = array();
    		
    		if(!isset($pricingMatrix[$ctKey][$coKey]))
    			$pricingMatrix[$ctKey][$coKey] = array();
    		
    		if($updatedPricingMatrix && $updatedPricingMatrix[$ctKey][$coKey][$clKey] != $price->getPriceGuidance())
    		{
    			$price->setPriceGuidance($updatedPricingMatrix[$ctKey][$coKey][$clKey]);
    		}
    		
    		$pricingMatrix[$ctKey][$coKey][$clKey] = $price->getPriceGuidance();
    	}
    	
    	if($updatedPricingMatrix)
    	{
    		$this->getDoctrine()->getManager()->flush();
    		return $this->redirect($this->generateUrl('sylius_backend_pricing_matrix'));
    	}
    	
    	$caratRanges = array(
    		1 => '.40 - .49 CT.', 2 => '.50 - .69 CT.', 3 => '.70 - .89 CT.',
			4 => '.90 - .99 CT.', 5 => '1.00 - 1.49 CT.', 6 => '1.50 - 1.99 CT.',
			7 => '2.00 - 3.00 CT.'
    	);
    	
    	return $this->render('SyliusWebBundle:Backend/Dashboard:pricingMatrix.html.twig', array(
    		'pricingMatrix' => $pricingMatrix,
    		'caratRanges' => $caratRanges
    	));
    }
}
