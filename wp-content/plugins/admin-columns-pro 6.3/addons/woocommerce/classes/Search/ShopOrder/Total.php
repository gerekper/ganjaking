<?php

namespace ACA\WC\Search\ShopOrder;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Total extends Comparison\Meta {

	public function __construct() {
		$operators = new Operators(
			[
				Operators::GT,
				Operators::LT,
				Operators::BETWEEN,
			]
		);

		parent::__construct( $operators, '_order_total', MetaType::POST );
	}

}