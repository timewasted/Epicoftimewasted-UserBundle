<?php

namespace Epicoftimewasted\UserBundle\Model;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUser implements EpicoftimewastedUserInterface
{
	const DEFAULT_ROLE = 'ROLE_USER';

	/**
	 * @var CryptoManager $cryptoManager
	 */
//	protected $cryptoManager;

	/**
	 * @var string $username
	 */
	protected $username;

	/**
	 * @var string $usernameCanonical
	 */
	protected $usernameCanonical;

	/**
	 * @var string $algorithm
	 */
	protected $algorithm;

	/**
	 * @var integer $workFactor
	 */
	protected $workFactor;

	/**
	 * @var string $salt
	 */
	protected $salt;

	/**
	 * @var string $password
	 */
	protected $password;

	/**
	 * @var string $plainPassword
	 */
	protected $plainPassword;

	/**
	 * @var string $email
	 */
	protected $email;

	/**
	 * @var string $emailCanonical
	 */
	protected $emailCanonical;

	/**
	 * @var string $confirmationToken
	 */
	protected $confirmationToken;

	/**
	 * @var DateTime $createdAt
	 */
	protected $createdAt;

	/**
	 * @var DateTime $passwordRequestedAt
	 */
	protected $passwordRequestedAt;

	/**
	 * @var DateTime $lastLogin
	 */
	protected $lastLogin;

	/**
	 * @var integer $failedLoginAttempts
	 */
	protected $failedLoginAttempts;

	/**
	 * @var DateTime $accountLockedUntil
	 */
	protected $accountLockedUntil;

	/**
	 * @var array $roles
	 */
	protected $roles;

	/**
	 * @var DateTime $accountExpiresAt
	 */
	protected $accountExpiresAt;

	/**
	 * @var boolean accountLocked
	 */
	protected $accountLocked;

	/**
	 * @var DateTime $credentialsExpireAt
	 */
	protected $credentialsExpireAt;

	/**
	 * @var boolean accountEnabled
	 */
	protected $accountEnabled;

	/**
	 * Constructor.
	 *
	 * @param CryptoManager $cryptoManager
	 */
	public function __construct(/*CryptoManager*/ $cryptoManager)
	{
//		$this->cryptoManager = $cryptoManager;

		$this->salt = $cryptoManager->getEntropy(32);
//		$this->createConfirmationToken();
		$this->createdAt = new \DateTime();
		$this->failedLoginAttempts = 0;
		$this->roles = array();
		$this->accountLocked = false;
		$this->accountEnabled = false;
	}

	public function __tostring()
	{
		return $this->getUsername();
	}

	public function onPrePersist()
	{
	}

	public function onPreUpdate()
	{
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function equals(UserInterface $user)
	{
		if( !$user instanceof User )
			return false;
		if( $this->getPassword() !== $user->getPassword() )
			return false;
		if( $this->getSalt() !== $user->getSalt() )
			return false;
		if( $this->isAccountNonExpired() !== $user->isAccountNonExpired() )
			return false;
		if( $this->isAccountNonLocked() !== $user->isAccountNonLocked() )
			return false;
		if( $this->isCredentialsNonExpired() !== $user->isCredentialsNonExpired() )
			return false;
		if( $this->isEnabled() !== $user->isEnabled() )
			return false;

		return true;
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function eraseCredentials()
	{
		$this->plainPassword = null;
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPlainPassword($password)
	{
		$this->plainPassword = $password;
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function getRoles()
	{
		$roles = $this->roles;
		$roles[] = self::DEFAULT_ROLE;
		return array_unique($roles);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Implements UserInterface
	 *
	 * {@inheritDoc}
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsernameCanonical()
	{
		return $this->usernameCanonical;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUsernameCanonical($username)
	{
		$this->usernameCanonical = $username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccountExpired()
	{
		return !$this->isAccountNonExpired();
	}

	/**
	 * Implements AdvancedUserInterface
	 *
	 * {@inheritDoc}
	 */
	public function isAccountNonExpired()
	{
		if( $this->accountExpiresAt === null || $this->accountExpiresAt->getTimestamp() > time() )
			return true;
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAccountExpiresAt($time)
	{
		$this->accountExpiresAt = $time;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAccountLocked()
	{
		return !$this->isAccountNonLocked();
	}

	/**
	 * Implements AdvancedUserInterface
	 *
	 * {@inheritDoc}
	 */
	public function isAccountNonLocked()
	{
		if( $this->accountLockedUntil !== null && time() > $this->accountLockedUntil->getTimestamp() )
			return false;
		return !$this->accountLocked;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isCredentialsExpired()
	{
		return !$this->isCredentialsNonExpired();
	}

	/**
	 * Implements AdvancedUserInterface
	 *
	 * {@inheritDoc}
	 */
	public function isCredentialsNonExpired()
	{
		if( $this->credentialsExpireAt === null || $this->credentialsExpireAt->getTimestamp() > time() )
			return true;
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setCredentialsExpireAt($time)
	{
		$this->credentialsExpireAt = $time;
	}

	/**
	 * Implements AdvancedUserInterface
	 *
	 * {@inheritDoc}
	 */
	public function isEnabled()
	{
		return $this->accountEnabled;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAccountLocked($isLocked)
	{
		$this->accountLocked = $isLocked;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAccountEnabled($isEnabled)
	{
		$this->accountEnabled = $isEnabled;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAlgorithm()
	{
		return $this->algorithm;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAlgorithm($algorithm)
	{
		$this->algorithm = $algorithm;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getWorkFactor()
	{
		return $this->workFactor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setWorkFactor($workFactor)
	{
		$this->workFactor = $workFactor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmailCanonical()
	{
		return $this->emailCanonical;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setEmailCanonical($email)
	{
		$this->emailCanonical = $email;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setConfirmationToken($token)
	{
		$this->confirmationToken = $token;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfirmationToken()
	{
		return $this->confirmationToken;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeConfirmationToken()
	{
		$this->confirmationToken = null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPasswordRequestedAt()
	{
		return $this->passwordRequestedAt;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPasswordRequestedAt($time)
	{
		$this->passwordRequestedAt = $time;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPasswordRequestNonExpired($ttl)
	{
		return $this->passwordRequestedAt instanceof \DateTime && $this->passwordRequestedAt->getTimestamp() + $ttl > time();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLastLogin()
	{
		return $this->lastLogin;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setLastLogin($time)
	{
		$this->lastLogin = $time;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFailedLoginAttempts()
	{
		return $this->failedLoginAttempts;
	}

	/**
	 * {@inheritDoc}
	 */
	public function incrementFailedLoginAttempts()
	{
		$this->failedLoginAttempts++;
	}

	/**
	 * {@inheritDoc}
	 */
	public function resetFailedLoginAttempts()
	{
		$this->failedLoginAttempts = 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAccountLockedUntil()
	{
		return $this->accountLockedUntil;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setAccountLockedUntil($time)
	{
		$this->accountLockedUntil = $time;
	}
}
