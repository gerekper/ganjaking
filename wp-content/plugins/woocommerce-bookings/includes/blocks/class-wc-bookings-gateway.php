<?php

namespace WooCommerce\Bookings\Blocks;

use Automattic\WooCommerce\Blocks\Assets\Api;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Bookings Gateway method integration
 *
 * @since 0.1
 */
final class Bookings_Gateway extends AbstractPaymentMethodType {
	/**
	 * Payment method name defined by payment methods extending this class.
	 *
	 * @var string
	 */
	protected $name = 'wc-bookings-gateway';

	/**
	 * An instance of the Asset Api
	 *
	 * @var Api
	 */
	private $asset_api;

	/**
	 * Is the payment method enabled
	 *
	 * @var Api
	 */
	private $enabled;

	/**
	 * Constructor
	 *
	 * @param Api $asset_api An instance of Api.
	 */
	public function __construct( Api $asset_api ) {
		$this->asset_api = $asset_api;
	}

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {

		$payment_gateways = WC()->payment_gateways->payment_gateways();
		$is_enabled       = isset( $payment_gateways['wc-bookings-gateway'] ) ? $payment_gateways['wc-bookings-gateway']->enabled : 'no';

		$this->enabled = $is_enabled;
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {

		if ( 'yes' !== $this->enabled ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {

		$base_path  = WC_BOOKINGS_PLUGIN_PATH;
		$asset_url  = WC_BOOKINGS_PLUGIN_URL . '/dist/blocks.js';
		$version    = 1.0;
		$asset_path = $base_path . '/dist/blocks.asset.php';

		$dependencies = array();
		if ( file_exists( $asset_path ) ) {
			$asset        = require $asset_path;
			$version      = is_array( $asset ) && isset( $asset['version'] )
				? $asset['version']
				: $version;
			$dependencies = is_array( $asset ) && isset( $asset['dependencies'] )
				? $asset['dependencies']
				: $dependencies;
		}

		wp_register_script(
			'wc-bookings-gateway',
			$asset_url,
			$dependencies,
			$version,
			true
		);

		return array( 'wc-bookings-gateway' );
	}

	/**
	 * Gets payment method supported features.
	 *
	 * @return array
	 */
	public function get_supported_features() {
		return array( 'products', 'booking_availability' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {

		return array(
			'title'             => __( 'Check booking availability', 'woocommerce-bookings' ),
			'description'       => __( 'Use this payment method to check out and get availability from the shop owner.', 'woocommerce-bookings' ),
			'order_button_text' => __( 'Request Confirmation', 'woocommerce-bookings' ),
			'supports'          => $this->get_supported_features(),
			'is_enabled'        => wc_booking_cart_requires_confirmation(),
		);
	}
}
