<?php

namespace Gecko\LegemdaryBundle\Mailer;

use Sylius\Component\Core\Model\User as User;
use Symfony\Component\DependencyInjection\Container;

class SendMailer
{
	
	private $message;
	private $container;
	
	public function __construct(Container $container){
		$this->message = \Swift_Message::newInstance();
		$this->container = $container;
	}

	public function sendToVendorsBidRequestedEmail(User $user)
	{
		$fullname = $user->getFullName();
		$email = $user->getEmail();
		$view = 'GeckoLegemdaryBundle:Frontend/Mailer:bid_requested.html.twig';
		
		$message = $this->getMessage($view, $email)
			->setSubject('Legemdary - New user request a bid');
		
		$failures = $this->send($message);
		
		return $failures;
	}
	
	public function sendToUserBidMakedEmail(User $user)
	{
		$fullname = $user->getFullName();
		$email = $user->getEmail();
		$view = 'GeckoLegemdaryBundle:Frontend/Mailer:bid_maked.html.twig';
		
		$message = $this->getMessage($view, $email)
						->setSubject($fullname.', you have a new bid for your request');
		
		$failures = $this->send($message);
		
		return $failures;
	}		
	
	public function sendContactEmail($data)
	{
		$view = 'GeckoLegemdaryBundle:Frontend/Mailer:contact.html.twig';
		
		$message = $this->message
			->setSubject('Legemdary - New contact')
			->setFrom(array($data['email'] => $data['name']))
			->setTo('legemdary1@gmail.com')
			->setBody(
				$this->container->get('templating')->render($view, array('data' => $data)),
				'text/html'
			);
		
		$failures = $this->send($message);
		
		return $failures;
	}
	
	private function getMessage($view, $emailTo)
	{
		return $this->message
			->setSubject('Legemdary')
			->setFrom(array('noreply@legemdary.com' => 'Legemdary'))
			->setTo($emailTo)
			->setBody(
				$this->container->get('templating')->render($view),
				'text/html'
			);
	}

	protected function send($message)
	{
		$failures = array();
	
		$mailer = $this->container->get('mailer');
		$mailer->send($message, $failures);
	
		// manually flush the queue (because using spool)
		$spool = $mailer->getTransport()->getSpool();
		$transport = $this->container->get('swiftmailer.transport.real');
		$spool->flushQueue($transport);
	
		return $failures;
	}
}