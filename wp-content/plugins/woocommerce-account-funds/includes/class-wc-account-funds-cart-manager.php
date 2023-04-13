<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Cart_Manager
 */
class WC_Account_Funds_Cart_Manager {
	public $partial_payment;
	public $give_discount;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_review_order_before_order_total', array( $this, 'display_used_funds' ) );
		add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_used_funds' ) );

		$this->partial_payment = get_option( 'account_funds_partial_payment', 'no' );
		$this->give_discount   = get_option( 'account_funds_give_discount', 'no' );

		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'update_order_review' ) );
		add_action( 'wp_loaded', array( $this, 'maybe_use_funds' ), 15 );

		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'calculate_totals' ), 99 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ) );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'after_calculate_totals' ), 99 );

		add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'get_discount_data' ), 10, 2 );
		add_filter( 'woocommerce_coupon_message', array( $this, 'get_discount_applied_message' ), 10, 3 );
		add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ) );
		add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'coupon_html' ), 10, 2 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount' ), 10, 5 );

		add_filter( 'woocommerce_paypal_args', array( $this, 'filter_paypal_line_item_names' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Checks if the customer can use funds.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	private static function can_use_funds() {
		$use_funds = false;

		if ( is_user_logged_in() && ! self::cart_contains_deposit() ) {
			$funds = WC_Account_Funds::get_account_funds( get_current_user_id(), false );

			if ( $funds > 0 ) {
				// Changing the payment method of a subscription.
				if ( isset( $_GET['change_payment_method'] ) ) {
					$subscription = wcs_get_subscription( (int) $_GET['change_payment_method'] );

					$use_funds = ( $subscription && $subscription->get_total( 'edit' ) <= $funds );
				} else {
					$use_funds = ( WC()->cart->get_total( 'edit' ) <= $funds || 'yes' === get_option( 'account_funds_partial_payment' ) );
				}
			}
		}

		/**
		 * Filters if the customer can use funds.
		 *
		 * @since 2.3.0
		 *
		 * @param bool $use_funds Whether the customer can use funds.
		 */
		return apply_filters( 'wc_account_funds_can_use_funds', $use_funds );
	}

	/**
	 * Gets if the 'Account Funds' payment gateway is available.
	 *
	 * @since 2.3.11
	 *
	 * @return bool
	 */
	public static function account_funds_gateway_is_available() {
		$gateways = WC()->payment_gateways()->get_available_payment_gateways();

		return ( isset( $gateways['accountfunds'] ) );
	}

	/**
	 * Gets if the 'Account Funds' payment gateway is selected.
	 *
	 * @return bool
	 */
	public static function account_funds_gateway_chosen() {
		$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

		return ( isset( $available_gateways['accountfunds'] ) && $available_gateways['accountfunds']->chosen ) || ( ! empty( $_POST['payment_method'] ) && 'accountfunds' === $_POST['payment_method'] );
	}

	/**
	 * Using funds right now?
	 *
	 * @return bool
	 */
	public static function using_funds() {
		return ( WC()->session && WC()->session->get( 'use-account-funds' ) );
	}

	/**
	 * Amount of funds being applied
	 *
	 * @return float
	 */
	public static function used_funds_amount() {
		return WC()->session->get( 'used-account-funds' );
	}

	/**
	 * Removes the usage of account funds.
	 *
	 * @since 2.7.2
	 */
	public static function remove_funds() {
		WC()->session->set( 'use-account-funds', false );
		WC()->session->set( 'used-account-funds', false );
	}

	/**
	 * Processes the updated order review.
	 *
	 * @since 2.3.0
	 *
	 * @param string $post_data Posted data.
	 */
	public function update_order_review( $post_data ) {
		parse_str( $post_data, $data );

		$data = wc_clean( wp_unslash( $data ) );

		if ( ! empty( $data['apply_account_funds'] ) && self::can_use_funds() ) {
			WC()->session->set( 'use-account-funds', true );
		}

		if ( self::using_funds() ) {
			$this->apply_discount();
		}
	}

	/**
	 * Use funds
	 */
	public function maybe_use_funds() {
		if ( ! WC()->cart || ! self::can_use_funds() ) {
			return;
		}

		if ( ! empty( $_POST['wc_account_funds_apply'] ) ) {
			WC()->session->set( 'use-account-funds', true );
		}

		if ( ! empty( $_GET['remove_account_funds'] ) ) {
			self::remove_funds();
			wp_redirect( esc_url_raw( remove_query_arg( 'remove_account_funds' ) ) );
			exit;
		}

		if ( self::using_funds() ) {
			$this->apply_discount();
		}
	}

	/**
	 * Applies a discount to the cart for using funds.
	 */
	public function apply_discount() {
		if ( 'no' === $this->give_discount || ! WC()->cart || $this->has_discount() || ( ! self::can_use_funds() && ! self::account_funds_gateway_chosen() ) ) {
			return;
		}

		WC()->cart->apply_coupon( self::generate_discount_code() );
	}

	/**
	 * Gets if the cart has applied the discount for using funds.
	 *
	 * @since 2.4.4
	 *
	 * @return bool
	 */
	public function has_discount() {
		return ( self::get_discount_code() && WC()->cart->has_discount( self::get_discount_code() ) );
	}

	/**
	 * Removes the discount applied for using funds.
	 *
	 * @since 2.4.4
	 *
	 * @return bool
	 */
	public function remove_discount() {
		$removed = false;

		if ( $this->has_discount() ) {
			$removed = WC()->cart->remove_coupon( self::get_discount_code() );

			if ( $removed ) {
				WC()->session->set( 'wc_account_funds_discount_code', null );
			}
		}

		return $removed;
	}

	/**
	 * get discount amount
	 */
	public function display_discount_amount() {
		$discount = wc_get_account_funds_discount_data();

		return ( 'fixed' === $discount['type'] ? wc_price( $discount['amount'] ) . ' ' : '' );
	}

	/**
	 * See if an cart contains a deposit
	 *
	 * @return bool
	 */
	public static function cart_contains_deposit() {
		if ( WC()->cart instanceof WC_Cart ) {
			foreach ( WC()->cart->get_cart() as $item ) {
				if ( $item['data']->is_type( 'deposit' ) || $item['data']->is_type( 'topup' ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Subscription?
	 *
	 * @return bool
	 */
	public static function cart_contains_subscription() {
		return ( WC()->cart instanceof WC_Cart && class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() );
	}

	/**
	 * Show amount of funds used
	 */
	public function display_used_funds() {
		if ( self::using_funds() ) {
			$funds_used = self::used_funds_amount();

			if ( $funds_used > 0 ) {
				?>
				<tr class="order-discount account-funds-discount">
					<th><?php echo esc_html( wc_get_account_funds_name() ); ?></th>
					<td>
						<?php
						echo wp_kses_post(
							sprintf(
								'-%1$s <a href="%2$s">%3$s</a>',
								wc_price( $funds_used ),
								esc_url( add_query_arg( 'remove_account_funds', true, wc_get_page_permalink( is_cart() ? 'cart' : 'checkout' ) ) ),
								__( '[Remove]', 'woocommerce' ) // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
							)
						);
						?>
					</td>
				</tr>
				<?php
			}
		} elseif ( self::can_use_funds() && ! self::account_funds_gateway_is_available() ) {
			$funds = min( WC()->cart->get_total( 'edit' ), WC_Account_Funds::get_account_funds( null, false ) );

			if ( 'yes' === $this->give_discount ) {
				$discount = wc_get_account_funds_discount_data();

				$label = sprintf(
					/* translators: 1: funds amount, 2: funds name, 3: discount */
					__( 'Use <strong>%1$s</strong> from your %2$s and get a %3$s discount.', 'woocommerce-account-funds' ),
					wc_price( $funds ),
					wc_get_account_funds_name(),
					( 'fixed' === $discount['type'] ? wc_price( $discount['amount'] ) : $discount['amount'] . '%' )
				);
			} else {
				$label = sprintf(
					/* translators: 1: funds amount, 2: funds name */
					__( 'Use <strong>%1$s</strong> from your %2$s.', 'woocommerce-account-funds' ),
					wc_price( $funds ),
					wc_get_account_funds_name()
				);
			}
			?>
			<tr class="account-funds">
				<th><?php echo esc_html( wc_get_account_funds_name() ); ?></th>
				<td>
					<input id="apply_account_funds" name="apply_account_funds" type="checkbox" value="1" />
					<label for="apply_account_funds"><?php echo wp_kses_post( $label ); ?></label>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Generates the coupon data required for the discount.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data Coupon data.
	 * @param string $code Coupon code.
	 * @return array
	 */
	public function get_discount_data( $data, $code ) {
		if ( is_admin() || 'no' === $this->give_discount || strtolower( $code ) !== $this->get_discount_code() ) {
			return $data;
		}

		$discount = wc_get_account_funds_discount_data();

		// We make our points discount "greedy" so as many points as possible are
		// applied to the order. However, we also want to play nice with other discounts
		// so if another coupon is applied we want to use less points than otherwise.
		// The solution is to make this discount apply post-tax so that both pre-tax
		// and post-tax discounts can be considered.  At the same time we use the cart
		// subtotal excluding tax to calculate the maximum points discount, so it
		// functions like a pre-tax discount in that sense.
		return array(
			'id'            => true,
			'discount_type' => ( 'fixed' === $discount['type'] ? 'fixed_cart' : 'percent' ),
			'amount'        => $discount['amount'],
		);
	}

	/**
	 * Get coupon discount amount as percentage.
	 *
	 * @param  float     $discount
	 * @param  float     $discounting_amount
	 * @param  object    $cart_item
	 * @param  bool      $single
	 * @param  WC_Coupon $coupon
	 * @return float
	 */
	public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( 'no' === $this->give_discount || strtolower( $coupon->get_code() ) !== $this->get_discount_code() ) {
			return $discount;
		}

		$discount_data = wc_get_account_funds_discount_data();

		if ( 'percentage' === $discount_data['type'] ) {
			if ( WC_Account_Funds::get_account_funds( get_current_user_id(), false ) < WC()->cart->get_subtotal() ) {
				$discount_percent = WC_Account_Funds::get_account_funds( get_current_user_id(), false ) / WC()->cart->get_subtotal();
			} else {
				$discount_percent = 1;
			}

			$discount *= $discount_percent;
		}

		return $discount;
	}

	/**
	 * Change the "Coupon applied successfully" message to "Discount Applied Successfully"
	 *
	 * @since 1.0
	 * @param string $message the message text
	 * @param string $message_code the message code
	 * @param object $coupon the WC_Coupon instance
	 * @return string the modified messages
	 */
	public function get_discount_applied_message( $message, $message_code, $coupon ) {
		if ( 'no' === $this->give_discount ) {
			return $message;
		}

		if ( WC_Coupon::WC_COUPON_SUCCESS === $message_code && $this->get_discount_code() === $coupon->get_code() ) {
			return sprintf(
				/* translators: %s: Funds name */
				__( 'Discount applied for using %s!', 'woocommerce-account-funds' ),
				wc_get_account_funds_name()
			);
		} else {
			return $message;
		}
	}

	/**
	 * Make the label for the coupon look nicer
	 *
	 * @param  string $label
	 * @return string
	 */
	public function coupon_label( $label ) {
		if ( 'no' === $this->give_discount ) {
			return $label;
		}

		if ( strstr( strtoupper( $label ), 'WC_ACCOUNT_FUNDS_DISCOUNT' ) ) {
			$label = esc_html( __( 'Discount', 'woocommerce-account-funds' ) );
		}
		return $label;
	}

	/**
	 * Make the html for the coupon look nicer
	 *
	 * @param  string $html
	 * @return string
	 */
	public function coupon_html( $html, $coupon ) {
		if ( 'no' === $this->give_discount ) {
			return $html;
		}

		if ( $this->get_discount_code() === $coupon->get_code() ) {
			$html = current( explode( '<a ', $html ) );
		}
		return $html;
	}

	/**
	 * Generates a unique discount code tied to the current user ID and timestamp
	 * Made of current user ID + the current time in YYYY_MM_DD_H_M format
	 */
	public static function generate_discount_code() {
		$discount_code = sprintf( 'wc_account_funds_discount_%s_%s', get_current_user_id(), date( 'Y_m_d_h_i', current_time( 'timestamp' ) ) );

		WC()->session->set( 'wc_account_funds_discount_code', $discount_code );

		return $discount_code;
	}

	/**
	 * Returns the unique discount code generated for the applied discount if set
	 *
	 * @since 1.0
	 */
	public static function get_discount_code() {
		return WC()->session->get( 'wc_account_funds_discount_code' );
	}

	/**
	 * Before calculate totals.
	 *
	 * @since 2.7.2
	 */
	public function before_calculate_totals() {
		if ( self::using_funds() && ! self::can_use_funds() ) {
			self::remove_funds();
		}
	}

	/**
	 * After calculate totals.
	 *
	 * @since 2.3.5
	 *
	 * @param WC_Cart $cart Cart object.
	 */
	public function after_calculate_totals( $cart ) {
		$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

		// Remove the discount if the payment gateway is no longer available and calculate the totals again.
		if ( ! isset( $available_gateways['accountfunds'] ) && 'accountfunds' === WC()->session->get( 'chosen_payment_method' ) ) {
			WC()->session->set( 'chosen_payment_method', '' );

			if ( ! self::using_funds() && $this->remove_discount() ) {
				WC()->cart->calculate_shipping();
				WC()->cart->calculate_totals();
				return;
			}
		}

		if ( ! self::using_funds() || property_exists( $cart, 'recurring_cart_key' ) ) {
			return;
		}

		$total = $cart->get_total( 'edit' );
		$funds = WC_Account_Funds::get_account_funds( null, false );

		// Use the payment gateway instead.
		if ( $funds >= $total ) {
			$this->remove_discount();
			self::remove_funds();
			WC()->cart->calculate_shipping();
			WC()->cart->calculate_totals();
			return;
		}

		$cart->set_total( max( 0, $total - $funds ) );

		/*
		 * The cart session is updated with priority 10 before WC 6.3.
		 * So we have to update the cart session manually.
		 */
		if ( version_compare( WC_VERSION, '6.3.0', '<' ) ) {
			WC()->session->set( 'cart_totals', WC()->cart->get_totals() );
		}

		WC()->session->set( 'used-account-funds', $funds );
	}

	/**
	 * Calculate totals
	 */
	public function calculate_totals() {
		// Changing the payment method of a subscription.
		if ( isset( $_GET['change_payment_method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( self::account_funds_gateway_chosen() ) {
			$this->apply_discount();
			WC()->cart->calculate_totals();
		} elseif ( ! self::using_funds() ) {
			$this->remove_discount();
		}
	}

	/**
	 * When AF is applied it causes order total mismatch with total from order
	 * items because AF is filtering the order total based on amount funds used.
	 *
	 * This filter adjust the line item name to indicate the amount is with tax
	 * and AF applied already.
	 *
	 * @since 2.0.11
	 * @version 2.1.8
	 *
	 * @param array    $paypal_args PayPal args.
	 * @param WC_Order $order       Order object.
	 * @return array PayPal args.
	 */
	public function filter_paypal_line_item_names( $paypal_args, $order ) {
		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( 0 >= $funds_used ) {
			return $paypal_args;
		}

		$item_indexes = $this->get_paypal_line_item_indexes( $paypal_args );

		foreach ( $item_indexes as $index ) {
			$key = 'item_name_' . $index;
			$val = $paypal_args[ $key ];

			$paypal_args[ $key ] = sprintf(
				/* translators: 1: Cart item, 2: Funds name */
				__( '%1$s (with tax, discount, and %2$s applied)', 'woocommerce-account-funds' ),
				$val,
				wc_get_account_funds_name()
			);
		}

		return $paypal_args;

	}

	/**
	 * Get the item indexes from all paypal itmes.
	 *
	 * Only indexes with existing name, amount and quantity are added.
	 *
	 * @since 2.0.11
	 *
	 * @param array $paypal_args PayPal Args.
	 *
	 * @return array Item indexes.
	 */
	public function get_paypal_line_item_indexes( $paypal_args ) {
		$item_indexes = array();

		foreach ( $paypal_args as $key => $arg ) {
			if ( ! preg_match( '/item_name_/', $key ) ) {
				continue;
			}

			$index = str_replace( 'item_name_', '', $key );

			// Make sure the item name, amount and quantity values exist.
			if ( isset( $paypal_args[ 'amount_' . $index ] )
				&& isset( $paypal_args[ 'item_name_' . $index ] )
				&& isset( $paypal_args[ 'quantity_' . $index ] ) ) {
				$item_indexes[] = $index;
			}
		}

		return $item_indexes;
	}

	/**
	 * Calculates remaining balance after of funds were applied
	 *
	 * @since 2.3.0
	 *
	 * @return float
	 */
	public static function get_remaining_balance() {
		return WC_Account_Funds::get_account_funds( get_current_user_id(), false ) - WC()->session->get( 'used-account-funds', 0 );
	}

	/**
	 * Enqueues the scripts.
	 *
	 * @since 2.3.0
	 */
	public function enqueue_scripts() {
		if ( ! is_cart() ) {
			return;
		}

		$suffix = wc_account_funds_get_scripts_suffix();

		wp_enqueue_script( 'wc-account-funds-cart', WC_ACCOUNT_FUNDS_URL . "assets/js/frontend/cart{$suffix}.js", array( 'jquery' ), WC_ACCOUNT_FUNDS_VERSION, true );
	}

	/**
	 * Calculated total.
	 *
	 * @deprecated 2.3.5
	 *
	 * @param float $total
	 * @return float
	 */
	public function calculated_total( $total ) {
		wc_deprecated_function( __FUNCTION__, '2.3.5' );

		return $total;
	}

	/**
	 * Calculated total.
	 *
	 * @deprecated 2.3.5
	 *
	 * @param string $total
	 * @return string
	 */
	public function display_total( $total ) {
		wc_deprecated_function( __FUNCTION__, '2.3.5' );

		return $total;
	}

	/**
	 * Show a notice to apply points towards your purchase
	 *
	 * @deprecated 2.3.0
	 */
	public function output_use_funds_notice() {
		wc_deprecated_function( __FUNCTION__, '2.3.0' );

		if ( ! self::can_use_funds() || self::using_funds() ) {
			return;
		}

		$message  = '<div class="woocommerce-info wc-account-funds-apply-notice">';
		$message .= '<form class="wc-account-funds-apply" method="post">';
		$message .= '<input type="submit" class="button wc-account-funds-apply-button" name="wc_account_funds_apply" value="' . __( 'Use Account Funds', 'woocommerce-account-funds' ) . '" />';
		$message .= sprintf( __( 'You have <strong>%s</strong> worth of funds on your account.', 'woocommerce-account-funds' ), WC_Account_Funds::get_account_funds() );
		if ( 'yes' === get_option( 'account_funds_give_discount' ) ) {
			$message .= '<br/><em>' . sprintf( __( 'Use your account funds and get a %s discount on your order.', 'woocommerce-account-funds' ), $this->display_discount_amount() ) . '</em>';
		}
		$message .= '</form>';
		$message .= '</div>';

		echo $message;
	}

	/**
	 * Can the user actually apply funds to this cart?
	 *
	 * @deprecated 2.3.0
	 *
	 * @return bool
	 */
	public static function can_apply_funds() {
		wc_deprecated_function( __FUNCTION__, '2.3.0', 'wc_account_funds_can_use_funds filter (see ' . __CLASS__ . '::can_use_funds()' );

		return self::can_use_funds();
	}

	/**
	 * Filters the available payment gateways.
	 *
	 * @since 2.3.0
	 * @deprecated 2.3.11
	 *
	 * @param array $gateways The available gateways.
	 * @return array
	 */
	public function available_payment_gateways( $gateways ) {
		wc_deprecated_function( __FUNCTION__, '2.3.11' );

		return $gateways;
	}

	/**
	 * Filters whether the cart needs payment.
	 *
	 * @since 2.3.0
	 * @deprecated 2.4.4
	 *
	 * @param bool $needs_payment Whether the cart needs payment.
	 * @return bool
	 */
	public function cart_needs_payment( $needs_payment ) {
		wc_deprecated_function( __FUNCTION__, '2.4.4' );

		return $needs_payment;
	}
}

new WC_Account_Funds_Cart_Manager();
