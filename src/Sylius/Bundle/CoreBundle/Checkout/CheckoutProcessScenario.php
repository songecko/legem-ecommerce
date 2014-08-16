<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Sylius checkout process.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CheckoutProcessScenario implements ProcessScenarioInterface
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;
    private $container;

    /**
     * Constructor.
     *
     * @param CartProviderInterface $cartProvider
     */
    public function __construct(CartProviderInterface $cartProvider, Container $container)
    {
        $this->cartProvider = $cartProvider;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ProcessBuilderInterface $builder)
    {
        $cart = $this->getOrderBidRequest();//$this->getCurrentCart();

        $builder
            ->add('security', 'sylius_checkout_security')
            ->add('addressing', 'sylius_checkout_addressing')
            ->add('shipping', 'sylius_checkout_shipping')
            ->add('payment', 'sylius_checkout_payment')
            ->add('finalize', 'sylius_checkout_finalize')
            ->add('purchase', 'sylius_checkout_purchase')
        ;

        $builder
            ->setDisplayRoute('sylius_checkout_display')
            ->setForwardRoute('sylius_checkout_forward')
            ->setRedirect('sylius_homepage')
            ->validate(function () use ($cart) {
                return !$cart->isEmpty();
            })
        ;
    }

    /**
     * Get current cart.
     *
     * @return OrderInterface
     */
    protected function getCurrentCart()
    {
        return $this->cartProvider->getCart();
    }
    
    /**
     * Get order.
     *
     * @return OrderInterface
     */
    protected function getOrderBidRequest()
    {
    	$order = $this->container->get('sylius.repository.order')->find($_SESSION['order_to_checkout']);
    	
    	return $order;
    }
}
