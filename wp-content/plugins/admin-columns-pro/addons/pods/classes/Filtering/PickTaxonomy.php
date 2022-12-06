<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Field;
use ACA\Pods\Filtering;

class PickTaxonomy extends Filtering {

	public function get_filtering_data() {
		$field = $this->column->get_field();

		if ( ! $field instanceof Field\Pick\Taxonomy ) {
			return false;
		}

		$term_ids = $this->get_meta_values();

		if ( ! $term_ids ) {
			return [];
		}

		$options = [];

		foreach ( $term_ids as $term_id ) {
			$term = get_term_by( 'id', $term_id, $field->get_taxonomy() );
			if ( $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return [
			'options'      => $options,
			'empty_option' => true,
		];
	}

}