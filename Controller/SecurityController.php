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
		} elseif( $session !== null && $session->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = null;
		}

		/**
		 * If there was an authentication error, return a generic error message
		 * to avoid leaking anything potentially sensitive.
		 */
		if( $error !== null )
			$error = 'security.login.authentication_failed';

		/**
		 * Store the referer information for use in the login form.  Symfony
		 * can somewhat handle this for us, but it doesn't work properly if the
		 * user actually clicks on a "login" link.
		 *
		 * FIXME: Perhaps sanitize the referer to make sure it points to our site?
		 */
		$referer = $request->headers->get('Referer');

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
		));
		$response->setPrivate();
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
