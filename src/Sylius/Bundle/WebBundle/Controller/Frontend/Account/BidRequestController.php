<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Gecko\LegemdaryBundle\Repository\DiamondBidRequestRepository;

class BidRequestController extends Controller
{
    /**
     * List orders of the current user.
     *
     * @return Response
     */
    public function indexAction()
    {
        $bidRequests = $this
            ->getBidRequestRepository()
            ->findByUser($this->getUser(), array('updatedAt' => 'desc'), false)
        ;

        $bids = array();
        foreach($bidRequests as $bidRequest)
        {
        	try {
        		$bidRequest->getOrderItem()->getVariant()->getProduct();
        		$bids[] = $bidRequest;
        	}catch(\Exception $e)
        	{}
        }
        
        return $this->render('SyliusWebBundle:Frontend/Account:Bids/index.html.twig', array(
            'bidRequests' => $bids
        ));
    }

    /**
     * Get single bid request of the current user.
     *
     * @param string $number
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showAction($id)
    {
        $bidRequest = $this->getBidRequestRepository()->findOneBy(array('id' => $id));

        if($bidRequest->isBidTimeout())
        {
        	return $this->redirect($this->generateUrl('sylius_account_bids_index'));
        }else
        {
	        return $this->render('SyliusWebBundle:Frontend/Account:Bids/show.html.twig', array(
	            'bidRequest' => $bidRequest
	        ));
        }
    }
    
    public function resendAction($id)
    {
    	$bidRequest = $this->getBidRequestRepository()->findOneBy(array('id' => $id));
    	
    	if($bidRequest->isBidTimeout())
    	{
    		$em = $this->getDoctrine()->getManager();
    		foreach($bidRequest->getDiamondBids() as $diamondBid)
    		{
    			$orderDiamondBid = $bidRequest->getOrderItem()->getDiamondBid();
    			if(!$orderDiamondBid || ($orderDiamondBid->getId() != $diamondBid->getId()))
    			{
	    			$em->remove($diamondBid);
    			}
    			else {
    				$bidRequest->getOrderItem()->setDiamondBid(null);
    				$em->remove($diamondBid);
    				//ldd($orderDiamondBid);
    			}
    		}
    		
    		$em->flush();
    		
    		$users = $this->get('sylius.repository.user')->findByRole('ROLE_SYLIUS_VENDOR');
    		foreach ($users as $user)
    		{
    			$this->get('legem.send.mailer')->sendToVendorsBidRequestedEmail($user);
    		}
    	}
    	
    	return $this->redirect($this->generateUrl('sylius_account_bids_index'));
    }
    
    public function applyAndCheckoutAction($id, $bidId)
    {
    	$bidRequest = $this->getBidRequestRepository()->findOneBy(array('id' => $id));
    	$selectedBid = $this->getBidRepository()->findOneBy(array('id' => $bidId));
    	
    	if($bidRequest && $selectedBid)
    	{
    		$bidRequest->getOrderItem()->setDiamondBid($selectedBid);
	    	$this->getDoctrine()->getManager()->flush();
	    	
	    	$_SESSION['order_to_checkout'] = $bidRequest->getOrderItem()->getOrder()->getId();
	    	
	    	return $this->redirect($this->generateUrl('sylius_checkout_start'));
    	}
    	
    	//back to bids select
    	return $this->redirect($this->generateUrl('sylius_account_bids_select', array(
    		'id' => $id
    	)));
    }

    /**
     * @return DiamondBidRequestRepository
     */
    protected function getBidRequestRepository()
    {
        return $this->get('legemdary.repository.diamond_bid_request');
    }
    
    /**
     * @return DiamondBidRepository
     */
    protected function getBidRepository()
    {
    	return $this->get('legemdary.repository.diamond_bid');
    }
}
