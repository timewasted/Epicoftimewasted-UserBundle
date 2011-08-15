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
		return array('data_class' => $this->class);
	}

	public function getName()
	{
		return 'epicoftimewasted_user_registration';
	}
}
