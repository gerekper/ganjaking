<?php

namespace ACA\JetEngine\Field;

interface DefaultValue {

	/**
	 * @return mixed
	 */
	public function get_default_value();

	/**
	 * @return bool
	 */
	public function has_default_value();

}