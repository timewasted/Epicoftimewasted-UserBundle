<?php

namespace Epicoftimewasted\UserBundle\Controller;

use Epicoftimewasted\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ResettingController extends Controller
{
	/**
	 * Displays the password reset request form.
	 */
	public function requestAction()
	{
		/**
		 * Check the session to see if there are any errors.
		 */
		$session = $this->get('session');
		if( $session->has('epicoftimewasted_user_resetting_request_error') ) {
			$error = $session->get('epicoftimewasted_user_resetting_request_error');
			$session->remove('epicoftimewasted_user_resetting_request_error');
		}

		/**
		 * Display the password reset request form.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:Resetting:request.html.twig', array(
			'error' => isset($error) ? $error : null,
			'username' => $this->isUserLoggedIn() ? $this->get('security.context')->getToken()->getUsername() : null,
			'captcha' => $this->get('epicoftimewasted_user.captcha')->generateCaptcha(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Processes the password reset request form and sends an e-mail with reset
	 * instructions upon success.
	 */
	public function sendEmailAction()
	{
		/**
		 * Verify that the captcha is valid, and if not, redirect back to the
		 * request form page.
		 */
		if( !$this->get('epicoftimewasted_user.captcha')->isCaptchaValid() ) {
			$this->get('session')->set('epicoftimewasted_user_resetting_request_error', 'resetting.request.invalid_captcha');
			return new RedirectResponse($this->generateUrl('epicoftimewasted_user_resetting_request'));
		}

		/**
		 * For the sake of preventing information disclosure, such as a list of
		 * current accounts on the system, we will not notify a user if the
		 * given username isn't valid.  While this is not ideal from a usability
		 * perspective, it is ideal from a security perspective.
		 */
		$username = $this->get('request')->request->get('username');
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByUsername($username);
		if( $user !== null && !$user->isPasswordRequestNonExpired($this->container->getParameter('epicoftimewasted_user.resetting.token_ttl')) ) {
			/**
			 * The user is able to request a password, so send an e-mail,
			 * explaining how to reset their password.
			 */
			$this->get('epicoftimewasted_user.user_manager')->createConfirmationToken($user);
			$this->get('epicoftimewasted_user.mailer')->sendResettingPasswordEmail($user);
			$user->setPasswordRequestedAt(new \DateTime());
			$this->get('epicoftimewasted_user.user_manager')->updateUser($user);
		}

		/**
		 * Redirect to the check e-mail page.
		 */
		return new RedirectResponse($this->generateUrl('epicoftimewasted_user_resetting_check_email'));
	}

	/**
	 * Tells the user to check their e-mail for password reset instructions.
	 */
	public function checkEmailAction()
	{
		$response = $this->render('EpicoftimewastedUserBundle:Resetting:check_email.html.twig');
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Display and process the password reset form.
	 */
	public function resetAction($token)
	{
		/**
		 * Verify that a user exists with the supplied confirmation token.
		 */
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByConfirmationToken($token);
		if( $user === null )
			throw new NotFoundHttpException(sprintf('Unable to find a user with the confirmation token "%s".', $token));

		/**
		 * If the token is expired, redirect to the request page.
		 */
		if( !$user->isPasswordRequestNonExpired($this->container->getParameter('epicoftimewasted_user.resetting.token_ttl')) )
			return new RedirectResponse($this->generateUrl('epicoftimewasted_user_resetting_request'));

		/**
		 * Determine whether or not to process the request.
		 */
		$form = $this->get('epicoftimewasted_user.resetting.form');
		$formHandler = $this->get('epicoftimewasted_user.resetting.form.handler');
		if( $formHandler->process($user) ) {
			$this->authenticateUser($user);

			$route = $this->container->getParameter('epicoftimewasted_user.resetting.routes.reset_success');
			return new RedirectResponse($this->generateUrl($route));
		}

		/**
		 * Display the password reset form.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:Resetting:reset.html.twig', array(
			'token' => $token,
			'form' => $form->createView(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Inform the user that the password reset was a success.
	 */
	public function resetSuccessAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		if( !is_object($user) || !$user instanceof UserInterface )
			throw new AccessDeniedHttpException('This page is only accessible to users who have just reset their password.');

		$response = $this->render('EpicoftimewastedUserBundle:Resetting:reset_success.html.twig', array(
			'user' => $user,
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Checks to see if the user is currently logged in or not.
	 *
	 * @return boolean
	 */
	public function isUserLoggedIn()
	{
		$securityContext = $this->get('security.context');
		if( $securityContext->getToken() !== null && $securityContext->isGranted('ROLE_USER') && !$securityContext->isGranted('ROLE_TEMPORARY') )
			return true;
		return false;
	}

	/**
	 * Authenticate a user with Symfony security.
	 *
	 * @param UserInterface $user
	 */
	public function authenticateUser(UserInterface $user)
	{
		$providerKey = $this->container->getParameter('epicoftimewasted_user.firewall_name');
		$token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

		$this->get('security.context')->setToken($token);
	}
}
