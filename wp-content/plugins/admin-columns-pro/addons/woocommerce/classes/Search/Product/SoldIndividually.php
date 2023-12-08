<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class SoldIndividually extends Comparison\Meta
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, '_sold_individually' );
	}

	public function get_values(): Options {
		return AC\Helper\Select\Options::create_from_array( [
			'yes' => __( 'Sold Individually', 'codepress-admin-columns' ),
			'no'  => __( 'Not Sold Individually', 'codepress-admin-columns' ),
		] );
	}

	protected function get_meta_query( string $operator, Value $value ): array {
		return [
			'key'     => $this->get_meta_key(),
			'value'   => 'yes',
			'compare' => 'yes' === $value->get_value() ? '=' : '!=',
		];
	}

}