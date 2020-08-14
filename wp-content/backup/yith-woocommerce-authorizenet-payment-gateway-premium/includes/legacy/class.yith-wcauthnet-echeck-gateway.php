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

if ( ! class_exists( 'YITH_WCAUTHNET_eCheck_Gateway' ) ) {
	/**
	 * WooCommerce Authorize.net eCheck gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_eCheck_Gateway extends WC_Payment_Gateway {
		/**
		 * Authorize.net gateway id
		 *
		 * @var string Id of specific gateway
		 *
		 * @since 1.0
		 */
		public static $gateway_id = 'yith_wcauthnet_echeck_gateway';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_eCheck_Gateway
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_eCheck_Gateway
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
		 * @return \YITH_WCAUTHNET_eCheck_Gateway
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                 = self::$gateway_id;
			$this->method_title       = apply_filters( 'yith_wcauthnet_echeck_method_title', __( 'Authorize.net eCheck', 'yith-woocommerce-authorizenet-payment-gateway' ) );
			$this->method_description = apply_filters( 'yith_wcauthnet_echeck_method_description', __( 'Pay with Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) );

			$this->init_form_fields();
			$this->init_settings();

			$login_id = YITH_WCAUTHNET_Credit_Card_Gateway()->get_option( 'login_id' );
			$login_id = ! empty( $login_id ) ? $login_id : $this->get_option( 'login_id' );
			$login_id = trim( $login_id );
			$tran_key = YITH_WCAUTHNET_Credit_Card_Gateway()->get_option( 'transaction_key' );
			$tran_key = ! empty( $tran_key ) ? $tran_key : $this->get_option( 'transaction_key' );
			$tran_key = trim( $tran_key );
			$md5_hash = YITH_WCAUTHNET_Credit_Card_Gateway()->get_option( 'md5_hash' );
			$md5_hash = ! empty( $md5_hash ) ? $md5_hash : $this->get_option( 'md5_hash' );
			$md5_hash = trim( $md5_hash );

			// retrieves gateway options
			$this->enabled           = $this->get_option( 'enabled' );
			$this->order_button_text = apply_filters( 'yith_wcauthnet_echeck_order_button_text', $this->get_option( 'order_button', __( 'Proceed to Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ) ) );
			$this->title             = $this->get_option( 'title' );
			$this->description       = $this->get_option( 'description' );
			$this->login_id          = $login_id;
			$this->transaction_key   = $tran_key;
			$this->md5_hash          = $md5_hash;
			$this->itemized          = $this->get_option( 'itemized' ) == 'yes';
			$this->sandbox           = $this->get_option( 'sandbox' ) == 'yes';
			$this->debug             = $this->get_option( 'debug' );

			// init api handler object
			$this->api                  = YITH_WCAUTHNET_CIM_API();
			$this->api->login_id        = $this->login_id;
			$this->api->transaction_key = $this->transaction_key;
			$this->api->sandbox         = ( $this->sandbox == 'yes' );
			$this->api->itemized        = $this->itemized == 'yes';
			$this->api->cim_handling    = true;

			$this->supports = array( 'default_credit_card_form', 'refunds' );

			// Logs
			if ( 'yes' == $this->debug ) {
				$this->log = new WC_Logger();
			}

			// gateway requires fields
			$this->has_fields = true;

			// register admin options
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );

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
				'enabled'         => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Authorize.net eCheck Payment', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default' => 'no'
				),
				'title'           => array(
					'title'       => __( 'Title', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'This option lets you change the title that users see during the checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => __( 'eCheck on Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'desc_tip'    => true,
				),
				'description'     => array(
					'title'       => __( 'Description', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'textarea',
					'description' => __( 'This option lets you change the description that users see during checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => ''
				),
				'order_button'    => array(
					'title'       => __( 'Order Button Text', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'This option lets you change the label of the button that users see during the checkout.', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => __( 'Proceed to Authorize.net', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'desc_tip'    => true,
				),
				'login_id'        => array(
					'title'       => __( 'Login ID', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'Univocal ID login associated to the account of the admin (it can be recovered in the "API Login ID and Transaction Key" section). If no detail is stated, the system will try to use the parameter of the "Authorize.net Credit Card" payment gateway.', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'transaction_key' => array(
					'title'       => __( 'Transaction Key', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'A unique key used to validate requests to Authorize.net (it can be recovered in the "API Login ID and Transaction Key" section). If no detail is stated, the system will try to use the parameter of the "Authorize.net Credit Card" payment gateway.', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'md5_hash'        => array(
					'title'       => __( 'Md5 Hash', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'text',
					'description' => __( 'A unique key used to validate the answers from Authorize.net (it can be set in the " MD5 Hash " section). If no detail is stated, the system will try to use the parameter of the "Authorize.net Credit Card" payment gateway.', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'itemized'        => array(
					'title'       => __( 'Enable itemized transaction', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => __( 'Enable the registration of the items in the cart during the transaction (up to a maximum of 30 items)', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'default'     => 'yes'
				),
				'sandbox'         => array(
					'title'       => __( 'Enable Authorize.net sandbox', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => __( 'Activate the sandbox mode to test the configuration', 'yith-woocommerce-authorizenet-payment-gateway' )
				),
				'debug'           => array(
					'title'       => __( 'Debug Log', 'yith-woocommerce-authorizenet-payment-gateway' ),
					'type'        => 'checkbox',
					'description' => sprintf( __( 'Log of the Authorize.net events inside <code>%s</code>', 'yith-woocommerce-authorizenet-payment-gateway' ), wc_get_log_file_path( 'authorize.net' ) )
				)
			) );
		}

		/**
		 * Display the payment fields on the checkout page
		 *
		 * @since  1.0
		 */
		public function payment_fields() {

			if ( $this->description ) {
				echo '<p>' . wp_kses_post( $this->description ) . '</p>';
			}
			?>
			<fieldset>
				<div class="yith-wcauthnet-echeck-form">
					<p class="form-row form-row-first">
						<label for="yith-wcauthnet-routing-number">
							<?php _e( "Bank Routing Number", 'yith-woocommerce-authorizenet-payment-gateway' ); ?>
							<span class="required">*</span>
						</label>
						<input type="text" class="input-text" id="<?php echo esc_attr( $this->id ) ?>-routing-number" name="<?php echo esc_attr( $this->id ) ?>-routing-number" maxlength="9" autocomplete="off"/>
					</p>

					<p class="form-row form-row-last">
						<label for="authorize-net-cim-account-number">
							<?php _e( "Bank Account Number", 'yith-woocommerce-authorizenet-payment-gateway' ); ?>
							<span class="required">*</span>
						</label>
						<input type="text" class="input-text" id="<?php echo esc_attr( $this->id ) ?>-account-number" name="<?php echo esc_attr( $this->id ) ?>-account-number" maxlength="17" autocomplete="off"/>
					</p>

					<div class="clear"></div>
				</div>
			</fieldset>
			<?php
		}

		/**
		 * Advise if the plugin cannot be performed
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_notices() {
			if ( empty( $this->login_id ) || empty( $this->transaction_key ) ) {
				echo '<div class="error"><p>' . __( 'Please enter Login ID and Transaction Key for Authorize.net eCheck gateway.', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p></div>';
			}
		}

		/**
		 * Process payment
		 *
		 * @param $order_id int Current order id
		 *
		 * @return null|array Null on failure; array on success ( id provided: 'status' [string] textual status of the payment / 'redirect' [string] Url where to redirect user )
		 */
		public function process_payment( $order_id ) {
			$order   = wc_get_order( $order_id );
			$payment = $this->_get_payment_details( $order );

			if ( 'echeck' == $payment->type && ( empty( $payment->routing_number ) || empty( $payment->account_number ) ) ) {
				wc_add_notice( __( 'Missing required information', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

				return;
			}

			$response = $this->api->create_payment_transaction( $order, $payment );

			if ( ! empty( $response ) ) {

				if ( ! empty( $response->transactionResponse ) ) {
					$transaction_status = (string) $response->transactionResponse->responseCode;
					if ( is_array( $response->messages ) ) {
						$transaction_message = (string) $response->messages->message[0]->text;
					} else {
						$transaction_message = (string) $response->messages->message->text;
					}
					$transaction_id          = (string) $response->transactionResponse->transId;
					$transaction_amount      = false;
					$transaction_email       = false;
					$transaction_hash        = (string) $response->transactionResponse->transHash;
					$transaction_account_num = (string) $response->transactionResponse->accountNumber;

					if ( ! empty( $response->transactionResponse->userFields->userField ) ) {
						foreach ( $response->transactionResponse->userFields->userField as $field ) {
							${(string) $field->name} = (string) $field->value;
						}
					}

					if ( 1 == $transaction_status ) {
						if ( $order->has_status( 'completed' ) ) {
							if ( 'yes' == $this->debug ) {
								$this->log->add( 'authorize.net', 'Aborting, Order #' . $order->id . ' is already complete.' );
							}

							return array(
								'status'   => 'success',
								'redirect' => $this->get_return_url( $order )
							);
						} else {
							// Validate amount
							if ( $order->get_total() != $transaction_amount ) {
								if ( 'yes' == $this->debug ) {
									$this->log->add( 'authorize.net', 'Payment error: Amounts do not match (gross ' . $transaction_amount . ')' );
								}

								// Put this order on-hold for manual checking
								$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_amount ) );

								wc_add_notice( sprintf( __( 'Validation error: Authorize.net amounts do not match with (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_amount ), 'error' );

								return;
							}

							// Validate Email Address
							if ( strcasecmp( trim( $order->billing_email ), trim( $transaction_email ) ) != 0 ) {
								if ( 'yes' == $this->debug ) {
									$this->log->add( 'authorize.net', "Payment error: Authorize.net email ({$transaction_email}) does not match our email ({$order->billing_email})" );
								}

								// Put this order on-hold for manual checking
								$order->update_status( 'on-hold', sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_email ) );

								wc_add_notice( sprintf( __( 'Validation error: Authorize.net responses from a different email address than (%s).', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_email ), 'error' );

								return;
							}

							// Redundant md5 hash check
							if ( ! empty( $this->md5_hash ) ) {
								$expected_hash = strtoupper( md5( $this->md5_hash . $this->login_id . $transaction_id . number_format( $order->get_total(), 2, '.', '' ) ) );

								if ( strcasecmp( trim( $transaction_hash ), trim( $expected_hash ) ) != 0 ) {
									if ( 'yes' == $this->debug ) {
										$this->log->add( 'authorize.net', "Payment error: MD5 Hash control failed" );
									}

									// Put this order on-hold for manual checking
									$order->update_status( 'on-hold', __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ) );

									wc_add_notice( __( 'Validation error: MD5 Hash control failed.', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

									return;
								}
							}

							// Mark as complete
							$order->add_order_note( sprintf( __( 'Authorize.net payment completed (message: %s). Transaction ID: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $transaction_message, $transaction_id ) );
							$order->payment_complete( $transaction_id );

							if ( ! empty( $transaction_account_num ) ) {
								update_post_meta( $order->id, 'x_account_num', wc_clean( $transaction_account_num ) );
								update_post_meta( $order->id, 'x_routing_num', wc_clean( $payment->routing_number ) );
							}

							// Remove cart
							WC()->cart->empty_cart();

							if ( 'yes' == $this->debug ) {
								$this->log->add( 'authorize.net', 'Payment Result: ' . print_r( $response, true ) );
							}

							return array(
								'result'   => 'success',
								'redirect' => $this->get_return_url( $order )
							);
						}
					} else {
						$response_message = '';

						if ( is_array( $response->transactionResponse->errors->error ) ) {
							foreach ( $response->transactionResponse->errors->error as $error ) {
								$response_message .= $error->errorText . '\n';
							}
						} else {
							$response_message = $response->transactionResponse->errors->error->errorText;
						}

						wc_add_notice( sprintf( __( 'Payment error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ), 'error' );

						return;
					}
				} else {
					$response_message = '';
					if ( isset( $response->messages ) ) {
						$response_message = $response->messages->message->text;
					}

					if ( 'yes' == $this->debug ) {
						$this->log->add( 'authorize.net', 'Payment error: ' . $response_message );
					}

					wc_add_notice( sprintf( __( 'Payment error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ), 'error' );

					return array(
						'result'   => 'fail',
						'redirect' => ''
					);
				}
			} else {
				wc_add_notice( __( 'An error occurred while processing the payment; please try again later', 'yith-woocommerce-authorizenet-payment-gateway' ), 'error' );

				return;
			}
		}

		/**
		 * Process refund request, via API calls
		 *
		 * @param $order_id int Order id
		 * @param $amount   float|null Amount to refund; if null, the entire amount will be refunded
		 * @param $reason   string Reason for refund
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );

			if ( ! $order || ! $this->can_refund_order( $order ) ) {
				$this->log->add( 'authorize.net', 'Refund Failed: No transaction ID' );
				$order->add_order_note( __( 'Authorize.net refund failed: No transaction ID', 'yith-woocommerce-authorizenet-payment-gateway' ) );

				return false;
			}

			$response = $this->api->crete_refund_transaction( $order, $amount, $this->_get_refund_details( $order ) );

			if ( ! empty( $response ) ) {
				$transaction_status = (int) $response->transactionResponse->responseCode;
				if ( 1 == $transaction_status ) {
					$this->log->add( 'authorize.net', 'Refund Result: ' . print_r( $response, true ) );
					$order->add_order_note( __( 'Authorize.net refund approved', 'yith-woocommerce-authorizenet-payment-gateway' ) );
				} else {
					$response_message = '';

					if ( is_array( $response->transactionResponse->errors->error ) ) {
						foreach ( $response->transactionResponse->errors->error as $error ) {
							$response_message .= $error->errorText . '\n';
						}
					} else {
						$response_message = $response->transactionResponse->errors->error->errorText;
					}

					$this->log->add( 'authorize.net', sprintf( __( 'Refund error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ) );
					$order->add_order_note( sprintf( __( 'Refund error: %s', 'yith-woocommerce-authorizenet-payment-gateway' ), $response_message ) );

					return false;
				}
			} else {
				$this->log->add( 'authorize.net', __( 'Refund error: unknown error', 'yith-woocommerce-authorizenet-payment-gateway' ) );
				$order->add_order_note( __( 'Refund error: unknown error', 'yith-woocommerce-authorizenet-payment-gateway' ) );

				return false;
			}

			return true;
		}

		/**
		 * Returns an object with payment details
		 *
		 * @param $order \WC_Order Order to pay
		 *
		 * @return \StdClass payment object
		 * @since 1.0.0
		 */
		protected function _get_payment_details( $order ) {
			$payment_obj = new StdClass();

			$payment_obj->type = "echeck";
			if ( isset( $_POST[ $this->id . '-routing-number' ] ) ) {
				$payment_obj->routing_number = esc_attr( $_POST[ $this->id . '-routing-number' ] );
			}

			if ( isset( $_POST[ $this->id . '-account-number' ] ) ) {
				$payment_obj->account_number = esc_attr( $_POST[ $this->id . '-account-number' ] );
			}

			$payment_obj->name_on_account = $order->billing_first_name . ' ' . $order->billing_last_name;

			return $payment_obj;
		}

		/**
		 * Returns an object with refund details
		 *
		 * @param $order \WC_Order Order to refund
		 *
		 * @return \StdClass refund object
		 * @since 1.0.0
		 */
		protected function _get_refund_details( $order ) {
			$payment_obj = new StdClass();

			$payment_obj->type = "echeck";

			$payment_obj->routing_number  = get_post_meta( $order->id, 'x_routing_num', true );
			$payment_obj->account_number  = get_post_meta( $order->id, 'x_account_num', true );
			$payment_obj->name_on_account = $order->billing_first_name . ' ' . $order->billing_last_name;

			return $payment_obj;
		}

		/* === UTILITY METHODS === */

		/**
		 * Can the order be refunded via authorize.net?
		 *
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function can_refund_order( $order ) {
			return $order && $order->get_transaction_id();
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_eCheck_Gateway class
 *
 * @return \YITH_WCAUTHNET_eCheck_Gateway
 * @since 1.0.0
 */
function YITH_WCAUTHNET_eCheck_Gateway() {
	return YITH_WCAUTHNET_eCheck_Gateway::get_instance();
}