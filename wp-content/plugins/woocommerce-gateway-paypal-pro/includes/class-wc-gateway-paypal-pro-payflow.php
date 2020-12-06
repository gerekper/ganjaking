<?php

/**
 * WC_Gateway_PayPal_Pro_PayFlow class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_PayPal_Pro_PayFlow extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id					= 'paypal_pro_payflow';
		$this->method_title 		= __( 'PayPal Pro PayFlow', 'woocommerce-gateway-paypal-pro' );
		$this->method_description 	= __( 'PayPal Pro PayFlow Edition works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'woocommerce-gateway-paypal-pro' );
		$this->icon 				= apply_filters('woocommerce_paypal_pro_payflow_icon', WP_PLUGIN_URL . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/assets/images/cards.png' );
		$this->has_fields 			= true;
		$this->supports             = array(
			'products',
			'refunds'
		);
		$this->liveurl				= 'https://payflowpro.paypal.com';
		$this->testurl				= 'https://pilot-payflowpro.paypal.com';
		$this->allowed_currencies   = apply_filters( 'woocommerce_paypal_pro_allowed_currencies', array( 'USD', 'EUR', 'GBP', 'CAD', 'JPY', 'AUD' ) );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values
		$this->title                = $this->get_option( 'title' );
		$this->description          = $this->get_option( 'description' );
		$this->enabled              = $this->get_option( 'enabled' );
		$this->paypal_vendor        = $this->get_option( 'paypal_vendor' );
		$this->paypal_partner       = $this->get_option( 'paypal_partner', 'PayPal' );
		$this->paypal_password      = trim( $this->get_option( 'paypal_password' ) );
		$this->paypal_user          = $this->get_option( 'paypal_user', $this->paypal_vendor );
		$this->testmode             = $this->get_option( 'testmode' ) === "yes" ? true : false;
		$this->debug                = $this->get_option( 'debug', "no" ) === "yes" ? true : false;
		$this->transparent_redirect = $this->get_option( 'transparent_redirect' ) === "yes" ? true : false;
		$this->soft_descriptor      = str_replace( ' ', '-', preg_replace('/[^A-Za-z0-9\-\.]/', '', $this->get_option( 'soft_descriptor', "" ) ) );
		$this->paymentaction        = strtoupper( $this->get_option( 'paypal_pro_payflow_paymentaction', 'S' ) );

		if ( $this->transparent_redirect ) {
			$this->order_button_text    = __( 'Enter payment details', 'woocommerce-gateway-paypal-pro' );
		}

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_receipt_paypal_pro_payflow', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_api_wc_gateway_paypal_pro_payflow', array( $this, 'return_handler' ) );
	}

	/**
     * Initialise Gateway Settings Form Fields
     */
	public function init_form_fields() {
    	$this->form_fields = array(
			'enabled'         => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Pro Payflow Edition', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title'           => array(
				'title'       => __( 'Title', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Credit card (PayPal)', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'description'     => array(
				'title'       => __( 'Description', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Pay with your credit card.', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'soft_descriptor' => array(
				'title'             => __( 'Soft Descriptor', 'woocommerce-gateway-paypal-pro' ),
				'type'              => 'text',
				'description'       => __( '(Optional) Information that is usually displayed in the account holder\'s statement, for example your website name. Only 23 alphanumeric characters can be included, including the special characters dash (-) and dot (.) . Asterisks (*) and spaces ( ) are NOT permitted.', 'woocommerce-gateway-paypal-pro' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'maxlength' => 23,
					'pattern' => '[a-zA-Z0-9.-]+'
				)
			),
			'testmode'        => array(
				'title'       => __( 'Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Sandbox/Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in development mode.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'transparent_redirect' => array(
				'title'       => __( 'Transparent Redirect', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable Transparent Redirect', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Rather than showing a credit card form on your checkout, this shows the form on it\'s own page and posts straight to PayPal, thus making the process more secure and more PCI friendly. "Enable Secure Token" needs to be enabled on your PayFlow account to work.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'paypal_vendor'   => array(
				'title'       => __( 'PayPal Vendor', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Your merchant login ID that you created when you registered for the account.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_password' => array(
				'title'       => __( 'PayPal Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'password',
				'description' => __( 'The password that you defined while registering for the account.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_user'     => array(
				'title'       => __( 'PayPal User', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'If you set up one or more additional users on the account, this value is the ID
			of the user authorized to process transactions. Otherwise, leave this field blank.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_partner'  => array(
				'title'       => __( 'PayPal Partner', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'The ID provided to you by the authorized PayPal Reseller who registered you
			for the Payflow SDK. If you purchased your account directly from PayPal, use PayPal or leave blank.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'PayPal',
				'desc_tip'    => true
			),
			'paypal_pro_payflow_paymentaction' => array(
				'title'       => __( 'Payment Action', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'select',
				'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'sale',
				'desc_tip'    => true,
				'options'     => array(
					'S'          => __( 'Capture', 'woocommerce-gateway-paypal-pro' ),
					'A'          => __( 'Authorize', 'woocommerce-gateway-paypal-pro' )
				)
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Log PayPal Pro (Payflow) events inside <code>woocommerce/logs/paypal-pro-payflow.txt</code>', 'woocommerce-gateway-paypal-pro' ),
			)
		);
    }

	/**
     * Check if this gateway is enabled and available in the user's country
     *
     * This method no is used anywhere??? put above but need a fix below
     */
	public function is_available() {
		if ( $this->enabled === "yes" ) {

			if ( ! is_ssl() && ! $this->testmode ) {
				return false;
			}

			// Currency check
			if ( ! in_array( get_option( 'woocommerce_currency' ), $this->allowed_currencies ) ) {
				return false;
			}

			// Required fields check
			if ( ! $this->paypal_vendor || ! $this->paypal_password ) {
				return false;
			}

			return true;
		}
		return false;
	}

	/**
     * Process the payment
     */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		$this->log( 'Processing order #' . $order_id );

		if ( $this->transparent_redirect ) {

			return array(
				'result' 	=> 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
			);

		} else {
			$card_number    = isset( $_POST['paypal_pro_payflow-card-number'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-number'] ) : '';
			$card_cvc       = isset( $_POST['paypal_pro_payflow-card-cvc'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-cvc'] ) : '';
			$card_expiry    = isset( $_POST['paypal_pro_payflow-card-expiry'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-expiry'] ) : '';

			// Format values
			$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
			$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
			$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
			$card_exp_year  = $card_expiry[1];

			if ( strlen( $card_exp_year ) == 4 ) {
				$card_exp_year = $card_exp_year - 2000;
			}

			// Do payment with paypal
			return $this->do_payment( $order, $card_number, $card_exp_month . $card_exp_year, $card_cvc );
		}
	}

	/**
	 * Receipt_page for showing the payment form which sends data to authorize.net
	 */
	public function receipt_page( $order_id ) {
		if ( $this->transparent_redirect ) {
			// load in script to better handle credit card form formatting
			wp_enqueue_script( 'jquery-payment' );

			// Get the order
			$order     = new WC_Order( $order_id );
			$url       = $this->testmode ? 'https://pilot-payflowlink.paypal.com' : 'https://payflowlink.paypal.com';
			$post_data = $this->_get_post_data( $order );

			// Request token
			$token     = $this->get_token( $order, $post_data );

			if ( ! $token ) {
				wc_print_notices();
				return;
			}

			echo wpautop( __( 'Enter your payment details below and click "Confirm and pay" to securely pay for your order.', 'woocommerce-gateway-paypal-pro' ) );
			?>
			<form method="POST" action="<?php echo $url; ?>">
				<div id="payment">
					<label style="padding:10px 0 0 10px;display:block;"><?php echo $this->title . ' ' . '<div style="vertical-align:middle;display:inline-block;margin:2px 0 0 .5em;">' . $this->get_icon() . '</div>'; ?></label>
					<div class="payment_box">
						<p><?php echo $this->description . ( $this->testmode ? ' ' . __( 'TEST/SANDBOX MODE ENABLED. In test mode, you can use the card number 4111111111111111 with any CVC and a valid expiration date.', 'woocommerce-gateway-paypal-pro' ) : '' ); ?></p>

						<fieldset id="paypal_pro_payflow-cc-form">
							<p class="form-row form-row-wide">
								<label for="paypal_pro_payflow-card-number"><?php _e( 'Card Number ', 'woocommerce-gateway-paypal-pro' ); ?><span class="required">*</span></label>
								<input type="text" id="paypal_pro_payflow-card-number" class="input-text wc-credit-card-form-card-number" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="CARDNUM" />
							</p>

							<p class="form-row form-row-first">
								<label for="paypal_pro_payflow-card-expiry"><?php _e( 'Expiry (MM/YY) ', 'woocommerce-gateway-paypal-pro' ); ?><span class="required">*</span></label>
								<input type="text" id="paypal_pro_payflow-card-expiry" class="input-text wc-credit-card-form-card-expiry" autocomplete="off" placeholder="MM / YY" name="EXPDATE" />
							</p>

							<p class="form-row form-row-last">
								<label for="paypal_pro_payflow-card-cvc"><?php _e( 'Card Code ', 'woocommerce-gateway-paypal-pro' ); ?><span class="required">*</span></label>
								<input type="text" id="paypal_pro_payflow-card-cvc" class="input-text wc-credit-card-form-card-cvc" autocomplete="off" placeholder="CVC" name="CVV2" />
							</p>

							<input type="hidden" name="SECURETOKEN" value="<?php echo esc_attr( $token['SECURETOKEN'] ); ?>" />
							<input type="hidden" name="SECURETOKENID" value="<?php echo esc_attr( $token['SECURETOKENID'] ); ?>" />
							<input type="hidden" name="SILENTTRAN" value="TRUE" />
						</fieldset>
					</div>
					<input type="submit" value="<?php _e( 'Confirm and pay', 'woocommerce-gateway-paypal-pro' ); ?>" class="submit buy button" style="float:right;"/>
				</div>
				<script type="text/javascript">
					jQuery( function( $ ) {
						$( '.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
						$( '.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
						$( '.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );
					});
				</script>
			</form>
			<?php
		}
	}

	/**
	 * handles return data and does redirects
	 */
	public function return_handler() {
		// Clean
		@ob_clean();

		// Header
		header('HTTP/1.1 200 OK');

		$result   = isset( $_POST['RESULT'] ) ? absint( $_POST['RESULT'] ) : null;
		$order_id = isset( $_POST['INVOICE'] ) ? absint( ltrim( $_POST['INVOICE'], '#' ) ) : 0;

		if ( is_null( $result ) || empty( $order_id ) ) {
			echo "Invalid request.";
			exit;
		}

		// Get the order
		$order = new WC_Order( $order_id );

		switch ( $result ) {
			// Approved or screening service was down
			case 0 :
			case 127 :
				$txn_id = ( ! empty( $_POST['PNREF'] ) ) ? wc_clean( $_POST['PNREF'] ) : '';

				// get transaction details
				$details = $this->get_transaction_details( $txn_id );

				// check if it is captured or authorization only [transstate 3 is authoriztion only]
				if ( $details && strtolower( $details['TRANSSTATE'] ) === '3' ) {
					// Store captured value
					update_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), '_paypalpro_charge_captured', 'no' );
					update_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), '_transaction_id', $txn_id );

					// Mark as on-hold
					$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro (PayFlow) charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-paypal-pro' ), $txn_id ) );

					// Reduce stock levels
					if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
						$order->reduce_order_stock();
					} else {
						wc_reduce_stock_levels( $order->get_id() );
					}
				} else {

					// Add order note
					$order->add_order_note( sprintf( __( 'PayPal Pro (Payflow) payment completed (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['PNREF'] ) );

					// Payment complete
					$order->payment_complete( $txn_id );
				}

				// Remove cart
				WC()->cart->empty_cart();
				$redirect = $order->get_checkout_order_received_url();
			break;
			// Under Review by Fraud Service
			case 126 :
				$order->add_order_note( $_POST['RESPMSG'] );
				$order->add_order_note( $_POST['PREFPSMSG'] );
				$order->update_status( 'on-hold', __( 'The payment was flagged by a fraud filter. Please check your PayPal Manager account to review and accept or deny the payment and then mark this order "processing" or "cancelled".', 'woocommerce-gateway-paypal-pro' ) );
				WC()->cart->empty_cart();
				$redirect = $order->get_checkout_order_received_url();
			break;
			default :
				// Mark failed
				$order->update_status( 'failed', $_POST['RESPMSG'] );

				$redirect = $order->get_checkout_payment_url( true );
				$redirect = add_query_arg( 'wc_error', urlencode( wp_kses_post( $_POST['RESPMSG'] ) ), $redirect );

				if ( is_ssl() || get_option( 'woocommerce_force_ssl_checkout' ) == 'yes' ) {
					$redirect = str_replace( 'http:', 'https:', $redirect );
				}
			break;
		}

		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Get a token for transparent redirect
	 * @param  object $order
	 * @param  array $post_data
	 * @return bool or array
	 */
	public function get_token( $order, $post_data, $force_new_token = false ) {
		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
		$order_id = $pre_wc_30 ? $order->id : $order->get_id();

		if ( ! $force_new_token && get_post_meta( $order_id, '_SECURETOKENHASH', true ) == md5( json_encode( $post_data ) ) ) {
			return array(
				'SECURETOKEN'   => get_post_meta( $order_id, '_SECURETOKEN', true ),
				'SECURETOKENID' => get_post_meta( $order_id, '_SECURETOKENID', true )
			);
		}
		$post_data['SECURETOKENID']     = uniqid() . md5( $pre_wc_30 ? $order->order_key : $order->get_order_key() );
		$post_data['CREATESECURETOKEN'] = 'Y';
		$post_data['SILENTTRAN']        = 'TRUE';
		$post_data['ERRORURL']          = WC()->api_request_url( get_class() );
		$post_data['RETURNURL']         = WC()->api_request_url( get_class() );
		$post_data['URLMETHOD']         = 'POST';

		$response = wp_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
			'method'      => 'POST',
			'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_request', $post_data, $order ), null, '&' ) ),
			'timeout'     => 70,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			wc_add_notice( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			return false;
		}

		if ( empty( $response['body'] ) ) {
			wc_add_notice( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
			return false;
		}

		parse_str( $response['body'], $parsed_response );

		if ( isset( $parsed_response['RESULT'] ) && in_array( $parsed_response['RESULT'], array( 160, 161, 162 ) ) ) {
			return $this->get_token( $order, $post_data, $force_new_token );
		} elseif ( isset( $parsed_response['RESULT'] ) && $parsed_response['RESULT'] == 0 && ! empty( $parsed_response['SECURETOKEN'] ) ) {
			update_post_meta( $order_id, '_SECURETOKEN', $parsed_response['SECURETOKEN'] );
			update_post_meta( $order_id, '_SECURETOKENID', $parsed_response['SECURETOKENID'] );
			update_post_meta( $order_id, '_SECURETOKENHASH', md5( json_encode( $post_data ) ) );

			return array(
				'SECURETOKEN'   => $parsed_response['SECURETOKEN'],
				'SECURETOKENID' => $parsed_response['SECURETOKENID']
			);
		} else {
			$order->update_status( 'failed', __( 'PayPal Pro (Payflow) token generation failed: ', 'woocommerce-gateway-paypal-pro' ) . '(' . $parsed_response['RESULT'] . ') ' . '"' . $parsed_response['RESPMSG'] . '"' );

			wc_add_notice( __( 'Payment error:', 'woocommerce-gateway-paypal-pro' ) . ' ' . $parsed_response['RESPMSG'], 'error' );

			return false;
		}
	}

	/**
	 * Get a list of parameters to send to paypal.
	 *
	 * @param object $order Order object.
	 *
	 * @return array
	 */
	protected function _get_post_data( $order ) {
		$post_data                 = array();
		$post_data['USER']         = $this->paypal_user;
		$post_data['VENDOR']       = $this->paypal_vendor;
		$post_data['PARTNER']      = $this->paypal_partner;
		$post_data['PWD']          = $this->paypal_password;
		$post_data['TENDER']       = 'C'; // Credit card
		$post_data['TRXTYPE']      = $this->paymentaction; // Sale / Authorize

		// Transaction Amount = Total Tax Amount + Total Freight Amount + Total Handling Amount + Total Line Item Amount.
		$post_data['AMT']          = wc_format_decimal( $order->get_total(), 2 );
		$post_data['CURRENCY']     = ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency() ); // Currency code
		$post_data['CUSTIP']       = $this->get_user_ip(); // User IP Address
		$post_data['EMAIL']        = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
		$post_data['INVNUM']       = $order->get_order_number();
		$post_data['BUTTONSOURCE'] = 'WooThemes_Cart';

		if ( $this->soft_descriptor ) {
			$post_data['MERCHDESCR'] = $this->soft_descriptor;
		}

		/* Send Item details */
		$item_loop = 0;

		if ( count( $order->get_items() ) > 0 ) {
			$ITEMAMT = 0;

			foreach ( $order->get_items() as $item ) {
				$_product = $order->get_product_from_item( $item );

				if ( $item['qty'] ) {
					$post_data[ 'L_NAME' . $item_loop ] = $item['name'];
					$post_data[ 'L_COST' . $item_loop ] = wc_format_decimal( $order->get_item_total( $item, false ), 2 );
					$post_data[ 'L_QTY' . $item_loop ]  = $item['qty'];

					if ( $_product->get_sku() ) {
						$post_data[ 'L_SKU' . $item_loop ] = $_product->get_sku();
					}

					$ITEMAMT += $order->get_item_total( $item, false, false ) * $item['qty'];

					$item_loop++;
				}
			}

			// Fees
			foreach ( $order->get_fees() as $fee ) {
				$post_data[ 'L_NAME' . $item_loop ] = 'Fees';
				$post_data[ 'L_DESC' . $item_loop ] = trim( substr( $fee['name'], 0, 127 ) );
				$post_data[ 'L_COST' . $item_loop ] = $fee['line_total'];
				$post_data[ 'L_QTY' . $item_loop ]  = 1;

				$ITEMAMT += $fee['line_total'];
				$fee_total += $fee['line_total'];

				$item_loop++;
			}

			// Shipping.
			if ( $order->get_total_shipping() > 0 ) {
				$post_data['FREIGHTAMT'] = wc_format_decimal( $order->get_total_shipping(), 2 );
			}

			// Discount.
			if ( $order->get_total_discount( true ) > 0 ) {
				$post_data['DISCOUNT'] = wc_format_decimal( $order->get_total_discount( true ), 2 );
			}

			// Tax.
			if ( $order->get_total_tax() > 0 ) {
				$post_data['TAXAMT'] = wc_format_decimal( $order->get_total_tax(), 2 );
			}

			$post_data['ITEMAMT'] = wc_format_decimal( $ITEMAMT, 2 );
		}

		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

		$post_data['ORDERDESC']      = 'Order ' . $order->get_order_number() . ' on ' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$post_data['FIRSTNAME']      = $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name();
		$post_data['LASTNAME']       = $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name();
		$post_data['STREET']         = $pre_wc_30 ? ( $order->billing_address_1 . ' ' . $order->billing_address_2 ) : ( $order->get_billing_address_1() . ' ' . $order->get_billing_address_2() );
		$post_data['CITY']           = $pre_wc_30 ? $order->billing_city : $order->get_billing_city();
		$post_data['STATE']          = $pre_wc_30 ? $order->billing_state : $order->get_billing_state();
		$post_data['COUNTRY']        = $pre_wc_30 ? $order->billing_country : $order->get_billing_country();
		$post_data['ZIP']            = $pre_wc_30 ? $order->billing_postcode : $order->get_billing_postcode();

		if ( $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1() ) {
			$post_data['SHIPTOFIRSTNAME'] = $pre_wc_30 ? $order->shipping_first_name : $order->get_shipping_first_name();
			$post_data['SHIPTOLASTNAME']  = $pre_wc_30 ? $order->shipping_last_name : $order->get_shipping_last_name();
			$post_data['SHIPTOSTREET']    = $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1();
			$post_data['SHIPTOCITY']      = $pre_wc_30 ? $order->shipping_city : $order->get_shipping_city();
			$post_data['SHIPTOSTATE']     = $pre_wc_30 ? $order->shipping_state : $order->get_shipping_state();
			$post_data['SHIPTOCOUNTRY']   = $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country();
			$post_data['SHIPTOZIP']       = $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode();
		}

		return apply_filters( 'woocommerce_gateway_paypal_pro_payflow_post_data', $post_data );
	}

	/**
	 * Do payment request.
	 *
	 * @throws Exception If request failed or got unexpected response.
	 *
	 * @since 1.0.0
	 * @version 4.4.8
	 * @param object $order       Order object.
	 * @param string $card_number Card number.
	 * @param string $card_exp    Card expire date.
	 * @param string $card_cvc    Card CVV.
	 */
	public function do_payment( $order, $card_number, $card_exp, $card_cvc ) {

		// Send request to paypal.
		try {
			$url                  = $this->testmode ? $this->testurl : $this->liveurl;
			$post_data            = $this->_get_post_data( $order );
			$post_data['ACCT']    = $card_number; // Credit Card
			$post_data['EXPDATE'] = $card_exp; // MMYY
			$post_data['CVV2']    = $card_cvc; // CVV code.

			if ( $this->debug ) {
				$log         = $post_data;
				$log['ACCT'] = '****';
				$log['CVV2'] = '****';
				$this->log( 'Do payment request ' . print_r( $log, true ) );
			}

			$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_request', $post_data, $order ), null, '&' ) ),
				'timeout'     => 70,
				'user-agent'  => 'WooCommerce',
				'httpversion' => '1.1',
			) );

			if ( is_wp_error( $response ) ) {
				$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

				throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			}

			if ( empty( $response['body'] ) ) {
				$this->log( 'Empty response!' );

				throw new Exception( __( 'Empty PayPal response.', 'woocommerce-gateway-paypal-pro' ) );
			}

			parse_str( $response['body'], $parsed_response );

			$this->log( 'Parsed Response ' . print_r( $parsed_response, true ) );

			if ( isset( $parsed_response['RESULT'] ) && in_array( $parsed_response['RESULT'], array( 0, 126, 127 ) ) ) {

				switch ( $parsed_response['RESULT'] ) {
					// Approved or screening service was down.
					case 0 :
					case 127 :
						$txn_id = ( ! empty( $parsed_response['PNREF'] ) ) ? wc_clean( $parsed_response['PNREF'] ) : '';

						// Get transaction details.
						$details = $this->get_transaction_details( $txn_id );

						// Check if it is captured or authorization only [transstate 3 is authoriztion only].
						if ( $details && strtolower( $details['TRANSSTATE'] ) === '3' ) {
							$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

							// Store captured value.
							update_post_meta( $order_id, '_paypalpro_charge_captured', 'no' );

							version_compare( WC_VERSION, '3.0', '<' ) ? update_post_meta( $order_id, '_transaction_id', $txn_id ) : $order->set_transaction_id( $txn_id );

							// Mark as on-hold.
							$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro (PayFlow) charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-paypal-pro' ), $txn_id ) );

							// Reduce stock levels.
							if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
								$order->reduce_order_stock();
							} else {
								wc_reduce_stock_levels( $order_id );
							}
						} else {

							// Add order note.
							$order->add_order_note( sprintf( __( 'PayPal Pro (Payflow) payment completed (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['PNREF'] ) );

							// Payment complete.
							$order->payment_complete( $txn_id );
						}

						// Remove cart.
						WC()->cart->empty_cart();
					break;
					// Under Review by Fraud Service.
					case 126 :
						$order->add_order_note( $parsed_response['RESPMSG'] );
						$order->add_order_note( $parsed_response['PREFPSMSG'] );
						$order->update_status( 'on-hold', __( 'The payment was flagged by a fraud filter. Please check your PayPal Manager account to review and accept or deny the payment and then mark this order "processing" or "cancelled".', 'woocommerce-gateway-paypal-pro' ) );
					break;
				}

				$redirect = $order->get_checkout_order_received_url();

				// Return thank you page redirect.
				return array(
					'result' 	=> 'success',
					'redirect'	=> $redirect,
				);

			} else {

				// Payment failed.
				$order->update_status( 'failed', __( 'PayPal Pro (Payflow) payment failed. Payment was rejected due to an error: ', 'woocommerce-gateway-paypal-pro' ) . '(' . $parsed_response['RESULT'] . ') ' . '"' . $parsed_response['RESPMSG'] . '"' );

				wc_add_notice( __( 'Payment error:', 'woocommerce-gateway-paypal-pro' ) . ' ' . $parsed_response['RESPMSG'], 'error' );
				return;
			}
		} catch ( Exception $e ) {
			wc_add_notice( __( 'Connection error:', 'woocommerce-gateway-paypal-pro' ) . ': "' . $e->getMessage() . '"', 'error' );
			return;
		}
	}

	/**
	 * Get transaction details.
	 *
	 * @throws Exception
	 *
	 * @param string $transaction_id Transaction ID.
	 */
	public function get_transaction_details( $transaction_id = 0 ) {
		$url = $this->testmode ? $this->testurl : $this->liveurl;

		$post_data                 = array();
		$post_data['USER']         = $this->paypal_user;
		$post_data['VENDOR']       = $this->paypal_vendor;
		$post_data['PARTNER']      = $this->paypal_partner;
		$post_data['PWD']          = $this->paypal_password;
		$post_data['TRXTYPE']      = 'I';
		$post_data['ORIGID']        = $transaction_id;

		$response = wp_remote_post( $url, array(
			'method'      => 'POST',
			'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_transaction_details_request', $post_data, null, '&' ) ) ),
			'timeout'     => 70,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1',
		) );

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		parse_str( $response['body'], $parsed_response );

		if ( isset( $parsed_response['RESULT'] ) && '0' === $parsed_response['RESULT'] ) {
			return $parsed_response;
		}

		return false;
	}

	/**
	 * Process a refund if supported.
	 *
	 * @throws Exception If refund failed.
	 *
	 * @param int    $order_id Order ID.
	 * @param float  $amount   Amount.
	 * @param string $reason   Refund reason.
	 *
	 * @return bool|wp_error True or false based on success, or a WP_Error object
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		$url = $this->testmode ? $this->testurl : $this->liveurl;

		if ( ! $order || ! $order->get_transaction_id() || ! $this->paypal_user || ! $this->paypal_vendor || ! $this->paypal_password ) {
			return false;
		}

		// Get transaction details.
		$details = $this->get_transaction_details( $order->get_transaction_id() );

		// Check if it is authorized only we need to void instead.
		if ( $details && strtolower( $details['TRANSSTATE'] ) === '3' ) {
			$order->add_order_note( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );

			$this->log( 'Refund order # ' . $order_id . ': authorized only transactions need to use cancel/void instead.' );

			throw new Exception( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );
		}

		$post_data            = array();
		$post_data['USER']    = $this->paypal_user;
		$post_data['VENDOR']  = $this->paypal_vendor;
		$post_data['PARTNER'] = $this->paypal_partner;
		$post_data['PWD']     = $this->paypal_password;
		$post_data['TRXTYPE'] = 'C'; // credit/refund.
		$post_data['ORIGID']  = $order->get_transaction_id();

		if ( ! is_null( $amount ) ) {
			$post_data['AMT']      = wc_format_decimal( $amount, 2 );
			$post_data['CURRENCY'] = ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency() );
		}

		if ( $reason ) {
			if ( 255 < strlen( $reason ) ) {
				$reason = substr( $reason, 0, 252 ) . '...';
			}

			$post_data['COMMENT1'] = html_entity_decode( $reason, ENT_NOQUOTES, 'UTF-8' );
		}

		$response = wp_remote_post( $url, array(
			'method'      => 'POST',
			'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_refund_request', $post_data, null, '&' ) ) ),
			'timeout'     => 70,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1',
		));

		parse_str( $response['body'], $parsed_response );

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		if ( ! isset( $parsed_response['RESULT'] ) ) {
			throw new Exception( __( 'Unexpected response from PayPal.', 'woocommerce-gateway-paypal-pro' ) );
		}

		if ( '0' !== $parsed_response['RESULT'] ) {
				// Log it.
				$this->log( 'Parsed Response (refund) ' . print_r( $parsed_response, true ) );
		} else {

			$order->add_order_note( sprintf( __( 'Refunded %1$s - PNREF: %2$s', 'woocommerce-gateway-paypal-pro' ), wc_price( wc_format_decimal( $amount, 2 ) ), $parsed_response['PNREF'] ) );

			return true;
		}

		return false;
	}

	/**
	 * Payment form on checkout page.
	 */
	public function payment_fields() {
		wp_enqueue_script( 'wc-credit-card-form' );

		if ( $this->description ) {
			if ( $this->transparent_redirect ) {
				echo '<p>' . $this->description . '</p>';
			} else {
				echo '<p>' . $this->description . ( $this->testmode ? ' ' . __( 'TEST/SANDBOX MODE ENABLED. In test mode, you can use the card number 4111111111111111 with any CVC and a valid expiration date.', 'woocommerce-gateway-paypal-pro' ) : '' ) . '</p>';
			}
		}

		if ( ! $this->transparent_redirect ) {
			?>
			<fieldset>
				<p class="form-row form-row-wide">
					<label for="<?php echo esc_attr( $this->id ); ?>-card-number"><?php  esc_html_e( 'Card Number', 'woocommerce-gateway-paypal-pro' ); ?> <span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ); ?>-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="<?php echo esc_attr( $this->id ); ?>-card-number" />
				</p>

				<p class="form-row form-row-first">
					<label for="<?php echo esc_attr( $this->id ); ?>-card-expiry"><?php esc_html_e( 'Expiry (MM/YY)', 'woocommerce-gateway-paypal-pro' ); ?> <span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ); ?>-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="<?php esc_attr_e( 'MM / YY', 'woocommerce-gateway-paypal-pro' ); ?>" name="<?php echo esc_attr( $this->id ); ?>-card-expiry" />
				</p>

				<p class="form-row form-row-last">
					<label for="<?php echo esc_attr( $this->id ); ?>-card-cvc"><?php esc_html_e( 'Card Code', 'woocommerce-gateway-paypal-pro' ); ?> <span class="required">*</span></label>
					<input id="<?php echo esc_attr( $this->id ); ?>-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="<?php esc_attr_e( 'CVC', 'woocommerce-gateway-paypal-pro' ); ?>" name="<?php echo esc_attr( $this->id ); ?>-card-cvc" />
				</p>
			</fieldset>
			<?php
		}
	}

	/**
	 * Get user's IP address.
	 */
	public function get_user_ip() {
		return ! empty( $_SERVER['HTTP_X_FORWARD_FOR'] ) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Add a log entry.
	 *
	 * @param string $message Message to log
	 */
	public function log( $message ) {
		if ( $this->debug ) {
			if ( ! isset( $this->log ) ) {
				$this->log = new WC_Logger();
			}
			$this->log->add( 'paypal-pro-payflow', $message );
		}
	}
}
