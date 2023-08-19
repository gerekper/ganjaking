<?php

namespace ACA\WC\Filtering;

use ACP;
use WP_Query;

abstract class ShopOrder extends ACP\Filtering\Model {

	/**
	 * @return string
	 */
	protected function get_meta_alias() {
		global $wpdb;

		return $wpdb->_escape( str_replace( '-', '_', sanitize_key( $this->column->get_name() ) ) );
	}

	/**
	 * @param string   $join
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function join_by_order_itemmeta( $join, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$alias = $this->get_meta_alias();

			$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi_{$alias} ON ( {$wpdb->posts}.ID = oi_{$alias}.order_id ) ";
			$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om_{$alias} ON ( oi_{$alias}.order_item_id = om_{$alias}.order_item_id ) ";
		}

		return $join;
	}

	/**
	 * @param string   $join
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function join_by_postmeta( $join, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$alias = $this->get_meta_alias();

			$join .= "LEFT JOIN {$wpdb->postmeta} AS pm_{$alias} ON ( pm_{$alias}.post_id = om_{$alias}.meta_value AND ( om_{$alias}.meta_key = '_product_id' OR om_{$alias}.meta_key = '_variation_id' ) )";
		}

		return $join;
	}

	/**
	 * @return string
	 */
	public function groupby_wc_product_ids() {
		global $wpdb;

		return "{$wpdb->posts}.ID";
	}

}