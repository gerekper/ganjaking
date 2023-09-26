<?php

namespace ACP\Filtering\Model\Taxonomy;

use ACP;

class TaxonomyParent extends ACP\Filtering\Model {

	public function get_filtering_vars( $vars ) {
		$vars['parent'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];
		$terms = get_terms( [
			'taxonomy'   => $this->column->get_taxonomy(),
			'hide_empty' => false,
		] );

		foreach ( $terms as $term ) {
			if ( 0 === $term->parent ) {
				continue;
			}

			$parent = get_term_by( 'id', $term->parent, $this->column->get_taxonomy() );
			$options[ $term->parent ] = $parent->name;
		}

		return [
			'options' => $options,
		];
	}

}