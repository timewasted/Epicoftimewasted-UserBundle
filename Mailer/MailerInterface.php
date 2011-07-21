<?php

namespace Epicoftimewasted\UserBundle\Mailer;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;

interface MailerInterface
{
	/**
	 * Send an e-mail to a user to confirm the account creation.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 */
	public function sendConfirmationEmail(EpicoftimewastedUserInterface $user);

	/**
	 * Send an e-mail to a user to confirm the password reset request.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 */
	public function sendResettingPasswordEmail(EpicoftimewastedUserInterface $user);
}
