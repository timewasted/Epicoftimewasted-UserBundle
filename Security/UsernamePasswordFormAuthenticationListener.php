<?php

namespace Epicoftimewasted\UserBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener as BaseListener;

/**
 * FIXME: If UserBundle is the only bundle that is extending the
 * security.authentication.listener.form service, this will work fine.  If
 * anything else attempts to extend the service, things become problematic.
 *
 * Considering the sole purpose of UserBundle is to handle tasks such as
 * authentication, any other class that needs to extend
 * UsernamePasswordFormAuthenticationListener should extend this class, as
 * opposed to the BaseListener.  Also, classes should implement setContainer()
 * so that this class can do its work.
 *
 * In a perfect world, Symfony would have a pre-authentication event that
 * could be handled to do tasks before actually attempting authentication,
 * so that we wouldn't have to use hacks such as this one.
 */
class UsernamePasswordFormAuthenticationListener extends BaseListener
{
	protected $container;

	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	protected function attemptAuthentication(Request $request)
	{
		if( $this->container === null ) {
			/**
			 * If the container is null, we can not do any login throttling
			 * because we simply have no clean way to get access to the
			 * services and variables that we need to do our work.
			 *
			 * FIXME: Should this fail in some noticable way?
			 */
			return parent::attemptAuthentication($request);
		}

		/**
		 * Perform login attempt throttling.
		 */
		if( $this->container->getParameter('epicoftimewasted_user.security.login_throttling.enabled') === true ) {
			$userManager = $this->container->get('epicoftimewasted_user.user_manager');
			$user = $userManager->findUserByUsername($request->getSession()->get(SecurityContext::LAST_USERNAME));
			if( $user !== null ) {
				/**
				 * See if the user has enough failed login attempts to trigger
				 * throttling.  Note that this will be triggered regardless of
				 * time elapsed between events.  So, 3 failed logins over 3
				 * seconds is treated the same as 3 failed logins over 3 months.
				 */
				$throttlingThreshold = $this->container->getParameter('epicoftimewasted_user.security.login_throttling.threshold');
				if( $user->getFailedLoginAttempts() >= $throttlingThreshold ) {
					/**
					 * Determine the throttling method to use.
					 */
					if( $this->container->getParameter('epicoftimewasted_user.captcha.enabled') === true ) {
						$captcha = $this->container->get('epicoftimewasted_user.captcha');
						if( !$captcha->isCaptchaValid() ) {
							/**
							 * FIXME: $user->incrementFailedLoginAttempts()?
							 */
							throw new AuthenticationException('Captcha input was incorrect.');
						}
					} else {
						/**
						 * FIXME: Implement a timed delay.
						 */
					}
				}
			}
		}

		return parent::attemptAuthentication($request);
	}
}
