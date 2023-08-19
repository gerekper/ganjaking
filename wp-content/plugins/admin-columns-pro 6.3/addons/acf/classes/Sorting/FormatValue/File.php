<?php

namespace ACA\ACF\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class File implements FormatValue {

	public function format_value( $media_id ) {
		return basename( get_attached_file( $media_id ) );
	}

}