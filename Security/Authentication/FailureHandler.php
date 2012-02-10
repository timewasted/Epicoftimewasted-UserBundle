<?php

/**
 * This file is part of the EpicoftimewastedUserBundle package.
 *
 * Copyright (c) 2011-2012 Ryan Rogers
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Epicoftimewasted\UserBundle\Security\Authentication;

use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class FailureHandler implements AuthenticationFailureHandlerInterface
{
	/**
	 * @var RouterInterface $router
	 */
	private $router;

	/**
	 * @var UserManagerInterface $userManager
	 */
	private $userManager;

	/**
	 * @param RouterInterface $router
	 * @param UserManagerInterface $userManager
	 */
	public function __construct(RouterInterface $router, UserManagerInterface $userManager)
	{
		$this->router = $router;
		$this->userManager = $userManager;
	}

	/**
	 * {@inheritDoc}
	 */
	function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$token = $exception->getExtraInformation();
		if( $token instanceof AbstractToken ) {
			// Note that $token->getUser() returns a string here.
			$user = $this->userManager->findUserByUsername($token->getUsername());
			if( $user !== null ) {
				$user->incrementFailedLoginAttempts();
				$this->userManager->updateUser($user);
			}
		}

		$request->getSession()->set(SecurityContext::AUTHENTICATION_ERROR, $exception);
		return new RedirectResponse($this->router->generate('_security_login'));
	}
}
