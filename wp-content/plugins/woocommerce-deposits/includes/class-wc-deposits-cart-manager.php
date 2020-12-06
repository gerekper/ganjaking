<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Cart_Manager class.
 */
class WC_Deposits_Cart_Manager {

	/** @var object Class Instance */
	private static $instance;

	/** @var bool Whether we're buffering */
	private $buffered = false;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'deposits_form_output' ), 99 );
		add_action( 'woocommerce_before_variations_form', array( $this, 'reposition_display_for_variable_product' ), 99 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 99, 1 );

		// Apply discounts after the cart is completely loaded.
		// Dynamic Pricing applies discounts after the cart is completely loaded
		// to account for category quantity discounts.
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'get_cart_from_session' ), 99, 1 );

		// Control how coupons apply to products including a deposit or payment plan
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'clear_deferred_discounts' ) );

		// Add WC3.2 Coupons upgrade compatibility
		if( version_compare( WC_VERSION, '3.2', '>=' ) ){
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount' ), 10, 5 );
			add_filter( 'woocommerce_cart_tax_totals' , array( $this, 'cart_totals_order_taxes' ), 10, 2 );
			add_filter( 'woocommerce_order_get_tax_totals' , array( $this, 'order_totals_order_taxes' ), 10, 2 );
			add_action( 'woocommerce_calculated_total', array( $this, 'adjust_cart_total' ), 10, 1 );
			add_action( 'woocommerce_cart_get_subtotal', array( $this, 'adjust_cart_subtotal' ), 10, 1 );
			add_action( 'woocommerce_coupon_validate_minimum_amount', array( $this, 'check_coupon_minimum_amount' ), 10, 3 );
			add_action( 'woocommerce_order_status_partial-payment', 'wc_update_coupon_usage_counts' );
			// Update order totals as late as possible, so deposit totals are recalculated before saving.
			add_action( 'woocommerce_checkout_create_order', array( $this, 'update_order_totals' ), 999, 2 );
		} else {
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount_legacy' ), 10, 5 );
		}

		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'display_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'display_item_subtotal' ), 10, 3 );
		add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_cart_totals_before' ), 99 );
		add_action( 'woocommerce_review_order_before_order_total', array( $this, 'display_cart_totals_before' ), 99 );
		add_action( 'woocommerce_cart_totals_after_order_total', array( $this, 'display_cart_totals_after' ), 1 );
		add_action( 'woocommerce_review_order_after_order_total', array( $this, 'display_cart_totals_after' ), 1 );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways' ) );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta_legacy' ), 50, 2 );
		} else {
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 50, 3 );
		}

		// Change button/cart URLs
		add_filter( 'add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'remove_add_to_cart_class' ), 10, 2 );
	}

	/**
	 * Scripts and styles.
	 */
	public function wp_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'wc-deposits-frontend', WC_DEPOSITS_PLUGIN_URL . '/assets/css/frontend.css', null, WC_DEPOSITS_VERSION );
		wp_register_script( 'wc-deposits-frontend', WC_DEPOSITS_PLUGIN_URL . '/assets/js/frontend' . $suffix . '.js', array( 'jquery' ), WC_DEPOSITS_VERSION, true );
	}

	/**
	 * Show deposits form.
	 */
	public function deposits_form_output() {
		if ( WC_Deposits_Product_Manager::deposits_enabled( $GLOBALS['post']->ID ) ) {
			wp_enqueue_script( 'wc-deposits-frontend' );
			wc_get_template( 'deposit-form.php', array( 'post' => $GLOBALS['post'] ), 'woocommerce-deposits', WC_DEPOSITS_TEMPLATE_PATH );
		}
	}

	/**
	 * Does the cart contain a deposit?
	 *
	 * @return boolean
	 */
	public function has_deposit() {
		if ( ! is_null( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( ! empty( $cart_item['is_deposit'] ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	* Are we paying for a deposit or payment plan order?
	*
	* @return boolean
	*/
	public function is_deposit_order() {
		global $wp;

		$order_id = isset( $wp->query_vars['order-pay'] ) ? absint( $wp->query_vars['order-pay'] ) : null;

		if ( ! empty( $order_id ) ) {
			$order = wc_get_order( $order_id );

			if ( $order && $order->has_status( 'pending-deposit' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * When checking if coupon meets the minimum amount core looks on the subtotal value in cart.
	 * When deposit item is in the cart, the subtotal value is missing the deposit part ( future payment ).
	 * This makes the minimum amount coupon unusable in some cases. This function tries to correct this
	 * by comparing minimum amount coupon setting to the subtotal with future payments added.
	 */
	public function check_coupon_minimum_amount( $is_invalid, $coupon, $subtotal ) {
		// Check if we can exit early.
		if( ! $this->has_deposit() ) {
			// Not a deposit case.
			return $is_invalid;
		}
		if( ! $is_invalid ) {
			// This method only adjusts for deffered part in case the coupon is invalid
			// if $is_invalid === fale this means that the coupon has alredy passed the
			// criteria and we do not need to check further.
			return $is_invalid;
		}

		// It is a deposit case, and coupon is not valid becus minimum spending amount
		// condition is not met. We will check if what is storred in deffered amount of
		// the deposit payment will not be enough to push the coupon over the minimum limit
		$future_payments = $this->get_deposit_remaining_amount() + $this->get_credit_amount();
		return $coupon->get_minimum_amount() > ( $subtotal + $future_payments );
	}


	/**
	 * See how much credit the user is giving the customer (for payment plans)
	 * If $cart_item is provided this function will calculate value only for this item.
	 * @param mixed $cart_item
	 * @return float
	 */
	public function get_future_payments_amount( $cart_item = null ) {
		return $this->get_deposit_remaining_amount( $cart_item ) + $this->get_credit_amount( $cart_item ) - self::get_deferred_discount_amount( null, $cart_item );
	}

	/**
	 * See how much credit the user is giving the customer. This calculates value excluding tax.
	 * If $cart_item is provided this function will calculate value only for this item.
	 * @param mixed $cart_item
	 * @return float
	 */
	public function get_future_payments_amount_no_tax( $_cart_item = null ) {

		$credit_amount = 0;
		$get_all_items = is_null( $_cart_item );
		$_search_key = $get_all_items ? null : self::generate_cart_id( $_cart_item );
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$should_fetch = $get_all_items ? true : ( $_search_key == self::generate_cart_id( $cart_item ) );
			if ( $should_fetch && ( ! empty( $cart_item['is_deposit'] ) ) ) {
				$quantity       = $cart_item['quantity'];
				$full_amount    = $cart_item['full_amount'];
				// We need to apply this filter to the deposit amount, as it may have been affected by Memberships
				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
				if( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
					$credit_amount += $this->get_price_including_tax( $cart_item['data'], array( 'qty' => $quantity, 'price' => ( $full_amount - $deposit_amount ) ) );
				} else {
					$credit_amount += $this->get_price_excluding_tax( $cart_item['data'], array( 'qty' => $quantity, 'price' => ( $full_amount - $deposit_amount ) ) );
				}
			}
		}

		return $credit_amount - self::get_deferred_discount_amount( null, $_cart_item );
	}

	/**
	 * See what is left to pay in future including future discount.
	 * If $cart_item is provided this function will calculate value only for this item.
	 * @param mixed $cart_item
	 * @return float
	 */
	public function get_future_payments_amount_with_discount( $cart_item = null ) {
		return $this->get_deposit_remaining_amount( $cart_item, false, true ) + $this->get_credit_amount( $cart_item, false, true );
	}

	/**
	 * Generate a unique ID for the cart item
	 * @param mixed $cart_item
	 *
	 * @return string
	 */
	public static function generate_cart_id( $cart_item ) {
		if ( is_null( $cart_item ) || is_null( WC()->cart ) ) {
			return null;
		}
		return WC()->cart->generate_cart_id( $cart_item['product_id'], $cart_item['variation_id'], $cart_item['variation'] );
	}

	/**
	 * See whats left to pay after deposits.
	 * If $cart_item is provided this function will calculate value only for this item.
	 * @param mixed $cart_item
	 * @return float
	 */
	public function get_deposit_remaining_amount( $_cart_item = null, $include_tax = false, $after_discount = false ) {
		$credit_amount = 0;
		$_search_key = self::generate_cart_id( $_cart_item );
		$get_all_items = is_null( $_cart_item );

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$should_fetch = $get_all_items || $_search_key == self::generate_cart_id( $cart_item );
			if ( $should_fetch && ( ! empty( $cart_item['is_deposit'] ) && empty( $cart_item['payment_plan'] ) ) ) {
				$_product       = $cart_item['data'];
				$quantity       = $cart_item['quantity'];
				$full_amount    = $cart_item['full_amount'];
				// We need to apply this filter to the deposit amount, as it may have been affected by Memberships
				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $_product );
				$discount = $after_discount ? $this->get_deferred_discount_amount( null, $cart_item ) : 0;
				$discount /= $quantity;

				if ( isset( $cart_item['full_amount'] ) ) {
					if ( WC()->customer->is_vat_exempt() || ( ! $include_tax && 'excl' === WC()->cart->get_tax_price_display_mode() ) ) {
						$credit_amount += $this->get_price_excluding_tax( $_product, array( 'qty' => $quantity, 'price' => ( $full_amount - $deposit_amount - $discount ) ) );
					} else {
						$credit_amount += $this->get_price_including_tax( $_product, array( 'qty' => $quantity, 'price' => ( $full_amount - $deposit_amount - $discount ) ) );
					}
				}
			}
		}

		return $credit_amount;
	}


	/**
	 * See how much credit the user is giving the customer (for payment plans).
	 *
	 * @param mixed $_cart_item     When not null we calculate the credit value only for this item.
	 * @param bool  $include_tax    Override global tax settings.
	 * @param bool  $after_discount Calculate credit after discount has already been applied.
	 * @return float
	 */
	public function get_credit_amount( $_cart_item = null, $include_tax = false, $after_discount = false ) {
		$credit_amount = 0;
		$_search_key   = self::generate_cart_id( $_cart_item );
		$get_all_items = is_null( $_cart_item );

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$should_fetch = $get_all_items || self::generate_cart_id( $cart_item ) === $_search_key;
			if ( ! $should_fetch || empty( $cart_item['is_deposit'] ) || empty( $cart_item['payment_plan'] ) ) {
				continue;
			}

			$_product    = $cart_item['data'];
			$quantity    = $cart_item['quantity'];
			$full_amount = $cart_item['full_amount'];
			$discount    = $after_discount ? $this->get_deferred_discount_amount( null, $cart_item ) : 0;
			$discount   /= $quantity;

			// We need to apply this filter to the deposit amount, as it may have been affected by Memberships.
			$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $_product );

			// Round early to get the full amount minus the amount we already paid.
			$args = array(
				'qty'   => $quantity,
				'price' => $full_amount - round( $deposit_amount + $discount, wc_get_price_decimals() ),
			);

			if ( ! $include_tax && ! WC()->cart->display_prices_including_tax() ) {
				$credit_amount += wc_get_price_excluding_tax( $_product, $args );
			} else {
				$credit_amount += wc_get_price_including_tax( $_product, $args );
			}
		}

		return $credit_amount;
	}

	/**
	 * When an item is added to the cart, validate it.
	 *
	 * @param  mixed $passed
	 * @param  mixed $product_id
	 * @param  mixed $qty
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty ) {
		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $passed;
		}

		$wc_deposit_option       = isset( $_POST['wc_deposit_option'] ) ? sanitize_text_field( $_POST['wc_deposit_option'] ) : false;
		$wc_deposit_payment_plan = isset( $_POST['wc_deposit_payment_plan'] ) ? sanitize_text_field( $_POST['wc_deposit_payment_plan'] ) : false;

		// Validate chosen plan
		if ( ( 'yes' === $wc_deposit_option || WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) && 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $product_id ) ) {
			if ( ! in_array( $wc_deposit_payment_plan, WC_Deposits_Plans_Manager::get_plan_ids_for_product( $product_id ) ) ) {
				wc_add_notice( __( 'Please select a valid payment plan', 'woocommerce-deposits' ), 'error' );
				return false;
			}
		}

		return $passed;
	}

	/**
	 * Add posted data to the cart item.
	 *
	 * @param  mixed $cart_item_meta
	 * @param  mixed $product_id
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $cart_item_meta;
		}

		$wc_deposit_option       = isset( $_POST['wc_deposit_option'] ) ? sanitize_text_field( $_POST['wc_deposit_option'] ) : false;
		$wc_deposit_payment_plan = isset( $_POST['wc_deposit_payment_plan'] ) ? sanitize_text_field( $_POST['wc_deposit_payment_plan'] ) : false;

		if ( 'yes' === $wc_deposit_option || WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) {
			$cart_item_meta['is_deposit'] = true;
			if ( 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $product_id ) ) {
				$cart_item_meta['payment_plan'] = $wc_deposit_payment_plan;
			} else {
				$cart_item_meta['payment_plan'] = 0;
			}
		}

		return $cart_item_meta;
	}


	/**
	 * Runs though all items in the cart applying any deposit information.
	 * Needs to run on the cart_loaded_from_session hook so it runs after the cart has been fully loaded.
	 * @param WC_Cart $cart
	 */
	public function get_cart_from_session( $cart ) {
		if ( sizeof( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
				$result                                = $this->get_cart_item_from_session( $cart_item, $cart_item, $cart_item_key );
				$cart->cart_contents[ $cart_item_key ] = $result;
			}
		}
	}

	/**
	 * Get data from the session and add to the cart item's meta.
	 *
	 * @param  mixed $cart_item
	 * @param  mixed $values
	 * @return array cart item
	 */
	public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
		$cart_item['is_deposit']   = ! empty( $values['is_deposit'] );
		$cart_item['payment_plan'] = ! empty( $values['payment_plan'] ) ? absint( $values['payment_plan'] ) : 0;
		return $this->add_cart_item( $cart_item );
	}

	/**
	 * Adjust the price of the product based on deposits.
	 *
	 * @param  mixed $cart_item
	 * @return array cart item
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$deposit_amount = WC_Deposits_Product_Manager::get_deposit_amount( $cart_item['data'], ! empty( $cart_item['payment_plan'] ) ? $cart_item['payment_plan'] : 0, 'order' );

			if ( false !== $deposit_amount ) {
				$cart_item['deposit_amount'] = $deposit_amount;

				// Bookings support
				if ( isset( $cart_item['booking']['_persons'] ) && 'yes' === WC_Deposits_Product_Meta::get_meta( $cart_item['data']->get_id(), '_wc_deposit_multiple_cost_by_booking_persons' ) ) {
					$cart_item['deposit_amount'] = $cart_item['deposit_amount'] * absint( is_array( $cart_item['booking']['_persons'] ) ? array_sum( $cart_item['booking']['_persons'] ) : $cart_item['booking']['_persons'] );
				}

				// Work out %
				if ( ! empty( $cart_item['payment_plan'] ) ) {
					$plan                     = WC_Deposits_Plans_Manager::get_plan( $cart_item['payment_plan'] );
					$total_percent            = $plan->get_total_percent();
					$cart_item['full_amount'] = ( $cart_item['data']->get_price() / 100 ) * $total_percent;
					$cart_item['data']->set_price( $cart_item['full_amount'] );
				} else {
					$cart_item['full_amount'] = $cart_item['data']->get_price();
				}
			}

			// Pre WC 3.2 uses first payment as item price.
			if( version_compare( WC_VERSION, '3.2', '<' ) ) {
				$cart_item['data']->set_price( $cart_item['deposit_amount'] );
			}
		}

		return $cart_item;
	}

	/**
	 * Clears all deferred discounts in the cart
	 *
	 * @since 1.1.11
	 *
	 * @return void
	 */
	public function clear_deferred_discounts() {
		WC()->session->set( 'deposits_deferred_discounts', array() );
		if( version_compare( WC_VERSION, '3.2', '>=' ) ) {
			WC()->session->set( 'deposits_present_discounts', array() );
			WC()->session->set( 'deposits_discount_tax', array() );
		}
	}

	/**
	 * Control how coupons apply to products including a deposit or payment plan
	 * Filters woocommerce_coupon_get_discount_amount (WC_Coupons get_discount_amount)
	 *
	 * @since 1.1.11
	 *
	 * @param float $discount
	 * @param float $discounting_amount Amount the coupon is being applied to
	 * @param array|null $cart_item Cart item being discounted if applicable
	 * @param boolean $single True if discounting a single qty item, false if its the line (always true in core)
	 * @param WC_Coupon coupon
	 *
	 * @return float Amount this coupon has discounted
	 */
	public function get_discount_amount_legacy( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$old_wc = version_compare( WC_VERSION, '3.0', '<' );
			$coupon_type = $old_wc ? $coupon->type : $coupon->get_discount_type(); // fixed_cart or fixed_product or percent or percent_product
			$coupon_id = $old_wc ? $coupon->id : $coupon->get_id();
			$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $cart_item['product_id'] ); // fixed or percent or plan

			// Initialize default condition
			$present_discount_amount = floatval( $discount );
			$deferred_discount_amount = 0.0;

			// For fixed coupons (fixed_cart or fixed_product)
			// For products with payment plans, discount a proportional amount of the fixed discount now, the rest defer for later
			// For products with fixed deposits, defer the entire fixed discount
			// For products with percentage based deposits, defer the entire fixed discount
			if ( in_array( $coupon_type, array( 'fixed_cart', 'fixed_product' ) ) ) {
				if ( 'plan' === $deposit_type ) {
					$full_amount = floatval( $cart_item['full_amount'] );
					$deposit_amount = floatval( $cart_item['deposit_amount'] );

					// Core has a LB set between the discount and discounting amount.
					// See https://github.com/woocommerce/woocommerce-deposits/issues/160#issuecomment-322428071.
					if ( $deposit_amount < $discount ) {
						$discount = $coupon->get_amount();
					}

					// Calculate proportion due now, avoiding (unlikely) division by zero
					if ( $full_amount > 0 ) {
						$present_proportion = $deposit_amount / $full_amount;
					} else {
						$present_proportion = 1.0;
					}
					// Present discount amount is always for quantity 1
					$present_discount_amount = round( $discount * $present_proportion, 2 );
					// Deferred discount amount is always for the line quantity
					$deferred_discount_amount = round( $discount * $cart_item['quantity'] * ( 1 - $present_proportion ), 2 );
				} else if ( in_array( $deposit_type, array( 'percent', 'fixed' ) ) ) {
					$present_discount_amount = 0;
					$deferred_discount_amount = round( $discount * $cart_item['quantity'], 2 ); // total for (line) quantity, not just unit
				}
			}

			// For percentage based coupons (percent or percent_product)
			// For products with payment plans, pass through the provided discount AND scale and defer it for later
			// For products with fixed deposits, defer the entire discount
			// For products with percentage based deposits, pass through the provided discount AND scale and defer it for later
			if ( in_array( $coupon_type, array( 'percent', 'percent_product' ) ) ) {
				$full_amount = floatval( $cart_item['full_amount'] );
				$deposit_amount = floatval( $cart_item['deposit_amount'] );
				if ( in_array( $deposit_type, array( 'plan', 'percent' ) ) ) {
					// Applies discount toward future amounts to ensure complete discount is not lost
					if ( $deposit_amount > 0 ) {
						$deferred_scaler = ( $full_amount - $deposit_amount ) / $deposit_amount;
						$deferred_discount_amount = round( $discount * $cart_item['quantity'] * $deferred_scaler, 2 );
					}
				} else if( 'fixed' === $deposit_type ) {
					// First, zero the present discount
					$present_discount_amount = 0;
					// Then scale and defer the entire discount
					if ( $deposit_amount > 0 ) {
						$deferred_scaler = $full_amount / $deposit_amount;
						$deferred_discount_amount = round( $discount * $cart_item['quantity'] * $deferred_scaler, 2 );
					}
				}
			}

			if ( $deferred_discount_amount > 0 ) {
				// Save the discount to be applied toward the future amount due
				$search_key = self::generate_cart_id( $cart_item );
				$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
				$deferred_discounts[ $search_key ][ $coupon_id ] = $deferred_discount_amount;
				WC()->session->set( 'deposits_deferred_discounts', $deferred_discounts );
			}

			// If future payments amount are less than 0 (i.e. the coupon has
			// higher discount than the value of the payment), apply the
			// remaining of the coupon amount to the present amount.
			if ( $this->get_future_payments_amount() < 0 ) {
				$present_discount_amount += absint( $this->get_future_payments_amount() );
			}

			// Return the discount to be applied now
			return $present_discount_amount;
		}

		// Otherwise, just pass through the original amount
		return $discount;
	}

	/**
	 * Control how coupons apply to products including a deposit or payment plan
	 * Filters woocommerce_coupon_get_discount_amount (WC_Coupons get_discount_amount)
	 *
	 * @since 1.3.7
	 *
	 * @param float $discount
	 * @param float $discounting_amount Amount the coupon is being applied to
	 * @param array|null $cart_item Cart item being discounted if applicable
	 * @param boolean $single True if discounting a single qty item, false if its the line (always true in core)
	 * @param WC_Coupon coupon
	 *
	 * @return float Amount this coupon has discounted
	 */
	public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( ! empty( $cart_item['is_deposit'] ) && 0 < intval( $discount ) ) {
			$coupon_type = $coupon->get_discount_type(); // fixed_cart or fixed_product or percent or percent_product
			$coupon_id = $coupon->get_id();
			$discounting_amount = $cart_item['deposit_amount'] * $cart_item['quantity'];

			$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $cart_item['product_id'] ); // fixed or percent or plan
			$item_tax = 0;

			// Calculate tax for coupon will be used for adjustment of totals.
			if ( wc_tax_enabled() && 'taxable' === $cart_item['data']->get_tax_status() ) {
				$tax_rates = WC_Tax::get_rates( $cart_item['data']->get_tax_class(), WC()->cart->get_customer() );
				$item_tax = array_sum( WC_Tax::calc_tax( $discount, $tax_rates, 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) );
			}

			// Initialize default condition
			$present_discount_amount = floatval( $discount );
			$deferred_discount_amount = 0.0;

			$full_amount = floatval( $cart_item['full_amount'] );
			$deposit_amount = floatval( $cart_item['deposit_amount'] );
			// Calculate proportion due now, avoiding (unlikely) division by zero
			if ( $full_amount > 0 ) {
				$present_proportion = $deposit_amount / $full_amount;
			} else {
				$present_proportion = 1.0;
			}

			// For fixed coupons (fixed_cart or fixed_product)
			// For products with payment plans, discount a proportional amount of the fixed discount now, the rest defer for later
			// For products with fixed deposits, defer the entire fixed discount
			// For products with percentage based deposits, defer the entire fixed discount
			if ( in_array( $coupon_type, array( 'fixed_cart', 'fixed_product' ) ) ) {
				if ( 'plan' === $deposit_type ) {

					// Present discount amount is always for quantity 1
					$present_discount_amount = round( $discount * $present_proportion, 2 );
					// Deferred discount amount is always for the line quantity
					$deferred_discount_amount = round( $discount * ( 1 - $present_proportion ), 2 );
				} else if ( in_array( $deposit_type, array( 'percent', 'fixed' ) ) ) {
					$present_discount_amount = 0;
					$deferred_discount_amount = round( $discount, 2 ); // total for (line) quantity, not just unit
				}
			}

			// For percentage based coupons (percent or percent_product)
			// For products with payment plans, pass through the provided discount AND scale and defer it for later
			// For products with fixed deposits, defer the entire discount
			// For products with percentage based deposits, pass through the provided discount AND scale and defer it for later
			if ( in_array( $coupon_type, array( 'percent', 'percent_product' ) ) ) {
				$full_amount = floatval( $cart_item['full_amount'] );
				$deposit_amount = floatval( $cart_item['deposit_amount'] );
				if ( in_array( $deposit_type, array( 'plan', 'percent' ) ) ) {
					// Applies discount toward future amounts to ensure complete discount is not lost
					$present_discount_amount = round( $discount * $present_proportion, 2 );
					// Deferred discount amount is always for the line quantity
					$deferred_discount_amount = round( $discount * ( 1 - $present_proportion ), 2 );
				} else if( 'fixed' === $deposit_type ) {
					// Then scale and defer the entire discount
					$deferred_discount_amount = min( $full_amount - $deposit_amount, $discount );
					$present_discount_amount = min( $discounting_amount, $discount - $deferred_discount_amount );
				}
			}

			$search_key = self::generate_cart_id( $cart_item );
			$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts = WC()->session->get( 'deposits_present_discounts', array() );
			$discount_tax = WC()->session->get( 'deposits_discount_tax', array() );

			// Save the discount to be applied toward the future amount due
			if( array_key_exists( $search_key, $deferred_discounts ) && array_key_exists( $coupon_id , $deferred_discounts[ $search_key ] ) ) {
				$deferred_discounts[ $search_key ][ $coupon_id ] += $deferred_discount_amount;
			} else {
				$deferred_discounts[ $search_key ][ $coupon_id ] = $deferred_discount_amount;
			}

			// If future payments amount are less than 0 (i.e. the coupon has
			// higher discount than the value of the payment), apply the
			// remaining of the coupon amount to the present amount and decrease the future part
			$future_payment_amount = $this->get_future_payments_amount_no_tax( $cart_item ) - $deferred_discount_amount;
			if ( $future_payment_amount < 0 ) {
				$deferred_discount_amount += $future_payment_amount;
				$deferred_discounts[ $search_key ][ $coupon_id ] += $future_payment_amount;
				$present_discount_amount += absint( $future_payment_amount );
				$present_discount_amount = min( $present_discount_amount, $discounting_amount );
			}

			if( array_key_exists( $search_key, $present_discounts ) && array_key_exists( $coupon_id , $present_discounts[ $search_key ] ) ) {
				$present_discounts[ $search_key ][ $coupon_id ] += $present_discount_amount;
			} else {
				$present_discounts[ $search_key ][ $coupon_id ] = $present_discount_amount;
			}

			$used_tax = ( $present_discount_amount + $deferred_discount_amount ) / $discount * $item_tax;
			if( array_key_exists( $search_key, $discount_tax ) && array_key_exists( $coupon_id , $discount_tax[ $search_key ] ) ) {
				$discount_tax[ $search_key ][ $coupon_id ] += $used_tax;
			} else {
				$discount_tax[ $search_key ][ $coupon_id ] = $used_tax;
			}

			WC()->session->set( 'deposits_deferred_discounts', $deferred_discounts );
			WC()->session->set( 'deposits_present_discounts', $present_discounts );
			WC()->session->set( 'deposits_discount_tax', $discount_tax );
			// Return the total used discount
			return $present_discount_amount + $deferred_discount_amount;
		}

		// Otherwise, just pass through the original amount
		return $discount;
	}

	/**
	 * @since 1.2.0
	 * @param WC_Coupon/null $coupon
	 * @return float
	 */
	public static function get_stored_discount_amount( $type, $coupon = null, $cart_item = null ) {
		$deferred_discount_amount = 0;

		if ( ! is_object( WC()->session ) ) {
			return $deferred_discount_amount;
		}

		if( ! is_null( $coupon ) ) {
			$coupon_id = version_compare( WC_VERSION, '3.0', '<' ) ? $coupon->id : $coupon->get_id();
		}

		if( ! is_null( $cart_item ) ) {
			$search_key = self::generate_cart_id( $cart_item );
		}

		$get_all_coupons = is_null( $coupon );
		$get_all_items = is_null( $cart_item );

		$deferred_discounts = WC()->session->get( $type, array() );
		foreach( $deferred_discounts as $item_key => $item_discounts ) {
			if( $get_all_items || $search_key == $item_key ) {
				foreach( $item_discounts as $id => $discount ) {
					if ( $get_all_coupons || $coupon_id == $id )
					$deferred_discount_amount += $discount;
				}
			}
		}
		return $deferred_discount_amount;
	}

	/**
	 * Calculates new total taking into account credit amount, deposit and deferred discount
	 *
	 * @param float $total Original cart total.
	 * @return float
	 */
	public function adjust_cart_total( $total ) {
		$deferred_discount_tax = 0;
		if ( wc_tax_enabled() && ! wc_prices_include_tax() ) {
			$tax                   = $this->calculate_deferred_and_present_discount_tax();
			$deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals() );
		}

		$deposits_remaining_amount = $this->get_deposit_remaining_amount( null, true );
		$credit_amount             = $this->get_credit_amount( null, true );
		$deferred_discount_amount  = self::get_deferred_discount_amount();
		return $total - ( $deposits_remaining_amount + $credit_amount ) + $deferred_discount_amount + $deferred_discount_tax;
	}

	/**
	 * Calculates how big portion of tax will be deferred into the future payments.
	 *
	 * @since 1.4.9
	 * @param object $order The order object.
	 * @return float $deferred_tax
	 */
	public function calculate_deferred_tax_from_order( $order ) {
		$deferred_tax = 0;
		$total_tax    = 0; // Used to add up original order tax amount.

		foreach ( $order->get_items( array( 'line_item', 'fee', 'shipping' ) ) as $item_key => $item ) {
			if ( empty( $item['is_deposit'] ) ) {
				$total_tax += floatval( $item['total_tax'] );
				continue;
			}

			$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $item['product_id'] );

			if ( in_array( $deposit_type, array( 'plan', 'percent', 'fixed' ), true ) ) {
				// Full tax for all of the plan payments. Value for order total so it is adjusted for items quantity.
				$full_tax = floatval( $item['deposit_full_amount'] - $item['deposit_full_amount_ex_tax'] );
				// Current tax. Tax for only first payment, just for one item so we need to adjust for quantity.
				$item_tax = floatval( $item['total_tax'] );
				// Deferred/future part.
				$future_tax    = ( $full_tax - $item_tax );
				$deferred_tax += $future_tax;
				$total_tax    += $full_tax;
			} else {
				$deferred_tax += 0;
				$total_tax    += floatval( $item['total_tax'] );
			}
		}

		/*
		 * If we update an order the total tax amount is saved with the
		 * deferred amount already subtracted. We add the tax totals here to
		 * compare the two totals. If the amounts aren't the same prevent the
		 * deferred amount from being subtracted a second time.
		 *
		 * Compare the difference so we aren't affected by rounding errors.
		 */
		if ( abs( floatval( $order->get_total_tax() ) - floatval( $total_tax ) ) >= 0.01 ) {
			return 0;
		}

		return round( $deferred_tax, 2 );
	}

	/**
	 * Calculates taxes which will be deferred into future payments.
	 *
	 * @return array List of all deferred tax rates.
	 */
	public function calculate_deferred_taxes_from_cart() {
		$deferred_taxes = array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( empty( $cart_item['is_deposit'] ) ) {
				continue;
			}

			$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $cart_item['product_id'] );
			if ( ! in_array( $deposit_type, array( 'plan', 'percent', 'fixed' ), true ) ) {
				continue;
			}

			$full_amount                  = floatval( $cart_item['full_amount'] );
			$deposit_amount               = floatval( $cart_item['deposit_amount'] );
			$quantity                     = $cart_item['quantity'];
			$deferred_discount            = $this->get_deferred_discount_amount( null, $cart_item );
			$present_discount             = $this->get_present_discount_amount( null, $cart_item );
			$full_amount_with_discount    = $full_amount * $quantity - ( $deferred_discount + $present_discount );
			$future_payment_with_discount = ( $full_amount - $deposit_amount ) * $quantity - $deferred_discount;
			$deferred_proportion          = 1;

			if ( $full_amount_with_discount ) {
				$deferred_proportion = $future_payment_with_discount / $full_amount_with_discount;
			}

			if ( ! isset( $cart_item['line_tax_data']['total'] ) || ! is_array( $cart_item['line_tax_data']['total'] ) ) {
				continue;
			}

			foreach ( $cart_item['line_tax_data']['total'] as $tax_id => $tax ) {
				if ( ! isset( $deferred_taxes[ $tax_id ] ) ) {
					$deferred_taxes[ $tax_id ] = 0;
				}
				$deferred_taxes[ $tax_id ] += $deferred_proportion * $tax;
			}
		}
		return $deferred_taxes;
	}

	public static function calculate_deferred_and_present_discount_tax( $_cart_item_key = null ) {
		$get_all_items = is_null( $_cart_item_key );
		$tax           = array( 'present' => 0, 'deferred' => 0 );

		$deferred_discounts = array();
		$present_discounts  = array();
		$discounts_tax      = array();

		if ( is_object( WC()->session ) ) {
			$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts  = WC()->session->get( 'deposits_present_discounts', array() );
			$discounts_tax      = WC()->session->get( 'deposits_discount_tax', array() );
		}

		foreach ( $discounts_tax as $cart_item_key => $coupons ) {
			foreach( $coupons as $coupon_id => $discount_tax ) {
				if( $get_all_items || $cart_item_key === $_cart_item_key ) {
					$present_discount = $present_discounts[ $cart_item_key][ $coupon_id ];
					$deferred_discount = $deferred_discounts[ $cart_item_key ][ $coupon_id ];
					$proportion = $present_discount / ( $present_discount + $deferred_discount );
					$tax['present']  += $discount_tax * $proportion;
					$tax['deferred'] += $discount_tax * ( 1 - $proportion );
				}
			}
		}

		return $tax;
	}

	/**
	 * Calculates new subtotal taking into account credit amount and deposit
	 *
	 * @param float $total
	 * @return float
	 */
	public function adjust_cart_subtotal( $total ) {
		return $total - ( $this->get_deposit_remaining_amount() + $this->get_credit_amount() );
	}

	/**
	 * Calculates the sum of all deferred discounts in the cart for all items
	 * or the sum of all discounts for one coupon
	 * Totals are for (line) quantity, not just unit
	 *
	 * @since 1.2.0
	 * @param WC_Coupon/null $coupon
	 * @return float
	 */
	public static function get_deferred_discount_amount( $coupon = null, $cart_item = null ) {
		return self::get_stored_discount_amount( 'deposits_deferred_discounts', $coupon, $cart_item );
	}

	/**
	 * Calculates the sum of all initial discounts in the cart for all items
	 * or the sum of all discounts for one coupon
	 * Totals are for (line) quantity, not just unit
	 *
	 * @since 1.2.0
	 * @param WC_Coupon/null $coupon
	 * @return float
	 */
	public static function get_present_discount_amount( $coupon = null, $cart_item = null ) {
		return self::get_stored_discount_amount( 'deposits_present_discounts', $coupon, $cart_item );
	}

	/**
	 * Get tax amount for discounts for all items
	 * or the sum of all discounts for one coupon
	 * Totals are for (line) quantity, not just unit
	 *
	 * @since 1.2.0
	 * @param WC_Coupon/null $coupon
	 * @return float
	 */
	public static function get_discount_tax( $coupon = null, $cart_item = null ) {
		return self::get_stored_discount_amount( 'deposits_discount_tax', $coupon, $cart_item );
	}

	/**
	 * Put meta data into format which can be displayed.
	 *
	 * @param  mixed $other_data
	 * @param  mixed $cart_item
	 * @return array meta
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['payment_plan'] ) ) {
			$plan         = WC_Deposits_Plans_Manager::get_plan( $cart_item['payment_plan'] );
			$other_data[] = array(
				'name'    => __( 'Payment Plan', 'woocommerce-deposits' ),
				'value'   => $plan->get_name(),
				'display' => '',
			);
		}
		return $other_data;
	}

	/**
	 * Show the correct item price.
	 */
	public function display_item_price( $output, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item['full_amount'] ) ) {
			return $output;
		}
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$_product = $cart_item['data'];
			if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
				$amount = $this->get_price_excluding_tax( $_product, array( 'qty' => 1, 'price' => $cart_item['full_amount'] ) );
			} else {
				$amount = $this->get_price_including_tax( $_product, array( 'qty' => 1, 'price' => $cart_item['full_amount'] ) );
			}
			$output = wc_price( $amount );
		}
		return $output;
	}

	/**
	 * Adjust the subtotal display in the cart.
	 */
	public function display_item_subtotal( $output, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item['full_amount'] ) ) {
			return $output;
		}

		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$_product       = $cart_item['data'];
			$quantity       = $cart_item['quantity'];
			$full_amount    = $cart_item['full_amount'];
			// We need to apply this filter to the deposit amount, as it may have been affected by Memberships
			$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $_product );

			if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
				$full_amount    = $this->get_price_excluding_tax( $_product, array( 'qty' => $quantity, 'price' => $full_amount ) );
				$deposit_amount = $this->get_price_excluding_tax( $_product, array( 'qty' => $quantity, 'price' => $deposit_amount ) );
			} else {
				$full_amount    = $this->get_price_including_tax( $_product, array( 'qty' => $quantity, 'price' => $full_amount ) );
				$deposit_amount = $this->get_price_including_tax( $_product, array( 'qty' => $quantity, 'price' => $deposit_amount ) );
			}

			// Adding this to be compatible with WC3.2 changes. Allow further modification by other plugins.
			if( version_compare( WC_VERSION, '3.2', '>=' ) ) {
				$output = apply_filters( 'woocommerce_cart_product_price', wc_price( $deposit_amount ), $_product );
			}

			if ( ! empty( $cart_item['payment_plan'] ) ) {
				$plan = new WC_Deposits_Plan( $cart_item['payment_plan'] );
				$output .= '<br/><small>' . $plan->get_formatted_schedule( $full_amount ) . '</small>';
			} else {
				$output .= '<br/><small>' . sprintf( __( '%s payable in total', 'woocommerce-deposits' ), wc_price( $full_amount ) ) . '</small>';
			}
		}

		return $output;
	}

	/**
	 * Before the main total.
	 */
	public function display_cart_totals_before() {
		if ( self::get_future_payments_amount() > 0 ) {
			ob_start();
			$this->buffered = true;
		}
	}

	/**
	 * Get order total html including inc tax if needed
	 *
	 * @access public
	 */
	private function cart_totals_order_total_html() {
		$value = '<strong>' . WC()->cart->get_total() . '</strong> ';

		$value .= $this->cart_totals_order_tax_formated_output();

		echo apply_filters( 'woocommerce_cart_totals_order_total_html', $value );
	}

	private function cart_totals_order_tax_formated_output() {
		// If prices are tax inclusive, show taxes here.
		$value = '';
		if ( wc_tax_enabled() && WC()->cart->display_prices_including_tax() ) {
			$value .= $this->cart_totals_tax_string();
		}
		return $value;
	}

	/**
	 * Generate a tax totals string to display for the Due Today line in the cart.
	 * This function is copied from "wc_cart_totals_order_total_html" and subtracts
	 * the deposit amount, when displaying a combined total.
	 *
	 * @return string
	 */
	private function cart_totals_tax_string() {
		$value            = '';
		$tax_string_array = array();
		$cart_tax_totals  = WC()->cart->get_tax_totals();

		if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) {
			foreach ( $cart_tax_totals as $code => $tax ) {
				$tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
			}
		} elseif ( ! empty( $cart_tax_totals ) ) {
			$tax                = WC()->cart->get_taxes_total( true, true ) - array_sum( $this->calculate_deferred_taxes_from_cart() );
			$tax_string_array[] = sprintf( '%s %s', wc_price( $tax ), WC()->countries->tax_or_vat() );
		}

		if ( ! empty( $tax_string_array ) ) {
			$taxable_address = WC()->customer->get_taxable_address();
			/* translators: %s: country name */
			$estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ? sprintf( ' ' . __( 'estimated for %s', 'woocommerce' ), WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] ) : '';
			/* translators: %s: tax information */
			$value .= '<small class="includes_tax">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) . $estimated_text ) . '</small>';
		}
		return $value;
	}

	/**
	 * Displays the adjusted deferred taxes on the order details page.
	 *
	 * @since 1.4.9
	 * @param float  $taxes The current tax totals.
	 * @param object $order The order object.
	 * @return array $tax Array of altered taxes.
	 */
	public function order_totals_order_taxes( $taxes, $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return $taxes;
		}

		// Deposit taxes are saved in the order since version 1.4.19, so we no longer need to adjust them here.
		if ( version_compare( $order->get_meta( '_wc_deposits_version' ), '1.4.19', '>=' ) ) {
			return $taxes;
		}

		$deferred_tax = $this->calculate_deferred_tax_from_order( $order );

		if ( ! $deferred_tax ) {
			// No modifications required.
			return $taxes;
		}

		$tax    = array();
		$code   = WC()->countries->tax_or_vat();
		$amount = $order->get_total_tax() - $deferred_tax;

		$tax[ $code ] = (object) array(
			'label'            => $code,
			'amount'           => $amount,
			'formatted_amount' => wc_price( $amount ),
		);

		return $tax;
	}

	/**
	 * Update order totals to include the deposit adjustments.
	 *
	 * @since 1.4.19
	 * @param WC_Order $order Order object.
	 * @param array    $data  Saved order data.
	 *
	 * @return void
	 */
	public function update_order_totals( $order, $data ) {
		if ( ! WC_Deposits_Order_Manager::has_deposit( $order ) ) {
			return;
		}

		$order->calculate_totals( true );

		// Save the deposit version in the order so we can display totals conditionally.
		$order->add_meta_data( '_wc_deposits_version', WC_DEPOSITS_VERSION );
	}

	/**
	 * Calculates the adjusted deferred taxes on the cart page.
	 *
	 * @param float $taxes The current tax totals.
	 * @return array List of adjusted taxes.
	 */
	public function cart_totals_order_taxes( $taxes ) {
		$deferred_tax = $this->calculate_deferred_taxes_from_cart();

		if ( empty( $deferred_tax ) ) {
			// No modifications required.
			return $taxes;
		}

		// Subtract deferred taxes from each rate.
		foreach ( $taxes as $code => $tax ) {
			if ( ! empty( $deferred_tax[ $tax->tax_rate_id ] ) ) {
				$taxes[ $code ]->amount           = $tax->amount - $deferred_tax[ $tax->tax_rate_id ];
				$taxes[ $code ]->formatted_amount = wc_price( $taxes[ $code ]->amount );
			}
		}

		return $taxes;
	}

	/**
	 * After the main total.
	 */
	public function display_cart_totals_after() {
		$_WC32plus = version_compare( WC_VERSION, '3.2', '>=' );

		if ( $_WC32plus ) {
			$future_payment_amount = self::get_future_payments_amount_with_discount();
		} else {
			$future_payment_amount = self::get_future_payments_amount();
		}

		$is_tax_included = wc_tax_enabled() && 'excl' != WC()->cart->get_tax_price_display_mode();
		$tax_message     = $is_tax_included ? __( '(includes tax)', 'woocommerce-deposits' ) : __( '(excludes tax)', 'woocommerce-deposits' );
		$tax_element     = wc_tax_enabled() && ! empty( WC()->cart->get_tax_totals() ) ? ' <small class="tax_label">' . $tax_message . '</small>' : '';
		$deferred_discount_tax = 0;
		if ( $_WC32plus ) {
			$tax = $this->calculate_deferred_and_present_discount_tax();
			if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
				if ( $is_tax_included ) {
					$deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals());
				}
			} else {
				if ( ! $is_tax_included ) {
					$deferred_discount_tax = -round($tax['deferred'], wc_get_price_decimals());
				}
			}
		}

		$deferred_discount_amount  = self::get_deferred_discount_amount();

		if ( ! empty( $this->buffered ) ) {
			ob_end_clean();
			$this->buffered = false;
		}

		if ( 0 >= $future_payment_amount && $deferred_discount_amount <= 0 ) {
			return;
		}
		?>
		<tr class="order-total">
			<th><?php _e( 'Due Today', 'woocommerce-deposits' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Due Today', 'woocommerce-deposits' ); ?>"><?php $_WC32plus ? $this->cart_totals_order_total_html() : wc_cart_totals_order_total_html(); ?></td>
		</tr>
		<?php
		if ( $deferred_discount_amount > 0 ) {
			?>
			<tr class="order-total">
				<th><?php _e( 'Discount Applied Toward Future Payments', 'woocommerce-deposits' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Discount Applied Toward Future Payments', 'woocommerce-deposits' ); ?>"><?php echo wc_price( -$deferred_discount_amount - $deferred_discount_tax); ?></td>
			</tr>
			<?php
		}
		?>
		<tr class="order-total">
		<th><?php _e( 'Future Payments', 'woocommerce-deposits' ); ?></th>
		<td data-title="<?php esc_attr_e( 'Future Payments', 'woocommerce-deposits' ); ?>"><?php echo wc_price( $future_payment_amount ); ?><?php echo $tax_element; ?></td>
		</tr><?php
	}

	/**
	 * Store cart info inside new orders.
	 * Runs on 2.6 and older.
	 * Hooked on woocommerce_add_order_item_meta action
	 *
	 * @version 1.2.0
	 *
	 * @param mixed $item_id
	 * @param mixed $cart_item
	 */
	public function add_order_item_meta_legacy( $item_id, $cart_item ) {
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			// Note: This code is called for the INITIAL order created from carts containing products
			// with fixed deposits, percentage based deposits or payment plans. HOWEVER, this
			// code is NOT used when WC_Deposits_Scheduled_Order_Manager::schedule_orders_for_plan
			// creates orders for the remaining payments for a payment plan product NOR when
			// the merchant invoices the customer for the remaining balance.

			// First, calculate the full amount (before deposits/payments)
			// Note that this is for the entire line quantity, not just a unit
			$full_amount_including_tax = $cart_item['data']->get_price_including_tax( $cart_item['quantity'], $cart_item['full_amount'] );
			$full_amount_excluding_tax = $cart_item['data']->get_price_excluding_tax( $cart_item['quantity'], $cart_item['full_amount'] );

			// Next, for fixed or percentage based deposits, calculate the initial deposit, prior to tax, regardless of discounts
			// so that WC_Deposits_Order_Manager::order_action_handler invoice_remaining_balance can calculate the correct amount to charge
			$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
			$deposit_amount_excluding_tax = $this->get_price_excluding_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $deposit_amount ) );

			// Next, add up any deferred discounts for the item
			// Note: $deferred_discount_amount is total for (line) quantity, not just unit
			$deferred_discount_amount = 0;
			$search_key = self::generate_cart_id( $cart_item );
			$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
			if ( array_key_exists( $search_key, $deferred_discounts ) ) {
				foreach ( $deferred_discounts[ $search_key ] as $coupon_id => $discount_amount ) {
					$deferred_discount_amount += $discount_amount;
				}
			}

			// Lastly, decorate the order item with this information so we can calculate future payment(s) later
			// in WC_Deposits_Order_Manager::order_action_handler for invoice_remaining_balance
			wc_add_order_item_meta( $item_id, '_is_deposit', 'yes' );
			wc_add_order_item_meta( $item_id, '_deposit_full_amount', $full_amount_including_tax ); // line quantity, not just a unit
			wc_add_order_item_meta( $item_id, '_deposit_full_amount_ex_tax', $full_amount_excluding_tax );
			wc_add_order_item_meta( $item_id, '_deposit_deposit_amount_ex_tax', $deposit_amount_excluding_tax );
			wc_add_order_item_meta( $item_id, '_deposit_deferred_discount', $deferred_discount_amount ); // total for (line) quantity, not just unit

			if ( ! empty( $cart_item['payment_plan'] ) ) {
				wc_add_order_item_meta( $item_id, '_payment_plan', $cart_item['payment_plan'] );
			}
		}
	}

	/**
	 * Store cart info inside new orders.
	 *
	 * @param WC_Order_Item $item
	 * @param string        $cart_item_key
	 * @param array         $values
	 */
	public function add_order_item_meta( $item, $cart_item_key, $values ) {
		$cart      = WC()->cart->get_cart();
		$cart_item = $cart[ $cart_item_key ];
		$_WC32plus = version_compare( WC_VERSION, '3.2', '>=' );

		if ( ! empty( $cart_item['is_deposit'] ) ) {
			if( $_WC32plus ) {
				// First, calculate the full amount (before deposits/payments)
				// Note that this is for the entire line quantity, not just a unit
				$full_amount_including_tax = $this->get_price_including_tax_no_round( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $cart_item['full_amount'] ) );
				$full_amount_excluding_tax = $this->get_price_excluding_tax_no_round( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $cart_item['full_amount'] ) );

				// Next, for fixed or percentage based deposits, calculate the initial deposit, prior to tax, regardless of discounts
				// so that WC_Deposits_Order_Manager::order_action_handler invoice_remaining_balance can calculate the correct amount to charge
				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
				$deposit_amount_excluding_tax = $this->get_price_excluding_tax_no_round( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $deposit_amount ) );
				$deposit_amount_including_tax = $this->get_price_including_tax_no_round( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $deposit_amount ) );
			} else {
				// First, calculate the full amount (before deposits/payments)
 			    // Note that this is for the entire line quantity, not just a unit
			    $full_amount_including_tax = $this->get_price_including_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $cart_item['full_amount'] ) );
			    $full_amount_excluding_tax = $this->get_price_excluding_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $cart_item['full_amount'] ) );

				// Next, for fixed or percentage based deposits, calculate the initial deposit, prior to tax, regardless of discounts
 				// so that WC_Deposits_Order_Manager::order_action_handler invoice_remaining_balance can calculate the correct amount to charge
 				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
				$deposit_amount_excluding_tax = $this->get_price_excluding_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $deposit_amount ) );
				$deposit_amount_including_tax = $this->get_price_including_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'], 'price' => $deposit_amount ) );
			}

			// We cannot use the cart_item_key provided since it differs from the one we use to store the discount.
			$search_key = self::generate_cart_id( $cart_item );

			// Retrieve present and deferred discounts for the item.
			$deferred_discounts       = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts        = WC()->session->get( 'deposits_present_discounts', array() );
			$deferred_discount_amount = isset( $deferred_discounts[ $search_key ] ) ? array_sum( $deferred_discounts[ $search_key ] ) : 0;
			$present_discount_amount  = isset( $present_discounts[ $search_key ] ) ? array_sum( $present_discounts[ $search_key ] ) : 0;

			// Adjust values to represent proper total and subtotal after applied discounts.
			if ( $_WC32plus ) {
				$discount_tax = $this->calculate_deferred_and_present_discount_tax( $search_key );
				$deferred_discount_tax_amount = $discount_tax['deferred'];

				if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
					$deferred_discount_amount_excluding_tax = $deferred_discount_amount;
					$deferred_discount_amount              += $deferred_discount_tax_amount;
				} else {
					$deferred_discount_amount_excluding_tax = $deferred_discount_amount - $deferred_discount_tax_amount;
				}

				$deposit_ratio = $deposit_amount_including_tax / $full_amount_including_tax;
				$item->set_total( ( $values['line_subtotal'] * $deposit_ratio ) - $present_discount_amount );
				$item->set_subtotal( $values['line_subtotal'] * $deposit_ratio );
				$taxes             = $item->get_taxes();
				$scale             = function ( $tax ) use ( $deposit_ratio ) { return $tax * $deposit_ratio; };
				$taxes['subtotal'] = array_map( $scale, $taxes['subtotal'] );
				$taxes['total']    = array_map( $scale, $taxes['total'] );
				$item->set_taxes( $taxes );
			}

			$item->add_meta_data( '_is_deposit', 'yes' );
			$item->add_meta_data( '_deposit_full_amount', $full_amount_including_tax ); // line quantity, not just a unit
			$item->add_meta_data( '_deposit_full_amount_ex_tax', $full_amount_excluding_tax );
			$item->add_meta_data( '_deposit_deposit_amount_ex_tax', $deposit_amount_excluding_tax );
			if ( $deferred_discount_amount > 0 ) {
				$item->add_meta_data( '_deposit_deferred_discount', $deferred_discount_amount ); // line quantity, not just a unit
				if( $_WC32plus ) {
					$item->add_meta_data( '_deposit_deferred_discount_ex_tax', $deferred_discount_amount_excluding_tax ); // line quantity, not just a unit
				}
			}

			if ( ! empty( $cart_item['payment_plan'] ) ) {
				$item->add_meta_data( '_payment_plan', $cart_item['payment_plan'] );
			}
		}
	}

	/**
	 * Disable gateways when using deposits.
	 *
	 * @param  array $gateways
	 * @return array
	 */
	public function disable_gateways( $gateways = array() ) {
		if ( is_admin() ) {
			return $gateways;
		}
		$disabled = get_option( 'wc_deposits_disabled_gateways', array() );
		if ( ( $this->has_deposit() || $this->is_deposit_order() ) && ! empty( $disabled ) && is_array( $disabled ) ) {
			return array_diff_key( $gateways, array_combine( $disabled, $disabled ) );
		}

		return $gateways;
	}

	/**
	 * Add to cart text.
	 */
	public function add_to_cart_text( $text ) {
		global $product;

		if ( ! is_object( $product ) ) {
			return $text;
		}

		if ( is_single( $product->get_id() ) ) {
			return $text;
		}

		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product->get_id() ) ) {
			return $text;
		}

		$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $product->get_id() );
		if ( WC_Deposits_Product_Manager::deposits_forced( $product->get_id() ) ) {
			if ( 'plan' !== $deposit_type ) {
				return $text;
			}
		}

		$text = apply_filters( 'woocommerce_deposits_add_to_cart_text', __( 'Select options', 'woocommerce-deposits' ) );

		return $text;
	}

	/**
	 * Add to cart URL.
	 *
	 * @version 1.2.2
	 *
	 * @param string $url URL.
	 *
	 * @return string URL.
	 */
	public function add_to_cart_url( $url ) {
		global $product;

		$product = wc_get_product( $product );
		if ( ! is_object( $product ) ) {
			return $url;
		}

		$product_id = $product->get_id();

		if ( is_single( $product_id ) ) {
			return $url;
		}

		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $url;
		}

		$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $product_id );
		if ( WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) {
			if ( 'plan' !== $deposit_type ) {
				return $url;
			}
		}

		$url = apply_filters( 'woocoommerce_deposits_add_to_cart_url', get_permalink( $product_id ) );
		return $url;
	}

	/**
	 * Remove the add to cart class from deposit products.
	 *
	 * @param  string $link HTML link
	 * @param  WC_Product $product Product
	 * @return string HTML link
	 */
	public function remove_add_to_cart_class( $link, $product ) {
		$product_id = $product->get_id();
		if ( WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			$deposit_type = WC_Deposits_Product_Manager::get_deposit_type( $product_id );
				// If the product has a Payment Plan or Deposits are optional, remove the add to cart class(Disable AJAX add to cart).
				if ( 'plan' === $deposit_type || ! WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) {
					$link = str_replace( 'add_to_cart_button', '', $link );
				}
			}
		return $link;
	}

	/**
	 * Provides a way to support both 2.6 and 3.0 since get_price_including_tax
	 * gets deprecated in 3.0, and wc_get_price_including_tax gets introduced in
	 * 3.0.
	 *
	 * @since  1.2
	 * @param  WC_Product $product
	 * @param  array      $args
	 * @return float
	 */
	private function get_price_including_tax( $product, $args ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$args = wp_parse_args( $args, array(
				'qty'   => '',
				'price' => '',
			) );
			return $product->get_price_including_tax( $args['qty'], $args['price'] );
		} else {
			return wc_get_price_including_tax( $product, $args );
		}
	}

	private function get_price_excluding_tax_no_round( $product, $args ) {
		$price = '' !== $args['price'] ? max( 0.0, (float) $args['price'] ) : $product->get_price();
		$qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;

		if ( $product->is_taxable() && wc_prices_include_tax() ) {
			$tax_rates  = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
			$taxes      = WC_Tax::calc_tax( $price * $qty, $tax_rates, true );
			$price      = $price * $qty - array_sum( $taxes );
		} else {
			$price = $price * $qty;
		}

		return apply_filters( 'woocommerce_get_price_excluding_tax', $price, $qty, $product );
	}

	private function get_price_including_tax_no_round( $product, $args ) {
		$price = '' !== $args['price'] ? max( 0.0, (float) $args['price'] ) : $product->get_price();
		$qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;

		$line_price   = $price * $qty;
		$return_price = $line_price;

		if ( $product->is_taxable() ) {
			if ( ! wc_prices_include_tax() ) {
				$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
				$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
				$tax_amount   = WC_Tax::get_tax_total( $taxes );
				$return_price = $line_price + $tax_amount;
			} else {
				$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
				$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

				/*
				 * If the customer is excempt from VAT, remove the taxes here.
				 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
				 */
				if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
					$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
					$remove_tax   = array_sum( $remove_taxes );
					$return_price = $line_price - $remove_tax;

					/**
				 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
				 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
				 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
				 */
				} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
					$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
					$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
					$return_price = $line_price - array_sum( $base_taxes ) + array_sum( $modded_taxes );
				}
			}
		}
		return apply_filters( 'woocommerce_get_price_including_tax', $return_price, $qty, $product );
	}


	/**
	 * Provides a way to support both 2.6 and 3.0 since get_price_excluding_tax
	 * gets deprecated in 3.0, and wc_get_price_excluding_tax gets introduced in
	 * 3.0.
	 *
	 * @since  1.2
	 * @param  WC_Product $product
	 * @param  array      $args
	 * @return float
	 */
	private function get_price_excluding_tax( $product, $args ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$args = wp_parse_args( $args, array(
				'qty'   => '',
				'price' => '',
			) );
			return $product->get_price_excluding_tax( $args['qty'], $args['price'] );
		} else {
			return wc_get_price_excluding_tax( $product, $args );
		}
	}

	/**
	 * Fix deposits position on variable products - show them after a single variation description
	 * or out of stock message.
	 */
	public function reposition_display_for_variable_product() {
		remove_action( 'woocommerce_before_add_to_cart_button', array( $this, 'deposits_form_output' ), 99 );
		add_action( 'woocommerce_single_variation', array( $this, 'deposits_form_output' ), 16 );
	}
}

WC_Deposits_Cart_Manager::get_instance();
