<?php

/**
 * This file is part of the EpicoftimewastedUserBundle package.
 *
 * Copyright (c) 2011-2012 Ryan Rogers
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
