<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP\Search\Operators;

class Text extends Comparison\Repeater {

	public function __construct( $meta_type, $parent_key, $sub_key ) {
		$operators = new Operators( [
			Operators::CONTAINS,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators );
	}

}