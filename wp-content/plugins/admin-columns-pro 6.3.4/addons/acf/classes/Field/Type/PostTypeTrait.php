<?php

namespace ACA\ACF\Field\Type;

trait PostTypeTrait {

	public function get_post_type() {
		$post_type = isset( $this->settings['post_type'] )
			? $this->settings['post_type']
			: [ 'any' ];

		if ( ! $post_type || in_array( $post_type, [ 'all', 'any' ] ) ) {
			$post_type = [ 'any' ];
		}

		return (array) $post_type;
	}

}