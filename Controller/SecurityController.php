<?php

namespace Epicoftimewasted\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
	/**
	 * @Route("/login/", name="_security_login", requirements={"_method"="GET", "_scheme"="https"}),
	 * @Route("/login/", name="_security_check", requirements={"_method"="POST", "_scheme"="https"})
	 */
	public function loginAction()
	{
		if( $this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR) ) {
			$error = true;
		} else {
			$error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
			$this->get('request')->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
		}
		if( $error !== null )
			$error = 'login_authentication_failed';

		$response = $this->render('EpicoftimewastedUserBundle:Security:login.html.twig', array(
			'error' => $error,
			'last_username' => null,
//			'last_username' => $this->container->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
		));
		$response->setPrivate();
		return $response;
	}

	/**
	 * @Route("/logout/", name="_security_logout", requirements={"_scheme"="https"})
	 */
	public function logoutAction()
	{
		throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
	}
}
