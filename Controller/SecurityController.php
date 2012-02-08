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
		 * FIXME: I'm not sure this is the best way to handle null sessions.
		 */
/*
		if( $session === null ) {
			$session = $this->get('session');
			$request->setSession($session);
		}
*/

		/**
		 * Check to see if there is an authentication error.
		 */
		if( $request->attributes->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} elseif( $session !== null && $session->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = null;
		}

		/**
		 * Check to see if this login requires a captcha, and if so, if the
		 * submitted captcha is valid.
		 *
		 * FIXME: This is part of the not yet implemented login throttling.
		 */
/*
		if( $session->has('_security.login_requires_captcha') ) {
			if( $this->get('epicoftimewasted_user.captcha')->isCaptchaValid() !== true )
				$error = true;
			$session->remove('_security.login_requires_captcha');
		}
*/

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

			/**
			 * Perform login rate throttling, if needed.
			 *
			 * FIXME: Login throttling is not yet implemented.
			 */
/*
			if( $this->get('epicoftimewasted_user.login_throttling.enabled') === true ) {
				$userManager = $this->get('epicoftimewasted_user.user_manager');
				$user = $userManager->findUserByUsername($session->get(SecurityContext::LAST_USERNAME));
				if( $user !== null ) {
					$threshold = $this->get('epicoftimewasted_user.login_throttling.threshold');
					if( $user->getFailedLoginAttempts() > $threshold ) {
						if( $this->get('epicoftimewasted_user.captcha.enabled') === true ) {
							$captcha = $this->get('epicoftimewasted_user.captcha')->generateCaptcha();
							$session->set('_security.login_requires_captcha', true);
						} else {
							// Implement a timed delay.
						}
					}
				}
			}
*/
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
		 * Render the login form.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:Security:login.html.twig', array(
			'error' => $error,
			'referer' => empty($referer) ? null : $referer,
			'last_username' => null,
//			'captcha' => isset($captcha) ? $captcha : null,
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
