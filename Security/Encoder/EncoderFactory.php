<?php

/**
 * This file is part of the EpicoftimewastedUserBundle package.
 *
 * Copyright (c) 2011-2012 Ryan Rogers
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Epicoftimewasted\UserBundle\Security\Encoder;

use Epicoftimewasted\CryptoBundle\Security\CryptoManagerInterface;
use Epicoftimewasted\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

class EncoderFactory implements EncoderFactoryInterface
{
	/**
	 * @var CryptoManagerInterface $cryptoManager
	 */
	private $cryptoManager;

	/**
	 * @var string $encoderClass
	 */
	private $encoderClass;

	/**
	 * @var EncoderFactoryInterface $genericFactory
	 */
	private $genericFactory;

	/**
	 * @var array $encoders
	 */
	private $encoders;

	/**
	 * Constructor.
	 *
	 * @param CryptoManagerInterface $cryptoManager
	 * @param string $encoderClass
	 * @param EncoderFactoryInterface $genericFactory
	 */
	public function __construct(CryptoManagerInterface $cryptoManager, $encoderClass, EncoderFactoryInterface $genericFactory)
	{
		$this->cryptoManager = $cryptoManager;
		$this->encoderClass = $encoderClass;
		$this->genericFactory = $genericFactory;
		$this->encoders = array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEncoder(SecurityUserInterface $user)
	{
		if( !$user instanceof UserInterface )
			return $this->genericFactory->getEncoder($user);

		return $this->createEncoder($user->getAlgorithm(), $user->getWorkFactor());
	}

	/**
	 * Returns an encoder instance with the specified parameters, or creates a
	 * new one if one doesn't already exist.
	 *
	 * @param string $algorithm
	 * @param integer $workFactor
	 */
	protected function createEncoder($algorithm, $workFactor)
	{
		if( isset($this->encoders[$algorithm]) )
			$this->encoders[$algorithm]->setWorkFactor($workFactor);
		else
			$this->encoders[$algorithm] = new $this->encoderClass($this->cryptoManager, $algorithm, $workFactor);
		return $this->encoders[$algorithm];
	}
}
