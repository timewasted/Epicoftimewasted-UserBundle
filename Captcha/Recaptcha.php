<?php

namespace Epicoftimewasted\UserBundle\Captcha;

use Symfony\Component\HttpFoundation\Request;

class Recaptcha implements CaptchaInterface
{
	/**
	 * @var Request $request
	 */
	private $request;

	/**
	 * @var boolean $captchaEnabled
	 */
	private $captchaEnabled;

	/**
	 * @var string $publicKey
	 */
	private $publicKey;

	/**
	 * @var string $privateKey
	 */
	private $privateKey;

	/**
	 * Constructor.
	 *
	 * @param Request $request
	 * @param boolean $captchaEnabled
	 * @param string $publicKey
	 * @param string $privateKey
	 */
	public function __construct(Request $request, $captchaEnabled, $publicKey, $privateKey)
	{
		$this->request = $request;
		$this->captchaEnabled = $captchaEnabled;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}

	/**
	 * {@inheritDoc}
	 */
	public function generateCaptcha()
	{
		return $this->captchaEnabled ? recaptcha_get_html($this->publicKey, null, true) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isCaptchaValid()
	{
		if( $this->request->getMethod() !== 'POST' )
			return true;

		$clientIP = $this->request->getClientIp(true);
		$challengeField = $this->request->get('recaptcha_challenge_field');
		$responseField = $this->request->get('recaptcha_response_field');

		return $this->captchaEnabled ? recaptcha_check_answer($this->privateKey, $clientIP, $challengeField, $responseField)->is_valid : true;
	}
}
