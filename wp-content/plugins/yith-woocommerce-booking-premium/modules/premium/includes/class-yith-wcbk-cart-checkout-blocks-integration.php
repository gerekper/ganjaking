<?php
/**
 * Cart & Checkout Blocks integration class.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cart_Checkout_Blocks_Integration' ) ) {
	/**
	 * Cart & Checkout Blocks integration class.
	 *
	 * @since 5.5.0
	 */
	class YITH_WCBK_Cart_Checkout_Blocks_Integration implements IntegrationInterface {

		/**
		 * The name of the integration.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'yith-wcbk-cart-checkout-blocks';
		}

		/**
		 * When called invokes any initialization/setup for the integration.
		 */
		public function initialize() {
			$dist_scripts = array(
				'yith-wcbk-wc-blocks-cart-checkout' => 'wc-blocks/cart-checkout',
			);

			foreach ( $dist_scripts as $handle => $path ) {
				$asset_file  = yith_wcbk_get_module_path( 'premium', "dist/{$path}/index.asset.php" );
				$script_data = file_exists( $asset_file ) ? require $asset_file : null;

				if ( $script_data ) {
					wp_register_script(
						$handle,
						yith_wcbk_get_module_url( 'premium', "dist/{$path}/index.js" ),
						$script_data['dependencies'] ?? array(),
						$script_data['version'] ?? YITH_WCBK_VERSION,
						true
					);
				}
			}
		}

		/**
		 * Get script handles.
		 *
		 * @return array
		 */
		public function get_script_handles() {
			return array( 'yith-wcbk-wc-blocks-cart-checkout' );
		}

		/**
		 * Get editor script handles.
		 *
		 * @return array
		 */
		public function get_editor_script_handles() {
			return array();
		}

		/**
		 * Get script data.
		 *
		 * @return array
		 */
		public function get_script_data() {
			return array(
				'showBookingOfInCartAndCheckout' => 'yes' === get_option( 'yith-wcbk-show-booking-of-in-cart-and-checkout', 'no' ),
				'bookingOfLabel'                 => yith_wcbk_get_label( 'booking-of' ),
			);
		}
	}
}
