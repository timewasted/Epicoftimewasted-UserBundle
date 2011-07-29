<?php

/**
 * FIXME: This whole class needs better, more consistent error handling.  For
 * example, I don't think HTTP exceptions are the correct choice for account
 * management errors.
 * FIXME: Account maintenance such as setting the last login time needs to be
 * handled with consistency.  For example, sometimes setting the time is done
 * in the controller, other times it's done in the form handler.
 */

namespace Epicoftimewasted\UserBundle\Controller;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

require __DIR__ . '/../Captcha/recaptchalib.php';

class UserController extends Controller
{
	/**
	 * Checks to see if the user is currently logged in or not.
	 *
	 * @return boolean
	 */
	protected function isUserLoggedIn()
	{
		$securityContext = $this->get('security.context');
		if( $securityContext->getToken() !== null && $securityContext->isGranted('ROLE_USER') && !$securityContext->isGranted('ROLE_TEMPORARY') )
			return true;
		return false;
	}

	/**
	 * Authenticate a user with Symfony Security
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @param boolean $reAuthenticate
	 * @return null
	 */
	protected function authenticateUser(EpicoftimewastedUserInterface $user, $reAuthenticate = false)
	{
		$providerKey = $this->container->getParameter('epicoftimewasted_user.firewall_name');
		$userToken = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
		if( $reAuthenticate === true )
			$token->setAuthenticated(false);
		$this->get('security.context')->setToken($userToken);
	}

	/**
	 * If the use of a captcha is enabled, return the HTML required to display
	 * the captcha.  If the captcha is disabled, return null.
	 *
	 * @return string|null
	 */
	protected function generateCaptcha()
	{
		$captchaEnabled = $this->container->getParameter('epicoftimewasted_user.captcha.enabled');
		return $captchaEnabled ? recaptcha_get_html($this->container->getParameter('epicoftimewasted_user.captcha.public_key'), null, true) : null;
	}

	/**
	 * Checks to see if the submitted captcha answer is valid.
	 * This returns true if the captcha is disabled, or it's valid.
	 *
	 * @param Request $request
	 * @return boolean
	 */
	protected function isCaptchaValid(Request $request)
	{
		$captchaEnabled = $this->container->getParameter('epicoftimewasted_user.captcha.enabled');
		$captchaPrivateKey = $this->container->getParameter('epicoftimewasted_user.captcha.private_key');

		return $captchaEnabled ? recaptcha_check_answer($captchaPrivateKey, $request->getClientIp(true), $request->get('recaptcha_challenge_field'), $request->get('recaptcha_response_field'))->is_valid : true;
	}

	/**
	 * Display the new user form, and process the submitted information.
	 *
	 * @Route("/new/", name="epicoftimewasted_user_user_new", requirements={"_method"="GET|POST", "_scheme"="https"})
	 */
	public function newAction()
	{
		/**
		 * Logged in users can not create a new user.
		 */
		if( $this->isUserLoggedIn() ) {
			$response = $this->render('EpicoftimewastedUserBundle:User:new_already_logged_in.html.twig');
			$response->setPrivate();
			$response->setMaxAge(0);
			return $response;
		}

		$form = $this->get('epicoftimewasted_user.form.user');
		$userManager = $this->get('epicoftimewasted_user.user_manager');
		$user = $userManager->createUser();
		$form->setData($user);

		$request = $this->get('request');
		if( $request->getMethod() === 'POST' ) {
			/**
			 * Before trying to process the user creation request, verify that
			 * the captcha, if enabled, is valid.
			 */
			if( $this->isCaptchaValid($request) ) {
				$form->bindRequest($request);
				if( $form->isValid() ) {
					$user = $form->getData();
					/**
					 * Handle the user's account, based on config parameters.
					 */
					if( $this->container->getParameter('epicoftimewasted_user.email.confirmation.enabled') ) {
						// Since we require confirmation, send the user an e-mail.
						$user->setAccountEnabled(false);
						$this->get('epicoftimewasted_user.mailer')->sendConfirmationEmail($user);
						$this->get('session')->set('epicoftimewasted_user_user_check_email/reason', 'confirm');
						$this->get('session')->set('epicoftimewasted_user_user_check_email/email', $user->getEmail());
						$route = 'epicoftimewasted_user_user_check_email';
					} else {
						// Since we don't require confirmation, authenticate the user.
						$user->setAccountEnabled(true);
						$user->removeConfirmationToken();
						$user->setLastLogin(new \DateTime());
						$this->authenticateUser($user);
						$route = $this->container->getParameter('epicoftimewasted_user.routes.account_active');
					}

					/**
					 * Update the user and redirect them to their destination.
					 */
					$userManager->updateUser($user);
					return new RedirectResponse($this->generateUrl($route));
				}
			}
		}

		/**
		 * Display the user registration form.
		 */
		$response = $this->render('EpicoftimewastedUserBundle:User:new.html.twig', array(
			'form' => $form->createView(),
			'captcha' => $this->generateCaptcha(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Tell the user to check their e-mail.
	 *
	 * @Route("/check-email/", name="epicoftimewasted_user_user_check_email", requirements={"_method"="GET", "_scheme"="https"})
	 */
	public function checkEmailAction()
	{
		// The user must have a reason for accessing this page stored in the session.
		if( !$this->get('session')->has('epicoftimewasted_user_user_check_email/reason') )
			throw new HttpException(403, 'This page is not meant for people with a valid, active account.');

		$reason = $this->get('session')->get('epicoftimewasted_user_user_check_email/reason');
		$this->get('session')->remove('epicoftimewasted_user_user_check_email/reason');

		// Password resets just require rendering a template.
		if( $reason === 'reset' ) {
			$response = $this->render('EpicoftimewastedUserBundle:User:resetting_password_email_sent.html.twig');
			$response->setPrivate();
			$response->setMaxAge(0);
			return $response;
		}

		// Get the e-mail address from the session and try to find an account.
		$email = $this->get('session')->get('epicoftimewasted_user_user_check_email/email');
		$this->get('session')->remove('epicoftimewasted_user_user_check_email/email');
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByEmail($email);
		if( $user === null ) {
			// FIXME: I don't know if a 404 exception is the correct response.
			throw new NotFoundHttpException('Unable to find your account information.');
		}

		// Render the template.
		$response = $this->render('EpicoftimewastedUserBundle:User:check_email.html.twig', array(
			'email' => $email,
		));
		// Because this contains user information, set the cache to private.
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Attempt to validate the token that the user provides.
	 *
	 * @Route("/confirm/{token}/", name="epicoftimewasted_user_user_confirm_account", requirements={"_method"="GET", "_scheme"="https", "token"="[A-Za-z0-9]+"})
	 */
	public function confirmAccountAction($token)
	{
		if( $this->isUserLoggedIn() ) {
			$response = $this->render('EpicoftimewastedUserBundle:User:confirm_already_logged_in.html.twig');
			$response->setPrivate();
			$response->setMaxAge(0);
			return $response;
		}

		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByConfirmationToken($token);
		if( $user === null )
			throw new HttpException(403, 'Unable to find a user with the supplied token.');

		$user->removeConfirmationToken();
		$user->setAccountEnabled(true);
		$user->setLastLogin(new \DateTime());
		$this->get('epicoftimewasted_user.user_manager')->updateUser($user);
		$this->authenticateUser($user);

		$route = $this->container->getParameter('epicoftimewasted_user.routes.account_active');
		return new RedirectResponse($this->generateUrl($route));
	}

	/**
	 * Inform the user that their account is now active.
	 *
	 * @Route("/account-active/", name="epicoftimewasted_user_user_account_active", requirements={"_method"="GET", "_scheme"="https"})
	 */
	public function accountActiveAction()
	{
		if( !$this->isUserLoggedIn() )
			throw new HttpException(403, 'This page is meant for people with a valid, active account.');

		$user = $this->get('security.context')->getToken()->getUser();
		$response = $this->render('EpicoftimewastedUserBundle:User:account_active.html.twig', array(
			'user' => $user,
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * Display the password reset request form, and process the submitted information.
	 *
	 * @Route("/reset-password-request/", name="epicoftimewasted_user_user_reset_password_request", requirements={"_method"="GET|POST", "_scheme"="https"})
	 */
	public function resetPasswordRequestAction()
	{
		$request = $this->get('request');
		if( $request->getMethod() === 'POST' ) {
			/**
			 * Before trying to process the password reset request, verify that
			 * the captcha, if enabled, is valid.
			 */
			if( $this->isCaptchaValid($request) ) {
				$user = $this->get('epicoftimewasted_user.user_manager')->findUserByUsername($request->get('username'));
				/**
				 * For the sake of preventing information disclosure, such as
				 * a list of current accounts on the system, we will not notify
				 * a user if the given username isn't valid.  While this is not
				 * ideal from a usability perspective, it is ideal from a
				 * security perspective.
				 */
				if( $user !== null && !$user->isPasswordRequestNonExpired($this->container->getParameter('epicoftimewasted_user.email.resetting_password.token_ttl')) ) {
					/**
					 * The user requests and is able to request a reset.  Send
					 * an e-mail explaining how to reset the password.
					 */
					$this->get('epicoftimewasted_user.user_manager')->createConfirmationToken($user);
					$this->get('epicoftimewasted_user.mailer')->sendResettingPasswordEmail($user);
					$user->setPasswordRequestedAt(new \DateTime());
					$this->get('epicoftimewasted_user.user_manager')->updateUser($user);
				}

				$this->get('session')->set('epicoftimewasted_user_user_check_email/reason', 'reset');
				return new RedirectResponse($this->generateUrl('epicoftimewasted_user_user_check_email'));
			}
		}

		/**
		 * Display the password reset request form.
		 */
		$username = $this->isUserLoggedIn() ? $this->get('security.context')->getToken()->getUsername() : null;
		$response = $this->render('EpicoftimewastedUserBundle:User:reset_password_request.html.twig', array(
			'username' => $username,
			'captcha' => $this->generateCaptcha(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}

	/**
	 * @Route("/reset-password/{token}/", name="epicoftimewasted_user_user_confirm_reset_password", requirements={"_method"="GET|POST", "_scheme"="https", "token"="[A-Za-z0-9]+"})
	 */
	public function confirmResetPasswordAction($token)
	{
		$user = $this->get('epicoftimewasted_user.user_manager')->findUserByConfirmationToken($token);
		if( $user === null || !$user->isPasswordRequestNonExpired($this->container->getParameter('epicoftimewasted_user.email.resetting_password.token_ttl')) )
			throw new HttpException(403, 'Unable to find a user with the supplied token.');

		$form = $this->get('epicoftimewasted_user.form.reset_password');
		$formHandler = $this->get('epicoftimewasted_user.form.handler.reset_password');
		if( $formHandler->process($user) ) {
			$this->authenticateUser($user);

			$route = $this->container->getParameter('epicoftimewasted_user.routes.account_active');
			return new RedirectResponse($this->generateUrl($route));
		}

		$response = $this->render('EpicoftimewastedUserBundle:User:reset_password.html.twig', array(
			'token' => $token,
			'form' => $form->createView(),
		));
		$response->setPrivate();
		$response->setMaxAge(0);
		return $response;
	}
}
