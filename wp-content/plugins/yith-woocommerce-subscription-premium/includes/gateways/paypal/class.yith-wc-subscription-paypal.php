<?php


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Paypal Class
 *
 * @class   YWSBS_Subscription_Paypal
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Paypal' ) ) {

	class YWSBS_Subscription_Paypal {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Cron
		 */
		protected static $instance;

		protected $wclog = '';

		protected $debug;
		protected $testmode;
		protected $email;
		protected $receiver_email;

		protected $api_username;
		protected $api_password;
		protected $api_signature;
		protected $api_endpoint;

		protected $setting_options;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_Paypal
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			$settings = get_option( 'woocommerce_paypal_settings' );

			if ( ! isset( $settings['enabled'] ) || $settings['enabled'] != 'yes' ) {
				return;
			}

			$this->setting_options = $settings;
			$this->debug           = ( isset( $settings['debug'] ) && $settings['debug'] == 'yes' ) ? true : false;
			$this->testmode        = ( isset( $settings['testmode'] ) && $settings['testmode'] == 'yes' ) ? true : false;
			$this->email           = ( isset( $settings['email'] ) ) ? $settings['email'] : '';
			$this->receiver_email  = ( isset( $settings['receiver_email'] ) ) ? $settings['receiver_email'] : $this->email;
			$option_suffix         = $this->testmode ? 'sandbox_' : '';
			if ( $this->debug ) {
				$this->wclog = new WC_Logger();
			}

			// When necessary, set the PayPal args to be for a subscription instead of shopping cart
			add_filter( 'woocommerce_paypal_args', array( $this, 'subscription_args' ) );

			// Check if there's a subcription in a valid PayPal IPN request
			include_once WC()->plugin_path() . '/includes/gateways/paypal/includes/class-wc-gateway-paypal-ipn-handler.php';
			include_once 'includes/class.ywsbs-paypal-ipn-handler.php';

			new YWSBS_PayPal_IPN_Handler( $this->testmode, $this->receiver_email );

			// Set API credentials
			if ( ! empty( $settings[ $option_suffix . 'api_username' ] ) && ! empty( $settings[ $option_suffix . 'api_password' ] ) && ! empty( $settings[ $option_suffix . 'api_signature' ] ) ) {

				$this->api_username  = $settings[ $option_suffix . 'api_username' ];
				$this->api_password  = $settings[ $option_suffix . 'api_password' ];
				$this->api_signature = $settings[ $option_suffix . 'api_signature' ];
				$this->api_endpoint  = ( $settings['testmode'] == 'yes' ) ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';

				add_filter( 'ywsbs_suspend_recurring_payment', array( $this, 'suspend_recurring_payment' ), 10, 2 );
				add_filter( 'ywsbs_resume_recurring_payment', array( $this, 'resume_recurring_payment' ), 10, 2 );
				add_filter( 'ywsbs_cancel_recurring_payment', array( $this, 'cancel_recurring_payment' ), 10, 2 );
			}

			add_filter( 'ywsbs_trial_in_gateway', array( $this, 'change_trial_in_gateway' ), 10, 3 );
			add_filter(
				'woocommerce_payment_gateway_supports',
				array(
					$this,
					'add_subscription_support_to_paypal_standard',
				),
				10,
				3
			);

		}

		/**
		 * Add yith_subscriptions support to PayPal Standard.
		 *
		 * @param $support
		 * @param $feature
		 * @param $gateway
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_subscription_support_to_paypal_standard( $support, $feature, $gateway ) {
			$supports = array(
				'yith_subscriptions',
				'yith_subscription_pause',
				'yith_subscription_pay_method_customer',
			);
			if ( in_array( $feature, $supports ) && 'paypal' == $gateway ) {
				$support = true;
			}

			return $support;
		}


		/**
		 * @param $args
		 *
		 * @return mixed
		 */
		public function subscription_args( $args ) {

			$order_info = $this->get_order_info( $args );

			if ( empty( $order_info ) || ! isset( $order_info['order_id'] ) ) {
				return $args;
			}

			$order = wc_get_order( $order_info['order_id'] );

			$is_a_renew = yit_get_prop( $order, 'is_a_renew' );

			if ( $is_a_renew == 'yes' ) {
				return $args;
			}

			// check if order has subscriptions
			$order_items = $order->get_items();

			if ( empty( $order_items ) ) {
				return $args;
			}

			$item_names       = array();
			$has_subscription = false;

			foreach ( $order_items as $key => $order_item ) {

				$product_id = ( $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];
				$product    = wc_get_product( $product_id );

				if ( YITH_WC_Subscription()->is_subscription( $product_id ) ) {
					// It's a subscription
					$has_subscription = true;
					$args['cmd']      = '_xclick-subscriptions';

					// 1 for reattempt failed recurring payments before canceling, use 0 for not
					$args['sra'] = apply_filters( 'ywsbs_reattempt_failed_recurring_payments', 1 );

					$subscription_info = wc_get_order_item_meta( $key, '_subscription_info', true );

					$price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
					$price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );
					$price_time_option = ywsbs_get_price_time_option_paypal( $price_time_option );
					$max_length        = yit_get_prop( $product, '_ywsbs_max_length' );

					// trial options
					$trial_switch_info = get_user_meta( get_current_user_id(), 'ywsbs_trial_' . $product_id, true );
					if ( $trial_switch_info != '' ) {
						$trial_info        = $trial_switch_info;
						$trial_is_per      = $trial_info['trial_days'];
						$trial_time_option = ywsbs_get_price_time_option_paypal( 'days' );
					} else {
						$user_id           = method_exists( $order, 'get_customer_id' ) ? $order->get_customer_id() : yit_get_prop( $order, '_customer_user' );
						$trial_is_per      = apply_filters( 'ywsbs_trial_in_gateway', yit_get_prop( $product, '_ywsbs_trial_per' ), $product_id, $user_id );
						$trial_time_option = ywsbs_get_price_time_option_paypal( apply_filters( 'ywsbs_trial_time_' . $product_id, yit_get_prop( $product, '_ywsbs_trial_time_option' ) ) );
					}

					// order total
					$order_total = $order->get_total();

					if ( $trial_is_per != '' && $trial_is_per != 0 ) {
						$args['a1'] = wc_format_decimal( $order_total, 2 );
						$args['p1'] = $trial_is_per;
						$args['t1'] = $trial_time_option;
					} else {
						if ( $subscription_info['order_total'] != $order->get_total() ) {
							$args['a1'] = wc_format_decimal( $order->get_total(), 2 );
							$args['p1'] = $price_is_per;
							$args['t1'] = $price_time_option;
						}
					}

					$subscription_num = ( $max_length ) ? $max_length / $price_is_per : '';

					$args['a3'] = wc_format_decimal( $subscription_info['order_total'], 2 );
					$args['p3'] = $price_is_per;
					$args['t3'] = $price_time_option;

					if ( $subscription_num == '' || $subscription_num > 1 ) {
						$args['src'] = 1;
						if ( $subscription_num != '' ) {
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
				$args['item_name'] = $this->format_item_name( sprintf( __( 'Order %s', 'yith-woocommerce-subscription' ), $order->get_order_number() . ' - ' . implode( ', ', $item_names ) ) );
			} else {
				$args['item_name'] = implode( ', ', $item_names );
			}

			$args['rm'] = 2;
			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Subscription Request: ' . print_r( $args, true ) );
			}

			return $args;
		}

		/**
		 * @param $args
		 *
		 * @return array|mixed|object
		 */
		protected function get_order_info( $args ) {
			if ( isset( $args['custom'] ) ) {
				$order_info = json_decode( $args['custom'], true );
			}

			return $order_info;
		}


		/**
		 * @param $item_name
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
		 * @param bool               $result
		 * @param YWSBS_Subscription $subscription
		 *
		 * @return bool
		 */
		public function cancel_recurring_payment( $result, $subscription ) {

			if ( ! isset( $subscription->paypal_subscriber_id ) || $subscription->paypal_subscriber_id == '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->paypal_subscriber_id, 'Cancel' );

			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Subscription Cancel Request: #' . $subscription->id . '. Details of response: ' . print_r( $response, true ) );
			}

			if ( $response !== true ) {
				YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'error', $subscription->order_id, __( 'Paypal Recurring payment was not cancelled ', 'yith-woocommerce-subscription' ) . $response );

				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'success', $subscription->order_id, __( 'Paypal Recurring payment cancelled', 'yith-woocommerce-subscription' ) );

				return $result;
			}
		}

		/**
		 * @param $result
		 * @param $subscription
		 *
		 * @return bool
		 */
		public function resume_recurring_payment( $result, $subscription ) {

			if ( ! isset( $subscription->paypal_subscriber_id ) || $subscription->paypal_subscriber_id == '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->paypal_subscriber_id, 'Reactivate' );

			if ( $response !== true ) {
				YITH_WC_Activity()->add_activity( $subscription->id, 'resumed', 'error', $subscription->order_id, __( 'Paypal Recurring payment was not resumed ', 'yith-woocommerce-subscription' ) . $response );

				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->id, 'resumed', 'success', $subscription->order_id, __( 'Paypal Recurring payment resumed', 'yith-woocommerce-subscription' ) );

				return true;
			}

		}

		/**
		 * @param $result
		 * @param $subscription
		 *
		 * @return bool
		 */
		public function suspend_recurring_payment( $result, $subscription ) {

			if ( ! isset( $subscription->paypal_subscriber_id ) || $subscription->paypal_subscriber_id == '' ) {
				return true;
			}

			$response = $this->change_paypal_subscription_status( $subscription->paypal_subscriber_id, 'Suspend' );

			if ( $response !== true ) {
				YITH_WC_Activity()->add_activity( $subscription->id, 'paused', 'error', $subscription->order_id, __( 'Paypal Recurring payment was not suspended ', 'yith-woocommerce-subscription' ) . $response );

				return false;
			} else {
				YITH_WC_Activity()->add_activity( $subscription->id, 'paused', 'success', $subscription->order_id, __( 'Paypal Recurring payment paused', 'yith-woocommerce-subscription' ) );

				return true;
			}
		}

		/**
		 * @param $subscriber_id
		 * @param $status
		 *
		 * @return bool
		 */
		public function change_paypal_subscription_status( $subscriber_id, $status ) {

			$response = $this->change_subscription_status( $subscriber_id, $status );

			if ( ! empty( $response ) ) {

				if ( $response['ACK'] == 'Failure' ) {
					if ( $this->debug ) {
						$this->wclog->add( 'paypal', "YSBS - Paypal was called to change status for '. $subscriber_id.' has Failed: " . $response['L_LONGMESSAGE0'] );
					}

					return $response['L_LONGMESSAGE0'];
				} else {
					return true;
				}
			}

		}

		function change_subscription_status( $subscriber_id, $status ) {

			$api_request = 'USER=' . urlencode( $this->api_username )
						   . '&PWD=' . urlencode( $this->api_password )
						   . '&SIGNATURE=' . urlencode( $this->api_signature )
						   . '&VERSION=76.0'
						   . '&METHOD=ManageRecurringPaymentsProfileStatus'
						   . '&PROFILEID=' . urlencode( $subscriber_id )
						   . '&ACTION=' . urlencode( $status )
						   . '&NOTE=' . urlencode( sprintf( __( 'Subscription %1$s at %2$s', 'yith-woocommerce-subscription' ), strtolower( $status ), get_bloginfo( 'name' ) ) );

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $this->api_endpoint ); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
			curl_setopt( $ch, CURLOPT_VERBOSE, 1 );

			// Uncomment these to turn off server and peer verification
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_POST, 1 );

			// Set the API parameters for this transaction
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );

			// Request response from PayPal
			$response = curl_exec( $ch );

			// If no response was received from PayPal there is no point parsing the response
			if ( ! $response ) {
				if ( $this->debug ) {
					$this->wclog->add( 'paypal', 'YSBS - Paypal was called to change status for  ' . $subscriber_id . 'has failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );
				}
			}
			if ( $this->debug ) {
				$this->wclog->add( 'paypal', 'YSBS - Paypal was called to change status for  ' . print_r( $response, true ) );
			}

			curl_close( $ch );
			// An associative array is more usable than a parameter string
			parse_str( $response, $parsed_response );

			return $parsed_response;
		}

		/**
		 * @param $subscriber_id
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
				$this->wclog->add( 'paypal', 'YSBS -Paypal was called has failed: Empty Paypal Response.' . print_r( $request_data, true ) );
			}

			if ( isset( $response['response']['message'] ) && $response['response']['message'] == 'OK' ) {
				$response_args = wp_parse_args( $response['body'] );
				$this->wclog->add( 'paypal', print_r( $response_args, true ) );
				if ( $response_args['ACK'] == 'Failure' ) {
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
		 * Check if there are subscription upgrade in progress and change the trial options
		 *
		 * @param int        $trial
		 * @param     $product_id
		 *
		 * @param     $user_id
		 *
		 * @return int|string
		 * @internal param array $cart_item
		 */
		public function change_trial_in_gateway( $trial, $product_id, $user_id ) {

			$new_trial = $trial;

			/*
			 UPGRADE PROCESS */
			// add fee is gap payment is available and choosed b user
			$subscription_info = get_user_meta( $user_id, 'ywsbs_upgrade_' . $product_id, true );
			if ( ! empty( $subscription_info ) ) {
				return '';
			}

			/* DOWNGRADE PROCESS */
			$subscription_trial_info = get_user_meta( $user_id, 'ywsbs_trial_' . $product_id, true );
			if ( ! empty( $subscription_trial_info ) ) {
				$new_trial = $subscription_trial_info['trial_days'];
			}

			return $new_trial;

		}

	}

}

/**
 * Unique access to instance of YWSBS_Subscription_Paypal class
 *
 * @return \YWSBS_Subscription_Paypal
 */
function YWSBS_Subscription_Paypal() {
	return YWSBS_Subscription_Paypal::get_instance();
}

