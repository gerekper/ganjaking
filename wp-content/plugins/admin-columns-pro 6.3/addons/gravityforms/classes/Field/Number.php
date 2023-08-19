<?php

namespace ACA\GravityForms\Field;

interface Number {

	/**
	 * @return string
	 */
	public function get_range_min();

	/**
	 * @return string
	 */
	public function get_range_max();

	/**
	 * @return string
	 */
	public function get_step();

}