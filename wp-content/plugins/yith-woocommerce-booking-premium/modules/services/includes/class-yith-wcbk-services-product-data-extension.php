<?php
/**
 * Class YITH_WCBK_Services_Product_Data_Extension
 * Handle product data for the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Services_Product_Data_Extension class.
	 */
	class YITH_WCBK_Services_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'tabs' => array(
					'services' => array(
						'id'     => 'yith_booking_services_tab',
						'wc_key' => 'yith_booking_services',
						'tab'    =>
							array(
								'label'    => _x( 'Services', 'Product tab title', 'yith-booking-for-woocommerce' ),
								'target'   => 'yith_booking_services_tab',
								'priority' => 50,
							),
						'module' => 'services',
					),
				),
			);
		}

		/**
		 * Save booking product meta.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$product->set_service_ids( wc_clean( wp_unslash( $_POST['_yith_booking_services'] ?? array() ) ) );
			// phpcs:enable
		}

		/**
		 * Update product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 * @param bool               $force   Force flag.
		 *
		 * @return array Array of updated props.
		 */
		protected function update_product_extra_data( WC_Product_Booking $product, bool $force ): array {
			$updated_props = array();
			$changes       = $product->get_changes();

			if ( $force || array_key_exists( 'service_ids', $changes ) ) {
				$results = wp_set_post_terms( $product->get_id(), $product->get_service_ids( 'edit' ), YITH_WCBK_Post_Types::SERVICE_TAX, false );

				if ( ! is_wp_error( $results ) && ! ! $results ) {
					$updated_props[] = 'service_ids';
				}
			}

			return $updated_props;
		}

		/**
		 * Read product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		protected function read_product_extra_data( WC_Product_Booking $product ) {
			$terms    = get_the_terms( $product->get_id(), YITH_WCBK_Post_Types::SERVICE_TAX );
			$term_ids = false === $terms || is_wp_error( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );

			$product->set_service_ids( $term_ids );
		}
	}
}
