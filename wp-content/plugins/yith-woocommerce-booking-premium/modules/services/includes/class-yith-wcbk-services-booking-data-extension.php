<?php
/**
 * Class YITH_WCBK_Services_Booking_Data_Extension
 * Handle booking data for the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Booking_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Services_Booking_Data_Extension class.
	 */
	class YITH_WCBK_Services_Booking_Data_Extension extends YITH_WCBK_Booking_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'csv_fields'         => array(
					'services',
				),
				'meta_keys_to_props' => array(
					'_service_quantities' => 'service_quantities',
				),
				'internal_meta_keys' => array(
					'_service_quantities',
				),
			);
		}

		/**
		 * Save booking meta.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function set_meta_before_saving( YITH_WCBK_Booking $booking ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['yith_booking_service_quantities'] ) ) {
				$service_quantities = wc_clean( wp_unslash( $_POST['yith_booking_service_quantities'] ) );
				$booking->set_service_quantities( $service_quantities );
			}
			// phpcs:enable
		}

		/**
		 * Update extra data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 * @param bool              $force   Force flag.
		 *
		 * @return array Array of updated props.
		 */
		protected function update_extra_data( YITH_WCBK_Booking $booking, bool $force ): array {
			$updated_props = array();
			$changes       = $booking->get_changes();

			if ( $force || array_key_exists( 'service_ids', $changes ) ) {
				$results = wp_set_post_terms( $booking->get_id(), $booking->get_service_ids( 'edit' ), YITH_WCBK_Post_Types::SERVICE_TAX, false );

				if ( ! is_wp_error( $results ) && ! ! $results ) {
					$updated_props[] = 'service_ids';
				}
			}

			return $updated_props;
		}

		/**
		 * Read extra data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		protected function read_extra_data( YITH_WCBK_Booking $booking ) {
			$terms    = get_the_terms( $booking->get_id(), YITH_WCBK_Post_Types::SERVICE_TAX );
			$term_ids = false === $terms || is_wp_error( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );

			$booking->set_service_ids( $term_ids );
		}

		/**
		 * Filter CSV field value.
		 *
		 * @param string            $value   The value to filter.
		 * @param string            $field   The field.
		 * @param YITH_WCBK_Booking $booking The booking product.
		 *
		 * @return string
		 */
		public function filter_csv_field_value( string $value, string $field, YITH_WCBK_Booking $booking ): string {
			if ( 'services' === $field ) {
				$services = $booking->get_service_names();

				$value = ! ! $services ? implode( ', ', $services ) : '';
			}

			return $value;
		}
	}
}
