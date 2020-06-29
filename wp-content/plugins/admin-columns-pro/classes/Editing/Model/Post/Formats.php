<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Formats extends Model\Post {

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => get_post_format_strings(),
		];
	}

	public function save( $id, $value ) {
		$result = set_post_format( $id, $value );

		if ( ! $result ) {
			return false;
		}

		if ( is_wp_error( $result ) ) {
			$this->set_error( $result );

			return false;
		}

		return true;
	}

}