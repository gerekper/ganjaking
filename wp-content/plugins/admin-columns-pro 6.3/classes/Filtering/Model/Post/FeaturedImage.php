<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class FeaturedImage extends Model\Meta {

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $media_id ) {
			$options[ $media_id ] = ac_helper()->image->get_file_name( $media_id );
		}

		return [
			'empty_option' => $this->get_empty_labels(),
			'options'      => $options,
		];
	}

}