<?php

namespace ACP\Settings\Column;

use AC;

class Gravatar extends AC\Settings\Column\Image {

	public function format( $value, $original_value ) {
		return ac_helper()->image->get_image( $value, $this->get_size_args(), true );
	}

}