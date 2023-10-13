<?php
/**
 * Cart & Checkout blocks class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cart_Checkout_Blocks' ) ) {
	/**
	 * Cart & Checkout blocks  class.
	 * Manage Cart and Checkout blocks' behaviors.
	 *
	 * @since 5.5.0
	 */
	class YITH_WCBK_Cart_Checkout_Blocks {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			if ( did_action( 'woocommerce_blocks_loaded' ) ) {
				$this->initialize();
			} else {
				add_action( 'woocommerce_blocks_loaded', array( $this, 'initialize' ) );
			}
		}

		/**
		 * Initialize Cart and Checkout blocks integration.
		 */
		public function initialize() {
			require_once yith_wcbk_get_module_path( 'premium', 'includes/class-yith-wcbk-cart-checkout-blocks-integration.php' );
			add_action( 'woocommerce_blocks_cart_block_registration', array( $this, 'register_cart_and_checkout_blocks_integration' ) );
			add_action( 'woocommerce_blocks_checkout_block_registration', array( $this, 'register_cart_and_checkout_blocks_integration' ) );

			woocommerce_store_api_register_endpoint_data(
				array(
					'endpoint'        => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema::IDENTIFIER,
					'namespace'       => 'yith-booking',
					'data_callback'   => array( $this, 'get_cart_item_data' ),
					'schema_callback' => array( $this, 'get_cart_item_data_schema' ),
					'schema_type'     => ARRAY_A,
				)
			);
		}

		/**
		 * Get data of the cart item.
		 *
		 * @param array $cart_item Cart item data.
		 *
		 * @return array
		 */
		public function get_cart_item_data( $cart_item ): array {
			$product = $cart_item['data'] ?? false;

			return array(
				'isBookableProduct' => yith_wcbk_is_booking_product( $product ),
			);
		}

		/**
		 * Get the schema for the data of cart items.
		 */
		public function get_cart_item_data_schema() {
			return array(
				'isBookableProduct' => array(
					'description' => __( 'True if the cart item is a bookable product.', 'yith-booking-for-woocommerce' ),
					'type'        => 'boolean',
					'readonly'    => true,
				),
			);
		}

		/**
		 * Register Cart and Checkout blocks integration
		 *
		 * @param Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry $integration_registry The registry.
		 */
		public function register_cart_and_checkout_blocks_integration( $integration_registry ) {
			$integration_registry->register( new YITH_WCBK_Cart_Checkout_Blocks_Integration() );
		}
	}
}
