<?php

namespace ACA\GravityForms;

interface Field {

	/**
	 * @return int
	 */
	public function get_form_id();

	/**
	 * @return string
	 */
	public function get_id();

	/**
	 * @return bool
	 */
	public function is_required();

}