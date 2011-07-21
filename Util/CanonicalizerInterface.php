<?php

namespace Epicoftimewasted\UserBundle\Util;

interface CanonicalizerInterface
{
	/**
	 * Canonicalize a string.
	 *
	 * @param string $data
	 */
	function canonicalize($data);
}
