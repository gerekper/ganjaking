<?php

class WoocommerceProductFeedsTermDepthRepository {

	private $cache = [];

	/**
	 * Find the depth of a term in the hierarchy.
	 *
	 * @param int|WP_Term $term WP_Term, or term_id to find depth of.
	 *
	 * @return mixed|null
	 */
	public function get_depth( $term ) {
		if ( is_int( $term ) ) {
			$term = get_term( $term );
		}
		if ( ! $term instanceof WP_Term ) {
			return null;
		}
		if ( isset( $this->cache[ $term->term_id ] ) ) {
			return $this->cache[ $term->term_id ];
		}

		$depth = 1;
		if ( 0 !== $term->parent ) {
			$depth = $this->get_depth( $term->parent ) + 1;
		}
		$this->cache[ $term->term_id ] = $depth;

		return $depth;
	}

	/**
	 * Order an array of term objects by their "depth", deepest last.
	 *
	 * @param array $terms
	 *
	 * @return array
	 */
	public function order_terms_by_depth( $terms ) {
		$sorted = $terms;
		usort( $sorted, [ $this, 'sort_callback' ] );

		return $sorted;
	}

	/**
	 * usort callback
	 *
	 * Sort two term objects based on their depth in the hierarchy, deepest last.
	 *
	 * @param $value_a
	 * @param $value_b
	 *
	 * @return int
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function sort_callback( $value_a, $value_b ) {
		// Sort by depth if we can.
		if ( $this->get_depth( $value_a ) > $this->get_depth( $value_b ) ) {
			return 1;
		}
		if ( $this->get_depth( $value_a ) < $this->get_depth( $value_b ) ) {
			return - 1;
		}
		// If depths are equal, sort on term ID to make sure we get
		// consistent results irrespective of input ordering.
		if ( $value_a->term_id > $value_b->term_id ) {
			return 1;
		}

		return - 1;
	}
}
