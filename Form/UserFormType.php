<?php

namespace Epicoftimewasted\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('username', 'text', array())
			->add('email', 'email', array())
			->add('plainPassword', 'repeated', array('type' => 'password'));
	}

	public function getName()
	{
		return 'userform';
	}
}
