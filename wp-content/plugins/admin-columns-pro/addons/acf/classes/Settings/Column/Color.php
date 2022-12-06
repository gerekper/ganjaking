<?php

namespace ACA\ACF\Settings\Column;

use AC;

class Color extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	protected function define_options() {
		return [ 'color' ];
	}

	public function format( $value, $original_value ) {
		return ac_helper()->string->get_color_block( $value );
	}

	public function create_view() {
		return '';
	}

}