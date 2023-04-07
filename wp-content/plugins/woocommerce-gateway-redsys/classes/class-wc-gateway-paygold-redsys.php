<?php
/**
 * Class WC_Gateway_Paygold_Redsys
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
 * Copyright: (C) 2013 - 2023 José Conti
 */
class WC_Gateway_Paygold_Redsys extends WC_Payment_Gateway {
	var $notify_url;

	/**
	 * Constructor for the gateway.
	 *
	 * @return void
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function __construct() {

		$this->id = 'paygold';
		if ( ! empty( WCRed()->get_redsys_option( 'logo', 'paygold' ) ) ) {
			$logo_url   = WCRed()->get_redsys_option( 'logo', 'paygold' );
			$this->icon = apply_filters( 'woocommerce_redsys_icon', $logo_url );
		} else {
			$this->icon = apply_filters( 'woocommerce_redsys_icon', REDSYS_PLUGIN_URL_P . 'assets/images/paygold.png' );
		}
		$this->has_fields           = false;
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntrada?wsdl';
		$this->method_title         = __( 'PayGold (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'PayGold works sending an email or SMS with a payment link.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'paygold' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) ) );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->title            = WCRed()->get_redsys_option( 'title', 'paygold' );
		$this->multisitesttings = WCRed()->get_redsys_option( 'multisitesttings', 'paygold' );
		$this->ownsetting       = WCRed()->get_redsys_option( 'ownsetting', 'paygold' );
		$this->hideownsetting   = WCRed()->get_redsys_option( 'hideownsetting', 'paygold' );
		$this->description      = WCRed()->get_redsys_option( 'description', 'paygold' );
		$this->customer         = WCRed()->get_redsys_option( 'customer', 'paygold' );
		$this->merchantgroup    = WCRed()->get_redsys_option( 'merchantgroup', 'paygold' );
		$this->transactionlimit = WCRed()->get_redsys_option( 'transactionlimit', 'paygold' );
		$this->commercename     = WCRed()->get_redsys_option( 'commercename', 'paygold' );
		$this->terminal         = WCRed()->get_redsys_option( 'terminal', 'paygold' );
		$this->secretsha256     = WCRed()->get_redsys_option( 'secretsha256', 'paygold' );
		$this->customtestsha256 = WCRed()->get_redsys_option( 'customtestsha256', 'paygold' );
		$this->redsyslanguage   = WCRed()->get_redsys_option( 'redsyslanguage', 'paygold' );
		$this->debug            = WCRed()->get_redsys_option( 'debug', 'paygold' );
		$this->testforuser      = WCRed()->get_redsys_option( 'testforuser', 'paygold' );
		$this->testforuserid    = WCRed()->get_redsys_option( 'testforuserid', 'paygold' );
		$this->buttoncheckout   = WCRed()->get_redsys_option( 'buttoncheckout', 'paygold' );
		$this->butonbgcolor     = WCRed()->get_redsys_option( 'butonbgcolor', 'paygold' );
		$this->butontextcolor   = WCRed()->get_redsys_option( 'butontextcolor', 'paygold' );
		$this->descripredsys    = WCRed()->get_redsys_option( 'descripredsys', 'paygold' );
		$this->testshowgateway  = WCRed()->get_redsys_option( 'testshowgateway', 'paygold' );
		$this->showcheckout     = WCRed()->get_redsys_option( 'showcheckout', 'paygold' );
		$this->subject          = WCRed()->get_redsys_option( 'subject', 'paygold' );
		$this->expiration       = WCRed()->get_redsys_option( 'expitation', 'paygold' );
		$this->sms              = WCRed()->get_redsys_option( 'sms', 'paygold' );
		$this->bulkactions      = WCRed()->get_redsys_option( 'bulkactions', 'paygold' );
		$this->testmode         = 'no';
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			// 'refunds',
		);
		// Actions.
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_paygold' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method_add_method' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );
		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
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
	 * @since 6.0.0
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'PayGold', 'woocommerce-redsys' ); ?></h3>
		<p><?php esc_html_e( 'PayGold works sending an email or SMS with a payment link.', 'woocommerce-redsys' ); ?></p>
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
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'paygold' );

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
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'paygold' );
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
				'label'   => __( 'Enable PayGold', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'showcheckout'     => array(
				'title'   => __( 'Show PayGold', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => __( 'Show PayGold in the checkout. By default, PayGold is not shown in the checkout page.', 'woocommerce-redsys' ),
				'default' => 'no',
			),
			'multisitesttings' => array(
				'title'       => __( 'Use in Network', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Use this setting around all Network', 'woocommerce-redsys' ),
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
			'title'            => array(
				'title'       => __( 'Title', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Bizum', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'      => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via Bizum you can pay with your Bizum account.', 'woocommerce-redsys' ),
			),
			'logo'             => array(
				'title'       => __( 'Gateway logo at checkout', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add link to image logo for Gateway at checkout.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'subject'          => array(
				'title'       => __( 'Subject', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( ' This is the subject email when the link is send, Default: "Follow the link below to complete your purchase".', 'woocommerce-redsys' ),
				'default'     => __( 'Follow the link below to complete your purchase', 'woocommerce-redsys' ),
			),
			'sms'              => array(
				'title'       => __( 'SMS Text', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This is the txt send by the SMS. You must use @COMERCIO@, @IMPORTE@, @MONEDA@ and @URL@. Max 160 caracters.', 'woocommerce-redsys' ),
				'default'     => __( 'Thank\'s for shopping at @COMERCIO@. You must pay @IMPORTE@ @MONEDA@ at the following url: @URL@', 'woocommerce-redsys' ),
			),
			'expiration'       => array(
				'title'       => __( 'Expiration', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This is the expiration of the link that is sent in minutes. For example, if you want the link to be valid for one hour, you should put "60", or if you want it to be valid for 2 days, you should put "2280". Defaut 1440 (1 day)', 'woocommerce-redsys' ),
				'default'     => __( '1440', 'woocommerce-redsys' ),
			),
			'buttoncheckout'   => array(
				'title'       => __( 'Button Checkout Text', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Add the button text at the checkout.', 'woocommerce-redsys' ),
			),
			'butonbgcolor'     => array(
				'title'       => __( 'Button Color Background', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button Color Background Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'butontextcolor'   => array(
				'title'       => __( 'Color text Button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'This if button text color Place Order at Checkout', 'woocommerce-redsys' ),
				'class'       => 'colorpick',
			),
			'customer'         => array(
				'title'       => __( 'Commerce number (FUC)', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce number (FUC) provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'commercename'     => array(
				'title'       => __( 'Commerce Name', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Commerce Name', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'terminal'         => array(
				'title'       => __( 'Terminal number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Terminal number provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'merchantgroup'    => array(
				'title'       => __( 'Merchant Group Number', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'It is an identifier for sharing tokens between websites of the same company', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'transactionlimit' => array(
				'title'       => __( 'Transaction Limit', 'woo-redsys-gateway-light' ),
				'type'        => 'text',
				'description' => __( 'Maximum transaction price for the cart.', 'woo-redsys-gateway-light' ),
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
			'not_use_https'    => array(
				'title'       => __( 'HTTPS SNI Compatibility', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Activate SNI Compatibility.', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => sprintf( __( 'If you are using HTTPS and Redsys don\'t support your certificate, example Lets Encrypt, you can deactivate HTTPS notifications. WARNING: If you are forcing redirection to HTTPS with htaccess, you need to add an exception for notification URL', 'woocommerce-redsys' ) ),
			),
			'secretsha256'     => array(
				'title'       => __( 'Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'redsyslanguage'   => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
			),
			'bulkactions'      => array(
				'title'       => __( 'Enable Bulk actions', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable bulk actions in Users list', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'With this option you will be able to send a Paygold link to all your users so that they can add their credit cards easily.', 'woocommerce-redsys' ),
			),
			'debug'            => array(
				'title'       => __( 'Debug Log', 'woocommerce-redsys' ),
				'type'        => 'checkbox',
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log Pay Gold events, such as notifications requests, inside <code>WooCommerce > Status > Logs > paygold-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
		$redsyslanguages   = WCRed()->get_redsys_languages();

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
	 * Send Link Paygold
	 *
	 * @param int $order_id Order ID.
	 */
	public function send_link_paygold( $order_id ) {
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '/*************************/' );
			$this->log->add( 'paygold', '  Asking for PayGold Link ' );
			$this->log->add( 'paygold', '/*************************/' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '$order_id: ' . $order_id );
		}

		WCRed()->update_order_meta( $order, '_paygold_link', $link );
		return true;
	}
	/**
	 * Disable paygold if the cart total is greater than the limit
	 *
	 * @param array $available_gateways Available gateways.
	 */
	public function disable_paygold( $available_gateways ) {

		if ( ! is_admin() && WCRed()->is_gateway_enabled( 'paygold' ) ) {
			$total = (int) WC()->cart->total;
			$limit = (int) $this->transactionlimit;
			if ( ! empty( $limit ) && $limit > 0 ) {
				$result = $limit - $total;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '$total: ' . $total );
					$this->log->add( 'paygold', '$limit: ' . $limit );
					$this->log->add( 'paygold', '$result: ' . $result );
					$this->log->add( 'paygold', ' ' );
				}
				if ( $result > 0 ) {
					return $available_gateways;
				} else {
					unset( $available_gateways['paygold'] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Unset Paygold in checkout
	 *
	 * @param array $available_gateways Available gateways.
	 */
	public function show_payment_method_add_method( $available_gateways ) {

		if ( ! is_admin() && 'yes' !== $this->showcheckout ) {
			unset( $available_gateways['paygold'] );
		}
		return $available_gateways;
	}
	/**
	 * Get Paygold URL
	 *
	 * @param int    $user_id User ID.
	 * @param string $type Type.
	 */
	public function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', '          URL Test RD         ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', '          URL Test WS         ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = false;
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'paygold', ' ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', '          URL Test RD         ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'paygold', ' ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', '          URL Test WS         ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'paygold', ' ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', '          URL Live RD         ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'paygold', ' ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', '          URL Live WS         ' );
						$this->log->add( 'paygold', '/****************************/' );
						$this->log->add( 'paygold', ' ' );
					}
					$url = $this->liveurlws;
				}
			}
		}
		return $url;
	}
	/**
	 * Get Paygold SHA256
	 *
	 * @param int $user_id User ID.
	 */
	public function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/****************************/' );
				$this->log->add( 'paygold', '         SHA256 Test.         ' );
				$this->log->add( 'paygold', '/****************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				$sha256 = $customtestsha256;
			} else {
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} else {
			$user_test = false;
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', '      USER SHA256 Test.       ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					$sha256 = $customtestsha256;
				} else {
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'paygold', '/****************************/' );
					$this->log->add( 'paygold', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}
	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 */
	public function process_payment( $order_id ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', '         Process Payment       ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', ' ' );
		}

		$order                           = WCRed()->get_order( $order_id );
		$user_id                         = $order->get_user_id();
		$mi_obj                          = new WooRedsysAPIWS();
		$order_total_sign                = WCRed()->redsys_amount_format( $order->get_total() );
		$orderid2                        = WCRed()->prepare_order_number( $order_id );
		$customer                        = $this->customer;
		$transaction_type                = 'F';
		$currency_codes                  = WCRed()->get_currencies();
		$currency                        = $currency_codes[ get_woocommerce_currency() ];
		$secretsha256                    = $this->secretsha256;
		$url_ok                          = esc_attr( add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
		$product_description             = WCRed()->product_description( $order, 'paygold' );
		$merchant_name                   = $this->commercename;
		$redsys_adr                      = $this->liveurlws;
		$ds_merchant_terminal            = $this->terminal;
		$name                            = remove_accents( $order->get_billing_first_name() );
		$last_name                       = remove_accents( $order->get_billing_last_name() );
		$adress_ship_ship_addr_line1     = remove_accents( $order->get_billing_address_1() );
		$adress_ship_ship_addr_line2     = remove_accents( $order->get_billing_address_2() );
		$adress_ship_ship_addr_city      = remove_accents( $order->get_billing_city() );
		$adress_ship_ship_addr_state     = remove_accents( strtolower( $order->get_billing_state() ) );
		$adress_ship_ship_addr_post_code = remove_accents( $order->get_billing_postcode() );
		$adress_ship_ship_addr_country   = remove_accents( strtolower( $order->get_billing_country() ) );
		$text_libre1                     = '';
		$customermail                    = $order->get_billing_email();
		$ds_signature                    = '';
		$expiration                      = $this->expiration;
		$subject                         = remove_accents( $this->subject );
		$description                     = WCRed()->product_description( $order, 'paygold' );
		$p2f_xmldata                     = '&lt;![CDATA[&lt;nombreComprador&gt;' . $name . ' ' . $last_name . '&lt;&#47;nombreComprador&gt;&lt;direccionComprador&gt;' . $adress_ship_ship_addr_line1 . ' ' . $adress_ship_ship_addr_line2 . ', ' . $adress_ship_ship_addr_city . ', ' . $adress_ship_ship_addr_state . ', ' . $adress_ship_ship_addr_post_code . ', ' . $adress_ship_ship_addr_country . '&lt;&#47;direccionComprador&gt;&lt;textoLibre1&gt;' . $subject . '&lt;&#47;textoLibre1&gt;&lt;subjectMailCliente&gt;' . $subject . '&lt;&#47;subjectMailCliente&gt;]]&gt;';

		if ( ! $expiration ) {
			$expiration = $expiration;
		} else {
			$expiration = '1440';
		}
		if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		if ( ! empty( $this->merchantgroup ) ) {
			$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $this->merchantgroup . '</DS_MERCHANT_GROUP>';
		} else {
			$ds_merchant_group = '';
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', '$order_id: ' . $order_id );
			$this->log->add( 'paygold', '$user_id: ' . $user_id );
			$this->log->add( 'paygold', '$url_ok: ' . $url_ok );
			$this->log->add( 'paygold', '$order_total_sign: ' . $order_total_sign );
			$this->log->add( 'paygold', '$orderid2: ' . $orderid2 );
			$this->log->add( 'paygold', '$customer: ' . $customer );
			$this->log->add( 'paygold', '$transaction_type: ' . $transaction_type );
			$this->log->add( 'paygold', '$currency: ' . $currency );
			$this->log->add( 'paygold', '$secretsha256: ' . $secretsha256 );
			$this->log->add( 'paygold', '$product_description: ' . $product_description );
			$this->log->add( 'paygold', '$merchant_name: ' . $merchant_name );
			$this->log->add( 'paygold', '$redsys_adr: ' . $redsys_adr );
			$this->log->add( 'paygold', '$ds_merchant_terminal: ' . $ds_merchant_terminal );
			$this->log->add( 'paygold', '$ds_merchant_group: ' . $ds_merchant_group );
			$this->log->add( 'paygold', '$customermail: ' . $customermail );
			$this->log->add( 'paygold', '$p2f_xmldata: ' . $p2f_xmldata );
			$this->log->add( 'paygold', '$final_notify_url: ' . $final_notify_url );
			$this->log->add( 'paygold', '$expiration: ' . $expiration );
			$this->log->add( 'paygold', ' ' );
		}

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_PRODUCTDESCRIPTION>' . $description . '</DS_MERCHANT_PRODUCTDESCRIPTION>';
		$datos_entrada .= '<DS_MERCHANT_CUSTOMER_MAIL>' . $customermail . '</DS_MERCHANT_CUSTOMER_MAIL>';
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= '<DS_MERCHANT_P2F_EXPIRYDATE>' . $expiration . '</DS_MERCHANT_P2F_EXPIRYDATE>';
		$datos_entrada .= '<DS_MERCHANT_P2F_XMLDATA>' . $p2f_xmldata . '</DS_MERCHANT_P2F_XMLDATA>';
		$datos_entrada .= '<DS_MERCHANT_URLOK>' . $url_ok . '</DS_MERCHANT_URLOK>';
		$datos_entrada .= '</DATOSENTRADA>';

		$xml  = '<REQUEST>';
		$xml .= $datos_entrada;
		$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml .= '</REQUEST>';

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', '          The XML             ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', $xml );
			$this->log->add( 'paygold', ' ' );
		}
		$cliente  = new SoapClient( $redsys_adr );
		$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

		if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno   = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$codigo        = json_decode( $xml_retorno->CODIGO ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$redsys_order  = json_decode( $xml_retorno->OPERACION->Ds_Order ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$terminal      = json_decode( $xml_retorno->OPERACION->Ds_Terminal ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$currency_code = json_decode( $xml_retorno->OPERACION->Ds_Currency ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$response      = json_decode( $xml_retorno->OPERACION->Ds_Response ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$urlpago2fases = (string) $xml_retorno->OPERACION->Ds_UrlPago2Fases; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			WCRed()->set_order_paygold_link( $order_id, $urlpago2fases );
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', 'XML Response: ' . $xml_retorno->asXML() );
			$this->log->add( 'paygold', '$codigo: ' . $codigo );
			$this->log->add( 'paygold', '$redsys_order: ' . $redsys_order );
			$this->log->add( 'paygold', '$terminal: ' . $terminal );
			$this->log->add( 'paygold', '$response: ' . $response );
			$this->log->add( 'paygold', '$urlpago2fases: ' . $urlpago2fases );
		}

		if ( ( 0 === (int) $codigo ) && ( 9998 === (int) $response ) ) {
			return array(
				'result'   => 'success',
				'redirect' => $url_ok,
			);
		} else {
			$error = WCRed()->get_error_by_code( $codigo );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', 'There was an error, and the error is: ' . $error );
			}
			wc_add_notice( 'We are having trouble sending the link, please try again. The error was: ' . $error, 'error' );
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

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		$usesecretsha256 = $this->secretsha256;
		if ( $usesecretsha256 ) {
			$version          = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$data             = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$remote_sign      = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$mi_obj           = new WooRedsysAPI();
			$decodec          = $mi_obj->decodeMerchantParameters( $data );
			$order_id         = $mi_obj->getParameter( 'Ds_Order' );
			$ds_merchant_code = $mi_obj->getParameter( 'Ds_MerchantCode' );
			$ds_merchant_iden = $mi_obj->getParameter( 'Ds_Merchant_Identifier' );
			if ( $ds_merchant_iden ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Is add Method' );
				}
				return true;
			}
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = WCRed()->get_order_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'paygold', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'paygold', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'paygold', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->getParameter( 'Ds_Order' );
				$this->log->add( 'paygold', 'Order ID: ' . $order_id );
			}
			$order           = WCRed()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'paygold', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'paygold', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'paygold', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'paygold', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', 'Using $secretsha256 Transcient' );
					$this->log->add( 'paygold', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'paygold', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'paygold', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Received INVALID notification from Servired/RedSys' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		} else {
			$version           = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$data              = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$remote_sign       = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$mi_obj            = new WooRedsysAPI();
			$decodec           = $mi_obj->decodeMerchantParameters( $data );
			$order_id          = $mi_obj->getParameter( 'Ds_Order' );
			$ds_merchant_code  = $mi_obj->getParameter( 'Ds_MerchantCode' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = WCRed()->get_order_meta( $order2, '_redsys_secretsha256', true );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( $ds_merchant_code === $this->customer ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'paygold', '$remote_sign: ' . $remote_sign );
					$this->log->add( 'paygold', '$localsecret: ' . $localsecret );
				}
				return false;
			}
		}
	}

	/**
	 * Check for Paygold HTTP Notification
	 *
	 * @return void
	 */
	public function check_ipn_response() {
		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$_POST = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			wp_die( 'Pay Gold Notification Request Failure' );
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	/**
	 * Successful Payment!
	 *
	 * @param array $posted wp_post.
	 */
	public function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', '      successful_request      ' );
			$this->log->add( 'paygold', '/****************************/' );
			$this->log->add( 'paygold', ' ' );
		}

		$version     = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$data        = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing
		$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.NonceVerification.Missing

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '$version: ' . $version );
			$this->log->add( 'paygold', '$data: ' . $data );
			$this->log->add( 'paygold', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'paygold', ' ' );
		}

		$mi_obj            = new WooRedsysAPI();
		$usesecretsha256   = $this->secretsha256;
		$dscardnumbercompl = '';
		$dsexpiration      = '';
		$dsmerchantidenti  = '';
		$dscardnumber4     = '';
		$dsexpiryyear      = '';
		$dsexpirymonth     = '';
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
		$card_txnid        = $mi_obj->getParameter( 'Ds_Merchant_Cof_Txnid' );
		$dscargtype        = $mi_obj->getParameter( 'Ds_Card_Type' );
		$expiry_date       = $mi_obj->getParameter( 'Ds_ExpiryDate' );
		$dserrorcode       = $mi_obj->getParameter( 'Ds_ErrorCode' );
		$dpaymethod        = $mi_obj->getParameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia. Si se paga por Iupay o TC, no se utiliza.
		$response          = intval( $response );
		$secretsha256      = get_transient( 'redsys_signature_' . sanitize_title( $ordermi ) );
		$order1            = $ordermi;
		if ( ! $dsmerchantidenti ) {
			$order2 = WCRed()->clean_order_number( $order1 );
			$order  = WCRed()->get_order( (int) $order2 );
		}
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'paygold', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'paygold', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'paygold', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'paygold', 'Ds_Amount: ' . $total );
			$this->log->add( 'paygold', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'paygold', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'paygold', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'paygold', 'Ds_Response: ' . $response );
			$this->log->add( 'paygold', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'paygold', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'paygold', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'paygold', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'paygold', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'paygold', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'paygold', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'paygold', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'paygold', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'paygold', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'paygold', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'paygold', 'Ds_Merchant_Identifier: ' . $dsmerchantidenti );
			$this->log->add( 'paygold', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'paygold', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'paygold', 'Ds_Merchant_Cof_Txnid: ' . $card_txnid );
			$this->log->add( 'paygold', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'paygold', 'Ds_PayMethod: ' . $dpaymethod );
		}

		if ( $dsmerchantidenti ) {
			$token_type = get_transient( $ordermi . '_add_method_type_subcription' );
			$user_id    = get_transient( $ordermi . '_user_id_token' );
			$dscargtype = $mi_obj->getParameter( 'Ds_Card_Type' );
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

			$token = new WC_Payment_Token_CC();
			$token->set_token( $dsmerchantidenti );
			$token->set_gateway_id( 'redsys' );
			$token->set_user_id( $user_id );
			$token->set_card_type( WCRed()->get_card_brand( $dscardbrand ) );
			$token->set_last4( $dscardnumber4 );
			$token->set_expiry_month( $dsexpirymonth );
			$token->set_expiry_year( $dsexpiryyear );
			$token->set_default( true );
			$token->save();
			$token_id = $token->get_id();
			WCRed()->set_txnid( $token_id, $card_txnid );
			WCRed()->set_token_type( $token_id, $token_type );
			exit();
		}

		// refund.

		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'WCRed()->update_order_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded by Redsys', 'woocommerce-redsys' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woocommerce-redsys' ) );
			exit;
		}

		$response = intval( $response );
		if ( $response <= 99 ) {
			// authorized.
			$order_total_compare = number_format( $order->get_total(), 2, '', '' );
			// remove 0 from bigining.
			$order_total_compare = ltrim( $order_total_compare, '0' );
			$total               = ltrim( $total, '0' );
			if ( $order_total_compare !== $total ) {
				// amount does not match.
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}
				// Put this order on-hold for manual checking.
				/* translators: order an received are the amount */
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2&s).', 'woocommerce-redsys' ), $order_total_compare, $total ) );
				exit;
			}
			$authorisation_code = $id_trans;
			$data               = array();

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/****************************/' );
				$this->log->add( 'paygold', '      Saving Order Meta       ' );
				$this->log->add( 'paygold', '/****************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			if ( ! empty( $order1 ) ) {
				$data['_payment_order_number_redsys'] = $order1;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_payment_order_number_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				$data['_payment_date_redsys'] = $dsdate;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_payment_date_redsys saved: ' . $dsdate );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_payment_date_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				$data['_payment_terminal_redsys'] = $dstermnal;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_payment_terminal_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $dshour ) ) {
				$data['_payment_hour_redsys'] = $dshour;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_payment_hour_redsys saved: ' . $dshour );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_payment_hour_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $id_trans ) ) {
				$data['_authorisation_code_redsys'] = $authorisation_code;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_authorisation_code_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $currency_code ) ) {
				$data['_corruncy_code_redsys'] = $currency_code;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_corruncy_code_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			if ( ! empty( $dscardcountry ) ) {
				$data['_card_country_redsys'] = $dscardcountry;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_card_country_redsys NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			// This meta is essential for later use.
			if ( ! empty( $secretsha256 ) ) {
				$data['_redsys_secretsha256'] = $secretsha256;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '_redsys_secretsha256 NOT SAVED!!!' );
					$this->log->add( 'paygold', ' ' );
				}
			}
			WCRed()->update_order_meta( $order->get_id(), $data );
			// Payment completed.
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();
			if ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Bizum', 'woocommerce-redsys' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', 'Payment complete.' );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', '  The final has come, this story has ended  ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			do_action( 'paygold_post_payment_complete', $order->get_id() );
		} else {
			$ds_response_value = WCRed()->get_error( $response );
			$ds_error_value    = WCRed()->get_error( $dserrorcode );

			if ( $ds_response_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_response_value );
				WCRed()->update_order_meta( $order->get_id(), '_redsys_error_payment_ds_response_value', $ds_response_value );
			}

			if ( $ds_error_value ) {
				$order->add_order_note( __( 'Order cancelled by Redsys: ', 'woocommerce-redsys' ) . $ds_error_value );
				WCRed()->update_order_meta( $order->get_id(), '_redsys_error_payment_ds_response_value', $ds_error_value );
			}
			if ( 'yes' === $this->debug ) {
				if ( $ds_response_value ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', $ds_response_value );
				}
				if ( $ds_error_value ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', $ds_error_value );
				}
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', '  The final has come, this story has ended  ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			// Order cancelled.
			$order->update_status( 'cancelled', __( 'Order cancelled by Redsys Bizum', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Order cancelled by Redsys Bizum', 'woocommerce-redsys' ) );
			WC()->cart->empty_cart();
			if ( ! $ds_response_value ) {
				$ds_response_value = '';
			}
			if ( ! $ds_error_value ) {
				$ds_error_value = '';
			}
			$error = $ds_response_value . ' ' . $ds_error_value;
			do_action( 'paygold_post_payment_error', $order->get_id(), $error );
		}
	}
	/**
	 * Ask for refund
	 *
	 * @param  int    $order_id Order ID.
	 * @param  string $transaction_id Transaction ID.
	 * @param  float  $amount Amount.
	 */
	public function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = WCRed()->get_order( $order_id );
		$terminal       = WCRed()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', '/**************************/' );
			$this->log->add( 'paygold', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', '/**************************/' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'paygold', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'paygold', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
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

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', '**********************' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'paygold', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'paygold', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'paygold', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'paygold', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );

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

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', '*********************************' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $this->customer );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'paygold', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'paygold', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'paygold', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'paygold', ' ' );
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
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'paygold', '*********************************' );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
			}
			return $post_arg;
		}
		return true;
	}
	/**
	 * Check if the pingback is valid
	 *
	 * @param string $order_id Order ID.
	 */
	public function check_redsys_refund( $order_id ) {
		// check postmeta.
		$order        = WCRed()->get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'paygold', '*****************************************' );
			$this->log->add( 'paygold', ' ' );
			$this->log->add( 'paygold', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'paygold', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Process a refund if supported
	 *
	 * @param int    $order_id Order ID.
	 * @param float  $amount Refund amount.
	 * @param string $reason Refund reason.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id.
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = WCRed()->get_order_meta( $order_id, '_payment_order_number_redsys', true );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'paygold', __( '$order_id#: ', 'woocommerce-redsys' ) . $transaction_id );
		}
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'paygold', __( 'check_redsys_refund Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'paygold', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
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
				$this->log->add( 'paygold', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/********************************/' );
				$this->log->add( 'paygold', '  Refund complete by Redsys   ' );
				$this->log->add( 'paygold', '/********************************/' );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', '  The final has come, this story has ended  ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'paygold', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'paygold', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'paygold', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', ' ' );
					$this->log->add( 'paygold', '/******************************************/' );
					$this->log->add( 'paygold', '  The final has come, this story has ended  ' );
					$this->log->add( 'paygold', '/******************************************/' );
					$this->log->add( 'paygold', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'paygold', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
				$this->log->add( 'paygold', ' ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', '  The final has come, this story has ended  ' );
				$this->log->add( 'paygold', '/******************************************/' );
				$this->log->add( 'paygold', ' ' );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
		}
	}
	/**
	 * Add Bulk Actions
	 *
	 * @param array $bulk_actions Array of actions.
	 */
	public static function add_bulk_actions( $bulk_actions ) {

		if ( WCRed()->is_gateway_enabled( 'paygold' ) && 'yes' === WCRed()->get_redsys_option( 'bulkactions', 'paygold' ) ) {
			$bulk_actions['paygold_send_paygold_email_subscription_token'] = __( 'Subscription Token Send Pay Gold email', 'woocommerce-redsys' );
			$bulk_actions['paygold_send_paygold_email_oneclic_token']      = __( '1clic Token Send Pay Gold email', 'woocommerce-redsys' );
		}
		return $bulk_actions;
	}
	/**
	 * Bulk Actions Handler
	 *
	 * @param string $redirect_to Redirect URL.
	 * @param string $doaction Action.
	 * @param array  $user_ids Array of user IDs.
	 */
	public static function paygold_bulk_actions_handler( $redirect_to, $doaction, $user_ids ) {

		$class_redsys = new WC_Gateway_Paygold_Redsys();

		if ( 'yes' === $class_redsys->debug ) {
			$class_redsys->log->add( 'paygold', ' ' );
			$class_redsys->log->add( 'paygold', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			$class_redsys->log->add( 'paygold', '     paygold_bulk_actions_handler   ' );
			$class_redsys->log->add( 'paygold', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
			$class_redsys->log->add( 'paygold', '$redirect_to = ' . $redirect_to );
			$class_redsys->log->add( 'paygold', '$doaction = ' . $doaction );
			$class_redsys->log->add( 'paygold', '$user_ids = ' . print_r( $user_ids, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$class_redsys->log->add( 'paygold', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
		}

		if ( 'paygold_send_paygold_email_subscription_token' === $doaction ) {
			// Enviar enlace Paygol para conseguir Token Suscripción.
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'paygold', __( 'Doing Bulk Actions: paygold_send_paygold_email_subscription_token', 'woocommerce-redsys' ) );
			}
			foreach ( $user_ids as $user_id ) {
				$user_info   = get_userdata( $user_id );
				$user_email  = $user_info->user_email;
				$description = '';
				$data        = array(
					'user_id'     => $user_id,
					'token_type'  => 'R',
					'send_type'   => 'email',
					'send_to'     => $user_email,
					'description' => $description,
				);
				$result      = WCRed()->send_paygold_link( false, $data );
				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'paygold', ' ' );
					$class_redsys->log->add( 'paygold', '/******************************************/' );
					$class_redsys->log->add( 'paygold', '  Result for user ID ' . $user_id . ' > ' . $result );
					$class_redsys->log->add( 'paygold', '/******************************************/' );
					$class_redsys->log->add( 'paygold', ' ' );
				}
			}
			$redirect_to = add_query_arg( 'paygold_send_paygold_email_subscription_token', count( $user_ids ), $redirect_to );
			return $redirect_to;
		}

		if ( 'paygold_send_paygold_email_oneclic_token' === $doaction ) {
			// Enviar enlace Paygol para conseguir Token 1clic.
			if ( 'yes' === $class_redsys->debug ) {
				$class_redsys->log->add( 'paygold', __( 'Doing Bulk Actions: paygold_send_paygold_email_oneclic_token', 'woocommerce-redsys' ) );
			}
			foreach ( $user_ids as $user_id ) {
				$user_info   = get_userdata( $user_id );
				$user_email  = $user_info->user_email;
				$description = '';
				$data        = array(
					'user_id'     => $user_id,
					'token_type'  => 'C',
					'send_type'   => 'email',
					'send_to'     => $user_email,
					'description' => $description,
				);
				$result      = WCRed()->send_paygold_link( false, $data );
				if ( 'yes' === $class_redsys->debug ) {
					$class_redsys->log->add( 'paygold', ' ' );
					$class_redsys->log->add( 'paygold', '/******************************************/' );
					$class_redsys->log->add( 'paygold', '  Result for user ID ' . $user_id . ' > ' . $result );
					$class_redsys->log->add( 'paygold', '/******************************************/' );
					$class_redsys->log->add( 'paygold', ' ' );
				}
			}
			$redirect_to = add_query_arg( 'paygold_send_paygold_email_oneclic_token', count( $user_ids ), $redirect_to );
			return $redirect_to;
		}
	}
	/**
	 * Check if user can show payment method.
	 *
	 * @param  int $userid User ID.
	 * @return bool
	 */
	public function check_user_show_payment_method( $userid = false ) {

		$test_mode  = $this->testmode;
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'paygold' );

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
	 * Show payment method.
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
 * Add the gateway to woocommerce.
 *
 * @param array $methods Payment methods.
 */
function woocommerce_add_gateway_paygold_redsys( $methods ) {
		$methods[] = 'WC_Gateway_Paygold_Redsys';
		return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_paygold_redsys' );
