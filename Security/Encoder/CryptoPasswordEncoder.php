<?php

namespace Epicoftimewasted\UserBundle\Security\Encoder;

use Epicoftimewasted\CryptoBundle\Security\CryptoManagerInterface;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class CryptoPasswordEncoder extends BasePasswordEncoder
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
	public function __construct(CryptoManagerInterface $cryptoManager, $algorithm, $workFactor = 10)
	{
		$this->cryptoManager = $cryptoManager;
		$this->algorithm = $algorithm;
		$this->workFactor = $workFactor;
	}

	/**
	 * Changes the work factor.
	 *
	 * @param integer $workFactor
	 * @return void
	 */
	public function setWorkFactor($workFactor)
	{
		$this->workFactor = $workFactor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function encodePassword($raw, $salt)
	{
		if( $this->algorithm === 'bcrypt' )
			return $this->cryptoManager->bcrypt($raw, $salt, $this->workFactor);

		// If the algorithm isn't bcrypt, it's a hash algorithm for pbkdf2.
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
		return $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
	}
}
