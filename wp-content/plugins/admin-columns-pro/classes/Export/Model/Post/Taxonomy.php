<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class Taxonomy implements Service {

	private $taxonomy;

	public function __construct( string $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	public function get_value( $id ) {
		$terms = wp_get_post_terms(
			(int) $id,
			$this->taxonomy,
			[
				'fields' => 'names',
			]
		);

		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}

		return implode( ', ', $terms );
	}

}