<?php

namespace Epicoftimewasted\UserBundle\Model;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Epicoftimewasted\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

abstract class AbstractUserManager implements UserManagerInterface, UserProviderInterface
{
	/**
	 * @var EncoderFactoryInterface $encoderFactory
	 */
	protected $encoderFactory;

	/**
	 * @var string $algorithm
	 */
	protected $algorithm;

	/**
	 * @var integer $workFactor
	 */
	protected $workFactor;

	/**
	 * @var CanonicalizerInterface $usernameCanonicalizer
	 */
	protected $usernameCanonicalizer;

	/**
	 * @var CanonicalizerInterface $emailCanonicalizer
	 */
	protected $emailCanonicalizer;

	/**
	 * @var CryptoManager $cryptoManager
	 */
	protected $cryptoManager;

	/**
	 * Constructor.
	 *
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param string $algorithm
	 * @param integer $workFactor
	 * @param CanonicalizerInterface $usernameCanonicalizer
	 * @param CanonicalizerInterface $emailCanonicalizer
	 * @param CryptoManager $cryptoManager
	 */
	public function __construct(EncoderFactoryInterface $encoderFactory, $algorithm, $workFactor, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, /*CryptoManager*/ $cryptoManager)
	{
		$this->encoderFactory = $encoderFactory;
		$this->algorithm = $algorithm;
		$this->workFactor = $workFactor;
		$this->usernameCanonicalizer = $usernameCanonicalizer;
		$this->emailCanonicalizer = $emailCanonicalizer;
		$this->cryptoManager = $cryptoManager;
	}

	/**
	 * Implements UserProviderInterface
	 *
	 * @param SecurityUserInterface $user
	 * @return UserInterface
	 */
	public function refreshUser(SecurityUserInterface $user)
	{
		if( !$user instanceof AbstractUser )
			throw new UnsupportedUserException(sprintf('Users of instance "%s" are not supported.', get_class($user)));

		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * Implements UserProviderInterface
	 *
	 * @param string $username
	 * @return UserInterface
	 */
	public function loadUserByUsername($username)
	{
		$user = $this->findUserByUsername($username);

		if( $user === null )
			throw new UsernameNotFoundException(sprintf('No user with the name "%s" found.', $username));

		return $user;
	}

	/**
	 * Implements UserProviderInterface
	 *
	 * @param string $class
	 * @return boolean
	 */
	public function supportsClass($class)
	{
		return $class === $this->getClass();
	}

	/**
	 * {@inheritDoc}
	 */
	public function createUser()
	{
		$class = $this->getClass();
		$user = new $class($this->cryptoManager);
		$this->createConfirmationToken($user);
		$user->setAlgorithm($this->algorithm);
		$user->setWorkFactor($this->workFactor);
		$user->setRoles(array('ROLE_USER'));
		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createTemporaryUser()
	{
		$class = $this->getClass();
		$user = new $class($this->cryptoManager);
		$tempString = 'temp_' . bin2hex($this->cryptoManager->getEntropy(16));
		$user->setUsername($tempString);
		$user->setEmail($tempString);
		$user->setAlgorithm($this->algorithm);
		$user->setWorkFactor(8);
		$user->setPlainPassword($tempString);
		$user->setAccountEnabled(true);
		$user->setRoles(array('ROLE_TEMPORARY'));

		$this->updateUser($user);
		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createConfirmationToken(UserInterface $user)
	{
		$user->setConfirmationToken(bin2hex($this->cryptoManager->getEntropy(20)));
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateCanonicalFields(UserInterface $user)
	{
		$user->setUsernameCanonical($this->usernameCanonicalizer->canonicalize($user->getUsername()));
		$user->setEmailCanonical($this->emailCanonicalizer->canonicalize($user->getEmail()));
	}

	/**
	 * {@inheritDoc}
	 */
	public function updatePassword(UserInterface $user)
	{
		$password = $user->getPlainPassword();
		if( is_string($password) && !empty($password) ) {
			$encoder = $this->encoderFactory->getEncoder($user);
			$user->setPassword($encoder->encodePassword($password, $user->getSalt()));
			$user->eraseCredentials();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUserByUsername($username)
	{
		return $this->findUserBy(array(
			'usernameCanonical' => $this->usernameCanonicalizer->canonicalize($username)
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUserByEmail($email)
	{
		return $this->findUserBy(array(
			'emailCanonical' => $this->emailCanonicalizer->canonicalize($email)
		));
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUserByConfirmationToken($token)
	{
		return $this->findUserBy(array(
			'confirmationToken' => $token
		));
	}
}
