<?php

namespace Epicoftimewasted\UserBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface UserInterface extends AdvancedUserInterface, \Serializable
{
	/**
	 * Sets the username of the account.
	 *
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username);

	/**
	 * Gets the canonical username of the account.
	 *
	 * @return string
	 */
	public function getUsernameCanonical();

	/**
	 * Sets the canonical username of the account.
	 *
	 * @param string $username
	 * @return void
	 */
	public function setUsernameCanonical($username);

	/**
	 * Checks if an account is expired or not.
	 *
	 * @return boolean
	 */
	public function isAccountExpired();

	/**
	 * Sets the expiration time of an account.
	 *
	 * @param DateTime $time
	 * @return void
	 */
	public function setAccountExpiresAt($time);

	/**
	 * Checks if an account is locked or not.
	 *
	 * @return boolean
	 */
	public function isAccountLocked();

	/**
	 * Checks if an account's credentials are expired or not.
	 *
	 * @return boolean
	 */
	public function isCredentialsExpired();

	/**
	 * Sets the time when the credentials of an account expire.
	 *
	 * @param DateTime $time
	 * @return void
	 */
	public function setCredentialsExpireAt($time);

	/**
	 * Sets an account locked or unlocked.
	 *
	 * @param boolean $isLocked
	 * @return void
	 */
	public function setAccountLocked($isLocked);

	/**
	 * Sets an account enabled or disabled.
	 *
	 * @param boolean $isEnabled
	 * @return void
	 */
	public function setAccountEnabled($isEnabled);

	/**
	 * Gets the algorithm used to encode the account's password.
	 *
	 * @return string
	 */
	public function getAlgorithm();

	/**
	 * Sets the algorithm used to encode the account's password.
	 *
	 * @param string $algorithm
	 * @return void
	 */
	public function setAlgorithm($algorithm);

	/**
	 * Gets the work factor used for the password encoding algorithm.
	 *
	 * @return integer
	 */
	public function getWorkFactor();

	/**
	 * Sets the work factor used for the password encoding algorithm.
	 *
	 * @param integer $workFactor
	 * @return void
	 */
	public function setWorkFactor($workFactor);

	/**
	 * Sets a user's password.
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password);

	/**
	 * Gets the user's plain text password, if it's set.
	 *
	 * @return string or null
	 */
	public function getPlainPassword();

	/**
	 * Sets the user's plain text password.
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPlainPassword($password);

	/**
	 * Sets a user's roles.
	 *
	 * @param array $roles
	 * @return void
	 */
	public function setRoles($roles);

	/**
	 * Sets a user's salt.
	 *
	 * @param string $salt
	 * @return void
	 */
	public function setSalt($salt);

	/**
	 * Gets the e-mail address associated with the account.
	 *
	 * @return string
	 */
	public function getEmail();

	/**
	 * Sets the e-mail address associated with the account.
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email);

	/**
	 * Gets the canonical e-mail address associated with the account.
	 *
	 * @return string
	 */
	public function getEmailCanonical();

	/**
	 * Sets the canonical e-mail address associated with the account.
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmailCanonical($email);

	/**
	 * Set the account's confirmation token.
	 *
	 * @param string $token
	 * @return void
	 */
	public function setConfirmationToken($token);

	/**
	 * Get the account's confirmation token.
	 *
	 * @return string
	 */
	public function getConfirmationToken();

	/**
	 * Removes the account's confirmation token.
	 *
	 * @return void
	 */
	public function removeConfirmationToken();

	/**
	 * Gets the time that an account was created at.
	 *
	 * @return DateTime
	 */
	public function getCreatedAt();

	/**
	 * Gets the time that a password reset request was issued at.
	 *
	 * @return DateTime
	 */
	public function getPasswordRequestedAt();

	/**
	 * Sets the time that a password reset request was issued at.
	 *
	 * @param DateTime $time
	 * @return void
	 */
	public function setPasswordRequestedAt($time);

	/**
	 * Checks if a password request has expired.
	 *
	 * @param integer $ttl
	 * @return boolean
	 */
	public function isPasswordRequestNonExpired($ttl);

	/**
	 * Gets the time that a user last logged in at.
	 *
	 * @return DateTime
	 */
	public function getLastLogin();

	/**
	 * Sets the time that a user last logged in at.
	 *
	 * @param DateTime $time
	 * @return void
	 */
	public function setLastLogin($time);

	/**
	 * Get the number of failed login attempts for the account.
	 *
	 * @return integer
	 */
	public function getFailedLoginAttempts();

	/**
	 * Increments the number of failed login attempts for the account.
	 *
	 * @return void
	 */
	public function incrementFailedLoginAttempts();

	/**
	 * Resets the number of failed login attempts.
	 *
	 * @return void
	 */
	public function resetFailedLoginAttempts();

	/**
	 * Gets the time that the account will next unlock at.
	 * Useful for a failed attempt count lockout.
	 *
	 * @return void
	 */
	public function getAccountLockedUntil();

	/**
	 * Sets the time that the account will next unlock at.
	 * Useful for a failed attempt count lockout.
	 *
	 * @param DateTime $time
	 * @return void
	 */
	public function setAccountLockedUntil($time);
}
