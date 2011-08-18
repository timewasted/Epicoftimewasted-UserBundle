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
		return array(
			'data_class' => 'Epicoftimewasted\UserBundle\Form\Model\ResetPassword',
			'intention' => '92f25582c991d46a4b7fd6c6217adcaa3117fd499837e39f1193f4f8a059d039',
		);
	}

	public function getName()
	{
		return 'epicoftimewasted_user_resetting';
	}
}
