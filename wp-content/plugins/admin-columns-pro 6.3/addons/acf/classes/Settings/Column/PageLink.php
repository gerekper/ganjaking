<?php

namespace ACA\ACF\Settings\Column;

use AC;

class PageLink extends AC\Settings\Column\Post {

	public function format( $id, $original_value ) {
		return is_numeric( $original_value )
			? parent::format( $id, $original_value )
			: $original_value;
	}

}