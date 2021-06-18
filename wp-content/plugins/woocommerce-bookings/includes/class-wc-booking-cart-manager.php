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
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'before_cart_item_quantity_zero' ), 10, 1 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			add_action( 'woocommerce_new_order_item', array( $this, 'order_item_meta' ), 50, 2 );
		} else {
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 50, 2 );
		}

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
	 */
	public function before_cart_item_quantity_zero( $cart_item_key ) {
		$cart       = WC()->cart->get_cart();
		$cart_item  = $cart[ $cart_item_key ];
		$booking_id = isset( $cart_item['booking'] ) && ! empty( $cart_item['booking']['_booking_id'] ) ? absint( $cart_item['booking']['_booking_id'] ) : '';

		if ( $booking_id ) {
			$booking = get_wc_booking( $booking_id );
			if ( $booking && $booking->has_status( array( 'was-in-cart', 'in-cart' ) ) ) {
				wp_delete_post( $booking_id );
				wp_clear_scheduled_hook( 'wc-booking-remove-inactive-cart', array( $booking_id ) );
			}
		}
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
				wp_clear_scheduled_hook( 'wc-booking-remove-inactive-cart', array( $booking_id ) );

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

				if ( ! $booking || ! $booking->has_status( array( 'was-in-cart', 'in-cart', 'unpaid', 'paid' ) ) ) {
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

			// Set as pending when the booking requires confirmation
			if ( wc_booking_requires_confirmation( $values['product_id'] ) ) {
				$booking_status = 'pending-confirmation';
			}

			$booking->set_order_id( $order_id );
			$booking->set_order_item_id( $item_id );
			$booking->set_status( $booking_status );
			$booking->save();
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

