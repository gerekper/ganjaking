<?php
/**
 * Plugin Name: WooCommerce PayPal Pro (Classic and PayFlow Editions) Gateway
 * Plugin URI: https://woocommerce.com/products/paypal-pro/
 * Description: A payment gateway for PayPal Pro classic and PayFlow edition. A PayPal Pro merchant account, Curl support, and a server with SSL support and an SSL certificate is required (for security reasons) for this gateway to function.
 * Version: 4.5.1
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Tested up to: 5.3
 * WC requires at least: 3.0
 * WC tested up to: 4.0
 * Copyright: © 2009-2017 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * PayPal Pro Docs:
 *     https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_WPP_IntegrationGuide.pdf
 *     https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/payflowgateway_guide.pdf
 *
 * @package WC_PayPal_Pro
 *
 * Woo: 18594:6d23ba7f0e0198937c0029f9e865b40e
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '6d23ba7f0e0198937c0029f9e865b40e', '18594' );

if ( ! class_exists( 'WC_PayPal_Pro' ) ) :

	define( 'WC_PAYPAL_PRO_VERSION', '4.5.1' );

	/**
	 * PayPal Pro main class.
	 */
	class WC_PayPal_Pro {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			if ( is_admin() ) {
				include( 'includes/admin/class-wc-gateway-paypal-pro-admin-notices.php' );
			}

			if ( is_woocommerce_active() && class_exists( 'WC_Payment_Gateway' ) ) {

				if ( version_compare( get_option( 'woocommerce_paypal_pro_version', 0 ), WC_PAYPAL_PRO_VERSION, '<' ) ) {
					$this->update();
				}

				add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );

				if ( is_admin() ) {
					add_action( 'admin_notices', array( $this, 'ssl_check' ) );
				}

				include( 'includes/class-wc-gateway-paypal-pro.php' );
				include( 'includes/class-wc-gateway-paypal-pro-payflow.php' );
				include( 'includes/class-wc-gateway-paypal-pro-privacy.php' );

				add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'capture_payment' ) );
				add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_payment' ) );
				add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'cancel_payment' ) );
				add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'cancel_payment' ) );
				add_action( 'admin_init', array( $this, 'update_ssl_nag' ) );
				add_action( 'admin_notices', array( $this, 'cardinal_upgrade_notice' ) );
				add_action( 'wp_ajax_dismiss-notice', array( $this, 'dissmissable_notice_handler' ) );
			}
		}

		/**
		 * Updates version
		 */
		public function update() {
			// 3dsecure update logic when updating from a version of this extension older than 4.4.0
			if ( version_compare( get_option( 'woocommerce_paypal_pro_version', 0 ), '4.4.0', '<' ) ) {
				$settings = get_option( 'woocommerce_paypal_pro_settings', array() );

				if ( ! empty( $settings['enabled'] ) && ! empty( $settings['enable_3dsecure'] ) && 'yes' === $settings['enable_3dsecure'] && 'yes' === $settings['enabled'] ) {
					$settings['enable_3dsecure'] = 'no';
					update_option( 'woocommerce_paypal_pro_settings', $settings );
					update_option( 'woocommerce_paypal_pro_cardinal_upgrade_notice', true );
				}
			}
			update_option( 'woocommerce_paypal_pro_version', WC_PAYPAL_PRO_VERSION );
		}

		/**
		 * cardinal_upgrade_notice
		 */
		public function cardinal_upgrade_notice() {
			if ( get_option( 'woocommerce_paypal_pro_cardinal_upgrade_notice' ) && current_user_can( 'manage_options' ) ) {
				echo '<div class="notice error is-dismissible" data-notice-id="cardinal_upgrade_notice">
					<p>' . __( 'PayPal Pro requires the "CC" TransactionType to be enabled on your Cardinal Commerce merchant profile in order to use 3dSecure. Please contact Cardinal support to get your account updated. In the meantime, 3dsecure has been disabled so you can continue to recieve payments. Re-enable it from the settings page once your account has been updated.', 'woocommerce-gateway-paypal-pro' ) . '</p>
				</div>
				<script type="text/javascript">
					jQuery(function() {
						jQuery(".is-dismissible[data-notice-id]").on("click", function(){
							jQuery.post( ajaxurl, {
								action:    "dismiss-notice",
								notice_id: jQuery( this ).data("notice-id")
							});
						});
					});
				</script>
				';
			}
		}

		/**
		 * Remove the notice
		 */
		public function dissmissable_notice_handler() {
			if ( ! empty( $_POST['notice_id'] ) && 'cardinal_upgrade_notice' === $_POST['notice_id'] && current_user_can( 'manage_options' ) ) {
				delete_option( 'woocommerce_paypal_pro_cardinal_upgrade_notice' );
			}
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'wc_paypal_pro_plugin_locale', get_locale(), 'woocommerce-gateway-paypal-pro' );

			load_textdomain( 'woocommerce-gateway-paypal-pro', trailingslashit( WP_LANG_DIR ) . 'woocommerce-gateway-paypal-pro/woocommerce-gateway-paypal-pro' . '-' . $locale . '.mo' );

			load_plugin_textdomain( 'woocommerce-gateway-paypal-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			return true;
		}

		/**
		 * Update SSL nag
		 *
		 * @since 4.3.2
		 * @return bool
		 */
		public function update_ssl_nag() {

			if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'wc_paypal_pro_ssl_nag_hide' ) ) {
				return;
			}

			if ( isset( $_GET['wc_paypal_pro_ssl_nag'] ) && '1' === $_GET['wc_paypal_pro_ssl_nag'] ) {
				add_user_meta( get_current_user_id(), '_wc_paypal_pro_ssl_nag_hide', '1', true );
			}
		}

		/**
		 * Register the gateway for use
		 *
		 * @param array $methods Payment methods.
		 *
		 * @return array Payment methods
		 */
		public function register_gateway( $methods ) {
			$methods[] = 'WC_Gateway_PayPal_Pro';
			$methods[] = 'WC_Gateway_PayPal_Pro_Payflow';

			return $methods;
		}

		/**
		 * Show a notice if SSL is disabled
		 */
		public function ssl_check() {

			$settings = get_option( 'woocommerce_paypal_pro_settings', array() );

			// Show message if enabled and FORCE SSL is disabled and WordpressHTTPS
			// plugin is not detected.
			if ( ! wc_checkout_is_https()
				&& isset( $settings['enabled'] )
				&& 'yes' === $settings['enabled']
				&& 'yes' !== $settings['testmode']
				&& ! get_user_meta( get_current_user_id(), '_wc_paypal_pro_ssl_nag_hide' )
			) {
				echo '<div class="error"><p>' . sprintf( __( 'PayPal Pro is enabled, but a SSL certificate is not detected. Your checkout may not be secure! Please ensure your server has a valid <a href="%1$s" target="_blank">SSL certificate</a>', 'woocommerce-gateway-paypal-pro' ), 'https://en.wikipedia.org/wiki/Transport_Layer_Security' ) . '</p></div>';
			}

			return true;
		}

		/**
		 * Capture payment when the order is changed from on-hold to complete or processing
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return bool True if capture succeed
		 */
		public function capture_payment( $order_id ) {
			if ( ! $this->_can_capture_order( $order_id ) ) {
				return false;
			}

			if ( ! $this->_get_transaction_id( $order_id ) ) {
				return false;
			}

			$order = wc_get_order( $order_id );

			$captured = false;
			switch ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method() ) {
				case 'paypal_pro':
					$captured = $this->_capture_payment_paypal_pro( $order );
					break;
				case 'paypal_pro_payflow':
					$captured = $this->_capture_payment_paypal_pro_payflow( $order );
					break;
			}

			return $captured;
		}

		/**
		 * Checks whether transaction in given order can be captured.
		 *
		 * @since 4.4.4
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return bool True if given order ID is not captured yet
		 */
		protected function _can_capture_order( $order_id ) {
			return 'no' === get_post_meta( $order_id, '_paypalpro_charge_captured', true );
		}

		/**
		 * Get transaction ID from given order ID
		 *
		 * @since 4.4.4
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return mixed Transaction ID
		 */
		protected function _get_transaction_id( $order_id ) {
			return get_post_meta( $order_id, '_transaction_id', true );
		}

		/**
		 * Capture transaction in given order when it's paid via PayPal Pro.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order Order object.
		 *
		 * @return bool Returns true if captured successfully
		 */
		protected function _capture_payment_paypal_pro( $order ) {
			$paypalpro = new WC_Gateway_PayPal_Pro();

			$txn_id = $this->_get_transaction_id( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() );
			$url    = $paypalpro->testmode ? $paypalpro->testurl : $paypalpro->liveurl;

			$post_data = array(
				'VERSION'         => $paypalpro->api_version,
				'SIGNATURE'       => $paypalpro->api_signature,
				'USER'            => $paypalpro->api_username,
				'PWD'             => $paypalpro->api_password,
				'METHOD'          => 'DoCapture',
				'AUTHORIZATIONID' => $txn_id,
				'AMT'             => $order->get_total(),
				'CURRENCYCODE'    => ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency() ),
				'COMPLETETYPE'    => 'Complete',
			);

			if ( $paypalpro->soft_descriptor ) {
				$post_data['SOFTDESCRIPTOR'] = $paypalpro->soft_descriptor;
			}

			$paypalpro->log( 'Capture payment request: ' . print_r( $post_data, true ) );

			$response = wp_remote_post( $url, array(
				'method'        => 'POST',
				'headers'       => array(
					'PAYPAL-NVP' => 'Y',
				),
				'body'          => $post_data,
				'timeout'       => 70,
				'user-agent'    => 'WooCommerce',
				'httpversion'   => '1.1',
			));

			if ( is_wp_error( $response ) ) {
				$order->add_order_note( __( 'Unable to capture charge!', 'woocommerce-gateway-paypal-pro' ) . ' ' . $response->get_error_message() );
				$paypalpro->log( 'Error: ' . $response->get_error_message() );

				return false;
			}

			parse_str( $response['body'], $parsed_response );

			$paypalpro->log( 'Parsed response: ' . print_r( $parsed_response, true ) );

			switch ( strtolower( $parsed_response['ACK'] ) ) {
				case 'success':
				case 'successwithwarning':
					$this->_capture_payment_paypal_pro_success( $order, $parsed_response );
					break;
				case 'failure':
				default:
					$this->_capture_payment_paypal_pro_failed( $order, $parsed_response );
					return false;
			}

			return true;
		}

		/**
		 * Action performed when capture succeed.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order           Order object.
		 * @param array    $parsed_response Parsed response.
		 *
		 * @return void
		 */
		protected function _capture_payment_paypal_pro_success( $order, $parsed_response ) {
			$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
			$order->add_order_note( sprintf( __( 'PayPal Pro charge complete (Transaction ID: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['TRANSACTIONID'] ) );

			update_post_meta( $order_id, '_paypalpro_charge_captured', 'yes' );

			// Update the transaction ID of the capture.
			update_post_meta( $order_id, '_transaction_id', $parsed_response['TRANSACTIONID'] );
		}

		/**
		 * Action performed when capture failed.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order           Order object.
		 * @param array    $parsed_response Parsed response.
		 *
		 * @return void
		 */
		protected function _capture_payment_paypal_pro_failed( $order, $parsed_response ) {
			// Get error message.
			if ( ! empty( $parsed_response['L_LONGMESSAGE0'] ) ) {
				$error_message = $parsed_response['L_LONGMESSAGE0'];
			} elseif ( ! empty( $parsed_response['L_SHORTMESSAGE0'] ) ) {
				$error_message = $parsed_response['L_SHORTMESSAGE0'];
			} elseif ( ! empty( $parsed_response['L_SEVERITYCODE0'] ) ) {
				$error_message = $parsed_response['L_SEVERITYCODE0'];
			}

			// Back to on-hold.
			$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro capture failed (Correlation ID: %s). Capture was rejected due to an error: ', 'woocommerce-gateway-paypal-pro' ), $parsed_response['CORRELATIONID'] ) . '(' . $parsed_response['L_ERRORCODE0'] . ') ' . '"' . $error_message . '"' );
		}

		/**
		 * Capture transaction in given order when it's paid via PayPal Pro PayFlow.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order Order object.
		 *
		 * @return bool Returns true if captured successfully
		 */
		protected function _capture_payment_paypal_pro_payflow( $order ) {
			$paypalpro_payflow = new WC_Gateway_PayPal_Pro_PayFlow();

			$txn_id = $this->_get_transaction_id( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() );
			$url    = $paypalpro_payflow->testmode ? $paypalpro_payflow->testurl : $paypalpro_payflow->liveurl;

			$post_data            = array();
			$post_data['USER']    = $paypalpro_payflow->paypal_user;
			$post_data['VENDOR']  = $paypalpro_payflow->paypal_vendor;
			$post_data['PARTNER'] = $paypalpro_payflow->paypal_partner;
			$post_data['PWD']     = $paypalpro_payflow->paypal_password;
			$post_data['TRXTYPE'] = 'D'; // payflow only allows delayed capture for authorized only transactions.
			$post_data['AMT']     = $order->get_total();
			$post_data['ORIGID']  = $txn_id;

			if ( $paypalpro_payflow->soft_descriptor ) {
				$post_data['MERCHDESCR'] = $paypalpro_payflow->soft_descriptor;
			}

			$paypalpro_payflow->log( 'Capture payment request: ' . print_r( $post_data, true ) );

			$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'body'        => urldecode( http_build_query( $post_data, null, '&' ) ),
				'timeout'     => 70,
				'user-agent'  => 'WooCommerce',
				'httpversion' => '1.1',
			));

			if ( is_wp_error( $response ) ) {
				$order->add_order_note( __( 'Unable to capture charge!', 'woocommerce-gateway-paypal-pro' ) . ' ' . $response->get_error_message() );
				$paypalpro_payflow->log( 'Error: ' . $response->get_error_message() );

				return false;
			}

			parse_str( $response['body'], $parsed_response );

			$paypalpro_payflow->log( 'Parsed Response ' . print_r( $parsed_response, true ) );

			if ( '0' !== $parsed_response['RESULT'] ) {
				$this->_capture_payment_paypal_pro_payflow_failed( $order, $parsed_response );
				return false;
			}

			$this->_capture_payment_paypal_pro_payflow_success( $order, $parsed_response );

			return true;
		}

		/**
		 * Action performed when capture succeed.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order           Order object.
		 * @param array    $parsed_response Parsed response.
		 *
		 * @return void
		 */
		protected function _capture_payment_paypal_pro_payflow_success( $order, $parsed_response ) {
			$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

			$order->add_order_note( sprintf( __( 'PayPal Pro (Payflow) delay charge complete (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['PNREF'] ) );

			update_post_meta( $order_id, '_paypalpro_charge_captured', 'yes' );

			// update the transaction ID of the capture.
			update_post_meta( $order_id, '_transaction_id', $parsed_response['PNREF'] );
		}

		/**
		 * Action performed when capture failed.
		 *
		 * @since 4.4.4
		 *
		 * @param WC_Order $order           Order object.
		 * @param array    $parsed_response Parsed response.
		 *
		 * @return void
		 */
		protected function _capture_payment_paypal_pro_payflow_failed( $order, $parsed_response ) {
			$correlation_id = ! empty( $parsed_response['CORRELATIONID'] ) ? $parsed_response['CORRELATIONID'] : 'N/A';
			// Back to on-hold.
			$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro (Payflow) capture failed (Correlation ID: %s). Capture was rejected due to an error: ', 'woocommerce-gateway-paypal-pro' ), $correlation_id ) . '(' . $parsed_response['RESULT'] . ') ' . '"' . $parsed_response['RESPMSG'] . '"' );
		}

		/**
		 * Cancel pre-auth on refund/cancellation
		 *
		 * @param int $order_id Order ID.
		 */
		public function cancel_payment( $order_id ) {
			$order = new WC_Order( $order_id );

			$txn_id   = get_post_meta( $order_id, '_transaction_id', true );
			$captured = get_post_meta( $order_id, '_paypalpro_charge_captured', true );

			$payment_method = version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method();

			if ( 'paypal_pro' === $payment_method && $txn_id && 'no' === $captured ) {

				$paypalpro = new WC_Gateway_PayPal_Pro();

				$url = $paypalpro->testmode ? $paypalpro->testurl : $paypalpro->liveurl;

				$post_data = array(
					'VERSION'         => $paypalpro->api_version,
					'SIGNATURE'       => $paypalpro->api_signature,
					'USER'            => $paypalpro->api_username,
					'PWD'             => $paypalpro->api_password,
					'METHOD'          => 'DoVoid',
					'AUTHORIZATIONID' => $txn_id,
				);

				$response = wp_remote_post( $url, array(
					'method'		=> 'POST',
					'headers'       => array(
						'PAYPAL-NVP' => 'Y',
					),
					'body'          => $post_data,
					'timeout'       => 70,
					'user-agent'    => 'WooCommerce',
					'httpversion'   => '1.1',
				));

				if ( is_wp_error( $response ) ) {
					$order->add_order_note( __( 'Unable to void charge!', 'woocommerce-gateway-paypal-pro' ) . ' ' . $response->get_error_message() );

				} else {
					parse_str( $response['body'], $parsed_response );

					$order->add_order_note( sprintf( __( 'PayPal Pro void complete (Authorization ID: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['AUTHORIZATIONID'] ) );

					delete_post_meta( $order_id, '_paypalpro_charge_captured' );
					delete_post_meta( $order_id, '_transaction_id' );
				}
			}

			if ( 'paypal_pro_payflow' === $payment_method && $txn_id && 'no' === $captured ) {

				$paypalpro_payflow = new WC_Gateway_PayPal_Pro_Payflow();

				$url = $paypalpro_payflow->testmode ? $paypalpro_payflow->testurl : $paypalpro_payflow->liveurl;

				$post_data                 = array();
				$post_data['USER']         = $paypalpro_payflow->paypal_user;
				$post_data['VENDOR']       = $paypalpro_payflow->paypal_vendor;
				$post_data['PARTNER']      = $paypalpro_payflow->paypal_partner;
				$post_data['PWD']          = $paypalpro_payflow->paypal_password;
				$post_data['TRXTYPE']      = 'V'; // Void.
				$post_data['ORIGID']        = $txn_id;

				$response = wp_remote_post( $url, array(
					'method'      => 'POST',
					'body'        => urldecode( http_build_query( $post_data, null, '&' ) ),
					'timeout'     => 70,
					'user-agent'  => 'WooCommerce',
					'httpversion' => '1.1',
				));

				parse_str( $response['body'], $parsed_response );

				if ( is_wp_error( $response ) ) {
					$order->add_order_note( __( 'Unable to void charge!', 'woocommerce-gateway-paypal-pro' ) . ' ' . $response->get_error_message() );

				} elseif ( '0' !== $parsed_response['RESULT'] ) {
					$order->add_order_note( __( 'Unable to void charge!', 'woocommerce-gateway-paypal-pro' ) . ' ' . $response->get_error_message() );

					// Log it.
					$paypalpro_payflow->log( 'Parsed Response ' . print_r( $parsed_response, true ) );
				} else {
					$order->add_order_note( sprintf( __( 'PayPal Pro (Payflow) void complete (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['PNREF'] ) );

					delete_post_meta( $order_id, '_paypalpro_charge_captured' );
					delete_post_meta( $order_id, '_transaction_id' );
				}
			}
		}
	}

	add_action( 'plugins_loaded', 'woocommerce_paypal_pro_init', 0 );

	/**
	 * Init function.
	 *
	 * @package  WC_PayPal_Pro
	 * @since 4.3.0
	 * @return bool
	 */
	function woocommerce_paypal_pro_init() {
		$GLOBALS['wc_paypal_pro'] = wc_paypal_pro();

		return true;
	}

	/**
	 * Return instance of WC_PayPal_Pro.
	 *
	 * @since 4.4.4
	 *
	 * @return WC_PayPal_Pro
	 */
	function wc_paypal_pro() {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = new WC_PayPal_Pro();
		}

		return $plugin;
	}

endif;
