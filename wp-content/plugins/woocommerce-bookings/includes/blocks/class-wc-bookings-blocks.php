<?php
/**
 * WooCommerce Bookings Blocks Integration.
 */

namespace WooCommerce\Bookings\Blocks;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Blocks\Registry\Container;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;

/**
 * This class is responsible for integrating a new payment method when using WooCommerce Blocks.
 */
class WC_Bookings_Blocks_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Include needed files.
		$this->includes();

		// Add woocommerce blocks support.
		$this->add_woocommerce_block_support();

		/**
		 * This function enables block based product list components to change
		 * the Add to cart button into a link to the product detail page.
		 */
		add_filter(
			'woocommerce_product_has_options',
			function ( $has_options, $product ) {
				if ( 'booking' === $product->get_type() ) {
					return true;
				}
				return $has_options;
			},
			10,
			2
		);
	}

	/**
	 * Add payment method block support
	 */
	public function add_woocommerce_block_support() {

		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			// Register payment method integrations.
			add_action( 'woocommerce_blocks_payment_method_type_registration', array( $this, 'register_payment_method_integrations' ) );
			$this->register_payment_methods();
			$this->register_payment_requirements();
		}
	}

	/**
	 * Register payment method
	 *
	 * @return void
	 */
	protected function register_payment_methods() {

		$container = Package::container();

		$container->register(
			Bookings_Gateway::class,
			function( Container $container ) {
				$asset_api = $container->get( AssetApi::class );
				return new Bookings_Gateway( $asset_api );
			}
		);
	}

	/**
	 * Register the payment requirements for blocks
	 *
	 * @return void
	 */
	public function register_payment_requirements() {

		// Get extend class from the container.
		$extend = StoreApi::container()->get( ExtendSchema::class );

		// Add payment requirements for booking availability carts.
		$extend->register_payment_requirements(
			array(
				'data_callback' => array( $this, 'add_booking_availability_payment_requirement' ),
			)
		);
	}

	/**
	 * Adds booking availability payment requirement for carts that contain a product that requires it.
	 *
	 * @return array
	 */
	public function add_booking_availability_payment_requirement() {
		if ( wc_booking_cart_requires_confirmation() ) {
			return array( 'booking_availability' );
		}
		return array();
	}

	/**
	 * Register payment method integration
	 *
	 * @param PaymentMethodRegistry $payment_method_registry Payment method registry object.
	 */
	public function register_payment_method_integrations( PaymentMethodRegistry $payment_method_registry ) {

		$payment_method_registry->register(
			Package::container()->get( Bookings_Gateway::class )
		);
	}

	/**
	 * Include needed files
	 */
	public function includes() {
		require_once __DIR__ . '/class-wc-bookings-gateway.php';
	}
}
