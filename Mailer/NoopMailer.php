<?php

namespace Epicoftimewasted\UserBundle\Mailer;

use Epicoftimewasted\UserBundle\Model\UserInterface;

class NoopMailer implements MailerInterface
{
	public function sendConfirmationEmail(UserInterface $user)
	{
	}

	public function sendResettingPasswordEmail(UserInterface $user)
	{
	}
}
