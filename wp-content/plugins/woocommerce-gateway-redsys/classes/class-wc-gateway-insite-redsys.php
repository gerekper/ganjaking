<?php

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gateway class
 */
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2021 José Conti
 */
class WC_Gateway_InSite_Redsys extends WC_Payment_Gateway {
	var $notify_url;

	/**
	 * Constructor for the gateway.
	 *
	 * @return void
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function __construct() {
		global $woocommerce;

		$this->id = 'insite';

		if ( ! empty( WCRed()->get_redsys_option( 'logo', 'insite' ) ) ) {
			$logo_url   = WCRed()->get_redsys_option( 'logo', 'insite' );
			$this->icon = apply_filters( 'woocommerce_insite_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_insite_icon', REDSYS_PLUGIN_URL_P . 'assets/images/redsys.png' );
		}

		$this->has_fields           = true;
		$this->liveurl              = 'https://sis.redsys.es/sis/services/SerClsWSEntrada';
		$this->testurl              = 'https://sis-i.redsys.es:25443/sis/services/SerClsWSEntrada"';
		$this->liveurlws2           = 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl';
		$this->testurlws2           = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl';
		$this->liveurlws            = 'https://sis.redsys.es/sis/rest/trataPeticionREST';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = WCRed()->get_redsys_option( 'testmode', 'insite' );
		$this->method_title         = __( 'Redsys in Checkout (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'Redsys in Checkout use InSite for add a Credit Card Form in the checkout. InSite is needed for use this payment form.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'insite' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_insiteredsys', home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_insiteredsys', home_url( '/' ) ) );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title            = WCRed()->get_redsys_option( 'title', 'insite' );
		$this->multisitesttings = WCRed()->get_redsys_option( 'multisitesttings', 'insite' );
		$this->ownsetting       = WCRed()->get_redsys_option( 'ownsetting', 'insite' );
		$this->hideownsetting   = WCRed()->get_redsys_option( 'hideownsetting', 'insite' );
		$this->logo             = WCRed()->get_redsys_option( 'logo', 'insite' );
		$this->description      = WCRed()->get_redsys_option( 'description', 'insite' );
		$this->textnotfillfilds = WCRed()->get_redsys_option( 'textnotfillfilds', 'insite' );
		$this->customer         = WCRed()->get_redsys_option( 'customer', 'insite' );
		$this->terminal         = WCRed()->get_redsys_option( 'terminal', 'insite' );
		$this->customfieldname  = WCRed()->get_redsys_option( 'customfieldname', 'insite' );
		$this->secretsha256     = WCRed()->get_redsys_option( 'secretsha256', 'insite' );
		$this->pay1clic         = WCRed()->get_redsys_option( 'pay1clic', 'insite' );
		$this->debug            = WCRed()->get_redsys_option( 'debug', 'insite' );
		$this->hashtype         = WCRed()->get_redsys_option( 'hashtype', 'insite' );
		$this->insitelanguage   = WCRed()->get_redsys_option( 'insitelanguage', 'insite' );
		$this->wooinsiteurlko   = WCRed()->get_redsys_option( 'wooinsiteurlko', 'insite' );
		$this->commercename     = WCRed()->get_redsys_option( 'wooinsitecomercename', 'insite' );
		$this->insitetype       = WCRed()->get_redsys_option( 'insitetype', 'insite' );
		$this->traactive        = WCRed()->get_redsys_option( 'traactive', 'insite' );
		$this->traamount        = WCRed()->get_redsys_option( 'traamount', 'insite' );
		$this->colorbutton      = WCRed()->get_redsys_option( 'colorbutton', 'insite' );
		$this->colorfieldtext   = WCRed()->get_redsys_option( 'colorfieldtext', 'insite' );
		$this->colortextbutton  = WCRed()->get_redsys_option( 'colortextbutton', 'insite' );
		$this->textcolor        = WCRed()->get_redsys_option( 'textcolor', 'insite' );
		$this->buttontext       = WCRed()->get_redsys_option( 'buttontext', 'insite' );
		$this->butonbgcolor     = WCRed()->get_redsys_option( 'butonbgcolor', 'insite' );
		$this->butontextcolor   = WCRed()->get_redsys_option( 'butontextcolor', 'insite' );
		$this->cvvboxcolor      = WCRed()->get_redsys_option( 'cvvboxcolor', 'insite' );
		$this->button_heigth    = WCRed()->get_redsys_option( 'button_heigth', 'insite' );
		$this->button_width     = WCRed()->get_redsys_option( 'button_width', 'insite' );
		$this->descripredsys    = WCRed()->get_redsys_option( 'descripredsys', 'insite' );
		$this->customtestsha256 = WCRed()->get_redsys_option( 'customtestsha256', 'insite' );
		$this->testforuser      = WCRed()->get_redsys_option( 'testforuser', 'insite' );
		$this->testforuserid    = WCRed()->get_redsys_option( 'testforuserid', 'insite' );
		$this->testshowgateway  = WCRed()->get_redsys_option( 'testshowgateway', 'insite' );
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			'tokenization',
			'refunds',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'multiple_subscriptions',
			'yith_subscriptions',
			'yith_subscriptions_scheduling',
			'yith_subscriptions_pause',
			'yith_subscriptions_multiple',
			'yith_subscriptions_payment_date',
			'yith_subscriptions_recurring_amount',
		);
		if ( ! $this->insitetype ) {
			$this->insitetype = 'intindepenelements';
		}
		// Actions.
		add_action( 'valid-insite-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_insite', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_insiteredsys', array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_field_update_order_meta' ) );
		// WooCommerce Subscriptions.
		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'doing_scheduled_subscription_payment' ), 10, 2 );
		}
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
		add_filter( 'woocommerce_checkout_fields', array( $this, 'update_checkout_on_change' ), 999 );
		add_action( 'wp_head', array( $this, 'add_insite_redsys2' ) );
		add_action( 'wp_footer', array( $this, 'add_insite_on_loadform' ), 900 );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hide_payment_method_add_method' ) );
	}

	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @return bool
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function admin_options() {
		?>
			<h3><?php _e( 'Redsys in Checkout (by José Conti) - InSite', 'woocommerce-redsys' ); ?></h3>
			<p><?php _e( 'InSite works by adding a Credit Card Form in the WooCommerce Checkout.', 'woocommerce-redsys' ); ?></p>
			<?php if ( class_exists( 'SitePress' ) ) { ?>
				<div class="updated fade"><h4><?php _e( 'Attention! WPML detected.', 'woocommerce-redsys' ); ?></h4>
					<p><?php _e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woocommerce-redsys' ); ?></p>
				</div>
				<?php
			}
			if ( class_exists( 'SOAPClient' ) ) {
				try {
					  $soapClient = new SoapClient( 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl' );
				} catch ( Exception $e ) {
					$exceptionMessage = $e->getMessage();
				}
				if ( $exceptionMessage ) {
					?>
							<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
								<p><?php _e( 'InSite will not work in Test Mode, Normally this happens because your hosting is blocking the Port 25443 for SOAP, please talk to your hosting and tell them to open port 25443 for SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl If the hosting does not open the port, the plugin will not work correctly in test mode..', 'woocommerce-redsys' ); ?></p>
							</div>
						<?PHP
				}
				try {
					  $soapClient = new SoapClient( 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl' );
				} catch ( Exception $e ) {
					$exceptionMessage = $e->getMessage();
				}
				if ( $exceptionMessage ) {
					?>
							<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
								<p><?php _e( 'InSite will not work in Real Mode, Normally this happens because your hosting is blocking SOAP, please talk to your hosting and tell them to open port 443 for SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl If the hosting does not open the port, the plugin will not work correctly in real mode.', 'woocommerce-redsys' ); ?></p>
							</div>
						<?PHP
				}
			} else {
				?>
					<div class="notice notice-error"><h4><?php _e( 'Attention! Problem with SOAP.', 'woocommerce-redsys' ); ?></h4>
					<?php _e( 'SOAP is needed for Pay with InSite. Ask to your hosting to enable it. Without active SOAP on the server, the functionality of the plugin is very limited.', 'woocommerce-redsys' ); ?>
					</div>
				<?php
			}
			echo WCRed()->return_help_notice();
			if ( $this->is_valid_for_use() ) :
				?>
				<table class="form-table">
					<?php
					// Generate the HTML For the settings form.
					$this->generate_settings_html();
					?>
				</table><!--/.form-table-->
				<?php
			else :
				include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';
				$currencies          = redsys_return_allowed_currencies();
				$formated_currencies = '';

				foreach ( $currencies as $currency ) {
					$formated_currencies .= $currency . ', ';
				}
				?>
				<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'woocommerce-redsys' ); ?></strong>: 
																	   <?php
																		esc_html_e( 'Servired/RedSys only support ', 'woocommerce-redsys' );
																		echo $formated_currencies;
																		?>
		</p></div>
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function init_form_fields() {

		$options    = array();
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'insite' );

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
		$options_show    = array();
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'insite' );
		if ( count( $selections_show ) !== 0 ) {
			foreach ( $selections_show as $user_id ) {
				if ( ! empty( $user_id ) ) {
					$user_data  = get_userdata( $user_id );
					$user_email = $user_data->user_email;
					if ( ! empty( esc_html( $user_email ) ) ) {
						$options_show[ esc_html( $user_id ) ] = esc_html( $user_email );
					}
				}
			}
		}

		$this->form_fields = array(
			'enabled'          => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable InSite', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'multisitesttings' => array(
				'title'       => __( 'Use in Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Use this setting arround all Network', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'hideownsetting'   => array(
				'title'       => __( 'Hide "NOT use Network" in subsites', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Hide "NOT use Network" in subsites', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'ownsetting'       => array(
				'title'       => __( 'NOT use Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Do NOT use Network settings. Use settings of this page', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'insitetype'       => array(
				'title'       => __( 'Select InSite Type', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Select Independent elements (default) o Integrated form.', 'woocommerce-redsys' ),
				'default'     => 'intindepenelements',
				'options'     => array(
					'intindepenelements' => __( 'Integration by independent elements (Default)', 'woocommerce-redsys' ),
					'unifiedintegration' => __( 'Unified integration', 'woocommerce-redsys' ),
				),
			),
			'pay1clic'         => array(
				'title'   => __( 'Pay with 1click', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Pay with 1click', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'title'            => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'InSite', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'      => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via InSite; you can pay with your credit card.', 'woocommerce-redsys' ),
			),
			'logo'             => array(
				'title'       => __( 'Gateway logo at checkout', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add link to image logo for Gateway at checkout.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'textnotfillfilds' => array(
				'title'       => __( 'Text when the customer', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'Text when the customer has not yet filled in the required billing fields for InSite.', 'woocommerce-redsys' ),
				'default'     => __( 'Please fill in the billing fields of the checkout form. After filling them, the credit card form will appear.', 'woocommerce-redsys' ),
			),
			'traactive'        => array(
				'title'   => __( 'Enable TRA', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable TRA for Pay with 1click. WARNING, your bank has to enable it before you use it.', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'traamount'        => array(
				'title'       => __( 'Limit import for TRA', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'TRA will be sent when the amount is inferior to what you specify here. Write the amount without the currency sign, i.e. if it is 250€, ONLY write 250', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'commercename'     => array(
				'title'       => __( 'Commerce Name', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce Name', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'customer'         => array(
				'title'       => __( 'Commerce number (FUC)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'terminal'         => array(
				'title'       => __( 'Terminal number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'descripredsys'    => array(
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
			'secretsha256'     => array(
				'title'       => __( 'Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'customtestsha256' => array(
				'title'       => __( 'TEST MODE: Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for test mode.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'insitelanguage'   => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'wooinsiteurlko'   => array(
				'title'       => __( 'Return URL (Redsys Error button)', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'When the user press the return button at Redsys Gateway (Ex: The user type an incorrect credit cart), you can redirect the user to My Cart page canceling the order, or you can redirect the user to Checkput page without cancel the order.', 'woocommerce-redsys' ),
				'default'     => 'returncancel',
				'options'     => array(
					'returncancel'   => __( 'Cancel the order and return to My Cart page', 'woocommerce-redsys' ),
					'returnnocancel' => __( 'Don\'t cancel the order and return to Checkout page', 'woocommerce-redsys' ),
				),
			),
			'not_use_https'    => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility (only activate it if José Conti indicate you).', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Only use it if José Conti indicate you. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woocommerce-redsys' ) ),
			),
			'customfieldname'  => array(
				'title'       => __( 'Custom "Name" field', 'woocommerce-redsys' ),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'In some cases you modify the billing field "Name" in the checkout. If you do this, add the custom name field or InSite will not work, example billing_first_name', 'woocommerce-redsys' ),
			),
			'buttontext'       => array(
				'title'       => __( 'Button Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'default'     => 'Realizar pago',
				'description' => __( 'Add the Button Text.', 'woocommerce-redsys' ),
			),
			'textcolor'        => array(
				'title'       => __( 'General Color Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This is the General text color added by InSite. Default #2e3131', 'woocommerce-redsys' ),
				'default'     => '#2e3131',
				'class'       => 'colorpick',
			),
			'colorbutton'      => array(
				'title'       => __( 'Color Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button color. Default #f39c12', 'woocommerce-redsys' ),
				'default'     => '#f39c12',
				'class'       => 'colorpick',
			),
			'colortextbutton'  => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button text color. Default #ffffff', 'woocommerce-redsys' ),
				'default'     => '#ffffff',
				'class'       => 'colorpick',
			),
			'colorfieldtext'   => array(
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
			'button_heigth'    => array(
				'title'       => __( 'Button Pay Now heigth', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'The heigth os Pay now button. Default 85px (you can use px or %)', 'woocommerce-redsys' ),
				'default'     => '85px',
			),
			'button_width'     => array(
				'title'       => __( 'Button Pay Now width', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'The width os Pay now button. Default 100% (you can use px or %)', 'woocommerce-redsys' ),
				'default'     => '100%',
			),
			'testmode'         => array(
				'title'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woocommerce-redsys' ) ),
			),
			'testshowgateway'  => array(
				'title'       => __( 'Show to this users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Show the gateway in the chcekout when it is in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-show-gateway-test-settings',
				'id'          => 'woocommerce_redsys_showtestforuserid',
				'options'     => $options_show,
				'default'     => '',
				'description' => sprintf( __( 'Select users that will see the gateway when it is in test mode. If no users are selected, will be shown to all users', 'woocommerce-redsys' ) ),
			),
			'testforuser'      => array(
				'title'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'default'     => '',
				'description' => sprintf( __( 'The user selected below will use the terminal in test mode. Other users will continue to use live mode unless you have the "Running in test mode" option checked.', 'woocommerce-redsys' ) ),
			),
			'testforuserid'    => array(
				'title'       => __( 'Users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Users running in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-allowed-users-settings',
				'id'          => 'woocommerce_redsys_testforuserid',
				'options'     => $options,
				'default'     => '',
				'description' => sprintf( __( 'Select users running in test mode', 'woocommerce-redsys' ) ),
			),
			'debug'            => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log InSite events, such as notifications requests, inside <code>WooCommerce > Status > Logs > insite-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
		$redsyslanguages   = WCRed()->get_redsys_languages();

		foreach ( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['insitelanguage']['options'][ $redsyslanguage ] = $valor;
		}
		if ( ! is_multisite() ) {
			unset( $this->form_fields['multisitesttings'] );
			unset( $this->form_fields['ownsetting'] );
			unset( $this->form_fields['hideownsetting'] );
		} else {
			if ( is_main_site() ) {
				unset( $this->form_fields['ownsetting'] );
			} else {
				unset( $this->form_fields['multisitesttings'] );
				unset( $this->form_fields['hideownsetting'] );
				$globalsettings = WCRed()->get_redsys_option( 'multisitesttings', $this->id );
				$hide           = WCRed()->get_redsys_option( 'hideownsetting', $this->id );
				if ( 'yes' === $hide || 'yes' !== $globalsettings ) {
					unset( $this->form_fields['ownsetting'] );
				}
			}
		}
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function add_insite_redsys2() {

		if ( is_wc_endpoint_url( 'order-pay' ) || is_checkout() ) {

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = '0';
			}
			echo '<!-- Comienza JS para InSite añadido por WooCommerce Redsys Gateway https://woocommerce.com/es-es/products/redsys-gateway/ -->';
			echo '<script type="text/javascript">var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";</script>';
			echo $this->get_js_header( $user_id );
			echo '<!-- Finaliza JS para InSite añadido por WooCommerce Redsys Gateway https://woocommerce.com/es-es/products/redsys-gateway/ -->';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function add_insite_on_loadform() {

		if ( is_wc_endpoint_url( 'order-pay' ) || is_checkout() ) {
			// echo '<script>window.onload = loadRedsysForm();</script>';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
			// $ran  = wp_rand( 6000, 10000 );
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
				// $ran  = wp_rand( 6000, 10000 );
				$code = '<script src="https://sis-t.redsys.es:25443/sis/NC/sandbox/redsysV2.js"></script>';
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Live WD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				// $ran  = wp_rand( 6000, 10000 );
				$code = '<script src="https://sis.redsys.es/sis/NC/redsysV2.js"></script>';
			}
		}
		return $code;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_url_gateway( $user_id = false ) {

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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_url_gateway_ws( $user_id = false ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          URL Test        ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			$url = $this->testurlws2;
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
				$url = $this->testurlws2;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          URL Live RD         ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$url = $this->liveurlws2;
			}
		}
		return $url;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_user_show_payment_method( $userid = false ) {

		$test_mode  = $this->testmode;
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'insite' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$test_mode: ' . $test_mode );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '$selections ' . print_r( $selections, true ) );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' !== $test_mode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '$test_mode different to yes showing Gateway' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			return true;
		}
		if ( $selections[0] !== '' || empty( $selections ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '$selections NOT empty' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			if ( ! $userid ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', 'Not loged In hiding gateway' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				return false;
			}
			foreach ( $selections as $user_id ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '$user_id: ' . $user_id );
					$this->log->add( 'insite', '$userid: ' . $userid );
					$this->log->add( 'insite', ' ' );
				}
				if ( (int) $user_id === (int) $userid ) {

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' $user_id === $userid, Showing gateway' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					return true;
				}
				continue;
			}
			return false;
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/*********************************/' );
				$this->log->add( 'insite', '$selections Empty, showing gateway' );
				$this->log->add( 'insite', '/*********************************/' );
				$this->log->add( 'insite', ' ' );
			}
			return true;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function show_payment_method( $available_gateways ) {

		if ( ! is_admin() ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '   Is NOT admin ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			if ( is_user_logged_in() ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '   Is user logget in ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$user_id = get_current_user_id();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '   $user_id: ' . $user_id );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$show = $this->check_user_show_payment_method( $user_id );
				if ( 'yes' === $this->debug ) {
					if ( $show ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '   SHOW Gateway' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					} else {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '   DONT SHOW Gateway' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '   $user_id: ' . $user_id );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				if ( ! $show ) {
					unset( $available_gateways[ $this->id ] );
				}
			} else {
				$show = $this->check_user_show_payment_method();
				if ( ! $show ) {
					unset( $available_gateways[ $this->id ] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function hide_payment_method_add_method( $available_gateways ) {

		if ( ! is_admin() && is_account_page() ) {
			unset( $available_gateways[ $this->id ] );
		}
		return $available_gateways;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_user_test_mode( $userid = false ) {

		if ( ! $userid ) {
			return false;
		}
		$usertest_active = $this->testforuser;
		$selections      = (array) WCRed()->get_redsys_option( 'testforuserid', 'insite' );
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
						$this->log->add( 'insite', '   Checking user ' . $userid );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '  User in forach ' . $user_id );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function update_checkout_on_change( $fields ) {

		$customfieldname = $this->customfieldname;
		if ( ! empty( $customfieldname ) || '' !== $customfieldname ) {
			$fields['billing'][ $customfieldname ]['class'][] = 'update_totals_on_change';
		} else {
			$fields['billing']['billing_first_name']['class'][] = 'update_totals_on_change';
		}
		return $fields;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function doing_scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$order_id    = $renewal_order->get_id();
		$redsys_done = get_post_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '       Once upon a time       ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', '       Payment is complete EXIT          ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', '  The final has come, this story has ended  ' );
				$this->log->add( 'insite', '/******************************************/' );
			}
			return;
		} else {

			$order  = $renewal_order;
			$amount = $amount_to_charge;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/**********************************************/' );
				$this->log->add( 'insite', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'insite', '/**********************************************/' );
				$this->log->add( 'insite', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', '   scheduled charge Amount: ' . $amount );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', ' ' );
			}

			$order_total_sign    = '';
			$transaction_id2     = '';
			$transaction_type    = '';
			$DSMerchantTerminal  = '';
			$final_notify_url    = '';
			$returnfromredsys    = '';
			$gatewaylanguage     = '';
			$currency            = '';
			$secretsha256        = '';
			$customer            = '';
			$url_ok              = '';
			$product_description = '';
			$merchant_name       = '';

			$order_id = $order->get_id();
			$user_id  = $order->get_user_id();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '  Generating Tokenized call   ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_id: ' . $order_id );
				$this->log->add( 'insite', '$user_id: ' . $user_id );
				$this->log->add( 'insite', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway_ws( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'insite', ' ' );
			}

			// $order_id = $order->get_id();.
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'insite', ' ' );
			}

			$transaction_type = '0';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$transaction_type: ' . $transaction_type );
			}

			if ( $this->wooredsysurlko ) {
				if ( 'returncancel' === $this->wooredsysurlko ) {
					$returnfromredsys = $order->get_cancel_order_url();
				} else {
					$returnfromredsys = wc_get_checkout_url();
				}
			} else {
				$returnfromredsys = $order->get_cancel_order_url();
			}
			if ( 'yes' === $this->useterminal2 ) {
				$toamount  = number_format( $this->toamount, 2, '', '' );
				$terminal  = $this->terminal;
				$terminal2 = $this->terminal2;
				if ( $order_total_sign <= $toamount ) {
					$DSMerchantTerminal = $terminal2;
				} else {
					$DSMerchantTerminal = $terminal;
				}
			} else {
				$DSMerchantTerminal = $this->terminal;
			}

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			if ( 'yes' === $this->psd2 ) {
				$customer_token = WCRed()->get_users_token_bulk( $user_id, 'R' );
				$txnid          = WCRed()->get_txnid( $customer_token );
			} else {
				$customer_token = WCRed()->get_users_token_bulk( $user_id );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$customer_token: ' . $customer_token );
				$this->log->add( 'insite', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'insite', ' ' );
			}

			$currency            = $currency_codes[ get_woocommerce_currency() ];
			$secretsha256        = $this->get_redsys_sha256( $user_id );
			$customer            = $this->customer;
			$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
			$product_description = WCRed()->product_description( $order, 'redsys' );
			$merchant_name       = $this->commercename;

			$redsys_data_send = array(
				'order_total_sign'    => $order_total_sign,
				'transaction_id2'     => $transaction_id2,
				'transaction_type'    => $transaction_type,
				'DSMerchantTerminal'  => $DSMerchantTerminal,
				'final_notify_url'    => $final_notify_url,
				'returnfromredsys'    => $returnfromredsys,
				'gatewaylanguage'     => $gatewaylanguage,
				'currency'            => $currency,
				'secretsha256'        => $secretsha256,
				'customer'            => $customer,
				'url_ok'              => $url_ok,
				'product_description' => $product_description,
				'merchant_name'       => $merchant_name,
			);

			if ( has_filter( 'redsys_modify_data_to_send' ) ) {

				$redsys_data_send = apply_filters( 'redsys_modify_data_to_send', $redsys_data_send );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'insite', ' ' );
				}
			}

			$secretsha256     = $redsys_data_send['secretsha256'];
			$order_total_sign = $redsys_data_send['order_total_sign'];
			$orderid2         = $redsys_data_send['transaction_id2'];
			$customer         = $redsys_data_send['customer'];
			$currency         = $redsys_data_send['currency'];
			$transaction_type = $redsys_data_send['transaction_type'];
			$terminal         = $redsys_data_send['DSMerchantTerminal'];
			$final_notify_url = $redsys_data_send['final_notify_url'];
			$url_ok           = $redsys_data_send['url_ok'];
			$gatewaylanguage  = $redsys_data_send['gatewaylanguage'];
			$merchant_name    = $redsys_data_send['merchant_name'];
			$merchan_name     = get_post_meta( $order_id, '_billing_first_name', true );
			$merchant_lastnme = get_post_meta( $order_id, '_billing_last_name', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'insite', '$order: ' . $orderid2 );
				$this->log->add( 'insite', '$customer: ' . $customer );
				$this->log->add( 'insite', '$currency: ' . $currency );
				$this->log->add( 'insite', '$transaction_type: 0' );
				$this->log->add( 'insite', '$terminal: ' . $terminal );
				$this->log->add( 'insite', '$url_ok: ' . $url_ok );
				$this->log->add( 'insite', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'insite', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'insite', ' ' );
			}

			$miObj = new RedsysAPIWs();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}

			$DATOS_ENTRADA  = '<DATOSENTRADA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
			$DATOS_ENTRADA .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The call            ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $DATOS_ENTRADA );
				$this->log->add( 'insite', ' ' );
			}

			$XML  = '<REQUEST>';
			$XML .= $DATOS_ENTRADA;
			$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The XML             ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $XML );
				$this->log->add( 'insite', ' ' );
			}

			$CLIENTE    = new SoapClient( $redsys_adr );
			$responsews = $CLIENTE->iniciaPeticion( array( 'datoEntrada' => $XML ) );

			if ( isset( $responsews->iniciaPeticionReturn ) ) {
				$XML_RETORNO = new SimpleXMLElement( $responsews->iniciaPeticionReturn );
				$respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				// $this->log->add( 'insite', '$acctinfo: ' . $acctinfo );
				$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
			}

			$ds_emv3ds_json       = $XML_RETORNO->INFOTARJETA->Ds_EMV3DS;
			$ds_emv3ds            = json_decode( $ds_emv3ds_json );
			$protocolVersion      = $ds_emv3ds->protocolVersion;
			$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
			$threeDSInfo          = $ds_emv3ds->threeDSInfo;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
				$this->log->add( 'insite', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
				$this->log->add( 'insite', '$threeDSServerTransID: ' . $threeDSServerTransID );
				$this->log->add( 'insite', '$threeDSInfo: ' . $threeDSInfo );
			}

			if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

				$DATOS_ENTRADA  = '<DATOSENTRADA>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
				$DATOS_ENTRADA .= '</DATOSENTRADA>';
				$XML            = '<REQUEST>';
				$XML           .= $DATOS_ENTRADA;
				$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
				$XML           .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          The XML             ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', $XML );
					$this->log->add( 'insite', ' ' );
				}
				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->trataPeticionReturn ) ) {
					$XML_RETORNO       = new SimpleXMLElement( $responsews->trataPeticionReturn );
					$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
					$codigo            = json_decode( $XML_RETORNO->CODIGO );
					$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
					$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
					$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
					$this->log->add( 'insite', 'Ds_AuthorisationCode: ' . $authorisationcode );
				}
				if ( $authorisationcode ) {
					update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', 'payment_complete() 1' );
					}
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '      Saving Order Meta       ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}

					if ( ! empty( $redsys_order ) ) {
						update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '/******************************************/' );
						$this->log->add( 'insite', '  The final has come, this story has ended  ' );
						$this->log->add( 'insite', '/******************************************/' );
					}
					return true;
				} else {
					// TO-DO: Enviar un correo con el problema al administrador
					/**
						if ( ! WCRed()->is_paid( $order->get_id() ) ) {
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						}
					 */
				}
			} else {
				$protocolVersion = '1.0.2';
				$need            = wp_json_encode( $data );
				$acctinfo        = WCPSD2()->get_acctinfo( $order, $datos_usuario );
				$DATOS_ENTRADA   = '<DATOSENTRADA>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$DATOS_ENTRADA  .= $ds_merchant_group;
				$DATOS_ENTRADA  .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$DATOS_ENTRADA  .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
				// $DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
				// $DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
				// $DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
				$DATOS_ENTRADA .= '</DATOSENTRADA>';
				$XML            = '<REQUEST>';
				$XML           .= $DATOS_ENTRADA;
				$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
				$XML           .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '          The XML             ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', $XML );
					$this->log->add( 'insite', ' ' );
				}
				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->trataPeticionReturn ) ) {
					$XML_RETORNO = new SimpleXMLElement( $responsews->trataPeticionReturn );
					// $respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '$responsews: ' . print_r( $responsews, true ) );
					$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
				}
				$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
				$codigo            = json_decode( $XML_RETORNO->CODIGO );
				$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
				$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
				$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );

				if ( $authorisationcode ) {
					update_post_meta( $order_id, '_redsys_done', 'yes' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', 'payment_complete() 2' );
					}
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '      Saving Order Meta       ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
					}

					if ( ! empty( $redsys_order ) ) {
						update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'insite', ' ' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '/******************************************/' );
						$this->log->add( 'insite', '  The final has come, this story has ended  ' );
						$this->log->add( 'insite', '/******************************************/' );
					}
					return true;
				} else {
					// TO-DO: Enviar un correo con el problema al administrador
					/**
						if ( ! WCRed()->is_paid( $order->get_id() ) ) {
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						}
					 */
				}
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function renew_yith_subscription( $renewal_order = null, $is_manual_renew = null ) {

		$order_id         = $renewal_order->get_id();
		$amount_to_charge = $renewal_order->get_total();
		$redsys_done      = get_post_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '       Once upon a time       ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'insite', '/***************************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', '       Payment is complete EXIT          ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', '  The final has come, this story has ended  ' );
				$this->log->add( 'insite', '/******************************************/' );
			}
			return;
		} else {

			$order  = $renewal_order;
			$amount = $amount_to_charge;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/**********************************************/' );
				$this->log->add( 'insite', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'insite', '/**********************************************/' );
				$this->log->add( 'insite', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', '   scheduled charge Amount: ' . $amount );
				$this->log->add( 'insite', '/***************************************/' );
				$this->log->add( 'insite', ' ' );
			}

			$order_total_sign    = '';
			$transaction_id2     = '';
			$transaction_type    = '';
			$DSMerchantTerminal  = '';
			$final_notify_url    = '';
			$returnfromredsys    = '';
			$gatewaylanguage     = '';
			$currency            = '';
			$secretsha256        = '';
			$customer            = '';
			$url_ok              = '';
			$product_description = '';
			$merchant_name       = '';

			$order_id = $order->get_id();
			$user_id  = $order->get_user_id();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '  Generating Tokenized call   ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_id: ' . $order_id );
				$this->log->add( 'insite', '$user_id: ' . $user_id );
				$this->log->add( 'insite', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway_ws( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'insite', ' ' );
			}

			// $order_id = $order->get_id();.
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'insite', ' ' );
			}

			$transaction_type = '0';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$transaction_type: ' . $transaction_type );
			}

			if ( $this->wooredsysurlko ) {
				if ( 'returncancel' === $this->wooredsysurlko ) {
					$returnfromredsys = $order->get_cancel_order_url();
				} else {
					$returnfromredsys = wc_get_checkout_url();
				}
			} else {
				$returnfromredsys = $order->get_cancel_order_url();
			}
			if ( 'yes' === $this->useterminal2 ) {
				$toamount  = number_format( $this->toamount, 2, '', '' );
				$terminal  = $this->terminal;
				$terminal2 = $this->terminal2;
				if ( $order_total_sign <= $toamount ) {
					$DSMerchantTerminal = $terminal2;
				} else {
					$DSMerchantTerminal = $terminal;
				}
			} else {
				$DSMerchantTerminal = $this->terminal;
			}

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			if ( 'yes' === $this->psd2 ) {
				$customer_token = WCRed()->get_users_token_bulk( $user_id, 'R' );
				$txnid          = WCRed()->get_txnid( $customer_token );
			} else {
				$customer_token = WCRed()->get_users_token_bulk( $user_id );
			}

			if ( ! $customer_token ) {
				if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
					ywsbs_register_failed_payment( $renewal_order, 'Error: No user token' );
				}
				return false;
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$customer_token: ' . $customer_token );
				$this->log->add( 'insite', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'insite', ' ' );
			}

			$currency            = $currency_codes[ get_woocommerce_currency() ];
			$secretsha256        = $this->get_redsys_sha256( $user_id );
			$customer            = $this->customer;
			$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
			$product_description = WCRed()->product_description( $order, 'redsys' );
			$merchant_name       = $this->commercename;

			$redsys_data_send = array(
				'order_total_sign'    => $order_total_sign,
				'transaction_id2'     => $transaction_id2,
				'transaction_type'    => $transaction_type,
				'DSMerchantTerminal'  => $DSMerchantTerminal,
				'final_notify_url'    => $final_notify_url,
				'returnfromredsys'    => $returnfromredsys,
				'gatewaylanguage'     => $gatewaylanguage,
				'currency'            => $currency,
				'secretsha256'        => $secretsha256,
				'customer'            => $customer,
				'url_ok'              => $url_ok,
				'product_description' => $product_description,
				'merchant_name'       => $merchant_name,
			);

			if ( has_filter( 'redsys_modify_data_to_send' ) ) {

				$redsys_data_send = apply_filters( 'redsys_modify_data_to_send', $redsys_data_send );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'insite', ' ' );
				}
			}

			$secretsha256     = $redsys_data_send['secretsha256'];
			$order_total_sign = $redsys_data_send['order_total_sign'];
			$orderid2         = $redsys_data_send['transaction_id2'];
			$customer         = $redsys_data_send['customer'];
			$currency         = $redsys_data_send['currency'];
			$transaction_type = $redsys_data_send['transaction_type'];
			$terminal         = $redsys_data_send['DSMerchantTerminal'];
			$final_notify_url = $redsys_data_send['final_notify_url'];
			$url_ok           = $redsys_data_send['url_ok'];
			$gatewaylanguage  = $redsys_data_send['gatewaylanguage'];
			$merchant_name    = $redsys_data_send['merchant_name'];
			$merchan_name     = get_post_meta( $order_id, '_billing_first_name', true );
			$merchant_lastnme = get_post_meta( $order_id, '_billing_last_name', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'insite', '$order: ' . $orderid2 );
				$this->log->add( 'insite', '$customer: ' . $customer );
				$this->log->add( 'insite', '$currency: ' . $currency );
				$this->log->add( 'insite', '$transaction_type: 0' );
				$this->log->add( 'insite', '$terminal: ' . $terminal );
				$this->log->add( 'insite', '$url_ok: ' . $url_ok );
				$this->log->add( 'insite', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'insite', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'insite', ' ' );
			}

			$miObj = new RedsysAPIWs();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}

			if ( 'yes' === $this->psd2 ) {
				$datos_usuario = array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocolVersion,
					'browserAcceptHeader' => $http_accept,
					'browserColorDepth'   => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'           => $browserIP,
					'browserJavaEnabled'  => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserLanguage'     => WCPSD2()->get_idioma_navegador( $order_id ),
					'browserScreenHeight' => WCPSD2()->get_altura_pantalla( $order_id ),
					'browserScreenWidth'  => WCPSD2()->get_anchura_pantalla( $order_id ),
					'browserTZ'           => WCPSD2()->get_diferencia_horaria( $order_id ),
					'browserUserAgent'    => WCPSD2()->get_agente_navegador( $order_id ),
					'notificationURL'     => $final_notify_url,
				);
				// $acctinfo       = WCPSD2()->get_acctinfo( $order, false , $user_id );
				$DATOS_ENTRADA  = '<DATOSENTRADA>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
				$DATOS_ENTRADA .= '</DATOSENTRADA>';
			} else {
				$DATOS_ENTRADA  = '<DATOSENTRADA>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$DATOS_ENTRADA .= $ds_merchant_group;
				$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>true</DS_MERCHANT_DIRECTPAYMENT>';
				$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
				// $DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
				$DATOS_ENTRADA .= '</DATOSENTRADA>';
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The call            ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $DATOS_ENTRADA );
				$this->log->add( 'insite', ' ' );
			}

			$XML  = '<REQUEST>';
			$XML .= $DATOS_ENTRADA;
			$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The XML             ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $XML );
				$this->log->add( 'insite', ' ' );
			}

			if ( 'yes' === $this->psd2 ) {

				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->iniciaPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->iniciaPeticionReturn ) ) {
					$XML_RETORNO = new SimpleXMLElement( $responsews->iniciaPeticionReturn );
					$respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					// $this->log->add( 'insite', '$acctinfo: ' . $acctinfo );
					$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
				}

				$ds_emv3ds_json       = $XML_RETORNO->INFOTARJETA->Ds_EMV3DS;
				$ds_emv3ds            = json_decode( $ds_emv3ds_json );
				$protocolVersion      = $ds_emv3ds->protocolVersion;
				$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
				$threeDSInfo          = $ds_emv3ds->threeDSInfo;

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'insite', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
					$this->log->add( 'insite', '$threeDSServerTransID: ' . $threeDSServerTransID );
					$this->log->add( 'insite', '$threeDSInfo: ' . $threeDSInfo );
				}

				if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

					$DATOS_ENTRADA  = '<DATOSENTRADA>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					// $DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
					$DATOS_ENTRADA .= '</DATOSENTRADA>';
					$XML            = '<REQUEST>';
					$XML           .= $DATOS_ENTRADA;
					$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
					$XML           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '          The XML             ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', $XML );
						$this->log->add( 'insite', ' ' );
					}
					$CLIENTE    = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

					if ( isset( $responsews->trataPeticionReturn ) ) {
						$XML_RETORNO       = new SimpleXMLElement( $responsews->trataPeticionReturn );
						$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
						$codigo            = json_decode( $XML_RETORNO->CODIGO );
						$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
						$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
						$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
						$this->log->add( 'insite', 'Ds_AuthorisationCode: ' . $authorisationcode );
					}
					if ( $authorisationcode ) {
						update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', 'payment_complete() 3' );
						}
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', '      Saving Order Meta       ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '/******************************************/' );
							$this->log->add( 'insite', '  The final has come, this story has ended  ' );
							$this->log->add( 'insite', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
								ywsbs_register_failed_payment( $renewal_order, 'Error' );
							}
							return false;
						} else {
							return true;
						}
					}
				} else {
					$protocolVersion = '1.0.2';
					$acctinfo        = WCPSD2()->get_acctinfo( $order, $datos_usuario );
					$DATOS_ENTRADA   = '<DATOSENTRADA>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$DATOS_ENTRADA  .= $ds_merchant_group;
					$DATOS_ENTRADA  .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$DATOS_ENTRADA  .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					$DATOS_ENTRADA  .= '</DATOSENTRADA>';
					$XML             = '<REQUEST>';
					$XML            .= $DATOS_ENTRADA;
					$XML            .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$XML            .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
					$XML            .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', '          The XML             ' );
						$this->log->add( 'insite', '/****************************/' );
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', $XML );
						$this->log->add( 'insite', ' ' );
					}
					$CLIENTE    = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

					if ( isset( $responsews->trataPeticionReturn ) ) {
						$XML_RETORNO = new SimpleXMLElement( $responsews->trataPeticionReturn );
						// $respuesta = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '$responsews: ' . print_r( $responsews, true ) );
						$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
					}
					$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
					$codigo            = json_decode( $XML_RETORNO->CODIGO );
					$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
					$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
					$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );

					if ( $authorisationcode ) {
						update_post_meta( $order_id, '_redsys_done', 'yes' );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', 'payment_complete() 4' );
						}
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', '      Saving Order Meta       ' );
							$this->log->add( 'insite', '/****************************/' );
							$this->log->add( 'insite', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'insite', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', '/******************************************/' );
							$this->log->add( 'insite', '  The final has come, this story has ended  ' );
							$this->log->add( 'insite', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
								ywsbs_register_failed_payment( $renewal_order, 'Error: No user token' );
							}
							return false;
						} else {
							return true;
						}
					}
				}
			} else {
				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->trataPeticionReturn ) ) {
					$XML_RETORNO = new SimpleXMLElement( $responsews->trataPeticionReturn );
					if ( isset( $XML_RETORNO->CODIGO ) ) {
						if ( '0' === (string) $XML_RETORNO->CODIGO ) {
							if ( isset( $XML_RETORNO->OPERACION->Ds_Response ) ) {
								$RESPUESTA = (int) $XML_RETORNO->OPERACION->Ds_Response;
								if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'insite', ' ' );
										$this->log->add( 'insite', 'Response: Ok > ' . $RESPUESTA );
										$this->log->add( 'insite', ' ' );
									}
									update_post_meta( $order_id, '_redsys_done', 'yes' );
								} else {
									// Ha habido un problema en el cobro
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'insite', ' ' );
										$this->log->add( 'insite', 'Response: Error > ' . $RESPUESTA );
										$this->log->add( 'insite', ' ' );
									}
									if ( ! WCRed()->is_paid( $order->get_id() ) ) {
										$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . $RESPUESTA );
										$renewal_order->update_status( 'failed' );
									}
								}
							} else {
								// No hay $XML_RETORNO->OPERACION->Ds_Response
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'insite', ' ' );
									$this->log->add( 'insite', 'Error > No hay $XML_RETORNO->OPERACION->Ds_Response' );
									$this->log->add( 'insite', ' ' );
								}
								if ( ! WCRed()->is_paid( $order->get_id() ) ) {
									$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
									$renewal_order->update_status( 'failed' );
								}
							}
						} else {
							// $XML_RETORNO->CODIGO es diferente a 0
							$error_code = WCRed()->get_error_by_code( (string) $XML_RETORNO->CODIGO );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', 'Error > $XML_RETORNO->CODIGO es diferente a 0. Error: ' . (string) $XML_RETORNO->CODIGO . '->' . $error_code );
								$this->log->add( 'insite', ' ' );
							}
							if ( $error_code ) {
								// Enviamos email al adminsitrador avisando de este problema
								$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . (string) $XML_RETORNO->CODIGO . ': ' . $error_code );
								$to      = get_bloginfo( 'admin_email' );
								$subject = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
								$body    = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
								$body    = __( 'The error was: ', 'woocommerce-redsys' );
								$body   .= $error_code;
								$headers = array( 'Content-Type: text/html; charset=UTF-8' );
								wp_mail( $to, $subject, $body, $headers );

							}
							if ( ! WCRed()->is_paid( $order->get_id() ) ) {
								$renewal_order->update_status( 'failed' );
							}
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'insite', ' ' );
								$this->log->add( 'insite', '/******************************************/' );
								$this->log->add( 'insite', '  The final has come, this story has ended  ' );
								$this->log->add( 'insite', '/******************************************/' );
								$this->log->add( 'insite', ' ' );
							}
						}
					} else {
						// No hay $XML_RETORNO->CODIGO
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', 'Error > No hay $XML_RETORNO->CODIGO' );
							$this->log->add( 'insite', ' ' );
						}
						if ( ! WCRed()->is_paid( $order->get_id() ) ) {
							$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', ' ' );
							$this->log->add( 'insite', '/******************************************/' );
							$this->log->add( 'insite', '  The final has come, this story has ended  ' );
							$this->log->add( 'insite', '/******************************************/' );
							$this->log->add( 'insite', ' ' );
						}
					}
				} else {
					// No hay $responsews->trataPeticionReturn
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', 'Error > No hay $responsews->trataPeticionReturn' );
						$this->log->add( 'insite', ' ' );
					}
					if ( ! WCRed()->is_paid( $order->get_id() ) ) {
						$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
						$renewal_order->update_status( 'failed' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '/******************************************/' );
						$this->log->add( 'insite', '  The final has come, this story has ended  ' );
						$this->log->add( 'insite', '/******************************************/' );
						$this->log->add( 'insite', ' ' );
					}
				}
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
		// $redirectok     = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order2 ) );
		// $minheigh       = $this->minheigh;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = '0';
		}
		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );
			// print_r( $_POST['post_data'] );
		}
		if ( ! WC()->cart->prices_include_tax ) {
			$order_total = WC()->cart->cart_contents_total;
		} else {
			$order_total = WC()->cart->cart_contents_total + WC()->cart->tax_total;
		}
		$order_total = WC()->cart->total;
		$the_card    = WC()->cart->get_cart();

		if ( isset( $post_data['billing_first_name'] ) ) {
			$billing_first_name = $post_data['billing_first_name'];
		} else {
			$billing_first_name = false;
		}

		$customfieldname = $this->customfieldname;
		if ( ! empty( $customfieldname ) || '' !== $customfieldname ) {
			if ( isset( $post_data[ $customfieldname ] ) ) {
				$billing_first_name = $post_data[ $customfieldname ];
			} else {
				$billing_first_name = false;
			}
		}

		if ( isset( $post_data['billing_last_name'] ) ) {
			$billing_last_name = $post_data['billing_last_name'];
		} else {
			$billing_last_name = false;
		}

		if ( empty( $billing_first_name ) || ! $billing_first_name || '' === $billing_first_name ) {
			echo '<legend>' . $this->description . '</legend><br />';
			$textnotfillfilds = $this->textnotfillfilds;
			if ( empty( $textnotfillfilds ) || ! $textnotfillfilds || '' === $textnotfillfilds ) {
				echo '<p> ' . __( 'Please fill in the billing fields of the checkout form. After filling them, the credit card form will appear.', 'woocommerce-redsys' ) . '</p>';
			} else {
				echo '<p> ' . $textnotfillfilds . '</p>';
			}
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

		$button_heigth = $this->button_heigth;

		if ( ! empty( $button_heigth ) ) {
			$button_heigth = $button_heigth;
		} else {
			$button_heigth = '85px';
		}

		$button_width = $this->button_width;

		if ( ! empty( $button_width ) ) {
			$button_width = $button_width;
		} else {
			$button_width = '100%';
		}

		if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			$http_accept = $_SERVER['HTTP_ACCEPT'];
		} else {
			$http_accept = 'false';
		}

		$token_type_needed = 'no';
		$need_token        = 'no';
		$there_are_tokens  = false;

		$nonce = wp_create_nonce( 'redsys_insite_nonce' );
		// update_post_meta( $order, '_payment_order_number_redsys', $orderId );
		$orderId = WCRed()->create_checkout_insite_number();

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
				.insite-unificado, .insite-unificado #card-form {
					height: 350px;
					margin-bottom: 35px;
				}
			</style>

			<div class="payment_method_insite">
				<fieldset class="card-saved">
                    <legend>
                        ' . $this->description . '
                    </legend>';
		if ( ( 'yes' === $this->pay1clic && is_user_logged_in() ) || 'R' === WCRed()->check_card_for_subscription( $the_card ) ) {
			$user_id           = get_current_user_id();
			$token_type_needed = WCRed()->check_card_for_subscription( $the_card );
			if ( WCRed()->check_tokens_exist( $user_id, $token_type_needed ) ) {
				$there_are_tokens = true;
				if ( 'R' === $token_type_needed ) {
					$need_token = 'yes';
				} else {
					$need_token = 'no';
				}
			} else {
				$there_are_tokens = false;
				if ( 'R' === $token_type_needed ) {
					$need_token = 'yes';
				} else {
					$need_token = 'no';
				}
			}
			if ( $there_are_tokens ) {
				echo '<div>';
				echo '<ul>';
				WCRed()->get_all_tokens_checkout( $user_id, $token_type_needed );
				echo '<input class="input-radio" type="radio" id="new" name="token" value="add" checked>';
				echo '<label for="new">' . __( 'Use a new payment method', 'woocommerce-redsys' ) . '</label>';
				echo '<input type="hidden" id="_redsys_token_type" name="_redsys_token_type" value="' . $token_type_needed . '"></>';
				echo '</ul>';
				echo '</div>';
			}

			// Debug line comment
			/*
						echo '$token_type_needed: ' . $token_type_needed . '<br />';
			echo '$need_token: ' . $need_token . '<br />';
			echo '$user_id: ' . $user_id . '<br />';
			if ( $there_are_tokens ) {
				echo '
					<input class="input-radio" type="radio" id="new" name="token" value="add" checked>
					<label for="new">' . __( 'Use a new payment method', 'woocommerce-redsys' ) . '</label>
					</ul>
				</div>';
			}
			*/
		}
		if ( 'intindepenelements' === $this->insitetype ) { // Integration by independent elements.
				   echo '
                </fieldset>
				<fieldset class="new-card-data">
					<div>
						<label class="cardinfo-label" for="card-number" autocomplete="cc-number">' . __( 'Credit Card Number', 'woocommerce-redsys' ) . '</label>
						<div class="input-wrap" id="card-number" autocomplete="cc-number"></div>
					</div>
					<div class="exp-date" >' . __( 'Expiration Date', 'woocommerce-redsys' ) . '</div>
					<div class="date-wrap">
						<div>
							<label class="cardinfo-label" for="expiration-date" autocomplete="cc-exp-month">' . __( 'Month (MM)', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="expiration-month" autocomplete="cc-exp-month"></div>
						</div>
						<div>
							<label class="cardinfo-label" for="expiration-date2" autocomplete="cc-exp-year" >' . __( 'Year (YY)', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="expiration-year" autocomplete="cc-exp-year" ></div>
						</div>
						<div class="cvv-wrap">
							<label class="cardinfo-label" for="cvv" autocomplete="cc-csc">' . __( 'CVV', 'woocommerce-redsys' ) . '</label>
							<div class="input-wrap" id="cvv" autocomplete="cc-csc"></div>
						</div>
					</div>
				</fieldset>
				<div>';
		} else {
			// Campo unificado
			echo '
			<fieldset class="new-card-data insite-unificado">
				<div id="card-form"/></div>
			</fieldset>
			<div>';
		}
		if ( ( ( 'yes' === $this->pay1clic && is_user_logged_in() ) || 'no' !== $need_token ) ) {
			if ( 'no' === $need_token ) {
				echo '
						<div id="redsys_save_token">
							<label><input type="checkbox" id="_redsys_save_token" name="_redsys_save_token" onclick="redysTokenCheck(this);" value="yes"> ' . __( 'Save payment information to my account for future purchases.', 'woocommerce-redsys' ) . '</label>
						</div>';
			} else {
				echo '
							<div id="redsys_save_token">
								' . __( 'We need to store your credit card for future payments. It will be stored by our bank, so it is totally safe.', 'woocommerce-redsys' ) . '
								<input type="hidden" id="_redsys_save_token" name="_redsys_save_token" value="yes">
							</div>';
			}
		}
					echo '
					<div class="input-wrapper" id="redsys-submit"></div>
				</div>
				<input type="hidden" id="token" ></input>
				<input type="hidden" id="errorCode" ></input>
				<input type="hidden" name="_temp_redsys_order_number" id="_temp_redsys_order_number" value="' . $orderId . '"></input>
				<div class="clear"></div>
			</div>
			<script type="text/javascript">	
				console.log("Start" );
				var c = new Date();';
		if ( 'yes' === $need_token ) {
			echo '
					var save = "yes";';
		} else {
			echo '
					var save = "no";';
		}
		if ( 'no' === $need_token ) {
			echo '
					function redysTokenCheck( cb ) {
						if (cb.checked) {
							delete save;
							save = "yes";
							console.log("save:", save );
						} else {
							delete save;
							save = "no";
							console.log("save:", save );
						}
					}
					console.log("save:", save );';
		}
		if ( 'intindepenelements' === $this->insitetype ) { // Integration by independent elements.
				echo '
				// Listener
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
											"redsysnonce"  : "' . $nonce . '",
											"userAgent"    : navigator.userAgent,
											"language"     : navigator.language,
											"height"       : screen.height,
											"width"        : screen.width,
											"colorDepth"   : screen.colorDepth,
											"Timezone"     : c.getTimezoneOffset(),
											"http_accept"  : "' . $http_accept . '",
											"need_token"   : "' . $need_token . '",
											"token_needed" : "' . $token_type_needed . '",
											"saved"        : save
										},
										success: function(response) {
											console.log("response:", response, "type of:", typeof response);
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
					console.log("Error:", errorCode.value );
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
					"height: ' . $button_heigth . '; font-size: 1.41575em; width: ' . $button_width . '; vertical-align: middle; background-color:' . $colorbutton . '; color:' . $colortextbutton . '; border-width: 0px; cursor: pointer; ",
					"' . $buttontext . '",
					"' . $fuc . '",
					"' . $terminal . '",
					"' . $orderId . '"
				);
			</script>
			<style>
				#redsys-hosted-pay-button {height: ' . $button_heigth . '!important; }
			</style>';
		} else {
			// Campos unificados
			echo '
						<!-- Listener -->
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
													"redsysnonce"  : "' . $nonce . '",
													"userAgent"    : navigator.userAgent,
													"language"     : navigator.language,
													"height"       : screen.height,
													"width"        : screen.width,
													"colorDepth"   : screen.colorDepth,
													"Timezone"     : c.getTimezoneOffset(),
													"http_accept"  : "' . $http_accept . '",
													"need_token"   : "' . $need_token . '",
													"token_needed" : "' . $token_type_needed . '",
													"saved"        : save
												},
												success: function(response) {
													console.log("response:", response, "type of:", typeof response);
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
						});
						getInSiteForm(
							"card-form",
							"background-color:' . $colorbutton . '; color:' . $colortextbutton . '",
							"color:' . $textcolor . ';",
							"color:' . $colorfieldtext . ';",
							";",
							"' . $buttontext . '",
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
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_the_ip() {

		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function pay_with_token_r( $order_id, $token_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', 'There is Token R: ' . $customer_token_r );
		}
		$order               = WCRed()->get_order( $order_id );
		$customer_token      = WCRed()->get_token_by_id( $token_id );
		$cof_txnid           = WCRed()->get_txnid( $customer_token );
		$miObj               = new RedsysAPIWs();
		$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
		$orderid2            = WCRed()->prepare_order_number( $order_id );
		$user_id             = $order->get_user_id();
		$customer            = $this->customer;
		$transaction_type    = '0';
		$currency_codes      = WCRed()->get_currencies();
		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$cof_ini             = 'N';
		$cof_type            = 'R';
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, 'redsys' );
		$merchant_name       = $this->commercename;
		$type                = 'ws';
		$redsys_adr          = $this->get_redsys_url_gateway_ws( $user_id, $type );
		$terminal            = $this->terminal;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'insite', '$orderid2: ' . $orderid2 );
			$this->log->add( 'insite', '$user_id: ' . $user_id );
			$this->log->add( 'insite', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'insite', '$currency: ' . $currency );
			$this->log->add( 'insite', '$cof_ini: ' . $cof_ini );
			$this->log->add( 'insite', '$cof_type: ' . $cof_type );
			$this->log->add( 'insite', '$cof_txnid: ' . $cof_txnid );
			$this->log->add( 'insite', '$product_description: ' . $product_description );
			$this->log->add( 'insite', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'insite', '$url_ok: ' . $url_ok );
			$this->log->add( 'insite', '$merchant_name: ' . $merchant_name );
			$this->log->add( 'insite', '$type: ' . $type );
			$this->log->add( 'insite', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'insite', '$DSMerchantTerminal: ' . $DSMerchantTerminal );
			$this->log->add( 'insite', ' ' );
		}

		if ( '000' === $order_total_sign || '0' === $order_total_sign || 0 === $order_total_sign ) {
			return true;
		}

		$DATOS_ENTRADA  = '<DATOSENTRADA>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
		$DATOS_ENTRADA .= '</DATOSENTRADA>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '          The call            ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', $DATOS_ENTRADA );
			$this->log->add( 'insite', ' ' );
		}

		$XML  = '<REQUEST>';
		$XML .= $DATOS_ENTRADA;
		$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$XML .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
		$XML .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '          The XML             ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', $XML );
			$this->log->add( 'insite', ' ' );
		}

		$CLIENTE  = new SoapClient( $redsys_adr );
		$RESPONSE = $CLIENTE->iniciaPeticion( array( 'datoEntrada' => $XML ) );

		if ( isset( $RESPONSE->iniciaPeticionReturn ) ) {
			$XML_RETORNO = new SimpleXMLElement( $RESPONSE->iniciaPeticionReturn );
			$respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			// $this->log->add( 'insite', '$acctinfo: ' . $acctinfo );
			$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
		}

		$ds_emv3ds_json       = $XML_RETORNO->INFOTARJETA->Ds_EMV3DS;
		$ds_emv3ds            = json_decode( $ds_emv3ds_json );
		$protocolVersion      = $ds_emv3ds->protocolVersion;
		$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
		$threeDSInfo          = $ds_emv3ds->threeDSInfo;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
			$this->log->add( 'insite', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
			$this->log->add( 'insite', '$threeDSServerTransID: ' . $threeDSServerTransID );
			$this->log->add( 'insite', '$threeDSInfo: ' . $threeDSInfo );
		}

		if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'insite', 'protocolVersion: ' . $protocolVersion );
			}
			$DATOS_ENTRADA  = '<DATOSENTRADA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$DATOS_ENTRADA .= '</DATOSENTRADA>';
			$XML            = '<REQUEST>';
			$XML           .= $DATOS_ENTRADA;
			$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The XML             ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $XML );
				$this->log->add( 'insite', ' ' );
			}
			$CLIENTE  = new SoapClient( $redsys_adr );
			$RESPONSE = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

			if ( isset( $RESPONSE->trataPeticionReturn ) ) {
				$XML_RETORNO       = new SimpleXMLElement( $RESPONSE->trataPeticionReturn );
				$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
				$codigo            = json_decode( $XML_RETORNO->CODIGO );
				$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
				$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
				$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
				$this->log->add( 'insite', 'Ds_AuthorisationCode: ' . $authorisationcode );
			}
			if ( $authorisationcode ) {
				update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'payment_complete() 5' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      Saving Order Meta       ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}

				if ( ! empty( $redsys_order ) ) {
					update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $terminal ) ) {
					update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $currency_code ) ) {
					update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $secretsha256 ) ) {
					update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/******************************************/' );
					$this->log->add( 'insite', '  The final has come, this story has ended  ' );
					$this->log->add( 'insite', '/******************************************/' );
				}
				return true;
			} else {
				return false;
			}
		} else {
			$protocolVersion = '1.0.2';
			$data            = array(
				'threeDSInfo'     => 'AuthenticationData',
				'protocolVersion' => '1.0.2',
			);
			$need            = wp_json_encode( $data );
			$acctinfo        = WCPSD2()->get_acctinfo( $order, $datos_usuario );
			$DATOS_ENTRADA   = '<DATOSENTRADA>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA  .= $ds_merchant_group;
			$DATOS_ENTRADA  .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$DATOS_ENTRADA  .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			// $DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
			// $DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
			$DATOS_ENTRADA .= '</DATOSENTRADA>';
			$XML            = '<REQUEST>';
			$XML           .= $DATOS_ENTRADA;
			$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '          The XML             ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', $XML );
				$this->log->add( 'insite', ' ' );
			}
			$CLIENTE  = new SoapClient( $redsys_adr );
			$RESPONSE = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

			if ( isset( $RESPONSE->trataPeticionReturn ) ) {
				$XML_RETORNO = new SimpleXMLElement( $RESPONSE->trataPeticionReturn );
				// $respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$RESPONSE: ' . print_r( $RESPONSE, true ) );
				$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
			}
			$authorisationcode = json_decode( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );
			$codigo            = json_decode( $XML_RETORNO->CODIGO );
			$redsys_order      = json_decode( $XML_RETORNO->OPERACION->Ds_Order );
			$terminal          = json_decode( $XML_RETORNO->OPERACION->Ds_Terminal );
			$currency_code     = json_decode( $XML_RETORNO->OPERACION->Ds_Currency );

			if ( $authorisationcode ) {
				update_post_meta( $order_id, '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'payment_complete() 6' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      Saving Order Meta       ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}

				if ( ! empty( $redsys_order ) ) {
					update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $redsys_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $terminal ) ) {
					update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $currency_code ) ) {
					update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency_code );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $secretsha256 ) ) {
					update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/******************************************/' );
					$this->log->add( 'insite', '  The final has come, this story has ended  ' );
					$this->log->add( 'insite', '/******************************************/' );
				}
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function pay_with_token_c( $order_id, $token_id ) {

		$customer_token_c = WCRed()->get_token_by_id( $token_id );
		$order            = WCRed()->get_order( $order_id );
		$currency_codes   = WCRed()->get_currencies();

		// Pay with 1 clic & token exist.
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$customer_token_c exist' );
			$this->log->add( 'insite', '$customer_token_c: ' . $customer_token_c );
			$this->log->add( 'insite', ' ' );
		}

		$miObj               = new RedsysAPIWs();
		$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
		$orderid2            = WCRed()->prepare_order_number( $order_id );
		$user_id             = $order->get_user_id();
		$customer            = $this->customer;
		$transaction_type    = '0';
		$currency_codes      = WCRed()->get_currencies();
		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$cof_ini             = 'N';
		$cof_type            = 'C';
		$cof_txnid           = WCRed()->get_txnid( $customer_token_c );
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, 'redsys' );
		$merchant_name       = $this->commercename;
		$type                = 'ws';
		$redsys_adr          = $this->get_redsys_url_gateway_ws( $user_id, $type );
		$http_accept         = get_post_meta( $order_id, '_accept_haders' );
		$DSMerchantTerminal  = $this->terminal;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'insite', '$orderid2: ' . $orderid2 );
			$this->log->add( 'insite', '$user_id: ' . $user_id );
			$this->log->add( 'insite', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'insite', '$currency: ' . $currency );
			$this->log->add( 'insite', '$cof_ini: ' . $cof_ini );
			$this->log->add( 'insite', '$cof_type: ' . $cof_type );
			$this->log->add( 'insite', '$cof_txnid: ' . $cof_txnid );
			$this->log->add( 'insite', '$product_description: ' . $product_description );
			$this->log->add( 'insite', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'insite', '$url_ok: ' . $url_ok );
			$this->log->add( 'insite', '$merchant_name: ' . $merchant_name );
			$this->log->add( 'insite', '$type: ' . $type );
			$this->log->add( 'insite', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'insite', '$DSMerchantTerminal: ' . $DSMerchantTerminal );
			$this->log->add( 'insite', 'Amount for use TRA: ' . $this->traamount );
			$this->log->add( 'insite', 'Amount to compare: ' . 100 * (int) $this->traamount );
			$this->log->add( 'insite', ' ' );
		}
		if ( $order_total_sign <= 3000 ) {
			$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
		} else {
			$lwv = '';
		}
		if ( 'yes' === $this->traactive && $order_total_sign > 3000 && $order_total_sign <= ( 100 * (int) $this->traamount ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', 'Using TRA' );
				$this->log->add( 'insite', ' ' );
			}
			$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
		}

		$DATOS_ENTRADA  = '<DATOSENTRADA>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $DSMerchantTerminal . '</DS_MERCHANT_TERMINAL>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$DATOS_ENTRADA .= $lwv;
		$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
		$DATOS_ENTRADA .= '</DATOSENTRADA>';

		$XML  = '<REQUEST>';
		$XML .= $DATOS_ENTRADA;
		$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$XML .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
		$XML .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
			$this->log->add( 'insite', '$XML: ' . $XML );
			$this->log->add( 'insite', ' ' );
		}

		$CLIENTE  = new SoapClient( $redsys_adr );
		$RESPONSE = $CLIENTE->iniciaPeticion( array( 'datoEntrada' => $XML ) );

		if ( isset( $RESPONSE->iniciaPeticionReturn ) ) {
			$XML_RETORNO = new SimpleXMLElement( $RESPONSE->iniciaPeticionReturn );
			$respuesta   = json_decode( $XML_RETORNO->INFOTARJETA->Ds_EMV3DS );
		}
		$protocolVersion      = '';
		$Ds_Card_PSD2         = '';
		$threeDSServerTransID = '';
		$threeDSInfo          = '';
		$threeDSMethodURL     = '';
		if ( isset( $respuesta->protocolVersion ) ) {
			$protocolVersion = trim( $respuesta->protocolVersion );
		}
		if ( isset( $XML_RETORNO->INFOTARJETA->Ds_Card_PSD2 ) ) {
			$Ds_Card_PSD2 = trim( $XML_RETORNO->INFOTARJETA->Ds_Card_PSD2 );
		}
		if ( isset( $respuesta->threeDSServerTransID ) ) {
			$threeDSServerTransID = trim( $respuesta->threeDSServerTransID );
		}
		if ( isset( $respuesta->threeDSInfo ) ) {
			$threeDSInfo = trim( $respuesta->threeDSInfo );
		}
		if ( isset( $respuesta->threeDSMethodURL ) ) {
			$threeDSMethodURL = trim( $respuesta->threeDSMethodURL );
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
			$this->log->add( 'insite', '$respuesta: ' . print_r( $respuesta, true ) );
			$this->log->add( 'insite', 'protocolVersion: ' . $protocolVersion );
			$this->log->add( 'insite', 'threeDSServerTransID: ' . $threeDSServerTransID );
			$this->log->add( 'insite', 'threeDSInfo: ' . $threeDSInfo );
			$this->log->add( 'insite', 'threeDSMethodURL: ' . $threeDSMethodURL );
			$this->log->add( 'insite', 'Ds_Card_PSD2: ' . $Ds_Card_PSD2 );
			$this->log->add( 'insite', ' ' );
		}

		if ( ( 'NO_3DS_v2' === $protocolVersion || ( '1.0.2' === $protocolVersion ) && 'Y' === $Ds_Card_PSD2 ) ) {
			// Es protocolo 1.0.2
			$protocolVersion = '1.0.2';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Es Protocolo NO_3DS_v2 (1.0.2) y PSD2' );
			}
			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			$browserIP     = $this->get_the_ip();
			$datos_usuario = array(
				'threeDSInfo'         => 'AuthenticationData',
				'protocolVersion'     => $protocolVersion,
				'browserAcceptHeader' => $http_accept,
				'browserColorDepth'   => WCPSD2()->get_profundidad_color( $order_id ),
				'browserIP'           => $browserIP,
				'browserJavaEnabled'  => WCPSD2()->get_browserjavaenabled( $order_id ),
				'browserLanguage'     => WCPSD2()->get_idioma_navegador( $order_id ),
				'browserScreenHeight' => WCPSD2()->get_altura_pantalla( $order_id ),
				'browserScreenWidth'  => WCPSD2()->get_anchura_pantalla( $order_id ),
				'browserTZ'           => WCPSD2()->get_diferencia_horaria( $order_id ),
				'browserUserAgent'    => WCPSD2()->get_agente_navegador( $order_id ),
				'notificationURL'     => $final_notify_url,
			);
			$needed        = wp_json_encode(
				array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocolVersion,
					'browserAcceptHeader' => $http_accept,
					'browserUserAgent'    => WCPSD2()->get_agente_navegador( $order_id ),
				)
			);
			$acctinfo      = WCPSD2()->get_acctinfo( $order, $datos_usuario );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$acctinfo: ' . $acctinfo );
			}
			if ( $order_total_sign <= 3000 ) {
				$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
			} else {
				$lwv = '';
			}
			if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int) $this->traamount ) && $order_total_sign > 3000 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', 'Using TRA' );
					$this->log->add( 'insite', ' ' );
				}
				$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
			}
			$DATOS_ENTRADA  = '<DATOSENTRADA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $DSMerchantTerminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA .= $lwv;
			$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
			$DATOS_ENTRADA .= '</DATOSENTRADA>';

			$XML  = '<REQUEST>';
			$XML .= $DATOS_ENTRADA;
			$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
				$this->log->add( 'insite', '$XML: ' . $XML );
				$this->log->add( 'insite', ' ' );
			}

			$CLIENTE  = new SoapClient( $redsys_adr );
			$RESPONSE = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

			if ( isset( $RESPONSE->trataPeticionReturn ) ) {
				$XML_RETORNO       = new SimpleXMLElement( $RESPONSE->trataPeticionReturn );
				$codigo            = trim( $XML_RETORNO->CODIGO );
				$respuestaeds      = json_decode( $XML_RETORNO->OPERACION->Ds_EMV3DS );
				$threeDSInfo       = trim( $respuestaeds->threeDSInfo );
				$protocolVersion   = trim( $respuestaeds->protocolVersion );
				$acsURL            = trim( $respuestaeds->acsURL );
				$PAReq             = trim( $respuestaeds->{ 'PAReq'} );
				$MD                = trim( $respuestaeds->MD );
				$authorisationcode = trim( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );

			}
			if ( 'yes' === $this->debug && ! $authorisationcode ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$RESPONSE: ' . print_r( $RESPONSE, true ) );
				$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
				$this->log->add( 'insite', '$codigo: ' . $codigo );
				$this->log->add( 'insite', '$respuesta: ' . print_r( $respuestaeds, true ) );
				$this->log->add( 'insite', 'protocolVersion: ' . $protocolVersion );
				if ( ! empty( $respuestaeds->threeDSServerTransID ) ) {
					$this->log->add( 'insite', 'threeDSServerTransID: ' . $respuestaeds->threeDSServerTransID );
				}
				$this->log->add( 'insite', 'threeDSInfo: ' . $threeDSInfo );
				if ( ! empty( $respuestaeds->threeDSMethodURL ) ) {
					$this->log->add( 'insite', 'threeDSMethodURL: ' . $respuestaeds->threeDSMethodURL );
				}
				$this->log->add( 'insite', 'Ds_Card_PSD2: ' . $Ds_Card_PSD2 );
				$this->log->add( 'insite', '$threeDSInfo: ' . $threeDSInfo );
				$this->log->add( 'insite', '$protocolVersion: ' . $protocolVersion );
				$this->log->add( 'insite', '$acsURL: ' . $acsURL );
				$this->log->add( 'insite', '$PAReq: ' . $PAReq );
				$this->log->add( 'insite', '$MD: ' . $MD );
				$this->log->add( 'insite', ' ' );
			}

			if ( 'ChallengeRequest' === $threeDSInfo ) {
				// hay challenge
				// Guardamos todo en transciends
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/***************/' );
					$this->log->add( 'insite', '  Hay Challenge  ' );
					$this->log->add( 'insite', '/***************/' );
				}
				set_transient( 'threeDSInfo_' . $order_id, $threeDSInfo, 300 );
				set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
				set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
				set_transient( 'PAReq_' . $order_id, $PAReq, 300 );
				set_transient( 'MD_' . $order_id, $MD, 300 );
				set_transient( $MD, $order_id, 300 );
				set_transient( 'amount_' . $MD, $order_total_sign, 300 );
				set_transient( 'order_' . $MD, $orderid2, 300 );
				set_transient( 'merchantcode_' . $MD, $customer, 300 );
				set_transient( 'terminal_' . $MD, $DSMerchantTerminal, 300 );
				set_transient( 'currency_' . $MD, $currency, 300 );
				set_transient( 'identifier_' . $MD, $customer_token_c, 300 );
				set_transient( 'cof_ini_' . $MD, $cof_ini, 300 );
				set_transient( 'cof_type_' . $MD, $cof_type, 300 );
				set_transient( 'cof_txnid_' . $MD, $cof_txnid, 300 );
				return 'ChallengeRequest';
			} elseif ( ! empty( $authorisationcode ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/***************/' );
					$this->log->add( 'insite', '  Paid  ' );
					$this->log->add( 'insite', '/***************/' );
				}
				$Ds_Order        = trim( $XML_RETORNO->OPERACION->Ds_Order );
				$Ds_MerchantCode = trim( $XML_RETORNO->OPERACION->Ds_MerchantCode );
				$Ds_Terminal     = trim( $XML_RETORNO->OPERACION->Ds_Terminal );
				update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'payment_complete() 7' );
				}
				$order->payment_complete();
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      Saving Order Meta       ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}

				if ( ! empty( $Ds_Order ) ) {
					update_post_meta( $order->get_id(), '_payment_order_number_redsys', $Ds_Order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $Ds_Order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $dsdate ) ) {
					update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_date_redsys saved: ' . $dsdate );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_date_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $Ds_Terminal ) ) {
					update_post_meta( $order->get_id(), '_payment_terminal_redsys', $Ds_Terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $Ds_Terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $dshour ) ) {
					update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_hour_redsys saved: ' . $dshour );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_hour_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $currency ) ) {
					update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				// This meta is essential for later use:
				if ( ! empty( $secretsha256 ) ) {
					update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				return 'success';
			}
		} elseif ( ( ( '2.1.0' === $protocolVersion ) || ( '2.2.0' === $protocolVersion ) ) && ( 'Y' === $Ds_Card_PSD2 ) ) {
			// Es protocolo 2.1.0
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Es Protocolo 2.1.0 y PSD2' );
			}

			$http_accept = WCPSD2()->get_accept_headers( $order_id );

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			$browserIP = $this->get_the_ip();

			set_transient( 'threeDSInfo_' . $order_id, $threeDSInfo, 300 );
			set_transient( 'accept_headers_' . $order_id, $http_accept, 300 );
			set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
			set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
			set_transient( 'threeDSServerTransID_' . $order_id, $threeDSServerTransID, 300 );
			set_transient( 'threeDSMethodURL_' . $order_id, $threeDSMethodURL, 300 );
			set_transient( 'amount_' . $order_id, $order_total_sign, 300 );
			set_transient( 'order_' . $order_id, $orderid2, 300 );
			set_transient( 'terminal_' . $order_id, $DSMerchantTerminal, 300 );
			set_transient( 'currency_' . $order_id, $currency, 300 );
			set_transient( 'identifier_' . $order_id, $customer_token_c, 300 );
			set_transient( 'cof_ini_' . $order_id, $cof_ini, 300 );
			set_transient( 'cof_type_' . $order_id, $cof_type, 300 );
			set_transient( 'cof_txnid_' . $order_id, $cof_txnid, 300 );
			set_transient( 'final_notify_url_' . $order_id, $final_notify_url, 300 );
			set_transient( $threeDSServerTransID, $order_id, 300 );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$threeDSServerTransID: ' . $threeDSServerTransID );
				$this->log->add( 'insite', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'insite', '$threeDSMethodURL: ' . $threeDSMethodURL );
			}
			$data     = array();
			$data     = array(
				'threeDSServerTransID'         => $threeDSServerTransID,
				'threeDSMethodNotificationURL' => $final_notify_url,
			);
			$json_pre = wp_json_encode( $data );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$json_pre: ' . $json_pre );
			}
			$json = base64_encode( $json_pre );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$json: ' . $json );
			}

			$body    = array(
				'threeDSMethodData' => $json,
			);
			$options = array(
				'method'  => 'POST',
				'header'  => array(
					'Content-type' => 'pplication/x-www-form-urlencoded',
				),
				'body'    => $body,
				'timeout' => 45,
			);
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$body: ' . print_r( $body, true ) );
				// $this->log->add( 'insite', '$options: ' . print_r( $options, true ) );
			}
			$response = wp_remote_post( $threeDSMethodURL, $options );
			if ( 'yes' === $this->debug ) {
				// $this->log->add( 'insite', '$response: ' . print_r( $response, true ) );
			}
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$response_body: ' . $response_body );
			}

			if ( strpos( $response_body, $final_notify_url ) !== false ) {
				$url = true;
			} else {
				$url = false;
			}
			if ( strpos( $response_body, $json ) !== false ) {
				$threeDSMethodDatatest = true;
			} else {
				$threeDSMethodDatatest = false;
			}
			if ( $url && $threeDSMethodDatatest ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'URL y threeDSMethodData coinciden' );
				}
				$threeDSCompInd = 'Y';
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'URL y threeDSMethodData NO coinciden' );
				}
				$threeDSCompInd = 'N';
			}

			if ( '2.2.0' === $protocolVersion ) {
				$datos_usuario = array(
					'threeDSInfo'              => 'AuthenticationData',
					'protocolVersion'          => $protocolVersion,
					'browserAcceptHeader'      => $http_accept,
					'browserColorDepth'        => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'                => $browserIP,
					'browserJavascriptEnabled' => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserJavaEnabled'       => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserLanguage'          => WCPSD2()->get_idioma_navegador( $order_id ),
					'browserScreenHeight'      => WCPSD2()->get_altura_pantalla( $order_id ),
					'browserScreenWidth'       => WCPSD2()->get_anchura_pantalla( $order_id ),
					'browserTZ'                => WCPSD2()->get_diferencia_horaria( $order_id ),
					'browserUserAgent'         => WCPSD2()->get_agente_navegador( $order_id ),
					'threeDSServerTransID'     => $threeDSServerTransID,
					'notificationURL'          => $final_notify_url,
					'threeDSCompInd'           => $threeDSCompInd,
				);
			} else {
				$datos_usuario = array(
					'threeDSInfo'          => 'AuthenticationData',
					'protocolVersion'      => $protocolVersion,
					'browserAcceptHeader'  => $http_accept,
					'browserColorDepth'    => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'            => $browserIP,
					'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserLanguage'      => WCPSD2()->get_idioma_navegador( $order_id ),
					'browserScreenHeight'  => WCPSD2()->get_altura_pantalla( $order_id ),
					'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla( $order_id ),
					'browserTZ'            => WCPSD2()->get_diferencia_horaria( $order_id ),
					'browserUserAgent'     => WCPSD2()->get_agente_navegador( $order_id ),
					'threeDSServerTransID' => $threeDSServerTransID,
					'notificationURL'      => $final_notify_url,
					'threeDSCompInd'       => $threeDSCompInd,
				);
			}
			$acctinfo = WCPSD2()->get_acctinfo( $order, $datos_usuario, $user_id );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$user_id: ' . $user_id );
				$this->log->add( 'insite', '$order_id: ' . $order_id );
				$this->log->add( 'insite', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'insite', 'protocolVersion: ' . $protocolVersion );
				$this->log->add( 'insite', 'threeDSServerTransID: ' . $threeDSServerTransID );
				$this->log->add( 'insite', 'notificationURL: ' . $final_notify_url );
				$this->log->add( 'insite', 'threeDSCompInd: ' . $threeDSCompInd );
				$this->log->add( 'insite', 'acctInfo: : ' . $acctinfo );
			}
			$order_total_sign   = get_transient( 'amount_' . $order_id );
			$orderid2           = get_transient( 'order_' . $order_id );
			$customer           = $this->customer;
			$DSMerchantTerminal = get_transient( 'terminal_' . $order_id );
			$currency           = get_transient( 'currency_' . $order_id );
			$customer_token_c   = get_transient( 'identifier_' . $order_id );
			$cof_ini            = get_transient( 'cof_ini_' . $order_id );
			$cof_type           = get_transient( 'cof_type_' . $order_id );
			$cof_txnid          = get_transient( 'cof_txnid_' . $order_id );

			if ( $order_total_sign <= 3000 ) {
				$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
			} else {
				$lwv = '';
			}
			if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int) $this->traamount ) && $order_total_sign > 3000 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', 'Using TRA' );
					$this->log->add( 'insite', ' ' );
				}
				$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
			}
			$miObj = new RedsysAPIWs();

			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}

			$secretsha256   = $this->get_redsys_sha256( $user_id );
			$DATOS_ENTRADA  = '<DATOSENTRADA>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $DSMerchantTerminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA .= $ds_merchant_group;
			$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA .= $lwv;
			$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
			$DATOS_ENTRADA .= '</DATOSENTRADA>';
			$XML            = '<REQUEST>';
			$XML           .= $DATOS_ENTRADA;
			$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'The XML: ' . $XML );
			}

			$CLIENTE  = new SoapClient( $redsys_adr );
			$RESPONSE = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

			if ( isset( $RESPONSE->trataPeticionReturn ) ) {
				$XML_RETORNO = new SimpleXMLElement( $RESPONSE->trataPeticionReturn );
			}
			$Ds_EMV3DS         = $XML_RETORNO->OPERACION->Ds_EMV3DS;
			$json_decode       = json_decode( $Ds_EMV3DS );
			$threeDSInfo       = $json_decode->threeDSInfo;
			$protocolVersion   = $json_decode->protocolVersion;
			$acsURL            = $json_decode->acsURL;
			$PAReq             = $json_decode->PAReq;
			$MD                = $json_decode->MD;
			$authorisationcode = trim( $XML_RETORNO->OPERACION->Ds_AuthorisationCode );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '$XML_RETORNO: ' . print_r( $XML_RETORNO, true ) );
				$this->log->add( 'insite', 'Ds_EMV3DS: ' . $Ds_EMV3DS );
				$this->log->add( 'insite', '$threeDSInfo: ' . $threeDSInfo );
				$this->log->add( 'insite', ' ' );
			}

			if ( 'ChallengeRequest' === $threeDSInfo ) {
				// hay challenge
				// Guardamos todo en transciends
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/***************/' );
					$this->log->add( 'insite', '  Hay Challenge  ' );
					$this->log->add( 'insite', '/***************/' );
				}
				set_transient( 'threeDSInfo_' . $order_id, $threeDSInfo, 300 );
				set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
				set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
				set_transient( 'PAReq_' . $order_id, $PAReq, 300 );
				set_transient( 'MD_' . $order_id, $MD, 300 );
				set_transient( $MD, $order_id, 300 );
				set_transient( 'amount_' . $MD, $order_total_sign, 300 );
				set_transient( 'order_' . $MD, $orderid2, 300 );
				set_transient( 'merchantcode_' . $MD, $customer, 300 );
				set_transient( 'terminal_' . $MD, $DSMerchantTerminal, 300 );
				set_transient( 'currency_' . $MD, $currency, 300 );
				set_transient( 'identifier_' . $MD, $customer_token_c, 300 );
				set_transient( 'cof_ini_' . $MD, $cof_ini, 300 );
				set_transient( 'cof_type_' . $MD, $cof_type, 300 );
				set_transient( 'cof_txnid_' . $MD, $cof_txnid, 300 );
				return 'ChallengeRequest';
			} elseif ( ! empty( $authorisationcode ) ) {
				// Pago directo sin challenge
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', '/***************/' );
					$this->log->add( 'insite', '  Paid  ' );
					$this->log->add( 'insite', '/***************/' );
				}
				$Ds_Order        = trim( $XML_RETORNO->OPERACION->Ds_Order );
				$Ds_MerchantCode = trim( $XML_RETORNO->OPERACION->Ds_MerchantCode );
				$Ds_Terminal     = trim( $XML_RETORNO->OPERACION->Ds_Terminal );
				update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'payment_complete() 8' );
				}
				$order->payment_complete();
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      Saving Order Meta       ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}

				if ( ! empty( $Ds_Order ) ) {
					update_post_meta( $order->get_id(), '_payment_order_number_redsys', $Ds_Order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_order_number_redsys saved: ' . $Ds_Order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $dsdate ) ) {
					update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_date_redsys saved: ' . $dsdate );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_date_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $Ds_Terminal ) ) {
					update_post_meta( $order->get_id(), '_payment_terminal_redsys', $Ds_Terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_terminal_redsys saved: ' . $Ds_Terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $dshour ) ) {
					update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_payment_hour_redsys saved: ' . $dshour );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_payment_hour_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				if ( ! empty( $currency ) ) {
					update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_corruncy_code_redsys saved: ' . $currency );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				// This meta is essential for later use:
				if ( ! empty( $secretsha256 ) ) {
					update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'insite', ' ' );
					}
				}
				return 'success';
			}
		}

	}
	/**
	 * Process the payment and return the result
	 *
	 * @access public
	 * @param int $order_id
	 * @return array
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function process_payment( $order_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', __( 'Next Step, Call', 'woocommerce-redsys' ) );
		}
		$ordermi        = get_post_meta( $order_id, '_temp_redsys_order_number', true );
		$order          = WCRed()->get_order( $order_id );
		$user_id        = $order->get_user_id();
		$rneeds_payment = get_transient( $ordermi . '_insite_needs_payment' );
		$secretsha256   = $this->get_redsys_sha256( $user_id );
		$redsys_adr     = $this->get_redsys_url_gateway( $user_id );
		$tokennum       = get_transient( $order_id . '_insite_use_token' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$ordermi: ' . $ordermi );
			$this->log->add( 'insite', '$user_id: ' . $user_id );
			$this->log->add( 'insite', '$rneeds_payment: ' . $rneeds_payment );
			$this->log->add( 'insite', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'insite', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'insite', '$tokennum: ' . $tokennum );
		}

		if ( 'no' !== $tokennum ) { // Using Token
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Using Token' );
			}
			if ( $order->get_total() > 0 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'Order bigger 0' );
				}
				$token_type = get_transient( $order_id . '_redsys_token_type' );
				if ( 'R' === $token_type ) {
					$result = $this->pay_with_token_r( $order_id, $tokennum );
					if ( $result ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', 'Pago mediante token CORRECTO' );
						}
						return array(
							'result'   => 'success',
							'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
						);
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'insite', 'Pago mediante token FALLIDO' );
						}
						wc_add_notice( 'We are having trouble charging the card, please try another one. ', 'error' );
					}
				} else {
					$result = $this->pay_with_token_c( $order_id, $tokennum );
					if ( 'success' === $result ) {
						return array(
							'result'   => 'success',
							'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
						);
					} elseif ( 'ChallengeRequest' === $result ) {
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ) . '#3DSform',
						);
					}
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'Token dont Needed, 0 card' );
				}
				return;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'NOT Using Token' );
			}
			$version       = get_transient( $ordermi . '_insite_version' );
			$params        = get_transient( $ordermi . '_insite_params' );
			$signature     = get_transient( $ordermi . '_insite_signature' );
			$response      = wp_remote_post(
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

			if ( ! empty( $result ) && $result->errorCode ) {
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
			if ( isset( $decodec_array->Ds_AuthorisationCode ) && ! empty( $decodec_array->Ds_AuthorisationCode ) ) {
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
					update_post_meta( $order_id, '_payment_date_redsys', $dsdate );
				}
				if ( ! empty( $dshour ) ) {
					update_post_meta( $order_id, '_payment_hour_redsys', $dshour );
				}
				if ( ! empty( $id_trans ) ) {
					update_post_meta( $order_id, '_authorisation_code_redsys', $authorisation_code );
				}
				if ( ! empty( $dscardcountry ) ) {
					update_post_meta( $order_id, '_card_country_insite', $dscardcountry );
				}
				if ( ! empty( $sha256 ) ) {
					update_post_meta( $order_id, '_order_sha256_insite', $sha256 );
				}
				$order = WCRed()->get_order( $order_id );
				// Payment completed
				$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'payment_complete() 9' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'Payment complete.' );
				}
				$order = new WC_Order( $order_id );
				return array(
					'result'   => 'success',
					'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
				);
			}

			if ( isset( $decodec_array->Ds_EMV3DS->acsURL ) ) { // Need verification

				if ( 'yes' === $rneeds_payment ) {
					update_post_meta( $order_id, '_redsystokenr', 'yes' );
				}

				$response = (int) $decodec_array->Ds_Response;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'La respuesta es $response: ' . $response );
				}
				$threedsinfo = $decodec_array->Ds_EMV3DS->threeDSInfo;
				if ( ! empty( $threedsinfo ) && 'ChallengeRequest' === $threedsinfo ) {
					if ( 'yes' === $this->debug ) {
						if ( ! empty( $redsys_insite ) ) {
							$this->log->add( 'insite', '$redsys_insite->secure3ds: ' . $redsys_insite->secure3ds );
						}
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
					// echo 'ChallengeRequest';
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', 'ChallengeRequest: TRUE' );
					}
					$order = new WC_Order( $order_id );
					return array(
						'result'   => 'success',
						'redirect' => $order->get_checkout_payment_url( true ),
					);
				}
			}

			$order = new WC_Order( $order_id );
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
			$redsys_insite->log->add( 'insite', 'Necesita token:' . $_POST['need_token'] );
			$redsys_insite->log->add( 'insite', 'Tipo de token:' . $_POST['token_needed'] );
			$redsys_insite->log->add( 'insite', 'Save:' . $_POST['saved'] );
		}

		$currency_codes    = WCRed()->get_currencies();
		$customer          = $redsys_insite->customer;
		$terminal          = $redsys_insite->terminal;
		$currency          = $currency_codes[ get_woocommerce_currency() ];
		$transaction_type  = '0';
		$final_notify_url  = $redsys_insite->notify_url;
		$redsys_token      = $_POST['token'];
		$order_id          = $_POST['order_id'];
		$amount            = $_POST['order_total'];
		$merchan_name      = $_POST['billing_first_name'];
		$merchant_lastnme  = $_POST['billing_last_name'];
		$user_id           = $_POST['user_id'];
		$usr_agent         = $_POST['userAgent'];
		$http_accept       = $_POST['http_accept'];
		$save              = $_POST['saved'];
		$redsys_amount     = WCRed()->redsys_amount_format( $amount );
		$secretsha256      = $redsys_insite->get_redsys_sha256( $user_id );
		$redsys_adr        = $redsys_insite->get_redsys_url_gateway( $user_id );
		$need_token        = $_POST['need_token'];
		$token_type_needed = $_POST['token_needed'];
		$merchant_module   = 'WooCommerce_Redsys_Gateway_' . REDSYS_VERSION . '_WooCommerce.com';
		$emv3ds            = '{"threeDSInfo":"AuthenticationData",
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
		set_transient( $order_id . '_insite_token_need', $token_type_needed, 46000 );

		if ( 0 === (int) $redsys_amount ) {
			set_transient( $order_id . '_insite_needs_payment', 'yes', 46000 );
		}

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
			$redsys_insite->log->add( 'insite', '$save: ' . $save );
		}

		$miObj = new RedsysAPI();

		$miObj->setParameter( 'DS_MERCHANT_MODULE', $merchant_module );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $customer );
		$miObj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$miObj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$miObj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$miObj->setParameter( 'DS_MERCHANT_AMOUNT', $redsys_amount );
		$miObj->setParameter( 'DS_MERCHANT_ORDER', $order_id );
		$miObj->setParameter( 'DS_MERCHANT_IDOPER', $redsys_token );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$miObj->setParameter( 'DS_MERCHANT_TITULAR', $merchan_name . ' ' . $merchant_lastnme );
		$miObj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', '3DS' );

		if ( 'yes' === $redsys_insite->pay1clic && ( 'yes' === $save || 'yes' === $need_token ) ) {
			if ( 'R' === $token_type_needed ) {
				$miObj->setParameter( 'Ds_Merchant_MerchantData', '0' );
				$miObj->setParameter( 'Ds_MERCHANT_IDENTIFIER', 'REQUIRED' );
				$miObj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
				$miObj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
				set_transient( $order_id . '_ds_merchant_cof_ini', 'R', 3600 );
			} else {
				$miObj->setParameter( 'Ds_Merchant_MerchantData', '0' );
				$miObj->setParameter( 'Ds_MERCHANT_IDENTIFIER', 'REQUIRED' );
				$miObj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
				$miObj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
				set_transient( $order_id . '_ds_merchant_cof_ini', 'C', 3600 );
			}
		}

		if ( (int) $redsys_amount < 3000 ) {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: LWV' );
			}
			$miObj->setParameter( 'DS_MERCHANT_EXCEP_SCA', 'LWV' );
		} elseif ( $redsys_amount <= ( 100 * (int) $redsys_insite->traamount ) && 'yes' === $redsys_insite->traactive ) {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: TRA' );
			}
			$miObj->setParameter( 'DS_MERCHANT_EXCEP_SCA', 'TRA' );
		} else {
			if ( 'yes' === $redsys_insite->debug ) {
				$redsys_insite->log->add( 'insite', 'Apply SCA: NO' );
			}
		}
		$miObj->setParameter( 'DS_MERCHANT_EMV3DS', json_decode( $emv3ds, true ) );
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function receipt_page( $order ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '       Once upon a time       ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '  Generating receipt_page     ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}

		$threeDSInfo       = get_transient( 'threeDSInfo_' . $order );
		$protocolVersion   = get_transient( 'protocolVersion_' . $order );
		$temp_order_number = get_post_meta( $order, '_temp_redsys_order_number', true );
		$do_challenge      = get_transient( $order . '_do_redsys_challenge' );

		if ( '2.1.0' === $protocolVersion ) {

			$threeDSServerTransID = get_transient( 'threeDSServerTransID_' . $order );
			$final_notify_url     = get_transient( 'final_notify_url_' . $order );
			$threeDSMethodURL     = get_transient( 'threeDSMethodURL_' . $order );
			$acsurl               = get_transient( $order . '_insite_acsurl' );
			$data                 = array();
			$data                 = array(
				'threeDSServerTransID'         => $threeDSServerTransID,
				'threeDSMethodNotificationURL' => $final_notify_url,
			);
			$json                 = base64_encode( wp_json_encode( $data ) );

			wc_enqueue_js(
				'$("body").block({
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
				jQuery("#submit_redsys_payment_form_3").click();
				'
			);
			echo '<form id="3DSform" method="POST" action="' . $threeDSMethodURL . '" target="_top">
				<input type="hidden" name="threeDSMethodData" value="' . $json . '" />
				<input type="submit" class="button-alt" id="submit_redsys_payment_form_3" value="' . __( 'Press here if you are not redirected', 'woocommerce-redsys' ) . '" />
				</form>';
		} elseif ( 'ChallengeRequest' === $threeDSInfo ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', '         Hay Challenge        ' );
				$this->log->add( 'insite', '/****************************/' );
				$this->log->add( 'insite', ' ' );
			}
			// Hay challenge
			$acsURL = trim( get_transient( 'acsURL_' . $order ) );
			$PAReq  = trim( get_transient( 'PAReq_' . $order ) );
			$MD     = trim( get_transient( 'MD_' . $order ) );
			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			wc_enqueue_js(
				'
		$("body").block({
			message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Bizum to make the payment.', 'woocommerce-redsys' ) . '",
			overlayCSS:
			{
				background: "#fff",
				opacity: 0.6
			},
			css: {
				padding:		20,
				textAlign:		"center",
				color:			"#555",
				border:			"3px solid #aaa",
				backgroundColor:"#fff",
				cursor:			"wait",
				lineHeight:		"32px"
			}
		});
	jQuery("#submit_redsys_payment_form_2").click();
	'
			);
			echo '<form action="' . esc_url( $acsURL ) . '" method="post" id="redsys_payment_form" target="_top">
		<input type="hidden" name="PaReq" value="' . esc_attr( $PAReq ) . '" />
		<input type="hidden" name="MD" value="' . esc_attr( $MD ) . '" />
		<input type="hidden" name="TermUrl" value="' . esc_attr( $final_notify_url ) . '" />
		<input type="submit" class="button-alt" id="submit_redsys_payment_form_2" value="' . __( 'Pay with Bizum', 'woocommerce-redsys' ) . '" />
	</form>';

		} else {

			add_post_meta( $order, '_order_number_redsys_woocommerce', $temp_order_number );
			set_transient( $temp_order_number . '_woocommrce_order_number_redsys', $order );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$temp_order_number: ' . $temp_order_number );
				$this->log->add( 'insite', '$do_challenge: ' . $do_challenge );
				$this->log->add( 'insite', '$_POST: ' . print_r( $_POST, true ) );
			}

			if ( isset( $_GET['returnfronredsys'] ) ) {

				if ( isset( $_POST['MD'] ) && isset( $_POST['PaRes'] ) ) {

					$fuc              = $this->customer;
					$currency_codes   = WCRed()->get_currencies();
					$terminal         = $this->terminal;
					$currency         = $currency_codes[ get_woocommerce_currency() ];
					$transaction_type = '0';
					// $final_notify_url = $this->notify_url;
					// $redsys_token     = esc_html( $_POST['token'] );
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
						$setOrder           = method_exists( $request, 'setOrder' );
						$setAmount          = method_exists( $request, 'setAmount' );
						$setCurrency        = method_exists( $request, 'setCurrency' );
						$setMerchant        = method_exists( $request, 'setMerchant' );
						$setTerminal        = method_exists( $request, 'setTerminal' );
						$setTransactionType = method_exists( $request, 'setTransactionType' );
						$addEmvParameters   = method_exists( $request, 'addEmvParameters' );
						$addEmvParameter    = method_exists( $request, 'addEmvParameter' );

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
					$request->addEmvParameters(
						array(
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

					$service = new ISAuthenticationService( $secretsha256, $entorno );
					$result  = $service->sendOperation( $request );

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
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '$_GET["challenge"]: is SET' );
					}
				} else {
					$challenge = 'no';
				}
				if ( 'yes' === $challenge || 'yes' === $do_challenge ) {
					$temp_order_number = get_post_meta( $order, '_temp_redsys_order_number', true );
					$order2            = WCRed()->get_order( $order );
					$redirectok        = $order2->get_checkout_payment_url( true ) . '&returnfronredsys=yes';
					$acsurl            = get_transient( $order . '_insite_acsurl' );
					$pareq             = get_transient( $order . '_insite_pareq' );
					$md                = get_transient( $order . '_insite_md' );

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '$temp_order_number: ' . $temp_order_number );
						$this->log->add( 'insite', '$order2: ' . $order2 );
						$this->log->add( 'insite', '$redirectok: ' . $redirectok );
						$this->log->add( 'insite', '$acsurl: ' . $acsurl );
						$this->log->add( 'insite', '$pareq: ' . $pareq );
						$this->log->add( 'insite', '$md: ' . $md );
					}

					wc_enqueue_js(
						'$("body").block({
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
			}
		}
	}

	/**
	 * Check redsys IPN validity
	 **/
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_ipn_request_is_valid() {
		global $woocommerce;

		if ( 'yes' == $this->debug ) {
			$this->log->add( 'insite', 'HTTP Notification received: ' . print_r( $_POST, true ) );
		}
		if ( isset( $_POST['Ds_SignatureVersion'] ) && isset( $_POST['Ds_MerchantParameters'] ) && isset( $_POST['Ds_Signature'] ) ) {
			$version     = $_POST['Ds_SignatureVersion'];
			$data        = $_POST['Ds_MerchantParameters'];
			$remote_sign = $_POST['Ds_Signature'];
			$miObj       = new RedsysAPI();
			$decodedata  = $miObj->decodeMerchantParameters( $data );
			// $localsecret        = $miObj->createMerchantSignatureNotif($usesecretsha256,$data);
			$ds_amount         = (int) $miObj->getParameter( 'Ds_Amount' );
			$ds_order          = $miObj->getParameter( 'Ds_Order' );
			$dscode            = $miObj->getParameter( 'Ds_MerchantCode' );
			$currency_code     = $miObj->getParameter( 'Ds_Currency' );
			$response          = $miObj->getParameter( 'Ds_Response' );
			$id_trans          = $miObj->getParameter( 'Ds_AuthorisationCode' );
			$dsdate            = $miObj->getParameter( 'Ds_Date' );
			$dshour            = $miObj->getParameter( 'Ds_Hour' );
			$dstermnal         = $miObj->getParameter( 'Ds_Terminal' );
			$dsmerchandata     = $miObj->getParameter( 'Ds_MerchantData' );
			$dssucurepayment   = $miObj->getParameter( 'Ds_SecurePayment' );
			$dscardcountry     = $miObj->getParameter( 'Ds_Card_Country' );
			$dsconsumercountry = $miObj->getParameter( 'Ds_ConsumerLanguage' );
			$dscargtype        = $miObj->getParameter( 'Ds_Card_Type' );
		} else {
			$ds_order  = $_POST['Ds_Order'];
			$ds_amount = (int) $_POST['Ds_Amount'];
		}
		if ( 900 === intval( $response ) ) {
			return true;
		}
		$order_id = get_transient( $ds_order . '_woocommrce_order_number_redsys' );
		$order    = WCRed()->get_order( $order_id );
		$amount   = (int) WCRed()->redsys_amount_format( $order->get_total() );
		if ( 'yes' == $this->debug ) {
			$this->log->add( 'insite', '$ds_order: ' . $ds_order );
			$this->log->add( 'insite', '$order_id: ' . $order_id );
			// $this->log->add( 'insite', '$order: ' . $order );
			$this->log->add( 'insite', '$ds_amount: ' . $ds_amount );
			$this->log->add( 'insite', '$amount: ' . $amount );
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
		// $usesecretsha256 = $this->secretsha256;
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		if ( $usesecretsha256 ) {
			$version     = $_POST['Ds_SignatureVersion'];
			$data        = $_POST['Ds_MerchantParameters'];
			$remote_sign = $_POST['Ds_Signature'];
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_confirm_pares( $post ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '           Is PaRes           ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}
		$pares              = sanitize_text_field( $_POST['PaRes'] );
		$md                 = sanitize_text_field( $_POST['MD'] );
		$order_id           = get_transient( $md );
		$order              = WCRed()->get_order( $order_id );
		$user_id            = $order->get_user_id();
		$type               = 'ws';
		$redsys_adr         = $this->get_redsys_url_gateway_ws( $user_id, $type );
		$order_total_sign   = get_transient( 'amount_' . $md );
		$orderid2           = get_transient( 'order_' . $md );
		$customer           = get_transient( 'merchantcode_' . $md );
		$DSMerchantTerminal = get_transient( 'terminal_' . $md );
		$currency           = get_transient( 'currency_' . $md );
		$customer_token_c   = get_transient( 'identifier_' . $md );
		$cof_ini            = get_transient( 'cof_ini_' . $md );
		$cof_type           = get_transient( 'cof_type_' . $md );
		$cof_txnid          = get_transient( 'cof_txnid_' . $md );
		$miObj              = new RedsysAPIWs();
		$secretsha256       = $this->get_redsys_sha256( $user_id );
		$url_ok             = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$needed             = wp_json_encode(
			array(
				'threeDSInfo'     => 'ChallengeResponse',
				'MD'              => $md,
				'protocolVersion' => '1.0.2',
				'PARes'           => $pares,
			)
		);

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', '$pares: ' . $pares );
			$this->log->add( 'insite', '$order_id: ' . $order_id );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}

		$DATOS_ENTRADA  = '<DATOSENTRADA>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $DSMerchantTerminal . '</DS_MERCHANT_TERMINAL>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
		$DATOS_ENTRADA .= '</DATOSENTRADA>';
		$XML            = '<REQUEST>';
		$XML           .= $DATOS_ENTRADA;
		$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$XML           .= '<DS_SIGNATURE>' . $miObj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
		$XML           .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
			$this->log->add( 'insite', '$XML: ' . $XML );
			$this->log->add( 'insite', ' ' );
		}

		$CLIENTE    = new SoapClient( $redsys_adr );
		$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' $responsews: ' . print_r( $responsews, true ) );
			$this->log->add( 'insite', '/****************************/' );
			$this->log->add( 'insite', ' ' );
		}

		if ( isset( $responsews->trataPeticionReturn ) ) {
			$XML_RETORNO = new SimpleXMLElement( $responsews->trataPeticionReturn );
			if ( isset( $XML_RETORNO->OPERACION->Ds_Response ) ) {
				$RESPUESTA = (int) $XML_RETORNO->OPERACION->Ds_Response;
				if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
					$auth_code = $XML_RETORNO->OPERACION->Ds_AuthorisationCode;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', ' ' );
						$this->log->add( 'insite', 'Response: Ok > ' . $RESPUESTA );
						$this->log->add( 'insite', 'Authorization code: ' . $auth_code );
						$this->log->add( 'insite', ' ' );
					}
					$auth_code = (string) $XML_RETORNO->OPERACION->Ds_AuthorisationCode;
					$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
					$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $auth_code );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', '$order_id: ' . $order_id );
						$this->log->add( 'insite', '_authorisation_code_redsys: ' . $auth_code );
						$this->log->add( 'insite', '_redsys_done: yes' );
						$this->log->add( 'insite', '_payment_terminal_redsys: ' . $DSMerchantTerminal );
						$this->log->add( 'insite', '_payment_order_number_redsys: ' . $orderid2 );
					}
					update_post_meta( $order_id, '_authorisation_code_redsys', $auth_code );
					update_post_meta( $order_id, '_redsys_done', 'yes' );
					update_post_meta( $order_id, '_payment_terminal_redsys', $DSMerchantTerminal );
					update_post_meta( $order_id, '_payment_order_number_redsys', $orderid2 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'insite', 'payment_complete() 10' );
					}
					$order->payment_complete();
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Check for Servired/RedSys HTTP Notification
	 *
	 * @access public
	 * @return void
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_ipn_response() {

		@ob_clean();
		$_POST = stripslashes_deep( $_POST );

		if ( isset( $_POST['PaRes'] ) ) {
			$result = $this->check_confirm_pares( $_POST );

			if ( $result ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '      Pares confirmado        ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				$md       = sanitize_text_field( $_POST['MD'] );
				$order_id = get_transient( $md );
				$order    = WCRed()->get_order( $order_id );
				$url_ok   = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
				echo '<script>window.top.location.href = "' . $url_ok . '"</script>';
				exit();
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', '     Pares NO confirmado      ' );
					$this->log->add( 'insite', '/****************************/' );
					$this->log->add( 'insite', ' ' );
				}
				echo 'Something was wrong';
				exit();
			}
		} else {
			if ( $this->check_ipn_request_is_valid() ) {
				header( 'HTTP/1.1 200 OK' );
				do_action( 'valid-insite-standard-ipn-request', $_POST );
			} else {
				wp_die( 'InSite Notification Request Failure' );
			}
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function successful_request( $posted ) {
		global $woocommerce;

		if ( isset( $_POST['Ds_SignatureVersion'] ) && isset( $_POST['Ds_MerchantParameters'] ) && isset( $_POST['Ds_Signature'] ) ) {
			$version           = $_POST['Ds_SignatureVersion'];
			$data              = $_POST['Ds_MerchantParameters'];
			$remote_sign       = $_POST['Ds_Signature'];
			$miObj             = new RedsysAPI();
			$decodedata        = $miObj->decodeMerchantParameters( $data );
			$total             = (int) $miObj->getParameter( 'Ds_Amount' );
			$ordermi           = $miObj->getParameter( 'Ds_Order' );
			$dstransactiontype = $miObj->getParameter( 'Ds_TransactionType' );
			$dscode            = $miObj->getParameter( 'Ds_MerchantCode' );
			$currency_code     = $miObj->getParameter( 'Ds_Currency' );
			$response          = $miObj->getParameter( 'Ds_Response' );
			$id_trans          = $miObj->getParameter( 'Ds_AuthorisationCode' );
			$dsdate            = $miObj->getParameter( 'Ds_Date' );
			$dshour            = $miObj->getParameter( 'Ds_Hour' );
			$dstermnal         = $miObj->getParameter( 'Ds_Terminal' );
			$dsmerchandata     = $miObj->getParameter( 'Ds_MerchantData' );
			$dssucurepayment   = $miObj->getParameter( 'Ds_SecurePayment' );
			$dscardcountry     = $miObj->getParameter( 'Ds_Card_Country' );
			$dsconsumercountry = $miObj->getParameter( 'Ds_ConsumerLanguage' );
			$dscargtype        = $miObj->getParameter( 'Ds_Card_Type' );
			$tokennum          = $miObj->getParameter( 'Ds_Merchant_Identifier' );
			$card_brand        = $miObj->getParameter( 'Ds_Card_Brand' );
			$card_txnid        = $miObj->getParameter( 'Ds_Merchant_Cof_Txnid' );
			$expiry_date       = $miObj->getParameter( 'Ds_ExpiryDate' );
			$order2            = get_transient( $ordermi . '_woocommrce_order_number_redsys' );
			if ( ! $order2 ) {
				$order2 = WCRed()->clean_order_number( $ordermi );
			}
			$order = WCRed()->get_order( (int) $order2 );
		} else {
			$total             = $_POST['Ds_Amount'];
			$ordermi           = $_POST['Ds_Order'];
			$dscode            = $_POST['Ds_MerchantCode'];
			$currency_code     = $_POST['Ds_Currency'];
			$response          = $_POST['Ds_Response'];
			$id_trans          = $_POST['Ds_AuthorisationCode'];
			$dsdate            = $_POST['Ds_Date'];
			$dshour            = $_POST['Ds_Hour'];
			$dstermnal         = $_POST['Ds_Terminal'];
			$dsmerchandata     = $_POST['Ds_MerchantData'];
			$dssucurepayment   = $_POST['Ds_SecurePayment'];
			$dscardcountry     = $_POST['Ds_SignatureVersion'];
			$tokennum          = $_POST['Ds_Merchant_Identifier'];
			$card_brand        = $_POST['Ds_Card_Brand'];
			$expiry_date       = $_POST['Ds_ExpiryDate'];
			$card_txnid        = $_POST['Ds_Merchant_Cof_Txnid'];
			$dstransactiontype = $_POST['Ds_TransactionType'];
			$order2            = get_transient( $ordermi . '_woocommrce_order_number_redsys' );
			if ( ! $order2 ) {
				$order2 = WCRed()->clean_order_number( $ordermi );
			}
			$order = WCRed()->get_order( (int) $order2 );
		}
		$user_id           = $order->get_user_id();
		$ds_merchant_cof   = get_transient( $ordermi . '_ds_merchant_cof_ini' );
		$save_token        = get_post_meta( $order2, '_redsys_save_token', true );
		$token_type_needed = get_transient( $ordermi . '_insite_token_need' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$ds_merchant_cof: ' . $ds_merchant_cof );
			$this->log->add( 'insite', '$save_token: ' . $save_token );
			$this->log->add( 'insite', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $ordermi . ',  Ds_MerchantCode: ' . $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
			$this->log->add( 'insite', '$dstransactiontype: ' . $dstransactiontype );
			$this->log->add( 'insite', '$response: ' . $response );
			$this->log->add( 'insite', 'print_r $_POST: ' . print_r( $_POST, true ) );
		}

		if ( 3 === intval( $dstransactiontype ) || 900 === intval( $response ) ) {
			if ( 900 === intval( $response ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'Response 900 (refund)' );
					$this->log->add( 'insite', '$order->get_id(): ' . $order->get_id() );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'update_post_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded', 'woocommerce-redsys' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woocommerce-redsys' ) );
			exit;
		}

		if ( ! empty( $expiry_date ) ) {
			$dsexpiryyear  = '20' . substr( $expiry_date, 0, 2 );
			$dsexpirymonth = substr( $expiry_date, -2 );
		} else {
			$dsexpiryyear  = '99';
			$dsexpirymonth = '12';
		}

		if ( ! empty( $dscardnumber4 ) ) {
			$dscardnumber4 = substr( $dscardnumbercompl, -4 );
		} else {
			$dscardnumber4 = '0000';
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$tokennum: ' . $tokennum );
			$this->log->add( 'insite', '$user_id: ' . $user_id );
			$this->log->add( 'insite', '$card_brand: ' . WCRed()->get_card_brand( $card_brand ) );
			$this->log->add( 'insite', '$dscardnumber4: ' . $dscardnumber4 );
			$this->log->add( 'insite', '$dsexpirymonth: ' . $dsexpirymonth );
			$this->log->add( 'insite', '$dsexpiryyear: ' . $dsexpiryyear );
		}

		if ( 'yes' === $save_token && ! empty( $tokennum ) || 'yes' === $token_type_needed && 'R' === $ds_merchant_cof ) {
			$token = new WC_Payment_Token_CC();
			$token->set_token( $tokennum );
			$token->set_gateway_id( 'redsys' );
			$token->set_user_id( $user_id );
			$token->set_card_type( WCRed()->get_card_brand( $card_brand ) );
			$token->set_last4( $dscardnumber4 );
			$token->set_expiry_month( $dsexpirymonth );
			$token->set_expiry_year( $dsexpiryyear );
			$token->set_default( true );
			$token->save();
			WCRed()->set_txnid( $tokennum, $card_txnid );
			WCRed()->set_token_type( $tokennum, $ds_merchant_cof );
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Removing Token' );
			}
			$data = array(
				'merchant_code'       => $dscode,
				'merchant_identifier' => $tokennum,
				'order_id'            => $ordermi,
				'terminal'            => $dstermnal,
				'sha256'              => $this->get_redsys_sha256( $user_id ),
				'redsys_adr'          => $this->get_redsys_url_gateway( $user_id ),
			);
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', '$data remove: ' . print_r( $data, true ) );
			}
			$result = WCRed()->remove_token( $data );

			if ( 'yes' === $this->debug ) {
				if ( $result ) {
					$this->log->add( 'insite', 'Result Token Removed: OK' );
				} else {
					$this->log->add( 'insite', 'Result Token Removed: ERROR' );
				}
			}
		}

		$response = intval( $response );
		if ( $response <= 99 ) {
			// authorized
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			if ( $order_total_compare != $total ) {
				// amount does not match
				if ( 'yes' == $this->debug ) {
					$this->log->add( 'insite', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}

				// Put this order on-hold for manual checking
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2$s).', 'woocommerce-redsys' ), $order_total_compare, $total ) );
				exit;
			}
			$authorisation_code = $id_trans;
			if ( ! empty( $ordermi ) ) {
				update_post_meta( $order->id, '_payment_order_number_redsys', $ordermi );
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->id, '_payment_date_redsys', $dsdate );
			}
			if ( ! empty( $dshour ) ) {
				update_post_meta( $order->id, '_payment_hour_redsys', $dshour );
			}
			if ( ! empty( $id_trans ) ) {
				update_post_meta( $order->id, '_authorisation_code_redsys', $authorisation_code );
			}
			if ( ! empty( $dscardcountry ) ) {
				update_post_meta( $order->id, '_card_country_insite', $dscardcountry );
			}
			if ( ! empty( $dscargtype ) ) {
				update_post_meta( $order->id, '_card_type_insite', $dscargtype == 'C' ? 'Credit' : 'Debit' );
			}
			if ( ! empty( $dstermnal ) ) {
				update_post_meta( $order->id, '_payment_terminal_redsys', $dstermnal );
			}
			// Payment completed
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorisation code: ', 'woocommerce-redsys' ) . $authorisation_code );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'payment_complete() 11' );
			}
			$order->payment_complete();
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', 'Payment complete.' );
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

			// Tarjeta caducada
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

			// Order cancelled
			$order->update_status( 'cancelled', __( 'Cancelled by InSite', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Order canceled by InSite', 'woocommerce-redsys' ) );
			WC()->cart->empty_cart();
		}
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS
		$order          = WCRed()->get_order( $order_id );
		$terminal       = get_post_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', '/**************************/' );
			$this->log->add( 'insite', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', '/**************************/' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'insite', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'insite', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$redsys_adr        = $this->get_redsys_url_gateway( $user_id );
		$autorization_code = get_post_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = get_post_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = get_post_meta( $order_id, '_corruncy_code_redsys', true );
		$order_fuc         = get_post_meta( $order_id, '_order_fuc_redsys', true );

		if ( ! $order_fuc ) {
			$order_fuc = $this->customer;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', '**********************' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'insite', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'insite', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'insite', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'insite', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );
			$this->log->add( 'insite', __( 'FUC : ', 'woocommerce-redsys' ) . $order_fuc );

		}

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$miObj = new RedsysAPI();
		$miObj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$miObj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $order_fuc );
		$miObj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$miObj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$miObj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$miObj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$miObj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$miObj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$miObj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', '*********************************' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'insite', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'insite', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'insite', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $order_fuc );
			$this->log->add( 'insite', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'insite', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'insite', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'insite', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'insite', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'insite', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'insite', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'insite', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'insite', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'insite', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'insite', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'insite', ' ' );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature( $secretsha256 );

		$post_arg = wp_remote_post(
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
		if ( is_wp_error( $post_arg ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'insite', '*********************************' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
			}
			return $post_arg;
		}
		return true;
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_redsys_refund( $order_id ) {
		// check postmeta
		$order = WCRed()->get_order( (int) $order_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', '$order_id: ' . $order_id );
			$this->log->add( 'insite', '$order->get_id(): ' . $order->get_id() );
		}
		$order_refund = get_transient( $order_id . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'insite', '*****************************************' );
			$this->log->add( 'insite', ' ' );
			$this->log->add( 'insite', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'insite', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = get_post_meta( $order_id, '_payment_order_number_redsys', true );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'insite', __( '$order_id#: ', 'woocommerce-redsys' ) . $transaction_id );
		}
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'insite', __( 'check_redsys_refund Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'insite', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
				}
				return new WP_Error( 'error', $refund_asked->get_error_message() );
			}
			$x = 0;
			do {
				sleep( 5 );
				$result = $this->check_redsys_refund( $order_id );
				$x++;
			} while ( $x <= 20 && false === $result );
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'insite', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/********************************/' );
				$this->log->add( 'insite', '  Refund complete by Redsys   ' );
				$this->log->add( 'insite', '/********************************/' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', '  The final has come, this story has ended  ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'insite', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'insite', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'insite', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', ' ' );
					$this->log->add( 'insite', '/******************************************/' );
					$this->log->add( 'insite', '  The final has come, this story has ended  ' );
					$this->log->add( 'insite', '/******************************************/' );
					$this->log->add( 'insite', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'insite', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
				$this->log->add( 'insite', ' ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', '  The final has come, this story has ended  ' );
				$this->log->add( 'insite', '/******************************************/' );
				$this->log->add( 'insite', ' ' );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_insite_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}

	public function save_field_update_order_meta( $order_id ) {
		/**
		 * Package: WooCommerce Redsys Gateway
		 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
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
		if ( ! empty( $_POST['_redsys_save_token'] ) && 'yes' === $_POST['_redsys_save_token'] ) {
			update_post_meta( $order_id, '_redsys_save_token', sanitize_text_field( $_POST['_redsys_save_token'] ) );
		}
		if ( ! empty( $_POST['token'] ) && 'add' !== $_POST['token'] ) {
			set_transient( $order_id . '_insite_use_token', sanitize_text_field( $_POST['token'] ), 36000 );
		} else {
			set_transient( $order_id . '_insite_use_token', 'no', 36000 );
		}
		if ( ! empty( $_POST['_redsys_token_type'] ) ) {
			set_transient( $order_id . '_redsys_token_type', sanitize_text_field( $_POST['_redsys_token_type'] ), 36000 );
		} else {
			set_transient( $order_id . '_redsys_token_type', 'no', 36000 );
		}
	}
}
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2021 José Conti
 */
function woocommerce_add_gateway_insite_gateway( $methods ) {
	$methods[] = 'WC_Gateway_InSite_Redsys';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_insite_gateway' );
