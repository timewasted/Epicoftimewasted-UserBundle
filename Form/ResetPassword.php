<?php

namespace Epicoftimewasted\UserBundle\Form;

use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;

class ResetPassword
{
	/**
	 * @var EpicoftimewastedUserInterface $user
	 */
	public $user;

	/**
	 * @var string $newPassword
	 */
	public $newPassword;

	/**
	 * Constructor.
	 *
	 * @param EpicoftimewastedUserInterface $user
	 */
	public function __construct(EpicoftimewastedUserInterface $user)
	{
		$this->user = $user;
	}
}
