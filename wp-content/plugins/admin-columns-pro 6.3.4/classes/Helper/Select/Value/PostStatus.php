<?php

namespace ACP\Helper\Select\Value;

use AC;

final class PostStatus
	implements AC\Helper\Select\Value {

	/**
	 * @param object $status
	 *
	 * @return string
	 */
	public function get_value( $status ) {
		return $status->name;
	}
}