<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class DiamondBidController extends ResourceController
{
	public function createWithBidRequestAction(Request $request, $diamond_bid_request_id)
    {
    	$diamondBidRequest = $this->get('legemdary.repository.diamond_bid_request')->findOneById($diamond_bid_request_id);
    	$vendor = $this->getUser();
    	
        $diamondBid = $this->createNew();
        $diamondBid->setCarat($diamondBidRequest->getCarat());
        $diamondBid->setColor($diamondBidRequest->getColor());
        $diamondBid->setClarity($diamondBidRequest->getClarity());
        $diamondBid->setCut($diamondBidRequest->getCut());
        $diamondBid->setVendor($vendor);
        $diamondBid->setDiamondBidRequest($diamondBidRequest);
        
        $form = $this->getForm($diamondBid);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $diamondBid = $this->domainManager->create($diamondBid);
            if (null === $diamondBid) {
                return $this->redirectHandler->redirectToIndex();
            }

            try {
	            $this->get('legem.send.mailer')->sendToUserBidMakedEmail($diamondBidRequest->getOrderItem()->getOrder()->getUser());
            }catch(Exception $e)
            {}
            
            return $this->redirectHandler->redirectTo($diamondBid);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $diamondBid,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }
}
