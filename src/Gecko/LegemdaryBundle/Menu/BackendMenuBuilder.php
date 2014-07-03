<?php

namespace Gecko\LegemdaryBundle\Menu;

use Sylius\Bundle\WebBundle\Menu\BackendMenuBuilder as Menu;
use Knp\Menu\ItemInterface;

class BackendMenuBuilder extends Menu
{
	protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
	{
		parent::addAssortmentMenu($menu, $childOptions, $section);
		
		$menu->addChild('post', array(
				'route' => 'gecko_legemdary_backend_post_index',
				'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
		))->setLabel('Posts');
	}
}