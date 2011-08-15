<?php

namespace Epicoftimewasted\UserBundle\Validator;

use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
	/**
	 * @var UserManagerInterface $userManager
	 */
	protected $userManager;

	/**
	 * Constructor.
	 *
	 * @param UserManagerInterface $userManager
	 */
	public function __construct(UserManagerInterface $userManager)
	{
		$this->userManager = $userManager;
	}

	/**
	 * Set the user manager.
	 *
	 * @param UserManagerInterface $userManager
	 */
	public function setUserManager(UserManagerInterface $userManager)
	{
		$this->userManager = $userManager;
	}

	/**
	 * Get the user manager.
	 *
	 * @return UserManagerInterface
	 */
	public function getUserManager()
	{
		return $this->userManager;
	}

	/**
	 * Indicates whether the constraint is valid or not.
	 *
	 * @param Entity $value
	 * @param Constraint $constraint
	 */
	public function isValid($value, Constraint $constraint)
	{
		if( !$this->getUserManager()->validateUnique($value, $constraint) ) {
			$this->setMessage($constraint->message);
			return false;
		}

		return true;
	}
}
