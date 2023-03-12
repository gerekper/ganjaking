<?php

namespace ACA\ACF\Field\Type;

use WP_Term;

trait TaxonomyFilterableTrait {

	public function get_taxonomies(): array {
		if ( empty( $this->settings['taxonomy'] ) ) {
			return [];
		}

		$valid_terms = [];
		$encoded_terms = $this->settings['taxonomy'];

		foreach ( $encoded_terms as $taxonomy ) {
			if ( $taxonomy instanceof WP_Term ) {
				$valid_terms[] = $taxonomy;
				continue;
			}

			$taxonomy = explode( ':', $taxonomy );
			$term = get_term_by( 'slug', $taxonomy[1], $taxonomy[0] );

			if ( $term instanceof WP_Term ) {
				$valid_terms[] = $term;
			}
		}

		return $valid_terms;
	}

}