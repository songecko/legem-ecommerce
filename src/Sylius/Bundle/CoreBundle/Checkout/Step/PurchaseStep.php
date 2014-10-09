<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Buzz\Client\Curl;
use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Registry\RegistryInterface;
use Payum\Paypal\ProCheckout\Nvp\PaymentFactory as PaypalProPaymentFactory;
use Payum\Paypal\ProCheckout\Nvp\Api as PaypalProApi;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Payment\SyliusPaymentEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Payum\Paypal\ProCheckout\Nvp\Bridge\Buzz\Request;
use Sylius\Component\Payment\Model\PaymentInterface;

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getOrderBidRequest();//$this->getCurrentCart();

        if($order->getPayment()->getMethod()->getGateway() == 'paypal_pro')
        {
        	$api = new PaypalProApi(
				new Curl,
				array(
		        	'username' => $this->container->getParameter('paypal.pro_checkout.username'),
					'password' => $this->container->getParameter('paypal.pro_checkout.password'),
					'partner' => $this->container->getParameter('paypal.pro_checkout.partner'),
					'vendor' => $this->container->getParameter('paypal.pro_checkout.vendor'),
					'sandbox' => $this->container->getParameter('paypal.pro_checkout.sandbox'),
        	 ));
        	
        	$request = new Request();
        	$request->setField('AMT', $order->getTotal());
        	$request->setField('CREATESECURETOKEN', 'Y');
        	$request->setField('SECURETOKENID', md5(time()));
        	
        	$response = $api->doPayment($request)->toArray();
        	
        	return $this->render('SyliusWebBundle:Frontend/Checkout/Step:purchase_paypalpro.html.twig', array(
        		'iframe_src'   => $this->getPaypalProIframeSrc($response['SECURETOKENID'], $response['SECURETOKEN']),
        	));
        	
        }else {
        
	        $captureToken = $this->getTokenFactory()->createCaptureToken(
	            $order->getPayment()->getMethod()->getGateway(),
	            $order,
	            'sylius_checkout_forward',
	            array('stepName' => $this->getName())
	        );
	        
	        $url = $captureToken->getTargetUrl();
	        
	        return new RedirectResponse($url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
    	$order = $this->getOrderBidRequest();
    	$gateway = $order->getPayment()->getMethod()->getGateway();
    	
    	if($gateway == 'paypal_pro')
    	{
    		$paymentStatus = PaymentInterface::STATE_COMPLETED;
    	}else
    	{
	        $token = $this->getHttpRequestVerifier()->verify($this->getRequest());
	        $this->getHttpRequestVerifier()->invalidate($token);
	
	        $status = new StatusRequest($token);
	        $this->getPayum()->getPayment($token->getPaymentName())->execute($status);
	
	        /** @var OrderInterface $order */
	        $order = $status->getModel();
	
	        if (!$order instanceof OrderInterface) {
	            throw new \RuntimeException(sprintf('Expected order to be set as model but it is %s', get_class($order)));
	        }
	        
	        $paymentStatus = $status->getStatus();
    	}

        $payment = $order->getPayment();
        $previousState = $order->getPayment()->getState();
        $payment->setState($paymentStatus);

        if ($previousState !== $payment->getState()) {
            $this->dispatchEvent(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }

        $this->getDoctrine()->getManager()->flush();

        if ($previousState !== $payment->getState()) {
            $this->dispatchEvent(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }

        $event = new PurchaseCompleteEvent($order->getPayment());
        $this->dispatchEvent(SyliusCheckoutEvents::PURCHASE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->complete();
    }
    
    public function getPaypalEndpoint($sandbox = false)
    {
    	$host = $sandbox ? 'pilot-payflowlink.paypal.com' : 'payflowlink.paypal.com';
    	
    	return sprintf(
    			'https://%s/',
    			$host
    	);
    }
    
    public function getPaypalProIframeSrc($tokenId, $token)
    {
    	$sandbox = $this->container->getParameter('paypal.pro_checkout.sandbox');
    	
    	$mode = $sandbox?'TEST':'LIVE';
    	
    	return $this->getPaypalEndpoint($sandbox).
    		'?MODE='.$mode.'&SECURETOKENID='.$tokenId.'&SECURETOKEN='.$token;
    }
    
    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return TokenFactory
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    protected function getHttpRequestVerifier()
    {
        return $this->get('payum.security.http_request_verifier');
    }
}
