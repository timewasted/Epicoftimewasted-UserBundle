<?php

namespace Epicoftimewasted\UserBundle\Form\Handler;

use Epicoftimewasted\UserBundle\Form\Model\ResetPassword;
use Epicoftimewasted\UserBundle\Model\UserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ResettingFormHandler
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
	 * Constructor.
	 *
	 * @param Form $form
	 * @param Request $request
	 * @param UserManagerInterface $userManager
	 */
	public function __construct(Form $form, Request $request, UserManagerInterface $userManager)
	{
		$this->form = $form;
		$this->request = $request;
		$this->userManager = $userManager;
	}

	/**
	 * Get the new password.
	 *
	 * @return string
	 */
	public function getNewPassword()
	{
		return $this->form->getData()->newPassword;
	}

	public function process(UserInterface $user)
	{
		$this->form->setData(new ResetPassword());

		if( $this->request->getMethod() === 'POST' ) {
			$this->form->bindRequest($this->request);

			if( $this->form->isValid() ) {
				$this->onSuccess($user);
				return true;
			}
		}

		return false;
	}

	protected function onSuccess(UserInterface $user)
	{
		$user->setPlainPassword($this->getNewPassword());
		$user->removeConfirmationToken();
		$user->setAccountEnabled(true);
		$this->userManager->updateUser($user);
	}
}
