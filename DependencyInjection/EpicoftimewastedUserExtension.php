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
		if( $config['db_driver'] !== 'orm' )
			throw new \InvalidArgumentException(sprintf('Invalid database driver "%s".', $config['db_driver']));
		$loader->load(sprintf('%s.xml', $config['db_driver']));

		foreach( array('mailer', 'security', 'util', 'validator') as $baseName )
			$loader->load(sprintf('%s.xml', $baseName));

		$container->setAlias('epicoftimewasted_user.mailer', $config['service']['mailer']);
		$container->setAlias('epicoftimewasted_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
		$container->setAlias('epicoftimewasted_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);
		$container->setAlias('epicoftimewasted_user.user_manager', $config['service']['user_manager']);

		if( $config['use_listener'] ) {
			switch( $config['db_driver'] ) {
				case 'orm':
					$container->getDefinition('epicoftimewasted_user.user_listener')->addTag('doctrine.event_subscriber');
					break;
				default:
					break;
			}
		}

		if( $config['use_username_form_type'] )
			$loader->load('username_form_type.xml');

		$this->remapParametersNamespaces($config, $container, array(
			'' => array(
				'firewall_name'			=> 'epicoftimewasted_user.firewall_name',
				'model_manager_name'	=> 'epicoftimewasted_user.model_manager_name',
				'user_class'			=> 'epicoftimewasted_user.model.user.class',
			),
			'encoder'	=> 'epicoftimewasted_user.encoder.%s',
		));
		$container->setParameter(
			'epicoftimewasted_user.confirmation.from_email',
			array($config['from_email']['address'] => $config['from_email']['sender_name'])
		);
		$container->setParameter(
			'epicoftimewasted_user.resetting.email.from_email',
			array($config['from_email']['address'] => $config['from_email']['sender_name'])
		);

		if( !empty($config['registration']) ) {
			$loader->load('registration.xml');

			$container->setAlias('epicoftimewasted_user.registration.form.handler', $config['registration']['form']['handler']);
			unset($config['registration']['form']['handler']);

			if( !empty($config['registration']['confirmation']['from_email']) ) {
				$container->setParameter(
					'epicoftimewasted_user.registration.confirmation.from_email',
					array($config['registration']['confirmation']['from_email']['address'] => $config['registration']['confirmation']['from_email']['sender_name'])
				);
			}
			unset($config['registration']['confirmation']['from_email']);

			$this->remapParametersNamespaces($config['registration'], $container, array(
				'confirmation'	=> 'epicoftimewasted_user.registration.confirmation.%s',
				'form'			=> 'epicoftimewasted_user.registration.form.%s',
				'routes'		=> 'epicoftimewasted_user.registration.routes.%s',
			));
		}
/*
		if( !empty($config['change_password']) ) {
			$loader->load('change_password.xml');

			$container->setAlias('epicoftimewasted_user.change_password.form.handler', $config['change_password']['form']['handler']);
			unset($config['change_password']['form']['handler']);

			$this->remapParametersNamespaces($config['change_password'], $container, array(
				'form' => 'epicoftimewasted_user.change_password.form.%s',
			));
		}
*/
		if( !empty($config['resetting']) ) {
			$loader->load('resetting.xml');

			$container->setAlias('epicoftimewasted_user.resetting.form.handler', $config['resetting']['form']['handler']);
			unset($config['resetting']['form']['handler']);

			if( !empty($config['resetting']['email']['from_email']) ) {
				$container->setParameter(
					'epicoftimewasted_user.resetting.email.from_email',
					array($config['resetting']['email']['from_email']['address'] => $config['resetting']['email']['from_email']['sender_name'])
				);
			}
			unset($config['resetting']['email']['from_email']);

			$this->remapParametersNamespaces($config['resetting'], $container, array(
				'' => array(
					'token_ttl' => 'epicoftimewasted_user.resetting.token_ttl',
				),
				'email'	=> 'epicoftimewasted_user.resetting.email.%s',
				'form'	=> 'epicoftimewasted_user.resetting.form.%s',
				'routes' => 'epicoftimewasted_user.resetting.routes.%s',
			));
		}

		if( !empty($config['captcha']) ) {
			if( $config['captcha']['public_key'] === null || $config['captcha']['private_key'] === null )
				throw new \InvalidArgumentException('Captcha requires a valid public_key and private_key.');

			$loader->load('captcha.xml');

			$this->remapParametersNamespaces($config['captcha'], $container, array(
				'' => array(
					'enabled'		=> 'epicoftimewasted_user.captcha.enabled',
					'public_key'	=> 'epicoftimewasted_user.captcha.public_key',
					'private_key'	=> 'epicoftimewasted_user.captcha.private_key',
				),
			));
		}

		if( !empty($config['security']) ) {
			if( !empty($config['security']['login_throttling']) ) {
				$this->remapParametersNamespaces($config['security']['login_throttling'], $container, array(
					'' => array(
						'enabled' => 'epicoftimewasted_user.security.login_throttling.enabled',
						'threshold' => 'epicoftimewasted_user.security.login_throttling.threshold',
					),
				));
			}
		}
	}

	protected function remapParameters(array $config, ContainerBuilder $container, array $map)
	{
		foreach( $map as $name => $paramName ) {
			if( array_key_exists($name, $config) )
				$container->setParameter($paramName, $config[$name]);
		}
	}

	protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
	{
		foreach( $namespaces as $ns => $map ) {
			if( $ns ) {
				if( !array_key_exists($ns, $config) )
					continue;
				$namespaceConfig = $config[$ns];
			} else {
				$namespaceConfig = $config;
			}
			if( is_array($map) ) {
				$this->remapParameters($namespaceConfig, $container, $map);
			} else {
				foreach( $namespaceConfig as $name => $value )
					$container->setParameter(sprintf($map, $name), $value);
			}
		}
	}
}
