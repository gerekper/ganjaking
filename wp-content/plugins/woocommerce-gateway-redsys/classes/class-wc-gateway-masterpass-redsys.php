<?php
/**
 * MasterPass Gateway
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
 * Gateway class
 */
/**
 * Copyright: (C) 2013 - 2023 José Conti
 */
class WC_Gateway_MasterPass_Redsys extends WC_Payment_Gateway {
	var $notify_url;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		global $woocommerce;

		$this->id = 'masterpass';

		if ( ! empty( WCRed()->get_redsys_option( 'logo', 'masterpass' ) ) ) {
			$logo_url   = WCRed()->get_redsys_option( 'logo', 'masterpass' );
			$this->icon = apply_filters( 'woocommerce_masterpass_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_masterpass_icon', REDSYS_PLUGIN_URL_P . 'assets/images/masterpass.png' );
		}

		$this->has_fields         = false;
		$this->liveurl            = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl            = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->testmode           = WCRed()->get_redsys_option( 'testmode', 'masterpass' );
		$this->method_title       = __( 'MasterPass (by José Conti)', 'woocommerce-redsys' );
		$this->method_description = __( 'MasterPass works redirecting customers to MasterPass.', 'woocommerce-redsys' );
		$this->notify_url         = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables!
		$this->title              = WCRed()->get_redsys_option( 'title', 'masterpass' );
		$this->multisitesttings   = WCRed()->get_redsys_option( 'multisitesttings', 'masterpass' );
		$this->ownsetting         = WCRed()->get_redsys_option( 'ownsetting', 'masterpass' );
		$this->hideownsetting     = WCRed()->get_redsys_option( 'hideownsetting', 'masterpass' );
		$this->description        = WCRed()->get_redsys_option( 'description', 'masterpass' );
		$this->customer           = WCRed()->get_redsys_option( 'customer', 'masterpass' );
		$this->terminal           = WCRed()->get_redsys_option( 'terminal', 'masterpass' );
		$this->secretsha256       = WCRed()->get_redsys_option( 'secretsha256', 'masterpass' );
		$this->debug              = WCRed()->get_redsys_option( 'debug', 'masterpass' );
		$this->hashtype           = WCRed()->get_redsys_option( 'hashtype', 'masterpass' );
		$this->masterpasslanguage = WCRed()->get_redsys_option( 'masterpasslanguage', 'masterpass' );
		$this->woomasterpassurlko = WCRed()->get_redsys_option( 'woomasterpassurlko', 'masterpass' );
		$this->commercename       = WCRed()->get_redsys_option( 'woomasterpasscomercename', 'masterpass' );
		$this->buttoncheckout     = WCRed()->get_redsys_option( 'buttoncheckout', 'masterpass' );
		$this->butonbgcolor       = WCRed()->get_redsys_option( 'butonbgcolor', 'masterpass' );
		$this->butontextcolor     = WCRed()->get_redsys_option( 'butontextcolor', 'masterpass' );
		$this->descripredsys      = WCRed()->get_redsys_option( 'descripredsys', 'masterpass' );
		$this->testmode           = WCRed()->get_redsys_option( 'testmode', 'masterpass' );
		$this->log                = new WC_Logger();

		// Actions!
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Payment listener/API hook!
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode_masterpass' ) );
		// add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function is_valid_for_use() {
		include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';
		if ( ! in_array( get_woocommerce_currency(), WCRed()->allowed_currencies(), true ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Admin Panel Options.
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
			<h3><?php esc_html_e( 'MasterPass', 'woocommerce-redsys' ); ?></h3>
			<p><?php esc_html_e( 'MasterPass works by sending the user to your bank TPV to enter their payment information.', 'woocommerce-redsys' ); ?></p>
			<?php
				WCRed()->return_help_notice();
			if ( class_exists( 'SitePress' ) ) {
				?>
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
					include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';
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
				</p></div>
					<?php
			endif;
	}
	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable MasterPass', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'multisitesttings'   => array(
				'title'       => __( 'Use in Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Use this setting around all Network', 'woocommerce-redsys' ),
				'description' => '',
				'default'     => 'no',
			),
			'hideownsetting'     => array(
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
			'buttoncheckout'     => array(
				'title'       => __( 'Button Checkout Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add the button text at the checkout.', 'woocommerce-redsys' ),
			),
			'butonbgcolor'       => array(
				'title'       => __( 'Button Color Background', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button Color Background Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'butontextcolor'     => array(
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
			'descripredsys'      => array(
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
			'testmode'           => array(
				'title'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woocommerce-redsys' ) ),
			),
			'debug'              => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log MasterPass events, such as notifications requests, inside <code>WooCommerce > Status > Logs > masterpass-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
		$redsyslanguages   = WCRed()->get_redsys_languages();

		foreach ( $redsyslanguages as $redsyslanguage => $valor ) {
			$this->form_fields['masterpasslanguage']['options'][ $redsyslanguage ] = $valor;
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
	 * Get error message by error code
	 *
	 * @param string $error_code Error code.
	 */
	public function get_error_by_code( $error_code ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', ' ' );
			$this->log->add( 'masterpass', '/****************************/' );
			$this->log->add( 'masterpass', '     DS Error Code: ' . $error_code );
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
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function get_currencies() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', ' ' );
			$this->log->add( 'masterpass', '/******************************/' );
			$this->log->add( 'masterpass', '  Loading currencies array()    ' );
			$this->log->add( 'masterpass', '/******************************/' );
			$this->log->add( 'masterpass', ' ' );
		}

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'currencies.php';

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
	 * @param WC_Order $order Order object.
	 */
	public function get_masterpass_args( $order ) {
		global $woocommerce;

		$order_id         = $order->id;
		$currency_codes   = $this->get_currencies();
		$transaction_id2  = WCRed()->prepare_order_number( $order_id );
		$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		$transaction_type = '0';
		$secretsha256     = utf8_decode( $this->secretsha256 );
		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
		} elseif ( $this->masterpasslanguage ) {
			$gatewaylanguage = $this->masterpasslanguage;
		} else {
			$gatewaylanguage = '001';
		}

		if ( $this->woomasterpassurlko ) {
			if ( 'returncancel' === $this->woomasterpassurlko ) {
				$$returnfrommasterpass = $order->get_cancel_order_url();
			} else {
				$$returnfrommasterpass = $woocommerce->cart->get_checkout_url();
			}
		} else {
			$$returnfrommasterpass = $order->get_cancel_order_url();
		}
		$ds_merchant_terminal = $this->terminal;

		// redsys Args.
		$mi_obj = new WooRedsysAPI();
		$mi_obj->setParameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $this->customer );
		$mi_obj->setParameter( 'DS_MERCHANT_CURRENCY', $currency_codes[ get_woocommerce_currency() ] );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $ds_merchant_terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTURL', $this->notify_url );
		$mi_obj->setParameter( 'DS_MERCHANT_URLOK', add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$mi_obj->setParameter( 'DS_MERCHANT_URLKO', $returnfrommasterpass );
		$mi_obj->setParameter( 'DS_CONSUMERLANGUAGE', $gatewaylanguage );
		$mi_obj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', __( 'Order', 'woocommerce-redsys' ) . ' ' . $order->get_order_number() );
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $this->commercename );
		$mi_obj->setParameter( 'Ds_Merchant_PayMethods', 'N' );

		$version         = 'HMAC_SHA256_V1';
		$request         = '';
		$params          = $mi_obj->createMerchantParameters();
		$signature       = $mi_obj->createMerchantSignature( $secretsha256 );
		$masterpass_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $masterpass_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		$masterpass_args = apply_filters( 'woocommerce_masterpass_args', $masterpass_args );
		return $masterpass_args;
	}
	/**
	 * Generate the redsys form
	 *
	 * @param mixed $order_id Order ID.
	 */
	public function generate_masterpass_form( $order_id ) {
		global $woocommerce;

		$usesecretsha256 = $this->secretsha256;
		$order           = new WC_Order( $order_id );
		if ( 'yes' === $this->testmode ) {
			$masterpass_adr = $this->testurl;
		} else {
			$masterpass_adr = $this->liveurl;
		}
		$masterpass_args = $this->get_masterpass_args( $order );
		$form_inputs     = '';
		foreach ( $masterpass_args as $key => $value ) {
			$form_inputs .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
		}
		wc_enqueue_js(
			'
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
			'
		);
			return '<form action="' . esc_url( $masterpass_adr ) . '" method="post" id="masterpass_payment_form" target="_top">
			' . $form_inputs . '<input type="submit" class="button-alt" id="submit_masterpass_payment_form" value="' . __( 'Pay with MasterPass account', 'woocommerce-redsys' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
		</form>';
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 */
	public function process_payment( $order_id ) {

		$order = new WC_Order( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @param WC_Order $order Order object.
	 */
	public function receipt_page( $order ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with MasterPass.', 'woocommerce-redsys' ) . '</p>';
		$allowed_html = array(
			'input' => array(
				'type'  => array(),
				'name'  => array(),
				'value' => array(),
				'class' => array(),
				'id'    => array(),
			),
			'form'  => array(
				'action' => array(),
				'method' => array(),
				'id'     => array(),
				'target' => array(),
			),
			'a'     => array(
				'href'  => array(),
				'class' => array(),
			),
		);
		echo wp_kses( $this->generate_masterpass_form( $order ), $allowed_html );
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
		global $woocommerce;

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		$usesecretsha256 = $this->secretsha256;
		if ( $usesecretsha256 ) {
			$version     = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
			$data        = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
			$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
			$mi_obj      = new WooRedsysAPI();
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );

			if ( $localsecret === $remote_sign ) {
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
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'masterpass', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( $data ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'masterpass', 'Received valid notification from MasterPass' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'masterpass', 'Received INVALID notification from MasterPass' );
				}
				return false;
			}
		}
	}

	/**
	 * Check for Servired/RedSys HTTP Notification
	 */
	public function check_ipn_response() {

		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$_POST = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			wp_die( 'MasterPass Notification Request Failure' );
		}
	}

	/**
	 * Successful Payment!
	 *
	 * @param array $posted Post data after notify.
	 */
	public function successful_request( $posted ) {
		global $woocommerce;

		$version     = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$data        = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$mi_obj      = new WooRedsysAPI();

		$decodedata        = $mi_obj->decodeMerchantParameters( $data );
		$localsecret       = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
		$total             = $mi_obj->getParameter( 'Ds_Amount' );
		$ordermi           = $mi_obj->getParameter( 'Ds_Order' );
		$dscode            = $mi_obj->getParameter( 'Ds_MerchantCode' );
		$currency_code     = $mi_obj->getParameter( 'Ds_Currency' );
		$response          = $mi_obj->getParameter( 'Ds_Response' );
		$id_trans          = $mi_obj->getParameter( 'Ds_AuthorisationCode' );
		$dsdate            = $mi_obj->getParameter( 'Ds_Date' );
		$dshour            = $mi_obj->getParameter( 'Ds_Hour' );
		$dstermnal         = $mi_obj->getParameter( 'Ds_Terminal' );
		$dsmerchandata     = $mi_obj->getParameter( 'Ds_MerchantData' );
		$dssucurepayment   = $mi_obj->getParameter( 'Ds_SecurePayment' );
		$dscardcountry     = $mi_obj->getParameter( 'Ds_Card_Country' );
		$dsconsumercountry = $mi_obj->getParameter( 'Ds_ConsumerLanguage' );
		$dscargtype        = $mi_obj->getParameter( 'Ds_Card_Type' );
		$order1            = $ordermi;
		$order2            = WCRed()->clean_order_number( $order1 );
		$order             = WCRed()->get_order( (int) $order2 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'masterpass', 'Ds_Amount: ' . $total . ', Ds_Order: ' . $order1 . ',  Ds_MerchantCode: ' . $dscode . ', Ds_Currency: ' . $currency_code . ', Ds_Response: ' . $response . ', Ds_AuthorisationCode: ' . $id_trans . ', $order2: ' . $order2 );
		}

		$response = intval( $response );
		if ( $response <= 99 ) {
			// authorized.
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			if ( $order_total_compare !== $total ) {
				// amount does not match.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'masterpass', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}

				// Put this order on-hold for manual checking.
				$order->update_status( 'on-hold', sprintf( esc_html__( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2$s).', 'woocommerce-redsys' ), $order_total_compare, $total ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				exit;
			}
			$authorisation_code = $id_trans;
			$data               = array();
			if ( ! empty( $order1 ) ) {
				$data['_payment_order_number_masterpass'] = $order1;
			}
			if ( ! empty( $dsdate ) ) {
				$data['_payment_date_redsys'] = $dsdate;
			}
			if ( ! empty( $dshour ) ) {
				$data['_payment_hour_redsys'] = $dshour;
			}
			if ( ! empty( $id_trans ) ) {
				$data['_authorisation_code_redsys'] = $authorisation_code;
			}
			if ( ! empty( $dscardcountry ) ) {
				$data['_card_country_masterpass'] = $dscardcountry;
			}
			WCRed()->update_order_meta( $order->id, $data );

			// Payment completed.
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorisation code: ', 'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();
			do_action( 'masterpass_post_payment_complete', $order->get_id() );
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
						WCRed()->update_order_meta( $order_id, '_redsys_error_payment_ds_response_value', $ds_response_value );
					}
				}
			}

			if ( ! empty( $ds_errors ) ) {
				foreach ( $ds_errors as $ds_error => $value ) {
					if ( $ds_error === $dserrorcode ) {
						$ds_error_value = $value;
						$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_error_value );
						WCRed()->update_order_meta( $order_id, '_redsys_error_payment_ds_error_value', $ds_error_value );
					}
				}
			}

			// Tarjeta caducada.
			if ( 'yes' === $this->debug ) {
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

			// Order cancelled.
			$order->update_status( 'cancelled', __( 'Cancelled by MasterPass', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Order canceled by MasterPass', 'woocommerce-redsys' ) );
			WC()->cart->empty_cart();
			if ( ! $ds_response_value ) {
				$ds_response_value = '';
			}
			if ( ! $ds_error_value ) {
				$ds_error_value = '';
			}
			$error = $ds_response_value . ' ' . $ds_error_value;
			do_action( 'masterpass_post_payment_error', $order->get_id(), $error );
		}
	}

	/**
	 * Get_masterpass_order function.
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_masterpass_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function warning_checkout_test_mode_masterpass() {
		if ( 'yes' === $this->testmode && WCRed()->is_gateway_enabled( $this->id ) ) {
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
			echo esc_html__( 'Warning: WooCommerce Redsys Gateway MasterPass is in test mode. Remember to uncheck it when you go live', 'woo-redsys-gateway-light' );
			echo '</div>';
		}
	}
	/**
	 * Check if user is in test mode
	 *
	 * @param int $userid User ID.
	 */
	public function check_user_show_payment_method( $userid = false ) {

		$test_mode  = $this->testmode;
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'masterpass' );

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
	 * Show payment method
	 *
	 * @param array $available_gateways Available gateways.
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
}
/**
 * Add the gateway to WooCommerce.
 *
 * @param array $methods WooCommerce payment methods.
 */
function woocommerce_add_gateway_masterpass_gateway( $methods ) {
	$methods[] = 'WC_Gateway_MasterPass_Redsys';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_masterpass_gateway' );
