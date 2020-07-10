<?php

/**
 * Handles order status transitions and keeps bookings in sync
 */
class WC_Booking_Order_Manager {

	/**
	 * ID being synced.
	 *
	 * @var array
	 */
	private static $syncing_ids = array();

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'booking_display' ), 10, 3 );
		// WooCommerce PIP compatibility
		add_action( 'wc_pip_order_item_meta_end', array( $this, 'booking_display' ), 10, 3 );

		// Add a "My Bookings" area to the My Account page
		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'my_account_menu_item' ) );
		add_action( 'woocommerce_account_' . $this->get_endpoint() . '_endpoint', array( $this, 'endpoint_content' ) );
		add_action( 'woocommerce_after_my_account', array( $this, 'legacy_account_page_content' ) );

		// Complete booking orders if virtual
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'complete_order' ), 20, 2 );

		// When an order is processed or completed, we can mark publish the pending bookings
		add_action( 'woocommerce_order_status_processing', array( $this, 'publish_bookings' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'publish_bookings' ), 10, 1 );

		// When an order goes from "Completed" or "Processing" to "Pending Payment", mark bookings as "Unpaid".
		add_action( 'woocommerce_order_status_processing_to_pending', array( $this, 'mark_as_unpaid_bookings' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed_to_pending', array( $this, 'mark_as_unpaid_bookings' ), 10, 1 );

		// When an order is cancelled/fully refunded, cancel the bookings
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'cancel_bookings' ), 10, 1 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'cancel_bookings' ), 10, 1 );
		add_action( 'woocommerce_order_partially_refunded', array( $this, 'cancel_bookings_for_partial_refunds' ), 10, 1 );

		// Status transitions
		add_action( 'before_delete_post', array( $this, 'delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );
		add_action( 'woocommerce_booking_cancelled', array( $this, 'maybe_cancel_order' ) );
		add_action( 'woocommerce_booking_paid', array( $this, 'maybe_process_order' ) );
		add_action( 'woocommerce_booking_unpaid', array( $this, 'maybe_pending_order' ) );

		// Prevent pending being cancelled
		add_filter( 'woocommerce_cancel_unpaid_order', array( $this, 'prevent_cancel' ), 10, 2 );

		// Control the my orders actions.
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'my_orders_actions' ), 10, 2 );

		// Sync order user with booking user
		add_action( 'woocommerce_booking_in-cart_to_unpaid', array( $this, 'attach_new_user' ), 10, 2 );
		add_action( 'woocommerce_booking_in-cart_to_pending-confirmation', array( $this, 'attach_new_user' ), 10, 2 );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
			add_action( 'added_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		} else {
			add_action( 'woocommerce_order_object_updated_props', array( $this, 'sync_booking_customer_id' ), 10, 2 );
		}
		// Failed Order Management.
		add_action( 'woocommerce_order_status_failed', array( $this, 'schedule_failed_order_event' ) );
		add_action( 'woocommerce_bookings_failed_order_expired', array( $this, 'handle_failed_order_scheduled_event' ), 20, 2 );

		// My account Bookings section scripts.
		// add_action( 'wp_enqueue_scripts', array( $this, 'my_account_scripts' ) );
	}

	/**
	 * Show booking data if a line item is linked to a booking ID.
	 */
	public function booking_display( $item_id, $item, $order ) {
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );

		wc_get_template( 'order/booking-display.php', array(
			'booking_ids' => $booking_ids,
			'endpoint'    => $this->get_endpoint(),
		), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
	}

	/**
	 * Is ID being synced?
	 */
	private static function is_syncing( $id ) {
		return in_array( $id, self::$syncing_ids );
	}

	/**
	 * Store ID on sync.
	 */
	private static function syncing_start( $id ) {
		self::$syncing_ids[] = $id;
	}

	/**
	 * Remove ID on sync completion.
	 */
	private static function syncing_stop( $id ) {
		self::$syncing_ids = array_diff( self::$syncing_ids, array( $id ) );
	}

	/**
	 * Return the my-account page endpoint.
	 *
	 * @since 1.9.11
	 * @return string
	 */
	public function get_endpoint() {
		return apply_filters( 'woocommerce_bookings_account_endpoint', 'bookings' );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @since 1.9.11
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( $this->get_endpoint(), EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @since 1.9.11
	 * @param array $vars
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = $this->get_endpoint();
		return $vars;
	}

	/**
	 * Change endpoint title.
	 *
	 * @since 1.9.11
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;
		$is_endpoint = isset( $wp_query->query_vars[ $this->get_endpoint() ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			$title = __( 'Bookings', 'woocommerce-bookings' );
			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @since 1.9.11
	 * @param array $items
	 * @return array
	 */
	public function my_account_menu_item( $items ) {
		// Remove logout menu item.
		if ( array_key_exists( 'customer-logout', $items ) ) {
			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
		}

		// Add bookings menu item.
		$items[ $this->get_endpoint() ] = __( 'Bookings', 'woocommerce-bookings' );

		// Add back the logout item.
		if ( isset( $logout ) ) {
			$items['customer-logout'] = $logout;
		}

		return $items;
	}

	/**
	 * Enqueue scripts and variables for my account pages Bookings section.
	 *
	 * @since 1.15.18
	 */
	public function my_account_scripts() {
		if ( ! is_account_page() ) {
			// Don't enqueue scripts if they are not needed.
			return;
		}

		wp_enqueue_script( 'wc-bookings-user-my-account', WC_BOOKINGS_PLUGIN_URL . '/dist/user-my-account.js', array(), WC_BOOKINGS_VERSION, true );
		$my_account_params = array(
			'timezone_conversion' => 'yes' === WC_Bookings_Timezone_Settings::get( 'use_client_timezone' ),
			'timezone_notice'     => __( 'All bookings start and end times except where stated differently are displayed in the following timezone: ', 'woocommerce-bookings' ),
		);

		wp_localize_script( 'wc-bookings-user-my-account', 'wc_bookings_user_my_account_params', $my_account_params );
	}

	/**
	 * Endpoint HTML content.
	 *
	 * @param int $current_page
	 *
	 * @since    1.9.11
	 * @version  1.10.8
	 */
	public function endpoint_content( $current_page ) {
		$current_page = empty( $current_page ) ? 1 : absint( $current_page );
		$this->my_bookings( $current_page );
	}

	/**
	 * Display the account page content for WooCommerce versions before 2.6
	 *
	 * @since 1.9.11
	 */
	public function legacy_account_page_content() {
		if ( version_compare( WC()->version, '2.6', '<' ) ) {
			$this->my_bookings();
		}
	}

	/**
	 * Show a users bookings in My Account > Bookings.
	 *
	 * @param int $current_page
	 *
	 * @since 1.8.0
	 * @since 1.13.0 Added Today's Bookings to My Account.
	 */
	public function my_bookings( $current_page = 0 ) {
		$user_id      = get_current_user_id();
		$current_page = empty( $current_page ) ? 1 : absint( $current_page );

		if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
			$bookings_per_page = apply_filters( 'woocommerce_bookings_my_bookings_per_page', 10 );

			$today_bookings = WC_Booking_Data_Store::get_bookings_for_user( $user_id, apply_filters( 'woocommerce_bookings_my_bookings_today_query_args', array(
				'order_by'       => apply_filters( 'woocommerce_bookings_my_bookings_today_order_by', 'start_date' ),
				'order'          => 'ASC',
				'date_after'     => current_time( 'timestamp' ),
				'date_before'    => strtotime( 'tomorrow', current_time( 'timestamp' ) ),
				'offset'         => ( $current_page - 1 ) * $bookings_per_page,
				'limit'          => $bookings_per_page,
			) ) );

			$past_bookings = WC_Booking_Data_Store::get_bookings_for_user( $user_id, apply_filters( 'woocommerce_bookings_my_bookings_past_query_args', array(
				'order_by'       => apply_filters( 'woocommerce_bookings_my_bookings_past_order_by', 'start_date' ),
				'order'          => 'DESC',
				'date_before'    => current_time( 'timestamp' ),
				'offset'         => ( $current_page - 1 ) * $bookings_per_page,
				'limit'          => $bookings_per_page,
			) ) );

			$upcoming_bookings = WC_Booking_Data_Store::get_bookings_for_user( $user_id, apply_filters( 'woocommerce_bookings_my_bookings_upcoming_query_args', array(
				'order_by'       => apply_filters( 'woocommerce_bookings_my_bookings_upcoming_order_by', 'start_date' ),
				'order'          => 'ASC',
				'date_after'     => strtotime( 'tomorrow', current_time( 'timestamp' ) ),
				'offset'         => ( $current_page - 1 ) * $bookings_per_page,
				'limit'          => $bookings_per_page,
			) ) );

			$tables = array();
			if ( ! empty( $today_bookings ) ) {
				$tables['today'] = array(
					'header'   => __( 'Today\'s Bookings', 'woocommerce-bookings' ),
					'bookings' => $today_bookings,
				);
			}
			if ( ! empty( $upcoming_bookings ) ) {
				$tables['upcoming'] = array(
					'header'   => __( 'Upcoming Bookings', 'woocommerce-bookings' ),
					'bookings' => $upcoming_bookings,
				);
			}
			if ( ! empty( $past_bookings ) ) {
				$tables['past'] = array(
					'header'   => __( 'Past Bookings', 'woocommerce-bookings' ),
					'bookings' => $past_bookings,
				);
			}

			wc_get_template(
				'myaccount/bookings.php',
				apply_filters( 'woocommerce_bookings_my_bookings_template_args', array(
					'tables'            => apply_filters( 'woocommerce_bookings_account_tables', $tables ),
					'page'              => $current_page,
					'bookings_per_page' => $bookings_per_page,
				) ),
			'woocommerce-bookings/', WC_BOOKINGS_TEMPLATE_PATH );
		} else {
			$all_bookings = WC_Booking_Data_Store::get_bookings_for_user( $user_id );

			if ( ! empty( $all_bookings ) ) {
				wc_get_template( 'myaccount/my-bookings.php', array( 'bookings' => $all_bookings ), 'woocommerce-bookings/', WC_BOOKINGS_TEMPLATE_PATH );
			}
		}
	}

	/**
	 * Called when an order is paid
	 * @param  int $order_id
	 */
	public function publish_bookings( $order_id ) {
		global $wpdb;

		$order          = wc_get_order( $order_id );
		$payment_method = is_callable( array( $order, 'get_payment_method' ) ) ? $order->get_payment_method() : $order->payment_method;

		$order_id = apply_filters( 'woocommerce_bookings_publish_bookings_order_id', $order_id );

		$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		// Don't publish bookings for COD orders, but still schedule their events
		$no_publish = $order->has_status( 'processing' ) && 'cod' === $payment_method;

		foreach ( $bookings as $booking_id ) {
			$booking = get_wc_booking( $booking_id );

			if ( $no_publish ) {
				$booking->maybe_schedule_event( 'reminder' );
				$booking->maybe_schedule_event( 'complete' );
			} else {
				$booking->paid();
			}
		}
	}

	/**
	 * Called when an order goes from Processing or Completed to Pending.
	 * Sets Bookings to status Unpaid.
	 *
	 * @since 1.15.14
	 * @param  int $order_id Id of current order.
	 * @return void
	 */
	public function mark_as_unpaid_bookings( $order_id ) {
		global $wpdb;
		$order    = wc_get_order( $order_id );
		$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		foreach ( $bookings as $booking_id ) {
			$booking = get_wc_booking( $booking_id );
			$booking->set_status( 'unpaid' );
			$booking->save();
		}
	}

	/**
	 * Complete virtual booking orders,
	 *
	 * Hooked into a filter that changes the status for bookings.
	 *
	 * @param $order_status
	 * @param $order_id
	 * @return string
	 */
	public function complete_order( $order_status, $order_id ) {
		$order = wc_get_order( $order_id );
		if ( 'processing' === $order_status
			&& $order->has_status( array( 'on-hold', 'pending', 'failed' ) ) ) {
			$virtual_booking_order = false;

			if ( count( $order->get_items() ) < 1 ) {
				return $order_status;
			}

			foreach ( $order->get_items() as $item ) {
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					if ( 'line_item' === $item['type'] ) {
						$product               = $order->get_product_from_item( $item );
						$virtual_booking_order = $product && $product->is_virtual() && $product->is_type( 'booking' );
					}
				} else {
					if ( $item->is_type( 'line_item' ) ) {
						$product               = $item->get_product();
						$virtual_booking_order = $product && $product->is_virtual() && $product->is_type( 'booking' );
					}
				}
				if ( ! $virtual_booking_order ) {
					break;
				}
			}
			// virtual order, mark as completed
			if ( $virtual_booking_order ) {
				return 'completed';
			}
		}

		// non-virtual order, return original status
		return $order_status;
	}

	/**
	 * Cancel bookings with order.
	 * @param  int $order_id
	 */
	public function cancel_bookings( $order_id ) {
		$order    = wc_get_order( $order_id );
		$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		foreach ( $bookings as $booking_id ) {
			if ( self::is_syncing( $booking_id ) ) {
				continue;
			}
			$booking = get_wc_booking( $booking_id );
			$booking->update_status( 'cancelled' );
		}

		self::syncing_stop( $order_id );
	}

	/**
	 * Cancel bookings when an order refunded partially.
	 *
	 * @since 1.10.0
	 *
	 * @version 1.10.0
	 *
	 * @see https://github.com/woocommerce/woocommerce-bookings/issues/817
	 *
	 * @param int $order_id Order ID.
	 */
	public function cancel_bookings_for_partial_refunds( $order_id ) {
		global $wpdb;

		$order              = wc_get_order( $order_id );
		$cancelled_bookings = array();

		// Prevents infinite loop during synch.
		update_post_meta( $order_id, '_booking_status_sync', true );

		// Collect booking IDs where refunded qty matches with its order item
		// being refunded.
		foreach ( $order->get_items() as $order_item_id => $item ) {
			$refunded_qty = $order->get_qty_refunded_for_item( $order_item_id );
			if ( 'line_item' === $item['type'] && 0 !== $refunded_qty ) {

				$booking_id = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_booking_order_item_id' AND meta_value = %d", $order_item_id ) );

				if ( $booking_id ) {
					$cancelled_bookings = array_merge(
						$cancelled_bookings,
						$booking_id
					);
				}
			}
		}

		// Update all cancelled bookings.
		foreach ( $cancelled_bookings as $booking_id ) {
			// Skip booking that still in synch state.
			if ( get_post_meta( $booking_id, '_booking_status_sync', true ) ) {
				continue;
			}

			$booking = get_wc_booking( $booking_id );
			$booking->update_status( 'cancelled' );
		}

		WC_Cache_Helper::get_transient_version( 'bookings', true );
		delete_post_meta( $order_id, '_booking_status_sync' );
	}

	/**
	 * Removes bookings related to the order being deleted.
	 *
	 * @param mixed $post_id ID of post being deleted
	 */
	public function delete_post( $post_id ) {
		if ( ! current_user_can( 'delete_posts' ) || ! $post_id ) {
			return;
		}

		if ( 'wc_booking' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$order_id = WC_Booking_Data_Store::get_booking_order_id( $post_id );
			$order = wc_get_order( $order_id );
			$item_count = is_a( $order, 'WC_Order' ) ? count( $order->get_items() ) : 0;

			if ( 1 === $item_count && $order_id && ! self::is_syncing( $order_id ) ) {
				wp_delete_post( $order_id, true );
			}

			$this->clear_cron_hooks( (int) $post_id );

			self::syncing_stop( $post_id );
		}

		if ( 'shop_order' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $post_id );

			foreach ( $bookings as $booking_id ) {
				if ( self::is_syncing( $booking_id ) ) {
					continue;
				}
				wp_delete_post( $booking_id, true );
			}

			self::syncing_stop( $post_id );
		}
	}

	/**
	 * Trash bookings with orders
	 *
	 * @param mixed $post_id
	 */
	public function trash_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		if ( 'wc_booking' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$order_id = WC_Booking_Data_Store::get_booking_order_id( $post_id );
			$order = wc_get_order( $order_id );
			$item_count = is_a( $order, 'WC_Order' ) ? count( $order->get_items() ) : 0;

			// only delete this order if this booking is the only item in it
			if ( 1 === $item_count && $order_id && ! self::is_syncing( $order_id ) ) {
				wp_trash_post( $order_id );
			}

			self::syncing_stop( $post_id );
		}

		if ( 'shop_order' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $post_id );

			foreach ( $bookings as $booking_id ) {
				if ( self::is_syncing( $booking_id ) ) {
					continue;
				}
				wp_trash_post( $booking_id );
			}

			self::syncing_stop( $post_id );
		}
	}

	/**
	 * Untrash bookings with orders
	 *
	 * @param mixed $post_id
	 */
	public function untrash_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		if ( 'wc_booking' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$order_id = WC_Booking_Data_Store::get_booking_order_id( $post_id );

			if ( $order_id && ! self::is_syncing( $order_id ) ) {
				wp_untrash_post( $order_id );
			}

			self::syncing_stop( $post_id );
		}

		if ( 'shop_order' === get_post_type( $post_id ) ) {
			self::syncing_start( $post_id );

			$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $post_id );

			foreach ( $bookings as $booking_id ) {
				if ( self::is_syncing( $booking_id ) ) {
					continue;
				}
				wp_untrash_post( $booking_id );
			}

			self::syncing_stop( $post_id );
		}
	}

	/**
	 * Clear cron hooks for booking
	 *
	 * @param mixed $post_id
	 */
	function clear_cron_hooks( $post_id ) {
		wp_clear_scheduled_hook( 'wc-booking-reminder', array( $post_id ) );
		wp_clear_scheduled_hook( 'wc-booking-complete', array( $post_id ) );
		wp_clear_scheduled_hook( 'wc-booking-remove-inactive-cart', array( $post_id ) );
	}


	/**
	 * Stops WC cancelling unpaid bookings orders.
	 *
	 * @param  bool $return
	 * @param  object $order
	 * @return bool
	 */
	public function prevent_cancel( $return, $order ) {
		$created_via    = is_callable( array( $order, 'get_created_via' ) ) ? $order->get_created_via() : $order->created_via;
		$payment_method = is_callable( array( $order, 'get_payment_method' ) ) ? $order->get_payment_method() : $order->payment_method;

		if ( 'bookings' === $created_via || 'wc-booking-gateway' === $payment_method ) {
			return false;
		}
		return $return;
	}

	/**
	 * My Orders custom actions.
	 *
	 * Remove the pay button when the booking requires confirmation.
	 * @hooked woocommerce_my_account_my_orders_actions
	 * @param  array $actions
	 * @param  WC_Order $order
	 * @return array
	 */
	public function my_orders_actions( $actions, $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return $actions;
		}
		$payment_method = is_callable( array( $order, 'get_payment_method' ) ) ? $order->get_payment_method() : $order->payment_method;

		if ( $order->has_status( 'pending' ) && 'wc-booking-gateway' === $payment_method ) {
			$status = array();

			foreach ( $order->get_items() as $order_item_id => $item ) {
				$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $order_item_id );
				if ( $booking_ids ) {
					foreach ( $booking_ids as $booking_id ) {
						$booking  = new WC_Booking( $booking_id );
						$status[] = $booking->get_status();
					}
				}
			}

			if ( in_array( 'pending-confirmation', $status ) && isset( $actions['pay'] ) ) {
				unset( $actions['pay'] );
			}
		}

		return $actions;
	}

	/**
	 * For 3.0, triggered after an order is updated.
	 * @param  WC_Order $order
	 * @param  array $props
	 */
	public function sync_booking_customer_id( $order, $props ) {
		if ( in_array( 'customer_id', $props ) ) {
			$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( ( is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id ) );

			foreach ( $booking_ids as $booking_id ) {
				$booking = new WC_Booking( $booking_id );

				if ( $booking->get_customer_id() !== $order->get_customer_id() ) {
					$booking->set_customer_id( $order->get_customer_id() );
					$booking->save();
				}
			}
		}

	}

	/**
	 * Sync customer between order + booking. 2.6 and below.
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		if ( '_customer_user' === $meta_key && 'shop_order' === get_post_type( $object_id ) ) {
			global $wpdb;

			$order    = wc_get_order( $object_id );
			$bookings = array();

			foreach ( $order->get_items() as $order_item_id => $item ) {
				if ( 'line_item' == $item['type'] ) {
					$bookings = array_merge( $bookings, $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_booking_order_item_id' AND meta_value = %d", $order_item_id ) ) );
				}
			}

			foreach ( $bookings as $booking_id ) {
				update_post_meta( $booking_id, '_booking_customer_id', $_meta_value );
			}
		}
	}

	/**
	 * Attaches a newly created user (during checkout) to a booking
	 */
	public function attach_new_user( $booking_id, $booking ) {
		if ( 0 === $booking->get_customer_id() && get_current_user_id() > 0 ) {
			$booking->set_customer_id( get_current_user_id() );
			$booking->save( false );
		}
	}

	/**
	 * Cancel order with bookings.
	 *
	 * @param  int $booking_id
	 */
	public function maybe_cancel_order( $booking_id ) {
		$this->maybe_update_order( $booking_id, 'cancelled' );
	}

	/**
	 * Sync order with bookings (paid) only in case where the booking is the only item in the order.
	 *
	 * @param  int $booking_id
	 */
	public function maybe_process_order( $booking_id ) {
		$this->maybe_update_order( $booking_id, 'processing' );
	}

	/**
	 * Sync order with bookings (unpaid) only in case where the booking is the only item in the order.
	 *
	 * @param  int $booking_id
	 */
	public function maybe_pending_order( $booking_id ) {
		$this->maybe_update_order( $booking_id, 'pending' );
	}

	/**
	 * Update booking's related order
	 * This only applies if the order has only the booking as an item.
	 *
	 * @param  int $booking_id
	 * @param  string $status
	 */
	private function maybe_update_order( $booking_id, $status ) {
		// Prevents infinite loop during synchronization
		self::syncing_start( $booking_id );

		$order_id  = WC_Booking_Data_Store::get_booking_order_id( $booking_id );
		$order     = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) ) {
			self::syncing_stop( $booking_id );
			return;
		}

		$completed = 'processing' === $status && 'completed' === $order->get_status();
		$refunded  = 'cancelled' === $status && 'refunded' === $order->get_status();

		// Do not update status of completed or refunded orders.
		if ( self::is_syncing( $order_id ) || $completed || $refunded ) {
			self::syncing_stop( $booking_id );
			return;
		}

		// Only update status if the order has 1 booking
		if ( 1 === count( $order->get_items() ) ) {
			$order->update_status( $status );
		} elseif ( 'cancelled' === $status ) {
			$booking_ids = WC_Booking_Data_Store::get_order_contains_only_bookings( $order );

			if ( empty( $booking_ids ) ) {
				self::syncing_stop( $booking_id );
				return;
			}

			$booking_statuses = array_map( function( $booking_id ) {
				return get_wc_booking( $booking_id )->get_status();
			}, $booking_ids );

			// Cancel the order only if all Bookings are cancelled
			if ( array( $status ) === array_unique( $booking_statuses ) ) {
				$order->update_status( $status );
			}
		}

		self::syncing_stop( $booking_id );
	}

	/**
	 * Scheduling an event for a failed order if it has bookings.
	 *
	 * @since 1.11.0
	 *
	 * @param integer $order_id The order that failed.
	 */
	public function schedule_failed_order_event( $order_id ) {
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );
		if ( empty( $booking_ids ) ) {
			return;
		}

		$args = array(
			'order_id'    => $order_id,
			'booking_ids' => $booking_ids,
		);

		$timestamp = apply_filters( 'woocommerce_bookings_failed_order_expire_scheduled_time_stamp', current_time( 'timestamp' ) + ( 7 * DAY_IN_SECONDS ) );
		wp_schedule_single_event( $timestamp, 'woocommerce_bookings_failed_order_expired', $args );
	}

	/**
	 * Responding to scheduled event for a failed booking.
	 *
	 * @since 1.11.0
	 *
	 * @param integer $order_id The order that failed.
	 * @param array   $booking_ids Attached to failed order.
	 */
	public function handle_failed_order_scheduled_event( $order_id = 0, $booking_ids = array() ) {
		$order = wc_get_order( $order_id );
		if ( ! is_a( $order, 'WC_Order' )
			|| 'failed' !== $order->get_status()
			|| empty( $booking_ids ) ) {
			return;
		}

		foreach ( $booking_ids as $booking_id ) {
			$booking = get_wc_booking( $booking_id );
			$booking->set_status( 'cancelled' );
			$booking->save();
		}
	}
}
