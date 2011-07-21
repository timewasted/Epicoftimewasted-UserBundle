<?php

namespace Epicoftimewasted\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EpicoftimewastedUserExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$processor = new Processor();
		$configuration = new Configuration();

		$config = $processor->processConfiguration($configuration, $configs);

		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

		$config['db_driver'] = strtolower($config['db_driver']);
//		if( !in_array($config['db_driver'], array('orm', 'mongodb')) )
		if( $config['db_driver'] !== 'orm' )
			throw new \InvalidArgumentException(sprintf('Invalid database driver "%s".  Currently only "orm" is supported.', $config['db_driver']));
		$loader->load(sprintf('%s.xml', $config['db_driver']));

//		foreach( array('services', 'controller', 'form', 'validator', 'security', 'util', 'mailer', 'listener') as $baseName )
		foreach( array('controller', 'form', 'validator', 'security', 'util', 'mailer', 'listener') as $baseName )
			$loader->load(sprintf('%s.xml', $baseName));

		$container->setAlias('epicoftimewasted_user.mailer', $config['service']['mailer']);
		$container->setAlias('epicoftimewasted_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
		$container->setAlias('epicoftimewasted_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);

		if( $config['use_listener'] ) {
			switch($config['db_driver']) {
				case 'orm':
					$container->getDefinition('epicoftimewasted_user.user_listener')->addTag('doctrine.event_subscriber');
					break;
/*
				case 'mongodb':
					$container->getDefinition('epicoftimewasted_user.user_listener')->addTag('doctrine.common.event_subscriber');
					break;
*/
			}
		}

		$this->remapParametersNamespaces($config, $container, array(
			'' => array(
				'firewall_name' => 'epicoftimewasted_user.firewall_name',
			),
			'encoder'	=> 'epicoftimewasted_user.encoder.%s',
			'form_name'	=> 'epicoftimewasted_user.form.%s.name',
		));

		$this->remapParametersNamespaces($config['class'], $container, array(
			'model'			=> 'epicoftimewasted_user.model.%s.class',
			'form'			=> 'epicoftimewasted_user.form.type.%s.class',
			'form_handler'	=> 'epicoftimewasted_user.form.handler.%s.class',
			'controller'	=> 'epicoftimewasted_user.controller.%s.class',
		));

		$this->remapParametersNamespaces($config['email'], $container, array(
			''						=> array('from_email' => 'epicoftimewasted_user.email.from_email'),
			'confirmation'			=> 'epicoftimewasted_user.email.confirmation.%s',
			'resetting_password'	=> 'epicoftimewasted_user.email.resetting_password.%s',
		));

		$this->remapParametersNamespaces($config['routes'], $container, array(
			'' => array(
				'account_active' => 'epicoftimewasted_user.routes.account_active',
			),
		));
/*
		$container->setAlias('fos_user.mailer', $config['service']['mailer']);
		$container->setAlias('fos_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
		$container->setAlias('fos_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);

		if( !empty($config['group']) ) {
			$loader->load('group.xml');
			$loader->load(sprintf('%s_group.xml', $config['db_driver']));
			$this->remapParametersNamespaces($config['group'], $container, array(
				'class'							=> 'fos_user.%s.group.class',
				'' => array(
					'form'						=> 'fos_user.form.type.group.class',
					'form_handler'				=> 'fos_user.form.handler.group.class',
					'form_name'					=> 'fos_user.form.group.name',
					'form_validation_groups'	=> 'fos_user.form.group.validation_groups',
				),
			));
		}

		if( $config['use_listener'] ) {
			switch($config['db_driver']) {
				case 'orm':
					$container->getDefinition('fos_user.user_listener')->addTag('doctrine.event_subscriber');
					break;
				case 'mongodb':
					$container->getDefinition('fos_user.user_listener')->addTag('doctrine.common.event_subscriber');
					break;
			}
		}

		$this->remapParametersNamespaces($config, $container, array(
			'' => array(
				'firewall_name' => 'fos_user.firewall_name',
			),
			'encoder'					=> 'fos_user.encoder.%s',
			'template'					=> 'fos_user.template.%s',
			'form_name'					=> 'fos_user.form.%s.name',
			'form_validation_groups'	=> 'fos_user.form.%s.validation.groups',
		));

		$this->remapParametersNamespaces($config['class'], $container, array(
			'model'			=> 'fos_user.model.%s.class',
			'form'			=> 'fos_user.form.type.%s.class',
			'form_handler'	=> 'fos_user.form.handler.%s.class',
			'controller'	=> 'fos_user.controller.%s.class',
		));

		$this->remapParametersNamespaces($config['email'], $container, array(
			''						=> array('from_email' => 'fos_user.email.from_email'),
			'confirmation'			=> 'fos_user.email.confirmation.%s',
			'resetting_password'	=> 'fos_user.email.resetting_password.%s',
		));
*/
	}

	protected function remapParameters(array $config, ContainerBuilder $container, array $map)
	{
		foreach( $map as $name => $paramName ) {
			if( isset($config[$name]) )
				$container->setParameter($paramName, $config[$name]);
		}
	}

	protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
	{
		foreach( $namespaces as $ns => $map ) {
			if( $ns ) {
				if( !isset($config[$ns]) )
					continue;
				$namespaceConfig = $config[$ns];
			} else {
				$namespaceConfig = $config;
			}
			if( is_array($map) ) {
				$this->remapParameters($namespaceConfig, $container, $map);
			} else {
				foreach( $namespaceConfig as $name => $value ) {
					if( $value !== null )
						$container->setParameter(sprintf($map, $name), $value);
				}
			}
		}
	}
}
