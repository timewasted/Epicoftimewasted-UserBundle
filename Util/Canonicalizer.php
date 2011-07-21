<?php

namespace Epicoftimewasted\UserBundle\Util;

class Canonicalizer implements CanonicalizerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function canonicalize($data)
	{
		return mb_convert_case($data, MB_CASE_LOWER, mb_detect_encoding($data));
	}
}
