<?php

namespace Epicoftimewasted\UserBundle\Controller;

use Epicoftimewasted\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends Controller
{
	/**
	 * Display and process the new user registration form.
	 */
	public function registerAction()
	{
		/**
		 * You can not register a new account if you're already logged in.
		 */
		if( $this->isUserLoggedIn() ) {
			$response = $this->render('EpicoftimewastedUserBundle:Registration:register_already_logged_in.html.twig');
			$response->setPrivate();
			$response->setMaxAge(0);
			return $response;
		}

		$form = $this->get('epicoftimewasted_user.registration.form');
		$formHandler = $this->get('epicoftimewasted_user.registration.form.handler');
		$confirmationEnabled = $this->container->getParameter('epicoftimewasted_user.registration.confirmation.enabled');

		/**
		 * Determine whether or not to process this request.
		 */
		if( $formHandler->process($confirmationEnabled) ) {
			$user = $form->getData();

			if( $confirmationEnabled ) {
				$this->get('session')->set('epicoftimewasted_user_send_confirmation_email/email', $user->getEmail());
				$route = 'epicoftimewasted_user_registration_check_email';
			} else {
				$this->authenticateUser($user);
				$route = 'epicoftimewasted_user_registration_confirmed';
			}

			$this->setFlash('epicoftimewasted_user_success', 'registration.flash.user_created');
			return new RedirectResponse($this->generateUrl($route));
		}

		/**
		 * Render the registration form.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:Registration:register.html.twig', array(
			'form' => $form->createView(),
			'captcha' => $this->get('epicoftimewasted_user.captcha')->generateCaptcha(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Tells the user to check their e-mail for confirmation instructions.
	 */
	public function checkEmailAction()
	{
		/**
		 * Verify that the user has the required session variables to access
		 * this page.
		 */
		if( !$this->get('session')->has('epicoftimewasted_user_send_confirmation_email/email') )
			throw new AccessDeniedHttpException('This page is only accessible to users who have just registered a new account.');

		$email = $this->get('session')->get('epicoftimewasted_user_send_confirmation_email/email');
		$this->get('session')->remove('epicoftimewasted_user_send_confirmation_email/email');

		/**
		 * Verify that the user exists.
		 */
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByEmail($email);
		if( $user === null )
			throw new NotFoundHttpException(sprintf('Unable to find a user with the e-mail address "%s".', $email));

		/**
		 * Render the check e-mail page.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:Registration:check_email.html.twig', array(
			'user' => $user,
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Confirm the user's confirmation token.
	 */
	public function confirmAccountAction($token)
	{
		/**
		 * You can not confirm an account if you're already logged in.
		 */
		if( $this->isUserLoggedIn() ) {
			$response = $this->render('EpicoftimewastedUserBundle:Registration:confirm_already_logged_in.html.twig');
			$response->setPrivate();
			$response->setMaxAge(0);
			return $response;
		}

		/**
		 * Verify that a user exists with the provided confirmation token.
		 */
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByConfirmationToken($token);
		if( $user === null )
			throw new NotFoundHttpException(sprintf('Unable to find a user with the confirmation token "%s".', $token));

		/**
		 * The user had a valid confirmation token, so authenticate them.
		 */
		$user->removeConfirmationToken();
		$user->setAccountEnabled(true);
		$user->setLastLogin(new \DateTime());
		$this->get('epicoftimewasted_user.user_manager')->updateUser($user);
		$this->authenticateUser($user);

		/**
		 * Redirect the user to where we send logged in users.
		 */
		$route = $this->container->getParameter('epicoftimewasted_user.registration.routes.confirmed');
		return new RedirectResponse($this->generateUrl($route));
	}

	/**
	 * Inform the user that they have successfully confirmed their account.
	 */
	public function accountConfirmedAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		if( !is_object($user) || !$user instanceof UserInterface )
			throw new AccessDeniedHttpException('This page is only accessible to users who have just confirmed their account.');

		$response = $this->render('EpicoftimewastedUserBundle:Registration:account_confirmed.html.twig', array(
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

	/**
	 * Store a message in the session.
	 */
	public function setFlash($action, $message)
	{
		$this->get('session')->setFlash($action, $message);
	}
}
