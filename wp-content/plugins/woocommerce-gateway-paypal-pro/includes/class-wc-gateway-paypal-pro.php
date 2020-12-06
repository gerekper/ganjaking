<?php
/**
 * WC_Gateway_PayPal_Pro class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_PayPal_Pro extends WC_Payment_Gateway {

	/**
	 * Store client
	 */
	private $centinel_client = false;

	/**
	 * Constuctor
	 */
	public function __construct() {
		$this->id                   = 'paypal_pro';
		$this->api_version          = '214';
		$this->method_title         = __( 'PayPal Pro', 'woocommerce-gateway-paypal-pro' );
		$this->method_description   = __( 'PayPal Pro works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'woocommerce-gateway-paypal-pro' );
		$this->icon                 = apply_filters('woocommerce_paypal_pro_icon', plugins_url( '/assets/images/cards.png', plugin_basename( dirname( __FILE__ ) ) ) );
		$this->has_fields           = true;
		$this->view_transaction_url = $this->get_option( 'testmode', "no" ) === "yes" ? 'https://www.sandbox.paypal.com/activity/payment/%s' : 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		$this->supports             = array(
			'products',
			'refunds',
		);
		$this->liveurl              = 'https://api-3t.paypal.com/nvp';
		$this->testurl              = 'https://api-3t.sandbox.paypal.com/nvp';
		$this->liveurl_3ds          = 'https://paypal.cardinalcommerce.com/maps/txns.asp';
		$this->testurl_3ds          = 'https://centineltest.cardinalcommerce.com/maps/txns.asp';
		$this->songbird_test_url    = 'https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js';
		$this->songbird_live_url    = 'https://songbird.cardinalcommerce.com/edge/v1/songbird.js';
		$this->available_card_types = apply_filters( 'woocommerce_paypal_pro_available_card_types', array(
			'GB' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'Maestro'       => 'Maestro/Switch',
				'Solo'          => 'Solo'
			),
			'US' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'Discover'      => 'Discover',
				'AmEx'          => 'American Express'
			),
			'CA' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard'
			),
			'AU' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard'
			),
			'JP' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'JCB'           => 'JCB'
			)
		) );
		// this redundant filter is target previous typo'd filter name
		$this->available_card_types = apply_filters( 'woocommerce_paypal_pro_avaiable_card_types', $this->available_card_types );

		$this->iso4217 = apply_filters( 'woocommerce_paypal_pro_iso_currencies', array(
			'AUD' => '036',
			'CAD' => '124',
			'CZK' => '203',
			'DKK' => '208',
			'EUR' => '978',
			'HUF' => '348',
			'JPY' => '392',
			'NOK' => '578',
			'NZD' => '554',
			'PLN' => '985',
			'GBP' => '826',
			'SGD' => '702',
			'SEK' => '752',
			'CHF' => '756',
			'USD' => '840'
		) );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values
		$this->title                    = $this->get_option( 'title' );
		$this->description              = $this->get_option( 'description' );
		$this->enabled                  = $this->get_option( 'enabled' );
		$this->api_username             = $this->get_option( 'api_username' );
		$this->api_password             = $this->get_option( 'api_password' );
		$this->api_signature            = $this->get_option( 'api_signature' );
		$this->testmode                 = $this->get_option( 'testmode', "no" ) === "yes" ? true : false;
		// Enables the new (2020) 3DSecure 1.x and 2.x integration with Cardinal Cruise
		$this->enable_3dsecure_cruise   = $this->get_option( 'enable_3dsecure_cruise', "no" ) === "yes" ? true : false;
		// Enables the legacy (pre-2020) 3DSecure 1.x integration only
		$this->enable_3dsecure_centinel = $this->get_option( 'enable_3dsecure', "no" ) === "yes" ? true : false;
		$this->liability_shift          = $this->get_option( 'liability_shift', "no" ) === "yes" ? true : false;
		$this->debug                    = $this->get_option( 'debug', "no" ) === "yes" ? true : false;
		$this->send_items               = $this->get_option( 'send_items', "no" ) === "yes" ? true : false;
		$this->soft_descriptor          = str_replace( ' ', '-', preg_replace('/[^A-Za-z0-9\-\.]/', '', $this->get_option( 'soft_descriptor', "" ) ) );
		$this->paymentaction            = $this->get_option( 'paypal_pro_paymentaction', 'sale' );

		// 3D Secure
		if ( $this->enable_3dsecure_cruise ) {
			$this->cruise_api_id      = trim( $this->get_option( 'cruise_api_id' ) );
			$this->cruise_api_key     = trim( $this->get_option( 'cruise_api_key' ) );
			$this->cruise_org_unit_id = trim( $this->get_option( 'cruise_org_unit_id' ) );

			if ( empty( $this->cruise_api_id ) || empty( $this->cruise_api_key ) || empty( $this->cruise_org_unit_id ) ) {
				$this->enable_3dsecure_cruise = false;
			}

			// If Cardinal Cruise is still set up at this point, disable legacy handling
			if ( $this->enable_3dsecure_cruise ) {
				$this->enable_3dsecure_centinel = false;
			}

			$this->songbird_url = $this->testmode ? $this->songbird_test_url : $this->songbird_live_url;
		}

		if ( $this->enable_3dsecure_centinel || $this->enable_3dsecure_cruise ) {
			$this->centinel_pid = $this->get_option( 'centinel_pid' );
			$this->centinel_mid = $this->get_option( 'centinel_mid' );
			$this->centinel_pwd = $this->get_option( 'centinel_pwd' );

			if ( empty( $this->centinel_pid ) || empty( $this->centinel_mid ) || empty( $this->centinel_pwd ) ) {
				$this->enable_3dsecure_centinel = false;
			}

			$this->centinel_url = $this->testmode ? $this->testurl_3ds : $this->liveurl_3ds;
		}

		// Maestro
		if ( ! $this->enable_3dsecure_centinel && ! $this->enable_3dsecure_cruise ) {
			unset( $this->available_card_types['GB']['Maestro'] );
		}

		// Hooks
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		if ( $this->enable_3dsecure_centinel || $this->enable_3dsecure_cruise ) {
			add_action( 'woocommerce_api_wc_gateway_paypal_pro', array( $this, 'handle_3dsecure' ) );
		}
		if ( $this->enable_3dsecure_cruise ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'prepare_order_pay_page' ) );
		}
	}

	/**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
		$cardinal_sign_up_url = "https://paypal3dsregistration.cardinalcommerce.com/UI/Registration.aspx";
		$cardinal_upgrade_url = "https://paypal3dsregistration.cardinalcommerce.com/UI/registrationcontactpage.aspx";

    	$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Pro', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Credit card (PayPal)', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Pay with your credit card via PayPal Website Payments Pro.', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true,
			),
			'testmode' => array(
				'title'       => __( 'Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Sandbox/Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in development mode.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'api_username' => array(
				'title'       => __( 'API Username', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'api_password' => array(
				'title'       => __( 'API Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'api_signature' => array(
				'title'       => __( 'API Signature', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'paypal_pro_paymentaction' => array(
				'title'       => __( 'Payment Action', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'select',
				'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'sale',
				'desc_tip'    => true,
				'options'     => array(
					'sale'          => __( 'Capture', 'woocommerce-gateway-paypal-pro' ),
					'authorization' => __( 'Authorize', 'woocommerce-gateway-paypal-pro' ),
				),
			),
			'enable_3dsecure_cruise' => array(
				'title'       => __( '3D Secure 2', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable 3D Secure 2 (Powered by Cardinal Cruise)', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => sprintf(
					__( 'Enables your participation in Verified by Visa and MasterCard SecureCode. Is also required to accept Maestro. Merchants new to Cardinal can <a href="%1$s">sign up here</a>. Merchants with an existing Cardinal account can <a href="%2$s">contact Cardinal to upgrade to Cruise</a>.<br/><br/>Cardinal Cruise provides a fallback to 3D Secure 1 when 3D Secure 2 is unavailable.', 'woocommerce-gateway-paypal-pro' ),
					esc_url( $cardinal_sign_up_url ),
					esc_url( $cardinal_upgrade_url )
				),
				'default'     => 'no',
				'desc_tip'    => false,
			),
			'cruise_org_unit_id' => array(
				'title'       => __( 'Cardinal Org Unit ID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Enter your Cardinal Merchant Organization Unit ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'cruise_api_id' => array(
				'title'       => __( 'Cardinal API ID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Enter your Cardinal API ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'cruise_api_key' => array(
				'title'       => __( 'Cardinal API Key', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'password',
				'description' => __( 'Enter your Cardinal API Key.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'enable_3dsecure' => array(
				'title'       => __( '3D Secure 1', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable 3D Secure 1 (Powered by Cardinal Centinel)', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Allows UK merchants to pass 3D Secure authentication data to PayPal for debit and credit cards. Updating your site with 3-D Secure enables your participation in the Verified by Visa and MasterCard SecureCode programs. (Required to accept Maestro)', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'centinel_pid' => array(
				'title'       => __( 'Cardinal Centinel PID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Enter your Cardinal Centinel Processor ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'centinel_mid' => array(
				'title'       => __( 'Cardinal Centinel MID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Enter your Cardinal Centinel Merchant ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'centinel_pwd' => array(
				'title'       => __( 'Cardinal Transaction Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'password',
				'description' => __( 'Enter your Cardinal Transaction Password.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'liability_shift' => array(
				'title'       => __( 'Liability Shift', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Require liability shift for payments using 3D Secure', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Only accept payments when liability shift has occurred.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'send_items' => array(
				'title'       => __( 'Send Item Details', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Send Line Items to PayPal', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Sends line items to PayPal. If you experience rounding errors this can be disabled.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'soft_descriptor' => array(
				'title'             => __( 'Soft Descriptor', 'woocommerce-gateway-paypal-pro' ),
				'type'              => 'text',
				'description'       => __( '(Optional) Information that is usually displayed in the account holder\'s statement, for example your website name. Only 23 alphanumeric characters can be included, including the special characters dash (-) and dot (.) . Asterisks (*) and spaces ( ) are NOT permitted.', 'woocommerce-gateway-paypal-pro' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'maxlength' => 23,
					'pattern' => '[a-zA-Z0-9.-]+',
				),
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Log PayPal Pro events inside <code>woocommerce/logs/paypal-pro.txt</code>', 'woocommerce-gateway-paypal-pro' ),
			),
		);
    }

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
  	public function admin_options() {
		parent::admin_options();
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				function update_paypal_pro_3ds_visibility() {
					var threeDSecureCruise = $( '#woocommerce_paypal_pro_enable_3dsecure_cruise' );
					var threeDSecureCruiseChecked = threeDSecureCruise.is( ':checked' );
					var threeDSecureCruiseRow = threeDSecureCruise.closest( 'tr' );
					var threeDSecureCruiseSettingRows = $( '#woocommerce_paypal_pro_cruise_org_unit_id, #woocommerce_paypal_pro_cruise_api_id, #woocommerce_paypal_pro_cruise_api_key' ).closest( 'tr' );

					var threeDSecureCentinel = $( '#woocommerce_paypal_pro_enable_3dsecure' );
					var threeDSecureCentinelChecked = threeDSecureCentinel.is( ':checked' );
					var threeDSecureCentinelRow = threeDSecureCentinel.closest( 'tr' );
					var threeDSecureCentinelSettingRows = $( '#woocommerce_paypal_pro_centinel_pid, #woocommerce_paypal_pro_centinel_mid, #woocommerce_paypal_pro_centinel_pwd' ).closest( 'tr' );

					var liabilityShiftRow = $( '#woocommerce_paypal_pro_liability_shift' ).closest( 'tr' );

					if ( threeDSecureCruiseChecked ) {
						threeDSecureCruiseSettingRows.show();
						threeDSecureCentinelRow.hide();
						threeDSecureCentinelSettingRows.hide();
					} else if ( threeDSecureCentinelChecked ) {
						threeDSecureCruiseSettingRows.hide();
						threeDSecureCentinelRow.show();
						threeDSecureCentinelSettingRows.show();
					} else {
						threeDSecureCruiseRow.show();
						threeDSecureCentinelRow.show();
						threeDSecureCruiseSettingRows.hide();
						threeDSecureCentinelSettingRows.hide();
					}

					if ( threeDSecureCruiseChecked || threeDSecureCentinelChecked ) {
						liabilityShiftRow.show();
					} else {
						liabilityShiftRow.hide();
					}
				}

				$( '#woocommerce_paypal_pro_enable_3dsecure_cruise' ).on( 'change', update_paypal_pro_3ds_visibility );
				$( '#woocommerce_paypal_pro_enable_3dsecure' ).on( 'change', update_paypal_pro_3ds_visibility );

				update_paypal_pro_3ds_visibility();
			} );
		</script>
		<?php
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
			if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_pro_allowed_currencies', array( 'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD' ) ) ) ) {
				return false;
			}

			// Required fields check
			if ( ! $this->api_username || ! $this->api_password || ! $this->api_signature ) {
				return false;
			}

			return isset( $this->available_card_types[ WC()->countries->get_base_country() ] );
		}

		return false;
	}

	/**
     * Payment form on checkout page
     */
	public function payment_fields() {
		wp_enqueue_script( 'wc-credit-card-form' );

		if ( $this->description ) {
			echo '<p>' . wp_kses_post( $this->description ) . ( $this->testmode ? ' ' . __( 'TEST/SANDBOX MODE ENABLED. In test mode, you can use the card number 4007000000027 with any CVC and a valid expiration date.  Note that you will get a faster processing result if you use a card from your developer\'s account.', 'woocommerce-gateway-paypal-pro' ) : '' ) . '</p>';
		}

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

			<?php if ( isset( $this->available_card_types[ WC()->countries->get_base_country() ]['Maestro'] ) ) : ?>
				<p class="form-row form-row-first">
					<label for="<?php echo esc_attr( $this->id ); ?>-card-startdate"><?php esc_html_e( 'Start Date (MM/YY)', 'woocommerce-gateway-paypal-pro' ); ?></label>
					<input id="<?php echo esc_attr( $this->id ); ?>-card-startdate" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="<?php  esc_html_e( 'MM / YY', 'woocommerce-gateway-paypal-pro' ); ?>" name="<?php echo esc_attr( $this->id ); ?>-card-startdate" />
				</p>
			<?php endif; ?>
		</fieldset>
		<?php
	}

	/**
	 * Enqueue scripts used for payment processing
	 *
	 * @since 4.5.0
	 */
	public function payment_scripts() {
		if ( ! $this->enable_3dsecure_cruise || ! is_checkout() && ! isset( $_GET['pay_for_order'] ) || is_order_received_page() ) {
			return;
		}

		$cruise_params = array(
			'jwt'   => $this->get_songbird_init_jwt(),
			'debug' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			'error' => __( 'There was an issue authorizing the payment with your bank. Please check your bank details and try again.', 'woocommerce-gateway-paypal-pro' ),
		);
		wp_register_script(
			'cardinal-songbird',
			$this->songbird_url,
			array(),
			false,
			true
		);
		wp_register_script(
			'wc-gateway-paypal-pro-cruise-checkout',
			plugins_url( 'assets/js/wc-gateway-paypal-pro-cruise-checkout.js', plugin_basename( dirname( __FILE__ ) ) ),
			array( 'cardinal-songbird', 'wc-checkout' ),
			WC_PAYPAL_PRO_VERSION,
			true
		);
		wp_localize_script(
			'wc-gateway-paypal-pro-cruise-checkout',
			'wc_paypal_pro_cruise_checkout_params',
			$cruise_params
		);
		wp_enqueue_script( 'wc-gateway-paypal-pro-cruise-checkout' );
	}

	/**
	 * Return JSON Web Token required by Cardinal Cruise for authentication
	 *
	 * @return string
	 */
	private function get_songbird_init_jwt() {
		$payload = array(
			'jti'              => uniqid(),
			'iat'              => time(),
			'iss'              => $this->cruise_api_id,
			'OrgUnitId'        => $this->cruise_org_unit_id,
			'Payload'          => array(
				'OrderDetails' => array(
					'CurrencyCode' => $this->iso4217[ get_woocommerce_currency() ],
				),
			),
			'ObjectifyPayload' => true,
		);
		return $this->get_jwt( $payload, $this->cruise_api_key );
	}

	/**
	 * Return JSON Web Token
	 *
	 * Adapted from https://dev.to/robdwaller/how-to-create-a-json-web-token-using-php-3gml
	 *
	 * @param array  $payload
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_jwt( $payload, $key ) {
		$parts = array();
		// Create token header as base64url-encoded JSON string
		$header   = [ 'typ' => 'JWT', 'alg' => 'HS256' ];
		$parts[0] = rtrim( strtr( base64_encode( json_encode( $header ) ), '+/', '-_' ), '=' );
		// Create token payload as base64url-encoded JSON string
		$parts[1] = rtrim( strtr( base64_encode( json_encode( $payload ) ), '+/', '-_' ), '=' );
		// Create base64url-encoded signature hash
		$signature = hash_hmac( 'sha256', implode( '.', $parts ), $key, true );
		$parts[2]  = rtrim( strtr( base64_encode( $signature ), '+/', '-_' ), '=' );
		return implode( '.', $parts );
	}

	/**
	 * Format and get posted details
	 * @return object
	 */
	private function get_posted_card() {
		$card_number    = isset( $_POST['paypal_pro-card-number'] ) ? wc_clean( $_POST['paypal_pro-card-number'] ) : '';
		$card_cvc       = isset( $_POST['paypal_pro-card-cvc'] ) ? wc_clean( $_POST['paypal_pro-card-cvc'] ) : '';
		$card_expiry    = isset( $_POST['paypal_pro-card-expiry'] ) ? wc_clean( $_POST['paypal_pro-card-expiry'] ) : '';

		// Format values
		$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
		$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
		$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
		$card_exp_year  = isset( $card_expiry[1] ) ? $card_expiry[1] : '';

		if ( isset( $_POST['paypal_pro-card-start'] ) ) {
			$card_start       = wc_clean( $_POST['paypal_pro-card-start'] );
			$card_start       = array_map( 'trim', explode( '/', $card_start ) );
			$card_start_month = str_pad( $card_start[0], 2, "0", STR_PAD_LEFT );
			$card_start_year  = $card_start[1];
		} else {
			$card_start_month = '';
			$card_start_year  = '';
		}

		if ( strlen( $card_exp_year ) == 2 ) {
			$card_exp_year += 2000;
		}

		if ( strlen( $card_start_year ) == 2 ) {
			$card_start_year += 2000;
		}

		return (object) array(
			'number'      => $card_number,
			'type'        => '',
			'cvc'         => $card_cvc,
			'exp_month'   => $card_exp_month,
			'exp_year'    => $card_exp_year,
			'start_month' => $card_start_month,
			'start_year'  => $card_start_year
		);
	}

	/**
     * Validate the payment form
     */
	public function validate_fields() {
		try {
			$card = $this->get_posted_card();

			if ( empty( $card->exp_month ) || empty( $card->exp_year ) ) {
				throw new Exception( __( 'Card expiration date is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			// Validate values
			if ( ! ctype_digit( $card->cvc ) ) {
				throw new Exception( __( 'Card security code is invalid (only digits are allowed)', 'woocommerce-gateway-paypal-pro' ) );
			}

			if (
				! ctype_digit( $card->exp_month ) ||
				! ctype_digit( $card->exp_year ) ||
				$card->exp_month > 12 ||
				$card->exp_month < 1 ||
				$card->exp_year < date( 'y' )
			) {
				throw new Exception( __( 'Card expiration date is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			if ( empty( $card->number ) || ! ctype_digit( $card->number ) ) {
				throw new Exception( __( 'Card number is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			return true;

		} catch( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return false;
		}
	}

	/**
	 * Get and clean a value from $this->centinel_client because the SDK does a poor job of cleaning.
	 * @return string
	 */
	public function get_centinel_value( $key ) {
		$value = $this->centinel_client->getValue( $key );
		$value = wc_clean( $value );
		return $value;
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id Order ID.
     */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$card  = $this->get_posted_card();

		$this->log( 'Processing order #' . $order_id );

		/**
	     * 3D Secure Handling
	     */
		if ( $this->enable_3dsecure_centinel || $this->enable_3dsecure_cruise ) {

			if ( ! class_exists( 'CentinelClient' ) ) {
				include_once( 'lib/CentinelClient.php' );
			}

			$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

			$this->clear_centinel_session();

			$this->centinel_client = new CentinelClient;
			$this->centinel_client->add( 'MsgType', 'cmpi_lookup' );
			$this->centinel_client->add( 'Version', '1.7' );
			$this->centinel_client->add( 'TransactionType', $this->enable_3dsecure_centinel ? 'CC' : 'C' );
			$this->centinel_client->add( 'OrderNumber', $order_id );
			$this->centinel_client->add( 'Amount', $this->decimal_to_cents( $order->get_total() ) );
			$this->centinel_client->add( 'CurrencyCode', $this->iso4217[ ( $pre_wc_30 ? $order->get_order_currency() : $order->get_currency() ) ] );
			$this->centinel_client->add( 'TransactionMode', 'S' );
			$this->centinel_client->add( 'ProductCode', 'PHY' );
			$this->centinel_client->add( 'CardExpMonth', $card->exp_month );
			$this->centinel_client->add( 'CardExpYear', $card->exp_year );
			$this->centinel_client->add( 'BillingFirstName', $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name() );
			$this->centinel_client->add( 'BillingLastName', $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name() );
			$this->centinel_client->add( 'BillingAddress1', $pre_wc_30 ? $order->billing_address_1 : $order->get_billing_address_1() );
			$this->centinel_client->add( 'BillingAddress2', $pre_wc_30 ? $order->billing_address_2 : $order->get_billing_address_2() );
			$this->centinel_client->add( 'BillingCity', $pre_wc_30 ? $order->billing_city : $order->get_billing_city() );
			$this->centinel_client->add( 'BillingState', $pre_wc_30 ? $order->billing_state : $order->get_billing_state() );
			$this->centinel_client->add( 'BillingPostalCode', $pre_wc_30 ? $order->billing_postcode : $order->get_billing_postcode() );
			$this->centinel_client->add( 'BillingCountryCode', $pre_wc_30 ? $order->billing_country : $order->get_billing_country() );
			$this->centinel_client->add( 'BillingPhone', $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone() );
			$this->centinel_client->add( 'ShippingFirstName', $pre_wc_30 ? $order->shipping_first_name : $order->get_shipping_first_name() );
			$this->centinel_client->add( 'ShippingLastName', $pre_wc_30 ? $order->shipping_last_name : $order->get_shipping_last_name() );
			$this->centinel_client->add( 'ShippingAddress1', $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1() );
			$this->centinel_client->add( 'ShippingAddress2', $pre_wc_30 ? $order->shipping_address_2 : $order->get_shipping_address_2() );
			$this->centinel_client->add( 'ShippingCity', $pre_wc_30 ? $order->shipping_city : $order->get_shipping_city() );
			$this->centinel_client->add( 'ShippingState', $pre_wc_30 ? $order->shipping_state : $order->get_shipping_state() );
			$this->centinel_client->add( 'ShippingPostalCode', $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode() );
			$this->centinel_client->add( 'ShippingCountryCode', $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country() );

			if ( isset( $_POST['paypal_pro-cardinal-sessionId'] ) ) {
				$this->centinel_client->add( 'DFReferenceId', $_POST['paypal_pro-cardinal-sessionId'] );
			}

			// Items.
			$item_loop = 0;

			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $item ) {
					$item_loop++;
					$this->centinel_client->add( 'Item_Name_' . $item_loop, $item['name'] );
					$this->centinel_client->add( 'Item_Price_' . $item_loop, $this->decimal_to_cents( $order->get_item_total( $item, true ) ) );
					$this->centinel_client->add( 'Item_Quantity_' . $item_loop, $item['qty'] );
					$this->centinel_client->add( 'Item_Desc_' . $item_loop, $item['name'] );
				}
			}

			// Credentials.
			if ( $this->enable_3dsecure_centinel ) {
				$this->centinel_client->add( 'ProcessorId', $this->centinel_pid );
				$this->centinel_client->add( 'MerchantId', $this->centinel_mid );
			} else {
				$timestamp = time() * 1000;
				$signature = base64_encode( hash( 'sha256', $timestamp . $this->cruise_api_key, true ) );
				$this->centinel_client->add( 'Algorithm', 'SHA-256' );
				$this->centinel_client->add( 'Identifier', $this->cruise_api_id );
				$this->centinel_client->add( 'OrgUnit', $this->cruise_org_unit_id );
				$this->centinel_client->add( 'Timestamp', $timestamp );
			}

			// Log request before setting sensitive information.
			$this->log( 'Centinel client request: ' . print_r( $this->centinel_client->request, true ) );

			// Finally, add remaining fields.
			if ( $this->enable_3dsecure_centinel ) {
				$this->centinel_client->add( 'TransactionPwd', $this->centinel_pwd );
			} else {
				$this->centinel_client->add( 'Signature', $signature );
			}
			$this->centinel_client->add( 'CardNumber', $card->number );
			$this->centinel_client->add( 'CardCode', $card->cvc );

		    // Send request.
		    $this->centinel_client->sendHttp( $this->centinel_url, '5000', '15000' );

			$this->log( 'Centinel client response: ' . print_r( $this->centinel_client->response, true ) );

			// Store response.
			WC()->session->set( 'Centinel_ErrorNo', $this->get_centinel_value( 'ErrorNo' ) );
			WC()->session->set( 'Centinel_ErrorDesc', $this->get_centinel_value( 'ErrorDesc' ) );
			WC()->session->set( 'Centinel_TransactionId', $this->get_centinel_value( 'TransactionId' ) );
			WC()->session->set( 'Centinel_OrderId', $this->get_centinel_value( 'OrderId' ) );
			WC()->session->set( 'Centinel_Enrolled', $this->get_centinel_value( 'Enrolled' ) );
			WC()->session->set( 'Centinel_ACSUrl', $this->get_centinel_value( 'ACSUrl' ) );
			WC()->session->set( 'Centinel_Payload', $this->get_centinel_value( 'Payload' ) );
			WC()->session->set( 'Centinel_EciFlag', $this->get_centinel_value( 'EciFlag' ) );

			// If session cookie has not been set, set it now.
			if ( ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			if ( $this->get_centinel_value( 'ErrorNo' ) ) {
				wc_add_notice( __( 'Error in 3D secure authentication: ', 'woocommerce-gateway-paypal-pro' ) . $this->get_centinel_value( 'ErrorDesc' ), 'error' );
				return;
			}

			if ( 'Y' === $this->get_centinel_value( 'Enrolled' ) ) {

				$this->log( 'Doing 3dsecure payment authorization' );
				$this->log( 'ASCUrl: ' . $this->get_centinel_value( 'ACSUrl' ) );
				$this->log( 'PaReq: ' . $this->get_centinel_value( 'Payload' ) );

				$redirect = add_query_arg( 'acs', $order_id, WC()->api_request_url( 'WC_Gateway_PayPal_Pro', true ) );

				if ( $this->enable_3dsecure_cruise ) {
					if ( ! empty( $this->get_centinel_value( 'Payload' ) ) ) {
						$redirect = sprintf(
							'#cardinal-continue-%s:%s:%s:%s',
							rawurlencode( $this->get_centinel_value( 'ACSUrl' ) ),
							$this->get_centinel_value( 'Payload' ),
							$this->get_centinel_value( 'TransactionId' ),
							rawurlencode( $redirect )
						);

						if ( is_wc_endpoint_url( 'order-pay' ) ) {
							$this->continue_3dsecure = $redirect;
							return array();
						}
					} else {
						if ( $this->liability_shift && ( $this->get_centinel_value( 'EciFlag' ) == '07' || $this->get_centinel_value( "EciFlag" ) == '00' ) ) {
							$order->update_status( 'failed', __( '3D Secure error: No liability shift', 'woocommerce-gateway-paypal-pro' ) );
							wc_add_notice( __( 'Authentication unavailable.  Please try a different payment method or card.', 'woocommerce-gateway-paypal-pro' ), 'error' );
							return;
						}

						if ( in_array( $this->get_centinel_value( 'PAResStatus' ), array( 'Y', 'A', 'U' ) ) && "Y" === $this->get_centinel_value( 'SignatureVerification' ) ) {
							$centinel                  = new stdClass();
							$centinel->paresstatus     = $this->get_centinel_value( 'PAResStatus' );
							$centinel->dstransactionid = $this->get_centinel_value( 'DSTransactionId' );
							$centinel->threedsversion  = $this->get_centinel_value( 'ThreeDSVersion' );
							$centinel->xid             = $this->get_centinel_value( 'Xid' );
							$centinel->cavv            = $this->get_centinel_value( 'Cavv' );
							$centinel->eciflag         = $this->get_centinel_value( 'EciFlag' );
							$centinel->enrolled        = WC()->session->get( 'Centinel_Enrolled' );

							// Do payment with paypal.
							return $this->do_payment( $order, $card, $centinel );
						} else {
							wc_add_notice( __( 'Payer Authentication failed. Please try a different payment method.', 'woocommerce-gateway-paypal-pro' ), 'error' );
							return;
						}
					}
				}

				return array(
					'result'   => 'success',
					'redirect' => $redirect,
				);

			} elseif ( $this->liability_shift && 'N' !== $this->get_centinel_value( 'Enrolled' ) ) {
				wc_add_notice( __( 'Authentication unavailable. Please try a different payment method or card.','woocommerce-gateway-paypal-pro' ), 'error' );
				return;
			}
		}

		// Do payment with paypal.
		return $this->do_payment( $order, $card );
	}

	/**
	 * Process a refund if supported.
	 *
	 * @throws Exception if request failed or got unexpected response.
	 *
	 * @param int    $order_id Order ID.
	 * @param float  $amount   Amount.
	 * @param string $reason   Refund reason.
	 *
	 * @return bool|wp_error True or false based on success, or a WP_Error object
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || ! $order->get_transaction_id() || ! $this->api_username || ! $this->api_password || ! $this->api_signature ) {
			return false;
		}

		// Get transaction details.
		$details = $this->get_transaction_details( $order->get_transaction_id() );

		// Check if it is authorized only we need to void instead.
		if ( $details && strtolower( $details['PENDINGREASON'] ) === 'authorization' ) {
			$order->add_order_note( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );
			$this->log( 'Refund order # ' . absint( $order_id ) . ': authorized only transactions need to use cancel/void instead.' );
			throw new Exception( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );
		}

		$post_data = array(
			'VERSION'       => $this->api_version,
			'SIGNATURE'     => $this->api_signature,
			'USER'          => $this->api_username,
			'PWD'           => $this->api_password,
			'METHOD'        => 'RefundTransaction',
			'TRANSACTIONID' => $order->get_transaction_id(),
			'REFUNDTYPE'    => is_null( $amount ) ? 'Full' : 'Partial',
		);

		if ( ! is_null( $amount ) ) {
			$post_data['AMT']          = wc_format_decimal( $amount, 2 );
			$post_data['CURRENCYCODE'] = ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency() );
		}

		if ( $reason ) {
			if ( 255 < strlen( $reason ) ) {
				$reason = substr( $reason, 0, 252 ) . '...';
			}
			$post_data['NOTE'] = html_entity_decode( $reason, ENT_NOQUOTES, 'UTF-8' );
		}

		$response = wp_safe_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
			'method'      => 'POST',
			'headers'     => array( 'PAYPAL-NVP' => 'Y' ),
			'body'        => $post_data,
			'timeout'     => 70,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );
			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		parse_str( $response['body'], $parsed_response );

		if ( ! isset( $parsed_response['ACK'] ) ) {
			throw new Exception( __( 'Unexpected response from PayPal.', 'woocommerce-gateway-paypal-pro' ) );
		}

		switch ( strtolower( $parsed_response['ACK'] ) ) {
			case 'success':
			case 'successwithwarning':
				$order->add_order_note( sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'woocommerce-gateway-paypal-pro' ), $parsed_response['GROSSREFUNDAMT'], $parsed_response['REFUNDTRANSACTIONID'] ) );
				return true;
			default:
				$this->log( 'Parsed Response (refund) ' . print_r( $parsed_response, true ) );
			break;
		}

		return false;
	}

	/**
	 * Auth 3dsecure.
	 */
	public function handle_3dsecure() {
		if ( $this->enable_3dsecure_centinel && ! empty( $_GET['acs'] ) ) {
			$order_id = wc_clean( $_GET['acs'] );
			$acsurl   = WC()->session->get( 'Centinel_ACSUrl' );
			$payload  = WC()->session->get( 'Centinel_Payload' );
			?>
			<html>
				<head>
					<title>3DSecure Payment Authorisation</title>
				</head>
				<body>
					<form name="frmLaunchACS" id="3ds_submit_form" method="POST" action="<?php echo esc_url( $acsurl ); ?>">
						<input type="hidden" name="PaReq" value="<?php echo esc_attr( $payload ); ?>">
						<input type="hidden" name="TermUrl" value="<?php echo esc_attr( WC()->api_request_url( 'WC_Gateway_PayPal_Pro', true ) ); ?>">
						<input type="hidden" name="MD" value="<?php echo absint( $order_id ); ?>">
						<noscript>
							<input type="submit" class="button" id="3ds_submit" value="Submit" />
						</noscript>
					</form>
					<script>
						document.frmLaunchACS.submit();
					</script>
				</body>
			</html>
			<?php
			exit;
		} else {
			$this->auth_3dsecure();
		}
	}

	/**
	 * Handle cmpi_authenticate 3dsecure.
	 *
	 * @throws Exception If request failed or got unexpected response.
	 */
	public function auth_3dsecure() {
		if ( ! class_exists( 'CentinelClient' ) ) {
			include_once( 'lib/CentinelClient.php' );
		}

		$pares        = ! empty( $_POST['PaRes'] ) ? $_POST['PaRes'] : '';
		$order_id     = $this->enable_3dsecure_centinel ? ( absint( ! empty( $_POST['MD'] ) ? $_POST['MD'] : 0 ) ) : $_GET['acs'];
		$order        = wc_get_order( $order_id );
		$redirect_url = $this->get_return_url( $order );

		$this->log( 'authorise_3dsecure() for order ' . absint( $order_id ) );
		$this->log( 'authorise_3dsecure() PARes ' . print_r( $pares, true ) );

		try {
			// If the PaRes is Not Empty then process the cmpi_authenticate message.
			if ( ! ( $this->enable_3dsecure_centinel && empty( $pares ) ) ) {
				$this->centinel_client = new CentinelClient;
				$this->centinel_client->add( 'MsgType', 'cmpi_authenticate' );
				$this->centinel_client->add( 'Version', '1.7' );
				$this->centinel_client->add( 'TransactionType', $this->enable_3dsecure_centinel ? 'CC' : 'C' );
				$this->centinel_client->add( 'TransactionId', WC()->session->get( 'Centinel_TransactionId' ) );
				$this->centinel_client->add( 'PAResPayload', $pares );

				// Credentials.
				if ( $this->enable_3dsecure_centinel ) {
					$this->centinel_client->add( 'ProcessorId', $this->centinel_pid );
					$this->centinel_client->add( 'MerchantId', $this->centinel_mid );
					$this->centinel_client->add( 'TransactionPwd', $this->centinel_pwd );
				} else {
					$timestamp = time() * 1000;
					$signature = base64_encode( hash( 'sha256', $timestamp . $this->cruise_api_key, true ) );
					$this->centinel_client->add( 'Algorithm', 'SHA-256' );
					$this->centinel_client->add( 'Identifier', $this->cruise_api_id );
					$this->centinel_client->add( 'OrgUnit', $this->cruise_org_unit_id );
					$this->centinel_client->add( 'Signature', $signature );
					$this->centinel_client->add( 'Timestamp', $timestamp );
				}

				// Send request.
				$this->centinel_client->sendHttp( $this->centinel_url, '5000', '15000' );

				$request_to_log = $this->centinel_client->request;
				$request_to_log['TransactionPwd'] = '[not logged]';
				$response_to_log = $this->centinel_client->response;
				if ( isset( $response_to_log['CardNumber'] ) || isset( $response_to_log['CardCode'] ) ) {
					$response_to_log['CardNumber'] = '[not logged]';
					$response_to_log['CardCode']   = '[not logged]';
				}
				$this->log( 'Centinel transaction ID: ' . WC()->session->get( 'Centinel_TransactionId' ) );
				$this->log( 'Centinel client request: ' . print_r( $request_to_log, true ) );
				$this->log( 'Centinel client response: ' . print_r( $response_to_log, true ) );
				$this->log( '3dsecure pa_res_status: ' . $this->get_centinel_value( 'PAResStatus' ) );

				if ( $this->liability_shift && ( $this->get_centinel_value( 'EciFlag' ) == '07' || $this->get_centinel_value( "EciFlag" ) == '00' ) ) {
					$order->update_status( 'failed', __( '3D Secure error: No liability shift', 'woocommerce-gateway-paypal-pro' ) );
					throw new Exception( __( 'Authentication unavailable.  Please try a different payment method or card.', 'woocommerce-gateway-paypal-pro' ) );
				}

				if ( ! $this->get_centinel_value( 'ErrorNo' ) && in_array( $this->get_centinel_value( 'PAResStatus' ), array( 'Y', 'A', 'U' ) ) && "Y" === $this->get_centinel_value( 'SignatureVerification' ) ) {
					if ( $this->enable_3dsecure_centinel ) {
						$card              = new stdClass();
						$card->number      = $this->get_centinel_value( 'CardNumber' );
						$card->type        = '';
						$card->cvc         = $this->get_centinel_value( 'CardCode' );
						$card->exp_month   = $this->get_centinel_value( 'CardExpMonth' );
						$card->exp_year    = $this->get_centinel_value( 'CardExpYear' );
						$card->start_month = WC()->session->get( 'Centinel_card_start_month' );
						$card->start_year  = WC()->session->get( 'Centinel_card_start_year' );
					} else {
						$card = $this->get_posted_card();
					}

					$centinel                  = new stdClass();
					$centinel->paresstatus     = $this->get_centinel_value( 'PAResStatus' );
					$centinel->dstransactionid = $this->get_centinel_value( 'DSTransactionId' );
					$centinel->threedsversion  = $this->get_centinel_value( 'ThreeDSVersion' );
					$centinel->xid             = $this->get_centinel_value( 'Xid' );
					$centinel->cavv            = $this->get_centinel_value( 'Cavv' );
					$centinel->eciflag         = $this->get_centinel_value( 'EciFlag' );
					$centinel->enrolled        = WC()->session->get( 'Centinel_Enrolled' );

					// If we are here we can process the card.
					$this->do_payment( $order, $card, $centinel );

				} else {
					$order->update_status( 'failed', sprintf( __( '3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $this->get_centinel_value( 'ErrorDesc' ) ) );
					throw new Exception( __( 'Payer Authentication failed. Please try a different payment method.','woocommerce-gateway-paypal-pro' ) );
				}
			} else {
				$order->update_status( 'failed', sprintf( __( '3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $this->get_centinel_value( 'ErrorDesc' ) ) );
				throw new Exception( __( 'Payer Authentication failed. Please try a different payment method.','woocommerce-gateway-paypal-pro' ) );
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Do payment request.
	 *
	 * @access public
	 * @param object      $order    Order object.
	 * @param object      $card     Card.
	 * @param object|bool $centinel Centinel.
	 */
	public function do_payment( $order, $card, $centinel = false ) {
		$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

		try {
			$post_data = array(
				'VERSION'           => $this->api_version,
				'SIGNATURE'         => $this->api_signature,
				'USER'              => $this->api_username,
				'PWD'               => $this->api_password,
				'METHOD'            => 'DoDirectPayment',
				'PAYMENTACTION'     => $this->paymentaction,
				'IPADDRESS'         => $this->get_user_ip(),
				'AMT'               => wc_format_decimal( $order->get_total(), 2 ),
				'INVNUM'            => $order->get_order_number(),
				'CURRENCYCODE'      => $pre_wc_30 ? $order->get_order_currency() : $order->get_currency(),
				'CREDITCARDTYPE'    => $card->type,
				'ACCT'              => $card->number,
				'EXPDATE'           => $card->exp_month . $card->exp_year,
				'STARTDATE'         => $card->start_month . $card->start_year,
				'CVV2'              => $card->cvc,
				'EMAIL'             => $pre_wc_30 ? $order->billing_email : $order->get_billing_email(),
				'FIRSTNAME'         => $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name(),
				'LASTNAME'          => $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name(),
				'STREET'            => $pre_wc_30 ? trim( $order->billing_address_1 . ' ' . $order->billing_address_2 ) : trim( $order->get_billing_address_1() . ' ' . $order->get_billing_address_2() ),
				'CITY'              => $pre_wc_30 ? $order->billing_city : $order->get_billing_city(),
				'STATE'             => $pre_wc_30 ? $order->billing_state : $order->get_billing_state(),
				'ZIP'               => $pre_wc_30 ? $order->billing_postcode : $order->get_billing_postcode(),
				'COUNTRYCODE'       => $pre_wc_30 ? $order->billing_country : $order->get_billing_country(),
				'SHIPTONAME'        => $pre_wc_30 ? ( $order->shipping_first_name . ' ' . $order->shipping_last_name ) : ( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ),
				'SHIPTOSTREET'      => $pre_wc_30 ? $order->shipping_address_1 : $order->get_shipping_address_1(),
				'SHIPTOSTREET2'     => $pre_wc_30 ? $order->shipping_address_2 : $order->get_shipping_address_2(),
				'SHIPTOCITY'        => $pre_wc_30 ? $order->shipping_city : $order->get_shipping_city(),
				'SHIPTOSTATE'       => $pre_wc_30 ? $order->shipping_state : $order->get_shipping_state(),
				'SHIPTOCOUNTRYCODE' => $pre_wc_30 ? $order->shipping_country : $order->get_shipping_country(),
				'SHIPTOZIP'         => $pre_wc_30 ? $order->shipping_postcode : $order->get_shipping_postcode(),
				'BUTTONSOURCE'      => 'WooThemes_Cart',
			);

			if ( $this->soft_descriptor ) {
				$post_data['SOFTDESCRIPTOR'] = $this->soft_descriptor;
			}

			/* Send Item details - thanks Harold Coronado */
			if ( $this->send_items ) {
				$item_loop = 0;

				if ( count( $order->get_items() ) > 0 ) {
					$ITEMAMT = 0;

					foreach ( $order->get_items() as $item ) {
						if ( $item['qty'] ) {
							$item_name = $item['name'];

							if ( $pre_wc_30 ) {
								$item_meta = new WC_Order_Item_Meta( $item );

								if ( $formatted_meta = $item_meta->display( true, true ) ) {
									$item_name .= ' ( ' . $formatted_meta . ' )';
								}
							} else {
								$item_meta = new WC_Order_Item_Product( $item );

								if ( $formatted_meta = $item_meta->get_formatted_meta_data() ) {
									foreach ( $formatted_meta as $meta ) {
										$item_name .= ' ( ' . $meta->display_key . ': ' . $meta->value . ' )';
									}
								}
							}

							$item_price                           = $this->round( $order->get_item_subtotal( $item, false, false ) );
							$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
							$post_data[ 'L_NAME' . $item_loop ]   = $item_name;
							$post_data[ 'L_AMT' . $item_loop ]    = $item_price;
							$post_data[ 'L_QTY' . $item_loop ]    = $item['qty'];

							$ITEMAMT += $item_price * $item['qty'];
							$item_loop++;
						}
					}

					// Fees
					foreach ( $order->get_fees() as $fee ) {
						$fee_amount                           = $this->round( $fee['line_total'] );
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = trim( substr( $fee['name'], 0, 127 ) );
						$post_data[ 'L_AMT' . $item_loop ]    = $fee_amount;
						$post_data[ 'L_QTY' . $item_loop ]    = 1;

						$ITEMAMT += $fee_amount;

						$item_loop++;
					}

					// Discount
					if ( $order->get_total_discount() > 0 ) {
						$discount_total                       = $this->round( $order->get_total_discount() );
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = __( 'Order Discount', 'woocommerce-gateway-paypal-pro' ) ;
						$post_data[ 'L_AMT' . $item_loop ]    = "-$discount_total";
						$post_data[ 'L_QTY' . $item_loop ]    = 1;

						$ITEMAMT -= $discount_total;
						$item_loop++;
					}

					// Shipping
					if ( $order->get_total_shipping() > 0 ) {
						$post_data['SHIPPINGAMT'] = $this->round( $order->get_total_shipping() );
					}

					// Shipping and tax charges are listed seperatly so subtract them from the expected item total.
					$item_subtotal = $this->round( $order->get_total() ) - $this->round( $order->get_total_shipping() ) - $this->round( $order->get_total_tax() );

					// Fix rounding by ensureing the combined item total is equal to the order item subtotal.
					if ( absint( $item_subtotal * 100 ) !== absint( $ITEMAMT * 100 ) ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = __( 'Rounding Amendment', 'woocommerce-gateway-paypal-pro' );
						$post_data[ 'L_AMT' . $item_loop ]    = ( absint( $item_subtotal * 100 ) - absint( $ITEMAMT * 100 ) ) / 100;
						$post_data[ 'L_QTY' . $item_loop ]    = 1;
					}

					$post_data['ITEMAMT'] = $this->round( $item_subtotal );
					$post_data['TAXAMT']  = $this->round( $order->get_total_tax() );
				}
			}

			if ( $this->debug ) {
				$log         = $post_data;
				$log['ACCT'] = '****';
				$log['CVV2'] = '****';
				$this->log( 'Do payment request ' . print_r( $log, true ) );
			}

			/* 3D Secure */
			if ( $centinel ) {
				$post_data['AUTHSTATUS3DS']   = $centinel->paresstatus;
				$post_data['MPIVENDOR3DS']    = $centinel->enrolled;
				$post_data['DSTRANSACTIONID'] = $centinel->dstransactionid;
				$post_data['THREEDSVERSION']  = $centinel->threedsversion;
				$post_data['CAVV']            = $centinel->cavv;
				$post_data['ECI3DS']          = $centinel->eciflag;
				$post_data['XID']             = $centinel->xid;
			}

			$response = wp_safe_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
				'method'         => 'POST',
				'headers'        => array(
					'PAYPAL-NVP' => 'Y',
				),
				'body'           => apply_filters( 'woocommerce-gateway-paypal-pro_request', $post_data, $order ),
				'timeout'        => 70,
				'user-agent'     => 'WooCommerce',
				'httpversion'    => '1.1',
			));

			if ( is_wp_error( $response ) ) {
				$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );
				throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			}

			$this->log( 'Response ' . print_r( $response['body'], true ) );

			if ( empty( $response['body'] ) ) {
				$this->log( 'Empty response!' );
				throw new Exception( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
			}

			parse_str( $response['body'], $parsed_response );

			$this->log( 'Parsed Response ' . print_r( $parsed_response, true ) );

			if ( ! isset( $parsed_response['ACK'] ) ) {
				throw new Exception( __( 'Unexpected response from PayPal.', 'woocommerce-gateway-paypal-pro' ) );
			}

			$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

			switch ( strtolower( $parsed_response['ACK'] ) ) {
				case 'success':
				case 'successwithwarning':
					$txn_id         = ( ! empty( $parsed_response['TRANSACTIONID'] ) ) ? wc_clean( $parsed_response['TRANSACTIONID'] ) : '';
					$correlation_id = ( ! empty( $parsed_response['CORRELATIONID'] ) ) ? wc_clean( $parsed_response['CORRELATIONID'] ) : '';

					// Get transaction details.
					$details = $this->get_transaction_details( $txn_id );

					// Check if it is captured or authorization only.
					if ( $details && strtolower( $details['PAYMENTSTATUS'] ) === 'pending' && strtolower( $details['PENDINGREASON'] ) === 'authorization' ) {
						// Store captured value
						update_post_meta( $order_id, '_paypalpro_charge_captured', 'no' );
						update_post_meta( $order_id, '_transaction_id', $txn_id );

						// Mark as on-hold
						$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-paypal-pro' ), $txn_id ) );

						// Reduce stock levels
						if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
							$order->reduce_order_stock();
						} else {
							wc_reduce_stock_levels( $order_id );
						}
					} else {

						// Add order note
						$order->add_order_note( sprintf( __( 'PayPal Pro payment completed (Transaction ID: %s, Correlation ID: %s)', 'woocommerce-gateway-paypal-pro' ), $txn_id, $correlation_id ) );

						// Payment complete
						$order->payment_complete( $txn_id );
					}

					// Remove cart
					WC()->cart->empty_cart();

					if ( method_exists( $order, 'get_checkout_order_received_url' ) ) {
	                	$redirect = $order->get_checkout_order_received_url();
	                } else {
	                	$redirect = add_query_arg( 'key', version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_key : $order->get_order_key(), add_query_arg( 'order', $order_id, get_permalink( get_option( 'woocommerce_thanks_page_id' ) ) ) );
	                }

					// Return thank you page redirect
					return array(
						'result' 	=> 'success',
						'redirect'	=> $redirect
					);
				break;
				case 'failure':
				default:

					// Get error message
					if ( ! empty( $parsed_response['L_LONGMESSAGE0'] ) ) {
						$error_message = $parsed_response['L_LONGMESSAGE0'];
					} elseif ( ! empty( $parsed_response['L_SHORTMESSAGE0'] ) ) {
						$error_message = $parsed_response['L_SHORTMESSAGE0'];
					} elseif ( ! empty( $parsed_response['L_SEVERITYCODE0'] ) ) {
						$error_message = $parsed_response['L_SEVERITYCODE0'];
					} elseif ( $this->testmode ) {
						$error_message = print_r( $parsed_response, true );
					}

					// Payment failed :(
					$order->update_status( 'failed', sprintf(__('PayPal Pro payment failed (Correlation ID: %s). Payment was rejected due to an error: ', 'woocommerce-gateway-paypal-pro'), $parsed_response['CORRELATIONID'] ) . '(' . $parsed_response['L_ERRORCODE0'] . ') ' . '"' . $error_message . '"' );

					throw new Exception( $error_message );

				break;
			}

		} catch( Exception $e ) {
			wc_add_notice( '<strong>' . __( 'Payment error', 'woocommerce-gateway-paypal-pro' ) . '</strong>: ' . $e->getMessage(), 'error' );
			return;
		}
	}

	/**
	 * Get transaction details
	 */
	public function get_transaction_details( $transaction_id = 0 ) {
		$url = $this->testmode ? $this->testurl : $this->liveurl;

		$post_data = array(
			'VERSION'       => $this->api_version,
			'SIGNATURE'     => $this->api_signature,
			'USER'          => $this->api_username,
			'PWD'           => $this->api_password,
			'METHOD'        => 'GetTransactionDetails',
			'TRANSACTIONID' => $transaction_id
		);

		$response = wp_safe_remote_post( $url, array(
			'method'      => 'POST',
			'headers'     => array(
				'PAYPAL-NVP' => 'Y'
			),
			'body'        => $post_data,
			'timeout'     => 70,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );
			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		parse_str( $response['body'], $parsed_response );

		if ( ! isset( $parsed_response['ACK'] ) ) {
			return false;
		}

		switch ( strtolower( $parsed_response['ACK'] ) ) {
			case 'success':
			case 'successwithwarning':
				return $parsed_response;
			break;
		}

		return false;
	}

	/**
	 * Get user's IP address.
	 */
	public function get_user_ip() {
		return WC_Geolocation::get_ip_address();
	}

	/**
	 * Clear centinel session.
	 */
	public function clear_centinel_session() {
		WC()->session->set( 'Centinel_ErrorNo', null );
		WC()->session->set( 'Centinel_ErrorDesc', null );
		WC()->session->set( 'Centinel_TransactionId', null );
		WC()->session->set( 'Centinel_OrderId', null );
		WC()->session->set( 'Centinel_Enrolled', null );
		WC()->session->set( 'Centinel_ACSUrl', null );
		WC()->session->set( 'Centinel_Payload', null );
		WC()->session->set( 'Centinel_EciFlag', null );
		WC()->session->set( 'Centinel_card_start_month', null );
		WC()->session->set( 'Centinel_card_start_year', null );
	}

	/**
	 * Adds the necessary hooks to modify the "Pay for order" page in order to clean
	 * it up and prepare it for the continuation of the 3D Secure flow.
	 *
	 * @since 4.5.0
	 * @param WC_Payment_Gateway[] $gateways A list of all available gateways.
	 * @return WC_Payment_Gateway[]          Either the same list or an empty one in the right conditions.
	 */
	public function prepare_order_pay_page( $gateways ) {
		if ( ! is_wc_endpoint_url( 'order-pay' ) || empty( $this->continue_3dsecure ) ) {
			return $gateways;
		}

		add_filter( 'woocommerce_checkout_show_terms', '__return_false' );
		add_filter( 'woocommerce_pay_order_button_html', '__return_false' );
		add_filter( 'woocommerce_no_available_payment_methods_message', array( $this, 'change_no_available_methods_message' ) );
		add_action( 'woocommerce_pay_order_before_submit', array( $this, 'print_hidden_card_fields' ) );

		return array();
	}

	/**
	 * Changes the text of the "No available methods" message to one that indicates
	 * the need for the payment to be authorized.
	 *
	 * @since 4.5.0
	 * @return string the new message.
	 */
	public function change_no_available_methods_message() {
		return wpautop( __( "Almost there!\n\nYour order has already been created, and all that is left to do is for you to authorize the payment with your bank.", 'woocommerce-gateway-paypal-pro' ) );
	}

	/**
	 * Include hidden inputs, to preserve card info for authorization and 3D Secure state.
	 *
	 * @since 4.5.0
	 */
	public function print_hidden_card_fields() {
		$card_number = isset( $_POST['paypal_pro-card-number'] ) ? wc_clean( $_POST['paypal_pro-card-number'] ) : '';
		$card_cvc    = isset( $_POST['paypal_pro-card-cvc'] ) ? wc_clean( $_POST['paypal_pro-card-cvc'] ) : '';
		$card_expiry = isset( $_POST['paypal_pro-card-expiry'] ) ? wc_clean( $_POST['paypal_pro-card-expiry'] ) : '';
		$card_start  = isset( $_POST['paypal_pro-card-start'] ) ? wc_clean( $_POST['paypal_pro-card-start'] ) : '';
		?>
			<input type="hidden" name="<?php echo esc_attr( $this->id ); ?>-card-number" value="<?php echo esc_attr( $card_number ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $this->id ); ?>-card-cvc" value="<?php echo esc_attr( $card_cvc ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $this->id ); ?>-card-expiry" value="<?php echo esc_attr( $card_expiry ); ?>" />
			<?php if ( isset( $_POST['paypal_pro-card-start'] ) ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $this->id ); ?>-card-start" value="<?php echo esc_attr( $card_start ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="<?php echo esc_attr( $this->id ); ?>-continue-3dsecure" value="<?php echo esc_attr( $this->continue_3dsecure ); ?>" />
		<?php
	}

	/**
	 * Add a log entry.
	 *
	 * @param string $message Message to log.
	 */
	public function log( $message ) {
		if ( $this->debug ) {
			if ( ! isset( $this->log ) ) {
				$this->log = new WC_Logger();
			}
			$this->log->add( 'paypal-pro', $message );
		}
	}

	/**
	 * Rounds a float value.
	 *
	 * @param float   $number The number to round.
	 * @param integer $precision The number of decimal places to round to. Optional. Default is 2. PayPal's default behaviour for a majority of currencies, is 2.
	 * @return float The number rounded to the given number of deicmals.
	 */
	protected function round( $number, $precision = 2 ) {
		return round( (float) $number, $precision );
	}

	/**
	 * Converts a 2 decimal float into cents (undecimalised format).
	 *
	 * Useful for converting order amounts into the required format for Centinel.
	 *
	 * @param float  $amount A float value to convert into undecimalised format.
	 * @param string $rounding_behaviour Whether to round the amount or not. Optional. Default is 'round' which will round the amount to 2 decimal places and return a value in the format required by Centinel.
	 *
	 * @return int The given amount in cents (undecimalised format).
	 */
	protected function decimal_to_cents( $amount, $rounding_behaviour = 'round' ) {
		if ( 'round' === $rounding_behaviour ) {
			$amount = $this->round( $amount );
		}

		return $amount * 100;
	}
}
