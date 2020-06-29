<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;

class Title extends PostField {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::EQ,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		], false );

		parent::__construct( $operators );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field() {
		return 'post_title';
	}

}