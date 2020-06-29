<?php

namespace ACP\ThirdParty\YoastSeo\Filtering;

use ACP\Filtering;

class PrimaryTaxonomy extends Filtering\Model\Meta {

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $term_id ) {
			$term = get_term_by( 'id', $term_id, $this->column->get_taxonomy() );

			if ( ! $term ) {
				continue;
			}

			$options[ $term_id ] = $term->name;
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}