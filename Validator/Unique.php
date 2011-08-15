<?php

namespace Epicoftimewasted\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Unique extends Constraint
{
	/**
	 * @var string $message
	 */
	public $message = 'One or more of the properties is not unique.';

	/**
	 * @var array $properties
	 */
	public $properties;

	/**
	 * {@inheritDoc}
	 */
	public function defaultOption()
	{
		return 'properties';
	}

	/**
	 * {@inheritDoc}
	 */
	public function requiredOptions()
	{
		return array('properties');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validatedBy()
	{
		return 'epicoftimewasted_user.validator.unique';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
