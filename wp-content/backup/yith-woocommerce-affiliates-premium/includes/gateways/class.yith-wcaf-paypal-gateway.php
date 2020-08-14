<?php
/**
 * Paypal Gateway class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Paypal_Gateway' ) ) {
	/**
	 * WooCommerce Paypal Gateway
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Paypal_Gateway {

		/**
		 * Status for payments correctly sent
		 *
		 * @cont  string Status for payments correctly sent
		 * @since 1.0.0
		 */
		const PAYMENT_STATUS_OK = 'Success';

		/**
		 * Status for payments failed
		 *
		 * @cont  string Status for payments failed
		 * @since 1.0.0
		 */
		const PAYMENT_STATUS_FAIL = 'Failure';

		/**
		 * Whether to activate PayPal sandbox or not
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_enable_sandbox = 'no';

		/**
		 * Whether to activate PayPal operations log or not
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_enable_log = 'yes';

		/**
		 * PayPal admin account API username
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_api_username = '';

		/**
		 * PayPal admin account API password
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_api_password = '';

		/**
		 * PayPal admin account API signature
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_api_signature = '';

		/**
		 * Payment notification email subject
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_payment_email_subject = '';

		/**
		 * WC Logger instance
		 *
		 * @var \WC_Logger
		 * @since 1.0.0
		 */
		protected $_log = null;

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Paypal_Gateway
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Paypal_Gateway
		 * @since 1.0.0
		 */
		public function __construct() {
			// init class attributes
			$this->_retrieve_options();

			// add paypal options to settings page
			add_filter( 'yith_wcaf_gateway_options', array( $this, 'add_gateway_options' ) );

			// add IPN handling
			add_action( 'init', array( $this, 'handle_notification' ), 15 );
		}

		/* === INIT METHODS === */

		/**
		 * Retrieve stored gateway options, and save them in class attributes
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			$this->_enable_sandbox        = get_option( 'yith_wcaf_paypal_enable_sandbox', $this->_enable_sandbox );
			$this->_enable_log            = get_option( 'yith_wcaf_paypal_enable_log', $this->_enable_log );
			$this->_api_username          = get_option( 'yith_wcaf_paypal_api_username', $this->_api_username );
			$this->_api_password          = get_option( 'yith_wcaf_paypal_api_password', $this->_api_password );
			$this->_api_signature         = get_option( 'yith_wcaf_paypal_api_signature', $this->_api_signature );
			$this->_payment_email_subject = get_option( 'yith_wcaf_paypal_email_subject', $this->_payment_email_subject );
		}

		/**
		 * Filters settings array, adding paypal gateway options
		 *
		 * @param $gateway_options array Gateway Options
		 *
		 * @return array Filtered gateway option
		 * @since 1.0.0
		 */
		public function add_gateway_options( $gateway_options ) {
			$gateway_options = array_merge(
				$gateway_options,
				array(
					'paypal-options' => array(
						'title' => __( 'PayPal Payment Gateway', 'yith-woocommerce-affiliates' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'yith_wcaf_paypal_options'
					),

					'paypal-enable-sandbox' => array(
						'title'   => __( 'Enable PayPal sandbox', 'yith-woocommerce-affiliates' ),
						'type'    => 'checkbox',
						'desc'    => __( 'Payments will be issued to PayPal sandbox server and will only be used for testing purposes', 'yith-woocommerce-affiliates' ),
						'id'      => 'yith_wcaf_paypal_enable_sandbox',
						'default' => 'no'
					),

					'paypal-enable-log' => array(
						'title'   => __( 'Enable PayPal log', 'yith-woocommerce-affiliates' ),
						'type'    => 'checkbox',
						'desc'    => sprintf( __( 'PayPal operations will be logged in <code>%s</code>', 'yith-woocommerce-affiliates' ), wc_get_log_file_path( 'yith_wcaf_paypal' ) ),
						'id'      => 'yith_wcaf_paypal_enable_log',
						'default' => 'yes'
					),

					'paypal-api-username' => array(
						'title'    => __( 'PayPal API username', 'yith-woocommerce-affiliates' ),
						'type'     => 'text',
						'desc'     => __( 'PayPal API username, used to access PayPal account via API requests', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_paypal_api_username',
						'default'  => '',
						'css'      => 'width: 400px;',
						'desc_tip' => true
					),

					'paypal-api-password' => array(
						'title'    => __( 'PayPal API password', 'yith-woocommerce-affiliates' ),
						'type'     => 'text',
						'desc'     => __( 'PayPal API password, used to access PayPal account via API requests', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_paypal_api_password',
						'default'  => '',
						'css'      => 'width: 400px;',
						'desc_tip' => true
					),

					'paypal-api-signature' => array(
						'title'    => __( 'PayPal API signature', 'yith-woocommerce-affiliates' ),
						'type'     => 'text',
						'desc'     => __( 'PayPal API signature, used to access PayPal account via API requests', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_paypal_api_signature',
						'default'  => '',
						'css'      => 'width: 400px;',
						'desc_tip' => true
					),

					'paypal-payment-email-subject' => array(
						'title'    => __( 'Payment mail subject', 'yith-woocommerce-affiliates' ),
						'type'     => 'text',
						'desc'     => __( 'Subject of the email sent by PayPal to customers when payment request is registered', 'yith-woocommerce-affiliates' ),
						'id'       => 'yith_wcaf_paypal_email_subject',
						'default'  => '',
						'css'      => 'width: 400px;',
						'desc_tip' => true
					),

					'paypal-notification-url' => array(
						'title'             => __( 'PayPal notification URL', 'yith-woocommerce-affiliates' ),
						'type'              => 'text',
						'desc'              => __( 'Copy this URL and set it into PayPal admin panel, to receive IPN from their server', 'yith-woocommerce-affiliates' ),
						'id'                => 'yith_wcaf_paypal_ipn_notification_url',
						'default'           => site_url() . '/?paypal_ipn_response=true',
						'css'               => 'width: 400px;',
						'desc_tip'          => true,
						'custom_attributes' => array(
							'readonly' => 'readonly'
						)
					),

					'paypal-options-end' => array(
						'type' => 'sectionend',
						'id'   => 'yith_wcaf_paypal_options'
					)
				)
			);

			return $gateway_options;
		}

		/* === PAYMENT METHODS === */

		/**
		 * Execute a mass payment
		 *
		 * @param $payments_id array Array of registered payments to issue to paypal servers
		 *
		 * @return mixed Array with operation status and messages
		 * @since 1.0.0
		 */
		public function pay( $payments_id ) {
			// include required libraries
			require_once( YITH_WCAF_INC . 'third-party/PayPal/PayPal.php' );

			if ( empty( $this->_api_username ) || empty( $this->_api_password ) || empty( $this->_api_signature ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'Missing required parameters for PayPal configuration', 'yith-woocommerce-affiliates' )
				);
			}

			$PayPalConfig = array(
				'Sandbox'      => ! ( $this->_enable_sandbox == 'no' ),
				'APIUsername'  => $this->_api_username,
				'APIPassword'  => $this->_api_password,
				'APISignature' => $this->_api_signature,
				'PrintHeaders' => true,
				'LogResults'   => false,
			);

			$PayPal = new angelleye\PayPal\PayPal( $PayPalConfig );

			// Prepare request arrays
			$MPFields = array(
				'emailsubject' => $this->_payment_email_subject,
				// The subject line of the email that PayPal sends when the transaction is completed.  Same for all recipients.  255 char max.
				'currencycode' => get_woocommerce_currency(),
				// Three-letter currency code.
				'receivertype' => 'EmailAddress'
				// Indicates how you identify the recipients of payments in this call to MassPay.  Must be EmailAddress or UserID
			);

			$MPItems = array();

			$mass_pay_payments = array();

			// if single payment id, convert it to array
			if ( ! is_array( $payments_id ) ) {
				$payments_id = (array) $payments_id;
			}

			foreach ( $payments_id as $payment_id ) {
				$payment = YITH_WCAF_Payment_Handler()->get_payment( $payment_id );

				if ( ! $payment || empty( $payment['payment_email'] ) ) {
					continue;
				}

				$mass_pay_payments[] = $payment;

				$MPItems[] = array(
					'l_email'    => $payment['payment_email'],
					// Required.  Email address of recipient.  You must specify either L_EMAIL or L_RECEIVERID but you must not mix the two.
					'l_amt'      => round( $payment['amount'], wc_get_price_decimals() ),
					// Required.  Payment amount.
					'l_uniqueid' => $payment['ID']
					// Transaction-specific ID number for tracking in an accounting system.
				);
			}

			if ( empty( $MPItems ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'No record could be processed for PayPal payment; payment email field is mandatory', 'yith-woocommerce-affiliates' )
				);
			}

			$PayPalRequestData = array( 'MPFields' => $MPFields, 'MPItems' => $MPItems );
			$PayPalResult      = $PayPal->MassPay( $PayPalRequestData );

			if ( $this->_enable_log == 'yes' ) {
				$this->log( __( 'Request correctly sent', 'yith-woocommerce-affiliates' ), false, $PayPalRequestData );
				$this->log( __( 'Paypal server response correctly received', 'yith-woocommerce-affiliates' ), false, $PayPalResult );
			}

			if ( ! empty( $mass_pay_payments ) ) {
				foreach ( $mass_pay_payments as $sent_payment ) {
					YITH_WCAF_Payment_Handler()->add_note( array(
						'payment_id'   => $sent_payment['ID'],
						'note_content' => __( 'Payment correctly issued to PayPal', 'yith-woocommerce-affiliates' )
					) );
				}
			}

			$errors = array();
			if ( $PayPalResult['ACK'] == self::PAYMENT_STATUS_FAIL ) {
				foreach ( $PayPalResult['ERRORS'] as $error ) {
					$errors[] = $error['L_LONGMESSAGE'];
				}
			}

			// if payment was correctly registered by PayPal servers, change commissions and payments status
			if ( $PayPalResult['ACK'] == self::PAYMENT_STATUS_OK ) {
				if ( ! empty( $mass_pay_payments ) ) {
					foreach ( $mass_pay_payments as $payment ) {
						YITH_WCAF_Payment_Handler()->change_payment_status( $payment['ID'], 'pending' );
						do_action( 'yith_wcaf_payment_sent', $payment );
					}
				}
			}

			return array(
				'status'   => $PayPalResult['ACK'] == self::PAYMENT_STATUS_OK,
				'messages' => ( $PayPalResult['ACK'] == self::PAYMENT_STATUS_FAIL ) ? $errors : __( 'Payment sent', 'yith-woocommerce-affiliates' )
			);
		}

		/**
		 * Handle PayPal IPN
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_notification() {
			if ( isset( $_GET['paypal_ipn_response'] ) && $_GET['paypal_ipn_response'] == true ) {
				$verified = false;

				// include required libraries
				require( YITH_WCAF_INC . '/third-party/IPNListener/ipnlistener.php' );

				$listener               = new IpnListener();
				$listener->force_ssl_v4 = apply_filters( 'yith_wcaf_ipn_listener_force_ssl_v4', false );
				$listener->use_sandbox  = ! ( $this->_enable_sandbox == 'no' );

				try {
					// process IPN request, require validation to PayPal server
					$listener->requirePostMethod();
					$verified = $listener->processIpn();

				} catch ( Exception $e ) {
					// fatal error trying to process IPN.
					if ( $this->_enable_log == 'yes' ) {
						$this->log( $listener->getTextReport(), true );
					}
					die();
				}

				// if PayPal says IPN is valid, process content
				if ( $verified ) {
					$request_data = $_POST;

					if ( ! isset( $request_data['payment_status'] ) ) {
						if ( $this->_enable_log == 'yes' ) {
							$this->log( __( 'Invalid status', 'yith-woocommerce-affiliates' ) );
						}
						die();
					}

					// format payment data
					$payment_data = array();
					for ( $i = 1; array_key_exists( 'status_' . $i, $request_data ); $i ++ ) {
						$data_index = array_keys( $request_data );

						foreach ( $data_index as $index ) {
							if ( strpos( $index, '_' . $i ) ) {
								$payment_data[ $i ][ str_replace( '_' . $i, '', $index ) ] = $request_data[ $index ];
								unset( $request_data[ $index ] );
							}
						}
					}

					$request_data['payment_data'] = $payment_data;

					if ( ! empty( $payment_data ) ) {
						foreach ( $payment_data as $payment ) {
							if ( ! isset( $payment['unique_id'] ) ) {
								continue;
							}

							$args                   = array();
							$args['unique_id']      = $payment['unique_id'];
							$args['gross']          = round( floatval( $payment['mc_gross'] ), 3 );
							$args['status']         = $payment['status'];
							$args['receiver_email'] = $payment['receiver_email'];
							$args['currency']       = $payment['mc_currency'];
							$args['txn_id']         = $payment['masspay_txn_id'];

							if ( $this->_enable_log == 'yes' ) {
								$this->log( 'notification received - ' . $payment['unique_id'] );
								$this->log( $listener->getTextReport(), true );
							}

							$record = YITH_WCAF_Payment_Handler()->get_payment( $payment['unique_id'] );

							if ( $record ) {

								if ( $args['status'] == 'pending' ) {
									$amount = round( floatval( $payment['amount'] ), 3 );

									if ( $amount != $args['gross'] ) {
										if ( $this->_enable_log == 'yes' ) {
											$this->log( sprintf( __( 'PayPal returned a notification for request #%s, indicating a different amount.', 'yith-wcaf' ), $args['unique_id'] ), false, array(
												'incoming_amount' => $args['gross'],
												'expected_amount' => $record['amount']
											) );
										}

										YITH_WCAF_Payment_Handler()->add_note( array(
											'payment_id'   => $record['ID'],
											'note_content' => sprintf( __( 'PayPal returned a notification for this payment, indicating a different amount (expected %.2f -> returned %.2f)', 'yith-wcaf' ), $record['amount'], $args['gross'] )
										) );
									}

									if ( get_woocommerce_currency() != $args['currency'] ) {
										if ( $this->_enable_log == 'yes' ) {
											$this->log( sprintf( __( 'PayPal returned a notification for request #%s, indicating a different currency for the receiver', 'yith-wcaf' ), $args['unique_id'] ), false, array(
												'incoming_currency' => $args['currency'],
												'expected_currency' => get_woocommerce_currency()
											) );
										}

										YITH_WCAF_Payment_Handler()->add_note( array(
											'payment_id'   => $record['ID'],
											'note_content' => sprintf( __( 'PayPal returned a notification for this payment, indicating a different currency (expected %s -> returned %s)', 'yith-wcaf' ), get_woocommerce_currency(), $args['currency'] )
										) );
									}

									if ( $this->_enable_log == 'yes' ) {
										$this->log( sprintf( __( 'Request #%s was modified due to IPN from PayPal (txn_id: %s)', 'yith-woocommerce-affiliates' ), $args['unique_id'], $args['txn_id'] ), false );
									}

									YITH_WCAF_Payment_Handler()->add_note( array(
										'payment_id'   => $record['ID'],
										'note_content' => sprintf( __( 'IPN response correctly received from PayPal server (txn_id: %s)', 'yith-woocommerce-affiliates' ), $args['txn_id'] )
									) );
								}
							}

							// call action to update request status
							do_action( 'yith_wcaf_ipn_received', $args );
						}
					}

					die();
				} else {
					if ( $this->_enable_log == 'yes' ) {
						$this->log( 'invalid request' );
						$this->log( $listener->getTextReport(), true );
					}
					die();
				}
			}
		}

		/* === LOG METHODS === */

		/**
		 * Add a log line to log file
		 *
		 * @param $log_line string New line to add to log
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function log( $log_line, $in_template = false, $additional_info = array() ) {
			if ( $this->_enable_log == 'yes' ) {
				if ( empty( $this->_log ) ) {
					$this->_log = new WC_Logger();
				}

				if ( ! $in_template ) {
					$r = '';
					for ( $i = 0; $i < 80; $i ++ ) {
						$r .= '-';
					}
					$r .= "\n[" . date( 'm/d/Y g:i A' ) . "] - " . $log_line . "\n";
					for ( $i = 0; $i < 80; $i ++ ) {
						$r .= '-';
					}

					if ( ! empty( $additional_info ) ) {
						$r .= "\n";

						foreach ( $additional_info as $key => $value ) {
							if ( is_array( $value ) && ! empty( $value ) ) {
								$r .= $key . ":\n";

								ob_start();
								print_r( $value );
								$r .= ob_get_clean();

								$r .= "\n";
							} elseif ( is_array( $value ) ) {
								$r .= $key . ": " . __( 'empty', 'yith-woocommerce-affiliates' ) . "\n";
							} else {
								$r .= $key . ": " . (string) $value . "\n";
							}
						}
					}

					$r .= "\n\n";

					$log_line = $r;
				}

				$log_line = "\n" . $log_line;

				$this->_log->add( 'yith_wcaf_paypal', $log_line );
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Paypal_Gateway
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Paypal_Gateway class
 *
 * @return \YITH_WCAF_Paypal_Gateway
 * @since 1.0.0
 */
function YITH_WCAF_Paypal_Gateway() {
	return YITH_WCAF_Paypal_Gateway::get_instance();
}