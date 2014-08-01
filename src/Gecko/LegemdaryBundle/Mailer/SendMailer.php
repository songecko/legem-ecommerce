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

	public function sendBillRequest(User $user)
	{
		$fullname = $user->getFullName();
		$email = $user->getEmail();
		$view = 'GeckoLegemdaryBundle:Frontend/Mailer:BillRequest.html.twig';
		
		$message = $this->getMessage($view, $email)
			->setSubject($fullname.', ya estás registrado en la app del Mes del Amigo LAN!');
		
		$failures = $this->send($message);
		
		return $failures;
	}
	
	public function sendVendorResponse(User $user)
	{
		$fullname = $user->getFullName();
		$email = $user->getEmail();
		$view = 'GeckoLegemdaryBundle:Frontend/Mailer:VendorResponse.html.twig';
		
		$message = $this->getMessage($view, $email)
						->setSubject($fullname.', ya ha comenzado la promoción!');
		
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