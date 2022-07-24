<?php
/**
 * Store Credit Settings.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Store_Credit_Admin_Settings', false ) ) {
	return new WC_Store_Credit_Admin_Settings();
}

/**
 * WC_Store_Credit_Admin_Settings class.
 */
class WC_Store_Credit_Admin_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->id    = 'store_credit';
		$this->label = _x( 'Store Credit', 'settings tab', 'woocommerce-store-credit' );

		parent::__construct();

		add_filter( "woocommerce_get_settings_{$this->id}", array( $this, 'register_settings' ), 0 );
		add_filter( 'woocommerce_admin_settings_sanitize_option_wc_store_credit_cart_notice', array( $this, 'sanitize_cart_notice' ) );
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function register_settings() {
		$settings = array(
			array(
				'id'   => 'store_credit_general',
				'name' => _x( 'Store Credit', 'settings title', 'woocommerce-store-credit' ),
				'desc' => _x( 'The following options are specific to store credit coupons.', 'settings desc', 'woocommerce-store-credit' ),
				'type' => 'title',
			),
			array(
				'id'      => 'wc_store_credit_show_my_account',
				'name'    => _x( 'My Account', 'setting label', 'woocommerce-store-credit' ),
				'desc'    => _x( 'Display store credit on the My Account page.', 'setting desc', 'woocommerce-store-credit' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'id'      => 'wc_store_credit_delete_after_use',
				'name'    => _x( 'Delete after use', 'setting label', 'woocommerce-store-credit' ),
				'desc'    => _x( 'Delete the coupon when the credit is exhausted.', 'setting desc', 'woocommerce-store-credit' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'id'      => 'wc_store_credit_individual_use',
				'name'    => _x( 'Individual use', 'setting label', 'woocommerce-store-credit' ),
				'desc'    => _x( 'The coupon cannot be used in conjunction with other coupons.', 'setting desc', 'woocommerce-store-credit' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
		);

		$setting_inc_tax = array(
			'id'       => 'wc_store_credit_inc_tax',
			'name'     => _x( 'Include tax', 'setting desc', 'woocommerce-store-credit' ),
			'desc'     => _x( 'The coupon amount includes taxes.', 'setting desc', 'woocommerce-store-credit' ),
			'desc_tip' => _x( "The options 'Prices entered with tax' and 'Round tax at subtotal' must be enabled.", 'setting desc', 'woocommerce-store-credit' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		);

		// Disable it if the option is not available.
		if ( ! wc_store_credit_coupons_can_inc_tax() ) {
			$setting_inc_tax['value']             = 'no';
			$setting_inc_tax['custom_attributes'] = array( 'disabled' => true );
		}

		$settings[] = $setting_inc_tax;

		$setting_apply_to_shipping = array(
			'id'      => 'wc_store_credit_apply_to_shipping',
			'name'    => _x( 'Apply to shipping', 'setting label', 'woocommerce-store-credit' ),
			'desc'    => _x( 'Apply the remaining coupon amount to the shipping costs.', 'setting desc', 'woocommerce-store-credit' ),
			'type'    => 'checkbox',
			'default' => 'no',
		);

		// Disable it if the option is not available.
		if ( ! wc_shipping_enabled() ) {
			$setting_apply_to_shipping['value']             = 'no';
			$setting_apply_to_shipping['desc_tip']          = _x( 'Shipping not enabled.', 'setting desc', 'woocommerce-store-credit' );
			$setting_apply_to_shipping['custom_attributes'] = array( 'disabled' => true );
		}

		$settings[] = $setting_apply_to_shipping;

		/* translators: %s: list of placeholders */
		$placeholder_text = sprintf( __( 'Available placeholders: %s', 'woocommerce-store-credit' ), '{coupon_code}' );

		$settings[] = array(
			'id'          => 'wc_store_credit_code_format',
			'name'        => _x( 'Coupon code format', 'setting label', 'woocommerce-store-credit' ),
			'desc'        => $placeholder_text,
			'desc_tip'    => true,
			'type'        => 'text',
			'placeholder' => '{coupon_code}',
		);

		$settings[] = array(
			'id'      => 'wc_store_credit_show_cart_notice',
			'name'    => _x( 'Display coupons', 'setting label', 'woocommerce-store-credit' ),
			'desc'    => _x( "Display the customer's coupons on the Cart and Checkout pages.", 'setting desc', 'woocommerce-store-credit' ),
			'type'    => 'checkbox',
			'default' => 'yes',
		);

		$settings[] = array(
			'id'                => 'wc_store_credit_cart_notice',
			'name'              => _x( 'Coupons notice', 'setting label', 'woocommerce-store-credit' ),
			'desc'              => _x( 'The notice to display on the Cart and Checkout pages when the customer has coupons available.', 'setting desc', 'woocommerce-store-credit' ),
			'desc_tip'          => true,
			'type'              => 'textarea',
			'placeholder'       => sprintf(
				'%1$s [link]%2$s[/link]',
				__( 'You have store credit coupons available!', 'woocommerce-store-credit' ),
				__( 'View coupons', 'woocommerce-store-credit' )
			),
			'custom_attributes' => array(
				'rows' => 3,
			),
		);

		$settings[] = array(
			'id'   => 'store_credit_general',
			'type' => 'sectionend',
		);

		return $settings;
	}

	/**
	 * Sanitizes the option 'cart_notice'.
	 *
	 * @since 4.2.0
	 *
	 * @param string $value The option value.
	 * @return string
	 */
	public function sanitize_cart_notice( $value ) {
		if ( ! empty( $value ) && ! preg_match( '/\[link].+\[\/link]/', $value ) ) {
			$value  = str_replace( array( '[link]', '[/link]' ), '', $value );
			$value .= ' [link]' . __( 'View coupons', 'woocommerce-store-credit' ) . '[/link]';
		}

		return $value;
	}
}

return new WC_Store_Credit_Admin_Settings();
