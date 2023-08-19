<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;
use ACP\Filtering\Strategy;

/**
 * @property Strategy\Post $strategy
 */
class Taxonomy extends Model {

	public function get_filtering_vars( $vars ) {
		if ( $this->strategy instanceof Strategy\Post ) {
			return $this->strategy->get_filterable_request_vars_taxonomy( $vars, $this->get_filter_value(), $this->column->get_taxonomy() );
		}

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'order'        => false,
			'empty_option' => $this->get_empty_labels(),
			'options'      => $this->get_terms_list( $this->column->get_taxonomy() ),
		];
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return array Term options
	 * @since 4.0
	 */
	public function get_terms_list( $taxonomy ) {
		$args = [];

		// Indenting only works if all terms are retrieved
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$args = [ 'hide_empty' => false ];
		}

		/**
		 * @param array $args
		 *
		 * @since 4.0
		 */
		$args = apply_filters( 'acp/filtering/terms_args', $args );

		$terms = get_terms( $taxonomy, $args );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return [];
		}

		return $this->apply_indenting_markup( ac_helper()->array->indent( $terms, 0, 'parent', 'term_id' ) );
	}

	/**
	 * Applies indenting markup for taxonomy dropdown
	 *
	 * @param array $array
	 * @param int   $level
	 * @param array $output
	 *
	 * @return array Output
	 * @since 1.0
	 */
	private function apply_indenting_markup( $array, $level = 0, $output = [] ) {
		$processed = [];

		foreach ( $array as $v ) {
			$prefix = '';

			for ( $i = 0; $i < $level; $i++ ) {
				$prefix .= '&nbsp;&nbsp;';
			}

			// Rename duplicates
			$label = $v->name;

			if ( in_array( $v->name, $processed ) ) {
				$label = $v->name . ' (' . $v->slug . ')';
			}

			$output[ $v->slug ] = $prefix . $label;

			$processed[] = $v->name;

			if ( ! empty( $v->children ) ) {
				$output = $this->apply_indenting_markup( $v->children, ( $level + 1 ), $output );
			}
		}

		return $output;
	}

}