<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class File extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$options = [];

		$ids = $this->get_meta_values();

		if ( $ids ) {
			foreach ( $ids as $post_id ) {
				$options[ $post_id ] = basename( get_attached_file( $post_id ) );
			}
		}

		return [
			'options'      => $options,
			'empty_option' => true,
		];
	}

}