<?php

namespace ACP\Helper\Select\Formatter;

use AC;

class PostTypeLabel extends AC\Helper\Select\Formatter {

	/**
	 * @param object $post_type
	 *
	 * @return string
	 */
	public function get_label( $post_type ) {
		return $post_type->label;
	}

}