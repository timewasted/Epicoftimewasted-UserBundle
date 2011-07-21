<?php

namespace Epicoftimewasted\UserBundle\Form;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordFormHandler
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
	 * @return string
	 */
	public function getNewPassword()
	{
		return $this->form->getData()->newPassword;
	}

	/**
	 * @param EpicoftimewastedUserInterface $user
	 * @return boolean
	 */
	public function process(EpicoftimewastedUserInterface $user)
	{
		$this->form->setData(new ResetPassword($user));

		if( $this->request->getMethod() === 'POST' ) {
			$this->form->bindRequest($this->request);

			if( $this->form->isValid() ) {
				$user->setPlainPassword($this->getNewPassword());
				$user->removeConfirmationToken();
				$user->setAccountEnabled(true);
				$this->userManager->updateUser($user);

				return true;
			}
		}

		return false;
	}
}
