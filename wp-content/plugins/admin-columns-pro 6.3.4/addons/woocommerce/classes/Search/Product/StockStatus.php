<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class StockStatus extends Comparison\Meta
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, '_stock_status', MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'instock'    => __( 'In stock', 'codepress-admin-columns' ),
			'outofstock' => __( 'Out of stock', 'codepress-admin-columns' ),
		] );
	}

}