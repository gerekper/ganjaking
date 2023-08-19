<?php

namespace ACA\ACF\Field;

interface Number {

	/**
	 * @return string
	 */
	public function get_step();

	/**
	 * @return int|null
	 */
	public function get_min();

	/**
	 * @return int|null
	 */
	public function get_max();

}