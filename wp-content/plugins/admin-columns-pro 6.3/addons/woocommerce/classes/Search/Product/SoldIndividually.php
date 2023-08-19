<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class SoldIndividually extends Comparison\Meta
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, '_sold_individually', MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'yes' => __( 'Sold Individually', 'codepress-admin-columns' ),
			'no'  => __( 'Not Sold Individually', 'codepress-admin-columns' ),
		] );
	}

	protected function get_meta_query( $operator, Value $value ) {
		return [
			'key'     => $this->get_meta_key(),
			'value'   => 'yes',
			'compare' => 'yes' === $value->get_value() ? '=' : '!=',
		];
	}

}