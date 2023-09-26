<?php

namespace ACP\Helper\Select\Value;

use AC;
use WP_Post;

final class Post
	implements AC\Helper\Select\Value {

	/**
	 * @param WP_Post $post
	 *
	 * @return int
	 */
	public function get_value( $post ) {
		return $post->ID;
	}

}