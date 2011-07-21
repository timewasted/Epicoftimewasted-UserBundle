<?php

namespace Epicoftimewasted\UserBundle\Security\Encoder;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class CryptoPasswordEncoder extends BasePasswordEncoder
{
	/**
	 * @var CryptoManager $cryptoManager
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
	 * @param CryptoManager $cryptoManager
	 * @param string $algorithm
	 * @param integer $workFactor
	 */
	public function __construct(/*CryptoManager*/ $cryptoManager, $algorithm, $workFactor = 10)
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
		$oldHashAlgorithm = $this->cryptoManager->getHashAlgorithm();
		$this->cryptoManager->setHashAlgorithm($this->algorithm);
		$password = $this->cryptoManager->pbkdf2($raw, $salt, $this->workFactor, $this->cryptoManager->getHashAlgorithmSize());
		$this->cryptoManager->setHashAlgorithm($oldHashAlgorithm);
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
