<?php

namespace ACA\GravityForms\Field;

use ACA\GravityForms\Field;

interface Container {

	/**
	 * @return Field[]
	 */
	public function get_sub_fields();

	/**
	 * @param string $id
	 *
	 * @return Field
	 */
	public function get_sub_field( $id );

}