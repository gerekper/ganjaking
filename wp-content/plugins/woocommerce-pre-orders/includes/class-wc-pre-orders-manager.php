<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package WC_Pre_Orders/Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pre-Orders Manager class
 *
 * Provides an API for managing pre-orders and their associated actions
 *
 * @since 1.0
 */
class WC_Pre_Orders_Manager {

	/**
	 * The Scheduled action hook used to process a batch of pre-order.
	 *
	 * @var string
	 */
	private static $scheduled_batch_processing_hook = 'wc_pre_orders_process_batch';

	/**
	 * The scheduled action hook used to complete a pre-order.
	 *
	 * @var string
	 */
	private static $scheduled_pre_order_complete_hook = 'wc_pre_orders_complete_pre_order';

	/**
	 * Adds hooks / filters
	 *
	 * @since 1.0
	 * @return \WC_Pre_Orders_Manager
	 */
	public function __construct() {

		$disable = get_option( 'wc_pre_orders_disable_auto_processing', 'no' );

		if ( 'yes' !== $disable ) {
			// If the site is running WC 3.5+ we can use the new queue scheduling library which uses Action Scheduler under the hood.
			if ( version_compare( WC_VERSION, '3.5.0', '>=' ) ) {
				add_action( 'wc_pre_orders_completion_check', array( $this, 'maybe_schedule_batch_processor' ) );
				add_action( self::$scheduled_batch_processing_hook, array( $this, 'schedule_actions_to_complete_pre_orders' ), 10, 2 );
				add_action( self::$scheduled_pre_order_complete_hook, array( __CLASS__, 'complete_pre_order' ) );
			} else {
				// hook into cron event to check if there are pre-orders to automatically complete
				add_action( 'wc_pre_orders_completion_check', array( $this, 'check_for_pre_orders_to_complete' ), 10 );

				// hook into same cron event as above and set products that didn't get any orders to a normal product
				add_action( 'wc_pre_orders_completion_check', array( $this, 'check_for_pre_order_products_to_reset' ), 11 );
			}
		}

		// prevent pre-orders with a 'pending' order status from being auto-cancelled
		add_filter( 'woocommerce_cancel_unpaid_order', array( $this, 'maybe_prevent_pending_order_cancel' ), 10, 2 );

		// prevent order stock reduction when a pre-order charged upon release is completed
		add_filter( 'woocommerce_payment_complete_reduce_order_stock', array( $this, 'maybe_prevent_payment_complete_order_stock_reduction' ), 10, 2 );

		// Automatically cancel pre-order when associated order is fully refunded.
		add_action( 'woocommerce_order_fully_refunded', __CLASS__ . '::cancel_pre_order', 10 );
		add_action( 'woocommerce_product_set_stock_status', array( $this, 'maybe_activate_preorder' ), 10, 3 );

	}

	/**
	 * Schedules individual background actions to process pre-order products passed their release date.
	 *
	 * @since 2.0.0
	 */
	public function maybe_schedule_batch_processor() {
		$product_ids = WC_Pre_Orders_Product::get_products_passed_release_date();

		// Generate a unique batch ID for this set of products which we will process together.
		$batch_id = self::generate_batch_id( $product_ids );

		// If we don't already have a batch processor scheduled for the products which need releasing, schedule one.
		if ( ! empty( $product_ids ) && ! $this->has_scheduled_batch_processor( $product_ids, $batch_id ) ) {
			$this->schedule_batch_processor( $product_ids, $batch_id );

			// Now that we have scheduled the action to process these products. Disable further pre-orders for them.
			$this->disable_pre_orders_for_products( $product_ids );
		}
	}

	/**
	 * Schedules a batch processor for a given batch of product IDs.
	 *
	 * @param int[]  $product_ids The product IDs to batch process pre-orders for.
	 * @param string $product_ids The unique batch ID for this set of products.
	 */
	private function schedule_batch_processor( $product_ids, $batch_id ) {
		// Schedule the batch to run as soon as possible. WC_Action_Queue::add() uses time().
		WC()->queue()->add(
			self::$scheduled_batch_processing_hook,
			array(
				'products' => $product_ids,
				'batch_id' => $batch_id,
			)
		);
	}

	/**
	 * Checks if there's already a scheduled batch processor action for a given set of products.
	 *
	 * @param int[]  $product_ids The product IDs to batch process pre-orders for.
	 * @param string $product_ids The unique batch ID for this set of products.
	 *
	 * @return bool True if there's a scheduled action already, otherwise false.
	 */
	private function has_scheduled_batch_processor( $product_ids, $batch_id ) {
		return null !== WC()->queue()->get_next(
			self::$scheduled_batch_processing_hook,
			array(
				'products' => $product_ids,
				'batch_id' => $batch_id,
			)
		);
	}

	/**
	 * Schedules a background job to process a single pre-order.
	 *
	 * @param WC_Order $order The order the schedule a completetion hook for.
	 */
	private function schedule_pre_order_complete( $order ) {
		$args = array( 'order_id' => $order->get_id() );

		if ( null === WC()->queue()->get_next( self::$scheduled_pre_order_complete_hook, $args ) ) {
			WC()->queue()->schedule_single( time() + MINUTE_IN_SECONDS, self::$scheduled_pre_order_complete_hook, $args );
		}
	}

	/**
	 * Finds pre-orders for a batch of given products and schedules a background job to process them.
	 *
	 * @since 2.0.0
	 *
	 * @param int[]  $product_ids The product IDs to schedule pre-order completion for.
	 * @param string $product_ids The unique batch ID for this set of products.
	 */
	public function schedule_actions_to_complete_pre_orders( $product_ids, $batch_id ) {
		// Generate a meta key flag which is stored on orders we have checked if they need processing by this batch so we can exclude them from future queries.
		$meta_key_flag = "_wc_pre_handled_by_product_batch_{$batch_id}";
		$batch_size    = apply_filters( 'wc_pre_orders_complete_pre_orders_batch_size', 200 );

		// Get pre-orders which haven't been handled by this batch ID.
		$query = new WP_Query(
			array(
				'post_status'    => 'wc-pre-ordered',
				'post_type'      => 'shop_order',
				'posts_per_page' => $batch_size,
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'   => '_wc_pre_orders_is_pre_order',
						'value' => 1,
					),
					array(
						'key'     => $meta_key_flag,
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		// If we got a full batch of orders, we haven't finished.
		$is_batch_complete = count( $query->posts ) !== $batch_size;

		// If we've finished processing these products, release the orders and clean up the meta flags.
		if ( $is_batch_complete ) {
			$this->release_orders_from_batch( $meta_key_flag );
		} else {
			// If we got a full batch of orders, schedule a followup action.
			$this->schedule_batch_processor( $product_ids, $batch_id );
		}

		foreach ( $query->posts as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				continue;
			}

			// Store meta on the order so we know it has been checked by this batch so it can be excluded by future batch queries.
			// Skip setting this meta if this is the last batch, as we've deleted it from all previous orders at this point.
			if ( ! $is_batch_complete ) {
				$order->update_meta_data( $meta_key_flag, 'true' );
				$order->save();
			}

			foreach ( $order->get_items() as $item ) {
				// If this order contains a product we need to process. Schedule an action to process it in the future.
				if ( in_array( $item->get_product_id(), $product_ids ) || in_array( $item->get_variation_id(), $product_ids ) ) {
					$this->schedule_pre_order_complete( $order );
					continue;
				}
			}
		}
	}

	/**
	 * Deletes a batch ID flag stored on orders handled by a batch processor.
	 *
	 * @param string $meta_key_flag The unique meta key used to flag orders as being handled by a batch processor.
	 * @return int The number of orders updated.
	 */
	private function release_orders_from_batch( $meta_key_flag ) {
		global $wpdb;
		return $wpdb->delete( "{$wpdb->prefix}postmeta", array( 'meta_key' => $meta_key_flag ) );
	}

	/**
	 * Called via wp-cron every 5 minutes to check if there are active pre-orders
	 * to complete.
	 *
	 * Note:  If you're planning on calling this manually for testing purposes,
	 * be sure to do so *after* the 'shop_order_status' taxonomy has been added
	 * by woocommerce.
	 *
	 * @since 1.0
	 * @version 1.5.3
	 *
	 * @return \WC_Pre_Orders_Manager
	 */
	public function check_for_pre_orders_to_complete() {
		do_action( 'wc_pre_orders_before_automatic_completion_check' );

		$args = array(
			'post_type'  => 'shop_order',
			'nopaging'   => true,
			'meta_query' => array(
				array(
					'key'   => '_wc_pre_orders_is_pre_order',
					'value' => 1,
				),
			),
		);

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$args['post_status'] = 'wc-pre-ordered';
		} else {
			$args['post_status'] = 'publish';
			$args['tax_query']   = array(
				array(
					'taxonomy' => 'shop_order_status',
					'field'    => 'slug',
					'terms'    => 'pre-ordered',
				),
			);
		}

		$query = new WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return;
		}

		$orders_to_complete  = array();
		$products_to_disable = array();

		foreach ( $query->posts as $order_post ) {
			$order = new WC_Order( $order_post );

			$product = WC_Pre_Orders_Order::get_pre_order_product( $order );

			if ( is_null( $product ) ) {
				continue;
			}
			$availability_timestamp_in_utc = (int) get_post_meta( $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_parent_id() : $product->get_id(), '_wc_pre_orders_availability_datetime', true );

			if ( ! $availability_timestamp_in_utc ) {
				continue;
			}

			$time_now_in_utc = time();

			// If the availability date has passed.
			if ( $availability_timestamp_in_utc <= $time_now_in_utc ) {

				// Add the pre-order to the list to complete.
				$orders_to_complete[] = $order;

				// Keep track of pre-order products to disable pre-orders on
				// after completion.
				$products_to_disable[] = $product->get_id();
			}
		}

		// Complete the pre-orders.
		if ( ! empty( $orders_to_complete ) ) {
			$this->complete_pre_orders( $orders_to_complete );
		}

		// Disable pre-orders on products now that they are available.
		if ( ! empty( $products_to_disable ) ) {
			$this->disable_pre_orders_for_products( array_unique( $products_to_disable ) );
		}

		do_action( 'wc_pre_orders_after_automatic_completion_check' );
	}

	/**
	 * Will reset all products back to no longer being a pre-order product if they have passed the release date/time
	 * but haven't gotten any orders at all, so the previous cron function didn't reset them.
	 *
	 * @since 1.0.5
	 * @return void
	 */
	public function check_for_pre_order_products_to_reset() {
		do_action( 'wc_pre_orders_before_products_reset' );

		$time_now_in_utc = time();

		// Get all products that are currently an active pre order product still
		$pre_order_product_ids = get_posts(
			array(
				'fields'      => 'ids',
				'nopaging'    => true,
				'post_status' => 'publish',
				'post_type'   => 'product',
				'meta_query'  => array(
					'relation' => 'AND',
					array(
						'key'   => '_wc_pre_orders_enabled',
						'value' => 'yes',
					),
					array(
						'key'     => '_wc_pre_orders_availability_datetime',
						'value'   => $time_now_in_utc,
						'compare' => '<',
					),
				),
			)
		);

		if ( ! empty( $pre_order_product_ids ) ) {
			$this->disable_pre_orders_for_products( $pre_order_product_ids );
		}

		do_action( 'wc_pre_orders_after_products_reset' );
	}

	/**
	 * Prevent completed pre-orders from being cancelled - Any new pre-orders that have not been processed yet
	 * (e.g. by checking out via PayPal but not completing purchase) should respect the default order cancel settings
	 *
	 * @since 1.0
	 * @param bool $cancel_order whether to cancel the pending order or not
	 * @param object $order the \WC_Order object
	 * @return bool true if the order should be cancelled, false otherwise
	 */
	public function maybe_prevent_pending_order_cancel( $cancel_order, $order ) {
		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) && 'completed' === WC_Pre_Orders_Order::get_pre_order_status( $order ) ) {
			$cancel_order = false;
		}

		return $cancel_order;
	}

	/**
	 * Prevent order stock reduction when WC_Order::payment_complete() is called for a pre-order charged upon release.
	 * Because order stock for pre-orders charged upon release is reduced during initial checkout, this prevents stock from
	 * being reduced twice.
	 *
	 * @since 1.0
	 * @param bool $reduce_stock whether to reduce stock for the order or not
	 * @param int $order_id the post ID of the order
	 * @return bool true if the order stock should be reduced, false otherwise
	 */
	public function maybe_prevent_payment_complete_order_stock_reduction( $reduce_stock, $order_id ) {

		$order = new WC_Order( $order_id );

		$order_status = $order->get_status();

		// stock reduction should only be prevented when order is being completed
		// ie when current order status is 'processing'
		if ( 'processing' !== $order_status ) {
			return $reduce_stock;
		}

		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) && WC_Pre_Orders_Order::order_will_be_charged_upon_release( $order ) && ! self::is_order_pay_later( $order_id ) ) {
			$reduce_stock = false;
		}

		return $reduce_stock;
	}

	/**
	 * Reduce the stock level for an order and record the stock reduction in data store
	 *
	 * @since 1.5.31
	 * @param object $order the \WC_Order object
	 */
	public static function reduce_stock_level( $order ) {
		$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$order->reduce_order_stock();
		} else {
			wc_reduce_stock_levels( $order->get_id() );
		}
		$order->get_data_store()->set_stock_reduced( $order_id, true );
	}

	/**
	 * Gets all pre-orders
	 *
	 * @since 1.0
	 * @return array
	 */
	public static function get_all_pre_orders() {

		$args = array(
			'post_type'   => 'shop_order',
			'post_status' => 'publish',
			'nopaging'    => true,
			'meta_key'    => '_wc_pre_orders_is_pre_order',
			'meta_value'  => 1,
		);

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$args['post_status'] = array_keys( wc_get_order_statuses() );
		}

		$query = new WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return array();
		}

		$orders = array();

		foreach ( $query->posts as $order_post ) {
			$order    = new WC_Order( $order_post );
			$orders[] = $order;
		}

		return $orders;
	}


	/**
	 * Gets all pre-orders for a given product
	 *
	 * @since 1.0
	 * @param object|int $product
	 * @return array
	 */
	public static function get_all_pre_orders_by_product( $product ) {
		global $wpdb;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$order_ids = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT items.order_id AS id
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_meta ON items.order_item_id = item_meta.order_item_id
				LEFT JOIN {$wpdb->postmeta} AS post_meta ON items.order_id = post_meta.post_id
				WHERE
					items.order_item_type = 'line_item' AND
					item_meta.meta_key = '_product_id' AND
					item_meta.meta_value = %s AND
					post_meta.meta_key = '_wc_pre_orders_is_pre_order' AND
					post_meta.meta_value = '1'
				",
				$product->get_id()
			)
		);

		if ( empty( $order_ids ) ) {
			return array();
		}

		$orders = array();

		foreach ( $order_ids as $order ) {
			$orders[] = new WC_Order( $order->id );
		}

		return $orders;
	}

	/**
	 * Gets all pre-orders for the currently logged in user, or the user identified by $user_id
	 *
	 * @since 1.0
	 * @param int $user_id optional user id to return pre-orders for. Defaults to the currently logged in user.
	 * @return array of WC_Order objects
	 */
	public static function get_users_pre_orders( $user_id = null ) {

		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$args = array(
			'post_type'   => 'shop_order',
			'post_status' => 'publish',
			'nopaging'    => true,
			'meta_query'  => array(
				array(
					'key'   => '_customer_user',
					'value' => $user_id,
				),
				array(
					'key'   => '_wc_pre_orders_is_pre_order',
					'value' => 1,
				),
			),
		);

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$args['post_status'] = array_keys( wc_get_order_statuses() );
		}

		$posts = get_posts( $args );

		$orders = array();

		foreach ( $posts as $order_post ) {
			$order    = new WC_Order( $order_post );
			$orders[] = $order;
		}

		return apply_filters( 'wc_pre_orders_users_pre_orders', $orders, $user_id );
	}

	/**
	 * Returns true if the pre-order identified by $order/$item can be changed to
	 * $new_status
	 *
	 * @since 1.0
	 * @param string $new_status the new status
	 * @param WC_Order $order the order object
	 * @return boolean true if the status can be changed, false otherwise
	 */
	public static function can_pre_order_be_changed_to( $new_status, $order ) {

		$status = WC_Pre_Orders_Order::get_pre_order_status( $order );

		// assume it can not be changed
		$can_be_changed = false;

		switch ( $new_status ) {
			case 'cancelled':
				if ( ! in_array( $status, array( 'cancelled', 'completed', 'trash' ) ) ) {
					$can_be_changed = true;
				}
				break;
			case 'completed':
				if ( 'active' == $status ) {
					$can_be_changed = true;
				}
				break;
		}

		return apply_filters( 'wc_pre_orders_status_can_be_changed_to_' . $new_status, $can_be_changed, $order );
	}


	/**
	 * Return a link for customers to change the status of their pre-order to $status
	 *
	 * @since 1.0
	 * @param string $new_status the new status
	 * @param WC_Order $order the order object
	 * @return string
	 */
	public static function get_users_change_status_link( $new_status, $order ) {
		$order_id    = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
		$action_link = add_query_arg(
			array(
				'order_id' => $order_id,
				'status'   => $new_status,
			)
		);
		$action_link = wp_nonce_url( $action_link, $order_id );

		return apply_filters( 'wc_pre_orders_users_action_link', $action_link, $order, $new_status );
	}


	/**
	 * Gets all products that are currently pre-order enabled
	 *
	 * @since 1.0
	 * @return array of WC_Product objects
	 */
	public static function get_all_pre_order_enabled_products() {

		$args = array(
			'fields'      => 'ids',
			'post_type'   => 'product',
			'post_status' => 'publish',
			'nopaging'    => true,
			'meta_key'    => '_wc_pre_orders_enabled',
			'meta_value'  => 'yes',
		);

		$product_post_ids = get_posts( $args );

		$products = array();

		foreach ( $product_post_ids as $product_id ) {

			$products[] = wc_get_product( $product_id );
		}

		return $products;
	}


	/**
	 * Sends a notification email (using the built-in 'Customer Note' email
	 * template) to all customers associated with the supplied $orders with an active pre-order
	 *
	 * @since 1.0
	 * @param int|WC_Order $order order object or identifier
	 * @param string $message required message to include in notification email to customer
	 */
	public static function email_pre_order_customer( $order, $message ) {
		global $woocommerce;

		// load email classes
		$woocommerce->mailer();

		if ( ! is_object( $order ) ) {
			$order = new WC_Order( $order );
		}

		if ( 'active' !== WC_Pre_Orders_Order::get_pre_order_status( $order ) ) {
			return;
		}

		// set email args
		$args = array(
			'order_id'      => version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(),
			'customer_note' => $message,
		);

		// fire the notification, which sends the emails
		do_action( 'woocommerce_new_customer_note_notification', $args );
	}


	/**
	 * Sends a notification email (using the built-in 'Customer Note' email
	 * template) to all customers associated with the supplied $orders with an active pre-order
	 *
	 * @since 1.0
	 * @param array $orders array of post ID or WC_Order objects
	 * @param string $message required message to include in notification email to customer
	 */
	public static function email_pre_order_customers( $orders, $message ) {
		foreach ( $orders as $order ) {
			self::email_pre_order_customer( $order, $message );
		}
	}

	/**
	 * Sends a notification email (using the built-in 'Customer Note' email template) to all customers who have pre-ordered
	 * the given product
	 *
	 * @since 1.0
	 * @param object|int $product the product to email all pre-ordered customers for
	 * @param string $message required message to include in notification email to customer
	 */
	public static function email_all_pre_order_customers( $product, $message ) {

		$orders = self::get_all_pre_orders_by_product( $product );

		if ( empty( $orders ) ) {
			return;
		}

		self::email_pre_order_customers( $orders, $message );
	}

	/**
	 * Change the release date for  pre-orders by updating the availability date for the pre-ordered product to a new date in the future
	 *
	 * @since 1.0
	 * @param object|int $product the product to change the release date for all pre-orders for
	 * @param string $new_availability_date the new availability date
	 * @param string $message an optional message to include in communications to the customer
	 */
	public static function change_release_date_for_all_pre_orders( $product, $new_availability_date, $message = '' ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		// get new availability date timestamp
		try {

			// get datetime object from site timezone
			$datetime = new DateTime( $new_availability_date, new DateTimeZone( wc_timezone_string() ) );

			// get the unix timestamp (adjusted for the site's timezone already)
			$timestamp = $datetime->format( 'U' );

			// Whether the product's release date passed.
			$is_past_date = $timestamp <= time();

		} catch ( Exception $e ) {
			global $wc_pre_orders;
			$wc_pre_orders->log( $e->getMessage() );
			$timestamp = '';
		}

		// set new availability date for product
		update_post_meta( $product->get_id(), '_wc_pre_orders_availability_datetime', $timestamp );

		// get associated orders
		$orders = self::get_all_pre_orders_by_product( $product );

		// fire action for each order
		foreach ( $orders as $order ) {

			if ( ! is_object( $order ) ) {
				$order = new WC_Order( $order );
			}

			// only delay active pre-orders
			if ( 'active' !== WC_Pre_Orders_Order::get_pre_order_status( $order ) ) {
				continue;
			}

			// When the product's release date passed and the order
			// status is set to 'pre-ordered', mark pre-orders as complete.
			if ( $is_past_date && 'pre-ordered' === $order->get_status() ) {
				// Doing this now to not to wait for the 'wc_pre_orders_completion_check' cron job.
				self::complete_pre_order( $order );
			}

			// Add 'release date changed' order note for admins.
			/* translators: %s: Availability date */
			$order->add_order_note( sprintf( __( 'Pre-order release date changed to %s', 'wc-pre-orders' ), WC_Pre_Orders_Product::get_localized_availability_date( $product, __( 'N/A', 'wc-pre-orders' ) ) ) );

			do_action(
				'wc_pre_orders_pre_order_date_changed',
				array(
					'order'             => $order,
					'availability_date' => $new_availability_date,
					'message'           => $message,
				)
			);
		}

		// Doing this after the pre_order_date_changed emails are sent
		// so the availability date in the email isn't "at a future date".
		if ( $is_past_date ) {
			// Unmark the product as a pre-order now that it's released.
			update_post_meta( $product->get_id(), '_wc_pre_orders_enabled', 'no' );

			do_action( 'wc_pre_orders_pre_orders_disabled_for_product', $product->get_id() );
		}
	}

	/**
	 * Checks to see if order is zero cost.
	 *
	 * @version 1.5.1
	 * @since 1.5.1
	 * @param object $order
	 * @return bool
	 */
	public static function is_zero_cost_order( $order = null ) {
		if ( is_a( $order, 'WC_Order' ) ) {
			return 0 >= ( version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->total : $order->get_total() );
		}

		return false;
	}

	/**
	 * Checks to see if order is initially using pay later gateway.
	 * This is needed because payment method will change later in the process
	 * when customer goes to pay when product is available. But we need to
	 * preserve what the order was originally used so we can know if we need
	 * to reduce stock or not based on that.
	 *
	 * @version 1.5.1
	 * @since 1.5.1
	 * @param object $order
	 * @return bool
	 */
	public static function is_order_pay_later( $order_id = null ) {
		return ( 'yes' === get_post_meta( $order_id, '_wc_pre_orders_is_pay_later', true ) );
	}

	/**
	 * Completes the pre-order by updating the pre-order status to 'completed' and following this process for handling payment :
	 *
	 * - for a pre-order charged upon release AND containing a payment token, an action is fired for the supported gateway
	 *   to hook into an charge the total payment amount. Note that the supported gateway will then call WC_Order::payment_complete()
	 *   upon successful charge
	 *
	 * - for a pre-order charged upon release with no payment token, the order status is changed to 'pending' and an email
	 *   is sent containing a link for the customer to come back to and pay for their order
	 *
	 * - for a pre-order charged upfront, the order status is changed to 'completed' or 'processing' based on the same rules
	 *   from WC_Order::payment_complete() -- this is because payment_complete() has already occurred for these order
	 *
	 * @since 1.0
	 * @param int|WC_Order $order post IDs or order object to complete the pre-order for
	 * @param string $message optional message to include in 'pre-order completed' email to customer
	 */
	public static function complete_pre_order( $order, $message = '' ) {

		if ( ! is_object( $order ) ) {
			$order = new WC_Order( $order );
		}

		if ( ! self::can_pre_order_be_changed_to( 'completed', $order ) ) {
			return;
		}

		// complete pre-order charged upon release.
		if ( WC_Pre_Orders_Order::order_will_be_charged_upon_release( $order ) ) {
			$zero_cost_order = self::is_zero_cost_order( $order );

			$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

			if ( ! $zero_cost_order ) {
				// Suppress stock increase when status update to pending
				remove_action( 'woocommerce_order_status_pending', 'wc_maybe_increase_stock_levels' );
				// update order status to pending so it can be paid by automatic payment,
				// or on pay page by customer if 'pay later' gateway was used.
				$order->update_status( 'pending' );
				add_action( 'woocommerce_order_status_pending', 'wc_maybe_increase_stock_levels' );

				if ( WC_Pre_Orders_Order::order_has_payment_token( $order ) || self::is_order_pay_later( $order_id ) ) {
					// load payment gateways.
					WC()->payment_gateways();

					// fire action for payment gateway to charge pre-order.
					do_action( 'wc_pre_orders_process_pre_order_completion_payment_' . ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method() ), $order );
				}
			} else {
				$product = WC_Pre_Orders_Order::get_pre_order_product( $order );

				// update order status to completed or processing - based on same process from WC_Order::payment_complete()
				if ( ( $product->is_downloadable() && $product->is_virtual() ) || ! apply_filters( 'woocommerce_order_item_needs_processing', true, $product, $order_id ) ) {
					$order->update_status( 'completed' );
				} else {
					$order->update_status( 'processing' );
				}

				self::reduce_stock_level( $order );

			}
		} else { // Complete pre-order charged upfront.

			$product = WC_Pre_Orders_Order::get_pre_order_product( $order );

			// update order status to completed or processing - based on same process from WC_Order::payment_complete()
			if ( ( $product->is_downloadable() && $product->is_virtual() ) || ! apply_filters( 'woocommerce_order_item_needs_processing', true, $product, version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() ) ) {
				$order->update_status( 'completed' );
			} else {
				$order->update_status( 'processing' );
			}
		}

		// update pre-order status to completed
		WC_Pre_Orders_Order::update_pre_order_status( $order, 'completed', $message );

		do_action( 'wc_pre_orders_pre_order_completed', $order, $message );
	}

	/**
	 * Completes the provided pre-orders
	 *
	 * @since 1.0
	 * @param array $orders an array of orders containing a pre-order to complete
	 * @param string $message optional message to include in 'pre-order completed' email to customer
	 */
	public static function complete_pre_orders( $orders, $message = '' ) {
		foreach ( $orders as $order ) {
			self::complete_pre_order( $order, $message );
		}
	}

	/**
	 * Helper function to complete all the pre-orders for a given product
	 *
	 * @since 1.0
	 * @param object|int $product the product to complete all pre-orders for
	 * @param string $message an optional message to include in communications to the customer
	 */
	public static function complete_all_pre_orders( $product, $message ) {

		$orders = self::get_all_pre_orders_by_product( $product );

		if ( empty( $orders ) ) {
			return;
		}

		self::complete_pre_orders( $orders, $message );
	}

	/**
	 * Cancel a pre-orders by changing its order status / pre-order status to 'cancelled'
	 *
	 * @since 1.0
	 * @param int|WC_Order $order post IDs or order object to cancel the pre-order for
	 * @param string $message an optional message to include in communications to the customer
	 */
	public static function cancel_pre_order( $order, $message = '' ) {

		if ( ! is_object( $order ) ) {
			$order = new WC_Order( $order );
		}

		if ( ! WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
			return;
		}

		if ( ! self::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
			return;
		}

		// update the pre-order status
		WC_Pre_Orders_Order::update_pre_order_status( $order, 'cancelled', $message );

		// add 'cancelled' order note for admins
		$order->add_order_note( __( 'Pre-order cancelled', 'wc-pre-orders' ) );

		// update the order status
		$order->update_status( 'cancelled' );

		do_action( 'wc_pre_orders_pre_order_cancelled', $order, $message );
	}

	/**
	 * Cancels pre-orders by changing their order status / pre-order status to 'cancelled'
	 *
	 * @since 1.0
	 * @param array $orders array of post IDs or order objects to cancel pre-orders for
	 * @param string $message an optional message to include in communications to the customer
	 */
	public static function cancel_pre_orders( $orders, $message = '' ) {
		foreach ( $orders as $order ) {
			self::cancel_pre_order( $order, $message );
		}
	}

	/**
	 * Helper function to cancel all pre-orders for a given product
	 *
	 * @see WC_Pre_Orders_Manager::cancel_pre_orders()
	 *
	 * @since 1.0
	 * @param object|int $product the product to complete all pre-orders for
	 * @param string $message an optional message to include in communications to the customer
	 */
	public static function cancel_all_pre_orders( $product, $message ) {

		$orders = self::get_all_pre_orders_by_product( $product );

		if ( empty( $orders ) ) {
			return;
		}

		self::cancel_pre_orders( $orders, $message );
	}

	/**
	 * Helper function to return a formatted pre-order order total, e.g. '$99 charged on Dec 1, 2014'
	 *
	 * @since 1.0
	 * @param string $total formatted order total to modify
	 * @param object|int $product the product that the pre-order contains
	 * @return string the new formatted order total
	 */
	public static function get_formatted_pre_order_total( $total, $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		// get order total format
		if ( WC_Pre_Orders_Product::product_is_charged_upon_release( $product ) ) {
			$formatted_total = get_option( 'wc_pre_orders_upon_release_order_total_format' );
		} else {
			$formatted_total = get_option( 'wc_pre_orders_upfront_order_total_format' );
		}

		// bail if no format is set
		if ( ! $formatted_total ) {
			return $total;
		}

		// add localized availability date if needed
		$formatted_total = str_replace( '{availability_date}', WC_Pre_Orders_Product::get_localized_availability_date( $product ), $formatted_total );

		// add order total
		$formatted_total = str_replace( '{order_total}', $total, $formatted_total );

		return apply_filters( 'wc_pre_orders_pre_order_order_total', $formatted_total, $product );
	}

	/**
	 * Helper method to disable pre-orders for a product, called after the availability date for a pre-order has been reached
	 * and pre-orders are completed
	 *
	 * @since 1.0
	 * @param array|int $product_ids product IDs to disable pre-orders for
	 */
	private function disable_pre_orders_for_products( $product_ids ) {

		if ( ! is_array( $product_ids ) ) {
			$product_ids = array( $product_ids );
		}

		foreach ( $product_ids as $product_id ) {
			update_post_meta( $product_id, '_wc_pre_orders_enabled', 'no' );

			do_action( 'wc_pre_orders_pre_orders_disabled_for_product', $product_id );
		}
	}

	/**
	 * Checks for action to cancel an existing pre order and if needed it gets executed
	 *
	 * @since 1.0.3
	 */
	public function check_cancel_pre_order() {
		global $woocommerce;

		if ( ! isset( $_GET['order_id'] ) || ! isset( $_GET['status'] ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), absint( $_GET['order_id'] ) ) ) {
			return;
		}

		self::cancel_pre_order( absint( $_GET['order_id'] ) );

		$string = __( 'Your pre-order has been cancelled.', 'wc-pre-orders' );

		// Backwards compatible (pre 2.1) for outputting notice
		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $string );
		} else {
			$woocommerce->add_message( $string );
		}
	}

	/**
	 * Generates a unique key used to recognise if an order has been handled by a batch processeor.
	 *
	 * @param int[] $product_ids An array of product IDs to generate the hash for.
	 * @return string A hash of the product IDs.
	 */
	private static function generate_batch_id( $product_ids ) {
		// Sort the product IDs so a consistant hash is generated.
		sort( $product_ids );
		return md5( implode( array_values( $product_ids ) ) );
	}

	/**
	 * When the setting is enabled makes out of stock products into pre-order products
	 *
	 * @param int $product_id
	 * @param string $stock_status
	 * @param WC_Product $product
	 */
	public function maybe_activate_preorder( $product_id, $stock_status, $product ) {
		global $typenow;

		if ( 'product' !== $typenow && 'outofstock' === $stock_status && 'yes' === get_option( 'wc_pre_orders_auto_pre_order_out_of_stock' ) ) {

			foreach ( $product->get_children() as $child ) {
				$child = wc_get_product( $child );
				$child->set_stock_status( 'instock' );
				$child->set_manage_stock( false );
				$child->save();
			}

			update_post_meta( $product->get_id(), '_wc_pre_orders_enabled', 'yes' );
			$product->set_manage_stock( false );
			$product->set_stock_status( 'instock' );
			$product->save();
		}
	}

} // end \WC_Pre_Orders_Manager class
