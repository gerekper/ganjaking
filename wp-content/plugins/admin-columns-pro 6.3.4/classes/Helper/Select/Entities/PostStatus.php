<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;

class PostStatus extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\PostStatus();
		}

		$entities = $this->get_statuses( $args['post_type'] );

		parent::__construct( $entities, $value );
	}

	public function get_total_pages() {
		return 1;
	}

	public function get_page() {
		return 1;
	}

	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

	/**
	 * @param string $post_type
	 *
	 * @return object[]
	 */
	private function get_statuses( $post_type ) {
		$status_count = (array) wp_count_posts( $post_type );

		// Filter statuses that have no posts
		$status_count = array_filter( $status_count );

		if ( empty( $status_count ) ) {
			return [];
		}

		$statuses = array_keys( $status_count );

		$stati = get_post_stati( [ 'internal' => 0 ], 'objects' );

		foreach ( $stati as $k => $status ) {
			if ( ! in_array( $status->name, $statuses ) ) {
				unset( $stati[ $k ] );
			}
		}

		return $stati;
	}

}