<?php

namespace Epicoftimewasted\UserBundle\Form;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class UserFormHandler
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
	 * @param EpicoftimewastedUserInterface $user
	 * @param boolean $confirmation
	 * @return boolean
	 */
	public function process(EpicoftimewastedUserInterface $user = null, $confirmation = null)
	{
		if( $user === null )
			$user = $this->userManager->createUser();

		$this->form->setData($user);

		if( $this->request->getMethod() === 'POST' ) {
			$this->form->bindRequest($this->request);

			if( $this->form->isValid() ) {
				if( $confirmation === true ) {
					$user->setAccountEnabled(false);
				} elseif( $confirmation === false ) {
					$user->removeConfirmationToken();
					$user->setAccountEnabled(true);
					$user->setLastLogin(new \DateTime());
				}

				$this->userManager->updateUser($user);
				return true;
			}
		}

		return false;
	}
}
