<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YWSBS_Subscription_Order Class
 *
 * @class   YWSBS_Subscription_Order
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Order' ) ) {

	/**
	 * Class YWSBS_Subscription_Order
	 */
	class YWSBS_Subscription_Order {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Order
		 */
		protected static $instance;

		/**
		 * An array of current order rates
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @var array
		 */
		protected $current_rates = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Order
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Array with the new subscription details
		 *
		 * @var array
		 */
		private $subscriptions_info = array();

		/**
		 * Cart item order item
		 *
		 * @var array
		 */
		private $cart_item_order_item = array();

		/**
		 * Temporary Cart.
		 *
		 * @var WC_Cart
		 */
		private $actual_cart;

		/**
		 * Current order id
		 *
		 * @var int
		 */
		private $current_order_id;

		/**
		 * Flag to check if the plugin is creating a subscription.
		 *
		 * @var boolean
		 */
		private $creating_subscription = false;


		/**
		 * Flag to check if the payment is done.
		 *
		 * @var boolean
		 */
		private $payment_done = array();

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			// Save details of subscription.
			add_action( 'woocommerce_new_order_item', array( $this, 'add_subscription_order_item_meta' ), 20, 3 );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'get_extra_subscription_meta' ), 10, 2 );

			// Add subscriptions to orders.
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'check_order_for_subscription' ), 100, 2 );
			add_action( 'woocommerce_resume_order', array( $this, 'remove_subscription_from_order' ) );

			// Start subscription after payment received.
			add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete' ) );
			add_action( 'woocommerce_order_status_completed', array( $this, 'payment_complete' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'payment_complete' ) );

			// When the order is deleted the subscription is deleted.
			add_action( 'before_delete_post', array( __CLASS__, 'delete_subscriptions' ), 10 );
			// When the order is trashed the subscription is trashed.
			add_action( 'wp_trash_post', array( __CLASS__, 'trash_subscriptions' ), 10 );
			// When the order is untrashed the subscription is untrashed.
			add_action( 'untrashed_post', array( __CLASS__, 'untrash_subscriptions' ), 10 );

			if ( get_option( 'ywsbs_delete_subscription_order_cancelled', 'yes' ) === 'yes' ) {
				add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'trash_subscriptions' ), 10 );
			} else {
				add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'cancel_subscriptions' ), 10 );
			}

			// On refund of the main order cancel the subscription.
			add_action( 'woocommerce_order_fully_refunded', array( $this, 'order_refunded' ), 10, 2 );
			add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 10 );
			add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'reactive_subscription' ), 10 );
			add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'reactive_subscription' ), 10 );

			// If there's a subscription inside the order, even if the order total is $0, it still needs payment.
			add_filter( 'woocommerce_order_needs_payment', array( $this, 'order_need_payment' ), 10, 3 );
			add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'can_reduce_order_stock' ), 10, 2 );

			// renew_manually from my account > orders.
			if ( 'yes' === get_option( 'ywsbs_renew_now_on_my_account', 'no' ) ) {
				add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_renew_subscription_manually' ), 10, 2 );
				add_action( 'wp', array( $this, 'pay_renew_order_now' ) );
			}

			// make renew order payable.
			add_filter( 'woocommerce_order_needs_payment', array( $this, 'renew_needs_payment' ), 10, 3 );

			// check if the taxes are correct.
			add_action( 'ywsbs_renew_order_saved', array( $this, 'recalculate_taxes_on_renew_order' ), 10, 2 );

			// add actions to the order.
			add_filter( 'woocommerce_order_actions', array( $this, 'add_order_action' ), 10 );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'create_subscription' ), 100 );

		}

		/**
		 * Add order action.
		 *
		 * @param array $actions Action list.
		 *
		 * @return array
		 * @since 2.3
		 */
		public function add_order_action( $actions ) {
			global $theorder;

			if ( apply_filters( 'ywsbs_disable_subscription_creation', ! in_array( $theorder->get_status(), array( 'pending', 'on-hold' ), true ), $theorder ) ) {
				return $actions;
			}

			$is_a_renew = $theorder->get_meta( 'is_a_renew' );
			if ( 'yes' === $is_a_renew ) {
				return $actions;
			}

			$subscription_items = $this->get_subscription_items_inside_the_order( $theorder );
			$subscriptions      = $theorder->get_meta( 'subscriptions' );

			$subscription_item_orders = array();
			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription ) {
					$subscription_item_orders[] = (int) get_post_meta( (int) $subscription, 'order_item_id', true );
				}
			}

			foreach ( $subscription_items as $subscription_item ) {
				if ( ! in_array( $subscription_item->get_id(), $subscription_item_orders, true ) ) {
					// translators: Placeholder is the name of the product.
					$actions[ 'ywsbs_create_subscription_item_' . $subscription_item->get_id() ] = sprintf( esc_html_x( 'Create a new subscription for %s', 'Placeholder is the name of the product', 'yith-woocommerce-subscription' ), wp_kses_post( wp_strip_all_tags( $subscription_item->get_product()->get_name() ) ) );
				}
			}

			return $actions;
		}

		/**
		 * Returns the subscription items
		 *
		 * @param WC_Order $order Order.
		 *
		 * @return array
		 * @since 2.3
		 */
		public function get_subscription_items_inside_the_order( $order ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( $order instanceof WC_Order ) {
				$items              = $order->get_items();
				$subscription_items = array();

				if ( $items ) {
					foreach ( $items as $item ) {
						$product = $item->get_product();
						if ( $product ) {
							$product_id = $product->get_id();
							if ( ywsbs_is_subscription_product( $product_id ) ) {
								$subscription_items[] = $item;
							}
						}
					}
				}
			}

			return $subscription_items;
		}

		/**
		 * Create subscription.
		 *
		 * @return void
		 * @since 2.3.0
		 */
		public function create_subscription() {

			if ( ! isset( $_POST['wc_order_action'] ) ) { //phpcs:ignore
				return;
			}

			$action = sanitize_text_field( wp_unslash( $_POST['wc_order_action'] ) ); //phpcs:ignore

			if ( false !== strpos( $action, 'ywsbs_create_subscription_item_' ) && ! $this->creating_subscription ) {
				$this->creating_subscription = true;
				$order_item_id               = (int) str_replace( 'ywsbs_create_subscription_item_', '', $action );
				$order_item                  = new WC_Order_Item_Product( $order_item_id );
				if ( $order_item instanceof WC_Order_Item_Product ) {
					$this->create_subscription_from_item( $order_item );
				}
			}
		}

		/**
		 * Create a subscription from an order item.
		 *
		 * @param WC_Order_Item_Product $order_item Order item product.
		 *
		 * @since 2.3
		 */
		protected function create_subscription_from_item( $order_item ) {

			$product    = $order_item->get_product();
			$order      = $order_item->get_order();
			$product_id = $product->get_id();

			if ( ! ywsbs_is_subscription_product( $product_id ) ) {
				return;
			}

			$price_is_per = (int) $product->get_meta( '_ywsbs_price_is_per' );
			$trial_per    = ywsbs_get_product_trial( $product );
			$max_length   = (int) YWSBS_Subscription_Helper()->get_subscription_product_max_length( $product );

			if ( $order_item->get_variation_id() ) {
				$formatted_data = $order_item->get_formatted_meta_data( '', true );
				$variations     = array();
				if ( $formatted_data ) {
					foreach ( $formatted_data as $data ) {
						$variations[ 'attribute_' . $data->key ] = $data->value;
					}
				}
			}

			$order_shipping     = 0;
			$order_shipping_tax = 0;

			if ( $product->needs_shipping() ) {
				$order_shipping      = (float) $order->get_shipping_total();
				$order_shipping_tax  = (float) $order->get_shipping_tax();
				$shippings           = array();
				$shipping_order_item = $order->get_items( 'shipping' );
				if ( $shipping_order_item ) {
					foreach ( $shipping_order_item as $shipping_item ) {
						/**
						 * Current WC_Order_Item_Shipping
						 *
						 * @var WC_Order_Item_Shipping
						 */
						$shippings['method_id'] = $shipping_item->get_method_id();
						$shippings['name']      = ucwords( str_replace( '_', ' ', $shipping_item->get_method_id() ) );
						$shippings['cost']      = $shipping_item->get_total();
						$shippings['taxes']     = $shipping_item->get_taxes();
						$shippings['total_tax'] = $shipping_item->get_total_tax();
						break;
					}
				}
			}

			$subscription_total    = (float) $order_item->get_total() + (float) $order_item->get_total_tax() + $order_shipping + $order_shipping_tax;
			$next_payment_due_date = YWSBS_Subscription_Helper()->get_billing_payment_due_date( $product, time() );
			if ( YWSBS_Subscription_Synchronization()->is_synchronizable( $product ) ) {
				$next_payment_due_date = YWSBS_Subscription_Synchronization()->get_next_payment_due_date_sync( $next_payment_due_date, $product );
			}

			// Fill the array for subscription creation.
			$args = array(
				'product_id'    => $order_item->get_product_id(),
				'variation_id'  => $order_item->get_variation_id(),
				'variation'     => ( isset( $variations ) ? $variations : '' ),
				'product_name'  => $product->get_name(),
				'quantity'      => $order_item->get_quantity(),
				'order_id'      => $order->get_id(),
				'order_item_id' => $order_item->get_id(),
				'order_ids'     => array( $order->get_id() ),

				'line_subtotal'     => $order_item->get_subtotal(),
				'line_subtotal_tax' => $order_item->get_subtotal_tax(),
				'line_total'        => $order_item->get_total(),
				'line_tax'          => $order_item->get_total_tax(),
				'line_tax_data'     => $order_item->get_taxes(),

				'order_total'             => $subscription_total,
				'subscription_total'      => $subscription_total,
				'order_tax'               => $order_item->get_total_tax(),
				'order_subtotal'          => (float) $order_item->get_total() + (float) $order_item->get_total_tax(),
				'prices_include_tax'      => $order->get_prices_include_tax(),
				'order_shipping'          => $order_shipping,
				'order_shipping_tax'      => $order_shipping_tax,
				'subscriptions_shippings' => $shippings,

				'payment_method'       => $order->get_payment_method(),
				'payment_method_title' => $order->get_payment_method_title(),

				'payment_due_date'  => $next_payment_due_date,
				'order_currency'    => $order->get_currency(),
				'user_id'           => $order->get_customer_id(),
				'price_is_per'      => $price_is_per,
				'price_time_option' => $product->get_meta( '_ywsbs_price_time_option' ),
				'max_length'        => $max_length,
				'trial_per'         => $trial_per,
				'trial_time_option' => ! empty( $trial_per ) ? $product->get_meta( '_ywsbs_trial_time_option' ) : '',
				'num_of_rates'      => ( $max_length && $price_is_per ) ? $max_length / $price_is_per : '',
				'ywsbs_version'     => YITH_YWSBS_VERSION,
				'created_via'       => 'backend',
			);

			$subscription_info = array(
				'price_is_per'          => $price_is_per,
				'price_time_option'     => $args['price_time_option'],
				'max_length'            => $args['max_length'],
				'trial_per'             => $args['trial_per'],
				'trial_time_option'     => $args['trial_time_option'],
				'next_payment_due_date' => $next_payment_due_date,
				'order_total'           => $subscription_total,
				'sync'                  => YWSBS_Subscription_Synchronization()->is_synchronizable( $product ),
			);
			wc_add_order_item_meta( $order_item->get_id(), '_subscription_info', $subscription_info, true );

			$has_delivered_scheduled = YWSBS_Subscription_Delivery_Schedules()->has_delivery_scheduled( $product );

			if ( $has_delivered_scheduled ) {
				$args['delivery_schedules'] = YWSBS_Subscription_Delivery_Schedules()->get_delivery_settings( $product );
			}

			$subscription = new YWSBS_Subscription( '', array_filter( $args ) );

			$subscription_id = $subscription->get_id();

			// save the version of plugin in the order.
			$order_args                         = array();
			$order_args['_ywsbs_order_version'] = YITH_YWSBS_VERSION;
			$subscriptions                      = (array) $order->get_meta( 'subscriptions' );
			if ( $subscription_id ) {
				$subscriptions[]             = $subscription_id;
				$order_args['subscriptions'] = array_filter( $subscriptions );

				wc_add_order_item_meta( $order_item->get_id(), '_subscription_id', $subscription_id, true );

				yith_subscription_log( 'Created via backend a new subscription ' . $subscription_id . ' for order: ' . $order->get_id() );

				do_action( 'ywsbs_subscription_created', $subscription_id );
				// translators: Placeholders: url of subscription, subscription number.
				$order->add_order_note( sprintf( _x( 'A new subscription <a href="%1$s">%2$s</a> has been created from this order', 'Placeholders: url of subscription, ID subscription', 'yith-woocommerce-subscription' ), admin_url( 'post.php?post=' . $subscription_id . '&action=edit' ), $subscription->get_number() ) );

			}

			if ( ! empty( $order_args ) ) {
				foreach ( $order_args as $key => $value ) {
					$order->update_meta_data( $key, $value );
				}
				$order->save();
			}

		}

		/**
		 * Return the list of subscription items
		 */
		/**
		 * Save the options of subscription in an array with order item id
		 *
		 * @access public
		 *
		 * @param int                   $item_id  Order item id.
		 * @param WC_Order_Item_Product $item     Order Item object.
		 * @param int                   $order_id Order id.
		 *
		 * @return void
		 */
		public function add_subscription_order_item_meta( $item_id, $item, $order_id ) {
			if ( isset( $item->legacy_cart_item_key ) ) {
				$this->cart_item_order_item[ $item->legacy_cart_item_key ] = $item_id;
			}
		}

		/**
		 * Save some info if a subscription is in the cart
		 *
		 * @access public
		 *
		 * @param int   $order_id Order id.
		 * @param array $posted   Post variable.
		 *
		 * @throws Exception Trigger error.
		 */
		public function get_extra_subscription_meta( $order_id, $posted ) {

			if ( ! YWSBS_Subscription_Cart::cart_has_subscriptions() || isset( $_REQUEST['cancel_order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$this->actual_cart      = WC()->session->get( 'cart' );
			$this->current_order_id = $order_id;

			add_filter( 'ywsbs_price_check', '__return_false' );
			remove_action( 'woocommerce_before_calculate_totals', array( YWSBS_Subscription_Cart(), 'add_change_prices_filter' ), 10 );
			remove_action( 'woocommerce_before_calculate_totals', array( YWSBS_Subscription_Cart(), 'before_calculate_totals' ), 200 );
			remove_action( 'woocommerce_before_checkout_process', array( YWSBS_Subscription_Cart(), 'sync_on_process_checkout' ), 200 );
			remove_filter( 'woocommerce_product_needs_shipping', array( YWSBS_Subscription_Cart(), 'maybe_not_shippable' ), 100 );
			remove_action( 'woocommerce_cart_needs_shipping', '__return_false', 200 );
			remove_filter( 'woocommerce_calculated_total', array( YWSBS_Subscription_Cart(), 'remove_shipping_cost_from_calculate_totals' ), 200 );
			remove_filter( 'woocommerce_cart_tax_totals', array( $this, 'remove_tax_shipping_cost_from_calculate_totals' ), 200 );

			WC()->cart->calculate_totals();
			$applied_coupons = WC()->cart->get_applied_coupons();

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				if ( ywsbs_is_subscription_product( $cart_item['data'] ) && isset( $cart_item['ywsbs-subscription-info'] ) ) {
					$this->collect_subscription_meta( $cart_item_key, $cart_item, $posted, $applied_coupons );
				}
			}
		}

		/**
		 * Collect subscription info for each subscription product on cart.
		 *
		 * @param string $cart_item_key   Cart item key.
		 * @param array  $cart_item       Cart item.
		 * @param array  $posted          $_POST variable.
		 * @param array  $applied_coupons Coupons applied to the cart.
		 *
		 * @throws Exception Trigger an error.
		 */
		private function collect_subscription_meta( $cart_item_key, $cart_item, $posted, $applied_coupons ) {

			$product            = $cart_item['data'];
			$product_id         = $product->get_id();
			$one_time_shippable = YWSBS_Subscription_Helper::is_one_time_shippable( $product );

			$subscription_info = array(
				'shipping'             => array(),
				'taxes'                => array(),
				'payment_method'       => '',
				'payment_method_title' => '',
			);

			$subscription_info = array_merge( $cart_item['ywsbs-subscription-info'], $subscription_info );

			// set the resubscribe subscription.
			$subscription_info['parent_subscription'] = isset( $cart_item['ywsbs-subscription-resubscribe'] ) ? $cart_item['ywsbs-subscription-resubscribe']['subscription_id'] : '';

			if ( isset( $cart_item['ywsbs-subscription-switch'] ) ) {
				$subscription_info['switched_from'] = $cart_item['ywsbs-subscription-switch']['subscription_id'];
			}

			// create new cart for this subscription.
			$new_cart = new WC_Cart();

			if ( defined( 'YITH_PAYPAL_PAYMENTS_VERSION' ) ) {
				$paypal_shipping = WC()->session->get( 'paypal_shipping_address', false );
				$paypal_billing  = WC()->session->get( 'paypal_billing_address', false );
				$ppwc            = WC()->session->get( 'paypal_order_id', false );
			}

			if ( 'yes' === $one_time_shippable ) {
				add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
			}

			yith_subscription_log( 'A subscription product ( with product id:' . $product_id . ' ) has been found during the creation of the order with id ' . $this->current_order_id );

			// set the variation id.
			if ( isset( $cart_item['variation'] ) ) {
				$subscription_info['variation'] = $cart_item['variation'];
			}

			// set payment method.
			if ( isset( $posted['payment_method'] ) && $posted['payment_method'] ) {
				$enabled_gateways = WC()->payment_gateways()->get_available_payment_gateways();

				if ( isset( $enabled_gateways[ $posted['payment_method'] ] ) ) {
					$payment_method = $enabled_gateways[ $posted['payment_method'] ];
					$payment_method->validate_fields();
					$subscription_info['payment_method']       = $payment_method->id;
					$subscription_info['payment_method_title'] = $payment_method->get_title();
				}
			}

			// Start simulation cart with a single subscription product.
			do_action( 'ywsbs_before_add_to_cart_subscription', $cart_item );
			add_filter( 'woocommerce_is_sold_individually', '__return_false', 200 );
			remove_filter( 'woocommerce_add_cart_item_data', array( YWSBS_Subscription_Cart(), 'set_subscription_meta_on_cart' ), 20 );
			remove_filter( 'woocommerce_is_purchasable', array( 'YITH_WC_Subscription_Limit', 'is_purchasable' ), 10 );
			$new_cart_item_key = $new_cart->add_to_cart(
				$cart_item['product_id'],
				$cart_item['quantity'],
				( isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : '' ),
				( isset( $cart_item['variation'] ) ? $cart_item['variation'] : '' ),
				$cart_item
			);

			do_action( 'ywsbs_after_add_to_cart_subscription', $cart_item );
			remove_filter( 'woocommerce_is_sold_individually', '__return_false', 200 );

			$new_cart = apply_filters( 'ywsbs_add_cart_item_data', $new_cart, $new_cart_item_key, $cart_item );

			// set the same subscription product price.
			$current_price = isset( $subscription_info['recurring_price'] ) ? $subscription_info['recurring_price'] : $product->get_price();

			$new_cart->cart_contents[ $new_cart_item_key ]['data']->set_price( $current_price );
			$new_cart_item_keys     = array_keys( $new_cart->cart_contents );
			$ywsbs_shipping_methods = WC()->session->get( 'ywsbs_shipping_methods' );
			foreach ( $new_cart_item_keys as $new_cart_item_key ) {

				// Get the shipping method for this subscription product.
				if ( $new_cart->needs_shipping() && $product->needs_shipping() ) {

					if ( method_exists( WC()->shipping, 'get_packages' ) ) {
						$packages = WC()->shipping->get_packages();

						foreach ( $packages as $key => $package ) {
							if ( isset( $package['rates'][ $ywsbs_shipping_methods[ $key ] ] ) ) {
								if ( isset( $package['contents'][ $cart_item_key ] ) || isset( $package['contents'][ $new_cart_item_key ] ) ) {
									// This shipping method has the current subscription.
									$shipping['method']      = $ywsbs_shipping_methods[ $key ];
									$shipping['destination'] = $package['destination'];

									break;
								}
							}
						}

						if ( isset( $shipping ) ) {
							// Get packages based on renewal order details.
							$new_packages = apply_filters(
								'woocommerce_cart_shipping_packages',
								array(
									0 => array(
										'contents'        => $new_cart->get_cart(),
										'contents_cost'   => isset( $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] ) ? $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] : 0,
										'applied_coupons' => $new_cart->applied_coupons,
										'destination'     => $shipping['destination'],
									),
								)
							);

							$save_temp_session_values = array(
								'shipping_method_counts'  => WC()->session->get( 'shipping_method_counts' ),
								'chosen_shipping_methods' => WC()->session->get( 'chosen_shipping_methods' ),
							);

							WC()->session->set( 'chosen_shipping_methods', array( $shipping['method'] ) );

							add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'change_shipping_chosen_method_temp' ) );
							$this->subscription_shipping_method_temp = $shipping['method'];

							WC()->shipping->calculate_shipping( $new_packages );

							remove_filter( 'woocommerce_shipping_chosen_method', array( $this, 'change_shipping_chosen_method_temp' ) );

							unset( $this->subscription_shipping_method_temp );
						}
					}
				}

				foreach ( $applied_coupons as $coupon_code ) {
					$coupon         = new WC_Coupon( $coupon_code );
					$coupon_type    = $coupon->get_discount_type();
					$coupon_amount  = $coupon->get_amount();
					$valid          = ywsbs_coupon_is_valid( $coupon, WC()->cart, $product );
					$limited        = $coupon->get_meta( 'ywsbs_limited_for_payments' );
					$is_trial       = ( ! empty( $subscription_info['trial_per'] ) && $subscription_info['trial_per'] > 0 );
					$limit_is_valid = empty( $limited ) || $limited > 1 || $is_trial || 0 === $product->get_price();
					if ( $valid && $limit_is_valid && in_array( $coupon_type, array( 'recurring_percent', 'recurring_fixed' ), true ) ) {

						$price               = $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal'];
						$price_tax           = $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal_tax'];
						$discount_amount     = 0;
						$discount_amount_tax = 0;

						switch ( $coupon_type ) {
							case 'recurring_percent':
								$discount_amount     = round( ( $price / 100 ) * $coupon_amount, WC()->cart->dp );
								$discount_amount_tax = round( ( $price_tax / 100 ) * $coupon_amount, WC()->cart->dp );
								break;
							case 'recurring_fixed':
								$discount_amount     = ( $price < $coupon_amount ) ? $price : $coupon_amount;
								$discount_amount_tax = 0;
								break;
						}

						$subscription_info['coupons'][] = array(
							'coupon_code'         => $coupon_code,
							'coupon_type'         => $coupon_type,
							'coupon_amount'       => $coupon_amount,
							'discount_amount'     => $discount_amount * $cart_item['quantity'],
							'discount_amount_tax' => $discount_amount_tax * $cart_item['quantity'],
							'limited'             => $limited,
							'used'                => ( $is_trial || 0 === $product->get_price() ) ? 0 : 1,
						);

						$new_cart->applied_coupons[]   = $coupon_code;
						$new_cart->coupon_subscription = true;

					}
				}

				if ( ! empty( $new_cart->applied_coupons ) ) {
					WC()->cart->discount_cart       = 0;
					WC()->cart->discount_cart_tax   = 0;
					WC()->cart->subscription_coupon = 1;
				}

				$new_cart->calculate_totals();
				// Recalculate totals.
				// save some order settings.

				$subscription_info['order_shipping']     = wc_format_decimal( $new_cart->shipping_total );
				$subscription_info['order_shipping_tax'] = wc_format_decimal( $new_cart->shipping_tax_total );
				$subscription_info['cart_discount']      = wc_format_decimal( $new_cart->get_cart_discount_total() );
				$subscription_info['cart_discount_tax']  = wc_format_decimal( $new_cart->get_cart_discount_tax_total() );
				$subscription_info['order_discount']     = $new_cart->get_total_discount();
				$subscription_info['order_tax']          = wc_format_decimal( $new_cart->tax_total );
				$subscription_info['order_subtotal']     = wc_format_decimal( $new_cart->subtotal, get_option( 'woocommerce_price_num_decimals' ) );
				$subscription_info['order_total']        = wc_format_decimal( $new_cart->total, get_option( 'woocommerce_price_num_decimals' ) );
				$subscription_info['line_subtotal']      = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal'] );
				$subscription_info['line_subtotal_tax']  = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal_tax'] );
				$subscription_info['line_total']         = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] );
				$subscription_info['line_tax']           = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_tax'] );
				$subscription_info['line_tax_data']      = $new_cart->cart_contents[ $new_cart_item_key ]['line_tax_data'];
			}

			// Get shipping details.
			if ( $product->needs_shipping() && 'yes' !== $one_time_shippable ) {
				if ( isset( $shipping['method'] ) ) {
					$method = null;
					foreach ( WC()->shipping->packages as $i => $package ) {
						if ( isset( $package['rates'][ $shipping['method'] ] ) ) {
							$method = $package['rates'][ $shipping['method'] ];
							break;
						}
					}

					if ( ! is_null( $method ) ) {
						$subscription_info['shipping'] = array(
							'name'      => $method->label,
							'method_id' => $method->id,
							'cost'      => wc_format_decimal( $method->cost ),
							'taxes'     => $method->taxes,
						);

						// Set session variables to original values and recalculate shipping for original order which is being processed now.
						isset( $save_temp_session_values['shipping_method_counts'] ) && WC()->session->set( 'shipping_method_counts', $save_temp_session_values['shipping_method_counts'] );
						isset( $save_temp_session_values['chosen_shipping_methods'] ) && WC()->session->set( 'chosen_shipping_methods', $save_temp_session_values['chosen_shipping_methods'] );
						WC()->shipping->calculate_shipping( WC()->shipping->packages );
					}
				}
			}

			// CALCULATE TAXES.
			$taxes          = $new_cart->get_cart_contents_taxes();
			$shipping_taxes = $new_cart->get_shipping_taxes();

			foreach ( $new_cart->get_tax_totals() as $rate_key => $rate ) {

				$rate_args = array(
					'name'     => $rate_key,
					'rate_id'  => $rate->tax_rate_id,
					'label'    => $rate->label,
					'compound' => absint( $rate->is_compound ? 1 : 0 ),

				);

				$rate_args['tax_amount']          = wc_format_decimal( isset( $taxes[ $rate->tax_rate_id ] ) ? $taxes[ $rate->tax_rate_id ] : 0 );
				$rate_args['shipping_tax_amount'] = wc_format_decimal( isset( $shipping_taxes[ $rate->tax_rate_id ] ) ? $shipping_taxes[ $rate->tax_rate_id ] : 0 );

				$subscription_info['taxes'][] = $rate_args;
			}

			if ( isset( $this->cart_item_order_item[ $cart_item_key ] ) ) {
				$order_item_id                                       = $this->cart_item_order_item[ $cart_item_key ];
				$this->subscriptions_info['order'][ $order_item_id ] = $subscription_info;
				wc_add_order_item_meta( $order_item_id, '_subscription_info', $subscription_info, true );
			}

			$new_cart->empty_cart( true );
			WC()->cart->empty_cart( true );
			WC()->session->set( 'cart', $this->actual_cart );

			if ( defined( 'YITH_PAYPAL_PAYMENTS_VERSION' ) ) {
				WC()->session->set( 'paypal_order_id', $ppwc );
				WC()->session->set( 'paypal_shipping_address', $paypal_shipping );
				WC()->session->set( 'paypal_billing_address', $paypal_billing );
			}
			WC()->cart->get_cart_from_session();
			WC()->cart->set_session();

		}

		/**
		 * Check in the order if there's a subscription and create it
		 *
		 * @param int   $order_id Order ID.
		 * @param array $posted   $_POST variable.
		 *
		 * @return void
		 * @throws Exception Trigger an error.
		 */
		public function check_order_for_subscription( $order_id, $posted ) {

			$order          = wc_get_order( $order_id );
			$order_items    = $order->get_items();
			$order_args     = array();
			$user_id        = $order->get_customer_id();
			$order_currency = $order->get_currency();

			// check id the the subscriptions are created.
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( empty( $order_items ) || ! empty( $subscriptions ) ) {
				return;
			}

			$subscriptions = is_array( $subscriptions ) ? $subscriptions : array();

			$order_has_subscription = apply_filters( 'ywsbs_force_order_has_subscriptions', false );

			foreach ( $order_items as $key => $order_item ) {

				/**
				 * WC_Product
				 *
				 * @var $_product
				 */

				$product = $order_item->get_product();

				if ( ! $product || ! isset( $this->subscriptions_info['order'][ $key ] ) ) {
					continue;
				}

				$product_id = $product->get_id();

				if ( ! ywsbs_is_subscription_product( $product_id ) ) {
					continue;
				}

				$order_has_subscription = true;
				$subscription_info      = $this->subscriptions_info['order'][ $key ];

				$price_is_per = $subscription_info['price_is_per'];
				$fee          = $subscription_info['fee'];
				$max_length   = $subscription_info['max_length'];

				if ( $fee ) {
					$order_item->add_meta_data( '_fee', $fee );
				}

				// Fill the array for subscription creation.
				$args = array(
					'product_id'    => $order_item['product_id'],
					'variation_id'  => $order_item['variation_id'],
					'variation'     => ( isset( $subscription_info['variation'] ) ? $subscription_info['variation'] : '' ),
					'product_name'  => $order_item['name'],
					'quantity'      => $order_item['qty'],

					// order details.
					'order_id'      => $order_id,
					'order_item_id' => $key,
					'order_ids'     => array( $order_id ),

					'line_subtotal'     => $subscription_info['line_subtotal'],
					'line_total'        => $subscription_info['line_total'],
					'line_subtotal_tax' => $subscription_info['line_subtotal_tax'],
					'line_tax'          => $subscription_info['line_tax'],
					'line_tax_data'     => $subscription_info['line_tax_data'],

					'cart_discount'     => $subscription_info['cart_discount'],
					'cart_discount_tax' => $subscription_info['cart_discount_tax'],
					'coupons'           => ( isset( $subscription_info['coupons'] ) ) ? $subscription_info['coupons'] : '',

					'order_total'        => $subscription_info['order_total'],
					'subscription_total' => $subscription_info['order_total'],
					'order_tax'          => $subscription_info['order_tax'],
					'order_subtotal'     => $subscription_info['order_subtotal'],
					'order_discount'     => $subscription_info['order_discount'],
					'prices_include_tax' => $order->get_meta( 'prices_include_tax' ),

					'order_shipping'          => $subscription_info['order_shipping'],
					'order_shipping_tax'      => $subscription_info['order_shipping_tax'],
					'subscriptions_shippings' => $subscription_info['shipping'],

					'payment_method'       => $subscription_info['payment_method'],
					'payment_method_title' => $subscription_info['payment_method_title'],

					'payment_due_date'    => $subscription_info['next_payment_due_date'],
					'order_currency'      => $order_currency,

					// user details.
					'user_id'             => $user_id,

					// item subscription detail.
					'price_is_per'        => $price_is_per,
					'price_time_option'   => $subscription_info['price_time_option'],
					'max_length'          => $max_length,
					'trial_per'           => $subscription_info['trial_per'],
					'trial_time_option'   => $subscription_info['trial_time_option'],
					'fee'                 => $fee,
					'num_of_rates'        => ( $max_length && $price_is_per ) ? $max_length / $price_is_per : '',
					'parent_subscription' => $subscription_info['parent_subscription'],

				);

				if ( ! empty( $subscription_info['switched_from'] ) ) {
					$args['switched_from'] = $subscription_info['switched_from'];
				}

				if ( ywsbs_scheduled_actions_enabled() ) {
					$args['ywsbs_version'] = YITH_YWSBS_VERSION;
				}

				$has_delivered_scheduled = YWSBS_Subscription_Delivery_Schedules()->has_delivery_scheduled( $product );

				if ( $has_delivered_scheduled ) {
					$args['delivery_schedules'] = YWSBS_Subscription_Delivery_Schedules()->get_delivery_settings( $product );
				}

				$subscription    = new YWSBS_Subscription( '', array_filter( $args ) );
				$subscription_id = $subscription->get_id();

				// save the version of plugin in the order.
				$order_args['_ywsbs_order_version'] = YITH_YWSBS_VERSION;

				if ( ! empty( $subscription_info['parent_subscription'] ) ) {
					$order_args['_parent_subscription'] = $subscription_info['parent_subscription'];
				}

				if ( $subscription_id ) {
					$subscriptions[]             = $subscription_id;
					$order_args['subscriptions'] = $subscriptions;

					wc_add_order_item_meta( $key, '_subscription_id', $subscription_id, true );

					yith_subscription_log( 'Created a new subscription ' . $subscription_id . ' for order: ' . $order_id );
					YWSBS_Subscription_User::delete_user_cache( $user_id );

					do_action( 'ywsbs_subscription_created', $subscription_id );
					// translators: Placeholders: url of subscription, subscription number.
					$order->add_order_note( sprintf( _x( 'A new subscription <a href="%1$s">%2$s</a> has been created from this order', 'Placeholders: url of subscription, ID subscription', 'yith-woocommerce-subscription' ), admin_url( 'post.php?post=' . $subscription_id . '&action=edit' ), $subscription->get_number() ) );

				}
			}

			if ( ! empty( $order_args ) ) {
				foreach ( $order_args as $key => $value ) {
					$order->update_meta_data( $key, $value );
				}
				$order->save();
				if ( apply_filters( 'ywsbs_calculate_order_totals_condition', true ) ) {
					$order->calculate_totals();
					WC()->session->set( 'ywsbs_order_args', $order_args );
				}
			}

			if ( $order_has_subscription ) {
				do_action( 'ywcsb_after_calculate_totals', $order );
			}

		}

		/**
		 * Remove subscription from Order if it is resumed.
		 *
		 * @param int $order_id Order ID.
		 */
		public function remove_subscription_from_order( $order_id ) {
			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( ! $subscriptions ) {
				return;
			}

			foreach ( $subscriptions as $subscription_id ) {
				$subscription = ywsbs_get_subscription( $subscription_id );
				$subscription->delete();
				// translators: Placeholder subscription id.
				$order->add_order_note( sprintf( esc_html_x( 'The subscription %s created from this orders has been cancelled because the order item was cancelled', 'subscription id', 'yith-woocommerce-subscription' ), $subscription_id ) );
			}

			$order->delete_meta_data( 'subscriptions' );
			$order->save();
		}

		/**
		 * Overwrite chosen shipping method temp for calculate the subscription shipping
		 *
		 * @param string $method Shipping method.
		 *
		 * @return string
		 */
		public function change_shipping_chosen_method_temp( $method ) {
			return isset( $this->subscription_shipping_method_temp ) ? $this->subscription_shipping_method_temp : $method;
		}

		/**
		 * Actives a subscription after a payment is done
		 *
		 * @param int $order_id Order id.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function payment_complete( $order_id ) {

			if ( isset( $this->payment_done[ $order_id ] ) ) {
				return;
			}

			$order_id = apply_filters( 'ywsbs_order_id_on_payment_complete', $order_id );

			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {
				! defined( 'YITH_DOING_RENEWS' ) && define( 'YITH_DOING_RENEWS', true );

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					// check if the subscription exists.
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$payed_order = is_array( $subscription->get( 'payed_order_list' ) ) ? $subscription->get( 'payed_order_list' ) : array();
					if ( ! in_array( $order_id, $payed_order ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
						$renew_order = $subscription->get_renew_order_id();
						if ( empty( $renew_order ) ) {
							$subscription->start( $order_id );
						} elseif ( ! empty( $renew_order ) && $renew_order == $order_id ) { // phpcs:ignore
							$subscription->update( $order_id );
						}
					}

					$product_id = $subscription->get_variation_id() ? $subscription->get_variation_id() : $subscription->get_product_id();
					delete_user_meta( $subscription->get_user_id(), 'ywsbs_trial_' . $product_id );

					do_action( 'ywsbs_subscription_payment_complete', $subscription, $order );
				}
			}

			$this->payment_done[ $order_id ] = true;
		}

		/**
		 * Delete all subscription if the main order in deleted.
		 *
		 * @param int $order_id Order id.
		 */
		public static function delete_subscriptions( $order_id ) {
			if ( 'shop_order' === get_post_type( $order_id ) ) {

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}

				$is_a_renew    = $order->get_meta( 'is_a_renew' );
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( empty( $subscriptions ) || 'yes' === $is_a_renew ) {
					return;
				}

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					// check if the subscription exists.
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$subscription->delete();
				}
			}
		}

		/**
		 * Trash all subscriptions if the main order in trashed.
		 *
		 * @param int $order_id Order id.
		 */
		public static function trash_subscriptions( $order_id ) {
			if ( 'shop_order' === get_post_type( $order_id ) ) {

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}

				$is_a_renew    = $order->get_meta( 'is_a_renew' );
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( empty( $subscriptions ) || 'yes' === $is_a_renew ) {
					return;
				}

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					// check if the subscription exists.
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$subscription->delete();
				}
			}
		}

		/**
		 * Cancel all subscriptions if the main order in cancelled.
		 *
		 * @param int $order_id Order id.
		 */
		public static function cancel_subscriptions( $order_id ) {
			if ( 'shop_order' === get_post_type( $order_id ) ) {

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}

				$is_a_renew    = $order->get_meta( 'is_a_renew' );
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( empty( $subscriptions ) || 'yes' === $is_a_renew ) {
					return;
				}

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					// check if the subscription exists.
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$subscription->cancel( false );
				}
			}
		}

		/**
		 * Un-trash all subscriptions if the main order in untrashed.
		 *
		 * @param int $order_id Order id.
		 */
		public static function untrash_subscriptions( $order_id ) {
			if ( 'shop_order' === get_post_type( $order_id ) ) {

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}

				$is_a_renew    = $order->get_meta( 'is_a_renew' );
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( empty( $subscriptions ) || 'yes' === $is_a_renew ) {
					return;
				}

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					// check if the subscription exists.
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$subscription->untrash();
				}
			}
		}

		/**
		 * Cancel the subscription if the order is refunded.
		 *
		 * @param int $order_id  Order id.
		 * @param int $refund_id Refund id.
		 *
		 * @return bool
		 * @since  1.0.1
		 */
		public function order_refunded( $order_id, $refund_id = 0 ) {

			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );

					if ( is_null( $subscription ) ) {
						continue;
					}

					if ( $subscription->get( 'status' ) === 'cancelled' ) {
						$subscription->set( 'end_date', current_time( 'timestamp' ) ); // phpcs:ignore
						do_action( 'ywsbs_refund_subscription', $subscription );
					} else {
						// filter added to gateway payments.
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$order->add_order_note( esc_html__( 'The subscription cannot be cancelled.', 'yith-woocommerce-subscription' ) );

							return false;
						}
						$subscription->update_status( 'cancel-now', 'refund' );
					}
				}
			}
		}

		/**
		 * Re-Active the subscription.
		 *
		 * @param int $order_id Order id.
		 */
		public function reactive_subscription( $order_id ) {

			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					if ( is_null( $subscription ) ) {
						continue;
					}

					$status = $subscription->get( 'status' );
					if ( ! in_array( $status, array( 'cancelled', 'trial' ), true ) ) {
						$subscription->update_status( 'active', 'resumed' );
					}
				}
			}
		}

		/**
		 * If there's a subscription inside the order, even if the order total is $0, it still needs payment
		 *
		 * @param bool     $needs_payment        Bool.
		 * @param WC_Order $order                Order.
		 * @param array    $valid_order_statuses Valid order status list.
		 *
		 * @return bool
		 *
		 * @since 1.0.0
		 */
		public function order_need_payment( $needs_payment, $order, $valid_order_statuses ) {

			if ( ! $needs_payment && YWSBS_Subscription_Cart::cart_has_subscriptions() && in_array( $order->get_status(), $valid_order_statuses, true ) && 0 === $order->get_total() ) {
				return true;
			}

			return $needs_payment;
		}

		/**
		 * Return false if the option reduce order stock is disabled for the renew order
		 *
		 * @param bool     $result Current filter value.
		 * @param WC_Order $order  Order.
		 *
		 * @return bool
		 * @since  1.2.6
		 */
		public function can_reduce_order_stock( $result, $order ) {
			$is_a_renew = $order->get_meta( 'is_a_renew' );

			if ( 'yes' === get_option( 'ywsbs_disable_the_reduction_of_order_stock_in_renew' ) && 'yes' === $is_a_renew ) {
				$result = false;
			}

			return $result;
		}

		/**
		 * Add the action Renew now on order list
		 *
		 * @param array    $actions List of action.
		 * @param WC_Order $order   Order.
		 *
		 * @return array
		 */
		public function add_renew_subscription_manually( $actions, $order ) {
			if ( apply_filters( 'ywsbs_renew_now_order_action', ( 'yes' === $order->get_meta( 'is_a_renew' ) && ywsbs_check_renew_order_before_pay( $order ) && $order->get_meta( 'failed_attemps' ) > 0 ), $order ) ) {
				$actions['renew_now'] = array(
					'url'  => add_query_arg(
						array(
							'renew_order' => $order->get_id(),
							'_nonce'      => wp_create_nonce( 'ywsbs-renew_order-' . $order->get_id() ),
						),
						wc_get_page_permalink( 'myaccount' )
					),
					'name' => __( 'Renew Now', 'yith-woocommerce-subscription' ),
				);
			}

			return $actions;

		}


		/**
		 * Return the renew order status.
		 *
		 * @param null $subscription Subscription.
		 *
		 * @return string
		 */
		public function get_renew_order_status( $subscription = null ) {

			$new_status = 'pending';

			if ( ! is_null( $subscription ) && WC()->payment_gateways() ) {
				$gateway = ywsbs_get_payment_gateway_by_subscription( $subscription );

				if ( $gateway && $gateway->supports( 'yith_subscriptions' ) ) {
					$new_status = 'on-hold';
				}
			}

			return apply_filters( 'ywsbs_renew_order_status', $new_status, $subscription );
		}

		/**
		 * Create a new order for next payments of a subscription
		 *
		 * @param int $subscription_id Subscription id.
		 *
		 * @return int
		 * @throws Exception Trigger an error.
		 *
		 * @since 1.0.0
		 */
		public function renew_order( $subscription_id ) {

			yith_subscription_log( 'Creating renew order for the subscription #' . $subscription_id, 'subscription_payment' );

			$subscription   = ywsbs_get_subscription( $subscription_id );
			$parent_order   = $subscription->get_order();
			$status         = $this->get_renew_order_status( $subscription );
			$renew_order_id = $subscription->can_be_create_a_renew_order();

			if ( ! $renew_order_id ) {
				yith_subscription_log( 'The renew order for subscription #' . $subscription_id . ' cannot be created', 'subscription_payment' );

				return false;
			}

			$indentation = '----';
			$message     = $indentation . ' Original order id ' . $parent_order->get_id();
			yith_subscription_log( 'Here the subscription data:', 'subscription_payment' );
			yith_subscription_log( $message, 'subscription_payment' );

			$message = $indentation . ' the renew order must have the status ' . $status;
			yith_subscription_log( $message, 'subscription_payment' );

			if ( true === $renew_order_id ) {
				$message = $indentation . ' the renew order not exist, create!';
				yith_subscription_log( $message, 'subscription_payment' );
			} else {
				$message = $indentation . ' the renew order exist and is ' . $renew_order_id;
				yith_subscription_log( $message, 'subscription_payment' );
				$renew_order = wc_get_order( $renew_order_id );
				if ( $renew_order ) {
					$message .= $indentation . $indentation . ' ' . $renew_order->get_formatted_billing_address() . '\n';
					yith_subscription_log( $message, 'subscription_payment' );

					return $renew_order_id;
				} else {
					$message = $indentation . ' the renew order not exist, create!';
					yith_subscription_log( $message, 'subscription_payment' );
				}
			}

			if ( ! $parent_order ) {
				$message = $indentation . ' the renew order cannot created because the parent order not exist';
				yith_subscription_log( $message, 'subscription_payment' );

				$subscription->cancel();

				return false;
			}

			if ( apply_filters( 'ywsbs_skip_create_renew_order', false, $subscription ) ) {
				$message = $indentation . ' the renew order cannot created because has been added a filter to skip';
				yith_subscription_log( $message, 'subscription_payment' );

				return false;
			}

			$order_args = array(
				'status'      => 'renew',
				'customer_id' => $subscription->get_user_id(),
			);
			$order      = wc_create_order( $order_args );

			$message = $indentation . 'the customer with subscription #' . $subscription_id . ' has this user id #' . $subscription->get( 'user_id' );
			yith_subscription_log( $message, 'subscription_payment' );
			$message = $indentation . ' the renew order created is #' . $order->get_id() . ' the customer';
			yith_subscription_log( $message, 'subscription_payment' );

			$args = array(
				'subscriptions'        => array( $subscription_id ),
				'payment_method'       => $subscription->get( 'payment_method' ),
				'payment_method_title' => $subscription->get( 'payment_method_title' ),
				'currency'             => $subscription->get_order_currency(),
				'failed_attemps'       => 0,
				'next_payment_attempt' => 0,
			);

			$customer_note = $parent_order->get_customer_note();
			if ( $customer_note ) {
				$args['customer_note'] = $customer_note;
			}

			$message = $indentation . 'Check the billing an shipping info';
			yith_subscription_log( $message, 'subscription_payment' );

			// get billing.
			$billing_fields = $subscription->get_address_fields( 'billing' );
			// get shipping.
			$shipping_fields = $subscription->get_address_fields( 'shipping' );

			$args = array_merge( $args, $shipping_fields, $billing_fields );

			foreach ( $args as $key => $field ) {
				$set     = 'set_' . $key;
				$message = $indentation . ' ' . $set . '\n';
				yith_subscription_log( $message, 'subscription_payment' );
				if ( method_exists( $order, $set ) ) {
					$message = $indentation . $indentation . ' ' . $field;
					yith_subscription_log( $message, 'subscription_payment' );
					$order->$set( $field );
				} else {
					$key = in_array( $key, apply_filters( 'yith_ywsbs_renew_order_custom_fields', array( 'billing_vat', 'billing_ssn' ), $order, $args ), true ) ? '_' . $key : $key;
					$order->update_meta_data( $key, $field );
					$message = $indentation . $indentation . ' ' . $key . ' ' . print_r( $field, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					yith_subscription_log( $message, 'subscription_payment' );
				}
			}

			// check if is necessary remove limited coupons.
			if ( ! empty( $subscription->get( 'coupons' ) ) ) {
				foreach ( $subscription->get( 'coupons' ) as $coupon ) {
					if ( isset( $coupon['limited'], $coupon['used'] ) && ( 0 < $coupon['limited'] && $coupon['limited'] <= $coupon['used'] ) ) {
						try {
							YWSBS_Subscription_Coupons()->remove_coupon_from_subscription( $subscription, $coupon['coupon_code'] );
						} catch ( Exception $e ) {
							continue;
						}
					}
				}
			}

			$order_id = $order->get_id();
			$_product = $subscription->get_product();

			$args = array(
				'variation' => array(),
				'totals'    => array(
					'subtotal'     => $subscription->get_line_subtotal(),
					'subtotal_tax' => $subscription->get_line_subtotal_tax(),
					'total'        => $subscription->get_line_total(),
					'tax'          => $subscription->get_line_tax(),
					'tax_data'     => $subscription->get_line_tax_data(),
				),
			);

			$item_id = $order->add_product( $_product, $subscription->get( 'quantity' ), $args );

			if ( ! $item_id ) {
				// translators: Error code.
				throw new Exception( sprintf( esc_html_x( 'Error %d: unable to create the order. Please try again.', 'Error code', 'yith-woocommerce-subscription' ), 402 ) );
			} else {

				$metadata         = get_metadata( 'order_item', $subscription->get( 'order_item_id' ) );
				$metadata_to_skip = apply_filters( 'ywsbs_itemmeta_to_skip_in_renew_order', array( '_reduced_stock' ) );
				if ( $metadata ) {
					foreach ( $metadata as $key => $value ) {
						if ( in_array( $key, $metadata_to_skip, true ) ) {
							continue;
						}
						if ( apply_filters( 'ywsbs_renew_order_item_meta_data', is_array( $value ) && count( $value ) === 1 && '_fee' !== $key, $subscription->get( 'order_item_id' ), $key, $value ) ) {
							add_metadata( 'order_item', $item_id, $key, maybe_unserialize( $value[0] ), true );
						}
					}
				}
			}

			$shipping_cost = 0;

			// Shipping.
			if ( apply_filters( 'ywsbs_add_shipping_cost_order_renew', ! empty( $subscription->subscriptions_shippings ) ) ) {

				$shipping_item_id = wc_add_order_item(
					$order_id,
					array(
						'order_item_name' => $subscription->subscriptions_shippings['name'],
						'order_item_type' => 'shipping',
					)
				);

				$shipping_cost     = isset( $subscription->subscriptions_shippings['cost'] ) ? $subscription->subscriptions_shippings['cost'] : 0;
				$shipping_cost_tax = 0;

				if ( isset( $subscription->subscriptions_shippings['method_id'] ) ) {
					wc_add_order_item_meta( $shipping_item_id, 'method_id', $subscription->subscriptions_shippings['method_id'] );
				}

				wc_add_order_item_meta( $shipping_item_id, 'cost', wc_format_decimal( $shipping_cost ) );
				if ( isset( $subscription->subscriptions_shippings['taxes'] ) ) {
					wc_add_order_item_meta( $shipping_item_id, 'taxes', $subscription->subscriptions_shippings['taxes'] );
				}

				if ( ! empty( $subscription->subscriptions_shippings['taxes'] ) ) {
					foreach ( $subscription->subscriptions_shippings['taxes'] as $tax_cost ) {
						$shipping_cost_tax += $tax_cost;
					}
				}

				$order->set_shipping_total( $shipping_cost );
				$order->set_shipping_tax( $shipping_cost_tax );
				$order->save();

			} else {
				do_action( 'ywsbs_add_custom_shipping_costs', $order, $subscription );
			}

			$cart_discount_total     = 0;
			$cart_discount_total_tax = 0;

			// coupons.
			$coupons = $subscription->get( 'coupons' );
			if ( ! empty( $coupons ) ) {
				$saved_coupons = array();
				foreach ( $coupons as $coupon ) {
					if ( ! isset( $coupon['limited'], $coupon['used'] ) ||
					     0 === (int) $coupon['limited'] ||
					     ( $coupon['limited'] > 0 && $coupon['used'] < $coupon['limited'] ) ) {

						$item = new WC_Order_Item_Coupon();
						$item->set_props(
							array(
								'code'         => $coupon['coupon_code'],
								'discount'     => $coupon['discount_amount'],
								'discount_tax' => $coupon['discount_amount_tax'],
								'order_id'     => $order->get_id(),
							)
						);
						$item->save();
						$order->add_item( $item );

						$cart_discount_total     += $coupon['discount_amount'];
						$cart_discount_total_tax += $coupon['discount_amount_tax'];

						if ( isset( $coupon['used'] ) && $coupon['limited'] > 0 ) {

							$coupon['used'] = $coupon['used'] + 1;
							$remain         = $coupon['limited'] - $coupon['used'];
							// translators: placeholder: order id.
							YITH_WC_Activity()->add_activity( $subscription->get_id(), 'renew-order', 'success', $order_id, sprintf( esc_html_x( 'Limited level reduced for the coupon %1$s: %2$d ->%3$d ', 'placeholder: coupon code, started limited value, current limited value ', 'yith-woocommerce-subscription' ), $coupon['coupon_code'], $remain + 1, $remain ) );
						}
					}

					$saved_coupons[] = $coupon;
				}

				$subscription->set( 'coupons', $saved_coupons );
			}

			$order->set_discount_total( $cart_discount_total );

			if ( isset( $subscription->subscriptions_shippings['taxes'] ) && $subscription->subscriptions_shippings['taxes'] ) {
				/**
				 * This fix the shipping taxes removed form WC settings
				 * if in a previous tax there was the taxes this will be forced
				 * even if they are disabled for the shipping
				 */
				add_action( 'woocommerce_find_rates', array( $this, 'add_shipping_tax' ), 10 );
			}

			$order->update_meta_data( 'is_a_renew', 'yes' );
			$order->set_discount_total( $cart_discount_total );

			$order->update_taxes();

			$order->calculate_totals();

			$order->set_status( $status );
			$order->save();

			do_action( 'ywsbs_renew_order_saved', $order, $subscription );
			// translators: Placeholder: subscription url, subscription number.
			$order->add_order_note( sprintf( '%1$s <a href="%2$s">%3$s</a>', esc_html__( 'This order has been created to renew subscription', 'yith-woocommerce-subscription' ), admin_url( 'post.php?post=' . $subscription->id . '&action=edit' ), $subscription->get_number() ) );

			// attach the new order to the subscription.
			$orders = $subscription->get( 'order_ids' );
			array_push( $orders, $order_id );
			$subscription->set( 'order_ids', $orders );
			// translators: placeholder: order id.
			YITH_WC_Activity()->add_activity( $subscription->id, 'renew-order', 'success', $order_id, sprintf( esc_html_x( 'The order %d has been created for the subscription', 'placeholder: order id', 'yith-woocommerce-subscription' ), $order_id ) );

			$subscription->set( 'renew_order', $order_id );

			do_action( 'ywsbs_renew_subscription', $order_id, $subscription_id );

			return $order_id;

		}

		/**
		 * Delete current renew order.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function delete_current_renew_order( $subscription ) {
			$renew_order = $subscription->get( 'renew_order' );
			if ( ! empty( $renew_order ) ) {
				wp_delete_post( $renew_order );
				$subscription->set( 'renew_order', '' );
				// translators: placeholder: renew order id.
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'renew-order', 'success', $renew_order, sprintf( esc_html_x( 'The order %d has been deleted for the subscription', 'placeholder: order id', 'yith-woocommerce-subscription' ), $renew_order ) );
			}

		}


		/**
		 * This fix the shipping taxes removed form WC settings
		 * if in a previous tax there was the taxes this will be forced
		 * even if they are disabled for the shipping.
		 *
		 * @param array $shipping_taxes Shipping taxes.
		 *
		 * @return mixed
		 */
		public function add_shipping_tax( $shipping_taxes ) {

			foreach ( $shipping_taxes as &$shipping_tax ) {
				$shipping_tax['shipping'] = 'yes';

			}

			return $shipping_taxes;
		}

		/**
		 * Filters needs_payment for the order, to make renew payable when user try to manually pay for it
		 *
		 * @param bool     $needs_payment  Whether order needs payment.
		 * @param WC_Order $order          Order.
		 * @param array    $valid_statuses Array of valid order statuses for payment.
		 *
		 * @return bool Filtered version of needs payment.
		 */
		public function renew_needs_payment( $needs_payment, $order, $valid_statuses ) {

			if ( 'yes' !== $order->get_meta( 'is_a_renew', true ) ) {
				return $needs_payment;
			}

			if ( ! is_checkout_pay_page() ) {
				return $needs_payment;
			}

			if ( ! isset( $_GET['ywsbs_manual_renew'] ) || ! wp_verify_nonce( $_GET['ywsbs_manual_renew'], 'ywsbs_manual_renew' ) ) { //phpcs:ignore
				return $needs_payment;
			}

			if ( ! $order->has_status( YWSBS_Subscription_Order()->get_renew_order_status() ) ) {
				return $needs_payment;
			}

			return true;
		}


		/**
		 * Pay renew order
		 *
		 * @param int  $renew_order_id  Renew order id to pay.
		 * @param bool $is_manual_renew Bool.
		 *
		 * @return void
		 */
		public function pay_renew_order( $renew_order_id, $is_manual_renew = false ) {

			if ( 'yes' === get_option( 'ywsbs_site_staging', 'no' ) ) {
				yith_subscription_log( 'The renew order #' . $renew_order_id . ' is not paid because the site is in staging mode', 'subscription_payment' );

				return;
			}

			$renew_order = apply_filters( 'ywsbs_check_order_before_pay_renew_order', wc_get_order( $renew_order_id ) );

			if ( ! $renew_order ) {
				yith_subscription_log( 'The renew order #' . $renew_order_id . ' does not exists.', 'subscription_payment' );
				$is_manual_renew && wc_add_notice( __( 'There was an error with your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			if ( ! ywsbs_check_renew_order_before_pay( $renew_order ) || ! WC()->payment_gateways() ) {
				yith_subscription_log( 'The renew order #' . $renew_order_id . ' cannot be paid.', 'subscription_payment' );
				$is_manual_renew && wc_add_notice( __( 'The renew order cannot be paid. Please contact the administrator.', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			yith_subscription_log( 'Pay order #' . $renew_order_id, 'subscription_payment' );
			! defined( 'YITH_DOING_RENEWS' ) && define( 'YITH_DOING_RENEWS', true );
			if ( isset( WC()->cart ) && function_exists( 'YWSBS_Subscription_Cart' ) ) {
				remove_action( 'woocommerce_available_payment_gateways', array( YWSBS_Subscription_Cart(), 'disable_gateways' ), 100 );
			}

			$subscriptions = $renew_order->get_meta( 'subscriptions' );
			$subscription  = isset( $subscriptions[0] ) ? ywsbs_get_subscription( $subscriptions[0] ) : false;

			if ( $subscription && apply_filters( 'ywsbs_check_the_renew_order', true ) ) {
				$subscription->set( 'check_the_renew_order', time() + 10 * MINUTE_IN_SECONDS );
			}

			$gateway_id = $renew_order->get_payment_method();

			if ( 'stripe' === $gateway_id || 'stripe_sepa' === $gateway_id ) {
				$source_id          = $subscription->get( '_stripe_source_id' );
				$stripe_customer_id = $subscription->get( '_stripe_customer_id' );
				$source_object      = WC_Stripe_API::retrieve( 'sources/' . $source_id );

				if ( ( empty( $source_object ) || ( ! empty( $source_object ) && isset( $source_object->status ) && 'consumed' === $source_object->status ) ) ) {
					/**
					 * If the source status is "Consumed" this means that the customer has removed it from its account.
					 * So we search for the default source ID.
					 * If this ID is empty, this means that the customer has no credit card saved on the account so the payment will fail.
					 */
					$customer       = WC_Stripe_API::retrieve( "customers/$stripe_customer_id" );
					$default_source = $customer->default_source;
					if ( $default_source ) {
						$source_object = WC_Stripe_API::retrieve( 'sources/' . $default_source );
					}
				}

				$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
				if ( 'stripe' === $gateway_id && 'sepa_debit' === $source_object->type ) {
					$payment_method = isset( $available_gateways['stripe_sepa'] ) ? $available_gateways['stripe_sepa'] : (object) array( 'title' => 'Stripe SEPA' );
					$subscription->set( 'payment_method', 'stripe_sepa' );
					$subscription->set( 'payment_method_title', $payment_method->title );
					$gateway_id = 'stripe_sepa';
					yith_subscription_log( 'The order should be paid with "Stripe Credit Card" but the source registered is "Stripe SEPA"', 'subscription_payment' );
				} elseif ( 'stripe_sepa' === $gateway_id && 'card' === $source_object->type ) {
					$payment_method = isset( $available_gateways['stripe'] ) ? $available_gateways['stripe'] : (object) array( 'title' => 'Stripe' );
					$subscription->set( 'payment_method', 'stripe' );
					$subscription->set( 'payment_method_title', $payment_method->title );
					$gateway_id = 'stripe';
					yith_subscription_log( 'The order should be paid with "Stripe SEPA" but the source registered is "Stripe Credit Card"', 'subscription_payment' );
				}
			}

			yith_subscription_log( 'The renew order ' . $renew_order . ' should be pay with ' . $renew_order->get_payment_method_title() . '( ' . $gateway_id . ' )', 'subscription_payment' );
			do_action( 'ywsbs_pay_renew_order_with_' . $gateway_id, $renew_order, $is_manual_renew );

		}

		/**
		 * Pay the renew order from my account page if the order has failed payments
		 *
		 * @return bool
		 */
		public function pay_renew_order_now() {

			if ( ! isset( $_GET['renew_order'] ) ) {
				return;
			}

			$renew_order_id = sanitize_text_field( wp_unslash( $_GET['renew_order'] ) );
			$renew_order    = apply_filters( 'ywsbs_check_order_before_pay_renew_order', wc_get_order( $renew_order_id ) );

			if ( wp_verify_nonce( wp_unslash( $_GET['_nonce'] ), 'ywsbs-renew_order-' . $renew_order_id ) === false || get_current_user_id() !== $renew_order->get_customer_id() ) { // phpcs:ignore
				wc_add_notice( __( 'There was an error with your request. Please try again.', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			$this->pay_renew_order( $renew_order_id, true );

			wp_safe_redirect( wc_get_endpoint_url( 'orders' ) );
			die();
		}

		/**
		 * Check if the new order have subscriptions
		 *
		 * @return     bool
		 * @since      1.0.0
		 * @deprecated 2.0.0
		 */
		public function the_order_have_subscriptions() {
			_deprecated_function( 'YWSBS_Subscription_Order::the_order_have_subscriptions', '2.0.0', 'YWSBS_Subscription_Cart::cart_has_subscriptions' );

			return YWSBS_Subscription_Cart::cart_has_subscriptions();
		}

		/**
		 * Revert cart after checkout.
		 *
		 * @deprecated 2.0.0
		 */
		public function revert_cart_after_checkout() {
			if ( isset( $this->order ) ) {
				_deprecated_function( 'YWSBS_Subscription_Order::revert_cart_after_checkout', '2.0.0' );
				$cart = get_post_meta( $this->order, 'saved_cart', true );
				WC()->cart->empty_cart( true );
				WC()->session->set( 'cart', $cart );
				WC()->cart->get_cart_from_session();
				WC()->cart->set_session();
			}
		}

		/**
		 * Check if the taxes should be recalculated.
		 *
		 * @param WC_Order           $order        Renew order to check.
		 * @param YWSBS_Subscription $subscription Subscription.
		 */
		public function recalculate_taxes_on_renew_order( $order, $subscription ) {

			if ( floatval( $order->get_total() ) !== floatval( $subscription->get_subscription_total() ) ) {

				$order_taxes = $order->get_taxes();
				$p_tax_rates = '';
				// apply taxes only one time and only if order total is greater then 0.
				if ( ! empty( $order_taxes ) && abs( $order->get_total() ) > 0 ) {
					foreach ( $order->get_items( array( 'line_item', 'fee' ) ) as $item_id => $item ) {
						/**
						 * Order item
						 *
						 * @var \WC_Order_Item $item
						 */
						$p_tax_rates = $this->get_rates( $order, $item->get_tax_class() );
						$line_total  = $subscription->get_line_total() + $subscription->get_line_tax();
						$line_taxes  = WC_Tax::calc_tax( $line_total, $p_tax_rates, true );
						$line_taxes  = array_map( 'wc_round_tax_total', $line_taxes ); // round taxes.
						$taxes       = array( 'total' => $line_taxes );
						$line_tax    = max( 0, array_sum( $line_taxes ) );

						$item->set_total( round( $line_total - $line_tax, wc_get_price_decimals() ) );
						wc_add_order_item_meta( $item_id, '_line_total_original', $item->get_total() );

						if ( method_exists( $item, 'get_subtotal' ) ) {
							// cause the subscription item quantity is always 1, get the total as item subtotal.
							$line_subtotal       = $subscription->get_line_total() + $subscription->get_line_tax();
							$line_subtotal_taxes = WC_Tax::calc_tax( $line_subtotal, $p_tax_rates, true );
							$line_subtotal_taxes = array_map( 'wc_round_tax_total', $line_subtotal_taxes );
							$taxes['subtotal']   = $line_subtotal_taxes;
							$line_subtotal_tax   = max( 0, array_sum( $line_subtotal_taxes ) );
							$item->set_subtotal( round( $line_subtotal - $line_subtotal_tax, wc_get_price_decimals() ) );
							wc_add_order_item_meta( $item_id, '_line_subtotal_original', $item->get_subtotal() );
						}

						$item->set_taxes( $taxes );
						$order->update_taxes();
						$order->calculate_totals( false ); // false to avoid tax calculation.

					}

					$shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
					if ( 'inherit' === $shipping_tax_class ) {
						$found_classes      = array_intersect( array_merge( array( '' ), WC_Tax::get_tax_class_slugs() ), $order->get_items_tax_classes() );
						$shipping_tax_class = count( $found_classes ) ? current( $found_classes ) : false;
					}

					foreach ( $order->get_shipping_methods() as $item_id => $item ) {
						$taxes = false;
						if ( false !== $shipping_tax_class ) {
							$tax_rates = $this->get_rates( $order, $shipping_tax_class );
							foreach ( $tax_rates as $key => $tax_rate ) {
								if ( 'no' === $tax_rate['shipping'] ) {
									unset( $tax_rates[ $key ] );
								}
							}
							$line_total = $subscription->get_order_shipping() + $subscription->get_order_shipping_tax();
							$line_taxes = WC_Tax::calc_tax( $line_total, $tax_rates, true );
							$line_taxes = array_map( 'wc_round_tax_total', $line_taxes ); // round taxes.
							$taxes      = array( 'total' => $line_taxes );
							$line_tax   = max( 0, array_sum( $line_taxes ) );

							$item->set_total( round( $line_total - $line_tax, wc_get_price_decimals() ) );
							wc_add_order_item_meta( $item_id, '_line_total_original', $item->get_total() );
						}

						$item->set_taxes( $taxes );
						$order->update_taxes();
						$order->calculate_totals( false ); // false to avoid tax calculation.
					}

					return true;
				}
			}

			return false;

		}

		/**
		 * Get tax rates for order
		 *
		 * @param \WC_Order $order     Order.
		 * @param mixed     $tax_class Tax class.
		 *
		 * @return array
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function get_rates( $order, $tax_class = '' ) {
			$key = $order->get_id() . ' ' . $tax_class;
			if ( empty( $this->current_rates[ $key ] ) ) {
				$this->current_rates[ $key ] = class_exists( 'WC_Tax' ) ? WC_Tax::find_rates(
					array(
						'country'   => $order->get_billing_country(),
						'state'     => $order->get_billing_state(),
						'postcode'  => $order->get_billing_postcode(),
						'city'      => $order->get_billing_city(),
						'tax_class' => $tax_class,
					)
				) : array();
			}

			return $this->current_rates[ $key ];
		}

	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Order class
 *
 * @return YWSBS_Subscription_Order
 */
function YWSBS_Subscription_Order() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YWSBS_Subscription_Order::get_instance();
}
