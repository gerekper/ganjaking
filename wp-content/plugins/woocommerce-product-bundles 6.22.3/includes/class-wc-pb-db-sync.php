<?php
/**
 * WC_PB_DB_Sync class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks for DB lifecycle management of products, bundles, bundled items and their meta.
 *
 * @class    WC_PB_DB_Sync
 * @version  6.14.1
 */
class WC_PB_DB_Sync {

	/**
	 * Task runner.
	 * @var WC_PB_DB_Sync_Task_Runner
	 */
	private static $sync_task_runner;

	/**
	 * Scan for bundles that need syncing on shutdown?
	 * @var boolean
	 */
	private static $sync_needed = false;

	/**
	 * Enable pre-syncing?
	 * @var int
	 */
	private static $bundled_product_stock_pre_sync = true;

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		// Duplicate bundled items when duplicating a bundle.
		add_action( 'woocommerce_product_duplicate_before_save', array( __CLASS__, 'duplicate_product_before_save' ), 10, 2 );

		// Delete bundled item DB entries when: i) the container bundle is deleted, or ii) the associated product is deleted.
		add_action( 'delete_post', array( __CLASS__, 'delete_post' ), 11 );
		add_action( 'woocommerce_delete_product', array( __CLASS__, 'delete_product' ), 11 );

		// When deleting a bundled item from the DB, clear the transients of the container bundle.
		add_action( 'woocommerce_delete_bundled_item', array( __CLASS__, 'delete_bundled_item' ) );

		// Delete meta reserved to the bundle type.
		add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'delete_reserved_price_meta' ) );

		if ( ! defined( 'WC_PB_DEBUG_STOCK_SYNC' ) ) {

			// Schedule bundled item stock meta update when stock changes.
			add_action( 'woocommerce_product_set_stock', array( __CLASS__, 'product_stock_changed' ), 100 );
			add_action( 'woocommerce_variation_set_stock', array( __CLASS__, 'product_stock_changed' ), 100 );

			// Schedule bundled item stock meta update when stock status changes.
			add_action( 'woocommerce_product_set_stock_status', array( __CLASS__, 'product_stock_status_changed' ), 100, 3 );
			add_action( 'woocommerce_variation_set_stock_status', array( __CLASS__, 'product_stock_status_changed' ), 100, 3 );

			// Schedule bundled item stock meta update when the backorder prop changes.
			add_action( 'woocommerce_product_object_updated_props', array( __CLASS__, 'backorder_prop_changed' ), 100, 2 );

			// Set stock update pre-syncing flag.
			add_action( 'woocommerce_init', array( __CLASS__, 'set_bundled_product_stock_pre_sync' ), 10 );

			if ( ! defined( 'WC_PB_DEBUG_STOCK_PARENT_SYNC' ) ) {

				include_once( WC_PB_ABSPATH . 'includes/class-wc-pb-db-sync-task-runner.php' );

				// Spawn task runner.
				add_action( 'init', array( __CLASS__, 'initialize_sync_task_runner' ), 5 );

				// Sync parent stock status and visibility with children on shutdown (not critical + async anyway).
				add_action( 'shutdown', array( __CLASS__, 'maybe_sync' ), 100 );
			}
		}
	}

	/**
	 * Duplicates bundled items when duplicating a bundle.
	 *
	 * @param  WC_Product  $duplicated_product
	 * @param  WC_Product  $product
	 */
	public static function duplicate_product_before_save( $duplicated_product, $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			$bundled_items      = $product->get_bundled_data_items( 'edit' );
			$bundled_items_data = array();

			if ( ! empty( $bundled_items ) ) {
				foreach ( $bundled_items as $bundled_item ) {

					$bundled_item_data = $bundled_item->get_data();

					$bundled_item_data[ 'bundled_item_id' ] = 0;

					$bundled_items_data[] = $bundled_item_data;
				}

				$duplicated_product->set_bundled_data_items( $bundled_items_data );
			}
		}
	}

	/**
	 * Deletes bundled item DB entries when: i) their container product bundle is deleted, or ii) the associated bundled product is deleted.
	 *
	 * @param  mixed  $id  ID of post being deleted.
	 */
	public static function delete_post( $id ) {

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			if ( 'product' === $post_type ) {
				self::delete_product( $id );
			}
		}
	}

	/**
	 * Deletes bundled item DB entries when: i) their container product bundle is deleted, or ii) the associated bundled product is deleted.
	 *
	 * @param  mixed  $id  ID of product being deleted.
	 */
	public static function delete_product( $id ) {

		// Delete bundled item DB entries and meta when deleting a bundle.
		$bundled_items = WC_PB_DB::query_bundled_items( array(
			'bundle_id' => $id,
			'return'    => 'objects'
		) );

		if ( ! empty( $bundled_items ) ) {
			foreach ( $bundled_items as $bundled_item ) {
				$bundled_item->delete();
			}
		}

		// Delete bundled item DB entries and meta when deleting an associated product.
		$bundled_item_ids = array_keys( wc_pb_get_bundled_product_map( $id, false ) );

		if ( ! empty( $bundled_item_ids ) ) {
			foreach ( $bundled_item_ids as $bundled_item_id ) {
				WC_PB_DB::delete_bundled_item( $bundled_item_id );
			}
		}
	}

	/**
	 * When deleting a bundled item from the DB, clear the transients of the container bundle.
	 *
	 * @param  WC_Bundled_Item_Data  $item  The bundled item DB object being deleted.
	 */
	public static function delete_bundled_item( $item ) {
		$bundle_id = $item->get_bundle_id();
		wc_delete_product_transients( $bundle_id );
	}

	/**
	 * Delete price meta reserved to bundles/composites.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function delete_reserved_price_meta( $product ) {
		if ( false === in_array( $product->get_type(), array( 'bundle', 'composite' ) ) ) {
			$product->delete_meta_data( '_wc_sw_max_price' );
			$product->delete_meta_data( '_wc_sw_max_regular_price' );
		}
	}

	/**
	 * Delete bundled item stock meta cache when a linked product stock changes.
	 *
	 * @param  mixed   $product_id
	 * @param  string  $stock_status
	 * @param  mixed   $product
	 * @return void
	 */
	public static function product_stock_status_changed( $product_id, $stock_status, $product = null ) {

		if ( is_null( $product ) ) {
			$product = wc_get_product( $product_id );
		}

		self::bundled_product_stock_changed( $product );
	}

	/**
	 * Delete bundled item stock meta cache when the 'backorders' prop of a linked product changes.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $changes
	 * @return void
	 */
	public static function backorder_prop_changed( $product, $changes ) {
		if ( in_array( 'backorders', $changes ) ) {
			self::bundled_product_stock_changed( $product );
		}
	}

	/**
	 * Delete bundled item stock meta cache when a linked product stock changes.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function product_stock_changed( $product ) {
		self::bundled_product_stock_changed( $product );
	}

	/**
	 * Set stock update pre-syncing flag.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function set_bundled_product_stock_pre_sync() {
		self::$bundled_product_stock_pre_sync = apply_filters( 'woocommerce_bundled_product_stock_pre_sync', true );
	}

	/**
	 * Trigger bundled items stock meta refresh when product stock (status) changes.
	 *
	 * @since  5.8.0
	 *
	 * @param  mixed  $product
	 * @return void
	 */
	public static function bundled_product_stock_changed( $product ) {

		if ( false === ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( absint( $product ) );
		}

		if ( ! $product ) {
			return;
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		$bundled_item_query_results = WC_PB_DB::query_bundled_items( array(
			'product_id' => $product_id,
			'meta_query' => array(
				array(
					'key'  => 'quantity_min',
					'type' => 'NUMERIC'
				)
			)
		) );

		// Not a bundled product?
		if ( empty( $bundled_item_query_results ) ) {
			return;
		}

		$bundled_item_ids_to_reset = array();
		$bundled_item_min_qty      = array_map( 'absint', wp_list_pluck( $bundled_item_query_results, 'meta_value' ) );
		$bundled_item_ids          = array_map( 'absint', wp_list_pluck( $bundled_item_query_results, 'bundled_item_id' ) );
		$bundle_ids                = array_map( 'absint', wp_list_pluck( $bundled_item_query_results, 'bundle_id' ) );

		$pre_sync_item = false;

		// Pre-sync only simple or subscription products.
		if ( $product->is_type( array( 'simple', 'subscription' ) ) ) {
			// Check if pre-syncing is disabled.
			if ( self::$bundled_product_stock_pre_sync ) {
				$pre_sync_item = true;
			}
		}

		if ( $pre_sync_item ) {

			$stock_meta_map = array();
			$stock_status   = '';
			$max_stock      = '';

			$bundled_item_ids_count = count( $bundled_item_ids );
			$backorders_allowed     = $product->backorders_allowed();

			// All bundled items out of stock.
			if ( false === $product->is_in_stock() ) {

				$stock_status = 'out_of_stock';
				$max_stock    = 0;

				$data = array(
					'stock_status' => $stock_status,
					'max_stock'    => $max_stock
				);

				$stock_meta_map = array_combine( $bundled_item_ids, array_fill( 0, $bundled_item_ids_count, $data ) );

			// All bundled items on backorder.
			} elseif ( $product->is_on_backorder( 1 ) ) {

				$stock_status = 'on_backorder';
				$max_stock    = '';

				$data = array(
					'stock_status' => $stock_status,
					'max_stock'    => $max_stock
				);

				$stock_meta_map = array_combine( $bundled_item_ids, array_fill( 0, $bundled_item_ids_count, $data ) );

			// All bundled items have infinite stock.
			} elseif ( false === $product->managing_stock() ) {

				$stock_status = 'in_stock';
				$max_stock    = '';

				$data = array(
					'stock_status' => $stock_status,
					'max_stock'    => $max_stock
				);

				$stock_meta_map = array_combine( $bundled_item_ids, array_fill( 0, $bundled_item_ids_count, $data ) );

			// Must work with each item individually.
			} else {

				$stock_quantity = $product->get_stock_quantity();
				$stock_quantity = ! is_null( $stock_quantity ) ? $stock_quantity : '';

				// The product is in stock and stock is being managed: Compare with the min item quantity.
				foreach ( $bundled_item_ids as $bundled_item_index => $bundled_item_id ) {

					$item_qty = max( 1, absint( $bundled_item_min_qty[ $bundled_item_index ] ) );
					$item_stock_qty = $stock_quantity;

					if ( '' !== $item_stock_qty ) {
						$item_stock_qty = intval( floor( $item_stock_qty / $item_qty ) * $item_qty );
					}

					if ( '' === $stock_quantity || $stock_quantity >= $item_qty ) {

						$stock_status = 'in_stock';
						$max_stock    = $backorders_allowed ? '' : $item_stock_qty;

					} elseif ( $backorders_allowed ) {

						$stock_status = 'on_backorder';
						$max_stock    = '';

					} else {

						$stock_status = 'out_of_stock';
						$max_stock    = '' !== $item_stock_qty ? $item_stock_qty : 0;
					}

					$data = array(
						'stock_status' => $stock_status,
						'max_stock'    => $max_stock
					);

					$stock_meta_map[ $bundled_item_id ] = $data;
				}
			}

			// Bulk updates.
			self::update_bundled_items_stock_status_meta( $stock_meta_map );
			self::update_bundled_items_max_stock_meta( $stock_meta_map );

		} else {

			// Delete 'stock_status' and 'max_stock' meta.
			WC_PB_DB::bulk_delete_bundled_item_stock_meta( $bundled_item_ids );
		}

		// Reset 'bundled_items_stock_status' on parent bundles.
		$data_store = WC_Data_Store::load( 'product-bundle' );
		$data_store->reset_bundled_items_stock_status( $bundle_ids );

		// Schedule sync task.
		self::schedule_sync();
	}

	/**
	 * Bulk update bundled item 'stock_status' meta.
	 *
	 * @since  5.8.0
	 *
	 * @param  array  $stock_meta_map
	 * @return void
	 */
	private static function update_bundled_items_stock_status_meta( $stock_meta_map ) {

		$stock_status_formatted = array();

		foreach ( $stock_meta_map as $item_id => $data ) {

			if ( ! isset( $stock_status_formatted[ $data[ 'stock_status' ] ] ) ) {
				$stock_status_formatted[ $data[ 'stock_status' ] ] = array();
			}

			$stock_status_formatted[ $data[ 'stock_status' ] ][] = $item_id;
		}

		foreach ( $stock_status_formatted as $meta_value => $bundled_item_ids ) {
			WC_PB_DB::bulk_update_bundled_item_meta( $bundled_item_ids, 'stock_status', $meta_value );
		}
	}

	/**
	 * Bulk update bundled item 'max_stock' meta.
	 *
	 * @since  5.8.0
	 *
	 * @param  array  $stock_meta_map
	 * @return void
	 */
	private static function update_bundled_items_max_stock_meta( $stock_meta_map ) {

		$max_stoke_formatted = array();

		foreach ( $stock_meta_map as $item_id => $data ) {

			$meta_value = '' === $data[ 'max_stock' ] ? 'inf' : $data[ 'max_stock' ];

			if ( ! isset( $stock_status_formatted[ $meta_value ] ) ) {
				$stock_status_formatted[ $meta_value ] = array();
			}

			$stock_status_formatted[ $data[ 'max_stock' ] ][] = $item_id;
		}

		foreach ( $stock_status_formatted as $meta_value => $bundled_item_ids ) {
			WC_PB_DB::bulk_update_bundled_item_meta( $bundled_item_ids, 'max_stock', 'inf' === $meta_value ? '' : $meta_value );
		}
	}

	/**
	 * Spawn task runner.
	 */
	public static function initialize_sync_task_runner() {
		self::$sync_task_runner = new WC_PB_DB_Sync_Task_Runner();
	}

	/**
	 * Maybe sync bundle stock data.
	 *
	 * @since  6.7.8
	 *
	 * @return void
	 */
	public static function maybe_sync() {

		if ( self::has_scheduled_sync() ) {

			if ( self::throttle_sync() ) {
				WC_PB_Core_Compatibility::log( 'Sync throttled...', 'info', 'wc_pb_db_sync_tasks' );
				update_option( 'wc_pb_db_sync_task_throttled', 'yes' );
				return;
			}

			self::sync();

		} elseif ( self::has_throttled_sync() ) {

			if ( self::throttle_sync() ) {
				return;
			}

			WC_PB_Core_Compatibility::log( 'Restarting sync...', 'info', 'wc_pb_db_sync_tasks' );
			self::sync();
		}
	}

	/**
	 * Sync:
	 *
	 * - bundled items stock status;
	 * - bundle stock status; and
	 * - bundle visibility.
	 *
	 * @see  'WC_PB_DB_Sync_Task_Runner::task'
	 *
	 * @return void
	 */
	public static function sync() {

		if ( ! is_object( self::$sync_task_runner ) ) {
			self::initialize_sync_task_runner();
		}

		WC_PB_Core_Compatibility::log( 'Syncing...', 'info', 'wc_pb_db_sync_tasks' );

		if ( self::$sync_task_runner->is_running() ) {
			WC_PB_Core_Compatibility::log( 'Aborting.', 'info', 'wc_pb_db_sync_tasks' );
			// If the task runner is working, throttle the operation.
			// This may happen if the task runner runs longer than our throttling threshold.
			update_option( 'wc_pb_db_sync_task_throttled', 'yes' );
			return;
		}

		$data_store = WC_Data_Store::load( 'product-bundle' );
		$ids        = $data_store->get_bundled_items_stock_sync_status_ids( 'unsynced' );

		if ( empty( $ids ) ) {
			WC_PB_Core_Compatibility::log( 'No IDs found.', 'info', 'wc_pb_db_sync_tasks' );
			update_option( 'wc_pb_db_sync_task_throttled', 'no' );
			return;
		}

		self::$sync_task_runner->push_to_queue( array(
			'sync_ids'   => $ids,
			'delete_ids' => array()
		) );

		if ( self::$sync_task_runner->maybe_save() ) {
			WC_PB_Core_Compatibility::log( sprintf( 'Queued %s IDs.', count( $ids ) ), 'info', 'wc_pb_db_sync_tasks' );
		} else {
			WC_PB_Core_Compatibility::log( 'Aborting! Queue full.', 'info', 'wc_pb_db_sync_tasks' );
		}

		// Log dispatch time.
		update_option( 'wc_pb_db_sync_task_runner_last_run', gmdate( 'U' ) );

		// Remote post to self.
		$dispatched = self::$sync_task_runner->dispatch();

		if ( ! is_wp_error( $dispatched ) ) {
			// Clear pending tasks.
			update_option( 'wc_pb_db_sync_task_throttled', 'no' );
		}
	}

	/**
	 * Determines if a sync operation can be started.
	 * If a sync operation hasn't been throttled, allow new sync tasks to run with a max frequency of 10 seconds.
	 * If a sync operation has been throttled, wait for at least 60 seconds before syncing again.
	 *
	 * @since  6.7.8
	 */
	protected static function throttle_sync() {
		$throttled = get_option( 'wc_pb_db_sync_task_runner_last_run', 0 );
		$delay     = self::has_throttled_sync() ? apply_filters( 'woocommerce_bundles_sync_task_runner_throttled_sync_delay', 60 ) : apply_filters( 'woocommerce_bundles_sync_task_runner_throttle_threshold', 10 );
		return gmdate( 'U' ) - $throttled < $delay;
	}

	/**
	 * Determines if a pending sync operation exists.
	 *
	 * @since  6.7.8
	 */
	protected static function has_throttled_sync() {
		return 'yes' === get_option( 'wc_pb_db_sync_task_throttled', 'no' );
	}

	/**
	 * Determines if a sync operation has been scheduled on this request.
	 *
	 * @since  6.7.8
	 */
	protected static function has_scheduled_sync() {
		return self::$sync_needed;
	}

	/**
	 * Schedules a sync check.
	 */
	public static function schedule_sync() {
		self::$sync_needed = true;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public static function reset_bundled_items_stock_status( $product_id ) {
		_deprecated_function( __METHOD__ . '()', '5.8.0', __CLASS__ . '::bundled_product_stock_changed()' );
		return self::bundled_product_stock_changed( $product_id );
	}

	public static function delete_reserved_price_post_meta( $post_id ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0' );

		$product_type = WC_Product_Factory::get_product_type( $post_id );

		if ( false === in_array( $product_type, array( 'bundle', 'composite' ) ) ) {
			delete_post_meta( $post_id, '_wc_sw_max_price' );
			delete_post_meta( $post_id, '_wc_sw_max_regular_price' );
		}
	}

	public static function delete_bundled_items_stock_cache( $product_id ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::bundled_product_stock_changed()' );
		return self::bundled_product_stock_changed( $product_id );
	}

	public static function delete_bundle_transients( $post_id ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0' );
		if ( $post_id > 0 ) {
			/*
			 * Delete associated bundled items stock cache when clearing product transients.
			 * Workaround for https://github.com/somewherewarm/woocommerce-product-bundles/issues/22 .
			 */
			self::bundled_product_stock_changed( $post_id );
		}
	}
}

WC_PB_DB_Sync::init();
