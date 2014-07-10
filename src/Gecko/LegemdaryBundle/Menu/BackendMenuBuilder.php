<?php

namespace Gecko\LegemdaryBundle\Menu;

use Sylius\Bundle\WebBundle\Menu\BackendMenuBuilder as Menu;
use Knp\Menu\ItemInterface;

class BackendMenuBuilder extends Menu
{
	protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
	{
		if($this->securityContext->isGranted('ROLE_SYLIUS_ADMIN'))
		{
			parent::addAssortmentMenu($menu, $childOptions, $section);
			
			$menu->addChild('post', array(
					'route' => 'gecko_legemdary_backend_post_index',
					'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
			))->setLabel('Blog');
		}
		
		if($this->securityContext->isGranted('ROLE_SYLIUS_VENDOR') || $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN'))
		{
			$menu->addChild('bid_request', array(
					'route' => 'sylius_backend_order_index',
					'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
			))->setLabel('Bid Request');
		}
	}
}