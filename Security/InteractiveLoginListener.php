<?php

namespace Epicoftimewasted\UserBundle\Security;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class InteractiveLoginListener
{
	protected $userManager;

	public function __construct(UserManagerInterface $userManager)
	{
		$this->userManager = $userManager;
	}

	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		$user = $event->getAuthenticationToken()->getUser();
		if( $user instanceof EpicoftimewastedUserInterface ) {
			$user->setLastLogin(new \DateTime());
			$user->resetFailedLoginAttempts();
			$this->userManager->updateUser($user);
		}
	}
}
