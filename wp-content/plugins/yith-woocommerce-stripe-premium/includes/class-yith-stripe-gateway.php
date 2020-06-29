<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

use \Stripe\Error;

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Gateway' ) ) {
	/**
	 * WooCommerce Stripe gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Gateway extends WC_Payment_Gateway_CC {

		/**
		 * @var YITH_Stripe_API API Library
		 */
		public $api = null;

		/**
		 * @var array List of standard localized message errors of Stripe SDK
		 */
		public $errors = array();

		/**
		 * @var array List of standard localized decline codes of Stripe SDK
		 */
		protected $decline_messages = array();

		/**
		 * @var array List of localized suggestions provided to the customer when an error occurs
		 */
		protected $further_steps = array();

		/**
		 * @var string The domain of this site used to identifier the website from Stripe
		 */
		public $instance = '';

		/**
		 * @var array List cards
		 */
		public $cards = array(
			'visa'       => 'Visa',
			'mastercard' => 'MasterCard',
			'discover'   => 'Discover',
			'amex'       => 'American Express',
			'diners'     => 'Diners Club',
			'jcb'        => 'JCB',
		);

		/**
		 * @var string $mode (standard|elements|checkout|hosted)
		 */
		public $mode;

		/**
		 * @var string $env (live|test)
		 */
		public $env;

		/**
		 * @var string $private_key Secret private API key
		 */
		public $private_key;

		/**
		 * @var string $public_key Sharable public API key
		 */
		public $public_key;

		/**
		 * @var string $token Token for current transaction
		 */
		public $token;

		/**
		 * @var string $modal_image Image to use to describe CVV field to customers
		 */
		public $modal_image;

		/**
		 * @var string $session_param Name of the session id param
		 */
		public $session_param;

		/**
		 * @var WC_Order
		 */
		protected $_current_order = null;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Gateway
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                 = YITH_WCStripe::$gateway_id;
			$this->has_fields         = true;
			$this->method_title       = apply_filters( 'yith_stripe_method_title', __( 'Stripe', 'yith-woocommerce-stripe' ) );
			$this->method_description = apply_filters( 'yith_stripe_method_description', __( 'Take payments via Stripe - uses stripe.js to create card tokens and the Stripe SDK. Requires SSL when sandbox is disabled.', 'yith-woocommerce-stripe' ) );
			$this->supports           = array(
				'products'
			);
			$this->instance           = preg_replace( '/http(s)?:\/\//', '', site_url() );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->enabled              = apply_filters( 'yith_wcstripe_gateway_enabled', $this->enabled );
			$this->title                = $this->get_option( 'title' );
			$this->description          = $this->get_option( 'description' );
			$this->env                  = apply_filters( 'yith_wcstripe_environment', ( $this->get_option( 'enabled_test_mode' ) == 'yes' || ( defined( 'WP_ENV' ) && 'development' == WP_ENV ) ) ? 'test' : 'live' );
			$this->private_key          = $this->get_option( $this->env . '_secrect_key' );
			$this->public_key           = $this->get_option( $this->env . '_publishable_key' );
			$this->modal_image          = $this->get_option( 'modal_image' );
			$this->mode                 = 'hosted';
			$this->view_transaction_url = 'https://dashboard.stripe.com/' . ( 'test' === $this->env ? 'test/' : '' ) . 'payments/%s';
			$this->session_param        = apply_filters( 'yith_wcstripe_session_param', 'session_id' );

			// post data
			$this->token = isset( $_POST['stripe_token'] ) ? wc_clean( $_POST['stripe_token'] ) : '';

			// save
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

			// others
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'return_handler' ) );
		}

		/* === GATEWAY METHODS === */

		/**
		 * Check if this gateway is enabled
		 *
		 * @since 1.0.0
		 */
		public function is_available() {
			if ( 'yes' != $this->enabled ) {
				return false;
			}

			if ( 'standard' == $this->mode && ! is_ssl() && 'test' != $this->env ) {
				return false;
			}

			if ( ! $this->public_key || ! $this->private_key ) {
				return false;
			}

			if ( WC()->cart && 0 < $this->get_order_total() && 0 < $this->max_amount && $this->max_amount < $this->get_order_total() ) {
				return false;
			}

			if ( $this->is_blocked() ) {
				return false;
			}

			return true;
		}

		/**
		 * Initialize form fields for the admin
		 *
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'              => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-stripe' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Stripe Payment', 'yith-woocommerce-stripe' ),
					'default' => 'yes'
				),
				'title'                => array(
					'title'       => __( 'Title', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'yith-woocommerce-stripe' ),
					'default'     => __( 'Credit Card', 'yith-woocommerce-stripe' ),
					'desc_tip'    => true,
				),
				'description'          => array(
					'title'       => __( 'Description', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'This controls the description which the user sees during checkout.', 'yith-woocommerce-stripe' ),
					'default'     => __( 'Pay with a credit card.', 'yith-woocommerce-stripe' )
				),
				'customization'        => array(
					'title'       => __( 'Customization', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => __( 'Customize the payment gateway on frontend', 'yith-woocommerce-stripe' ),
				),
				'modal_image'          => array(
					'title'       => __( 'Modal image', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Define the URL of image to show on Stripe checkout modal.', 'yith-woocommerce-stripe' ),
					'default'     => ''
				),
				'testing'              => array(
					'title'       => __( 'Testing & Debug', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => __( 'Enable here the testing mode, to debug the payment system before going into production', 'yith-woocommerce-stripe' ),
				),
				'enabled_test_mode'    => array(
					'title'   => __( 'Enable Test Mode', 'yith-woocommerce-stripe' ),
					'type'    => 'checkbox',
					'label'   => __( 'Check this option if you want to test the gateway before going into production', 'yith-woocommerce-stripe' ),
					'default' => 'yes'
				),
				'keys'                 => array(
					'title'       => __( 'API Keys', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => sprintf( __( 'You can find it in <a href="%s">your stripe dashboard</a>', 'yith-woocommerce-stripe' ), 'https://dashboard.stripe.com/account/apikeys' ),
				),
				'test_secrect_key'     => array(
					'title'       => __( 'Test Secret Key', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'description' => __( 'Set the secret API key for test', 'yith-woocommerce-stripe' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'test_publishable_key' => array(
					'title'       => __( 'Test Publishable Key', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'description' => __( 'Set the published API key for test', 'yith-woocommerce-stripe' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'live_secrect_key'     => array(
					'title'       => __( 'Live Secret Key', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'description' => __( 'Set the secret API key for live production', 'yith-woocommerce-stripe' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'live_publishable_key' => array(
					'title'       => __( 'Live Publishable Key', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'description' => __( 'Set the published API key for live production', 'yith-woocommerce-stripe' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			);
		}

		/**
		 * Payment form on checkout page
		 *
		 * @since 1.0.0
		 */
		public function payment_fields() {
			$description = $this->get_description();

			if ( 'test' == $this->env ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s', 'yith-woocommerce-stripe' ), '<a href="https://stripe.com/docs/testing">https://stripe.com/docs/testing</a>' );
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) );
			}
		}

		/**
		 * get_icon function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_icon() {
			switch ( WC()->countries->get_base_country() ) {

				case 'US' :
					$allowed = apply_filters( 'yith_wcstripe_gateway_us_icons', array(
						'visa',
						'mastercard',
						'amex',
						'discover',
						'diners',
						'jcb'
					) );
					break;

				default :
					$allowed = apply_filters( 'yith_wcstripe_gateway_default_icons', array(
						'visa',
						'mastercard',
						'amex'
					) );
					break;
			}

			$icon = '';
			foreach ( $allowed as $name ) {
				$icon .= apply_filters( 'yith_wcstripe_gateway_icon', '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $name . '.png' ) . '" alt="' . $this->cards[ $name ] . '" style="width:40px;" />', $name, $this->cards[ $name ], $allowed );
			}

			return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
		}

		/* === PAYMENT METHODS === */

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @throws Stripe\Exception\ApiErrorException
		 * @since 1.0.0
		 */
		public function process_payment( $order_id ) {
			$order                = wc_get_order( $order_id );
			$this->_current_order = $order;

			return $this->process_hosted_payment();
		}

		/**
		 * Return handler for Hosted Payments
		 */
		public function return_handler() {
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) ) {
				return;
			}

			@ob_clean();
			status_header( 200 );

			if ( isset( $_REQUEST[ $this->session_param ] ) ) {

				$session_id = sanitize_text_field( $_REQUEST[ $this->session_param ] );
				$order      = $this->get_order_by_session_id( $session_id );

				if ( $order ) {

					if ( $order->has_status( array( 'completed', 'processing' ) ) ) {
						wp_redirect( $this->get_return_url( $order ) );
						exit();
					}

					// Initialize SDK and set private key
					$this->init_stripe_sdk();

					$session = $this->api->get_session( $session_id );

					if ( $session && $session->payment_intent ) {
						$intent = $this->api->get_intent( $session->payment_intent );

						if ( $intent && in_array( $intent->status, array( 'succeeded', 'requires_capture' ) ) ) {
							// register intent for the order
							$order->update_meta_data( 'intent_id', $intent->id );

							// update intent data
							$this->api->update_intent( $intent->id, array(
								'description' => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
								'metadata'    => apply_filters( 'yith_wcstripe_metadata', array(
									'order_id'    => $order->get_id(),
									'order_email' => yit_get_prop( $order, 'billing_email' ),
									'instance'    => $this->instance,
								), 'charge' )
							) );

							// retrieve charge to use for next steps
							$charge = end( $intent->charges->data );

							// Payment complete
							$order->payment_complete( $charge->id );

							// Add order note
							$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

							// Remove cart
							WC()->cart->empty_cart();

							wp_redirect( $this->get_return_url( $order ) );
							exit();
						}
					}
				}
			}

			wc_add_notice( __( 'There was a error during payment; please, try again later', 'yith-woocommerce-stripe' ) );

			wp_redirect( wc_get_checkout_url() );
			exit();
		}

		/**
		 * Process standard payments
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 */
		protected function process_hosted_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			try {
				$session = $this->create_checkout_session( array(
					'order_id' => $order->get_id()
				) );
			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$this->error_handling( $e, array(
					'mode'  => 'both',
					'order' => $order
				) );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}

			if ( ! $session ) {
				wc_add_notice( __( 'There was a problem during payment; please try again later', 'yith-woocommerce-stripe' ), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}

			$order->update_meta_data( 'session_id', $session->id );
			$order->save_meta_data();

			return array(
				'result'   => 'success',
				'redirect' => add_query_arg( $this->session_param, $session->id, $order->get_checkout_payment_url( true ) )
			);
		}

		/* === FRONTEND METHODS === */

		/**
		 * Javascript library
		 *
		 * @since 1.0.0
		 */
		public function payment_scripts() {
			$load_scripts = false;

			if ( $this->is_available() && ( is_checkout() || apply_filters( 'yith_wcstripe_load_assets', false ) ) ) {
				$load_scripts = true;
			}

			if ( false === $load_scripts ) {
				return;
			}

			// scripts
			wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), YITH_WCSTRIPE_VERSION, true );
			wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-checkout.js', array(
				'jquery',
				'jquery-blockui',
				'stripe-js'
			), YITH_WCSTRIPE_VERSION, true );
			wp_enqueue_script( 'yith-stripe-js' );

			wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
				'public_key' => $this->public_key,
			) );
		}

		/* === CHECKOUT SESSION METHODS */

		/**
		 * Create checkout session
		 *
		 * @param array $args Params used to create CheckoutSession object.
		 *
		 * @return \Stripe\StripeObject|bool Checkout session or false on failure
		 */
		public function create_checkout_session( $args = array() ) {
			global $wp;

			$order_id = false;

			if ( isset( $args['order_id'] ) ) {
				$order_id = $args['order_id'];
				unset( $args['order_id'] );
			} elseif ( is_checkout_pay_page() ) {
				$order_id = isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false;
			}

			if ( ! $order_id ) {
				return false;
			}

			$order          = wc_get_order( $order_id );
			$currency       = $order->get_currency();
			$customer_email = wp_get_current_user()->billing_email;

			if ( apply_filters( 'yith_wcstripe_checkout_session_detailed_line_items', true ) ) {
				$items      = $order->get_items( array( 'line_item', 'shipping', 'tax', 'fee' ) );
				$line_items = array();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						$line_item = array(
							'currency' => $order->get_currency(),
							'quantity' => $item->get_quantity(),
						);

						if ( $item->is_type( 'line_item' ) ) {
							/**
							 * If product is a line item, we can retrieve related product
							 *
							 * @var $product WC_Product Product object for the item.
							 */
							$product  = $item->get_product();
							$image_id = $product->get_image_id();

							if ( ! $total = floatval( $item->get_total() ) ) {
								continue;
							}

							$line_item['name']        = $item->get_name();
							if ( $product->get_short_description() ) {
                                $line_item['description'] = $product->get_short_description();
                            }
							$line_item['amount']      = YITH_WCStripe::get_amount( $total / $item->get_quantity(), $order->get_currency(), $order );
							$line_item['images']      = array(
								$image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src(),
							);
						} elseif ( $item->is_type( array( 'fee' ) ) ) {
							if ( ! $total = floatval( $item->get_total() ) ) {
								continue;
							}

							// translators: 1. Fee name.
							$line_item['name']   = sprintf( __( 'Fee: %s', 'yith-woocommerce-stripe' ), $item->get_name() );
							$line_item['amount'] = YITH_WCStripe::get_amount( $total, $order->get_currency(), $order );
						} elseif ( $item->is_type( 'shipping' ) ) {
							if ( ! $total = floatval( $item->get_total() ) ){
								continue;
							}

							// translators: 1. Shipping name.
							$line_item['name']   = sprintf( __( 'Shipping: %s', 'yith-woocommerce-stripe' ), $item->get_name() );
							$line_item['amount'] = YITH_WCStripe::get_amount( $total, $order->get_currency(), $order );
						} elseif ( $item->is_type( 'tax' ) ) {
							$total = floatval( $item->get_tax_total() ) + floatval( $item->get_shipping_tax_total() );

							if ( ! $total ) {
								continue;
							}

							// translators: 1. Tax name.
							$line_item['name']   = sprintf( __( 'Tax: %s', 'yith-woocommerce-stripe' ), $item->get_name() );
							$line_item['amount'] = YITH_WCStripe::get_amount( $total, $order->get_currency(), $order );
						}

						$line_items[] = $line_item;
					}
				}
			} else {
				$line_items = array(
					array(
						// translators: 1. Blog name. 2. Order id.
						'name'     => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%1$s - Order %2$s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
						'amount'   => YITH_WCStripe::get_amount( $order->get_total(), $currency, $order ),
						'currency' => $currency,
						'quantity' => 1,
					),
				);
			}

			$defaults = apply_filters(
				'yith_wcstripe_create_checkout_session',
				array_merge(
					array(
						'payment_method_types'       => array( 'card' ),
						'line_items'                 => $line_items,
						'locale'                     => apply_filters( 'yith_stripe_locale', substr( get_locale(), 0, 2 ) ),
						'cancel_url'                 => $order->get_checkout_payment_url(),
						'success_url'                => add_query_arg( $this->session_param, '{CHECKOUT_SESSION_ID}', WC()->api_request_url( get_class( $this ) ) ),
						'billing_address_collection' => 'auto',
						'payment_intent_data'        => array(
							'capture_method' => 'automatic',
						),
						$customer_email ? array( 'customer_email' => $customer_email ) : array(),
					)
				)
			);

			$args = wp_parse_args( $args, $defaults );

			// Initialize SDK and set private key.
			$this->init_stripe_sdk();

			$session = $this->api->create_session( $args );

			return $session;
		}

		/* === HELPER METHODS === */

		/**
		 * Init Stripe SDK.
		 *
		 * @return void
		 */
		public function init_stripe_sdk( $private_key = '' ) {
			if ( is_a( $this->api, 'YITH_Stripe_Api' ) ) {
				return;
			}

			// Include lib
			require_once( YITH_WCSTRIPE_DIR . 'includes/class-yith-stripe-api.php' );

			$private_key = ! $private_key ? $this->private_key : $private_key;
			$this->api   = new YITH_Stripe_API( $private_key );
		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @since 1.0.0
		 */
		public function admin_notices() {
			if ( $this->enabled == 'no' ) {
				return;
			}

			if ( ! function_exists( 'curl_init' ) ) {
				echo '<div class="error"><p>' . __( 'Stripe needs the CURL PHP extension.', 'yith-woocommerce-stripe' ) . '</p></div>';
			}

			if ( ! function_exists( 'json_decode' ) ) {
				echo '<div class="error"><p>' . __( 'Stripe needs the JSON PHP extension.', 'yith-woocommerce-stripe' ) . '</p></div>';
			}

			if ( ! function_exists( 'mb_detect_encoding' ) ) {
				echo '<div class="error"><p>' . __( 'Stripe needs the Multibyte String PHP extension.', 'yith-woocommerce-stripe' ) . '</p></div>';
			}

			if ( ! $this->public_key || ! $this->private_key ) {
				echo '<div class="error"><p>' . __( 'Please enter the public and private keys for Stripe gateway.', 'yith-woocommerce-stripe' ) . '</p></div>';
			}

			if ( 'standard' == $this->mode && $this->env != 'test' && ! wc_checkout_is_https() && ! class_exists( 'WordPressHTTPS' ) ) {
				echo '<div class="error"><p>' . sprintf( __( 'Stripe sandbox testing is disabled and can performe live transactions but the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate. <a href="%s">Learn more</a>.', 'woothemes' ), admin_url( 'admin.php?page=wc-settings' ), 'https://stripe.com/help/ssl' ) . '</p></div>';
			}
		}

		/**
		 * Method to check blacklist (only for premium)
		 *
		 * @since 1.1.3
		 */
		public function is_blocked() {
			return false;
		}

		/**
		 * Receipt page
		 *
		 * @param int $order_id
		 */
		public function receipt_page( $order_id ) {
			if ( in_array( $this->mode, array( 'elements', 'standard' ) ) ) {
				return;
			}

			$session_id = isset( $_GET[ $this->session_param ] ) ? sanitize_text_field( $_GET[ $this->session_param ] ) : false;
			$order      = wc_get_order( $order_id );

			if ( $session_id && $order ) {
				echo '<p>' . __( 'Thank you for your order, please click the button below to pay with credit card using Stripe.', 'yith-woocommerce-stripe' ) . '</p>';
				echo '<a href="#" class="button" id="yith_wcstripe_open_checkout" data-session_id="' . $session_id . '">' . __( 'Proceed to payment', 'yith-woocommerce-stripe' ) . '</a>';
			}
		}

		/**
		 * Standard error handling for exceptions thrown by API class
		 *
		 * @param $e Stripe\Exception\ApiErrorException
		 *
		 * @return string Final error message
		 */
		public function error_handling( $e, $args = array() ) {
			$body     = $e->getJsonBody();
			$message  = $e->getMessage();
			$defaults = array(
				'order'  => null,
				// order: required for note mode
				'mode'   => 'notice',
				// error handling mode: notice to print message via wc_add_notice / note to add message as order note / both execute both handlings
				'format' => ''
				// message format: when not empty, this format string will be used for sprintf(), using message as only parameter
			);

			/**
			 * @var $order  \WC_Order
			 * @var $mode   string
			 * @var $format string
			 */
			extract( wp_parse_args( $args, $defaults ) );

			if ( $body ) {
				$err = $body['error'];

				if ( isset( $err['code'] ) ) {
					$message = $this->get_error_message( $err['code'], $message, $err );
				}
			}

			if ( ! empty( $format ) ) {
				$message = sprintf( $format, $message );
			}

			switch ( $mode ) {
				case 'both':
				case 'note':
					if ( $order && $order instanceof WC_Order ) {
						$note_error_code = isset( $err ) && isset( $err['decline_code'] ) ? sprintf( ' (%s)', $err['decline_code'] ) : '';
						$note            = sprintf( __( 'Stripe Error: %s - %s', 'yith-woocommerce-stripe' ), $e->getHttpStatus() . $note_error_code, $message );
						$order->add_order_note( apply_filters( 'yith_wcstripe_error_message_order_note', $note, $e, isset( $err ) ? $err : false ) );
					}

					if ( $mode == 'note' ) {
						break;
					}

				case 'notice':
				default:
					wc_add_notice( $message, 'error' );
					break;
			}

			do_action( 'yith_wcstripe_gateway_error', $message, $order, $mode, $format );

			return $message;
		}

		/**
		 * Initialize and localize error messages
		 *
		 * @since 1.0.0
		 */
		protected function init_errors() {
			$this->errors = apply_filters( 'yith-wcstripe-error-messages', array(
				// Codes
				'incorrect_number'     => __( 'The card number is incorrect.', 'yith-woocommerce-stripe' ),
				'invalid_number'       => __( 'The card number is not a valid credit card number.', 'yith-woocommerce-stripe' ),
				'invalid_expiry_month' => __( 'The card\'s expiration month is invalid.', 'yith-woocommerce-stripe' ),
				'invalid_expiry_year'  => __( 'The card\'s expiration year is invalid.', 'yith-woocommerce-stripe' ),
				'invalid_cvc'          => __( 'The card\'s security code is invalid.', 'yith-woocommerce-stripe' ),
				'expired_card'         => __( 'The card has expired.', 'yith-woocommerce-stripe' ),
				'incorrect_cvc'        => __( 'The card\'s security code is incorrect.', 'yith-woocommerce-stripe' ),
				'incorrect_zip'        => __( 'The card\'s zip code failed validation.', 'yith-woocommerce-stripe' ),
				'card_declined'        => __( 'An error occurred while processing the card.', 'yith-woocommerce-stripe' ),
				'missing'              => __( 'There is no card on a customer that is being charged.', 'yith-woocommerce-stripe' ),
				'processing_error'     => __( 'An error occurred while processing the card.', 'yith-woocommerce-stripe' ),
				'rate_limit'           => __( 'An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.', 'yith-woocommerce-stripe' )
			) );

			$this->decline_messages = apply_filters( 'yith-wcstripe-decline-messages', array(
				// Codes
				'approve_with_id'                   => array(
					'message'       => __( 'The payment cannot be authorized.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'try_again'
				),
				'call_issuer'                       => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'card_not_supported'                => array(
					'message'       => __( 'The card does not support this type of purchase.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'card_velocity_exceeded'            => array(
					'message'       => __( 'You have exceeded the balance or credit limit available on your card.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'currency_not_supported'            => array(
					'message'       => __( 'The card does not support the specified currency.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'do_not_honor'                      => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'do_not_try_again'                  => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'duplicate_transaction'             => array(
					'message'       => __( 'A transaction with identical amount and credit card information was submitted very recently.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_us'
				),
				'expired_card'                      => array(
					'message'       => __( 'The card has expired.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'fraudulent'                        => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'generic_decline'                   => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'incorrect_number'                  => array(
					'message'       => __( 'The card number is incorrect.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'incorrect_cvc'                     => array(
					'message'       => __( 'The CVC number is incorrect.', 'yith-woocommerce-stripe' ),
					'further_steps' => ''
				),
				'incorrect_zip'                     => array(
					'message'       => __( 'The ZIP/postal code is incorrect.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'insufficient_funds'                => array(
					'message'       => __( 'The card has insufficient funds to complete the purchase.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'invalid_account'                   => array(
					'message'       => __( 'The card, or account the card is connected to, is invalid.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'invalid_amount'                    => array(
					'message'       => __( 'The payment amount is invalid, or exceeds the amount that is allowed.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'invalid_cvc'                       => array(
					'message'       => __( 'The CVC number is incorrect.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'invalid_expiry_year'               => array(
					'message'       => __( 'The expiration year invalid.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'invalid_number'                    => array(
					'message'       => __( 'The card number is incorrect.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'issuer_not_available'              => array(
					'message'       => __( 'The card issuer could not be reached, so the payment could not be authorized.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'try_again'
				),
				'lost_card'                         => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'merchant_blacklist'                => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'new_account_information_available' => array(
					'message'       => __( 'The card, or account the card is connected to, is invalid.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'no_action_taken'                   => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'not_permitted'                     => array(
					'message'       => __( 'The payment is not permitted.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'pickup_card'                       => array(
					'message'       => __( 'The card cannot be used to make this payment (it is possible it has been reported lost or stolen).', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'processing_error'                  => array(
					'message'       => __( 'An error occurred while processing the card.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'try_again'
				),
				'reenter_transaction'               => array(
					'message'       => __( 'The payment could not be processed by the issuer.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'try_again'
				),
				'restricted_card'                   => array(
					'message'       => __( 'The card cannot be used to make this payment (it is possible it has been reported lost or stolen).', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'revocation_of_all_authorizations'  => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'revocation_of_authorization'       => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'security_violation'                => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'service_not_allowed'               => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'stolen_card'                       => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'stop_payment_order'                => array(
					'message'       => __( 'The card has been declined.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'testmode_decline'                  => array(
					'message'       => __( 'A Stripe test card number was used.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
				'transaction_not_allowed'           => array(
					'message'       => __( 'The card has been declined for an unknown reason.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'contact_bank'
				),
				'try_again_later'                   => array(
					'message'       => __( 'The card has been declined for an unknown reason.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'try_again'
				),
				'withdrawal_count_limit_exceeded'   => array(
					'message'       => __( 'You have exceeded the balance or credit limit available on your card.', 'yith-woocommerce-stripe' ),
					'further_steps' => 'change_card'
				),
			) );

			$this->further_steps = apply_filters( 'yith-wcstripe-decline-messages', array(
				'try_again'    => __( 'Please, try again later. If the problem persists, contact you card issuer for more information.', 'yith-woocommerce-stripe' ),
				'contact_bank' => __( 'Please, contact your card issuer for more information.', 'yith-woocommerce-stripe' ),
				'contact_us'   => __( 'Please, contact us to get help with this issue.', 'yith-woocommerce-stripe' ),
				'change_card'  => __( 'Please, double check information entered for your card, or try again using another card', 'yith-woocommerce-stripe' )
			) );
		}

		/**
		 * Returns error messages from a valid error code
		 * This is required in order to have localized messages shown at checkout
		 *
		 * @param $error_code    string Error code from Stripe API
		 * @param $error_message string Error code coming from Stripe API, to be used when we cannot retrieve a better message
		 * @param $error_object  array Error object, as it was retrieved from Stripe API call response body
		 *
		 * @return string Error message to be shown to the customer
		 * @since 1.8.2
		 */
		protected function get_error_message( $error_code, $error_message = '', $error_object = null ) {
			$error = $error_message;

			if ( apply_filters( 'yith_wcstripe_use_plugin_error_codes', true ) ) {
				if ( empty( $this->errors ) ) {
					$this->init_errors();
				}

				$error = isset( $this->errors[ $error_code ] ) ? $this->errors[ $error_code ] : $error_message;

				if ( ! empty( $error_object ) && isset( $error_object['decline_code'] ) && isset( $this->decline_messages[ $error_object['decline_code'] ] ) ) {
					$additional_notes   = $this->decline_messages[ $error_object['decline_code'] ];
					$additional_message = $additional_notes['message'];
					$further_steps      = '';

					if ( ! empty( $additional_notes['further_steps'] ) && isset( $this->further_steps[ $additional_notes['further_steps'] ] ) ) {
						$further_steps = $this->further_steps[ $additional_notes['further_steps'] ];
						$further_steps = ' ' . $further_steps;
					}

					$error .= sprintf( ' (%s%s)', $additional_message, $further_steps );
				}
			}

			if ( ! $error ) {
				$error = apply_filters( 'yith_wcstripe_generic_error_message', __( 'An error occurred during your transaction. Please, try again later', 'yith-woocommerce-stripe' ), $error_message, $error_code, $error_object );
			}

			return apply_filters( 'yith_wcstripe_error_message', $error, $error_message, $error_code, $error_object );
		}

		/**
		 * Return currency for the order; if no order was sent, use default store currency
		 *
		 * @param $order \WC_Order Order
		 *
		 * @return string Currency
		 */
		protected function get_currency( $order = null ) {
			$currency = $order ? $order->get_currency() : get_woocommerce_currency();

			return apply_filters( 'yith_wcstripe_gateway_currency', $currency, $order );
		}

		/**
		 * Retrieves order by session id
		 *
		 * @param $session_id string Session id
		 *
		 * @return WC_Order|bool Order, or false on failure
		 */
		protected function get_order_by_session_id( $session_id ) {
			$orders = wc_get_orders( array(
				'stripe_session_id' => $session_id
			) );

			if ( empty( $orders ) ) {
				return false;
			}

			return array_shift( $orders );
		}
	}
}