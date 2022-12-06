<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Cart Class.
 *
 * @class   YWSBS_Subscription_Cart
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Cart' ) ) {

	/**
	 * Class YWSBS_Subscription_Cart
	 */
	class YWSBS_Subscription_Cart {
		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Cart
		 */
		protected static $instance;

		/**
		 * Temporary Cart.
		 *
		 * @var WC_Cart
		 */
		private $actual_cart;

		/**
		 * Temporary variable to avoid loop.
		 *
		 * @var WC_Cart
		 */
		protected $recurring_calculation = false;

		/**
		 * List of not shippable products
		 *
		 * @var array
		 */
		protected $list_of_not_shippable = array();

		/**
		 * List of not shippable products
		 *
		 * @var array
		 */
		protected $clear_shipping = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Coupons
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// change prices in calculation totals to add the fee amount.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'add_change_prices_filter' ), 10 );
			add_action( 'woocommerce_calculate_totals', array( $this, 'remove_change_prices_filter' ), 10 );
			add_action( 'woocommerce_after_calculate_totals', array( $this, 'remove_change_prices_filter' ), 10 );

			// Change prices and totals in cart.
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'change_subtotal_price_in_cart_html' ), 99, 3 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_price_in_cart_html' ), 99, 3 );
			add_filter( 'woocommerce_cart_needs_payment', array( $this, 'cart_needs_payment' ), 10, 2 );
			add_filter( 'ywsbs_signup_fee_in_cart', array( $this, 'change_signup_fee_in_cart' ), 10, 2 );

			// Cart and checkout validation.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_item_validate' ), 10, 4 );
			add_action( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways' ), 100 );

			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'set_subscription_meta_on_cart' ), 15, 4 );

			add_action( 'woocommerce_cart_totals_after_order_total', array( $this, 'cart_recurring_totals' ), 10 );
			add_action( 'woocommerce_review_order_after_order_total', array( $this, 'cart_recurring_totals' ), 10 );

			// remove the shipping for sync products not prorated.
			add_action( 'woocommerce_before_checkout_process', array( $this, 'sync_on_process_checkout' ), 200 );
			add_action( 'woocommerce_checkout_update_customer', array( $this, 'check_shipping_to_clear' ), 200 );

			add_filter( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 200, 2 );

		}

		/**
		 * Clear the shipping methods
		 */
		public function clear_shipping() {
			$this->clear_shipping = true;
			WC()->session->set( 'chosen_shipping_methods', array() );
		}

		/**
		 * Clear the shipping methods
		 */
		public function check_shipping_to_clear() {
			if ( $this->clear_shipping ) {
				WC()->session->set( 'chosen_shipping_methods', array() );
			}
		}

		/**
		 * During the checkout process remove the shipping from order.
		 */
		public function sync_on_process_checkout() {
			$ywsbs_sync_on_process_checkout = WC()->session->get( 'ywsbs_sync_on_process_checkout', false );

			if ( $ywsbs_sync_on_process_checkout ) {
				add_filter( 'woocommerce_after_checkout_validation', array( $this, 'clear_shipping' ), 200 );

			}
		}

		/**
		 * Remove temporary the shipping calculation for the products that are syncronized.
		 *
		 * @param   WC_Cart  $cart  Cart.
		 */
		public function before_calculate_totals( $cart ) {
			if ( ! self::cart_has_subscriptions() ) {
				return;
			}
			if ( WC()->session->get( 'reload_checkout', false ) === true ) {
				add_filter( 'woocommerce_after_checkout_validation', array( $this, 'clear_shipping' ), 200 );
			}
			WC()->session->set( 'ywsbs_sync_on_process_checkout', false );
			WC()->session->set( 'ywsbs_shipping_methods', WC()->session->get( 'chosen_shipping_methods', array() ) );

			$add_filter     = true; // check if there are only synch subscription on cart.
			$prorate_option = get_option( 'ywsbs_sync_first_payment', 'no' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$apply_shipping_on_sync =
					'no' === $prorate_option && ywsbs_is_subscription_product( $cart_item['data'] ) && $cart_item['data']->needs_shipping() &&
					(
						isset( $cart_item['ywsbs-subscription-info']['sync'] ) && 1 === (int) $cart_item['ywsbs-subscription-info']['sync'] &&
						( empty( $cart_item['data']->get_price() ) || ( '' !== $cart_item['ywsbs-subscription-info']['fee'] && 0 == ( $cart_item['data']->get_price() - apply_filters( 'ywsbs_product_fee', $cart_item['ywsbs-subscription-info']['fee'], $cart_item['data'] ) ) ) ) //phpcs:ignore
					);

				if ( apply_filters( 'ywsbs_apply_shipping_on_synch_subscription', $apply_shipping_on_sync, $cart_item, $cart_item_key ) ) {
					$product_id = $cart_item['data']->get_id();
					! in_array( $product_id, $this->list_of_not_shippable, true ) && array_push( $this->list_of_not_shippable, $cart_item['data']->get_id() );
				} else {
					$add_filter = false;
				}
			}

			if ( ! empty( $this->list_of_not_shippable ) ) {
				if ( $add_filter ) {
					add_filter( 'woocommerce_calculated_total', array( $this, 'remove_shipping_cost_from_calculate_totals' ), 200, 2 );
					add_filter( 'woocommerce_cart_tax_totals', array( $this, 'remove_tax_shipping_cost_from_calculate_totals' ), 200, 2 );
					WC()->session->set( 'ywsbs_sync_on_process_checkout', true );
				} else {
					add_filter( 'woocommerce_product_needs_shipping', array( $this, 'maybe_not_shippable' ), 100, 2 );
					add_filter( 'woocommerce_cart_needs_shipping_address', '__return_true' );
					WC()->session->set( 'ywsbs_sync_on_process_checkout', false );
				}
			}

		}

		/**
		 * Remove the shipping amount from cart if there'are only synch subscription on cart with price 0.
		 *
		 * @param   float    $total  Cart total.
		 * @param   WC_Cart  $cart   Cart.
		 *
		 * @return mixed
		 */
		public function remove_shipping_cost_from_calculate_totals( $total, $cart ) {
			$totals = $cart->get_totals();
			$total  -= ( $totals['shipping_total'] + $totals['shipping_tax'] );

			return $total;
		}

		/**
		 * Remove the shipping amount from cart if there'are only synch subscription on cart with price 0.
		 *
		 * @param   array    $total  Cart total.
		 * @param   WC_Cart  $cart   Cart.
		 *
		 * @return mixed
		 */
		public function remove_tax_shipping_cost_from_calculate_totals( $total, $cart ) {

			foreach ( $cart->get_shipping_taxes() as $key => $value ) {
				foreach ( $total as $k => $t ) {
					if ( $t->tax_rate_id === $key ) {
						$total[ $k ]->amount -= $value;

						if ( empty( $total[ $k ]->amount ) ) {
							unset( $total[ $k ] );
						} else {
							$total[ $k ]->formatted_amount = wc_price( $total[ $k ]->amount );
						}
					}
				}
			}

			return $total;

		}

		/**
		 * Return false for the product saved on list.
		 *
		 * @param   bool        $value    Value passed to filter.
		 * @param   WC_Product  $product  Product.
		 *
		 * @return bool
		 */
		public function maybe_not_shippable( $value, $product ) {
			if ( in_array( $product->get_id(), $this->list_of_not_shippable, true ) ) {
				return false;
			}

			return $value;
		}

		/**
		 * Add change prices filter.
		 *
		 * @since 1.4.6
		 */
		public function add_change_prices_filter() {
			add_filter( 'woocommerce_product_get_price', array( $this, 'change_prices_for_calculation' ), 100, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'change_prices_for_calculation' ), 100, 2 );
		}

		/**
		 * Remove the change price filter.
		 *
		 * @since 1.4.6
		 */
		public function remove_change_prices_filter() {
			remove_filter( 'woocommerce_product_get_price', array( $this, 'change_prices_for_calculation' ), 100 );
			remove_filter( 'woocommerce_product_variation_get_price', array( $this, 'change_prices_for_calculation' ), 100 );

		}

		/**
		 * Add additional cart item data to the subscription products.
		 *
		 * @param   array  $cart_item_data  Cart item data.
		 * @param   int    $product_id      Product id.
		 * @param   int    $variation_id    Variation id.
		 * @param   int    $quantity        Quantity.
		 *
		 * @return array
		 */
		public function set_subscription_meta_on_cart( $cart_item_data, $product_id, $variation_id, $quantity = 1 ) {
			$product_id = empty( $variation_id ) ? $product_id : $variation_id;
			if ( ! ywsbs_is_subscription_product( $product_id ) ) {
				return $cart_item_data;
			}

			$product                     = wc_get_product( $product_id );
			$cart_item_subscription_data = $this->get_subscription_meta_on_cart( $product );

			if ( $cart_item_subscription_data ) {
				$cart_item_data['ywsbs-subscription-info'] = $cart_item_subscription_data;
			}

			return $cart_item_data;
		}

		/**
		 * Get the subscription meta
		 *
		 * @param   WC_Product  $product  Product.
		 */
		public function get_subscription_meta_on_cart( $product ) {
			$cart_item_subscription_data = array();
			if ( $product ) {
				$cart_item_subscription_data = array(
					'recurring_price'       => $product->get_price( 'edit' ),
					'price_is_per'          => $product->get_meta( '_ywsbs_price_is_per' ),
					'price_time_option'     => $product->get_meta( '_ywsbs_price_time_option' ),
					'fee'                   => ywsbs_get_product_fee( $product, 'edit' ),
					'trial_per'             => ywsbs_get_product_trial( $product ),
					'trial_time_option'     => $product->get_meta( '_ywsbs_trial_time_option' ),
					'max_length'            => YWSBS_Subscription_Helper::get_subscription_product_max_length( $product ),
					'next_payment_due_date' => '',
				);
			}

			return apply_filters( 'ywsbs_subscription_meta_on_cart', $cart_item_subscription_data, $product );
		}


		/**
		 * Change price
		 *
		 * @param   float       $price    Price.
		 * @param   WC_Product  $product  WC_Product.
		 *
		 * @return mixed
		 */
		public function change_prices_for_calculation( $price, $product ) {

			// Integration with YITH WC Request a Quote.
			$is_raq = $product->get_meta( 'ywraq_product' );

			if ( ! ywsbs_is_subscription_product( $product->get_id() ) || $is_raq ) {
				return $price;
			}

			$signup_fee   = ywsbs_get_product_fee( $product );
			$trial_period = ywsbs_get_product_trial( $product );
			$cart_item    = array();

			if ( WC()->cart ) {
				$in_cart = false;
				foreach ( WC()->cart->get_cart() as $cart_item_element ) {
					$product_in_cart   = (int) $cart_item_element['product_id'];
					$variation_in_cart = (int) $cart_item_element['variation_id'];
					if ( $product->get_id() === $variation_in_cart || $product->get_id() === $product_in_cart ) {
						$in_cart   = true;
						$cart_item = $cart_item_element;
						break;
					}
				}

				if ( $in_cart ) {
					if ( isset( $cart_item['ywsbs-subscription-info'] ) ) {
						$signup_fee   = apply_filters( 'ywsbs_product_fee', $cart_item['ywsbs-subscription-info']['fee'], $cart_item['data'] );
						$trial_period = $cart_item['ywsbs-subscription-info']['trial_per'];
					}
				}
			}

			if ( ! empty( $trial_period ) ) {
				$price = 0;
			}

			if ( ! empty( $signup_fee ) ) {
				$price = floatval( $signup_fee ) + $price;
			}

			return $price;

		}

		/**
		 * Change subtotal html price on cart.
		 *
		 * @param   string  $price_html     Html Price.
		 * @param   array   $cart_item      Cart item.
		 * @param   string  $cart_item_key  Cart Item key.
		 *
		 * @return string
		 */
		public function change_subtotal_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ! ywsbs_is_subscription_product( $product_id ) || ! isset( $cart_item['data'] ) ) {
				return $price_html;
			}

			$product = $cart_item['data'];
			$price   = apply_filters( 'ywsbs_change_subtotal_price_in_cart_html', $cart_item['data']->get_price( 'edit' ), $cart_item['data'], $cart_item );

			$price_current = apply_filters( 'ywsbs_change_subtotal_price_current_in_cart_html', $product->get_price( 'edit' ), $product );

			$product->set_price( $price );
			$price_html = $this->change_general_price_html( $product, $cart_item['quantity'], false, $cart_item );

			$product->set_price( $price_current );

			return apply_filters( 'ywsbs_subscription_subtotal_html', $price_html, $cart_item['data'], $cart_item );
		}


		/**
		 * Return the subscription total amount of a product.
		 *
		 * @param   WC_Product  $product            Product.
		 * @param   int         $quantity           Quantity.
		 * @param   bool|array  $subscription_info  Subscription information.
		 *
		 * @return string
		 */
		public function get_formatted_subscription_total_amount( $product, $quantity, $subscription_info = false ) {

			$sbs_total_format = '';
			$max_length       = YWSBS_Subscription_Helper::get_subscription_product_max_length( $product );

			if ( $max_length && $max_length > 1 && 'yes' === get_option( 'ywsbs_subscription_total_amount', 'no' ) ) {

				$sbs_total_format         = get_option( 'ywsbs_total_subscription_length_text', esc_html_x( 'Subscription total for {{sub-time}}: {{sub-total}}', 'do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) );
				$max_length_text          = YWSBS_Subscription_Helper::get_subscription_max_length_formatted_for_price( $product );
				$total_subscription_price = YWSBS_Subscription_Helper::get_total_subscription_price( $product, $subscription_info );

				$total_subscription_price = wc_get_price_to_display(
					$product,
					array(
						'qty'   => $quantity,
						'price' => $total_subscription_price,
					)
				);
				$sbs_total_format         = str_replace( '{{sub-time}}', $max_length_text, $sbs_total_format );
				$sbs_total_format         = str_replace( '{{sub-total}}', wc_price( $total_subscription_price ), $sbs_total_format );

				if ( ! wc_prices_include_tax() ) {
					$sbs_total_format .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}

				$sbs_total_format = '<div class="ywsbs-subscription-total">' . $sbs_total_format . '</div>';

			}

			return apply_filters( 'ywsbs_checkout_subscription_total_amount', $sbs_total_format, $product, $quantity );
		}


		/**
		 * Return the subscription next billing date.
		 *
		 * @param   WC_Product  $product            Product.
		 * @param   bool|array  $subscription_info  Subscription information.
		 *
		 * @return string
		 */
		public function get_formatted_subscription_next_billing_date( $product, $subscription_info = false ) {

			$billing_date_text = '';

			if ( 'yes' === get_option( 'ywsbs_show_next_billing_date', 'no' ) ) {
				$billing_date = $subscription_info && isset( $subscription_info['next_payment_due_date'] ) && ! empty( $subscription_info['next_payment_due_date'] ) ? $subscription_info['next_payment_due_date'] : YWSBS_Subscription_Helper::get_billing_payment_due_date( $product );

				if ( $billing_date ) {
					$billing_date_text = empty( ywsbs_get_product_trial( $product ) ) ? get_option( 'ywsbs_show_next_billing_date_text', esc_html__( 'Next billing on:', 'yith-woocommerce-subscription' ) ) : get_option( 'ywsbs_show_next_billing_date_text_for_trial',
						esc_html__( 'First billing on:', 'yith-woocommerce-subscription' ) );
					$billing_date_text = '<div class="ywsbs-next-billing-date"><strong>' . $billing_date_text . '</strong> ' . date_i18n( wc_date_format(), $billing_date ) . '</div>';
				}
			}

			return apply_filters( 'ywsbs_checkout_subscription_next_billing_date', $billing_date_text, $product );
		}

		/**
		 * Change price HTML to the product
		 *
		 * @param   WC_Product  $product              WC_Product.
		 * @param   int         $quantity             Quantity.
		 * @param   bool        $show_complete_price  To show the complete price inside cart subtotal.
		 * @param   array       $cart_item            Cart item.
		 *
		 * @return string
		 * @since  1.2.0
		 */
		public function change_general_price_html( $product, $quantity = 1, $show_complete_price = false, $cart_item = null ) {

			if ( is_null( $cart_item ) ) {
				return $product->get_price_html();
			}

			$show_complete_price_on_substotal_cart = apply_filters( 'ywsbs_show_complete_price_on_substotal_cart', $show_complete_price );

			if ( isset( $cart_item['ywsbs-subscription-info'] ) ) {
				$subscription_info = $cart_item['ywsbs-subscription-info'];
			} else {
				$subscription_info = $this->get_subscription_meta_on_cart( $cart_item['data'] );
			}

			$price = wc_get_price_to_display(
				$product,
				array(
					'qty'   => $quantity,
					'price' => $product->get_price( 'edit' ),
				)
			);

			$price_html = '<div class="ywsbs-wrapper"><div class="ywsbs-price">';

			$price_html .= wc_price( $price );

			if ( ! isset( $subscription_info['sync'] ) || ! $subscription_info['sync'] ) {
				$price_html .= '<span class="price_time_opt"> / ' . YWSBS_Subscription_Helper::get_subscription_period_for_price( $product, $subscription_info ) . '</span>';
			} elseif ( isset( $subscription_info['sync'], $subscription_info['next_payment_due_date'] ) && $subscription_info['sync'] && 0 == $price ) { //phpcs:ignore
				if ( current_action() === 'woocommerce_cart_item_subtotal' ) {
					return $price_html;
				}
				$recurring_period        = YWSBS_Subscription_Helper::get_subscription_period_for_price( $cart_item['data'], $cart_item['ywsbs-subscription-info'] );
				$recurring_price         = YWSBS_Subscription_Helper::get_subscription_recurring_price( $cart_item['data'], $cart_item['ywsbs-subscription-info'] );
				$recurring_price_display = wc_get_price_to_display(
					$cart_item['data'],
					array(
						'qty'   => $cart_item['quantity'],
						'price' => $recurring_price,
					)
				);

				if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
					$recurring_tax = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				} else {
					$recurring_tax = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}

				$pri = wc_price( $recurring_price_display ) . ' / ' . $recurring_period . ' ' . $recurring_tax;
				$pri = apply_filters( 'ywsbs_recurring_price_html', $pri, $recurring_price, $recurring_period, $cart_item );

				// translators: placeholder 1. first price, 2. valid period of the first price, 3. end price.
				return sprintf( __( '%1$s until %2$s then %3$s', 'yith-woocommerce-subscription' ), $price_html, date_i18n( wc_date_format(), $subscription_info['next_payment_due_date'] ), $pri );
			}
			$max_length = YWSBS_Subscription_Helper::get_subscription_max_length_formatted_for_price( $product, $subscription_info );
			$max_length = ! empty( $max_length ) ? esc_html__( ' for ', 'yith-woocommerce-subscription' ) . $max_length : '';

			if ( is_cart() && ! $show_complete_price_on_substotal_cart ) {

				$signup_fee = apply_filters( 'ywsbs_product_fee', $subscription_info['fee'], $product );
				$signup_fee = empty( $signup_fee ) ? 0 : (float) $signup_fee;
				$signup_fee = wc_get_price_to_display(
					$product,
					array(
						'qty'   => $quantity,
						'price' => $signup_fee,
					)
				);

				$total_price = ( $price + $signup_fee );
				$price_html  = wc_price( $total_price );

			} else {

				$price_html  = $price_html . '<span class="ywsbs-max-lenght">' . $max_length . '</span>';
				$price_html  .= '</div>';
				$trial_price = YWSBS_Subscription_Helper::get_trial_price( $product, 'cart', $subscription_info );
				$fee_price   = YWSBS_Subscription_Helper::get_fee_price( $product, $quantity, null, $subscription_info );

				if ( trim( $fee_price ) !== '' ) {
					$price_html .= '<span class="ywsbs-fee-price">' . $fee_price . '</span>';
				}

				if ( trim( $trial_price ) !== '' ) {
					$price_html .= '<span class="ywsbs-trial-price">' . $trial_price . '</span>';
				}
				$price_html .= '</div>';

			}

			$price_html = apply_filters_deprecated( 'ywsbs_change_general_price_html',
				array( $price_html, $product, $product->get_meta( '_ywsbs_price_is_per' ), $product->get_meta( '_ywsbs_price_time_option' ), $product->get_meta( '_ywsbs_max_length' ), ywsbs_get_product_fee( $product ), ywsbs_get_product_trial( $product ), $quantity ), '2.0.0',
				'ywsbs_change_subtotal_product_price', 'This filter will be removed in next major release.' );

			// APPLY_FILTER: ywsbs_change_subtotal_product_price: to change the html price of a subscription product.
			return apply_filters( 'ywsbs_change_subtotal_product_price', $price_html, $product, $quantity, $cart_item, $show_complete_price_on_substotal_cart );

		}

		/**
		 * Change price in cart.
		 *
		 * @param   string  $price_html     HTML price.
		 * @param   array   $cart_item      Cart Item.
		 * @param   string  $cart_item_key  Cart Item Key.
		 *
		 * @return mixed|void
		 */
		public function change_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ywsbs_is_subscription_product( $product_id ) && isset( $cart_item['data'] ) ) {
				$product = $cart_item['data'];

				$price = apply_filters( 'ywsbs_change_price_in_cart_html', $cart_item['data']->get_price( 'edit' ), $cart_item['data'] );

				$price_current = apply_filters( 'ywsbs_change_price_current_in_cart_html', $product->get_price( 'edit' ), $product );
				$product->set_price( $price );

				$price_html = $this->change_general_price_html( $product, 1, true, $cart_item );

				$price_html = apply_filters( 'ywsbs_get_price_html', $price_html, $cart_item, $product_id );
				$product->set_price( $price_current );
			}

			return $price_html;

		}

		/**
		 * Check if there are subscription upgrade in progress and change the fee
		 *
		 * @param   float  $fee        Fee amount.
		 * @param   array  $cart_item  Cart Item.
		 *
		 * @return bool
		 */
		public function change_signup_fee_in_cart( $fee, $cart_item ) {

			$signup_fee = $fee;

			// add fee is gap payment is available and choosed by user.
			$product = $cart_item['data'];
			$id      = $product->get_id();

			$subscription_info = get_user_meta( get_current_user_id(), 'ywsbs_upgrade_' . $id, true );
			$gap_payment       = $product->get_meta( '_ywsbs_gap_payment' );
			$pay_gap           = 0;

			if ( ! empty( $subscription_info ) && isset( $subscription_info['pay_gap'] ) ) {
				$pay_gap = $subscription_info['pay_gap'];
			}

			if ( 'yes' === $gap_payment && $pay_gap > 0 ) {
				// change the fee of the subscription adding the total amount of the previous rates.
				$signup_fee += $pay_gap;
			}

			return $signup_fee;

		}

		/**
		 * Check if there are subscription upgrade in progress and change the trial options
		 * During the upgrade or downgrade the trial period will be nulled.
		 *
		 * @param   int    $trial      Trial.
		 * @param   array  $cart_item  Cart Item.
		 *
		 * @return int | string
		 */
		public function change_trial_in_cart( $trial, $cart_item ) {

			$new_trial = $trial;

			$product = $cart_item['data'];
			$id      = $product->get_id();

			/* UPGRADE PROCESS */
			$subscription_upgrade_info = get_user_meta( get_current_user_id(), 'ywsbs_upgrade_' . $id, true );
			if ( ! empty( $subscription_upgrade_info ) ) {
				return '';
			}

			/* DOWNGRADE PROCESS */
			$subscription_downgrade_info = get_user_meta( get_current_user_id(), 'ywsbs_trial_' . $id, true );
			if ( ! empty( $subscription_downgrade_info ) ) {
				$new_trial = $subscription_downgrade_info['trial_days'];
			}

			return $new_trial;

		}

		/**
		 * Only a subscription can be added to the cart this method check if there's
		 * a subscription in cart and remove the element if the next product to add is another subscription
		 *
		 * @param   bool  $valid         Is valid boolean.
		 * @param   int   $product_id    Product id.
		 * @param   int   $quantity      Quantity.
		 * @param   int   $variation_id  Variation id.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function cart_item_validate( $valid, $product_id, $quantity, $variation_id = 0 ) {

			$product_id = (int) ( ! empty( $variation_id ) ? $variation_id : $product_id );

			/**
			 * Current product.
			 *
			 * @var WC_Product
			 */
			$product = wc_get_product( $product_id );

			if ( ! YITH_WC_Subscription_Limit::is_purchasable( true, $product ) ) {
				$message = esc_html__( 'You have already an active subscription with this product.', 'yith-woocommerce-subscription' );
				wc_add_notice( $message, 'error' );

				return false;
			}

			if ( ywsbs_enable_subscriptions_multiple() ) {
				return $valid;
			}

			if ( ywsbs_is_subscription_product( $product ) ) {

				$item_keys = self::cart_has_subscriptions();

				if ( $item_keys ) {
					foreach ( $item_keys as $item_key ) {
						$current_item = WC()->cart->get_cart_item( $item_key );
						if ( ! empty( $current_item ) ) {
							$item_id = (int) ( ! empty( $current_item['variation_id'] ) ? $current_item['variation_id'] : $current_item['product_id'] );
							if ( $item_id !== $product_id ) {
								self::remove_subscription_from_cart( $item_key );
								$message = __( 'A subscription has been removed from your cart. You cannot purchase different subscriptions at the same time.', 'yith-woocommerce-subscription' );
								wc_add_notice( $message, 'error' );
							}
						}
					}
				}
			}

			return $valid;
		}

		/**
		 * Disable gateways that don't support multiple subscription on cart.
		 *
		 * @param   array  $gateways  Gateways list.
		 */
		public function disable_gateways( $gateways ) {

			if ( WC()->cart && is_checkout() ) {
				$subscription_on_cart = self::cart_has_subscriptions();

				if ( ! $subscription_on_cart || ! is_array( $subscription_on_cart ) ) {
					return $gateways;
				}

				$manual_renews_allowed = ( 'yes' === get_option( 'ywsbs_enable_manual_renews', 'yes' ) );
				foreach ( $gateways as $gateway_id => $gateway ) {
					if ( ! $gateway->supports( 'yith_subscriptions' ) ) {
						if ( ! $manual_renews_allowed ) {
							unset( $gateways[ $gateway_id ] );
						}
						continue;
					}

					if ( count( $subscription_on_cart ) >= 2 && WC()->payment_gateways() ) {
						if ( ! $gateway->supports( 'yith_subscriptions_multiple' ) ) {
							unset( $gateways[ $gateway_id ] );
						}
					}
				}
			}

			return $gateways;
		}

		/*
		|--------------------------------------------------------------------------
		| Checking Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Check if on cart there are subscriptions with signup fee.
		 *
		 * @return bool
		 */
		public static function cart_has_subscription_with_signup() {

			$check = false;

			if ( isset( WC()->cart ) ) {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
					/**
					 * Current product.
					 *
					 * @var WC_Product
					 */
					$product = $cart_item['data'];
					$id      = $product->get_id();

					if ( ywsbs_is_subscription_product( $id ) ) {
						$fee = ywsbs_get_product_fee( $product );
						if ( ! empty( $fee ) && $fee > 0 ) {
							$check = true;
							break;
						}
					}
				}
			}

			return $check;
		}

		/**
		 * Check if in the cart there are subscription products.
		 *
		 * @return bool|array
		 * @since  2.0.0
		 */
		public static function cart_has_subscriptions() {

			$count = 0;
			$items = array();

			if ( did_action( 'wp_loaded' ) && isset( WC()->cart ) ) {
				$contents = WC()->cart->get_cart();
				if ( ! empty( $contents ) ) {

					foreach ( $contents as $item_key => $item ) {
						$product = $item['data'];
						if ( ywsbs_is_subscription_product( $product ) ) {
							$count = array_push( $items, $item_key );
						}
					}
				}
			}

			return 0 === $count ? false : $items;
		}

		/**
		 * Check whether the cart needs payment even if the order total is $0
		 *
		 * @param   bool     $needs_payment  Need payment or is free.
		 * @param   WC_Cart  $cart           Cart.
		 *
		 * @return bool
		 */
		public static function cart_needs_payment( $needs_payment, $cart ) {
			/**
			 * APPLY_FILTERS: ywsbs_subscription_cart_needs_payments
			 *
			 * This filter allows to set if a cart needs of payments or not
			 *
			 * @param   bool  $needs_payment  Value that can be filtered.
			 *
			 * @return bool
			 */
			$cart_needs_payments = apply_filters( 'ywsbs_subscription_cart_needs_payments', ! $needs_payment && self::cart_has_subscriptions() && 0 == $cart->get_total( 'edit' ) ); // phpcs:ignore
			if ( $cart_needs_payments ) {
				$needs_payment = true;
			}

			return $needs_payment;
		}

		/**
		 * Removes all subscription products from the shopping cart.
		 *
		 * @param   int  $item_key  Cart item key to remove.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function remove_subscription_from_cart( $item_key ) {
			WC()->cart->set_quantity( $item_key, 0 );
		}


		/*
		|--------------------------------------------------------------------------
		| Deprecated Methods
		|--------------------------------------------------------------------------
		*/
		/**
		 * Get price.
		 *
		 * @param   int    $product_id  Product id.
		 * @param   float  $price       Price.
		 * @param   int    $quantity    Quantity.
		 *
		 * @return     float
		 * @deprecated 2.0.0
		 */
		public function get_price( $product_id, $price, $quantity = 1 ) {
			// Load product object.
			$product = wc_get_product( $product_id );

			$price = $product->get_regular_price();

			// Get correct price.
			if ( get_option( 'woocommerce_tax_display_cart' ) ) {
				$price = yit_get_price_including_tax( $product, $quantity, $price );
			} else {
				$price = yit_get_price_excluding_tax( $product, $quantity, $price );
			}

			return (float) $price;
		}

		/**
		 * Add recurring totals inside the cart.
		 */
		public function cart_recurring_totals() {
			if ( ! isset( WC()->cart ) || ! self::cart_has_subscriptions() ) {
				return false;
			}

			wc_get_template( 'cart/ywsbs-recurring-totals.php', array(), '', YITH_YWSBS_TEMPLATE_PATH . '/' );

		}

	}
}


/**
 * Unique access to instance of YWSBS_Subscription_Cart class
 *
 * @return YWSBS_Subscription_Cart
 */
function YWSBS_Subscription_Cart() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YWSBS_Subscription_Cart::get_instance();
}
