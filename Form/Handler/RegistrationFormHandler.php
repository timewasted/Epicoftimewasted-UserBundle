<?php

namespace Epicoftimewasted\UserBundle\Form\Handler;

use Epicoftimewasted\UserBundle\Captcha\CaptchaInterface;
use Epicoftimewasted\UserBundle\Mailer\MailerInterface;
use Epicoftimewasted\UserBundle\Model\UserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormHandler
{
	/**
	 * @var Form $form
	 */
	protected $form;

	/**
	 * @var Request $request
	 */
	protected $request;

	/**
	 * @var UserManagerInterface $userManager
	 */
	protected $userManager;

	/**
	 * @var MailerInterface $mailer
	 */
	protected $mailer;

	/**
	 * @var CaptchaInterface $captcha
	 */
	protected $captcha;

	/**
	 * Constructor.
	 *
	 * @param Form $form
	 * @param Request $request
	 * @param UserManagerInterface $userManager
	 * @param MailerInterface $mailer
	 * @param CaptchaInterface $captcha
	 */
	public function __construct(Form $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, CaptchaInterface $captcha)
	{
		$this->form = $form;
		$this->request = $request;
		$this->userManager = $userManager;
		$this->mailer = $mailer;
		$this->captcha = $captcha;
	}

	public function process($confirmationRequired = false)
	{
		$user = $this->userManager->createUser();
		$this->form->setData($user);

		if( $this->request->getMethod() === 'POST' ) {
			$this->form->bindRequest($this->request);
			if( $this->captcha->isCaptchaValid() && $this->form->isValid() ) {
				$this->onSuccess($user, $confirmationRequired);
				return true;
			}
		}

		return false;
	}

	protected function onSuccess(UserInterface $user, $confirmationRequired)
	{
		if( $confirmationRequired ) {
			$user->setAccountEnabled(false);
			$this->mailer->sendConfirmationEmail($user);
		} else {
			$user->removeConfirmationToken();
			$user->setAccountEnabled(true);
			$user->setLastLogin(new \DateTime());
		}

		$this->userManager->updateUser($user);
	}
}
