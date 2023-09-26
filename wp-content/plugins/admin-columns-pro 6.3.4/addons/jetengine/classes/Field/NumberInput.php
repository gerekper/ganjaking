<?php

namespace ACA\JetEngine\Field;

interface NumberInput {

	/**
	 * @return bool
	 */
	public function has_step();

	/**
	 * @return string
	 */
	public function get_step();

	/**
	 * @return bool
	 */
	public function has_min_value();

	/**
	 * @return string
	 */
	public function get_min_value();

	/**
	 * @return bool
	 */
	public function has_max_value();

	/**
	 * @return string
	 */
	public function get_max_value();

}