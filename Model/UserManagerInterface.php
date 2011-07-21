<?php

namespace Epicoftimewasted\UserBundle\Model;

use Symfony\Component\Validator\Constraint;

interface UserManagerInterface
{
	/**
	 * Returns the class name of the user.
	 *
	 * @return string
	 */
	public function getClass();

	/**
	 * Creates a new skeleton user.
	 *
	 * @return EpicoftimewastedUserInterface
	 */
	public function createUser();

	/**
	 * Creates a temporary user skeleton account.
	 *
	 * @return EpicoftimewastedUserInterface
	 */
	public function createTemporaryUser();

	/**
	 * Creates a secure confirmation token for the account.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @return void
	 */
	public function createConfirmationToken(EpicoftimewastedUserInterface $user);

	/**
	 * Updates a user's canonical information.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @return void
	 */
	public function updateCanonicalFields(EpicoftimewastedUserInterface $user);

	/**
	 * Encodes a user's plain text password, then removes the plain text password.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @return void
	 */
	public function updatePassword(EpicoftimewastedUserInterface $user);

	/**
	 * Updates a user's information.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @param boolean $shouldFlush
	 * @return void
	 */
	public function updateUser(EpicoftimewastedUserInterface $user, $shouldFlush);

	/**
	 * Deletes a user.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @return void
	 */
	public function deleteUser(EpicoftimewastedUserInterface $user);

	/**
	 * Finds a user by the specified criteria.
	 *
	 * @param array $criteria
	 * @return EpicoftimewastedUserInterface or null
	 */
	public function findUserBy(array $criteria);

	/**
	 * Wrapper for findUserBy to find a user by their username.
	 *
	 * @param string $username
	 * @return EpicoftimewastedUserInterface or null
	 */
	public function findUserByUsername($username);

	/**
	 * Wrapper for findUserBy to find a user by their e-mail addres.
	 *
	 * @param string $email
	 * @return EpicoftimewastedUserInterface or null
	 */
	public function findUserByEmail($email);

	/**
	 * Wrapper for findUserBy to find a user by their confirmation token.
	 *
	 * @param string $token
	 * @return EpicoftimewastedUserInterface or null
	 */
	public function findUserByConfirmationToken($token);

	/**
	 * Verifies that the fields provided are unique.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 * @param Constraint $constraint
	 * @return boolean
	 */
	public function validateUnique(EpicoftimewastedUserInterface $user, Constraint $constraint);
}
