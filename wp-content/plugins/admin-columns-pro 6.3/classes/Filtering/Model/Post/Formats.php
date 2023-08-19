<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class Formats extends Model\Post\Taxonomy {

	public function get_filtering_data() {
		$options = $this->get_terms_list( $this->column->get_taxonomy() );
		$options['cpac_empty'] = get_post_format_string( 'standard' );

		return [
			'options' => $options,
		];
	}

}