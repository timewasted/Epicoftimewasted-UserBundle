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
	 * @return UserInterface
	 */
	public function createUser();

	/**
	 * Creates a temporary user skeleton account.
	 *
	 * @return UserInterface
	 */
	public function createTemporaryUser();

	/**
	 * Creates a secure confirmation token for the account.
	 *
	 * @param UserInterface $user
	 * @return void
	 */
	public function createConfirmationToken(UserInterface $user);

	/**
	 * Updates a user's canonical information.
	 *
	 * @param UserInterface $user
	 * @return void
	 */
	public function updateCanonicalFields(UserInterface $user);

	/**
	 * Encodes a user's plain text password, then removes the plain text password.
	 *
	 * @param UserInterface $user
	 * @return void
	 */
	public function updatePassword(UserInterface $user);

	/**
	 * Updates a user's information.
	 *
	 * @param UserInterface $user
	 * @param boolean $shouldFlush
	 * @return void
	 */
	public function updateUser(UserInterface $user, $shouldFlush);

	/**
	 * Deletes a user.
	 *
	 * @param UserInterface $user
	 * @return void
	 */
	public function deleteUser(UserInterface $user);

	/**
	 * Finds a user by the specified criteria.
	 *
	 * @param array $criteria
	 * @return UserInterface or null
	 */
	public function findUserBy(array $criteria);

	/**
	 * Wrapper for findUserBy to find a user by their username.
	 *
	 * @param string $username
	 * @return UserInterface or null
	 */
	public function findUserByUsername($username);

	/**
	 * Wrapper for findUserBy to find a user by their e-mail addres.
	 *
	 * @param string $email
	 * @return UserInterface or null
	 */
	public function findUserByEmail($email);

	/**
	 * Wrapper for findUserBy to find a user by their confirmation token.
	 *
	 * @param string $token
	 * @return UserInterface or null
	 */
	public function findUserByConfirmationToken($token);

	/**
	 * Verifies that the fields provided are unique.
	 *
	 * @param UserInterface $user
	 * @param Constraint $constraint
	 * @return boolean
	 */
	public function validateUnique(UserInterface $user, Constraint $constraint);
}
