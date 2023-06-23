<?php

/**
 * WC_Booking_Cart_Manager class.
 */
class WC_Booking_Cart_Manager {

	/**
	 * The class id used for identification in logging.
	 *
	 * @var $id
	 */
	public $id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_booking_add_to_cart', array( $this, 'add_to_cart' ), 30 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 3 );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_booking_order_legacy_checkout' ), 999, 2 );
		add_action( 'woocommerce_store_api_cart_errors', array( $this, 'validate_booking_order_checkout_block_support' ), 999, 2 );

		add_action( 'woocommerce_new_order_item', array( $this, 'order_item_meta' ), 50, 2 );

		add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'review_items_on_block_checkout' ), 10, 1 );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'review_items_on_shortcode_checkout' ), 10, 1 );

		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_booking_requires_confirmation' ), 20, 2 );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 20 );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 20 );

		if ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ) );
		}

		add_action( 'woocommerce_system_status_tool_executed', array( $this, 'maybe_remove_active_cart_sessions' ), 10 );
		add_action( 'woocommerce_rest_insert_system_status_tool', array( $this, 'maybe_remove_active_cart_sessions' ), 10 );

		$this->id = 'wc_booking_cart_manager';

		// Active logs.
		if ( class_exists( 'WC_Logger' ) ) {
			$this->log = new WC_Logger();
		}
	}

	/**
	 * Add to cart for bookings
	 */
	public function add_to_cart() {
		global $product;

		// Prepare form
		$booking_form = new WC_Booking_Form( $product );

		// Get template
		wc_get_template( 'single-product/add-to-cart/booking.php', array( 'booking_form' => $booking_form ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
	}

	/**
	 * When a booking is added to the cart, validate it
	 *
	 * @param mixed $passed
	 * @param mixed $product_id
	 * @param mixed $qty
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty ) {
		$product = wc_get_product( $product_id );

		if ( ! is_wc_booking_product( $product ) ) {
			return $passed;
		}

		$data     = wc_bookings_get_posted_data( $_POST, $product );
		$validate = $product->is_bookable( $data );

		if ( is_wp_error( $validate ) ) {
			wc_add_notice( $validate->get_error_message(), 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Should validate booking product order for booking for checkout block.
	 *
	 * @since 1.15.76
	 *
	 * @param WP_Error $errors
	 * @param WC_Cart  $cart
	 *
	 * @return void
	 */
	public function validate_booking_order_checkout_block_support( \WP_Error $errors, \WC_Cart $cart ) {
		// Existing checkout validation if rest api request generates from gutenberg editor.
		if ( is_admin() ) {
			return;
		}

		$this->validate_booking_order( $errors, $cart );
	}

	/**
	 * Should validate booking product order for booking for legacy checkout.
	 *
	 * @since 1.15.76
	 *
	 * @param WP_Error $errors
	 * @param WC_Cart  $cart
	 *
	 * @return void
	 */
	public function validate_booking_order_legacy_checkout( array $data, \WP_Error $errors ) {
		$this->validate_booking_order( $errors, WC()->cart );
	}

	/**
	 * Should validate booking order items.
	 *
	 * Booking availability validates without in-cart bookings.
	 * This helps to reduce race condition: https://github.com/woocommerce/woocommerce-bookings/issues/3324
	 *
	 * @since 1.15.76
	 *
	 * @return void
	 */
	public function validate_booking_order( \WP_Error $errors, \WC_Cart $cart ) {
		// Do not need to validate if cart is empty.
		if ( $cart->is_empty() ) {
			return;
		}

		$cart_items = $cart->get_cart();
		$temporary_confirmed_order_bookings = [];

		$booking_errors = [];

		foreach ( $cart_items as $product_data ) {
			/* @var WC_Product_Booking $product */
			$product = $product_data['data'];

			if ( ! is_wc_booking_product( $product ) ) {
				continue;
			}

			$booking = new WC_Booking( $product_data['booking']['_booking_id'] );

			// Unique key to store temporary confirmed bookings in array.
			// Each booking has following unique key: booking_id + resource_id + start_date + end_date.
			$temporary_confirmed_checkout_bookings_array_key = "{$booking->get_product_id()}_{$booking->get_resource_id()}_{$booking->get_start()}_{$booking->get_end()}";

			if ( array_key_exists( $temporary_confirmed_checkout_bookings_array_key,
				$temporary_confirmed_order_bookings ) ) {
				$product->confirmed_order_bookings[] = $temporary_confirmed_order_bookings[ $temporary_confirmed_checkout_bookings_array_key ];
			}

			$product->check_in_cart = false;
			$validate               = $product->is_bookable( $product_data['booking'] );

			if ( is_wp_error( $validate ) ) {
				/* translators: 1: Booking product name */
				$booking_errors["booking-order-item-error-{$booking->get_product_id()}"] = sprintf(
					esc_html__(
						'Sorry, the selected block is no longer available for %1$s. Please choose another block.',
						'woocommerce-bookings' ),
					$product->get_name()
				);
			}

			// Flag booking as temporary confirmed for availability check.
			$temporary_confirmed_order_bookings[ $temporary_confirmed_checkout_bookings_array_key ] = $booking;
		}

		// Add booking checkout errors.
		if ( ! empty( $booking_errors ) ) {
			foreach ( $booking_errors as $error_code => $error_message ) {
				$errors->add( $error_code, $error_message );
			}
		}
	}

	/**
	 * Adjust the price of the booking product based on booking properties
	 *
	 * @param mixed $cart_item
	 * @return array cart item
	 *
	 * @version  1.10.5
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['booking'] ) && isset( $cart_item['booking']['_cost'] ) && '' !== $cart_item['booking']['_cost'] ) {
			$cart_item['data']->set_price( $cart_item['booking']['_cost'] );
		}
		return $cart_item;
	}

	/**
	 * Get data from the session and add to the cart item's meta
	 *
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return array cart item
	 */
	public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
		if ( ! empty( $values['booking'] ) ) {
			$cart_item['booking'] = $values['booking'];
			$cart_item            = $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}

	/**
	 * Before delete
	 *
	 * @param string $cart_item_key identifying which item in cart.
	 */
	public function cart_item_removed( $cart_item_key ) {
		$cart_item = WC()->cart->removed_cart_contents[ $cart_item_key ];

		if ( isset( $cart_item['booking'] ) ) {
			$booking_id = $cart_item['booking']['_booking_id'];
			$booking    = get_wc_booking( $booking_id );

			if ( $booking && $booking->has_status( 'in-cart' ) ) {

				$booking->update_status( 'was-in-cart' );
				WC_Cache_Helper::get_transient_version( 'bookings', true );

				if ( isset( $this->log ) ) {

					$message = sprintf( 'Booking ID: %s removed from cart by user', $booking->get_id() );
					$this->log->add( $this->id, $message );

				}
			}
		}
	}

	/**
	 * Restore item
	 *
	 * @param string $cart_item_key identifying which item in cart.
	 */
	public function cart_item_restored( $cart_item_key ) {
		$cart      = WC()->cart->get_cart();
		$cart_item = $cart[ $cart_item_key ];

		if ( isset( $cart_item['booking'] ) ) {
			$booking_id = $cart_item['booking']['_booking_id'];
			$booking    = get_wc_booking( $booking_id );

			if ( $booking && $booking->has_status( 'was-in-cart' ) ) {

				$booking->update_status( 'in-cart' );
				WC_Cache_Helper::get_transient_version( 'bookings', true );
				$this->schedule_cart_removal( $booking_id );

				if ( isset( $this->log ) ) {

					$message = sprintf( 'Booking ID: %s was restored to cart by user', $booking->get_id() );
					$this->log->add( $this->id, $message );

				}
			}
		}
	}

	/**
	 * Schedule booking to be deleted if inactive
	 */
	public function schedule_cart_removal( $booking_id ) {

		$minutes = apply_filters( 'woocommerce_bookings_remove_inactive_cart_time', 60 );

		/**
		 * If this has been emptied, or set to 0, it will just exit. This means that in-cart bookings will need to be manually removed.
		 * Also take note that if the $minutes var is set to 5 or less, this means that it is possible for the in-cart booking to be
		 * removed before the customer is able to check out.
		 */
		if ( empty( $minutes ) ) {
			return;
		}

		$timestamp = time() + MINUTE_IN_SECONDS * (int) $minutes;

		wp_schedule_single_event( $timestamp, 'wc-booking-remove-inactive-cart', array( $booking_id ) );
	}

	/**
	 * Check for invalid bookings
	 */
	public function cart_loaded_from_session() {
		$titles       = array();
		$count_titles = 0;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['booking'] ) ) {
				// If the booking is gone, remove from cart!
				$booking_id = $cart_item['booking']['_booking_id'];
				$booking    = get_wc_booking( $booking_id );

				if ( ! $booking || ! $booking->has_status( array( 'was-in-cart', 'in-cart', 'unpaid', 'paid', 'pending-confirmation' ) ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );

					WC()->cart->calculate_totals();

					if ( $cart_item['product_id'] ) {
						$title = '<a href="' . get_permalink( $cart_item['product_id'] ) . '">' . get_the_title( $cart_item['product_id'] ) . '</a>';
						$count_titles++;
						if ( ! in_array( $title, $titles, true ) ) {
							$titles[] = $title;
						}
					}
				}
			}
		}

		if ( $count_titles < 1 ) {
			return;
		}
		$formatted_titles = wc_format_list_of_items( $titles );
		/* translators: Admin notice with title and link to bookable product removed from cart. */
		$notice = sprintf( __( 'A booking for %s has been removed from your cart due to inactivity.', 'woocommerce-bookings' ), $formatted_titles );

		if ( $count_titles > 1 ) {
			/* translators: Admin notice with list of titles and links to bookable products removed from cart. */
			$notice = sprintf( __( 'Bookings for %s have been removed from your cart due to inactivity.', 'woocommerce-bookings' ), $formatted_titles );
		}

		wc_add_notice( $notice, 'notice' );
	}

	/**
	 * Add posted data to the cart item
	 *
	 * @param mixed $cart_item_meta
	 * @param mixed $product_id
	 * @return array $cart_item_meta
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! is_wc_booking_product( $product ) ) {
			return $cart_item_meta;
		}

		if ( ! key_exists( 'booking', $cart_item_meta ) ) {
			$cart_item_meta['booking'] = wc_bookings_get_posted_data( $_POST, $product );
		}
		$cart_item_meta['booking']['_cost'] = WC_Bookings_Cost_Calculation::calculate_booking_cost( $cart_item_meta['booking'], $product );

		if ( $cart_item_meta['booking']['_cost'] instanceof WP_Error ) {
			throw new Exception( $cart_item_meta['booking']['_cost']->get_error_message() );
		}

		// Create the new booking
		$new_booking = $this->create_booking_from_cart_data( $cart_item_meta, $product_id );

		// Store in cart
		$cart_item_meta['booking']['_booking_id'] = $new_booking->get_id();

		// Schedule this item to be removed from the cart if the user is inactive.
		$this->schedule_cart_removal( $new_booking->get_id() );

		return $cart_item_meta;
	}

	/**
	 * Create booking from cart data
	 *
	 * @param        $cart_item_meta
	 * @param        $product_id
	 * @param string $status
	 *
	 * @return WC_Booking
	 */
	private function create_booking_from_cart_data( $cart_item_meta, $product_id, $status = 'in-cart' ) {
		// Create the new booking
		$new_booking_data = array(
			'product_id'     => $product_id, // Booking ID
			'cost'           => $cart_item_meta['booking']['_cost'], // Cost of this booking
			'start_date'     => $cart_item_meta['booking']['_start_date'],
			'end_date'       => $cart_item_meta['booking']['_end_date'],
			'all_day'        => $cart_item_meta['booking']['_all_day'],
			'local_timezone' => $cart_item_meta['booking']['_local_timezone'],
		);

		// Check if the booking has resources
		if ( isset( $cart_item_meta['booking']['_resource_id'] ) ) {
			$new_booking_data['resource_id'] = $cart_item_meta['booking']['_resource_id']; // ID of the resource
		}

		// Checks if the booking allows persons
		if ( isset( $cart_item_meta['booking']['_persons'] ) ) {
			$new_booking_data['persons'] = $cart_item_meta['booking']['_persons']; // Count of persons making booking
		}

		$new_booking = get_wc_booking( $new_booking_data );
		$new_booking->create( $status );

		return $new_booking;
	}

	/**
	 * Put meta data into format which can be displayed
	 *
	 * @param mixed $other_data
	 * @param mixed $cart_item
	 * @return array meta
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( empty( $cart_item['booking'] ) ) {
			return $other_data;
		}

		if ( ! empty( $cart_item['booking']['_booking_id'] ) ) {
			$booking = get_wc_booking( $cart_item['booking']['_booking_id'] );
			if ( wc_should_convert_timezone( $booking ) ) {
				$timezone_data = array(
					'name'    => get_wc_booking_data_label( 'timezone', $cart_item['data'] ),
					'value'   => str_replace( '_', ' ', $booking->get_local_timezone() ),
					'display' => '',
				);
				if ( isset( $cart_item['booking']['time'] ) ) {
					$cart_item['booking']['date'] = date_i18n( wc_bookings_date_format(), $booking->get_start( 'view', true ) );
					$cart_item['booking']['time'] = date_i18n( wc_bookings_time_format(), $booking->get_start( 'view', true ) );
				}
			}
		}

		foreach ( $cart_item['booking'] as $key => $value ) {
			if ( substr( $key, 0, 1 ) !== '_' ) {
				$other_data[] = array(
					'name'    => get_wc_booking_data_label( $key, $cart_item['data'] ),
					'value'   => $value,
					'display' => '',
				);
			}
		}

		if ( ! empty( $timezone_data ) ) {
			// Add timezone to the end.
			$other_data[] = $timezone_data;
		}

		return $other_data;
	}

	/**
	 * order_item_meta function
	 *
	 * @param mixed $item_id
	 * @param mixed $values
	 */
	public function order_item_meta( $item_id, $values ) {

		if ( ! empty( $values['booking'] ) ) {
			$product        = $values['data'];
			$booking_id     = $values['booking']['_booking_id'];
		}

		if ( ! isset( $booking_id ) && ! empty( $values->legacy_values ) && is_array( $values->legacy_values ) && ! empty( $values->legacy_values['booking'] ) ) {
			$product        = $values->legacy_values['data'];
			$booking_id     = $values->legacy_values['booking']['_booking_id'];
		}

		if ( isset( $booking_id ) ) {
			$booking        = get_wc_booking( $booking_id );
			$booking_status = 'unpaid';

			if ( function_exists( 'wc_get_order_id_by_order_item_id' ) ) {
				$order_id = wc_get_order_id_by_order_item_id( $item_id );
			} else {
				global $wpdb;
				$order_id = (int) $wpdb->get_var( $wpdb->prepare(
					"SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d",
					$item_id
				) );
			}

			$order        = wc_get_order( $order_id );
			$order_status = $order->get_status();

			$booking->set_order_id( $order_id );
			$booking->set_order_item_id( $item_id );

			/**
			 * In this particular case, the status will be 'in-cart' as we don't want to change it
			 * before the actual order is done if we're dealing with the checkout blocks.
			 * The checkout block creates a draft order before it is then changes to another more final status.
			 * Later the woocommerce_blocks_checkout_order_processed hook is called and
			 * review_items_on_checkout runs to change the status of the booking to their correct value.
			 */

			if ( 'checkout-draft' === $order_status ) {
				$booking->set_status( 'in-cart' );
			}
			$booking->save();
		}
	}

	/**
	 * Goes through all the bookings after the order is submitted via a checkout block to update their statuses.
	 *
	 * @param WC_Order $order The order represented.
	 */
	public function review_items_on_block_checkout( $order ) {
		$order_id = $order->get_id();

		if ( empty( $order_id ) ) {
			return;
		}

		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();

		$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		foreach ( $bookings as $booking_id ) {

			$booking    = get_wc_booking( $booking_id );
			$product_id = $booking->get_product_id();
			if ( empty( $product_id ) ) {
				continue;
			}

			/**
			 * We just want to deal with the bookings that we left forcibly on the 'in-cart' state
			 * and provide them the same state they would be if not using blocks.
			 */
			if ( ! wc_booking_requires_confirmation( $product_id ) && ! in_array( $order_status, array( 'processing', 'completed' ), true ) ) {
				/**
				 * We need to bring the booking status from the new in-cart status to unpaid if it doesn't require confirmation
				 */
				$booking->set_status( 'unpaid' );
				$booking->save();
			} elseif ( 'in-cart' === $booking->get_status() && wc_booking_requires_confirmation( $product_id ) ) {
				/**
				 * If the order is in cart and requires confirmation, we need to change this.
				 */
				$booking->set_status( 'pending-confirmation' );
				$booking->save();
			}
		}
	}

	/**
	 * Makes sure we change the booking statuses to account for the new order statuses created by WooCommerce Blocks
	 * and also account for products that might have the new in-cart status.
	 *
	 * @param mixed $order_id The order represented.
	 */
	public function review_items_on_shortcode_checkout( $order_id ) {

		if ( empty( $order_id ) ) {
			return;
		}

		/**
		 * We need to make sure we don't do anything to the booking just yet because of the new checkout-draft status
		 * assigned by the checkout block when entering the checkout page.
		 */
		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();

		if ( 'checkout-draft' === $order_status ) {
			return;
		}

		$bookings = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		foreach ( $bookings as $booking_id ) {

			$booking    = get_wc_booking( $booking_id );
			$product_id = $booking->get_product_id();

			if ( empty( $product_id ) ) {
				continue;
			}
			if ( ! wc_booking_requires_confirmation( $product_id ) && ! in_array( $order_status, array( 'processing', 'completed' ), true ) ) {
				/**
				 * We need to bring the booking status from the new in-cart status to unpaid if it doesn't require confirmation
				 */
				$booking->set_status( 'unpaid' );
				$booking->save();
			} elseif ( 'in-cart' === $booking->get_status() && wc_booking_requires_confirmation( $product_id ) ) {
				/**
				 * If the order is in cart and requires confirmation, we need to change this.
				 */
				$booking->set_status( 'pending-confirmation' );
				$booking->save();
			}
		}
	}

	/**
	 * Redirects directly to the cart the products they need confirmation.
	 *
	 * @since 1.0.0
	 * @version 1.10.8
	 *
	 * @param string $url URL.
	 */
	public function add_to_cart_redirect( $url ) {
		if ( isset( $_REQUEST['add-to-cart'] ) && is_numeric( $_REQUEST['add-to-cart'] ) && wc_booking_requires_confirmation( intval( $_REQUEST['add-to-cart'] ) ) ) {
			// Remove add to cart messages only in case there's no error.
			$notices = wc_get_notices();
			if ( empty( $notices['error'] ) ) {
				wc_clear_notices();

				// Go to checkout.
				return wc_get_cart_url();
			}
		}

		return $url;
	}

	/**
	 * Remove all bookings that requires confirmation.
	 *
	 * @return void
	 */
	protected function remove_booking_that_requires_confirmation() {
		foreach ( WC()->cart->cart_contents as $item_key => $item ) {
			if ( wc_booking_requires_confirmation( $item['product_id'] ) ) {
				WC()->cart->set_quantity( $item_key, 0 );
			}
		}
	}

	/**
	 * Removes all products when cart have a booking which requires confirmation
	 *
	 * @param  bool $passed
	 * @param  int  $product_id
	 *
	 * @return bool
	 */
	public function validate_booking_requires_confirmation( $passed, $product_id ) {
		if ( wc_booking_requires_confirmation( $product_id ) ) {

			$items = WC()->cart->get_cart();

			foreach ( $items as $item_key => $item ) {
				if ( ! isset( $item['booking'] ) || ! wc_booking_requires_confirmation( $item['product_id'] ) ) {
					WC()->cart->remove_cart_item( $item_key );
				}
			}
		} elseif ( wc_booking_cart_requires_confirmation() ) {
			// Remove bookings that requires confirmation.
			$this->remove_booking_that_requires_confirmation();

			wc_add_notice( __( 'A booking that requires confirmation has been removed from your cart. It is not possible to complete the purchased along with a booking that doesn\'t require confirmation.', 'woocommerce-bookings' ), 'notice' );
		}

		return $passed;
	}

	/**
	 * Check to see if we should remove all In Cart bookings.
	 *
	 * @param  array $tool  The system tool that is being run.
	 */
	public function maybe_remove_active_cart_sessions( $tool ) {
		if ( 'clear_sessions' === $tool['id'] && $tool['success'] ) {
			WC_Bookings_Tools::remove_in_cart_bookings( 'all' );
		}
	}
}

