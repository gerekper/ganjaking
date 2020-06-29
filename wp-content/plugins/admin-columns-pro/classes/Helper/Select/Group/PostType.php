<?php

namespace ACP\Helper\Select\Group;

use AC;
use WP_Post;

class PostType extends AC\Helper\Select\Group {

	/**
	 * @param WP_Post                 $post
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $post, AC\Helper\Select\Option $option ) {
		return get_post_type_object( $post->post_type )->labels->singular_name;
	}

}