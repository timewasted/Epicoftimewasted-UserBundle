<?php

namespace Epicoftimewasted\UserBundle\Form;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Events;
use Symfony\Component\Form\Event\DataEvent;

class UpdateCanonicalFieldsListener implements EventSubscriberInterface
{
	/**
	 * @var UserManagerInterface
	 */
	private $userManager;

	public function __construct(UserManagerInterface $userManager)
	{
		$this->userManager = $userManager;
	}

	public static function getSubscribedEvents()
	{
		return Events::postBind;
	}

	public function postBind(DataEvent $event)
	{
		$user = $event->getForm()->getData();
		if( $user instanceof EpicoftimewastedUserInterface )
			$this->userManager->updateCanonicalFields($user);
	}
}
