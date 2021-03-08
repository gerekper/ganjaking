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
class WC_Gateway_InSite_Redsys extends WC_Payment_Gateway {
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

		$this->id = 'insite';

		if ( ! empty( $this->get_option( 'logo' ) ) ) {
			$logo_url   = $this->get_option( 'logo' );
			$this->icon = apply_filters( 'woocommerce_insite_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_insite_icon', REDSYS_PLUGIN_URL_P . 'assets/images/redsys.png' );
		}

		$this->has_fields         = true;
		$this->liveurl            = 'https://sis.redsys.es/sis/services/SerClsWSEntrada';
		$this->testurl            = 'https://sis-i.redsys.es:25443/sis/services/SerClsWSEntrada"';
		$this->liveurlws          = 'https://sis.redsys.es/sis/rest/trataPeticionREST';
		$this->testurlws          = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
		$this->testsha256         = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode           = $this->get_option( 'testmode' );
		$this->method_title       = __( 'Redsys in Checkout (by José Conti)', 'woocommerce-redsys' );
		$this->method_description = __( 'Redsys in Checkout use InSite for add a Credit Card Form in the checkout. InSite is needed for use this payment form.', 'woocommerce-redsys' );
		$this->notify_url         = add_query_arg( 'wc-api', 'WC_Gateway_insiteredsys', home_url( '/' ) );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->customer           = $this->get_option( 'customer' );
		$this->terminal           = $this->get_option( 'terminal' );
		$this->secretsha256       = $this->get_option( 'secretsha256' );
		$this->secure3ds          = $this->get_option( 'secure3ds' );
		$this->debug              = $this->get_option( 'debug' );
		$this->hashtype           = $this->get_option( 'hashtype' );
		$this->insitelanguage     = $this->get_option( 'insitelanguage' );
		$this->wooinsiteurlko     = $this->get_option( 'wooinsiteurlko' );
		$this->commercename       = $this->get_option( 'wooinsitecomercename' );
		$this->insitetype         = 'unifiedintegration'; // Temporal mientras no activo lo anterior.
		$this->traactive          = $this->get_option( 'traactive' );
		$this->traamount          = $this->get_option( 'traamount' );
		$this->colorbutton        = $this->get_option( 'colorbutton' );
		$this->colorfieldtext     = $this->get_option( 'colorfieldtext' );
		$this->colortextbutton    = $this->get_option( 'colortextbutton' );
		$this->textcolor          = $this->get_option( 'textcolor' );
		$this->buttontext         = $this->get_option( 'buttontext' );
		$this->butonbgcolor       = $this->get_option( 'butonbgcolor' );
		$this->butontextcolor     = $this->get_option( 'butontextcolor' );
		$this->cvvboxcolor        = $this->get_option( 'cvvboxcolor' );
		$this->descripredsys      = $this->get_option( 'descripredsys' );
		
		$this->customtestsha256   = $this->get_option( 'customtestsha256' );
		
		$this->testforuser        = $this->get_option( 'testforuser' );
		$this->testforuserid      = $this->get_option( 'testforuserid' );
		$this->log                = new WC_Logger();
		$this->supports           = array(
			'products',
		);
		// Actions
		add_action( 'valid-insite-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_insite', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Payment listener/API hook
		add_action( 'woocommerce_api_wc_gateway_insiteredsys', array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_field_update_order_meta' ) );
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
		
		add_action( 'wp_head', array( $this, 'add_insite_redsys2' ) );
		add_action( 'wp_footer', array( $this, 'add_insite_on_loadform' ), 900 );
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
		
		include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';
		
		if ( ! in_array( get_woocommerce_currency(), redsys_return_allowed_currencies(), true ) ) {
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
			<h3><?php _e( 'InSite', 'woocommerce-redsys' ); ?></h3>
			<p><?php _e( 'InSite works by sending the user to your bank TPV to enter their payment information.', 'woocommerce-redsys' ); ?></p>
			<?php if ( class_exists( 'SitePress' ) ) { ?>
				<div class="updated fade"><h4><?php _e( 'Attention! WPML detected.', 'woocommerce-redsys' ); ?></h4>
					<p><?php _e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woocommerce-redsys' ); ?></p>
				</div>
			<?php }
				if ( class_exists( 'SOAPClient' ) ) {
					try {
						  $soapClient = new SoapClient( 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl' );
						}
						catch( Exception $e ) {
						  $exceptionMessage = $e->getMessage();
						}
						if ( $exceptionMessage ) { ?>
							<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
								<p><?php _e( 'InSite will not work in Test Mode, Normally this happens because your hosting is blocking the Port 25443 for SOAP, please talk to your hosting and tell them to open port 25443 for SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl If the hosting does not open the port, the plugin will not work correctly in test mode..', 'woocommerce-redsys' ); ?></p>
							</div>
						<?PHP }
					try {
						  $soapClient = new SoapClient( 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl' );
						}
						catch( Exception $e ) {
						  $exceptionMessage = $e->getMessage();
						}
						if ( $exceptionMessage ) { ?>
							<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
								<p><?php _e( 'InSite will not work in Real Mode, Normally this happens because your hosting is blocking SOAP, please talk to your hosting and tell them to open port 443 for SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl If the hosting does not open the port, the plugin will not work correctly in real mode.', 'woocommerce-redsys' ); ?></p>
							</div>
						<?PHP }
				} else { ?>
					<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
					<?php _e( 'SOAP is needed for Pay with InSite. Ask to your hosting to enable it. Without active SOAP on the server, the functionality of the plugin is very limited.', 'woocommerce-redsys' ); ?>
					</div>
				<?php }
			?>
			<?php if ( $this->is_valid_for_use() ) : ?>
				<table class="form-table">
					<?php
					// Generate the HTML For the settings form.
					$this->generate_settings_html();
					?>
				</table><!--/.form-table-->
			<?php else :
				include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';
				$currencies = redsys_return_allowed_currencies();
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
		
		$options    = array();
		$selections = (array)$this->get_option( 'testforuserid' );

		if ( count( $selections ) !== 0 ) {
			foreach ( $selections as $user_id ) {
				if ( ! empty( $user_id ) ) {
					$user_data  = get_userdata( $user_id );
					$user_email = $user_data->user_email;
					if ( ! empty( esc_html( $user_email ) ) ) {
						$options[ esc_html( $user_id ) ] = esc_html( $user_email );
					}
				}
			}
		}
		
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable InSite', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'title'              => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'InSite', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via InSite; you can pay with your credit card.', 'woocommerce-redsys' ),
			),
			'traactive'              => array(
				'title'   => __( 'Enable TRA', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable TRA for Pay with 1click. WARNING, your bank has to enable it before you use it.', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'traamount'             => array(
				'title'       => __( 'Limit import for TRA', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'TRA will be sent when the amount is inferior to what you specify here. Write the amount without the currency sign, i.e. if it is 250€, ONLY write 250', 'woocommerce-redsys' ),
				'desc_tip'    => true,
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
			'customtestsha256'     => array(
				'title'       => __( 'TEST MODE: Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for test mode.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'insitelanguage' => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'wooinsiteurlko' => array(
				'title'       => __( 'Return URL (Redsys Error button)', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'When the user press the return button at Redsys Gateway (Ex: The user type an incorrect credit cart), you can redirect the user to My Cart page canceling the order, or you can redirect the user to Checkput page without cancel the order.', 'woocommerce-redsys' ),
				'default'     => 'returncancel',
				'options'     => array(
					'returncancel'   => __( 'Cancel the order and return to My Cart page', 'woocommerce-redsys' ),
					'returnnocancel' => __( 'Don\'t cancel the order and return to Checkout page', 'woocommerce-redsys' ),
				),
			),
			'buttontext'           => array(
				'title'       => __( 'Button Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'default'     => 'Realizar pago',
				'description' => __( 'Add the Button Text.', 'woocommerce-redsys' ),
			),
			'textcolor'          => array(
				'title'       => __( 'General Color Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This is the General text color added by InSite. Default #2e3131', 'woocommerce-redsys' ),
				'default'     => '#2e3131',
				'class'       => 'colorpick',
			),
			'colorbutton'          => array(
				'title'       => __( 'Color Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button color. Default #f39c12', 'woocommerce-redsys' ),
				'default'     => '#f39c12',
				'class'       => 'colorpick',
			),
			'colortextbutton'          => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button text color. Default #ffffff', 'woocommerce-redsys' ),
				'default'     => '#ffffff',
				'class'       => 'colorpick',
			),
			'colorfieldtext'      => array(
				'title'       => __( 'Color Fields Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This the text color of the field text. Default #95a5a6', 'woocommerce-redsys' ),
				'default'     => '#95a5a6',
				'class'       => 'colorpick',
			),
			'cvvboxcolor'      => array(
				'title'       => __( 'CVV box background color', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This the background color of CVV field. Default #d5d5d5', 'woocommerce-redsys' ),
				'default'     => '#d5d5d5',
				'class'       => 'colorpick',
			),
			'testmode'             => array(
				'title'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woocommerce-redsys' ) ),
			),
			'testforuser'           => array(
				'title'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'default'     => '',
				'description' => sprintf( __( 'The user selected below will use the terminal in test mode. Other users will continue to use live mode unless you have the "Running in test mode" option checked.', 'woocommerce-redsys' ) ),
			),
			'testforuserid'         => array(
				'title'       => __( 'Users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Users running in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-allowed-users-settings',
				'id'          => 'woocommerce_redsys_testforuserid',
				'options'     => $options,
				'default'     => '',
				'description' => sprintf( __( 'Select users running in test mode', 'woocommerce-redsys' ) ),
			),
			'debug'              => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log InSite events, such as notifications requests, inside <code>WooCommerce > Status > Logs > insite-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
		$redsyslanguages = WCRed()->get_redsys_languages();

		foreach( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['insitelanguage']['options'][$redsyslanguage] = $valor;
		}
	}

	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function add_insite_redsys2() {
		
		if ( is_wc_endpoint_url( 'order-pay' ) || is_checkout() ) {
			
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = '0';
			}
			echo '<!-- Comienza JS para InSite añadido por WooCommerce Redsys Gateway de WooCommerce.com -->';
			echo '<script type="text/javascript">var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";</script>';
			echo $this->get_js_header( $user_id );
			echo '<!-- Finaliza JS para InSite añadido por WooCommerce Redsys Gateway de WooCommerce.com -->';
		}
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function add_insite_on_loadform() {
		
		if ( is_wc_endpoint_url( 'order-pay' ) || is_checkout() ) {
			// echo '<script>window.onload = loadRedsysForm();</script>';	
		}
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_js_header( $user_id = false ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          URL Test        ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			$code = '<script src="https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js"></script>';
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Test WD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$code = '<script src="https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js"></script>';
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Live WD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$code = '<script src="https://sis.redsys.es/sis/NC/redsysV2.js"></script>';
			}
		}
		return $code;
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_redsys_url_gateway( $user_id = false  ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          URL Test        ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			$url = $this->testurlws;
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Test RD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$url = $this->testurlws;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Live RD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$url = $this->liveurlws;
			}
		}
		return $url;
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_user_test_mode( $userid = false ) {

		if ( ! $userid ) {
			return false;
		}
		$usertest_active = $this->testforuser;
		$selections      = (array)$this->get_option( 'testforuserid' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '     Checking user test       ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' === $usertest_active ) {

			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '   Checking user ' . $userid    );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '  User in forach ' . $user_id   );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					if ( (string)$user_id === (string)$userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', '   Checking user test TRUE    ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '/********************************************/' );
							$this->log->add( 'insite', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'insite', '/********************************************/' );
							$this->log->add( 'insite', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '  Checking user test continue ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '  Checking user test FALSE    ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '  Checking user test FALSE    ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '     User test Disabled.      ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			return false;
		}
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_redsys_sha256( $user_id = false ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '         SHA256 Test.         ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      SHA256 Test Custom.     ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$sha256 = $customtestsha256;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '     SHA256 Test Standard.    ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} elseif ( '0' === $user_id || ! $user_id ) {
			if ( 'yes' === $this->testmode ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '         SHA256 Test.         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '      SHA256 Test Custom.     ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					$sha256 = $customtestsha256;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '     SHA256 Test Standard.    ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      USER SHA256 Test.       ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '      SHA256 Test Custom.     ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					$sha256 = $customtestsha256;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '     SHA256 Test Standard.    ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function payment_fields() {
		global $woocommerce;

		echo '<script type="text/javascript">
					if (typeof(receive) !== "undefined"){
						window.removeEventListener("message", receive );
					}
				</script>';
		$minheigh       = '';
		$margintop      = '';
		$colorbutton    = '';
		$colorfieldtext = '';
		$terminal       = $this->terminal;
		$fuc            = $this->customer;
		$redirectok     = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order2 ) );
		$minheigh       = $this->minheigh;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = '0';
		}
		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );
			//print_r( $_POST['post_data'] );
		}
		if ( ! WC()->cart->prices_include_tax ) {
		    $order_total = WC()->cart->cart_contents_total;
		} else {
		    $order_total = WC()->cart->cart_contents_total + WC()->cart->tax_total;
		}
		$order_total = WC()->cart->total;

		$billing_first_name = $post_data['billing_first_name'];
		$billing_last_name  = $post_data['billing_last_name'];
		
		if ( empty( $billing_first_name ) ) {
			echo '<p> ' . $this->description . '</p><br />';
			echo '<p> ' . __( 'Fill the billing fields for credit card fields.', 'woocommerce-redsys' ) . '</p>';
			return;
		}
		
		$colorbutton = $this->colorbutton;
		
		if ( ! empty( $colorbutton ) ) {
			$colorbutton = $colorbutton;
		} else {
			$colorbutton = '#f39c12';
		}

		$colorfieldtext = $this->colorfieldtext;

		if ( ! empty( $colorfieldtext ) ) {
			$colorfieldtext = $colorfieldtext;
		} else {
			$colorfieldtext = '#43454b';
		}
		
		$colortextbutton = $this->colortextbutton;
		
		if ( ! empty( $colortextbutton ) ) {
			$colortextbutton = $colortextbutton;
		} else {
			$colortextbutton = '#ffffff';
		}
		
		$textcolor = $this->textcolor;
		
		if ( ! empty( $textcolor ) ) {
			$textcolor = $textcolor;
		} else {
			$textcolor = '#2e3131';
		}
		
		$cvvbox = $this->cvvboxcolor;
		if ( ! empty( $cvvbox ) ) {
			$cvvbox = $cvvbox;
		} else {
			$cvvbox = '#d5d5d5';
		}
		
		$buttontext = $this->buttontext;
		
		if ( ! empty( $buttontext ) ) {
			$buttontext = $buttontext;
		} else {
			$buttontext = __( 'Pay Now', 'woocommerce-redsys' );
		}
		
		if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			$http_accept = $_SERVER['HTTP_ACCEPT'];
		} else {
			$http_accept = 'false';
		}

		$nonce = wp_create_nonce( 'redsys_insite_nonce' );
		update_post_meta( $order, '_payment_order_number_redsys', $orderId );
		$orderId  = WCRed()->create_checkout_insite_number();
			
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/*******************************************/' );
			$this->log->add( 'insite', '  Cargamos el formulario InSite sencillo    ' );
			$this->log->add( 'insite', '/*******************************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$orderId: ' . $orderId );
			$this->log->add( 'insite', '$order_total: ' . $order_total );
			$this->log->add( 'insite', '$billing_first_name: ' . $billing_first_name );
			$this->log->add( 'insite', '$billing_last_name: ' . $billing_last_name );
			$this->log->add( 'insite', '$http_accept: ' . $http_accept );
			$this->log->add( 'insite', ' ' );
		}
		echo '
			<style>
				.payment_method_insite .input-wrap {
					height: 60px !important;
					margin-left: -8px;
					margin-bottom: 15px;
				}

				.payment_method_insite .input-wrap#card-number {
					margin-right: -8px;
				}

				.payment_method_insite .date-wrap {
					display: flex;
					justify-content: space-between;
				}

				.payment_method_insite .date-wrap > div {
					width: 30%;
					display: flex;
					flex-direction: column;
					justify-content: flex-end;
				}

				.payment_method_insite .date-wrap > div label {
					line-height: 1.2em;
				}

				.payment_method_insite .date-wrap .cvv-wrap {
					background-color: ' . $cvvbox . ';
					padding-top: 2px;
					width: 40%;
				}

				#payment .payment_methods li .payment_box fieldset .cvv-wrap label {
					width: 85%;
					margin-left: auto;
				}

				#payment .payment_methods li .payment_box fieldset .cvv-wrap #cvv {
					width: 85% !important;
					margin-left: auto;
					margin-right: auto;
				}

				#payment .payment_methods li .payment_box {
					padding-top: 5px;
				}

				#payment .payment_methods li .payment_box fieldset.card-saved {
					padding-top: 0;
					padding-bottom: 15px;
					font-size: .875em;
					line-height: 1.4em;
				}

				#payment .payment_methods li .payment_box fieldset input[type="radio"] + label,
				#payment .payment_methods li .payment_box fieldset input[type="checkbox"] + label {
					margin-left: 6px;
				}

				.payment_box fieldset.card-saved div {
					padding-bottom: 5px;
				}

				.token-wrap {
					margin: 15px 0 15px;
				}

				#redsys-submit {
					margin: 0 -8px;
					max-height: 100px;
				}
			</style>

			<div class="payment_method_insite">
				<p>' . $this->description . '</p>
				<fieldset class="new-card-data">
					<div>
						<label class="cardinfo-label" for="card-number">' . __( 'Credit Card Number', 'woocommerce-redsys' ) . '</label>
						<div class="input-wrap" id="card-number"></div>
					</div>
					<div class="exp-date">' . __( 'Expiration Date', 'woocommerce-redsys' ) . '</div>
					<div class="date-wrap">
						<div>
							<label class="cardinfo-label" for="expiration-date">' . __( 'Month (MM)', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="expiration-month"></div>
						</div>
						<div>
							<label class="cardinfo-label" for="expiration-date2">' . __( 'Year (YY)', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="expiration-year"></div>
						</div>
						<div class="cvv-wrap">
							<label class="cardinfo-label" for="cvv">' . __( 'CVV', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="cvv"></div>
						</div>
					</div>
				</fieldset>
				<div>
					<div class="input-wrapper" id="redsys-submit"></div>
				</div>

				<input type="hidden" id="token" ></input>
				<input type="hidden" id="errorCode" ></input>
				<input type="hidden" name="_temp_redsys_order_number" id="_temp_redsys_order_number" value="' . $orderId . '"></input>
				<div class="clear"></div>
			</div>
			<script type="text/javascript">
				console.log("Start" );
				var c = new Date();
				// Listener
				//const orderId = Math.round(Math.random() * 10000).toString();
				var receive = function receiveMessage(event) {
					storeIdOper(event,"token", "errorCode");
					console.log("pre check token" );
					if ( token.value ) {
						console.log(event);
						console.log("Order Id token:", token.value );
						console.log("Error:", errorCode.value);
						console.log("Ajax URL:", ajaxurl);
						if ( token.value ) {
							jQuery(document).ready( function() {
								console.log("llega Ajax");
								if ( token.value ) {
									console.log("se defino token.value:", token.value );
									console.log("El numero de pedido es:", ' . $orderId . ' );
									jQuery.ajax({
										type : "post",
										url : ajaxurl,
										data : {
											"action": "check_token_insite_from_action_checkout",
											"token" : token.value,
											"order_id" : "' . $orderId . '",
											"order_total" : "' . $order_total . '",
											"billing_first_name" : "' . $billing_first_name . '",
											"billing_last_name" : "' . $billing_last_name . '",
											"user_id" : "' . $user_id . '",
											"redsysnonce" : "' . $nonce . '",
											"userAgent"   : navigator.userAgent,
											"language"    : navigator.language,
											"height"      : screen.height,
											"width"       : screen.width,
											"colorDepth"  : screen.colorDepth,
											"Timezone"    : c.getTimezoneOffset(),
											"http_accept" : "' . $http_accept . '"
										},
										success: function(response) {
											console.log("response:", response, "type of:", typeof response)
											if(response=="success") {
												document.getElementById("place_order").click();
											} else if ( response=="ChallengeRequest" ) {
												document.getElementById("place_order").click();
											} else {
												location.reload();
												if ( "La firma no coincide" == response ) {
													alert("' . __( 'Error: Please make sure that everything is filled in and enter your card details again.', 'woocommerce-redsys' ) . '");
												} else {
													alert("Error: " + response );
												}
											}
										}
									})
								}
							});
						}	
					}
					console.log("No hay token" );
				};
				
				window.addEventListener("message", receive );

				getCardInput(
					"card-number",
                    "box-sizing: border-box; width: 100%; font-size: 0.875em; padding: 10px; color: ' . $colorfieldtext . '; border: 2px solid #ddd; "
				);
				getExpirationMonthInput(
					"expiration-month",
					"box-sizing: border-box; width: 100%; font-size: 0.875em; padding: 10px; color: ' . $colorfieldtext . '; text-align: center; border: 2px solid #ddd;"
				);
				getExpirationYearInput(
					"expiration-year",
					"box-sizing: border-box; width: 100%; font-size: 0.875em; padding: 10px; color: ' . $colorfieldtext . '; text-align: center; border: 2px solid #ddd;"
				);
				getCVVInput(
					"cvv",
					"box-sizing: border-box; width: 100%; font-size: 0.875em; padding: 10px; color: ' . $colorfieldtext . '; text-align: center; border: 2px solid #ddd;"
				);
				getPayButton(
					"redsys-submit",
					"min-height: 85px; font-size: 1.41575em; width: 100%; vertical-align: middle; background-color:' . $colorbutton . '; color:' . $colortextbutton . '; border-width: 0px",
					"' .  $buttontext . '",
					"' . $fuc . '",
					"' . $terminal . '",
					"' . $orderId . '"
				);
			</script>
			<style>
				#redsys-hosted-pay-button {max-height: 85px; }
			</style>';
		;
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
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', __( 'Next Step, Call', 'woocommerce-redsys' ) );
		}
		$ordermi      = get_post_meta( $order_id, '_temp_redsys_order_number', true );
		$version      = get_transient( $ordermi . '_insite_version' );
		$params       = get_transient( $ordermi . '_insite_params' );
		$signature    = get_transient( $ordermi . '_insite_signature' );
		$user_id      = get_transient( $ordermi . '_insite_user_id' );
		$secretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr   = $this->get_redsys_url_gateway( $user_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$ordermi: ' . $ordermi );
			$this->log->add( 'insite', 'Ds_SignatureVersion: ' . $version );
			$this->log->add( 'insite', 'Ds_MerchantParameters: ' . $params );
			$this->log->add( 'insite', 'Ds_Signature: ' . $signature );
		}
		$response  = wp_remote_post(
			$redsys_adr,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce',
				'body'        => array(
					'Ds_SignatureVersion'   => $version,
					'Ds_MerchantParameters' => $params,
					'Ds_Signature'          => $signature,
				),
			)
		);
		$miObj         = new RedsysAPI();
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body );
		
		if ( empty( $response ) ) {
			wc_add_notice( 'Try again', 'error' );
			return;
		}
		
		if ( $result->errorCode ) {
			$response = WCRed()->get_response_by_code( $result->errorCode );
			$error    = WCRed()->get_error_by_code( $result->errorCode );
			if ( ! empty( $response ) ) {
				wc_add_notice( $response, 'error' );
			}
			if ( ! empty( $error ) ) {
				wc_add_notice( $error, 'error' );
			} 
			return;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$response_body: ' . $response_body );
			$this->log->add( 'insite', 'Ds_SignatureVersion: ' . $result->Ds_SignatureVersion );
			$this->log->add( 'insite', 'Ds_MerchantParameters: ' . $result->Ds_MerchantParameters );
			$this->log->add( 'insite', 'Ds_Signature: ' . $result->Ds_Signature );
		}

		$decodec            = $miObj->decodeMerchantParameters( $result->Ds_MerchantParameters );
		$response           = $miObj->getParameter( 'Ds_Response' );
		$decodec_array      = json_decode( $decodec );
		$signatureCalculada = $miObj->createMerchantSignatureNotif( $secretsha256, $result->Ds_MerchantParameters );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$response: ' . $response );
			$this->log->add( 'insite', '$decodec_array: ' . print_r( $decodec_array, true ) );
			$this->log->add( 'insite', '$signatureCalculada: ' . $signatureCalculada );
			// $this->log->add( 'insite', '$codigoRespuesta: ' . $codigoRespuesta );
			$this->log->add( 'insite', 'Ds_Signature: ' . $result->Ds_Signature );
			$this->log->add( 'insite', 'print_r: ' . print_r( $result, true ) );
		}
		if ( isset( $decodec_array->Ds_AuthorisationCode ) &&  ! empty( $decodec_array->Ds_AuthorisationCode ) ) {
			$autorization        = $decodec_array->Ds_AuthorisationCode;
			$total               = $decodec_array->Ds_Amount;
			$ordermi             = $decodec_array->Ds_Order;
			$dscode              = $decodec_array->Ds_MerchantCode;
			$currency_code       = $decodec_array->Ds_Currency;
			$response            = $decodec_array->Ds_Response;
			$id_trans            = $decodec_array->Ds_AuthorisationCode;
			$dstermnal           = $decodec_array->Ds_Terminal;
			$dsmerchandata       = $decodec_array->Ds_MerchantData;
			$dscardcountry       = $decodec_array->Ds_Card_Country;
			$descardbrand        = $decodec_array->Ds_Card_Brand;
			$desprocesspaymethod = $decodec_array->Ds_ProcessedPayMethod;
			$dsdate              = date( 'd/m/Y', current_time( 'timestamp', 0 ) );
			$dshour              = date( 'H:i', current_time( 'timestamp', 0 ) );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$autorization: ' . $autorization );
				$this->log->add( 'insite', '$total: ' . $total );
				$this->log->add( 'insite', '$ordermi: ' . $ordermi );
				$this->log->add( 'insite', '$dscode: ' . $dscode );
				$this->log->add( 'insite', '$currency_code: ' . $currency_code );
				$this->log->add( 'insite', '$response: ' . $response );
				$this->log->add( 'insite', '$id_trans: ' . $id_trans );
				$this->log->add( 'insite', '$dstermnal: ' . $dstermnal );
				$this->log->add( 'insite', '$dsmerchandata: ' . $dsmerchandata );
				$this->log->add( 'insite', '$dscardcountry: ' . $dscardcountry );
				$this->log->add( 'insite', '$descardbrand: ' . $descardbrand );
				$this->log->add( 'insite', '$desprocesspaymethod: ' . $desprocesspaymethod );
				$this->log->add( 'insite', '$dsdate: ' . $dsdate );
				$this->log->add( 'insite', '$dshour: ' . $dshour );
			}
			
			$authorisation_code = $id_trans;

			if ( ! empty( $ordermi ) ) {
				update_post_meta( $order_id, '_payment_order_number_redsys', $ordermi );
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order_id, '_payment_date_redsys',   $dsdate );
			}
			if ( ! empty( $dshour ) ) {
				update_post_meta( $order_id, '_payment_hour_redsys',   $dshour );
			}
			if ( ! empty( $id_trans ) ) {
				update_post_meta( $order_id, '_authorisation_code_redsys', $authorisation_code );
			}
			if ( ! empty( $dscardcountry ) ) {
				update_post_meta( $order_id, '_card_country_insite',   $dscardcountry );
			}
			if ( ! empty( $sha256 ) ) {
				update_post_meta( $order_id, '_order_sha256_insite',   $sha256 );
			}
			$order = WCRed()->get_order( $order_id );
			// Payment completed
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorization code: ',  'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Payment complete.' );
			}
			$order = new WC_Order( $order_id );
			return array(
				'result'    => 'success',
				'redirect'  => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
			);
		}
		
		if ( isset( $decodec_array->Ds_EMV3DS->acsURL ) ) { // Need verification
			$response = (int)$decodec_array->Ds_Response;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'La respuesta es $response: ' . $response );
			}
			$threedsinfo = $decodec_array->Ds_EMV3DS->threeDSInfo;
			if ( ! empty( $threedsinfo ) && 'ChallengeRequest' === $threedsinfo ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '$redsys_insite->secure3ds: ' . $redsys_insite->secure3ds );
					$this->log->add( 'insite', 'La respuesta es $response: ' . $response );
				}
				$acsurl = $decodec_array->Ds_EMV3DS->acsURL;
				$pareq  = $decodec_array->Ds_EMV3DS->PAReq;
				$md     = $decodec_array->Ds_EMV3DS->MD;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '$acsurl: ' . $acsurl );
					$this->log->add( 'insite', '$pareq: ' . $pareq );
					$this->log->add( 'insite', '$md: ' . $md );
				}
				set_transient( $order_id . '_insite_acsurl', $acsurl, 36000 );
				set_transient( $order_id . '_insite_pareq', $pareq, 36000 );
				set_transient( $order_id . '_insite_md', $md, 36000 );
				set_transient( $order_id . '_do_redsys_challenge', 'yes', 36000 );
				echo 'ChallengeRequest';
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'ChallengeRequest: TRUE' );
				}
				$order = new WC_Order( $order_id );
				return array(
					'result'    => 'success',
					'redirect'  => $order->get_checkout_payment_url( true ),
				);
			}
		}

		$order = new WC_Order( $order_id );
		return array(
			'result'    => 'success',
			'redirect'  => $order->get_checkout_payment_url( true ),
		);
	}
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	
	public static function check_token_insite_from_action_checkout() {

		$redsys_insite = new WC_Gateway_InSite_Redsys();

		if ( 'yes' === $redsys_insite->debug ) {
			$redsys_insite->log->add( 'insite', ' ' );
			$redsys_insite->log->add( 'insite', '/******************************/' );
			$redsys_insite->log->add( 'insite', '  Llega a la función de InSite  ' );
			$redsys_insite->log->add( 'insite', '/******************************/' );
			$redsys_insite->log->add( 'insite', ' ' );
			$redsys_insite->log->add( 'insite', 'El token que hay que enviar a Redsys es:' . $_POST['token'] );
			$redsys_insite->log->add( 'insite', 'El Order ID que hay que enviar a Redsys es:' . $_POST['order_id'] );
			$redsys_insite->log->add( 'insite', 'El userAgent que hay que enviar a Redsys es:' . $_POST['userAgent'] );
			$redsys_insite->log->add( 'insite', 'El language que hay que enviar a Redsys es:' . $_POST['language'] );
			$redsys_insite->log->add( 'insite', 'El height que hay que enviar a Redsys es:' . $_POST['height'] );
			$redsys_insite->log->add( 'insite', 'El width que hay que enviar a Redsys es:' . $_POST['width'] );
			$redsys_insite->log->add( 'insite', 'El colorDepth que hay que enviar a Redsys es:' . $_POST['colorDepth'] );
			$redsys_insite->log->add( 'insite', 'El Timezone que hay que enviar a Redsys es:' . $_POST['Timezone'] );
			$redsys_insite->log->add( 'insite', 'Los HTTP Accept headers son:' . $_POST['http_accept'] );
		}

		$currency_codes   = WCRed()->get_currencies();
		$customer         = $redsys_insite->customer;
		$terminal         = $redsys_insite->terminal;
		$currency         = $currency_codes[ get_woocommerce_currency() ];
		$transaction_type = '0';
		$final_notify_url = $redsys_insite->notify_url;
		$redsys_token     = $_POST['token'];
		$order_id         = $_POST['order_id'];
		$amount           = $_POST['order_total'];
		$merchan_name     = $_POST['billing_first_name'];
		$merchant_lastnme = $_POST['billing_last_name'];
		$user_id          = $_POST['user_id'];
		$usr_agent        = $_POST['userAgent'];
		$http_accept      = $_POST['http_accept'];
		$redsys_amount    = WCRed()->redsys_amount_format( $amount );
		$secretsha256     = $redsys_insite->get_redsys_sha256( $user_id );
		$redsys_adr       = $redsys_insite->get_redsys_url_gateway( $user_id );
		$merchant_module  = 'WooCommerce_Redsys_Gateway_' . REDSYS_VERSION . '_WooCommerce.com';
		$emv3ds           = '{"threeDSInfo":"AuthenticationData",
							"protocolVersion":"1.0.2",
							"browserAcceptHeader":"' . $http_accept . '",
							"browserUserAgent":"' . $usr_agent . '"}';

		if ( 'yes' === $redsys_insite->debug ) {
			$redsys_insite->log->add( 'insite', ' ' );
			$redsys_insite->log->add( 'insite', '$emv3ds: ' . $emv3ds );
			$redsys_insite->log->add( 'insite', '$currency: ' . $currency );
		}

		check_ajax_referer( 'redsys_insite_nonce', 'redsysnonce' );

		set_transient( $order_id . '_insite_token', $redsys_token, 46000 );
		set_transient( $order_id . '_insite_user_id', $user_id, 46000 );


		if ( 'yes' === $redsys_insite->debug ) {
			$redsys_insite->log->add( 'insite', ' ' );
			$redsys_insite->log->add( 'insite', '$user_id: ' . $user_id );
			$redsys_insite->log->add( 'insite', '$customer:' . $customer );
			$redsys_insite->log->add( 'insite', '$terminal: ' . $terminal );
			$redsys_insite->log->add( 'insite', '$currency: ' . $currency );
			$redsys_insite->log->add( 'insite', '$transaction_type: ' . $transaction_type );
			$redsys_insite->log->add( 'insite', '$redsys_amount: ' . $redsys_amount );
			$redsys_insite->log->add( 'insite', '$redsys_order_id: ' . $order_id );
			$redsys_insite->log->add( 'insite', '$redsys_token: ' . $redsys_token );
			$redsys_insite->log->add( 'insite', '$final_notify_url: ' . $final_notify_url );
			$redsys_insite->log->add( 'insite', '$merchan_name: ' . $merchan_name );
			$redsys_insite->log->add( 'insite', '$merchant_lastnme: ' . $merchant_lastnme );
			$redsys_insite->log->add( 'insite', '$redsys_adr: ' . $redsys_adr );
			$redsys_insite->log->add( 'insite', '$secretsha256: ' . $secretsha256 );
		}

		$miObj = new RedsysAPI();

		$miObj->setParameter( 'DS_MERCHANT_MODULE', $merchant_module );
		$miObj->setParameter( "DS_MERCHANT_MERCHANTCODE", $customer );
		$miObj->setParameter( "DS_MERCHANT_TERMINAL", $terminal );
		$miObj->setParameter( "DS_MERCHANT_CURRENCY", $currency );
		$miObj->setParameter( "DS_MERCHANT_TRANSACTIONTYPE", $transaction_type );
		$miObj->setParameter( "DS_MERCHANT_AMOUNT", $redsys_amount );
		$miObj->setParameter( "DS_MERCHANT_ORDER", $order_id );
		$miObj->setParameter( "DS_MERCHANT_IDOPER", $redsys_token );
		$miObj->setParameter( "DS_MERCHANT_MERCHANTURL", $final_notify_url );
		$miObj->setParameter( "DS_MERCHANT_TITULAR", $merchan_name . ' ' . $merchant_lastnme );
		$miObj->setParameter( "DS_MERCHANT_DIRECTPAYMENT", "3DS" );

		if ( (int)$redsys_amount < 3000 ) {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: LWV' );
			}
			$miObj->setParameter( "DS_MERCHANT_EXCEP_SCA", "LWV" );
		} elseif ( $redsys_amount <= ( 100 * (int)$redsys_insite->traamount ) && 'yes' === $redsys_insite->traactive ) {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: TRA' );
			}
			$miObj->setParameter( "DS_MERCHANT_EXCEP_SCA", "TRA" );
		} else {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: NO' );
			}
		}
		$miObj->setParameter( "DS_MERCHANT_EMV3DS", json_decode( $emv3ds, true ) );
		/*
		if ( 'yes' === $redsys_insite->secure3ds ) {
			$miObj->setParameter("DS_MERCHANT_EMV3DS", json_decode( $emv3ds, true ) );
		}
		*/
		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature( $secretsha256 );

		if ( 'yes' === $redsys_insite->debug ) {
			$redsys_insite->log->add( 'insite', '$version: ' . $version );
			$redsys_insite->log->add( 'insite', '$params: ' . $params );
			$redsys_insite->log->add( 'insite', '$signature: ' . $signature );

		}
		set_transient( $order_id . '_insite_version', $version, 3600 );
		set_transient( $order_id . '_insite_params', $params, 3600 );
		set_transient( $order_id . '_insite_signature', $signature, 3600 );
		echo 'success';
		wp_die();
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
		global $woocommerce;
		
		$temp_order_number = get_post_meta( $order, '_temp_redsys_order_number', true );
		$do_challenge      = get_transient( $temp_order_number . '_do_redsys_challenge' );
		add_post_meta( $order, '_order_number_redsys_woocommerce', $temp_order_number );
		set_transient( $temp_order_number . '_woocommrce_order_number_redsys', $order );
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$temp_order_number: ' . $temp_order_number );
			$this->log->add( 'insite', '$do_challenge: ' . $do_challenge );
			$this->log->add( 'insite', '$_POST: ' . print_r( $_POST, true ) );
		}

		if ( isset( $_GET['returnfronredsys'] ) ) {

			if ( isset( $_POST['MD'] ) && isset( $_POST['PaRes'] ) ) {
				
				$fuc               = $this->customer;
				$currency_codes    = WCRed()->get_currencies();
				$terminal          = $this->terminal;
				$currency          = $currency_codes[ get_woocommerce_currency() ];
				$transaction_type  = '0';
				//$final_notify_url = $this->notify_url;
				//$redsys_token     = esc_html( $_POST['token'] );
				$order_id          = $order;
				$merchan_name      = get_post_meta( $order_id, '_billing_first_name', true );
				$merchant_lastnme  = get_post_meta( $order_id, '_billing_last_name', true );
				$temp_order_number = get_post_meta( $order, '_temp_redsys_order_number', true );
				$redsys_order_id   = get_post_meta( $order_id, '_payment_order_number_redsys', true );
				$order             = WCRed()->get_order( $order_id );
				$amount            = $order->get_total();
				$redsys_amount     = WCRed()->redsys_amount_format( $amount );
				$merchant_module   = 'WooCommerce_Redsys_Gateway_' . REDSYS_VERSION . '_WooCommerce.com';
				$user_id           = $order->get_user_id();
				$secretsha256      = $this->get_redsys_sha256( $user_id );
				$redsys_adr        = $this->get_redsys_url_gateway( $user_id );

				$temp_order_number = get_post_meta( $order_id, '_temp_redsys_order_number', true );
				delete_transient( $temp_order_number . '_do_redsys_challenge' );
				$token = get_transient( $order_id . '_insite_token' );

				$md     = $_POST['MD'];
				$pares  = $_POST['PaRes'];
				$emv3ds = '{"threeDSInfo":"ChallengeResponse","protocolVersion":"1.0.2","PARes":"' . $pares . '","MD":"' . $md . '"}';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '$fuc: ' . $fuc );
					$this->log->add( 'insite', '$terminal: ' . $terminal );
					$this->log->add( 'insite', '$currency: ' . $currency );
					$this->log->add( 'insite', '$transaction_type: ' . $transaction_type );
					$this->log->add( 'insite', '$order_id: ' . $order_id );
					$this->log->add( 'insite', '$merchan_name: ' . $merchan_name );
					$this->log->add( 'insite', '$redsys_order_id: ' . $redsys_order_id );
					$this->log->add( 'insite', '$temp_order_number: ' . $temp_order_number );
					$this->log->add( 'insite', '$amount: ' . $amount );
					$this->log->add( 'insite', '$redsys_amount: ' . $redsys_amount );
					$this->log->add( 'insite', '$secretsha256: ' . $secretsha256 );
					$this->log->add( 'insite', '$redsys_adr: ' . $redsys_adr );
					$this->log->add( 'insite', '$md: ' . $md );
					$this->log->add( 'insite', '$pares: ' . $pares );
					$this->log->add( 'insite', '$emv3ds: ' . $emv3ds );
					$this->log->add( 'insite', '$token: ' . $token );
				}
				if ( class_exists( 'ISAuthenticationMessage' ) ) {
				$request = new ISAuthenticationMessage();
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', 'ISAuthenticationMessage: NOT DEFINED' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$setOrder          = method_exists( $request,'setOrder' );
					$setAmount         = method_exists( $request,'setAmount' );
					$setCurrency       = method_exists( $request,'setCurrency' );
					$setMerchant       = method_exists( $request,'setMerchant' );
					$setTerminal       = method_exists( $request,'setTerminal' );
					$setTransactionType= method_exists( $request,'setTransactionType' );
					$addEmvParameters  = method_exists( $request,'addEmvParameters' );
					$addEmvParameter   = method_exists( $request,'addEmvParameter' );
					
					if ( $setOrder ) {
						$this->log->add( 'insite', 'METHOD $setOrder: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setOrder: NOT EXIST' );
					}
					
					if ( $setAmount ) {
						$this->log->add( 'insite', 'METHOD $setAmount: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setAmount: NOT EXIST' );
					}
					
					if ( $setCurrency ) {
						$this->log->add( 'insite', 'METHOD $setCurrency: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setCurrency: NOT EXIST' );
					}
					
					if ( $setMerchant ) {
						$this->log->add( 'insite', 'METHOD $setMerchant: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setMerchant: NOT EXIST' );
					}
					
					if ( $setTerminal ) {
						$this->log->add( 'insite', 'METHOD $setTerminal: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setTerminal: NOT EXIST' );
					}
					
					if ( $setTransactionType ) {
						$this->log->add( 'insite', 'METHOD $setTransactionType: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $setTransactionType: NOT EXIST' );
					}
					
					if ( $addEmvParameters ) {
						$this->log->add( 'insite', 'METHOD $addEmvParameters: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $addEmvParameters: NOT EXIST' );
					}
					
					if ( $addEmvParameter ) {
						$this->log->add( 'insite', 'METHOD $addEmvParameter: EXIST' );
					} else {
						$this->log->add( 'insite', 'METHOD $addEmvParameter: NOT EXIST' );
					}
				}

				$request->setOrder( $temp_order_number );
				$request->setAmount( $redsys_amount );
				$request->setCurrency( $currency );
				$request->setMerchant( $fuc );
				$request->setTerminal( $terminal );
				$request->setTransactionType( $transaction_type );
				$request->addEmvParameters( array(
					'threeDSInfo'     => 'ChallengeResponse',
					'protocolVersion' => '1.0.2',
					'PARes'           => $pares,
					'MD'              => $md,
					)
				);

				if ( 'yes' === $this->testmode ) {
					$entorno = '0';
				} else {
					$entorno = '1';
				}

				$service = new ISAuthenticationService ( $secretsha256, $entorno );
				$result  = $service->sendOperation ( $request );

				$resultado = $result->getResult();

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '$resultado: ' . $resultado );
				}
				
				if ( 'OK' === $resultado ) {
					$location = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
					wp_safe_redirect( esc_url( $location ) );
					exit;
				} else {
					echo __( 'There was a problem:', '' ) . ' ' . $resultado;
				}

			} else {
				echo 'Error';
			}
		}

		if ( isset( $_GET['challenge'] ) || 'yes' === $do_challenge ) {
			if ( isset( $_GET['challenge'] ) ) {
				$challenge = sanitize_text_field( $_GET['challenge'] );
			}
			if ( 'yes' === $challenge || 'yes' === $do_challenge ) {
				$temp_order_number  = get_post_meta( $order, '_temp_redsys_order_number', true );
				$order2             = WCRed()->get_order( $order );
				$redirectok         = $order2->get_checkout_payment_url( true ) . '&returnfronredsys=yes';
				$acsurl             = get_transient( $temp_order_number . '_insite_acsurl' );
				$pareq              = get_transient( $temp_order_number . '_insite_pareq' );
				$md                 = get_transient( $temp_order_number . '_insite_md' );

				wc_enqueue_js('$("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to 3DSecure Form.', 'woocommerce-redsys' ) . '",
				overlayCSS:
				{
					background: "#fff",
					opacity: 0.6
				},
				css: {
					padding:         20,
					textAlign:       "center",
					color:           "#555",
					border:          "3px solid #aaa",
					backgroundColor: "#fff",
					cursor:          "wait",
					lineHeight:      "32px"
				}
			});
			jQuery("#submit_redsys_payment_form").click();
			'
		);
		echo '<form action="' . esc_url( $acsurl ) . '" method="post" id="redsys_payment_form" target="_top">
		<input type="hidden" name="PaReq" value="' . esc_attr( $pareq ) . '" />
		<input type="hidden" name="TermUrl" value="' . $redirectok . '">
		<input type="hidden" name="MD" value="' . esc_attr( $md ) . '" />
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Press here if you are not redirected', 'woocommerce-redsys' ) . '" />
		</form>';
			}
		} else {
			$minheigh        = '';
			$margintop       = '';
			$colorbutton     = '';
			$colorfieldtext = '';
			$terminal        = $this->terminal;
			$fuc             = $this->customer;
			$orderId         = WCRed()->prepare_order_number( $order );
			$order2          = WCRed()->get_order( $order );
			//$redirectok      = $order2->get_checkout_payment_url( true ) . '&returnfronredsys=yes';
			$redirectok      = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order2 ) );
			$redirectchck    = $order2->get_checkout_payment_url( true ) . '&challenge=yes';
			$minheigh        = $this->minheigh;
				
			if ( ! empty( $minheigh ) ) {
				$minheigh = $minheigh;
			} else {
				$minheigh = '300';
			}
			
			$margintop = $this->margintop;
			
			if ( ! empty( $margintop ) ) {
				$margintop = $margintop;
			} else {
				$margintop = '50';
			}
			
			$colorbutton = $this->colorbutton;
			
			if ( ! empty( $colorbutton ) ) {
				$colorbutton = $colorbutton;
			} else {
				$colorbutton = '#f39c12';
			}

			$colorfieldtext = $this->colorfieldtext;

			if ( ! empty( $colorfieldtext ) ) {
				$colorfieldtext = $colorfieldtext;
			} else {
				$colorfieldtext = '#95a5a6';
			}
			
			$colortextbutton = $this->colortextbutton;
			
			if ( ! empty( $colortextbutton ) ) {
				$colortextbutton = $colortextbutton;
			} else {
				$colortextbutton = '#ffffff';
			}
			
			$textcolor = $this->textcolor;
			
			if ( ! empty( $textcolor ) ) {
				$textcolor = $textcolor;
			} else {
				$textcolor = '#2e3131';
			}
			
			$buttontext = $this->buttontext;
			
			if ( ! empty( $buttontext ) ) {
				$buttontext = $buttontext;
			} else {
				$buttontext = __( 'Pay Now', 'woocommerce-redsys' );
			}

			$nonce        = wp_create_nonce( 'redsys_insite_nonce' );
			update_post_meta( $order, '_payment_order_number_redsys', $orderId );

			if ( 'Intindepenelements' === $this->insitetype ) { // Integration by independent elements
			
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/*******************************************/' );
					$this->log->add( 'insite', '   Cargamos el formulario InSite Complejo    ' );
					$this->log->add( 'insite', '/*******************************************/' );
					$this->log->add( 'insite', ' ' );
				}
				
				echo '<div class="cardinfo-card-number">
						<label class="cardinfo-label" for="card-number">Numero de tarjeta</label>
						<div class="input-wrapper" id="card-number"></div>
					</div>
					<div class="expiry-date">
						<div class="cardinfo-exp-date">
							<label class="cardinfo-label" for="expiration-date">Mes Caducidad (MM)</label>
							<div class="input-wrapper" id="expiration-month"></div>
						</div>
						<div class="cardinfo-exp-date2">
							<label class="cardinfo-label" for="expiration-date2">Ano Caducidad (AA)</label>
							<div class="input-wrapper" id="expiration-year"></div>
						</div>
						<div class="cardinfo-cvv">
							<label class="cardinfo-label" for="cvv">CVV</label>
							<div class="input-wrapper" id="cvv"></div>
						</div>
						<div class="cardinfo-submit">
							<div class="input-wrapper" id="submit"></div>
						</div>
					</div>
	
					<input type="hidden" id="token" ></input>
					<input type="hidden" id="errorCode" ></input>
					<script>
						function merchantValidationEjemplo() {
							//Insertar validaciones...
							return true;
						}
						<!-- Listener -->
						const orderId = Math.round(Math.random() * 10000).toString();
						window.addEventListener("message", function receiveMessage(event) {
							console.log(event);
							console.log("Operation Id:", event.data.idOper);
							console.log("Order Id:", orderId);
							storeIdOper(event,"token", "errorCode", merchantValidationEjemplo);
							console.log("Order Id token:", token.value);
							console.log("Error:", errorCode.value);
						});
	
						<!-- Peticion de carga de iframes -->
						getCardInput("card-number","");
						getExpirationMonthInput("expiration-month", "");
						getExpirationYearInput("expiration-year", "");
						getCVVInput("cvv", "");
						getPayButton("submit", "", "Pagar con Redsys", "' . $fuc . '", "' . $terminal . '", orderId);
					</script>
					<style>
						#redsys-hosted-pay-button {margin-bottom: 25px:}
					</style>';
			} else { // Unified integration
				
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/*******************************************/' );
					$this->log->add( 'insite', '  Cargamos el formulario InSite sencillo    ' );
					$this->log->add( 'insite', '/*******************************************/' );
					$this->log->add( 'insite', ' ' );
				}
				echo '
					<div id="bloque-CC">
						<div id="card-form"/>
					</div>
					<input type="hidden" id="token" ></input>
					<input type="hidden" id="errorCode" ></input>
					<script>
						<!-- Listener -->
						const orderId = Math.round(Math.random() * 10000).toString();
						window.addEventListener("message", function receiveMessage(event) {
							storeIdOper(event,"token", "errorCode");
							if ( token.value ) {
								console.log(event);
								console.log("Order Id token:", token.value );
								console.log("Error:", errorCode.value);
								console.log("Ajax URL:", ajaxurl);
								if ( token.value ) {
									jQuery(document).ready( function() {
										console.log("llega Ajax");
										if ( token.value ) {
											console.log("se defino token.value:", token.value );
											console.log("se defino token.value:", ' . $orderId . ' );
											jQuery.ajax({
												type : "post",
												url : ajaxurl,
												data : {
													"action": "check_token_insite_from_action",
													"token" : token.value,
													"order_id" : ' . $order . ',
													"redsysnonce" : "' . $nonce . '"
												},
												success: function(response) {
													console.log("response:", response, "type of:", typeof response)
													if(response=="success") {
														window.location.href = "' . $redirectok . '";
													} else if (response=="ChallengeRequest"){
														window.location.href = "' . $redirectchck . '";
													} else {
														alert("Error: " + response );
													}
												}
											})
										}
									});
								}	
							}
						});
						getInSiteForm(
							"card-form",
							"background-color:' . $colorbutton . '; color:' . $colortextbutton . '",
							"color:' . $textcolor . ';",
							"color:' . $colorfieldtext . ';",
							";",
							"' .  $buttontext . '",
							"' . $fuc . '",
							"' . $terminal . '",
							"' . $orderId . '"
						);

					</script>
					<style>
						#redsys-hosted-pay-button {margin-top:' . $margintop . 'px; min-height: ' . $minheigh . 'px;}
					</style>';
			}
		}
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
			$this->log->add( 'insite', 'HTTP Notification received: ' . print_r( $_POST, true ) );
		}
		if ( isset( $_POST["Ds_SignatureVersion"] ) &&  isset( $_POST["Ds_MerchantParameters"] ) && isset( $_POST["Ds_Signature"] ) ) {
			$version            = $_POST["Ds_SignatureVersion"];
			$data               = $_POST["Ds_MerchantParameters"];
			$remote_sign        = $_POST["Ds_Signature"];
			$miObj              = new RedsysAPI;
			$decodedata         = $miObj->decodeMerchantParameters($data);
			//$localsecret        = $miObj->createMerchantSignatureNotif($usesecretsha256,$data);
			$ds_amount          = (int)$miObj->getParameter('Ds_Amount');
			$ds_order           = $miObj->getParameter('Ds_Order');
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
		} else {
			$ds_order  = $_POST['Ds_Order'];
			$ds_amount = (int)$_POST['Ds_Amount'];
		}
		$order_id  = get_transient( $ds_order . '_woocommrce_order_number_redsys' );
		$order     = WCRed()->get_order( $order_id );
		$amount    = (int)WCRed()->redsys_amount_format( $order->get_total() );
		if ( 'yes' == $this->debug ) {
			$this->log->add( 'insite', '$ds_order: ' . $ds_order );
			$this->log->add( 'insite', '$order_id: ' . $order_id );
			// $this->log->add( 'insite', '$order: ' . $order );
			$this->log->add( 'insite', '$ds_amount: ' . $ds_amount );
			$this->log->add( 'insite', '$amount: ' .$amount );
		}
		
		if ( $ds_amount === $amount ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Received valid notification from InSite' );
				$this->log->add( 'insite', $data );
			}
			return true;
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Received Invalid notification from InSite' );
				$this->log->add( 'insite', $data );
			}
			return false;
		}
		//$usesecretsha256 = $this->secretsha256;
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		if ( $usesecretsha256 ) {
			$version     = $_POST["Ds_SignatureVersion"];
			$data        = $_POST["Ds_MerchantParameters"];
			$remote_sign = $_POST["Ds_Signature"];
			$miObj       = new RedsysAPI();
			$localsecret = $miObj->createMerchantSignature( $usesecretsha256, $data );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$localsecret: ' . $localsecret );
				$this->log->add( 'insite', '$remote_sign: ' . $remote_sign );
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'insite', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			}
			if ( $_POST['Ds_MerchantCode'] === $this->customer ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'insite', 'Received valid notification from InSite' );
				}
				return true;
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'insite', 'Received INVALID notification from InSite' );
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
			do_action( "valid-insite-standard-ipn-request", $_POST );
		} else {
			wp_die( 'InSite Notification Request Failure' );
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

		if ( isset( $_POST["Ds_SignatureVersion"] ) &&  isset( $_POST["Ds_MerchantParameters"] ) && isset( $_POST["Ds_Signature"] ) ) {
			$version            = $_POST["Ds_SignatureVersion"];
			$data               = $_POST["Ds_MerchantParameters"];
			$remote_sign        = $_POST["Ds_Signature"];
			$miObj              = new RedsysAPI;
			$decodedata         = $miObj->decodeMerchantParameters($data);
			//$localsecret        = $miObj->createMerchantSignatureNotif($usesecretsha256,$data);
			$total              = (int)$miObj->getParameter('Ds_Amount');
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
			$order2             = get_transient( $ordermi . '_woocommrce_order_number_redsys' );
			$order              = WCRed()->get_order( (int)$order2 );
		} else {
			$total              = $_POST["Ds_Amount"];
			$ordermi            = $_POST["Ds_Order"];
			$dscode             = $_POST["Ds_MerchantCode"];
			$currency_code      = $_POST["Ds_Currency"];
			$response           = $_POST["Ds_Response"];
			$id_trans           = $_POST["Ds_AuthorisationCode"];
			$dsdate             = $_POST["Ds_Date"];
			$dshour             = $_POST["Ds_Hour"];
			$dstermnal          = $_POST["Ds_Terminal"];
			$dsmerchandata      = $_POST["Ds_MerchantData"];
			$dssucurepayment    = $_POST["Ds_SecurePayment"];
			$dscardcountry      = $_POST["Ds_SignatureVersion"];
			$order2             = get_transient( $ordermi . '_woocommrce_order_number_redsys' );
			$order              = WCRed()->get_order( (int)$order2 );
		}
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $ordermi . ',  Ds_MerchantCode: '. $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
		}
		
		$response = intval( $response );
		if ( $response  <= 99 ) {
			//authorized
			$order_total_compare = number_format( $order->get_total() , 2 , '' , '' );
			if ( $order_total_compare != $total ) {
				//amount does not match
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'insite', 'Payment error: Amounts do not match (order: '.$order_total_compare.' - received: ' . $total . ')' );
				}
				
				// Put this order on-hold for manual checking
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %s - received: %s).', 'woocommerce-redsys' ), $order_total_compare , $total ) );
				exit;
			}
			$authorisation_code = $id_trans;
			if ( ! empty( $ordermi ) ) {
				update_post_meta( $order->id, '_payment_order_number_redsys', $ordermi );
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
				update_post_meta( $order->id, '_card_country_insite',   $dscardcountry );
			}
			if ( ! empty( $dscargtype ) ) {
				update_post_meta( $order->id, '_card_type_insite',   $dscargtype == 'C' ? 'Credit' : 'Debit' );
			}
			
			// Payment completed
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorisation code: ',  'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Payment complete.' );
			}
		} else {
			$ds_responses = $this->get_ds_response();
			$ds_errors    = $this->get_ds_error();
			
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
				$this->log->add( 'insite', 'Pedido cancelado por InSite' );
			}
			
			if ( 'yes' === $this->debug ) {
				if ( ! empty( $ds_responses ) ) {
					$this->log->add( 'insite', 'Error: ' . $ds_response_value );
				}
				if ( ! empty( $ds_errors ) ) {
					$this->log->add( 'insite', 'Error: ' . $ds_error_value );
				}
			}

			//Order cancelled
			$order->update_status( 'cancelled', __( 'Cancelled by InSite', 'woocommerce-redsys' ) );
			$order->add_order_note( __('Order canceled by InSite', 'woocommerce-redsys') );
			WC()->cart->empty_cart();
		}
	}
	
	/**
	* get_insite_order function.
	*
	* @access public
	* @param mixed $posted
	* @return void
	*/
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	function get_insite_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	
	public function save_field_update_order_meta( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$order   = WCRed()->get_order( $order_id );
		$user_id = $order->get_user_id();
		if ( 'yes' == $this->debug ) {
			$this->log->add( 'insite', 'HTTP Notification received: ' . print_r( $_POST, true ) );
		}
		if ( ! empty( $_POST['billing_agente_navegador'] ) ) {
			update_post_meta( $order_id, '_billing_agente_navegador_field', sanitize_text_field( $_POST['billing_agente_navegador'] ) );
			update_user_meta( $user_id, '_billing_agente_navegador_field', sanitize_text_field( $_POST['billing_agente_navegador'] ) );
		}
		if ( ! empty( $_POST['billing_idioma_navegador'] ) ) {
			update_post_meta( $order_id, '_billing_idioma_navegador_field', sanitize_text_field( $_POST['billing_idioma_navegador'] ) );
			update_user_meta( $user_id, '_billing_idioma_navegador_field', sanitize_text_field( $_POST['billing_idioma_navegador'] ) );
		}
		if ( ! empty( $_POST['billing_altura_pantalla'] ) ) {
			update_post_meta( $order_id, '_billing_altura_pantalla_field', sanitize_text_field( $_POST['billing_altura_pantalla'] ) );
			update_user_meta( $user_id, '_billing_altura_pantalla_field', sanitize_text_field( $_POST['billing_altura_pantalla'] ) );
		}
		if ( ! empty( $_POST['billing_anchura_pantalla'] ) ) {
			update_post_meta( $order_id, '_billing_anchura_pantalla_field', sanitize_text_field( $_POST['billing_anchura_pantalla'] ) );
			update_user_meta( $user_id, '_billing_anchura_pantalla_field', sanitize_text_field( $_POST['billing_anchura_pantalla'] ) );
		}
		if ( ! empty( $_POST['billing_profundidad_color'] ) ) {
			update_post_meta( $order_id, '_billing_profundidad_color_field', sanitize_text_field( $_POST['billing_profundidad_color'] ) );
			update_user_meta( $user_id, '_billing_profundidad_color_field', sanitize_text_field( $_POST['billing_profundidad_color'] ) );
		}
		if ( ! empty( $_POST['billing_diferencia_horaria'] ) ) {
			update_post_meta( $order_id, '_billing_diferencia_horaria_field', sanitize_text_field( $_POST['billing_diferencia_horaria'] ) );
			update_user_meta( $user_id, '_billing_diferencia_horaria_field', sanitize_text_field( $_POST['billing_diferencia_horaria'] ) );
		}
		if ( ! empty( $_POST['_temp_redsys_order_number'] ) ) {
			update_post_meta( $order_id, '_temp_redsys_order_number', sanitize_text_field( $_POST['_temp_redsys_order_number'] ) );
		}
	}
}
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
function woocommerce_add_gateway_insite_gateway( $methods ) {
	$methods[] = 'WC_Gateway_InSite_Redsys';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_insite_gateway' );
