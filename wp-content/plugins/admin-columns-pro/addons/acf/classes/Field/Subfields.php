<?php

namespace ACA\ACF\Field;

interface Subfields {

	/**
	 * @return array
	 */
	public function get_sub_fields();

	/**
	 * @param string $key
	 *
	 * @return array|null
	 */
	public function get_sub_field( $key );

}