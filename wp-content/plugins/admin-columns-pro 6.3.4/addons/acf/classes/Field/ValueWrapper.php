<?php

namespace ACA\ACF\Field;

interface ValueWrapper {

	/**
	 * @return string
	 */
	public function get_append();

	/**
	 * @return string
	 */
	public function get_prepend();

}