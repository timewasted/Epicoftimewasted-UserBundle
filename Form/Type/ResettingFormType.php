<?php

namespace Epicoftimewasted\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ResettingFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('newPassword', 'repeated', array('type' => 'password'));
	}

	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Epicoftimewasted\UserBundle\Form\Model\ResetPassword');
	}

	public function getName()
	{
		return 'epicoftimewasted_user_resetting';
	}
}
