<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Operators;

class Agent extends Field {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'comment_agent';
	}

}