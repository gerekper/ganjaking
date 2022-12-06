<?php

namespace ACA\WC\Search\Product;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Sale extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, Value::INT );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'onsale'    => __( 'On Sale', 'codepress-admin-columns' ),
			'regular'   => __( 'Not on Sale', 'codepress-admin-columns' ),
			'scheduled' => __( 'Scheduled', 'codepress-admin-columns' ),
		] );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();
		$bindings->where( $this->get_where( $value ) )
		         ->meta_query( $this->get_meta_query( $value ) );

		return $bindings;
	}

	public function get_meta_query( Value $value ) {
		$meta_query = [];

		if ( 'scheduled' === $value->get_value() ) {
			$meta_query = [
				'key'     => '_sale_price_dates_from',
				'value'   => time(),
				'compare' => '>',
			];
		}

		return $meta_query;
	}

	public function get_where( Value $value ) {
		global $wpdb;

		$on_sale_products = wc_get_product_ids_on_sale();
		$operator = ( 'onsale' === $value->get_value() ) ? 'IN' : 'NOT IN';

		return sprintf( "{$wpdb->posts}.ID {$operator} ( %s )", implode( ',', $on_sale_products ) );
	}

}