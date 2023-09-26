<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Operators;

class Content extends Field {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::EQ,
			Operators::NOT_CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		], false );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'comment_content';
	}

}