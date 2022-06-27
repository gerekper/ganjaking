<?php
/**
 * Copyright: (C) 2013 - 2022 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Gateway_Preauthorizations_Redsys extends WC_Payment_Gateway {
	var $notify_url;
	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function __construct() {
		global $checkfor254;

		$this->id = 'preauthorizationsredsys';

		if ( ! empty( WCRed()->get_redsys_option( 'logo', 'preauthorizationsredsys' ) ) ) {
			$logo_url   = WCRed()->get_redsys_option( 'logo', 'preauthorizationsredsys' );
			$this->icon = apply_filters( 'woocommerce_preauthorizationsredsys_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_preauthorizationsredsys_icon', REDSYS_PLUGIN_URL_P . 'assets/images/redsys.png' );
		}
		$this->has_fields           = false;
		$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = WCRed()->get_redsys_option( 'testmode', 'preauthorizationsredsys' );
		$this->method_title         = __( 'Redsys Preauthorizations (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'Redsys Preauthorizations works redirecting customers to Redsys or paying directly without leaving the website if you have active payment with 1 click and a terminal not secure.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'preauthorizationsredsys' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_preauthorizationsredsys', home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_preauthorizationsredsys', home_url( '/' ) ) );
		// Load the settings
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables
		$this->title                = WCRed()->get_redsys_option( 'title', 'preauthorizationsredsys' );
		$this->multisitesttings     = WCRed()->get_redsys_option( 'multisitesttings', 'preauthorizationsredsys' );
		$this->ownsetting           = WCRed()->get_redsys_option( 'ownsetting', 'preauthorizationsredsys' );
		$this->hideownsetting       = WCRed()->get_redsys_option( 'hideownsetting', 'preauthorizationsredsys' );
		$this->description          = WCRed()->get_redsys_option( 'description', 'preauthorizationsredsys' );
		$this->logo                 = WCRed()->get_redsys_option( 'logo', 'preauthorizationsredsys' );
		$this->orderdo              = WCRed()->get_redsys_option( 'orderdo', 'preauthorizationsredsys' );
		$this->customer             = WCRed()->get_redsys_option( 'customer', 'preauthorizationsredsys' );
		$this->merchantgroup        = WCRed()->get_redsys_option( 'merchantgroup', 'preauthorizationsredsys' );
		$this->commercename         = WCRed()->get_redsys_option( 'commercename', 'preauthorizationsredsys' );
		$this->terminal             = WCRed()->get_redsys_option( 'terminal', 'preauthorizationsredsys' );
		$this->secret               = WCRed()->get_redsys_option( 'secret', 'preauthorizationsredsys' );
		$this->secretsha256         = WCRed()->get_redsys_option( 'secretsha256', 'preauthorizationsredsys' );
		$this->customtestsha256     = WCRed()->get_redsys_option( 'customtestsha256', 'preauthorizationsredsys' );
		$this->debug                = WCRed()->get_redsys_option( 'debug', 'preauthorizationsredsys' );
		$this->hashtype             = WCRed()->get_redsys_option( 'hashtype', 'preauthorizationsredsys' );
		$this->redsyslanguage       = WCRed()->get_redsys_option( 'redsyslanguage', 'preauthorizationsredsys' );
		$this->wooredsysurlko       = WCRed()->get_redsys_option( 'wooredsysurlko', 'preauthorizationsredsys' );
		$this->terminal2            = WCRed()->get_redsys_option( 'terminal2', 'preauthorizationsredsys' );
		$this->useterminal2         = WCRed()->get_redsys_option( 'useterminal2', 'preauthorizationsredsys' );
		$this->toamount             = WCRed()->get_redsys_option( 'toamount', 'preauthorizationsredsys' );
		$this->usetokens            = WCRed()->get_redsys_option( 'usetokens', 'preauthorizationsredsys' );
		$this->subsusetokensdisable = WCRed()->get_redsys_option( 'subsusetokensdisable', 'preauthorizationsredsys' );
		$this->usetokensdirect      = WCRed()->get_redsys_option( 'usetokensdirect', 'preauthorizationsredsys' );
		$this->bulkrefund           = WCRed()->get_redsys_option( 'bulkrefund', 'preauthorizationsredsys' );
		$this->sendemails           = WCRed()->get_redsys_option( 'sendemails', 'preauthorizationsredsys' );
		$this->checkoutredirect     = WCRed()->get_redsys_option( 'checkoutredirect', 'preauthorizationsredsys' );
		$this->traactive            = WCRed()->get_redsys_option( 'traactive', 'preauthorizationsredsys' );
		$this->traamount            = WCRed()->get_redsys_option( 'traamount', 'preauthorizationsredsys' );
		$this->redsysdirectdeb      = 'T';
		$this->preauthorization     = 'yes';
		$this->privateproduct       = WCRed()->get_redsys_option( 'privateproduct', 'preauthorizationsredsys' );
		$this->sentemailscustomers  = WCRed()->get_redsys_option( 'sentemailscustomers', 'preauthorizationsredsys' );
		$this->sendemailthankyou    = WCRed()->get_redsys_option( 'sendemailthankyou', 'preauthorizationsredsys' );
		$this->sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'preauthorizationsredsys' );
		$this->testforuser          = WCRed()->get_redsys_option( 'testforuser', 'preauthorizationsredsys' );
		$this->testforuserid        = WCRed()->get_redsys_option( 'testforuserid', 'preauthorizationsredsys' );
		$this->redsysbanktransfer   = WCRed()->get_redsys_option( 'redsysbanktransfer', 'preauthorizationsredsys' );
		$this->redirectiontime      = WCRed()->get_redsys_option( 'redirectiontime', 'preauthorizationsredsys' );
		$this->sendemailsdscard     = WCRed()->get_redsys_option( 'sendemailsdscard', 'preauthorizationsredsys' );
		$this->buttoncheckout       = WCRed()->get_redsys_option( 'buttoncheckout', 'preauthorizationsredsys' );
		$this->butonbgcolor         = WCRed()->get_redsys_option( 'butonbgcolor', 'preauthorizationsredsys' );
		$this->butontextcolor       = WCRed()->get_redsys_option( 'butontextcolor', 'preauthorizationsredsys' );
		$this->descripredsys        = WCRed()->get_redsys_option( 'descripredsys', 'preauthorizationsredsys' );
		$this->testshowgateway      = WCRed()->get_redsys_option( 'testshowgateway', 'preauthorizationsredsys' );
		$this->notiemail            = WCRed()->get_redsys_option( 'notiemail', 'preauthorizationsredsys' );
		$this->log                  = new WC_Logger();
		$this->supports             = array(
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
		);
		// Actions
		add_action( 'valid-preauthorizationsredsys-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_preauthorizationsredsys', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );

		// Payment listener/API hook
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );

		// Yith Subscriptions Premium.
		if ( defined( 'YITH_YWSBS_PREMIUM' ) ) {
			add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'renew_yith_subscription' ), 10, 1 );
		}
		
		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'doing_scheduled_subscription_payment' ), 10, 2 );
		}

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public static function admin_notice_mcrypt_encrypt() {
		if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
			if ( ! function_exists( 'mcrypt_encrypt' ) ) {
				$class   = 'error';
				$message = __( 'WARNING: The PHP mcrypt_encrypt module is not installed on your server. The new API Redsys SHA-256 needs this module in order to work.  Please contact your hosting provider and ask them to install it. Otherwise, your shop will stop working.', 'woocommerce-redsys' );
				echo '<div class=\"$class\"> <p>$message</p></div>';
			} else {
				return;
			}
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country
	 *
	 * @access public
	 * @return bool
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function is_valid_for_use() {

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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'Servired/RedSys Spain', 'woocommerce-redsys' ); ?></h3>
		<p><?php esc_html_e( 'Servired/RedSys works by sending the user to your bank TPV to enter their payment information.', 'woocommerce-redsys' ); ?></p>
		<?php
			echo WCRed()->return_help_notice();
		if ( isset( $_GET['quijote'] ) ) {
			?>
			<div class="quijote">
			<?php include_once REDSYS_PLUGIN_DATA_PATH_P . 'data.php'; ?>
			</div>
			<?php
		}
		?>
		<?php if ( class_exists( 'SitePress' ) ) { ?>
			<div class="updated fade"><h4><?php esc_html_e( 'Attention! WPML detected.', 'woocommerce-redsys' ); ?></h4>
				<p><?php esc_html_e( 'The Gateway will be shown in the customer language. The option "Language Gateway" is not taken into consideration', 'woocommerce-redsys' ); ?></p>
			</div>
		<?php } ?>
		<?php if ( $this->is_valid_for_use() ) : ?>
			<table class="form-table">
				<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html();
				?>
			</table><!--/.form-table-->
			<?php
		else :

			$currencies          = WCRed()->allowed_currencies();
			$formated_currencies = '';

			foreach ( $currencies as $currency ) {
				$formated_currencies .= $currency . ', ';
			}
			?>
				<div class="inline error">
					<p><strong><?php esc_html_e( 'Gateway Disabled', 'woocommerce-redsys' ); ?></strong>: 
				<?php
						esc_html_e( 'Servired/RedSys only support ', 'woocommerce-redsys' );
						echo esc_html( $formated_currencies );
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function init_form_fields() {

		$options    = array();
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'preauthorizationsredsys' );

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
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'preauthorizationsredsys' );
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
			'enabled'              => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Servired/RedSys', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'multisitesttings'   => array(
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
			'ownsetting'         => array(
				'title'       => __( 'NOT use Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Do NOT use Network settings. Use settings of this page', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'redirectiontime'      => array(
				'title'       => __( 'Redirection time', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'If you want users to be immediately redirected to the payment gateway when they press the pay button, don\'t add anything. If you want to give them time to think about it, add the seconds in milliseconds, for example, 5 seconds are 5000 milliseconds.', 'woocommerce-redsys' ),
			),
			'usetokens'            => array(
				'title'       => __( 'Pay with One Click', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'With Pay with one Click, users who have bought before in your store should not fill the credit card number in Redsys again. Make sure you have activated in Redsys that he send to your store the credit card number.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Pay with One Click', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'subsusetokensdisable' => array(
				'title'       => __( 'Disable Subscription token', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Tokenization is enabled by default (Enable Pay with One Click is not needed). Here you can disable tokenization for WooCommerce Subscriptions.', 'woocommerce-redsys' ),
				'label'       => __( 'Disable Subscription token, it is enabled by default', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'traactive'              => array(
				'title'   => __( 'Enable TRA', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable TRA. WARNING, your bank has to enable it before you use it.', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'traamount'             => array(
				'title'       => __( 'Limit import for TRA', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'TRA will be sent when the amount is inferior to what you specify here. Write the amount without the currency sign, i.e. if it is 250€, ONLY write 250', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'notiemail'            => array(
				'title'       => __( 'Notification email', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Email errors will arrive to this email', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'usetokensdirect'      => array(
				'title'       => __( 'One Click in page?', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'ATTENTION: Pay with one Click has to be active before mark this option. With this option, users to whom you have already collected Tokens for previous purchases, they do not leave the page after pressing the payment button. Your terminal must be unsafe, or it will not work. ', 'woocommerce-redsys' ),
				'label'       => __( 'Enable One Click in page', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'bulkrefund'           => array(
				'title'       => __( 'Add Bulk Action Refunds', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'This option adds the bulk action Refunds. For security reasons, do not activate it if not needed', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Bulk Action Refunds', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'checkoutredirect'     => array(
				'title'       => __( 'One Click to Checkout', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'ATTENTION: This option can break your website under some circunstances, check your website and checkout before and after enable this option. With this option, the customer is redirected to checkout after add a product to the card. Only activate this option if your customers ONLY buy ONE product every time.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable One Click to Checkout', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemails'           => array(
				'title'       => __( 'Send emails', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users fails to pay in Redsys, and email will be send to you with the problem, amount and link to the order details.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails when payment fails', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailsdscard'     => array(
				'title'       => __( 'Send emails Ds_Card_Number problem', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'If tokenization is used, the filed Ds_Card_number can be a very interesting information. If Redsys isn\'t sending this field and this options is active, and email will be sent to the website administrator.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails Ds_Card_number problem', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sentemailscustomers'  => array(
				'title'       => __( 'Send emails to customers', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users fails to pay in Redsys, and email will be send to the customer with the problem, This can increase cart recovery.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails to customers when payment fails', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailthankyou'    => array(
				'title'       => __( 'Notice Thank you problem', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users arrive to Thank you page from Redsys, and the order is not marked as paid, and email will be send to adminsitrator for to warn the administrator to check Redsys to see if payment has been made and a notice will be shown to customer at Thank you Page.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send email Thank you problem for be noticed', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailthankyoutxt' => array(
				'title'       => __( 'Text on the thank you page', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the text that will be show to customers that arrive to the Thank You page if their order is not marked as paid.', 'woocommerce-redsys' ),
				'default'     => __( '<p><b>ATTENTION:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid.  Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.</p>', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'title'                => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Servired/RedSys', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'          => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via Servired/RedSys; you can pay with your credit card.', 'woocommerce-redsys' ),
			),
			'logo'                 => array(
				'title'       => __( 'Gateway logo at checkout', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add link to image logo for Gateway at checkout.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'buttoncheckout'       => array(
				'title'       => __( 'Button Checkout Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add the button text at the checkout.', 'woocommerce-redsys' ),
			),
			'butonbgcolor'         => array(
				'title'       => __( 'Button Color Background', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Button Color Background Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'butontextcolor'       => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Text color Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'customer'             => array(
				'title'       => __( 'Commerce number (FUC)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'merchantgroup'        => array(
				'title'       => __( 'Merchant Group Number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'It is an identifier for sharing tokens between websites of the same company', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'commercename'         => array(
				'title'       => __( 'Commerce Name', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce Name', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'terminal'             => array(
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
			'useterminal2'         => array(
				'title'       => __( 'Activate Second Terminal', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate Second Terminal.', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'If you use a second terminal, you need to add it in the field above and activate it here. You will need to set when use the Second Terminal in the field below.', 'woocommerce-redsys' ) ),
			),
			'terminal2'            => array(
				'title'       => __( 'Second Terminal', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'If you use a second Terminal number, you need to add here the second terminal provided by your bank', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'toamount'             => array(
				'title'       => __( 'Use the Second Terminal from 0 to (Don\'t use Currency Symbol)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'When will the Second Terminal used? from 0 to...? Add the amount. Ex. Add 100 and the Second Terminal will be used when the amount be from 0 to 100', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'not_use_https'        => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility (only activate it if José Conti indicate you).', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Only use it if José Conti indicate you. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woocommerce-redsys' ) ),
			),
			'orderdo'              => array(
				'title'       => __( 'What to do after payment?', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose what to do after the customer pay the order.', 'woocommerce-redsys' ),
				'default'     => 'processing',
				'options'     => array(
					'processing' => __( 'Mark as Processing (default & recomended)', 'woocommerce-redsys' ),
					'completed'  => __( 'Mark as Complete', 'woocommerce-redsys' ),
				),
			),
			'secretsha256'         => array(
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
			'redsyslanguage'       => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'wooredsysurlko'       => array(
				'title'       => __( 'Return URL (Redsys Error button)', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'When the user press the return button at Redsys Gateway (Ex: The user type an incorrect credit card), you can redirect the user to My Cart page canceling the order, or you can redirect the user to Checkput page without cancel the order.', 'woocommerce-redsys' ),
				'default'     => 'returncancel',
				'options'     => array(
					'returncancel'   => __( 'Cancel the order and return to My Cart page', 'woocommerce-redsys' ),
					'returnnocancel' => __( 'Don\'t cancel the order and return to Checkout page', 'woocommerce-redsys' ),
				),
			),
			'privateproduct'       => array(
				'title'       => __( 'Private Products', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Activate Private Products if you need to create products visible per customer', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Private Products', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'testmode'             => array(
				'title'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woocommerce-redsys' ) ),
			),
			'testshowgateway'   => array(
				'title'       => __( 'Show to this users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Show the gateway in the chcekout when it is in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-show-gateway-test-settings',
				'id'          => 'woocommerce_redsys_showtestforuserid',
				'options'     => $options_show,
				'default'     => '',
				'description' => sprintf( __( 'Select users that will see the gateway when it is in test mode. If no users are selected, will be shown to all users', 'woocommerce-redsys' ) ),
			),
			'testforuser'          => array(
				'title'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'The user selected below will use the terminal in test mode. Other users will continue to use live mode unless you have the "Running in test mode" option checked.', 'woocommerce-redsys' ) ),
			),
			'testforuserid'        => array(
				'title'       => __( 'Users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Users running in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-allowed-users-settings',
				'id'          => 'woocommerce_redsys_testforuserid',
				'options'     => $options,
				'default'     => '',
				'description' => sprintf( __( 'Select users running in test mode', 'woocommerce-redsys' ) ),
			),
			'debug'                => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log Servired/RedSys events, such as notifications requests, inside <code>WooCommerce > Status > Logs > redsypreathorizations-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);

		$redsyslanguages = WCRed()->get_redsys_languages();

		foreach ( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['redsyslanguage']['options'][ $redsyslanguage ] = $valor;
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_user_test_mode( $userid ) {

		$usertest_active = $this->testforuser;
		$selections      = (array) WCRed()->get_redsys_option( 'testforuserid', 'preauthorizationsredsys' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '     Checking user test       ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $usertest_active ) {

			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '   Checking user ' . $userid );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '  User in forach ' . $user_id );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '   Checking user test TRUE    ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/********************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'preauthorizationsredsys', '/********************************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '  Checking user test continue ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '     User test Disabled.      ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '          URL Test RD         ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '          URL Test WS         ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          URL Test RD         ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          URL Test WS         ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          URL Live RD         ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          URL Live WS         ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$url = $this->liveurlws;
				}
			}
		}
		return $url;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '         SHA256 Test.         ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '      SHA256 Test Custom.     ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$sha256 = $customtestsha256;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '     SHA256 Test Standard.    ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '      USER SHA256 Test.       ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '      SHA256 Test Custom.     ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$sha256 = $customtestsha256;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '     SHA256 Test Standard.    ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function get_redsys_args( $order ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '     Making redsys_args       ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		$order_id         = $order->get_id();
		$currency_codes   = WCRed()->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$transaction_type = '1';
		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
		} elseif ( $this->redsyslanguage ) {
			$gatewaylanguage = $this->redsyslanguage;
		} else {
			$gatewaylanguage = '001';
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
				$ds_merchant_terminal = $terminal2;
			} else {
				$ds_merchant_terminal = $terminal;
			}
		} else {
			$ds_merchant_terminal = $this->terminal;
		}

		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$psd2 = WCPSD2()->get_acctinfo( $order );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', '$psd2: ' . $psd2 );
		}
		if ( 'yes' !== WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
			$customer_token = WCRed()->get_redsys_users_token();
		} else {
			$customer_token_r = WCRed()->get_redsys_users_token( 'R' );
			$customer_token_c = WCRed()->get_redsys_users_token( 'C' );
			$customer_token_r_id = WCRed()->get_redsys_users_token( 'R', 'id' );
			$customer_token_c_id = WCRed()->get_redsys_users_token( 'C', 'id' );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'preauthorizationsredsys', '$customer_token_r: ' . $customer_token_r );
				$this->log->add( 'preauthorizationsredsys', '$customer_token_c: ' . $customer_token_c );
			}
		}
		$customer_token = WCRed()->get_redsys_users_token();

		$redsys_data_send = array();

		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$user_id             = $order->get_user_id();
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$customer            = $this->customer;
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, $this->id );
		$merchant_name       = $this->commercename;

		$redsys_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => $transaction_id2,
			'transaction_type'    => $transaction_type,
			'DSMerchantTerminal'  => $ds_merchant_terminal,
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Using filter redsys_modify_data_to_send' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		}

		$secretsha256     = $redsys_data_send['secretsha256'];
		$merchan_name     = get_post_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme = get_post_meta( $order_id, '_billing_last_name', true );

		// redsys Args
		$mi_obj = new RedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $redsys_data_send['order_total_sign'] );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $redsys_data_send['transaction_id2'] );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $redsys_data_send['customer'] );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $redsys_data_send['currency'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $redsys_data_send['transaction_type'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $redsys_data_send['DSMerchantTerminal'] );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $redsys_data_send['final_notify_url'] );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', $redsys_data_send['url_ok'] );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $redsys_data_send['returnfromredsys'] );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $redsys_data_send['gatewaylanguage'] );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', $redsys_data_send['product_description'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', $merchan_name . ' ' . $merchant_lastnme );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $redsys_data_send['merchant_name'] );

		// [T = Pago con Tarjeta + iupay , R = Pago por Transferencia, D = Domiciliacion, C = Sólo Tarjeta (mostrará sólo el formulario para datos de tarjeta)] por defecto es T
		if ( 'T' === $this->redsysdirectdeb || empty( $this->redsysdirectdeb ) ) { // No se puede ofrecer domiciliación y tarjeta con pago por referencia a la vez
			if ( $this->order_contains_subscription( $order_id ) ) {
				if ( $this->order_contains_subscription( $order_id ) && 'yes' !== $this->subsusetokensdisable ) {
					if ( ! $customer_token_r ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
						$mi_obj->setParameter( 'Ds_MERCHANT_IDENTIFIER', 'REQUIRED' );
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
							$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
						}
						$ds_merchant_data = 'no';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: 0' );
							$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
							if ( ! empty( $this->merchantgroup ) ) {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
							} else {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
							}
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'preauthorizationsredsys', '/***************************************************************/' );
								$this->log->add( 'preauthorizationsredsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
								$this->log->add( 'preauthorizationsredsys', '/***************************************************************/' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_INI: S' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TYPE: R' );
								$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
							}
						}
					} else {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_r );
						if ( WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
							$txnid = WCRed()->get_txnid( $customer_token_r );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TXNID', $txnid );
						}
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
						$ds_merchant_data           = 'yes';
						$ds_merchant_direct_payment = 'false';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_r );
							$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_INI: N' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TYPE: R' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
							}
						}
					}
				}
			} elseif ( 'yes' === $this->usetokens ) {
				// Pago con 1 clic activo
				if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
					// PSD2 activo
					if ( ! $customer_token_c ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
						$mi_obj->setParameter( 'Ds_MERCHANT_IDENTIFIER', 'REQUIRED' );
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						if ( $psd2 ) {
							$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'C' );
							$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
						}
						$ds_merchant_data = 'no';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: 0' );
							$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
							if ( ! empty( $this->merchantgroup ) ) {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
							} else {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
							}
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'preauthorizationsredsys', '/***************************************************************/' );
								$this->log->add( 'preauthorizationsredsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
								$this->log->add( 'preauthorizationsredsys', '/***************************************************************/' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_INI: N' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TYPE: C' );
								$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
							}
						}
					} else {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_c );
						if ( $psd2 ) {
							$txnid = WCRed()->get_txnid( $customer_token_c );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'C' );
							$mi_obj->setParameter( 'DS_MERCHANT_COF_TXNID', $txnid );
						}
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
						$ds_merchant_data           = 'yes';
						$ds_merchant_direct_payment = 'false';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_c );
							$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_INI: N' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TYPE: C' );
								$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
							}
						}
					}
				} elseif ( empty( $customer_token ) ) {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
					if ( ! empty( $this->merchantgroup ) ) {
						$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
					}
					$ds_merchant_data = 'no';
				} else {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token );
				}
				if ( ! empty( $this->merchantgroup ) ) {
					$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
				}
				$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
				$ds_merchant_data           = 'yes';
				$ds_merchant_direct_payment = 'false';
			}
		} elseif ( 'TD' === $this->redsysdirectdeb ) {
			$mi_obj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'TD' );
			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
			}
		} else {
			$mi_obj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'D' );
			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
			}
		}

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición
		$request      = '';
		$params       = $mi_obj->createMerchantParameters();
		$signature    = $mi_obj->createMerchantSignature( $secretsha256 );
		$order_id_set = $redsys_data_send['transaction_id2'];
		set_transient( 'redsys_signature_' . sanitize_text_field( $order_id_set ), $secretsha256, 600 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) );
			$this->log->add( 'preauthorizationsredsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'preauthorizationsredsys', 'set_transient: ' . get_transient( 'redsys_signature_' . sanitize_title( $order_id_set ) ) );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_AMOUNT: ' . $redsys_data_send['order_total_sign'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_ORDER: ' . $redsys_data_send['transaction_id2'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $redsys_data_send['customer'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_CURRENCY: ' . $redsys_data_send['currency'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $redsys_data_send['transaction_type'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_TERMINAL: ' . $redsys_data_send['DSMerchantTerminal'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_MERCHANTURL: ' . $redsys_data_send['final_notify_url'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_URLOK: ' . $redsys_data_send['url_ok'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_URLKO: ' . $redsys_data_send['returnfromredsys'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $redsys_data_send['gatewaylanguage'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $redsys_data_send['product_description'] );
			$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_MERCHANTNAME: ' . $redsys_data_send['merchant_name'] );
			$this->log->add( 'preauthorizationsredsys', 'SECRETSHA256: ' . $secretsha256 );
			if ( ! empty( $customer_token ) && ( 'yes' === $this->usetokens ) ) {
				$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token );
				$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
				$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			} elseif ( empty( $customer_token ) && ( 'yes' === $this->usetokens ) ) {
				$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED (Se está pidiendo el token en esta transacción)' );
				if ( ! empty( $this->merchantgroup ) ) {
					$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
				} else {
					$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
				}
				$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			}
			if ( 'T' !== $this->redsysdirectdeb ) {
				$this->log->add( 'preauthorizationsredsys', 'DS_MERCHANT_PAYMETHODS: ' . $this->redsysdirectdeb . ' ( T = Pago con Tarjeta, D = Domiciliación, TD = Tarjeta + Domiciliación )' );
			}
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		// $redsys_args = apply_filters( 'woocommerce_redsys_args', $redsys_args );
		return $redsys_args;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function redsys_get_tag_content( $tag, $xml ) {
		$retorno = null;

		if ( $tag && $xml ) {

			$ini = strpos( $xml, '<' . $tag . '>' );
			$fin = strpos( $xml, '</' . $tag . '>' );
			if ( false !== $ini && false !== $fin ) {
				$ini = $ini + strlen( '<' . $tag . '>' );
				if ( $ini <= $fin ) {
					$retorno = substr( $xml, $ini, $fin - $ini );
				}
			}
		}
		return $retorno;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function redsys_process_payment_token( $order_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/*********************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Processing token 1 click insite  ' );
			$this->log->add( 'preauthorizationsredsys', '/*********************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order_total_sign    = '';
		$transaction_id2     = '';
		$transaction_type    = '';
		$ds_merchant_terminal  = '';
		$final_notify_url    = '';
		$returnfromredsys    = '';
		$gatewaylanguage     = '';
		$currency            = '';
		$secretsha256        = '';
		$customer            = '';
		$url_ok              = '';
		$product_description = '';
		$merchant_name       = '';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Generating Tokenized call   ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		$type       = 'ws';
		$order      = WCRed()->get_order( $order_id );
		$user_id    = $order->get_user_id();
		$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', 'Using WS URL: ' . $redsys_adr );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		// $order_id = $order->get_id();.
		$currency_codes   = WCRed()->get_currencies();

		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $this->preauthorization && 'yes' !== $this->redsysdirectdeb && ( 'T' === $this->redsysdirectdeb || empty( $this->redsysdirectdeb ) ) ) {
			$transaction_type = '1';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Is a pre-authorization' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		} else {
			$transaction_type = '1';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Is a payment' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		}

		$gatewaylanguage = $this->redsyslanguage;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
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
				$ds_merchant_terminal = $terminal2;
			} else {
				$ds_merchant_terminal = $terminal;
			}
		} else {
			$ds_merchant_terminal = $this->terminal;
		}

		if ( 'yes' === $this->not_use_https ){
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$customer_token = WCRed()->get_redsys_users_token();

		$redsys_data_send = array();

		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$user_id             = $order->get_user_id();
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$customer            = $this->customer;
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, $this->id );
		$merchant_name       = $this->commercename;

		$redsys_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => $transaction_id2,
			'transaction_type'    => $transaction_type,
			'DSMerchantTerminal'  => $ds_merchant_terminal,
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Using filter redsys_modify_data_to_send' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		}

		$secretsha256     = $redsys_data_send['secretsha256'];
		$order_total_sign = $redsys_data_send['order_total_sign'];
		$order            = $redsys_data_send['transaction_id2'];
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
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', '$order: ' . $order );
			$this->log->add( 'preauthorizationsredsys', '$customer: ' . $customer );
			$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
			$this->log->add( 'preauthorizationsredsys', '$transaction_type: 0' );
			$this->log->add( 'preauthorizationsredsys', '$terminal: ' . $terminal );
			$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $gatewaylanguage );
			$this->log->add( 'preauthorizationsredsys', '$final_notify_url: ' . $final_notify_url );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$mi_obj = new RedsysAPIWs();
		
		if ( ! empty( $this->merchantgroup ) ) {
			$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
		} else {
			$ds_merchant_group = '';
		}

		$DATOS_ENTRADA = "<DATOSENTRADA>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTCODE>" . $customer . "</DS_MERCHANT_MERCHANTCODE>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_TERMINAL>" . $terminal . "</DS_MERCHANT_TERMINAL>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_CURRENCY>" . $currency . "</DS_MERCHANT_CURRENCY>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_TRANSACTIONTYPE>" . $transaction_type . "</DS_MERCHANT_TRANSACTIONTYPE>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_AMOUNT>" . $order_total_sign . "</DS_MERCHANT_AMOUNT>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_ORDER>" . $order . "</DS_MERCHANT_ORDER>";
		$DATOS_ENTRADA .= $ds_merchant_group;
		$DATOS_ENTRADA .= "<DS_MERCHANT_IDENTIFIER>" . $customer_token . "</DS_MERCHANT_IDENTIFIER>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_DIRECTPAYMENT>true</DS_MERCHANT_DIRECTPAYMENT>";
		$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
		//$DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
		$DATOS_ENTRADA .= "</DATOSENTRADA>";

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '          The call            ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', $DATOS_ENTRADA );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$XML = "<REQUEST>";
		$XML .= $DATOS_ENTRADA;
		$XML .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
		$XML .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
		$XML .= "</REQUEST>";

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', $XML );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$CLIENTE  = new SoapClient( $redsys_adr ); // Entorno de prueba.
		$responsews = $CLIENTE->trataPeticion(array("datoEntrada"=>$XML));

		if ( isset( $responsews->trataPeticionReturn ) ) {
			$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
			if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) {
				$RESPUESTA = (int) $xml_retorno->OPERACION->Ds_Response;
				if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Response: Ok > ' . $RESPUESTA );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					return $url_ok;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Response: Error > ' . $RESPUESTA );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					return false;
				}
			}
		}
	}

	function generate_redsys_form_browser( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/************************************/' );
			$this->log->add( 'preauthorizationsredsys', '   Generating Redsys Form Browser     ' );
			$this->log->add( 'preauthorizationsredsys', '/*************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr      = $this->get_redsys_url_gateway( $user_id );
		$redsys_args     = $this->get_redsys_args( $order );
		$form_inputs     = array();
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', 'function generate_redsys_form_browser()' );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
			$this->log->add( 'preauthorizationsredsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		wc_enqueue_js( 'jQuery("#submit_redsys_payment_form").click();' );
		return '
			<style>
			.browser {
				font-size: 18px;
				padding: 2.1em 0 0 0;
				border-radius: 0.25em;
				background: #ddd;
				display: inline-block;
				position: relative;
				overflow: hidden;
				box-shadow: 0 0.25em 0.9em -0.1em rgba(0,0,0,.3);
			}
			.browser .browser-navigation-bar {
				display: block;
				box-sizing: border-box;
				height: 2.1em;
				position: absolute;
				top: 0;
				padding: 0.3em;
				width: 100%;
				background: linear-gradient(to bottom, #edeaed 0%, #dddfdd 100%);
				border-bottom: 2px solid #cbcbcb;
			}
			.browser i {
				display: inline-block;
				height: 0.7em;
				width: 0.7em;
				border-radius: 0.45em;
				background-color: #eee;
				margin: 0.4em 0.15em;
			}
			.browser i:nth-child(1) {background-color: rgb(255, 86, 79)}
			.browser i:nth-child(1):hover {background-color: rgb(255, 20, 25)}
			.browser i:nth-child(2) {background-color: rgb(255, 183, 42)}
			.browser i:nth-child(2):hover {background-color: rgb(230, 175, 42)}
			.browser i:nth-child(3) {background-color: rgb(37, 198, 58)}
			.browser i:nth-child(3):hover {background-color: rgb(10, 225, 10)}
			.browser input {
				font-size: 0.75em;
				vertical-align: top;
				display: inline-block;
				height: 1.6em;
				color: #aaa;
				width: calc(100% - 6em);
				border: 0.1em solid #E1E1E1;
				border-radius: 0.25em;
				background-color: #eee;
				margin: 0.1em;
				padding: 0 0.4em;
			}
			.browser-container {
				height: 100%;
				width: 100%;
				overflow-x: hidden;
				overflow-y: auto;
				text-align: center;
			}
			.button-redsys {
				display:none;
				visibility: hidden;
			}
			</style>
		<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="redsys">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-redsys" id="submit_redsys_payment_form" value="" />
		</form>
		<div class="browser">
				<div class="browser-navigation-bar">
					<i></i><i></i><i></i>
					<!-- Place your URL into <input> below -->
					<input value="' . esc_url( $redsys_adr ) . '" disabled />
				</div>
				<div class="browser-container">
					<!-- Place your content of any type here -->
					<iframe name="redsys" src="" class="iframe_3DS_Challenge" width="800" height="1000" frameBorder="0"></iframe>
				</div>
			</div>';
	}

	/**
	 * Generate the redsys form
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return string
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function generate_redsys_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '   Generating Redsys Form     ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr      = $this->get_redsys_url_gateway( $user_id );
		$redsys_args     = $this->get_redsys_args( $order );
		$form_inputs     = array();
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		// $post = substr($post, 0, -1);
		$time = '';
		$time = $this->redirectiontime;
		if ( empty( $time ) ) {
			wc_enqueue_js(
				' $("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Servired/RedSys to make the payment.', 'woocommerce-redsys' ) . '",
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
		} else {
			wc_enqueue_js(
				'
				setTimeout(function ()
					{
					$("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Servired/RedSys to make the payment.', 'woocommerce-redsys' ) . '",
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
					}, ' . esc_html( $time ) . ');
				'
			);
		}
		return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Credit Card via Servired/RedSys', 'woocommerce-redsys' ) . '" />
		<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
		</form>';
	}

	/**
	 * Generate the redsys Subscription form
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return string
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function generate_redsys_subscription_form_browser( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***********************************************/' );
			$this->log->add( 'preauthorizationsredsys', '   Generating Redsys Subscription Form Browser   ' );
			$this->log->add( 'preauthorizationsredsys', '/***********************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', 'function generate_redsys_subscription_form()' );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
			$this->log->add( 'preauthorizationsredsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$redsys_adr  = $this->get_redsys_url_gateway( $user_id );
		$redsys_args = $this->get_redsys_args( $order );
		$form_inputs = array();
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		wc_enqueue_js( 'jQuery("#submit_redsys_payment_form").click();' );
		return '
			<style>
			.browser {
				font-size: 18px;
				padding: 2.1em 0 0 0;
				border-radius: 0.25em;
				background: #ddd;
				display: inline-block;
				position: relative;
				overflow: hidden;
				box-shadow: 0 0.25em 0.9em -0.1em rgba(0,0,0,.3);
			}
			.browser .browser-navigation-bar {
				display: block;
				box-sizing: border-box;
				height: 2.1em;
				position: absolute;
				top: 0;
				padding: 0.3em;
				width: 100%;
				background: linear-gradient(to bottom, #edeaed 0%, #dddfdd 100%);
				border-bottom: 2px solid #cbcbcb;
			}
			.browser i {
				display: inline-block;
				height: 0.7em;
				width: 0.7em;
				border-radius: 0.45em;
				background-color: #eee;
				margin: 0.4em 0.15em;
			}
			.browser i:nth-child(1) {background-color: rgb(255, 86, 79)}
			.browser i:nth-child(1):hover {background-color: rgb(255, 20, 25)}
			.browser i:nth-child(2) {background-color: rgb(255, 183, 42)}
			.browser i:nth-child(2):hover {background-color: rgb(230, 175, 42)}
			.browser i:nth-child(3) {background-color: rgb(37, 198, 58)}
			.browser i:nth-child(3):hover {background-color: rgb(10, 225, 10)}
			.browser input {
				font-size: 0.75em;
				vertical-align: top;
				display: inline-block;
				height: 1.6em;
				color: #aaa;
				width: calc(100% - 6em);
				border: 0.1em solid #E1E1E1;
				border-radius: 0.25em;
				background-color: #eee;
				margin: 0.1em;
				padding: 0 0.4em;
			}
			.browser-container {
				height: 100%;
				width: 100%;
				overflow-x: hidden;
				overflow-y: auto;
				text-align: center;
			}
			</style>
		<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="redsys">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="" />
		</form>
		<div class="browser">
				<div class="browser-navigation-bar">
					<i></i><i></i><i></i>
					<!-- Place your URL into <input> below -->
					<input value="' . esc_url( $redsys_adr ) . '" disabled />
				</div>
				<div class="browser-container">
					<!-- Place your content of any type here -->
					<iframe name="redsys" src="" class="iframe_3DS_Challenge" width="800" height="1000" frameBorder="0"></iframe>
				</div>
			</div>';
	}
	/**
	 * Generate the redsys Subscription form
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return string
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function generate_redsys_subscription_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '   Generating Redsys Subscription Form   ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$redsys_adr  = $this->get_redsys_url_gateway( $user_id );
		$redsys_args = $this->get_redsys_args( $order );
		$form_inputs = array();
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		// $post = substr($post, 0, -1);
		wc_enqueue_js(
			'$("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Servired/RedSys to make the payment.', 'woocommerce-redsys' ) . '",
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
		return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Credit Card via Servired/RedSys', 'woocommerce-redsys' ) . '" />
		<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
		</form>';
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function process_subscription_payment( $order, $amount = 0 ) {

		if ( 0 == $amount ) {

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '                  Amount 0               ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			// Payment complete
			$order->payment_complete();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '            Order Complete               ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			return true;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '      scheduled charge Amount: ' . $amount );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$order_total_sign    = '';
		$transaction_id2     = '';
		$transaction_type    = '';
		$ds_merchant_terminal  = '';
		$final_notify_url    = '';
		$returnfromredsys    = '';
		$gatewaylanguage     = '';
		$currency            = '';
		$secretsha256        = '';
		$customer            = '';
		$url_ok              = '';
		$product_description = '';
		$merchant_name       = '';
		$order_id            = $order->get_id();
		$user_id             = $order->get_user_id();

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Generating Tokenized call   ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		$type       = 'ws';
		$order      = WCRed()->get_order( $order_id );
		$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', 'Using WS URL: ' . $redsys_adr );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		// $order_id = $order->get_id();.
		$currency_codes = WCRed()->get_currencies();

		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$order_total_sign = WCRed()->redsys_amount_format( $amount );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '  $transaction_type = 0.      ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***********************************************************************************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Are you interested in preaturizartion on subscriptions?, speak with me j.conti@joseconti.com   ' );
			$this->log->add( 'preauthorizationsredsys', '/***********************************************************************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$transaction_type = '0';

		$gatewaylanguage = $this->redsyslanguage;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
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
				$ds_merchant_terminal = $terminal2;
			} else {
				$ds_merchant_terminal = $terminal;
			}
		} else {
			$ds_merchant_terminal = $this->terminal;
		}

		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$customer_token = WCRed()->get_users_token_bulk( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$customer_token: ' . $customer_token );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$redsys_data_send    = array();
		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$customer            = $this->customer;
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, $this->id );
		$merchant_name       = $this->commercename;

		$redsys_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => $transaction_id2,
			'transaction_type'    => $transaction_type,
			'DSMerchantTerminal'  => $ds_merchant_terminal,
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Using filter redsys_modify_data_to_send' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
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
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', '$order: ' . $orderid2 );
			$this->log->add( 'preauthorizationsredsys', '$customer: ' . $customer );
			$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
			$this->log->add( 'preauthorizationsredsys', '$transaction_type: 0' );
			$this->log->add( 'preauthorizationsredsys', '$terminal: ' . $terminal );
			$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $gatewaylanguage );
			$this->log->add( 'preauthorizationsredsys', '$final_notify_url: ' . $final_notify_url );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$mi_obj = new RedsysAPIWs();

		if ( ! empty( $this->merchantgroup ) ) {
			$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
		} else {
			$ds_merchant_group = '';
		}

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

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '          The call            ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', $DATOS_ENTRADA );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$XML  = '<REQUEST>';
		$XML .= $DATOS_ENTRADA;
		$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$XML .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
		$XML .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', $XML );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$CLIENTE  = new SoapClient( $redsys_adr );
		$response = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

		if ( isset( $response->trataPeticionReturn ) ) {
			$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn );
			if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) {
				$RESPUESTA = (int) $xml_retorno->OPERACION->Ds_Response;
				if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Response: Ok > ' . $RESPUESTA );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$order->payment_complete();
					update_post_meta( $order_id, 'redsys_done', 'yes' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					}
					return true;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Response: Error > ' . $RESPUESTA );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					return new WP_Error( 'Redsys', __( 'There was an error:' . $RESPUESTA, 'woocommerce-redsys' ) );
				}
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function doing_scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$order_id    = $renewal_order->get_id();
		$redsys_done = get_post_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '       Once upon a time       ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '       Payment is complete EXIT          ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
			}
			return;
		} else {

			$order  = $renewal_order;
			$amount = $amount_to_charge;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/**********************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'preauthorizationsredsys', '/**********************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '   scheduled charge Amount: ' . $amount );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$order_total_sign    = '';
			$transaction_id2     = '';
			$transaction_type    = '';
			$ds_merchant_terminal  = '';
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Generating Tokenized call   ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
				$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			// $order_id = $order->get_id();.
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$transaction_type = '1';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
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
					$ds_merchant_terminal = $terminal2;
				} else {
					$ds_merchant_terminal = $terminal;
				}
			} else {
				$ds_merchant_terminal = $this->terminal;
			}

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$customer_token = WCRed()->get_users_token_bulk( $user_id, 'R' );
				$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
				$txnid          = WCRed()->get_txnid( $customer_token_id );
			} else {
				$customer_token = WCRed()->get_users_token_bulk( $user_id );
			}

			if ( ! $customer_token || empty( $customer_token ) || '' === trim( $customer_token ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '$customer_token: NO Token or expired Credit Card' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$url = 'URL: ' . WCRed()->get_post_edit_url( $order_id );
				$order->add_order_note( esc_html__( 'No credit card or expired', 'woocommerce-redsys' ) );
				$message = __( '⚠️ No credit card or expired', 'woocommerce-redsys' );
				WCRed()->add_subscription_note( $message, $order_id );
				WCRed()->push( $message . ' ' . $url );
				$renewal_order->update_status( 'failed' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'preauthorizationsredsys', ' ' );
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
				'DSMerchantTerminal'  => $ds_merchant_terminal,
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
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', '$order: ' . $orderid2 );
				$this->log->add( 'preauthorizationsredsys', '$customer: ' . $customer );
				$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
				$this->log->add( 'preauthorizationsredsys', '$transaction_type: 1' );
				$this->log->add( 'preauthorizationsredsys', '$terminal: ' . $terminal );
				$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
				$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'preauthorizationsredsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$mi_obj = new RedsysAPIWs();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}

			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$datos_usuario = array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocolVersion,
					'browserAcceptHeader' => $http_accept,
					'browserColorDepth'   => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'           => $browser_ip,
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '          The call            ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', $DATOS_ENTRADA );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$XML  = '<REQUEST>';
			$XML .= $DATOS_ENTRADA;
			$XML .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$XML .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
			$XML .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', $XML );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {

				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->iniciaPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->iniciaPeticionReturn ) ) {
					$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn );
					$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					// $this->log->add( 'preauthorizationsredsys', '$acctinfo: ' . $acctinfo );
					$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
				}

				$ds_emv3ds_json       = $xml_retorno->INFOTARJETA->Ds_EMV3DS;
				$ds_emv3ds            = json_decode( $ds_emv3ds_json );
				$protocolVersion      = $ds_emv3ds->protocolVersion;
				$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
				$three_ds_info          = $ds_emv3ds->threeDSInfo;

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
					$this->log->add( 'preauthorizationsredsys', '$threeDSServerTransID: ' . $threeDSServerTransID );
					$this->log->add( 'preauthorizationsredsys', '$three_ds_info: ' . $three_ds_info );
				}

				if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

					$datos_usuario = array(
						'threeDSInfo'              => 'AuthenticationData',
						'protocolVersion'          => $protocolVersion,
						'browserAcceptHeader'      => WCPSD2()->get_accept_headers_user( $user_id ),
						'browserColorDepth'        => WCPSD2()->get_profundidad_color_user( $user_id ),
						'browserIP'                => '86.0.4240.111',
						'browserJavaEnabled'       => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserJavascriptEnabled' => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserLanguage'          => WCPSD2()->get_idioma_navegador_user( $user_id ),
						'browserScreenHeight'      => WCPSD2()->get_altura_pantalla_user( $user_id ),
						'browserScreenWidth'       => WCPSD2()->get_anchura_pantalla_user( $user_id ),
						'browserTZ'                => WCPSD2()->get_diferencia_horaria_user( $user_id ),
						'browserUserAgent'         => WCPSD2()->get_agente_navegador_user( $user_id ),
						'threeDSServerTransID'     => $threeDSServerTransID,
						'notificationURL'          => $final_notify_url,
						'threeDSCompInd'           => 'N',
					);
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: AuthenticationData' );
						$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
					}
					$acctinfo       = WCPSD2()->get_acctinfo( $order, $datos_usuario );
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
					$XML           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
					$XML           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$CLIENTE    = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

					if ( isset( $responsews->trataPeticionReturn ) ) {
						$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn );
						$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
						$codigo            = json_decode( $xml_retorno->CODIGO );
						$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
						$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
						$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
						$this->log->add( 'preauthorizationsredsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
					}
					if ( $authorisationcode ) {
						update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						/**
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						 */
					}
				} else {
					$protocolVersion = '1.0.2';
					$datos_usuario   = array(
						'threeDSInfo'         => 'AuthenticationData',
						'protocolVersion'     => $protocolVersion,
						'browserAcceptHeader' => WCPSD2()->get_accept_headers_user( $user_id ),
						'browserColorDepth'   => WCPSD2()->get_profundidad_color_user( $user_id ),
						'browserIP'           => '86.0.4240.111',
						'browserJavaEnabled'  => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserLanguage'     => WCPSD2()->get_idioma_navegador_user( $user_id ),
						'browserScreenHeight' => WCPSD2()->get_altura_pantalla_user( $user_id ),
						'browserScreenWidth'  => WCPSD2()->get_anchura_pantalla_user( $user_id ),
						'browserTZ'           => WCPSD2()->get_diferencia_horaria_user( $user_id ),
						'browserUserAgent'    => WCPSD2()->get_agente_navegador_user( $user_id ),
						'notificationURL'     => $final_notify_url,
						'threeDSCompInd'      => 'N',
					);
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
					// $DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
					// $DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
					// $DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
					$DATOS_ENTRADA .= '</DATOSENTRADA>';
					$XML            = '<REQUEST>';
					$XML           .= $DATOS_ENTRADA;
					$XML           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$XML           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . '</DS_SIGNATURE>';
					$XML           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$CLIENTE    = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

					if ( isset( $responsews->trataPeticionReturn ) ) {
						$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
						// $respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$responsews: ' . print_r( $responsews, true ) );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
					}
					$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
					$codigo            = json_decode( $xml_retorno->CODIGO );
					$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
					$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
					$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );

					if ( $authorisationcode ) {
						update_post_meta( $order_id, '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						/**
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						 */
					}
				}
			} else {
				$CLIENTE    = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->trataPeticion( array( 'datoEntrada' => $XML ) );

				if ( isset( $responsews->trataPeticionReturn ) ) {
					$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
					if ( isset( $xml_retorno->CODIGO ) ) {
						if ( '0' === (string) $xml_retorno->CODIGO ) {
							if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) {
								$RESPUESTA = (int) $xml_retorno->OPERACION->Ds_Response;
								if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'preauthorizationsredsys', ' ' );
										$this->log->add( 'preauthorizationsredsys', 'Response: Ok > ' . $RESPUESTA );
										$this->log->add( 'preauthorizationsredsys', ' ' );
									}
									update_post_meta( $order_id, '_redsys_done', 'yes' );
								} else {
									// Ha habido un problema en el cobro
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'preauthorizationsredsys', ' ' );
										$this->log->add( 'preauthorizationsredsys', 'Response: Error > ' . $RESPUESTA );
										$this->log->add( 'preauthorizationsredsys', ' ' );
									}
									$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . $RESPUESTA );
									$renewal_order->update_status( 'failed' );
								}
							} else {
								// No hay $xml_retorno->OPERACION->Ds_Response
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', 'Error > No hay $xml_retorno->OPERACION->Ds_Response' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
								$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
								$renewal_order->update_status( 'failed' );
							}
						} else {
							// $xml_retorno->CODIGO es diferente a 0
							$error_code = WCRed()->get_error_by_code( (string) $xml_retorno->CODIGO );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', 'Error > $xml_retorno->CODIGO es diferente a 0. Error: ' . (string) $xml_retorno->CODIGO . '->' . $error_code );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
							if ( $error_code ) {
								// Enviamos email al adminsitrador avisando de este problema
								$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . (string) $xml_retorno->CODIGO . ': ' . $error_code );
								if ( $this->notiemail ) {
									$to      = $his->notiemail;
									$subject = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
									$body    = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
									$body    = __( 'The error was: ', 'woocommerce-redsys' );
									$body   .= $error_code;
									$headers = array( 'Content-Type: text/html; charset=UTF-8' );
									wp_mail( $to, $subject, $body, $headers );
								}

							}
							$renewal_order->update_status( 'failed' );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
					} else {
						// No hay $xml_retorno->CODIGO
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', 'Error > No hay $xml_retorno->CODIGO' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
						$renewal_order->update_status( 'failed' );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
					}
				} else {
					// No hay $responsews->trataPeticionReturn
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Error > No hay $responsews->trataPeticionReturn' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
					$renewal_order->update_status( 'failed' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
				}
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function process_payment( $order_id ) {
		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$psd2            = WCRed()->get_redsys_option( 'psd2', 'redsys' );
		$usetokensdirect = $this->usetokensdirect;
		$terminal2       = $this->terminal2;
		$terminal        = $this->terminal;
		$url_ok          = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );

		if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			$http_accept = $_SERVER['HTTP_ACCEPT'];
			update_post_meta( $order_id, '_accept_haders', $http_accept );
			update_user_meta( $user_id, '_accept_haders', $http_accept );
		} else {
			$http_accept = "text\/html,application\/xhtml+xml,application\/xml;q=0.9,*\/*;q=0.8";
			update_post_meta( $order_id, '_accept_haders', $http_accept );
			update_user_meta( $user_id, '_accept_haders', $http_accept );
		}

		if ( 'yes' === $psd2 && 'yes' === $usetokensdirect ) {
			$customer_token_r = WCRed()->get_redsys_users_token( 'R' );
			$customer_token_c = WCRed()->get_redsys_users_token( 'C' );
			$customer_token_r_id = WCRed()->get_redsys_users_token( 'R', 'id' );
			$customer_token_c_id = WCRed()->get_redsys_users_token( 'C', 'id' );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '$psd2: YES' );
				$this->log->add( 'preauthorizationsredsys', '$usetokensdirect: YES' );
				$this->log->add( 'preauthorizationsredsys', '$customer_token_r: ' . $customer_token_r );
				$this->log->add( 'preauthorizationsredsys', '$customer_token_c: ' . $customer_token_c );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			if ( $this->order_contains_subscription( $order_id ) && 'yes' !== $this->subsusetokensdisable ) {

				if ( $customer_token_r ) {

					$customer_token      = $customer_token_r;
					$cof_txnid           = WCRed()->get_txnid( $customer_token_r );
					$mi_obj               = new RedsysAPIWs();
					$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
					$orderid2            = WCRed()->prepare_order_number( $order_id );
					$user_id             = $order->get_user_id();
					$customer            = $this->customer;
					$transaction_type    = '1';
					$currency_codes      = WCRed()->get_currencies();
					$currency            = $currency_codes[ get_woocommerce_currency() ];
					$cof_ini             = 'N';
					$cof_type            = 'R';
					$cof_txnid           = WCRed()->get_txnid( $customer_token_c );
					$secretsha256        = $this->get_redsys_sha256( $user_id );
					$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
					$product_description = WCRed()->product_description( $order, 'redsys' );
					$merchant_name       = $this->commercename;
					$type                = 'ws';
					$redsys_adr          = $this->get_redsys_url_gateway( $user_id, $type );
					if ( 'yes' === $this->useterminal2 ) {
						$toamount  = number_format( $this->toamount, 2, '', '' );
						$terminal  = $this->terminal;
						$terminal2 = $this->terminal2;
						if ( $order_total_sign <= $toamount ) {
							$ds_merchant_terminal = $terminal2;
						} else {
							$ds_merchant_terminal = $terminal;
						}
					} else {
						$ds_merchant_terminal = $this->terminal;
					}
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
						$this->log->add( 'preauthorizationsredsys', '$orderid2: ' . $orderid2 );
						$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
						$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
						$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
						$this->log->add( 'preauthorizationsredsys', '$cof_ini: ' . $cof_ini );
						$this->log->add( 'preauthorizationsredsys', '$cof_type: ' . $cof_type );
						$this->log->add( 'preauthorizationsredsys', '$cof_txnid: ' . $cof_txnid );
						$this->log->add( 'preauthorizationsredsys', '$product_description: ' . $product_description );
						$this->log->add( 'preauthorizationsredsys', '$secretsha256: ' . $secretsha256 );
						$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
						$this->log->add( 'preauthorizationsredsys', '$merchant_name: ' . $merchant_name );
						$this->log->add( 'preauthorizationsredsys', '$type: ' . $type );
						$this->log->add( 'preauthorizationsredsys', '$redsys_adr: ' . $redsys_adr );
						$this->log->add( 'preauthorizationsredsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					
					$datos_usuario = array(
						'threeDSInfo'          => 'AuthenticationData',
						'protocolVersion'      => $protocolVersion,
						'browserAcceptHeader'  => $http_accept,
						'browserColorDepth'    => WCPSD2()->get_profundidad_color( $order_id ),
						'browserIP'            => $browser_ip,
						'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled( $order_id ),
						'browserLanguage'      => WCPSD2()->get_idioma_navegador( $order_id ),
						'browserScreenHeight'  => WCPSD2()->get_altura_pantalla( $order_id ),
						'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla( $order_id ),
						'browserTZ'            => WCPSD2()->get_diferencia_horaria( $order_id ),
						'browserUserAgent'     => WCPSD2()->get_agente_navegador( $order_id ),
						'notificationURL'      => $final_notify_url,
					);
					//$acctinfo       = WCPSD2()->get_acctinfo( $order, false , $user_id );
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
					$DATOS_ENTRADA .= "</DATOSENTRADA>";
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The call            ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $DATOS_ENTRADA );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}

					$XML = "<REQUEST>";
					$XML .= $DATOS_ENTRADA;
					$XML .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
					$XML .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
					$XML .= "</REQUEST>";
		
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
			
					$CLIENTE  = new SoapClient( $redsys_adr );
					$response = $CLIENTE->iniciaPeticion( array( "datoEntrada" => $XML ) );
					
					if ( isset( $response->iniciaPeticionReturn ) ) {
						$xml_retorno = new SimpleXMLElement( $response->iniciaPeticionReturn );
						$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
					}
	
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						//$this->log->add( 'preauthorizationsredsys', '$acctinfo: ' . $acctinfo );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
					}
					
					$ds_emv3ds_json       = $xml_retorno->INFOTARJETA->Ds_EMV3DS;
					$ds_emv3ds            = json_decode( $ds_emv3ds_json );
					$protocolVersion      = $ds_emv3ds->protocolVersion;
					$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
					$three_ds_info          = $ds_emv3ds->threeDSInfo;
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
						$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
						$this->log->add( 'preauthorizationsredsys', '$threeDSServerTransID: ' . $threeDSServerTransID );
						$this->log->add( 'preauthorizationsredsys', '$three_ds_info: ' . $three_ds_info );
					}

					if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

						$datos_usuario = array(
							'threeDSInfo'              => 'AuthenticationData',
							'protocolVersion'          => $protocolVersion,
							'browserAcceptHeader'      => WCPSD2()->get_accept_headers_user( $user_id ),
							'browserColorDepth'        => WCPSD2()->get_profundidad_color_user( $user_id ),
							'browserIP'                => '86.0.4240.111',
							'browserJavaEnabled'       => WCPSD2()->get_browserjavaenabled_user( $user_id ),
							'browserJavascriptEnabled' => WCPSD2()->get_browserjavaenabled_user( $user_id ),
							'browserLanguage'          => WCPSD2()->get_idioma_navegador_user( $user_id ),
							'browserScreenHeight'      => WCPSD2()->get_altura_pantalla_user( $user_id ),
							'browserScreenWidth'       => WCPSD2()->get_anchura_pantalla_user( $user_id ),
							'browserTZ'                => WCPSD2()->get_diferencia_horaria_user( $user_id ),
							'browserUserAgent'         => WCPSD2()->get_agente_navegador_user( $user_id ),
							'threeDSServerTransID'     => $threeDSServerTransID,
							'notificationURL'          => $final_notify_url,
							'threeDSCompInd'           => 'N',
						);
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: AuthenticationData' );
							$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
						}
						$acctinfo       = WCPSD2()->get_acctinfo( $order, $datos_usuario );
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
						//$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
						$DATOS_ENTRADA .= '</DATOSENTRADA>';
						$XML            = "<REQUEST>";
						$XML           .= $DATOS_ENTRADA;
						$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
						$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
						$XML           .= "</REQUEST>";
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', $XML );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						$CLIENTE  = new SoapClient( $redsys_adr );
						$response = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
						
						if ( isset( $response->trataPeticionReturn ) ) {
							$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn );
							$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
							$codigo            = json_decode( $xml_retorno->CODIGO );
							$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
							$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
							$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );
						}
		
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
							$this->log->add( 'preauthorizationsredsys', 'Ds_AuthorisationCode: ' .$authorisationcode );
						}
						if ( $authorisationcode ) {
							update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
							$order->payment_complete();
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
	
							if ( ! empty( $redsys_order ) ) {
								update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $terminal ) ) {
								update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $authorisationcode ) ) {
								update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $currency_code ) ) {
								update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $secretsha256 ) ) {
								update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							}
							return array(
								'result'   => 'success',
								'redirect' => $url_ok,
							);
						} else {
							// TO-DO: Enviar un correo con el problema al administrador
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						}
					} else {
						$protocolVersion = '1.0.2';
						$datos_usuario   = array(
							'threeDSInfo'          => 'AuthenticationData',
							'protocolVersion'      => $protocolVersion,
							'browserAcceptHeader'  => WCPSD2()->get_accept_headers_user( $user_id ),
							'browserColorDepth'    => WCPSD2()->get_profundidad_color_user( $user_id ),
							'browserIP'            => '86.0.4240.111',
							'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled_user( $user_id ),
							'browserLanguage'      => WCPSD2()->get_idioma_navegador_user( $user_id ),
							'browserScreenHeight'  => WCPSD2()->get_altura_pantalla_user( $user_id ),
							'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla_user( $user_id ),
							'browserTZ'            => WCPSD2()->get_diferencia_horaria_user( $user_id ),
							'browserUserAgent'     => WCPSD2()->get_agente_navegador_user( $user_id ),
							'notificationURL'      => $final_notify_url,
							'threeDSCompInd'       => 'N',
						);
						$data   = array(
							'threeDSInfo'          => 'AuthenticationData',
							'protocolVersion'      => '1.0.2',
						);
						$need = wp_json_encode( $data );
						$acctinfo       = WCPSD2()->get_acctinfo( $order, $datos_usuario );
						$DATOS_ENTRADA = "<DATOSENTRADA>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_AMOUNT>" . $order_total_sign . "</DS_MERCHANT_AMOUNT>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_ORDER>" . $orderid2 . "</DS_MERCHANT_ORDER>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTCODE>" . $customer . "</DS_MERCHANT_MERCHANTCODE>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_TERMINAL>" . $terminal . "</DS_MERCHANT_TERMINAL>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_CURRENCY>" . $currency . "</DS_MERCHANT_CURRENCY>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>";
						$DATOS_ENTRADA .= "<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>";
						$DATOS_ENTRADA .= $ds_merchant_group;
						$DATOS_ENTRADA .= "<DS_MERCHANT_IDENTIFIER>" . $customer_token . "</DS_MERCHANT_IDENTIFIER>";
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
						//$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
						//$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
						//$DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
						$DATOS_ENTRADA .= "</DATOSENTRADA>";
						$XML            = "<REQUEST>";
						$XML           .= $DATOS_ENTRADA;
						$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
						$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
						$XML           .= "</REQUEST>";
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', $XML );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						$CLIENTE  = new SoapClient( $redsys_adr );
						$response = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
						
						if ( isset( $response->trataPeticionReturn ) ) {
							$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn );
							//$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
						}
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '$response: ' . print_r( $response, true ) );
							$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
						}
						$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
						$codigo            = json_decode( $xml_retorno->CODIGO );
						$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
						$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
						$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );
						
						if ( $authorisationcode ) {
							update_post_meta( $order_id, '_redsys_done', 'yes' );
							$order->payment_complete();
							$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
	
							if ( ! empty( $redsys_order ) ) {
								update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $terminal ) ) {
								update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $authorisationcode ) ) {
								update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $currency_code ) ) {
								update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $secretsha256 ) ) {
								update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							}
							return array(
								'result'   => 'success',
								'redirect' => $url_ok,
							);
						} else {
							// TO-DO: Enviar un correo con el problema al administrador
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
						}
					}
				} else {
					if ( 'yes' === $this->usebrowserreceipt ) {
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ) . '#redsys_payment_form',
						);
					} else {
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ),
						);
					}
				}
			} else {
				if ( $customer_token_c ) {
					// Pay with 1 clic & token exist.
					
					$mi_obj               = new RedsysAPIWs();
					$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
					$orderid2            = WCRed()->prepare_order_number( $order_id );
					$user_id             = $order->get_user_id();
					$customer            = $this->customer;
					$transaction_type    = '1';
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
					$redsys_adr          = $this->get_redsys_url_gateway( $user_id, $type );
					if ( 'yes' === $this->useterminal2 ) {
						$toamount  = number_format( $this->toamount, 2, '', '' );
						$terminal  = $this->terminal;
						$terminal2 = $this->terminal2;
						if ( $order_total_sign <= $toamount ) {
							$ds_merchant_terminal = $terminal2;
						} else {
							$ds_merchant_terminal = $terminal;
						}
					} else {
						$ds_merchant_terminal = $this->terminal;
					}
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
						$this->log->add( 'preauthorizationsredsys', '$orderid2: ' . $orderid2 );
						$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
						$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
						$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
						$this->log->add( 'preauthorizationsredsys', '$cof_ini: ' . $cof_ini );
						$this->log->add( 'preauthorizationsredsys', '$cof_type: ' . $cof_type );
						$this->log->add( 'preauthorizationsredsys', '$cof_txnid: ' . $cof_txnid );
						$this->log->add( 'preauthorizationsredsys', '$product_description: ' . $product_description );
						$this->log->add( 'preauthorizationsredsys', '$secretsha256: ' . $secretsha256 );
						$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
						$this->log->add( 'preauthorizationsredsys', '$merchant_name: ' . $merchant_name );
						$this->log->add( 'preauthorizationsredsys', '$type: ' . $type );
						$this->log->add( 'preauthorizationsredsys', '$redsys_adr: ' . $redsys_adr );
						$this->log->add( 'preauthorizationsredsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					if ( $order_total_sign <= 3000 ) {
						$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
					} else {
						$lwv = '';
					}
					if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int)$this->traamount ) && $order_total_sign > 3000 ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', 'Using TRA' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
					}
					$DATOS_ENTRADA = '<DATOSENTRADA>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
					$DATOS_ENTRADA .= $lwv;
					$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
					$DATOS_ENTRADA .= '</DATOSENTRADA>';
					
					$XML            = "<REQUEST>";
					$XML           .= $DATOS_ENTRADA;
					$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
					$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
					$XML           .= "</REQUEST>";
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
						$this->log->add( 'preauthorizationsredsys', '$XML: ' . $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					
					$CLIENTE        = new SoapClient( $redsys_adr );
					$response       = $CLIENTE->iniciaPeticion( array( "datoEntrada" => $XML ) );
					
					if ( isset( $response->iniciaPeticionReturn ) ) {
						$xml_retorno  = new SimpleXMLElement( $response->iniciaPeticionReturn );
						$respuesta    = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
					}
					$protocolVersion      = '';
					$ds_card_psd2         = '';
					$threeDSServerTransID = '';
					$three_ds_info          = '';
					$three_ds_method_url     = '';
					if ( isset( $respuesta->protocolVersion ) ) {
						$protocolVersion = trim( $respuesta->protocolVersion );
					}
					if ( isset( $xml_retorno->INFOTARJETA->Ds_Card_PSD2 ) ) {
						$ds_card_psd2 = trim( $xml_retorno->INFOTARJETA->Ds_Card_PSD2 );
					}
					if ( isset( $respuesta->threeDSServerTransID ) ) {
						$threeDSServerTransID = trim( $respuesta->threeDSServerTransID );
					}
					if ( isset( $respuesta->threeDSInfo ) ) {
						$three_ds_info = trim( $respuesta->threeDSInfo );
					}
					if ( isset( $respuesta->threeDSMethodURL ) ) {
						$three_ds_method_url = trim( $respuesta->threeDSMethodURL );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
						$this->log->add( 'preauthorizationsredsys', '$respuesta: ' . print_r( $respuesta, true ) );
						$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
						$this->log->add( 'preauthorizationsredsys', 'threeDSServerTransID: ' . $threeDSServerTransID );
						$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: ' . $three_ds_info );
						$this->log->add( 'preauthorizationsredsys', 'threeDSMethodURL: ' . $three_ds_method_url );
						$this->log->add( 'preauthorizationsredsys', 'Ds_Card_PSD2: ' . $ds_card_psd2 );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					
					if ( ( 'NO_3DS_v2' ===  $protocolVersion ||  ( '1.0.2' ===  $protocolVersion ) && 'Y' === $ds_card_psd2 ) ) {
						// Es protocolo 1.0.2
						$protocolVersion = '1.0.2';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Es Protocolo NO_3DS_v2 (1.0.2) y PSD2' );
						}
						if ( 'yes' === $this->not_use_https ) {
							$final_notify_url = $this->notify_url_not_https;
						} else {
							$final_notify_url = $this->notify_url;
						}
						$browser_ip = WCRed()->get_the_ip();
						$datos_usuario = array(
							'threeDSInfo'          => 'AuthenticationData',
							'protocolVersion'      => $protocolVersion,
							'browserAcceptHeader'  => $http_accept,
							'browserColorDepth'    => WCPSD2()->get_profundidad_color( $order_id ),
							'browserIP'            => $browser_ip,
							'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled( $order_id ),
							'browserLanguage'      => WCPSD2()->get_idioma_navegador( $order_id ),
							'browserScreenHeight'  => WCPSD2()->get_altura_pantalla( $order_id ),
							'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla( $order_id ),
							'browserTZ'            => WCPSD2()->get_diferencia_horaria( $order_id ),
							'browserUserAgent'     => WCPSD2()->get_agente_navegador( $order_id ),
							'notificationURL'      => $final_notify_url,
						);
						$needed = wp_json_encode( array(
							'threeDSInfo'         => 'AuthenticationData',
							'protocolVersion'     => $protocolVersion,
							'browserAcceptHeader' => $http_accept,
							'browserUserAgent'    => WCPSD2()->get_agente_navegador( $order_id ),
						) );
						$acctinfo = WCPSD2()->get_acctinfo( $order, $datos_usuario );
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '$acctinfo: ' . $acctinfo );
						}
						if ( $order_total_sign <= 3000 ) {
							$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
						} else {
							$lwv = '';
						}
						if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int)$this->traamount ) && $order_total_sign > 3000 ) {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', 'Using TRA' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
							$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
						}
						$DATOS_ENTRADA = '<DATOSENTRADA>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
						$DATOS_ENTRADA .= $lwv;
						$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
						$DATOS_ENTRADA .= '</DATOSENTRADA>';
					} elseif ( ( ( '2.1.0' ===  $protocolVersion ) || ( '2.2.0' === $protocolVersion )  ) && ( 'Y' === $ds_card_psd2 ) ) {
						// Es protocolo 2.1.0
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Es Protocolo 2.1.0 y PSD2' );
						}
						
						$http_accept = WCPSD2()->get_accept_headers( $order_id );
						
						if ( 'yes' === $this->not_use_https ){
							$final_notify_url = $this->notify_url_not_https;
						} else {
							$final_notify_url = $this->notify_url;
						}
						$browser_ip     = WCRed()->get_the_ip();
						
						set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
						set_transient( 'accept_headers_' . $order_id, $http_accept, 300 );
						set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
						set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
						set_transient( 'threeDSServerTransID_' . $order_id, $threeDSServerTransID, 300 );
						set_transient( 'threeDSMethodURL_' . $order_id, $three_ds_method_url, 300 );
						set_transient( 'amount_' . $order_id, $order_total_sign, 300 );
						set_transient( 'order_' . $order_id, $orderid2, 300 );
						set_transient( 'terminal_' . $order_id, $ds_merchant_terminal, 300 );
						set_transient( 'currency_' . $order_id, $currency, 300 );
						set_transient( 'identifier_' . $order_id, $customer_token_c, 300 );
						set_transient( 'cof_ini_' . $order_id, $cof_ini, 300 );
						set_transient( 'cof_type_' . $order_id, $cof_type, 300 );
						set_transient( 'cof_txnid_' . $order_id, $cof_txnid, 300 );
						set_transient( 'final_notify_url_' . $order_id, $final_notify_url, 300 );
						set_transient( $threeDSServerTransID, $order_id, 300 );
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '$threeDSServerTransID: ' . $threeDSServerTransID );
							$this->log->add( 'preauthorizationsredsys', '$final_notify_url: ' . $final_notify_url );
							$this->log->add( 'preauthorizationsredsys', '$three_ds_method_url: ' . $three_ds_method_url );
						}
						$data = array();
						$data = array(
							'threeDSServerTransID'         => $threeDSServerTransID,
							'threeDSMethodNotificationURL' => $final_notify_url,
						);
						$json_pre = wp_json_encode( $data );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '$json_pre: ' . $json_pre );
						}
						$json = base64_encode( $json_pre );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '$json: ' . $json );
						}
						
						$body = array(
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
							$this->log->add( 'preauthorizationsredsys', '$body: ' . print_r( $body, true ) );
							//$this->log->add( 'preauthorizationsredsys', '$options: ' . print_r( $options, true ) );
						}
						$response      = wp_remote_post( $three_ds_method_url, $options );
						if ( 'yes' === $this->debug ) {
							//$this->log->add( 'preauthorizationsredsys', '$response: ' . print_r( $response, true ) );
						}
						$response_code = wp_remote_retrieve_response_code( $response );
						$response_body = wp_remote_retrieve_body( $response );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '$response_body: ' . $response_body );
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
								$this->log->add( 'preauthorizationsredsys', 'URL y threeDSMethodData coinciden' );
							}
							$threeDSCompInd = 'Y';
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', 'URL y threeDSMethodData NO coinciden' );
							}
							$threeDSCompInd = 'N';
						}
						
						if ( '2.2.0' === $protocolVersion ) {
							$datos_usuario = array(
								'threeDSInfo'              => 'AuthenticationData',
								'protocolVersion'          => $protocolVersion,
								'browserAcceptHeader'      => $http_accept,
								'browserColorDepth'        => WCPSD2()->get_profundidad_color( $order_id ),
								'browserIP'                => $browser_ip,
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
								'browserIP'            => $browser_ip,
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
							$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
							$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
							$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: AuthenticationData' );
							$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
							$this->log->add( 'preauthorizationsredsys', 'threeDSServerTransID: ' . $threeDSServerTransID );
							$this->log->add( 'preauthorizationsredsys', 'notificationURL: ' . $final_notify_url );
							$this->log->add( 'preauthorizationsredsys', 'threeDSCompInd: ' . $threeDSCompInd );
							$this->log->add( 'preauthorizationsredsys', 'acctInfo: : ' . $acctinfo );
						}
						$order_total_sign   = get_transient( 'amount_' . $order_id );
						$orderid2           = get_transient( 'order_' . $order_id );
						$customer           = $this->customer;
						$ds_merchant_terminal = get_transient( 'terminal_' . $order_id );
						$currency           = get_transient( 'currency_' . $order_id );
						$customer_token_c   = get_transient( 'identifier_' . $order_id  );
						$cof_ini            = get_transient( 'cof_ini_' . $order_id );
						$cof_type           = get_transient( 'cof_type_' . $order_id );
						$cof_txnid          = get_transient( 'cof_txnid_' . $order_id );
						
						if ( $order_total_sign <= 3000 ) {
							$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
						} else {
							$lwv = '';
						}
						if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int)$this->traamount ) && $order_total_sign > 3000 ) {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', 'Using TRA' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
							$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
						}
						$mi_obj = new RedsysAPIWs();
						
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
						$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>1</DS_MERCHANT_TRANSACTIONTYPE>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
						$DATOS_ENTRADA .= $ds_merchant_group;
						$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
						$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
						$DATOS_ENTRADA .= $lwv;
						$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
						$DATOS_ENTRADA .= '</DATOSENTRADA>';
						$XML            = "<REQUEST>";
						$XML           .= $DATOS_ENTRADA;
						$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
						$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
						$XML           .= "</REQUEST>";
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'The XML: ' . $XML );
						}

						$CLIENTE        = new SoapClient( $redsys_adr );
						$response       = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
						
						if ( isset( $response->trataPeticionReturn ) ) {
							$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn );
						}
						$Ds_EMV3DS         = $xml_retorno->OPERACION->Ds_EMV3DS;
						$json_decode       = json_decode( $Ds_EMV3DS );
						$three_ds_info       = $json_decode->threeDSInfo;
						$protocolVersion   = $json_decode->protocolVersion;
						$acsURL            = $json_decode->acsURL;
						$PAReq             = trim( $json_decode->{ 'PAReq'} );
						$MD                = $json_decode->MD;
						$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode );

						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
							$this->log->add( 'preauthorizationsredsys', 'Ds_EMV3DS: ' . $Ds_EMV3DS );
							$this->log->add( 'preauthorizationsredsys', '$three_ds_info: ' . $three_ds_info );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						
						if ( 'ChallengeRequest' === $three_ds_info ) {
							// hay challenge
							// Guardamos todo en transciends
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '/***************/' );
								$this->log->add( 'preauthorizationsredsys', '  Hay Challenge  ' );
								$this->log->add( 'preauthorizationsredsys', '/***************/' );
							}
							set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
							set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
							set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
							set_transient( 'PAReq_' . $order_id, $PAReq, 300 );
							set_transient( 'MD_' . $order_id, $MD, 300 );
							set_transient( $MD, $order_id, 300 );
							set_transient( 'amount_' . $MD, $order_total_sign, 300 );
							set_transient( 'order_' . $MD, $orderid2, 300 );
							set_transient( 'merchantcode_' . $MD, $customer, 300 );
							set_transient( 'terminal_' . $MD, $ds_merchant_terminal, 300 );
							set_transient( 'currency_' . $MD, $currency, 300 );
							set_transient( 'identifier_' . $MD, $customer_token_c, 300 );
							set_transient( 'cof_ini_' . $MD, $cof_ini, 300 );
							set_transient( 'cof_type_' . $MD, $cof_type, 300 );
							set_transient( 'cof_txnid_' . $MD, $cof_txnid, 300 );
							return array(
								'result'   => 'success',
								'redirect' => $order->get_checkout_payment_url( true ) . '#3DSform',
							);
						} elseif ( ! empty( $authorisationcode ) ) {
							// Pago directo sin challenge
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '/***************/' );
								$this->log->add( 'preauthorizationsredsys', '  Paid  ' );
								$this->log->add( 'preauthorizationsredsys', '/***************/' );
							}
							$Ds_Order        = trim( $xml_retorno->OPERACION->Ds_Order );
							$Ds_MerchantCode = trim( $xml_retorno->OPERACION->Ds_MerchantCode );
							$Ds_Terminal     = trim( $xml_retorno->OPERACION->Ds_Terminal );
	
							$order->payment_complete();
							$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
							$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
							$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );
							
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
								$this->log->add( 'preauthorizationsredsys', '/****************************/' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
	
							if ( ! empty( $Ds_Order ) ) {
								update_post_meta( $order->get_id(), '_payment_order_number_redsys', $Ds_Order );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $Ds_Order );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $dsdate ) ) {
								update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys saved: ' . $dsdate );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $Ds_Terminal ) ) {
								update_post_meta( $order->get_id(), '_payment_terminal_redsys', $Ds_Terminal );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $Ds_Terminal );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $dshour ) ) {
								update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys saved: ' . $dshour );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $authorisationcode ) ) {
								update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							if ( ! empty( $currency ) ) {
								update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							// This meta is essential for later use:
							if ( ! empty( $secretsha256 ) ) {
								update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
							}
							
							return array(
								'result'   => 'success',
								'redirect' => $url_ok,
							);
							
						} 
					}
					$XML  = "<REQUEST>";
					$XML .= $DATOS_ENTRADA;
					$XML .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
					$XML .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
					$XML .= "</REQUEST>";
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
						$this->log->add( 'preauthorizationsredsys', '$XML: ' . $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					
					$CLIENTE        = new SoapClient( $redsys_adr );
					$response       = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
					
					if ( isset( $response->trataPeticionReturn ) ) {
						$xml_retorno       = new SimpleXMLElement( $response->trataPeticionReturn );
						$codigo            = trim( $xml_retorno->CODIGO );
						$respuestaeds      = json_decode( $xml_retorno->OPERACION->Ds_EMV3DS );
						$three_ds_info       = trim( $respuestaeds->threeDSInfo );
						$protocolVersion   = trim( $respuestaeds->protocolVersion );
						$acsURL            = trim( $respuestaeds->acsURL );
						$PAReq             = trim( $respuestaeds->{ 'PAReq'} );
						$MD                = trim( $respuestaeds->MD );
						$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode );
						
					}
					if ( 'yes' === $this->debug && ! $authorisationcode ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$response: ' . print_r( $response, true ) );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
						$this->log->add( 'preauthorizationsredsys', '$codigo: ' . $codigo );
						$this->log->add( 'preauthorizationsredsys', '$respuesta: ' . print_r( $respuestaeds, true ) );
						$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
						$this->log->add( 'preauthorizationsredsys', 'threeDSServerTransID: ' . $respuestaeds->threeDSServerTransID );
						$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: ' . $three_ds_info );
						$this->log->add( 'preauthorizationsredsys', 'threeDSMethodURL: ' . $respuestaeds->threeDSMethodURL );
						$this->log->add( 'preauthorizationsredsys', 'Ds_Card_PSD2: ' . $ds_card_psd2 );
						$this->log->add( 'preauthorizationsredsys', '$three_ds_info: ' . $three_ds_info );
						$this->log->add( 'preauthorizationsredsys', '$protocolVersion: ' . $protocolVersion );
						$this->log->add( 'preauthorizationsredsys', '$acsURL: ' . $acsURL );
						$this->log->add( 'preauthorizationsredsys', '$PAReq: ' . $PAReq );
						$this->log->add( 'preauthorizationsredsys', '$MD: ' . $MD );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					
					if ( 'ChallengeRequest' === $three_ds_info ) {
						// hay challenge
						// Guardamos todo en transciends
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/***************/' );
							$this->log->add( 'preauthorizationsredsys', '  Hay Challenge  ' );
							$this->log->add( 'preauthorizationsredsys', '/***************/' );
						}
						set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
						set_transient( 'protocolVersion_' . $order_id, $protocolVersion, 300 );
						set_transient( 'acsURL_' . $order_id, $acsURL, 300 );
						set_transient( 'PAReq_' . $order_id, $PAReq, 300 );
						set_transient( 'MD_' . $order_id, $MD, 300 );
						set_transient( $MD, $order_id, 300 );
						set_transient( 'amount_' . $MD, $order_total_sign, 300 );
						set_transient( 'order_' . $MD, $orderid2, 300 );
						set_transient( 'merchantcode_' . $MD, $customer, 300 );
						set_transient( 'terminal_' . $MD, $ds_merchant_terminal, 300 );
						set_transient( 'currency_' . $MD, $currency, 300 );
						set_transient( 'identifier_' . $MD, $customer_token_c, 300 );
						set_transient( 'cof_ini_' . $MD, $cof_ini, 300 );
						set_transient( 'cof_type_' . $MD, $cof_type, 300 );
						set_transient( 'cof_txnid_' . $MD, $cof_txnid, 300 );
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ) . '#3DSform',
						);
					} elseif ( ! empty ( $authorisationcode ) ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/***************/' );
							$this->log->add( 'preauthorizationsredsys', '  Paid  ' );
							$this->log->add( 'preauthorizationsredsys', '/***************/' );
						}
						$Ds_Order        = trim( $xml_retorno->OPERACION->Ds_Order );
						$Ds_MerchantCode = trim( $xml_retorno->OPERACION->Ds_MerchantCode );
						$Ds_Terminal     = trim( $xml_retorno->OPERACION->Ds_Terminal );

						$order->payment_complete();
						$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
						$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
						$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );
						$order->add_order_note( __( 'Order Preauthorized by Redsys', 'woocommerce-redsys' ) );
						
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}

						if ( ! empty( $Ds_Order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $Ds_Order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $Ds_Order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $dsdate ) ) {
							update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys saved: ' . $dsdate );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $Ds_Terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $Ds_Terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $Ds_Terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $dshour ) ) {
							update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys saved: ' . $dshour );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $currency ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						// This meta is essential for later use:
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						
						return array(
							'result'   => 'success',
							'redirect' => $url_ok,
						);
					}
				} else {
					if ( 'yes' === $this->usebrowserreceipt ) {
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ) . '#redsys_payment_form',
						);
					} else {
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ),
						);
					}
				}
			}
		} else {
			if ( 'yes' === $this->usebrowserreceipt ) {
				return array(
					'result'   => 'success',
					'redirect' => $order->get_checkout_payment_url( true ) . '#redsys_payment_form',
				);
			} else {
				return array(
					'result'   => 'success',
					'redirect' => $order->get_checkout_payment_url( true ),
				);
			}
		}
	}
	public function renew_yith_subscription( $renewal_order = null, $is_manual_renew = null ) {

		if ( ! defined( 'YITH_YWSBS_PREMIUM' ) ) {
			return;
		}
		
		$order_id         = $renewal_order->get_id();
		$amount_to_charge = $renewal_order->get_total();
		$redsys_done      = get_post_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '       Once upon a time       ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '       Payment is complete EXIT          ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
			}
			return;
		} else {

			$order  = $renewal_order;
			$amount = $amount_to_charge;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/**********************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'preauthorizationsredsys', '/**********************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', '   scheduled charge Amount: ' . $amount    );
				$this->log->add( 'preauthorizationsredsys', '/***************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$order_total_sign    = '';
			$transaction_id2     = '';
			$transaction_type    = '';
			$ds_merchant_terminal  = '';
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Generating Tokenized call   ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
				$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			// $order_id = $order->get_id();.
			$currency_codes   = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$transaction_type = '1';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$transaction_type: ' . $transaction_type );
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
					$ds_merchant_terminal = $terminal2;
				} else {
					$ds_merchant_terminal = $terminal;
				}
			} else {
				$ds_merchant_terminal = $this->terminal;
			}

			if ( 'yes' === $this->not_use_https ){
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			if ( 'yes' === $this->psd2 ) {
				$customer_token = WCRed()->get_users_token_bulk( $user_id, 'R' );
				$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
				$txnid          = WCRed()->get_txnid( $customer_token_id );
			} else {
				$customer_token = WCRed()->get_users_token_bulk( $user_id );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'preauthorizationsredsys', ' ' );
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
				'DSMerchantTerminal'  => $ds_merchant_terminal,
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
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'preauthorizationsredsys', '$order: ' . $orderid2 );
				$this->log->add( 'preauthorizationsredsys', '$customer: ' . $customer );
				$this->log->add( 'preauthorizationsredsys', '$currency: ' . $currency );
				$this->log->add( 'preauthorizationsredsys', '$transaction_type: 0' );
				$this->log->add( 'preauthorizationsredsys', '$terminal: ' . $terminal );
				$this->log->add( 'preauthorizationsredsys', '$url_ok: ' . $url_ok );
				$this->log->add( 'preauthorizationsredsys', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'preauthorizationsredsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$mi_obj = new RedsysAPIWs();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}
			
			if ( 'yes' === $this->psd2 ) {
				$datos_usuario = array(
					'threeDSInfo'          => 'AuthenticationData',
					'protocolVersion'      => $protocolVersion,
					'browserAcceptHeader'  => $http_accept,
					'browserColorDepth'    => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'            => $browser_ip,
					'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserLanguage'      => WCPSD2()->get_idioma_navegador( $order_id ),
					'browserScreenHeight'  => WCPSD2()->get_altura_pantalla( $order_id ),
					'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla( $order_id ),
					'browserTZ'            => WCPSD2()->get_diferencia_horaria( $order_id ),
					'browserUserAgent'     => WCPSD2()->get_agente_navegador( $order_id ),
					'notificationURL'      => $final_notify_url,
				);
				//$acctinfo       = WCPSD2()->get_acctinfo( $order, false , $user_id );
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
				$DATOS_ENTRADA .= "</DATOSENTRADA>";
			} else {
				$DATOS_ENTRADA = "<DATOSENTRADA>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTCODE>" . $customer . "</DS_MERCHANT_MERCHANTCODE>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_TERMINAL>" . $terminal . "</DS_MERCHANT_TERMINAL>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_CURRENCY>" . $currency . "</DS_MERCHANT_CURRENCY>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_TRANSACTIONTYPE>" . $transaction_type . "</DS_MERCHANT_TRANSACTIONTYPE>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_AMOUNT>" . $order_total_sign . "</DS_MERCHANT_AMOUNT>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_ORDER>" . $orderid2 . "</DS_MERCHANT_ORDER>";
				$DATOS_ENTRADA .= $ds_merchant_group;
				$DATOS_ENTRADA .= "<DS_MERCHANT_IDENTIFIER>" . $customer_token . "</DS_MERCHANT_IDENTIFIER>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_DIRECTPAYMENT>true</DS_MERCHANT_DIRECTPAYMENT>";
				$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
				//$DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
				$DATOS_ENTRADA .= "</DATOSENTRADA>";
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '          The call            ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', $DATOS_ENTRADA );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			$XML = "<REQUEST>";
			$XML .= $DATOS_ENTRADA;
			$XML .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
			$XML .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
			$XML .= "</REQUEST>";

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', $XML );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			
			if ( 'yes' === $this->psd2 ) {
				
				$CLIENTE  = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->iniciaPeticion( array( "datoEntrada" => $XML ) );
				
				if ( isset( $responsews->iniciaPeticionReturn ) ) {
					$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn );
					$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					//$this->log->add( 'preauthorizationsredsys', '$acctinfo: ' . $acctinfo );
					$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
				}
				
				$ds_emv3ds_json       = $xml_retorno->INFOTARJETA->Ds_EMV3DS;
				$ds_emv3ds            = json_decode( $ds_emv3ds_json );
				$protocolVersion      = $ds_emv3ds->protocolVersion;
				$threeDSServerTransID = $ds_emv3ds->threeDSServerTransID;
				$three_ds_info          = $ds_emv3ds->threeDSInfo;
				
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'preauthorizationsredsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) );
					$this->log->add( 'preauthorizationsredsys', '$threeDSServerTransID: ' . $threeDSServerTransID );
					$this->log->add( 'preauthorizationsredsys', '$three_ds_info: ' . $three_ds_info );
				}
				
				if ( '2.1.0' === $protocolVersion || '2.2.0' === $protocolVersion ) {

					$datos_usuario = array(
						'threeDSInfo'              => 'AuthenticationData',
						'protocolVersion'          => $protocolVersion,
						'browserAcceptHeader'      => WCPSD2()->get_accept_headers_user( $user_id ),
						'browserColorDepth'        => WCPSD2()->get_profundidad_color_user( $user_id ),
						'browserIP'                => '86.0.4240.111',
						'browserJavaEnabled'       => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserJavascriptEnabled' => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserLanguage'          => WCPSD2()->get_idioma_navegador_user( $user_id ),
						'browserScreenHeight'      => WCPSD2()->get_altura_pantalla_user( $user_id ),
						'browserScreenWidth'       => WCPSD2()->get_anchura_pantalla_user( $user_id ),
						'browserTZ'                => WCPSD2()->get_diferencia_horaria_user( $user_id ),
						'browserUserAgent'         => WCPSD2()->get_agente_navegador_user( $user_id ),
						'threeDSServerTransID'     => $threeDSServerTransID,
						'notificationURL'          => $final_notify_url,
						'threeDSCompInd'           => 'N',
					);
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: AuthenticationData' );
						$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
					}
					$acctinfo       = WCPSD2()->get_acctinfo( $order, $datos_usuario );
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
					//$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
					$DATOS_ENTRADA .= '</DATOSENTRADA>';
					$XML            = "<REQUEST>";
					$XML           .= $DATOS_ENTRADA;
					$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
					$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
					$XML           .= "</REQUEST>";
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$CLIENTE  = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
					
					if ( isset( $responsews->trataPeticionReturn ) ) {
						$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
						$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
						$codigo            = json_decode( $xml_retorno->CODIGO );
						$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
						$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
						$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );
					}
	
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
						$this->log->add( 'preauthorizationsredsys', 'Ds_AuthorisationCode: ' .$authorisationcode );
					}
					if ( $authorisationcode ) {
						update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						/**
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						 */
					}
				} else {
					$protocolVersion = '1.0.2';
					$datos_usuario   = array(
						'threeDSInfo'          => 'AuthenticationData',
						'protocolVersion'      => $protocolVersion,
						'browserAcceptHeader'  => WCPSD2()->get_accept_headers_user( $user_id ),
						'browserColorDepth'    => WCPSD2()->get_profundidad_color_user( $user_id ),
						'browserIP'            => '86.0.4240.111',
						'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled_user( $user_id ),
						'browserLanguage'      => WCPSD2()->get_idioma_navegador_user( $user_id ),
						'browserScreenHeight'  => WCPSD2()->get_altura_pantalla_user( $user_id ),
						'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla_user( $user_id ),
						'browserTZ'            => WCPSD2()->get_diferencia_horaria_user( $user_id ),
						'browserUserAgent'     => WCPSD2()->get_agente_navegador_user( $user_id ),
						'notificationURL'      => $final_notify_url,
						'threeDSCompInd'       => 'N',
					);
					$data   = array(
						'threeDSInfo'          => 'AuthenticationData',
						'protocolVersion'      => '1.0.2',
					);
					$need = wp_json_encode( $data );
					$acctinfo       = WCPSD2()->get_acctinfo( $order, $datos_usuario );
					$DATOS_ENTRADA = "<DATOSENTRADA>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_AMOUNT>" . $order_total_sign . "</DS_MERCHANT_AMOUNT>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_ORDER>" . $orderid2 . "</DS_MERCHANT_ORDER>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTCODE>" . $customer . "</DS_MERCHANT_MERCHANTCODE>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_TERMINAL>" . $terminal . "</DS_MERCHANT_TERMINAL>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_CURRENCY>" . $currency . "</DS_MERCHANT_CURRENCY>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>";
					$DATOS_ENTRADA .= "<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>";
					$DATOS_ENTRADA .= $ds_merchant_group;
					$DATOS_ENTRADA .= "<DS_MERCHANT_IDENTIFIER>" . $customer_token . "</DS_MERCHANT_IDENTIFIER>";
					$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$DATOS_ENTRADA .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					//$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
					//$DATOS_ENTRADA .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
					//$DATOS_ENTRADA .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
					$DATOS_ENTRADA .= "</DATOSENTRADA>";
					$XML            = "<REQUEST>";
					$XML           .= $DATOS_ENTRADA;
					$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
					$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
					$XML           .= "</REQUEST>";
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '          The XML             ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', $XML );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$CLIENTE  = new SoapClient( $redsys_adr );
					$responsews = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
					
					if ( isset( $responsews->trataPeticionReturn ) ) {
						$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
						//$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS );
					}
					
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '$responsews: ' . print_r( $responsews, true ) );
						$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
					}
					$authorisationcode = json_decode( $xml_retorno->OPERACION->Ds_AuthorisationCode );
					$codigo            = json_decode( $xml_retorno->CODIGO );
					$redsys_order      = json_decode( $xml_retorno->OPERACION->Ds_Order );
					$terminal          = json_decode( $xml_retorno->OPERACION->Ds_Terminal );
					$currency_code     = json_decode( $xml_retorno->OPERACION->Ds_Currency );
					
					if ( $authorisationcode ) {
						update_post_meta( $order_id, '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
							$this->log->add( 'preauthorizationsredsys', '/****************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}

						if ( ! empty( $redsys_order ) ) {
							update_post_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							update_post_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						}
						return true;
					} else {
						// TO-DO: Enviar un correo con el problema al administrador
						/**
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$renewal_order->update_status( 'failed' );
						 */
					}
					
				}
			} else {
				$CLIENTE  = new SoapClient( $redsys_adr );
				$responsews = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
	
				if ( isset( $responsews->trataPeticionReturn ) ) {
					$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
					if ( isset( $xml_retorno->CODIGO ) ) {
						if ( '0' === (string)$xml_retorno->CODIGO ) {
							if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) {
								$RESPUESTA = (int) $xml_retorno->OPERACION->Ds_Response;
								if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'preauthorizationsredsys', ' ' );
										$this->log->add( 'preauthorizationsredsys', 'Response: Ok > ' . $RESPUESTA );
										$this->log->add( 'preauthorizationsredsys', ' ' );
									}
									update_post_meta( $order_id, '_redsys_done', 'yes' );
								} else {
									// Ha habido un problema en el cobro
									if ( 'yes' === $this->debug ) {
										$this->log->add( 'preauthorizationsredsys', ' ' );
										$this->log->add( 'preauthorizationsredsys', 'Response: Error > ' . $RESPUESTA );
										$this->log->add( 'preauthorizationsredsys', ' ' );
									}
									$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . $RESPUESTA );
									$renewal_order->update_status( 'failed' );
								}
							} else {
								// No hay $xml_retorno->OPERACION->Ds_Response
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', ' ' );
									$this->log->add( 'preauthorizationsredsys', 'Error > No hay $xml_retorno->OPERACION->Ds_Response' );
									$this->log->add( 'preauthorizationsredsys', ' ' );
								}
								$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
								$renewal_order->update_status( 'failed' );
							}
						} else {
							// $xml_retorno->CODIGO es diferente a 0
							$error_code = WCRed()->get_error_by_code( (string)$xml_retorno->CODIGO );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', 'Error > $xml_retorno->CODIGO es diferente a 0. Error: ' .  (string)$xml_retorno->CODIGO . '->' . $error_code);
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
							if ( $error_code && $this->notiemail ) {
								// Enviamos email al adminsitrador avisando de este problema
								$order->add_order_note( __( 'There was a Problem. The problem was: ', 'woocommerce-redsys' ) . (string)$xml_retorno->CODIGO . ': ' . $error_code  );
								$to      = $this->notiemail;
								$subject = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
								$body    = __( 'There was a problem with a scheduled subscription.', 'woocommerce-redsys' );
								$body    = __( 'The error was: ', 'woocommerce-redsys' );
								$body   .= $error_code;
								$headers = array( 'Content-Type: text/html; charset=UTF-8' );
								wp_mail( $to, $subject, $body, $headers );
	
							}
							$renewal_order->update_status( 'failed' );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', ' ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
								$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
								$this->log->add( 'preauthorizationsredsys', ' ' );
							}
						}
					} else {
						// No hay $xml_retorno->CODIGO
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', 'Error > No hay $xml_retorno->CODIGO' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
						$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
						$renewal_order->update_status( 'failed' );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', ' ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
							$this->log->add( 'preauthorizationsredsys', ' ' );
						}
					}
				} else {
					// No hay $responsews->trataPeticionReturn
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Error > No hay $responsews->trataPeticionReturn' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$order->add_order_note( __( 'Redsys connection failed', 'woocommerce-redsys' ) );
					$renewal_order->update_status( 'failed' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
				}
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	protected function order_contains_subscription( $order_id ) {
		if ( WCRed()->check_order_has_yith_subscriptions( $order_id ) ) {
			return true;
		} elseif ( WCRed()->get_redsys_token_r( $order_id ) ) {
			return true;
		} elseif ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return false;
		} elseif ( wcs_order_contains_subscription( $order_id ) ) {
			return true;
		} elseif ( wcs_order_contains_resubscribe( $order_id ) ) {
			return true;
		} elseif ( wcs_order_contains_renewal( $order_id ) ) {
			return true;
		} else {
			return false;
		}
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function receipt_page( $order ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '       Once upon a time       ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Generating receipt_page     ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		$customer_token = WCRed()->get_redsys_users_token();
		if ( ( ( 'yes' === $this->usetokensdirect ) && ( ( ! class_exists( 'WC_Subscriptions_Order' ) ) || ( ! wcs_order_contains_subscription( $order ) ) ) && ( 'yes' === $this->usetokens ) && ( ! empty( $customer_token ) ) ) && ( 'T' === $this->redsysdirectdeb || empty( $this->redsysdirectdeb ) ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Doing payment token from page ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			$order_p          = WCRed()->get_order( $order );
			$url_ok           = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order_p ) );
			$order_total_sign = number_format( $order_p->get_total(), 2, '', '' );

			if ( $order_total_sign === 0 || $order_total_sign === 000 || $order_total_sign === '0' || $order_total_sign === '000' ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/*************************************/' );
					$this->log->add( 'preauthorizationsredsys', '  Amount 0, dont redireting to Redsys  ' );
					$this->log->add( 'preauthorizationsredsys', '/*************************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				}
				echo '<p>' . esc_html__( 'Thank you for your order', 'woocommerce-redsys' ) . '</p>';
				wp_safe_redirect( $url_ok );
				exit();
			} else {
				echo '<p>' . esc_html__( 'Thank you for your order, Please wait for a moment while we charge your credit card via Servired/RedSys.', 'woocommerce-redsys' ) . '</p>';
				$result = $this->redsys_process_payment_token( $order );
				if ( $result ) {
					wp_safe_redirect( $result );
					exit();
				} else {
					echo '<p>' . esc_html__( 'There is a problem with your payment, please try again.', 'woocommerce-redsys' ) . '</p>';
				}
			}
		} elseif ( class_exists( 'WC_Subscriptions_Order' ) && wcs_order_contains_subscription( $order ) ) {
			// Is a Subscription
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Doing payment Subscription    ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			$customer_token   = WCRed()->get_redsys_users_token();
			$order_p          = WCRed()->get_order( $order );
			$url_ok           = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order_p ) );
			$order_total_sign = number_format( $order_p->get_total(), 2, '', '' );
			if ( ! empty( $customer_token ) && ( $order_total_sign === 0 || $order_total_sign === 000 || $order_total_sign === '0' || $order_total_sign === '000' ) ) {
				$order_p->payment_complete();
				echo '<p>' . esc_html__( 'Thank you for your order', 'woocommerce-redsys' ) . '</p>';
				wp_safe_redirect( $url_ok );
				exit();
			} else {
				echo '<p>' . esc_html__( 'Thank you for your Subscription, please click the button below to pay with Credit Card via Servired/RedSys.', 'woocommerce-redsys' ) . '</p>';
				echo $this->generate_redsys_subscription_form( $order );
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Doing payment by redirection  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Credit Card via Servired/RedSys.', 'woocommerce-redsys' ) . '</p>';
			echo $this->generate_redsys_form( $order );
		}
	}
	/**
	 * Check redsys IPN validity
	 **/
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_ipn_request_is_valid() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '  Starting check IPN Request  ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		if ( isset( $_POST['threeDSMethodData'] ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '   Es IPN threeDSMethodData   ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			return true;
		}

		if ( isset( $_POST['PaRes'] ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '         Es IPN PaRes         ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			return true;
		}

		$usesecretsha256  = $this->secretsha256;
		$customtestsha256 = $this->customtestsha256;
		$testsha256       = $this->testsha256;

		if ( $usesecretsha256 || $customtestsha256 || $testsha256 ) {
			$version           = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
			$data              = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
			$remote_sign       = sanitize_text_field( $_POST['Ds_Signature'] );
			$mi_obj             = new RedsysAPI();
			$decodec           = $mi_obj->decodeMerchantParameters( $data );
			$order_id          = $mi_obj->getParameter( 'Ds_Order' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = get_post_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'preauthorizationsredsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'preauthorizationsredsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->getParameter( 'Ds_Order' );
				$this->log->add( 'preauthorizationsredsys', 'Order ID: ' . $order_id );
			}
			$order           = WCRed()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'preauthorizationsredsys', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'preauthorizationsredsys', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', 'Using $secretsha256 Transcient' );
					$this->log->add( 'preauthorizationsredsys', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'preauthorizationsredsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) );
			}
			if ( isset( $_POST['Ds_MerchantCode'] ) && $_POST['Ds_MerchantCode'] === $this->customer ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Received INVALID notification from Servired/RedSys' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', '== End check IPN Request ==' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
			$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
			$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
	}
	function check_confirm_pares( $post ) {
		/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '           Is PaRes           ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		$pares              = sanitize_text_field( $_POST['PaRes'] );
		$md                 = sanitize_text_field( $_POST['MD'] );
		$order_id           = get_transient( $md );
		$order              = WCRed()->get_order( $order_id );
		$user_id            = $order->get_user_id();
		$type               = 'ws';
		$redsys_adr         = $this->get_redsys_url_gateway( $user_id, $type );
		$order_total_sign   = get_transient( 'amount_' . $md );
		$orderid2           = get_transient( 'order_' . $md );
		$customer           = get_transient( 'merchantcode_' . $md );
		$ds_merchant_terminal = get_transient( 'terminal_' . $md );
		$currency           = get_transient( 'currency_' . $md );
		$customer_token_c   = get_transient( 'identifier_' . $md );
		$cof_ini            = get_transient( 'cof_ini_' . $md );
		$cof_type           = get_transient( 'cof_type_' . $md );
		$cof_txnid          = get_transient( 'cof_txnid_' . $md );
		$mi_obj              = new RedsysAPIWs();
		$secretsha256       = $this->get_redsys_sha256( $user_id );
		$url_ok             = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$needed             = wp_json_encode( array(
			'threeDSInfo'     => 'ChallengeResponse',
			'MD'              => $md,
			'protocolVersion' => '1.0.2',
			'PARes'           => $pares,
		) );
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '$pares: ' . $pares );
			$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order_id );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		
		$DATOS_ENTRADA = '<DATOSENTRADA>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>1</DS_MERCHANT_TRANSACTIONTYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
		$DATOS_ENTRADA .= '</DATOSENTRADA>';
		$XML            = "<REQUEST>";
		$XML           .= $DATOS_ENTRADA;
		$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
		$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
		$XML           .= "</REQUEST>";

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '$DATOS_ENTRADA: ' . $DATOS_ENTRADA );
			$this->log->add( 'preauthorizationsredsys', '$XML: ' . $XML );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$CLIENTE         = new SoapClient( $redsys_adr );
		$responsews      = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
		
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' $responsews: ' . print_r( $responsews, true ) );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		
		if ( isset( $responsews->trataPeticionReturn ) ) {
			$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
			if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) {
				$RESPUESTA = (int) $xml_retorno->OPERACION->Ds_Response;
				if ( ( $RESPUESTA >= 0 ) && ( $RESPUESTA <= 99 ) ) {
					$auth_code = $xml_retorno->OPERACION->Ds_AuthorisationCode;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', 'Response: Ok > ' . $RESPUESTA );
						$this->log->add( 'preauthorizationsredsys', 'Authorization code: ' . $auth_code );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$auth_code = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode;
					$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
					$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $auth_code );
					update_post_meta( $order_id, '_authorisation_code_redsys', $auth_code );
					update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
					$order->payment_complete();
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_ipn_response() {
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '      check_ipn_response      ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}
		@ob_clean();
		$_POST = stripslashes_deep( $_POST );
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid-preauthorizationsredsys-standard-ipn-request', $_POST );
		} else {
			wp_die( 'Servired/RedSys Preauthorizations Notification Request Failure' );
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', '      successful_request      ' );
			$this->log->add( 'preauthorizationsredsys', '/****************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$version              = sanitize_text_field( $_POST['Ds_SignatureVersion'] );
		$data                 = sanitize_text_field( $_POST['Ds_MerchantParameters'] );
		$remote_sign          = sanitize_text_field( $_POST['Ds_Signature'] );
		$threeDSMethodData    = sanitize_text_field( $_POST['threeDSMethodData'] );
		$mi_obj                = new RedsysAPI();
		$usesecretsha256      = $this->secretsha256;
		$dscardnumbercompl    = '';
		$dsexpiration         = '';
		$dsmerchantidenti     = '';
		$dscardnumber4        = '';
		$dsexpiryyear         = '';
		$dsexpirymonth        = '';
		$decodedata           = $mi_obj->decodeMerchantParameters( $data );
		$localsecret          = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
		$total                = $mi_obj->getParameter( 'Ds_Amount' );
		$ordermi              = $mi_obj->getParameter( 'Ds_Order' );
		$dscode               = $mi_obj->getParameter( 'Ds_MerchantCode' );
		$currency_code        = $mi_obj->getParameter( 'Ds_Currency' );
		$response             = $mi_obj->getParameter( 'Ds_Response' );
		$id_trans             = $mi_obj->getParameter( 'Ds_AuthorisationCode' );
		$dsdate               = htmlspecialchars_decode( $mi_obj->getParameter( 'Ds_Date' ) );
		$dshour               = htmlspecialchars_decode( $mi_obj->getParameter( 'Ds_Hour' ) );
		$dstermnal            = $mi_obj->getParameter( 'Ds_Terminal' );
		$dsmerchandata        = $mi_obj->getParameter( 'Ds_MerchantData' );
		$dssucurepayment      = $mi_obj->getParameter( 'Ds_SecurePayment' );
		$dscardcountry        = $mi_obj->getParameter( 'Ds_Card_Country' );
		$dsconsumercountry    = $mi_obj->getParameter( 'Ds_ConsumerLanguage' );
		$dstransactiontype    = $mi_obj->getParameter( 'Ds_TransactionType' );
		$dsmerchantidenti     = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );
		$dscardbrand          = $mi_obj->getParameter( 'Ds_Card_Brand' );
		$dsmechandata         = $mi_obj->getParameter( 'Ds_MerchantData' );
		$dscargtype           = $mi_obj->getParameter( 'Ds_Card_Type' );
		$dserrorcode          = $mi_obj->getParameter( 'Ds_ErrorCode' );
		$dpaymethod           = $mi_obj->getParameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia. Si se paga por Iupay o TC, no se utiliza.
		$response             = intval( $response );
		$secretsha256         = get_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		$order1               = $ordermi;
		$order2               = WCRed()->clean_order_number( $order1 );
		$order                = WCRed()->get_order( (int) $order2 );
		$user_id              = $order->get_user_id();
		$usesecretsha256      = $this->get_redsys_sha256( $user_id );
		
		delete_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		
		if ( ! empty( $threeDSMethodData ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' Is successful_request IF $threeDSMethodData' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
			}
			$decoded_post_json    = base64_decode( $threeDSMethodData );
			$decoded_post         = json_decode( $decoded_post_json );
			$threeDSServerTransID = $decoded_post->threeDSServerTransID;
			$order2               = get_transient( $threeDSServerTransID );
			$order                = WCRed()->get_order( (int) $order2 );
			$user_id              = $order->get_user_id();
			$protocolVersion      = get_transient( 'protocolVersion_' . $order2 );
			$agente_navegador     = WCPSD2()->get_agente_navegador( $order2 );
			$idioma_navegador     = WCPSD2()->get_idioma_navegador( $order2 );
			$altura_pantalla      = WCPSD2()->get_altura_pantalla( $order2 );
			$anchura_pantalla     = WCPSD2()->get_anchura_pantalla( $order2 );
			$profundidad_color    = WCPSD2()->get_profundidad_color( $order2 );
			$diferencia_horaria   = WCPSD2()->get_diferencia_horaria( $order2 );
			$accept_headers       = WCPSD2()->get_accept_headers( $order2 );
			$javaenabled          = WCPSD2()->get_browserjavaenabled( $order2 );
			$type                 = 'ws';
			$redsys_adr           = $this->get_redsys_url_gateway( $user_id, $type );
			if ( 'yes' === $this->not_use_https ){
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}

			$user_ip       = WCRed()->get_the_ip();
			$datos_usuario = array(
				'threeDSInfo'          => 'AuthenticationData',
				'protocolVersion'      => $protocolVersion,
				'browserAcceptHeader'  => $accept_headers,
				'browserColorDepth'    => $profundidad_color,
				'browserIP'            => $user_ip,
				'browserJavaEnabled'   => $javaenabled,
				'browserLanguage'      => $idioma_navegador,
				'browserScreenHeight'  => $altura_pantalla,
				'browserScreenWidth'   => $anchura_pantalla,
				'browserTZ'            => $diferencia_horaria,
				'browserUserAgent'     => $agente_navegador,
				'threeDSServerTransID' => $threeDSServerTransID,
				'notificationURL'      => $final_notify_url,
				'threeDSCompInd'       => 'Y',
			);
			$acctinfo = WCPSD2()->get_acctinfo( $order, $datos_usuario, $user_id );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '$user_id: ' . $user_id );
				$this->log->add( 'preauthorizationsredsys', '$order_id: ' . $order2 );
				$this->log->add( 'preauthorizationsredsys', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'preauthorizationsredsys', 'protocolVersion: ' . $protocolVersion );
				$this->log->add( 'preauthorizationsredsys', 'browserIP: ' . $user_ip );
				$this->log->add( 'preauthorizationsredsys', 'browserJavaEnabled: ' . $javaenabled );
				$this->log->add( 'preauthorizationsredsys', 'browserLanguage: ' . $idioma_navegador );
				$this->log->add( 'preauthorizationsredsys', 'browserScreenHeight: ' . $altura_pantalla );
				$this->log->add( 'preauthorizationsredsys', 'browserScreenWidth: ' . $anchura_pantalla );
				$this->log->add( 'preauthorizationsredsys', 'browserTZ: ' . $agente_navegador );
				$this->log->add( 'preauthorizationsredsys', 'browserUserAgent: ' . $agente_navegador );
				$this->log->add( 'preauthorizationsredsys', 'threeDSServerTransID: ' . $threeDSServerTransID );
				$this->log->add( 'preauthorizationsredsys', 'notificationURL: ' . $final_notify_url );
				$this->log->add( 'preauthorizationsredsys', 'threeDSCompInd: : Y' );
				$this->log->add( 'preauthorizationsredsys', 'acctInfo: : ' . $acctinfo );
			}
			$order_total_sign   = get_transient( 'amount_' . $order2 );
			$orderid2           = get_transient( 'order_' . $order2 );
			$customer           = $this->customer;
			$ds_merchant_terminal = get_transient( 'terminal_' . $order2 );
			$currency           = get_transient( 'currency_' . $order2 );
			$customer_token_c   = get_transient( 'identifier_' . $order2  );
			$cof_ini            = get_transient( 'cof_ini_' . $order2 );
			$cof_type           = get_transient( 'cof_type_' . $order2 );
			$cof_txnid          = get_transient( 'cof_txnid_' . $order2 );
			
			$mi_obj = new RedsysAPIWs();
			
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
			$DATOS_ENTRADA .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$DATOS_ENTRADA .= $ds_merchant_group;
			$DATOS_ENTRADA .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$DATOS_ENTRADA .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
			$DATOS_ENTRADA .= '</DATOSENTRADA>';
			$XML            = "<REQUEST>";
			$XML           .= $DATOS_ENTRADA;
			$XML           .= "<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>";
			$XML           .= "<DS_SIGNATURE>" . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $DATOS_ENTRADA ) . "</DS_SIGNATURE>";
			$XML           .= "</REQUEST>";
			
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', 'The XML: ' . $XML );
			}
			
			$CLIENTE        = new SoapClient( $redsys_adr );
			$responsews     = $CLIENTE->trataPeticion( array( "datoEntrada" => $XML ) );
			
			if ( isset( $responsews->trataPeticionReturn ) ) {
				$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn );
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		}

		if ( $this->order_contains_subscription( $order->get_id() ) ) {
			if ( $this->order_contains_subscription( $order->get_id() ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					$this->log->add( 'preauthorizationsredsys', ' This is a subscription order ' );
					$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
						$this->log->add( 'preauthorizationsredsys', '         This is PSD2         ' );
						$this->log->add( 'preauthorizationsredsys', '/****************************/' );
					}
				}
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', ' This is NOT a subscription order ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
			}
		}

		if ( $this->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable && (int)$response <= 99 || ( ( 'yes' === $this->usetokens ) && ( ! empty( $dsmerchantidenti ) ) && ( '3' !== $dstransactiontype ) && ( '2' !== $dstransactiontype ) && ( 'yes' !== $this->redsysdirectdeb ) && $response  <= 99 ) ) {
			$number           = $mi_obj->getParameter( 'Ds_Card_Number' );
			$number2          = $mi_obj->getParameter( 'Ds_CardNumber' );
			$dsexpiration     = $mi_obj->getParameter( 'Ds_ExpiryDate' );
			$dsmerchantidenti = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );
			$dscardbrand2     = $mi_obj->getParameter( 'Ds_Card_Brand' );
			$dscardbrand      = WCRed()->get_card_brand( $dscardbrand2 );

			if ( empty( $dsexpiration ) || empty( $dscardbrand2 ) || ( empty( $number ) && empty( $number2 ) && $this->notiemail ) ) {
				$to      = $this->notiemail;
				$subject = __( 'Your order will not be marked as paid, missing Redsys fields.', 'woocommerce-redsys' );
				$body    = __( 'You need to ask to Redsys to sent some fields for tokenization (Pay with one Click). Please ask to Redsys to sent with the callback the following fields. WooCommerce cannot work without these fields', 'woocommerce-redsys' );
				$body   .= '<p>Ds_Card_Brand</p>';
				$body   .= '<p>Ds_ExpiryDate</p>';
				$body   .= '<p>Ds_Card_Number</p>';
				$body   .= '<p>Once Redsys start to sent this fields, Pay with one clic will start to work</p>';
				$body   .= '<p>Some times you need to ask to your Bank and not to Redsys</p>';
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				if ( 'yes' === $this->sendemailsdscard ) {
					wp_mail( $to, $subject, $body, $headers );
				}
			}
			$dscardnumber4 = WCRed()->get_last_four( $number, $number2 );
			$dsexpiryyear  = '20' . substr( $dsexpiration, 0, 2 );
			$dsexpirymonth = substr( $dsexpiration, -2 );
			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$redsys_txnid = $mi_obj->getParameter( 'Ds_Merchant_Cof_Txnid' );
				$token_type   = 'R';
			}
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'preauthorizationsredsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'preauthorizationsredsys', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'preauthorizationsredsys', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'preauthorizationsredsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Response: ' . $response );
			$this->log->add( 'preauthorizationsredsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'preauthorizationsredsys', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'preauthorizationsredsys', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'preauthorizationsredsys', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'preauthorizationsredsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'preauthorizationsredsys', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'preauthorizationsredsys', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'preauthorizationsredsys', 'Ds_PayMethod: ' . $dpaymethod );
			if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
				$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_Cof_Txnid: ' . $redsys_txnid );
				$this->log->add( 'preauthorizationsredsys', '$token_type: R' );
			}
		}

		// refund.

		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'update_post_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded', 'woocommerce-redsys' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woocommerce-redsys' ) );
			exit;
		}

		// Confirm Preauthorization.
		if ( '2' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Response 900 (confirmed preauthorization)' );
				}
				set_transient( $order->get_id() . '_redsys_preauth', 'yes' );
				$order->add_order_note( __( 'Confirmed Order Preauthorization', 'woocommerce-redsys' ) );
				$order->update_status( 'completed', __( 'Order Completed', 'woocommerce-redsys' ) );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'update_post_meta to "Complete"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'New Status in request: ' . $status );
				}
				exit;
			}
			$order->add_order_note( __( 'Redsys return an error confirming preauthorization', 'woocommerce-redsys' ) );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			exit;
		}

		if ( $dstransactiontype != '0' && $dstransactiontype != '1' ) {
			return;
		}

		if ( ! empty( $dscardnumbercomp ) ) {
			$dscardnumbercomp = $dscardnumbercomp;
		} else {
			$dscardnumbercomp = 'unknown';
		}

		if ( ! empty( $dsexpiryyear ) && '2020' !== $dsexpiryyear && '20' !==  $dsexpiryyear ) {
			 $dsexpiryyear = $dsexpiryyear;
		} else {
			 $dsexpiryyear = '2099';
		}

		if ( ! empty( $dsexpirymonth ) ) {
			$dsexpirymonth = $dsexpirymonth;
		} else {
			$dsexpirymonth = '12';
		}

		if ( ! empty( $dscardnumber4 ) ) {
			$dscardnumber4 = $dscardnumber4;
		} else {
			$dscardnumber4 = '0000';
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Order: ' . $order1 );
			$this->log->add( 'preauthorizationsredsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Response: ' . $response );
			$this->log->add( 'preauthorizationsredsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'preauthorizationsredsys', '$order2: ' . $order2 );
			$this->log->add( 'preauthorizationsredsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Card_Number: ' . $dscardnumbercomp );
			$this->log->add( 'preauthorizationsredsys', 'Ds_ExpiryDate: ' . $dsexpiration );
			$this->log->add( 'preauthorizationsredsys', 'Ds_Merchant_Identifier: ' . $dsmerchantidenti );
			$this->log->add( 'preauthorizationsredsys', '$dscardnumber4: ' . $dscardnumber4 );
			$this->log->add( 'preauthorizationsredsys', '$dsexpiryyear: ' . $dsexpiryyear );
			$this->log->add( 'preauthorizationsredsys', '$dsexpirymonth: ' . $dsexpirymonth );
			$this->log->add( 'preauthorizationsredsys', 'all data: ' . $decodedata );
			$this->log->add( 'preauthorizationsredsys', 'Response: ' . $response );
		}

		if ( (int)$response <= 99 ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', '$response: <= 99' );
			}
			//authorized
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			$order_total_compare = ltrim( $order_total_compare, '0' );
			$total               = ltrim( $total, '0' );
			if ( 'partial-payment' !== $order->get_status() ) {
				if ( $order_total_compare !== $total ) {
					//amount does not match
					if ( 'yes' === $this->debug )
						$this->log->add( 'preauthorizationsredsys', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
					// Put this order on-hold for manual checking
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %s - received: %s).', 'woocommerce-redsys' ), $order_total_compare , $total ) );
					exit;
				}
			} else {
				set_transient( $order->get_id() . '_redsys_collect', 'yes' );
			}
			$contais_subscription = $this->order_contains_subscription( $order->get_id() );
			
			if ( $contais_subscription ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Order has subscription' );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', 'Order has not subscription' );
				}
			}
			if ( ( ( $this->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable ) ) || ( ( 'yes' === $this->usetokens ) && ( ! empty( $dsmerchantidenti ) ) && ( '0' === $dsmechandata || '1' === $dsmechandata ) ) ) {
				if ( $this->order_contains_subscription( $order->get_id() ) ) {
					if ( $this->order_contains_subscription( $order->get_id() ) ) {
						$user_id = $order->get_user_id();
						$tokens  = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Is a Subscription' );
						}
						
						if ( $this->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable && 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', 'Is a PSD2 Subscription' );
							}
							$exist_r =  WCRed()->check_type_exist_in_tokens( $tokens, 'R' );
							if ( ! $exist_r ) {
								$token = new WC_Payment_Token_CC();
								$token->set_token( $dsmerchantidenti );
								$token->set_gateway_id( 'redsys' );
								$token->set_user_id( $order->get_user_id() );
								$token->set_card_type( $dscardbrand );
								$token->set_last4( $dscardnumber4 );
								$token->set_expiry_month( $dsexpirymonth );
								$token->set_expiry_year( $dsexpiryyear );
								$token->set_default( true );
								$token->save();
								$token_id = $token->get_id();
								WCRed()->set_txnid( $token_id, $redsys_txnid );
								WCRed()->set_token_type( $token_id, 'R' );
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', '$redsys_txnid: ' . $redsys_txnid );
									$this->log->add( 'preauthorizationsredsys', '$token_type: R' );
								}
							} else {
								if ( 'yes' === $this->debug ) {
									$this->log->add( 'preauthorizationsredsys', 'Existe Token R' );
								}
							}
						} elseif ( $this->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable ) {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', 'Is NOT a PSD2 Subscription' );
							}
							$token = new WC_Payment_Token_CC();
							$token->set_token( $dsmerchantidenti );
							$token->set_gateway_id( 'redsys' );
							$token->set_user_id( $order->get_user_id() );
							$token->set_card_type( $dscardbrand );
							$token->set_last4( $dscardnumber4 );
							$token->set_expiry_month( $dsexpirymonth );
							$token->set_expiry_year( $dsexpiryyear );
							$token->set_default( true );
							$token->save();
						}
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', 'Token 1 clic' );
					}
					$user_id = $order->get_user_id();
					$tokens  = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
					if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'Es PSD2' );
						}
						$exist_c =  WCRed()->check_type_exist_in_tokens( $tokens, 'C' );
						if ( ! $exist_c ) {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', 'NO existe Token C' );
								$this->log->add( 'preauthorizationsredsys', 'Vamos a guardarlo' );
							}
							$token = new WC_Payment_Token_CC();
							$token->set_token( $dsmerchantidenti );
							$token->set_gateway_id( 'redsys' );
							$token->set_user_id( $order->get_user_id() );
							$token->set_card_type( $dscardbrand );
							$token->set_last4( $dscardnumber4 );
							$token->set_expiry_month( $dsexpirymonth );
							$token->set_expiry_year( $dsexpiryyear );
							$token->set_default( true );
							$token->save();
							$token_id = $token->get_id();
							WCRed()->set_txnid( $token_id, $redsys_txnid );
							WCRed()->set_token_type( $token_id, 'C' );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', '$redsys_txnid: ' . $redsys_txnid );
								$this->log->add( 'preauthorizationsredsys', '$token_type: C' );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'preauthorizationsredsys', 'Existe Token C' );
							}
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'preauthorizationsredsys', 'No es PSD2' );
						}
						if ( empty( $tokens ) ) {
							$token = new WC_Payment_Token_CC();
							$token->set_token( $dsmerchantidenti );
							$token->set_gateway_id( 'redsys' );
							$token->set_user_id( $order->get_user_id() );
							$token->set_card_type( $dscardbrand );
							$token->set_last4( $dscardnumber4 );
							$token->set_expiry_month( $dsexpirymonth );
							$token->set_expiry_year( $dsexpiryyear );
							$token->set_default( true );
							$token->save();
						}
					}
				}
			}

			$authorisation_code = $id_trans;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '      Saving Order Meta       ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			if ( ! empty( $order1 ) ) {
				update_post_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_payment_order_number_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys saved: ' . $dsdate );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_payment_date_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				update_post_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_payment_terminal_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $dshour ) ) {
				update_post_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys saved: ' . $dshour );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_payment_hour_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $id_trans ) ) {
				update_post_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_authorisation_code_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $currency_code ) ) {
				update_post_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_corruncy_code_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $dscardcountry ) ) {
				update_post_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_card_country_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			if ( ! empty( $dscargtype ) ) {
				update_post_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				if ( 'yes' === $this->debug ) {
				 $this->log->add( 'preauthorizationsredsys', '_card_type_redsys saved: ' . $dscargtype );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_card_type_redsys NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			// This meta is essential for later use:
			if ( ! empty( $secretsha256 ) ) {
				update_post_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '_redsys_secretsha256 NOT SAVED!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}
			// Payment completed
			if ( 'yes' === $this->preauthorization && 'D' !== $dpaymethod && 'R' !== $dpaymethod ) {
				$order->add_order_note( __( 'HTTP Notification received - Transaction Preauthorized', 'woocommerce-redsys' ) );
			} elseif ( 'D' === $dpaymethod ) {
				$order->add_order_note( __( 'HTTP Notification received - Resident payment', 'woocommerce-redsys' ) );
			} else {
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
			}
			$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisation_code );

			if ( 'yes' === $this->preauthorization && 'D' !== $dpaymethod && 'R' !== $dpaymethod ) {
				update_post_meta( $ordermi, '_redsys_done', 'yes' );
				$order->payment_complete( $ordermi );
				$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
			} elseif ( ! empty( $dpaymethod ) && 'D' === $dpaymethod ) {
				update_post_meta( $ordermi, '_redsys_done', 'yes' );
				$order->payment_complete( $ordermi );
				$order->update_status( 'redsys-residentp', __( 'Resident Payment', 'woocommerce-redsys' ) );
			} elseif ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Redsys', 'woocommerce-redsys' ) );
			} else {
				update_post_meta( $order->get_id(), '_redsys_done', 'yes' );
				$order->payment_complete();
			}

			if ( 'yes' === $this->debug && 'yes' === $this->preauthorization ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Order Preauthorized by Redsys   ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			} elseif ( 'yes' === $this->debug && 'D' === $dpaymethod ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '      Resident Payment        ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '     Payment Complete         ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		} else {

			$order_id          = $order->get_id();
			$admin_url         = admin_url();
			$url_to_order      = $admin_url . 'post.php?post=' . $order_id . '&action=edit';
			$ds_response_value = WCRed()->get_error( $response );
			$ds_error_value    = WCRed()->get_error( $dserrorcode );

			
			if ( $ds_response_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_response_value );
				update_post_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_response_value );
			}
			
			if ( $ds_error_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_error_value );
				update_post_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_error_value );
			}

			if ( 'yes' === $this->debug ) {
				if ( $ds_response_value ) {
					$this->log->add( 'preauthorizationsredsys', $ds_response_value );
				}
				if ( $ds_error_value ) {
					$this->log->add( 'preauthorizationsredsys', $ds_error_value );
				}
			}

			if ( 'yes' === $this->sendemails && $this->notiemail ) {
				$to      = $this->notiemail;
				$subject = __( 'A customer has had payment errors', 'woocommerce-redsys' );
				$body    = __( 'A customer has had payment errors:', 'woocommerce-redsys' );
				$body   .= '<p>' . $ds_response_value . '</p>';
				$body   .= '<p>' . $ds_error_value . '</p>';
				$body   .= '<p>' . __( 'Total Order: ', 'woocommerce-redsys' ) . $order->get_total() . '</p>';
				$body   .= '<p>' . __( 'Customer Name: ', 'woocommerce-redsys' ) . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</p>';
				$body   .= '<p>' . __( 'Customer Country: ', 'woocommerce-redsys' ) . $order->get_billing_country() . '</p>';
				$body   .= '<p>' . __( 'Customer Telephone: ', 'woocommerce-redsys' ) . $order->get_billing_phone() . '</p>';
				$body   .= '<p>' . __( 'Customer Email: ', 'woocommerce-redsys' ) . $order->get_billing_email() . '</p>';
				$body   .= '<p><a href="' . $url_to_order . '">' . __( 'Check order for details', 'woocommerce-redsys' ) . '</a></p>';
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				wp_mail( $to, $subject, $body, $headers );
			}

			if ( 'yes' === $this->sentemailscustomers ) {

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', ' Sending email Error  to customer ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				$redsys_email_customer_options = get_option( 'woocommerce_redsys_customer_email_order_settings' );

				if ( $redsys_email_customer_options ) {
					if ( array_key_exists( 'enabled', $redsys_email_customer_options ) ) {
						$enabled = $redsys_email_customer_options['enabled'];
					} else {
						$enabled = 'no';
					}
					if ( array_key_exists( 'subject', $redsys_email_customer_options ) && ! empty( $redsys_email_customer_options['subject'] ) ) {
						$subject = $redsys_email_customer_options['subject'];
					} else {
						$subject = esc_html__( 'Credit Cart Payment problem at ', 'woocommerce-redsys' ) . get_bloginfo( 'name' );
					}
					if ( array_key_exists( 'heading', $redsys_email_customer_options ) && ! empty( $redsys_email_customer_options['heading'] ) ) {
						$heading = $redsys_email_customer_options['heading'];
					} else {
						$heading = esc_html__( 'Credit Cart Payment problem', 'woocommerce-redsys' );
					}
				}
				$email_name = get_option( 'woocommerce_email_from_name' );
				$email_from = get_option( 'woocommerce_email_from_address' );
				$headers[]  = 'Content-Type: text/html; charset=UTF-8';
				$headers[]  = 'From: ' . $email_name . ' <' . $email_from . '>';

				$mailer     = WC()->mailer();
				$order      = new wc_order( $order_id );

				$message    =	'<p>' . esc_html__( 'Thank you very much for shopping in our store.', 'woocommerce-redsys' ) . '</p>';
				$message   .=	'<p>' . esc_html__( 'There was a problem with the credit card payment.', 'woocommerce-redsys' ) . '</p>';
				$message   .=	'<p>' . esc_html__( 'If you don\'t know what the error was.', 'woocommerce-redsys' ) . '<br />';

				if ( ! empty( $ds_error_value ) ) {
					$message .=	 __( 'The error was: ', 'woocommerce-redsys' ) . $ds_error_value . '</p>';
				}
				if ( ! empty( $ds_response_value ) ) {
					$message .=	 __( 'The error was: ', 'woocommerce-redsys' ) . $ds_response_value . '</p>';
				}
				$message .=	'<p>' . esc_html__( 'If you wish, you can try again at this link: ', 'woocommerce-redsys' ) . wc_get_checkout_url() . '</p>';
				$message .=	'<p>' . esc_html__( 'Thank you very much for choosing us.', 'woocommerce-redsys' ) . '</p>';

				$message = apply_filters( 'redsys_sent_email_customer_pay_error', $message, $ds_error_value, $ds_response_value );

				$email           = $order->get_billing_email();
				$wrapped_message = $mailer->wrap_message( $heading, $message );
				$wc_email        = new WC_Email;
				$html_message    = $wc_email->style_inline( $wrapped_message );

				wp_mail( $email, $subject, $html_message, $headers );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', '   Email Error to customer sent   ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
			}

			if ( $this->wooredsysurlko ) {
				if ( 'returncancel' === $this->wooredsysurlko ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/********************************/' );
						$this->log->add( 'preauthorizationsredsys', '          Order Cancelled         ' );
						$this->log->add( 'preauthorizationsredsys', '/********************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$this->log->add( 'preauthorizationsredsys', ' ' );
					}
					$order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woocommerce-redsys' ) );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', '          Order Cancelled         ' );
					$this->log->add( 'preauthorizationsredsys', '/********************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				// $order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woocommerce-redsys' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', '          Order Cancelled         ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS
		$order          = WCRed()->get_order( $order_id );
		$terminal       = get_post_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', '/**************************/' );
			$this->log->add( 'preauthorizationsredsys', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', '/**************************/' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'preauthorizationsredsys', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'preauthorizationsredsys', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
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
		$merchan_name      = get_post_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = get_post_meta( $order_id, '_billing_last_name', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', '**********************' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'preauthorizationsredsys', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'preauthorizationsredsys', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'preauthorizationsredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'preauthorizationsredsys', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );
		}

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new RedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', $merchan_name . ' ' . $merchant_lastnme );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', '*********************************' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $this->customer );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'preauthorizationsredsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'preauthorizationsredsys', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'preauthorizationsredsys', ' ' );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

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
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'preauthorizationsredsys', '*********************************' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
			}
			return $post_arg;
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', 'Refund Ok, returning TRUE' );
		}
		return true;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_redsys_refund( $order_id ) {
		// check postmeta
		$order        = WCRed()->get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', '*****************************************' );
			$this->log->add( 'preauthorizationsredsys', ' ' );
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
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
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function ask_for_confirm_preauthorization( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS
		$order          = WCRed()->get_order( $order_id );
		$terminal       = get_post_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}

		$transaction_type  = '2';
		$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
		} else {
			$secretsha256 = $secretsha256;
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
		$merchan_name      = get_post_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = get_post_meta( $order_id, '_billing_last_name', true );

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new RedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', $merchan_name . ' ' . $merchant_lastnme );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $this->customer );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'preauthorizationsredsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'preauthorizationsredsys', __( 'ask_for_confirm_preauthorization Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Next Step, Call', 'woocommerce-redsys' ) );
		}
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
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'The call is already made and this is the response: ', 'woocommerce-redsys' ) . print_r( $post_arg ) );
		}
		if ( is_wp_error( $post_arg ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function ask_for_collect_remainder( $order_id, $amount ) {

		// post code to REDSYS
		$order            = WCRed()->get_order( $order_id );
		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$terminal         = get_post_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes   = WCRed()->get_currencies();
		$user_id          = $order->get_user_id();
		$secretsha256     = $this->get_redsys_sha256( $user_id );
		$customer_token   = WCRed()->get_users_token_bulk( $user_id );
		$order_total_sign = $amount;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}

		$transaction_type  = '0';
		$secretsha256_meta = get_post_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
		} else {
			$secretsha256 = $secretsha256;
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
		$merchan_name      = get_post_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = get_post_meta( $order_id, '_billing_last_name', true );

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new RedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', $merchan_name . ' ' . $merchant_lastnme );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		if ( ! empty( $this->merchantgroup ) ) {
			$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
		}
		$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token );
		$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'true' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $order_total_sign );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $this->customer );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'preauthorizationsredsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'preauthorizationsredsys', __( 'DS_MERCHANT_IDENTIFIER: ', 'woocommerce-redsys' ) . $customer_token );
			$this->log->add( 'preauthorizationsredsys', __( 'ask_for_collect_remainder Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Next Step, Call', 'woocommerce-redsys' ) );
		}
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
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'The call is already made and this is the response: ', 'woocommerce-redsys' ) . print_r( $post_arg ) );
		}
		if ( is_wp_error( $post_arg ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_confirm_preauth( $order_id ) {

		$order         = WCRed()->get_order( (int) $order_id );
		$order_preauth = get_transient( $order->get_id() . '_redsys_preauth' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_preauth );
		}
		if ( 'yes' === $order_preauth ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_collect_remainder( $order_id ) {

		$order         = WCRed()->get_order( (int) $order_id );
		$order_collect = get_transient( $order->get_id() . '_redsys_collect' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'preauthorizationsredsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_collect );
		}
		if ( 'yes' === $order_collect ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public static function redsys_preauthorized_js_callback() {
		global $wpdb;

		if ( ! is_admin() ) {
			return;
		}

		set_time_limit( 0 );
		$order_id         = intval( $_POST['order_id'] );
		$order            = WCRed()->get_order( $order_id );
		$transaction_id   = get_post_meta( $order_id, '_payment_order_number_redsys', true );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$redsys_class     = new WC_Gateway_Preauthorizations_Redsys();

		if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'preauthorizationsredsys', __( 'Firs step for confirm Preauthorization for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'preauthorizationsredsys', __( 'Checking for Confirm Preauthorization for order #: ', 'woocommerce-redsys' ) . $order_id );
			}
			$confirm_preauthorization = $redsys_class->ask_for_confirm_preauthorization( $order_id, $transaction_id, $order_total_sign );
			if ( ! $confirm_preauthorization ) {

				if ( 'yes' === $redsys_class->debug ) {
					$redsys_class->log->add( 'preauthorizationsredsys', __( 'Error confirming Preauthorization', 'woocommerce-redsys' ) );
				}
				$confirm_result = __( 'There was an error confirming Preauthorization', 'woocommerce-redsys' );
			} else {
				$x = 0;
				do {
					sleep( 5 );
					$result = $redsys_class->check_confirm_preauth( $order_id );
					$x++;
				} while ( $x <= 20 && false === $result );
				@ob_clean();
				if ( 'yes' === $redsys_class->debug && $result ) {
					$redsys_class->log->add( 'preauthorizationsredsys', __( 'Confirming Preauthorization = true ', 'woocommerce-redsys' ) );
				}
				if ( 'yes' === $redsys_class->debug && ! $result ) {
					$redsys_class->log->add( 'preauthorizationsredsys', __( 'Confirming Preauthorization = false ', 'woocommerce-redsys' ) );
				}
				if ( $result ) {
					delete_transient( $order_id . '_redsys_preauth' );
					$confirm_result = __( 'Successfully Confirming Preauthorization', 'woocommerce-redsys' );
					$redsys_class->log->add( 'preauthorizationsredsys', __( 'Deleted transcient _redsys_preauth', 'woocommerce-redsys' ) );
				} else {
					if ( 'yes' === $redsys_class->debug && $result ) {
						$redsys_class->log->add( 'preauthorizationsredsys', __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' ) );
					}
					$confirm_result = __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' );
				}
			}
		} else {
			if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'preauthorizationsredsys', __( 'Failed Confirming Preauthorization: No transaction ID', 'woocommerce-redsys' ) );
			}
			$confirm_result = __( 'Confirm Preauthorization Failed: No transaction ID', 'woocommerce-redsys' );
		}

		echo esc_html( $confirm_result );
		wp_die();
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public static function redsys_charge_depo_js_callback() {
		global $wpdb;

		$redsys_depo = new WC_Gateway_Preauthorizations_Redsys();
		if ( ! is_admin() ) {
			return;
		}

		set_time_limit( 0 );
		$order_id = intval( $_POST['order_id'] );
		$order    = WCRed()->get_order( $order_id );
		$total    = $order->get_total();
		foreach ( $order->get_items() as $item ) {
			if ( ! empty( $item['is_deposit'] ) ) {
				$deposit_full_amount_ex_vat = '';
				$deposit_full_amount        = '';
				$deposit_full_amount_ex_vat = (float) $item['_deposit_full_amount_ex_tax'];
				$deposit_full_amount        = (float) $item['_deposit_full_amount'];

				if ( ! empty( $deposit_full_amount ) ) {
					$amount = $deposit_full_amount + $amount;
				} else {
					$amount = $deposit_full_amount_ex_vat + $amount;
				}
			}
		}
		$charge           = $amount - $total;
		$order_total_sign = WCRed()->redsys_amount_format( $charge );
		$transaction_id   = get_post_meta( $order_id, '_payment_order_number_redsys', true );

		if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'preauthorizationsredsys', __( 'First step for collect remainder for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Checking for collect remainder for for order #: ', 'woocommerce-redsys' ) . $order_id );
			}
			$confirm_collect_remainder = $redsys_depo->ask_for_collect_remainder( $order_id, $order_total_sign );
			if ( ! $confirm_collect_remainder ) {

				if ( 'yes' === $redsys_depo->debug ) {
					$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Error Collecting Remainder', 'woocommerce-redsys' ) );
				}
				$confirm_result = __( 'There was an error collecting remainder', 'woocommerce-redsys' );
			} else {
				$x = 0;
				do {
					sleep( 5 );
					$result = $redsys_depo->check_collect_remainder( $order_id );
					$x++;
				} while ( $x <= 20 && false === $result );

				@ob_clean();

				if ( 'yes' === $redsys_depo->debug && $result ) {
					$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Confirming Collecting Remainder = true ', 'woocommerce-redsys' ) );
				}

				if ( 'yes' === $redsys_depo->debug && ! $result ) {
					$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Confirming Collecting Remainder = false ', 'woocommerce-redsys' ) );
				}

				if ( $result ) {
					delete_transient( $order_id . '_redsys_collect' );
					$confirm_result = __( 'Successfully Collected Remainder', 'woocommerce-redsys' );
					$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Deleted transcient _redsys_collect', 'woocommerce-redsys' ) );

					foreach ( $order->get_items() as $order_item_id => $order_item ) {

						if ( 'yes' === $redsys_depo->debug && $order_item_id ) {
							$redsys_depo->log->add( 'preauthorizationsredsys', 'Item ID: ' . $order_item_id );
						} else {
							$tredsys_depohis->log->add( 'preauthorizationsredsys', 'No Item ID?' );
						}
						wc_add_order_item_meta( $order_item_id, '_remaining_balance_paid', 1 );
					}
					update_post_meta( $order_id, '_order_total', $amount );
					$order->update_status( 'completed', __( 'Order Completed', 'woocommerce-redsys' ) );

				} else {
					if ( 'yes' === $redsys_depo->debug && $result ) {
						$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Failed Collecting Remainder, please try again', 'woocommerce-redsys' ) );
					}
					$confirm_result = __( 'Failed Collecting Remainder, please try again', 'woocommerce-redsys' );
				}
			}
		} else {
			if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'preauthorizationsredsys', __( 'Failed Collecting Remainder: No transaction ID', 'woocommerce-redsys' ) );
			}
			$confirm_result = __( 'Confirm Collecting Remainder: No transaction ID', 'woocommerce-redsys' );
		}

		echo esc_html( $confirm_result );
		wp_die();
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = get_post_meta( $order_id, '_payment_order_number_redsys', true );
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = WCRed()->redsys_amount_format( $amount );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', '       Once upon a time       ' );
				$this->log->add( 'preauthorizationsredsys', '/****************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', __( 'check_redsys_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'preauthorizationsredsys', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
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
				$this->log->add( 'preauthorizationsredsys', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'preauthorizationsredsys', '/********************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$this->log->add( 'preauthorizationsredsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'preauthorizationsredsys', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'preauthorizationsredsys', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'preauthorizationsredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$this->log->add( 'preauthorizationsredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'preauthorizationsredsys', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public static function preauthorizationsredsys_add_bulk_actions( $bulk_actions ) {

		if ( WCRed()->is_gateway_enabled( 'preauthorizationsredsys' ) ) {
			$bulk_actions['redsys_aprobe_preauthorizations'] = __( 'Approve Pre-authorization', 'woocommerce-redsys' );
		}
		return $bulk_actions;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public static function preauthorizationsredsys_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {

		if ( $doaction === 'redsys_aprobe_preauthorizations' ) {
			$clase_pre = new WC_Gateway_Preauthorizations_Redsys();
			if ( 'yes' === $clase_pre->debug ) {
				$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
				$clase_pre->log->add( 'preauthorizationsredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
				$clase_pre->log->add( 'preauthorizationsredsys', '     redsys_bulk_actions_handler   ' );
				$clase_pre->log->add( 'preauthorizationsredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
				$clase_pre->log->add( 'preauthorizationsredsys', '$redirect_to = ' . $redirect_to );
				$clase_pre->log->add( 'preauthorizationsredsys', '$doaction = ' . $doaction );
				$clase_pre->log->add( 'preauthorizationsredsys', '$post_ids = ' . print_r( $post_ids, true ) );
				$clase_pre->log->add( 'preauthorizationsredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			}
			// Solo continúa si son las acciones que hemos creado nosotros

			if ( 'yes' === $clase_pre->debug ) {
				$clase_pre->log->add( 'preauthorizationsredsys', __( 'Doing Bulk Actions', 'woocommerce-redsys' ) );
			}
			// Si es la acción primera, realizará estas accion

			if ( 'redsys_aprobe_preauthorizations' === $doaction ) {

				foreach ( $post_ids as $post_id ) {

					$order            = wc_get_order( $post_id );
					$status           = $order->get_status();
					$transaction_id   = get_post_meta( $post_id, '_payment_order_number_redsys', true );
					$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
					if ( 'redsys-pre' === $status ) {
						$confirm_preauthorization = $clase_pre->ask_for_confirm_preauthorization( $post_id, $transaction_id, $order_total_sign );
						if ( true !== $confirm_preauthorization ) {
							if ( 'yes' === $clase_pre->debug ) {
								$clase_pre->log->add( 'preauthorizationsredsys', __( 'Error confirming Preauthorization', 'woocommerce-redsys' ) );
							}
							continue;
						} else {
							$x = 0;
							do {
								sleep( 5 );
								$result = $clase_pre->check_confirm_preauth( $post_id );
								$x++;
							} while ( $x <= 20 && false === $result );
							@ob_clean();
							if ( 'yes' === $clase_pre->debug && $result ) {
								$clase_pre->log->add( 'preauthorizationsredsys', __( 'Confirming Preauthorization = true ', 'woocommerce-redsys' ) );
							}
							if ( 'yes' === $clase_pre->debug && ! $result ) {
								$clase_pre->log->add( 'preauthorizationsredsys', __( 'Confirming Preauthorization = false ', 'woocommerce-redsys' ) );
							}
							if ( $result ) {
								delete_transient( $order_id . '_redsys_preauth' );
								if ( 'yes' === $clase_pre->debug ) {
									$clase_pre->log->add( 'preauthorizationsredsys', __( 'Deleted transcient _redsys_preauth', 'woocommerce-redsys' ) );
								}
								continue;
							} else {
								if ( 'yes' === $clase_pre->debug && $result ) {
									$clase_pre->log->add( 'preauthorizationsredsys', __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' ) );
								}
								continue;
							}
						}
					} else {
						continue;
					}
				}
				$redirect_to = add_query_arg( 'redsys_aprobe_preauthorizations', count( $post_ids ), $redirect_to );
				if ( 'yes' === $clase_pre->debug ) {
					$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
					$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$clase_pre->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
					$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
					$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
				}
				return $redirect_to;
			}
		}
		/*
		if ( WCRed()->is_gateway_enabled( 'preauthorizationsredsys' ) ) {
			return $redirect_to;
		}
		*/
		// Solo continúa si son las acciones que hemos creado nosotros
		/*
		if ( 'redsys_charge_invoice_token' !== $doaction &&  'redsys_bulk_refund' !== $doaction ) {
			return $redirect_to;
		}*/
		// Si es la acción primera, realizará estas accion

		if ( 'redsys_bulk_refund' === $doaction ) {
			$class_redsys = new WC_Gateway_Redsys();
			if ( 'yes' === $clase_pre->debug ) {
				$clase_pre->log->add( 'preauthorizationsredsys', __( 'Doing Bulk Actions', 'woocommerce-redsys' ) );
			}
			foreach ( $post_ids as $post_id ) {
				$order                  = wc_get_order( $post_id );
				$status                 = $order->get_status();
				$transaction_id         = get_post_meta( $post_id, '_payment_order_number_redsys', true );
				$refund_amount          = $order->get_total();
				$refund_amount_format   = wc_format_decimal( $order->get_total() );
				$refunded_amount        = 0;
				$refund_reason          = __( 'Bulk refund', 'woocommerce-redsys' );
				$line_item_qtys         = array();
				$line_item_totals       = array();
				$line_item_tax_totals   = array();
				$api_refund             = 'true';
				$restock_refunded_items = 'true';
				$refund                 = false;
				$response               = array();
				$order_id               = $post_id;

				if ( 'pending' !== $status && 'refunded' !== $status ) {
					if ( 'yes' === $clase_pre->debug ) {
						$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
						$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$clase_pre->log->add( 'preauthorizationsredsys', '  Refund order ID:   ' . $post_id );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $status:   ' . $status );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $transaction_id:   ' . $transaction_id );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $refund_amount:   ' . $refund_amount );
						$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
					}

					$get_total_refunded = $order->get_total_refunded();
					$max_refund         = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

					if ( 'yes' === $clase_pre->debug ) {
						$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
						$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $refund_amount:   ' . $refund_amount );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $refund_amount_format:   ' . $refund_amount_format );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $get_total_refunded:   ' . $get_total_refunded );
						$clase_pre->log->add( 'preauthorizationsredsys', '  $max_refund:   ' . $max_refund );
						$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
						$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
					}

					try {
						$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

						if ( ! $max_refund || 0 > $refund_amount ) {
							if ( 'yes' === $clase_pre->debug && $response ) {
								$clase_pre->log->add( 'preauthorizationsredsys', __( 'Invalid refund amount', 'woocommerce-redsys' ) );
							}
							throw new Exception( __( 'Invalid refund amount', 'woocommerce' ) );
						}

						// Prepare line items which we are refunding.
						$line_items = array();
						$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys ), array_keys( $line_item_totals ) ) );

						foreach ( $item_ids as $item_id ) {
							$line_items[ $item_id ] = array(
								'qty'          => 0,
								'refund_total' => 0,
								'refund_tax'   => array(),
							);
						}
						foreach ( $line_item_qtys as $item_id => $qty ) {
							$line_items[ $item_id ]['qty'] = max( $qty, 0 );
						}
						foreach ( $line_item_totals as $item_id => $total ) {
							$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
						}
						foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
							$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
						}

						// Create the refund object.
						$refund = wc_create_refund(
							array(
								'amount'         => $max_refund,
								'reason'         => $refund_reason,
								'order_id'       => $order_id,
								'line_items'     => $line_items,
								'refund_payment' => $api_refund,
								'restock_items'  => $restock_refunded_items,
							)
						);

						if ( is_wp_error( $refund ) ) {
							throw new Exception( $refund->get_error_message() );
						}

						if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
							$response = 'fully_refunded';
						}
					} catch ( Exception $e ) {
						$response = 'error' . $e->getMessage();
					}

					if ( 'fully_refunded' === $response ) {
						continue;
					} else {
						if ( 'yes' === $clase_pre->debug && $response ) {
							$clase_pre->log->add( 'preauthorizationsredsys', __( 'Failed refund order : ', 'woocommerce-redsys' ) . $response );
						}
						continue;
					}
				} else {
					if ( 'yes' === $clase_pre->debug && $response ) {
						$clase_pre->log->add( 'preauthorizationsredsys', __( 'The order is pending payment, or has already been refunded.', 'woocommerce-redsys' ) );
					}
					continue;
				}
			}
			$redirect_to = add_query_arg( 'redsys_bulk_refund', count( $post_ids ), $redirect_to );
			if ( 'yes' === $clase_pre->debug ) {
				$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
				$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$clase_pre->log->add( 'preauthorizationsredsys', '  The final has come, this story has ended  ' );
				$clase_pre->log->add( 'preauthorizationsredsys', '/******************************************/' );
				$clase_pre->log->add( 'preauthorizationsredsys', ' ' );
			}
		}
		return $redirect_to;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	public function warning_checkout_test_mode() {
		if ( 'yes' === $this->testmode && WCRed()->is_gateway_enabled( $this->id ) ) {
			echo '<div class="checkout-message" style="
			background-color: #f39c12;
			padding: 1em 1.618em;
			margin-bottom: 2.617924em;
			margin-left: 0;
			border-radius: 2px;
			color: #fff;
			clear: both;
			border-left: 0.6180469716em solid rgb(228, 120, 51);
			">';
			echo __( 'Warning: WooCommerce Redsys Gateway Preauthorzations is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function check_user_show_payment_method( $userid = false ) {

		$test_mode  = $this->testmode;
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'preauthorizationsredsys' );

		if ( 'yes' !== $test_mode ) {
			return true;
		}
		if ( $selections[0] !== '' || empty( $selections ) ) {
			if ( ! $userid ) {
				return false;
			}
			foreach ( $selections as $user_id ) {
				if ( (int) $user_id === (int) $userid ) {
					return true;
				}
				continue;
			}
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2022 José Conti
	 */
	function show_payment_method( $available_gateways ) {

		if ( ! is_admin() ) {
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$show = $this->check_user_show_payment_method( $user_id );
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
}
/**
 * Copyright: (C) 2013 - 2022 José Conti
 */
function woocommerce_add_gateway_preauthorizations_gateway( $methods ) {
	$methods[] = 'WC_Gateway_Preauthorizations_Redsys';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_preauthorizations_gateway' );
