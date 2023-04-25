<?php

class WoocommerceProductFeedsTermDepthRepository {

	private $cache = [];

	/**
	 * Get the depth of a given term.
	 *
	 * @param WP_Term $term WP_Term to find depth of.
	 *
	 * @return int|null
	 */
	public function get_depth( $term ) {
		return $this->get_value_for_term( $term, 'depth' );
	}

	/**
	 * @param WP_Term $term WP_Term to find depth of.
	 *
	 * @return string
	 */
	public function get_hierarchy_string( $term ) {
		return $this->get_value_for_term( $term, 'hierarchy_string' );
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
	 * @param WP_Term $term WP_Term to find depth of.
	 * @param string $value 'depth', or 'hierarchy_string'
	 *
	 * @return mixed
	 */
	private function get_value_for_term( $term, $value ) {
		// If it is cached already, use it.
		if ( isset( $this->cache[ $term->term_id ][ $value ] ) ) {
			return $this->cache[ $term->term_id ][ $value ];
		}

		// Prime the cache.
		$this->prime_cache( $term );

		// Use the primed value.
		return $this->cache[ $term->term_id ][ $value ];
	}

	/**
	 * Prime the cache for a term.
	 *
	 * @param WP_Term $term WP_Term to prime the cache for.
	 */
	private function prime_cache( $term ) {
		// Cache already exists. We're done.
		if ( isset( $this->cache[ $term->term_id ] ) ) {
			return;
		}
		// If the term is NULL (E.g. if we're recursing and a parent has been deleted)
		if ( is_null( $term ) ) {
			$this->cache[ $term->term_id ] = [
				'depth'            => 0,
				'hierarchy_string' => _x(
					'Unknown',
					'Term name to use when passed an invalid term ID',
					'woocommerce_gpf'
				),
			];
			return;
		}
		$depth            = 1;
		$hierarchy_string = $term->name;
		if ( 0 !== $term->parent ) {
			$parent_term      = get_term( $term->parent );
			$depth            = $this->get_depth( $parent_term ) + 1;
			$hierarchy_string = $this->get_hierarchy_string( $parent_term ) .
								apply_filters( 'woocommerce_gpf_hierarchy_separator', ' > ' ) .
								$hierarchy_string;
		}
		$this->cache[ $term->term_id ] = [
			'depth'            => $depth,
			'hierarchy_string' => $hierarchy_string,
		];
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
