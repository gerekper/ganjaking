<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class BackordersAllowed extends Comparison\Meta
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, '_backorders', MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' ),
		] );
	}

}