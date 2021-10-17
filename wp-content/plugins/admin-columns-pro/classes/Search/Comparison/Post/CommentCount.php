<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;
use ACP\Search\Value;

class CommentCount extends PostField {

	public function __construct() {
		$operators = new Operators( [
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $operators, Value::INT );
	}

	protected function get_field() {
		return 'comment_count';
	}

}