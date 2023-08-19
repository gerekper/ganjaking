<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class PostTitle implements FormatValue {

	public function format_value( $id ) {
		return get_post_field( 'post_title', $id );
	}

}
