<?php

namespace ACP\Helper\Select\Value;

use AC;

final class PostType
	implements AC\Helper\Select\Value {

	/**
	 * @param object $post_type
	 *
	 * @return string
	 */
	public function get_value( $post_type ) {
		return $post_type->name;
	}
}