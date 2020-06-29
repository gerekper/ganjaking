<?php

namespace ACP\Column\Post;

use AC;
use ACP\Search;

class PostVisibility extends AC\Column
	implements Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-post_visibility' );
		$this->set_label( __( 'Post Visibility', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $post_id ) {
		$states = get_post_states( get_post( $post_id ) );

		if ( isset( $states['protected'] ) ) {
			return $states['protected'];
		}

		if ( isset( $states['private'] ) ) {
			return $states['private'];
		}

		return __( 'Public' );
	}

	public function search() {
		return new Search\Comparison\Post\PostVisibility();
	}

}