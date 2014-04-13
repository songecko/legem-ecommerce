<?php

namespace Gecko\LegemdaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LegemdaryBundle:Default:index.html.twig', array('name' => $name));
    }
}
