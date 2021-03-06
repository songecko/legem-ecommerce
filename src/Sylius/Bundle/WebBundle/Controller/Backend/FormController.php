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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backend forms controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FormController extends Controller
{
    /**
     * Render form.
     *
     * @param Request $request
     */
    public function showAction($type, $template)
    {
        return $this->render($template, array(
            'form' => $this->createForm($type)->createView()
        ));
    }

    /**
     * Render filter form.
     *
     * @param string $type
     * @param string $template
     *
     * @return Response
     */
    public function filterAction($type, $template = 'SyliusWebBundle:Backend/Form:filter.html.twig')
    {
        return $this->render($template, array(
            'form' => $this->get('form.factory')->createNamed('criteria', $type)->createView()
        ));
    }
}
