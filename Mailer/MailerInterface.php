<?php

namespace Epicoftimewasted\UserBundle\Mailer;

use Epicoftimewasted\UserBundle\Model\UserInterface;

interface MailerInterface
{
	/**
	 * Send an e-mail to a user to confirm the account creation.
	 *
	 * @param UserInterface $user
	 */
	public function sendConfirmationEmail(UserInterface $user);

	/**
	 * Send an e-mail to a user to confirm the password reset request.
	 *
	 * @param UserInterface $user
	 */
	public function sendResettingPasswordEmail(UserInterface $user);
}
