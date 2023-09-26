<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP\Search\Operators;

class Number extends Comparison\Repeater {

	public function __construct( $meta_type, $parent_key, $sub_key ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators );
	}

}