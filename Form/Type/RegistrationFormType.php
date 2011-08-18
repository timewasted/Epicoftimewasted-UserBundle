<?php

namespace Epicoftimewasted\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RegistrationFormType extends AbstractType
{
	/**
	 * @var string $class
	 */
	private $class;

	/**
	 * Constructor.
	 *
	 * @param string $class
	 */
	public function __construct($class)
	{
		$this->class = $class;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('username')
			->add('email', 'email')
			->add('plainPassword', 'repeated', array('type' => 'password'));
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => $this->class,
			'intention' => 'd95c58afec47723bb3f95e955403f9e695ab347610a0c2095832b0348003f0b5',
		);
	}

	public function getName()
	{
		return 'epicoftimewasted_user_registration';
	}
}
