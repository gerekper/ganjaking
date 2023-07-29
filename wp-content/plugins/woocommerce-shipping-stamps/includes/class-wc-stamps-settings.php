<?php
/**
 * Settings class.
 *
 * @package WC_Stamps_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Stamps_Settings class
 */
class WC_Stamps_Settings {

	const SETTINGS_NAMESPACE = 'stamps';

	/**
	 * Get the setting fields
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @return array $setting_fields
	 */
	private static function get_fields() {
		$states = WC()->countries->get_states( 'US' );

		// Like the USPS, Stamps.com also accepts selected US territories as states
		// for the purpose of shipping origin (return) address.
		//
		// Note: 'UM' - the United States (US) Minor Outlying Islands was not
		// included since the islands are not open for commercial activity.
		$countries_supported_as_states = array(
			'AS',	// American Samoa.
			'GU',	// Guam.
			'MP',	// Northern Mariana Islands.
			'PR',	// Puerto Rico.
			'VI',	// United States (US) Virgin Islands.
		);

		$all_countries = WC()->countries->get_countries();
		foreach ( $countries_supported_as_states as $country_supported_as_state ) {
			// AS, GU, MP, PR and VI were moved from states into countries in WooCommerce 2.6
			// so we need to test for the array key before using it in case we are running
			// in a pre 2.6 environment.
			if ( array_key_exists( $country_supported_as_state, $all_countries ) ) {
				$states[ $country_supported_as_state ] = $all_countries[ $country_supported_as_state ];
			}
		}

		$setting_fields = array(
			'account' => array(
				'name' => __( 'Stamps.com account', 'woocommerce-shipping-stamps' ),
				'type' => 'title',
				'desc' => __( 'Input your Stamps.com account details so that the plugin can make requests on your behalf.', 'woocommerce-shipping-stamps' ),
				'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_account',
			),
			'stamps_useranme' => array(
				'name'     => __( 'Username', 'woocommerce-shipping-stamps' ),
				'type'     => 'text',
				'desc'     => __( 'Use your Stamps.com credentials.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_username',
				'default'  => '',
				'css'      => 'min-width: 400px;',
			),
			'stamps_password' => array(
				'name'     => __( 'Password', 'woocommerce-shipping-stamps' ),
				'type'     => 'password',
				'desc'     => __( 'Use your Stamps.com credentials.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_password',
				'default'  => '',
				'css'      => 'min-width: 400px;',
			),
			'logging' => array(
				'name'     => __( 'Logging', 'woocommerce-shipping-stamps' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable logging, for debugging.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => __( 'Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce-shipping-stamps' ),
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_logging',
				'default'  => 'no',
			),
			'account_end' => array(
				'type' => 'sectionend',
			),
			'auto_funding' => array(
				'name' => __( 'Automatic funding', 'woocommerce-shipping-stamps' ),
				'type' => 'title',
				'desc' => __( 'These settings let you automatically purchase postage when your balance reaches a certain threshold.', 'woocommerce-shipping-stamps' ),
				'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_auto_funding',
			),
			'threshold' => array(
				'name'        => __( 'Threshold', 'woocommerce-shipping-stamps' ),
				'placeholder' => __( 'n/a', 'woocommerce-shipping-stamps' ),
				'type'        => 'number',
				'css'         => 'width:50px;',
				'desc'        => __( 'Top up when balance goes below this amount. Leave blank to disable.', 'woocommerce-shipping-stamps' ),
				'desc_tip'    => true,
				'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_top_up_threshold',
				'default'     => '',
			),
			'purchase_amount' => array(
				'name'              => __( 'Purchase amount', 'woocommerce-shipping-stamps' ),
				'placeholder'       => '0',
				'type'              => 'number',
				'css'               => 'width:50px;',
				'desc'              => __( 'Purchase this much postage when the threshold is reached. Enter whole amount (integer) in dollars. E.g. <code>100</code>. Minimum purchase is 10 dollars. Maximum purchase is 500 dollars.', 'woocommerce-shipping-stamps' ),
				'desc_tip'          => true,
				'id'                => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_purchase_amount',
				'default'           => '100',
				'custom_attributes' => array(
					'min'  => 10,
					'step' => 1,
				),
			),
			'auto_funding_end' => array(
				'type' => 'sectionend',
			),
			'labels' => array(
				'name' => __( 'Label settings', 'woocommerce-shipping-stamps' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_labels',
			),
			'shipping_date' => array(
				'name'     => __( 'Default shipping date', 'woocommerce-shipping-stamps' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc'     => __( 'Specifies the default shipping date when printing a label.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_shipping_date',
				'default'  => '1',
				'options'  => array(
					'0' => __( 'Same Day', 'woocommerce-shipping-stamps' ),
					'1' => __( 'Next Day', 'woocommerce-shipping-stamps' ),
					'2' => __( 'Two Days Later', 'woocommerce-shipping-stamps' ),
					'3' => __( 'Three Days Later', 'woocommerce-shipping-stamps' ),
				),
			),
			'image_type' => array(
				'name'     => __( 'Image type', 'woocommerce-shipping-stamps' ),
				'type'     => 'select',
				'desc'     => __( 'Specifies the image type for the returned label.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_image_type',
				'default'  => 'Pdf',
				'options' => array(
					'Auto' => __( 'Default format; PNG for domestic, PDF for international', 'woocommerce-shipping-stamps' ),
					'Epl'  => __( 'EPL', 'woocommerce-shipping-stamps' ),
					'Gif'  => __( 'GIF', 'woocommerce-shipping-stamps' ),
					'Jpg'  => __( 'JPG', 'woocommerce-shipping-stamps' ),
					'Pdf'  => __( 'PDF', 'woocommerce-shipping-stamps' ),
					'Png'  => __( 'PNG', 'woocommerce-shipping-stamps' ),
					'Zpl'  => __( 'ZPL', 'woocommerce-shipping-stamps' ),
				),
			),
			'paper_size' => array(
				'name'     => __( 'Paper size (for PDF)', 'woocommerce-shipping-stamps' ),
				'type'     => 'select',
				'desc'     => __( 'Specifies the page size of PDF labels. This value only applies to PDF.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paper_size',
				'default'  => 'Default',
				'options'  => array(
					'Default'     => __( 'Use default page size.', 'woocommerce-shipping-stamps' ),
					'Letter85x11' => __( 'Use letter page size.', 'woocommerce-shipping-stamps' ),
					'LabelSize'   => __( 'The page size is same as label size.', 'woocommerce-shipping-stamps' ),
				),
			),
			'print_layout' => array(
				'name'     => __( 'Print layout (for PDF)', 'woocommerce-shipping-stamps' ),
				'type'     => 'select',
				'desc'     => __( 'Specifies the print layout for labels. This value only applies to PDF.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => true,
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_print_layout',
				'default'  => '',
				'options'  => array(
					'Normal'      => __( 'Default', 'woocommerce-shipping-stamps' ),
					'NormalLeft'  => __( '4x6 label generated on the left side of the page.', 'woocommerce-shipping-stamps' ),
					'NormalRight' => __( '4x6 label generated on the right side of the page.', 'woocommerce-shipping-stamps' ),
					'Normal4X6'   => __( '4x6 label generated on a 4x6 page.', 'woocommerce-shipping-stamps' ),
					'Normal6X4'   => __( '6x4 label generated on a 6x4 page.', 'woocommerce-shipping-stamps' ),
					'Normal75X2'  => __( '7.5x2 label generated on a 7.5x2 page.', 'woocommerce-shipping-stamps' ),
					'Normal4X675' => __( '4x6 3⁄4 doc-tab will be generated.', 'woocommerce-shipping-stamps' ),
				),
			),
			'sample_only' => array(
				'name'     => __( 'Samples only', 'woocommerce-shipping-stamps' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable create samples only.', 'woocommerce-shipping-stamps' ),
				'desc_tip' => __( 'This will create sample labels which cannot be used for posting items. No payments will be taken.', 'woocommerce-shipping-stamps' ),
				'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_sample_only',
				'default'  => 'yes',
			),
			'labels_end' => array(
				'type' => 'sectionend',
			),
			'shipping_address' => array(
				'name' => __( 'Shipping return address', 'woocommerce-shipping-stamps' ),
				'type' => 'title',
				'desc' => __( 'This address is used for the "from" address when getting rates from Stamps.com.', 'woocommerce-shipping-stamps' ),
				'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_shipping_address',
			),
			'full_name' => array(
				'name'    => __( 'Full name', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_full_name',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'company' => array(
				'name'    => __( 'Company', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_company',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'address_1' => array(
				'name'    => __( 'Address line 1', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_address_1',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'address_2' => array(
				'name'    => __( 'Address line 2', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_address_2',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'city' => array(
				'name'    => __( 'City', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_city',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'state' => array(
				'name'    => __( 'State', 'woocommerce-shipping-stamps' ),
				'type'    => 'select',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_state',
				'default' => '',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width: 350px;',
				'options' => $states,
			),
			'zip' => array(
				'name'              => __( 'ZIP code', 'woocommerce-shipping-stamps' ),
				'type'              => 'number',
				'desc'              => '',
				'id'                => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_zip',
				'default'           => '',
				'css'               => 'min-width: 400px;',
				'custom_attributes' => array(
					'maxlength' => 5,
					'max'       => 99999,
				),
			),
			'phone' => array(
				'name'    => __( 'Phone number', 'woocommerce-shipping-stamps' ),
				'type'    => 'text',
				'desc'    => '',
				'id'      => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_phone',
				'default' => '',
				'css'     => 'min-width: 400px;',
			),
			'shipping_address_end' => array(
				'type' => 'sectionend',
			),
		);

		/**
		 * Filter: 'wc_settings_tab_anti_fraud' - Allow altering extension setting fields
		 *
		 * @param array $setting_fields The fields
		 */
		return apply_filters( 'wc_settings_tab_' . self::SETTINGS_NAMESPACE, $setting_fields );
	}

	/**
	 * Get an option set in our settings tab
	 *
	 * @param string $key Key name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Option value.
	 */
	public static function get_option( $key ) {
		$fields = self::get_fields();

		return apply_filters( 'wc_option_' . $key, get_option( 'wc_settings_' . self::SETTINGS_NAMESPACE . '_' . $key, ( ( isset( $fields[ $key ] ) ) ? $fields[ $key ] : '' ) ) );
	}

	/**
	 * Setup the WooCommerce settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'add_settings_tab' ), 70 );
		add_action( 'woocommerce_settings_tabs_' . self::SETTINGS_NAMESPACE, array( __CLASS__, 'tab_content' ) );
		add_action( 'woocommerce_update_options_' . self::SETTINGS_NAMESPACE, array( __CLASS__, 'update_settings' ) );
	}

	/**
	 * Add a settings tab to the settings page
	 *
	 * @param array $settings_tabs List of setting tabs.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array List of setting tabs.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs[ self::SETTINGS_NAMESPACE ] = __( 'Stamps.com', 'woocommerce-shipping-stamps' );
		return $settings_tabs;
	}

	/**
	 * Output the tab content.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public static function tab_content() {
		if ( get_option( 'wc_settings_stamps_username' ) && get_option( 'wc_settings_stamps_password' ) && ! get_option( 'wc_settings_stamps_zip' ) ) {
			/* translators: 1) opening anchor tag 2) closing anchor tag */
			echo '<div class="error"><p>' . sprintf( esc_html__( 'Shipping Return Address: Zip code is a required field. Please enter it on the %1$sStamps.com settings page%2$s.', 'woocommerce-shipping-stamps' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=stamps' ) ) . '">', '</a>' ) . '</p></div>';
		}
		woocommerce_admin_fields( self::get_fields() );
	}

	/**
	 * Update the settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_fields() );
	}
}

WC_Stamps_Settings::init();
