<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;

class PostName extends PostField {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'post_name';
	}

}