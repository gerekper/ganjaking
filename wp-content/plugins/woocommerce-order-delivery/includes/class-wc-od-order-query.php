<?php
/**
 * Handles the order queries with delivery conditions.
 *
 * @package WC_OD
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Order_Query.
 */
class WC_OD_Order_Query {

	/**
	 * Init.
	 *
	 * @since 2.4.0
	 */
	public static function init() {
		add_filter( 'woocommerce_order_query_args', array( __CLASS__, 'order_query_args' ) );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( __CLASS__, 'get_orders_query' ), 10, 2 );
	}

	/**
	 * Filters the Order query vars.
	 *
	 * @since 2.4.0
	 *
	 * @param array $args The query vars.
	 * @return array
	 */
	public static function order_query_args( $args ) {
		if ( isset( $args['delivery_query'] ) && wc_od_is_custom_order_tables_enabled() ) {
			if ( isset( $args['meta_query'] ) ) {
				array_push( $args['meta_query'], ...$args['delivery_query'] );
			} else {
				$args['meta_query'] = $args['delivery_query']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			}

			unset( $args['delivery_query'] );
		}

		return $args;
	}

	/**
	 * Handles WC order query with custom metas.
	 *
	 * @since 2.4.0
	 *
	 * @param array $query      Query for WC_Order_Query.
	 * @param array $query_vars Query vars from a WC_Order_Query.
	 * @return array
	 */
	public static function get_orders_query( $query, $query_vars ) {
		if ( isset( $query_vars['delivery_query'] ) && ! wc_od_is_custom_order_tables_enabled() ) {
			array_push( $query['meta_query'], ...$query_vars['delivery_query'] );
		}

		return $query;
	}
}

WC_OD_Order_Query::init();
