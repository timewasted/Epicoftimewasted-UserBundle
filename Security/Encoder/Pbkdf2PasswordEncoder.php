<?php

namespace Epicoftimewasted\UserBundle\Security\Encoder;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class Pbkdf2PasswordEncoder extends BasePasswordEncoder
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
	public function __construct(/*CryptoManager*/ $cryptoManager, $algorithm = 'sha512', $iterations = 5000)
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
		return $this->comparePassword($encoded, $this->encodePassword($raw, $salt));
	}
}
