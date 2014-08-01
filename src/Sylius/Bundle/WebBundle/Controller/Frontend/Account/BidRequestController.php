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
            ->findByUser($this->getUser(), array('updatedAt' => 'desc'))
        ;

        return $this->render('SyliusWebBundle:Frontend/Account:Bids/index.html.twig', array(
            'bidRequests' => $bidRequests
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

        return $this->render('SyliusWebBundle:Frontend/Account:Bids/show.html.twig', array(
            'bidRequest' => $bidRequest
        ));
    }

    /**
     * @return DiamondBidRequestRepository
     */
    protected function getBidRequestRepository()
    {
        return $this->get('legemdary.repository.diamond_bid_request');
    }
}
