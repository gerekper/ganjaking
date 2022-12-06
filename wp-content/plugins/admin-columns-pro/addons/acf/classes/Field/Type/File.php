<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class File extends Field {

	/**
	 * @return bool
	 */
	public function is_all_file_types_allowed() {
		return isset( $this->settings['mime_types'] )
			? strlen( $this->settings['mime_types'] ) === 0
			: true;
	}

	/**
	 * @return array
	 */
	public function get_allowed_file_types() {
		if ( ! $this->is_all_file_types_allowed() ) {
			return [];
		}

		return isset( $this->settings['mime_types'] )
			? explode( ',', $this->settings['mime_types'] )
			: [];
	}

}