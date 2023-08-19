<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ShippingMethod extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new Bindings();

		$alias_order = $bindings->get_unique_alias( 'shippingmethod_order' );
		$alias_meta = $bindings->get_unique_alias( 'shippingmethod_meta' );

		$where = $wpdb->prepare( "{$alias_meta}.meta_value = %s AND {$alias_meta}.meta_key = 'method_id'", $value->get_value() );

		$bindings->where( $where );

		$join = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS {$alias_order} ON ( {$wpdb->posts}.ID = {$alias_order}.order_id ) ";
		$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS {$alias_meta} ON ( {$alias_order}.order_item_id = {$alias_meta}.order_item_id ) ";

		$bindings->join( $join );

		return $bindings;
	}

	public function get_values() {
		$options = [];

		foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
			$options[ $key ] = $method->method_title;
		}

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}