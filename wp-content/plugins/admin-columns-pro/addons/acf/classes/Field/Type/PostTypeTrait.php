<?php

namespace ACA\ACF\Field\Type;

trait PostTypeTrait {

	public function get_post_types(): array {
		$post_type = $this->settings['post_type'] ?? null;

		if ( ! $post_type || in_array( $post_type, [ 'all', 'any' ] ) ) {
			return [];
		}

		return (array) $post_type;
	}

}