<?php
/**
 * Settings: General
 *
 * @package WC_Account_Funds/Admin/Settings
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Account_Funds_Settings_API', false ) ) {
	include_once WC_ACCOUNT_FUNDS_PATH . 'includes/abstracts/abstract-wc-account-funds-settings-api.php';
}

if ( class_exists( 'WC_Account_Funds_Settings_General', false ) ) {
	return;
}

/**
 * WC_Account_Funds_Settings_General class.
 */
class WC_Account_Funds_Settings_General extends WC_Account_Funds_Settings_API {

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		$this->id = 'settings';
	}

	/**
	 * Gets the name of the option in the WP DB.
	 *
	 * @since 2.6.0
	 *
	 * @param string $setting Optional. Setting key.
	 * @return string
	 */
	public function get_option_key( $setting = '' ) {
		$option_key = $this->plugin_id;

		// The setting key when saving them individually.
		if ( $setting ) {
			$option_key .= $setting;
		}

		return $option_key;
	}

	/**
	 * Prefix key for settings.
	 *
	 * @since 2.6.0
	 *
	 * @param  string $key Field key.
	 * @return string
	 */
	public function get_field_key( $key ) {
		return ( $this->plugin_id . $key );
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 2.6.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'name'            => array(
				'type'        => 'text',
				'title'       => __( 'Funds name', 'woocommerce-account-funds' ),
				'description' => __( 'Use a different name to refer to the account funds.', 'woocommerce-account-funds' ),
				'placeholder' => __( 'Account Funds', 'woocommerce-account-funds' ),
			),
			'partial_payment' => array(
				'type'        => 'checkbox',
				'title'       => __( 'Partial payment', 'woocommerce-account-funds' ),
				'label'       => __( 'Allow customers to apply available funds and pay the difference via another gateway.', 'woocommerce-account-funds' ),
				'description' => __( 'If disabled, users must pay for the entire order using the account funds payment gateway.', 'woocommerce-account-funds' ),
			),
			'add_on_register' => array(
				'type'        => 'price',
				'title'       => __( 'Funds on register', 'woocommerce-account-funds' ),
				'description' => __( 'Add funds to the customer account on registration.', 'woocommerce-account-funds' ),
				'placeholder' => __( 'N/A', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
			),
			'funding_title'   => array(
				'type'  => 'title',
				'title' => __( 'Funding', 'woocommerce-account-funds' ),
			),
			'enable_topup'    => array(
				'type'  => 'checkbox',
				'title' => __( 'Enable "My Account" Top-up', 'woocommerce-account-funds' ),
				'label' => __( 'Allow customers to top up funds via their account page.', 'woocommerce-account-funds' ),
			),
			'min_topup'       => array(
				'type'        => 'price',
				'title'       => __( 'Minimum Top-up', 'woocommerce-account-funds' ),
				'placeholder' => 1,
			),
			'max_topup'       => array(
				'type'        => 'price',
				'title'       => __( 'Maximum Top-up', 'woocommerce-account-funds' ),
				'placeholder' => __( 'N/A', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
			),
			'discount_title'  => array(
				'type'  => 'title',
				'title' => __( 'Discount Settings', 'woocommerce-account-funds' ),
			),
			'give_discount'   => array(
				'type'  => 'checkbox',
				'title' => __( 'Give discount', 'woocommerce-account-funds' ),
				'label' => __( 'Apply a discount when account funds are used to purchase items', 'woocommerce-account-funds' ),
			),
			'discount_type'   => array(
				'type'     => 'select',
				'title'    => __( 'Discount type', 'woocommerce-account-funds' ),
				'desc_tip' => __( 'Percentage discounts will be based on the amount of funds used.', 'woocommerce-account-funds' ),
				'options'  => array(
					'fixed'      => __( 'Fixed Price', 'woocommerce-account-funds' ),
					'percentage' => __( 'Percentage', 'woocommerce-account-funds' ),
				),
			),
			'discount_amount' => array(
				'type'     => 'price',
				'title'    => __( 'Discount amount', 'woocommerce-account-funds' ),
				'desc_tip' => __( 'Enter numbers only. Do not include the percentage sign.', 'woocommerce-account-funds' ),
			),
		);
	}

	/**
	 * Gets the form title.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return __( 'Account Funds', 'woocommerce-account-funds' );
	}
}
