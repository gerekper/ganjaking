<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Service;

class PostField implements Service {

	private $field;

	public function __construct( string $field ) {
		$this->field = $field;
	}

	public function get_value( $id ) {
		$post = get_post( $id );

		return property_exists( $post, $this->field )
			? (string) $post->{$this->field}
			: null;
	}

}