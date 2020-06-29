<?php

use angelleye\PayPal\PayPal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendors_Gateway_Paypal_Masspay' ) ) {
	/**
	 * YITH Gateway Paypal
	 *
	 * Define methods and properties for class that manages payments via paypal
	 *
	 * @package   YITH_Marketplace
	 * @author    Your Inspiration <info@yourinspiration.it>
	 * @license   GPL-2.0+
	 * @link      http://yourinspirationstore.it
	 * @copyright 2014 Your Inspiration
	 */
	class YITH_Vendors_Gateway_Paypal_Masspay extends YITH_Vendors_Gateway {
		/**
		 * @var string gateway slug
		 */
		protected $_id = 'paypal-masspay';

		/**
		 * @var string gateway name
		 */
		protected $_method_title = 'PayPal MassPay';

		/**
		 * Status for payments correctly sent
		 *
		 * @cont string Status for payments correctly sent
		 *
		 * @since 1.0
		 */
		const PAYMENT_STATUS_OK = 'Success';

		/**
		 * Status for payments failed
		 *
		 * @cont string Status for payments failed
		 *
		 * @since 1.0
		 */
		const PAYMENT_STATUS_FAIL = 'Failure';

		/**
		 * YITH_Vendors_Gateway_Paypal_Masspay constructor.
		 *
		 * @param $gateway
		 */
		public function __construct( $gateway ) {
			if ( apply_filters( 'yith_deprecated_paypal_service_support', false ) ) {
			    $this->set_is_available_on_checkout( true );

				parent::__construct( $gateway );

				//Change default value for PayPal gateway option
				add_filter( 'yith_wcmv_is_enable_gateway_default_value', '__yith_wcmv_return_yes' );

				/* === Admin Panel === */
				add_filter( 'yith_wcmv_panel_gateways_options', 'YITH_Vendors_Gateway_Paypal_Masspay::add_paypal_section_options' );
				add_filter( 'yith_wcmv_panel_sections', 'YITH_Vendors_Gateway_Paypal_Masspay::add_paypal_section' );
				add_filter( 'yith_wcmv_panel_gateways_options', 'YITH_Vendors_Gateway_Paypal_Masspay::remove_vendor_payment_choosing' );

				add_action( 'init', 'YITH_Vendors_Gateway_Paypal_Masspay::restore_paypal_options', 99 );

				if ( $this->is_enabled() ) {
					/* === Commissions Table === */
					// Bulk Actions
					add_filter( 'yith_wcmv_commissions_bulk_actions', 'YITH_Vendors_Gateway_Paypal_Masspay::commissions_bulk_actions' );

					/* === Vendor's Panel === */
					//Payments tab
					add_action( 'yith_wcmv_vendor_panel_payments', 'YITH_Vendors_Gateway_Paypal_Masspay::add_vendor_panel_payments_options', 10, 1 );

					/* === Check For Option Enabled === */
					$check = 'masspay' == get_option( 'yith_wcmv_paypal_payment_gateway', 'masspay' ) ? '__return_true' : '__return_false';
					add_filter( "yith_wcmv_show_pay_button_for_{$this->get_id()}", $check );

					/* === Checkout Payment === */
					if( $this->is_enabled_for_checkout() ){
						// hook the IPNListener
						add_action( 'init', array( $this, 'handle_notification' ), 30 );
						add_action( 'woocommerce_order_status_changed', array( $this, 'process_credit' ), 20, 3 );
					}
				}
			} else {
				add_filter( 'yith_wcmv_show_enabled_gateways_table', array( $this, 'disable_all' ) );
			}
		}

		/**
		 * Retreive the paypal options array from this plugin.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array paypal option array
		 */
		public static function get_paypal_options_array() {
			return apply_filters( 'yith_wcmv_paypal_gateways_options', array(
					'paypal-masspay' => array(

						'payments_options_start' => array(
							'type' => 'sectionstart',
						),

						'payments_options_title' => array(
							'title' => __( 'PayPal settings', 'yith-woocommerce-product-vendors' ),
							'type'  => 'title',
							'desc'  => __( 'Please note! Since December 2017, PayPal deprecated MassPay and Adaptive Payments methods. This implies that it is no longer possible to request the activation of these services for new accounts. However, those who have the services already active will still be able to use them.', 'yith-woocommerce-product-vendors' ),
						),

						'payments_enable_service' => array(
							'id'      => 'yith_wcmv_enable_paypal-masspay_gateway',
							'type'    => 'checkbox',
							'title'   => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
							'desc'    => __( 'Enable PayPal gateway', 'yith-woocommerce-product-vendors' ),
							'default' => 'yes'
						),

						'payment_gateway' => array(
							'id'      => 'yith_wcmv_paypal_payment_gateway',
							'type'    => 'select',
							'title'   => __( 'PayPal Service', 'yith-woocommerce-product-vendors' ),
							'desc'    => __( 'Choose PayPal service to pay the commissions to vendors (the only option currently available is MassPay).', 'yith-woocommerce-product-vendors' ),
							'options' => apply_filters( 'yith_wcmv_payments_gateway', array(
									'masspay' => __( 'MassPay', 'yith-woocommerce-product-vendors' ),
								)
							),
							'default' => 'masspay'
						),

						'payment_method' => array(
							'id'      => 'yith_wcmv_paypal_payment_method',
							'type'    => 'select',
							'title'   => __( 'Payment Method', 'yith-woocommerce-product-vendors' ),
							'desc'    => __( 'Choose how to pay the commissions to vendors', 'yith-woocommerce-product-vendors' ),
							'options' => array(
								'manual' => __( 'Pay manually', 'yith-woocommerce-product-vendors' ),
								'choose' => __( 'Let vendors decide', 'yith-woocommerce-product-vendors' ),
							),
							'default' => 'choose',
						),

						'payment_minimum withdrawals' => array(
							'id'                => 'yith_wcmv_paypal_payment_minimum_withdrawals',
							'type'              => 'number',
							'title'             => __( 'Minimum Withdrawal', 'yith-woocommerce-product-vendors' ) . ' ' . get_woocommerce_currency_symbol(),
							'desc'              => __( "Set the minimum value for commission withdrawals. This setting will update all vendors' accounts that still have a threshold lower than the one set.", 'yith-woocommerce-product-vendors' ),
							'custom_attributes' => array(
								'min' => 1
							),
							'default'           => 1
						),

						'paypal_sandbox' => array(
							'id'      => 'yith_wcmv_paypal_sandbox',
							'type'    => 'checkbox',
							'title'   => __( 'Sandbox environment', 'yith-woocommerce-product-vendors' ),
							'desc'    => __( 'Set environment as sandbox, for test purpose', 'yith-woocommerce-product-vendors' ),
							'default' => 'yes'
						),

						'paypal_api_username'         => array(
							'id'    => 'yith_wcmv_paypal_api_username',
							'type'  => 'text',
							'title' => __( 'API Username', 'yith-woocommerce-product-vendors' ),
							'desc'  => sprintf( __( 'API username of PayPal administration account (if empty, settings of PayPal in <a href="%s">WooCommmerce Settings page</a> apply).', 'yith-woocommerce-product-vendors' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_paypal' ) )
						),
						'paypal_api_password'         => array(
							'id'    => 'yith_wcmv_paypal_api_password',
							'type'  => 'text',
							'title' => __( 'API Password', 'yith-woocommerce-product-vendors' ),
							'desc'  => sprintf( __( 'API password of PayPal administration account (if empty, settings of PayPal in <a href="%s">WooCommmerce Settings page</a> apply).', 'yith-woocommerce-product-vendors' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_paypal' ) )
						),
						'paypal_api_signature'        => array(
							'id'    => 'yith_wcmv_paypal_api_signature',
							'type'  => 'text',
							'title' => __( 'API Signature', 'yith-woocommerce-product-vendors' ),
							'desc'  => sprintf( __( 'API signature of PayPal administration account (if empty, settings of PayPal in <a href="%s">WooCommmerce Settings page</a> apply).', 'yith-woocommerce-product-vendors' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_paypal' ) )
						),
						'paypal_payment_mail_subject' => array(
							'id'    => 'yith_wcmv_paypal_payment_mail_subject',
							'type'  => 'text',
							'title' => __( 'Payment Email Subject', 'yith-woocommerce-product-vendors' ),
							'desc'  => __( 'Subject of the email sent by PayPal to customers when a payment request is registered', 'yith-woocommerce-product-vendors' )
						),
						'paypal_ipn_notification_url' => array(
							'id'                => 'yith_wcmv_paypal_ipn_notification_url',
							'type'              => 'text',
							'title'             => __( 'Notification URL', 'yith-woocommerce-product-vendors' ),
							'desc'              => __( 'Copy this URL and set it into PayPal admin panel, to receive IPN from their server', 'yith-woocommerce-product-vendors' ),
							'default'           => site_url() . '/?paypal_ipn_response=true',
							'css'               => 'width: 400px;',
							'custom_attributes' => array(
								'readonly' => 'readonly'
							)
						),

						'vendors_options_end' => array(
							'type' => 'sectionend',
						),
					)
				)
			);
		}

		/**
		 * Add PayPal Section
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array paypal option array
		 */
		public static function add_paypal_section( $sections ) {
			$sections['gateways']['paypal-masspay'] = _x( 'PayPal (Deprecated Services)', '[Admin]: Option description', 'yith-woocommerce-product-vendors' );

			return $sections;
		}

		/**
		 * Add  paypal options array from this plugin.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array paypal option array
		 */
		public static function add_paypal_section_options( $options ) {
			return array_merge( $options, self::get_paypal_options_array() );
		}

		/**
		 * Add Pay Bulk Actions
		 *
		 * @param $actions array Bulk actions for commissions table
		 *
		 * @return array allowed bulk actions
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function commissions_bulk_actions( $actions ) {
			if ( 'masspay' == get_option( 'yith_wcmv_paypal_payment_gateway', 'masspay' ) ) {
				$actions = array_merge( array( 'paypal-masspay' => __( 'Pay with PayPal MassPay', 'yith-woocommerce-product-vendors' ) ), $actions );
			}

			return $actions;
		}

		/**
		 * Add Payments option to Payment tab
		 *
		 * @param $args array template args
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function add_vendor_panel_payments_options( $args = array() ) {
			$currency_symbol = get_woocommerce_currency_symbol();
			$payments_type   = array(
				'instant'   => __( 'Instant Payment', 'yith-woocommerce-product-vendors' ),
				'threshold' => __( 'Payment threshold', 'yith-woocommerce-product-vendors' )
			);
			$step            = 'any';
			$min             = get_option( 'yith_wcmv_paypal_payment_minimum_withdrawals', 1 );
			$vendor          = ! empty( $args['vendor'] ) && $args['vendor'] instanceof YITH_Vendor ? $args['vendor'] : yith_get_vendor( 'current', 'user' );

			if ( 'choose' == get_option( 'yith_wcmv_paypal_payment_method', false ) ) : ?>
				<?php ob_start(); ?>
                <h3><?php _e( 'PayPal MassPay', 'yith-woocommerce-product-vendors' ) ?></h3>
                <div class="form-field">
                    <label for="vendor_payment_type"><?php _e( 'Payment type:', 'yith-woocommerce-product-vendors' ) ?></label>
                    <select name="yith_vendor_data[payment_type]" id="vendor_payment_type" class="vendor_payment_type">
						<?php foreach ( $payments_type as $value => $label ) : ?>
                            <option <?php selected( $vendor->payment_type, $value ) ?>
                                    value="<?php echo $value ?>"><?php echo $label ?></option>
						<?php endforeach; ?>
                    </select>
                    <br/>
                    <span
                            class="description"><?php _e( 'Choose payment method for crediting commissions', 'yith-woocommerce-product-vendors' ); ?></span>
                </div>


                <div class="form-field">
                    <label class="yith_vendor_payment_threshold"
                           for="yith_vendor_payment_threshold"><?php _e( 'Threshold', 'yith-woocommerce-product-vendors' ); ?></label>
                    <input type="number" class="payment-threshold-field" name="yith_vendor_data[threshold]"
                           id="yith_vendor_payment_threshold" value="<?php echo $vendor->threshold ?>"
                           min="<?php echo $min ?>"
                           step="<?php echo $step ?>"/>
					<?php echo $currency_symbol ?>
                    <br/>
                    <span class="description"><?php printf( '%s (%s: <strong>%s</strong>).',
							__( "Minimum vendor's earnings before vendor commissions can be paid", 'yith-woocommerce-product-vendors' ),
							__( 'Minimum threshold allowed by site administrator is', 'yith-woocommerce-product-vendors' ),
							wc_price( get_option( 'yith_wcmv_paypal_payment_minimum_withdrawals' ) )
						); ?></span>
                </div>
				<?php echo ob_get_clean(); ?>
			<?php endif;
		}

		/**
		 * Restore PayPal Options
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @ since 2.5.0
		 */
		public static function restore_paypal_options() {
			$is_restored = get_option( 'yith_wcmv_deprecated_paypal_options_restored', false );
			if ( ! $is_restored ) {
				$to_restore = get_option( 'yith_wcmv_deprecated_paypal_options', array() );
				foreach ( $to_restore as $option => $value ) {
					update_option( $option, $value );
				}

				add_option( 'yith_wcmv_deprecated_paypal_options_restored', true );
			}
		}

		/* === PAYMENT METHODS === */

		/**
		 * Get the data for pay() method
		 *
		 * @args Array argument to retreive payment data
		 * @return array
		 */
		public function get_pay_data( $args = array() ) {
			$data = $commissions = $vendor_payment_args = array();

			if ( ! empty( $args['type'] ) && 'massive_payment' == $args['type'] ) {

				if ( ! empty( $args['commissions'] ) ) {
					$commission_ids      = $args['commissions'];
					$vendor_payment_args = $this->build_args_to_register_vendor_payments( $commission_ids );

				} else {

					$commission_ids = array();
					if ( ! empty( $args['order_id'] ) ) {

						$args           = array(
							'order_id' => $args['order_id'],
							'status'   => 'all',
							'fields'   => 'ids'
						);
						$commission_ids = YITH_Commissions()->get_commissions( $args );
					}

					if ( count( $commission_ids ) > 0 ) {

						$vendors_with_threshold = array();
						$vendor_payment_args    = array();
						foreach ( $commission_ids as $commission_id ) {
							$commission = YITH_Commission( $commission_id );
							// save the amount to pay for each commission of vendor
							if ( $commission instanceof YITH_Commission ) {
								$vendor    = $commission->get_vendor();
								$vendor_id = $vendor->id;
								$order     = $commission->get_order();
								$currency  = $order->get_currency();
								if ( $vendor instanceof YITH_Vendor && ! empty( $vendor->paypal_email ) ) {
									if ( 'instant' == $vendor->payment_type ) {

										if ( ! isset( $vendor_payment_args[ $vendor_id ] ) ) {
											$vendor_payment_args[ $vendor_id ]['user_id']          = $vendor->get_owner();
											$vendor_payment_args[ $vendor_id ]['payment_date']     = current_time( 'mysql' );
											$vendor_payment_args[ $vendor_id ]['payment_date_gmt'] = current_time( 'mysql', 1 );
											$vendor_payment_args[ $vendor_id ]['gateway_id']       = $this->get_id();
										}

										if ( ! isset( $vendor_payment_args[ $vendor_id ]['amount'][ $currency ] ) ) {
											$vendor_payment_args[ $vendor_id ]['amount'][ $currency ] = $commission->get_amount();


										} else {
											$vendor_payment_args[ $vendor_id ]['amount'][ $currency ] += $commission->get_amount();
										}

										if ( ! isset( $vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ] ) ) {
											$vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ] = array( $commission_id );
										} else {

											$vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ][] = $commission_id;
										}
									} elseif ( 'threshold' == $vendor->payment_type ) {
										$vendors_with_threshold[ $vendor->id ] = $vendor;
									}
								}
							}
						}

						if ( ! empty( $vendors_with_threshold ) ) {
							foreach ( $vendors_with_threshold as $vendor_id => $vendor ) {
								$commission_ids = $vendor->get_unpaid_commissions_if_out_threshold();
								foreach ( $commission_ids as $commission_id ) {
									$commission = YITH_Commission( $commission_id );
									// save the amount to pay for each commission of vendor
									if ( $commission instanceof YITH_Commission ) {
										$vendor    = $commission->get_vendor();
										$vendor_id = $vendor->id;
										$order     = $commission->get_order();
										$currency  = $order->get_currency();

										if ( ! isset( $vendor_payment_args[ $vendor_id ] ) ) {
											$vendor_payment_args[ $vendor_id ]['user_id']          = $vendor->get_owner();
											$vendor_payment_args[ $vendor_id ]['payment_date']     = current_time( 'mysql' );
											$vendor_payment_args[ $vendor_id ]['payment_date_gmt'] = current_time( 'mysql', 1 );
											$vendor_payment_args[ $vendor_id ]['gateway_id']       = $this->get_id();
										}

										if ( ! isset( $vendor_payment_args[ $vendor_id ]['amount'][ $currency ] ) ) {
											$vendor_payment_args[ $vendor_id ]['amount'][ $currency ] = $commission->get_amount();


										} else {
											$vendor_payment_args[ $vendor_id ]['amount'][ $currency ] += $commission->get_amount();
										}

										if ( ! isset( $vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ] ) ) {
											$vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ] = array( $commission_id );
										} else {

											$vendor_payment_args[ $vendor_id ]['commission_ids'][ $currency ][] = $commission_id;
										}
									}
								}
							}
						}
					}
				}

				//register data information to send at paypal
				$commissions = array();
				foreach ( $vendor_payment_args as $vendor_id => $arg ) {

					$currency_amounts = $arg['amount'];
					$current_args     = array(
						'vendor_id'        => $vendor_id,
						'user_id'          => $arg['user_id'],
						'status'           => 'processing',
						'payment_date'     => $arg['payment_date'],
						'payment_date_gmt' => $arg['payment_date_gmt'],
						'gateway_id'       => isset( $arg['gateway_id'] ) ? $arg['gateway_id'] : $this->get_id(),

					);

					foreach ( $currency_amounts as $currency => $amount ) {

						$current_args['currency'] = $currency;
						$current_args['amount']   = $amount;

						$payment_id = YITH_Vendors()->payments->add_vendor_payments_log( $current_args );
						$vendor     = yith_get_vendor( $vendor_id, 'vendor' );

						if ( $payment_id ) {

							foreach ( $arg['commission_ids'][ $currency ] as $commission_id ) {
								YITH_Vendors()->payments->add_vendor_payment_relationship( $payment_id, $commission_id );
								$commissions[] = YITH_Commission( $commission_id );
							}

							if ( $vendor instanceof YITH_Vendor && ! empty( $vendor->paypal_email ) ) {
								$data[ $currency ][] = array(
									'paypal_email' => $vendor->paypal_email,
									'amount'       => round( $amount, 2 ),
									'request_id'   => $payment_id
								);
							}
						}
					}
				}

				$data['commissions'] = $commissions;
			}

			return $data;
		}

		/**
		 * Pay method, used to process payment requests
		 *
		 * @param $payment_detail  array  Array of parameters for the single requests
		 * Excepts at least the following parameters for each payment to process
		 * [
		 *     paypal_email => string (Paypal email of the receiver)
		 *     amount => float (Amount to pay to user)
		 *     request_id => int (Unique id of the request paid)
		 * ]
		 *
		 * @return array An array holding the status of the operation; it should have at least a boolean status, a verbose status and an array of messages
		 * [
		 *     status => bool (status of the operation)
		 *     verbose_status => string (one between PAYMENT_STATUS_OK and PAYMENT_STATUS_FAIL)
		 *     messages => string|array (one or more message describing operation status)
		 * ]
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function pay( $payment_detail ) {
			// include required libraries
			require_once( dirname( dirname( __FILE__ ) ) . '/third-party/PayPal/PayPal.php' );

			// retrieve saved options from panel
			$stored_options = $this->get_gateway_options();
			$currency       = get_woocommerce_currency();

			if ( empty( $stored_options['api_username'] ) || empty( $stored_options['api_password'] ) || empty( $stored_options['api_signature'] ) ) {
				return array(
					'status'         => false,
					'verbose_status' => self::PAYMENT_STATUS_FAIL,
					'messages'       => __( 'Missing required parameters for PayPal configuration', 'yith-woocommerce-product-vendors' )
				);
			}

			$PayPalConfig = array(
				'Sandbox'      => ! ( $stored_options['sandbox'] == 'no' ),
				'APIUsername'  => $stored_options['api_username'],
				'APIPassword'  => $stored_options['api_password'],
				'APISignature' => $stored_options['api_signature'],
				'PrintHeaders' => true,
				'LogResults'   => false,
			);

			$PayPal = new PayPal( $PayPalConfig );

			// Prepare request arrays
			$MPFields = array(
				'emailsubject' => $stored_options['payment_mail_subject'],
				// The subject line of the email that PayPal sends when the transaction is completed.  Same for all recipients.  255 char max.
				'currencycode' => $currency,
				// Three-letter currency code.
				'receivertype' => 'EmailAddress'
				// Indicates how you identify the recipients of payments in this call to MassPay.  Must be EmailAddress or UserID
			);


			$paypal_response = array();
			unset( $payment_detail['commissions'] );
			foreach ( $payment_detail as $currency => $payments ) {
				$MPFields['currencycode'] = $currency;
				$MPItems                  = array();
				foreach ( $payments as $payment ) {
					if ( ! empty ( $payment['paypal_email'] ) && ! empty ( $payment['amount'] ) && ! empty ( $payment['request_id'] ) ) {
						$MPItems[] = array(
							'l_email'    => $payment['paypal_email'],
							// Required.  Email address of recipient.  You must specify either L_EMAIL or L_RECEIVERID but you must not mix the two.
							'l_amt'      => $payment['amount'],
							// Required.  Payment amount.
							'l_uniqueid' => $payment['request_id']
							// Transaction-specific ID number for tracking in an accounting system.
						);
					}
				}

				$PayPalRequestData = array( 'MPFields' => $MPFields, 'MPItems' => $MPItems );
				$PayPalResult      = $PayPal->MassPay( $PayPalRequestData );

				$errors = array();
				if ( $PayPalResult['ACK'] == self::PAYMENT_STATUS_FAIL ) {
					foreach ( $PayPalResult['ERRORS'] as $error ) {
						$errors[] = $error['L_LONGMESSAGE'];
					}
				}

				$paypal_response[ $currency ] = array(
					'status'         => $PayPalResult['ACK'] == self::PAYMENT_STATUS_OK,
					'verbose_status' => $PayPalResult['ACK'],
					'messages'       => ( $PayPalResult['ACK'] == self::PAYMENT_STATUS_FAIL ) ? implode( "\n", $errors ) : __( 'Payment sent', 'yith-woocommerce-product-vendors' )
				);

			}

			return $paypal_response;
		}

		/**
		 * Method used to handle notification from paypal server
		 *
		 * @return void
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function handle_notification() {

			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( empty( $_REQUEST['paypal_ipn_response'] ) ) {
				return;
			}

			// include required libraries
			require( dirname( dirname( __FILE__ ) ) . '/third-party/IPNListener/ipnlistener.php' );

			// retrieve saved options from panel
			$stored_options = $this->get_gateway_options();

			$listener              = new IpnListener();
			$listener->use_sandbox = ! ( $stored_options['sandbox'] == 'no' );

			try {
				// process IPN request, require validation to PayPal server
				$listener->requirePostMethod();
				$verified = $listener->processIpn();

			} catch ( Exception $e ) {
				// fatal error trying to process IPN.
				die();
			}

			// if PayPal says IPN is valid, process content
			if ( $verified ) {
				$request_data = $_REQUEST;

				if ( ! isset( $request_data['payment_status'] ) ) {
					die();
				}

				// format payment data
				$payment_data = array();
				for ( $i = 1; array_key_exists( 'status_' . $i, $request_data ); $i ++ ) {
					$data_index = array_keys( $request_data );

					foreach ( $data_index as $index ) {
						if ( strpos( $index, '_' . $i ) !== false ) {
							$payment_data[ $i ][ str_replace( '_' . $i, '', $index ) ] = $request_data[ $index ];
							unset( $request_data[ $index ] );
						}
					}
				}

				if ( ! empty( $payment_data ) ) {
					foreach ( $payment_data as $payment ) {
						if ( ! isset( $payment['unique_id'] ) ) {
							continue;
						}

						$args                   = array();
						$args['unique_id']      = $payment['unique_id'];
						$args['gross']          = $payment['mc_gross'];
						$args['status']         = $payment['status'];
						$args['receiver_email'] = $payment['receiver_email'];
						$args['currency']       = $payment['mc_currency'];
						$args['txn_id']         = $payment['masspay_txn_id'];

						$this->handle_payment_successful( $args );

						// call action to update request status
						do_action( 'yith_vendors_gateway_notification', $args );
					}
				}

			}

			die();
		}

		/**
		 * Get the gateway options
		 *
		 * @return array
		 */
		public function get_gateway_options() {

			$api_username  = get_option( 'yith_wcmv_' . $this->gateway . '_api_username' );
			$api_password  = get_option( 'yith_wcmv_' . $this->gateway . '_api_password' );
			$api_signature = get_option( 'yith_wcmv_' . $this->gateway . '_api_signature' );

			// If empty, get from woocommerce settings
			if ( empty( $api_username ) && empty( $api_password ) && empty( $api_signature ) ) {
				$gateways = WC()->payment_gateways()->get_available_payment_gateways();
				if ( isset( $gateways['paypal'] ) ) {
					/** @var WC_Gateway_Paypal $paypal */
					$paypal = $gateways['paypal'];

					$api_username  = $paypal->testmode ? $paypal->get_option( 'sandbox_api_username' ) : $paypal->get_option( 'api_username' );
					$api_password  = $paypal->testmode ? $paypal->get_option( 'sandbox_api_password' ) : $paypal->get_option( 'api_password' );
					$api_signature = $paypal->testmode ? $paypal->get_option( 'sandbox_api_signature' ) : $paypal->get_option( 'api_signature' );
				}
			}

			$args = array(
				'sandbox'              => get_option( 'yith_wcmv_' . $this->gateway . '_sandbox' ),
				'api_username'         => $api_username,
				'api_password'         => $api_password,
				'api_signature'        => $api_signature,
				'payment_mail_subject' => get_option( 'yith_wcmv_' . $this->gateway . '_payment_mail_subject' ),
				'ipn_notification_url' => site_url() . '/?paypal_ipn_response=true',
			);

			$args = wp_parse_args( $args, array() );

			return $args;
		}

		/**
		 * Remove the option where give the ability to vendor to choose the payment method
		 *
		 * @param array $fields
		 *
		 * @return array
		 */
		public static function remove_vendor_payment_choosing(
			$fields
		) {
			$payment_method = get_option( 'yith_wcmv_paypal_payment_method', 'choose' );
			if ( 'choose' != $payment_method ) {
				unset( $fields['payment_method'] );
			}

			return $fields;
		}

		/**
		 * Check if the current gateway is enabled or not
		 *
		 * @return bool TRUE if enabled, FALSE otherwise
		 */
		public function is_enabled() {
			$enabled      = $is_masspay_enabled = false;
			$gateway_slug = $this->get_id();

			if ( ! empty( $gateway_slug ) ) {
				$default            = apply_filters( 'yith_wcmv_is_enable_gateway_default_value', 'no' );
				$enabled            = 'yes' == get_option( "yith_wcmv_enable_{$gateway_slug}_gateway", $default );
				$is_masspay_enabled = 'masspay' == get_option( 'yith_wcmv_paypal_payment_gateway', 'masspay' );
			}

			return $enabled && $is_masspay_enabled;
		}

		/**
		 * Disable All Gateway features if the integration plugin is disabled
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @param $available_gateways
		 *
		 * @return mixed
		 */
		public function disable_all(
			$available_gateways
		) {
			$gateway_id = $this->get_id();

			if ( isset( $available_gateways[ $gateway_id ] ) ) {
				unset( $available_gateways[ $gateway_id ] );
			}

			return $available_gateways;
		}

		/**
		 * Handle the single commission from commission list
		 */
		public function handle_single_commission_pay() {
			$message = $text = '';
			if ( current_user_can( 'manage_woocommerce' ) && wp_verify_nonce( $_GET['_wpnonce'], 'yith-vendors-pay-commission' ) && isset( $_GET['commission_id'] ) ) {
				$commission_id = absint( $_GET['commission_id'] );
				$result        = $this->pay_commission( $commission_id );
				$message       = $result['status'] ? 'pay-process' : 'pay-failed';
				$text          = $result['status'] ? '' : $result['messages'];

			}

			wp_safe_redirect( esc_url_raw( add_query_arg( array(
				'message' => $message,
				'text'    => urlencode( $text )
			), wp_get_referer() ) ) );
			exit();
		}

		/**
		 * Handle the massive commission from commission list
		 */
		public function handle_massive_commissions_pay( $vendor, $commission_ids, $action ) {
			$message = $text = '';
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$result  = $this->pay_massive_commissions( $commission_ids, $action );
				$message = $result['status'] ? 'pay-process' : 'pay-failed';
				$text    = $result['status'] ? '' : $result['messages'];
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( array(
				'message' => $message,
				'text'    => urlencode( $text )
			), wp_get_referer() ) ) );
			exit();
		}

		/**
		 * Process success payment
		 *
		 * @param $args
		 */
		public function handle_payment_successful( $args ) {
			if ( empty( $args['unique_id'] ) ) {
				return;
			}

			$payment_id     = $args['unique_id'];
			$commission_ids = YITH_Vendors()->payments->get_commissions_by_payment_id( $payment_id );
			// emails
			WC()->mailer();

			$status = $args['status'] == 'Completed' ? 'paid' : 'failed';
			if ( count( $commission_ids ) > 0 ) {
				foreach ( $commission_ids as $commission_id ) {

					$commission = YITH_Commission( absint( $commission_id ) );

					// perform only if the commission is in progress

					if ( ! $commission->has_status( 'processing' ) ) {
						continue;
					}


					// if completed, set as paid
					if ( $args['status'] == 'Completed' ) {
						$gateway_name = YITH_Vendors_Gateway( $this->gateway )->get_method_title();
						$commission->update_status( 'paid', sprintf( __( 'Commission paid via %s (txn ID: %s)', 'yith-woocommerce-product-vendors' ), $gateway_name, $args['txn_id'] ) );
						$this->set_payment_post_meta( $commission );
						do_action( 'yith_vendors_commissions_paid', $commission );
					} // set unpaid if failed
					else {
						$status = 'failed';
						$commission->update_status( 'unpaid', sprintf( __( 'Payment %s', 'yith-woocommerce-product-vendors' ), $args['status'] ) );
						do_action( 'yith_vendors_commissions_unpaid', $commission );
					}
				}

				YITH_Vendors()->payments->update_payment_status( $payment_id, $status );
			}
		}

		/**
		 * Pay the commission to vendor
		 *
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 */
		public function process_credit( $order_id, $old_status, $new_status ) {
			if ( 'completed' != $new_status ) {
				return false;
			}

			if ( 'manual' == get_option( 'yith_wcmv_paypal_payment_method' ) ) {
				return false;
			}

			$args = array(
				'order_id' => $order_id,
				'type'     => 'massive_payment'
			);

			$data = $this->get_pay_data( $args );

			// pay
			$result      = $this->pay( $data );
			$commissions = isset( $data['commissions'] ) ? $data['commissions'] : array();

			foreach ( $commissions as $commission ) {
				$order    = $commission->get_order();
				$currency = $order->get_currency();
				$status   = isset( $result[ $currency ]['status'] ) ? $result[ $currency ]['status'] : ( isset( $result['status'] ) ? isset( $result['status'] ) : false );

				$messages = isset( $result[ $currency ]['messages'] ) ? $result[ $currency ]['messages'] : ( isset( $result['messages'] ) ? isset( $result['messages'] ) : '' );
				// set as processing, because paypal will set as paid as soon as the transaction is completed
				if ( $status ) {
					$commission->update_status( 'processing' );
				} // save the error in the note
				else {
					$commission->add_note( sprintf( __( 'Payment failed: %s', 'yith-woocommerce-product-vendors' ), $messages ) );
				}
			}
		}
	}
}