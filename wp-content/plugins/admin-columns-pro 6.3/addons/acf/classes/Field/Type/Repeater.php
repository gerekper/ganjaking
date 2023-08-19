<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Repeater extends Field implements Field\Subfields {

	public function get_sub_fields() {
		return isset( $this->settings['sub_fields'] ) && is_array( $this->settings['sub_fields'] )
			? $this->settings['sub_fields']
			: [];
	}

	public function get_sub_field( $key ) {
		$fields = $this->get_sub_fields();

		return isset( $fields[ $key ] ) && is_array( isset( $fields[ $key ] ) )
			? $fields[ $key ]
			: null;
	}

}