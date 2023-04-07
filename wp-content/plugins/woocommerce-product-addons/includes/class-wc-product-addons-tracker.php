<?php
/**
 * WC_PAO_Tracker class
 *
 * @package  WooCommerce Product Add-Ons
 * @since    6.1.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Add-Ons Tracker.
 *
 * @class    WC_PAO_Tracker
 * @version  6.1.3
 */
class WC_PAO_Tracker {

	/**
	 * Property to store reusable query data.
	 *
	 * @var array
	 */
	private static $reusable_data = array();

	/**
	 * Property to store and share tracking data in the class.
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * Property to store the starting time of the process.
	 *
	 * @var int
	 */
	private static $start_time = 0;

	/**
	 * Property to store the tracking events.
	 *
	 * @var array
	 */
	private static $tracking_events = array();

	/**
	 * Property to store the HPOS table name.
	 *
	 * @var string
	 */
	private static $hpos_orders_table = '';

	/**
	 * Property to store how often the data will be invalidated.
	 *
	 * @var string
	 */
	private static $invalidation_interval = '-1 week';

	/**
	 * Initialize the tracker.
	 */
	public static function init() {
		if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			add_filter( 'woocommerce_tracker_data', array( __CLASS__, 'add_tracking_data' ) );

			// Async tasks.
			if ( defined( 'WC_CALYPSO_BRIDGE_TRACKER_FREQUENCY' ) ) {
				add_action( 'wc_pao_hourly', array( __CLASS__, 'maybe_calculate_tracking_data' ) );
			} else {
				add_action( 'wc_pao_daily', array( __CLASS__, 'maybe_calculate_tracking_data' ) );
			}

		}

		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'track_product_addons' ), 100 );
	}

	/**
	 * Add PAO data to the tracked data.
	 *
	 * @param  array  $data
	 * @return array  all the tracking data.
	 */
	public static function add_tracking_data( $data ) {
		$data[ 'extensions' ][ 'wc_pao' ] = self::get_tracking_data();
		return $data;
	}

	/**
	 * Get all tracking data from options.
	 *
	 * @return array PAO's tracking data.
	 */
	private static function get_tracking_data() {
		self::read_data();
		self::maybe_initialize_data();

		// if there are no data calculated, it will calculate them and then send the data.

		if ( self::has_pending_calculations() ) {
			return array();
		}

		if ( isset( self::$data[ 'info' ][ 'started_time' ] ) ) {
			unset( self::$data[ 'info' ][ 'started_time' ] );
		}

		return self::$data;
	}

	/**
	 * Calculates all tracking-related data for the previous month and year.
	 * Runs independently in a background task.
	 *
	 * @see ::maybe_calculate_tracking_data().
	 */
	private static function calculate_tracking_data() {
		self::set_start_time();
		self::calculate_product_data();
		self::calculate_order_data();
	}

	/**
	 * Maybe calculate orders data. Also, handles the caching strategy.
	 *
	 * @return bool Returns true if the data are re-calculated, false otherwise.
	 */
	public static function maybe_calculate_tracking_data() {

		self::read_data();
		self::maybe_initialize_data();

		// Let's check if the array has pending data to calculate.
		if ( self::has_pending_calculations() ) {

			self::calculate_tracking_data();
			self::increase_iterations();
			self::set_option_data();

			return true;
		}

		return false;
	}

	/**
	 * Track product addons first date when saving a product (wp-admin / rest api).
	 *
	 * @param  WC_Product  $product
	 */
	public static function track_product_addons( $product ) {

		$product_addons = array_filter( (array) $product->get_meta( '_product_addons' ) );

		if ( ! empty( $product_addons ) ) {
			$events = get_option( 'woocommerce_pao_tracking_events', array() );

			if ( is_array( $events ) && ! isset( $events[ 'product_addon_first_create_date' ] ) ) {
				$events[ 'product_addon_first_create_date' ] = gmdate( 'Y-m-d H:i:s' );
				update_option( 'woocommerce_pao_tracking_events', $events );
			}
		}
	}

	/**
	 * Calculate product aggregation data.
	 *
	 * @return void
	 */
	private static function calculate_product_data() {

		global $wpdb;

		$data = &self::$data[ 'products' ];

		// Number of products in catalog.
		if ( ! isset( $data[ 'products_count' ] ) ) {
			$data[ 'products_count' ] = (int) $wpdb->get_var( "
				SELECT COUNT(*)
				FROM `{$wpdb->posts}`
				WHERE `post_type` = 'product'
					AND `post_status` = 'publish'
			" );

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with product-level add-ons created over time.
		if ( ! isset( $data[ 'products_with_addons_count' ] ) ) {

			$data[ 'products_with_addons_count' ] = self::get_reusable_data( 'products_with_addons_count' );

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Creation date of first add-on (product-level).
		if ( ! isset( $data[ 'product_addon_first_create_date' ] ) ) {

			// @see maybe_initialize_data() for tracking events default values.
			if ( self::get_reusable_data( 'products_with_addons_count' )
			     && null === self::$tracking_events[ 'product_addon_first_create_date' ] ) {

				self::$tracking_events[ 'product_addon_first_create_date' ] = gmdate( 'Y-m-d H:i:s' );

				update_option( 'woocommerce_pao_tracking_events', self::$tracking_events );
			}

			$data[ 'product_addon_first_create_date' ] = self::$tracking_events[ 'product_addon_first_create_date' ];

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of global add-ons created over time.
		if ( ! isset( $data[ 'global_addons_count' ] ) ) {

			$data[ 'global_addons_count' ] = self::get_reusable_data( 'global_addons_count' );

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Creation date of first product add-on (global).
		if ( ! isset( $data[ 'global_addon_first_create_date' ] ) ) {

			// @see maybe_initialize_data() for tracking events default values.
			if ( self::get_reusable_data( 'global_addons_count' )
			     && null === self::$tracking_events[ 'global_addon_first_create_date' ] ) {

				self::$tracking_events[ 'global_addon_first_create_date' ] = $wpdb->get_var( "
					SELECT post_date_gmt
					FROM `{$wpdb->posts}` AS posts
					WHERE posts.ID IN ( " . self::get_reusable_data( 'global_addons_ids' ) . ")
					ORDER BY post_date_gmt ASC
					LIMIT 1
				" );

				update_option( 'woocommerce_pao_tracking_events', self::$tracking_events );
			}

			$data[ 'global_addon_first_create_date' ] = self::$tracking_events[ 'global_addon_first_create_date' ];

			if ( self::time_or_memory_exceeded() ) {
				// If we don't unset now, it would exit and would need
				// an additional run just to remove the pending flag.
				unset( $data[ 'pending' ] );
				return;
			}
		}

		unset( $data[ 'pending' ] );

	}

	/**
	 * Calculate order data.
	 *
	 * @return void
	 */
	private static function calculate_order_data() {
		global $wpdb;

		$hpos_orders_table = self::$hpos_orders_table;

		$data = &self::$data[ 'orders' ];

		// Number of orders containing products with add-ons (global and product level).
		if ( ! isset( $data[ 'addons_count' ] ) ) {

			$data[ 'addons_count' ] = (int) $wpdb->get_var( "
				SELECT
					COUNT(DISTINCT orders.order_id)
				FROM
					`{$wpdb->prefix}wc_order_product_lookup` AS orders
					INNER JOIN `{$wpdb->prefix}woocommerce_order_items` AS order_items ON orders.order_item_id = order_items.order_item_id
						AND orders.order_id = order_items.order_id
					INNER JOIN `{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta ON order_itemmeta.order_item_id = order_items.order_item_id
				WHERE
					order_itemmeta.meta_key = '_pao_ids'
			" );

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Revenue from products with add-ons over time — including add-ons revenue (global and product level).
		if ( ! isset( $data[ 'addons_revenue' ] ) ) {

			$data[ 'addons_revenue' ] = (float) $wpdb->get_var( "
				SELECT
					SUM(orders.product_gross_revenue)
				FROM
					`{$wpdb->prefix}wc_order_product_lookup` AS orders
					INNER JOIN `{$wpdb->prefix}woocommerce_order_items` AS order_items ON orders.order_item_id = order_items.order_item_id
						AND orders.order_id = order_items.order_id
					INNER JOIN `{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta ON order_itemmeta.order_item_id = order_items.order_item_id
				WHERE
					order_itemmeta.meta_key = '_pao_ids'
			" );

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Multi-currency data.
		if ( ! isset( $data[ 'in_multiple_currencies' ] ) ) {

			if ( WC_PAO_Core_Compatibility::is_hpos_enabled() ) {
				$orders_currencies_count = (int) $wpdb->get_var( "
					SELECT COUNT( DISTINCT( `currency` ) )
					FROM `{$hpos_orders_table}` AS `orders`
				" );
			} else {
				$orders_currencies_count = (int) $wpdb->get_var( "
					SELECT COUNT( DISTINCT( `meta_value` ) )
					FROM `{$wpdb->postmeta}` AS `orders_meta`
					WHERE `orders_meta`.`meta_key` = '_order_currency'
				" );
			}

			$data[ 'in_multiple_currencies' ] = ( $orders_currencies_count > 1 ) ? true : false;

			if ( self::time_or_memory_exceeded() ) {
				// If we don't unset now, it would exit and would need
				// an additional run just to remove the pending flag.
				unset( $data[ 'pending' ] );
				return;
			}
		}

		unset( $data[ 'pending' ] );

	}

	/**
	 * Get any reusable data, without re-querying the DB.
	 *
	 * @param  array  $key  Reusable data key.
	 * @return mixed
	 */
	private static function get_reusable_data( $key = '' ) {

		global $wpdb;

		$valid_keys = array(
			'products_with_addons_count',
			'global_addons_count',
			'global_addons_ids',
		);

		if ( ! in_array( $key, $valid_keys ) ) {
			$notice = sprintf( __( 'Invalid key &quot;%1$s&quot; passed to get_reusable_data.', 'woocommerce-product-addons' ), $key );
			throw new Exception( $notice );
		}

		// Check if the specific data key is already calculated and bail out early.
		if ( isset( self::$reusable_data[ $key ] ) ) {
			return self::$reusable_data[ $key ];
		}

		if ( 'products_with_addons_count' === $key ) {

			self::$reusable_data[ 'products_with_addons_count' ] = (int) $wpdb->get_var( "
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = '_product_addons'
					AND(postmeta.meta_value != 'a:0:{}'
						AND postmeta.meta_value != ''
						AND postmeta.meta_value IS NOT NULL)
			" );

		} elseif ( in_array( $key, array( 'global_addons_count', 'global_addons_ids' ), true ) ) {

			$global_addons_array = $wpdb->get_results( "
				SELECT posts.ID as post_id, postmeta.meta_value AS addons
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'global_product_addon'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = '_product_addons'
					AND(postmeta.meta_value != 'a:0:{}'
						AND postmeta.meta_value != ''
						AND postmeta.meta_value IS NOT NULL)
			", ARRAY_A );

			self::$reusable_data[ 'global_addons_count' ] = 0;

			foreach ( $global_addons_array as $addon ) {
				self::$reusable_data[ 'global_addons_count' ] += count( maybe_unserialize( $addon[ 'addons' ] ) );
			}

			$global_addons_array                        = wp_list_pluck( $global_addons_array, 'post_id' );
			self::$reusable_data[ 'global_addons_ids' ] = self::$reusable_data[ 'global_addons_count' ]
				? implode( ',', array_map( 'absint', $global_addons_array ) )
				: 0;

		}

		return self::$reusable_data[ $key ];

	}

	/**
	 * Check if all the main aggregations have pending data.
	 *
	 * @return bool Pending status.
	 */
	private static function has_pending_calculations() {

		if (
			! isset( self::$data[ 'products' ][ 'pending' ] )
			&& ! isset( self::$data[ 'orders' ][ 'pending' ] )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if execution time is high or if available memory is almost consumed.
	 *
	 * @return bool Returns true if we're about to consume our available resources.
	 */
	private static function time_or_memory_exceeded() {
		return self::time_exceeded() || self::memory_exceeded();
	}

	/**
	 * Initialize data if they are empty month/year has changed.
	 *
	 * @return void
	 */
	private static function maybe_initialize_data() {

		// Default interval is -1 week.
		if ( defined( 'WC_CALYPSO_BRIDGE_TRACKER_FREQUENCY' ) ) {
			self::$invalidation_interval = '-1 day';
		}

		if (
			empty( self::$data )
			|| ! isset( self::$data[ 'info' ][ 'started_time' ] )
			|| self::$data[ 'info' ][ 'started_time' ] <= strtotime( self::$invalidation_interval )
		) {
			self::$data = array(
				'products' => array( 'pending' => true ),
				'orders'   => array( 'pending' => true ),
				'info'     => array(
					'iterations'   => 0,
					'started_time' => time(),
				),
			);
		}

		self::$tracking_events = get_option( 'woocommerce_pao_tracking_events', array() );
		$defaults              = array(
			'product_addon_first_create_date' => null,
			'global_addon_first_create_date'  => null,
		);
		self::$tracking_events = wp_parse_args( self::$tracking_events, $defaults );

		if ( WC_PAO_Core_Compatibility::is_hpos_enabled() ) {
			self::$hpos_orders_table = Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_orders_table_name();
		}
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	private static function time_exceeded() {
		$finish = self::$start_time + 20; // 20 seconds
		return time() >= $finish;
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	private static function memory_exceeded() {
		$memory_limit   = self::get_memory_limit() * 0.8; // 80% of max memory
		$current_memory = memory_get_usage( true );
		return $current_memory >= $memory_limit;
	}

	/**
	 * Get memory limit.
	 *
	 * @return int
	 */
	private static function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Increase iterations.
	 *
	 * @return void
	 */
	private static function increase_iterations() {
		if ( isset( self::$data[ 'info' ] ) && isset( self::$data[ 'info' ][ 'iterations' ] ) ) {
			self::$data[ 'info' ][ 'iterations' ] += 1;
		}
	}

	/**
	 * Set starting time.
	 *
	 * @return void
	 */
	private static function set_start_time() {
		self::$start_time = time();
	}

	/**
	 * Set data from option.
	 *
	 * @return void
	 */
	private static function read_data() {
		self::$data = get_option( 'woocommerce_pao_tracking_data' );
	}

	/**
	 * Set option with data.
	 *
	 * @return void
	 */
	private static function set_option_data() {
		update_option( 'woocommerce_pao_tracking_data', self::$data );
	}
}

WC_PAO_Tracker::init();
