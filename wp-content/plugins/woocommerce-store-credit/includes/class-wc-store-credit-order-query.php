<?php
/**
 * Handles the order queries with store credit conditions.
 *
 * @package WC_Store_Credit/Classes
 * @since   4.2.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Store_Credit_Order_Query' ) ) {

	/**
	 * Class WC_Store_Credit_Order_Query
	 */
	class WC_Store_Credit_Order_Query {

		/**
		 * Constructor.
		 *
		 * @since 4.2.4
		 */
		public function __construct() {
			add_filter( 'woocommerce_order_query_args', array( $this, 'order_query_args' ) );
			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'get_orders_query' ), 10, 2 );
		}

		/**
		 * Filters the Order query vars.
		 *
		 * @since 4.2.4
		 *
		 * @param array $args The query vars.
		 * @return array
		 */
		public static function order_query_args( $args ) {
			if ( isset( $args['store_credit_query'] ) && self::is_custom_order_tables_enabled() ) {
				if ( isset( $args['meta_query'] ) ) {
					array_push( $args['meta_query'], ...$args['store_credit_query'] );
				} else {
					$args['meta_query'] = $args['store_credit_query']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				}

				unset( $args['store_credit_query'] );
			}

			return $args;
		}

		/**
		 * Handles WC order query with custom metas
		 *
		 * @since 4.2.4
		 *
		 * @param array $query Query for WC_Order_Query.
		 * @param array $query_vars Query vars from a WC_Order_Query.
		 * @return array
		 */
		public static function get_orders_query( $query, $query_vars ) {
			if ( isset( $query_vars['store_credit_query'] ) && ! self::is_custom_order_tables_enabled() ) {
				array_push( $query['meta_query'], ...$query_vars['store_credit_query'] );
			}

			return $query;
		}

		/**
		 * Gets whether the custom order tables are enabled or not.
		 *
		 * @since 4.2.4
		 *
		 * @return bool
		 */
		protected static function is_custom_order_tables_enabled() {
			if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
				return Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
			}

			return false;
		}
	}
}

return new WC_Store_Credit_Order_Query();
