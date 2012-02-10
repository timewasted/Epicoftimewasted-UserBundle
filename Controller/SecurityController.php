<?php

namespace Epicoftimewasted\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
	public function loginAction()
	{
		$request = $this->get('request');
		$session = $request->getSession();

		/**
		 * Check to see if there is an authentication error.
		 */
		if( $request->attributes->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} elseif( $session->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = null;
		}

		/**
		 * Perform error handling tasks.
		 */
		if( $error !== null ) {
			/**
			 * First, replace the error message with something generic to
			 * avoid potentially leaking sensitive information.
			 */
			$error = 'security.login.authentication_failed';

			/**
			 * Reload the referer URL so that we can send the user back to the
			 * correct page even after a failed authorization attempt.
			 */
			if( $session->has('_security.login_referer_url') ) {
				$referer = $session->get('_security.login_referer_url');
				$session->remove('_security.login_referer_url');
			}
		}

		/**
		 * If we don't have a referer saved from a previous login attempt, get
		 * the referer for this request.  This will allow us to redirect back
		 * to the proper page after a successful login attempt.  Symfony can
		 * somewhat handle this for us, but by doing it manually we get a much
		 * larger degree of control.
		 */
		if( !isset($referer) )
			$referer = $request->headers->get('Referer');

		/**
		 * Attempt to sanitize the referer a bit.
		 *
		 * FIXME: Find a way to verify that the referer is from our domain name.
		 */
		if( $referer == $this->generateUrl('_security_login', array(), true) )
			$referer = null;

		/**
		 * Store the referer in the session.
		 */
		if( !empty($referer) )
			$session->set('_security.login_referer_url', $referer);

		/**
		 * Get the last username the user attempted to login as.
		 */
//		$lastUsername = $session === null ? null : $session->get(SecurityContext::LAST_USERNAME);

		/**
		 * Perform login attempt throttling.
		 */
		if( $this->container->getParameter('epicoftimewasted_user.security.login_throttling.enabled') === true ) {
			$userManager = $this->get('epicoftimewasted_user.user_manager');
			$user = $userManager->findUserByUsername($session->get(SecurityContext::LAST_USERNAME));
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
						$captcha = $this->get('epicoftimewasted_user.captcha')->generateCaptcha();
					} else {
						/**
						 * FIXME: Implement a timed delay.
						 */
					}
				}
			}
		}

		/**
		 * Render the login form.
		 */
		$csrfToken = $this->get('form.csrf_provider')->generateCsrfToken('authenticate');
		$response = $this->render('EpicoftimewastedUserBundle:Security:login.html.twig', array(
			'csrf_token' => $csrfToken,
			'error' => $error,
			'referer' => empty($referer) ? null : $referer,
			'last_username' => null,
			'captcha' => isset($captcha) ? $captcha : null,
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	public function loginCheckAction()
	{
		throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
	}

	public function logoutAction()
	{
		throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
	}
}
