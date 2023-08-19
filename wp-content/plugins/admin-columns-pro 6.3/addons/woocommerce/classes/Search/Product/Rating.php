<?php

namespace ACA\WC\Search\Product;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Rating extends Comparison\Meta {

	public function __construct() {
		$operators = new Operators( [
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		] );

		parent::__construct( $operators, '_wc_average_rating', MetaType::POST, Value::INT );
	}

}