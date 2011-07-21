<?php

namespace Epicoftimewasted\UserBundle\Mailer;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;

class NoopMailer implements MailerInterface
{
	public function sendConfirmationEmail(EpicoftimewastedUserInterface $user)
	{
	}

	public function sendResettingPasswordEmail(EpicoftimewastedUserInterface $user)
	{
	}
}
