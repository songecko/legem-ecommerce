<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Frontend menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FrontendMenuBuilder extends MenuBuilder
{
    /**
     * Currency repository.
     *
     * @var RepositoryInterface
     */
    protected $exchangeRateRepository;

    /**
     * Taxonomy repository.
     *
     * @var RepositoryInterface
     */
    protected $taxonomyRepository;

    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Money extension.
     *
     * @var SyliusMoneyExtension
     */
    protected $moneyExtension;

    /**
     * Constructor.
     *
     * @param FactoryInterface         $factory
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RepositoryInterface      $exchangeRateRepository
     * @param RepositoryInterface      $taxonomyRepository
     * @param CartProviderInterface    $cartProvider
     * @param SyliusMoneyExtension     $moneyExtension
     */
    public function __construct(
        FactoryInterface         $factory,
        SecurityContextInterface $securityContext,
        TranslatorInterface      $translator,
        EventDispatcherInterface $eventDispatcher,
        RepositoryInterface      $exchangeRateRepository,
        RepositoryInterface      $taxonomyRepository,
        CartProviderInterface    $cartProvider,
        SyliusMoneyExtension     $moneyExtension
    )
    {
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher);

        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->cartProvider = $cartProvider;
        $this->moneyExtension = $moneyExtension;
    }

    /**
     * Builds frontend main menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
        	'attributes' => array(
        		'class' => 'dropdown'
        	),
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right'            	
            )
        ));
        
        $taxonomies = $this->taxonomyRepository->findAll();        
        foreach ($taxonomies as $taxonomy) {
        	$child = $menu->addChild("Browse Rings", array(
        		'uri' => '#',
        		'attributes' => array('class' => 'dropdown'),
        		'childrenAttributes' => array('class' => 'dropdown-menu'),
        		'linkAttributes'    => array(
        				'class' => 'dropdown-toggle', 
        				'data-toggle' => 'dropdown'
        		),
        		'labelAttributes' => array(
        				'icon' => 'fa fa-chevron-down'
        		)
        	));
        
        	$this->createTaxonomiesMenuNode($child, $taxonomy->getRoot());
        }

        $menu->addChild('How it works', array(
        		/*'route' => 'fos_user_security_login',*/
        		'uri' => '#'
        ));
        
        $menu->addChild('Diamond education', array(
        		/*'route' => 'fos_user_security_login',*/
        		'uri' => '#'
        ));
        
        /*if ($this->cartProvider->hasCart()) {
            $cart = $this->cartProvider->getCart();
            $cartTotals = array('items' => $cart->getTotalItems(), 'total' => $cart->getTotal());
        } else {
            $cartTotals = array('items' => 0, 'total' => 0);
        }*/

        /*$menu->addChild('cart', array(
            'route' => 'sylius_cart_summary',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
                '%items%' => $cartTotals['items'],
                '%total%' => $this->moneyExtension->formatPrice($cartTotals['total'])
            ))),
            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
        ))->setLabel($this->translate('sylius.frontend.menu.main.cart', array(
            '%items%' => $cartTotals['items'],
            '%total%' => $this->moneyExtension->formatPrice($cartTotals['total'])
        )));*/

        return $menu;
    }

    /**
     * Builds frontend currency menu.
     *
     * @return ItemInterface
     */
    public function createCurrencyMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        foreach ($this->exchangeRateRepository->findAll() as $exchangeRate) {
            $menu->addChild($exchangeRate->getCurrency(), array(
                'route' => 'sylius_currency_change',
                'routeParameters' => array('currency' => $exchangeRate->getCurrency()),
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.currency', array('%currency%' => $exchangeRate->getCurrency()))),
            ))->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($exchangeRate->getCurrency()));
        }

        return $menu;
    }

    /**
     * Builds frontend taxonomies menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createTaxonomiesMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header'),
        );

        $taxonomies = $this->taxonomyRepository->findAll();

        foreach ($taxonomies as $taxonomy) {
            $child = $menu->addChild($taxonomy->getName(), $childOptions);

            if ($taxonomy->getRoot()->hasPath()) {
                $child->setLabelAttribute('data-image', $taxonomy->getRoot()->getPath());
            }

            $this->createTaxonomiesMenuNode($child, $taxonomy->getRoot());
        }

        return $menu;
    }

    private function createTaxonomiesMenuNode(ItemInterface $menu, TaxonInterface $taxon)
    {
        foreach ($taxon->getChildren() as $child) {
            $childMenu = $menu->addChild($child->getName(), array(
                'route'           => 'sylius_product_index_by_taxon',
                'routeParameters' => array('permalink' => $child->getPermalink()),
                'labelAttributes' => array('icon' => 'icon-angle-right')
            ));
            if ($child->getPath()) {
                $childMenu->setLabelAttribute('data-image', $child->getPath());
            }

            $this->createTaxonomiesMenuNode($childMenu, $child);
        }
    }

    /**
     * Builds frontend social menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createSocialMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills pull-right'
            )
        ));

        $menu->addChild('github', array(
            'uri' => 'https://github.com/Sylius',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.github')),
            'labelAttributes' => array('icon' => 'icon-github-sign icon-large', 'iconOnly' => true)
        ));
        $menu->addChild('twitter', array(
            'uri' => 'https://twitter.com/Sylius',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.twitter')),
            'labelAttributes' => array('icon' => 'icon-twitter-sign icon-large', 'iconOnly' => true)
        ));
        $menu->addChild('facebook', array(
            'uri' => 'http://facebook.com/SyliusEcommerce',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.facebook')),
            'labelAttributes' => array('icon' => 'icon-facebook-sign icon-large', 'iconOnly' => true)
        ));

        return $menu;
    }

    /**
     * Creates user account menu
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createAccountMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-account navbar-nav navbar-right hidden-xs'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header')
        );

        //$child = $menu->addChild($this->translate('sylius.account.title'), $childOptions);

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_USER')) 
        {		
			$menu->addChild('account', array(
            	'route' => 'sylius_account_homepage',
            	'linkAttributes' => array('title' => "My account", 'class' => 'signup'),
        	))->setLabel("My account");
        
        	$menu->addChild('orders', array(
        			'route' => 'sylius_account_order_index',
        			'linkAttributes' => array('title' => "My bids", 'class' => 'signup'),
        	))->setLabel("My bids");

        	if($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) 
        	{        	
        		$routeParams = array(
        				'route' => 'sylius_backend_dashboard',
        				'linkAttributes' => array('title' => "Administration", 'target' => '_blank', 'class' => 'signup'),
        		);
        	
        		if($this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN')) 
        		{
        			$routeParams = array_merge($routeParams, array(
        					'route' => 'sylius_switch_user_return',
        					'routeParameters' => array(
        							'username' => $this->securityContext->getToken()->getUsername(),
        							'_switch_user' => '_exit'
        					)
        			));
        		}
        	
        		$menu->addChild('administration', $routeParams)->setLabel("Administration");
        	}
        	
        	$menu->addChild('logout', array(
        			'route' => 'fos_user_security_logout',
        			'linkAttributes' => array('title' => "Logout", 'class' => 'signup')
        	))->setLabel("Logout");
        	
		} else {
			$menu->addChild('login', array(
				'route' => 'fos_user_security_login',
				'linkAttributes' => array('title' => "Login", 'class' => 'signup'),
			))->setLabel("Login");
        		
			$menu->addChild('register', array(
				'route' => 'fos_user_registration_register',
				'linkAttributes' => array('title' => "Sign up free", 'class' => 'signup'),
			))->setLabel("Sign up free");
		}
		
        /*$menu->addChild('profile', array(
            'route' => 'fos_user_profile_edit',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.profile')),
            'labelAttributes' => array('icon' => 'icon-info-sign', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.profile'));

        $menu->addChild('password', array(
            'route' => 'fos_user_change_password',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.password')),
            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.password'));*/

        /*$menu->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.addresses')),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.addresses'));*/

        return $menu;
    }
}
