<?php
/**
 * WooCommerce Redsys Gateway Redirection
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2013 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2023 José Conti
 */
class WC_Gateway_Redsys extends WC_Payment_Gateway {
	var $notify_url;
	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		global $checkfor254;

		$this->id = 'redsys';

		if ( ! empty( WCRed()->get_redsys_option( 'logo', 'redsys' ) ) ) {
			$logo_url   = WCRed()->get_redsys_option( 'logo', 'redsys' );
			$this->icon = apply_filters( 'woocommerce_redsys_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_redsys_icon', REDSYS_PLUGIN_URL_P . 'assets/images/redsys.png' );
		}
		$this->has_fields           = true;
		$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = WCRed()->get_redsys_option( 'testmode', 'redsys' );
		$this->method_title         = __( 'Redsys Redirection (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'This payment form works redirecting customers to Redsys or paying directly without leaving the website if you have active payment with 1 click and the user has a token saved.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'redsys' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_redsys', home_url( '/' ) ) );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->psd2                 = 'yes';
		$this->multisitesttings     = WCRed()->get_redsys_option( 'multisitesttings', 'redsys' );
		$this->ownsetting           = WCRed()->get_redsys_option( 'ownsetting', 'redsys' );
		$this->hideownsetting       = WCRed()->get_redsys_option( 'hideownsetting', 'redsys' );
		$this->deletetoken          = WCRed()->get_redsys_option( 'deletetoken', 'redsys' );
		$this->bankingnetwork       = WCRed()->get_redsys_option( 'bankingnetwork', 'redsys' );
		$this->title                = WCRed()->get_redsys_option( 'title', 'redsys' );
		$this->description          = WCRed()->get_redsys_option( 'description', 'redsys' );
		$this->logo                 = WCRed()->get_redsys_option( 'logo', 'redsys' );
		$this->orderdo              = WCRed()->get_redsys_option( 'orderdo', 'redsys' );
		$this->customer             = WCRed()->get_redsys_option( 'customer', 'redsys' );
		$this->merchantgroup        = WCRed()->get_redsys_option( 'merchantgroup', 'redsys' );
		$this->commercename         = WCRed()->get_redsys_option( 'commercename', 'redsys' );
		$this->terminal             = WCRed()->get_redsys_option( 'terminal', 'redsys' );
		$this->secret               = WCRed()->get_redsys_option( 'secret', 'redsys' );
		$this->secretsha256         = WCRed()->get_redsys_option( 'secretsha256', 'redsys' );
		$this->customtestsha256     = WCRed()->get_redsys_option( 'customtestsha256', 'redsys' );
		$this->debug                = WCRed()->get_redsys_option( 'debug', 'redsys' );
		$this->hashtype             = WCRed()->get_redsys_option( 'hashtype', 'redsys' );
		$this->redsyslanguage       = WCRed()->get_redsys_option( 'redsyslanguage', 'redsys' );
		$this->redsysordertype      = WCRed()->get_redsys_option( 'redsysordertype', 'redsys' );
		$this->subfix               = WCRed()->get_redsys_option( 'subfix', 'redsys' );
		$this->wooredsysurlko       = WCRed()->get_redsys_option( 'wooredsysurlko', 'redsys' );
		$this->terminal2            = WCRed()->get_redsys_option( 'terminal2', 'redsys' );
		$this->useterminal2         = WCRed()->get_redsys_option( 'useterminal2', 'redsys' );
		$this->toamount             = WCRed()->get_redsys_option( 'toamount', 'redsys' );
		$this->usetokens            = WCRed()->get_redsys_option( 'usetokens', 'redsys' );
		$this->subsusetokensdisable = WCRed()->get_redsys_option( 'subsusetokensdisable', 'redsys' );
		$this->usetokensdirect      = WCRed()->get_redsys_option( 'usetokensdirect', 'redsys' );
		$this->bulkcharge           = WCRed()->get_redsys_option( 'bulkcharge', 'redsys' );
		$this->bulkrefund           = WCRed()->get_redsys_option( 'bulkrefund', 'redsys' );
		$this->sendemails           = WCRed()->get_redsys_option( 'sendemails', 'redsys' );
		$this->checkoutredirect     = WCRed()->get_redsys_option( 'checkoutredirect', 'redsys' );
		$this->showthankyourecipe   = WCRed()->get_redsys_option( 'showthankyourecipe', 'redsys' );
		$this->usebrowserreceipt    = WCRed()->get_redsys_option( 'usebrowserreceipt', 'redsys' );
		$this->lwvactive            = WCRed()->get_redsys_option( 'lwvactive', 'redsys' );
		$this->traactive            = WCRed()->get_redsys_option( 'traactive', 'redsys' );
		$this->traamount            = WCRed()->get_redsys_option( 'traamount', 'redsys' );
		$this->notiemail            = WCRed()->get_redsys_option( 'notiemail', 'redsys' );
		$this->hidegatewaychckout   = WCRed()->get_redsys_option( 'hidegatewaychckout', 'redsys' );
		$this->redsysdirectdeb      = 'T';
		$this->privateproduct       = WCRed()->get_redsys_option( 'privateproduct', 'redsys' );
		$this->sentemailscustomers  = WCRed()->get_redsys_option( 'sentemailscustomers', 'redsys' );
		$this->sendemailthankyou    = WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' );
		$this->sendemailthankyoutxt = WCRed()->get_redsys_option( 'sendemailthankyoutxt', 'redsys' );
		$this->testforuser          = WCRed()->get_redsys_option( 'testforuser', 'redsys' );
		$this->testforuserid        = WCRed()->get_redsys_option( 'testforuserid', 'redsys' );
		$this->redsysbanktransfer   = WCRed()->get_redsys_option( 'redsysbanktransfer', 'redsys' );
		$this->redirectiontime      = WCRed()->get_redsys_option( 'redirectiontime', 'redsys' );
		$this->sendemailsdscard     = WCRed()->get_redsys_option( 'sendemailsdscard', 'redsys' );
		$this->buttoncheckout       = WCRed()->get_redsys_option( 'buttoncheckout', 'redsys' );
		$this->butonbgcolor         = WCRed()->get_redsys_option( 'butonbgcolor', 'redsys' );
		$this->butontextcolor       = WCRed()->get_redsys_option( 'butontextcolor', 'redsys' );
		$this->descripredsys        = WCRed()->get_redsys_option( 'descripredsys', 'redsys' );
		$this->markpending          = WCRed()->get_redsys_option( 'markpending', 'redsys' );
		$this->testshowgateway      = WCRed()->get_redsys_option( 'testshowgateway', 'redsys' );
		$this->disablesubscrippaid  = WCRed()->get_redsys_option( 'disablesubscrippaid', 'redsys' );
		$this->log                  = new WC_Logger();
		$this->supports             = array(
			'products',
			'tokenization',
			'add_payment_method',
			'refunds',
			'pre-orders',
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
			'redsys_preauth',
			'redsys_token_r',
		);
		// Actions WooCommerce.
		add_action( 'valid-redsys-standard-ipn-request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_redsys', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// add_action( 'before_woocommerce_pay', array( $this, 'redirect_to_checkout' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode' ) );
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'renew_yith_subscription' ), 10, 1 );
		add_action( 'woocommerce_after_checkout_form', array( $this, 'custom_jquery_checkout' ) );

		// WooCommerce Subscriptions.
		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'doing_scheduled_subscription_payment' ), 10, 2 );
		}
		add_action( 'wp_footer', array( $this, 'add_js_footer_checkout' ), 100 );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'override_checkout_fields' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_priority_fields' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_field_update_order_meta' ) );
		add_action( 'wp_head', array( $this, 'add_ajax_url_header' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'thankyou_redirect' ), 10, 1 );
		add_filter( 'woocommerce_account_payment_methods_columns', array( $this, 'anadir_column' ) );
		add_action( 'woocommerce_account_payment_methods_column_redsys', array( $this, 'anadir_column_content' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hide_payment_method_add_method' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hide_payment_method_by_country_redsys' ) );

		// Sumo subscriptions.

		add_filter( 'sumosubscriptions_available_payment_gateways', __CLASS__ . '::add_subscription_supports' );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
	public function is_valid_for_use() {

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
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'Servired/RedSys Spain', 'woocommerce-redsys' ); ?></h3>
		<p><?php esc_html_e( 'Servired/RedSys works by sending the user to your bank TPV to enter their payment information.', 'woocommerce-redsys' ); ?></p>
		<?php
		$screen = get_current_screen();
		WCRed()->return_help_notice();

		if ( isset( $_GET['quijote'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
		<div class="inline error"><p><strong><?php esc_html_e( 'Gateway Disabled', 'woocommerce-redsys' ); ?></strong>: 
			<?php
				esc_html_e( 'Servired/RedSys only support ', 'woocommerce-redsys' );
				echo esc_html( $formated_currencies );
			?>
			</p>
		</div>
			<?php
		endif;
	}
	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @return void
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function init_form_fields() {

		$options    = array();
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'redsys' );

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
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'redsys' );
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
			'esencial' => array( // Customizations.
				'title'       => __( 'Essential', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'This is the essential configuration, everything must be filled in. If something is not filled in or is filled in incorrectly, the payment will fail.', 'woocommerce-redsys' ),
			),
			'enabled'               => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Servired/RedSys', 'woocommerce-redsys' ),
				'description' => __( 'Enable this payment method in checkout.', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'title'                 => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Here add the title which the user sees during checkout. Ex: "Pay with Credit Card', 'woocommerce-redsys' ),
				'default'     => __( 'Redsys', 'woocommerce-redsys' ),
			),
			'description'           => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'Add here a text or explanation of what this payment method is. It will be displayed at Checkout when the customer selects this payment method.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via RedSys; you can pay with your credit card.', 'woocommerce-redsys' ),
			),
			'customer'              => array(
				'title'       => __( 'Commerce number (FUC)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woocommerce-redsys' ),
			),
			'commercename'          => array(
				'title'       => __( 'Commerce Name', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add here the name of your store. This name is what your customers will normally see when they come to Redsys.', 'woocommerce-redsys' ),
			),
			'terminal'              => array(
				'title'       => __( 'Terminal number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add here the Terminal Number provided by your bank. If, for example, the number you have been given is "001", enter "1".', 'woocommerce-redsys' ),
			),
			'secretsha256'          => array(
				'title'       => __( 'Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for production.', 'woocommerce-redsys' ),
			),
			'customtestsha256'      => array(
				'title'       => __( 'TEST MODE: Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for testing.', 'woocommerce-redsys' ),
			),
			// *******************************************/
			'esencial_extra' => array( // Customizations.
				'title'       => __( 'Essential Extra', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'In some cases, these fields may also be essential, depending on your needs, but if you do not touch them, the gateway will work perfectly.', 'woocommerce-redsys' ),
			),
			'merchantgroup'         => array(
				'title'       => __( 'Merchant Group Number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'It is an identifier for sharing tokens between websites of the same company. You will not usually have this number, so unless you have expressly requested it from your bank, you should never fill it in.', 'woocommerce-redsys' ),
			),
			'descripredsys'         => array(
				'title'       => __( 'Redsys description', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Select what will be displayed in Redsys as the order description. You have, by default, different possibilities, but you can customize them by using a filter. If you are interested in the filter, open a ticket.', 'woocommerce-redsys' ),
				'default'     => 'order',
				'options'     => array(
					'order' => __( 'Order ID', 'woocommerce-redsys' ),
					'id'    => __( 'List of products ID', 'woocommerce-redsys' ),
					'name'  => __( 'List of products name', 'woocommerce-redsys' ),
					'sku'   => __( 'List of products SKU', 'woocommerce-redsys' ),
				),
			),
			'bankingnetwork'        => array(
				'title'       => __( 'When show redirection', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Select Show when is NOT Banking network, Spain & Portugal or Show to all countries.', 'woocommerce-redsys' ),
				'default'     => 'showallcountries',
				'options'     => array(
					'showbankingnetwork' => __( 'Show when is NOT Banking network (NO show to Spain & Portugal)', 'woocommerce-redsys' ),
					'showallcountries'   => __( 'Show to all countries', 'woocommerce-redsys' ),
				),
			),
			'multisitesttings'      => array(
				'title'       => __( 'Use in Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Use this setting around all Network', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'hideownsetting'        => array(
				'title'       => __( 'Hide "NOT use Network" in subsites', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Hide "NOT use Network" in subsites', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'ownsetting'            => array(
				'title'       => __( 'NOT use Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Do NOT use Network settings. Use settings of this page', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			// *******************************************/
			'customization_details' => array( // Customizations.
				'title'       => __( 'Customization', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'Here you can customize how the payment method is displayed at checkout..', 'woocommerce-redsys' ),
			),
			'logo'                  => array(
				'title'       => __( 'Gateway logo at checkout', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add link to image logo for Gateway at checkout.', 'woocommerce-redsys' ),
			),
			'usebrowserreceipt'     => array(
				'title'       => __( 'How to show Redsys', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Select how do you want to show Redsys payment page.', 'woocommerce-redsys' ),
				'default'     => 'redirection',
				'options'     => array(
					'redirection' => __( 'Redirect to Redsys for payment', 'woocommerce-redsys' ),
					'iframe'      => __( 'Modal in the checkout.', 'woocommerce-redsys' ),
				),
			), 
			'buttoncheckout'        => array(
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
			'butontextcolor'        => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button text color Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			// *******************************************/

			'buls_actions'          => array( // Bulk Actions.
				'title'       => __( 'Bulk Actions', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'Configure Bulk Actions. By default, they are disabled for security, but you can enable the ones you need. Be aware that they can be dangerous if you use them unintentionally.', 'woocommerce-redsys' ),
			),
			'bulkcharge'            => array(
				'title'       => __( 'Add Bulk Action Immediate Charge', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'ATTENTION: Pay with one Click has to be active before mark this option and terminal has to be NOT SECURE. With this option, you can charge many orders using users cart tokens', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Bulk Action Immediate Charge', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'bulkrefund'            => array(
				'title'       => __( 'Add Bulk Action Refunds', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'This option adds the bulk action Refunds. For security reasons, do not activate it if not needed', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Bulk Action Refunds', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			// *******************************************/

			'subscriptions_details' => array( // Subscriptions Settings.
				'title'       => __( 'Subscriptions Settings', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'Configure certain functionalities for subscriptions. You must read well what each one is for. Even if you use subscriptions, you may never need to touch any of the following features.', 'woocommerce-redsys' ),
			),
			'subsusetokensdisable'  => array(
				'title'       => __( 'Disable Subscription token', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Tokenization is enabled by default (Enable Pay with One Click is not needed). Here you can disable tokenization for WooCommerce Subscriptions. This is a unique feature that triggers a subscription token NOT to be captured. This means that the customer must always make payments manually. Payments can NOT be made automatically if this option is enabled.', 'woocommerce-redsys' ),
				'label'       => __( 'Disable Subscription token, it is enabled by default', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'disablesubscrippaid'   => array(
				'title'       => __( 'Disable mark as paid Subscriptions by plugin', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'In some installations, Subscriptions mark orders as paid, while in others, it does not. This causes that in some installations the orders are marked as paid twice, once by the Subscriptions plugin and once by the Redsys plugin. If, in your installation, the orders are marked as paid twice, activate this option so that the Redsys plugin does not mark it as paid.', 'woocommerce-redsys' ),
				'label'       => __( 'Disable mark Subscription as paid by Redsys plugin', 'woocommerce-redsys' ),
				'default'     => 'no',
			),

			'advenced_details'      => array( // Advenced Settings.
				'title'       => __( 'Advanced', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your.', 'woocommerce-redsys' ),
			),
			// *******************************************/

			'csa_details'           => array( // SCs Settings.
				'title'       => __( 'SCAs', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'SCAs are options that require less customer authentication at the time of payment. You must ask Redsys to activate them before using them, never activate them if they are not active in the terminal or you may be penalized for it.', 'woocommerce-redsys' ),
			),
			'lwvactive'             => array(
				'title'       => __( 'Enable LWV', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable SCA LWV.', 'woocommerce-redsys' ),
				'description' => __( 'Enable SCA LWV. WARNING, your bank has to enable it before you use it.', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'traactive'             => array(
				'title'       => __( 'Enable TRA', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable SCA TRA.', 'woocommerce-redsys' ),
				'description' => __( 'Enable SCA TRA. WARNING, your bank has to enable it before you use it.', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'traamount'             => array(
				'title'       => __( 'Limit import for TRA', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'TRA will be sent when the amount is inferior to what you specify here. Write the amount without the currency sign, i.e. if it is 250€, ONLY write 250', 'woocommerce-redsys' ),
			),
			// *******************************************/

			'hidegatewaychckout'    => array(
				'title'   => __( 'Hide in Checkout', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Checking this option, the payment method will be shown only in the user account "Add method"', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'redirectiontime'       => array(
				'title'       => __( 'Redirection time', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'If you want users to be immediately redirected to the payment gateway when they press the pay button, don\'t add anything. If you want to give them time to think about it, add the seconds in milliseconds, for example, 5 seconds are 5000 milliseconds.', 'woocommerce-redsys' ),
			),
			'showthankyourecipe'    => array(
				'title'       => __( 'Show Redsys Authorization Code at the Thank You Page', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'If you have asked the bank to redirect customers to your site after payment without them having to click on "continue", you must activate this option so that the authorization number requested by Redsys is displayed.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Show Authorization Code', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'usetokens'             => array(
				'title'       => __( 'Pay with One Click', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'With Pay with one Click, users who have bought before in your store should not fill the credit card number in Redsys again. Make sure you have activated in Redsys that he send to your store the credit card number.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Pay with One Click', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'deletetoken'           => array(
				'title'   => __( 'Delete expired tokens', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable automatically delete tokens if the asociated credit card has expired. WARNING: If your bank is not sending you the expiration dates, and fake dates are being saved, you can delete valid tokens.', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'notiemail'             => array(
				'title'       => __( 'Notification email', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Email errors will arrive to this email', 'woocommerce-redsys' ),
			),
			'usetokensdirect'       => array(
				'title'       => __( 'One Click in page?', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'ATTENTION: Pay with one Click has to be active before mark this option. With this option, users to whom you have already collected Tokens for previous purchases, they do not leave the page after pressing the payment button. Your terminal must be unsafe, or it will not work. ', 'woocommerce-redsys' ),
				'label'       => __( 'Enable One Click in page', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'checkoutredirect'      => array(
				'title'       => __( 'One Click to Checkout', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'ATTENTION: This option can break your website under some circunstances, check your website and checkout before and after enable this option. With this option, the customer is redirected to checkout after add a product to the card. Only activate this option if your customers ONLY buy ONE product every time.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable One Click to Checkout', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemails'            => array(
				'title'       => __( 'Send emails', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users fails to pay in Redsys, and email will be send to you with the problem, amount and link to the order details.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails when payment fails', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailsdscard'      => array(
				'title'       => __( 'Send emails Ds_Card_Number problem', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'If tokenization is used, the filed Ds_Card_number can be a very interesting information. If Redsys isn\'t sending this field and this options is active, and email will be sent to the website administrator.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails Ds_Card_number problem', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sentemailscustomers'   => array(
				'title'       => __( 'Send emails to customers', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users fails to pay in Redsys, and email will be send to the customer with the problem, This can increase cart recovery.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send emails to customers when payment fails', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailthankyou'     => array(
				'title'       => __( 'Notice Thank you problem', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Every time that a users arrive to Thank you page from Redsys, and the order is not marked as paid, and email will be send to adminsitrator for to warn the administrator to check Redsys to see if payment has been made and a notice will be shown to customer at Thank you Page.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Send email Thank you problem for be noticed', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'sendemailthankyoutxt'  => array(
				'title'       => __( 'Text on the thank you page', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the text that will be show to customers that arrive to the Thank You page if their order is not marked as paid.', 'woocommerce-redsys' ),
				'default'     => __( '<p><b>ATTENTION:</b> You have used Redsys for the payment. We have detected that there may have been a problem with your payment and it has not been marked as paid.  Do not worry, we have detected it and we have received an email with the notice, so we let\'s check it to make sure it has.</p>', 'woocommerce-redsys' ), // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			),
			'redsyspreauthall'      => array(
				'title'       => __( 'Preauthorization for all payments', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'This option will make all payments as preauthorization. You can capture the payment later in the order details.', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Preauthorization for all payments', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			'useterminal2'          => array(
				'title'       => __( 'Activate Second Terminal', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate Second Terminal.', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'If you use a second terminal, you need to add it in the field above and activate it here. You will need to set when use the Second Terminal in the field below.', 'woocommerce-redsys' ) ),
			),
			'terminal2'             => array(
				'title'       => __( 'Second Terminal', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'If you use a second Terminal number, you need to add here the second terminal provided by your bank', 'woocommerce-redsys' ),
			),
			'toamount'              => array(
				'title'       => __( 'Use the Second Terminal from 0 to (Don\'t use Currency Symbol)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'When will the Second Terminal used? from 0 to...? Add the amount. Ex. Add 100 and the Second Terminal will be used when the amount be from 0 to 100', 'woocommerce-redsys' ),
			),
			'not_use_https'         => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility (only activate it if José Conti indicate you).', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Only use it if José Conti indicate you. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woocommerce-redsys' ) ),
			),
			'redsysordertype'       => array(
				'title'       => __( 'Order Number Format', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the Order Number Format send to Redsys', 'woocommerce-redsys' ),
				'default'     => 'threepluszeros',
				'options'     => array(),
			),
			'subfix'                => array(
				'title'       => __( 'Add a Sufix', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'If you want to add a subfix to your Order number, add it here.', 'woocommerce-redsys' ),
			),
			'markpending'           => array(
				'title'       => __( 'Before Pay', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Which status has an order before being paid?', 'woocommerce-redsys' ),
				'default'     => 'processing',
				'options'     => array(
					'pending'       => __( 'Mark as Pending Payment (default WooCommerce)', 'woocommerce-redsys' ),
					'redsyspending' => __( 'Mark as Pending Redsys Payment', 'woocommerce-redsys' ),
				),
			),
			'orderdo'               => array(
				'title'       => __( 'What to do after payment?', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose what to do after the customer pay the order.', 'woocommerce-redsys' ),
				'default'     => 'processing',
				'options'     => array(
					'processing' => __( 'Mark as Processing (default & recomended)', 'woocommerce-redsys' ),
					'completed'  => __( 'Mark as Complete', 'woocommerce-redsys' ),
				),
			),
			'redsyslanguage'        => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'wooredsysurlko'        => array(
				'title'       => __( 'Return URL (Redsys Error button)', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'When the user press the return button at Redsys Gateway (Ex: The user type an incorrect credit card), you can redirect the user to My Cart page canceling the order, or you can redirect the user to Checkput page without cancel the order.', 'woocommerce-redsys' ),
				'default'     => 'returncancel',
				'options'     => array(
					'returncancel'   => __( 'Cancel the order and return to My Cart page', 'woocommerce-redsys' ),
					'returnnocancel' => __( 'Don\'t cancel the order and return to Checkout page', 'woocommerce-redsys' ),
				),
			),
			'privateproduct'        => array(
				'title'       => __( 'Private Products', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'description' => __( 'Activate Private Products if you need to create products visible per customer', 'woocommerce-redsys' ),
				'label'       => __( 'Enable Private Products', 'woocommerce-redsys' ),
				'default'     => 'no',
			),
			// *******************************************/
			'test_details'          => array( // Test Settings.
				'title'       => __( 'Test & Debug', 'woocommerce-redsys' ),
				'type'        => 'title',
				'description' => __( 'When you need to perform tests and debug, you can configure everything here. Read what each option is for to configure everything as you need.', 'woocommerce-redsys' ),
			),
			'testmode'              => array(
				'title'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woocommerce-redsys' ),
			),
			'testshowgateway'       => array(
				'title'       => __( 'Show to this users', 'woocommerce-redsys' ),
				'type'        => 'multiselect',
				'label'       => __( 'Show the gateway in the chcekout when it is in test mode', 'woocommerce-redsys' ),
				'class'       => 'js-woo-show-gateway-test-settings',
				'id'          => 'woocommerce_redsys_showtestforuserid',
				'options'     => $options_show,
				'default'     => '',
				'description' => sprintf( __( 'Select users that will see the gateway when it is in test mode. If no users are selected, will be shown to all users', 'woocommerce-redsys' ) ),
			),
			'testforuser'           => array(
				'title'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode for a user', 'woocommerce-redsys' ),
				'default'     => 'yes',
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
			'debug'                 => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log Servired/RedSys events, such as notifications requests, inside <code>WooCommerce > Status > Logs > redsys-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);

		$redsyslanguages = WCRed()->get_redsys_languages();
		foreach ( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['redsyslanguage']['options'][ $redsyslanguage ] = $valor;
		}

		$redsyordertypes = WCRed()->get_orders_number_type();
		foreach ( $redsyordertypes as $redsyordertype => $valor ) {
			$this->form_fields['redsysordertype']['options'][ $redsyordertype ] = $valor;
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
	 * Add colum
	 *
	 * @param array $columns Colums.
	 */
	public function anadir_column( $columns ) {
		$new_columns           = array();
		$new_columns['redsys'] = __( 'Type', 'woocommerce-redsys' );
		foreach ( $columns as $column => $valor ) {
			$new_columns[ $column ] = $valor;
		}
		return $new_columns;
	}
	/**
	 * Add colum to methods.
	 *
	 * @param array $method Method.
	 */
	public function anadir_column_content( $method ) {

		$url      = $method['actions']['delete']['url'];
		$clean    = wp_parse_url( $url, PHP_URL_PATH );
		$token_id = basename( $clean );
		$token    = WC_Payment_Tokens::get( $token_id );
		if ( $token ) {
			$tonen_num  = $token->get_token();
			$token_type = WCRed()->get_token_type( $token->get_id() );

			if ( 'C' === $token_type ) {
				$token_type_name = __( 'Pay with 1click', 'woocommerce-redsys' );
			} elseif ( 'R' === $token_type ) {
				$token_type_name = __( 'Subscription', 'woocommerce-redsys' );
			} else {
				$token_type_name = '-';
			}
		}
		echo esc_html( $token_type_name );
	}
	/**
	 * Redirect to thankyou page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_redirect( $order_id ) {
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			$transient = get_transient( $order_id . '_iframe' );
			if ( 'yes' === $transient ) {
				delete_transient( $order_id . '_iframe' );
				$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidatedNotSanitized
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  URL a redirigir por iFrame  ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '$actual_link: ' . $actual_link );
				}
				$current_url = str_replace( '#038;', '&', $actual_link );
				$actual_link = str_replace( '&&', '&', $current_url );
				echo '<script>window.top.location.href = "' . esc_url( $actual_link ) . '"</script>';
				exit();
			}
		}
	}
	/**
	 * Check if user is in test mode.
	 *
	 * @param int $userid User ID.
	 */
	public function check_user_test_mode( $userid ) {

		$usertest_active = $this->testforuser;
		$selections      = (array) WCRed()->get_redsys_option( 'testforuserid', 'redsys' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '     Checking user test       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $usertest_active ) {
			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '   Checking user ' . $userid );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '  User in forach ' . $user_id );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', '   Checking user test TRUE    ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/********************************************/' );
							$this->log->add( 'redsys', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'redsys', '/********************************************/' );
							$this->log->add( 'redsys', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '  Checking user test continue ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  Checking user test FALSE    ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  Checking user test FALSE    ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '     User test Disabled.      ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			return false;
		}
	}
	/**
	 * Get Redsys URL Gateway
	 *
	 * @param  string $user_id User ID.
	 * @param  string $type    Type.
	 * @return string
	 */
	public function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          URL Test RD         ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          URL Test WS         ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          URL Test RD         ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          URL Test WS         ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          URL Live RD         ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          URL Live WS         ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$url = $this->liveurlws;
				}
			}
		}
		return $url;
	}
	/**
	 * Get Redsys URL Gateway
	 *
	 * @param  string $user_id User ID.
	 * @param  string $type    Type.
	 * @return string
	 */
	public static function get_redsys_url_gateway_p( $user_id, $type = 'rd' ) {

		$log    = new WC_Logger();
		$redsys = new WC_Gateway_Redsys();
		if ( 'yes' === $redsys->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $redsys->debug ) {
					$log->add( 'redsys', ' ' );
					$log->add( 'redsys', '/****************************/' );
					$log->add( 'redsys', '          URL Test RD         ' );
					$log->add( 'redsys', '/****************************/' );
					$log->add( 'redsys', ' ' );
				}
				$url = $redsys->testurl;
			} else {
				if ( 'yes' === $redsys->debug ) {
					$log->add( 'redsys', ' ' );
					$log->add( 'redsys', '/****************************/' );
					$log->add( 'redsys', '          URL Test WS         ' );
					$log->add( 'redsys', '/****************************/' );
					$log->add( 'redsys', ' ' );
				}
				$url = $redsys->testurlws;
			}
		} else {
			$user_test = $redsys->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $redsys->debug ) {
						$log->add( 'redsys', ' ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', '          URL Test RD         ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', ' ' );
					}
					$url = $redsys->testurl;
				} else {
					if ( 'yes' === $redsys->debug ) {
						$log->add( 'redsys', ' ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', '          URL Test WS         ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', ' ' );
					}
					$url = $redsys->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $redsys->debug ) {
						$log->add( 'redsys', ' ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', '          URL Live RD         ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', ' ' );
					}
					$url = $redsys->liveurl;
				} else {
					if ( 'yes' === $redsys->debug ) {
						$log->add( 'redsys', ' ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', '          URL Live WS         ' );
						$log->add( 'redsys', '/****************************/' );
						$log->add( 'redsys', ' ' );
					}
					$url = $redsys->liveurlws;
				}
			}
		}
		return $url;
	}
	/**
	 * Get Redsys SHA256
	 *
	 * @param  string $user_id User ID.
	 * @return string
	 */
	public function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '         SHA256 Test.         ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      SHA256 Test Custom.     ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$sha256 = $customtestsha256;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '     SHA256 Test Standard.    ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      USER SHA256 Test.       ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      SHA256 Test Custom.     ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$sha256 = $customtestsha256;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '     SHA256 Test Standard.    ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}
	/**
	 * Get redsys Args for passing to PP
	 *
	 * @param  WC_Order $order Order object.
	 * @return array
	 */
	public function get_redsys_args( $order ) {

		$customer_token   = '';
		$customer_token_c = '';
		$customer_token_r = '';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '     Making redsys_args       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		$order_id         = $order->get_id();
		$currency_codes   = WCRed()->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$transaction_id2: ' . $transaction_id2 );
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', ' ' );
		}
		if ( WCRed()->order_needs_preauth( $order_id ) ) {
			if ( 'yes' === $redsys->debug ) {
				$this->log->add( 'redsys', 'IS Preauthorization' );
			}
			$transaction_type = '1';
		} else {
			if ( 'yes' === $redsys->debug ) {
				$this->log->add( 'redsys', 'IS NOT Preauthorization' );
			}
			$transaction_type = '0';
		}

		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Using WPML' );
				$this->log->add( 'redsys', 'The ICL_LANGUAGE_CODE is: ' . ICL_LANGUAGE_CODE );
				$this->log->add( 'redsys', ' ' );
			}
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
			$this->log->add( 'redsys', '$psd2: ' . $psd2 );
		}
		if ( 'yes' !== $this->psd2 ) {
			$customer_token = WCRed()->get_redsys_users_token();
		} else {
			$customer_token_r    = WCRed()->get_redsys_users_token( 'R' );
			$customer_token_c    = WCRed()->get_redsys_users_token( 'C' );
			$customer_token_r_id = WCRed()->get_redsys_users_token( 'R', 'id' );
			$customer_token_c_id = WCRed()->get_redsys_users_token( 'C', 'id' );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'redsys', '$customer_token_r: ' . $customer_token_r );
				$this->log->add( 'redsys', '$customer_token_c: ' . $customer_token_c );
			}
		}
		$customer_token = WCRed()->get_redsys_users_token();

		$redsys_data_send = array();

		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$user_id             = $order->get_user_id();
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$customer            = $this->customer;
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, 'redsys' );
		$merchant_name       = $this->commercename;

		$redsys_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => (string) $transaction_id2,
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
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
				$this->log->add( 'redsys', ' ' );
			}
		}

		$secretsha256     = $redsys_data_send['secretsha256'];
		$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		// redsys Args.
		$mi_obj = new WooRedsysAPI();
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
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $redsys_data_send['merchant_name'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );

		// [T = Pago con Tarjeta + iupay , R = Pago por Transferencia, D = Domiciliacion, C = Sólo Tarjeta (mostrará sólo el formulario para datos de tarjeta)] por defecto es T
		if ( 'T' === $this->redsysdirectdeb || empty( $this->redsysdirectdeb ) ) { // No se puede ofrecer domiciliación y tarjeta con pago por referencia a la vez.
			if ( WCRed()->order_contains_subscription( $order_id ) ) {
				if ( WCRed()->order_contains_subscription( $order_id ) && 'yes' !== $this->subsusetokensdisable ) {
					if ( ! $customer_token_r ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
						$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
						$ds_merchant_data = 'no';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: 0' );
							$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
							if ( ! empty( $this->merchantgroup ) ) {
								$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
							} else {
								$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
							}
							$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'redsys', '/***************************************************************/' );
								$this->log->add( 'redsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
								$this->log->add( 'redsys', '/***************************************************************/' );
								$this->log->add( 'redsys', 'DS_MERCHANT_COF_INI: S' );
								$this->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: R' );
								$this->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
							}
						}
					} else {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_r );
						$txnid = WCRed()->get_txnid( $customer_token_r_id );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TXNID', $txnid );
						if ( ! empty( $this->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
						$ds_merchant_data           = 'yes';
						$ds_merchant_direct_payment = 'false';
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_r );
							$this->log->add( 'redsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
							$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							if ( $psd2 ) {
								$this->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
								$this->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: R' );
								$this->log->add( 'redsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
							}
						}
					}
				}
			} elseif ( 'yes' === $this->usetokens ) {
				// Pago con 1 clic activo.
				if ( ! $customer_token_c ) {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
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
						$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: 0' );
						$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
						if ( ! empty( $this->merchantgroup ) ) {
							$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
						} else {
							$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
						}
						$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
						if ( $psd2 ) {
							$this->log->add( 'redsys', '/***************************************************************/' );
							$this->log->add( 'redsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
							$this->log->add( 'redsys', '/***************************************************************/' );
							$this->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
							$this->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: C' );
							$this->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
						}
					}
				} else {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_c );
					if ( $psd2 ) {
						$txnid = WCRed()->get_txnid( $customer_token_c_id );
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
						$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_c );
						$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
						if ( $psd2 ) {
							$this->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
							$this->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: C' );
							$this->log->add( 'redsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
						}
					}
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
			$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
		} else {
			$mi_obj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'D' );
			$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
		}

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
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
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'redsys', 'set_transient: ' . get_transient( 'redsys_signature_' . sanitize_text_field( $order_id_set ) ) );
			$this->log->add( 'redsys', 'DS_MERCHANT_AMOUNT: ' . $redsys_data_send['order_total_sign'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_ORDER: ' . $redsys_data_send['transaction_id2'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTCODE: ' . $redsys_data_send['customer'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_CURRENCY: ' . $redsys_data_send['currency'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $redsys_data_send['transaction_type'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_TERMINAL: ' . $redsys_data_send['DSMerchantTerminal'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTURL: ' . $redsys_data_send['final_notify_url'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_URLOK: ' . $redsys_data_send['url_ok'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_URLKO: ' . $redsys_data_send['returnfromredsys'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $redsys_data_send['gatewaylanguage'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $redsys_data_send['product_description'] );
			$this->log->add( 'redsys', 'DS_MERCHANT_MERCHANTNAME: ' . $redsys_data_send['merchant_name'] );
			$this->log->add( 'redsys', 'SECRETSHA256: ' . $secretsha256 );
			$this->log->add( 'redsys', 'DS_MERCHANT_TITULAR: ' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );
			$this->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
			if ( ! empty( $customer_token ) && ( 'yes' === $this->usetokens ) ) {
				$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token );
				$this->log->add( 'redsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
				$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			} elseif ( empty( $customer_token ) && ( 'yes' === $this->usetokens ) ) {
				$this->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED (Se está pidiendo el token en esta transacción)' );
				if ( ! empty( $this->merchantgroup ) ) {
					$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $this->merchantgroup );
				} else {
					$this->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
				}
				$this->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			}
			if ( 'T' !== $this->redsysdirectdeb ) {
				$this->log->add( 'redsys', 'DS_MERCHANT_PAYMETHODS: ' . $this->redsysdirectdeb . ' ( T = Pago con Tarjeta, D = Domiciliación, TD = Tarjeta + Domiciliación )' );
			}
			$this->log->add( 'redsys', ' ' );
		}
		return $redsys_args;
	}
	/**
	 * Get Redsys Args for passing to TPV
	 *
	 * @param  WC_Order $order Order object.
	 * @return array
	 */
	public static function get_redsys_args_p( $order ) {

		$redsys = new WC_Gateway_Redsys();

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referer  = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), PHP_URL_HOST );
			$site_url = wp_parse_url( get_site_url(), PHP_URL_HOST );

			if ( $referer !== $site_url ) {
				if ( 'yes' === $redsys->debug ) {
					$redsys->log->add( 'redsys', ' ' );
					$redsys->log->add( 'redsys', '/****************************/' );
					$redsys->log->add( 'redsys', '      Wrong referer         ' );
					$redsys->log->add( 'redsys', '/****************************/' );
					$redsys->log->add( 'redsys', ' ' );
					$redsys->log->add( 'redsys', ' ' );
					$redsys->log->add( 'redsys', '/****************************/' );
					$redsys->log->add( 'redsys', '   Redirecting to Checkout    ' );
					$redsys->log->add( 'redsys', '/****************************/' );
					$redsys->log->add( 'redsys', ' ' );
				}
				$checkout_url = wc_get_checkout_url();
				wp_safe_redirect( $checkout_url, 301 );
				exit();
			}
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', '/****************************/' );
				$redsys->log->add( 'redsys', '      Correct referer         ' );
				$redsys->log->add( 'redsys', '/****************************/' );
				$redsys->log->add( 'redsys', ' ' );
			}
		}

		$customer_token   = '';
		$customer_token_c = '';
		$customer_token_r = '';

		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', ' ' );
			$redsys->log->add( 'redsys', '/****************************/' );
			$redsys->log->add( 'redsys', '     Making redsys_args_p      ' );
			$redsys->log->add( 'redsys', '/****************************/' );
			$redsys->log->add( 'redsys', ' ' );
		}
		$order_id         = $order->get_id();
		$currency_codes   = WCRed()->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$save_token       = get_transient( $order_id . '_redsys_save_token' );
		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', ' ' );
			$redsys->log->add( 'redsys', '$order_id: ' . $order_id );
			$redsys->log->add( 'redsys', '$transaction_id2: ' . $transaction_id2 );
			$redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$redsys->log->add( 'redsys', '$save_token: ' . $save_token );
			$redsys->log->add( 'redsys', ' ' );
		}
		if ( WCRed()->order_needs_preauth( $order_id ) ) {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', 'IS Preauthorization' );
			}
			$transaction_type = '1';
		} else {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', 'IS NOT Preauthorization' );

			}
			$transaction_type = '0';
		}

		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'Using WPML' );
				$redsys->log->add( 'redsys', 'The ICL_LANGUAGE_CODE is: ' . ICL_LANGUAGE_CODE );
				$redsys->log->add( 'redsys', ' ' );
			}
		} elseif ( $redsys->redsyslanguage ) {
			$gatewaylanguage = $redsys->redsyslanguage;
		} else {
			$gatewaylanguage = '001';
		}

		if ( 'yes' === $redsys->not_use_https ) {
			$final_notify_url = $redsys->notify_url_not_https;
		} else {
			$final_notify_url = $redsys->notify_url;
		}

		if ( $redsys->wooredsysurlko ) {
			if ( 'returncancel' === $redsys->wooredsysurlko ) {
				if ( 'iframe' === $redsys->usebrowserreceipt ) {
					$returnfromredsys = $final_notify_url . '&order_id=' . $order->get_id() . '&redsys-step=cancel';
				} else {
					$returnfromredsys = $order->get_cancel_order_url();
				}
			} else {
				if ( 'iframe' === $redsys->usebrowserreceipt ) {
					$returnfromredsys = $final_notify_url . '&order_id=' . $order->get_id() . '&redsys-step=cancel';
				} else {
					$returnfromredsys = wc_get_checkout_url();
				}
			}
		} else {
			if ( 'iframe' === $redsys->usebrowserreceipt ) {
				$returnfromredsys = $final_notify_url . '&order_id=' . $order->get_id() . '&redsys-step=cancel';
			} else {
				$returnfromredsys = $order->get_cancel_order_url();
			}
		}
		if ( 'yes' === $redsys->useterminal2 ) {
			$toamount  = number_format( $redsys->toamount, 2, '', '' );
			$terminal  = $redsys->terminal;
			$terminal2 = $redsys->terminal2;
			if ( $order_total_sign <= $toamount ) {
				$ds_merchant_terminal = $terminal2;
			} else {
				$ds_merchant_terminal = $terminal;
			}
		} else {
			$ds_merchant_terminal = $redsys->terminal;
		}

		$psd2 = WCPSD2()->get_acctinfo( $order );
		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', '$psd2: ' . $psd2 );
		}
		$customer_token_r = '';
		$customer_token_c = '';
		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
			$redsys->log->add( 'redsys', '$customer_token_r: ' . $customer_token_r );
			$redsys->log->add( 'redsys', '$customer_token_c: ' . $customer_token_c );
		}

		$customer_token = get_transient( $order_id . '_redsys_use_token' );
		$token_type     = get_transient( $order_id . '_redsys_token_type' );

		if ( 'no' !== $customer_token && 'R' === $token_type ) {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$redsys->log->add( 'redsys', '$customer_token_r: ' . $customer_token_r );
			}
			$customer_token_r = $customer_token;
		} else {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$redsys->log->add( 'redsys', '$customer_token_r: FALSE' );
			}
			$customer_token_r = false;
		}
		if ( 'no' !== $customer_token && 'C' === $token_type ) {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$redsys->log->add( 'redsys', '$customer_token_c: ' . $customer_token_r );
			}
			$customer_token_c = $customer_token;
		} else {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$redsys->log->add( 'redsys', '$customer_token_c: FALSE' );
			}
			$customer_token_c = false;
		}

		$redsys_data_send = array();
		$currency         = $currency_codes[ get_woocommerce_currency() ];
		$user_id          = $order->get_user_id();
		$secretsha256     = $redsys->get_redsys_sha256( $user_id );
		$customer         = $redsys->customer;
		$url_ok           = add_query_arg(
			array(
				'redsys-iframe'  => 'yes',
				'utm_nooverride' => '1',
			),
			$redsys->get_return_url( $order )
		);
		$url_ok_transient = add_query_arg( 'utm_nooverride', '1', $redsys->get_return_url( $order ) );
		set_transient( $order_id . '_redsys_url_ok', $url_ok_transient, 300 * MINUTE_IN_SECONDS );
		$product_description = WCRed()->product_description( $order, 'redsys' );
		$merchant_name       = $redsys->commercename;

		$redsys_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => (string) $transaction_id2,
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

			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
				$redsys->log->add( 'redsys', ' ' );
			}
		}

		$secretsha256     = $redsys_data_send['secretsha256'];
		$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );
		$order_total_sign = $redsys_data_send['order_total_sign'];

		// redsys Args.
		$mi_obj = new WooRedsysAPI();
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
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $redsys_data_send['merchant_name'] );
		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', 'get_redsys_args_p: fase 1' );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_AMOUNT: ' . $redsys_data_send['order_total_sign'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_ORDER: ' . $redsys_data_send['transaction_id2'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTCODE: ' . $redsys_data_send['customer'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_CURRENCY: ' . $redsys_data_send['currency'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $redsys_data_send['transaction_type'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_TERMINAL: ' . $redsys_data_send['DSMerchantTerminal'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTURL: ' . $redsys_data_send['final_notify_url'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_URLOK: ' . $redsys_data_send['url_ok'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_URLKO: ' . $redsys_data_send['returnfromredsys'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $redsys_data_send['gatewaylanguage'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $redsys_data_send['product_description'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTNAME: ' . $redsys_data_send['merchant_name'] );
			$redsys->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $redsys->traactive && $order_total_sign > 3000 && $order_total_sign <= ( 100 * (int) $redsys->traamount ) ) {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'Using TRA' );
				$redsys->log->add( 'redsys', ' ' );
			}
			$mi_obj->setParameter( 'DS_MERCHANT_EXCEP_SCA', 'TRA' );
		} else {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'NOT Using TRA' );
				$redsys->log->add( 'redsys', ' ' );
			}
		}
		if ( $order_total_sign <= 3000 && 'yes' === $redsys->lwvactive ) {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'Using LWV' );
				$redsys->log->add( 'redsys', ' ' );
			}
			$mi_obj->setParameter( 'DS_MERCHANT_EXCEP_SCA', 'LWV' );
		} else {
			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'redsys', ' ' );
				$redsys->log->add( 'redsys', 'NOT Using LWV' );
				$redsys->log->add( 'redsys', ' ' );
			}
		}
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );

		// [T = Pago con Tarjeta + iupay , R = Pago por Transferencia, D = Domiciliacion, C = Sólo Tarjeta (mostrará sólo el formulario para datos de tarjeta)] por defecto es T
		if ( 'T' === $redsys->redsysdirectdeb || empty( $redsys->redsysdirectdeb ) ) { // No se puede ofrecer domiciliación y tarjeta con pago por referencia a la vez.
			$order_id = $order->get_id();
			if ( WCRed()->order_contains_subscription( $order_id ) ) {
				if ( WCRed()->order_contains_subscription( $order_id ) && 'yes' !== $redsys->subsusetokensdisable ) {
					if ( ! $customer_token_r ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
						if ( ! empty( $redsys->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
						$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
						$ds_merchant_data = 'no';
						if ( 'yes' === $redsys->debug ) {
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: 0' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
							if ( ! empty( $redsys->merchantgroup ) ) {
								$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $redsys->merchantgroup );
							} else {
								$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
							}
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							$redsys->log->add( 'redsys', '/***************************************************************/' );
							$redsys->log->add( 'redsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
							$redsys->log->add( 'redsys', '/***************************************************************/' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_INI: S' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: R' );
							$redsys->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
						}
					} else {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_r );
						$txnid = WCRed()->get_txnid( $customer_token_r_id );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TXNID', $txnid );
						if ( ! empty( $redsys->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
						$ds_merchant_data           = 'yes';
						$ds_merchant_direct_payment = 'false';
						if ( 'yes' === $redsys->debug ) {
							$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_r );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: R' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
						}
					}
				}
			} elseif ( 'yes' === $redsys->usetokens ) {
				// Pago con 1 clic activo.
				if ( 'yes' === $redsys->psd2 ) {
					// PSD2 activo.
					if ( ! $customer_token_c && 'yes' === $save_token ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
						if ( ! empty( $redsys->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'C' );
						$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
						$ds_merchant_data = 'no';
						if ( 'yes' === $redsys->debug ) {
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: 0' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED' );
							if ( ! empty( $redsys->merchantgroup ) ) {
								$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $redsys->merchantgroup );
							} else {
								$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
							}
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							$redsys->log->add( 'redsys', '/***************************************************************/' );
							$redsys->log->add( 'redsys', ' PSD2 Activado. Enviamos todo lo necesario según nueva normativa ' );
							$redsys->log->add( 'redsys', '/***************************************************************/' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: C' );
							$redsys->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
						}
					} elseif ( $customer_token_c ) {
						$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
						$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token_c );
						$txnid = WCRed()->get_txnid( $customer_token_c_id );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'N' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'C' );
						$mi_obj->setParameter( 'DS_MERCHANT_COF_TXNID', $txnid );
						if ( ! empty( $redsys->merchantgroup ) ) {
							$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
						}
						$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
						$ds_merchant_data           = 'yes';
						$ds_merchant_direct_payment = 'false';
						if ( 'yes' === $redsys->debug ) {
							$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token_c );
							$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_INI: N' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TYPE: C' );
							$redsys->log->add( 'redsys', 'DS_MERCHANT_COF_TXNID: ' . $txnid );
						}
					}
				} elseif ( empty( $customer_token ) ) {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );
					if ( ! empty( $redsys->merchantgroup ) ) {
						$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
					}
					$ds_merchant_data = 'no';
				} else {
					$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '1' );
					$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token );
				}
				if ( ! empty( $redsys->merchantgroup ) ) {
					$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $redsys->merchantgroup );
				}
				$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'false' ); // TODO: Añadir una lógica para que el administrador pueda seleccionar si lo quiere en true o en fasle. True en todos trae probelmas por configuraciones en Redsys.
				$ds_merchant_data           = 'yes';
				$ds_merchant_direct_payment = 'false';
			}
		} elseif ( 'TD' === $redsys->redsysdirectdeb ) {
			$mi_obj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'TD' );
			$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
		} else {
			$mi_obj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'D' );
			$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', $psd2 );
		}

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
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
		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'redsys', ' ' );
			$redsys->log->add( 'redsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$redsys->log->add( 'redsys', 'Helping to understand the encrypted code: ' );
			$redsys->log->add( 'redsys', 'set_transient: ' . get_transient( 'redsys_signature_' . sanitize_text_field( $order_id_set ) ) );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_AMOUNT: ' . $redsys_data_send['order_total_sign'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_ORDER: ' . $redsys_data_send['transaction_id2'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTCODE: ' . $redsys_data_send['customer'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_CURRENCY: ' . $redsys_data_send['currency'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $redsys_data_send['transaction_type'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_TERMINAL: ' . $redsys_data_send['DSMerchantTerminal'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTURL: ' . $redsys_data_send['final_notify_url'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_URLOK: ' . $redsys_data_send['url_ok'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_URLKO: ' . $redsys_data_send['returnfromredsys'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $redsys_data_send['gatewaylanguage'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $redsys_data_send['product_description'] );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_MERCHANTNAME: ' . $redsys_data_send['merchant_name'] );
			$redsys->log->add( 'redsys', 'SECRETSHA256: ' . $secretsha256 );
			$redsys->log->add( 'redsys', 'DS_MERCHANT_TITULAR: ' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );
			$redsys->log->add( 'redsys', 'Ds_Merchant_EMV3DS: ' . $psd2 );
			if ( ! empty( $customer_token ) && ( 'yes' === $redsys->usetokens ) ) {
				$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: ' . $customer_token );
				$redsys->log->add( 'redsys', 'DS_MERCHANT_DIRECTPAYMENT: ' . $ds_merchant_direct_payment );
				$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			} elseif ( empty( $customer_token ) && ( 'yes' === $redsys->usetokens ) ) {
				$redsys->log->add( 'redsys', 'DS_MERCHANT_IDENTIFIER: REQUIRED (Se está pidiendo el token en esta transacción)' );
				if ( ! empty( $redsys->merchantgroup ) ) {
					$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: ' . $redsys->merchantgroup );
				} else {
					$redsys->log->add( 'redsys', 'DS_MERCHANT_GROUP: There is no DS_MERCHANT_GROUP defined' );
				}
				$redsys->log->add( 'redsys', 'Ds_Merchant_MerchantData: ' . $ds_merchant_data );
			}
			if ( 'T' !== $redsys->redsysdirectdeb ) {
				$redsys->log->add( 'redsys', 'DS_MERCHANT_PAYMETHODS: ' . $redsys->redsysdirectdeb . ' ( T = Pago con Tarjeta, D = Domiciliación, TD = Tarjeta + Domiciliación )' );
			}
			$redsys->log->add( 'redsys', ' ' );
		}
		return $redsys_args;
	}
	/**
	 * Get redsys Args for passing Add Method.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function get_redsys_args_add_method( $order_id ) {

		$customer_token   = '';
		$customer_token_c = '';
		$customer_token_r = '';
		$log              = new WC_Logger();
		$redsys           = new WC_Gateway_Redsys();

		if ( 'yes' === $redsys->debug ) {
			$log->add( 'redsys', ' ' );
			$log->add( 'redsys', '/****************************/' );
			$log->add( 'redsys', '     Making redsys_args       ' );
			$log->add( 'redsys', '/****************************/' );
			$log->add( 'redsys', ' ' );
		}
		$currency_codes   = WCRed()->get_currencies();
		$transaction_id2  = $order_id;
		$order_total_sign = '0';
		$token_type       = get_transient( $order_id . '_get_method' );
		if ( 'yes' === $redsys->debug ) {
			$log->add( 'redsys', ' ' );
			$log->add( 'redsys', '$order_id: ' . $order_id );
			$log->add( 'redsys', '$transaction_id2: ' . $transaction_id2 );
			$log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$log->add( 'redsys', ' ' );
		}
		$transaction_type = '0';

		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
			if ( 'yes' === $redsys->debug ) {
				$log->add( 'redsys', ' ' );
				$log->add( 'redsys', 'Using WPML' );
				$log->add( 'redsys', 'The ICL_LANGUAGE_CODE is: ' . ICL_LANGUAGE_CODE );
				$log->add( 'redsys', ' ' );
			}
		} elseif ( $redsys->redsyslanguage ) {
			$gatewaylanguage = $redsys->redsyslanguage;
		} else {
			$gatewaylanguage = '001';
		}

		$returnfromredsys     = wc_get_endpoint_url( 'add-payment-method' );
		$ds_merchant_terminal = $redsys->terminal;

		if ( 'yes' === $redsys->not_use_https ) {
			$final_notify_url = $redsys->notify_url_not_https;
		} else {
			$final_notify_url = $redsys->notify_url;
		}
		$redsys_data_send    = array();
		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$user_id             = get_transient( $order_id );
		$secretsha256        = $redsys->get_redsys_sha256( $user_id );
		$customer            = $redsys->customer;
		$url_ok              = wc_get_endpoint_url( 'payment-methods', '', get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
		$product_description = __( 'Adding Payment Method', 'woocommerc-redsys' );
		$merchant_name       = $redsys->commercename;

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

			if ( 'yes' === $redsys->debug ) {
				$log->add( 'redsys', ' ' );
				$log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
				$log->add( 'redsys', ' ' );
			}
		}

		$secretsha256 = $redsys_data_send['secretsha256'];

		// redsys Args.
		$mi_obj = new WooRedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $redsys_data_send['order_total_sign'] );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $redsys_data_send['transaction_id2'] );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $redsys_data_send['customer'] );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $redsys_data_send['currency'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $redsys_data_send['transaction_type'] );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $redsys_data_send['DSMerchantTerminal'] );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $redsys_data_send['final_notify_url'] );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', $redsys_data_send['url_ok'] );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $redsys_data_send['url_ok'] );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $redsys_data_send['gatewaylanguage'] );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', $redsys_data_send['product_description'] );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $redsys_data_send['merchant_name'] );
		// $mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchant_name . ' ' . WCRed()->clean_data( $merchant_lastnme ) );
		$mi_obj->setParameter( 'Ds_Merchant_MerchantData', '0' );
		$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', 'REQUIRED' );

		if ( 'tokenr' === $token_type ) {
			$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
			$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'R' );
		} else {
			$mi_obj->setParameter( 'DS_MERCHANT_COF_INI', 'S' );
			$mi_obj->setParameter( 'DS_MERCHANT_COF_TYPE', 'C' );
		}
		$mi_obj->setParameter( 'Ds_Merchant_EMV3DS', '{}' );
		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
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
		return $redsys_args;
	}
	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 */
	public function redsys_process_payment_token( $order_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/*********************************/' );
			$this->log->add( 'redsys', '  Processing token 1 click insite  ' );
			$this->log->add( 'redsys', '/*********************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$order_total_sign     = '';
		$transaction_id2      = '';
		$transaction_type     = '';
		$ds_merchant_terminal = '';
		$final_notify_url     = '';
		$returnfromredsys     = '';
		$gatewaylanguage      = '';
		$currency             = '';
		$secretsha256         = '';
		$customer             = '';
		$url_ok               = '';
		$product_description  = '';
		$merchant_name        = '';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '  Generating Tokenized call   ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', ' ' );
		}
		$type       = 'ws';
		$order      = WCRed()->get_order( $order_id );
		$user_id    = $order->get_user_id();
		$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
			$this->log->add( 'redsys', ' ' );
		}
		$currency_codes   = WCRed()->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', ' ' );
		}

		if ( WCRed()->order_needs_preauth( $order_id ) ) {
			$transaction_type = '1';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Is a pre-authorization' );
				$this->log->add( 'redsys', ' ' );
			}
		} else {
			$transaction_type = '0';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Is a payment' );
				$this->log->add( 'redsys', ' ' );
			}
		}

		$gatewaylanguage = $this->redsyslanguage;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
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
		$customer_token = WCRed()->get_redsys_users_token();

		$redsys_data_send = array();

		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$user_id             = $order->get_user_id();
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
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
				$this->log->add( 'redsys', ' ' );
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
		$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', '$order: ' . $order );
			$this->log->add( 'redsys', '$customer: ' . $customer );
			$this->log->add( 'redsys', '$currency: ' . $currency );
			$this->log->add( 'redsys', '$transaction_type: 0' );
			$this->log->add( 'redsys', '$terminal: ' . $terminal );
			$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
			$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
			$this->log->add( 'redsys', ' ' );
		}

		$mi_obj = new WooRedsysAPIWS();

		if ( ! empty( $this->merchantgroup ) ) {
			$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
		} else {
			$ds_merchant_group = '';
		}

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $order . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= $ds_merchant_group;
		$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
		$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>true</DS_MERCHANT_DIRECTPAYMENT>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
		$datos_entrada .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
		$datos_entrada .= '</DATOSENTRADA>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '          The call            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', $datos_entrada );
			$this->log->add( 'redsys', ' ' );
		}

		$xml  = '<REQUEST>';
		$xml .= $datos_entrada;
		$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '          The XML 1            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', $xml );
			$this->log->add( 'redsys', ' ' );
		}

		$cliente    = new SoapClient( $redsys_adr ); // Entorno de prueba.
		$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

		if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuesta = (int) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( ( $respuesta >= 0 ) && ( $respuesta <= 99 ) ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'Response: Ok > ' . $respuesta );
						$this->log->add( 'redsys', ' ' );
					}
					return $url_ok;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'Response: Error > ' . $respuesta );
						$this->log->add( 'redsys', ' ' );
					}
					$message = __( '⚠️ There was a problem, the problem was: ', 'woocommerce-redsys' ) . WCRed()->get_error( $respuesta );
					WCRed()->push( $message );
					return false;
				}
			}
		}
	}
	/**
	 * Generate the redsys form
	 *
	 * @param   int $order_id Order ID.
	 * @return  string        Form fields
	 */
	public function generate_redsys_form_browser( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/************************************/' );
			$this->log->add( 'redsys', '   Generating Redsys Form Browser     ' );
			$this->log->add( 'redsys', '/*************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr      = $this->get_redsys_url_gateway( $user_id );
		$redsys_args     = $this->get_redsys_args( $order );
		$form_inputs     = array();
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', 'function generate_redsys_form_browser()' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
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
	 * @param mixed $order_id Order ID.
	 * @return string
	 */
	public function generate_redsys_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '   Generating Redsys Form     ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		$redsys_adr      = $this->get_redsys_url_gateway( $user_id );
		$redsys_args     = $this->get_redsys_args( $order );
		$form_inputs     = array();
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', 'function generate_redsys_form()' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
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
	 * @param mixed $order_id Order ID.
	 * @return string
	 */
	public function generate_redsys_subscription_form_browser( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***********************************************/' );
			$this->log->add( 'redsys', '   Generating Redsys Subscription Form Browser   ' );
			$this->log->add( 'redsys', '/***********************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', 'function generate_redsys_subscription_form()' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', ' ' );
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
	 * Generate Subscriptions Form.
	 *
	 * @param  int $order_id Order ID.
	 * @return string
	 */
	public function generate_redsys_subscription_form( $order_id ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', '   Generating Redsys Subscription Form   ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$order           = WCRed()->get_order( $order_id );
		$user_id         = $order->get_user_id();
		$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', 'function generate_redsys_subscription_form()' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', ' ' );
		}

		$redsys_adr  = $this->get_redsys_url_gateway( $user_id );
		$redsys_args = $this->get_redsys_args( $order );
		$form_inputs = array();
		foreach ( $redsys_args as $key => $value ) {
			$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
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
	 * Add gateway to support subscriptions.
	 *
	 * @param array $subscription_gateways
	 * @return array
	 */
	public static function add_subscription_supports( $subscription_gateways ) {
		$subscription_gateways[] = 'redsys';
		return $subscription_gateways;
	}
	/**
	 * Process the Subscription payment and return the result.
	 *
	 * @param  int $amount_to_charge Amount to charge.
	 * @param  int $renewal_order    Renewal ID.
	 */
	public function doing_scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$order_id    = $renewal_order->get_id();
		$redsys_done = WCRed()->get_order_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '       Once upon a time       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', '       Payment is complete EXIT          ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
			}
			return;
		} else {
			$order  = $renewal_order;
			$amount = $amount_to_charge;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/**********************************************/' );
				$this->log->add( 'redsys', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'redsys', '/**********************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', '   scheduled charge Amount: ' . $amount );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			$order_total_sign     = '';
			$transaction_id2      = '';
			$transaction_type     = '';
			$ds_merchant_terminal = '';
			$final_notify_url     = '';
			$returnfromredsys     = '';
			$gatewaylanguage      = '';
			$currency             = '';
			$secretsha256         = '';
			$customer             = '';
			$url_ok               = '';
			$product_description  = '';
			$merchant_name        = '';

			$order_id = $order->get_id();
			$user_id  = $order->get_user_id();
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '  Generating Tokenized call   ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_id: ' . $order_id );
				$this->log->add( 'redsys', '$user_id: ' . $user_id );
				$this->log->add( 'redsys', ' ' );
			}
			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'redsys', ' ' );
			}
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'redsys', ' ' );
			}

			$transaction_type = '0';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			}

			$ds_merchant_terminal = $this->terminal;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
			}

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
			}
			$customer_token = WCRed()->get_users_token_bulk( $user_id, 'R' );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$customer_token: ' . $customer_token );
			}
			$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$customer_token_id: ' . $customer_token_id );
			}
			$txnid = WCRed()->get_txnid( $customer_token_id );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$txnid: ' . $txnid );
			}
			if ( ! $customer_token || empty( $customer_token ) || '' === trim( $customer_token ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$customer_token: NO Token or expired Credit Card' );
					$this->log->add( 'redsys', ' ' );
				}
				$url = 'URL: ' . WCRed()->get_order_edit_url( $order_id );
				$order->add_order_note( esc_html__( 'No credit card or expired', 'woocommerce-redsys' ) );
				$message = __( '⚠️ No credit card or expired', 'woocommerce-redsys' );
				WCRed()->add_subscription_note( $message, $order_id );
				WCRed()->push( $message . ' ' . $url );
				$renewal_order->update_status( 'failed' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'redsys', '$txnid: ' . $txnid );
				$this->log->add( 'redsys', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'redsys', ' ' );
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
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'redsys', ' ' );
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
			$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
			$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'redsys', '$order: ' . $orderid2 );
				$this->log->add( 'redsys', '$customer: ' . $customer );
				$this->log->add( 'redsys', '$currency: ' . $currency );
				$this->log->add( 'redsys', '$transaction_type: 0' );
				$this->log->add( 'redsys', '$terminal: ' . $terminal );
				$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
				$this->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'redsys', ' ' );
			}

			$mi_obj = new WooRedsysAPIWS();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}

			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
			// $datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The call            ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $datos_entrada );
				$this->log->add( 'redsys', ' ' );
			}

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML 2            ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'iniciaPeticion 1: ' . $xml );
				$this->log->add( 'redsys', ' ' );
			}
			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $responsews->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( isset( $xml_retorno->INFOTARJETA->Ds_EMV3DS ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' Llega $xml_retorno->INFOTARJETA->Ds_EMV3DS' );
					$this->log->add( 'redsys', '/****************************/' );
				}
				$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_emv3ds                = json_decode( $ds_emv3ds_json ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
					$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/**********************************************/' );
					$this->log->add( 'redsys', ' NO llega $xml_retorno->INFOTARJETA->Ds_EMV3DS' );
					$this->log->add( 'redsys', '/**********************************************/' );
				}
				$ds_emv3ds_json           = '';
				$ds_emv3ds                = array( 'none' );
				$protocol_version         = '';
				$three_ds_server_trans_id = '';
				$three_ds_info            = '';
			}

			if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  $protocol_version = ' . $protocol_version );
					$this->log->add( 'redsys', '/****************************/' );
				}

				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
				$datos_entrada .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
				// $datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
				$datos_entrada .= '</DATOSENTRADA>';
				$xml            = '<REQUEST>';
				$xml           .= $datos_entrada;
				$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml           .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML  3           ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $xml );
					$this->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
				}
				if ( $authorisationcode ) {
					$data = array();
					$data['_redsys_done'] = 'yes';
					if ( 'yes' !== $this->disablesubscrippaid ) {
						$order->payment_complete();
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 1' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      Saving Order Meta       ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}

					if ( ! empty( $redsys_order ) ) {
						$data['_payment_order_number_redsys'] = $redsys_order;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						$data['_payment_terminal_redsys'] = $terminal;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						$data['_authorisation_code_redsys'] = $authorisationcode;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						$data['_corruncy_code_redsys'] = $currency_code;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						$data['_redsys_secretsha256'] = $secretsha256;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'redsys', '/******************************************/' );
					}
					WCRed()->update_order_meta( $order->get_id(), $data );
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					// TO-DO: Enviar un correo con el problema al administrador.
					if ( ! WCRed()->is_paid( $order->get_id() ) ) {
						$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
						$renewal_order->update_status( 'failed' );
					}
					$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
					do_action( 'redsys_post_payment_error', $order->get_id(), $error );
				}
			} else {
				$protocol_version = '1.0.2';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  $protocol_version = "1.0.2" ' );
					$this->log->add( 'redsys', '/****************************/' );
				}

				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= $ds_merchant_group;
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
				$datos_entrada .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
				$datos_entrada .= '</DATOSENTRADA>';
				$xml            = '<REQUEST>';
				$xml           .= $datos_entrada;
				$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml           .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML 4            ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $xml );
					$this->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}
				$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( $authorisationcode ) {
					$data = array();
					$data['_redsys_done'] =  'yes';
					if ( 'yes' !== $this->disablesubscrippaid ) {
						$order->payment_complete();
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 2' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      Saving Order Meta       ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );

					if ( ! empty( $redsys_order ) ) {
						$data['_payment_order_number_redsys'] =  $redsys_order;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						$data['_payment_terminal_redsys'] =  $terminal;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						$data['_authorisation_code_redsys'] =  $authorisationcode;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						$data['_corruncy_code_redsys'] =  $currency_code;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						$data['_redsys_secretsha256'] =  $secretsha256;
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					WCRed()->update_order_meta( $order->get_id(), $data );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'redsys', '/******************************************/' );
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					if ( ! WCRed()->is_paid( $order->get_id() ) ) {
						$url   = 'URL: ' . WCRed()->get_order_edit_url( $order_id );
						$error = WCRed()->get_error( $response );
						$order->add_order_note( __( 'There was and error. The error was: ', 'woocommerce-redsys' ) . $error );
						$message = __( '⚠️ Subscription Payment failed. Error: ', 'woocommerce-redsys' ) . $error . '. URL: ' . WCRed()->get_order_edit_url( $order_id );
						WCRed()->add_subscription_note( $message, $order_id );
						WCRed()->push( $message . ' ' . $url );
						$renewal_order->update_status( 'failed' );
						do_action( 'redsys_post_payment_error', $order->get_id(), $error );
					}
				}
			}
		}
	}
	/**
	 * Get Redsys URL Gateway by User ID
	 *
	 * @param int $user_id User ID.
	 */
	public function get_redsys_url_gateway_ws( $user_id = false ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          URL Test        ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			$url = $this->testurlws;
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          URL Test RD         ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$url = $this->testurlws;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          URL Live RD         ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$url = $this->liveurlws;
			}
		}
		return $url;
	}
	/**
	 * Renew SUMO Subscriptions
	 *
	 * @param bool $bool True or false.
	 * @param int  $subscription_id Subscription ID.
	 * @param obj  $renewal_order Renewal Order.
	 * @param bool $retry True or false.
	 *
	 * @return bool
	 */
	public function renew_sumo_subscription( $bool, $subscription_id, $renewal_order, $retry = false ) {
		$user_id          = sumo_get_subscription_payment( $subscription_id, 'payment_key' );
		$order_id         = $renewal_order->get_id();
		$amount_to_charge = $renewal_order->get_total();
		$redsys_done      = WCRed()->get_order_meta( $order_id, '_redsys_done', true );

		if ( $user_id ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '       Once upon a time       ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/*******************************************/' );
				$this->log->add( 'redsys', '  Doing SUMO scheduled_subscription_payment   ' );
				$this->log->add( 'redsys', '/*******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', '      $order_id = ' . $order_id . '      ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( 'yes' === $redsys_done ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/***************************************/' );
					$this->log->add( 'redsys', '       Payment is complete EXIT          ' );
					$this->log->add( 'redsys', '/***************************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
				}
				return;
			} else {

				$order  = $renewal_order;
				$amount = $amount_to_charge;

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/**********************************************/' );
					$this->log->add( 'redsys', '  Function  doing_scheduled_subscription_payment' );
					$this->log->add( 'redsys', '/**********************************************/' );
					$this->log->add( 'redsys', ' ' );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/***************************************/' );
					$this->log->add( 'redsys', '   scheduled charge Amount: ' . $amount );
					$this->log->add( 'redsys', '/***************************************/' );
					$this->log->add( 'redsys', ' ' );
				}

				$order_total_sign     = '';
				$transaction_id2      = '';
				$transaction_type     = '';
				$ds_merchant_terminal = '';
				$final_notify_url     = '';
				$returnfromredsys     = '';
				$gatewaylanguage      = '';
				$currency             = '';
				$secretsha256         = '';
				$customer             = '';
				$url_ok               = '';
				$product_description  = '';
				$merchant_name        = '';

				$order_id = $order->get_id();
				$user_id  = $order->get_user_id();

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '  Generating Tokenized call   ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$order_id: ' . $order_id );
					$this->log->add( 'redsys', '$user_id: ' . $user_id );
					$this->log->add( 'redsys', ' ' );
				}

				$type       = 'ws';
				$order      = WCRed()->get_order( $order_id );
				$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
					$this->log->add( 'redsys', ' ' );
				}
				$currency_codes = WCRed()->get_currencies();

				$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
				$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
					$this->log->add( 'redsys', ' ' );
				}

				$transaction_type = '0';

				$gatewaylanguage = $this->redsyslanguage;

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
				}
				$ds_merchant_terminal = $this->terminal;

				if ( 'yes' === $this->not_use_https ) {
					$final_notify_url = $this->notify_url_not_https;
				} else {
					$final_notify_url = $this->notify_url;
				}
				$customer_token    = WCRed()->get_users_token_bulk( $user_id, 'R' );
				$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
				$txnid             = WCRed()->get_txnid( $customer_token_id );

				if ( ! $customer_token ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'NO Customer Token' );
						$this->log->add( 'redsys', ' ' );
					}
					if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
						ywsbs_register_failed_payment( $renewal_order, 'Error: No user token' );
					}
					return false;
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$customer_token: ' . $customer_token );
					$this->log->add( 'redsys', ' ' );
				}

				$redsys_data_send = array();

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Order Currency: ' . get_woocommerce_currency() );
					$this->log->add( 'redsys', ' ' );
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
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
						$this->log->add( 'redsys', ' ' );
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
				$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
				$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
					$this->log->add( 'redsys', '$order: ' . $orderid2 );
					$this->log->add( 'redsys', '$customer: ' . $customer );
					$this->log->add( 'redsys', '$currency: ' . $currency );
					$this->log->add( 'redsys', '$transaction_type: 0' );
					$this->log->add( 'redsys', '$terminal: ' . $terminal );
					$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
					$this->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
					$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
					$this->log->add( 'redsys', ' ' );
				}

				$mi_obj = new WooRedsysAPIWS();
				if ( ! empty( $this->merchantgroup ) ) {
					$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
				} else {
					$ds_merchant_group = '';
				}
				$datos_usuario  = array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocol_version,
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
				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
				$datos_entrada .= '</DATOSENTRADA>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The call            ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $datos_entrada );
					$this->log->add( 'redsys', ' ' );
				}

				$xml  = '<REQUEST>';
				$xml .= $datos_entrada;
				$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML 5            ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'iniciaPeticion 2' . $xml );
					$this->log->add( 'redsys', ' ' );
				}
					$cliente    = new SoapClient( $redsys_adr );
					$responsews = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$respuesta   = (string) $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				} // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

					$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ds_emv3ds                = json_decode( $ds_emv3ds_json ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
					$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				}

				if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

					$datos_entrada  = '<DATOSENTRADA>';
					$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
					$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
					$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					$datos_entrada .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
					$datos_entrada .= '</DATOSENTRADA>';
					$xml            = '<REQUEST>';
					$xml           .= $datos_entrada;
					$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
					$xml           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          The XML  6          ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', $xml );
						$this->log->add( 'redsys', ' ' );
					}
					$cliente    = new SoapClient( $redsys_adr );
					$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

					if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
					}
					if ( $authorisationcode ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'payment_complete 3' );
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', '      Saving Order Meta       ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', ' ' );
						}
						if ( ! empty( $redsys_order ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '/******************************************/' );
							$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'redsys', '/******************************************/' );
						}
						do_action( 'redsys_post_payment_complete', $order->get_id() );
						if ( function_exists( 'sumosubs_set_transaction_id' ) ) {
							sumosubs_set_transaction_id( $renewal_order->get_id(), $transaction_id, true );
						}
						return true;
					} else {
						if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
							$error = WCRed()->get_error( $response );
							$order->add_order_note( __( 'There was an error:', 'woocommerce-redsys' ) . $error );
							$message = __( '⚠️ Subscription Payment failed. Error: ', 'woocommerce-redsys' ) . $error . ' URL: ' . WCRed()->get_order_edit_url( $order->get_id() );
							WCRed()->push( $message );
							do_action( 'redsys_post_payment_error', $order->get_id(), $error );
							return false;
						} else {
							if ( function_exists( 'sumosubs_set_transaction_id' ) ) {
								sumosubs_set_transaction_id( $renewal_order->get_id(), $redsys_order, true );
							}
							return true;
						}
					}
				} else {
					$protocol_version = '1.0.2';
					$acctinfo         = WCPSD2()->get_acctinfo( $order, $datos_usuario );
					$datos_entrada    = '<DATOSENTRADA>';
					$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
					$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$datos_entrada   .= $ds_merchant_group;
					$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
					$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					$datos_entrada   .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
					$datos_entrada   .= '</DATOSENTRADA>';
					$xml              = '<REQUEST>';
					$xml             .= $datos_entrada;
					$xml             .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$xml             .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
					$xml             .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          The XML  7           ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', $xml );
						$this->log->add( 'redsys', ' ' );
					}
					$cliente    = new SoapClient( $redsys_adr );
					$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

					if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					}
					$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					if ( $authorisationcode ) {
						WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'payment_complete 4' );
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', '      Saving Order Meta       ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', ' ' );
						}
						if ( ! empty( $redsys_order ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '/******************************************/' );
							$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'redsys', '/******************************************/' );
						}
						do_action( 'redsys_post_payment_complete', $order->get_id() );
						if ( function_exists( 'sumosubs_set_transaction_id' ) ) {
							sumosubs_set_transaction_id( $renewal_order->get_id(), $redsys_order, true );
						}
						return true;
					} else {
						if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
							$error = WCRed()->get_error( $response );
							$order->add_order_note( __( 'There was an error:', 'woocommerce-redsys' ) . $error );
							$message = __( '⚠️ Subscription Payment failed. Error: ', 'woocommerce-redsys' ) . $error . ' URL: ' . WCRed()->get_order_edit_url( $order->get_id() );
							WCRed()->push( $message );
							do_action( 'redsys_post_payment_error', $order->get_id(), $error );
							return false;
						} else {
							if ( function_exists( 'sumosubs_set_transaction_id' ) ) {
								sumosubs_set_transaction_id( $renewal_order->get_id(), $redsys_order, true );
							}
							return true;
						}
					}
				}
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '       Once upon a time       ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***********************************************/' );
				$this->log->add( 'redsys', '  KO Doing SUMO scheduled_subscription_payment   ' );
				$this->log->add( 'redsys', '/***********************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
		}
	}
	/**
	 * Change Yith Subscription.
	 *
	 * @param int $renewal_order Renewal Order.
	 * @param int $is_manual_renew Is Manual Renew.
	 */
	public function renew_yith_subscription( $renewal_order = null, $is_manual_renew = null ) {

		$order_id         = $renewal_order->get_id();
		$amount_to_charge = $renewal_order->get_total();
		$redsys_done      = WCRed()->get_order_meta( $order_id, '_redsys_done', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '       Once upon a time       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', '  Doing scheduled_subscription_payment   ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', '      $order_id = ' . $order_id . '      ' );
			$this->log->add( 'redsys', '/***************************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( 'yes' === $redsys_done ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', '       Payment is complete EXIT          ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
			}
			return;
		} else {

			$order  = $renewal_order;
			$amount = $amount_to_charge;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/**********************************************/' );
				$this->log->add( 'redsys', '  Function  doing_scheduled_subscription_payment' );
				$this->log->add( 'redsys', '/**********************************************/' );
				$this->log->add( 'redsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', '   scheduled charge Amount: ' . $amount );
				$this->log->add( 'redsys', '/***************************************/' );
				$this->log->add( 'redsys', ' ' );
			}

			$order_total_sign     = '';
			$transaction_id2      = '';
			$transaction_type     = '';
			$ds_merchant_terminal = '';
			$final_notify_url     = '';
			$returnfromredsys     = '';
			$gatewaylanguage      = '';
			$currency             = '';
			$secretsha256         = '';
			$customer             = '';
			$url_ok               = '';
			$product_description  = '';
			$merchant_name        = '';

			$order_id = $order->get_id();
			$user_id  = $order->get_user_id();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '  Generating Tokenized call   ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_id: ' . $order_id );
				$this->log->add( 'redsys', '$user_id: ' . $user_id );
				$this->log->add( 'redsys', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $this->get_redsys_url_gateway( $user_id, $type );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
				$this->log->add( 'redsys', ' ' );
			}
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'redsys', ' ' );
			}

			$transaction_type = '0';

			$gatewaylanguage = $this->redsyslanguage;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			}
			$ds_merchant_terminal = $this->terminal;

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			$customer_token    = WCRed()->get_users_token_bulk( $user_id, 'R' );
			$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
			$txnid             = WCRed()->get_txnid( $customer_token_id );

			if ( ! $customer_token ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'NO Customer Token' );
					$this->log->add( 'redsys', ' ' );
				}
				if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
					ywsbs_register_failed_payment( $renewal_order, 'Error: No user token' );
				}
				return false;
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$this->log->add( 'redsys', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Order Currency: ' . get_woocommerce_currency() );
				$this->log->add( 'redsys', ' ' );
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
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
					$this->log->add( 'redsys', ' ' );
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
			$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
			$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'redsys', '$order: ' . $orderid2 );
				$this->log->add( 'redsys', '$customer: ' . $customer );
				$this->log->add( 'redsys', '$currency: ' . $currency );
				$this->log->add( 'redsys', '$transaction_type: 0' );
				$this->log->add( 'redsys', '$terminal: ' . $terminal );
				$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
				$this->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
				$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'redsys', ' ' );
			}

			$mi_obj = new WooRedsysAPIWS();
			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}
			$datos_usuario  = array(
				'threeDSInfo'         => 'AuthenticationData',
				'protocolVersion'     => $protocol_version,
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
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The call            ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $datos_entrada );
				$this->log->add( 'redsys', ' ' );
			}

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML 5            ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'iniciaPeticion 2' . $xml );
				$this->log->add( 'redsys', ' ' );
			}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $responsews->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuesta   = (string) $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			} // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

				$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_emv3ds                = json_decode( $ds_emv3ds_json ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
				$this->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
				$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
			}

			if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
				$datos_entrada .= '</DATOSENTRADA>';
				$xml            = '<REQUEST>';
				$xml           .= $datos_entrada;
				$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml           .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML  6          ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $xml );
					$this->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
				}
				if ( $authorisationcode ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 3' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      Saving Order Meta       ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					if ( ! empty( $redsys_order ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'redsys', '/******************************************/' );
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
						$error = WCRed()->get_error( $response );
						$order->add_order_note( __( 'There wasnand error: ', 'woocommerce-redsys' ) . $error );
						if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
							ywsbs_register_failed_payment( $renewal_order, 'Error' );
						}
						$message = __( '⚠️ Subscription Payment failed. Error: ', 'woocommerce-redsys' ) . $error . ' URL: ' . WCRed()->get_order_edit_url( $order->get_id() );
						WCRed()->push( $message );
						do_action( 'redsys_post_payment_error', $order->get_id(), $error );
						return false;
					} else {
						return true;
					}
				}
			} else {
				$protocol_version = '1.0.2';
				$acctinfo         = WCPSD2()->get_acctinfo( $order, $datos_usuario );
				$datos_entrada    = '<DATOSENTRADA>';
				$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada   .= $ds_merchant_group;
				$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada   .= '<DS_MERCHANT_TITULAR>' . WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) . '</DS_MERCHANT_TITULAR>';
				$datos_entrada   .= '</DATOSENTRADA>';
				$xml              = '<REQUEST>';
				$xml             .= $datos_entrada;
				$xml             .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml             .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml             .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML  7           ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $xml );
					$this->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}
				$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( $authorisationcode ) {
					WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 4' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      Saving Order Meta       ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					if ( ! empty( $redsys_order ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'redsys', '/******************************************/' );
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					if ( ! WCRed()->check_order_is_paid_loop( $order->get_id() ) ) {
						$error = WCRed()->get_error( $response );
						$order->add_order_note( __( 'There was an error:', 'woocommerce-redsys' ) . $error );
						if ( function_exists( 'ywsbs_register_failed_payment' ) ) {
							ywsbs_register_failed_payment( $renewal_order, 'Error: ' . $error );
						}
						$message = __( '⚠️ Subscription Payment failed. Error: ', 'woocommerce-redsys' ) . $error . ' URL: ' . WCRed()->get_order_edit_url( $order->get_id() );
						WCRed()->push( $message );
						do_action( 'redsys_post_payment_error', $order->get_id(), $error );
						return false;
					} else {
						return true;
					}
				}
			}
		}
	}
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 * @param int $token_id Token ID.
	 */
	public function pay_with_token_r( $order_id, $token_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'There is Token R: ' . $customer_token_r );
		}
		$order               = WCRed()->get_order( $order_id );
		$customer_token      = WCRed()->get_token_by_id( $token_id );
		$cof_txnid           = WCRed()->get_txnid( $token_id );
		$mi_obj              = new WooRedsysAPIWS();
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
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', '$orderid2: ' . $orderid2 );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'redsys', '$currency: ' . $currency );
			$this->log->add( 'redsys', '$cof_ini: ' . $cof_ini );
			$this->log->add( 'redsys', '$cof_type: ' . $cof_type );
			$this->log->add( 'redsys', '$cof_txnid: ' . $cof_txnid );
			$this->log->add( 'redsys', '$product_description: ' . $product_description );
			$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'redsys', '$merchant_name: ' . $merchant_name );
			$this->log->add( 'redsys', '$type: ' . $type );
			$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'redsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
			$this->log->add( 'redsys', ' ' );
		}

		if ( '000' === $order_total_sign || '0' === $order_total_sign || 0 === $order_total_sign ) {
			return true;
		}

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
		$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
		$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
		$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
		$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
		$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
		$datos_entrada .= '</DATOSENTRADA>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '          The call  3          ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', $datos_entrada );
			$this->log->add( 'redsys', ' ' );
		}

		$xml  = '<REQUEST>';
		$xml .= $datos_entrada;
		$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '          The XML            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'iniciaPeticion 3' . $xml );
			$this->log->add( 'redsys', ' ' );
		}

		$cliente  = new SoapClient( $redsys_adr );
		$response = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

		if ( isset( $response->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno = new SimpleXMLElement( $response->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$xml_retorno 7: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}

		$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$ds_emv3ds                = json_decode( $ds_emv3ds_json );
		$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
			$this->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
			$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
		}

		if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
			}
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$datos_entrada .= '</DATOSENTRADA>';
			$xml            = '<REQUEST>';
			$xml           .= $datos_entrada;
			$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML             ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $xml );
				$this->log->add( 'redsys', ' ' );
			}
			$cliente  = new SoapClient( $redsys_adr );
			$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno       = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno 8: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
			}
			if ( $authorisationcode ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 5' );
				}
				$order->payment_complete();
				if ( WCRed()->check_order_has_sumo_subscriptions( $order->get_id() ) ) {
					if ( function_exists( 'sumo_save_subscription_payment_info' ) ) {
						sumo_save_subscription_payment_info(
							$order->get_id(),
							array(
								'payment_type'         => 'auto',
								'payment_method'       => 'redsys',
								'payment_key'          => $order->get_user_id(), // Optional. Default it is empty for Manual Payments. Required for Automatic payments(In order to capture the future payments)
								'payment_start_date'   => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
								'payment_end_date'     => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
								'payment_order_amount' => $order->get_total(),
							)
						);
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete 5' );
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      Saving Order Meta       ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}

				if ( ! empty( $redsys_order ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $terminal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $currency_code ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $secretsha256 ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				return true;
			} else {
				$error = 'Unknown';
				do_action( 'redsys_post_payment_error', $order->get_id(), $error );
				return false;
			}
		} else {
			$protocol_version = '1.0.2';
			$data             = array(
				'threeDSInfo'     => 'AuthenticationData',
				'protocolVersion' => '1.0.2',
			);
			$need             = wp_json_encode( $data );
			$acctinfo         = WCPSD2()->get_acctinfo( $order, $datos_usuario );
			$datos_entrada    = '<DATOSENTRADA>';
			$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada   .= $ds_merchant_group;
			$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			// $datos_entrada .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
			// $datos_entrada .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
			$datos_entrada .= '</DATOSENTRADA>';
			$xml            = '<REQUEST>';
			$xml           .= $datos_entrada;
			$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML             ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $xml );
				$this->log->add( 'redsys', ' ' );
			}
			$cliente  = new SoapClient( $redsys_adr );
			$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$response: ' . print_r( $response, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$xml_retorno 9: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			if ( $authorisationcode ) {
				WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 6' );
				}
				$order->payment_complete();
				if ( WCRed()->check_order_has_sumo_subscriptions( $order->get_id() ) ) {
					if ( function_exists( 'sumo_save_subscription_payment_info' ) ) {
						sumo_save_subscription_payment_info(
							$order->get_id(),
							array(
								'payment_type'         => 'auto',
								'payment_method'       => 'redsys',
								'payment_key'          => $order->get_user_id(), // Optional. Default it is empty for Manual Payments. Required for Automatic payments(In order to capture the future payments)
								'payment_start_date'   => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
								'payment_end_date'     => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
								'payment_order_amount' => $order->get_total(),
							)
						);
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      Saving Order Meta       ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}
				if ( ! empty( $redsys_order ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $terminal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $currency_code ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $secretsha256 ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				return true;
			} else {
				$error = 'Unknown';
				do_action( 'redsys_post_payment_error', $order->get_id(), $error );
				return false;
			}
		}
	}
	/**
	 * Function pay_with_token_c
	 *
	 * @param  int    $order_id Order ID.
	 * @param  string $token_id Token ID.
	 * @return bool
	 */
	public function pay_with_token_c( $order_id, $token_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'Function pay_with_token_c( $order_id, $token_id )' );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '$token_id: ' . $token_id );
			$this->log->add( 'redsys', ' ' );
		}
		$customer_token_c = WCRed()->get_token_by_id( $token_id );
		$order            = WCRed()->get_order( $order_id );
		$currency_codes   = WCRed()->get_currencies();

		// Pay with 1 clic & token exist.
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$customer_token_c exist' );
			$this->log->add( 'redsys', '$customer_token_c: ' . $customer_token_c );
			$this->log->add( 'redsys', ' ' );
		}

		$mi_obj               = new WooRedsysAPIWS();
		$order_total_sign     = WCRed()->redsys_amount_format( $order->get_total() );
		$orderid2             = WCRed()->prepare_order_number( $order_id );
		$user_id              = $order->get_user_id();
		$customer             = $this->customer;
		$currency_codes       = WCRed()->get_currencies();
		$currency             = $currency_codes[ get_woocommerce_currency() ];
		$cof_ini              = 'N';
		$cof_type             = 'C';
		$cof_txnid            = WCRed()->get_txnid( $token_id );
		$secretsha256         = $this->get_redsys_sha256( $user_id );
		$url_ok               = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description  = WCRed()->product_description( $order, 'redsys' );
		$merchant_name        = $this->commercename;
		$type                 = 'ws';
		$redsys_adr           = $this->get_redsys_url_gateway_ws( $user_id, $type );
		$http_accept          = WCRed()->get_order_meta( $order_id, '_accept_haders' );
		$ds_merchant_terminal = $this->terminal;
		if ( WCRed()->order_needs_preauth( $order_id ) ) {
			$transaction_type = '1';
		} else {
			$transaction_type = '0';
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', '$orderid2: ' . $orderid2 );
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'redsys', '$currency: ' . $currency );
			$this->log->add( 'redsys', '$cof_ini: ' . $cof_ini );
			$this->log->add( 'redsys', '$cof_type: ' . $cof_type );
			$this->log->add( 'redsys', '$cof_txnid: ' . $cof_txnid );
			$this->log->add( 'redsys', '$product_description: ' . $product_description );
			$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'redsys', '$merchant_name: ' . $merchant_name );
			$this->log->add( 'redsys', '$type: ' . $type );
			$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'redsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
			$this->log->add( 'redsys', 'Amount for use TRA: ' . $this->traamount );
			$this->log->add( 'redsys', 'Amount to compare: ' . 100 * (int) $this->traamount );
			$this->log->add( 'redsys', ' ' );
		}
		if ( $order_total_sign <= 3000 && 'yes' === $this->lwvactive ) {
			$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
			set_transient( $order_id . '_ds_merchant_excep_sca', 'LWV', 3600 );
		} else {
			$lwv = '';
		}
		if ( 'yes' === $this->traactive && $order_total_sign > 3000 && $order_total_sign <= ( 100 * (int) $this->traamount ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Using TRA' );
				$this->log->add( 'redsys', ' ' );
			}
			$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
			set_transient( $order_id . '_ds_merchant_excep_sca', 'TRA', 3600 );
		}

		set_transient( $order_id . '_ds_merchant_cof_ini', $cof_ini, 3600 );
		set_transient( $order_id . '_ds_merchant_cof_type', $cof_type, 3600 );
		set_transient( $order_id . '_insite_token_redsys', $customer_token_c, 3600 );
		set_transient( $order_id . '_insite_token_txnid', $cof_txnid, 3600 );

		set_transient( $order_id . '_insite_merchant_amount', $order_total_sign, 3600 );
		set_transient( $order_id . '_insite_merchant_order', $orderid2, 3600 );
		set_transient( $order_id . '_insite_merchantcode', $customer, 3600 );
		set_transient( $order_id . '_insite_terminal', $ds_merchant_terminal, 3600 );
		set_transient( $order_id . '_insite_transaction_type', $transaction_type, 3600 );
		set_transient( $order_id . '_insite_currency', $currency, 3600 );

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
		$datos_entrada .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
		$datos_entrada .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
		$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$datos_entrada .= $lwv;
		$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
		$datos_entrada .= '</DATOSENTRADA>';

		$xml  = '<REQUEST>';
		$xml .= $datos_entrada;
		$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$datos_entrada 1: ' . $datos_entrada );
			$this->log->add( 'redsys', 'iniciaPeticion 4: ' . $xml );
			$this->log->add( 'redsys', ' ' );
		}

		$cliente  = new SoapClient( $redsys_adr );
		$response = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

		if ( isset( $response->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno = new SimpleXMLElement( $response->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		$protocol_version         = '';
		$ds_card_psd2             = '';
		$three_ds_server_trans_id = '';
		$three_ds_info            = '';
		$three_ds_method_url      = '';
		if ( isset( $respuesta->protocolVersion ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$protocol_version = (string) $respuesta->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		if ( isset( $xml_retorno->INFOTARJETA->Ds_Card_PSD2 ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$ds_card_psd2 = trim( $xml_retorno->INFOTARJETA->Ds_Card_PSD2 ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		if ( isset( $respuesta->threeDSServerTransID ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$three_ds_server_trans_id = trim( $respuesta->threeDSServerTransID ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		if ( isset( $respuesta->threeDSInfo ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$three_ds_info = trim( $respuesta->threeDSInfo ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		if ( isset( $respuesta->threeDSMethodURL ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$three_ds_method_url = trim( $respuesta->threeDSMethodURL ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$xml_retorno 10: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', '$respuesta: ' . print_r( $respuesta, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
			$this->log->add( 'redsys', 'threeDSServerTransID: ' . $three_ds_server_trans_id );
			$this->log->add( 'redsys', 'threeDSInfo: ' . $three_ds_info );
			$this->log->add( 'redsys', 'threeDSMethodURL: ' . $three_ds_method_url );
			$this->log->add( 'redsys', 'Ds_Card_PSD2: ' . $ds_card_psd2 );
			$this->log->add( 'redsys', ' ' );
		}

		if ( ( 'NO_3DS_v2' === $protocol_version || ( '1.0.2' === $protocol_version ) ) ) {
			// Es protocolo 1.0.2.
			$protocol_version = '1.0.2';
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Es Protocolo NO_3DS_v2 (1.0.2) y PSD2' );
			}
			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			$browser_ip    = WCRed()->get_the_ip();
			$datos_usuario = array(
				'threeDSInfo'         => 'AuthenticationData',
				'protocolVersion'     => $protocol_version,
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
			$needed        = wp_json_encode(
				array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocol_version,
					'browserAcceptHeader' => $http_accept,
					'browserUserAgent'    => WCPSD2()->get_agente_navegador( $order_id ),
				)
			);
			$acctinfo      = WCPSD2()->get_acctinfo( $order, $datos_usuario );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$acctinfo: ' . $acctinfo );
			}
			if ( $order_total_sign <= 3000 && 'yes' === $this->lwvactive ) {
				$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
			} else {
				$lwv = '';
			}
			if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int) $this->traamount ) && $order_total_sign > 3000 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using TRA' );
					$this->log->add( 'redsys', ' ' );
				}
				$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
			}
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= $lwv;
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$datos_entrada 2: ' . $datos_entrada );
				$this->log->add( 'redsys', '$xml: ' . $xml );
				$this->log->add( 'redsys', ' ' );
			}

			$cliente  = new SoapClient( $redsys_adr );
			$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno       = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = trim( $xml_retorno->CODIGO ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuestaeds      = json_decode( $xml_retorno->OPERACION->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_info     = trim( $respuestaeds->threeDSInfo ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$protocol_version  = trim( $respuestaeds->protocolVersion ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$acs_url           = trim( $respuestaeds->acsURL ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$par_eq            = trim( $respuestaeds->{ 'PAReq'} );
				$md                = trim( $respuestaeds->MD ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			}
			if ( 'yes' === $this->debug && ! $authorisationcode ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$response: ' . print_r( $response, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$xml_retorno 11: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$codigo: ' . $codigo );
				$this->log->add( 'redsys', '$respuesta: ' . print_r( $respuestaeds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
				if ( ! empty( $respuestaeds->threeDSServerTransID ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$this->log->add( 'redsys', 'threeDSServerTransID: ' . $respuestaeds->threeDSServerTransID ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}
				$this->log->add( 'redsys', 'threeDSInfo: ' . $three_ds_info );
				if ( ! empty( $respuestaeds->threeDSMethodURL ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$this->log->add( 'redsys', 'threeDSMethodURL: ' . $respuestaeds->threeDSMethodURL ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}
				$this->log->add( 'redsys', 'Ds_Card_PSD2: ' . $ds_card_psd2 );
				$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
				$this->log->add( 'redsys', '$acs_url: ' . $acs_url );
				$this->log->add( 'redsys', '$par_eq: ' . $par_eq );
				$this->log->add( 'redsys', '$md: ' . $md );
				$this->log->add( 'redsys', ' ' );
			}

			if ( 'ChallengeRequest' === $three_ds_info ) {
				// hay challenge
				// Guardamos todo en transciends.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/***************/' );
					$this->log->add( 'redsys', '     1.0.2' );
					$this->log->add( 'redsys', '  Hay Challenge  ' );
					$this->log->add( 'redsys', '/***************/' );
				}
				set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
				set_transient( 'protocolVersion_' . $order_id, $protocol_version, 300 );
				set_transient( 'acsURL_' . $order_id, $acs_url, 300 );
				set_transient( 'PAReq_' . $order_id, $par_eq, 300 );
				set_transient( 'MD_' . $order_id, $md, 300 );
				set_transient( $md, $order_id, 300 );
				set_transient( 'amount_' . $md, $order_total_sign, 300 );
				set_transient( 'order_' . $md, $orderid2, 300 );
				set_transient( 'merchantcode_' . $md, $customer, 300 );
				set_transient( 'terminal_' . $md, $ds_merchant_terminal, 300 );
				set_transient( 'currency_' . $md, $currency, 300 );
				set_transient( 'identifier_' . $md, $customer_token_c, 300 );
				set_transient( 'cof_ini_' . $md, $cof_ini, 300 );
				set_transient( 'cof_type_' . $md, $cof_type, 300 );
				set_transient( 'cof_txnid_' . $md, $cof_txnid, 300 );
				return 'ChallengeRequest';
			} elseif ( ! empty( $authorisationcode ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/***************/' );
					$this->log->add( 'redsys', '  Paid  ' );
					$this->log->add( 'redsys', '/***************/' );
				}
				$ds_order         = trim( $xml_retorno->OPERACION->Ds_Order ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_merchant_code = trim( $xml_retorno->OPERACION->Ds_MerchantCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_terminal      = trim( $xml_retorno->OPERACION->Ds_Terminal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 7' );
				}
				$order->payment_complete();
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      Saving Order Meta       ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}

				if ( ! empty( $ds_order ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $ds_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $ds_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $dsdate ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_date_redsys saved: ' . $dsdate );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_date_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $ds_terminal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $ds_terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $ds_terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $dshour ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_hour_redsys saved: ' . $dshour );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_hour_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $currency ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				// This meta is essential for later use.
				if ( ! empty( $secretsha256 ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				return 'success';
			} else {
				$error = 'Unknown';
				do_action( 'redsys_post_payment_error', $order->get_id(), $error );
			}
		} elseif ( ( ( '2.1.0' === $protocol_version ) || ( '2.2.0' === $protocol_version ) ) ) {
			// Es protocolo 2.1.0.
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Es Protocolo 2.1.0 y PSD2' );
			}

			$http_accept = WCPSD2()->get_accept_headers( $order_id );

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}
			$browser_ip = WCRed()->get_the_ip();

			set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
			set_transient( 'accept_headers_' . $order_id, $http_accept, 300 );
			set_transient( 'protocolVersion_' . $order_id, $protocol_version, 300 );
			set_transient( 'acsURL_' . $order_id, $acs_url, 300 );
			set_transient( 'threeDSServerTransID_' . $order_id, $three_ds_server_trans_id, 300 );
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
			set_transient( $three_ds_server_trans_id, $order_id, 300 );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
				$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'redsys', '$three_ds_method_url: ' . $three_ds_method_url );
			}

			if ( ! empty( $three_ds_method_url ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'There is threeDSMethodURL, continue with PSD2 Autentication' . $json_pre );
				}
				return 'threeDSMethodURL';
			}
			$data     = array();
			$data     = array(
				'threeDSServerTransID'         => $three_ds_server_trans_id,
				'threeDSMethodNotificationURL' => $final_notify_url,
			);
			$json_pre = wp_json_encode( $data );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$json_pre: ' . $json_pre );
			}
			$json = base64_encode( $json_pre ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$json: ' . $json );
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
				$this->log->add( 'redsys', '$body: ' . print_r( $body, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			$response      = wp_remote_post( $three_ds_method_url, $options );
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$response_body: ' . $response_body );
			}

			if ( strpos( $response_body, $final_notify_url ) !== false ) {
				$url = true;
			} else {
				$url = false;
			}
			if ( strpos( $response_body, $json ) !== false ) {
				$three_ds_method_datatest = true;
			} else {
				$three_ds_method_datatest = false;
			}
			if ( $url && $three_ds_method_datatest ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'URL y threeDSMethodData coinciden' );
				}
				$three_ds_comp_ind = 'Y';
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'URL y threeDSMethodData NO coinciden' );
				}
				$three_ds_comp_ind = 'N';
			}

			if ( '2.2.0' === $protocol_version ) {
				$datos_usuario = array(
					'threeDSInfo'              => 'AuthenticationData',
					'protocolVersion'          => $protocol_version,
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
					'threeDSServerTransID'     => $three_ds_server_trans_id,
					'notificationURL'          => $final_notify_url,
					'threeDSCompInd'           => $three_ds_comp_ind,
				);
			} else {
				$datos_usuario = array(
					'threeDSInfo'          => 'AuthenticationData',
					'protocolVersion'      => $protocol_version,
					'browserAcceptHeader'  => $http_accept,
					'browserColorDepth'    => WCPSD2()->get_profundidad_color( $order_id ),
					'browserIP'            => $browser_ip,
					'browserJavaEnabled'   => WCPSD2()->get_browserjavaenabled( $order_id ),
					'browserLanguage'      => WCPSD2()->get_idioma_navegador( $order_id ),
					'browserScreenHeight'  => WCPSD2()->get_altura_pantalla( $order_id ),
					'browserScreenWidth'   => WCPSD2()->get_anchura_pantalla( $order_id ),
					'browserTZ'            => WCPSD2()->get_diferencia_horaria( $order_id ),
					'browserUserAgent'     => WCPSD2()->get_agente_navegador( $order_id ),
					'threeDSServerTransID' => $three_ds_server_trans_id,
					'notificationURL'      => $final_notify_url,
					'threeDSCompInd'       => $three_ds_comp_ind,
				);
			}
			$acctinfo = WCPSD2()->get_acctinfo( $order, $datos_usuario, $user_id );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$user_id: ' . $user_id );
				$this->log->add( 'redsys', '$order_id: ' . $order_id );
				$this->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
				$this->log->add( 'redsys', 'threeDSServerTransID: ' . $three_ds_server_trans_id );
				$this->log->add( 'redsys', 'notificationURL: ' . $final_notify_url );
				$this->log->add( 'redsys', 'threeDSCompInd: ' . $three_ds_comp_ind );
				$this->log->add( 'redsys', 'acctInfo: : ' . $acctinfo );
			}
			$order_total_sign     = get_transient( 'amount_' . $order_id );
			$orderid2             = get_transient( 'order_' . $order_id );
			$customer             = $this->customer;
			$ds_merchant_terminal = get_transient( 'terminal_' . $order_id );
			$currency             = get_transient( 'currency_' . $order_id );
			$customer_token_c     = get_transient( 'identifier_' . $order_id );
			$cof_ini              = get_transient( 'cof_ini_' . $order_id );
			$cof_type             = get_transient( 'cof_type_' . $order_id );
			$cof_txnid            = get_transient( 'cof_txnid_' . $order_id );

			if ( $order_total_sign <= 3000 && 'yes' === $this->lwvactive ) {
				$lwv = '<DS_MERCHANT_EXCEP_SCA>LWV</DS_MERCHANT_EXCEP_SCA>';
			} else {
				$lwv = '';
			}
			if ( 'yes' === $this->traactive && $order_total_sign <= ( 100 * (int) $this->traamount ) && $order_total_sign > 3000 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using TRA' );
					$this->log->add( 'redsys', ' ' );
				}
				$lwv = '<DS_MERCHANT_EXCEP_SCA>TRA</DS_MERCHANT_EXCEP_SCA>';
			}
			$mi_obj = new WooRedsysAPIWS();

			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}
			if ( WCRed()->order_needs_preauth( $order_id ) ) {
				$transaction_type = '1';
			} else {
				$transaction_type = '0';
			}

			$secretsha256   = $this->get_redsys_sha256( $user_id );
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= $ds_merchant_group;
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= $lwv;
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';
			$xml            = '<REQUEST>';
			$xml           .= $datos_entrada;
			$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'The XML 1: ' . $xml );
			}

			$cliente  = new SoapClient( $redsys_adr );
			$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
			$ds_emv3ds         = $xml_retorno->OPERACION->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$json_decode       = json_decode( $ds_emv3ds );
			$three_ds_info     = $json_decode->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$protocol_version  = $json_decode->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$acs_url           = $json_decode->acsURL; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$par_eq            = trim( $json_decode->{ 'PAReq'} );
			$creq              = trim( $json_decode->{ 'creq'} );
			$md                = $json_decode->MD; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno 12: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', 'Ds_EMV3DS: ' . $ds_emv3ds );
				$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				$this->log->add( 'redsys', ' ' );
			}

			if ( 'ChallengeRequest' === $three_ds_info ) {
				// hay challenge
				// Guardamos todo en transciends.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/***************/' );
					$this->log->add( 'redsys', '  2.2.0 y 2.2.1' );
					$this->log->add( 'redsys', 'pay_with_token_c()' );
					$this->log->add( 'redsys', '  Hay Challenge  ' );
					$this->log->add( 'redsys', '/***************/' );
				}
				set_transient( 'threeDSInfo_' . $order_id, $three_ds_info, 300 );
				set_transient( 'protocolVersion_' . $order_id, $protocol_version, 300 );
				set_transient( 'acsURL_' . $order_id, $acs_url, 300 );
				set_transient( 'PAReq_' . $order_id, $par_eq, 300 );
				set_transient( 'MD_' . $order_id, $md, 300 );
				set_transient( $md, $order_id, 300 );
				set_transient( 'creq_' . $order_id, $creq, 300 );
				set_transient( 'amount_' . $md, $order_total_sign, 300 );
				set_transient( 'order_' . $md, $orderid2, 300 );
				set_transient( 'merchantcode_' . $md, $customer, 300 );
				set_transient( 'terminal_' . $md, $ds_merchant_terminal, 300 );
				set_transient( 'currency_' . $md, $currency, 300 );
				set_transient( 'identifier_' . $md, $customer_token_c, 300 );
				set_transient( 'cof_ini_' . $md, $cof_ini, 300 );
				set_transient( 'cof_type_' . $md, $cof_type, 300 );
				set_transient( 'cof_txnid_' . $md, $cof_txnid, 300 );
				return 'ChallengeRequest';
			} elseif ( ! empty( $authorisationcode ) ) {
				// Pago directo sin challenge.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/***************/' );
					$this->log->add( 'redsys', '  Paid  ' );
					$this->log->add( 'redsys', '/***************/' );
				}
				$ds_order         = trim( $xml_retorno->OPERACION->Ds_Order ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_merchant_code = trim( $xml_retorno->OPERACION->Ds_MerchantCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_terminal      = trim( $xml_retorno->OPERACION->Ds_Terminal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 8' );
				}
				$order->payment_complete();
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisationcode );
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      Saving Order Meta       ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}

				if ( ! empty( $ds_order ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $ds_order );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $ds_order );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $dsdate ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_date_redsys saved: ' . $dsdate );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_date_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $ds_terminal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $ds_terminal );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $ds_terminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $dshour ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_payment_hour_redsys saved: ' . $dshour );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_payment_hour_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				if ( ! empty( $currency ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				// This meta is essential for later use.
				if ( ! empty( $secretsha256 ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'redsys', ' ' );
					}
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				return 'success';
			}
		}

	}
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 */
	public function process_payment( $order_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '  Function process_payment()  ' );
			$this->log->add( 'redsys', '/****************************/' );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '  $_POST: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', '/****************************/' );
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}

		$order                = WCRed()->get_order( $order_id );
		$user_id              = $order->get_user_id();
		$usetokensdirect      = $this->usetokensdirect;
		$terminal2            = $this->terminal2;
		$terminal             = $this->terminal;
		$use_token            = get_transient( $order_id . '_redsys_use_token' );
		$token_type           = get_transient( $order_id . '_redsys_token_type' );
		$url_ok               = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$rneeds_payment       = get_transient( $order_id . '_redsys_needs_payment' );
		$tokennum             = get_transient( $order_id . '_redsys_use_token' );
		$save_token           = get_transient( $order_id . '_redsys_save_token' );
		$contais_subscription = WCRed()->order_contains_subscription( $order->get_id() );

		if ( 'redsyspending' === $this->markpending ) {
			$order->update_status( 'redsys-wait', __( 'Pending Redsys Payment', 'woocommerce-redsys' ) );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Updating Status to: Pending Redsys Payment' );
			}
		}

		if ( isset( $_POST['token'] ) && isset( $_POST['_redsys_token_type'] ) && 'add' !== $_POST['token'] ) {
			$token_type = sanitize_text_field( wp_unslash( $_POST['_redsys_token_type'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$tokennum   = sanitize_text_field( wp_unslash( $_POST['token'] ) );
			$use_token  = sanitize_text_field( wp_unslash( $_POST['token'] ) );
			set_transient( $order_id . '$token_type', $token_type, 36000 );
		}

		if ( isset( $_POST['token'] ) && isset( $_POST['_redsys_save_token'] ) && 'add' === $_POST['token'] ) {
			$token_type = sanitize_text_field( wp_unslash( $_POST['_redsys_token_type'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$tokennum   = 'no';
			$use_token  = 'no';
			$save_token = sanitize_text_field( wp_unslash( $_POST['_redsys_save_token'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			set_transient( $order_id . '_redsys_use_token', $use_token, 36000 );
			set_transient( $order_id . '_insite_use_token', sanitize_text_field( wp_unslash( $_POST['token'] ) ), 36000 );
			set_transient( $order_id . '_redsys_token_type', $token_type, 36000 );
			set_transient( $order_id . '_redsys_save_token', $save_token, 36000 );
		}

		if ( isset( $_POST['_redsys_save_token'] ) ) {
			$save_token = sanitize_text_field( wp_unslash( $_POST['_redsys_save_token'] ) );
			set_transient( $order_id . '_redsys_save_token', $save_token, 3600 );
		}

		if ( empty( $token_type ) && empty( $tokennum ) && $contais_subscription ) {
			$save_token = 'yes';
			$token_type = 'R';
			$tokennum   = 'no';
			$use_token  = 'no';
		}

		if ( isset( $_POST['token'] ) && empty( $tokennum && ! $contais_subscription ) ) {
			$token_type = sanitize_text_field( wp_unslash( $_POST['_redsys_token_type'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			set_transient( $order_id . '_redsys_token_type', $token_type, 3600 );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$user_id: ' . $user_id );
			$this->log->add( 'redsys', '$usetokensdirect: ' . $usetokensdirect );
			$this->log->add( 'redsys', '$terminal2: ' . $terminal2 );
			$this->log->add( 'redsys', '$terminal: ' . $terminal );
			$this->log->add( 'redsys', '$use_token: ' . $use_token );
			$this->log->add( 'redsys', '$token_type: ' . $token_type );
			$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
			$this->log->add( 'redsys', '$tokennum: ' . $tokennum );
			$this->log->add( 'redsys', '$save_token: ' . $save_token );
		}

		if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			$http_accept = wp_unslash( $_SERVER['HTTP_ACCEPT'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			WCRed()->update_order_meta( $order_id, '_accept_haders', $http_accept );
			update_user_meta( $user_id, '_accept_haders', $http_accept );
		} else {
			$http_accept = 'null';
			WCRed()->update_order_meta( $order_id, '_accept_haders', $http_accept );
			update_user_meta( $user_id, '_accept_haders', $http_accept );
		}
		if ( empty( $tokennum ) ) {
			$tokennum = 'no';
		}
		if ( 'no' !== $tokennum ) { // Using Token.
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Using Token' );
			}
			if ( $order->get_total() > 0 ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Order bigger 0' );
				}
				$token_type = get_transient( $order_id . '_redsys_token_type' );
				if ( 'R' === $token_type ) {
					$result = $this->pay_with_token_r( $order_id, $tokennum );
					if ( $result ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'Pago mediante token CORRECTO' );
						}
						return array(
							'result'   => 'success',
							'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
						);
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'Pago mediante token FALLIDO' );
						}
						$error = 'We are having trouble charging the card, please try another one';
						do_action( 'redsys_post_payment_error', $order->get_id(), $error );
						wc_add_notice( 'We are having trouble charging the card, please try another one. ', 'error' );
					}
				} else {
					WCRed()->print_overlay_image();
					$result = $this->pay_with_token_c( $order_id, $tokennum );
					if ( 'success' === $result ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '$result: success' );
						}
						return array(
							'result'   => 'success',
							'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
						);
					} elseif ( 'ChallengeRequest' === $result ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '$result: ChallengeRequest' );
						}
						return array(
							'result'   => 'success',
							'redirect' => $order->get_checkout_payment_url( true ) . '#3DSform',
						);
					} elseif ( 'threeDSMethodURL' === $result ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '$result: threeDSMethodURL' );
						}
						return array(
							'result'   => 'success',
							'redirect' => $this->notify_url . '&threeDSMethodURL=true&order=' . $order_id,
						);
					}
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Token dont Needed, 0 card' );
					$this->log->add( 'redsys', 'payment_complete() 9' );
				}
				$order->payment_complete();
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				return array(
					'result'   => 'success',
					'redirect' => add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ),
				);
			}
		}

		$customer_token_r    = WCRed()->get_redsys_users_token( 'R' );
		$customer_token_c    = WCRed()->get_redsys_users_token( 'C' );
		$customer_token_r_id = WCRed()->get_redsys_users_token( 'R', 'id' );
		$customer_token_c_id = WCRed()->get_redsys_users_token( 'C', 'id' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$psd2: YES' );
			$this->log->add( 'redsys', '$usetokensdirect: YES' );
			$this->log->add( 'redsys', '$customer_token_r: ' . $customer_token_r );
			$this->log->add( 'redsys', '$customer_token_c: ' . $customer_token_c );
			$this->log->add( 'redsys', ' ' );
		}
		if ( WCRed()->order_contains_subscription( $order_id ) && 'yes' !== $this->subsusetokensdisable ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'order contains subscription & is token is not disabled' );
			}
			if ( $customer_token_r && 'no' !== $tokennum ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'There is Token R: ' . $customer_token_r );
				}
				$customer_token      = $customer_token_r;
				$cof_txnid           = WCRed()->get_txnid( $customer_token_r_id );
				$mi_obj              = new WooRedsysAPIWS();
				$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
				$orderid2            = WCRed()->prepare_order_number( $order_id, 'redsys' );
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
					$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
					$this->log->add( 'redsys', '$orderid2: ' . $orderid2 );
					$this->log->add( 'redsys', '$user_id: ' . $user_id );
					$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
					$this->log->add( 'redsys', '$currency: ' . $currency );
					$this->log->add( 'redsys', '$cof_ini: ' . $cof_ini );
					$this->log->add( 'redsys', '$cof_type: ' . $cof_type );
					$this->log->add( 'redsys', '$cof_txnid: ' . $cof_txnid );
					$this->log->add( 'redsys', '$product_description: ' . $product_description );
					$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
					$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
					$this->log->add( 'redsys', '$merchant_name: ' . $merchant_name );
					$this->log->add( 'redsys', '$type: ' . $type );
					$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
					$this->log->add( 'redsys', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
					$this->log->add( 'redsys', ' ' );
				}

				if ( '000' === $order_total_sign || 0 === (int) $order_total_sign ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Free order' );
					}
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 10' );
					}
					return array(
						'result'   => 'success',
						'redirect' => $url_ok,
					);
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Amount is different to 0' );
					$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				}

				$datos_usuario  = array(
					'threeDSInfo'         => 'AuthenticationData',
					'protocolVersion'     => $protocol_version,
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
				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
				$datos_entrada .= '</DATOSENTRADA>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The call            ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', $datos_entrada );
					$this->log->add( 'redsys', ' ' );
				}

				$xml  = '<REQUEST>';
				$xml .= $datos_entrada;
				$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml .= '</REQUEST>';

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '          The XML 8            ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'iniciaPeticion 5' . $xml );
					$this->log->add( 'redsys', ' ' );
				}

				$cliente  = new SoapClient( $redsys_adr );
				$response = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $response->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno = new SimpleXMLElement( $response->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}

				$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ds_emv3ds                = json_decode( $ds_emv3ds_json );
				$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
					$this->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
					$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				}

				if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
						$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
					}
					$datos_entrada  = '<DATOSENTRADA>';
					$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
					$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
					$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					$datos_entrada .= '</DATOSENTRADA>';
					$xml            = '<REQUEST>';
					$xml           .= $datos_entrada;
					$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
					$xml           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          The XML 9            ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', $xml );
						$this->log->add( 'redsys', ' ' );
					}
					$cliente  = new SoapClient( $redsys_adr );
					$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

					if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$xml_retorno       = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
					}
					if ( $authorisationcode ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'payment_complete 11' );
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', '      Saving Order Meta       ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', ' ' );
						}
						$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
						if ( $needs_preauth ) {
							$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
						}

						if ( ! empty( $redsys_order ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '/******************************************/' );
							$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'redsys', '/******************************************/' );
						}
						do_action( 'redsys_post_payment_complete', $order->get_id() );
						return array(
							'result'   => 'success',
							'redirect' => $url_ok,
						);
					} else {
						// TO-DO: Enviar un correo con el problema al administrador.
						if ( ! WCRed()->is_paid( $order->get_id() ) ) {
							$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
							$order->update_status( 'failed' );
							$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
							do_action( 'redsys_post_payment_error', $order->get_id(), $error );
						}
					}
				} else {
					$protocol_version = '1.0.2';
					$datos_entrada    = '<DATOSENTRADA>';
					$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
					$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
					$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
					$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
					$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
					$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
					$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
					$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
					$datos_entrada   .= $ds_merchant_group;
					$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
					$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
					$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
					$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
					// $datos_entrada .= "<DS_MERCHANT_MERCHANTURL>" . $final_notify_url . "</DS_MERCHANT_MERCHANTURL>";
					// $datos_entrada .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
					$datos_entrada .= '</DATOSENTRADA>';
					$xml            = '<REQUEST>';
					$xml           .= $datos_entrada;
					$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
					$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
					$xml           .= '</REQUEST>';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '          The XML  10           ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', $xml );
						$this->log->add( 'redsys', ' ' );
					}
					$cliente  = new SoapClient( $redsys_adr );
					$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

					if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$xml_retorno = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$response: ' . print_r( $response, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					}
					$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					if ( $authorisationcode ) {
						WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
						$order->payment_complete();
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'payment_complete 12' );
						}
						$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
						if ( $needs_preauth ) {
							$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', '      Saving Order Meta       ' );
							$this->log->add( 'redsys', '/****************************/' );
							$this->log->add( 'redsys', ' ' );
						}
						if ( ! empty( $redsys_order ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $terminal ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $authorisationcode ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $currency_code ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( ! empty( $secretsha256 ) ) {
							WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
							}
						} else {
							if ( 'yes' === $this->debug ) {
								$this->log->add( 'redsys', ' ' );
								$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
								$this->log->add( 'redsys', ' ' );
							}
						}
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '/******************************************/' );
							$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
							$this->log->add( 'redsys', '/******************************************/' );
						}
						do_action( 'redsys_post_payment_complete', $order->get_id() );
						return array(
							'result'   => 'success',
							'redirect' => $url_ok,
						);
					} else {
						// TO-DO: Enviar un correo con el problema al administrador.
						$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
						$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
						do_action( 'redsys_post_payment_error', $order->get_id(), $error );
					}
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '/*************************************************************/' );
					$this->log->add( 'redsys', '  There is not Token for Subscriptions, redirecting to Redsys  ' );
					$this->log->add( 'redsys', '/*************************************************************/' );
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '$redsys_save_token: ' . $redsys_save_token );
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'set_transient: _redsys_save_token' );
					$this->log->add( 'redsys', '$order->get_id(): ' . $order->get_id() );
				}
				set_transient( $order->get_id() . '_redsys_save_token', 'yes', 36000 );
				$redirect = WCRed()->get_url_redsys_payment( $order_id, $final_notify_url );
				if ( 'iframe' === $this->usebrowserreceipt ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'Utilizando Modal para pago' );
						$this->log->add( 'redsys', ' ' );
					}
					return array(
						'result'   => 'success',
						'redirect' => '?order_id=' . $order_id . '&method=redsys#open-popup',
						'order_id' => $order_id,
						'url'      => WCRed()->get_url_redsys_payment( $order_id, $final_notify_url ),
					);
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$redirect: ' . $redirect );
					$this->log->add( 'redsys', ' ' );
				}
				return array(
					'result'   => 'success',
					'redirect' => $redirect,
				);
			}
		} else {

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Not pay with 1clic' );
				$this->log->add( 'redsys', '$customer_token_c: ' . $customer_token_c );
				$this->log->add( 'redsys', ' ' );
			}
			$redirect = WCRed()->get_url_redsys_payment( $order_id, $final_notify_url );
			if ( 'iframe' === $this->usebrowserreceipt ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Utilizando Modal para pago' );
					$this->log->add( 'redsys', ' ' );
				}
				return array(
					'result'   => 'success',
					'redirect' => '?order_id=' . $order_id . '&method=redsys#open-popup',
					'order_id' => $order_id,
					'url'      => WCRed()->get_url_redsys_payment( $order_id, $final_notify_url ),
				);
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$redirect: ' . $redirect );
				$this->log->add( 'redsys', ' ' );
			}
			return array(
				'result'   => 'success',
				'redirect' => $redirect,
			);
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function add_ajax_url_header() {
		if ( is_wc_endpoint_url( 'order-pay' ) ) {
			echo '<script type="text/javascript">var ajaxurl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";</script>';
		}
	}
	/**
	 * Payment_fields function.
	 */
	public function payment_fields() {

		if ( is_checkout() ) {
			$allowed_html        = array(
				'br'     => array(),
				'p'      => array(
					'style' => array(),
					'class' => array(),
					'id'    => array(),
				),
				'span'   => array(
					'style' => array(),
					'class' => array(),
					'id'    => array(),
				),
				'strong' => array(),
			);
			$allowed_html_filter = apply_filters( 'redsys_kses_descripcion', $allowed_html );
			echo '
				<style>
					.payment_method_redsys .input-wrap {
						height: 60px !important;
						margin-left: -8px;
						margin-bottom: 15px;
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
				</style>
	
				<div class="payment_method_redsys">
					<fieldset class="card-saved">
						<p>
							' . wp_kses( $this->description, $allowed_html_filter ) . '
						</p>';
			$the_card = WC()->cart->get_cart();
			if ( ( 'yes' === $this->usetokens && is_user_logged_in() ) || 'R' === WCRed()->check_card_for_subscription( $the_card ) ) {
				$user_id           = get_current_user_id();
				$token_type_needed = WCRed()->check_card_for_subscription( $the_card );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '$token_type_needed: ' . $token_type_needed );
				}
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
					echo '<label for="new">' . esc_html__( 'Use a new payment method', 'woocommerce-redsys' ) . '</label>';
					echo '<input type="hidden" id="_redsys_token_type" name="_redsys_token_type" value="' . esc_html( $token_type_needed ) . '"></>';
					echo '</ul>';
					echo '</div>';
				}

				if ( 'no' === $need_token ) {
					echo '
						<div id="redsys_save_token">
							<label><input type="checkbox" id="_redsys_save_token" name="_redsys_save_token" value="yes"> ' . esc_html__( 'Save payment information to my account for future purchases.', 'woocommerce-redsys' ) . '</label>
						</div>';
				} else {
					$text        = __( 'We need to store your credit card for future payments. It will be stored by our bank, so it is totally safe.', 'woocommerce-redsys' );
					$text_filter = apply_filters( 'redsys_text_get_token', $text );
					echo '
						<div id="redsys_save_token">
							' . esc_html( $text_filter ) . '
							<input type="hidden" id="_redsys_save_token" name="_redsys_save_token" value="yes">
						</div>';
				}
			}

			do_action( 'redsys_payment_fields' );

			// Preauthotization.
			if ( WCRed()->check_card_preauth( $the_card ) ) {
				$text        = __( 'We will preauthorize the Order and will be charge later when we know the final cost.', 'woocommerce-redsys' );
				$text_filter = apply_filters( 'redsys_text_preauth', $text );
				echo '
					<div id="redsys_preauth_message">
						<p><br />
						' . esc_html( $text_filter ) . '
						</p>
					</div>';
			}
		}

		if ( ! is_checkout() ) {
			$text             = array(
				'title'        => esc_html__( 'Select how you will use your credit card.', 'woocommerce-redsys' ),
				'subscription' => esc_html__( 'Add a credit card for Subscriptions', 'woocommerce-redsys' ),
				'oneclick'     => esc_html__( 'Add a credit card for Pay with 1click', 'woocommerce-redsys' ),
			);
			$the_text         = apply_filters( 'text_add_card_my_account', $text );
			$title            = $the_text['title'];
			$add_subscription = $the_text['subscription'];
			$add_oneclick     = $the_text['oneclick'];
			if ( is_wc_endpoint_url( 'add-payment-method' ) ) {
				echo '<div class="payment_method_redsys">
					<fieldset class="card-saved">';
				if ( WCRed()->subscription_plugin_exist() && 'yes' === $this->usetokens ) {
					echo '<h4>' . esc_html( $title ) . '</h4><br />
					<input type="radio" id="tokens" name="tokentype" value="tokens" checked><label for="tokens">' . ' ' . esc_html( $add_oneclick ) . '</label><br />
					<input type="radio" id="tokenr" name="tokentype" value="tokenr"><label for="tokenr">' . ' ' . esc_html( $add_subscription ) . '</label>
					';
				} elseif ( WCRed()->subscription_plugin_exist() ) {
					echo '<p>' . esc_html( $add_subscription ) . '</p><br />';
					echo '<input type="hidden" id="tokenr" name="tokentype" value="tokenr" />';
				} elseif ( 'yes' === $this->usetokens ) {
					echo '<p>' . esc_html( $add_oneclick ) . '</p><br />';
					echo '<input type="hidden" id="tokens" name="tokentype" value="tokens" />';
				}
			}
		}
		echo '</fieldset>';
		echo '</div>';
	}
	/**
	 * Redirecto to checkout.
	 */
	public function redirect_to_checkout() {
		global $wp;

		if ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) && isset( $wp->query_vars['order-pay'] ) && ! isset( $_GET['subscription_switch'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$order_key = sanitize_text_field( wp_unslash( $_GET['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_id  = ( isset( $wp->query_vars['order-pay'] ) ) ? $wp->query_vars['order-pay'] : absint( $_GET['order_id'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Recommended
			$order     = wc_get_order( $wp->query_vars['order-pay'] );

			$is_redsys = WCRed()->is_redsys_order( $order_id, 'redsys' );

			if ( $is_redsys ) {

				if ( 'yes' === $this->not_use_https ) {
					$final_notify_url = $this->notify_url_not_https;
				} else {
					$final_notify_url = $this->notify_url;
				}
				$url = WCRed()->get_url_redsys_payment( $order_id, $final_notify_url );
				wp_safe_redirect( $url );
				exit;
				/*
				if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
					WC()->cart->empty_cart( true );

					foreach ( $order->get_items() as $item_id => $line_item ) {

						unset( $_GET['item'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$order_item         = WCRed()->get_order_item( $item_id, $order );
						$product            = wc_get_product( WCRed()->get_canonical_product_id( $order_item ) );
						$product_id         = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
						$order_product_data = array(
							'_qty'          => (int) $line_item['qty'],
							'_variation_id' => (int) $line_item['variation_id'],
						);

						$variations = array();

						foreach ( $order_item['item_meta'] as $meta_key => $meta_value ) {
							$meta_value = is_array( $meta_value ) ? $meta_value[0] : $meta_value; // In WC 3.0 the meta values are no longer arrays.

							if ( taxonomy_is_product_attribute( $meta_key ) || meta_is_product_attribute( $meta_key, $meta_value, $product_id ) ) {
								$variations[ $meta_key ]           = $meta_value;
								$_POST[ 'attribute_' . $meta_key ] = $meta_value;
							}
						}

						$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $order_product_data['_qty'], $order_product_data['_variation_id'] );

						if ( $passed_validation ) {
							$cart_item_key = WC()->cart->add_to_cart( $product_id, $order_product_data['_qty'], $order_product_data['_variation_id'], $variations, array() );
						}
					}
				}
				wp_safe_redirect( wc_get_checkout_url() );
				exit;
				*/
			}
		}
	}
	/**
	 * Output for the order received page.
	 *
	 * @param int $order Order ID.
	 */
	public function receipt_page( $order ) {
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '       Once upon a time       ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '  Generating receipt_page     ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( isset( $_GET['threeDSServerTransID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$_GET["threeDSServerTransID"] receipt page' );
			}

			$ordermum             = sanitize_text_field( wp_unslash( $_GET['order'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Recommended
			$order                = WCRed()->get_order( $ordermum );
			$threeddservertransid = sanitize_text_field( wp_unslash( $_GET['threeDSServerTransID'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$ordermum: ' . $ordermum );
				$this->log->add( 'redsys', '$threeddservertransid: ' . $threeddservertransid );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}

			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$browser_user_agent = wp_unslash( $_SERVER['HTTP_USER_AGENT'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} else {
				$browser_user_agent = false;
			}

			$user_id               = get_current_user_id();
			$browser_accept_header = WCRed()->get_order_meta( $ordermum, '_accept_haders', true );
			$browser_color_depth   = WCRed()->get_order_meta( $ordermum, '_billing_profundidad_color_field', true );
			$browser_language      = WCRed()->get_order_meta( $ordermum, '_billing_idioma_navegador_field', true );
			$browser_screen_height = WCRed()->get_order_meta( $ordermum, '_billing_altura_pantalla_field', true );
			$browser_screen_width  = WCRed()->get_order_meta( $ordermum, '_billing_anchura_pantalla_field', true );
			$browser_tz            = WCRed()->get_order_meta( $ordermum, '_billing_tz_horaria_field', true );
			$java_enabled          = WCRed()->get_order_meta( $ordermum, '_billing_js_enabled_navegador_field', true );
			$protocol_version      = get_transient( 'protocolVersion_' . $ordermum );
			$merchant_cof          = get_transient( $ordermum . '_ds_merchant_cof_ini' );
			$merchant_type         = get_transient( $ordermum . '_ds_merchant_cof_type' );
			$excep_sca             = get_transient( $ordermum . '_ds_merchant_excep_sca' );
			$token_ioper           = get_transient( $ordermum . '_insite_token' );
			$merchant_identifier   = get_transient( $ordermum . '_insite_token_redsys' );
			$merchant_txnid        = get_transient( $ordermum . '_insite_token_txnid' );
			$redsys_adr            = $this->get_redsys_url_gateway_ws();
			$mi_obj                = new WooRedsysAPIWS();
			$secretsha256          = $this->get_redsys_sha256( $user_id );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$merchant_cof: ' . $merchant_cof );
				$this->log->add( 'redsys', '$merchant_type: ' . $merchant_type );
				$this->log->add( 'redsys', '$excep_sca: ' . $excep_sca );
				$this->log->add( 'redsys', '$token_ioper: ' . $token_ioper );
				$this->log->add( 'redsys', '$merchant_identifier: ' . $merchant_identifier );
				$this->log->add( 'redsys', '$merchant_txnid: ' . $merchant_txnid );

				$this->log->add( 'redsys', '$user_id: ' . $user_id );
				$this->log->add( 'redsys', '$browser_accept_header: ' . WCRed()->clean_data( $browser_accept_header ) );
				$this->log->add( 'redsys', '$browser_color_depth: ' . $browser_color_depth );
				$this->log->add( 'redsys', '$browser_language: ' . WCRed()->clean_data( $browser_language ) );
				$this->log->add( 'redsys', '$browser_screen_height: ' . WCRed()->clean_data( $browser_screen_height ) );
				$this->log->add( 'redsys', '$browser_screen_width: ' . WCRed()->clean_data( $browser_screen_width ) );
				$this->log->add( 'redsys', '$browser_tz: ' . $browser_tz );
				$this->log->add( 'redsys', '$java_enabled: ' . WCRed()->clean_data( $java_enabled ) );
				$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
				$this->log->add( 'redsys', '$merchant_cof: ' . $merchant_cof );
				$this->log->add( 'redsys', '$merchant_type: ' . $merchant_type );
				$this->log->add( 'redsys', '$excep_sca: ' . $excep_sca );
				$this->log->add( 'redsys', '$token_ioper: ' . $token_ioper );
				$this->log->add( 'redsys', '$merchant_identifier: ' . $merchant_identifier );
				$this->log->add( 'redsys', '$merchant_txnid: ' . $merchant_txnid );
				$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
				$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
				$this->log->add( 'redsys', '/****************************/' );
			}

			$order_total_sign = get_transient( $ordermum . '_insite_merchant_amount' );

			if ( $order_total_sign ) {
				$orderid2         = get_transient( $ordermum . '_insite_merchant_order' );
				$customer         = get_transient( $ordermum . '_insite_merchantcode' );
				$terminal         = get_transient( $ordermum . '_insite_terminal' );
				$transaction_type = get_transient( $ordermum . '_insite_transaction_type' );
				$currency         = get_transient( $ordermum . '_insite_currency' );
			} else {
				$orderid2                    = WCRed()->get_order_meta( $ordermum, '_temp_redsys_order_number', true );
				$customer                    = get_transient( $orderid2 . '_insite_customer' );
				$terminal                    = get_transient( $orderid2 . '_insite_terminal' );
				$currency                    = get_transient( $orderid2 . '_insite_currency' );
				$transaction_type            = get_transient( $orderid2 . '_insite_transaction_type' );
				$insite_ds_merchant_cof_ini  = get_transient( $orderid2 . '_insite_DS_MERCHANT_COF_INI' );
				$insite_ds_merchant_cof_type = get_transient( $orderid2 . '_insite_DS_MERCHANT_COF_TYPE' );
				$order_total_sign            = get_transient( $orderid2 . '_insite_redsys_amount' );

			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$orderid2: ' . $orderid2 );
				$this->log->add( 'redsys', '$customer: ' . $customer );
				$this->log->add( 'redsys', '$terminal: ' . $terminal );
				$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
				$this->log->add( 'redsys', '$currency: ' . $currency );
				$this->log->add( 'redsys', '$insite_ds_merchant_cof_ini: ' . $insite_ds_merchant_cof_ini );
				$this->log->add( 'redsys', '$insite_ds_merchant_cof_type: ' . $insite_ds_merchant_cof_type );
				$this->log->add( 'redsys', '$token_ioper: ' . $token_ioper );
				$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$this->log->add( 'redsys', '$merchant_cof: ' . $merchant_cof );
				$this->log->add( 'redsys', '$merchant_type: ' . $merchant_type );
			}

			if ( ( 'no' === $merchant_identifier || empty( $merchant_identifier ) ) && ! empty( $merchant_cof ) ) {
				$merchant_identifier = 'REQUIRED';
			}
			if ( 'no' !== $merchant_identifier && ! empty( $merchant_identifier ) ) {
				$merchant_identifier_d = '<DS_MERCHANT_IDENTIFIER>' . $merchant_identifier . '</DS_MERCHANT_IDENTIFIER>';
			} else {
				$merchant_identifier_d = '';
			}
			if ( $merchant_cof ) {
				$merchant_cof = '<DS_MERCHANT_COF_INI>' . $merchant_cof . '</DS_MERCHANT_COF_INI>';
			} else {
				$merchant_cof = '';
			}
			if ( $merchant_type ) {
				$merchant_type = '<DS_MERCHANT_COF_TYPE>' . $merchant_type . '</DS_MERCHANT_COF_TYPE>';
			} else {
				$merchant_type = '';
			}
			if ( $merchant_txnid ) {
				$merchant_txnid = '<DS_MERCHANT_COF_TXNID>' . $merchant_txnid . '</DS_MERCHANT_COF_TXNID>';
			} else {
				$merchant_txnid = '';
			}

			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}

			$datos_usuario = array(
				'threeDSInfo'              => 'AuthenticationData',
				'protocolVersion'          => $protocol_version,
				'browserAcceptHeader'      => WCRed()->clean_data( $browser_accept_header ),
				'browserColorDepth'        => $browser_color_depth,
				'browserIP'                => (string) WCRed()->get_the_ip(),
				'browserJavaEnabled'       => WCRed()->clean_data( $java_enabled ),
				'browserJavascriptEnabled' => 'enabled',
				'browserLanguage'          => WCRed()->clean_data( $browser_language ),
				'browserScreenHeight'      => WCRed()->clean_data( $browser_screen_height ),
				'browserScreenWidth'       => WCRed()->clean_data( $browser_screen_width ),
				'browserTZ'                => $browser_tz,
				'threeDSServerTransID'     => (string) $threeddservertransid,
				'browserUserAgent'         => $browser_user_agent,
				'notificationURL'          => (string) $final_notify_url,
				'threeDSCompInd'           => (string) 'Y',
			);
			$acctinfo      = WCPSD2()->get_acctinfo( $order, $datos_usuario );

			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= $merchant_identifier_d; // '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= $merchant_cof; // '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= $merchant_type; // '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= $merchant_txnid; // '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			// $datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The call  5          ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $datos_entrada );
				$this->log->add( 'redsys', ' ' );
			}

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML             ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'trataPeticion 11: ' . $xml );
				$this->log->add( 'redsys', ' ' );
			}

			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuestaeds      = json_decode( $xml_retorno->OPERACION->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$three_ds_info     = trim( $respuestaeds->threeDSInfo ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$protocol_version  = trim( $respuestaeds->protocolVersion ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$acs_url           = trim( $respuestaeds->acsURL ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$par_eq            = trim( $respuestaeds->{'PAReq'} );
				$md                = trim( $respuestaeds->MD ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$creq              = trim( $respuestaeds->{'creq'} );
				$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$ordermi           = trim( $xml_retorno->OPERACION->Ds_Order ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$dstermnal         = trim( $xml_retorno->OPERACION->Ds_Terminal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$dscardcountry     = trim( $xml_retorno->OPERACION->Ds_Card_Country ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$operacion         = trim( $xml_retorno->CODIGO ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno 15: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', 'Ds_EMV3DS: ' . $xml_retorno->OPERACION->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$this->log->add( 'redsys', 'threeDSInfo: ' . $three_ds_info );
				$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
				$this->log->add( 'redsys', '$acs_url: ' . $acs_url );
				$this->log->add( 'redsys', '$par_eq: ' . $par_eq );
				$this->log->add( 'redsys', '$md: ' . $md );
				$this->log->add( 'redsys', '$creq: ' . $creq );
				$this->log->add( 'redsys', '$authorisationcode: ' . $authorisationcode );
				$this->log->add( 'redsys', '$operacion: ' . $operacion );
			}
			if ( 'ChallengeRequest' === $three_ds_info ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Challenge' );
				}
				WCRed()->print_overlay_image();
				if ( $par_eq ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$par_eq' );
						$this->log->add( 'redsys', '$acs_url: ' . $acs_url );
					}
					?>
					<form method="POST" action="<?php echo esc_url( $acs_url ); ?>"  enctype = "application/x-www-form-urlencoded">
						<input type="hidden" name="PaReq" value="<?php echo esc_attr( $par_eq ); ?>" />
						<input type="hidden" name="MD" value="<?php echo esc_attr( $md ); ?>" />
						<input type="hidden" name="TermUrl" value="<?php echo esc_attr( $final_notify_url ); ?>" />
						<input name="submit_3ds" type="submit" class="button-alt" id="submit_pareq" value="<?php esc_html__( 'Press here if you are not redirected', 'woocommerce-redsys' ); ?>" />
					</form>
					<script type="text/javascript">
						document.getElementById('submit_pareq').click();
					</script>
					<?php
					exit();
				}
				if ( $creq ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '$creq' );
						$this->log->add( 'redsys', '$acsUR: ' . esc_url( $acs_url ) );
						$this->log->add( 'redsys', '$creq: ' . esc_html( $creq ) );
					}
					?>
					<form method="POST" action="<?php echo esc_url( $acs_url ); ?>" enctype="application/x-www-form-urlencoded">
						<input type="hidden" name="creq" value="<?php echo esc_html( $creq ); ?>" />
						<input name="submit_3ds" type="submit" class="button-alt" id="submit_creq" value="<?php __( 'Press here if you are not redirected', 'woocommerce-redsys' ); ?>" />
					</form>
					<script type="text/javascript">
						document.getElementById('submit_creq').click();
					</script>
					<?php
					exit();
				}
			}
			if ( ! empty( $authorisationcode ) ) {
				echo 'La operción ha sido aceptado y el número de autorización es: ' . esc_html( $authorisationcode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '$ordermi: ' . $ordermi );
				}
				$order_id = WCRed()->clean_order_number( $ordermi );
				$order    = WCRed()->get_order( $order_id );
				$url_ok   = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
				$url_ko   = $order->get_cancel_order_url();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '$url_ok: ' . $url_ok );
					$this->log->add( 'redsys', '$url_ko: ' . $url_ko );
				}
				if ( ! empty( $ordermi ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $ordermi );
				}
				if ( ! empty( $dsdate ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				}
				if ( ! empty( $dshour ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
				}
				if ( ! empty( $dscardcountry ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_card_country_insite', $dscardcountry );
				}
				if ( ! empty( $dscargtype ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_card_type_insite', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				}
				if ( ! empty( $dstermnal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				}
				// Payment completed.
				$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorisation code: ', 'woocommerce-redsys' ) . $authorisationcode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 13' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Payment complete.' );
				}
				$needs_preauth = WCRed()->order_needs_preauth( $order->id );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				wp_safe_redirect( $url_ok );
				exit;
			}
			$error = WCRed()->get_error( $operacion );
			wc_add_notice( $error, 'error' );
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}

		$three_ds_info    = get_transient( 'threeDSInfo_' . $order );
		$protocol_version = get_transient( 'protocolVersion_' . $order );

		if ( '2.1.0' === $protocol_version ) {

			$three_ds_server_trans_id = get_transient( 'threeDSServerTransID_' . $order );
			$final_notify_url         = get_transient( 'final_notify_url_' . $order );
			$three_ds_method_url      = get_transient( 'threeDSMethodURL_' . $order );
			$acsurl                   = get_transient( $order . '_insite_acsurl' );
			$data                     = array();
			$data                     = array(
				'threeDSServerTransID'         => $three_ds_server_trans_id,
				'threeDSMethodNotificationURL' => $final_notify_url,
			);
			$json                     = base64_encode( wp_json_encode( $data ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			$creq                     = trim( get_transient( 'creq_' . $order ) );
			$acsurl2                  = get_transient( 'acsURL_' . $order );
			$json                     = base64_encode( wp_json_encode( $data ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			if ( $creq ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Doing Creq Form POST ' );
				}
				?>
				<form method="POST" action="<?php echo esc_url( $acsurl2 ); ?>" enctype="application/x-www-form-urlencoded">
					<input type="hidden" name="creq" value="<?php echo esc_html( $creq ); ?>" />
					<input name="submit_3ds" type="submit" class="button-alt" id="submit_creq" value="<?php __( 'Press here if you are not redirected', 'woocommerce-redsys' ); ?>" />
				</form>
				<script type="text/javascript">
					document.getElementById('submit_creq').click();
				</script>
				<?php
				exit();
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'NO Hay Creq Form POST ' );
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
				jQuery("#submit_redsys_payment_form_3").click();
				'
				);
				echo '<form id="3DSform" method="POST" action="' . esc_url( $three_ds_method_url ) . '" target="_top">
				<input type="hidden" name="threeDSMethodData" value="' . esc_html( $json ) . '" />
				<input type="submit" class="button-alt" id="submit_redsys_payment_form_3" value="' . esc_html__( 'Press here if you are not redirected', 'woocommerce-redsys' ) . '" />
				</form>';
			}
		} elseif ( 'ChallengeRequest' === $three_ds_info ) {
			if ( '2.2.0' === $protocol_version ) {
				$creq    = get_transient( 'creq_' . $order );
				$acsurl2 = get_transient( 'acsURL_' . $order );

				if ( $creq ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Doing Creq Form POST 2.2.0 ' );
						$this->log->add( 'redsys', '$acsurl2: ' . $acsurl2 );
						$this->log->add( 'redsys', '$creq: ' . $creq );
					}
					?>
				<form method="POST" action="<?php echo esc_url( $acsurl2 ); ?>" enctype="application/x-www-form-urlencoded">
					<input type="hidden" name="creq" value="<?php echo esc_html( $creq ); ?>" />
					<input name="submit_3ds" type="submit" class="button-alt" id="submit_creq" value="<?php __( 'Press here if you are not redirected', 'woocommerce-redsys' ); ?>" />
				</form>
				<script type="text/javascript">
					document.getElementById('submit_creq').click();
				</script>
					<?php
					exit();
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '         Hay Challenge        ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				// Hay challenge.
				$acs_url = trim( get_transient( 'acsURL_' . $order ) );
				$par_eq  = trim( get_transient( 'PAReq_' . $order ) );
				$md      = trim( get_transient( 'MD_' . $order ) );
				if ( 'yes' === $this->not_use_https ) {
					$final_notify_url = $this->notify_url_not_https;
				} else {
					$final_notify_url = $this->notify_url;
				}
				wc_enqueue_js(
					'
			$("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/select2-spinner.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to Redsys to make the payment.', 'woocommerce-redsys' ) . '",
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
				echo '<form action="' . esc_url( $acs_url ) . '" method="post" id="redsys_payment_form" target="_top">
			<input type="hidden" name="PaReq" value="' . esc_attr( $par_eq ) . '" />
			<input type="hidden" name="MD" value="' . esc_attr( $md ) . '" />
			<input type="hidden" name="TermUrl" value="' . esc_attr( $final_notify_url ) . '" />
			<input type="submit" class="button-alt" id="submit_redsys_payment_form_2" value="' . esc_html__( 'Pay with Redsys', 'woocommerce-redsys' ) . '" />
		</form>';
			}
		} else {

			WCRed()->update_order_meta( $order, '_order_number_redsys_woocommerce', $temp_order_number );
			set_transient( $temp_order_number . '_woocommrce_order_number_redsys', $order );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$temp_order_number: ' . $temp_order_number );
				$this->log->add( 'redsys', '$do_challenge: ' . $do_challenge );
				$this->log->add( 'redsys', '$_POST: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( isset( $_GET['returnfronredsys'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( isset( $_POST['MD'] ) && isset( $_POST['PaRes'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Missing

					$fuc            = $this->customer;
					$currency_codes = WCRed()->get_currencies();
					$terminal       = $this->terminal;
					$currency       = $currency_codes[ get_woocommerce_currency() ];
					if ( WCRed()->order_needs_preauth( $order ) ) {
						$transaction_type = '1';
					} else {
						$transaction_type = '0';
					}

					$order_id          = $order;
					$merchan_name      = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
					$merchant_lastnme  = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );
					$temp_order_number = WCRed()->get_order_meta( $order, '_temp_redsys_order_number', true );
					$redsys_order_id   = WCRed()->get_redsys_order_number( $order_id );
					$order             = WCRed()->get_order( $order_id );
					$amount            = $order->get_total();
					$redsys_amount     = WCRed()->redsys_amount_format( $amount );
					$merchant_module   = 'WooCommerce_Redsys_Gateway_' . REDSYS_VERSION . '_WooCommerce.com';
					$user_id           = $order->get_user_id();
					$secretsha256      = $this->get_redsys_sha256( $user_id );
					$redsys_adr        = $this->get_redsys_url_gateway( $user_id );

					$temp_order_number = WCRed()->get_order_meta( $order_id, '_temp_redsys_order_number', true );
					delete_transient( $temp_order_number . '_do_redsys_challenge' );
					$token = get_transient( $order_id . '_insite_token' );

					$md     = sanitize_text_field( wp_unslash( $_POST['MD'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$pares  = sanitize_text_field( wp_unslash( $_POST['PaRes'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$emv3ds = '{"threeDSInfo":"ChallengeResponse","protocolVersion":"1.0.2","PARes":"' . $pares . '","MD":"' . $md . '"}';

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '$fuc: ' . $fuc );
						$this->log->add( 'redsys', '$terminal: ' . $terminal );
						$this->log->add( 'redsys', '$currency: ' . $currency );
						$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
						$this->log->add( 'redsys', '$order_id: ' . $order_id );
						$this->log->add( 'redsys', '$merchan_name: ' . $merchan_name );
						$this->log->add( 'redsys', '$redsys_order_id: ' . $redsys_order_id );
						$this->log->add( 'redsys', '$temp_order_number: ' . $temp_order_number );
						$this->log->add( 'redsys', '$amount: ' . $amount );
						$this->log->add( 'redsys', '$redsys_amount: ' . $redsys_amount );
						$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
						$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
						$this->log->add( 'redsys', '$md: ' . $md );
						$this->log->add( 'redsys', '$pares: ' . $pares );
						$this->log->add( 'redsys', '$emv3ds: ' . $emv3ds );
						$this->log->add( 'redsys', '$token: ' . $token );
					}
					if ( class_exists( 'ISAuthenticationMessage' ) ) {
						$request = new ISAuthenticationMessage();
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'ISAuthenticationMessage: NOT DEFINED' );
						}
					}
					if ( 'yes' === $this->debug ) {
						$set_order            = method_exists( $request, 'setOrder' );
						$set_amount           = method_exists( $request, 'setAmount' );
						$set_currency         = method_exists( $request, 'setCurrency' );
						$set_merchant         = method_exists( $request, 'setMerchant' );
						$set_terminal         = method_exists( $request, 'setTerminal' );
						$set_transaction_type = method_exists( $request, 'setTransactionType' );
						$add_emv_parameters   = method_exists( $request, 'addEmvParameters' );
						$add_emv_parameter    = method_exists( $request, 'addEmvParameter' );

						if ( $set_order ) {
							$this->log->add( 'redsys', 'METHOD $set_order: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_order: NOT EXIST' );
						}

						if ( $set_amount ) {
							$this->log->add( 'redsys', 'METHOD $set_amount: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_amount: NOT EXIST' );
						}

						if ( $set_currency ) {
							$this->log->add( 'redsys', 'METHOD $set_currency: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_currency: NOT EXIST' );
						}

						if ( $set_merchant ) {
							$this->log->add( 'redsys', 'METHOD $set_merchant: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_merchant: NOT EXIST' );
						}

						if ( $set_terminal ) {
							$this->log->add( 'redsys', 'METHOD $set_terminal: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_terminal: NOT EXIST' );
						}

						if ( $set_transaction_type ) {
							$this->log->add( 'redsys', 'METHOD $set_transaction_type: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $set_transaction_type: NOT EXIST' );
						}

						if ( $add_emv_parameters ) {
							$this->log->add( 'redsys', 'METHOD $add_emv_parameters: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $add_emv_parameters: NOT EXIST' );
						}

						if ( $add_emv_parameter ) {
							$this->log->add( 'redsys', 'METHOD $add_emv_parameter: EXIST' );
						} else {
							$this->log->add( 'redsys', 'METHOD $add_emv_parameter: NOT EXIST' );
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
						$this->log->add( 'redsys', '$resultado: ' . $resultado );
					}

					if ( 'OK' === $resultado ) {
						$location = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
						wp_safe_redirect( esc_url( $location ) );
						exit;
					} else {
						echo esc_html__( 'There was a problem:', 'woocommerce-redsys' ) . ' ' . esc_html( $resultado );
					}
				} else {
					echo 'Error';
				}
			}

			if ( isset( $_GET['challenge'] ) || 'yes' === $do_challenge ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['challenge'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$challenge = sanitize_text_field( wp_unslash( $_GET['challenge'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '$_GET["challenge"]: is SET' );
					}
				} else {
					$challenge = 'no';
				}
				if ( 'yes' === $challenge || 'yes' === $do_challenge ) {
					$temp_order_number = WCRed()->get_order_meta( $order, '_temp_redsys_order_number', true );
					$order2            = WCRed()->get_order( $order );
					$redirectok        = $order2->get_checkout_payment_url( true ) . '&returnfronredsys=yes';
					$acsurl            = get_transient( $order . '_insite_acsurl' );
					$pareq             = get_transient( $order . '_insite_pareq' );
					$md                = get_transient( $order . '_insite_md' );

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', '$temp_order_number: ' . $temp_order_number );
						$this->log->add( 'redsys', '$order2: ' . $order2 );
						$this->log->add( 'redsys', '$redirectok: ' . $redirectok );
						$this->log->add( 'redsys', '$acsurl: ' . $acsurl );
						$this->log->add( 'redsys', '$pareq: ' . $pareq );
						$this->log->add( 'redsys', '$md: ' . $md );
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
			<input type="hidden" name="TermUrl" value="' . esc_url( $redirectok ) . '">
			<input type="hidden" name="MD" value="' . esc_attr( $md ) . '" />
			<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . esc_html__( 'Press here if you are not redirected', 'woocommerce-redsys' ) . '" />
			</form>';
				}
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function add_payment_method() {

		$user_id    = get_current_user_id();
		$token_type = sanitize_text_field( wp_unslash( $_POST['tokentype'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/******************************/' );
			$this->log->add( 'redsys', ' $_POST: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', ' $user_id: ' . $user_id );
			$this->log->add( 'redsys', ' $token_type: ' . $token_type );
			$this->log->add( 'redsys', '/******************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		return array(
			'result'   => '',
			'redirect' => WCRed()->get_url_add_payment_method( $this->id, $user_id, $token_type ),
		);
	}
	/**
	 * Redsys hadler.
	 *
	 * @param Array $wp Array.
	 */
	public static function redsys_handle_requests( $wp ) {
		global $woocommerce;
		if ( isset( $_GET['redsys-payment-method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$redsys_payment_method = sanitize_text_field( wp_unslash( $_GET['redsys-payment-method'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$redsys_gateway        = sanitize_text_field( wp_unslash( $_GET['redsys-gateway'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$redsys_token          = sanitize_text_field( wp_unslash( $_GET['redsys-token'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		}
		if ( isset( $redsys_payment_method ) ) {
			$user_id = get_transient( $redsys_payment_method );
			wc_nocache_headers();
			// Clean the API request.
			$api_request = strtolower( wc_clean( $redsys_payment_method ) );
			$gateway     = strtolower( wc_clean( $redsys_gateway ) );
			$user_id     = get_transient( $redsys_payment_method );
			$redsys_adr  = self::get_redsys_url_gateway_p( $user_id );
			$redsys_args = self::get_redsys_args_add_method( $redsys_payment_method );
			$form_inputs = array();
			foreach ( $redsys_args as $key => $value ) {
				$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
			}
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
			echo '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
			' . implode( '', $form_inputs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			. '
			<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . esc_html__( 'Pay with Credit Card via Servired/RedSys', 'woocommerce-redsys' ) . '" />
			<a class="button cancel" href="' . esc_url( wc_get_endpoint_url( 'add-payment-method' ) ) . '">' . esc_html__( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
			</form>';
		}
	}
	/**
	 * Check redsys IPN validity
	 **/
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function check_ipn_request_is_valid() {

		if ( isset( $_GET['redsys-step'] ) && 'cancel' === $_GET['redsys-step'] ) {
			$order_id  = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
			$order     = wc_get_order( $order_id );
			$transient = get_transient( $order_id . '_iframe' );
			if ( 'returncancel' === $this->wooredsysurlko ) {
				$redirect = $order->get_cancel_order_url();
			} else {
				$redirect = wc_get_checkout_url();
			}
			if ( 'yes' === $transient ) {
				delete_transient( $order_id . '_iframe' );
				echo '<script>window.top.location.href = "' . esc_url( $redirect ) . '"</script>';
				exit();
			}
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '  Starting check IPN Request  ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', ' ' );
		}

		if ( isset( $_GET['threeDSMethodURL'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'check_ipn_request_is_valid > $_GET["threeDSMethodURL"]' );
			}
			$order_id                 = sanitize_text_field( wp_unslash( $_GET['order'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$three_ds_info            = get_transient( 'threeDSInfo_' . $order_id );
			$accept_headers           = get_transient( 'accept_headers_' . $order_id );
			$protocol_version         = get_transient( 'protocolVersion_' . $order_id );
			$acs_url                  = get_transient( 'acsURL_' . $order_id );
			$three_ds_server_trans_id = get_transient( 'threeDSServerTransID_' . $order_id );
			$three_ds_method_url      = get_transient( 'threeDSMethodURL_' . $order_id );
			$amount                   = get_transient( 'amount_' . $order_id );
			$order                    = get_transient( 'order_' . $order_id );
			$terminal                 = get_transient( 'terminal_' . $order_id );
			$currency                 = get_transient( 'currency_' . $order_id );
			$identifier               = get_transient( 'identifier_' . $order_id );
			$cof_ini                  = get_transient( 'cof_ini_' . $order_id );
			$cof_type                 = get_transient( 'cof_type_' . $order_id );
			$cof_txnid                = get_transient( 'cof_txnid_' . $order_id );
			$final_notify_url         = get_transient( 'final_notify_url_' . $order_id );
			$token_redsys             = get_transient( 'redys_token' . $order_id );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '     IS threeDSMethodURL      ' );
				$this->log->add( 'redsys', '$order_id: ' . $order_id );
				$this->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
				$this->log->add( 'redsys', '$accept_headers: ' . $accept_headers );
				$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
				$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
				$this->log->add( 'redsys', '$three_ds_method_url: ' . $three_ds_method_url );
				$this->log->add( 'redsys', '$amount: ' . $amount );
				$this->log->add( 'redsys', '$order: ' . $order );
				$this->log->add( 'redsys', '$terminal: ' . $terminal );
				$this->log->add( 'redsys', '$currency: ' . $currency );
				$this->log->add( 'redsys', '$identifier: ' . $identifier );
				$this->log->add( 'redsys', '$cof_ini: ' . $cof_ini );
				$this->log->add( 'redsys', '$cof_type: ' . $cof_type );
				$this->log->add( 'redsys', '$cof_txnid: ' . $cof_txnid );
				$this->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
				$this->log->add( 'redsys', '$token_redsys: ' . $token_redsys );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( ! empty( $three_ds_server_trans_id ) && ! empty( $three_ds_method_url ) ) {

				WCRed()->print_overlay_image();
				WCRed()->do_make_3dmethod( $order_id );
				?>
				<script type="text/javascript">
					document.getElementById('submit_redsys_3ds_method').click();
				</script>
				<?php
			}
			echo 'Es una llamada threeDSMethodURL';
		}

		if ( isset( $_POST['threeDSMethodData'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '   Es IPN threeDSMethodData   ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			return true;
		}

		if ( isset( $_POST['PaRes'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '         Es IPN PaRes         ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			return true;
		}

		$usesecretsha256  = $this->secretsha256;
		$customtestsha256 = $this->customtestsha256;
		$testsha256       = $this->testsha256;

		if ( $usesecretsha256 || $customtestsha256 || $testsha256 ) {
			if ( ! isset( $_POST['Ds_SignatureVersion'] ) || ! isset( $_POST['Ds_MerchantParameters'] ) || ! isset( $_POST['Ds_Signature'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
			$version           = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$data              = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$remote_sign       = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$mi_obj            = new WooRedsysAPI();
			$decodec           = $mi_obj->decodeMerchantParameters( $data );
			$order_id          = $mi_obj->getParameter( 'Ds_Order' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_text_field( $order_id ) );
			$is_get_method     = get_transient( $order_id . '_get_method' );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = WCRed()->get_order_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $is_get_method ) {
				return true;
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'redsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'redsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'redsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->getParameter( 'Ds_Order' );
				$this->log->add( 'redsys', 'Order ID: ' . $order_id );
			}
			$order           = WCRed()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'redsys', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'redsys', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'redsys', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'redsys', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', 'Using $secretsha256 Transcient' );
					$this->log->add( 'redsys', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'redsys', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'redsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( isset( $_POST['Ds_MerchantCode'] ) && $_POST['Ds_MerchantCode'] === $this->customer ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Received INVALID notification from Servired/RedSys' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '== End check IPN Request ==' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/******************************************/' );
			$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
			$this->log->add( 'redsys', '/******************************************/' );
			$this->log->add( 'redsys', ' ' );
		}
	}
	/**
	 * Check for CRES
	 *
	 * @param  array $post Post data.
	 */
	public function check_confirm_cres( $post ) {
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '           Is CRES            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		$cres                     = sanitize_text_field( wp_unslash( $_POST['cres'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$decoded                  = (string) rtrim( strtr( base64_decode( $cres ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$json_data                = stripslashes( html_entity_decode( $decoded ) );
		$deco_json                = json_decode( $json_data );
		$three_ds_server_trans_id = (string) $deco_json->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$acs_trans_id             = (string) $deco_json->acsTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$message_type             = (string) $deco_json->messageType; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$message_version          = (string) $deco_json->messageVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$trans_status             = (string) $deco_json->transStatus; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$order_id                 = get_transient( $three_ds_server_trans_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$cress: ' . $cres );
			$this->log->add( 'redsys', '$decoded: ' . $decoded );
			$this->log->add( 'redsys', '$json_data: ' . $json_data );
			$this->log->add( 'redsys', '$deco_json: ' . print_r( $deco_json, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
			$this->log->add( 'redsys', '$acs_trans_id: ' . $acs_trans_id );
			$this->log->add( 'redsys', '$message_type: ' . $message_type );
			$this->log->add( 'redsys', '$message_version: ' . $message_version );
			$this->log->add( 'redsys', '$trans_status: ' . $trans_status );
			$this->log->add( 'redsys', ' ' );
		}

		$user_id             = get_current_user_id();
		$protocol_version    = get_transient( 'protocolVersion_' . $order_id );
		$merchant_cof        = get_transient( $order_id . '_ds_merchant_cof_ini' );
		$merchant_type       = get_transient( $order_id . '_ds_merchant_cof_type' );
		$excep_sca           = get_transient( $order_id . '_ds_merchant_excep_sca' );
		$token_ioper         = get_transient( $order_id . '_insite_token' );
		$merchant_identifier = get_transient( $order_id . '_insite_token_redsys' );
		$merchant_txnid      = get_transient( $order_id . '_insite_token_txnid' );
		$redsys_adr          = $this->get_redsys_url_gateway_ws();
		$mi_obj              = new WooRedsysAPIWS();
		$secretsha256        = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '           Is CRES            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
			$this->log->add( 'redsys', '$merchant_cof: ' . $merchant_cof );
			$this->log->add( 'redsys', '$merchant_type: ' . $merchant_type );
			$this->log->add( 'redsys', '$excep_sca: ' . $excep_sca );
			$this->log->add( 'redsys', '$token_ioper: ' . $token_ioper );
			$this->log->add( 'redsys', '$merchant_identifier: ' . $merchant_identifier );
			$this->log->add( 'redsys', '$merchant_txnid: ' . $merchant_txnid );
			$this->log->add( 'redsys', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'redsys', '/****************************/' );
		}

		if ( $token_ioper ) {
			$token_ioper = '<DS_MERCHANT_IDOPER>' . $merchant_identifier . '</DS_MERCHANT_IDOPER>';
		} else {
			$token_ioper = '';
		}
		if ( 'no' !== $merchant_identifier && ! empty( $merchant_identifier ) ) {
			$merchant_identifier_d = '<DS_MERCHANT_IDENTIFIER>' . $merchant_identifier . '</DS_MERCHANT_IDENTIFIER>';
		} else {
			$merchant_identifier_d = '';
		}
		if ( $merchant_cof ) {
			$merchant_cof_d = '<DS_MERCHANT_COF_INI>' . $merchant_cof . '</DS_MERCHANT_COF_INI>';
		} else {
			$merchant_cof_d = '';
		}
		if ( $merchant_type ) {
			$merchant_type_d = '<DS_MERCHANT_COF_TYPE>' . $merchant_type . '</DS_MERCHANT_COF_TYPE>';
		} else {
			$merchant_type_d = '';
		}
		if ( $merchant_txnid ) {
			$merchant_txnid_d = '<DS_MERCHANT_COF_TXNID>' . $merchant_txnid . '</DS_MERCHANT_COF_TXNID>';
		} else {
			$merchant_txnid_d = '';
		}
		$order_total_sign = get_transient( $order_id . '_insite_merchant_amount' );
		$orderid2         = get_transient( $order_id . '_insite_merchant_order' );
		$customer         = get_transient( $order_id . '_insite_merchantcode' );
		$terminal         = get_transient( $order_id . '_insite_terminal' );
		$transaction_type = get_transient( $order_id . '_insite_transaction_type' );
		$currency         = get_transient( $order_id . '_insite_currency' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '           Is CRES            ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'redsys', '$orderid2: ' . $orderid2 );
			$this->log->add( 'redsys', '$customer: ' . $customer );
			$this->log->add( 'redsys', '$terminal: ' . $terminal );
			$this->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'redsys', '$currency: ' . $currency );
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}

		$response3ds      = array(
			'threeDSInfo'     => 'ChallengeResponse',
			'protocolVersion' => $message_version,
			'cres'            => $cres,
		);
		$response3ds_json = wp_json_encode( $response3ds );

		if ( $merchant_identifier && $merchant_txnid ) {
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= $merchant_identifier_d; // '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= $merchant_cof_d; // '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= $merchant_type_d;// '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= $merchant_txnid_d;// '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			// $datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $response3ds_json . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The call  6          ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $datos_entrada );
				$this->log->add( 'redsys', ' ' );
			}

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML             ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $xml );
				$this->log->add( 'redsys', ' ' );
			}

			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( isset( $xml_retorno->OPERACION->Ds_EMV3DS ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$respuestaeds = json_decode( $xml_retorno->OPERACION->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$respuestaeds = false;
				}
				if ( isset( $xml_retorno->CODIGO ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$codigo = false;
				}
				if ( isset( $xml_retorno->OPERACION->Ds_Order ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ordermi = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$ordermi = false;
				}
				if ( isset( $xml_retorno->OPERACION->Ds_Terminal ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$dstermnal = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$dstermnal = false;
				}
				if ( isset( $respuestaeds->threeDSInfo ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$three_ds_info = trim( $respuestaeds->threeDSInfo ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$three_ds_info = false;
				}
				if ( isset( $respuestaeds->protocolVersion ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$protocol_version = trim( $respuestaeds->protocolVersion ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$protocol_version = false;
				}
				if ( isset( $respuestaeds->acsURL ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$acs_url = trim( $respuestaeds->acsURL ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$acs_url = false;
				}
				if ( isset( $respuestaeds->{ 'PAReq'} ) ) {
					$par_eq = trim( $respuestaeds->{ 'PAReq'} );
				} else {
					$par_eq = false;
				}
				if ( isset( $respuestaeds->MD ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$md = trim( $respuestaeds->MD ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$md = false;
				}
				if ( isset( $respuestaeds->{ 'creq'} ) ) {
					$creq = trim( $respuestaeds->{ 'creq'} );
				} else {
					$creq = false;
				}
				if ( isset( $xml_retorno->OPERACION->Ds_AuthorisationCode ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$authorisationcode = trim( $xml_retorno->OPERACION->Ds_AuthorisationCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$authorisationcode = false;
				}
				$dsdate = date( get_option( 'date_format' ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$dshour = date( 'H:i:s', current_time( 'timestamp', 0 ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested,WordPress.DateTime.RestrictedFunctions.date_date
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno 16: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', 'Ds_EMV3DS: ' . $xml_retorno->OPERACION->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$this->log->add( 'redsys', '$codigo: ' . $codigo );
				$this->log->add( 'redsys', 'threeDSInfo: ' . $three_ds_info );
				$this->log->add( 'redsys', '$protocol_version: ' . $protocol_version );
				$this->log->add( 'redsys', '$acs_url: ' . $acs_url );
				$this->log->add( 'redsys', '$par_eq: ' . $par_eq );
				$this->log->add( 'redsys', '$md: ' . $md );
				$this->log->add( 'redsys', '$creq: ' . $creq );
				$this->log->add( 'redsys', '$authorisationcode: ' . $authorisationcode );
			}

			if ( $authorisationcode ) {
				$order  = WCRed()->get_order( $order_id );
				$url_ok = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
				$url_ko = $order->get_cancel_order_url();
				if ( ! empty( $ordermi ) ) {
					WCRed()->update_order_meta( $order->id, '_payment_order_number_redsys', $ordermi );
				}
				if ( ! empty( $dsdate ) ) {
					WCRed()->update_order_meta( $order->id, '_payment_date_redsys', $dsdate );
				}
				if ( ! empty( $dshour ) ) {
					WCRed()->update_order_meta( $order->id, '_payment_hour_redsys', $dshour );
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->id, '_authorisation_code_redsys', $authorisationcode );
				}
				if ( ! empty( $dscardcountry ) ) {
					WCRed()->update_order_meta( $order->id, '_card_country_insite', $dscardcountry );
				}
				if ( ! empty( $dscargtype ) ) {
					WCRed()->update_order_meta( $order->id, '_card_type_insite', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				}
				if ( ! empty( $dstermnal ) ) {
					WCRed()->update_order_meta( $order->id, '_payment_terminal_redsys', $dstermnal );
				}
				// Payment completed.
				$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorisation code: ', 'woocommerce-redsys' ) . $authorisationcode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 14' );
				}
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Payment complete.' );
				}
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				wp_safe_redirect( $url_ok );
				exit;
			}
			if ( $codigo ) {
				$order = WCRed()->get_order( $order_id );
				$error = WCRed()->get_error( $codigo );
				$order->add_order_note( esc_html__( 'There was a problem with this order. The Error was', 'woocommerce-redsys' ) . esc_html( $error ) );
				do_action( 'redsys_post_payment_error', $order->get_id(), $error );
				wp_safe_redirect( $url_ko );
				exit;
			} else {
				wp_safe_redirect( $url_ko );
				exit;
			}
		} else {
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= $merchant_identifier_d; // '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= $merchant_cof_d; // '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= $merchant_type_d;// '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= $merchant_txnid_d;// '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			// $datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $response3ds_json . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The call  7          ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $datos_entrada );
				$this->log->add( 'redsys', ' ' );
			}

			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '          The XML             ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', $xml );
				$this->log->add( 'redsys', ' ' );
			}

			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( isset( $xml_retorno->OPERACION->Ds_Merchant_Identifier ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xpiration_date = (string) $xml_retorno->OPERACION->Ds_ExpiryDate; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$identifier     = (string) $xml_retorno->OPERACION->Ds_Merchant_Identifier; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$dscardbrand    = (string) $xml_retorno->OPERACION->Ds_Card_Brand; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$number         = (string) $xml_retorno->OPERACION->Ds_Card_Number; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$number2        = (string) $xml_retorno->OPERACION->Ds_CardNumber; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} else {
					$identifier = false;
				}
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$xml_retorno 17: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$authorisationcode: ' . $authorisationcode );
				$this->log->add( 'redsys', '$codigo: ' . $codigo );
				$this->log->add( 'redsys', '$redsys_order: ' . $redsys_order );
				$this->log->add( 'redsys', '$terminal: ' . $terminal );
				$this->log->add( 'redsys', '$currency_code: ' . $currency_code );
			}

			if ( $authorisationcode ) {
				$order  = WCRed()->get_order( $order_id );
				$url_ok = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
				$url_ko = $order->get_cancel_order_url();
				if ( ! empty( $ordermi ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $ordermi );
				}
				if ( ! empty( $dsdate ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				}
				if ( ! empty( $dshour ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				}
				if ( ! empty( $authorisationcode ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
				}
				if ( ! empty( $dscardcountry ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_card_country_insite', $dscardcountry );
				}
				if ( ! empty( $dscargtype ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_card_type_insite', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				}
				if ( ! empty( $dstermnal ) ) {
					WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				}
				// Payment completed.
				$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
				$order->add_order_note( __( 'Authorisation code: ', 'woocommerce-redsys' ) . $authorisationcode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete() 15' );
				}
				$order->payment_complete();
				do_action( 'redsys_post_payment_complete', $order->get_id() );
				$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
				if ( $needs_preauth ) {
					$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Payment complete.' );
				}
				if ( $identifier ) {
					$user_id       = $order->get_user_id();
					$dsexpiryyear  = '20' . substr( $xpiration_date, 0, 2 );
					$dsexpirymonth = substr( $xpiration_date, -2 );
					$dscardbrand   = WCRed()->get_card_brand( $dscardbrand );
					$dscardnumber4 = WCRed()->get_last_four( $number, $number2 );

					if ( 'C' === $merchant_type ) {
						$token_type = 'C';
					} else {
						$token_type = 'R';
					}

					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Saving Credit Card $dsexpirymonth.' );
						$this->log->add( 'redsys', '$user_id: ' . $user_id );
						$this->log->add( 'redsys', '$dsexpirymonth: ' . $dsexpirymonth );
						$this->log->add( 'redsys', '$dsexpiryyear: ' . $dsexpiryyear );
						$this->log->add( 'redsys', '$dscardbrand: ' . $dscardbrand );
						$this->log->add( 'redsys', '$identifier: ' . $identifier );
						$this->log->add( 'redsys', '$txnid: ' . $txnid );
						$this->log->add( 'redsys', '$token_type: ' . $token_type );

					}

					$token = new WC_Payment_Token_CC();
					$token->set_token( $identifier );
					$token->set_gateway_id( 'redsys' );
					$token->set_user_id( $user_id );
					$token->set_card_type( $dscardbrand );
					$token->set_last4( $dscardnumber4 );
					$token->set_expiry_month( $dsexpirymonth );
					$token->set_expiry_year( $dsexpiryyear );
					$token->set_default( true );
					$token->save();
					$token_id = $token->get_id();
					WCRed()->set_txnid( $token_id, $txnid );
					WCRed()->set_token_type( $token_id, $token_type );
				}
				wp_safe_redirect( $url_ok );
				exit;
			}
		}
	}
	/**
	 * Check for PaRes
	 *
	 * @param array $post $_POST data.
	 */
	public function check_confirm_pares( $post ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '           Is PaRes           ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		$pares                = sanitize_text_field( wp_unslash( $_POST['PaRes'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$md                   = sanitize_text_field( wp_unslash( $_POST['MD'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$order_id             = get_transient( $md );
		$order                = WCRed()->get_order( $order_id );
		$user_id              = $order->get_user_id();
		$type                 = 'ws';
		$redsys_adr           = $this->get_redsys_url_gateway( $user_id, $type );
		$order_total_sign     = get_transient( 'amount_' . $md );
		$orderid2             = get_transient( 'order_' . $md );
		$customer             = get_transient( 'merchantcode_' . $md );
		$ds_merchant_terminal = get_transient( 'terminal_' . $md );
		$currency             = get_transient( 'currency_' . $md );
		$customer_token_c     = get_transient( 'identifier_' . $md );
		$cof_ini              = get_transient( 'cof_ini_' . $md );
		$cof_type             = get_transient( 'cof_type_' . $md );
		$cof_txnid            = get_transient( 'cof_txnid_' . $md );
		$mi_obj               = new WooRedsysAPIWS();
		$secretsha256         = $this->get_redsys_sha256( $user_id );
		$url_ok               = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$needed               = wp_json_encode(
			array(
				'threeDSInfo'     => 'ChallengeResponse',
				'MD'              => $md,
				'protocolVersion' => '1.0.2',
				'PARes'           => $pares,
			)
		);

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '$pares: ' . $pares );
			$this->log->add( 'redsys', '$order_id: ' . $order_id );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$needs_preauth = WCRed()->order_needs_preauth( $order->get_id() );
		if ( $needs_preauth ) {
			$dstransactiontype = '1';
		} else {
			$dstransactiontype = '0';
		}

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $dstransactiontype . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
		$datos_entrada .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
		$datos_entrada .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
		$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
		$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $needed . '</DS_MERCHANT_EMV3DS>';
		$datos_entrada .= '</DATOSENTRADA>';
		$xml            = '<REQUEST>';
		$xml           .= $datos_entrada;
		$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml           .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$datos_entrada 3: ' . $datos_entrada );
			$this->log->add( 'redsys', '$xml: ' . $xml );
			$this->log->add( 'redsys', ' ' );
		}

		$cliente    = new SoapClient( $redsys_adr );
		$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' $responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuesta = (int) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( ( $respuesta >= 0 ) && ( $respuesta <= 99 ) ) {
					$auth_code = $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', 'Response: Ok > ' . $respuesta );
						$this->log->add( 'redsys', 'Authorization code: ' . $auth_code );
						$this->log->add( 'redsys', ' ' );
					}
					$auth_code        = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ds_order         = trim( $xml_retorno->OPERACION->Ds_Order ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ds_merchant_code = trim( $xml_retorno->OPERACION->Ds_MerchantCode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ds_terminal      = trim( $xml_retorno->OPERACION->Ds_Terminal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
					$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $auth_code );
					WCRed()->update_order_meta( $order_id, '_authorisation_code_redsys', $auth_code );
					WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
					$order->payment_complete();
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'payment_complete 16' );
					}
					if ( '1' === $dstransactiontype ) {
						$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', '      Saving Order Meta       ' );
						$this->log->add( 'redsys', '/****************************/' );
						$this->log->add( 'redsys', ' ' );
					}

					if ( ! empty( $ds_order ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $ds_order );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $ds_order );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $dsdate ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_date_redsys saved: ' . $dsdate );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_date_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $ds_terminal ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $ds_terminal );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $ds_terminal );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $dshour ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_payment_hour_redsys saved: ' . $dshour );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_payment_hour_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $auth_code ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $auth_code );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $auth_code );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					// This meta is essential for later use.
					if ( ! empty( $secretsha256 ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', ' ' );
							$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$this->log->add( 'redsys', ' ' );
						}
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check for Servired/RedSys HTTP Notification
	 *
	 * @return void
	 */
	public function check_ipn_response() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '      check_ipn_response      ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}
		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$post = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['cres'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			WCRed()->print_overlay_image();
			$this->check_confirm_cres( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
		if ( isset( $_GET['redsys-order-id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_id = sanitize_text_field( wp_unslash( $_GET['redsys-order-id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wc_nocache_headers();
			$order       = WCRed()->get_order( $order_id );
			$user_id     = $order->get_user_id();
			$redsys_adr  = self::get_redsys_url_gateway_p( $user_id );
			$redsys_args = self::get_redsys_args_p( $order );
			$form_inputs = array();

			foreach ( $redsys_args as $key => $value ) {
				$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
			}

			if ( ! isset( $_GET['redsys-iframe'] ) || 'yes' !== $_GET['redsys-iframe'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				echo '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
				' . implode( '', $form_inputs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				. '
				<input type="hidden" class="button-alt" id="submit_redsys_payment_form" value="' . esc_html__( 'Pay with Credit Card via Servired/RedSys', 'woocommerce-redsys' ) . '" />
				</form>';
				echo '<script>document.getElementById("redsys_payment_form").submit();</script>';
				exit();
			} else {
				// get_header();
				echo '
				<style type="text/css">
					body {
						margin:0;
					}
					.redsys-iframe-container {
						width:100vw;
						height:1000px;
						margin:0 !important;
						padding:0 !important;
						text-align:center !important;
					}
					iframe {
						position: absolute;
						left: 0px;
						display: block;       /* iframes are inline by default */
						border: none;         /* Reset default border */
						width:100vw !important;
						height:1000px;
						margin:0 !important;
						padding:0 !important;
						text-align:center !important;
						margin-left: 0px !important;
						max-width: 100vw !important;
						z-index: 999999999;
					}
				</style>
				
				<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="redsysiframe">
				' . implode( '', $form_inputs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				. '
				</form>
					<iframe name="redsysiframe" src="" class="iframe_3DS_Challenge" allowfullscreen></iframe>';
				echo '<script>document.getElementById("redsys_payment_form").submit();</script>';
				// get_footer();
				exit();
			}
		}

		if ( isset( $_POST['threeDSMethodData'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'check_ipn_request_is_valid > $_POST["threeDSMethodData"]' );
			}
			$json_datos_3d_secure = (string) sanitize_text_field( wp_unslash( $_POST['threeDSMethodData'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$decoded              = (string) rtrim( strtr( base64_decode( $json_datos_3d_secure ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$json_data            = stripslashes( html_entity_decode( $decoded ) );
			$deco_json            = json_decode( $json_data );
			$order_id             = get_transient( $deco_json->threeDSServerTransID ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$order                = WCRed()->get_order( $order_id );
			$url                  = $order->get_checkout_payment_url( true ) . '&threeDSServerTransID=' . $deco_json->threeDSServerTransID . '&order=' . $order_id; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$deco_json: ' . print_r( $deco_json, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', '$order_id: ' . $order_id );
				$this->log->add( 'redsys', '$url: ' . $url );
			}
			wp_safe_redirect( $url );
			exit;
		}
		if ( isset( $_POST['PaRes'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$result = $this->check_confirm_pares( $post );

			if ( $result ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '      Pares confirmado        ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				$md       = sanitize_text_field( wp_unslash( $_POST['MD'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				$order_id = get_transient( $md );
				$order    = WCRed()->get_order( $order_id );
				$url_ok   = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
				echo '<script>window.top.location.href = "' . esc_url( $url_ok ) . '"</script>';
				exit();
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', '     Pares NO confirmado      ' );
					$this->log->add( 'redsys', '/****************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				echo 'Something was wrong';
				exit();
			}
		} else {
			if ( $this->check_ipn_request_is_valid() ) {
				header( 'HTTP/1.1 200 OK' );
				do_action( 'valid-redsys-standard-ipn-request', $post ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			} else {
				wp_die( 'There is nothing to see here, do not access this page directly (Redsys redirection)' );
			}
		}
	}
	/**
	 * Successful Payment.
	 *
	 * @param array $posted Post data after notify.
	 */
	public function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', '      successful_request      ' );
			$this->log->add( 'redsys', '/****************************/' );
			$this->log->add( 'redsys', ' ' );
		}

		$version              = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$data                 = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$remote_sign          = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		if ( isset( $_POST['threeDSMethodData'] ) ) {
			$three_ds_method_data = sanitize_text_field( wp_unslash( $_POST['threeDSMethodData'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		} else {
			$three_ds_method_data = '';
		}
		$mi_obj            = new WooRedsysAPI();
		$usesecretsha256   = $this->secretsha256;
		$dscardnumbercompl = '';
		$dsexpiration      = '';
		$dsmerchantidenti  = '';
		$dscardnumber4     = '';
		$dsexpiryyear      = '';
		$dsexpirymonth     = '';
		$user_id           = '';
		$decodedata        = $mi_obj->decodeMerchantParameters( $data );
		$localsecret       = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
		$total             = $mi_obj->getParameter( 'Ds_Amount' );
		$ordermi           = $mi_obj->getParameter( 'Ds_Order' );
		$dscode            = $mi_obj->getParameter( 'Ds_MerchantCode' );
		$currency_code     = $mi_obj->getParameter( 'Ds_Currency' );
		$response          = $mi_obj->getParameter( 'Ds_Response' );
		$id_trans          = $mi_obj->getParameter( 'Ds_AuthorisationCode' );
		$dsdate            = htmlspecialchars_decode( $mi_obj->getParameter( 'Ds_Date' ) );
		$dshour            = htmlspecialchars_decode( $mi_obj->getParameter( 'Ds_Hour' ) );
		$dstermnal         = $mi_obj->getParameter( 'Ds_Terminal' );
		$dsmerchandata     = $mi_obj->getParameter( 'Ds_MerchantData' );
		$dssucurepayment   = $mi_obj->getParameter( 'Ds_SecurePayment' );
		$dscardcountry     = $mi_obj->getParameter( 'Ds_Card_Country' );
		$dsconsumercountry = $mi_obj->getParameter( 'Ds_ConsumerLanguage' );
		$dstransactiontype = $mi_obj->getParameter( 'Ds_TransactionType' );
		$dsmerchantidenti  = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );
		$dscardbrand       = $mi_obj->getParameter( 'Ds_Card_Brand' );
		$dsmechandata      = $mi_obj->getParameter( 'Ds_MerchantData' );
		$dscargtype        = $mi_obj->getParameter( 'Ds_Card_Type' );
		$dserrorcode       = $mi_obj->getParameter( 'Ds_ErrorCode' );
		$dpaymethod        = $mi_obj->getParameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia. Si se paga por Iupay o TC, no se utiliza.
		$response          = (int) $response;
		$secretsha256      = get_transient( 'redsys_signature_' . sanitize_text_field( $ordermi ) );
		$is_add_method     = get_transient( $ordermi . '_get_method' );
		$order1            = $ordermi;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$version: ' . $version );
			$this->log->add( 'redsys', '$data: ' . $data );
			$this->log->add( 'redsys', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'redsys', '$us$threeDSMethodDataer_id: ' . $three_ds_method_data );
			$this->log->add( 'redsys', '$usesecretsha256: ' . $usesecretsha256 );
			$this->log->add( 'redsys', '$total: ' . $total );
			$this->log->add( 'redsys', '$ordermi: ' . $ordermi );
			$this->log->add( 'redsys', '$dscode: ' . $dscode );
			$this->log->add( 'redsys', '$currency_code: ' . $currency_code );
			$this->log->add( 'redsys', '$response: ' . $response );
			$this->log->add( 'redsys', '$id_trans: ' . $id_trans );
			$this->log->add( 'redsys', '$dsdate: ' . $dsdate );
			$this->log->add( 'redsys', '$dshour: ' . $dshour );
			$this->log->add( 'redsys', '$dstermnal: ' . $dstermnal );
			$this->log->add( 'redsys', '$dsmerchandata: ' . $dsmerchandata );
			$this->log->add( 'redsys', '$dssucurepayment: ' . $dssucurepayment );
			$this->log->add( 'redsys', '$dscardcountry: ' . $dscardcountry );
			$this->log->add( 'redsys', '$dsconsumercountry: ' . $dsconsumercountry );
			$this->log->add( 'redsys', '$dstransactiontype: ' . $dstransactiontype );
			$this->log->add( 'redsys', '$dsmerchantidenti: ' . $dsmerchantidenti );
			$this->log->add( 'redsys', '$dscardbrand: ' . $dscardbrand );
			$this->log->add( 'redsys', '$dsmechandata: ' . $dsmechandata );
			$this->log->add( 'redsys', '$dscargtype: ' . $dscargtype );
			$this->log->add( 'redsys', '$dserrorcode: ' . $dserrorcode );
			$this->log->add( 'redsys', '$dpaymethod: ' . $dpaymethod );
			$this->log->add( 'redsys', '$response: ' . $response );
			$this->log->add( 'redsys', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'redsys', '$is_add_method: ' . $is_add_method );
			$this->log->add( 'redsys', '$order1: ' . $order1 );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '$ordermi: ' . $ordermi );
			$this->log->add( 'redsys', '$is_add_method: ' . $is_add_method );
		}
		if ( 'yes' !== $is_add_method ) {
			$order2  = WCRed()->clean_order_number( $order1 );
			$order   = WCRed()->get_order( (int) $order2 );
			$user_id = $order->get_user_id();
			sleep( 3 );
			$is_paid = WCRed()->is_paid( $order->get_id() );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$order2: ' . $order2 );
				$this->log->add( 'redsys', '$user_id: ' . $user_id );
			}
		}

		if ( $user_id && (int) $user_id > 0 ) {
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
		}

		delete_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		delete_transient( $ordermi . '_get_method' );

		if ( 'yes' === $is_add_method ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/*****************************/' );
				$this->log->add( 'redsys', ' User is Adding a Credit Card  ' );
				$this->log->add( 'redsys', '/*****************************/' );
				$this->log->add( 'redsys', ' ' );
			}

			$dscardbrand      = WCRed()->get_card_brand( $dscardbrand );
			$dsexpiration     = $mi_obj->getParameter( 'Ds_ExpiryDate' );
			$dsmerchantidenti = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );
			$number           = $mi_obj->getParameter( 'Ds_Card_Number' );
			$number2          = $mi_obj->getParameter( 'Ds_CardNumber' );
			$dscardnumber4    = WCRed()->get_last_four( $number, $number2 );
			$dsexpiryyear     = '20' . substr( $dsexpiration, 0, 2 );
			$dsexpirymonth    = substr( $dsexpiration, -2 );
			$redsys_txnid     = $mi_obj->getParameter( 'Ds_Merchant_Cof_Txnid' );
			$token_type       = get_transient( $ordermi . '_token_type' );
			$user_id          = get_transient( $ordermi );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$dsexpiryyear: ' . $dsexpiryyear );
				$this->log->add( 'redsys', '$dsexpirymonth: ' . $dsexpirymonth );
				$this->log->add( 'redsys', ' ' );
			}
			if ( empty( $dsexpiryyear ) || '20' === $dsexpiryyear || '2020' === $dsexpiryyear ) {
				$dsexpiryyear  = '2099';
				$dsexpirymonth = '12';
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$token->set_token( $dsmerchantidenti ): ' . $dsmerchantidenti );
				$this->log->add( 'redsys', '$token->set_gateway_id( "redsys" ): redsys' );
				$this->log->add( 'redsys', '$token->set_user_id( $user_id ): ' . $user_id );
				$this->log->add( 'redsys', '$token->set_card_type( $dscardbrand ): ' . $dscardbrand );
				$this->log->add( 'redsys', '$token->set_last4( $dscardnumber4 ): ' . $dscardnumber4 );
				$this->log->add( 'redsys', '$token->set_expiry_month( $dsexpirymonth ): ' . $dsexpirymonth );
				$this->log->add( 'redsys', '$token->set_expiry_year( $dsexpiryyear ): ' . $dsexpiryyear );
				$this->log->add( 'redsys', '/*****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( 'tokenr' === $token_type ) {
				$token_type = 'R';
			} else {
				$token_type = 'C';
			}
			$token = new WC_Payment_Token_CC();
			$token->set_token( $dsmerchantidenti );
			$token->set_gateway_id( 'redsys' );
			$token->set_user_id( $user_id );
			$token->set_card_type( $dscardbrand );
			$token->set_last4( $dscardnumber4 );
			$token->set_expiry_month( $dsexpirymonth );
			$token->set_expiry_year( $dsexpiryyear );
			$token->set_default( true );
			$token->save();
			$token_id = $token->get_id();
			WCRed()->set_txnid( $token_id, $redsys_txnid );
			WCRed()->set_token_type( $token_id, $token_type );
			delete_transient( $ordermi );
			return;
		}

		if ( ! empty( $three_ds_method_data ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' Is successful_request IF $three_ds_method_data' );
				$this->log->add( 'redsys', '/******************************************/' );
			}
			$decoded_post_json        = base64_decode( $three_ds_method_data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$decoded_post             = json_decode( $decoded_post_json );
			$three_ds_server_trans_id = $decoded_post->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$order2                   = get_transient( $three_ds_server_trans_id );
			$order                    = WCRed()->get_order( (int) $order2 );
			$user_id                  = $order->get_user_id();
			$protocol_version         = get_transient( 'protocolVersion_' . $order2 );
			$agente_navegador         = WCPSD2()->get_agente_navegador( $order2 );
			$idioma_navegador         = WCPSD2()->get_idioma_navegador( $order2 );
			$altura_pantalla          = WCPSD2()->get_altura_pantalla( $order2 );
			$anchura_pantalla         = WCPSD2()->get_anchura_pantalla( $order2 );
			$profundidad_color        = WCPSD2()->get_profundidad_color( $order2 );
			$diferencia_horaria       = WCPSD2()->get_diferencia_horaria( $order2 );
			$accept_headers           = WCPSD2()->get_accept_headers( $order2 );
			$javaenabled              = WCPSD2()->get_browserjavaenabled( $order2 );
			$type                     = 'ws';
			$redsys_adr               = $this->get_redsys_url_gateway( $user_id, $type );
			if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
			} else {
				$final_notify_url = $this->notify_url;
			}

			$user_ip       = WCRed()->get_the_ip();
			$datos_usuario = array(
				'threeDSInfo'          => 'AuthenticationData',
				'protocolVersion'      => $protocol_version,
				'browserAcceptHeader'  => $accept_headers,
				'browserColorDepth'    => $profundidad_color,
				'browserIP'            => $user_ip,
				'browserJavaEnabled'   => $javaenabled,
				'browserLanguage'      => $idioma_navegador,
				'browserScreenHeight'  => $altura_pantalla,
				'browserScreenWidth'   => $anchura_pantalla,
				'browserTZ'            => $diferencia_horaria,
				'browserUserAgent'     => $agente_navegador,
				'threeDSServerTransID' => $three_ds_server_trans_id,
				'notificationURL'      => $final_notify_url,
				'threeDSCompInd'       => 'Y',
			);
			$acctinfo      = WCPSD2()->get_acctinfo( $order, $datos_usuario, $user_id );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$user_id: ' . $user_id );
				$this->log->add( 'redsys', '$order_id: ' . $order2 );
				$this->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
				$this->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
				$this->log->add( 'redsys', 'browserIP: ' . $user_ip );
				$this->log->add( 'redsys', 'browserJavaEnabled: ' . $javaenabled );
				$this->log->add( 'redsys', 'browserLanguage: ' . $idioma_navegador );
				$this->log->add( 'redsys', 'browserScreenHeight: ' . $altura_pantalla );
				$this->log->add( 'redsys', 'browserScreenWidth: ' . $anchura_pantalla );
				$this->log->add( 'redsys', 'browserTZ: ' . $agente_navegador );
				$this->log->add( 'redsys', 'browserUserAgent: ' . $agente_navegador );
				$this->log->add( 'redsys', 'threeDSServerTransID: ' . $three_ds_server_trans_id );
				$this->log->add( 'redsys', 'notificationURL: ' . $final_notify_url );
				$this->log->add( 'redsys', 'threeDSCompInd: : Y' );
				$this->log->add( 'redsys', 'acctInfo: : ' . $acctinfo );
			}
			$order_total_sign     = get_transient( 'amount_' . $order2 );
			$orderid2             = get_transient( 'order_' . $order2 );
			$customer             = $this->customer;
			$ds_merchant_terminal = get_transient( 'terminal_' . $order2 );
			$currency             = get_transient( 'currency_' . $order2 );
			$customer_token_c     = get_transient( 'identifier_' . $order2 );
			$cof_ini              = get_transient( 'cof_ini_' . $order2 );
			$cof_type             = get_transient( 'cof_type_' . $order2 );
			$cof_txnid            = get_transient( 'cof_txnid_' . $order2 );

			$mi_obj = new WooRedsysAPIWS();

			if ( ! empty( $this->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}
			$secretsha256   = $this->get_redsys_sha256( $user_id );
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= $ds_merchant_group;
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token_c . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>' . $cof_ini . '</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>' . $cof_type . '</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $cof_txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>' . $acctinfo . '</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';
			$xml            = '<REQUEST>';
			$xml           .= $datos_entrada;
			$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml           .= '</REQUEST>';

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'The XM 12L: ' . $xml );
			}

			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$this->log->add( 'redsys', ' ' );
			}
		}
		if ( WCRed()->order_contains_subscription( $order->get_id() ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' This is a subscription order ' );
				$this->log->add( 'redsys', '/****************************/' );
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', ' This is NOT a subscription order ' );
				$this->log->add( 'redsys', '/********************************/' );
			}
		}

		// refund.

		if ( '3' === $dstransactiontype ) {
			if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Is refund' );
			}
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'WCRed()->update_order_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded by Redsys', 'woocommerce-redsys' ) );
				WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_refund_redsys', $id_trans );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woocommerce-redsys' ) );
			exit;
		}

		if ( $is_paid ) {
			exit();
		}

		// Confirm Preauthorization.
		if ( '2' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Response 900 (confirmed preauthorization)' );
				}
				set_transient( $order->get_id() . '_redsys_preauth', 'yes' );
				$order->add_order_note( __( 'Confirmed Order Preauthorization', 'woocommerce-redsys' ) );
				$order->update_status( 'completed', __( 'Order Completed', 'woocommerce-redsys' ) );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'WCRed()->update_order_meta to "Complete"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'New Status in request: ' . $status );
				}
				exit;
			}
			$order->add_order_note( __( 'Redsys return an error confirming preauthorization', 'woocommerce-redsys' ) );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			exit;
		}

		$is_order_paid = WCRed()->get_order_meta( $order->get_id(), '_redsys_done', true );

		if ( 'yes' === $is_order_paid ) {
			return;
		}

		$save_token = get_transient( $order->get_id() . '_redsys_save_token' );

		if ( WCRed()->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable && (int) $response <= 99 || ( ( 'yes' === $this->usetokens ) && ( ! empty( $dsmerchantidenti ) ) && ( '3' !== $dstransactiontype ) && ( '2' !== $dstransactiontype ) && ( 'yes' !== $this->redsysdirectdeb ) && $response <= 99 ) ) {
			if ( WCRed()->check_order_has_sumo_subscriptions( $order->get_id() ) ) {
				if ( function_exists( 'sumo_save_subscription_payment_info' ) ) {
					sumo_save_subscription_payment_info(
						$order->get_id(),
						array(
							'payment_type'         => 'auto',
							'payment_method'       => 'redsys',
							'payment_key'          => $user_id, // Optional. Default it is empty for Manual Payments. Required for Automatic payments(In order to capture the future payments)
							'payment_start_date'   => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
							'payment_end_date'     => '', // Optional. Default empty. Use only if it is needed by Payment Gateway
							'payment_order_amount' => $order->get_total(),
						)
					);
				}
			}
			$number           = $mi_obj->getParameter( 'Ds_Card_Number' );
			$number2          = $mi_obj->getParameter( 'Ds_CardNumber' );
			$dscardnumber4    = WCRed()->get_last_four( $number, $number2 );
			$dscardbrand      = WCRed()->get_card_brand( $dscardbrand );
			$dsexpiration     = $mi_obj->getParameter( 'Ds_ExpiryDate' );
			$dsmerchantidenti = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );

			if ( empty( $dsexpiration ) || empty( $dscardbrand2 ) || empty( $dscardnumbercompl ) && $this->notiemail ) {
				$to      = $this->notiemail;
				$subject = __( 'There is a little problem:', 'woocommerce-redsys' );
				$body    = __( 'You need to ask to Redsys to sent some fields for tokenization (Pay with one Click & Subscriptions). Please ask to Redsys to sent with the callback the following fields. This fields will help your customers differentiate their credit cards', 'woocommerce-redsys' );
				$body   .= '<p>Ds_Card_Brand</p>';
				$body   .= '<p>Ds_ExpiryDate</p>';
				$body   .= '<p>Ds_Card_Number</p>';
				$body   .= '<p>Some times you need to ask to your Bank and not to Redsys</p>';
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				if ( 'yes' === $this->sendemailsdscard ) {
					wp_mail( $to, $subject, $body, $headers );
				}
			}
			$dsexpiryyear  = '20' . substr( $dsexpiration, 0, 2 );
			$dsexpirymonth = substr( $dsexpiration, -2 );
			$redsys_txnid  = $mi_obj->getParameter( 'Ds_Merchant_Cof_Txnid' );
			$token_type    = 'R';
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'redsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'redsys', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'redsys', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'redsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'redsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'redsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'redsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'redsys', 'Ds_Response: ' . $response );
			$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'redsys', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'redsys', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'redsys', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'redsys', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'redsys', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'redsys', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'redsys', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'redsys', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'redsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'redsys', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'redsys', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'redsys', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'redsys', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'redsys', 'Ds_PayMethod: ' . $dpaymethod );
			if ( ! empty( $redsys_txnid ) ) {
				$this->log->add( 'redsys', 'Ds_Merchant_Cof_Txnid: ' . $redsys_txnid );
			}
			$this->log->add( 'redsys', '$token_type: R' );
		}

		if ( '0' !== (string) $dstransactiontype && '1' !== (string) $dstransactiontype ) {
			return;
		}

		if ( ! empty( $dscardnumbercomp ) ) {
			$dscardnumbercomp = $dscardnumbercomp;
		} else {
			$dscardnumbercomp = 'unknown';
		}

		if ( ! empty( $dsexpiryyear ) && '2020' !== $dsexpiryyear && '20' !== $dsexpiryyear ) {
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
			$this->log->add( 'redsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'redsys', 'Ds_Order: ' . $order1 );
			$this->log->add( 'redsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'redsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'redsys', 'Ds_Response: ' . $response );
			$this->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'redsys', '$order2: ' . $order2 );
			$this->log->add( 'redsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'redsys', 'Ds_Card_Number: ' . $dscardnumbercomp );
			$this->log->add( 'redsys', 'Ds_ExpiryDate: ' . $dsexpiration );
			$this->log->add( 'redsys', 'Ds_Merchant_Identifier: ' . $dsmerchantidenti );
			$this->log->add( 'redsys', '$dscardnumber4: ' . $dscardnumber4 );
			$this->log->add( 'redsys', '$dsexpiryyear: ' . $dsexpiryyear );
			$this->log->add( 'redsys', '$dsexpirymonth: ' . $dsexpirymonth );
			$this->log->add( 'redsys', 'all data: ' . $decodedata );
			$this->log->add( 'redsys', 'Response: ' . $response );
		}

		if ( (int) $response <= 99 ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$response: <= 99' );
			}
			// authorized.
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			$order_total_compare = ltrim( $order_total_compare, '0' );
			$total               = ltrim( $total, '0' );
			if ( 'partial-payment' !== $order->get_status() ) {
				if ( $order_total_compare !== $total ) {
					// amount does not match.
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
					}
					// Put this order on-hold for manual checking.
					$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2$s).', 'woocommerce-redsys' ), $order_total_compare, $total ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					exit;
				}
			} else {
				set_transient( $order->get_id() . '_redsys_collect', 'yes' );
			}
			$contais_subscription = WCRed()->order_contains_subscription( $order->get_id() );

			if ( $contais_subscription ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Order has subscription' );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'Order has not subscription' );
				}
			}
			if ( ( ( WCRed()->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable ) ) || ( ( 'yes' === $this->usetokens ) && ( ! empty( $dsmerchantidenti ) ) && ( '0' === $dsmechandata || '1' === $dsmechandata ) ) ) {
				if ( WCRed()->order_contains_subscription( $order->get_id() ) && 'yes' !== $this->subsusetokensdisable ) {
					$user_id = $order->get_user_id();
					$tokens  = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Is a Subscription' );
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Is a PSD2 Subscription' );
					}

					if ( 'yes' === $save_token ) {
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
							$this->log->add( 'redsys', '$redsys_txnid: ' . $redsys_txnid );
							$this->log->add( 'redsys', '$token_type: R' );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'Dont Save Token' );
						}
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', 'Token 1 clic' );
					}
					$user_id = $order->get_user_id();
					$tokens  = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
					if ( 'yes' === $save_token ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'NO existe Token C' );
							$this->log->add( 'redsys', 'Vamos a guardarlo' );
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
							$this->log->add( 'redsys', '$redsys_txnid: ' . $redsys_txnid );
							$this->log->add( 'redsys', '$token_type: C' );
						}
					} else {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'redsys', 'Dont Save Token' );
						}
					}
				}
			}

			$authorisation_code = $id_trans;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '      Saving Order Meta       ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( ! empty( $order1 ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_payment_date_redsys saved: ' . $dsdate );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_payment_date_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dshour ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_payment_hour_redsys saved: ' . $dshour );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_payment_hour_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $id_trans ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $currency_code ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dscardcountry ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_card_country_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dscode ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_order_fuc_redsys', $dscode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_order_fuc_redsys: ' . $dscode );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_order_fuc_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			if ( ! empty( $dscargtype ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_card_type_redsys saved: ' . $dscargtype );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_card_type_redsys NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			// This meta is essential for later use.
			if ( ! empty( $secretsha256 ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
					$this->log->add( 'redsys', ' ' );
				}
			}
			// Payment completed.
			if ( '1' === $dstransactiontype && 'D' !== $dpaymethod && 'R' !== $dpaymethod ) {
				$order->add_order_note( __( 'HTTP Notification received - Transaction Preauthorized', 'woocommerce-redsys' ) );
			} elseif ( 'D' === $dpaymethod ) {
				$order->add_order_note( __( 'HTTP Notification received - Resident payment', 'woocommerce-redsys' ) );
			} else {
				$order->add_order_note( __( 'HTTP Notification received - Payment completed', 'woocommerce-redsys' ) );
			}
			$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisation_code );

			if ( '1' === $dstransactiontype && 'D' !== $dpaymethod && 'R' !== $dpaymethod ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				$order->payment_complete( $order->get_id() );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete 17' );
				}
				$order->update_status( 'redsys-pre', __( 'Preauthorized by Redsys', 'woocommerce-redsys' ) );
			} elseif ( ! empty( $dpaymethod ) && 'D' === $dpaymethod ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				$order->payment_complete( $order->get_id() );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete 18' );
				}
				$order->update_status( 'redsys-residentp', __( 'Resident Payment', 'woocommerce-redsys' ) );
			} elseif ( 'completed' === $this->orderdo ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				$order->update_status( 'completed', __( 'Order Completed by Redsys', 'woocommerce-redsys' ) );
			} else {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
				$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', 'payment_complete 19' );
				}
			}
			do_action( 'resdys_post_payment', $ordermi );

			if ( 'yes' === $this->debug && '1' === $dstransactiontype ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', '  Order Preauthorized by Redsys   ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			} elseif ( 'yes' === $this->debug && 'D' === $dpaymethod ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '      Resident Payment        ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			} elseif ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '     Payment Complete         ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
		} else {

			$order_id          = $order->get_id();
			$admin_url         = admin_url();
			$url_to_order      = WCRed()->get_order_edit_url( $order_id );
			$ds_response_value = WCRed()->get_error( $response );
			$ds_error_value    = WCRed()->get_error( $dserrorcode );

			if ( $ds_response_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_response_value );
				WCRed()->update_order_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_response_value );
			}

			if ( $ds_error_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_error_value );
				WCRed()->update_order_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_error_value );
			}

			if ( 'yes' === $this->debug ) {
				if ( $ds_response_value ) {
					$this->log->add( 'redsys', $ds_response_value );
				}
				if ( $ds_error_value ) {
					$this->log->add( 'redsys', $ds_error_value );
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
				$message = __( '⚠️ A customer has had payment errors: ', 'woocommerce-redsys' ) . $ds_response_value . '. Check order for detais: ' . $url_to_order . ' Check the email for more datails.';
				WCRed()->push( $message );
				wp_mail( $to, $subject, $body, $headers );
			}

			if ( 'yes' === $this->sentemailscustomers ) {

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', ' Sending email Error  to customer ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', ' ' );
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
						$subject = esc_html__( 'Credit Cart Payment Problem at ', 'woocommerce-redsys' ) . get_bloginfo( 'name' );
					}
					if ( array_key_exists( 'heading', $redsys_email_customer_options ) && ! empty( $redsys_email_customer_options['heading'] ) ) {
						$heading = $redsys_email_customer_options['heading'];
					} else {
						$heading = esc_html__( 'Credit Cart Payment Problem', 'woocommerce-redsys' );
					}
				}
				$email_name = get_option( 'woocommerce_email_from_name' );
				$email_from = get_option( 'woocommerce_email_from_address' );
				$headers[]  = 'Content-Type: text/html; charset=UTF-8';
				$headers[]  = 'From: ' . $email_name . ' <' . $email_from . '>';

				$mailer = WC()->mailer();
				$order  = new wc_order( $order_id );

				$message  = '<p>' . esc_html__( 'Thank you very much for shopping in our store.', 'woocommerce-redsys' ) . '</p>';
				$message .= '<p>' . esc_html__( 'There was a problem with the credit card payment.', 'woocommerce-redsys' ) . '</p>';
				$message .= '<p>' . esc_html__( 'If you don\'t know what the error was.', 'woocommerce-redsys' ) . '<br />';

				if ( ! empty( $ds_error_value ) ) {
					$message .= __( 'The error was: ', 'woocommerce-redsys' ) . $ds_error_value . '</p>';
				}
				if ( ! empty( $ds_response_value ) ) {
					$message .= __( 'The error was: ', 'woocommerce-redsys' ) . $ds_response_value . '</p>';
				}
				$message .= '<p>' . esc_html__( 'If you wish, you can try again at this link: ', 'woocommerce-redsys' ) . wc_get_checkout_url() . '</p>';
				$message .= '<p>' . esc_html__( 'Thank you very much for choosing us.', 'woocommerce-redsys' ) . '</p>';

				$message         = apply_filters( 'redsys_sent_email_customer_pay_error', $message, $ds_error_value, $ds_response_value );
				$heading         = esc_html__( 'Credit Cart Payment Problem', 'woocommerce-redsys' );
				$email           = $order->get_billing_email();
				$wrapped_message = $mailer->wrap_message( $heading, $message );
				$wc_email        = new Redsys_Customer_Email_Order();
				$html_message    = $wc_email->style_inline( $wrapped_message );
				$subject         = esc_html__( 'There was a problem with the credit card payment.', 'woocommerce-redsys' );

				wp_mail( $email, $subject, $html_message, $headers );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', '   Email Error to customer sent   ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', ' ' );
				}
			}

			if ( $this->wooredsysurlko ) {
				if ( 'returncancel' === $this->wooredsysurlko ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/********************************/' );
						$this->log->add( 'redsys', '          Order Cancelled         ' );
						$this->log->add( 'redsys', '/********************************/' );
						$this->log->add( 'redsys', ' ' );
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$this->log->add( 'redsys', '/******************************************/' );
						$this->log->add( 'redsys', ' ' );
					}
					$order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woocommerce-redsys' ) );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', '          Order Cancelled         ' );
					$this->log->add( 'redsys', '/********************************/' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				// $order->update_status( 'cancelled', __( 'Cancelled by Redsys', 'woocommerce-redsys' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', '          Order Cancelled         ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( $ds_response_value ) {
				wc_add_notice( $ds_response_value, 'error' );
			}
			if ( $ds_error_value ) {
				wc_add_notice( $ds_error_value, 'error' );
			}
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
	}
	/**
	 * Ask for refund
	 *
	 * @param  int    $order_id Order ID.
	 * @param  string $transaction_id Transaction ID.
	 * @param  float  $amount Amount.
	 * @return bool
	 */
	public function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = WCRed()->get_order( $order_id );
		$terminal       = WCRed()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );
		$commerce_fuc   = WCRed()->get_order_meta( $order_id, '_order_fuc_redsys', true );

		if ( ! $commerce_fuc ) {
			$commerce_fuc = $this->customer;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', '/**************************/' );
			$this->log->add( 'redsys', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', '/**************************/' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'redsys', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'redsys', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		}
		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		$redsys_adr        = $this->get_redsys_url_gateway( $user_id );
		$autorization_code = WCRed()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = WCRed()->get_order_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = WCRed()->get_order_meta( $order_id, '_corruncy_code_redsys', true );
		$merchan_name      = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', '**********************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'redsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'redsys', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );
			$this->log->add( 'redsys', __( 'FUC : ', 'woocommerce-redsys' ) . $commerce_fuc );
		}

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new WooRedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $commerce_fuc );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', '*********************************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $commerce_fuc );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'redsys', ' ' );
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
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'redsys', '*********************************' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
			}
			return $post_arg;
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', 'Refund Ok, returning TRUE' );
		}
		return true;
	}
	/**
	 * Check if the order has been refunded
	 *
	 * @param  int $order_id Order ID.
	 * @return bool
	 */
	public function check_redsys_refund( $order_id ) {
		// check postmeta.
		$order        = WCRed()->get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', '*****************************************' );
			$this->log->add( 'redsys', ' ' );
			$this->log->add( 'redsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'redsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Ask for preauthorization confirmation
	 *
	 * @param  int $order_id Order ID.
	 * @param  int $transaction_id Transaction ID.
	 * @param  int $amount Amount.
	 */
	public function ask_for_confirm_preauthorization( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = WCRed()->get_order( $order_id );
		$terminal       = WCRed()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );
		$commerce_fuc   = WCRed()->get_order_meta( $order_id, '_order_fuc_redsys', true );

		if ( ! $commerce_fuc ) {
			$commerce_fuc = $this->customer;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}

		$transaction_type  = '2';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
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
		$autorization_code = WCRed()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = WCRed()->get_order_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = WCRed()->get_order_meta( $order_id, '_corruncy_code_redsys', true );
		$merchan_name      = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new WooRedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $amount );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $commerce_fuc );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $final_notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $order->get_cancel_order_url() );
		$mi_obj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', '001' );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', WCRed()->product_description( $order, $this->id ) );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $commerce_fuc );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'ask_for_confirm_preauthorization Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Next Step, Call', 'woocommerce-redsys' ) );
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
			$this->log->add( 'redsys', __( 'The call is already made and this is the response: ', 'woocommerce-redsys' ) . print_r( $post_arg ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		if ( is_wp_error( $post_arg ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Ask for collect remainder
	 *
	 * @param  int    $order_id Order ID.
	 * @param  string $amount Amount.
	 * @return bool
	 */
	public function ask_for_collect_remainder( $order_id, $amount ) {

		// post code to REDSYS.
		$order            = WCRed()->get_order( $order_id );
		$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
		$terminal         = WCRed()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes   = WCRed()->get_currencies();
		$user_id          = $order->get_user_id();
		$secretsha256     = $this->get_redsys_sha256( $user_id );
		$customer_token   = WCRed()->get_users_token_bulk( $user_id );
		$order_total_sign = $amount;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}

		$transaction_type  = '0';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
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
		$autorization_code = WCRed()->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		$autorization_date = WCRed()->get_order_meta( $order_id, '_payment_date_redsys', true );
		$currencycode      = WCRed()->get_order_meta( $order_id, '_corruncy_code_redsys', true );
		$merchan_name      = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$merchant_lastnme  = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		if ( ! empty( $currencycode ) ) {
			$currency = $currencycode;
		} else {
			if ( ! empty( $currency_codes ) ) {
				$currency = $currency_codes[ get_woocommerce_currency() ];
			}
		}

		$mi_obj = new WooRedsysAPI();
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
		$mi_obj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $merchan_name ) . ' ' . WCRed()->clean_data( $merchant_lastnme ) );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		if ( ! empty( $this->merchantgroup ) ) {
			$mi_obj->setParameter( 'DS_MERCHANT_GROUP', $this->merchantgroup );
		}
		$mi_obj->setParameter( 'DS_MERCHANT_IDENTIFIER', $customer_token );
		$mi_obj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'true' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $order_total_sign );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $this->customer );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'redsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'redsys', __( 'DS_MERCHANT_IDENTIFIER: ', 'woocommerce-redsys' ) . $customer_token );
			$this->log->add( 'redsys', __( 'ask_for_collect_remainder Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		$version   = 'HMAC_SHA256_V1';
		$request   = '';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Next Step, Call', 'woocommerce-redsys' ) );
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
			$this->log->add( 'redsys', __( 'The call is already made and this is the response: ', 'woocommerce-redsys' ) . print_r( $post_arg ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		if ( is_wp_error( $post_arg ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Check if order is preauthorized
	 *
	 * @param  int $order_id Order ID.
	 * @return bool
	 */
	public function check_confirm_preauth( $order_id ) {

		$order         = WCRed()->get_order( (int) $order_id );
		$order_preauth = get_transient( $order->get_id() . '_redsys_preauth' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'redsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_preauth );
		}
		if ( 'yes' === $order_preauth ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if order is preauthorized
	 *
	 * @param  int $order_id Order ID.
	 */
	public function check_collect_remainder( $order_id ) {

		$order         = WCRed()->get_order( (int) $order_id );
		$order_collect = get_transient( $order->get_id() . '_redsys_collect' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'redsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_collect );
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
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function redsys_preauthorized_js_callback() {
		global $wpdb;

		if ( ! is_admin() ) {
			return;
		}

		if ( ! isset( $_POST['order_id'] ) ) {
			return;
		}

		set_time_limit( 0 );
		$order_id       = intval( $_POST['order_id'] );
		$order          = WCRed()->get_order( $order_id );
		$transaction_id = WCRed()->get_redsys_order_number( $order_id );
		set_transient( 'redys_order_temp_' . $transaction_id, $order_id, 3600 );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$redsys_class     = new WC_Gateway_Redsys();

		if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'redsys', __( 'Firs step for confirm Preauthorization for order #: ', 'woocommerce-redsys' ) . $order_id );
				$redsys_class->log->add( 'redsys', __( '$transaction_id: ', 'woocommerce-redsys' ) . $transaction_id );
				$redsys_class->log->add( 'redsys', __( '$order_total_sign: ', 'woocommerce-redsys' ) . $order_total_sign );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'redsys', __( 'Checking for Confirm Preauthorization for order #: ', 'woocommerce-redsys' ) . $order_id );
			}
			$confirm_preauthorization = $redsys_class->ask_for_confirm_preauthorization( $order_id, $transaction_id, $order_total_sign );
			if ( ! $confirm_preauthorization ) {

				if ( 'yes' === $redsys_class->debug ) {
					$redsys_class->log->add( 'redsys', __( 'Error confirming Preauthorization', 'woocommerce-redsys' ) );
				}
				$confirm_result = __( 'There was an error confirming Preauthorization', 'woocommerce-redsys' );
			} else {
				$x = 0;
				do {
					sleep( 5 );
					$result = $redsys_class->check_confirm_preauth( $order_id );
					$x++;
				} while ( $x <= 20 && false === $result );
				@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( 'yes' === $redsys_class->debug && $result ) {
					$redsys_class->log->add( 'redsys', __( 'Confirming Preauthorization = true ', 'woocommerce-redsys' ) );
				}
				if ( 'yes' === $redsys_class->debug && ! $result ) {
					$redsys_class->log->add( 'redsys', __( 'Confirming Preauthorization = false ', 'woocommerce-redsys' ) );
				}
				if ( $result ) {
					delete_transient( $order_id . '_redsys_preauth' );
					$confirm_result = __( 'Successfully Confirming Preauthorization', 'woocommerce-redsys' );
					$redsys_class->log->add( 'redsys', __( 'Deleted transcient _redsys_preauth', 'woocommerce-redsys' ) );
				} else {
					if ( 'yes' === $redsys_class->debug && $result ) {
						$redsys_class->log->add( 'redsys', __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' ) );
					}
					$confirm_result = __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' );
				}
			}
		} else {
			if ( 'yes' === $redsys_class->debug ) {
				$redsys_class->log->add( 'redsys', __( 'Failed Confirming Preauthorization: No transaction ID', 'woocommerce-redsys' ) );
			}
			$confirm_result = __( 'Confirm Preauthorization Failed: No transaction ID', 'woocommerce-redsys' );
		}

		echo esc_html( $confirm_result );
		wp_die();
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function redsys_charge_depo_js_callback() {
		global $wpdb;

		if ( ! is_admin() ) {
			return;
		}
		$redsys_depo = new WC_Gateway_redsys();
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
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$transaction_id   = WCRed()->get_redsys_order_number( $order_id );

		if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'redsys', __( 'First step for collect remainder for order #: ', 'woocommerce-redsys' ) . $order_id );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'redsys', __( 'Checking for collect remainder for for order #: ', 'woocommerce-redsys' ) . $order_id );
			}
			$confirm_collect_remainder = $redsys_depo->ask_for_collect_remainder( $order_id, $order_total_sign );
			if ( ! $confirm_collect_remainder ) {

				if ( 'yes' === $redsys_depo->debug ) {
					$redsys_depo->log->add( 'redsys', __( 'Error Collecting Remainder', 'woocommerce-redsys' ) );
				}
				$confirm_result = __( 'There was an error collecting remainder', 'woocommerce-redsys' );
			} else {
				$x = 0;
				do {
					sleep( 5 );
					$result = $redsys_depo->check_collect_remainder( $order_id );
					$x++;
				} while ( $x <= 20 && false === $result );

				@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

				if ( 'yes' === $redsys_depo->debug && $result ) {
					$redsys_depo->log->add( 'redsys', __( 'Confirming Collecting Remainder = true ', 'woocommerce-redsys' ) );
				}

				if ( 'yes' === $redsys_depo->debug && ! $result ) {
					$redsys_depo->log->add( 'redsys', __( 'Confirming Collecting Remainder = false ', 'woocommerce-redsys' ) );
				}

				if ( $result ) {
					delete_transient( $order_id . '_redsys_collect' );
					$confirm_result = __( 'Successfully Collected Remainder', 'woocommerce-redsys' );
					$redsys_depo->log->add( 'redsys', __( 'Deleted transcient _redsys_collect', 'woocommerce-redsys' ) );

					foreach ( $order->get_items() as $order_item_id => $order_item ) {

						if ( 'yes' === $redsys_depo->debug && $order_item_id ) {
							$redsys_depo->log->add( 'redsys', 'Item ID: ' . $order_item_id );
						} else {
							$redsys_depo->log->add( 'redsys', 'No Item ID?' );
						}
						wc_add_order_item_meta( $order_item_id, '_remaining_balance_paid', 1 );
					}
					WCRed()->update_order_meta( $order_id, '_order_total', $amount );
					$order->update_status( 'completed', __( 'Order Completed', 'woocommerce-redsys' ) );

				} else {
					if ( 'yes' === $redsys_depo->debug && $result ) {
						$redsys_depo->log->add( 'redsys', __( 'Failed Collecting Remainder, please try again', 'woocommerce-redsys' ) );
					}
					$confirm_result = __( 'Failed Collecting Remainder, please try again', 'woocommerce-redsys' );
				}
			}
		} else {
			if ( 'yes' === $redsys_depo->debug ) {
				$redsys_depo->log->add( 'redsys', __( 'Failed Collecting Remainder: No transaction ID', 'woocommerce-redsys' ) );
			}
			$confirm_result = __( 'Confirm Collecting Remainder: No transaction ID', 'woocommerce-redsys' );
		}

		echo esc_html( $confirm_result );
		wp_die();
	}
	/**
	 * Process a refund if supported.
	 *
	 * @param int    $order_id Order ID.
	 * @param float  $amount Refund amount.
	 * @param string $reason Refund reason.
	 * @return bool|WP_Error True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id.
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = WCRed()->get_redsys_order_number( $order_id );
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = WCRed()->redsys_amount_format( $amount );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', '       Once upon a time       ' );
				$this->log->add( 'redsys', '/****************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', __( 'check_redsys_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'redsys', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
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
				$this->log->add( 'redsys', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'redsys', '/********************************/' );
				$this->log->add( 'redsys', ' ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'redsys', '/******************************************/' );
				$this->log->add( 'redsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'redsys', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'redsys', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'redsys', ' ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'redsys', '/******************************************/' );
					$this->log->add( 'redsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'redsys', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
		}
	}
	/**
	 * Add Bulk Actions
	 *
	 * @param array $bulk_actions All bulk actions.
	 */
	public static function redsys_add_bulk_actions( $bulk_actions ) {

		if ( 'yes' === WCRed()->get_redsys_option( 'bulkcharge', 'redsys' ) ) {
			$bulk_actions['redsys_charge_invoice_token'] = __( 'Immediate Redsys Charge', 'woocommerce-redsys' );
		}
		if ( 'yes' === WCRed()->get_redsys_option( 'preauthorization', 'redsys' ) ) {
			$bulk_actions['redsys_aprobe_preauthorizations'] = __( 'Approve Pre-authorization', 'woocommerce-redsys' );
		}
		if ( 'yes' === WCRed()->get_redsys_option( 'bulkrefund', 'redsys' ) ) {
			$bulk_actions['redsys_bulk_refund'] = __( 'Bulk Refund Redsys (Warning)', 'woocommerce-redsys' );
		}
		return $bulk_actions;
	}
	/**
	 * Process Bulk Actions
	 *
	 * @param Object $order Redirect to.
	 */
	public static function charge_invoive_by_order( $order ) {
		$class_redsys = new WC_Gateway_Redsys();
		$user_id      = $order->get_user_id();
		$status       = $order->get_status();
		$order_id     = $order->get_id();

		if ( 'pending' === $status ) {

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/**********************************************/' );
				$class_redsys->log->add( 'redsys', '                Global function                 ' );
				$class_redsys->log->add( 'redsys', '/**********************************************/' );
				$class_redsys->log->add( 'redsys', '$user_id: ' . $user_id );
				$class_redsys->log->add( 'redsys', '$status: ' . $status );
				$class_redsys->log->add( 'redsys', '$order_id: ' . $order_id );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$order_total_sign     = '';
			$transaction_id2      = '';
			$transaction_type     = '';
			$ds_merchant_terminal = '';
			$final_notify_url     = '';
			$returnfromredsys     = '';
			$gatewaylanguage      = '';
			$currency             = '';
			$secretsha256         = '';
			$customer             = '';
			$url_ok               = '';
			$product_description  = '';
			$merchant_name        = '';

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', '  Generating Tokenized call   ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$order_id: ' . $order_id );
				$class_redsys->log->add( 'redsys', '$user_id: ' . $user_id );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$type       = 'ws';
			$order      = WCRed()->get_order( $order_id );
			$redsys_adr = $class_redsys->get_redsys_url_gateway( $user_id, $type );

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
				$class_redsys->log->add( 'redsys', ' ' );
			}
			$currency_codes = WCRed()->get_currencies();

			$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$transaction_type = '0';

			$gatewaylanguage = $class_redsys->redsyslanguage;

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
			}

			if ( $class_redsys->wooredsysurlko ) {
				if ( 'returncancel' === $class_redsys->wooredsysurlko ) {
					$returnfromredsys = $order->get_cancel_order_url();
				} else {
					$returnfromredsys = wc_get_checkout_url();
				}
			} else {
				$returnfromredsys = $order->get_cancel_order_url();
			}
			if ( 'yes' === $class_redsys->useterminal2 ) {
				$toamount  = number_format( $class_redsys->toamount, 2, '', '' );
				$terminal  = $class_redsys->terminal;
				$terminal2 = $class_redsys->terminal2;
				if ( $order_total_sign <= $toamount ) {
					$ds_merchant_terminal = $terminal2;
				} else {
					$ds_merchant_terminal = $terminal;
				}
			} else {
				$ds_merchant_terminal = $class_redsys->terminal;
			}

			if ( 'yes' === $class_redsys->not_use_https ) {
				$final_notify_url = $class_redsys->notify_url_not_https;
			} else {
				$final_notify_url = $class_redsys->notify_url;
			}
			$customer_token    = WCRed()->get_users_token_bulk( $user_id, 'R' );
			$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
			$txnid             = WCRed()->get_txnid( $customer_token_id );

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$redsys_data_send = array();

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', 'Order Currency: ' . get_woocommerce_currency() );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$currency            = $currency_codes[ get_woocommerce_currency() ];
			$secretsha256        = $class_redsys->get_redsys_sha256( $user_id );
			$customer            = $class_redsys->customer;
			$url_ok              = add_query_arg( 'utm_nooverride', '1', $class_redsys->get_return_url( $order ) );
			$product_description = WCRed()->product_description( $order, 'redsys' );
			$merchant_name       = $class_redsys->commercename;

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

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
					$class_redsys->log->add( 'redsys', ' ' );
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
			$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
			$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
				$class_redsys->log->add( 'redsys', '$order: ' . $orderid2 );
				$class_redsys->log->add( 'redsys', '$customer: ' . $customer );
				$class_redsys->log->add( 'redsys', '$currency: ' . $currency );
				$class_redsys->log->add( 'redsys', '$transaction_type: 0' );
				$class_redsys->log->add( 'redsys', '$terminal: ' . $terminal );
				$class_redsys->log->add( 'redsys', '$url_ok: ' . $url_ok );
				$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
				$class_redsys->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
				$class_redsys->log->add( 'redsys', ' ' );
			}

			$mi_obj = new WooRedsysAPIWS();
			if ( ! empty( $class_redsys->merchantgroup ) ) {
				$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $class_redsys->merchantgroup . '</DS_MERCHANT_GROUP>';
			} else {
				$ds_merchant_group = '';
			}
			$datos_entrada  = '<DATOSENTRADA>';
			$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
			$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
			$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
			$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
			$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
			$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
			$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
			$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
			$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
			$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
			$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
			$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
			$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
			$datos_entrada .= '</DATOSENTRADA>';
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', '          The call            ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', $datos_entrada );
				$class_redsys->log->add( 'redsys', ' ' );
			}
			$xml  = '<REQUEST>';
			$xml .= $datos_entrada;
			$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
			$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
			$xml .= '</REQUEST>';

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', '          The XML  13           ' );
				$class_redsys->log->add( 'redsys', '/****************************/' );
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', 'iniciaPeticion 6' . $xml );
				$class_redsys->log->add( 'redsys', ' ' );
			}
			$cliente    = new SoapClient( $redsys_adr );
			$responsews = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

			if ( isset( $responsews->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$respuesta   = json_decode( $xml_retorno->INFOTARJETA->Ds_EMV3DS ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$ds_emv3ds                = json_decode( $ds_emv3ds_json );
			$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
				$class_redsys->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$class_redsys->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
				$class_redsys->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
			}

			if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
					$class_redsys->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
				}
				$datos_entrada  = '<DATOSENTRADA>';
				$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				$datos_entrada .= '</DATOSENTRADA>';
				$xml            = '<REQUEST>';
				$xml           .= $datos_entrada;
				$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml           .= '</REQUEST>';

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', '/****************************/' );
					$class_redsys->log->add( 'redsys', '          The XML 14            ' );
					$class_redsys->log->add( 'redsys', '/****************************/' );
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', $xml );
					$class_redsys->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$class_redsys->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
				}
				if ( $authorisationcode ) {
					WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
					$order->payment_complete();
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', 'payment_complete 20' );
					}
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', ' ' );
						$class_redsys->log->add( 'redsys', '/****************************/' );
						$class_redsys->log->add( 'redsys', '      Saving Order Meta       ' );
						$class_redsys->log->add( 'redsys', '/****************************/' );
						$class_redsys->log->add( 'redsys', ' ' );
					}

					if ( ! empty( $redsys_order ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					// TO-DO: Enviar un correo con el problema al administrador.
					$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
					$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
					do_action( 'redsys_post_payment_error', $order->get_id(), $error );
					return false;
				}
			} else {
				$protocol_version = '1.0.2';
				$data             = array(
					'threeDSInfo'     => 'AuthenticationData',
					'protocolVersion' => '1.0.2',
				);
				$need             = wp_json_encode( $data );
				$datos_entrada    = '<DATOSENTRADA>';
				$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
				$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
				$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
				$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
				$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
				$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
				$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
				$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
				$datos_entrada   .= $ds_merchant_group;
				$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
				$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
				$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
				// $datos_entrada .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
				$datos_entrada .= '</DATOSENTRADA>';
				$xml            = '<REQUEST>';
				$xml           .= $datos_entrada;
				$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
				$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
				$xml           .= '</REQUEST>';

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', '/****************************/' );
					$class_redsys->log->add( 'redsys', '          The XML 15            ' );
					$class_redsys->log->add( 'redsys', '/****************************/' );
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', $xml );
					$class_redsys->log->add( 'redsys', ' ' );
				}
				$cliente    = new SoapClient( $redsys_adr );
				$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

				if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}
				$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				if ( $authorisationcode ) {
					WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
					$order->payment_complete();
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', 'payment_complete 21' );
					}
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', ' ' );
						$class_redsys->log->add( 'redsys', '/****************************/' );
						$class_redsys->log->add( 'redsys', '      Saving Order Meta       ' );
						$class_redsys->log->add( 'redsys', '/****************************/' );
						$class_redsys->log->add( 'redsys', ' ' );
					}

					if ( ! empty( $redsys_order ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $terminal ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $authorisationcode ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $currency_code ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( ! empty( $secretsha256 ) ) {
						WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
						}
					} else {
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
							$class_redsys->log->add( 'redsys', ' ' );
						}
					}
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
					}
					do_action( 'redsys_post_payment_complete', $order->get_id() );
					return true;
				} else {
					// TO-DO: Enviar un correo con el problema al administrador.
					$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
					$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
					do_action( 'redsys_post_payment_error', $order->get_id(), $error );
					return false;
				}
			}
		} else {
			return false;
		}
	}
	/**
	 * Bulk Actions Handler
	 *
	 * @param  string $redirect_to Where redirect to.
	 * @param  string $doaction    Action.
	 * @param  array  $post_ids    Post ids.
	 *
	 * @throws Exception Exception.
	 */
	public static function redsys_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {

		if ( 'redsys_charge_invoice_token' !== $doaction && 'redsys_bulk_refund' !== $doaction && 'redsys_aprobe_preauthorizations' !== $doaction ) {
			return $redirect_to;
		}

		$class_redsys = new WC_Gateway_Redsys();

		if ( 'yes' === $class_redsys->debug ) {
			$class_redsys->log->add( 'redsys', ' ' );
			$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			$class_redsys->log->add( 'redsys', '     redsys_bulk_actions_handler   ' );
			$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			$class_redsys->log->add( 'redsys', '$redirect_to = ' . $redirect_to );
			$class_redsys->log->add( 'redsys', '$doaction = ' . $doaction );
			$class_redsys->log->add( 'redsys', '$post_ids = ' . print_r( $post_ids, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
		}

		// Si es la acción primera, realizará estas accion.
		if ( 'redsys_charge_invoice_token' === $doaction ) {
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', __( 'Doing Bulk Actions', 'woocommerce-redsys' ) );
			}
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/*********************************/' );
				$class_redsys->log->add( 'redsys', '     Before foreach $post_ids      ' );
				$class_redsys->log->add( 'redsys', '/*********************************/' );
			}

			foreach ( $post_ids as $post_id ) {
				// Get all order information.
				$order = wc_get_order( $post_id );
				// Get user ID.
				$user_id = $order->get_user_id();
				// Check if is pending.
				$status = $order->get_status();
				if ( 'pending' === $status ) {
					if ( 'yes' === WCRed()->get_redsys_option( 'psd2', 'redsys' ) ) {
						// Is PSD2.
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '/**********************************************/' );
							$class_redsys->log->add( 'redsys', '  Function redsys_bulk_actions_handler' );
							$class_redsys->log->add( 'redsys', '/**********************************************/' );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$order_total_sign     = '';
						$transaction_id2      = '';
						$transaction_type     = '';
						$ds_merchant_terminal = '';
						$final_notify_url     = '';
						$returnfromredsys     = '';
						$gatewaylanguage      = '';
						$currency             = '';
						$secretsha256         = '';
						$customer             = '';
						$url_ok               = '';
						$product_description  = '';
						$merchant_name        = '';

						$order_id = $post_id;
						$user_id  = $order->get_user_id();

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', '  Generating Tokenized call   ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$order_id: ' . $order_id );
							$class_redsys->log->add( 'redsys', '$user_id: ' . $user_id );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$type       = 'ws';
						$order      = WCRed()->get_order( $order_id );
						$redsys_adr = $class_redsys->get_redsys_url_gateway( $user_id, $type );

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
							$class_redsys->log->add( 'redsys', ' ' );
						}
						$currency_codes   = WCRed()->get_currencies();
						$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
						$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$transaction_type = '0';

						$gatewaylanguage = $class_redsys->redsyslanguage;

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
						}

						if ( $class_redsys->wooredsysurlko ) {
							if ( 'returncancel' === $class_redsys->wooredsysurlko ) {
								$returnfromredsys = $order->get_cancel_order_url();
							} else {
								$returnfromredsys = wc_get_checkout_url();
							}
						} else {
							$returnfromredsys = $order->get_cancel_order_url();
						}
						if ( 'yes' === $class_redsys->useterminal2 ) {
							$toamount  = number_format( $class_redsys->toamount, 2, '', '' );
							$terminal  = $class_redsys->terminal;
							$terminal2 = $class_redsys->terminal2;
							if ( $order_total_sign <= $toamount ) {
								$ds_merchant_terminal = $terminal2;
							} else {
								$ds_merchant_terminal = $terminal;
							}
						} else {
							$ds_merchant_terminal = $class_redsys->terminal;
						}

						if ( 'yes' === $class_redsys->not_use_https ) {
							$final_notify_url = $class_redsys->notify_url_not_https;
						} else {
							$final_notify_url = $class_redsys->notify_url;
						}
						$customer_token    = WCRed()->get_users_token_bulk( $user_id, 'R' );
						$customer_token_id = WCRed()->get_users_token_bulk( $user_id, 'R', 'id' );
						$txnid             = WCRed()->get_txnid( $customer_token_id );

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$customer_token: ' . $customer_token );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$redsys_data_send = array();

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', 'Order Currency: ' . get_woocommerce_currency() );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$currency            = $currency_codes[ get_woocommerce_currency() ];
						$secretsha256        = $class_redsys->get_redsys_sha256( $user_id );
						$customer            = $class_redsys->customer;
						$url_ok              = add_query_arg( 'utm_nooverride', '1', $class_redsys->get_return_url( $order ) );
						$product_description = WCRed()->product_description( $order, 'redsys' );
						$merchant_name       = $class_redsys->commercename;

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

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
								$class_redsys->log->add( 'redsys', ' ' );
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
						$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
						$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
							$class_redsys->log->add( 'redsys', '$order: ' . $orderid2 );
							$class_redsys->log->add( 'redsys', '$customer: ' . $customer );
							$class_redsys->log->add( 'redsys', '$currency: ' . $currency );
							$class_redsys->log->add( 'redsys', '$transaction_type: 0' );
							$class_redsys->log->add( 'redsys', '$terminal: ' . $terminal );
							$class_redsys->log->add( 'redsys', '$url_ok: ' . $url_ok );
							$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
							$class_redsys->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
							$class_redsys->log->add( 'redsys', ' ' );
						}

						$mi_obj = new WooRedsysAPIWS();
						if ( ! empty( $class_redsys->merchantgroup ) ) {
							$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $class_redsys->merchantgroup . '</DS_MERCHANT_GROUP>';
						} else {
							$ds_merchant_group = '';
						}
						$datos_entrada  = '<DATOSENTRADA>';
						$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
						$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
						$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
						$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
						$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
						$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
						$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
						$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
						$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
						$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
						$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
						$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
						$datos_entrada .= '<DS_MERCHANT_EMV3DS>{"threeDSInfo":"CardData"}</DS_MERCHANT_EMV3DS>';
						$datos_entrada .= '</DATOSENTRADA>';
						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', '          The call            ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', $datos_entrada );
							$class_redsys->log->add( 'redsys', ' ' );
						}
						$xml  = '<REQUEST>';
						$xml .= $datos_entrada;
						$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
						$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
						$xml .= '</REQUEST>';

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', '          The XML 16            ' );
							$class_redsys->log->add( 'redsys', '/****************************/' );
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', 'iniciaPeticion 7' . $xml );
							$class_redsys->log->add( 'redsys', ' ' );
						}
						$cliente    = new SoapClient( $redsys_adr );
						$responsews = $cliente->iniciaPeticion( array( 'datoEntrada' => $xml ) );

						if ( isset( $responsews->iniciaPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$xml_retorno = new SimpleXMLElement( $responsews->iniciaPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						}

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						}

						$ds_emv3ds_json           = $xml_retorno->INFOTARJETA->Ds_EMV3DS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$ds_emv3ds                = json_decode( $ds_emv3ds_json );
						$protocol_version         = $ds_emv3ds->protocolVersion; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$three_ds_server_trans_id = $ds_emv3ds->threeDSServerTransID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$three_ds_info            = $ds_emv3ds->threeDSInfo; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

						if ( 'yes' === $class_redsys->debug ) {
							$class_redsys->log->add( 'redsys', ' ' );
							$class_redsys->log->add( 'redsys', '$ds_emv3ds_json: ' . $ds_emv3ds_json );
							$class_redsys->log->add( 'redsys', '$ds_emv3ds: ' . print_r( $ds_emv3ds, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
							$class_redsys->log->add( 'redsys', '$three_ds_server_trans_id: ' . $three_ds_server_trans_id );
							$class_redsys->log->add( 'redsys', '$three_ds_info: ' . $three_ds_info );
						}

						if ( '2.1.0' === $protocol_version || '2.2.0' === $protocol_version ) {

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', 'threeDSInfo: AuthenticationData' );
								$class_redsys->log->add( 'redsys', 'protocolVersion: ' . $protocol_version );
							}
							$datos_entrada  = '<DATOSENTRADA>';
							$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
							$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
							$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
							$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
							$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
							$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
							$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
							$datos_entrada .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
							$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
							$datos_entrada .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
							$datos_entrada .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
							$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
							$datos_entrada .= '</DATOSENTRADA>';
							$xml            = '<REQUEST>';
							$xml           .= $datos_entrada;
							$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
							$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
							$xml           .= '</REQUEST>';

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '          The XML 16            ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', $xml );
								$class_redsys->log->add( 'redsys', ' ' );
							}
							$cliente    = new SoapClient( $redsys_adr );
							$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

							if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$xml_retorno       = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							}

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
								$class_redsys->log->add( 'redsys', 'Ds_AuthorisationCode: ' . $authorisationcode );
							}
							if ( $authorisationcode ) {
								WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
								$order->payment_complete();
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', 'payment_complete 22' );
								}
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', ' ' );
									$class_redsys->log->add( 'redsys', '/****************************/' );
									$class_redsys->log->add( 'redsys', '      Saving Order Meta       ' );
									$class_redsys->log->add( 'redsys', '/****************************/' );
									$class_redsys->log->add( 'redsys', ' ' );
								}

								if ( ! empty( $redsys_order ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $terminal ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $authorisationcode ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $currency_code ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $secretsha256 ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', '/******************************************/' );
									$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
									$class_redsys->log->add( 'redsys', '/******************************************/' );
								}
								do_action( 'redsys_post_payment_complete', $order->get_id() );
								continue;
							} else {
								// TO-DO: Enviar un correo con el problema al administrador.
								$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
								$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
								do_action( 'redsys_post_payment_error', $order->get_id(), $error );
								continue;
							}
						} else {
							$protocol_version = '1.0.2';
							$data             = array(
								'threeDSInfo'     => 'AuthenticationData',
								'protocolVersion' => '1.0.2',
							);
							$need             = wp_json_encode( $data );
							$datos_entrada    = '<DATOSENTRADA>';
							$datos_entrada   .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
							$datos_entrada   .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
							$datos_entrada   .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
							$datos_entrada   .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
							$datos_entrada   .= '<DS_MERCHANT_TRANSACTIONTYPE>0</DS_MERCHANT_TRANSACTIONTYPE>';
							$datos_entrada   .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
							$datos_entrada   .= '<DS_MERCHANT_COF_INI>N</DS_MERCHANT_COF_INI>';
							$datos_entrada   .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';
							$datos_entrada   .= $ds_merchant_group;
							$datos_entrada   .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
							$datos_entrada   .= '<DS_MERCHANT_COF_TXNID>' . $txnid . '</DS_MERCHANT_COF_TXNID>';
							$datos_entrada   .= '<DS_MERCHANT_EXCEP_SCA>MIT</DS_MERCHANT_EXCEP_SCA>';
							$datos_entrada   .= '<DS_MERCHANT_DIRECTPAYMENT>TRUE</DS_MERCHANT_DIRECTPAYMENT>';
							// $datos_entrada .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
							$datos_entrada .= '</DATOSENTRADA>';
							$xml            = '<REQUEST>';
							$xml           .= $datos_entrada;
							$xml           .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
							$xml           .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
							$xml           .= '</REQUEST>';

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '          The XML 18            ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', $xml );
								$class_redsys->log->add( 'redsys', ' ' );
							}
							$cliente    = new SoapClient( $redsys_adr );
							$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

							if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							}

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$responsews: ' . print_r( $responsews, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
								$class_redsys->log->add( 'redsys', '$xml_retorno: ' . print_r( $xml_retorno, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
							}
							$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

							if ( $authorisationcode ) {
								WCRed()->update_order_meta( $order_id, '_redsys_done', 'yes' );
								$order->payment_complete();
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', 'payment_complete 23' );
								}
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', ' ' );
									$class_redsys->log->add( 'redsys', '/****************************/' );
									$class_redsys->log->add( 'redsys', '      Saving Order Meta       ' );
									$class_redsys->log->add( 'redsys', '/****************************/' );
									$class_redsys->log->add( 'redsys', ' ' );
								}

								if ( ! empty( $redsys_order ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $terminal ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $authorisationcode ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $currency_code ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( ! empty( $secretsha256 ) ) {
									WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
									}
								} else {
									if ( 'yes' === $class_redsys->debug ) {
										$class_redsys->log->add( 'redsys', ' ' );
										$class_redsys->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
										$class_redsys->log->add( 'redsys', ' ' );
									}
								}
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', '/******************************************/' );
									$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
									$class_redsys->log->add( 'redsys', '/******************************************/' );
								}
								do_action( 'redsys_post_payment_complete', $order->get_id() );
								continue;
							} else {
								// TO-DO: Enviar un correo con el problema al administrador.
								$order->add_order_note( __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' ) );
								$error = __( 'There wasn\'t respond from Redsys', 'woocommerce-redsys' );
								do_action( 'redsys_post_payment_error', $order->get_id(), $error );
								continue;
							}
						}
					} else {
						// Get user Token.
						$customer_token = WCRed()->get_users_token_bulk( $user_id );
						if ( $customer_token ) {
							$order_total_sign     = '';
							$transaction_id2      = '';
							$transaction_type     = '';
							$ds_merchant_terminal = '';
							$final_notify_url     = '';
							$returnfromredsys     = '';
							$gatewaylanguage      = '';
							$currency             = '';
							$secretsha256         = '';
							$customer             = '';
							$url_ok               = '';
							$product_description  = '';
							$merchant_name        = '';
							$amount               = '';
							$order_id             = $post_id;
							$type                 = 'ws';
							$user_id              = $order->get_user_id();
							$redsys_adr           = $class_redsys->get_redsys_url_gateway( $user_id, $type );

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', 'Using WS URL: ' . $redsys_adr );
								$class_redsys->log->add( 'redsys', ' ' );
							}
							$amount         = $order->get_total();
							$currency_codes = WCRed()->get_currencies();

							$transaction_id2  = WCRed()->prepare_order_number( $order_id, 'redsys' );
							$order_total_sign = WCRed()->redsys_amount_format( $amount );

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
								$class_redsys->log->add( 'redsys', ' ' );
							}

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '  $transaction_type = 0.      ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
							}

							$transaction_type = '0';
							$gatewaylanguage  = $class_redsys->redsyslanguage;

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $order_total_sign );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$transaction_type: ' . $transaction_type );
							}

							if ( $class_redsys->wooredsysurlko ) {
								if ( 'returncancel' === $class_redsys->wooredsysurlko ) {
									$returnfromredsys = $order->get_cancel_order_url();
								} else {
									$returnfromredsys = wc_get_checkout_url();
								}
							} else {
								$returnfromredsys = $order->get_cancel_order_url();
							}
							if ( 'yes' === $class_redsys->useterminal2 ) {
								$toamount  = number_format( $class_redsys->toamount, 2, '', '' );
								$terminal  = $class_redsys->terminal;
								$terminal2 = $class_redsys->terminal2;
								if ( $order_total_sign <= $toamount ) {
									$ds_merchant_terminal = $terminal2;
								} else {
									$ds_merchant_terminal = $terminal;
								}
							} else {
								$ds_merchant_terminal = $class_redsys->terminal;
							}

							if ( 'yes' === $class_redsys->not_use_https ) {
								$final_notify_url = $class_redsys->notify_url_not_https;
							} else {
								$final_notify_url = $class_redsys->notify_url;
							}
							$redsys_data_send = array();

							$currency            = $currency_codes[ get_woocommerce_currency() ];
							$secretsha256        = $class_redsys->get_redsys_sha256( $user_id );
							$customer            = $class_redsys->customer;
							$url_ok              = add_query_arg( 'utm_nooverride', '1', $class_redsys->get_return_url( $order ) );
							$product_description = WCRed()->product_description( $order, 'redsys' );
							$merchant_name       = $class_redsys->commercename;

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
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', ' ' );
									$class_redsys->log->add( 'redsys', 'Using filter redsys_modify_data_to_send' );
									$class_redsys->log->add( 'redsys', ' ' );
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
							$merchan_name     = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
							$merchant_lastnme = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '$order_total_sign: ' . $order_total_sign );
								$class_redsys->log->add( 'redsys', '$order: ' . $orderid2 );
								$class_redsys->log->add( 'redsys', '$customer: ' . $customer );
								$class_redsys->log->add( 'redsys', '$currency: ' . $currency );
								$class_redsys->log->add( 'redsys', '$transaction_type: 0' );
								$class_redsys->log->add( 'redsys', '$terminal: ' . $terminal );
								$class_redsys->log->add( 'redsys', '$url_ok: ' . $url_ok );
								$class_redsys->log->add( 'redsys', '$gatewaylanguage: ' . $gatewaylanguage );
								$class_redsys->log->add( 'redsys', '$final_notify_url: ' . $final_notify_url );
								$class_redsys->log->add( 'redsys', ' ' );
							}

							$mi_obj = new WooRedsysAPIWS();

							if ( ! empty( $class_redsys->merchantgroup ) ) {
								$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $class_redsys->merchantgroup . '</DS_MERCHANT_GROUP>';
							} else {
								$ds_merchant_group = '';
							}
							$datos_entrada  = '<DATOSENTRADA>';
							$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
							$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $terminal . '</DS_MERCHANT_TERMINAL>';
							$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
							$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
							$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
							$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
							$datos_entrada .= $ds_merchant_group;
							$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>' . $customer_token . '</DS_MERCHANT_IDENTIFIER>';
							$datos_entrada .= '<DS_MERCHANT_DIRECTPAYMENT>true</DS_MERCHANT_DIRECTPAYMENT>';
							// $datos_entrada .= "<DS_MERCHANT_TITULAR>" . $merchan_name . ' ' . $merchant_lastnme . "</DS_MERCHANT_TITULAR>";
							$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
							$datos_entrada .= '</DATOSENTRADA>';

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '          The call            ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', $datos_entrada );
								$class_redsys->log->add( 'redsys', ' ' );
							}

							$xml  = '<REQUEST>';
							$xml .= $datos_entrada;
							$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
							$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
							$xml .= '</REQUEST>';

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '          The XML 19            ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', $xml );
								$class_redsys->log->add( 'redsys', ' ' );
							}

							$cliente    = new SoapClient( $redsys_adr ); // Entorno de prueba.
							$responsews = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', '        connection done       ' );
								$class_redsys->log->add( 'redsys', '/****************************/' );
								$class_redsys->log->add( 'redsys', ' ' );
								$class_redsys->log->add( 'redsys', $xml );
								$class_redsys->log->add( 'redsys', ' ' );
							}
							if ( isset( $responsews->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								$xml_retorno = new SimpleXMLElement( $responsews->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
								if ( isset( $xml_retorno->OPERACION->Ds_Response ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
									$respuesta = (int) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
									if ( ( $respuesta >= 0 ) && ( $respuesta <= 99 ) ) {
										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', ' ' );
											$class_redsys->log->add( 'redsys', 'Response: Ok > ' . $respuesta );
											$class_redsys->log->add( 'redsys', ' ' );
										}
										WCRed()->update_order_meta( $order->get_id(), '_redsys_done', 'yes' );
										$order->payment_complete();
										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', 'payment_complete 24' );
										}
										$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
										$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
										$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
										$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
										$secretsha256      = '';

										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', ' ' );
											$class_redsys->log->add( 'redsys', '/****************************/' );
											$class_redsys->log->add( 'redsys', '      Saving Order Meta       ' );
											$class_redsys->log->add( 'redsys', '/****************************/' );
											$class_redsys->log->add( 'redsys', ' ' );
										}

										if ( ! empty( $redsys_order ) ) {
											WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $redsys_order );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', '_payment_order_number_redsys saved: ' . $redsys_order );
											}
										} else {
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', '_payment_order_number_redsys NOT SAVED!!!' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										if ( ! empty( $terminal ) ) {
											WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $terminal );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', '_payment_terminal_redsys saved: ' . $terminal );
											}
										} else {
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', '_payment_terminal_redsys NOT SAVED!!!' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										if ( ! empty( $authorisationcode ) ) {
											WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisationcode );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', '_authorisation_code_redsys saved: ' . $authorisationcode );
											}
										} else {
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', '_authorisation_code_redsys NOT SAVED!!!' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										if ( ! empty( $currency_code ) ) {
											WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', '_corruncy_code_redsys saved: ' . $currency_code );
											}
										} else {
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', '_corruncy_code_redsys NOT SAVED!!!' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										if ( ! empty( $secretsha256 ) ) {
											WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
											}
										} else {
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', '_redsys_secretsha256 NOT SAVED!!!' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', '/******************************************/' );
											$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
											$class_redsys->log->add( 'redsys', '/******************************************/' );
										}
										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', ' ' );
											$class_redsys->log->add( 'redsys', 'Order marked as Processing' );
											$class_redsys->log->add( 'redsys', ' ' );
										}
										if ( 'completed' === $class_redsys->orderdo ) {
											$order->update_status( 'completed', __( 'Order Completed by Redsys', 'woocommerce-redsys' ) );
											if ( 'yes' === $class_redsys->debug ) {
												$class_redsys->log->add( 'redsys', ' ' );
												$class_redsys->log->add( 'redsys', 'Order marked as Complete' );
												$class_redsys->log->add( 'redsys', ' ' );
											}
										}
										do_action( 'redsys_post_payment_complete', $order->get_id() );
										continue;
									} else {
										if ( 'yes' === $class_redsys->debug ) {
											$class_redsys->log->add( 'redsys', ' ' );
											$class_redsys->log->add( 'redsys', 'Response: Error > ' . $respuesta );
											$class_redsys->log->add( 'redsys', ' ' );
										}
										do_action( 'redsys_post_payment_error', $order->get_id(), $respuesta );
										continue;
									}
								}
							}
						} else {
							continue;
						}
					}
				} else {
					continue;
				}
			}
			$redirect_to = add_query_arg( 'redsys_charge_invoice_token', count( $post_ids ), $redirect_to );
			return $redirect_to;
		}

		if ( 'redsys_bulk_refund' === $doaction ) {
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', __( 'Doing Bulk Actions', 'woocommerce-redsys' ) );
			}
			foreach ( $post_ids as $post_id ) {
				$order                  = wc_get_order( $post_id );
				$status                 = $order->get_status();
				$transaction_id         = WCRed()->get_redsys_order_number( $post_id );
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
					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', ' ' );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', '  Refund order ID:   ' . $post_id );
						$class_redsys->log->add( 'redsys', '  $status:   ' . $status );
						$class_redsys->log->add( 'redsys', '  $transaction_id:   ' . $transaction_id );
						$class_redsys->log->add( 'redsys', '  $refund_amount:   ' . $refund_amount );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', ' ' );
					}

					$get_total_refunded = $order->get_total_refunded();
					$max_refund         = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

					if ( 'yes' === $class_redsys->debug ) {
						$class_redsys->log->add( 'redsys', ' ' );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', '  $refund_amount:   ' . $refund_amount );
						$class_redsys->log->add( 'redsys', '  $refund_amount_format:   ' . $refund_amount_format );
						$class_redsys->log->add( 'redsys', '  $get_total_refunded:   ' . $get_total_refunded );
						$class_redsys->log->add( 'redsys', '  $max_refund:   ' . $max_refund );
						$class_redsys->log->add( 'redsys', '/******************************************/' );
						$class_redsys->log->add( 'redsys', ' ' );
					}

					try {
						$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

						if ( ! $max_refund || 0 > $refund_amount ) {
							if ( 'yes' === $class_redsys->debug && $response ) {
								$class_redsys->log->add( 'redsys', __( 'Invalid refund amount', 'woocommerce-redsys' ) );
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

						$array = array(
							'amount'         => $max_refund,
							'reason'         => $refund_reason,
							'order_id'       => $order_id,
							'line_items'     => $line_items,
							'refund_payment' => $api_refund,
							'restock_items'  => $restock_refunded_items,
						);

						$array_filter = apply_filters( 'redsys_refund_filter', $array );

						// Create the refund object.
						$refund = wc_create_refund( $array_filter );

						if ( is_wp_error( $refund ) ) {
							throw new Exception( $refund->get_error_message() );
						}

						if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
							$response = 'fully_refunded';
						}
						if ( did_action( 'woocommerce_order_partially_refunded' ) ) {
							$response = 'partially_refunded';
						}
					} catch ( Exception $e ) {
						$response = 'error ' . $e->getMessage();
					}

					if ( 'fully_refunded' === $response ) {
						continue;
					} elseif ( 'partially_refunded' === $response ) {
						continue;
					} else {
						if ( 'yes' === $class_redsys->debug && $response ) {
							$class_redsys->log->add( 'redsys', __( 'Failed refund order : ', 'woocommerce-redsys' ) . $response );
						}
						continue;
					}
				} else {
					if ( 'yes' === $class_redsys->debug && $response ) {
						$class_redsys->log->add( 'redsys', __( 'The order is pending payment, or has already been refunded.', 'woocommerce-redsys' ) );
					}
					continue;
				}
			}
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '/******************************************/' );
				$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
				$class_redsys->log->add( 'redsys', '/******************************************/' );
				$class_redsys->log->add( 'redsys', ' ' );
			}
			$redirect_to = add_query_arg( 'redsys_bulk_refund', count( $post_ids ), $redirect_to );
			return $redirect_to;
		}
		if ( 'redsys_aprobe_preauthorizations' === $doaction ) {
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', ' ' );
				$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
				$class_redsys->log->add( 'redsys', '     redsys_bulk_actions_handler   ' );
				$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
				$class_redsys->log->add( 'redsys', '$doaction = ' . $doaction );
				$class_redsys->log->add( 'redsys', '$post_ids = ' . print_r( $post_ids, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$class_redsys->log->add( 'redsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			}
			// Solo continúa si son las acciones que hemos creado nosotros.

			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'redsys', __( 'Doing Bulk Actions', 'woocommerce-redsys' ) );
			}
			// Si es la acción primera, realizará estas accion.

			if ( 'redsys_aprobe_preauthorizations' === $doaction ) {

				foreach ( $post_ids as $id ) {

					$order            = wc_get_order( $id );
					$status           = $order->get_status();
					$transaction_id   = WCRed()->get_redsys_order_number( $id );
					$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
					if ( 'redsys-pre' === $status ) {
						$confirm_preauthorization = $class_redsys->ask_for_confirm_preauthorization( $id, $transaction_id, $order_total_sign );
						if ( true !== $confirm_preauthorization ) {
							if ( 'yes' === $class_redsys->debug ) {
								$class_redsys->log->add( 'redsys', __( 'Error confirming Preauthorization', 'woocommerce-redsys' ) );
							}
							continue;
						} else {
							$x = 0;
							do {
								sleep( 5 );
								$result = $class_redsys->check_confirm_preauth( $id );
								$x++;
							} while ( $x <= 20 && false === $result );
							@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
							if ( 'yes' === $class_redsys->debug && $result ) {
								$class_redsys->log->add( 'redsys', __( 'Confirming Preauthorization = true ', 'woocommerce-redsys' ) );
							}
							if ( 'yes' === $class_redsys->debug && ! $result ) {
								$class_redsys->log->add( 'redsys', __( 'Confirming Preauthorization = false ', 'woocommerce-redsys' ) );
							}
							if ( $result ) {
								delete_transient( $id . '_redsys_preauth' );
								if ( 'yes' === $class_redsys->debug ) {
									$class_redsys->log->add( 'redsys', __( 'Deleted transcient _redsys_preauth', 'woocommerce-redsys' ) );
								}
								continue;
							} else {
								if ( 'yes' === $class_redsys->debug && $result ) {
									$class_redsys->log->add( 'redsys', __( 'Failed Confirming Preauthorization, please try again', 'woocommerce-redsys' ) );
								}
								continue;
							}
						}
					} else {
						continue;
					}
				}
				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'redsys', ' ' );
					$class_redsys->log->add( 'redsys', '/******************************************/' );
					$class_redsys->log->add( 'redsys', '  The final has come, this story has ended  ' );
					$class_redsys->log->add( 'redsys', '/******************************************/' );
					$class_redsys->log->add( 'redsys', ' ' );
				}
				$redirect_to = add_query_arg( 'redsys_aprobe_preauthorizations', count( $post_ids ), $redirect_to );
				return $redirect_to;
			}
		}
	}
	/**
	 * Hide payment method Add Method
	 *
	 * @param array $available_gateways All available gateways.
	 */
	public function hide_payment_method_add_method( $available_gateways ) {

		if ( ! is_admin() && is_checkout() && 'yes' === $this->hidegatewaychckout ) {
			unset( $available_gateways[ $this->id ] );
		}
		return $available_gateways;
	}
	/**
	 * Hide payment method by country
	 *
	 * @param array $available_gateways All available gateways.
	 */
	public function hide_payment_method_by_country_redsys( $available_gateways ) {
		if ( is_admin() ) {
			return $available_gateways;
		}
		if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
			return $available_gateways;
		}
		if ( ! function_exists( 'WC' ) ) {
			return $available_gateways;
		}
		if ( ! isset( $available_gateways[ $this->id ] ) ) {
			return $available_gateways;
		}
		if ( ! is_checkout() ) {
			return $available_gateways;
		}
		$country = WC()->customer->get_billing_country();
		if ( ! isset( $this->bankingnetwork ) ) {
			return $available_gateways;
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'redsys', '$country: ' . $country );
			$this->log->add( 'redsys', '$showbankingnetwork: ' . $this->bankingnetwork );
		}
		if ( is_null( $country ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', '$country: is false' );
			}
			$country = false;
		}
		if ( isset( $available_gateways[ $this->id ] ) && ( 'ES' === $country || 'PT' === $country ) && 'showbankingnetwork' === $this->bankingnetwork ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Resultado: Es España o Portugal y es showbankingnetwork' );
			}
			unset( $available_gateways[ $this->id ] );
			return $available_gateways;
		} elseif ( isset( $available_gateways[ $this->id ] ) && 'showbankingnetwork' !== $this->bankingnetwork ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Resultado: No es showbankingnetwork' );
			}
			return $available_gateways;
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'Resultado: Se debe mostrar redirección en el checkout' );
			}
			return $available_gateways;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
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
			echo esc_html__( 'Warning: WooCommerce Redsys Gateway is in test mode. Remember to uncheck it when you go live', 'woocommerce-redsys' );
			echo '</div>';
		}
		echo '<noscript>' . esc_html__( 'Due to the European PSD2 regulation, we need to capture some screen configuration data that can only be done using JavaScript. You have JS disabled, and this increases the chances that the gateway reject your payment.', 'woocommerce-redsys' ) . '</noscript>';
	}
	/**
	 * Add JS to footer filling the fields for PSD2.
	 */
	public function add_js_footer_checkout() {
		/**
		 * Package: WooCommerce Redsys Gateway
		 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
		 * Copyright: (C) 2013 - 2023 José Conti
		 */

		if ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
			?>
			<script type="text/javascript">
			// Script necesario para capturar los datos a enviar a Redsys por la PSD2
			var RedsysDate = new Date();
			if (document.getElementById('billing_agente_navegador')) {
				document.getElementById('billing_agente_navegador').value = btoa(navigator.userAgent);
			}
			if (document.getElementById('billing_idioma_navegador')) {
				document.getElementById('billing_idioma_navegador').value = navigator.language;
			}
			if (document.getElementById('billing_js_enabled_navegador')) {
				document.getElementById('billing_js_enabled_navegador').value = navigator.javaEnabled();
			}
			if (document.getElementById('billing_altura_pantalla')) {
				document.getElementById('billing_altura_pantalla').value = screen.height;
			}
			if (document.getElementById('billing_anchura_pantalla')) {
				document.getElementById('billing_anchura_pantalla').value = screen.width;
			}
			if (document.getElementById('billing_profundidad_color')) {
				document.getElementById('billing_profundidad_color').value = screen.colorDepth;
			}
			if (document.getElementById('billing_diferencia_horaria')) {
				document.getElementById('billing_diferencia_horaria').value = RedsysDate.getTimezoneOffset();
			}
			if (document.getElementById('billing_tz_horaria')) {
				document.getElementById('billing_tz_horaria').value = RedsysDate.getTimezoneOffset();
			}
			<?php
			if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
				?>
				if ( document.getElementById( 'billing_http_accept_headers') ) {
					document.getElementById( 'billing_http_accept_headers').value = btoa( <?php echo '"' . esc_html( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) . '"'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?> );
				}
				<?php
			} else {
				?>
				if ( document.getElementById( 'billing_http_accept_headers') ) {
					document.getElementById( 'billing_http_accept_headers').value = btoa( "text\/html,application\/xhtml+xml,application\/xml;q=0.9,*\/*;q=0.8" );
				}
				<?php
			}
			?>

			</script>
			<style>
			/* CSS necesario para esconder de forma correcta los campos de captura de datos para Redsys por la PSD2 */
			.hidden.form-row-wide.redsys {
				display: none;
				visibility:hidden;
			}
			</style>
			<?php
		}
	}
	/**
	 * Add fields to checkout (Redsys needed fields).
	 *
	 * @param array $show_fields Fields to Add.
	 */
	public function override_checkout_fields( $show_fields ) {
		/**
		 * Package: WooCommerce Redsys Gateway
		 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
		 * Copyright: (C) 2013 - 2023 José Conti
		 */
		$show_fields['billing']['billing_agente_navegador']     = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_idioma_navegador']     = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_altura_pantalla']      = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_anchura_pantalla']     = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_profundidad_color']    = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_diferencia_horaria']   = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_http_accept_headers']  = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_tz_horaria']           = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		$show_fields['billing']['billing_js_enabled_navegador'] = array(
			'label'       => '',
			'placeholder' => '',
			'required'    => false,
			'clear'       => false,
			'type'        => 'hidden',
			'class'       => array( 'hidden form-row-wide redsys' ),
		);
		return $show_fields;
	}
	/**
	 * Priority fields to checkout (Redsys needed fields).
	 *
	 * @param array $fields Fields to Add.
	 */
	public function checkout_priority_fields( $fields ) {
		/**
		 * Package: WooCommerce Redsys Gateway
		 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
		 * Copyright: (C) 2013 - 2023 José Conti
		 */
		$fields['billing']['billing_agente_navegador']['priority']     = 120;
		$fields['billing']['billing_idioma_navegador']['priority']     = 120;
		$fields['billing']['billing_altura_pantalla']['priority']      = 120;
		$fields['billing']['billing_anchura_pantalla']['priority']     = 120;
		$fields['billing']['billing_profundidad_color']['priority']    = 120;
		$fields['billing']['billing_diferencia_horaria']['priority']   = 120;
		$fields['billing']['billing_http_accept_headers']['priority']  = 120;
		$fields['billing']['billing_tz_horaria']['priority']           = 120;
		$fields['billing']['billing_js_enabled_navegador']['priority'] = 120;
		return $fields;
	}
	/**
	 * Save fields to checkout (Redsys needed fields).
	 *
	 * @param int $order_id Order ID.
	 */
	public function save_field_update_order_meta( $order_id ) {

		if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ), 'woocommerce-process_checkout' ) && 'redsys' === sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) ) {
			$order   = WCRed()->get_order( $order_id );
			$user_id = $order->get_user_id();
			$data    = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'redsys', 'HTTP $_POST checkout received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( ! empty( $_POST['billing_http_accept_headers'] ) ) {
				$headers = base64_decode( sanitize_text_field( wp_unslash( $_POST['billing_http_accept_headers'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				$data['_accept_haders'] = sanitize_text_field( $headers );
			}
			if ( ! empty( $_POST['billing_agente_navegador'] ) ) {
				$agente = base64_decode( sanitize_text_field( wp_unslash( $_POST['billing_agente_navegador'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				$data['_billing_agente_navegador_field'] = sanitize_text_field( $agente );
			}
			if ( ! empty( $_POST['billing_idioma_navegador'] ) ) {
				$data['_billing_idioma_navegador_field'] = sanitize_text_field( wp_unslash( $_POST['billing_idioma_navegador'] ) );
			}
			if ( ! empty( $_POST['billing_altura_pantalla'] ) ) {
				$data['_billing_altura_pantalla_field'] = sanitize_text_field( wp_unslash( $_POST['billing_altura_pantalla'] ) );
			}
			if ( ! empty( $_POST['billing_anchura_pantalla'] ) ) {
				$data['_billing_anchura_pantalla_field'] = sanitize_text_field( wp_unslash( $_POST['billing_anchura_pantalla'] ) );
			}
			if ( ! empty( $_POST['billing_profundidad_color'] ) ) {
				$data['_billing_profundidad_color_field'] = sanitize_text_field( wp_unslash( $_POST['billing_profundidad_color'] ) );
			}
			if ( ! empty( $_POST['billing_diferencia_horaria'] ) ) {
				$data['_billing_diferencia_horaria_field'] = sanitize_text_field( wp_unslash( $_POST['billing_diferencia_horaria'] ) );
			}
			if ( ! empty( $_POST['billing_tz_horaria'] ) ) {
				$data['_billing_tz_horaria_field'] = sanitize_text_field( wp_unslash( $_POST['billing_tz_horaria'] ) );
			}
			if ( ! empty( $_POST['billing_js_enabled_navegador'] ) ) {
				$data['_billing_js_enabled_navegador_field'] = sanitize_text_field( wp_unslash( $_POST['billing_js_enabled_navegador'] ) );
			}
			if ( ! empty( $_POST['token'] ) && 'add' !== $_POST['token'] ) {
				set_transient( $order_id . '_redsys_use_token', sanitize_text_field( wp_unslash( $_POST['token'] ) ), 36000 );
			} else {
				set_transient( $order_id . '_redsys_use_token', 'no', 36000 );
			}
			if ( ! empty( $_POST['_redsys_token_type'] ) ) {
				set_transient( $order_id . '_redsys_token_type', sanitize_text_field( wp_unslash( $_POST['_redsys_token_type'] ) ), 36000 );
			} else {
				set_transient( $order_id . '_redsys_token_type', 'no', 36000 );
			}
			if ( ! empty( $_POST['_redsys_save_token'] ) ) {
				set_transient( $order_id . '_redsys_save_token', sanitize_text_field( wp_unslash( $_POST['_redsys_save_token'] ) ), 36000 );
			} else {
				set_transient( $order_id . '_redsys_save_token', 'no', 36000 );
			}
			WCRed()->update_order_meta( $order_id, $data );
			do_action( 'save_field_update_order_meta', $_POST );
		}
	}
	/**
	 * Check if user can see payment method
	 *
	 * @param  int $userid User ID.
	 * @return bool
	 */
	public function check_user_show_payment_method( $userid = false ) {

		$test_mode  = $this->testmode;
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'redsys' );

		if ( 'yes' !== $test_mode ) {
			return true;
		}
		if ( '' !== $selections[0] || empty( $selections ) ) {
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
	 * Check if user can see payment method
	 *
	 * @param  array $available_gateways Available gateways.
	 * @return array
	 */
	public function show_payment_method( $available_gateways ) {

		if ( ! is_admin() ) {
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$show    = $this->check_user_show_payment_method( $user_id );
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

	public function custom_jquery_checkout() {

		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		if ( isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] && 'redsys' === $_GET['method'] ) ) {
			$order_id     = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
			$url          = $final_notify_url;
			$current_page = get_permalink( wc_get_page_id( 'checkout' ) );
			?>
			<style>
				#open-popup {
					display: none;
					position: fixed;
					top: 0;
					bottom: 0;
					left: 0;
					right: 0;
					background-color: rgba(0, 0, 0, 0.5);
					z-index: 9999;
				}
				.popup-content {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					height: 550px;
					background-color: #fff;
				}
				#redsys-iframe {
					width: 100%;
					height: 100%;
				}
				#close-popup {
					background-color: #2C3E50;
					color: #fff;
				}
				@media only screen and (min-width: 280px) {
					.popup-content {
						width: 270px;
					}
				}
				@media only screen and (min-width: 320px) {
					.popup-content {
						width: 300px;
					}
				}
				@media only screen and (min-width: 400px) {
					.popup-content {
						width: 380px;
					}
				}
				@media only screen and (min-width: 480px) {
					.popup-content {
						width: 470px;
					}
				}
				@media only screen and (min-width: 768px) {
					.popup-content {
						width: 760px;
					}
				}
				@media only screen and (min-width: 992px) {
					.popup-content {
						width: 900px;
					}
				}
				@media only screen and (min-width: 1200px) {
					.popup-content {
						width: 900px;
					}
				}
			</style>
			<div id="open-popup">
				<div class="popup-content">
					<iframe id="redsys-iframe" src="" frameborder="0"></iframe>
					<button id="close-popup"><?php esc_html_e( 'Close', 'woocommerce-redsys' ); ?></button>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$.urlParam = function(name) {
						var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
						if (results == null) {
							return null;
						} else {
							console.log('order_id = ' + results[1] || 0 + '');
							return results[1] || 0;
						}
					}

					if ($('#payment_method_redsys').is(':checked')) {
						var order_id = $.urlParam('order_id');
						var domain = '<?php echo esc_url( $final_notify_url ); ?>';
						var url = domain + '&redsys-order-id=' + order_id + '&redsys-iframe=yes';
						if (order_id != null) {
							console.log('order_id = ' + order_id);
							$('#redsys-iframe').attr('src', url);
							$('#open-popup').fadeIn();
						}
					}

					$('body').on('click', '#close-popup', function() {
						var url = '<?php echo esc_url( $current_page ); ?>';
						$('#open-popup').fadeOut();
						window.location.href = url;
					});
				});
			</script>
			<?php
		}
	}
}
/**
 * Check sutomer can pay for SUMO Subscriptions
 *
 * @param bool $bool True or false.
 * @param int  $subscription_id Subscription ID.
 * @param obj  $renewal_order Renewal Order.
 * @return bool
 */
function redsys_can_charge_customer( $bool, $subscription_id, $renewal_order ) {
	return true;
}
add_filter( 'sumosubscriptions_is_redsys_preapproval_status_valid', 'redsys_can_charge_customer', 10, 3 );
/**
 * Renew SUMO Subscriptions
 * @param bool $bool True or false.
 * @param int  $subscription_id Subscription ID.
 * @param obj  $renewal_order Renewal Order.
 * @param bool $retry True or false.
 * 
 * @return bool
 */
function redsys_renew_sumo_subscription( $bool, $subscription_id, $renewal_order, $retry = false ) {
	$redsys = new WC_Gateway_Redsys();
	$result = $redsys->renew_sumo_subscription( $bool, $subscription_id, $renewal_order, $retry = false );
	return $result;
}
add_filter( 'sumosubscriptions_is_redsys_preapproved_payment_transaction_success', 'redsys_renew_sumo_subscription', 10, 3 );
