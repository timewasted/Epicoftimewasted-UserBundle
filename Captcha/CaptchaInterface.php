<?php

namespace Epicoftimewasted\UserBundle\Captcha;

interface CaptchaInterface
{
	/**
	 * Generate the HTML required to display the captcha to the user.
	 *
	 * @return string|null The HTML to render the captcha or null if the captcha is disabled
	 */
	public function generateCaptcha();

	/**
	 * Checks the validity of the submitted captcha.
	 *
	 * @return boolean True if the captcha is disabled or valid, false otherwise
	 */
	public function isCaptchaValid();
}
