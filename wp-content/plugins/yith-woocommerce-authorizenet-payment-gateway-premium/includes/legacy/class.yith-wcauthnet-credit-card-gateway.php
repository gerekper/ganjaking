<?php
/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Gateway class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_Credit_Card_Gateway' ) ) {
	/**
	 * WooCommerce Authorize.net gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_Credit_Card_Gateway extends WC_Payment_Gateway {

		/**
		 * @const Sandbox payment url
		 */
		const AUTHORIZE_NET_SANDBOX_PAYMENT_URL = 'https://test.authorize.net/gateway/transact.dll';

		/**
		 * @const Public payment url
		 */
		const AUTHORIZE_NET_PRODUCTION_PAYMENT_URL = 'https://secure2.authorize.net/gateway/transact.dll';

		/**
		 * Authorize.net gateway id
		 *
		 * @var string Id of specific gateway
		 *
		 * @since 1.0
		 */
		public static $gateway_id = 'yith_wcauthnet_credit_card_gateway';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_Credit_Card_Gateway
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_Credit_Card_Gateway
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
		 * @param array $details
		 *
		 * @return \YITH_WCAUTHNET_Credit_Card_Gateway
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                 = self::$gateway_id;
			$this->method_title       = apply_filters( 'yith_wcauthnet_method_title', __( 'Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) );
			$this->method_description = apply_filters( 'yith_wcauthnet_method_description', __( 'Pay with Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) );

			$this->init_form_fields();
			$this->init_settings();

			// retrieves gateway options
			$this->enabled           = $this->get_option( 'enabled' );
			$this->order_button_text = apply_filters( 'yith_wcauthnet_order_button_text', $this->get_option( 'order_button', __( 'Proceed to Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) ) );
			$this->title             = $this->get_option( 'title' );
			$this->description       = $this->get_option( 'description' );
			$this->card_types        = $this->get_option( 'card_types' );
			$this->login_id          = trim( $this->get_option( 'login_id' ) );
			$this->transaction_key   = trim( $this->get_option( 'transaction_key' ) );
			$this->md5_hash          = trim( $this->get_option( 'md5_hash' ) );
			$this->sandbox           = $this->get_option( 'sandbox' );
			$this->transaction_type  = $this->get_option( 'transaction_type' );
			$this->debug             = $this->get_option( 'debug' );

			// Logs
			if ( 'yes' == $this->debug ) {
				$this->log = new WC_Logger();
			}

			// gateway requires fields only if API methods are used
			$this->has_fields = false;

			// register payment form print
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'print_authorize_net_payment_form' ), 10, 1 );

			// register admin options
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

			// register ipn response handler
			add_action( 'woocommerce_api_' . $this->id, array( $this, 'handle_ipn_response' ) );

			// register admin notices
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Initialize options field for payment gateway
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = apply_filters( 'yith_wcauthnet_credit_card_gateway_options', array(
				'enabled'          => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Authorize.net Payment', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default' => 'no'
				),
				'title'            => array(
					'title'       => __( 'Title', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'This option lets you change the title that users see during the checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => __( 'Authorize.net Payment', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'desc_tip'    => true,
				),
				'description'      => array(
					'title'       => __( 'Description', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'textarea',
					'description' => __( 'This option lets you change the description that users see during checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => __( 'Accepts Payments. Anywhere', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'order_button'     => array(
					'title'       => __( 'Order Button Text', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'This option lets you change the label of the button that users see during the checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => __( 'Proceed to Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'desc_tip'    => true,
				),
				'card_types'       => array(
					'title'    => __( 'Acceptance logos', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'     => 'multiselect',
					'desc_tip' => __( 'Select which credit card logo to display on your checkout page', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'  => array( 'visa', 'mastercard', 'amex', 'discover', 'diners', 'jcb' ),
					'class'    => 'chosen_select',
					'css'      => 'width: 350px;',
					'options'  => apply_filters( 'yith_wcauthnet_card_types',
						array(
							'visa'       => __( 'Visa', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'mastercard' => __( 'MasterCard', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'amex'       => __( 'American Express', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'discover'   => __( 'Discover', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'diners'     => __( 'Diner\'s Club', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'jcb'        => __( 'JCB', 'yith-woocommerce-authorizenet-payment-gateway' ),
						)
					)
				),
				'login_id'         => array(
					'title'       => __( 'Login ID', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'Univocal ID login associated to the account of the admin (it can be recovered in the "API Login ID and Transaction Key" section)', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'transaction_key'  => array(
					'title'       => __( 'Transaction Key', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'A unique key used to validate requests to Authorize.net (it can be recovered in the "API Login ID and Transaction Key" section)', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'md5_hash'         => array(
					'title'       => __( 'Md5 Hash', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'A unique key used to validate the answers from Authorize.net (it can be set in the "MD5 Hash" section). You can activate this mode and set these details of your Authorize.net dashboard in Account -> Md5 Hash. The check will be done only in redirect mode, as the API connection is already protected with SSL.', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'sandbox'          => array(
					'title'       => __( 'Enable Authorize.net sandbox', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => __( 'Activate the sandbox mode to test the configuration', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'transaction_type' => array(
					'title'    => __( 'Transaction type', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'     => 'select',
					'desc_tip' => __( 'Select which type of transaction you want to send', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'  => 'AUTH_CAPTURE',
					'css'      => 'width: 350px;',
					'options'  => apply_filters( 'yith_wcauthnet_transaction_type',
						array(
							'AUTH_CAPTURE' => __( 'Authorize & Capture', 'yith-woocommerce-authorizenet-payment-gateway' ),
							'AUTH_ONLY'    => __( 'Authorize only', 'yith-woocommerce-authorizenet-payment-gateway' )
						)
					)
				),
				'debug'            => array(
					'title'       => __( 'Debug Log', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => sprintf( __( 'Log of the Authorize.net events inside <code>%s</code>', 'yith-woocommerce-authorizenet-payment-gateway' ), wc_get_log_file_path( 'authorize.net' ) )
				)
			) );
		}

		/**
		 * Process payment
		 *
		 * @param $order_id int Current order id
		 *
		 * @return null|array Null on failure; array on success ( id provided: 'status' [string] textual status of the payment / 'redirect' [string] Url where to redirect user )
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			return $this->_process_external_payment( $order );
		}

		/**
		 * Add selected card icons to payment method label, defaults to Visa/MC/Amex/Discover
		 *
		 * @return string HTML to print in icon section
		 * @since 1.0.0
		 */
		public function get_icon() {
			$icon = '';

			if ( $this->icon ) {

				// use icon provided by filter
				$icon .= '<img src="' . esc_url( WC_HTTPS::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';

			}

			if ( ! empty( $this->card_types ) ) {

				// display icons for the selected card types
				foreach ( $this->card_types as $card_type ) {

					if ( file_exists( YITH_WCAUTHNET_DIR . 'assets/images/icons/credit-cards/' . strtolower( $card_type ) . '.png' ) ) {
						$icon .= '<img src="' . esc_url( WC_HTTPS::force_https_url( YITH_WCAUTHNET_URL ) . '/assets/images/icons/credit-cards/' . strtolower( $card_type ) . '.png' ) . '" alt="' . esc_attr( strtolower( $card_type ) ) . '" />';
					}

				}

			}

			return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_notices() {
			if ( empty( $this->login_id ) || empty( $this->transaction_key ) ) {
				echo '<div class="error"><p>' . __( 'Please enter Login ID and Transaction Key for Authorize.net gateway.', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p></div>';
			}
		}

		/**
		 * Add banner on payment gateway page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_options() {
			?>
			<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings', 'woocommerce' ); ?></h3>

			<?php if ( empty( $this->login_id ) || empty( $this->transaction_key ) ): ?>
				<div class="simplify-commerce-banner updated">
					<img alt="<?php _e( 'Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) ?>" src="<?php echo YITH_WCAUTHNET_URL . '/assets/images/logo.jpg'; ?>" style="width: 300px"/>
					<p class="main">
						<strong><?php _e( 'Getting started', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></strong>
					</p>
					<p><?php _e( 'An Authorize.Net Payment Gateway account allows you to accept credit cards and electronic checks from websites and Internet auction sites. Our solutions are designed to save time and money for small- to medium-sized businesses.', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></p>

					<p>
						<a href="https://account.authorize.net/" target="_blank" class="button button-primary"><?php _e( 'Sign up now', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></a>
						<a href="http://www.authorize.net/" target="_blank" class="button"><?php _e( 'Learn more', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></a>
					</p>
				</div>
			<?php endif; ?>

			<?php echo ( ! empty( $this->method_description ) ) ? wpautop( $this->method_description ) : ''; ?>

			<table class="form-table">
			<?php $this->generate_settings_html(); ?>
			</table><?php
		}

		/* === DIRECT PAYMENT METHODS === */

		/**
		 * Prints Authorize.net checkout form
		 *
		 * @param $order_id int Current order id
		 *
		 * @return void
		 */
		public function print_authorize_net_payment_form( $order_id ) {
			$order          = wc_get_order( $order_id );
			$order_number   = $order->get_order_number();
			$order_total    = $order->get_total();
			$order_currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency();

			// Define variables to use in the template
			$login_id            = $this->login_id;
			$amount              = $order_total;
			$invoice             = $order_id;
			$sequence            = $order_id;
			$version             = '3.1';
			$relay_response      = 'TRUE';
			$type                = $this->transaction_type;
			$description         = 'Order ' . $order_number;
			$show_form           = 'PAYMENT_FORM';
			$currency_code       = $order_currency;
			$first_name          = $order->billing_first_name;
			$last_name           = $order->billing_last_name;
			$company             = $order->billing_company;
			$address             = $order->billing_address_1 . ' ' . $order->billing_address_2;
			$country             = $order->billing_country;
			$phone               = $order->billing_phone;
			$state               = $order->billing_state;
			$city                = $order->billing_city;
			$zip                 = $order->billing_postcode;
			$email               = $order->billing_email;
			$ship_to_first_name  = $order->shipping_first_name;
			$ship_to_last_name   = $order->shipping_last_name;
			$ship_to_address     = $order->shipping_address_1;
			$ship_to_city        = $order->shipping_city;
			$ship_to_zip         = $order->shipping_postcode;
			$ship_to_state       = $order->shipping_state;
			$cancel_url          = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url();
			$cancel_button_label = apply_filters( 'yith_wcauthnet_cancel_button_label', __( 'Cancel Payment', 'yith-woocommerce-authorizenet-payment-gateway' ) );
			$relay_url           = esc_url( add_query_arg( 'wc-api', $this->id, user_trailingslashit( home_url() ) ) );

			// Itemized request information
			$tax_info  = array();
			$item_info = array();

			if ( 'yes' == $this->sandbox ) {
				$process_url = self::AUTHORIZE_NET_SANDBOX_PAYMENT_URL;
			} else {
				$process_url = self::AUTHORIZE_NET_PRODUCTION_PAYMENT_URL;
			}

			// Security params
			$timestamp = time();

			if ( phpversion() >= '5.1.2' ) {
				$fingerprint = hash_hmac( "md5", $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . number_format( $order_total, 2, '.', '' ) . "^" . $order_currency, $this->transaction_key );
			} else {
				$fingerprint = bin2hex( mhash( MHASH_MD5, $this->login_id . "^" . $order_id . "^" . $timestamp . "^" . number_format( $order_total, 2, '.', '' ) . "^" . $order_currency, $this->transaction_key ) );
			}

			// Include payment form template
			$template_name = 'authorize-net-payment-form.php';
			$locations     = array(
				trailingslashit( WC()->template_path() ) . $template_name,
				$template_name
			);

			$template = locate_template( $locations );

			if ( ! $template ) {
				$template = YITH_WCAUTHNET_DIR . 'templates/' . $template_name;
			}

			include_once( $template );
		}

		/**
		 * Redirect to payment page, when using "redirect" method
		 *
		 * @param $order \WC_Order Current order
		 *
		 * @return array
		 */
		protected function _process_external_payment( $order ) {
			// Redirect to payment page, where payment form will be printed
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true )
			);
		}

		/* === IPN RESPONSE HANDLER === */

		/**
		 * Handles ipn responses from Authorize.net
		 *
		 * @return void
		 */
		public function handle_ipn_response() {
			$order_id             = isset( $_POST['x_invoice_num'] ) ? $_POST['x_invoice_num'] : false;
			$response             = isset( $_POST['x_response_code'] ) ? $_POST['x_response_code'] : false;
			$md5_hash             = isset( $_POST['x_MD5_Hash'] ) ? $_POST['x_MD5_Hash'] : false;
			$trans_id             = isset( $_POST['x_trans_id'] ) ? $_POST['x_trans_id'] : false;
			$amount               = isset( $_POST['x_amount'] ) ? $_POST['x_amount'] : false;
			$email                = isset( $_POST['x_email'] ) ? $_POST['x_email'] : false;
			$trans_message        = ! empty( $_POST['x_response_reason_text'] ) ? $_POST['x_response_reason_text'] : __( 'N/D', 'yith-woocommerce-authorizenet-payment-gateway' );
			$trans_account_number = ! empty( $_POST['x_account_number'] ) ? $_POST['x_account_number'] : '';

			if ( isset( $order_id ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( ! $order_id || ! $response || ! $md5_hash || ! $trans_id || ! $amount || ! $email ) {
				// Redirect to error page and set order as failed

				if ( ! empty( $order ) ) {
					$order->update_status( 'failed', __( 'Authorize.net API error: unknown error.', 'yith-woocommerce-authorizenet-payment-gateway' ) );
					wc_add_notice( __( 'Unknown error', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );
					$this->redirect_via_html( $order->get_checkout_order_received_url() );
					die();
				} else {
					$this->redirect_via_html( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url() );
					die();
				}
			}

			if ( $response == 1 ) {
				$valid_response = true;

				// Validate amount
				if ( $order->get_total() != $amount ) {
					if ( 'yes' == $this->debug ) {
						$this->log->add( 'authorize.net', 'Payment error: Amounts do not match (gross ' . $amount . ')' );
					}

					// Put this order on-hold for manual checking
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $amount ) );

					wc_add_notice( sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $amount ), 'error' );
					$valid_response = false;
				}

				// Validate Email Address
				if ( strcasecmp( trim( $order->billing_email ), trim( $email ) ) != 0 ) {
					if ( 'yes' == $this->debug ) {
						$this->log->add( 'authorize.net', "Payment error: Authorize.net email ({$email}) does not match our email ({$order->billing_email})" );
					}

					// Put this order on-hold for manual checking
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $email ) );

					wc_add_notice( sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $email ), 'error' );
					$valid_response = false;
				}

				// Redundant md5 hash check
				if ( ! empty( $this->md5_hash ) ) {
					$expected_hash = strtoupper( md5( $this->md5_hash . $this->login_id . $trans_id . number_format( $order->get_total(), 2, '.', '' ) ) );

					if ( strcasecmp( trim( $md5_hash ), trim( $expected_hash ) ) != 0 ) {
						if ( 'yes' == $this->debug ) {
							$this->log->add( 'authorize.net', "Payment error: MD5 Hash control failed" );
						}

						// Put this order on-hold for manual checking
						$order->update_status( 'on-hold', __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ) );

						wc_add_notice( __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );
						$valid_response = false;
					}
				}

				if ( $valid_response ) {
					// Mark as complete
					$order->add_order_note( sprintf( __( 'Authorize.net payment completed (message: %s). Transaction ID: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $trans_message, $trans_id ) );
					$order->payment_complete( $trans_id );

					if ( ! empty( $trans_account_number ) ) {
						update_post_meta( $order->id, 'x_card_num', $trans_account_number );
					}

					if ( 'yes' == $this->debug ) {
						$this->log->add( 'authorize.net', 'Payment Result: ' . print_r( $_POST, true ) );
					}

					// Remove cart
					WC()->cart->empty_cart();
				}
			} else {
				wc_add_notice( sprintf( __( 'Payment error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $trans_message ), 'error' );
			}

			$this->redirect_via_html( $order->get_checkout_order_received_url() );
			die();
		}

		/**
		 * Print HTML code to redirect to a specific url
		 *
		 * @param $url string Url to redirect to
		 *
		 * @return void
		 */
		public function redirect_via_html( $url ) {
			?>
			<html <?php language_attributes(); ?> >
			<head>
				<script language="javascript">
					<!--
					window.location = "<?php echo $url ?>";
					//-->
				</script>
			</head>
			<body>
			<noscript>
				<meta http-equiv="refresh" content="0;url=<?php echo $url ?>">
			</noscript>
			</body>
			</html>
			<?php
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_Credit_Card_Gateway class
 *
 * @return \YITH_WCAUTHNET_Credit_Card_Gateway
 * @since 1.0.0
 */
function YITH_WCAUTHNET_Credit_Card_Gateway() {
	return YITH_WCAUTHNET_Credit_Card_Gateway::get_instance();
}