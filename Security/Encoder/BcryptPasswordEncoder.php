<?php

namespace Epicoftimewasted\UserBundle\Security\Encoder;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class BcryptPasswordEncoder extends BasePasswordEncoder
{
	/**
	 * @var CryptoManager $cryptoManager
	 */
	private $cryptoManager;

	/**
	 * @var integer $workFactor
	 */
	private $workFactor;

	/**
	 * Constructor.  Note that algorithm is actually ignored here.
	 *
	 * @param CryptoManager $cryptoManager
	 * @param string $algorithm (ignored)
	 * @param integer $workFactor
	 */
	public function __construct(/*CryptoManager*/ $cryptoManager, $algorithm = null, $workFactor = 10)
	{
		$this->cryptoManager = $cryptoManager;
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
		return $this->cryptoManager->bcrypt($raw, $salt, $this->workFactor);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPasswordValid($encoded, $raw, $salt)
	{
		return $this->comparePassword($encoded, $this->encodePassword($raw, $salt));
	}
}
