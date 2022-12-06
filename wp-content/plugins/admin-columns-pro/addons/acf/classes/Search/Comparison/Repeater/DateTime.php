<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class DateTime extends Comparison\Repeater {

	public function __construct( $meta_type, $parent_key, $sub_key ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::FUTURE,
			Operators::PAST,
			Operators::TODAY,
			Operators::LT_DAYS_AGO,
			Operators::GT_DAYS_AGO,
			Operators::WITHIN_DAYS,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators, Value::DATE, false, new ACP\Search\Labels\Date() );
	}

}