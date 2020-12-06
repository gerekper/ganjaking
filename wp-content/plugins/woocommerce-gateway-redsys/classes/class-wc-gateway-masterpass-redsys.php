<?php

/**
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* Gateway class
*/
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
class WC_Gateway_MasterPass_Redsys extends WC_Payment_Gateway {
	var $notify_url;

	/**
	* Constructor for the gateway.
	*
	* @access public
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function __construct() {
		global $woocommerce;

		$this->id                 = 'masterpass';

		if ( ! empty( $this->get_option( 'logo' ) ) ) {
			$logo_url   = $this->get_option( 'logo' );
			$this->icon = apply_filters( 'woocommerce_masterpass_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_masterpass_icon', REDSYS_PLUGIN_URL . 'assets/images/masterpass.png' );
		}

		$this->has_fields         = false;
		$this->liveurl            = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl            = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->testmode           = $this->get_option( 'testmode' );
		$this->method_title       = __( 'MasterPass (by José Conti)', 'woocommerce-redsys' );
		$this->method_description = __( 'MasterPass works redirecting customers to MasterPass.', 'woocommerce-redsys' );
		$this->notify_url         = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->customer           = $this->get_option( 'customer' );
		$this->terminal           = $this->get_option( 'terminal' );
		$this->secretsha256       = $this->get_option( 'secretsha256' );
		$this->debug              = $this->get_option( 'debug' );
		$this->hashtype           = $this->get_option( 'hashtype' );
		$this->masterpasslanguage = $this->get_option( 'masterpasslanguage' );
		$this->woomasterpassurlko = $this->get_option( 'woomasterpassurlko' );
		$this->commercename       = $this->get_option( 'woomasterpasscomercename' );
		$this->buttoncheckout     = $this->get_option( 'buttoncheckout' );
		$this->butonbgcolor       = $this->get_option( 'butonbgcolor' );
		$this->butontextcolor     = $this->get_option( 'butontextcolor' );
		$this->descripredsys      = $this->get_option( 'descripredsys' );
		$this->log                = new WC_Logger();

		// Actions
		add_action( 'valid-' . $this->id . '-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Payment listener/API hook
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode_masterpass' ) );
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	* Check if this gateway is enabled and available in the user's country
	*
	* @access public
	* @return bool
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function is_valid_for_use() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'allowed-currencies.php';
		
		if ( ! in_array( get_woocommerce_currency(), WCRed()->allowed_currencies(), true ) ) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	* Admin Panel Options
	*
	* @since 1.0.0
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function admin_options() {
			?>
			<h3><?php _e( 'MasterPass', 'woocommerce-redsys' ); ?></h3>
			<p><?php _e( 'MasterPass works by sending the user to your bank TPV to enter their payment information.', 'woocommerce-redsys' ); ?></p>
			<?php if ( class_exists( 'SitePress' ) ) { ?>
				<div class="updated fade"><h4><?php _e( 'Attention! WPML detected.', 'woocommerce-redsys' ); ?></h4>
					<p><?php _e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woocommerce-redsys' ); ?></p>
				</div>
			<?php } ?>
			<?php if ( $this->is_valid_for_use() ) : ?>
				<table class="form-table">
					<?php
					// Generate the HTML For the settings form.
					$this->generate_settings_html();
					?>
				</table><!--/.form-table-->
			<?php else :
				include_once REDSYS_PLUGIN_DATA_PATH . 'allowed-currencies.php';
				$currencies = WCRed()->allowed_currencies();
				$formated_currencies = '';
			
				foreach ( $currencies as $currency ) {
					$formated_currencies .= $currency . ', ';
				}
				?>
				<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'woocommerce-redsys' ); ?></strong>: <?php esc_html_e( 'Servired/RedSys only support ', 'woocommerce-redsys' );
		echo $formated_currencies; ?></p></div>
				<?php
			endif;
		}
	/**
	* Initialise Gateway Settings Form Fields
	*
	* @access public
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function init_form_fields() {
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable MasterPass', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'title'              => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'MasterPass', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via MasterPass; you can pay with your credit card.', 'woocommerce-redsys' ),
			),
			'buttoncheckout'      => array(
				'title'       => __( 'Button Checkout Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add the button text at the checkout.', 'woocommerce-redsys' ),
			),
			'butonbgcolor'          => array(
				'title'       => __( 'Button Color Background', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button Color Background Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'butontextcolor'          => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button text color Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'commercename'       => array(
				'title'       => __( 'Commerce Name', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce Name', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'customer'           => array(
				'title'       => __( 'Commerce number (FUC)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'terminal'           => array(
				'title'       => __( 'Terminal number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'descripredsys'        => array(
				'title'       => __( 'Redsys description', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose what to show in Redsys as description.', 'woocommerce-redsys' ),
				'default'     => 'order',
				'options'     => array(
					'order' => __( 'Order ID', 'woocommerce-redsys' ),
					'id'    => __( 'List of products ID', 'woocommerce-redsys' ),
					'name'  => __( 'List of products name', 'woocommerce-redsys' ),
					'sku'   => __( 'List of products SKU', 'woocommerce-redsys' ),
				),
			),
			'secretsha256'       => array(
				'title'       => __( 'Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'masterpasslanguage' => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'woomasterpassurlko' => array(
				'title'       => __( 'Return URL (Redsys Error button)', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'When the user press the return button at Redsys Gateway (Ex: The user type an incorrect creadit cart), you can redirect the user to My Cart page canceling the order, or you can redirect the user to Checkput page without cancel the order.', 'woocommerce-redsys' ),
				'default'     => 'returncancel',
				'options'     => array(
					'returncancel'   => __( 'Cancel the order and return to My Cart page', 'woocommerce-redsys' ),
					'returnnocancel' => __( 'Don\'t cancel the order and return to Checkout page', 'woocommerce-redsys' ),
				),
			),
			'debug'              => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log MasterPass events, such as notifications requests, inside <code>WooCommerce > Status > Logs > masterpass-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
		$redsyslanguages = WCRed()->get_redsys_languages();
	
		foreach( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['masterpasslanguage']['options'][$redsyslanguage] = $valor;
		}
	}

	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_error_by_code( $error_code ) {
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', ' ' );
			$this->log->add( 'masterpass', '/****************************/' );
			$this->log->add( 'masterpass', '     DS Error Code: ' .  $error_code );
			$this->log->add( 'masterpass', '/****************************/' );
			$this->log->add( 'masterpass', ' ' );
		}
		
		$ds_errors = array();
		$ds_errors = WCRed()->get_ds_error();
		
		if ( ! empty( $error_code ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'masterpass', ' ' );
				$this->log->add( 'masterpass', '/****************************/' );
				$this->log->add( 'masterpass', '        DS Error Found        ' );
				$this->log->add( 'masterpass', '/****************************/' );
				$this->log->add( 'masterpass', ' ' );
			}
			if ( ! empty( $ds_errors ) ) {
				foreach ( $ds_errors as $ds_error => $value ) {
					if ( $ds_error === $error_code ) {
						return $value;
					} else {
						continue;
					}
				}
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'masterpass', ' ' );
				$this->log->add( 'masterpass', '/****************************/' );
				$this->log->add( 'masterpass', '       DS Error NOT Found    ' );
				$this->log->add( 'masterpass', '/****************************/' );
				$this->log->add( 'masterpass', ' ' );
			}
			return false;
		}
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_currencies() {
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', ' ' );
			$this->log->add( 'masterpass', '/******************************/' );
			$this->log->add( 'masterpass', '  Loading currencies array()    ' );
			$this->log->add( 'masterpass', '/******************************/' );
			$this->log->add( 'masterpass', ' ' );
		}
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'currencies.php';
		
		if ( 'yes' === $this->debug ) {
			if ( function_exists( 'redsys_return_currencies' ) ) {
				$this->log->add( 'masterpass', ' ' );
				$this->log->add( 'masterpass', '/******************************************/' );
				$this->log->add( 'masterpass', '  Function redsys_return_currencies exist   ' );
				$this->log->add( 'masterpass', '/******************************************/' );
				$this->log->add( 'masterpass', ' ' );
			} else {
				$this->log->add( 'masterpass', ' ' );
				$this->log->add( 'masterpass', '/**********************************************/' );
				$this->log->add( 'masterpass', '  Function redsys_return_currencies NOT exist   ' );
				$this->log->add( 'masterpass', '/**********************************************/' );
				$this->log->add( 'masterpass', ' ' );
			}
		}
		$currencies = array();
		$currencies = redsys_return_currencies();
		return $currencies;
	}

	/**
	* Get redsys Args for passing to the tpv
	*
	* @access public
	* @param mixed $order
	* @return array
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_masterpass_args( $order ) {
		global $woocommerce;

		$order_id         = $order->id;
		$currency_codes   = $this->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$transaction_type = '0';
		$secretsha256     = utf8_decode(  $this->secretsha256 );
		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
		} elseif ( $this->masterpasslanguage ) {
			$gatewaylanguage = $this->masterpasslanguage;
		} else {
			$gatewaylanguage = '001';
		}
	
		if ( $this->woomasterpassurlko ) {
			if ( $this->woomasterpassurlko == 'returncancel' ) {
				$$returnfrommasterpass = $order->get_cancel_order_url();
			} else {
				$$returnfrommasterpass = $woocommerce->cart->get_checkout_url();
			}
		} else {
			$$returnfrommasterpass = $order->get_cancel_order_url();
		}
		$DSMerchantTerminal = $this->terminal;

		// redsys Args
		$miObj = new RedsysAPI;
		$miObj->setParameter( "DS_MERCHANT_AMOUNT", $order_total_sign );
		$miObj->setParameter( "DS_MERCHANT_ORDER", $transaction_id2 );
		$miObj->setParameter( "DS_MERCHANT_MERCHANTCODE", $this->customer );
		$miObj->setParameter( "DS_MERCHANT_CURRENCY", $currency_codes[ get_woocommerce_currency() ] );
		$miObj->setParameter( "DS_MERCHANT_TRANSACTIONTYPE", $transaction_type );
		$miObj->setParameter( "DS_MERCHANT_TERMINAL", $DSMerchantTerminal );
		$miObj->setParameter( "DS_MERCHANT_MERCHANTURL", $this->notify_url );
		$miObj->setParameter( "DS_MERCHANT_URLOK", add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$miObj->setParameter( "DS_MERCHANT_URLKO", $returnfrommasterpass );
		$miObj->setParameter( "DS_CONSUMERLANGUAGE", $gatewaylanguage );
		$miObj->setParameter( "DS_MERCHANT_PRODUCTDESCRIPTION", __( 'Order' , 'woocommerce-redsys' ) . ' ' .  $order->get_order_number() );
		$miObj->setParameter( "DS_MERCHANT_MERCHANTNAME", $this->commercename );
		$miObj->setParameter( "Ds_Merchant_PayMethods", 'N' );
		
		$version         = 'HMAC_SHA256_V1';
		$request         = '';
		$params          = $miObj->createMerchantParameters();
		$signature       = $miObj->createMerchantSignature( $secretsha256 );
		$masterpass_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' == $this->debug )
		$this->log->add( 'masterpass', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r($masterpass_args, true) );
		$masterpass_args = apply_filters( 'woocommerce_masterpass_args', $masterpass_args );
		return $masterpass_args;
	}
	/**
	* Generate the redsys form
	*
	* @access public
	* @param mixed $order_id
	* @return string
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function generate_masterpass_form( $order_id ) {
		global $woocommerce;
		
		$usesecretsha256 = $this->secretsha256;
		$order           = new WC_Order( $order_id );
		$masterpass_adr  = $this->liveurl . '?';
		$masterpass_args = $this->get_masterpass_args( $order );
		$form_inputs     = '';
		foreach ( $masterpass_args as $key => $value ) {
			$form_inputs .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		wc_enqueue_js( '
			jQuery("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to MasterPass to make the payment.', 'woocommerce-redsys' ) . '",
				overlayCSS:
					{
					background: "#fff",
					opacity: 0.6
					},
					css: {
						padding:        20,
						textAlign:      "center",
						color:          "#555",
						border:         "3px solid #aaa",
						backgroundColor:"#fff",
						cursor:         "wait",
						lineHeight:     "32px"
				}
			});
			jQuery("#submit_masterpass_payment_form").click();
			' );
			return '<form action="'.esc_url( $masterpass_adr ).'" method="post" id="masterpass_payment_form" target="_top">
			' . $form_inputs . '<input type="submit" class="button-alt" id="submit_masterpass_payment_form" value="'.__( 'Pay with MasterPass account', 'woocommerce-redsys' ).'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ).'</a>
		</form>';
	}
	
	/**
	* Process the payment and return the result
	*
	* @access public
	* @param int $order_id
	* @return array
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function process_payment( $order_id ) {
		
		$order = new WC_Order( $order_id );
		return array(
			'result'    => 'success',
			'redirect'  => $order->get_checkout_payment_url( true ),
		);
	}
	
	/**
	* Output for the order received page.
	*
	* @access public
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function receipt_page( $order ) {
		echo '<p>'.__( 'Thank you for your order, please click the button below to pay with MasterPass.', 'woocommerce-redsys' ).'</p>';
		echo $this->generate_masterpass_form( $order );
	}
	
	/**
	* Check redsys IPN validity
	**/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function check_ipn_request_is_valid() {
		global $woocommerce;
		
		if ( 'yes' == $this->debug ) {
			$this->log->add( 'masterpass', 'HTTP Notification received: ' . print_r( $_POST, true ) );
		}
		$usesecretsha256 = $this->secretsha256;
		if ( $usesecretsha256 ) {
			$version     = $_POST["Ds_SignatureVersion"];
			$data        = $_POST["Ds_MerchantParameters"];
			$remote_sign = $_POST["Ds_Signature"];
			$miObj       = new RedsysAPI;
			$localsecret = $miObj->createMerchantSignatureNotif( $usesecretsha256, $data );
			
			if ( $localsecret == $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'masterpass', 'Received valid notification from MasterPass' );
					$this->log->add( 'masterpass', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'masterpass', 'Received INVALID notification from MasterPass' );
				}
				return false;
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'masterpass', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			}
			if ( $_POST['Ds_MerchantCode'] === $this->customer ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'masterpass', 'Received valid notification from MasterPass' );
				}
				return true;
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'masterpass', 'Received INVALID notification from MasterPass' );
				}
				return false;
			}
		}
	}
	
	/**
	* Check for Servired/RedSys HTTP Notification
	*
	* @access public
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function check_ipn_response() {
		
		@ob_clean();
		$_POST = stripslashes_deep( $_POST );
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid-' . $this->id . '-standard-ipn-request', $_POST );
		} else {
			wp_die( 'MasterPass Notification Request Failure' );
		}
	}
	
	/**
	* Successful Payment!
	*
	* @access public
	* @param array $posted
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function successful_request( $posted ) {
		global $woocommerce;
		
		$version            = $_POST["Ds_SignatureVersion"];
		$data               = $_POST["Ds_MerchantParameters"];
		$remote_sign        = $_POST["Ds_Signature"];
		$miObj              = new RedsysAPI;
		
		$decodedata         = $miObj->decodeMerchantParameters($data);
		$localsecret        = $miObj->createMerchantSignatureNotif($usesecretsha256,$data);
		$total              = $miObj->getParameter('Ds_Amount');
		$ordermi            = $miObj->getParameter('Ds_Order');
		$dscode             = $miObj->getParameter('Ds_MerchantCode');
		$currency_code      = $miObj->getParameter('Ds_Currency');
		$response           = $miObj->getParameter('Ds_Response');
		$id_trans           = $miObj->getParameter('Ds_AuthorisationCode');
		$dsdate             = $miObj->getParameter('Ds_Date');
		$dshour             = $miObj->getParameter('Ds_Hour');
		$dstermnal          = $miObj->getParameter('Ds_Terminal');
		$dsmerchandata      = $miObj->getParameter('Ds_MerchantData');
		$dssucurepayment    = $miObj->getParameter('Ds_SecurePayment');
		$dscardcountry      = $miObj->getParameter('Ds_Card_Country');
		$dsconsumercountry  = $miObj->getParameter('Ds_ConsumerLanguage');
		$dscargtype         = $miObj->getParameter('Ds_Card_Type');
		$order1             = $ordermi;
		$order2             = WCRed()->clean_order_number( $order1 );
		$order              = WCRed()->get_order( (int)$order2 );
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $order1 . ',  Ds_MerchantCode: '. $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
		}
		
		$response = intval( $response );
		if ( $response  <= 99 ) {
			//authorized
			$order_total_compare = number_format( $order->get_total() , 2 , '' , '' );
			if ( $order_total_compare != $total ) {
				//amount does not match
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'masterpass', 'Payment error: Amounts do not match (order: '.$order_total_compare.' - received: ' . $total . ')' );
				}
				
				// Put this order on-hold for manual checking
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %s - received: %s).', 'woocommerce-redsys' ), $order_total_compare , $total ) );
				exit;
			}
			$authorisation_code = $id_trans;
			if ( ! empty( $order1 ) ) {
				update_post_meta( $order->id, '_payment_order_number_masterpass', $order1 );
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->id, '_payment_date_redsys',   $dsdate );
			}
			if ( ! empty( $dshour ) ) {
				update_post_meta( $order->id, '_payment_hour_redsys',   $dshour );
			}
			if ( ! empty( $id_trans ) ) {
				update_post_meta( $order->id, '_authorisation_code_redsys', $authorisation_code );
			}
			if ( ! empty( $dscardcountry ) ) {
				update_post_meta( $order->id, '_card_country_masterpass',   $dscardcountry );
			}
			if ( ! empty( $dscargtype ) ) {
				update_post_meta( $order->id, '_card_type_masterpass',   $dscargtype == 'C' ? 'Credit' : 'Debit' );
			}
			
			// Payment completed
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorisation code: ',  'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'masterpass', 'Payment complete.' );
			}
		} else {
			$ds_responses = WCRed()->get_ds_response();
			$ds_errors    = WCRed()->get_ds_error();
			
			if ( ! empty( $ds_responses ) ) {
				foreach ( $ds_responses as $ds_response => $value ) {
					if ( $ds_response === $response ) {
						$ds_response_value = $value;
						$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_response_value );
						update_post_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_response_value );
					}
				}
			}

			if ( ! empty( $ds_errors ) ) {
				foreach ( $ds_errors as $ds_error => $value ) {
					if ( $ds_error === $dserrorcode ) {
						$ds_error_value = $value;
						$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_error_value );
						update_post_meta( $order_id, '_redsys_error_payment_ds_error_value', $ds_error_value );
					}
				}
			}
			
			//Tarjeta caducada
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'masterpass', 'Pedido cancelado por MasterPass' );
			}
			
			if ( 'yes' === $this->debug ) {
				if ( ! empty( $ds_responses ) ) {
					$this->log->add( 'masterpass', 'Error: ' . $ds_response_value );
				}
				if ( ! empty( $ds_errors ) ) {
					$this->log->add( 'masterpass', 'Error: ' . $ds_error_value );
				}
			}

			//Order cancelled
			$order->update_status( 'cancelled', __( 'Cancelled by MasterPass', 'woocommerce-redsys' ) );
			$order->add_order_note( __('Order canceled by MasterPass', 'woocommerce-redsys') );
			WC()->cart->empty_cart();
		}
	}
	
	/**
	* get_masterpass_order function.
	*
	* @access public
	* @param mixed $posted
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_masterpass_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function warning_checkout_test_mode_masterpass() {
		if ( 'yes' === $this->testmode  && WCRed()->is_gateway_enabled( $this->id ) ) {
			echo '<div class="checkout-message" style="
			background-color: rgb(214, 69, 65);
			padding: 1em 1.618em;
			margin-bottom: 2.617924em;
			margin-left: 0;
			border-radius: 2px;
			color: #fff;
			clear: both;
			border-left: 0.6180469716em solid rgb(150, 40, 27);
			">';
			echo __( 'Warning: WooCommerce Redsys Gateway MasterPass is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
}
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
function woocommerce_add_gateway_masterpass_gateway( $methods ) {
	$methods[] = 'WC_Gateway_MasterPass_Redsys';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_masterpass_gateway' );
