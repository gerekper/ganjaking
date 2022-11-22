<?php
/**
 * Handles the order queries with funds conditions.
 *
 * @package WC_Account_Funds
 * @since   2.7.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Order_Query.
 */
class WC_Account_Funds_Order_Query {

	/**
	 * Init.
	 *
	 * @since 2.7.3
	 */
	public static function init() {
		add_filter( 'woocommerce_order_query_args', array( __CLASS__, 'order_query_args' ) );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( __CLASS__, 'get_orders_query' ), 10, 2 );
	}

	/**
	 * Filters the Order query vars.
	 *
	 * @since 2.7.3
	 *
	 * @param array $args The query vars.
	 * @return array
	 */
	public static function order_query_args( $args ) {
		if ( isset( $args['funds_query'] ) && self::is_custom_order_tables_enabled() ) {
			if ( isset( $args['meta_query'] ) ) {
				array_push( $args['meta_query'], ...$args['funds_query'] );
			} else {
				$args['meta_query'] = $args['funds_query']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			}

			unset( $args['funds_query'] );
		}

		return $args;
	}

	/**
	 * Handles WC order query with custom metas
	 *
	 * @since 2.7.3
	 *
	 * @param array $query Query for WC_Order_Query.
	 * @param array $query_vars Query vars from a WC_Order_Query.
	 * @return array
	 */
	public static function get_orders_query( $query, $query_vars ) {
		if ( isset( $query_vars['funds_query'] ) && ! self::is_custom_order_tables_enabled() ) {
			array_push( $query['meta_query'], ...$query_vars['funds_query'] );
		}

		return $query;
	}

	/**
	 * Gets whether the custom order tables are enabled or not.
	 *
	 * @since 2.7.3
	 * @return bool
	 */
	protected static function is_custom_order_tables_enabled() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
			return Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}

		return false;
	}
}

WC_Account_Funds_Order_Query::init();
