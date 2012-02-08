<?php

namespace Epicoftimewasted\UserBundle\Form\Handler;

use Epicoftimewasted\CryptoBundle\Security\CryptoManagerInterface;
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
	 * @var CryptoManagerInterface $cryptoManager
	 */
	protected $cryptoManager;

	/**
	 * Constructor.
	 *
	 * @param Form $form
	 * @param Request $request
	 * @param UserManagerInterface $userManager
	 * @param CryptoManagerInterface $cryptoManager
	 */
	public function __construct(Form $form, Request $request, UserManagerInterface $userManager, CryptoManagerInterface $cryptoManager)
	{
		$this->form = $form;
		$this->request = $request;
		$this->userManager = $userManager;
		$this->cryptoManager = $cryptoManager;
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
		$user->setSalt($this->cryptoManager->getEntropy(32));
		$user->setPlainPassword($this->getNewPassword());
		$user->removeConfirmationToken();
		$user->setPasswordRequestedAt(null);
		$user->setAccountEnabled(true);
		$this->userManager->updateUser($user);
	}
}
