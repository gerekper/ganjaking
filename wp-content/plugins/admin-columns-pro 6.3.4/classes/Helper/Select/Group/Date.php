<?php

namespace ACP\Helper\Select\Group;

use AC;
use WP_Post;

class Date extends AC\Helper\Select\Group {

	/**
	 * @param WP_Post                 $post
	 * @param AC\Helper\Select\Option $option
	 *
	 * @return string
	 */
	public function get_label( $post, AC\Helper\Select\Option $option ) {
		return ac_format_date( 'F Y', strtotime( $post->post_date_gmt ) );
	}

	protected function sort( array $groups ) {
		return $groups;
	}

}