<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;

class PostType extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\PostType();
		}

		$entities = $this->get_post_types( $args );

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
	 * @param array $args
	 *
	 * @return object[]
	 */
	private function get_post_types( $args ) {
		$post_types = [];

		foreach ( get_post_types( $args ) as $post_type ) {
			$post_types[ $post_type ] = get_post_type_object( $post_type );
		}

		return $post_types;
	}

}