<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;

class Excerpt extends PostField {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'post_excerpt';
	}

}