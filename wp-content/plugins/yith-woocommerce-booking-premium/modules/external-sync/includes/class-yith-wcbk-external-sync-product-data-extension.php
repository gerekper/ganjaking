<?php
/**
 * Class YITH_WCBK_External_Sync_Product_Data_Extension
 * Handle product data for the External Sync module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\ExternalSync
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_External_Sync_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_External_Sync_Product_Data_Extension class.
	 */
	class YITH_WCBK_External_Sync_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					'_yith_booking_external_calendars'           => 'external_calendars',
					'_yith_booking_external_calendars_key'       => 'external_calendars_key',
					'_yith_booking_external_calendars_last_sync' => 'external_calendars_last_sync',
				),
				'internal_meta_keys' => array(
					'_yith_booking_external_calendars',
					'_yith_booking_external_calendars_key',
					'_yith_booking_external_calendars_last_sync',
				),
				'tabs'               => array(
					'sync' => array(
						'id'     => 'yith_booking_sync_tab',
						'wc_key' => 'yith_booking_sync',
						'tab'    => array(
							'label'    => _x( 'Sync', 'Product tab title', 'yith-booking-for-woocommerce' ),
							'target'   => 'yith_booking_sync_tab',
							'priority' => 90,
						),
						'module' => 'external-sync',
					),
				),
			);
		}

		/**
		 * Save booking product meta for people.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$calendars = isset( $_POST['_yith_booking_external_calendars'] ) ? wp_unslash( $_POST['_yith_booking_external_calendars'] ) : array();
			foreach ( $calendars as $key => $calendar ) {
				$calendars[ $key ] = array_merge(
					wc_clean( $calendar ),
					array(
						'url' => esc_url_raw( $calendar['url'] ?? '' ),
					)
				);
			}

			$product->set_props(
				array(
					'external_calendars'           => $calendars,
					'external_calendars_key'       => isset( $_POST['_yith_booking_external_calendars_key'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_external_calendars_key'] ) ) : null,
					'external_calendars_last_sync' => isset( $_POST['_yith_booking_external_calendars_last_sync'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_external_calendars_last_sync'] ) ) : null,
				)
			);

			// phpcs:enable
		}

		/**
		 * Handle updated props.
		 *
		 * @param WC_Product_Booking $product       The booking product.
		 * @param array              $updated_props The updated props.
		 */
		public function handle_product_updated_props( WC_Product_Booking $product, array $updated_props ) {
			if ( in_array( 'external_calendars', $updated_props, true ) ) {
				yith_wcbk_booking_externals()->delete_externals_from_product_id( $product->get_id() );
				yith_wcbk_product_delete_external_calendars_last_sync( $product );
			}
		}
	}
}
