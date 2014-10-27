<?php

namespace Gecko\LegemdaryBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Container;

/**
 * Listener responsible to change the redirection at the registration
 */
class RegistrationListener implements EventSubscriberInterface
{
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
		);
	}

	private function get($serviceName)
	{
		return $this->container->get($serviceName);	
	}
	
	public function onRegistrationSuccess(FormEvent $event)
	{
		try {
           	$targetUrl = $this->get('session')->get('_security.main.target_path');
           	$parsedTargetUrl = parse_url($targetUrl);
           	$targetPath = isset($parsedTargetUrl['path'])?str_replace('/app_dev.php', '', $parsedTargetUrl['path']):'';
           	$routeMatch = $this->container->get('router')->match($targetPath);
           	$route = isset($routeMatch['_route'])?$routeMatch['_route']:'';
           	if($route == 'sylius_cart_item_add')
           	{
				$event->setResponse(new RedirectResponse($targetUrl));
           	}
		}catch (\Exception $e)
		{}
	}
}