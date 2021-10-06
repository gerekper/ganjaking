<?php
/**
 * WC_CP_Admin_Analytics_Sync class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    8.3.0-dev
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
use Automattic\WooCommerce\Admin\API\Reports\Products\DataStore as ProductsDataStore;

/**
 * Admin Reports Class for syncing the lookup table.
 *
 * @class    WC_CP_Admin_Analytics_Sync
 * @version  8.3.0-dev
 */
class WC_CP_Admin_Analytics_Sync {

	/**
	 * Keeps track of which order ID is being processed.
	 * @var int
	 */
	protected static $updating_order_id = false;

	/**
	 * The last item in an order is the one to trigger the aggregation process.
	 * @var int
	 */
	protected static $trigger_order_item_id = false;

	/**
	 * Keeps track of which composited order items need to be aggregated into their container.
	 * @var int
	 */
	protected static $composited_order_item_ids = array();

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		/*
		 * Update order stats to merge composited item stats into the container item.
		 */
		add_action( 'woocommerce_analytics_update_product', array( __CLASS__, 'update_order_item_stats' ), 10, 2 );

		/*
		 * Clean composites data when an order is deleted.
		 */
		add_action( 'woocommerce_analytics_delete_order_stats', array( __CLASS__, 'sync_on_order_delete' ), 10 );

		if ( method_exists( WC(), 'queue' ) ) {

			if ( is_admin() ) {

				// Add status tool to regenerate order stats table.
				add_filter( 'woocommerce_debug_tools', array( __CLASS__, 'add_regenerate_order_item_stats_tool' ) );

				// Handle regenerate button clicks.
				add_action( 'admin_init', array( __CLASS__, 'handle_trigger_order_item_stats_update' ) );
			}

			// AS action for regenerating order stats for a batch of orders.
			add_action( 'wc_cp_process_order_item_stats_update_batch', array( __CLASS__, 'process_order_item_stats_update_batch' ), 10, 2 );
		}
	}

	/**
	 * Update order stats if triggered.
	 */
	public static function handle_trigger_order_item_stats_update() {

		if ( ! empty( $_GET[ 'trigger_wc_cp_order_item_stats_db_update' ] ) && isset( $_GET[ '_wc_cp_admin_nonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wc_cp_admin_nonce' ] ), 'wc_cp_trigger_order_item_stats_db_update_nonce' ) && ! self::is_order_item_stats_update_queued() ) {
			self::queue_order_item_stats_update();
			wp_redirect( remove_query_arg( array( 'trigger_wc_cp_order_item_stats_db_update', '_wc_cp_admin_nonce' ) ) );
			exit;
		}
	}

	/**
	 * Number of orders per regeneration batch.
	 *
	 * @return int
	 */
	protected static function get_batch_size() {
		return 20;
	}

	/**
	 * Updates all order stats table rows that involve composite products.
	 *
	 * @return void
	 */
	public static function queue_order_item_stats_update() {

		global $wpdb;

		$order_item_stats_table_name = ProductsDataStore::get_db_table_name();

		// Count distinct orders in order product stats lookup table that also exist in CP items table.
		$count = $wpdb->get_var( "
			SELECT
				COUNT(DISTINCT stats.order_id)
			FROM
				$order_item_stats_table_name AS stats
				INNER JOIN {$wpdb->prefix}term_relationships AS term_relationships 
				    ON stats.product_id = term_relationships.object_id
				INNER JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy 
				    ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
				INNER JOIN {$wpdb->prefix}terms AS terms 
				    ON terms.term_id = term_taxonomy.term_id
			WHERE
				terms.slug = 'composite';
		" );

		if ( ! $count ) {
			return __( 'Tool ran. No orders found to process.', 'woocommerce-composite-products' );
		}

		$batches = ceil( $count / self::get_batch_size() );

		// Cancel existing jobs and restart.
		WC()->queue()->cancel_all( 'wc_cp_process_order_item_stats_update_batch' );

		// Queue first batch.
		self::queue_order_item_stats_update_batch( 1, $batches );

		if ( ! class_exists( 'WC_CP_Admin_Notices' ) ) {
			require_once  WC_CP_ABSPATH . 'includes/admin/class-wc-cp-admin-notices.php' ;
		}

		WC_CP_Admin_Notices::add_maintenance_notice( 'update_order_item_stats' );

		return __( 'Tool ran.', 'woocommerce-composite-products' );
	}

	/**
	 * Schedules a batch of row regenerations for order stats.
	 *
	 * @param  int  $batch
	 * @param  int  $batches
	 * @return void
	 */
	protected static function queue_order_item_stats_update_batch( $batch, $batches ) {
		WC()->queue()->add( 'wc_cp_process_order_item_stats_update_batch', array( $batch, $batches ), 'wc_cp_regenerate_order_item_stats' );
	}

	/**
	 * Indicates whether an order stats regeneration has been attempted in the past.
	 *
	 * @return boolean
	 */
	public static function is_order_item_stats_update_actioned() {

		$order_item_stats_update_batches = WC()->queue()->search( array(
			'hook' => 'wc_cp_process_order_item_stats_update_batch'
		) );

		return ! empty( $order_item_stats_update_batches );
	}

	/**
	 * Indicates whether an order stats regeneration is currently queued.
	 *
	 * @return boolean
	 */
	public static function is_order_item_stats_update_queued() {

		$order_item_stats_update_batches = WC()->queue()->search( array(
			'hook'   => 'wc_cp_process_order_item_stats_update_batch',
			'status' => ActionScheduler_Store::STATUS_PENDING
		) );

		return ! empty( $order_item_stats_update_batches );
	}

	/**
	 * Prepares the controller logic for updating order item stats data.
	 *
	 * @param  WC_Order  $order
	 * @return void
	 */
	protected static function prepare_order_item_stats_update( $order ) {

		self::$updating_order_id = $order->get_id();
		$search_order_items      = $order->get_items();
		$search_order_item_ids   = array_keys( $search_order_items );

		foreach ( $search_order_items as $search_order_item_id => $search_order_item ) {

			if ( $composited_order_item_ids = wc_cp_get_composited_order_items( $search_order_item, $order, true ) ) {
				self::$composited_order_item_ids[ $search_order_item_id ] = $composited_order_item_ids;
			}

			if ( $search_order_item_id === end( $search_order_item_ids ) ) {
				self::$trigger_order_item_id = $search_order_item_id;
			}
		}
	}

	/**
	 * Updates the order item stats table for a single item.
	 *
	 * @return void
	 */
	protected static function process_order_item_stats_update() {

		global $wpdb;

		// Nothing to process?
		if ( false === self::$updating_order_id || empty( self::$composited_order_item_ids ) ) {
			return;
		}

		$cp_table_name = WC_CP_Analytics_Revenue_Data_Store::get_db_table_name();
		$wc_table_name = ProductsDataStore::get_db_table_name();

		// Get existing items in core lookup table.
		$results = $wpdb->get_results( $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT * FROM {$wc_table_name} WHERE order_id = %d",
			self::$updating_order_id
		) );

		$order_item_stats = array();
		foreach ( $results as $item_results ) {
			$order_item_stats[ $item_results->order_item_id ] = array_diff_key( (array) $item_results, array( 'shipping_amount' => 1, 'shipping_tax_amount' => 1 ) );
		}

		// Delete existing items in our lookup table.
		$wpdb->query( $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"DELETE FROM {$cp_table_name} WHERE order_id = %d",
			self::$updating_order_id
		) );

		foreach ( self::$composited_order_item_ids as $container_order_item_id => $composited_order_item_ids ) {

			if ( ! isset( $order_item_stats[ $container_order_item_id ] ) ) {
				continue;
			}

			$item_stats              = $order_item_stats[ $container_order_item_id ];
			$composite_id            = $item_stats[ 'product_id' ];
			$composite_order_item_id = $item_stats[ 'order_item_id' ];

			// Write container item stats.
			$wpdb->replace(
				$cp_table_name,
				array(
					'order_item_id'         => $item_stats[ 'order_item_id' ],
					'parent_order_item_id'  => 0,
					'order_id'              => $item_stats[ 'order_id' ],
					'composite_id'          => $composite_id,
					'product_id'            => $item_stats[ 'product_id' ],
					'variation_id'          => $item_stats[ 'variation_id' ],
					'customer_id'           => $item_stats[ 'customer_id' ],
					'product_qty'           => $item_stats[ 'product_qty' ],
					'product_net_revenue'   => $item_stats[ 'product_net_revenue' ],
					'date_created'          => $item_stats[ 'date_created' ],
					'coupon_amount'         => $item_stats[ 'coupon_amount' ],
					'tax_amount'            => $item_stats[ 'tax_amount' ],
					'product_gross_revenue' => $item_stats[ 'product_gross_revenue' ],
				),
				array(
					'%d', // order_item_id.
					'%d', // parent_order_item_id.
					'%d', // order_id.
					'%d', // composite_id.
					'%d', // product_id.
					'%d', // variation_id.
					'%d', // customer_id.
					'%d', // product_qty.
					'%f', // product_net_revenue.
					'%s', // date_created.
					'%f', // coupon_amount.
					'%f', // tax_amount.
					'%f', // product_gross_revenue.
				)
			); // WPCS: cache ok, DB call ok, unprepared SQL ok.

			foreach ( $composited_order_item_ids as $composited_order_item_id ) {

				if ( ! isset( $order_item_stats[ $composited_order_item_id ] ) ) {
					continue;
				}

				$item_stats = $order_item_stats[ $composited_order_item_id ];

				// Write composited item stats.
				$wpdb->replace(
					$cp_table_name,
					array(
						'order_item_id'         => $item_stats[ 'order_item_id' ],
						'parent_order_item_id'  => $composite_order_item_id,
						'order_id'              => $item_stats[ 'order_id' ],
						'composite_id'          => $composite_id,
						'product_id'            => $item_stats[ 'product_id' ],
						'variation_id'          => $item_stats[ 'variation_id' ],
						'customer_id'           => $item_stats[ 'customer_id' ],
						'product_qty'           => $item_stats[ 'product_qty' ],
						'product_net_revenue'   => $item_stats[ 'product_net_revenue' ],
						'date_created'          => $item_stats[ 'date_created' ],
						'coupon_amount'         => $item_stats[ 'coupon_amount' ],
						'tax_amount'            => $item_stats[ 'tax_amount' ],
						'product_gross_revenue' => $item_stats[ 'product_gross_revenue' ],
					),
					array(
						'%d', // order_item_id.
						'%d', // parent_order_item_id.
						'%d', // order_id.
						'%d', // composite_id.
						'%d', // product_id.
						'%d', // variation_id.
						'%d', // customer_id.
						'%d', // product_qty.
						'%f', // product_net_revenue.
						'%s', // date_created.
						'%f', // coupon_amount.
						'%f', // tax_amount.
						'%f', // product_gross_revenue.
					)
				); // WPCS: cache ok, DB call ok, unprepared SQL ok.
			}
		}

		// Reset.
		self::reset_runtime_variables();
	}

	/**
	 * Resets shared variables in context.
	 *
	 * @param  int  $batch
	 * @param  int  $batches
	 * @return void
	 */
	protected static function reset_runtime_variables() {
		self::$updating_order_id      = false;
		self::$trigger_order_item_id  = false;
		self::$composited_order_item_ids = array();
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Regenerates order stats for single batch of orders.
	 *
	 * @param  int  $batch
	 * @param  int  $batches
	 * @return void
	 */
	public static function process_order_item_stats_update_batch( $batch, $batches ) {

		global $wpdb;

		$order_item_stats_table_name = ProductsDataStore::get_db_table_name();
		$cp_composited_items_table_name = $wpdb->prefix . 'woocommerce_composited_items';

		// Find order IDs to process.
		$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT order_id
			FROM
				$order_item_stats_table_name AS stats
				INNER JOIN {$wpdb->prefix}term_relationships AS term_relationships 
				    ON stats.product_id = term_relationships.object_id
				INNER JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy 
				    ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
				INNER JOIN {$wpdb->prefix}terms AS terms 
				    ON terms.term_id = term_taxonomy.term_id
			WHERE
				terms.slug = 'composite'
			ORDER BY order_id
			LIMIT %d
			OFFSET %d
		", self::get_batch_size(), ( $batch - 1 ) * self::get_batch_size() ), ARRAY_A );

		if ( empty( $results ) ) {
			return;
		}

		$order_ids = wp_list_pluck( $results, 'order_id' );

		// Do the work.
		foreach ( $order_ids as $order_id ) {
			ProductsDataStore::sync_order_products( $order_id );
		}

		// Queue next batch.
		if ( $batch < $batches ) {
			self::queue_order_item_stats_update_batch( $batch + 1, $batches );
		}

		// Invalidate report's cache.
		Automattic\WooCommerce\Admin\API\Reports\Cache::invalidate();
	}

	/**
	 * Updates the order item stats table for a single item.
	 *
	 * @param  int  $order_item_id
	 * @param  int  $order_id
	 * @return void
	 */
	public static function update_order_item_stats( $order_item_id, $order_id ) {

		// Find items to process.
		if ( false === self::$updating_order_id ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			if ( 'shop_order' !== $order->get_type() ) {
				return;
			}

			self::prepare_order_item_stats_update( $order );

		// Currently working for updating_order_id...
		} else {

			// This should never happen.
			if ( self::$updating_order_id !== $order_id ) {
				self::reset_runtime_variables();
				return;
			}

			// Nothing to process?
			if ( empty( self::$composited_order_item_ids ) ) {
				self::reset_runtime_variables();
				return;
			}

			// This should never happen, either.
			if ( empty( self::$trigger_order_item_id ) ) {
				self::reset_runtime_variables();
				return;
			}

			// Data has been collected, but we are not at the end yet.
			if ( $order_item_id !== self::$trigger_order_item_id ) {
				return;
			}
		}

		// Do the work!
		if ( $order_item_id === self::$trigger_order_item_id ) {
			self::process_order_item_stats_update();
		}
	}

	/**
	 * Clean composites data when an order is deleted.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function sync_on_order_delete( $order_id ) {
		global $wpdb;

		$cp_table_name = WC_CP_Analytics_Revenue_Data_Store::get_db_table_name();
		$wpdb->delete( $cp_table_name, array( 'order_id' => $order_id ) );

		/**
		 * Fires when product's reports are removed from database.
		 *
		 * @param int $product_id Product ID.
		 * @param int $order_id   Order ID.
		 */
		do_action( 'woocommerce_analytics_delete_composite', 0, $order_id );

		ReportsCache::invalidate();
	}

	/**
	 * Adds status tool to regenerate the order stats table.
	 *
	 * @param  array
	 * @return array
	 */
	public static function add_regenerate_order_item_stats_tool( $tools ) {

		if ( WC_CP_Core_Compatibility::is_wc_admin_active() ) {

			$tools[ 'cp_regenerate_order_item_stats' ] = array(
				'name'     => __( 'Regenerate Composite Products revenue analytics data', 'woocommerce-composite-products' ),
				'button'   => __( 'Regenerate data', 'woocommerce-composite-products' ),
				'desc'     => sprintf( __( 'Regenerates historical Revenue data under <strong>Analytics > Composites</strong>.', 'woocommerce-composite-products' ), WC_CP()->get_resource_url( 'analytics-revenue' ) ),
				'callback' => array( __CLASS__, 'queue_order_item_stats_update' )
			);
		}

		return $tools;
	}

}
