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
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * NOTE: This is NOT used.  It is only included for reference.
 */
class Pbkdf2PasswordEncoder extends BasePasswordEncoder
{
	/**
	 * @var CryptoManagerInterface $cryptoManager
	 */
	private $cryptoManager;

	/**
	 * @var string $algorithm
	 */
	private $algorithm;

	/**
	 * @var integer $workFactor
	 */
	private $workFactor;

	/**
	 * Constructor.
	 *
	 * @param CryptoManagerInterface $cryptoManager
	 * @param string $algorithm
	 * @param integer $workFactor
	 */
	public function __construct(CryptoManagerInterface $cryptoManager, $algorithm = 'sha512', $iterations = 5000)
	{
		$this->cryptoManager = $cryptoManager;
		$this->algorithm = $algorithm;
		$this->workFactor = $iterations;
	}

	/**
	 * Changes the number of iterations to be used.
	 *
	 * @param integer $iterations
	 * @return void
	 */
	public function setWorkFactor($iterations)
	{
		$this->workFactor = $iterations;
	}

	/**
	 * {@inheritDoc}
	 */
	public function encodePassword($raw, $salt)
	{
		$this->cryptoManager->changeHashAlgorithm($this->algorithm);
		$password = $this->cryptoManager->pbkdf2($raw, $salt, $this->workFactor, $this->cryptoManager->getHashAlgorithmSize());
		$this->cryptoManager->restoreHashAlgorithm();

		return $password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPasswordValid($encoded, $raw, $salt)
	{
		return $this->comparePassword($encoded, $this->encodePassword($raw, $salt));
	}
}
