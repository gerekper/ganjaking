<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe' ) ) {
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCStripe
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Stripe gateway id
		 *
		 * @var string Id of specific gateway
		 * @since 1.0
		 */
		public static $gateway_id = 'yith-stripe';

		/**
		 * The gateway object
		 *
		 * @var YITH_WCStripe_Gateway|YITH_WCStripe_Gateway_Advanced
		 * @since 1.0
		 */
		protected $gateway = null;

		/**
		 * Admin main class
		 *
		 * @var YITH_WCStripe_Admin
		 */
		public $admin = null;

		/**
		 * Zero decimals currencies
		 *
		 * @var array Zero decimals currencies
		 */
		public static $zero_decimals = array(
			'BIF',
			'CLP',
			'DJF',
			'GNF',
			'JPY',
			'KMF',
			'KRW',
			'MGA',
			'PYG',
			'RWF',
			'VND',
			'VUV',
			'XAF',
			'XOF',
			'XPF'
		);

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCStripe
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			// custom query param
			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array(
				$this,
				'handle_custom_query_var'
			), 10, 2 );

			// capture charge if completed, only if set the option
			add_action( 'woocommerce_order_status_processing_to_completed', array( $this, 'capture_charge' ) );
			add_action( 'woocommerce_payment_complete', array( $this, 'capture_charge' ) );

			// includes
			if ( file_exists( 'functions-yith-stripe.php' ) ) {
				include_once( 'functions-yith-stripe.php' );
			}

			// admin includes
			if ( is_admin() ) {
				include_once( 'class-yith-stripe-admin.php' );
				if ( ! defined( 'YITH_WCSTRIPE_PREMIUM' ) || ! YITH_WCSTRIPE_PREMIUM ) {
					$this->admin = new YITH_WCStripe_Admin();
				}
			}

			// security check
			add_action( 'init', array( $this, 'security_check' ) );

			// add filter to append wallet as payment gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_to_gateways' ) );
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/* === PRIVACY LOADER === */

		/**
		 * Loads privacy class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function privacy_loader() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCSTRIPE_INC . 'class-yith-stripe-privacy.php' );
				new YITH_WCStripe_Privacy();
			}
		}

		/**
		 * Adds Stripe Gateway to payment gateways available for woocommerce checkout
		 *
		 * @param $methods array Previously available gataways, to filter with the function
		 *
		 * @return array New list of available gateways
		 * @since 1.0.0
		 */
		public function add_to_gateways( $methods ) {
			self::$gateway_id = apply_filters( 'yith_wcstripe_gateway_id', self::$gateway_id );

			include_once( 'class-yith-stripe-gateway.php' );
			$methods[] = 'YITH_WCStripe_Gateway';

			return $methods;
		}

		/**
		 * Get the gateway object
		 *
		 * @return YITH_WCStripe_Gateway|YITH_WCStripe_Gateway_Advanced|YITH_WCStripe_Gateway_Addons
		 * @since 1.0.0
		 */
		public function get_gateway() {
			if ( ! is_a( $this->gateway, 'YITH_WCStripe_Gateway' ) && ! is_a( $this->gateway, 'YITH_WCStripe_Gateway_Advanced' ) && ! is_a( $this->gateway, 'YITH_WCStripe_Gateway_Addons' ) ) {
				$gateways = WC()->payment_gateways()->get_available_payment_gateways();

				if ( ! isset( $gateways[ self::$gateway_id ] ) ) {
					return false;
				}

				$this->gateway = $gateways[ self::$gateway_id ];
			}

			return $this->gateway;
		}

		/**
		 * Checks whether plugin is currently active on the site it was originally installed
		 *
		 * If site url has changed from original one, it could happen that db was cloned on another installation
		 * To avoid this installation (maybe a staging one) to interact with original stripe data, we enable
		 *
		 * @return void
		 * @since 1.8.2
		 */
		public function security_check() {
			$registered_url = get_option( 'yith_wcstripe_registered_url', '' );

			if ( ! $registered_url ) {
				update_option( 'yith_wcstripe_registered_url', get_site_url() );

				return;
			}

			$registered_url = str_replace( array( 'https://', 'http://', 'www.' ), '', $registered_url );
			$current_url    = str_replace( array( 'https://', 'http://', 'www.' ), '', get_site_url() );

			if ( $current_url != $registered_url ) {
				$gateway_id      = self::$gateway_id;
				$gateway_options = get_option( "woocommerce_{$gateway_id}_settings", array() );

				if ( isset( $gateway_options['enabled_test_mode'] ) && $gateway_options['enabled_test_mode'] == 'no' ) {
					$gateway_options['enabled_test_mode'] = 'yes';

					update_option( "woocommerce_{$gateway_id}_settings", $gateway_options );
					update_option( 'yith_wcstripe_site_changed', 'yes' );
				}
			}
		}

		/**
		 * Capture charge if the payment is been only authorized
		 *
		 * @param integer $order_id
		 *
		 * @since 1.0.0
		 */
		public function capture_charge( $order_id ) {

			// get order data
			$order = wc_get_order( $order_id );

			// check if payment method is Stripe
			if ( yit_get_prop( $order, 'payment_method' ) != self::$gateway_id ) {
				return;
			}

			// exit if the order is in processing
			if ( $order->get_status() == 'processing' ) {
				return;
			}

			// lets third party plugin skip this execution
			if ( ! apply_filters( 'yith_stripe_skip_capture_charge', true, $order_id ) ) {
				return;
			}

			// Support to subscriptions
			if ( $order->get_total() == 0 || $order->get_total() == get_post_meta( $order_id, '_stripe_subscription_total', true ) ) {
				yit_save_prop( $order, '_captured', 'yes' );

				return;
			}

			$transaction_id = $order->get_transaction_id();
			$intent_id      = yit_get_prop( $order, 'intent_id' );
			$captured       = strcmp( yit_get_prop( $order, '_captured' ), 'yes' ) == 0;

			if ( $captured ) {
				return;
			}

			if ( ! $gateway = $this->get_gateway() ) {
				return;
			}

			try {
				// init Stripe api
				$gateway->init_stripe_sdk();

				if ( $intent_id ) {
					$intent = $gateway->api->get_intent( $intent_id );

					if ( $intent && $intent->status == 'requires_capture' ) {
						$params = apply_filters('yith_wcstripe_capture_charge_params',null,$intent,$order);
						$intent->capture($params);
					}
				} else {
					if ( ! $transaction_id ) {
						// Support to subscriptions with trial period
						if ( $order->get_total() == 0 || $order->get_total() == get_post_meta( $order_id, '_stripe_subscription_total', true ) ) {
							update_post_meta( $order_id, '_captured', 'yes' );

							return;
						} else {
							throw new Exception( __( 'Stripe Credit Card Charge failed because the Transaction ID is missing.', 'yith-woocommerce-stripe' ) );
						}
					}

					// capture
					$charge = $gateway->api->capture_charge( $transaction_id );
				}

				// update post meta
				yit_save_prop( $order, '_captured', 'yes' );

			} catch ( Exception $e ) {
				$message = $e->getMessage();
				$order->add_order_note( sprintf( __( 'Stripe Error - Charge not captured. %s', 'yith-woocommerce-wishlist' ), $message ) );

				if ( is_admin() ) {
					wp_die( $message );
				}

				wc_add_notice( $message, 'error' );
			}
		}

		/**
		 * Returns order details for hosted checkout
		 */
		public function send_checkout_details() {
			check_ajax_referer( 'yith-stripe-refresh-details', 'refresh-details', true );

			WC()->cart->calculate_totals();

			wp_send_json( array(
				'amount'   => self::get_amount( WC()->cart->total ),
				'currency' => strtolower( get_woocommerce_currency() )
			) );
		}

		/**
		 * Register custom query vars for orders filtering
		 *
		 * @param $query      array Current query configuration
		 * @param $query_vars array Query vars passed to wc_get_orders function
		 *
		 * @return array Array of filtered query configuration
		 */
		public function handle_custom_query_var( $query, $query_vars ) {
			if ( ! empty( $query_vars['stripe_session_id'] ) ) {
				$query['meta_query'][] = array(
					'key'   => 'session_id',
					'value' => esc_attr( $query_vars['stripe_session_id'] ),
				);
			}

			return $query;
		}

		/**
		 * Get Stripe amount to pay
		 *
		 * @param           $total
		 * @param string    $currency
		 * @param \WC_Order $order
		 *
		 * @return float
		 * @since 1.0.0
		 */
		public static function get_amount( $total, $currency = '', $order = null ) {
			if ( empty( $currency ) ) {
				$currency = get_woocommerce_currency();
			}

			$total = apply_filters( 'yith_wcstripe_gateway_amount', $total, $order );

			if ( ! in_array( $currency, self::$zero_decimals ) ) {
				$total *= 100;
			}

			return round( $total );
		}

		/**
		 * Get original amount
		 *
		 * @param        $total
		 * @param string $currency
		 *
		 * @return float
		 * @since 1.0.0
		 */
		public static function get_original_amount( $total, $currency = '' ) {
			if ( empty( $currency ) ) {
				$currency = get_woocommerce_currency();
			}

			if ( in_array( $currency, self::$zero_decimals ) ) {
				$total = absint( $total );
			} else {
				$total /= 100;
			}

			return $total;
		}
	}
}