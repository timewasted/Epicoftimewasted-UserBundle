<?php

namespace Epicoftimewasted\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ResetPasswordFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('newPassword', 'repeated', array('type' => 'password'));
	}

	public function getName()
	{
		return 'resetpasswordform';
	}
}
