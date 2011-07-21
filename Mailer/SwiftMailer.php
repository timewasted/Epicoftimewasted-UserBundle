<?php

namespace Epicoftimewasted\UserBundle\Mailer;

use Epicoftimewasted\UserBundle\Mailer\MailerInterface;
use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

class SwiftMailer implements MailerInterface
{
	protected $mailer;

	/**
	 * @var RouterInterface $router
	 */
	protected $router;

	/**
	 * @var EngineInterface $templating
	 */
	protected $templating;

	/**
	 * @var array $parameters
	 */
	protected $parameters;

	/**
	 * Constructor.
	 *
	 * @param .. $mailer
	 * @param RouterInterface $router
	 * @param EngineInterface $templating
	 * @param array $parameters
	 */
	public function __construct($mailer, RouterInterface $router, EngineInterface $templating, array $parameters)
	{
		$this->mailer = $mailer;
		$this->router = $router;
		$this->templating = $templating;
		$this->parameters = $parameters;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendConfirmationEmail(EpicoftimewastedUserInterface $user)
	{
		$template = $this->parameters['confirmation.template'];
		$url = $this->router->generate('epicoftimewasted_user_user_confirm_account', array('token' => $user->getConfirmationToken()), true);
		$message = $this->templating->render($template . '.txt.twig', array(
			'user' => $user,
			'url' => $url,
		));
		$this->sendEmail($message, $this->parameters['from_email'], $user->getEmail());
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendResettingPasswordEmail(EpicoftimewastedUserInterface $user)
	{
		$template = $this->parameters['resetting_password.template'];
		$url = $this->router->generate('epicoftimewasted_user_user_confirm_reset_password', array('token' => $user->getConfirmationToken()), true);
		$message = $this->templating->render($template . '.txt.twig', array(
			'user' => $user,
			'url' => $url,
		));
		$this->sendEmail($message, $this->parameters['from_email'], $user->getEmail());
	}

	/**
	 * @param string $message
	 * @param string $fromEmail
	 * @param string $toEmail
	 */
	protected function sendEmail($message, $fromEmail, $toEmail)
	{
		// The first line of the message is the subject, the rest is the body.
		$message = explode("\n", trim($message));
		$subject = $message[0];
		$body = implode("\n", array_slice($message, 1));

		$email = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($fromEmail)
			->setTo($toEmail)
			->setBody($body);
		$this->mailer->send($email);
	}
}
