<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACA\WC\Column;
use ACA\WC\Filtering\ShopOrder;
use WP_Query;

/**
 * @property Column\ShopOrder\ShippingMethod $column
 */
class ShippingMethod extends ShopOrder {

	public function __construct( Column\ShopOrder\ShippingMethod $column ) {
		parent::__construct( $column );
	}

	public function filter_by_wc_shipping_method( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$alias = $this->get_meta_alias();

			$where .= $wpdb->prepare( "AND om_{$alias}.meta_value = %s AND om_{$alias}.meta_key = 'method_id'", $this->get_filter_value() );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_join', [ $this, 'join_by_order_itemmeta' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'filter_by_wc_shipping_method' ], 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
			$options[ $key ] = $method->method_title;
		}

		return [
			'options' => $options,
		];
	}

}