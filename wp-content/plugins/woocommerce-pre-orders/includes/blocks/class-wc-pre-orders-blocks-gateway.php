<?php
/**
 * WooCommerce Pre-Orders Payment Gateway for WooCommerce Blocks.
 *
 * A class to extend the payment methods type class provided by WooCommerce Blocks.
 *
 * @package WooCommerce Pre-orders
 */

namespace WooCommerce\Pre_Orders\Blocks;

use Automattic\WooCommerce\Blocks\Assets\Api;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Pre-orders Gateway method integration.
*/
final class WC_Pre_Orders_Blocks_Gateway extends AbstractPaymentMethodType {
	/**
	 * Payment method name defined by payment methods extending this class.
	 *
	 * @var string
	 */
	protected $name = 'pre_orders_pay_later';

	/**
	 * An instance of the Asset Api.
	 *
	 * @var Api
	 */
	private $asset_api;

	/**
	 * Is the payment method enabled.
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
		$is_enabled = isset( $payment_gateways['pre_orders_pay_later'] ) ? $payment_gateways['pre_orders_pay_later']->enabled : 'no';

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

		$base_path  = WC_PRE_ORDERS_PLUGIN_PATH;
		$asset_url  = WC_PRE_ORDERS_PLUGIN_URL . '/build/index.js';
		$css_url    = WC_PRE_ORDERS_PLUGIN_URL . '/build/index.css';
		$version    = WC_PRE_ORDERS_VERSION;
		$asset_path = $base_path . '/build/index.asset.php';

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
			'pre_orders_pay_later',
			$asset_url,
			$dependencies,
			$version,
			true
		);

		wp_enqueue_style(
			'pre_orders_pay_later_css',
			$css_url,
			array(),
			$version
		);

		return array( 'pre_orders_pay_later' );
	}

	/**
	 * Gets payment method supported features.
	 *
	 * @return array
	 */
	public function get_supported_features() {
		return array( 'products', 'pre-orders' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {

		return array(
			'title'             => __( 'Pay Later', 'wc-pre-orders' ),
			'description'       => __( 'You will receive an email when the pre-order is available along with instructions on how to complete your order.', 'wc-pre-orders' ),
			'order_button_text' => __( 'Place Pre-order Now', 'wc-pre-orders' ),
			'supports'          => $this->get_supported_features(),
			'is_enabled'        => WC_Pre_Orders_Blocks_Integration::is_pre_order_and_charged_upon_release(),
		);
	}
}
