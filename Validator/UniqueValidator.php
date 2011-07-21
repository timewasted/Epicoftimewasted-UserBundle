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

	public function isValid($value, Constraint $constraint)
	{
		if( !$this->userManager->validateUnique($value, $constraint) ) {
			$this->setMessage($constraint->message);
			return false;
		}
		return true;
	}
}
