<?php

namespace ACA\ACF\Field;

interface Date {

	/**
	 * @return string
	 */
	public function get_display_format();

	/**
	 * @return integer
	 */
	public function get_first_day();
}