<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Operators;

class ID extends Field {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'comment_ID';
	}

}