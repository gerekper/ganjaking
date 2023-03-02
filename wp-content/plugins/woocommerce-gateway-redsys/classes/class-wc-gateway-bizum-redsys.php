<?php
/**
 * Bizum Gateway
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
 * WC_Gateway_Bizum_Redsys Class.
 */
class WC_Gateway_Bizum_Redsys extends WC_Payment_Gateway {
	var $notify_url;

	/**
	 * Constructor for the gateway.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->id                   = 'bizumredsys';
		$this->icon                 = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL_P . 'assets/images/bizum.png' );
		$this->has_fields           = false;
		$this->liveurl              = 'https://sis.redsys.es/sis/realizarPago';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		$this->liveurlws            = 'https://sis.redsys.es/sis/services/SerClsWSEntrada?wsdl';
		$this->testurlws            = 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada?wsdl';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = WCRed()->get_redsys_option( 'testmode', 'bizumredsys' );
		$this->method_title         = __( 'Bizum (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'Bizum works redirecting customers to Bizum.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'bizumredsys' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) ) );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->title            = WCRed()->get_redsys_option( 'title', 'bizumredsys' );
		$this->multisitesttings = WCRed()->get_redsys_option( 'multisitesttings', 'bizumredsys' );
		$this->ownsetting       = WCRed()->get_redsys_option( 'ownsetting', 'bizumredsys' );
		$this->hideownsetting   = WCRed()->get_redsys_option( 'hideownsetting', 'bizumredsys' );
		$this->description      = WCRed()->get_redsys_option( 'description', 'bizumredsys' );
		$this->customer         = WCRed()->get_redsys_option( 'customer', 'bizumredsys' );
		$this->transactionlimit = WCRed()->get_redsys_option( 'transactionlimit', 'bizumredsys' );
		$this->commercename     = WCRed()->get_redsys_option( 'commercename', 'bizumredsys' );
		$this->terminal         = WCRed()->get_redsys_option( 'terminal', 'bizumredsys' );
		$this->secretsha256     = WCRed()->get_redsys_option( 'secretsha256', 'bizumredsys' );
		$this->customtestsha256 = WCRed()->get_redsys_option( 'customtestsha256', 'bizumredsys' );
		$this->redsyslanguage   = WCRed()->get_redsys_option( 'redsyslanguage', 'bizumredsys' );
		$this->debug            = WCRed()->get_redsys_option( 'debug', 'bizumredsys' );
		$this->testforuser      = WCRed()->get_redsys_option( 'testforuser', 'bizumredsys' );
		$this->testforuserid    = WCRed()->get_redsys_option( 'testforuserid', 'bizumredsys' );
		$this->buttoncheckout   = WCRed()->get_redsys_option( 'buttoncheckout', 'bizumredsys' );
		$this->butonbgcolor     = WCRed()->get_redsys_option( 'butonbgcolor', 'bizumredsys' );
		$this->butontextcolor   = WCRed()->get_redsys_option( 'butontextcolor', 'bizumredsys' );
		$this->descripredsys    = WCRed()->get_redsys_option( 'descripredsys', 'bizumredsys' );
		$this->testshowgateway  = WCRed()->get_redsys_option( 'testshowgateway', 'bizumredsys' );
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			'refunds',
		);
		// Actions.
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'warning_checkout_test_mode_bizum' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_bizum' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'show_payment_method' ) );
		// La siguiente línea carga el JS para el iframe. Por si algun dia deja Bizum estar en un iframe
		// add_action( 'woocommerce_after_checkout_form', array( $this, 'custom_jquery_checkout' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
	}
	/**
	 * Check if this gateway is enabled and available with the current currency.
	 *
	 * @return bool
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
	public function admin_options() {
		?>
		<h3><?php esc_html_e( 'Bizum', 'woocommerce-redsys' ); ?></h3>
		<p><?php esc_html_e( 'Bizum works by sending the user to Bizum Gateway', 'woocommerce-redsys' ); ?></p>
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
	public function init_form_fields() {

		$options    = array();
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'bizumredsys' );

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
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'bizumredsys' );
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
				'label'   => __( 'Enable Bizum', 'woocommerce-redsys' ),
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
			'customtestsha256' => array(
				'title'       => __( 'TEST MODE: Encryption secret passphrase SHA-256', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Encryption secret passphrase SHA-256 provided by your bank for test mode.', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'redsyslanguage'   => array(
				'title'       => __( 'Language Gateway', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Choose the language for the Gateway. Not all Banks accept all languages', 'woocommerce-redsys' ),
				'default'     => '001',
				'options'     => array(),
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
				'default'     => 'yes',
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
				'label'       => __( 'Running in test mode', 'woocommerce-redsys' ),
				'label'       => __( 'Enable logging', 'woocommerce-redsys' ),
				'default'     => 'no',
				'description' => __( 'Log Bizum events, such as notifications requests, inside <code>WooCommerce > Status > Logs > bizum-{date}-{number}.log</code>', 'woocommerce-redsys' ),
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
	 * Check if this gateway is enabled in test mode for a user
	 *
	 * @param int $userid User ID.
	 *
	 * @return bool
	 */
	public function check_user_test_mode( $userid ) {

		$usertest_active = $this->testforuser;
		$selections      = (array) WCRed()->get_redsys_option( 'testforuserid', 'bizumredsys' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '     Checking user test       ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}

		if ( 'yes' === $usertest_active ) {

			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '   Checking user ' . $userid );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '  User in forach ' . $user_id );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', '/****************************/' );
							$this->log->add( 'bizumredsys', '   Checking user test TRUE    ' );
							$this->log->add( 'bizumredsys', '/****************************/' );
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', ' ' );
							$this->log->add( 'bizumredsys', '/********************************************/' );
							$this->log->add( 'bizumredsys', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'bizumredsys', '/********************************************/' );
							$this->log->add( 'bizumredsys', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '  Checking user test continue ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '     User test Disabled.      ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			return false;
		}
	}
	/**
	 * Check if this gateway is enabled by price
	 *
	 * @param array $available_gateways Available gateways.
	 *
	 * @return bool
	 */
	public function disable_bizum( $available_gateways ) {
		global $woocommerce;

		if ( ! is_admin() && WCRed()->is_gateway_enabled( 'bizumredsys' ) ) {
			$total = (int) $woocommerce->cart->get_cart_total();
			$limit = (int) $this->transactionlimit;
			if ( ! empty( $limit ) && $limit > 0 ) {
				$result = $limit - $total;
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '$total: ' . $total );
					$this->log->add( 'bizumredsys', '$limit: ' . $limit );
					$this->log->add( 'bizumredsys', '$result: ' . $result );
					$this->log->add( 'bizumredsys', ' ' );
				}
				if ( $result > 0 ) {
					return $available_gateways;
				} else {
					unset( $available_gateways['bizumredsys'] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Get redsys URL
	 *
	 * @param int  $user_id User ID.
	 * @param bool $type Type.
	 *
	 * @return string
	 */
	public function get_redsys_url_gateway( $user_id, $type = 'rd' ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'rd' === $type ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '          URL Test RD         ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '          URL Test WS         ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Test RD         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Test WS         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Live RD         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'bizumredsys', ' ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', '          URL Live WS         ' );
						$this->log->add( 'bizumredsys', '/****************************/' );
						$this->log->add( 'bizumredsys', ' ' );
					}
					$url = $this->liveurlws;
				}
			}
		}
		return $url;
	}
	/**
	 * Get redsys SHA256
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string
	 */
	public function get_redsys_sha256( $user_id ) {

		if ( 'yes' === $this->testmode ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '         SHA256 Test.         ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			$customtestsha256 = utf8_decode( $this->customtestsha256 );
			if ( ! empty( $customtestsha256 ) ) {
				$sha256 = $customtestsha256;
			} else {
				$sha256 = utf8_decode( $this->testsha256 );
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '      USER SHA256 Test.       ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					$sha256 = $customtestsha256;
				} else {
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'bizumredsys', '/****************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$sha256 = utf8_decode( $this->secretsha256 );
			}
		}
		return $sha256;
	}
	/**
	 * Get redsys Args for passing to PP
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return array
	 */
	public function get_redsys_args( $order ) {

		$order_id            = $order->get_id();
		$currency_codes      = WCRed()->get_currencies();
		$transaction_id2     = WCRed()->prepare_order_number( $order_id );
		$order_total_sign    = WCRed()->redsys_amount_format( $order->get_total() );
		$transaction_type    = '0';
		$user_id             = $order->get_user_id();
		$secretsha256        = $this->get_redsys_sha256( $user_id );
		$customer            = $this->customer;
		$url_ok              = add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) );
		$product_description = WCRed()->product_description( $order, $this->id );
		$merchant_name       = $this->commercename;
		$currency            = $currency_codes[ get_woocommerce_currency() ];
		$name                = WCRed()->get_order_meta( $order_id, '_billing_first_name', true );
		$lastname            = WCRed()->get_order_meta( $order_id, '_billing_last_name', true );

		if ( class_exists( 'SitePress' ) ) {
			$gatewaylanguage = WCRed()->get_lang_code( ICL_LANGUAGE_CODE );
		} elseif ( $this->redsyslanguage ) {
			$gatewaylanguage = $this->redsyslanguage;
		} else {
			$gatewaylanguage = '001';
		}
		$returnfromredsys   = $order->get_cancel_order_url();
		$dsmerchantterminal = $this->terminal;
		if ( 'yes' === $this->not_use_https ) {
				$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}

		$bizum_data_send = array(
			'order_total_sign'    => $order_total_sign,
			'transaction_id2'     => $transaction_id2,
			'transaction_type'    => $transaction_type,
			'DSMerchantTerminal'  => $dsmerchantterminal,
			'final_notify_url'    => $final_notify_url,
			'returnfromredsys'    => $returnfromredsys,
			'gatewaylanguage'     => $gatewaylanguage,
			'currency'            => $currency,
			'secretsha256'        => $secretsha256,
			'customer'            => $customer,
			'url_ok'              => $url_ok,
			'product_description' => $product_description,
			'merchant_name'       => $merchant_name,
			'name'                => $name,
			'lastname'            => $lastname,
		);

		if ( has_filter( 'bizum_modify_data_to_send' ) ) {

			$bizum_data_send = apply_filters( 'bizum_modify_data_to_send', $bizum_data_send );

			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'bizumredsys', ' ' );
				$redsys->log->add( 'bizumredsys', 'Using filter bizum_modify_data_to_send' );
				$redsys->log->add( 'bizumredsys', ' ' );
			}
		}

		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'bizumredsys', ' ' );
			$redsys->log->add( 'bizumredsys', 'Date sent to Bizum, $bizum_data_send: ' . print_r( $bizum_data_send, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$redsys->log->add( 'bizumredsys', ' ' );
		}

		// redsys Args.
		$miobj = new WooRedsysAPI();
		$miobj->setParameter( 'DS_MERCHANT_AMOUNT', $bizum_data_send['order_total_sign'] );
		$miobj->setParameter( 'DS_MERCHANT_ORDER', $bizum_data_send['transaction_id2'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $bizum_data_send['customer'] );
		$miobj->setParameter( 'DS_MERCHANT_CURRENCY', $bizum_data_send['currency'] );
		$miobj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $bizum_data_send['name'] ) . ' ' . WCRed()->clean_data( $bizum_data_send['lastname'] ) );
		$miobj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $bizum_data_send['transaction_type'] );
		$miobj->setParameter( 'DS_MERCHANT_TERMINAL', $bizum_data_send['DSMerchantTerminal'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTURL', $bizum_data_send['final_notify_url'] );
		$miobj->setParameter( 'DS_MERCHANT_URLOK', $bizum_data_send['url_ok'] );
		$miobj->setParameter( 'DS_MERCHANT_URLKO', $bizum_data_send['returnfromredsys'] );
		$miobj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $bizum_data_send['gatewaylanguage'] );
		$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', $bizum_data_send['product_description'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $bizum_data_send['merchant_name'] );
		$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'z' );

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request      = '';
		$params       = $miobj->createMerchantParameters();
		$signature    = $miobj->createMerchantSignature( $bizum_data_send['secretsha256'] );
		$order_id_set = $bizum_data_send['transaction_id2'];
		set_transient( 'redsys_signature_' . sanitize_text_field( $order_id_set ), $bizum_data_send['secretsha256'], 600 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'bizumredsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_AMOUNT: ' . $bizum_data_send['order_total_sign'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_ORDER: ' . $bizum_data_send['transaction_id2'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TITULAR: ' . WCRed()->clean_data( $bizum_data_send['name'] ) . ' ' . WCRed()->clean_data( $bizum_data_send['lastname'] ) );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $bizum_data_send['customer'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CURRENCY' . $bizum_data_send['currency'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $bizum_data_send['transaction_type'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_TERMINAL: ' . $bizum_data_send['DSMerchantTerminal'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_MERCHANTURL: ' . $bizum_data_send['final_notify_url'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLOK: ' . $bizum_data_send['url_ok'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_URLKO: ' . $bizum_data_send['returnfromredsys'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $bizum_data_send['gatewaylanguage'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $bizum_data_send['product_description'] );
			$this->log->add( 'bizumredsys', 'DS_MERCHANT_PAYMETHODS: z' );
		}
		$redsys_args = apply_filters( 'woocommerce_redsys_args', $redsys_args );
		return $redsys_args;
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
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '   Generating Redsys Form     ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
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
	jQuery("#submit_redsys_payment_form").click();
	'
		);
		return '<form action="' . esc_url( $redsys_adr ) . '" method="post" id="redsys_payment_form" target="_top">
		' . implode( '', $form_inputs ) . '
		<input type="submit" class="button-alt" id="submit_redsys_payment_form" value="' . __( 'Pay with Bizum', 'woocommerce-redsys' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-redsys' ) . '</a>
	</form>';
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = WCRed()->get_order( $order_id );
		/*
		// Lo dejo aquí por si Bizum decide que se pueda utilizar un iframe.
		return array(
			'result'   => 'success',
			'redirect' => '?order_id=' . $order_id . '#bizum-open-popup',
			'order_id' => $order_id,
			'url'      => $order->get_checkout_payment_url( true ),
		);
		*/
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @param obj $order Order object.
	 */
	public function receipt_page( $order ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay with Bizum.', 'woocommerce-redsys' ) . '</p>';
		echo $this->generate_redsys_form( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check redsys IPN validity
	 */
	public function check_ipn_request_is_valid() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}
		$usesecretsha256 = $this->secretsha256;
		if ( ! isset( $_POST['Ds_SignatureVersion'] ) || ! isset( $_POST['Ds_MerchantParameters'] ) || ! isset( $_POST['Ds_Signature'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return false;
		}
		if ( $usesecretsha256 ) {
			$version           = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$data              = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$remote_sign       = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$mi_obj            = new WooRedsysAPI();
			$decodec           = $mi_obj->decodeMerchantParameters( $data );
			$order_id          = $mi_obj->getParameter( 'Ds_Order' );
			$ds_merchant_code  = $mi_obj->getParameter( 'Ds_MerchantCode' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_text_field( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = WCRed()->get_order_meta( $order2, '_redsys_secretsha256', true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'bizumredsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'bizumredsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'bizumredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->getParameter( 'Ds_Order' );
				$this->log->add( 'bizumredsys', 'Order ID: ' . $order_id );
			}
			$order           = WCRed()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', 'Using $secretsha256 Transcient' );
					$this->log->add( 'bizumredsys', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'bizumredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'bizumredsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received INVALID notification from Servired/RedSys' );
				}
				delete_transient( 'redsys_signature_' . sanitize_title( $order_id ) );
				return false;
			}
		} else {
			$version           = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$data              = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$remote_sign       = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$mi_obj            = new WooRedsysAPI();
			$decodec           = $mi_obj->decodeMerchantParameters( $data );
			$order_id          = $mi_obj->getParameter( 'Ds_Order' );
			$ds_merchant_code  = $mi_obj->getParameter( 'Ds_MerchantCode' );
			$secretsha256      = get_transient( 'redsys_signature_' . sanitize_text_field( $order_id ) );
			$order1            = $order_id;
			$order2            = WCRed()->clean_order_number( $order1 );
			$secretsha256_meta = WCRed()->get_order_meta( $order2, '_redsys_secretsha256', true );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( $ds_merchant_code === $this->customer ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
					$this->log->add( 'bizumredsys', '$localsecret: ' . $localsecret );
				}
				return false;
			}
		}
	}

	/**
	 * Check for Bizum HTTP Notification
	 */
	public function check_ipn_response() {
		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$_POST = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_GET['bizum-order-id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_id = sanitize_text_field( wp_unslash( $_GET['bizum-order-id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wc_nocache_headers();
			$order       = WCRed()->get_order( $order_id );
			$user_id     = $order->get_user_id();
			$redsys_adr  = self::get_redsys_url_gateway( $user_id );
			$redsys_args = self::get_redsys_args( $order );
			$form_inputs = array();

			foreach ( $redsys_args as $key => $value ) {
				$form_inputs[] .= '<input type="hidden" name="' . $key . '" value="' . esc_attr( $value ) . '" />';
			}

			if ( isset( $_GET['bizum-iframe'] ) && 'yes' === $_GET['bizum-iframe'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
				
				<form action="' . esc_url( $redsys_adr ) . '" method="post" id="bizum_payment_form" target="bizumiframe">
				' . implode( '', $form_inputs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				. '
				</form>
					<iframe name="bizumiframe" src="" class="iframe_3DS_Challenge" allowfullscreen></iframe>';
				echo '<script>document.getElementById("bizum_payment_form").submit();</script>';
				exit();
			}
		}
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			wp_die( 'Bizum Notification Request Failure' );
		}
	}
	/**
	 * Successful Payment.
	 *
	 * @param array $posted Post data after notify.
	 */
	public function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', '      successful_request      ' );
			$this->log->add( 'bizumredsys', '/****************************/' );
			$this->log->add( 'bizumredsys', ' ' );
		}

		if ( ! isset( $_POST['Ds_SignatureVersion'] ) || ! isset( $_POST['Ds_Signature'] ) || ! isset( $_POST['Ds_MerchantParameters'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_die( 'Do not access this page directly ' );
		}

		$version     = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data        = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '$version: ' . $version );
			$this->log->add( 'bizumredsys', '$data: ' . $data );
			$this->log->add( 'bizumredsys', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'bizumredsys', ' ' );
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
		$dscargtype        = $mi_obj->getParameter( 'Ds_Card_Type' );
		$dserrorcode       = $mi_obj->getParameter( 'Ds_ErrorCode' );
		$dpaymethod        = $mi_obj->getParameter( 'Ds_PayMethod' ); // D o R, D: Domiciliacion, R: Transferencia. Si se paga por Iupay o TC, no se utiliza.
		$response          = intval( $response );
		$secretsha256      = get_transient( 'redsys_signature_' . sanitize_text_field( $ordermi ) );
		$order1            = $ordermi;
		$order2            = WCRed()->clean_order_number( $order1 );
		$order             = WCRed()->get_order( (int) $order2 );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'bizumredsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'bizumredsys', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'bizumredsys', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'bizumredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'bizumredsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'bizumredsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'bizumredsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'bizumredsys', 'Ds_Response: ' . $response );
			$this->log->add( 'bizumredsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'bizumredsys', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'bizumredsys', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'bizumredsys', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'bizumredsys', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'bizumredsys', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'bizumredsys', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'bizumredsys', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'bizumredsys', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'bizumredsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'bizumredsys', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'bizumredsys', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'bizumredsys', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'bizumredsys', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'bizumredsys', 'Ds_PayMethod: ' . $dpaymethod );
		}

		// refund.
		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'WCRed()->update_order_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', 'New Status in request: ' . $status );
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
					$this->log->add( 'bizumredsys', 'Payment error: Amounts do not match (order: ' . $order_total_compare . ' - received: ' . $total . ')' );
				}
				// Put this order on-hold for manual checking.
				/* translators: order an received are the amount */
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2&s).', 'woocommerce-redsys' ), $order_total_compare, $total ) );
				exit;
			}
			$authorisation_code = $id_trans;

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', '      Saving Order Meta       ' );
				$this->log->add( 'bizumredsys', '/****************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( ! empty( $order1 ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_order_number_redsys', $order1 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_order_number_redsys saved: ' . $order1 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_order_number_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_date_redsys', $dsdate );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_date_redsys saved: ' . $dsdate );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_date_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dsdate ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_terminal_redsys', $dstermnal );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_terminal_redsys saved: ' . $dstermnal );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_terminal_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dshour ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_payment_hour_redsys', $dshour );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_payment_hour_redsys saved: ' . $dshour );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_payment_hour_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $id_trans ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_authorisation_code_redsys', $authorisation_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_authorisation_code_redsys saved: ' . $authorisation_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_authorisation_code_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $currency_code ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_corruncy_code_redsys', $currency_code );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_corruncy_code_redsys saved: ' . $currency_code );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_corruncy_code_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dscardcountry ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_card_country_redsys', $dscardcountry );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_country_redsys saved: ' . $dscardcountry );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_card_country_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dscargtype ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_card_type_redsys', 'C' === $dscargtype ? 'Credit' : 'Debit' );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_card_type_redsys saved: ' . $dscargtype );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_card_type_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			// This meta is essential for later use.
			if ( ! empty( $secretsha256 ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_redsys_secretsha256', $secretsha256 );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_redsys_secretsha256 NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			if ( ! empty( $dscode ) ) {
				WCRed()->update_order_meta( $order->get_id(), '_order_fuc_redsys', $dscode );
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', '_order_fuc_redsys: ' . $dscode );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '_order_fuc_redsys NOT SAVED!!!' );
					$this->log->add( 'bizumredsys', ' ' );
				}
			}
			// Payment completed.
			$order->add_order_note( __( 'HTTP Notification received - payment completed', 'woocommerce-redsys' ) );
			$order->add_order_note( __( 'Authorization code: ', 'woocommerce-redsys' ) . $authorisation_code );
			$order->payment_complete();

			if ( 'completed' === $this->orderdo ) {
				$order->update_status( 'completed', __( 'Order Completed by Bizum', 'woocommerce-redsys' ) );
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', 'Payment complete.' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			do_action( 'bizum_post_payment_complete', $order->get_id() );
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
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', $ds_response_value );
				}
				if ( $ds_error_value ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', $ds_error_value );
				}
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
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
			do_action( 'bizum_post_payment_error', $order->get_id(), $error );
		}
	}
	/**
	 * Ask for Refund
	 *
	 * @param  int    $order_id Order ID.
	 * @param  string $transaction_id Transaction ID.
	 * @param  float  $amount Amount.
	 * @return bool|WP_Error
	 */
	public function ask_for_refund( $order_id, $transaction_id, $amount ) {

		// post code to REDSYS.
		$order          = WCRed()->get_order( $order_id );
		$terminal       = WCRed()->get_order_meta( $order_id, '_payment_terminal_redsys', true );
		$currency_codes = WCRed()->get_currencies();
		$user_id        = $order->get_user_id();
		$secretsha256   = $this->get_redsys_sha256( $user_id );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', '/**************************/' );
			$this->log->add( 'bizumredsys', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', '/**************************/' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'bizumredsys', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'bizumredsys', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
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
		$order_fuc         = WCRed()->get_order_meta( $order_id, '_order_fuc_redsys', true );

		if ( ! $order_fuc ) {
			$order_fuc = $this->customer;
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', '**********************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'bizumredsys', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'bizumredsys', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'bizumredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'bizumredsys', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );
			$this->log->add( 'bizumredsys', __( 'FUC : ', 'woocommerce-redsys' ) . $order_fuc );
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
		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $order_fuc );
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
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', '*********************************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $order_fuc );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'bizumredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'bizumredsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'bizumredsys', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'bizumredsys', ' ' );
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
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'bizumredsys', '*********************************' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
			}
			return $post_arg;
		}
		return true;
	}
	/**
	 * Check if the ping is from Redsys
	 *
	 * @param  int $order_id Order ID.
	 * @return bool
	 */
	public function check_redsys_refund( $order_id ) {
		// check postmeta.
		$order        = WCRed()->get_order( (int) $order_id );
		$order_refund = get_transient( $order->get_id() . '_redsys_refund' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'bizumredsys', '*****************************************' );
			$this->log->add( 'bizumredsys', ' ' );
			$this->log->add( 'bizumredsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'bizumredsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
		}
		if ( 'yes' === $order_refund ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Process a refund if supported.
	 *
	 * @param  int    $order_id Order ID.
	 * @param  float  $amount Refund amount.
	 * @param  string $reason Refund reason.
	 * @return bool True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		// Do your refund here. Refund $amount for the order with ID $order_id _transaction_id.
		set_time_limit( 0 );
		$order = wc_get_order( $order_id );

		$transaction_id = WCRed()->get_redsys_order_number( $order_id );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'bizumredsys', __( '$order_id#: ', 'woocommerce-redsys' ) . $transaction_id );
		}
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'bizumredsys', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
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
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/********************************/' );
				$this->log->add( 'bizumredsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'bizumredsys', '/********************************/' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'bizumredsys', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'bizumredsys', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'bizumredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', ' ' );
					$this->log->add( 'bizumredsys', '/******************************************/' );
					$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'bizumredsys', '/******************************************/' );
					$this->log->add( 'bizumredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'bizumredsys', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
				$this->log->add( 'bizumredsys', ' ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'bizumredsys', '/******************************************/' );
				$this->log->add( 'bizumredsys', ' ' );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
		}
	}
	/**
	 * Warning when Bizum is in test mode.
	 */
	public function warning_checkout_test_mode_bizum() {
		if ( 'yes' === $this->testmode && WCRed()->is_gateway_enabled( $this->id ) ) {
			echo '<div class="checkout-message" style="
			background-color: rgb(3, 166, 120);
			padding: 1em 1.618em;
			margin-bottom: 2.617924em;
			margin-left: 0;
			border-radius: 2px;
			color: #fff;
			clear: both;
			border-left: 0.6180469716em solid rgb(1, 152, 117);
			">';
			echo esc_html__( 'Warning: WooCommerce Redsys Gateway Bizum is in test mode. Remember to uncheck it when you go live', 'woocommerce-redsys' );
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
		$selections = (array) WCRed()->get_redsys_option( 'testshowgateway', 'bizumredsys' );

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
	 * Check if show gateway.
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
	/**
	 * Add custom jQuery to checkout page.
	 */
	public function custom_jquery_checkout() {

		if ( 'yes' === $this->not_use_https ) {
			$final_notify_url = $this->notify_url_not_https;
		} else {
			$final_notify_url = $this->notify_url;
		}
		if ( isset( $_GET['order_id'] ) && ! empty( $_GET['order_id'] ) ) {
			$order_id     = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
			$url          = $final_notify_url;
			$current_page = get_permalink( wc_get_page_id( 'checkout' ) );
			$order        = WCRed()->get_order( $order_id );
			?>
			<style>
				#bizum-open-popup {
					display: none;
					position: fixed;
					top: 0;
					bottom: 0;
					left: 0;
					right: 0;
					background-color: rgba(0, 0, 0, 0.5);
					z-index: 9999;
				}
				.bizum-popup-content {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					height: 550px;
					background-color: #fff;
				}
				#bizum-iframe {
					width: 100%;
					height: 100%;
				}
				@media only screen and (min-width: 280px) {
					.bizum-popup-content {
						width: 270px;
					}
				}
				@media only screen and (min-width: 320px) {
					.bizum-popup-content {
						width: 300px;
					}
				}
				@media only screen and (min-width: 400px) {
					.bizum-popup-content {
						width: 380px;
					}
				}
				@media only screen and (min-width: 480px) {
					.bizum-popup-content {
						width: 470px;
					}
				}
				@media only screen and (min-width: 768px) {
					.bizum-popup-content {
						width: 760px;
					}
				}
				@media only screen and (min-width: 992px) {
					.bizum-popup-content {
						width: 900px;
					}
				}
				@media only screen and (min-width: 1200px) {
					.bizum-popup-content {
						width: 900px;
					}
				}
			</style>
			<div id="bizum-open-popup">
				<div class="bizum-popup-content">
					<iframe id="bizum-iframe" src="" frameborder="0"></iframe>
					<button id="bizum-close-popup"><?php esc_html_e( 'Close', 'woocommerce-redsys' ); ?></button>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$.urlParam = function(name){
						var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
						if (results==null){
						return null;
						}
						else{
						console.log('order_id = ' + results[1] || 0 + '');
						return results[1] || 0;
						}
					}
					$(document).ready(function() {
						if ( $( '#payment_method_bizumredsys' ).is( ':checked' ) ) {
							var order_id = $.urlParam('order_id');
						var domain   = '<?php echo $final_notify_url; ?>';
						var url = domain + '&bizum-order-id=' + order_id + '&bizum-iframe=yes';
							if ( order_id != null ) {
								console.log('order_id = ' + order_id );
								$('#bizum-iframe').attr('src', url );
								$('#bizum-open-popup').fadeIn();
							}
						}
					});
					$(document).ready(function() {
						$( 'body' ).on( 'click', '#bizum-close-popup', function() {
							var url = '<?php echo esc_url( $current_page ); ?>';
							$('#bizum-open-popup').fadeOut();
							window.location.href = url;
						});
					});
				});
			</script>
			<?php
		}
	}
}
/**
 * Add the gateway to WooCommerce
 *
 * @param array $methods WooCommerce payment methods.
 */
function woocommerce_add_gateway_bizum_redsys( $methods ) {
		$methods[] = 'WC_Gateway_Bizum_Redsys';
		return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_bizum_redsys' );
