<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Coupons Class.
 *
 * @class   YWSBS_Subscription_Coupons
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Coupons' ) ) {

	/**
	 * Class YWSBS_Subscription_Coupons
	 */
	class YWSBS_Subscription_Coupons {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Coupons
		 */
		protected static $instance;

		/**
		 * List of coupon types
		 *
		 * @var array
		 */
		protected $coupon_types = array();

		/**
		 * Coupon error message
		 *
		 * @var string
		 */
		protected $coupon_error = '';

		/**
		 * Removing coupon
		 *
		 * @var array
		 */
		protected $removing_coupon = array();

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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->coupon_types = array( 'signup_percent', 'signup_fixed', 'recurring_percent', 'recurring_fixed' );

			// Add new coupons type to administrator.
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_coupon_discount_types' ) );
			add_filter( 'woocommerce_product_coupon_types', array( $this, 'add_coupon_discount_types_list' ) );

			// Apply discounts to a product and get the discounted price (before tax is applied).
			add_filter( 'woocommerce_get_discounted_price', array( $this, 'get_discounted_price' ), 10, 3 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'coupon_get_discount_amount' ), 10, 5 );
			add_filter( 'woocommerce_coupon_sort', array( $this, 'sort_coupons' ), 10, 2 );

			// Validate coupons.
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate_coupon' ), 10, 2 );

			// Limited coupons.
			add_action( 'woocommerce_coupon_options', array( $this, 'add_meta_to_coupon' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'save_custom_fields' ), 10 );
			add_action( 'manage_edit-shop_coupon_columns', array( $this, 'add_custom_column' ), 20, 2 );
			add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'add_value_to_custom_column' ), 20, 2 );
			add_action( 'wp_ajax_woocommerce_remove_order_coupon', array( $this, 'before_remove_coupon' ), 1 );
			add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'after_removed_coupon' ), 100, 2 );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'increase_usage_limit' ), 100 );
			add_action( 'woocommerce_before_delete_shop_order', array( $this, 'increase_usage_limit' ), 100 );
		}

		/**
		 * Increment the usage of the limited coupon when an order is cancelled or deleted.
		 *
		 * @param int $order_id Order id.
		 * @return void;
		 * @since 2.3.0
		 */
		public function increase_usage_limit( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order instanceof WC_Order ) {
				return;
			}

			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( empty( $subscriptions ) ) {
				return;
			}

			$is_a_renew = $order->get_meta( 'is_a_renew' );

			if ( 'yes' !== $is_a_renew ) {
				return;
			}

			$coupons = $order->get_items( 'coupon' );

			if ( ! empty( $coupons ) ) {
				$subscription = ywsbs_get_subscription( $subscriptions[0] );
				if ( $subscription instanceof YWSBS_Subscription ) {
					$subscription_coupons = $subscription->get( 'coupons' );
					if ( ! empty( $subscription_coupons ) ) {
						foreach ( $subscription_coupons as $key => $subscription_coupon ) {
							if ( isset( $subscription_coupon['limited'] ) && $subscription_coupon['limited'] > 0 && $subscription_coupon['used'] > 0 ) {
								$remain                               = $subscription_coupon['limited'] - $subscription_coupons[ $key ]['used'];
								$subscription_coupons[ $key ]['used'] = $subscription_coupon['used'] - 1;
								YITH_WC_Activity()->add_activity(
									$subscription->get_id(),
									'changed',
									$status                           = 'success',
									$order                            = 0,
									// translators: Placeholder: coupon name, previous limited value, current limited values, order cancelled.
									sprintf( esc_html_x( 'Limited level increased for the coupon %1$s: %2$d->%3$d. Because the order #%4$s has been cancelled.', 'Placeholder: coupon name, previous limited value, current limited values, order cancelled', 'yith-woocommerce-subscription' ), $subscription_coupon['coupon_code'], $remain, $remain + 1, $order_id )
								);

							}
						}

						$subscription->set( 'coupons', $subscription_coupons );
					}
				}
			}
		}

		/**
		 * Save temporary coupon data that will be removed.
		 *
		 * @since 2.3.0
		 */
		public function before_remove_coupon() {

			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				wp_die( -1 );
			}

			if ( ! isset( $_POST['coupon'], $_POST['order_id'] ) ) {
				return;
			}

			$order_id = absint( sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) );
			$order    = wc_get_order( $order_id );
			if ( 'yes' === $order->get_meta( 'is_a_renew' ) ) {
				$coupon_code           = wc_format_coupon_code( sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) );
				$this->removing_coupon = array(
					'order_id'    => $order_id,
					'coupon_code' => $coupon_code,
				);

			}

		}

		/**
		 * Save temporary coupon data that will be removed.
		 *
		 * @param bool     $and_taxes Calc taxes if true.
		 * @param WC_Order $order Renew order.
		 * @since 2.3.0
		 */
		public function after_removed_coupon( $and_taxes, $order ) {

			if ( empty( $this->removing_coupon ) ) {
				return;
			}

			if ( $order->get_id() === $this->removing_coupon['order_id'] ) {
				$coupons = $order->get_items( 'coupon' );
				// Remove the coupon line.
				$removed = true;
				foreach ( $coupons as $item_id => $coupon ) {
					if ( $coupon->get_code() === $this->removing_coupon['coupon_code'] ) {
						$removed = false;
						break;
					}
				}

				if ( $removed ) {
					$subscriptions = $order->get_meta( 'subscriptions' );
					foreach ( $subscriptions as $subscription ) {
						$subscription = ywsbs_get_subscription( $subscription );
						$coupons      = $subscription->get( 'coupons' );

						if ( $coupons ) {
							foreach ( $coupons as $key => $coupon ) {
								if ( $coupon['coupon_code'] === $this->removing_coupon['coupon_code'] ) {
									$coupons[ $key ]['used'] = (int) $coupon['used'] - 1;
									YITH_WC_Activity()->add_activity(
										$subscription->get_id(),
										'changed',
										$status              = 'success',
										$order->get_id(),
										// translators: Placeholder: coupon code, renew order id, usage counter value.
										sprintf( esc_html_x( 'Removed coupon %1$s from the renew order #%2$s. Decreased the usage limit to %3$d', 'Placeholder: coupon code, renew order id, usage counter value', 'yith-woocommerce-subscription' ), $coupon['coupon_code'], $order->get_id(), $coupons[ $key ]['used'] )
									);

								}
							}

							$subscription->set( 'coupons', $coupons );
						}
					}

					$this->removing_coupon = array();
				}
			}
		}


		/**
		 * Override sort coupons during the discount calculation.
		 *
		 * @param int       $sort Sort priority.
		 * @param WC_Coupon $coupon Coupon.
		 * @return int
		 * @since 2.1
		 */
		public function sort_coupons( $sort, $coupon ) : int {
			$coupon_type = $coupon->get_discount_type();
			if ( ! in_array( $coupon_type, $this->coupon_types, true ) ) {
				return $sort;
			}

			if ( in_array( $coupon_type, array( 'signup_percent', 'recurring_percent' ), true ) ) {
				$sort = 2;
			}

			if ( in_array( $coupon_type, array( 'signup_fixed', 'signup_fixed' ), true ) ) {
				$sort = 1;
			}

			return $sort;
		}

		/**
		 * Add discount types on coupon system
		 *
		 * @param array $coupons_type List of coupon types.
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function add_coupon_discount_types( $coupons_type ) {

			$coupons_type['signup_percent']    = esc_html__( 'Subscription Signup % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['signup_fixed']      = esc_html__( 'Subscription Signup Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_percent'] = esc_html__( 'Subscription Recurring % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_fixed']   = esc_html__( 'Subscription Recurring Discount', 'yith-woocommerce-subscription' );

			return $coupons_type;
		}

		/**
		 * Add subscription coupons to WooCommerce List.
		 *
		 * @param array $coupons_type Coupon type list.
		 *
		 * @return array
		 */
		public function add_coupon_discount_types_list( $coupons_type ) {
			return array_merge( $coupons_type, $this->coupon_types );
		}

		/**
		 * Return the discounted price.
		 *
		 * @param float   $price Price of cart item.
		 * @param array   $cart_item Cart item.
		 * @param WC_Cart $cart Cart Object.
		 *
		 * @return mixed
		 * @throws Exception Return an error.
		 */
		public function get_discounted_price( $price, $cart_item, $cart ) {

			$id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ! $price || ! ywsbs_is_subscription_product( $id ) ) {
				return $price;
			}

			$regular_price   = $price;
			$applied_coupons = ywsbs_get_applied_coupons( $cart );

			if ( ! empty( $applied_coupons ) ) {

				$product = $cart_item['data'];
				foreach ( $applied_coupons as $code => $coupon ) {
					$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

					if ( $valid ) {
						$discount_amount = (float) $coupon->get_discount_amount( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ? $price : $regular_price, $cart_item, true );

						// Store the totals for DISPLAY in the cart.
						$total_discount     = $discount_amount * $cart_item['quantity'];
						$total_discount_tax = 0;

						if ( wc_tax_enabled() && $product->is_taxable() ) {
							$tax_rates          = WC_Tax::get_rates( $product->get_tax_class() );
							$taxes              = WC_Tax::calc_tax( $discount_amount, $tax_rates, $cart->prices_include_tax );
							$total_discount_tax = WC_Tax::get_tax_total( $taxes ) * $cart_item['quantity'];
							$total_discount     = $cart->prices_include_tax ? $total_discount - $total_discount_tax : $total_discount;

							$cart->discount_cart_tax += $total_discount_tax;

						}

						$cart->discount_cart += $total_discount;

						$this->increase_coupon_discount_amount( $code, $total_discount, $total_discount_tax, $cart );

					}

					// If the price is 0, we can stop going through coupons because there is nothing more to discount for this product.
					if ( 0 >= $price ) {
						break;
					}
				}
			}

			return $price;
		}


		/**
		 * Total of coupon discounts
		 *
		 * @param string  $coupon_code Coupon Code.
		 * @param float   $amount Amount.
		 * @param float   $total_discount_tax Total discount tax.
		 * @param WC_Cart $cart Cart.
		 *
		 * @return void
		 */
		public function increase_coupon_discount_amount( $coupon_code, $amount, $total_discount_tax, $cart ) {
			$cart->coupon_discount_amounts[ $coupon_code ]     = isset( $cart->coupon_discount_amounts[ $coupon_code ] ) ? $cart->coupon_discount_amounts[ $coupon_code ] + $amount : $amount;
			$cart->coupon_discount_tax_amounts[ $coupon_code ] = isset( $cart->coupon_discount_tax_amounts[ $coupon_code ] ) ? $cart->coupon_discount_tax_amounts[ $coupon_code ] + $total_discount_tax : $total_discount_tax;
		}


		/**
		 * Check if coupon is valid.
		 *
		 * @param bool      $is_valid Is valid.
		 * @param WC_Coupon $coupon WC_Coupon.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function validate_coupon( $is_valid, $coupon ) {

			$this->coupon_error   = '';
			$coupon_type          = $coupon->get_discount_type();
			$subscription_in_cart = YWSBS_Subscription_Cart::cart_has_subscriptions();

			if ( ! in_array( $coupon_type, $this->coupon_types, true ) && ! $subscription_in_cart ) {
				return $is_valid;
			}

			// ignore non-subscription coupons.
			if ( ! $subscription_in_cart ) {
				$this->coupon_error = esc_html__( 'Sorry, this coupon can be used only if there is a subscription in the cart', 'yith-woocommerce-subscription' );
			} else {
				if ( in_array( $coupon_type, array( 'signup_percent', 'signup_fixed' ), true ) && ! YWSBS_Subscription_Cart::cart_has_subscription_with_signup() ) {
					$this->coupon_error = __( 'Sorry, this coupon can be used only if there is a subscription with signup fees', 'yith-woocommerce-subscription' );
				}
			}

			if ( ! empty( $this->coupon_error ) ) {
				$is_valid = false;
				add_filter( 'woocommerce_coupon_error', array( $this, 'add_coupon_error' ), 10 );
			}

			return $is_valid;
		}

		/**
		 * Get discount amount.
		 *
		 * @param float      $discount Amount this coupon has discounted.
		 * @param float      $discounting_amount Amount the coupon is being applied to.
		 * @param array|null $cart_item Cart item being discounted if applicable.
		 * @param boolean    $single True if discounting a single qty item, false if its the line.
		 * @param WC_Coupon  $coupon Coupon Object.
		 *
		 * @return float|int|mixed
		 * @throws Exception Return error.
		 */
		public function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

			$product = $cart_item['data'];
			$id      = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ! ywsbs_is_subscription_product( $id ) ) {
				return $discount;
			}

			$fee             = ywsbs_get_product_fee( $product );
			$trial_per       = ywsbs_get_product_trial( $product );
			$recurring_price = $discounting_amount;

			$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

			if ( ! empty( $coupon ) && $valid ) {

				$coupon_type   = $coupon->get_discount_type();
				$coupon_amount = $coupon->get_amount();

				switch ( $coupon_type ) {
					case 'signup_percent':
						if ( ! empty( $fee ) && 0 !== $fee ) {
							$discount = round( ( $fee / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'recurring_percent':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = round( ( $recurring_price / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'signup_fixed':
						if ( ! empty( $fee ) && 0 !== $fee ) {
							$discount = ( $fee < $coupon_amount ) ? $fee : $coupon_amount;
						}
						break;
					case 'recurring_fixed':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = ( $recurring_price < $coupon_amount ) ? $recurring_price : $coupon_amount;
						}
						break;
					default:
				}
			}

			return $discount;

		}

		/**
		 * Add coupon error if the coupon is not valid
		 *
		 * @param string $errors Error.
		 * @return string
		 * @since  1.0.0
		 */
		public function add_coupon_error( $errors ) {
			if ( ! empty( $this->coupon_error ) ) {
				$errors = $this->coupon_error;
			}

			return $errors;
		}

		/**
		 * Add the option of limited coupon to the recurring type coupon
		 *
		 * @param int       $coupon_id Coupon id.
		 * @param WC_Coupon $coupon Coupon object.
		 * @since 2.3.0
		 */
		public function add_meta_to_coupon( $coupon_id, $coupon ) {

			// Usage limit per coupons.
			woocommerce_wp_text_input(
				array(
					'id'                => 'ywsbs_limited_for_payments',
					'label'             => esc_html_x( 'Recurring limit per coupon', 'Coupon option label', 'yith-woocommerce-subscription' ),
					'placeholder'       => esc_attr_x( 'Valid for all recurring payments', 'Limited coupon placeholder', 'yith-woocommerce-subscription' ),
					'description'       => esc_html__( 'Choose after how many recurring payments, the discount will be stopped.', 'yith-woocommerce-subscription' ),
					'type'              => 'number',
					'desc_tip'          => false,
					'class'             => 'short',
					'custom_attributes' => array(
						'step' => 1,
						'min'  => 0,
					),
					'value'             => $coupon->get_meta( 'ywsbs_limited_for_payments' ),
				)
			);
		}


		/**
		 * Save our limited coupon option.
		 *
		 * @param int $coupon_id Coupon id.
		 * @since 2.3.0
		 */
		public function save_custom_fields( $coupon_id ) {

			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
				return;
			}

			if ( isset( $_POST['ywsbs_limited_for_payments'] ) ) {
				$coupon = new WC_Coupon( $coupon_id );
				$coupon->add_meta_data( 'ywsbs_limited_for_payments', sanitize_text_field( wp_unslash( $_POST['ywsbs_limited_for_payments'] ) ), true );
				$coupon->save();
			}

		}

		/**
		 * Add a custom column to the Coupon list table
		 *
		 * @param array $columns List of columns.
		 * @return array
		 * @since 2.3.0
		 */
		public function add_custom_column( $columns ) {
			$columns['ywsbs_limited_for_payments'] = esc_html_x( 'Limited for recurring payments', 'header on coupon list table', 'yith-woocommerce-subscription' );
			return $columns;
		}

		/**
		 * Add a custom column to the Coupon list table
		 *
		 * @param string $column Column.
		 *
		 * @since 2.3.0
		 */
		public function add_value_to_custom_column( $column ) {
			if ( 'ywsbs_limited_for_payments' !== $column ) {
				return;
			}
			global $post;

			$coupon                     = new WC_Coupon( $post->ID );
			$ywsbs_limited_for_payments = $coupon->get_meta( 'ywsbs_limited_for_payments' );

			echo empty( $ywsbs_limited_for_payments ) ? '-' : esc_html( $ywsbs_limited_for_payments );
		}

		/**
		 * After applying coupons via the YWSBS_Subscription_Discounts class update subscription amounts.
		 *
		 * @param YWSBS_Subscription           $subscription Subscription object.
		 * @param YWSBS_Subscription_Discounts $discounts Discounts class.
		 * @param string                       $coupon_code Coupon code.
		 * @since 2.3.0
		 */
		protected function set_coupon_discount_amounts( $subscription, $discounts, $coupon_code ) {
			$coupon_discounts = $discounts->get_discounts_by_coupon();

			$order            = $subscription->get_order();
			$billing          = $subscription->get_address_fields( 'billing' );
			$shipping         = $subscription->get_address_fields( 'shipping' );
			$shipping_country = isset( $shipping['country'] ) ? $shipping['country'] : false;

			$tax_based_on = get_option( 'woocommerce_tax_based_on' );

			if ( 'shipping' === $tax_based_on && ! $shipping_country ) {
				$tax_based_on = 'billing';
			}

			$args = array(
				'country'  => 'billing' === $tax_based_on ? $billing['billing_country'] : $shipping['shipping_country'],
				'state'    => 'billing' === $tax_based_on ? $billing['billing_state'] : $shipping['shipping_state'],
				'postcode' => 'billing' === $tax_based_on ? $billing['billing_postcode'] : $shipping['shipping_postcode'],
				'city'     => 'billing' === $tax_based_on ? $billing['billing_city'] : $shipping['shipping_city'],
			);

			if ( 'base' === $tax_based_on || empty( $args['country'] ) ) {
				$args['country']  = WC()->countries->get_base_country();
				$args['state']    = WC()->countries->get_base_state();
				$args['postcode'] = WC()->countries->get_base_postcode();
				$args['city']     = WC()->countries->get_base_city();
			}

			$tax_location = $args;
			$tax_location = array(
				$tax_location['country'],
				$tax_location['state'],
				$tax_location['postcode'],
				$tax_location['city'],
			);

			$order_item = $order->get_item( $subscription->get( 'order_item_id' ) );

			$discount_tax = 0;
			$coupons      = (array) $subscription->get( 'coupons' );

			foreach ( $coupon_discounts as $code => $item_discount_amount ) {
				$amount = $item_discount_amount;
				$coupon = new WC_Coupon( $code );

				$taxes = array_sum( WC_Tax::calc_tax( $item_discount_amount, $this->get_tax_rates( $order_item->get_tax_class(), $tax_location ), $order->get_prices_include_tax() ) );
				if ( 'yes' !== get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
					$taxes = wc_round_tax_total( $taxes );
				}

				$discount_tax += $taxes;

				if ( $order->get_prices_include_tax() ) {
					$amount = $amount - $taxes;
				}

				if ( $coupon_code === $code ) {
					$coupons[] = array(
						'coupon_code'         => $coupon_code,
						'coupon_type'         => $coupon->get_discount_type(),
						'coupon_amount'       => $coupon->get_amount(),
						'discount_amount'     => $amount,
						'discount_amount_tax' => $taxes,
						'limited'             => $coupon->get_meta( 'ywsbs_limited_for_payments' ),
						'used'                => 0,
					);
				}
			}

			$line_tax               = $subscription->get_line_tax() - $discount_tax;
			$line_total             = $subscription->get_line_total() - $amount;
			$line_tax_data          = $subscription->get_line_tax_data();
			$line_tax_data['total'] = $line_total;
			$subscription->set( 'line_tax', $line_tax );
			$subscription->set( 'line_tax_data', $line_tax_data );
			$subscription->set( 'order_tax', $line_tax );
			$subscription->set( 'line_total', $line_total );
			$order_total = $line_total + $line_tax + $subscription->get_order_shipping() + $subscription->get_order_shipping_tax();
			$subscription->set( 'order_total', $order_total );
			$subscription->set( 'subscription_total', $order_total );
			$subscription->set( 'coupons', $coupons );
		}

		/**
		 * Remove coupon from subscription.
		 *
		 * @param YWSBS_Subscription $subscription Subscription object.
		 * @param string             $coupon_code Coupon code.
		 * @param int                $user_id User id.
		 * @param string             $user_email User email.
		 *
		 * @return bool
		 */
		public function add_coupon_to_subscription( $subscription, $coupon_code, $user_id, $user_email ) {

			if ( is_a( $coupon_code, 'WC_Coupon' ) ) {
				$coupon = $coupon_code;
			} elseif ( is_string( $coupon_code ) ) {
				$code   = wc_format_coupon_code( $coupon_code );
				$coupon = new WC_Coupon( $code );

				if ( $coupon->get_code() !== $code ) {
					return new WP_Error( 'invalid_coupon', esc_html__( 'Invalid coupon code', 'yith-woocommerce-subscription' ) );
				}
			} else {
				return new WP_Error( 'invalid_coupon', esc_html__( 'Invalid coupon', 'yith-woocommerce-subscription' ) );
			}

			// Check to make sure coupon is not already applied.
			$applied_coupons = $subscription->get( 'coupons' );
			foreach ( $applied_coupons as $applied_coupon ) {
				if ( $applied_coupon['coupon_code'] === $coupon->get_code() ) {
					return new WP_Error( 'invalid_coupon', esc_html__( 'Coupon code already applied!', 'yith-woocommerce-subscription' ) );
				}
			}

			$coupon_type = $coupon->get_discount_type();
			if ( ! in_array( $coupon_type, array( 'recurring_percent', 'recurring_fixed' ), true ) ) {
				return new WP_Error( 'invalid_coupon', esc_html__( 'Invalid coupon type', 'yith-woocommerce-subscription' ) );
			}

			// Remove the filter that validate the coupon on cart.
			remove_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate_coupon' ), 10 );

			$discount = new YWSBS_Subscription_Discounts( $subscription );
			$applied  = $discount->apply_coupon( $coupon );

			if ( is_wp_error( $applied ) ) {
				return $applied;
			}

			$data_store = $coupon->get_data_store();

			// Check specific for guest checkouts here as well since WC_Cart handles that separately in check_customer_coupons.
			if ( $data_store && 0 === $user_id ) {
				$usage_count = $data_store->get_usage_by_email( $coupon, $user_email );
				if ( 0 < $coupon->get_usage_limit_per_user() && $usage_count >= $coupon->get_usage_limit_per_user() ) {
					return new WP_Error(
						'invalid_coupon',
						$coupon->get_coupon_error( 106 ),
						array(
							'status' => 400,
						)
					);
				}
			}

			$this->set_coupon_discount_amounts( $subscription, $discount, $coupon->get_code() );
			$used_by = $user_id ? $user_id : $user_email;
			$coupon->increase_usage_count( $used_by );

			do_action( 'ywsbs_applied_coupon_to_subscription', $subscription, $coupon_code, $user_id, $user_email );
			$stored_coupon = array();
			$coupons       = $subscription->get( 'coupons' );
			if ( $coupons ) {
				foreach ( $coupons as $single_coupon ) {
					if ( $coupon_code === $single_coupon['coupon_code'] ) {
						$stored_coupon = $single_coupon;
						break;
					}
				}
			}

			$readable_coupon = '';
			foreach ( $stored_coupon as $key => $value ) {
				$stored_coupon .= '<strong>' . $key . '</strong> :' . $value . '<br>';
			}

			YITH_WC_Activity()->add_activity(
				$subscription->get_id(),
				'changed',
				$status = 'success',
				$order  = 0,
				// translators: Placeholder: coupon details.
				sprintf( esc_html_x( 'Added coupon: %1$s', 'Placeholder: coupon details', 'yith-woocommerce-subscription' ), $readable_coupon )
			);

			return true;

		}


		/**
		 * Get tax rates for an order. Use order's shipping or billing address, defaults to base location.
		 *
		 * @param string $tax_class Tax class to get rates for.
		 * @param array  $tax_location Location to compute rates for. Should be in form: array( country, state, postcode, city).
		 * @param object $customer Only used to maintain backward compatibility for filter `woocommerce-matched_rates`.
		 *
		 * @return mixed|void Tax rates.
		 */
		protected function get_tax_rates( $tax_class, $tax_location = array(), $customer = null ) {
			return WC_Tax::get_rates_from_location( $tax_class, $tax_location, $customer );
		}

		/**
		 * Remove coupon from subscription.
		 *
		 * @param YWSBS_Subscription $subscription Subscription object.
		 * @param string             $coupon_code Coupon code.
		 *
		 * @return bool
		 */
		public function remove_coupon_from_subscription( $subscription, $coupon_code ) {
			$coupons        = $subscription->get( 'coupons' );
			$coupon_removed = array();
			if ( $coupons ) {
				foreach ( $coupons as $key => $coupon ) {
					if ( $coupon['coupon_code'] === $coupon_code ) {
						$coupon_removed = $coupon;
						unset( $coupons[ $key ] );
					}
				}

				$line_total = $subscription->get_line_subtotal();
				$line_tax   = $subscription->get_line_subtotal_tax();

				$subscription->set( 'line_total', $line_total );
				$subscription->set( 'line_tax', $line_tax );

				$order = $subscription->get_order();

				if ( $coupons ) {

					foreach ( $coupons as $key => $current_coupon ) {

						$coupon_code = $current_coupon['coupon_code'];
						$coupon_id   = wc_get_coupon_id_by_code( $coupon_code );
						if ( $coupon_id ) {
							$coupon_object = new WC_Coupon( $coupon_id );
						} else {
							// If we do not have a coupon ID (was it virtual? has it been deleted?) we must create a temporary coupon using what data we have stored during checkout.
							$coupon_object = new WC_Coupon();
							$coupon_object->set_code( $coupon_code );
							$coupon_object->set_virtual( true );

							// If there is no coupon amount (maybe dynamic?), set it to the given **discount** amount so the coupon's same value is applied.
							if ( ! $coupon_object->get_amount() ) {

								// If the order originally had prices including tax, remove the discount + discount tax.
								if ( $order->get_prices_include_tax() ) {
									$coupon_object->set_amount( (float) $current_coupon['discount_amount'] + (float) $current_coupon['discount_amount_tax'] );
								} else {
									$coupon_object->set_amount( $current_coupon['discount_amount'] );
								}
								$coupon_object->set_discount_type( 'recurring_fixed' );
							}
						}

						$discount = new YWSBS_Subscription_Discounts( $subscription );
						$applied  = $discount->apply_coupon( $coupon_object, false );
						if ( $applied ) {
							$this->set_coupon_discount_amounts( $subscription, $discount, $coupon_object->get_code() );
						}
					}
				} else {

					$order_total = $line_total + $line_tax + $subscription->get_order_shipping() + $subscription->get_order_shipping_tax();
					if ( isset( $line_tax_data['subtotal'], $line_tax_data['total'] ) ) {
						$line_tax_data['total'] = $line_tax;
						$subscription->set( 'line_tax_data', $line_tax_data );
					}

					$subscription->set( 'order_tax', $line_tax );
					$subscription->set( 'order_total', $order_total );
					$subscription->set( 'subscription_total', $order_total );
					$subscription->set( 'order_subtotal', $line_total + $line_tax );
				}

				$subscription->set( 'coupons', $coupons );

				do_action( 'ywsbs_removed_coupon_from_subscription', $subscription, $coupon_code );

				$readable_coupon = '';
				foreach ( $coupon as $key => $value ) {
					$readable_coupon .= '<strong>' . $key . '</strong> :' . $value . '<br>';
				}

				YITH_WC_Activity()->add_activity(
					$subscription->get_id(),
					'changed',
					$status = 'success',
					$order  = 0,
					// translators: Placeholder: coupon details.
					sprintf( esc_html_x( 'Removed coupon: %1$s', 'Placeholder: coupon details', 'yith-woocommerce-subscription' ), $readable_coupon )
				);

				return true;
			}

			return false;
		}
	}

}

/**
 * Unique access to instance of YWSBS_Subscription_Coupons class
 *
 * @return YWSBS_Subscription_Coupons
 */
function YWSBS_Subscription_Coupons() { // phpcs:ignore
	return YWSBS_Subscription_Coupons::get_instance();
}
