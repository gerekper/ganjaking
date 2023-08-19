<?php

namespace ACA\JetEngine\Field;

interface MaxLength {

	/**
	 * @return int
	 */
	public function get_maxlength();

	/**
	 * @return bool
	 */
	public function has_maxlength();

}