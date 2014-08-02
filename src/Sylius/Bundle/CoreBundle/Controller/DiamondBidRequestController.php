<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class DiamondBidRequestController extends ResourceController
{
    public function indexByVendorAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException('Requested user does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByVendorPaginator($user, $this->config->getSorting())
        ;

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());

        return $this->render('SyliusWebBundle:Backend/Bids:index.html.twig', array(
            'user'   => $user,
            'diamond_bid_requests' => $paginator
        ));
    }
}
