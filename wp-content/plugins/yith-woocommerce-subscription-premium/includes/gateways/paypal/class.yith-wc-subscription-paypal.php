<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YWSBS_Subscription_Paypal Class
 *
 * @class   YWSBS_Subscription_Paypal
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Paypal' ) ) {
	/**
	 * Class YWSBS_Subscription_Paypal
	 */
	class YWSBS_Subscription_Paypal {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Paypal
		 */
		protected static $instance;

		/**
		 * Wc Log Parameter.
		 *
		 * @var WC_Logger
		 */
		protected $wclog = '';

		/**
		 * Check if the debug is active.
		 *
		 * @var boolean
		 */
		protected $debug;

		/**
		 * Check if the gateway is on test mode.
		 *
		 * @var boolean
		 */
		protected $testmode;

		/**
		 * Email of PayPal Account
		 *
		 * @var string
		 */
		protected $email;

		/**
		 * Email of Receiver
		 *
		 * @var string
		 */
		protected $receiver_email;

		/**
		 * API username
		 *
		 * @var string
		 */
		protected $api_username;

		/**
		 * API Password
		 *
		 * @var string
		 */
		protected $api_password;

		/**
		 * API Signature
		 *
		 * @var string
		 */
		protected $api_signature;

		/**
		 * API Endpoint
		 *
		 * @var string
		 */
		protected $api_endpoint;

		/**
		 * Setting Options
		 *
		 * @var array
		 */
		protected $setting_options;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Paypal
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

			$settings = get_option( 'woocommerce_paypal_settings' );

			if ( ! isset( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
				return;
			}

			$this->setting_options = $settings;
			$this->debug           = isset( $settings['debug'] ) && 'yes' === $settings['debug'];
			$this->testmode        = isset( $settings['testmode'] ) && 'yes' === $settings['testmode'];
			$this->email           = isset( $settings['email'] ) ? $settings['email'] : '';
			$this->receiver_email  = isset( $settings['receiver_email'] ) ? $settings['receiver_email'] : $this->email;
			$option_suffix         = $this->testmode ? 'sandbox_' : '';

			if ( $this->debug ) {
				$this->wclog = new WC_Logger();
			}

			// When necessary, set the PayPal args to be for a subscription instead of shopping cart.
			add_filter( 'woocommerce_paypal_args', array( $this, 'subscription_args' ) );

			// Check if there's a subcription in a valid PayPal IPN request.
			include_once WC()->plugin_path() . '/includes/gateways/paypal/includes/class-wc-gateway-paypal-ipn-handler.php';
			include_once 'includes/class.ywsbs-paypal-ipn-handler.php';

			new YWSBS_PayPal_IPN_Handler( $this->testmode, $this->receiver_email );

			// Set API credentials.
			if ( ! empty( $settings[ $option_suffix . 'api_username' ] ) && ! empty( $settings[ $option_suffix . 'api_password' ] ) && ! empty( $settings[ $option_suffix . 'api_signature' ] ) ) {
				$this->api_username  = $settings[ $option_suffix . 'api_username' ];
				$this->api_password  = $settings[ $option_suffix . 'api_password' ];
				$this->api_signature = $settings[ $option_suffix . 'api_signature' ];
				$this->api_endpoint  = $this->testmode ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';

				add_filter( 'ywsbs_suspend_recurring_payment', array( $this, 'suspend_recurring_payment' ), 10, 2 );
				add_filter( 'ywsbs_resume_recurring_payment', array( $this, 'resume_recurring_payment' ), 10, 2 );
				add_filter( 'ywsbs_cancel_recurring_payment', array( $this, 'cancel_recurring_payment' ), 10, 2 );
			}

			add_filter( 'woocommerce_payment_gateway_supports', array( $this, 'add_subscription_support_to_paypal_standard' ), 10, 3 );
			add_action( 'woocommerce_thankyou_paypal', array( $this, 'check_pdt_response' ), 1 );

		}

		/**
		 * Add yith_subscriptions support to PayPal Standard.
		 *
		 * @param bool          $support Tell if the feature is support.
		 * @param string        $feature Feature to check.
		 * @param object|string $gateway Current gateway.
		 *
		 * @return bool
		 */
		public function add_subscription_support_to_paypal_standard( $support, $feature, $gateway ) {

			$gateway = is_object( $gateway ) ? $gateway->id : $gateway;

			if ( 'paypal' !== $gateway ) {
				return $support;
			}

			$supports = array( 'yith_subscriptions', 'yith_subscription_pause', 'yith_subscription_pay_method_customer' );

			return in_array( $feature, $supports, true ) ? true : $support;
		}

		/**
		 * Set arguments for subscription products.
		 *
		 * @param array $args Arguments.
		 *
		 * @return mixed
		 */
		public function subscription_args( $args ) {

			$order_info = $this->get_order_info( $args );

			if ( empty( $order_info ) || ! isset( $order_info['order_id'] ) ) {
				return $args;
			}

			$order      = wc_get_order( $order_info['order_id'] );
			$is_a_renew = $order->get_meta( 'is_a_renew' );

			if ( 'yes' === $is_a_renew ) {
				return $args;
			}

			// check if order has subscriptions.
			$order_items = $order->get_items();

			if ( empty( $order_items ) ) {
				return $args;
			}

			$item_names       = array();
			$has_subscription = false;

			foreach ( $order_items as $key => $order_item ) {

				$product_id = $order_item['variation_id'] ? $order_item['variation_id'] : $order_item['product_id'];
				$product    = wc_get_product( $product_id );

				if ( ywsbs_is_subscription_product( $product_id ) ) {
					// It's a subscription.
					$has_subscription = true;
					$args['cmd']      = '_xclick-subscriptions';

					// 1 for reattempt failed recurring payments before canceling, use 0 for not.
					$args['sra'] = apply_filters( 'ywsbs_reattempt_failed_recurring_payments', 1 );

					$subscription_info = wc_get_order_item_meta( $key, '_subscription_info', true );

					$price_is_per      = isset( $subscription_info['price_is_per'] ) ? $subscription_info['price_is_per'] : $product->get_meta( '_ywsbs_price_is_per' );
					$price_time_option = isset( $subscription_info['price_time_option'] ) ? $subscription_info['price_time_option'] : $product->get_meta( '_ywsbs_price_time_option' );
					$price_time_option = ywsbs_get_price_time_option_paypal( $price_time_option );
					$max_length        = isset( $subscription_info['max_length'] ) ? $subscription_info['max_length'] : YWSBS_Subscription_Helper::get_subscription_product_max_length( $product );
					$trial_is_per      = isset( $subscription_info['trial_per'] ) ? $subscription_info['trial_per'] : (int) ywsbs_get_product_trial( $product );
					$trial_is_per      = apply_filters( 'ywsbs_trial_in_gateway', $trial_is_per, $product_id, $order->get_customer_id() );
					$trial_time_option = isset( $subscription_info['trial_time_option'] ) ? $subscription_info['trial_time_option'] : (int) $product->get_meta( '_ywsbs_trial_time_option' );
					$trial_time_option = ywsbs_get_price_time_option_paypal( apply_filters( 'ywsbs_trial_time_' . $product_id, $trial_time_option ) );

					// order total.
					$order_total = $order->get_total();

					$next_payment_due_date = $subscription_info['next_payment_due_date'];
					if ( isset( $subscription_info['sync'] ) && $subscription_info['sync'] && $next_payment_due_date ) {
						// Calculate the trial periods.
						$trial_periods = $this->calculate_trial_periods( $next_payment_due_date );
						if ( isset( $trial_periods['p1'] ) ) {
							$args['a1'] = wc_format_decimal( $order_total, 2 );
							$args['p1'] = $trial_periods['p1'];
							$args['t1'] = $trial_periods['t1'];
						}

						if ( isset( $trial_periods['p2'] ) ) {
							$args['a2'] = 0;
							$args['p2'] = $trial_periods['p2'];
							$args['t2'] = $trial_periods['t2'];
						}
					} else {
						if ( ! empty( $trial_is_per ) ) {
							$args['a1'] = wc_format_decimal( $order_total, 2 );
							$args['p1'] = $trial_is_per;
							$args['t1'] = $trial_time_option;
						} else {
							if ( $subscription_info['order_total'] !== $order_total ) {
								$args['a1'] = wc_format_decimal( $order->get_total(), 2 );
								$args['p1'] = $price_is_per;
								$args['t1'] = $price_time_option;
							}
						}
					}

					$subscription_num = ( $max_length ) ? $max_length / $price_is_per : '';

					$args['a3'] = wc_format_decimal( $subscription_info['order_total'], 2 );
					$args['p3'] = $price_is_per;
					$args['t3'] = $price_time_option;

					if ( '' === $subscription_num || $subscription_num > 1 ) {
						$args['src'] = 1;
						if ( '' !== $subscription_num ) {
							$args['srt'] = $subscription_num;
						}
					} else {
						$args['src'] = 0;
					}
				}

				if ( $order_item['qty'] > 1 ) {
					$item_names[] = $order_item['qty'] . ' x ' . $this->format_item_name( $order_item['name'] );
				} else {
					$item_names[] = $this->format_item_name( $order_item['name'] );
				}
			}

			if ( ! $has_subscription ) {
				return $args;
			}

			if ( count( $item_names ) > 1 ) {
				// translators: placeholder is the order number.
				$args['item_name'] = $this->format_item_name( sprintf( __( 'Order %s', 'yith-woocommerce-subscription' ), $order->get_order_number() . ' - ' . implode( ', ', $item_names ) ) );
			} else {
				$args['item_name'] = implode( ', ', $item_names );
			}

			$args['rm'] = 2;
			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Subscription Request: ' . print_r( $args, true ) ); //phpcs:ignore
			}

			return $args;
		}

		/**
		 * Get the order info from PayPal Arguments
		 *
		 * @param array $args PayPal arguments.
		 *
		 * @return array
		 */
		protected function get_order_info( $args ) {
			$order_info = array();
			if ( isset( $args['custom'] ) ) {
				$order_info = json_decode( $args['custom'], true );
			}

			return $order_info;
		}


		/**
		 * Format item name
		 *
		 * @param string $item_name Item name.
		 *
		 * @return string
		 */
		protected static function format_item_name( $item_name ) {
			if ( strlen( $item_name ) > 127 ) {
				$item_name = substr( $item_name, 0, 124 ) . '...';
			}

			return html_entity_decode( $item_name, ENT_NOQUOTES, 'UTF-8' );
		}

		/**
		 * Cancel recurring payment if the subscription has a paypal subscription
		 *
		 * @param bool               $result Bool.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return bool
		 */
		public function cancel_recurring_payment( $result, $subscription ) {

			if ( $subscription->get( 'paypal_subscriber_id' ) === '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->get( 'paypal_subscriber_id' ), 'Cancel' );

			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Subscription Cancel Request: #' . $subscription->get_id() . '. Details of response: ' . print_r( $response, true ) ); // phpcs:ignore
			}

			if ( ! $response ) {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'cancelled', 'error', $subscription->get_order_id(), __( 'Paypal Recurring payment was not cancelled ', 'yith-woocommerce-subscription' ) . $response );
				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'cancelled', 'success', $subscription->get_order_id(), __( 'Paypal Recurring payment cancelled', 'yith-woocommerce-subscription' ) );
				return $result;
			}
		}

		/**
		 * Resume recurring payment if the subscription has a PayPal subscription paused
		 *
		 * @param bool               $result Bool.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return bool
		 */
		public function resume_recurring_payment( $result, $subscription ) {

			if ( $subscription->get( 'paypal_subscriber_id' ) === '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->get( 'paypal_subscriber_id' ), 'Reactivate' );

			if ( ! $response ) {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'resumed', 'error', $subscription->get_order_id(), __( 'Paypal Recurring payment was not resumed ', 'yith-woocommerce-subscription' ) . $response );
				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'resumed', 'success', $subscription->get_order_id(), __( 'Paypal Recurring payment resumed', 'yith-woocommerce-subscription' ) );
				return true;
			}

		}

		/**
		 * Suspend recurring payment if the subscription has a PayPal subscription paused
		 *
		 * @param bool               $result Bool.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return bool
		 */
		public function suspend_recurring_payment( $result, $subscription ) {

			if ( $subscription->get( 'paypal_subscriber_id' ) === '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->get( 'paypal_subscriber_id' ), 'Suspend' );

			if ( ! $response ) {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'paused', 'error', $subscription->get_order_id(), __( 'Paypal Recurring payment was not suspended ', 'yith-woocommerce-subscription' ) . $response );
				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->get_id(), 'paused', 'success', $subscription->get_order_id(), __( 'Paypal Recurring payment paused', 'yith-woocommerce-subscription' ) );
				return true;
			}
		}

		/**
		 * Change the status to a PayPal Subscription.
		 *
		 * @param string $subscriber_id Subscriber ID.
		 * @param string $status New status.
		 *
		 * @return bool
		 */
		public function change_paypal_subscription_status( $subscriber_id, $status ) {

			$response = $this->change_subscription_status( $subscriber_id, $status );

			if ( ! empty( $response ) ) {
				if ( 'Failure' === $response['ACK'] ) {
					if ( $this->debug ) {
						$this->wclog->add( 'paypal', "YSBS - Paypal was called to change status for '. $subscriber_id.' has Failed: " . $response['L_LONGMESSAGE0'] );
					}

					return $response['L_LONGMESSAGE0'];
				} else {
					return true;
				}
			}

		}

		/**
		 * Do the request to PayPal to change the Subscription status.
		 *
		 * @param string $subscriber_id Subscriber ID.
		 * @param string $status New status.
		 *
		 * @return mixed
		 */
		public function change_subscription_status( $subscriber_id, $status ) {

			// translators: Placeholder 1 new status, 2 blog name.
			$api_request = 'USER=' . rawurlencode( $this->api_username )
				. '&PWD=' . rawurlencode( $this->api_password )
				. '&SIGNATURE=' . rawurlencode( $this->api_signature )
				. '&VERSION=76.0'
				. '&METHOD=ManageRecurringPaymentsProfileStatus'
				. '&PROFILEID=' . rawurlencode( $subscriber_id )
				. '&ACTION=' . rawurlencode( $status )
				. '&NOTE=' . rawurlencode( sprintf( __( 'Subscription %1$s at %2$s', 'yith-woocommerce-subscription' ), strtolower( $status ), get_bloginfo( 'name' ) ) ); // phpcs:ignore

			$ch = curl_init(); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_init
			curl_setopt( $ch, CURLOPT_URL, $this->api_endpoint ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt
			curl_setopt( $ch, CURLOPT_VERBOSE, 1 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt

			// Uncomment these to turn off server and peer verification.
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt
			curl_setopt( $ch, CURLOPT_POST, 1 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt

			// Set the API parameters for this transaction.
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request ); // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_setopt

			// Request response from PayPal.
			$response = curl_exec( $ch ); //phpcs:ignore

			// If no response was received from PayPal there is no point parsing the response.
			if ( ! $response ) {
				if ( $this->debug ) {
					$this->wclog->add( 'paypal', 'YSBS - Paypal was called to change status for  ' . $subscriber_id . 'has failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' ); //phpcs:ignore
				}
			}
			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Paypal was called to change status for  ' . print_r( $response, true ) ); //phpcs:ignore
			}

			curl_close( $ch ); // phpcs:ignore
			// An associative array is more usable than a parameter string.
			parse_str( $response, $parsed_response );

			return $parsed_response;
		}

		/**
		 * Get the recurring payment profile of a subscriber id.
		 *
		 * @param string $subscriber_id Subscriber ID.
		 *
		 * @return bool
		 */
		public function get_recurring_payments_profile( $subscriber_id ) {

			$request_data = array(
				'VERSION'           => '115.0',
				'USER'              => $this->api_username . 'DFG',
				'PWD'               => $this->api_password,
				'SIGNATURE'         => $this->api_signature,
				'METHOD'            => 'UpdateRecurringPaymentsProfile',
				'MAXFAILEDPAYMENTS' => 10,
				'PROFILEID'         => $subscriber_id,
			);

			$response = wp_remote_post(
				$this->api_endpoint,
				array(
					'method'    => 'POST',
					'body'      => $request_data,
					'timeout'   => 100,
					'sslverify' => false,
				)
			);

			if ( empty( $response['body'] ) && $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS -Paypal was called has failed: Empty Paypal Response.' . print_r( $request_data, true ) ); //phpcs:ignore
			}

			if ( isset( $response['response']['message'] ) && 'OK' === $response['response']['message'] ) {
				$response_args = wp_parse_args( $response['body'] );
				$this->wclog->add( 'paypal', print_r( $response_args, true ) ); //phpcs:ignore
				if ( 'Failure' === $response_args['ACK'] ) {
					if ( $this->debug ) {
						$this->wclog->add( 'paypal', "YSBS - Paypal was called to change status for '. $subscriber_id.' has Failed: " . $response_args['L_LONGMESSAGE0'] );
					}

					return $response_args['L_LONGMESSAGE0'];
				} else {
					return true;
				}
			}
		}




		/**
		 * Reset the PDT Payment Response if the order has a subscription.
		 */
		public function check_pdt_response() {
			if ( empty( $_REQUEST['cm'] ) || empty( $_REQUEST['tx'] ) || empty( $_REQUEST['st'] ) ) { // phpcs:ignore Input var ok, CSRF ok, sanitization ok.
				return;
			}

			$order_info = json_decode( wc_clean( wp_unslash( $_REQUEST['cm'] ) ), true ); //phpcs:ignore
			$order      = wc_get_order( $order_info['order_id'] );

			if ( $order ) {
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( ! empty( $subscriptions ) ) {
					$_REQUEST['cm'] = '';
				}
			}

		}

		/**
		 * Return the trial periods for register the subscription on PayPal.
		 *
		 * @param int $next_payment_due_date Timestamp.
		 * @return array
		 * @since 2.1.0
		 */
		private function calculate_trial_periods( $next_payment_due_date ) {
			$args      = array();
			$days_left = ( $next_payment_due_date - time() ) / DAY_IN_SECONDS;
			if ( $days_left <= 0 ) {
				return false;
			} elseif ( $days_left > 0 && $days_left <= 1 ) {
				$args['p1'] = 1;
				$args['t1'] = 'D';
			} else {
				$days_left         = round( $days_left );
				$first_calculation = $this->calculate_single_period( $days_left );

				$args['p1'] = $first_calculation['value'];
				$args['t1'] = $first_calculation['period'];

				$days_left = $first_calculation['day_left'];

				if ( $days_left > 0 ) {
					$second_calculation = $this->calculate_single_period( $days_left );
					$args['p2']         = $second_calculation['value'];
					$args['t2']         = $second_calculation['period'];
				}
			}

			return $args;
		}

		/**
		 * Calculate single trial period.
		 *
		 * @param int $days Num of day left.
		 * @return array
		 * @since 2.1.0
		 */
		private function calculate_single_period( $days ) {

			$result = array();
			if ( $days <= 90 ) {
				$result['value']    = $days;
				$result['period']   = 'D';
				$result['day_left'] = 0;
			} else {
				$weeks = floor( $days / 7 );
				if ( $weeks <= 52 ) {
					$result['value']    = $weeks;
					$result['period']   = 'W';
					$result['day_left'] = $days % 7;
				}
				$months = floor( $days / 30 );
				if ( $months <= 24 ) {
					$result['value']    = $months;
					$result['period']   = 'M';
					$result['day_left'] = $days % 30;
				}
			}

			return $result;
		}
	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Paypal class
 *
 * @return YWSBS_Subscription_Paypal
 */
function YWSBS_Subscription_Paypal() { // phpcs:ignore
	return YWSBS_Subscription_Paypal::get_instance();
}

