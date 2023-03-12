<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class ChildPages implements Service {

	private $post_type;

	public function __construct( string $post_type ) {
		$this->post_type = $post_type;
	}

	private function get_child_ids( $id ): array {
		return get_posts( [
			'post_type'      => $this->post_type,
			'post_parent'    => $id,
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );
	}

	public function get_value( $id ) {
		$titles = [];

		foreach ( $this->get_child_ids( $id ) as $post_id ) {
			$titles[] = (string) get_post_field( 'post_title', (int) $post_id );
		}

		return implode( ', ', $titles );
	}

}