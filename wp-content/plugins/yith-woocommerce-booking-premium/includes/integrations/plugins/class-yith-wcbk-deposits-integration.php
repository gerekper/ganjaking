<?php
/**
 * Class YITH_WCBK_Deposits_Integration
 * Deposits integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Deposits_Integration
 *
 * @since   1.0.1
 */
class YITH_WCBK_Deposits_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Stores expiration type specific to booking products.
	 *
	 * @var array
	 */
	protected $expiration_types;

	/**
	 * Init
	 */
	protected function init() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->expiration_types = array(
			'on_booking_start'     => __( 'On the booking start date', 'yith-booking-for-woocommerce' ),
			'before_booking_start' => __( 'On a specific time range before the booking start date', 'yith-booking-for-woocommerce' ),
		);

		add_filter( 'yith_wcdp_is_deposit_enabled_on_product', array( $this, 'disable_deposit_on_bookings_requiring_confirmation' ), 10, 2 );
		add_action( 'yith_wcdp_booking_add_to_cart', array( $this, 'add_deposit_to_booking' ) );
		add_filter( 'yith_wcbk_product_form_get_booking_data', array( $this, 'add_deposit_data_to_booking_data' ), 10, 23 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'woocommerce_order_status_cancelled', array( $this, 'set_booking_as_cancelled_when_balance_is_cancelled' ), 10, 2 );

		add_filter( 'yith_wcbk_get_bookings_by_order_args', array( $this, 'filter_get_bookings_by_order_args' ), 10, 3 );
		add_filter( 'yith_wcbk_orders_should_set_booking_as_paid', array( $this, 'disable_setting_booking_as_paid_for_deposit_or_balance_orders' ), 10, 3 );
		add_filter( 'yith_wcbk_booking_get_sold_price_item_total', array( $this, 'filter_booking_sold_price_item_total' ), 10, 3 );

		if ( $this->is_200_or_greater() ) {
			// custom balance expiration.
			add_filter( 'yith_wcdp_deposit_expiration_types', array( $this, 'filter_expiration_types' ) );
			add_filter( 'yith_wcdp_supported_options', array( $this, 'add_custom_expiration_options' ) );
			add_filter( 'yith_wcdp_balances_settings', array( $this, 'add_custom_expiration_settings' ) );
			add_filter( 'yith_wcdp_admin_product_fields', array( $this, 'add_custom_expiration_settings' ) );

			add_filter( 'yith_wcdp_deposit_expiration_timestamp', array( $this, 'set_custom_deposit_expiration' ), 10, 3 );
			add_filter( 'yith_wcdp_balance_expiration_date', array( $this, 'set_custom_balance_expiration' ), 10, 3 );
			add_action( 'yith_wcbk_order_booking_created', array( $this, 'set_custom_suborders_expiration' ), 10, 3 );
		}
	}

	/**
	 * Returns true when version installed of YITH WooCommerce Deposit and Down Payments is greater than or equal to 2.0.0
	 * This is used to enable some advanced compatibilities available starting from version 2.0.0
	 *
	 * @return bool Whether installed version is >= 2.0.0
	 */
	public function is_200_or_greater() {
		return class_exists( 'YITH_WCDP' ) && version_compare( YITH_WCDP::YITH_WCDP_VERSION, '2.0.0', '>=' );
	}

	/**
	 * Filter args of YITH_WCBK_Booking_Helper::get_bookings_by_order to retrieve the correct bookings related to balance orders.
	 *
	 * @param array     $args          Arguments to be filtered.
	 * @param int       $order_id      Order ID.
	 * @param int|false $order_item_id Order item ID.
	 *
	 * @return array
	 * @since 3.0
	 */
	public function filter_get_bookings_by_order_args( $args, $order_id, $order_item_id = false ) {
		if ( ! $order_item_id ) {
			$order = wc_get_order( $order_id );

			if ( $order && $order->is_created_via( 'yith_wcdp_balance_order' ) ) {
				$args['order_id'] = $order->get_parent_id();

				$items    = $order->get_items();
				$item_ids = array_filter(
					array_map(
						function ( $item ) {
							return absint( $item->get_meta( '_deposit_item_id' ) );
						},
						$items
					)
				);

				/**
				 * If Deposits plugin creates a unique order for all items, there is no '_deposit_item_id' meta set in order items.
				 * So, in this case, we should take all the bookings of the deposit order, instead of retrieving only the ones related to specific item IDs.
				 */
				if ( $item_ids ) {
					$args['data_query'] = array(
						array(
							'key'     => '_order_item_id',
							'value'   => $item_ids,
							'compare' => 'IN',
						),
					);
				}
			}
		}

		return $args;
	}

	/**
	 * Disable setting booking as paid for deposit/balance orders.
	 *
	 * @param bool              $should_set_paid Should set paid flag.
	 * @param YITH_WCBK_Booking $booking         The booking.
	 * @param WC_Order          $order           The order.
	 *
	 * @return bool
	 * @since 3.0
	 */
	public function disable_setting_booking_as_paid_for_deposit_or_balance_orders( $should_set_paid, $booking, $order ) {
		$set_booking_as_paid_when = get_option( 'yith-wcbk-set-booking-paid-for-deposits', 'deposit' );
		$order_item               = $booking->get_order_item();
		$item_has_deposit         = $order_item && $order_item->get_meta( '_deposit' );

		if ( $item_has_deposit ) {
			if ( 'balance' === $set_booking_as_paid_when && ! ! $order->get_meta( '_has_deposit' ) ) {
				$should_set_paid = false;
			} elseif ( 'deposit' === $set_booking_as_paid_when && ! ! $order->get_meta( '_has_full_payment' ) ) {
				$should_set_paid = false;
			}
		}

		return $should_set_paid;
	}

	/**
	 * Disable deposits on booking products that requires confirmation
	 *
	 * @param bool $enabled    Deposit enabled flag.
	 * @param int  $product_id Product ID.
	 *
	 * @return bool
	 * @since 2.1
	 */
	public function disable_deposit_on_bookings_requiring_confirmation( $enabled, $product_id ) {
		/**
		 * Booking product.
		 *
		 * @var WC_Product_Booking $product
		 */
		$product = wc_get_product( $product_id );
		if ( $product && yith_wcbk_is_booking_product( $product ) && $product->is_confirmation_required() ) {
			$enabled = false;
		}

		return $enabled;
	}

	/**
	 * Add deposit price to booking data.
	 *
	 * @param array              $booking_data  Booking data.
	 * @param WC_Product_Booking $product       The booking product.
	 * @param array              $bookable_args Array of parameters submitted by customer.
	 *
	 * @return array
	 */
	public function add_deposit_data_to_booking_data( $booking_data, $product, $bookable_args ) {
		$price = isset( $booking_data['raw_price'] ) ? $booking_data['raw_price'] : $product->calculate_price( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! $this->is_200_or_greater() ) {
			$deposit_price = YITH_WCDP_Premium()->get_deposit( $product->get_id(), $price );
		} else {
			$deposit_price = YITH_WCDP_Deposits::get_deposit( $product->get_id(), false, $price, 'view' );

			if ( isset( $bookable_args['from'] ) ) {
				$booking_start         = gmdate( 'Y-m-d', $bookable_args['from'] );
				$deposit_expiration    = $this->get_booking_deposit_expiration( $product->get_id(), $booking_start, 'edit' );
				$deposit_expiration_dt = $deposit_expiration instanceof WC_DateTime ? $deposit_expiration->date_i18n( wc_date_format() ) : false;
				$deposit_expiration_ts = $deposit_expiration instanceof WC_DateTime ? $deposit_expiration->getTimestamp() : false;

				if ( $deposit_expiration ) {
					$booking_data['deposit_expired']    = $deposit_expiration_ts < time();
					$booking_data['deposit_expiration'] = $deposit_expiration_dt;
				}
			}
		}

		$deposit_price      = apply_filters( 'yith_wcbk_booking_product_get_deposit', $deposit_price, $product );
		$deposit_price_html = wc_price( $deposit_price );

		$booking_data['deposit_price'] = $deposit_price_html;

		return $booking_data;
	}

	/**
	 * Add Deposits to Booking Products
	 *
	 * @param WC_Product_Booking $product Booking product.
	 */
	public function add_deposit_to_booking( $product ) {
		if ( $product->is_confirmation_required() ) {
			return;
		}

		if ( ! $this->is_200_or_greater() ) {
			add_action( 'woocommerce_before_add_to_cart_button', array( YITH_WCDP_Frontend_Premium(), 'print_single_add_deposit_to_cart_template' ) );
		} else {
			YITH_WCDP_Frontend()->print_single_add_deposit_to_cart_template();
		}
	}

	/**
	 * Returns post parent of a Balance order
	 * If order is not a balance order, it will return false
	 *
	 * @param int|WC_Order $order_id Order or order ID.
	 *
	 * @return int|bool If order is a balance order, and has post parent, returns parent ID; false otherwise
	 */
	public function get_parent_order_id( $order_id ) {
		$order = wc_get_order( $order_id );

		return $order && $order->get_meta( '_has_full_payment' ) ? $order->get_parent_id() : false;

	}

	/**
	 * Set Booking as cancelled when the balance is cancelled
	 *
	 * @param int      $order_id Order id.
	 * @param WC_Order $order    The order.
	 *
	 * @since 2.1.4
	 */
	public function set_booking_as_cancelled_when_balance_is_cancelled( $order_id, $order ) {
		$parent_order_id = $this->get_parent_order_id( $order_id );
		$bookings        = $parent_order_id ? yith_wcbk_booking_helper()->get_bookings_by_order( $parent_order_id ) : false;
		if ( ! ! $bookings ) {
			$order_number = $order ? $order->get_order_number() : $order_id;
			foreach ( $bookings as $booking ) {
				if ( $booking instanceof YITH_WCBK_Booking ) {
					$order_link      = sprintf(
						'<a href="%s">#%s</a>',
						admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
						$order_number
					);
					$additional_note = sprintf(
					// translators: %s is the order link.
						__( 'Reason: balance order %s has been cancelled.', 'yith-booking-for-woocommerce' ),
						$order_link
					);
					$booking->update_status( 'cancelled', $additional_note );
				}
			}
		}
	}

	/**
	 * Filter booking sold price, to calculate the correct total by the sum of deposit and balance.
	 *
	 * @param false|string          $price       The price.
	 * @param WC_Order_Item_Product $order_item  The order item.
	 * @param bool                  $include_tax Include tax flag.
	 *
	 * @return false|string
	 * @since 3.3.1
	 */
	public function filter_booking_sold_price_item_total( $price, $order_item, $include_tax ) {
		if ( $order_item->get_meta( '_deposit' ) ) {
			$deposit_value   = $order_item->get_meta( '_deposit_value' );
			$deposit_balance = $order_item->get_meta( '_deposit_balance' );
			$product         = $order_item->get_product();
			$price           = $deposit_value + $deposit_balance;

			if ( $product ) {
				if ( $include_tax ) {
					$price = wc_get_price_including_tax( $product, array( 'price' => $price ) );
				} else {
					$price = wc_get_price_excluding_tax( $product, array( 'price' => $price ) );
				}
			}
		}

		return $price;
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'yith-wcbk-integration-deposits-booking-form', YITH_WCBK_ASSETS_URL . '/js/integrations/deposits/deposits-booking-form' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

		wp_enqueue_script( 'yith-wcbk-integration-deposits-booking-form' );

	}

	/**
	 * Filter balance options of Deposits plugin, to add custom expiration dates for the deposit.
	 *
	 * @param array $options Available options.
	 *
	 * @return array Filtered options.
	 */
	public function filter_expiration_types( $options ) {
		global $post;

		if ( $post && ! yith_wcbk_is_booking_product( $post->ID ) ) {
			return $options;
		}

		$options = array_merge(
			$options,
			$this->expiration_types
		);

		return $options;
	}

	/**
	 * Add new option among plugin's supported one
	 *
	 * @param array $options Settings to filter.
	 *
	 * @return array Array of filtered settings.
	 */
	public function add_custom_expiration_options( $options ) {
		$options['days_before_booking'] = array(
			'default'  => 30,
			'meta'     => '_days_before_booking',
			'option'   => 'days_before_booking',
			'override' => 'balance',
		);

		return $options;
	}

	/**
	 * Add custom settings to plugin settings tab / product edit tab
	 *
	 * @param array $settings Settings to filter.
	 *
	 * @return array Array of filtered settings.
	 */
	public function add_custom_expiration_settings( $settings ) {
		$current_action = current_action();

		$field_to_add = array(
			'title'             => __( 'Require balance payment', 'yith-booking-for-woocommerce' ),
			'type'              => 'number',
			'desc'              => __( 'days before the booking', 'yith-booking-for-woocommerce' ),
			'id'                => 'yith_wcdp_days_before_booking',
			'class'             => 'inline-description',
			'default'           => 30,
			'custom_attributes' => array(
				'min'  => 1,
				'max'  => 9999999,
				'step' => 1,
			),
		);

		if ( 'yith_wcdp_admin_product_fields' === $current_action ) {
			$field_to_add = array_merge(
				$field_to_add,
				array(
					'default'      => get_option( 'yith_wcdp_days_before_booking' ),
					'dependencies' => array(
						'expiration_type' => 'before_booking_start',
					),
				)
			);

			$settings = yith_wcdp_append_items(
				$settings,
				'expiration_duration',
				array(
					'days_before_booking' => $field_to_add,
				)
			);
		} elseif ( 'yith_wcdp_balances_settings' === $current_action ) {
			$field_to_add = array_merge(
				$field_to_add,
				array(
					'type'      => 'yith-field',
					'yith-type' => 'number',
					'deps'      => array(
						'id'    => 'yith_wcdp_deposits_expiration_type',
						'value' => 'before_booking_start',
					),
				)
			);

			$settings['settings-balances'] = yith_wcdp_append_items(
				$settings['settings-balances'],
				'deposit-expiration-duration',
				array(
					'deposit-expiration-days-before-booking' => $field_to_add,
				)
			);
		}

		return $settings;
	}

	/**
	 * Calculate deposit expiration for Booking products, starting from booking start
	 * If product isn't booking, or expiration type isn't related to booking object
	 * return default expiration as calculated by Deposit plugin. If booking_start is empty, will use current date
	 *
	 * @param int    $product_id    Product id.
	 * @param string $booking_start Booking start date; if empty current date will be used.
	 * @param string $context       Context of the operation.
	 *
	 * @return bool|string|WC_DateTime Expiration time, or false if none set.
	 */
	public function get_booking_deposit_expiration( $product_id, $booking_start = false, $context = 'view' ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return false;
		}

		$should_expire = YITH_WCDP_Options::get( 'enable_expiration', $product_id );

		if ( ! yith_plugin_fw_is_true( $should_expire ) ) {
			return false;
		}

		$expiration_type = YITH_WCDP_Options::get( 'expiration_type', $product_id );

		if ( ! yith_wcbk_is_booking_product( $product ) || ! in_array( $expiration_type, array_keys( $this->expiration_types ), true ) ) {
			return YITH_WCDP_Deposits::get_expiration_date( $product_id, $context );
		}

		$expiration_ts = $this->get_booking_deposit_expiration_timestamp( $product_id, $booking_start, $context );

		if ( ! $expiration_ts ) {
			return false;
		}

		try {
			$expiration_dt = new WC_DateTime( "@{$expiration_ts}", new DateTimeZone( 'UTC' ) );

			if ( 'view' === $context ) {
				$expiration_dt->setTimezone( new DateTimeZone( wc_timezone_string() ) );

				return $expiration_dt->format( 'Y-m-d' );
			}

			return $expiration_dt;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Returns deposit expiration timestamp for a booking product
	 * If product isn't booking, or it doesn't expire, or expiration mode isn't booking-related, returns false.
	 *
	 * @param int    $product_id    Product id.
	 * @param string $booking_start Booking start date.
	 * @param string $context       Context of the operation.
	 *
	 * @return int|bool Expiration timestamp, or false.
	 */
	public function get_booking_deposit_expiration_timestamp( $product_id, $booking_start = false, $context = 'view' ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return false;
		}

		$should_expire = YITH_WCDP_Options::get( 'enable_expiration', $product_id );

		if ( ! yith_plugin_fw_is_true( $should_expire ) ) {
			return false;
		}

		$expiration_type = YITH_WCDP_Options::get( 'expiration_type', $product_id );

		if ( ! yith_wcbk_is_booking_product( $product ) || ! in_array( $expiration_type, array_keys( $this->expiration_types ), true ) ) {
			return false;
		}

		$booking_start_ts = $booking_start ? strtotime( $booking_start ) : time();

		if ( 'on_booking_start' === $expiration_type ) {
			$expiration_ts = $booking_start_ts;
		} elseif ( 'before_booking_start' === $expiration_type ) {
			$days_from_start = YITH_WCDP_Options::get( 'days_before_booking', $product_id );
			$expiration_ts   = $booking_start_ts - $days_from_start * DAY_IN_SECONDS;
		} else {
			return false;
		}

		// if we're asking a TS for view purpose, make sure it is at least future.
		if ( 'view' === $context ) {
			$expiration_ts = max( time(), $expiration_ts );
		}

		return $expiration_ts;
	}

	/**
	 * Filters default deposit expiration, to account for booking custom options
	 *
	 * @param int    $expiration_ts   Expiration timestamp, as calculated by Deposit plugin.
	 * @param string $expiration_type Expiration type configured for current product.
	 * @param int    $product_id      Product id.
	 *
	 * @return int|bool Filtered expiration timestamp.
	 */
	public function set_custom_deposit_expiration( $expiration_ts, $expiration_type, $product_id ) {
		if ( in_array( $expiration_type, array_keys( $this->expiration_types ), true ) ) {
			$booking_start = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$expiration_ts = $this->get_booking_deposit_expiration_timestamp( $product_id, $booking_start, 'edit' );
		}

		return $expiration_ts;
	}

	/**
	 * Filters balance expiration, using correct Booking starting date to calculate expiration for Bookable products deposits
	 *
	 * @param string $expiration_date  Calculated expiration date for current balance.
	 * @param array  $balance_contents Array of contents of current balance.
	 *
	 * @eturn string Filtered expiration date.
	 */
	public function set_custom_balance_expiration( $expiration_date, $balance_contents ) {
		$cart = WC()->cart;

		if ( ! $cart ) {
			return $expiration_date;
		}

		$cart_contents = $cart->get_cart_contents();
		$expiration    = false;

		if ( empty( $cart_contents ) || empty( $balance_contents ) ) {
			return $expiration_date;
		}

		foreach ( $balance_contents as $cart_item_key => $product ) {
			$cart_item = isset( $cart_contents[ $cart_item_key ] ) ? $cart_contents[ $cart_item_key ] : false;

			if ( ! $cart_item ) {
				continue;
			}

			$product_id         = $product->get_id();
			$expiration_enabled = YITH_WCDP_Options::get( 'enable_expiration', $product_id );
			$expiration_type    = YITH_WCDP_Options::get( 'expiration_type', $product_id );

			if ( ! yith_plugin_fw_is_true( $expiration_enabled ) ) {
				continue;
			}

			if ( yith_wcbk_is_booking_product( $product ) && in_array( $expiration_type, array_keys( $this->expiration_types ), true ) ) {
				$booking_start = $cart_item['yith_booking_data']['from'];

				$item_expiration = $this->get_booking_deposit_expiration_timestamp( $product_id, gmdate( 'Y-m-d', $booking_start ), 'edit' );
			} else {
				$item_expiration = YITH_WCDP_Deposits::get_expiration_date( $product_id, 'edit' );
				$item_expiration = $item_expiration ? $item_expiration->getTimestamp() : false;
			}

			if ( $item_expiration && ( ! $expiration || $item_expiration < $expiration ) ) {
				$expiration = $item_expiration;
			}
		}

		return $expiration ? gmdate( 'Y-m-d', $expiration ) : false;
	}

	/**
	 * Set expiration for suborders containing booking products with custom expiration settings
	 *
	 * @param YITH_WCBK_Booking $booking       Booking option for main deposit order.
	 * @param WC_Order          $order         Deposit order object.
	 * @param int               $order_item_id Order item id that generated booking object.
	 */
	public function set_custom_suborders_expiration( $booking, $order, $order_item_id ) {
		$item        = $order->get_item( $order_item_id );
		$suborder_id = $item ? $item->get_meta( '_full_payment_id' ) : false;
		$suborder    = $suborder_id ? wc_get_order( $suborder_id ) : false;

		if ( ! $suborder ) {
			return;
		}

		$expiration         = false;
		$notification_limit = false;
		$contains_booking   = false;

		foreach ( $suborder->get_items() as $item ) {
			$product                 = $item->get_product();
			$product_id              = $product->get_id();
			$item_expiration         = $this->get_booking_deposit_expiration( $product_id, gmdate( 'Y-m-d', $booking->get_from() ) );
			$item_notification_limit = (int) YITH_WCDP_Options::get( 'expiration_notification_limit', $product_id );

			if ( yith_wcbk_is_booking_product( $product ) ) {
				$contains_booking = true;
			}

			if ( $item_expiration && ( ! $expiration || $item_expiration < $expiration ) ) {
				$expiration = $item_expiration;
			}

			$notification_limit = max( $item_notification_limit, $notification_limit );
		}

		if ( ! $contains_booking ) {
			return;
		}

		if ( ! $expiration ) {
			$suborder->update_meta_data( '_will_suborder_expire', 'no' );
			$suborder->save();

			return;
		}

		$expiration = max( gmdate( 'Y-m-d' ), $expiration );

		// update expiration date.
		$suborder->update_meta_data( '_will_suborder_expire', 'yes' );
		$suborder->update_meta_data( '_suborder_expiration', $expiration );

		// update notification date too.
		if ( $suborder->get_meta( '_suborder_expiration_notification_date' ) ) {
			$notification_ts   = strtotime( $expiration ) - $notification_limit * DAY_IN_SECONDS;
			$notification_ts   = max( $notification_ts, time() );
			$notification_date = gmdate( 'Y-m-d', $notification_ts );

			$suborder->update_meta_data( '_suborder_expiration_notification_date', $notification_date );
		}

		$suborder->save();
	}
}
