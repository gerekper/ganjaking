<?php
/**
 * Google Pay Gateway
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 22.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Gateway_Googlepay_Checkout class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Googlepay_Checkout extends WC_Payment_Gateway {

	var $notify_url;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                   = 'googlepayredsys';
		$this->icon                 = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL_P . 'assets/images/googlepay.png' );
		$this->has_fields           = true;
		$this->liveurl              = 'https://sis.redsys.es/sis/rest/trataPeticionREST';
		$this->testurl              = 'https://sis-t.redsys.es:25443/sis/rest/trataPeticionREST';
		$this->testsha256           = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
		$this->testmode             = WCRed()->get_redsys_option( 'testmode', 'googlepayredsys' );
		$this->method_title         = __( 'Google Pay Checkout (by José Conti)', 'woocommerce-redsys' );
		$this->method_description   = __( 'Google Pay Checkout adding the Gpay Button in the checkout.', 'woocommerce-redsys' );
		$this->not_use_https        = WCRed()->get_redsys_option( 'not_use_https', 'googlepayredsys' );
		$this->notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) );
		$this->notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_' . $this->id, home_url( '/' ) ) );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->g_merchant_id    = WCRed()->get_redsys_option( 'g_merchant_id', 'googlepayredsys' );
		$this->xpay_type        = 'Google';
		$this->xpay_origen      = 'WEB';
		$this->title            = WCRed()->get_redsys_option( 'title', 'googlepayredsys' );
		$this->multisitesttings = WCRed()->get_redsys_option( 'multisitesttings', 'googlepayredsys' );
		$this->ownsetting       = WCRed()->get_redsys_option( 'ownsetting', 'googlepayredsys' );
		$this->hideownsetting   = WCRed()->get_redsys_option( 'hideownsetting', 'googlepayredsys' );
		$this->description      = WCRed()->get_redsys_option( 'description', 'googlepayredsys' );
		$this->customer         = WCRed()->get_redsys_option( 'customer', 'googlepayredsys' );
		$this->commercename     = WCRed()->get_redsys_option( 'commercename', 'googlepayredsys' );
		$this->terminal         = WCRed()->get_redsys_option( 'terminal', 'googlepayredsys' );
		$this->secretsha256     = WCRed()->get_redsys_option( 'secretsha256', 'googlepayredsys' );
		$this->customtestsha256 = WCRed()->get_redsys_option( 'customtestsha256', 'googlepayredsys' );
		$this->redsyslanguage   = WCRed()->get_redsys_option( 'redsyslanguage', 'googlepayredsys' );
		$this->debug            = WCRed()->get_redsys_option( 'debug', 'googlepayredsys' );
		$this->time_load_button = WCRed()->get_redsys_option( 'time_load_button', 'googlepayredsys' );
		$this->testforuser      = WCRed()->get_redsys_option( 'testforuser', 'googlepayredsys' );
		$this->testforuserid    = WCRed()->get_redsys_option( 'testforuserid', 'googlepayredsys' );
		$this->buttoncheckout   = WCRed()->get_redsys_option( 'buttoncheckout', 'googlepayredsys' );
		$this->butonbgcolor     = WCRed()->get_redsys_option( 'butonbgcolor', 'googlepayredsys' );
		$this->butontextcolor   = WCRed()->get_redsys_option( 'butontextcolor', 'googlepayredsys' );
		$this->descripredsys    = WCRed()->get_redsys_option( 'descripredsys', 'googlepayredsys' );
		$this->testshowgateway  = WCRed()->get_redsys_option( 'testshowgateway', 'googlepayredsys' );
		$this->button_color     = WCRed()->get_redsys_option( 'button_color', 'googlepayredsys' );
		$this->button_type      = WCRed()->get_redsys_option( 'button_type', 'googlepayredsys' );
		$this->button_locale    = WCRed()->get_redsys_option( 'button_locale', 'googlepayredsys' );
		$this->log              = new WC_Logger();
		$this->supports         = array(
			'products',
			'refunds',
		);
		add_action( 'valid_' . $this->id . '_standard_ipn_request', array( $this, 'successful_request' ) );

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Payment listener/API hook.
		add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_ipn_response' ) );

		// Google Pay JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_pay_js' ) );

		// Save checkout data form Google Pay.
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_field_update_order_meta' ) );

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
		<h3><?php esc_html_e( 'Google Pay', 'woocommerce-redsys' ); ?></h3>
		<p><?php esc_html_e( 'Google Pay works by Showing a Google Pay buttom at Checkout', 'woocommerce-redsys' ); ?></p>
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
		$selections = (array) WCRed()->get_redsys_option( 'testforuserid', 'googlepayredsys' );

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
		$selections_show = (array) WCRed()->get_redsys_option( 'testshowgateway', 'googlepayredsys' );
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
				'label'   => __( 'Enable Google Pay', 'woocommerce-redsys' ),
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
				'default'     => __( 'Gpay', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'description'      => array(
				'title'       => __( 'Description', 'woocommerce-redsys' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-redsys' ),
				'default'     => __( 'Pay via Gpay With your Google Account.', 'woocommerce-redsys' ),
			),
			'g_merchant_id'    => array(
				'title'       => __( 'Google Merchant ID', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Google Merchant ID', 'woocommerce-redsys' ),
				'desc_tip'    => true,
			),
			'time_load_button' => array(
				'title'       => __( 'Time to load the button', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Time to load the button in milliseconds', 'woocommerce-redsys' ),
				'default'     => '2500',
				'desc_tip'    => true,
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
			'button_color'     => array(
				'title'       => __( 'GPay Button Color', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose GPay button color.', 'woocommerce-redsys' ),
				'default'     => 'black',
				'options'     => array(
					'black' => __( 'Black', 'woocommerce-redsys' ),
					'white' => __( 'White', 'woocommerce-redsys' ),
				),
			),
			'button_type'      => array(
				'title'       => __( 'GPay Button Type', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose Button Type.', 'woocommerce-redsys' ),
				'default'     => 'buy',
				'options'     => array(
					'buy'       => __( 'buy', 'woocommerce-redsys' ),
					'book'      => __( 'book', 'woocommerce-redsys' ),
					'checkout'  => __( 'checkout', 'woocommerce-redsys' ),
					'donate'    => __( 'donate', 'woocommerce-redsys' ),
					'order'     => __( 'order', 'woocommerce-redsys' ),
					'pay'       => __( 'pay', 'woocommerce-redsys' ),
					'plain'     => __( 'plain', 'woocommerce-redsys' ),
					'subscribe' => __( 'subscribe', 'woocommerce-redsys' ),
				),
			),
			'button_locale'    => array(
				'title'       => __( 'GPay Button Language', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Chose Button Language.', 'woocommerce-redsys' ),
				'default'     => 'es',
				'options'     => array(
					'es' => __( 'Spanish', 'woocommerce-redsys' ),
					'ar' => __( 'Arabic', 'woocommerce-redsys' ),
					'bg' => __( 'Bulgarian', 'woocommerce-redsys' ),
					'ca' => __( 'Catalan', 'woocommerce-redsys' ),
					'zh' => __( 'Chinese', 'woocommerce-redsys' ),
					'hr' => __( 'Croatian', 'woocommerce-redsys' ),
					'cs' => __( 'Czech', 'woocommerce-redsys' ),
					'da' => __( 'Danish', 'woocommerce-redsys' ),
					'nl' => __( 'Dutch', 'woocommerce-redsys' ),
					'en' => __( 'English', 'woocommerce-redsys' ),
					'et' => __( 'Estonian', 'woocommerce-redsys' ),
					'fi' => __( 'Finnish', 'woocommerce-redsys' ),
					'fr' => __( 'French', 'woocommerce-redsys' ),
					'de' => __( 'German', 'woocommerce-redsys' ),
					'el' => __( 'Greek', 'woocommerce-redsys' ),
					'id' => __( 'Indonesian', 'woocommerce-redsys' ),
					'it' => __( 'Italian', 'woocommerce-redsys' ),
					'ja' => __( 'Japanese', 'woocommerce-redsys' ),
					'ko' => __( 'Korean', 'woocommerce-redsys' ),
					'ms' => __( 'Malay', 'woocommerce-redsys' ),
					'no' => __( 'Norwegian', 'woocommerce-redsys' ),
					'pl' => __( 'Polish', 'woocommerce-redsys' ),
					'pt' => __( 'Portuguese', 'woocommerce-redsys' ),
					'ru' => __( 'Russian', 'woocommerce-redsys' ),
					'sr' => __( 'Serbian', 'woocommerce-redsys' ),
					'sk' => __( 'Slovak', 'woocommerce-redsys' ),
					'sl' => __( 'Slovenian', 'woocommerce-redsys' ),
					'es' => __( 'Spanish', 'woocommerce-redsys' ),
					'sv' => __( 'Swedish', 'woocommerce-redsys' ),
					'th' => __( 'Thai', 'woocommerce-redsys' ),
					'tr' => __( 'Turkish', 'woocommerce-redsys' ),
					'uk' => __( 'Ukrainian', 'woocommerce-redsys' ),
				),
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
				'description' => __( 'Log Gpay events, such as notifications requests, inside <code>WooCommerce > Status > Logs > googlepayredsys-{date}-{number}.log</code>', 'woocommerce-redsys' ),
			),
		);
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
		$selections      = (array) WCRed()->get_redsys_option( 'testforuserid', 'googlepayredsys' );
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', '/****************************/' );
			$this->log->add( 'googlepayredsys', '     Checking user test       ' );
			$this->log->add( 'googlepayredsys', '/****************************/' );
			$this->log->add( 'googlepayredsys', ' ' );
		}

		if ( 'yes' === $usertest_active ) {

			if ( ! empty( $selections ) ) {
				foreach ( $selections as $user_id ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '   Checking user ' . $userid );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '  User in forach ' . $user_id );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
					if ( (string) $user_id === (string) $userid ) {
						if ( 'yes' === $this->debug ) {
							$this->log->add( 'googlepayredsys', ' ' );
							$this->log->add( 'googlepayredsys', '/****************************/' );
							$this->log->add( 'googlepayredsys', '   Checking user test TRUE    ' );
							$this->log->add( 'googlepayredsys', '/****************************/' );
							$this->log->add( 'googlepayredsys', ' ' );
							$this->log->add( 'googlepayredsys', ' ' );
							$this->log->add( 'googlepayredsys', '/********************************************/' );
							$this->log->add( 'googlepayredsys', '  User ' . $userid . ' is equal to ' . $user_id );
							$this->log->add( 'googlepayredsys', '/********************************************/' );
							$this->log->add( 'googlepayredsys', ' ' );
						}
						return true;
					}
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '  Checking user test continue ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
					continue;
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				return false;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '  Checking user test FALSE    ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '/****************************/' );
				$this->log->add( 'googlepayredsys', '     User test Disabled.      ' );
				$this->log->add( 'googlepayredsys', '/****************************/' );
				$this->log->add( 'googlepayredsys', ' ' );
			}
			return false;
		}
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
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '          URL Test RD         ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$url = $this->testurl;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '          URL Test WS         ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$url = $this->testurlws;
			}
		} else {
			$user_test = $this->check_user_test_mode( $user_id );
			if ( $user_test ) {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '          URL Test RD         ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
					$url = $this->testurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '          URL Test WS         ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
					$url = $this->testurlws;
				}
			} else {
				if ( 'rd' === $type ) {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '          URL Live RD         ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
					$url = $this->liveurl;
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', '          URL Live WS         ' );
						$this->log->add( 'googlepayredsys', '/****************************/' );
						$this->log->add( 'googlepayredsys', ' ' );
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
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '/****************************/' );
				$this->log->add( 'googlepayredsys', '         SHA256 Test.         ' );
				$this->log->add( 'googlepayredsys', '/****************************/' );
				$this->log->add( 'googlepayredsys', ' ' );
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
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '      USER SHA256 Test.       ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$customtestsha256 = utf8_decode( $this->customtestsha256 );
				if ( ! empty( $customtestsha256 ) ) {
					$sha256 = $customtestsha256;
				} else {
					$sha256 = utf8_decode( $this->testsha256 );
				}
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '     USER SHA256 NOT Test.    ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
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

		$gpay_data_send = array(
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

		if ( has_filter( 'gpay_modify_data_to_send' ) ) {

			$gpay_data_send = apply_filters( 'gpay_modify_data_to_send', $gpay_data_send );

			if ( 'yes' === $redsys->debug ) {
				$redsys->log->add( 'googlepayredsys', ' ' );
				$redsys->log->add( 'googlepayredsys', 'Using filter gpay_modify_data_to_send' );
				$redsys->log->add( 'googlepayredsys', ' ' );
			}
		}

		if ( 'yes' === $redsys->debug ) {
			$redsys->log->add( 'googlepayredsys', ' ' );
			$redsys->log->add( 'googlepayredsys', 'Data sent to Gpay, $gpay_data_send: ' . print_r( $gpay_data_send, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$redsys->log->add( 'googlepayredsys', ' ' );
		}

		// redsys Args.
		$miobj = new WooRedsysAPI();
		$miobj->setParameter( 'DS_MERCHANT_AMOUNT', $gpay_data_send['order_total_sign'] );
		$miobj->setParameter( 'DS_MERCHANT_ORDER', $gpay_data_send['transaction_id2'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $gpay_data_send['customer'] );
		$miobj->setParameter( 'DS_MERCHANT_CURRENCY', $gpay_data_send['currency'] );
		$miobj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $gpay_data_send['name'] ) . ' ' . WCRed()->clean_data( $gpay_data_send['lastname'] ) );
		$miobj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $gpay_data_send['transaction_type'] );
		$miobj->setParameter( 'DS_MERCHANT_TERMINAL', $gpay_data_send['DSMerchantTerminal'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTURL', $gpay_data_send['final_notify_url'] );
		$miobj->setParameter( 'DS_MERCHANT_URLOK', $gpay_data_send['url_ok'] );
		$miobj->setParameter( 'DS_MERCHANT_URLKO', $gpay_data_send['returnfromredsys'] );
		$miobj->setParameter( 'DS_MERCHANT_CONSUMERLANGUAGE', $gpay_data_send['gatewaylanguage'] );
		$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', $gpay_data_send['product_description'] );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $gpay_data_send['merchant_name'] );
		$miobj->setParameter( 'DS_MERCHANT_PAYMETHODS', 'xpay' );

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request      = '';
		$params       = $miobj->createMerchantParameters();
		$signature    = $miobj->createMerchantSignature( $gpay_data_send['secretsha256'] );
		$order_id_set = $gpay_data_send['transaction_id2'];
		set_transient( 'redsys_signature_' . sanitize_text_field( $order_id_set ), $gpay_data_send['secretsha256'], 600 );
		$redsys_args = array(
			'Ds_SignatureVersion'   => $version,
			'Ds_MerchantParameters' => $params,
			'Ds_Signature'          => $signature,
		);
		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', 'Generating payment form for order ' . $order->get_order_number() . '. Sent data: ' . print_r( $redsys_args, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'googlepayredsys', 'Helping to understand the encrypted code: ' );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_AMOUNT: ' . $gpay_data_send['order_total_sign'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_ORDER: ' . $gpay_data_send['transaction_id2'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TITULAR: ' . WCRed()->clean_data( $gpay_data_send['name'] ) . ' ' . WCRed()->clean_data( $gpay_data_send['lastname'] ) );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $gpay_data_send['customer'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_CURRENCY' . $gpay_data_send['currency'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $gpay_data_send['transaction_type'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TERMINAL: ' . $gpay_data_send['DSMerchantTerminal'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_MERCHANTURL: ' . $gpay_data_send['final_notify_url'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_URLOK: ' . $gpay_data_send['url_ok'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_URLKO: ' . $gpay_data_send['returnfromredsys'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_CONSUMERLANGUAGE: ' . $gpay_data_send['gatewaylanguage'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $gpay_data_send['product_description'] );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_PAYMETHODS: xpay' );
		}
		/**
		 * Filter hook to allow 3rd parties to add more fields to the form
		 *
		 * @since 1.0.0
		 * @param array $redsys_args The arguments sent to Redsys.
		 */
		$redsys_args = apply_filters( 'woocommerce_' . $this->id . '_args', $redsys_args );
		return $redsys_args;
	}

	/**
	 * Generate the redsys form
	 *
	 * @param mixed $order_id Order ID.
	 */
	public function generate_redsys_form( $order_id ) {
		// NOT USED.
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order               = WCRed()->get_order( $order_id );
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
		$ds_xpay_data        = WCRed()->get_order_meta( $order_id, '_gpay_token_redsys', true );
		$g_merchant_id       = $this->g_merchant_id;
		$ds_xpay_type        = $this->xpay_type;
		$ds_xpay_origen      = $this->xpay_origen;
		$dsmerchantterminal  = $this->terminal;

		if ( 'yes' === $this->testmode ) {
			$redsys_adr = $this->testurl;
		} else {
			$redsys_adr = $this->liveurl;
		}
		$miobj = new WooRedsysAPI();
		$miobj->setParameter( 'DS_MERCHANT_AMOUNT', $order_total_sign );
		$miobj->setParameter( 'DS_MERCHANT_ORDER', $transaction_id2 );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $customer );
		$miobj->setParameter( 'DS_MERCHANT_CURRENCY', $currency );
		$miobj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', $transaction_type );
		$miobj->setParameter( 'DS_MERCHANT_TERMINAL', $dsmerchantterminal );
		$miobj->setParameter( 'DS_MERCHANT_TITULAR', WCRed()->clean_data( $name ) . ' ' . WCRed()->clean_data( $lastname ) );
		$miobj->setParameter( 'DS_MERCHANT_PRODUCTDESCRIPTION', $product_description );
		$miobj->setParameter( 'DS_MERCHANT_MERCHANTNAME', $merchant_name );
		$miobj->setParameter( 'DS_XPAYDATA', $ds_xpay_data );
		$miobj->setParameter( 'DS_XPAYTYPE', $ds_xpay_type );
		$miobj->setParameter( 'DS_XPAYORIGEN', $ds_xpay_origen );
		$miobj->setParameter( 'DS_MERCHANT_DIRECTPAYMENT', 'TRUE' );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_AMOUNT: ' . $order_total_sign );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_ORDER: ' . $transaction_id2 );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_MERCHANTCODE: ' . $customer );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_CURRENCY: ' . $currency );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TRANSACTIONTYPE: ' . $transaction_type );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TERMINAL: ' . $dsmerchantterminal );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_TITULAR: ' . WCRed()->clean_data( $name ) . ' ' . WCRed()->clean_data( $lastname ) );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_PRODUCTDESCRIPTION: ' . $product_description );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_MERCHANTNAME: ' . $merchant_name );
			$this->log->add( 'googlepayredsys', 'DS_XPAYDATA: ' . $ds_xpay_data );
			$this->log->add( 'googlepayredsys', 'DS_XPAYTYPE: ' . $ds_xpay_type );
			$this->log->add( 'googlepayredsys', 'DS_XPAYORIGEN: ' . $ds_xpay_origen );
			$this->log->add( 'googlepayredsys', 'DS_MERCHANT_DIRECTPAYMENT: TRUE' );
			$this->log->add( 'googlepayredsys', ' ' );
		}

		$version = 'HMAC_SHA256_V1';
		// Se generan los parámetros de la petición.
		$request       = '';
		$params        = $miobj->createMerchantParameters();
		$signature     = $miobj->createMerchantSignature( $secretsha256 );
		$version       = 'HMAC_SHA256_V1';
		$response      = wp_remote_post(
			$redsys_adr,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce_Redsys_Gateway',
				'body'        => array(
					'Ds_SignatureVersion'   => $version,
					'Ds_MerchantParameters' => $params,
					'Ds_Signature'          => $signature,
				),
			)
		);
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body, true );

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', 'Response from Redsys: ' . print_r( $result, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->log->add( 'googlepayredsys', ' ' );
		}
		if ( $result['Ds_MerchantParameters'] ) {
			$version     = $result['Ds_SignatureVersion']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$data        = $result['Ds_MerchantParameters']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$remote_sign = $result['Ds_Signature']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$decodec     = $miobj->decodeMerchantParameters( $data );
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '$decodec: ' . $decodec );
				$this->log->add( 'googlepayredsys', ' ' );
			}
			$decocec_json = json_decode( $decodec, true );
			if ( '0000' === $decocec_json['Ds_Response'] ) {
				$ds_authorisation_code = $decocec_json['Ds_AuthorisationCode'];
			} else {
				$ds_authorisation_code = false;
			}
			if ( $ds_authorisation_code ) {
				$data = array();
					$order->payment_complete();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'payment_complete 1' );
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', '      Saving Order Meta       ' );
					$this->log->add( 'googlepayredsys', '/****************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}

				if ( ! empty( $transaction_id2 ) ) {
					$data['_payment_order_number_redsys'] = $transaction_id2;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', '_payment_order_number_redsys saved: ' . $transaction_id2 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '_payment_order_number_redsys NOT SAVED!!!' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
				}
				if ( ! empty( $dsmerchantterminal ) ) {
					$data['_payment_terminal_redsys'] = $dsmerchantterminal;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', '_payment_terminal_redsys saved: ' . $dsmerchantterminal );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '_payment_terminal_redsys NOT SAVED!!!' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
				}
				if ( ! empty( $ds_authorisation_code ) ) {
					$data['_authorisation_code_redsys'] = $ds_authorisation_code;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', '_authorisation_code_redsys saved: ' . $ds_authorisation_code );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '_authorisation_code_redsys NOT SAVED!!!' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
				}
				if ( ! empty( $currency ) ) {
					$data['_corruncy_code_redsys'] = $currency;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', '_corruncy_code_redsys saved: ' . $currency );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '_corruncy_code_redsys NOT SAVED!!!' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
				}
				if ( ! empty( $secretsha256 ) ) {
					$data['_redsys_secretsha256'] = $secretsha256;
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', '_redsys_secretsha256 saved: ' . $secretsha256 );
					}
				} else {
					if ( 'yes' === $this->debug ) {
						$this->log->add( 'googlepayredsys', ' ' );
						$this->log->add( 'googlepayredsys', '_redsys_secretsha256 NOT SAVED!!!' );
						$this->log->add( 'googlepayredsys', ' ' );
					}
				}
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', '/******************************************/' );
					$this->log->add( 'googlepayredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'googlepayredsys', '/******************************************/' );
				}
					WCRed()->update_order_meta( $order->get_id(), $data );
					do_action( $this->id . '_post_payment_complete', $order->get_id() );
					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
			}
		}
		if ( isset( $result['errorCode'] ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', 'Error: ' . $result['errorCode'] );
				$this->log->add( 'googlepayredsys', ' ' );
			}
			$error = WCRed()->get_error( $result['errorCode'] );
			do_action( $this->id . '_post_payment_error', $order->get_id(), $error );
			$order->add_order_note( __( 'There was an Errro: ', 'woocommerce-redsys' ) . $error );
			wc_add_notice( $error, 'error' );
		}
	}
	/**
	 * Output for the order received page.
	 *
	 * @param obj $order Order object.
	 */
	public function receipt_page( $order ) {
		// NOT USED.
	}

	/**
	 * Check redsys IPN validity
	 */
	public function check_ipn_request_is_valid() {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
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
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', 'Signature from Redsys: ' . $remote_sign );
				$this->log->add( 'googlepayredsys', 'Name transient remote: redsys_signature_' . sanitize_title( $order_id ) );
				$this->log->add( 'googlepayredsys', 'Secret SHA256 transcient: ' . $secretsha256 );
				$this->log->add( 'googlepayredsys', ' ' );
			}

			if ( 'yes' === $this->debug ) {
				$order_id = $mi_obj->getParameter( 'Ds_Order' );
				$this->log->add( 'googlepayredsys', 'Order ID: ' . $order_id );
			}
			$order           = WCRed()->get_order( $order2 );
			$user_id         = $order->get_user_id();
			$usesecretsha256 = $this->get_redsys_sha256( $user_id );
			if ( empty( $secretsha256 ) && ! $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', 'Using $usesecretsha256 Settings' );
					$this->log->add( 'googlepayredsys', 'Secret SHA256 Settings: ' . $usesecretsha256 );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$usesecretsha256 = $usesecretsha256;
			} elseif ( $secretsha256_meta ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', 'Using $secretsha256_meta Meta' );
					$this->log->add( 'googlepayredsys', 'Secret SHA256 Meta: ' . $secretsha256_meta );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256_meta;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', 'Using $secretsha256 Transcient' );
					$this->log->add( 'googlepayredsys', 'Secret SHA256 Transcient: ' . $secretsha256 );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				$usesecretsha256 = $secretsha256;
			}
			$localsecret = $mi_obj->createMerchantSignatureNotif( $usesecretsha256, $data );
			if ( $localsecret === $remote_sign ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'Received valid notification from Servired/RedSys' );
					$this->log->add( 'googlepayredsys', $data );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'Received INVALID notification from Servired/RedSys' );
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
				$this->log->add( 'googlepayredsys', 'HTTP Notification received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( $ds_merchant_code === $this->customer ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'Received valid notification from Servired/RedSys' );
				}
				return true;
			} else {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'Received INVALID notification from Servired/RedSys' );
					$this->log->add( 'googlepayredsys', '$remote_sign: ' . $remote_sign );
					$this->log->add( 'googlepayredsys', '$localsecret: ' . $localsecret );
				}
				return false;
			}
		}
	}

	/**
	 * Check for GPay HTTP Notification
	 */
	public function check_ipn_response() {

		@ob_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( isset( $_GET['checkout-price'] ) ) {
			WC()->frontend_includes();
			if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
				wc_load_cart();
			}
			$total = WC()->cart->total;
			echo wp_json_encode( array( 'total' => $total ) );
			exit;
		}
		$_POST = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $this->check_ipn_request_is_valid() ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_' . $this->id . '_standard_ipn_request', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			wp_die( 'There is nothing to see here, do not access this page directly (Google Pay redirection)' );
		}
	}
	/**
	 * Successful Payment.
	 *
	 * @param array $posted Post data after notify.
	 */
	public function successful_request( $posted ) {

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', '/****************************/' );
			$this->log->add( 'googlepayredsys', '      successful_request      ' );
			$this->log->add( 'googlepayredsys', '/****************************/' );
			$this->log->add( 'googlepayredsys', ' ' );
		}

		if ( ! isset( $_POST['Ds_SignatureVersion'] ) || ! isset( $_POST['Ds_Signature'] ) || ! isset( $_POST['Ds_MerchantParameters'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_die( 'Do not access this page directly ' );
		}

		$version     = sanitize_text_field( wp_unslash( $_POST['Ds_SignatureVersion'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data        = sanitize_text_field( wp_unslash( $_POST['Ds_MerchantParameters'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$remote_sign = sanitize_text_field( wp_unslash( $_POST['Ds_Signature'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( 'yes' === $this->debug ) {
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', '$version: ' . $version );
			$this->log->add( 'googlepayredsys', '$data: ' . $data );
			$this->log->add( 'googlepayredsys', '$remote_sign: ' . $remote_sign );
			$this->log->add( 'googlepayredsys', ' ' );
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
			$this->log->add( 'googlepayredsys', 'SHA256 Settings: ' . $usesecretsha256 );
			$this->log->add( 'googlepayredsys', 'SHA256 Transcient: ' . $secretsha256 );
			$this->log->add( 'googlepayredsys', 'decodeMerchantParameters: ' . $decodedata );
			$this->log->add( 'googlepayredsys', 'createMerchantSignatureNotif: ' . $localsecret );
			$this->log->add( 'googlepayredsys', 'Ds_Amount: ' . $total );
			$this->log->add( 'googlepayredsys', 'Ds_Order: ' . $ordermi );
			$this->log->add( 'googlepayredsys', 'Ds_MerchantCode: ' . $dscode );
			$this->log->add( 'googlepayredsys', 'Ds_Currency: ' . $currency_code );
			$this->log->add( 'googlepayredsys', 'Ds_Response: ' . $response );
			$this->log->add( 'googlepayredsys', 'Ds_AuthorisationCode: ' . $id_trans );
			$this->log->add( 'googlepayredsys', 'Ds_Date: ' . $dsdate );
			$this->log->add( 'googlepayredsys', 'Ds_Hour: ' . $dshour );
			$this->log->add( 'googlepayredsys', 'Ds_Terminal: ' . $dstermnal );
			$this->log->add( 'googlepayredsys', 'Ds_MerchantData: ' . $dsmerchandata );
			$this->log->add( 'googlepayredsys', 'Ds_SecurePayment: ' . $dssucurepayment );
			$this->log->add( 'googlepayredsys', 'Ds_Card_Country: ' . $dscardcountry );
			$this->log->add( 'googlepayredsys', 'Ds_ConsumerLanguage: ' . $dsconsumercountry );
			$this->log->add( 'googlepayredsys', 'Ds_Card_Type: ' . $dscargtype );
			$this->log->add( 'googlepayredsys', 'Ds_TransactionType: ' . $dstransactiontype );
			$this->log->add( 'googlepayredsys', 'Ds_Merchant_Identifiers_Amount: ' . $response );
			$this->log->add( 'googlepayredsys', 'Ds_Card_Brand: ' . $dscardbrand );
			$this->log->add( 'googlepayredsys', 'Ds_MerchantData: ' . $dsmechandata );
			$this->log->add( 'googlepayredsys', 'Ds_ErrorCode: ' . $dserrorcode );
			$this->log->add( 'googlepayredsys', 'Ds_PayMethod: ' . $dpaymethod );
		}

		// refund.
		if ( '3' === $dstransactiontype ) {
			if ( 900 === $response ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'Response 900 (refund)' );
				}
				set_transient( $order->get_id() . '_redsys_refund', 'yes' );

				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'WCRed()->update_order_meta to "refund yes"' );
				}
				$status = $order->get_status();
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', 'New Status in request: ' . $status );
				}
				$order->add_order_note( __( 'Order Payment refunded by Redsys', 'woocommerce-redsys' ) );
				return;
			}
			$order->add_order_note( __( 'There was an error refunding', 'woocommerce-redsys' ) );
			exit;
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
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', '/**************************/' );
			$this->log->add( 'googlepayredsys', __( 'Starting asking for Refund', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', '/**************************/' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
		}
		$transaction_type  = '3';
		$secretsha256_meta = WCRed()->get_order_meta( $order_id, '_redsys_secretsha256', true );
		if ( $secretsha256_meta ) {
			$secretsha256 = $secretsha256_meta;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', __( 'Using meta for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'googlepayredsys', __( 'The SHA256 Meta is: ', 'woocommerce-redsys' ) . $secretsha256 );
			}
		} else {
			$secretsha256 = $secretsha256;
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', __( 'Using settings for SHA256', 'woocommerce-redsys' ) );
				$this->log->add( 'googlepayredsys', __( 'The SHA256 settings is: ', 'woocommerce-redsys' ) . $secretsha256 );
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
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', '**********************' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'If something is empty, the data was not saved', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'All data from meta', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', __( 'Authorization Code : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'googlepayredsys', __( 'Authorization Date : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'googlepayredsys', __( 'Currency Codey : ', 'woocommerce-redsys' ) . $currencycode );
			$this->log->add( 'googlepayredsys', __( 'Terminal : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'googlepayredsys', __( 'SHA256 : ', 'woocommerce-redsys' ) . $secretsha256_meta );
			$this->log->add( 'googlepayredsys', __( 'FUC : ', 'woocommerce-redsys' ) . $order_fuc );
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
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'Data sent to Redsys for refund', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', '*********************************' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'URL to Redsys : ', 'woocommerce-redsys' ) . $redsys_adr );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_AMOUNT : ', 'woocommerce-redsys' ) . $amount );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_ORDER : ', 'woocommerce-redsys' ) . $transaction_id );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_MERCHANTCODE : ', 'woocommerce-redsys' ) . $order_fuc );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_CURRENCY : ', 'woocommerce-redsys' ) . $currency );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_TRANSACTIONTYPE : ', 'woocommerce-redsys' ) . $transaction_type );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_TERMINAL : ', 'woocommerce-redsys' ) . $terminal );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_MERCHANTURL : ', 'woocommerce-redsys' ) . $final_notify_url );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_URLOK : ', 'woocommerce-redsys' ) . add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_URLKO : ', 'woocommerce-redsys' ) . $order->get_cancel_order_url() );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_CONSUMERLANGUAGE : 001', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_PRODUCTDESCRIPTION : ', 'woocommerce-redsys' ) . WCRed()->product_description( $order, $this->id ) );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_MERCHANTNAME : ', 'woocommerce-redsys' ) . $this->commercename );
			$this->log->add( 'googlepayredsys', __( 'DS_MERCHANT_AUTHORISATIONCODE : ', 'woocommerce-redsys' ) . $autorization_code );
			$this->log->add( 'googlepayredsys', __( 'Ds_Merchant_TransactionDate : ', 'woocommerce-redsys' ) . $autorization_date );
			$this->log->add( 'googlepayredsys', __( 'ask_for_refund Asking por order #: ', 'woocommerce-redsys' ) . $order_id );
			$this->log->add( 'googlepayredsys', ' ' );
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
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', __( 'There is an error', 'woocommerce-redsys' ) );
				$this->log->add( 'googlepayredsys', '*********************************' );
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', __( 'The error is : ', 'woocommerce-redsys' ) . $post_arg );
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
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'Checking and waiting ping from Redsys', 'woocommerce-redsys' ) );
			$this->log->add( 'googlepayredsys', '*****************************************' );
			$this->log->add( 'googlepayredsys', ' ' );
			$this->log->add( 'googlepayredsys', __( 'Check order status #: ', 'woocommerce-redsys' ) . $order->get_id() );
			$this->log->add( 'googlepayredsys', __( 'Check order status with get_transient: ', 'woocommerce-redsys' ) . $order_refund );
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
			$this->log->add( 'googlepayredsys', __( '$order_id#: ', 'woocommerce-redsys' ) . $transaction_id );
		}
		if ( ! $amount ) {
			$order_total_sign = WCRed()->redsys_amount_format( $order->get_total() );
		} else {
			$order_total_sign = number_format( $amount, 2, '', '' );
		}

		if ( ! empty( $transaction_id ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', __( 'check_redsys_refund Asking for order #: ', 'woocommerce-redsys' ) . $order_id );
			}

			$refund_asked = $this->ask_for_refund( $order_id, $transaction_id, $order_total_sign );

			if ( is_wp_error( $refund_asked ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'googlepayredsys', __( 'Refund Failed: ', 'woocommerce-redsys' ) . $refund_asked->get_error_message() );
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
				$this->log->add( 'googlepayredsys', __( 'check_redsys_refund = true ', 'woocommerce-redsys' ) . $result );
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '/********************************/' );
				$this->log->add( 'googlepayredsys', '  Refund complete by Redsys   ' );
				$this->log->add( 'googlepayredsys', '/********************************/' );
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '/******************************************/' );
				$this->log->add( 'googlepayredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'googlepayredsys', '/******************************************/' );
				$this->log->add( 'googlepayredsys', ' ' );
			}
			if ( 'yes' === $this->debug && ! $result ) {
				$this->log->add( 'googlepayredsys', __( 'check_redsys_refund = false ', 'woocommerce-redsys' ) . $result );
			}
			if ( $result ) {
				delete_transient( $order->get_id() . '_redsys_refund' );
				return true;
			} else {
				if ( 'yes' === $this->debug && $result ) {
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'googlepayredsys', __( '!!!!Refund Failed, please try again!!!!', 'woocommerce-redsys' ) );
					$this->log->add( 'googlepayredsys', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' );
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', ' ' );
					$this->log->add( 'googlepayredsys', '/******************************************/' );
					$this->log->add( 'googlepayredsys', '  The final has come, this story has ended  ' );
					$this->log->add( 'googlepayredsys', '/******************************************/' );
					$this->log->add( 'googlepayredsys', ' ' );
				}
				return false;
			}
		} else {
			if ( 'yes' === $this->debug && $result ) {
				$this->log->add( 'googlepayredsys', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
				$this->log->add( 'googlepayredsys', ' ' );
				$this->log->add( 'googlepayredsys', '/******************************************/' );
				$this->log->add( 'googlepayredsys', '  The final has come, this story has ended  ' );
				$this->log->add( 'googlepayredsys', '/******************************************/' );
				$this->log->add( 'googlepayredsys', ' ' );
			}
			return new WP_Error( 'error', __( 'Refund Failed: No transaction ID', 'woocommerce-redsys' ) );
		}
	}
	/**
	 * Payment_fields function.
	 */
	public function payment_fields() {

		if ( is_checkout() && WCRed()->is_gateway_enabled( 'googlepayredsys' ) ) {

			echo '<style>
					#redsys-gpay-button {
						width: 300px;
						height: 50px;
						text-align: center;
						margin: 0 auto;
						margin-top: 15px;
					}
					@media only screen and (min-width: 768px) {
						#redsys-gpay-button {
							width: 400px;
							height: 50px;
						}
					}
				</style>';
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
				'a'      => array(
					'href'   => array(),
					'title'  => array(),
					'class'  => array(),
					'id'     => array(),
					'target' => array(),
				),
			);
			/**
			 * Filter to add more tags to the allowed html.
			 *
			 * @since 22.0.0
			 */
			$allowed_html_filter = apply_filters( 'redsys_kses_descripcion', $allowed_html );
			echo '<p>' . wp_kses( $this->description, $allowed_html_filter ) . '</p>';
			echo '<div id="redsys-gpay-button"></div>';
			echo '<input type="hidden" id="gpay-token-redsys" name="gpay-token-redsys" value="" />';
			$price                              = WC()->cart->get_cart_total();
			$_SESSION['redsys_gpay_cart_total'] = $price;
		}
	}
	/**
	 * Load Google Pay JS
	 *
	 * @param int $price Price.
	 */
	public function load_google_pay_js( $price = false ) {
		if ( is_checkout() && WCRed()->is_gateway_enabled( 'googlepayredsys' ) ) {
			if ( ! $price ) {
				$order_total = WC()->cart->total;
			} else {
				$order_total = $price;
			}
			if ( 'yes' === $this->testmode ) {
				$environment = 'TEST';
			} else {
				$environment = 'PRODUCTION';
			}
			$store_country = wc_get_base_location()['country'];
			$time          = time();
			wp_deregister_script( 'gpay-redsys' );
			wp_register_script( 'redsys-external-pay-js', 'https://pay.google.com/gp/p/js/pay.js', array(), $time, true );
			wp_register_script( 'gpay-redsys', esc_url( REDSYS_PLUGIN_URL_P ) . 'assets/js/gpay-redsys.js', array( 'jquery', 'redsys-external-pay-js' ), $time, true );
			$script_data_array = array(
				'gatewayMerchantId' => $this->customer,
				'merchantId'        => $this->g_merchant_id,
				'merchantName'      => WCRed()->clean_data( $this->commercename ),
				'merchantOrigin'    => get_site_url(),
				'environment'       => $environment,
				'countryCode'       => $store_country,
				'currencyCode'      => get_woocommerce_currency(),
				'totalPriceStatus'  => 'FINAL',
				'totalPrice'        => $order_total,
				'timeLoadButton'    => $this->time_load_button,
				'url_site'          => get_site_url(),
				'buttonColor'       => $this->button_color,
				'buttonType'        => $this->button_type,
				'buttonLocale'      => $this->button_locale,
			);
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', '$script_data_array: ' . print_r( $script_data_array, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			wp_localize_script( 'gpay-redsys', 'gpay_redsys', $script_data_array );
			wp_enqueue_script( 'redsys-external-pay-js' );
			wp_enqueue_script( 'gpay-redsys' );
			wp_add_inline_script( 'gpay-redsys', 'onGooglePayLoaded()' );
		}
	}
	/**
	 * Save fields to checkout (Gpay).
	 *
	 * @param int $order_id Order ID.
	 */
	public function save_field_update_order_meta( $order_id ) {

		if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ), 'woocommerce-process_checkout' ) && 'googlepayredsys' === sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( 'googlepayredsys', 'HTTP $_POST checkout received: ' . print_r( $_POST, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
			if ( ! empty( $_POST['gpay-token-redsys'] ) ) {
				$gpay_token                 = sanitize_text_field( wp_unslash( $_POST['gpay-token-redsys'] ) );
				$data['_gpay_token_redsys'] = sanitize_text_field( $gpay_token );
			}
			WCRed()->update_order_meta( $order_id, $data );
		}
	}
}
/**
 * Add the gateway to WooCommerce
 *
 * @param array $methods WooCommerce payment methods.
 */
function woocommerce_add_gateway_googlepay_redsys( $methods ) {
	$methods[] = 'WC_Gateway_Googlepay_Checkout';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_googlepay_redsys' );
