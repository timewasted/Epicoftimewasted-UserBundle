<?php

namespace Epicoftimewasted\UserBundle\Form\DataTransformer;

use Epicoftimewasted\UserBundle\Model\UserInterface;
use Epicoftimewasted\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class UsernameToUserTransformer implements DataTransformerInterface
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
	 * Transforms a UserInterface instance into a username string.
	 *
	 * @param mixed $value A UserInterface object
	 * @return string|null Username or null on error
	 */
	public function transform($value)
	{
		if( $value === null )
			return null;
		if( !$value instanceof UserInterface )
			throw new UnexpectedTypeException($value, 'Epicoftimewasted\UserBundle\Model\UserInterface');

		return $value->getUsername();
	}

	/**
	 * Transforms a username into a UserInterface instance.
	 *
	 * @param string $value A username
	 * @return UserInterface|null UserInterface object or null on error
	 */
	public function reverseTransform($value)
	{
		if( $value === null )
			return null;
		if( !is_string($value) )
			throw new UnexpectedTypeException($value, 'string');

		return $this->userManager->findUserByUsername($value);
	}
}
