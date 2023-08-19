<?php

namespace ACP\Helper\Select\Value;

use AC;

final class MimeType
	implements AC\Helper\Select\Value {

	public function get_value( $mime_type ) {
		return $mime_type;
	}

}