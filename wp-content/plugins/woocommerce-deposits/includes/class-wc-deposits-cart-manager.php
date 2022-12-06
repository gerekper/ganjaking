<?php
/**
 * Deposits cart manager
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Cart_Manager class.
 */
class WC_Deposits_Cart_Manager {

	/**
	 * Class Instance
	 *
	 * @var WC_Deposits_Cart_Manager
	 */
	private static $instance;

	/**
	 * Whether we're buffering
	 *
	 * @var boolean
	 */
	private $buffered = false;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'deposits_form_output' ), 99 );
		add_action( 'woocommerce_before_variations_form', array( $this, 'reposition_display_for_variable_product' ), 99 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 6 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 99, 1 );

		// Apply deposit information on cart delayed in case of membership plugin is installed (need to apply on front-end only).
		// Memberships plugins adds filters on price on `wp_loaded` with 15 priority.
		// So, apply deposit info to cart at `wp_loaded` with 20 priority.
		// see issue https://github.com/woocommerce/woocommerce-deposits/issues/381.
		if ( function_exists( 'wc_memberships' ) &&
			( ! is_admin() || defined( 'DOING_AJAX' ) ) &&
			! defined( 'DOING_CRON' ) &&
			! WC()->is_rest_api_request()
		) {
			add_action( 'wp_loaded', array( $this, 'apply_deposit_info_to_cart' ), 20 );
		} else {
			// Apply discounts after the cart is completely loaded.
			// Dynamic Pricing applies discounts after the cart is completely loaded
			// to account for category quantity discounts.
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'get_cart_from_session' ), 99, 1 );
		}

		// Control how coupons apply to products including a deposit or payment plan.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'clear_deferred_discounts' ) );

		// Adjust mini-cart subtotal.
		add_action( 'woocommerce_widget_shopping_cart_total', array( $this, 'adjust_cart_subtotal' ), 10 );

		// Add WC3.2 Coupons upgrade compatibility.
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount' ), 10, 5 );
		add_filter( 'woocommerce_cart_tax_totals', array( $this, 'cart_totals_order_taxes' ), 10, 2 );
		add_filter( 'woocommerce_order_get_tax_totals', array( $this, 'order_totals_order_taxes' ), 10, 2 );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'adjust_cart_totals' ), 10, 1 );
		add_action( 'woocommerce_coupon_validate_minimum_amount', array( $this, 'check_coupon_minimum_amount' ), 10, 3 );
		add_action( 'woocommerce_order_status_partial-payment', 'wc_update_coupon_usage_counts' );
		// Update order totals as late as possible, so deposit totals are recalculated before saving.
		add_action( 'woocommerce_checkout_create_order', array( $this, 'update_order_totals' ), 999, 2 );

		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'display_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'display_item_subtotal' ), 10, 3 );
		add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_cart_totals_before' ), 99 );
		add_action( 'woocommerce_review_order_before_order_total', array( $this, 'display_cart_totals_before' ), 99 );
		add_action( 'woocommerce_cart_totals_after_order_total', array( $this, 'display_cart_totals_after' ), 1 );
		add_action( 'woocommerce_review_order_after_order_total', array( $this, 'display_cart_totals_after' ), 1 );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways' ) );

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 50, 3 );

		// Change button/cart URLs.
		add_filter( 'add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 15 );
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 10, 1 );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'remove_add_to_cart_class' ), 10, 2 );

		// Handle Order Again.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 10, 3 );

		// TaxJar compatibility.
		add_filter( 'taxjar_after_calculate_cart_totals', array( $this, 'adjust_cart_totals_after_taxjar' ) );

		// Display correct tax when the "Display Tax Totals" setting is set to "As a single total".
		// @see https://github.com/woocommerce/woocommerce-deposits/issues/385.
		add_filter( 'woocommerce_cart_totals_taxes_total_html', array( $this, 'cart_totals_taxes_total_html' ) );
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
	 * @param WC_Cart|null $cart WooCommerce cart.
	 * @return boolean
	 */
	public function has_deposit( $cart = null ) {

		$cart = empty( $cart ) && ! is_null( WC()->cart ) ? WC()->cart : $cart;

		if ( $cart ) {
			foreach ( $cart->get_cart() as $cart_item ) {
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
	 *
	 * @param boolean   $is_invalid Default filter value.
	 * @param WC_Coupon $coupon Coupon.
	 * @param float     $subtotal Subtotal.
	 * @return boolean
	 */
	public function check_coupon_minimum_amount( $is_invalid, $coupon, $subtotal ) {
		// Check if we can exit early.
		if ( ! $this->has_deposit() ) {
			// Not a deposit case.
			return $is_invalid;
		}
		if ( ! $is_invalid ) {
			// This method only adjusts for deffered part in case the coupon is invalid
			// if $is_invalid === fale this means that the coupon has alredy passed the
			// criteria and we do not need to check further.
			return $is_invalid;
		}

		// It is a deposit case, and coupon is not valid becus minimum spending amount
		// condition is not met. We will check if what is storred in deffered amount of
		// the deposit payment will not be enough to push the coupon over the minimum limit.
		$future_payments = $this->get_deposit_remaining_amount() + $this->get_credit_amount();
		return $coupon->get_minimum_amount() > ( $subtotal + $future_payments );
	}


	/**
	 * See how much credit the user is giving the customer (for payment plans)
	 * If $cart_item is provided this function will calculate value only for this item.
	 *
	 * @param mixed $cart_item Cart Item.
	 * @return float
	 */
	public function get_future_payments_amount( $cart_item = null ) {
		return $this->get_deposit_remaining_amount( $cart_item ) + $this->get_credit_amount( $cart_item ) - self::get_deferred_discount_amount( null, $cart_item );
	}

	/**
	 * See how much credit the user is giving the customer. This calculates value excluding tax.
	 * If $cart_item is provided this function will calculate value only for this item.
	 *
	 * @param mixed $_cart_item Cart Item.
	 * @return float
	 */
	public function get_future_payments_amount_no_tax( $_cart_item = null ) {

		$credit_amount = 0;
		$get_all_items = is_null( $_cart_item );
		$_search_key   = $get_all_items ? null : self::generate_cart_id( $_cart_item );
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$should_fetch = $get_all_items ? true : ( self::generate_cart_id( $cart_item ) === $_search_key );
			if ( $should_fetch && ( ! empty( $cart_item['is_deposit'] ) ) ) {
				$quantity    = $cart_item['quantity'];
				$full_amount = $cart_item['full_amount'];
				// We need to apply this filter to the deposit amount, as it may have been affected by Memberships.
				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
				if ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
					$credit_amount += $this->get_price_including_tax(
						$cart_item['data'],
						array(
							'qty'   => $quantity,
							'price' => ( $full_amount - $deposit_amount ),
						)
					);
				} else {
					$credit_amount += $this->get_price_excluding_tax(
						$cart_item['data'],
						array(
							'qty'   => $quantity,
							'price' => ( $full_amount - $deposit_amount ),
						)
					);
				}
			}
		}

		return $credit_amount - self::get_deferred_discount_amount( null, $_cart_item );
	}

	/**
	 * See what is left to pay in future including future discount.
	 * If $cart_item is provided this function will calculate value only for this item.
	 *
	 * @param mixed $cart_item Cart Item.
	 * @return float
	 */
	public function get_future_payments_amount_with_discount( $cart_item = null ) {
		return $this->get_deposit_remaining_amount( $cart_item, false, true ) + $this->get_credit_amount( $cart_item, false, true );
	}

	/**
	 * Generate a unique ID for the cart item
	 *
	 * @param array|null $cart_item Cart Item.
	 * @return string
	 */
	public static function generate_cart_id( $cart_item ) {
		if ( is_null( $cart_item ) || is_null( WC()->cart ) ) {
			return null;
		}

		$cart_item_data = array();
		/**
		 * Consider booking data in generate cart id,
		 * Otherwise it will generate same cart id for 2 booking items from same bookable product.
		 *
		 * @see https://github.com/woocommerce/woocommerce-deposits/issues/427
		 */
		if ( isset( $cart_item['booking'] ) && isset( $cart_item['booking']['_booking_id'] ) ) {
			$cart_item_data['booking_id'] = $cart_item['booking']['_booking_id'];
		}

		// Consider product addons.
		if ( isset( $cart_item['addons'] ) && ! empty( $cart_item['addons'] ) ) {
			$cart_item_data['addons'] = $cart_item['addons'];
		}

		return WC()->cart->generate_cart_id( $cart_item['product_id'], $cart_item['variation_id'], $cart_item['variation'], $cart_item_data );
	}

	/**
	 * Checks if a parameter exist in $_POST or $_GET.
	 *
	 * @deprecated 1.6.1
	 * @param string $name a key/parameter.
	 *
	 * @return mixed|null
	 */
	public static function check_global_param_exist( $name ) {
		wc_deprecated_function( __FUNCTION__, '1.6.1', 'get_global_param' );
		return self::get_global_param( $name );
	}

	/**
	 * Checks if a parameter exist in $_POST or $_GET.
	 *
	 * @param string $name a key/parameter.
	 *
	 * @return mixed|null
	 */
	public static function get_global_param( $name ) {
		$value = null;
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ $name ] ) && ! empty( $_POST[ $name ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $name ] ) );
		} elseif ( isset( $_GET[ $name ] ) && ! empty( $_GET[ $name ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_GET[ $name ] ) );
		}
		// phpcs:enable

		return $value;
	}

	/**
	 * See whats left to pay after deposits.
	 * If $cart_item is provided this function will calculate value only for this item.
	 *
	 * @param array|null $_cart_item Cart Item.
	 * @param boolean    $include_tax Include tax.
	 * @param boolean    $after_discount After discount.
	 * @return float
	 */
	public function get_deposit_remaining_amount( $_cart_item = null, $include_tax = false, $after_discount = false ) {
		$credit_amount = 0;
		$_search_key   = self::generate_cart_id( $_cart_item );
		$get_all_items = is_null( $_cart_item );

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$should_fetch = $get_all_items || self::generate_cart_id( $cart_item ) === $_search_key;
			if ( $should_fetch && ( ! empty( $cart_item['is_deposit'] ) && empty( $cart_item['payment_plan'] ) ) ) {
				$_product    = $cart_item['data'];
				$quantity    = $cart_item['quantity'];
				$full_amount = $cart_item['full_amount'];
				// We need to apply this filter to the deposit amount, as it may have been affected by Memberships.
				$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $_product );
				$discount       = $after_discount ? $this->get_deferred_discount_amount( null, $cart_item ) : 0;
				$discount      /= $quantity;

				if ( isset( $cart_item['full_amount'] ) ) {
					if ( WC()->customer->is_vat_exempt() || ( ! $include_tax && 'excl' === WC()->cart->get_tax_price_display_mode() ) ) {
						$credit_amount += $this->get_price_excluding_tax(
							$_product,
							array(
								'qty'   => $quantity,
								'price' => ( $full_amount - $deposit_amount - $discount ),
							)
						);
					} else {
						$credit_amount += $this->get_price_including_tax(
							$_product,
							array(
								'qty'   => $quantity,
								'price' => ( $full_amount - $deposit_amount - $discount ),
							)
						);
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
	 * @param bool  $passed         Default validation value.
	 * @param int   $product_id     Product ID.
	 * @param int   $qty            Quantity.
	 * @param int   $variation_id   Variation ID.
	 * @param array $variations     Variations array.
	 * @param array $cart_item_data Cart item data.
	 *
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty, $variation_id = 0, $variations = array(), $cart_item_data = array() ) {
		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $passed;
		}

		$wc_deposit_option       = self::get_global_param( 'wc_deposit_option' );
		$wc_deposit_payment_plan = (int) self::get_global_param( 'wc_deposit_payment_plan' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['order_again'] ) && ! empty( $cart_item_data ) ) {
			$wc_deposit_option       = ( isset( $cart_item_data['is_deposit'] ) && true === $cart_item_data['is_deposit'] ) ? 'yes' : 'no';
			$wc_deposit_payment_plan = isset( $cart_item_data['payment_plan'] ) ? absint( $cart_item_data['payment_plan'] ) : 0;
		}

		// Validate chosen plan.
		if ( ( 'yes' === $wc_deposit_option || WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) && 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $product_id ) ) {
			if ( ! in_array( $wc_deposit_payment_plan, WC_Deposits_Plans_Manager::get_plan_ids_for_product( $product_id ), true ) ) {
				wc_add_notice( __( 'Please select a valid payment plan', 'woocommerce-deposits' ), 'error' );
				return false;
			}
		}

		// Validate sold individually.
		$product = wc_get_product( $product_id );

		if ( $product->is_sold_individually() ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( $cart_item['product_id'] === $product_id ) {
					/* translators: %s: product name */
					$message = sprintf( __( 'You cannot add another "%s" to your cart.', 'woocommerce' ), $product->get_name() );
					wc_add_notice( $message, 'error' );
					return false;
				}
			}
		}

		return $passed;
	}

	/**
	 * Add posted data to the cart item.
	 *
	 * @param array $cart_item_meta Cart Item meta.
	 * @param int   $product_id Product ID.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $cart_item_meta;
		}

		$wc_deposit_option       = self::get_global_param( 'wc_deposit_option' );
		$wc_deposit_payment_plan = self::get_global_param( 'wc_deposit_payment_plan' );

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
	 * Add deposits related data to cart item data.
	 *
	 * Function adds payment_plan and is_deposit data to cart item.
	 *
	 * @param array                 $cart_item_data Cart Item Data.
	 * @param WC_Order_Item_Product $item  Cart     Item.
	 * @param WC_Order              $order          Order.
	 * @return array
	 */
	public function order_again_cart_item_data( $cart_item_data, $item, $order ) {
		$product_id = $item->get_product_id();
		if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
			return $cart_item_data;
		}

		// Add deposits data to cart item if order again behavior is enabled OR deposits forced on product.
		if ( 'yes' === get_option( 'wc_deposits_order_again_behaviour', 'no' ) || WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) {
			$is_deposit   = $item->get_meta( '_is_deposit' );
			$payment_plan = absint( $item->get_meta( '_payment_plan' ) );

			if ( 'yes' === $is_deposit || WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) {
				if ( 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $product_id ) ) {
					if ( ! in_array( $payment_plan, WC_Deposits_Plans_Manager::get_plan_ids_for_product( $product_id ), true ) ) {
						return $cart_item_data;
					}
					$cart_item_data['payment_plan'] = $payment_plan;
				} else {
					$cart_item_data['payment_plan'] = 0;
				}
				$cart_item_data['is_deposit'] = true;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Apply deposit information in the cart (if any deposit item)
	 * This function is used only when wocommerce-memberships plugin is active.
	 *
	 * @return void
	 */
	public function apply_deposit_info_to_cart() {
		$cart = WC()->cart;
		if ( ! is_null( $cart ) ) {
			$this->get_cart_from_session( $cart );
		}
	}

	/**
	 * Runs though all items in the cart applying any deposit information.
	 * Needs to run on the cart_loaded_from_session hook so it runs after the cart has been fully loaded.
	 *
	 * @param WC_Cart $cart WooCommerce cart.
	 */
	public function get_cart_from_session( $cart ) {
		if ( count( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
				$result                                = $this->get_cart_item_from_session( $cart_item, $cart_item, $cart_item_key );
				$cart->cart_contents[ $cart_item_key ] = $result;
			}
		}
	}

	/**
	 * Get data from the session and add to the cart item's meta.
	 *
	 * @param array $cart_item Cart Item.
	 * @param mixed $values Cart Item values.
	 * @param mixed $cart_item_key Cart Item key.
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
	 * @param mixed $cart_item Cart item.
	 * @return array cart item
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$deposit_amount = WC_Deposits_Product_Manager::get_deposit_amount( $cart_item['data'], ! empty( $cart_item['payment_plan'] ) ? $cart_item['payment_plan'] : 0, 'order' );

			if ( false !== $deposit_amount ) {
				$cart_item['deposit_amount'] = $deposit_amount;

				// Bookings support.
				if ( isset( $cart_item['booking']['_persons'] ) && 'yes' === WC_Deposits_Product_Meta::get_meta( $cart_item['data']->get_id(), '_wc_deposit_multiple_cost_by_booking_persons' ) ) {
					$cart_item['deposit_amount'] = $cart_item['deposit_amount'] * absint( is_array( $cart_item['booking']['_persons'] ) ? array_sum( $cart_item['booking']['_persons'] ) : $cart_item['booking']['_persons'] );
				}

				// Work out %.
				if ( ! empty( $cart_item['payment_plan'] ) ) {
					$plan                     = WC_Deposits_Plans_Manager::get_plan( $cart_item['payment_plan'] );
					$total_percent            = $plan->get_total_percent();
					$cart_item['full_amount'] = ( $cart_item['data']->get_price() / 100 ) * $total_percent;
					$cart_item['data']->set_price( ( $cart_item['data']->get_price( 'edit' ) / 100 ) * $total_percent );
				} else {
					$cart_item['full_amount'] = $cart_item['data']->get_price();
				}
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
		WC()->session->set( 'deposits_present_discounts', array() );
		WC()->session->set( 'deposits_discount_tax', array() );
	}

	/**
	 * Control how coupons apply to products including a deposit or payment plan
	 * Filters woocommerce_coupon_get_discount_amount (WC_Coupons get_discount_amount)
	 *
	 * @since 1.3.7
	 *
	 * @param float      $discount Discount.
	 * @param float      $discounting_amount Amount the coupon is being applied to.
	 * @param array|null $cart_item Cart item being discounted if applicable.
	 * @param boolean    $single True if discounting a single qty item, false if its the line (always true in core).
	 * @param WC_Coupon  $coupon Coupon.
	 *
	 * @return float Amount this coupon has discounted
	 */
	public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( ! empty( $cart_item['is_deposit'] ) && 0 < intval( $discount ) ) {
			$coupon_type        = $coupon->get_discount_type(); // fixed_cart or fixed_product or percent or percent_product.
			$coupon_id          = $coupon->get_id();
			$discounting_amount = $cart_item['deposit_amount'] * $cart_item['quantity'];
			$deposit_type       = WC_Deposits_Product_Manager::get_deposit_type( $cart_item['product_id'] ); // fixed or percent or plan.
			$item_tax           = 0;
			$tax_inclusive      = 'yes' === get_option( 'woocommerce_prices_include_tax' );

			// Calculate tax for coupon will be used for adjustment of totals.
			if ( wc_tax_enabled() && 'taxable' === $cart_item['data']->get_tax_status() ) {
				$tax_rates = WC_Tax::get_rates( $cart_item['data']->get_tax_class(), WC()->cart->get_customer() );
				$item_tax  = array_sum( WC_Tax::calc_tax( $discount, $tax_rates, $tax_inclusive ) );
			}

			$full_amount    = floatval( $cart_item['full_amount'] );
			$deposit_amount = floatval( $cart_item['deposit_amount'] );
			// Calculate proportion due now, avoiding (unlikely) division by zero.
			if ( $full_amount > 0 ) {
				$present_proportion = $deposit_amount / $full_amount;
			} else {
				$present_proportion = 1.0;
			}

			// Initialize default conditions.
			$discount_pre_tax         = $tax_inclusive ? $discount - $item_tax : $discount;
			$present_discount_tax     = $tax_inclusive ? round( $item_tax * $present_proportion, 2 ) : 0;
			$present_discount_amount  = floatval( $discount_pre_tax );
			$deferred_discount_amount = 0.0;

			// For fixed coupons (fixed_cart or fixed_product).
			// For products with payment plans, discount a proportional amount of the fixed discount now, the rest defer for later.
			// For products with fixed deposits, defer the entire fixed discount.
			// For products with percentage based deposits, defer the entire fixed discount.
			if ( in_array( $coupon_type, array( 'fixed_cart', 'fixed_product' ), true ) ) {
				if ( 'plan' === $deposit_type ) {
					// Present discount amount is always for quantity 1.
					// Deferred discount amount is always for the line quantity.
					$present_discount_amount  = round( floor( $discount_pre_tax * $present_proportion * 100 ) / 100, 2 );
					$deferred_discount_amount = round( $discount * ( 1 - $present_proportion ), 2 );
				} elseif ( in_array( $deposit_type, array( 'percent', 'fixed' ), true ) ) {
					$present_discount_amount  = 0;
					$present_discount_tax     = 0;
					$deferred_discount_amount = round( $discount, 2 ); // total for (line) quantity, not just unit.
				}
			}

			// For percentage based coupons (percent or percent_product).
			// For products with payment plans, pass through the provided discount AND scale and defer it for later.
			// For products with fixed deposits, defer the entire discount.
			// For products with percentage based deposits, pass through the provided discount AND scale and defer it for later.
			if ( in_array( $coupon_type, array( 'percent', 'percent_product' ), true ) ) {
				$full_amount    = floatval( $cart_item['full_amount'] );
				$deposit_amount = floatval( $cart_item['deposit_amount'] );

				if ( in_array( $deposit_type, array( 'plan', 'percent' ), true ) ) {
					// Applies discount toward future amounts to ensure complete discount is not lost,
					// Deferred discount amount is always for the line quantity.
					$present_discount_amount  = round( floor( $discount_pre_tax * $present_proportion * 100 ) / 100, 2 );
					$deferred_discount_amount = round( $discount * ( 1 - $present_proportion ), 2 );
				} elseif ( 'fixed' === $deposit_type ) {
					// Then scale and defer the entire discount.
					$deferred_discount_amount = min( $full_amount - $deposit_amount, $discount );
					$present_discount_amount  = min( $discounting_amount, $discount - $deferred_discount_amount );
					$present_discount_tax     = ( $present_discount_amount / $deferred_discount_amount ) * $item_tax;
				}
			}

			$search_key         = self::generate_cart_id( $cart_item );
			$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts  = WC()->session->get( 'deposits_present_discounts', array() );
			$discount_tax       = WC()->session->get( 'deposits_discount_tax', array() );

			// Save the discount to be applied toward the future amount due.
			if ( array_key_exists( $search_key, $deferred_discounts ) && array_key_exists( $coupon_id, $deferred_discounts[ $search_key ] ) ) {
				$deferred_discounts[ $search_key ][ $coupon_id ] += $deferred_discount_amount;
			} else {
				$deferred_discounts[ $search_key ][ $coupon_id ] = $deferred_discount_amount;
			}

			// If future payments amount are less than 0 (i.e. the coupon has
			// higher discount than the value of the payment), apply the
			// remaining of the coupon amount to the present amount and decrease the future part.
			$future_payment_amount = $this->get_future_payments_amount_no_tax( $cart_item ) - $deferred_discount_amount;
			if ( $future_payment_amount < 0 ) {
				$deferred_discount_amount                        += $future_payment_amount;
				$deferred_discounts[ $search_key ][ $coupon_id ] += $future_payment_amount;
				$present_discount_amount                         += absint( $future_payment_amount );
				$present_discount_amount                          = min( $present_discount_amount, $discounting_amount );
			}

			if ( array_key_exists( $search_key, $present_discounts ) && array_key_exists( $coupon_id, $present_discounts[ $search_key ] ) ) {
				$present_discounts[ $search_key ][ $coupon_id ] += $present_discount_amount;
			} else {
				$present_discounts[ $search_key ][ $coupon_id ] = $present_discount_amount;
			}

			$used_tax = ( $present_discount_amount + $deferred_discount_amount ) / $discount * $item_tax;
			if ( array_key_exists( $search_key, $discount_tax ) && array_key_exists( $coupon_id, $discount_tax[ $search_key ] ) ) {
				$discount_tax[ $search_key ][ $coupon_id ] += $used_tax;
			} else {
				$discount_tax[ $search_key ][ $coupon_id ] = $used_tax;
			}

			WC()->session->set( 'deposits_deferred_discounts', $deferred_discounts );
			WC()->session->set( 'deposits_present_discounts', $present_discounts );
			WC()->session->set( 'deposits_discount_tax', $discount_tax );

			// Return the total used discount.
			return $present_discount_amount + $present_discount_tax + $deferred_discount_amount;
		}

		// Otherwise, just pass through the original amount.
		return $discount;
	}

	/**
	 * Returns stored discount amount
	 *
	 * @since 1.2.0
	 *
	 * @param string         $type Discount type.
	 * @param WC_Coupon|null $coupon Coupon.
	 * @param array          $cart_item Cart item.
	 * @return float
	 */
	public static function get_stored_discount_amount( $type, $coupon = null, $cart_item = null ) {
		$deferred_discount_amount = 0;

		if ( ! is_object( WC()->session ) ) {
			return $deferred_discount_amount;
		}

		if ( ! is_null( $coupon ) ) {
			$coupon_id = $coupon->get_id();
		}

		if ( ! is_null( $cart_item ) ) {
			$search_key = self::generate_cart_id( $cart_item );
		}

		$get_all_coupons = is_null( $coupon );
		$get_all_items   = is_null( $cart_item );

		$deferred_discounts = WC()->session->get( $type, array() );
		foreach ( $deferred_discounts as $item_key => $item_discounts ) {
			if ( $get_all_items || $search_key === $item_key ) {
				foreach ( $item_discounts as $id => $discount ) {
					if ( $get_all_coupons || $coupon_id === $id ) {
						$deferred_discount_amount += $discount;
					}
				}
			}
		}
		return $deferred_discount_amount;
	}

	/**
	 * Calculates new cart totals taking into account credit amount, deposit and deferred discount
	 *
	 * @param WC_Cart $cart Cart.
	 */
	public function adjust_cart_totals( $cart ) {
		if ( ! $this->has_deposit( $cart ) ) {
			return;
		}

		$total_deferred_discount_tax = 0;

		if ( wc_tax_enabled() && ! wc_prices_include_tax() ) {
			$tax                         = $this->calculate_deferred_and_present_discount_tax();
			$total_deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals() );
		}

		$cart->set_subtotal( $cart->get_subtotal() - ( $this->get_deposit_remaining_amount() + $this->get_credit_amount() ) );
		$cart->set_total( $cart->get_total( 'total' ) - ( $this->get_deposit_remaining_amount( null, true ) + $this->get_credit_amount( null, true ) ) + self::get_deferred_discount_amount() + $total_deferred_discount_tax );
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
	 * @param WC_Cart $cart Cart.
	 * @return array List of all deferred tax rates.
	 */
	public function calculate_deferred_taxes_from_cart( $cart = null ) {
		$deferred_taxes = array();
		$cart           = empty( $cart ) && isset( WC()->cart ) ? WC()->cart : $cart;

		if ( $cart ) {
			foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
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
		}
		return $deferred_taxes;
	}

	/**
	 * Calculate deferred and present discount tax
	 *
	 * @param string $_cart_item_key Cart item key.
	 * @return array
	 */
	public static function calculate_deferred_and_present_discount_tax( $_cart_item_key = null ) {
		$get_all_items = is_null( $_cart_item_key );
		$tax           = array(
			'present'  => 0,
			'deferred' => 0,
		);

		$deferred_discounts = array();
		$present_discounts  = array();
		$discounts_tax      = array();

		if ( is_object( WC()->session ) ) {
			$deferred_discounts = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts  = WC()->session->get( 'deposits_present_discounts', array() );
			$discounts_tax      = WC()->session->get( 'deposits_discount_tax', array() );
		}

		foreach ( $discounts_tax as $cart_item_key => $coupons ) {
			foreach ( $coupons as $coupon_id => $discount_tax ) {
				if ( $get_all_items || $cart_item_key === $_cart_item_key ) {
					$present_discount  = $present_discounts[ $cart_item_key ][ $coupon_id ];
					$deferred_discount = $deferred_discounts[ $cart_item_key ][ $coupon_id ];
					$proportion        = $present_discount / ( $present_discount + $deferred_discount );
					$tax['present']   += $discount_tax * $proportion;
					$tax['deferred']  += $discount_tax * ( 1 - $proportion );
				}
			}
		}

		return $tax;
	}

	/**
	 * Calculates new subtotal taking into account credit amount and deposit
	 *
	 * @return float
	 */
	public function adjust_cart_subtotal() {
		return WC()->cart->get_subtotal() - ( $this->get_deposit_remaining_amount() + $this->get_credit_amount() );
	}

	/**
	 * Calculates the sum of all deferred discounts in the cart for all items
	 * or the sum of all discounts for one coupon
	 * Totals are for (line) quantity, not just unit
	 *
	 * @since 1.2.0
	 * @param WC_Coupon/null $coupon Coupon.
	 * @param array          $cart_item Cart item.
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
	 * @param WC_Coupon/null $coupon Coupon.
	 * @param array          $cart_item Cart item.
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
	 * @param WC_Coupon/null $coupon Coupon.
	 * @param array          $cart_item Cart item.
	 * @return float
	 */
	public static function get_discount_tax( $coupon = null, $cart_item = null ) {
		return self::get_stored_discount_amount( 'deposits_discount_tax', $coupon, $cart_item );
	}

	/**
	 * Put meta data into format which can be displayed.
	 *
	 * @param array $other_data Current item data.
	 * @param array $cart_item Cart item.
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
	 *
	 * @param string $output Price HTML.
	 * @param array  $cart_item Cart item.
	 * @param string $cart_item_key Cart item key.
	 * @return string
	 */
	public function display_item_price( $output, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item['full_amount'] ) ) {
			return $output;
		}
		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$_product = $cart_item['data'];
			if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
				$amount = $this->get_price_excluding_tax(
					$_product,
					array(
						'qty'   => 1,
						'price' => $cart_item['full_amount'],
					)
				);
			} else {
				$amount = $this->get_price_including_tax(
					$_product,
					array(
						'qty'   => 1,
						'price' => $cart_item['full_amount'],
					)
				);
			}
			$output = wc_price( $amount );
		}
		return $output;
	}

	/**
	 * Adjust the subtotal display in the cart.
	 *
	 * @param string $output Subtotal HTML.
	 * @param array  $cart_item Cart item.
	 * @param string $cart_item_key Cart item key.
	 * @return string
	 */
	public function display_item_subtotal( $output, $cart_item, $cart_item_key ) {
		if ( ! isset( $cart_item['full_amount'] ) ) {
			return $output;
		}

		if ( ! empty( $cart_item['is_deposit'] ) ) {
			$_product    = $cart_item['data'];
			$quantity    = $cart_item['quantity'];
			$full_amount = $cart_item['full_amount'];
			// We need to apply this filter to the deposit amount, as it may have been affected by Memberships.
			$deposit_amount = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $_product );

			if ( 'excl' === WC()->cart->get_tax_price_display_mode() ) {
				$full_amount    = $this->get_price_excluding_tax(
					$_product,
					array(
						'qty'   => $quantity,
						'price' => $full_amount,
					)
				);
				$deposit_amount = $this->get_price_excluding_tax(
					$_product,
					array(
						'qty'   => $quantity,
						'price' => $deposit_amount,
					)
				);
				$output         = wc_price( $deposit_amount );

				/**
				 * Optionally add (ex. tax) suffix.
				 *
				 * @see WC_Cart::get_product_subtotal
				 */
				if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
					$output .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			} else {
				$full_amount    = $this->get_price_including_tax(
					$_product,
					array(
						'qty'   => $quantity,
						'price' => $full_amount,
					)
				);
				$deposit_amount = $this->get_price_including_tax(
					$_product,
					array(
						'qty'   => $quantity,
						'price' => $deposit_amount,
					)
				);
				$output         = wc_price( $deposit_amount );

				/**
				 * Optionally add (incl. tax) suffix.
				 *
				 * @see WC_Cart::get_product_subtotal
				 */
				if ( ! wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
					$output .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}

			// Adding this to be compatible with WC3.2 changes. Allow further modification by other plugins.
			$output = apply_filters( 'woocommerce_cart_product_price', $output, $_product );

			if ( ! empty( $cart_item['payment_plan'] ) ) {
				$plan    = new WC_Deposits_Plan( $cart_item['payment_plan'] );
				$output .= '<br/><small>' . $plan->get_formatted_schedule( $full_amount ) . '</small>';
			} else {
				/* translators: item subtotal */
				$output .= '<br/><small>' . sprintf( __( '%s payable in total', 'woocommerce-deposits' ), wc_price( $full_amount ) ) . '</small>';
			}
		}

		return $output;
	}

	/**
	 * Before the main total.
	 */
	public function display_cart_totals_before() {
		if ( $this->get_future_payments_amount() > 0 ) {
			ob_start();
			$this->buffered = true;
		}
	}

	/**
	 * Print order total html including inc tax if needed
	 */
	private function cart_totals_order_total_html() {
		$value = '<strong>' . WC()->cart->get_total() . '</strong> ';

		$value .= $this->cart_totals_order_tax_formated_output();

		echo apply_filters( 'woocommerce_cart_totals_order_total_html', $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get order total html including inc tax if needed
	 *
	 * @return string
	 */
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
	 * @param array   $taxes The current tax totals.
	 * @param WC_Cart $cart The current cart.
	 * @return array List of adjusted taxes.
	 */
	public function cart_totals_order_taxes( $taxes, $cart = null ) {
		if ( ! $cart || ! $this->has_deposit( $cart ) ) {
			return $taxes;
		}

		$deferred_tax = $this->calculate_deferred_taxes_from_cart( $cart );

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
	 * Display the correct tax when the "Display Tax Totals" setting is set to "As a single total".
	 *
	 * @param string $total_html  Tax Total HTML.
	 * @return string Tax Total HTML.
	 */
	public function cart_totals_taxes_total_html( $total_html ) {
		if ( ! WC()->cart || ! $this->has_deposit( WC()->cart ) ) {
			return $total_html;
		}

		$cart_tax_totals = WC()->cart->get_tax_totals();
		$deferred_tax    = $this->calculate_deferred_taxes_from_cart( WC()->cart );
		if ( empty( $deferred_tax ) || empty( $cart_tax_totals ) ) {
			// No modifications required.
			return $total_html;
		}

		$tax = WC()->cart->get_taxes_total() - array_sum( $this->calculate_deferred_taxes_from_cart() );
		return wc_price( $tax );
	}

	/**
	 * After the main total.
	 */
	public function display_cart_totals_after() {
		$future_payment_amount = self::get_future_payments_amount_with_discount();
		$is_tax_included       = wc_tax_enabled() && 'excl' !== WC()->cart->get_tax_price_display_mode();
		$tax_message           = $is_tax_included ? __( '(includes tax)', 'woocommerce-deposits' ) : __( '(excludes tax)', 'woocommerce-deposits' );
		$tax_element           = wc_tax_enabled() && ! empty( WC()->cart->get_tax_totals() ) ? ' <small class="tax_label">' . $tax_message . '</small>' : '';
		$deferred_discount_tax = 0;
		$tax                   = $this->calculate_deferred_and_present_discount_tax();

		if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
			if ( $is_tax_included ) {
				$deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals() );
			}
		} else {
			if ( ! $is_tax_included ) {
				$deferred_discount_tax = -round( $tax['deferred'], wc_get_price_decimals() );
			}
		}

		$deferred_discount_amount = self::get_deferred_discount_amount();

		if ( ! empty( $this->buffered ) ) {
			ob_end_clean();
			$this->buffered = false;
		}

		if ( 0 >= $future_payment_amount && $deferred_discount_amount <= 0 ) {
			return;
		}
		?>
		<tr class="order-total">
			<th><?php esc_html_e( 'Due Today', 'woocommerce-deposits' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Due Today', 'woocommerce-deposits' ); ?>"><?php $this->cart_totals_order_total_html(); ?></td>
		</tr>
		<?php
		if ( $deferred_discount_amount > 0 ) {
			?>
			<tr class="order-total">
				<th><?php esc_html_e( 'Discount Applied Toward Future Payments', 'woocommerce-deposits' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Discount Applied Toward Future Payments', 'woocommerce-deposits' ); ?>"><?php echo wc_price( -$deferred_discount_amount - $deferred_discount_tax ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
			<?php
		}
		?>
		<tr class="order-total">
		<th><?php esc_html_e( 'Future Payments', 'woocommerce-deposits' ); ?></th>
		<td data-title="<?php esc_attr_e( 'Future Payments', 'woocommerce-deposits' ); ?>"><?php echo wc_price( $future_payment_amount ); ?><?php echo $tax_element; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<?php
	}

	/**
	 * Store cart info inside new orders.
	 *
	 * @param WC_Order_Item_Product $item Order item.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values Values.
	 */
	public function add_order_item_meta( $item, $cart_item_key, $values ) {
		$cart      = WC()->cart->get_cart();
		$cart_item = $cart[ $cart_item_key ];

		if ( ! empty( $cart_item['is_deposit'] ) ) {
			// First, calculate the full amount (before deposits/payments)
			// Note that this is for the entire line quantity, not just a unit.
			$full_amount_including_tax = $this->get_price_including_tax_no_round(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $cart_item['full_amount'],
				)
			);
			$full_amount_excluding_tax = $this->get_price_excluding_tax_no_round(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $cart_item['full_amount'],
				)
			);

			// Next, for fixed or percentage based deposits, calculate the initial deposit, prior to tax, regardless of discounts
			// so that WC_Deposits_Order_Manager::order_action_handler invoice_remaining_balance can calculate the correct amount to charge.
			$deposit_amount               = apply_filters( 'woocommerce_deposits_get_deposit_amount', $cart_item['deposit_amount'], $cart_item['data'] );
			$deposit_amount_excluding_tax = $this->get_price_excluding_tax_no_round(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $deposit_amount,
				)
			);
			$deposit_amount_including_tax = $this->get_price_including_tax_no_round(
				$cart_item['data'],
				array(
					'qty'   => $cart_item['quantity'],
					'price' => $deposit_amount,
				)
			);

			// We cannot use the cart_item_key provided since it differs from the one we use to store the discount.
			$search_key = self::generate_cart_id( $cart_item );

			// Retrieve present and deferred discounts for the item.
			$deferred_discounts       = WC()->session->get( 'deposits_deferred_discounts', array() );
			$present_discounts        = WC()->session->get( 'deposits_present_discounts', array() );
			$deferred_discount_amount = isset( $deferred_discounts[ $search_key ] ) ? array_sum( $deferred_discounts[ $search_key ] ) : 0;
			$present_discount_amount  = isset( $present_discounts[ $search_key ] ) ? array_sum( $present_discounts[ $search_key ] ) : 0;

			// Adjust values to represent proper total and subtotal after applied discounts.
			$discount_tax                 = $this->calculate_deferred_and_present_discount_tax( $search_key );
			$deferred_discount_tax_amount = $discount_tax['deferred'];

			if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
				$deferred_discount_amount_excluding_tax = $deferred_discount_amount;
				$deferred_discount_amount              += $deferred_discount_tax_amount;
			} else {
				$deferred_discount_amount_excluding_tax = $deferred_discount_amount - $deferred_discount_tax_amount;
			}

			// Avoid divide by zero errors. See https://github.com/woocommerce/woocommerce-deposits/issues/483.
			if ( $full_amount_including_tax > 0 ) {
				$deposit_ratio = $deposit_amount_including_tax / $full_amount_including_tax;
			} else {
				$deposit_ratio = 1;
			}
			$item->set_total( ( $values['line_subtotal'] * $deposit_ratio ) - $present_discount_amount );
			$item->set_subtotal( $values['line_subtotal'] * $deposit_ratio );
			$taxes             = $item->get_taxes();
			$scale             = function ( $tax ) use ( $deposit_ratio ) {
				return $tax * $deposit_ratio;
			};
			$taxes['subtotal'] = array_map( $scale, $taxes['subtotal'] );
			$taxes['total']    = array_map( $scale, $taxes['total'] );
			$item->set_taxes( $taxes );

			$item->add_meta_data( '_is_deposit', 'yes' );
			$item->add_meta_data( '_deposit_full_amount', $full_amount_including_tax ); // line quantity, not just a unit.
			$item->add_meta_data( '_deposit_full_amount_ex_tax', $full_amount_excluding_tax );
			$item->add_meta_data( '_deposit_deposit_amount_ex_tax', $deposit_amount_excluding_tax );
			if ( $deferred_discount_amount > 0 ) {
				$item->add_meta_data( '_deposit_deferred_discount', $deferred_discount_amount ); // line quantity, not just a unit.
				$item->add_meta_data( '_deposit_deferred_discount_ex_tax', $deferred_discount_amount_excluding_tax ); // line quantity, not just a unit.
			}

			if ( ! empty( $cart_item['payment_plan'] ) ) {
				$item->add_meta_data( '_payment_plan', $cart_item['payment_plan'] );
			}
		}
	}

	/**
	 * Disable gateways when using deposits.
	 *
	 * @param  array $gateways Gateways.
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
	 *
	 * @param string $text Add to cart text.
	 * @return string
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
	 * @param string     $link HTML link.
	 * @param WC_Product $product Product.
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
	 * @since 1.2
	 * @param WC_Product $product Product.
	 * @param array      $args Arguments.
	 * @return float
	 */
	private function get_price_including_tax( $product, $args ) {
		return wc_get_price_including_tax( $product, $args );
	}

	/**
	 * Returns price excluding tax (not rounded)
	 *
	 * @param WC_Product $product Product.
	 * @param array      $args Arguments.
	 * @return float
	 */
	private function get_price_excluding_tax_no_round( $product, $args ) {
		$price = '' !== $args['price'] ? max( 0.0, (float) $args['price'] ) : $product->get_price();
		$qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;

		if ( $product->is_taxable() && wc_prices_include_tax() ) {
			$tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
			$taxes     = WC_Tax::calc_tax( $price * $qty, $tax_rates, true );
			$price     = $price * $qty - array_sum( $taxes );
		} else {
			$price = $price * $qty;
		}

		return apply_filters( 'woocommerce_get_price_excluding_tax', $price, $qty, $product );
	}

	/**
	 * Returns price including tax (not rounded)
	 *
	 * @param WC_Product $product Product.
	 * @param array      $args Arguments.
	 * @return float
	 */
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
	 * @since 1.2
	 * @param WC_Product $product Product.
	 * @param array      $args Arguments.
	 * @return float
	 */
	private function get_price_excluding_tax( $product, $args ) {
		return wc_get_price_excluding_tax( $product, $args );
	}

	/**
	 * Fix deposits position on variable products - show them after a single variation description
	 * or out of stock message.
	 */
	public function reposition_display_for_variable_product() {
		remove_action( 'woocommerce_before_add_to_cart_button', array( $this, 'deposits_form_output' ), 99 );
		add_action( 'woocommerce_single_variation', array( $this, 'deposits_form_output' ), 16 );
	}

	/* Deprecated */

	/**
	 * Calculates new total taking into account credit amount, deposit and deferred discount
	 *
	 * @deprecated 1.5.7
	 * @param float $total Original cart total.
	 * @return float
	 */
	public function adjust_cart_total( $total ) {
		_deprecated_function( 'WC_Deposits_Cart_Manager::adjust_cart_total', '1.5.7', 'Use adjust_cart_totals instead.' );

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
	 * Calculates new cart totals after TaxJar totals are applied on the cart.
	 *
	 * @param WC_Cart $cart cart.
	 */
	public function adjust_cart_totals_after_taxjar( $cart ) {
		if ( ! $this->has_deposit( $cart ) ) {
			return;
		}

		$total_deferred_discount_tax = 0;

		if ( wc_tax_enabled() && ! wc_prices_include_tax() ) {
			$tax                         = $this->calculate_deferred_and_present_discount_tax();
			$total_deferred_discount_tax = round( $tax['deferred'], wc_get_price_decimals() );
		}

		$cart->set_total( $cart->get_total( 'total' ) - ( $this->get_deposit_remaining_amount( null, true ) + $this->get_credit_amount( null, true ) ) + self::get_deferred_discount_amount() + $total_deferred_discount_tax );
	}
}

WC_Deposits_Cart_Manager::get_instance();
