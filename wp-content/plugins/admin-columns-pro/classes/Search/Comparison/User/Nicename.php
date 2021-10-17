<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Operators;

class Nicename extends UserField {

	public function __construct() {
		$operators = new Operators( [
			Operators::CONTAINS,
			Operators::EQ,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		], false );

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'user_nicename';
	}

}