<?php

namespace Epicoftimewasted\UserBundle\Form\Type;

use Epicoftimewasted\UserBundle\Form\DataTransformer\UsernameToUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UsernameFormType extends AbstractType
{
	/**
	 * @var UsernameToUserTransformer
	 */
	protected $usernameTransformer;

	/**
	 * Constructor.
	 *
	 * @param UsernameToUserTransformer $userTransformer
	 */
	public function __construct(UsernameToUserTransformer $userTransformer)
	{
		$this->userTransformer = $userTransformer;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$builder->appendClientTransformer($this->usernameTransformer);
	}

	public function getParent(array $options)
	{
		return 'text';
	}

	public function getName()
	{
		return 'epicoftimewasted_user_username';
	}
}
