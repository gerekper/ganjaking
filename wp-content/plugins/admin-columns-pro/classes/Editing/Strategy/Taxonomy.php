<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\Strategy;
use WP_Term;

class Taxonomy implements Strategy {

	protected $taxonomy;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	/**
	 * @param WP_Term|int $term_or_term_id
	 *
	 * @return bool
	 */
	public function user_has_write_permission( $term_or_term_id ) {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return false;
		}

		if ( ! $term_or_term_id instanceof WP_Term ) {
			$term = get_term_by( 'id', $term_or_term_id, $this->taxonomy );

			if ( ! $term instanceof WP_Term ) {
				return false;
			}
		}

		return true;
	}

}