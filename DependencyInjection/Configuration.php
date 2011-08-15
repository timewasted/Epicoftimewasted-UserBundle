<?php

namespace Epicoftimewasted\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree.
	 *
	 * @return TreeBuilder
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('epicoftimewasted_user');

		$rootNode
			->children()
				->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
				->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
				->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
				->scalarNode('model_manager_name')->defaultNull()->end()
				->booleanNode('use_listener')->defaultTrue()->end()
				->booleanNode('use_username_form_type')->defaultTrue()->end()
				->arrayNode('from_email')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('address')->defaultValue('webmaster@example.com')->cannotBeEmpty()->end()
						->scalarNode('sender_name')->defaultValue('webmaster')->cannotBeEmpty()->end()
					->end()
				->end()
			->end();

		$this->addCaptchaSection($rootNode);
//		$this->addChangePasswordSection($rootNode);
		$this->addEncoderSection($rootNode);
		$this->addRegistrationSection($rootNode);
		$this->addResettingSection($rootNode);
		$this->addServiceSection($rootNode);

		return $treeBuilder;
	}

	private function addCaptchaSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('captcha')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->booleanNode('enabled')->defaultFalse()->end()
						->scalarNode('public_key')->defaultNull()->cannotBeEmpty()->end()
						->scalarNode('private_key')->defaultNull()->cannotBeEmpty()->end()
					->end()
				->end()
			->end();
	}
/*
    private function addChangePasswordSection(ArrayNodeDefinition $node)
    {
		$node
			->children()
				->arrayNode('change_password')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->arrayNode('form')
						->addDefaultsIfNotSet()
							->children()
								->scalarNode('type')->defaultValue('epicoftimewasted_user_change_password')->end()
								->scalarNode('handler')->defaultValue('epicoftimewasted_user.change_password.form.handler.default')->end()
								->scalarNode('name')->defaultValue('epicoftimewasted_user_change_password_form')->cannotBeEmpty()->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
*/
	private function addEncoderSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('encoder')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('algorithm')->defaultValue('bcrypt')->end()
						->scalarNode('work_factor')->defaultValue(10)->end()
					->end()
				->end()
			->end();
	}

	private function addRegistrationSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('registration')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->arrayNode('confirmation')
							->addDefaultsIfNotSet()
							->children()
								->booleanNode('enabled')->defaultFalse()->end()
								->scalarNode('template')->defaultValue('EpicoftimewastedUserBundle:Registration:email.txt.twig')->end()
								->arrayNode('from_email')
									->canBeUnset()
									->children()
										->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
										->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
									->end()
								->end()
							->end()
						->end()
						->arrayNode('form')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('type')->defaultValue('epicoftimewasted_user_registration')->end()
								->scalarNode('handler')->defaultValue('epicoftimewasted_user.registration.form.handler.default')->end()
								->scalarNode('name')->defaultValue('epicoftimewasted_user_registration_form')->cannotBeEmpty()->end()
							->end()
						->end()
						->arrayNode('routes')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('confirmed')->defaultValue('epicoftimewasted_user_registration_account_confirmed')->cannotBeEmpty()->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}

	private function addResettingSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('resetting')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->scalarNode('token_ttl')->defaultValue(86400)->end()
						->arrayNode('email')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('template')->defaultValue('EpicoftimewastedUserBundle:Resetting:email.txt.twig')->end()
								->arrayNode('from_email')
									->canBeUnset()
									->children()
										->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
										->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
									->end()
								->end()
							->end()
						->end()
						->arrayNode('form')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('type')->defaultValue('epicoftimewasted_user_resetting')->end()
								->scalarNode('handler')->defaultValue('epicoftimewasted_user.resetting.form.handler.default')->end()
								->scalarNode('name')->defaultValue('epicoftimewasted_user_resetting_form')->cannotBeEmpty()->end()
							->end()
						->end()
						->arrayNode('routes')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('reset_success')->defaultValue('epicoftimewasted_user_resetting_reset_success')->cannotBeEmpty()->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}

	private function addServiceSection(ArrayNodeDefinition $node)
	{
		$node
			->addDefaultsIfNotSet()
			->children()
				->arrayNode('service')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('mailer')->defaultValue('epicoftimewasted_user.mailer.default')->end()
						->scalarNode('email_canonicalizer')->defaultValue('epicoftimewasted_user.util.email_canonicalizer.default')->end()
						->scalarNode('username_canonicalizer')->defaultValue('epicoftimewasted_user.util.username_canonicalizer.default')->end()
						->scalarNode('user_manager')->defaultValue('epicoftimewasted_user.user_manager.default')->end()
					->end()
				->end()
			->end();
	}
}
