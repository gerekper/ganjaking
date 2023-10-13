<?php
/**
 * Class YITH_WCBK_Google_Maps_Product_Data_Extension
 * Handle product data for the Google Maps module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Maps_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Google_Maps_Product_Data_Extension class.
	 */
	class YITH_WCBK_Google_Maps_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * YITH_WCBK_Google_Maps_Product_Data_Extension constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'yith_wcbk_product_tab_settings_after', array( $this, 'print_google_maps_section_in_product_settings' ), 10, 2 );
		}

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					'_yith_booking_location'     => 'location',
					'_yith_booking_location_lat' => 'location_latitude',
					'_yith_booking_location_lng' => 'location_longitude',
				),
				'internal_meta_keys' => array(
					'_yith_booking_location',
					'_yith_booking_location_lat',
					'_yith_booking_location_lng',
				),
			);
		}

		/**
		 * Handle updated props.
		 *
		 * @param WC_Product_Booking $product       The booking product.
		 * @param array              $updated_props The updated props.
		 */
		public function handle_product_updated_props( WC_Product_Booking $product, array $updated_props ) {
			if (
				in_array( 'location', $updated_props, true ) ||
				( $product->get_location( 'edit' ) && ( ! $product->get_location_latitude( 'edit' ) || ! $product->get_location_longitude( 'edit' ) ) )
			) {
				$location  = $product->get_location( 'edit' );
				$latitude  = '';
				$longitude = '';
				if ( $location && yith_wcbk()->maps() ) {
					$coordinates = yith_wcbk()->maps()->get_location_by_address( $location );
					if ( isset( $coordinates['lat'] ) && isset( $coordinates['lng'] ) ) {
						$latitude  = $coordinates['lat'];
						$longitude = $coordinates['lng'];
					}
				}

				update_post_meta( $product->get_id(), '_yith_booking_location_lat', $latitude );
				update_post_meta( $product->get_id(), '_yith_booking_location_lng', $longitude );
				$product->set_location_latitude( $latitude );
				$product->set_location_longitude( $longitude );
			}
		}

		/**
		 * Save booking product meta for resources.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing

			$product->set_props(
				array(
					'location'           => isset( $_POST['_yith_booking_location'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_location'] ) ) : null,
					'location_latitude'  => isset( $_POST['_yith_booking_location_lat'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_location_lat'] ) ) : null,
					'location_longitude' => isset( $_POST['_yith_booking_location_lng'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_location_lng'] ) ) : null,
				)
			);

			// phpcs:enable
		}

		/**
		 * Add product settings fields.
		 *
		 * @param WC_Product_Booking|false $product The booking product.
		 */
		public function print_google_maps_section_in_product_settings( $product ) {
			yith_wcbk_get_module_view( 'google-maps', 'product-tabs/google-maps.php', array( 'booking_product' => $product ) );
		}
	}
}
