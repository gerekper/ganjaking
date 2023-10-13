<?php
/**
 * Class YITH_WCBK_Resources_Booking_Data_Extension
 * Handle booking data for the Resources module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resources_Booking_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Resources_Booking_Data_Extension class.
	 */
	class YITH_WCBK_Resources_Booking_Data_Extension extends YITH_WCBK_Booking_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'csv_fields' => array(
					'resources',
				),
			);
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
			global $wpdb;
			$updated_props = array();
			$changed_props = $booking->get_changes();
			$updated       = false;

			if ( $force || array_key_exists( 'resource_ids', $changed_props ) ) {
				$new_ids = $booking->get_resource_ids( 'edit' );
				$old_ids = $wpdb->get_col( $wpdb->prepare( "SELECT resource_id FROM $wpdb->yith_wcbk_booking_resources WHERE booking_id=%d", $booking->get_id() ) );
				$old_ids = array_map( 'absint', $old_ids );

				$to_add_ids    = array_diff( $new_ids, $old_ids );
				$to_remove_ids = array_map( 'absint', array_diff( $old_ids, $new_ids ) );

				if ( $to_remove_ids ) {
					$updated       = true;
					$to_remove_ids = "'" . implode( "', '", $to_remove_ids ) . "'";
					$wpdb->query(
						$wpdb->prepare(
							"DELETE FROM $wpdb->yith_wcbk_booking_resources WHERE booking_id = %d AND resource_id IN ($to_remove_ids)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$booking->get_id()
						)
					);
				}

				foreach ( $to_add_ids as $resource_id ) {
					$updated = true;
					$wpdb->insert(
						$wpdb->yith_wcbk_booking_resources,
						array(
							'booking_id'  => $booking->get_id(),
							'resource_id' => $resource_id,
						)
					);
				}
			}

			if ( $updated ) {
				$updated_props[] = 'resources_data';
			}

			return $updated_props;
		}

		/**
		 * Read extra data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		protected function read_extra_data( YITH_WCBK_Booking $booking ) {
			global $wpdb;
			$resource_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT resource_id FROM $wpdb->yith_wcbk_booking_resources WHERE booking_id=%d",
					$booking->get_id()
				)
			);

			$resource_ids = ! ! $resource_ids ? $resource_ids : array();

			$booking->set_resource_ids( $resource_ids );
		}

		/**
		 * Clear caches booking data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking product.
		 */
		public function clear_caches( YITH_WCBK_Booking $booking ) {
			$resource_ids = $booking->get_resource_ids( 'edit' );
			if ( $resource_ids ) {
				yith_wcbk_clear_resource_related_caches( $resource_ids );
			}
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
			if ( 'resources' === $field ) {
				$resource_ids = $booking->get_resource_ids();

				if ( $resource_ids ) {
					$resources = array_filter( array_map( 'yith_wcbk_get_resource', $resource_ids ) );
					$names     = array_filter(
						array_map(
							function ( YITH_WCBK_Resource $resource ) {
								return $resource->get_name();
							},
							$resources
						)
					);

					$value = ! ! $names ? implode( ', ', $names ) : '';
				}
			}

			return $value;
		}

		/**
		 * Handle booking delete.
		 *
		 * @param int $id The booking ID.
		 */
		protected function handle_booking_delete( int $id ) {
			global $wpdb;

			$resource_ids = $wpdb->get_col( $wpdb->prepare( "SELECT resource_id FROM $wpdb->yith_wcbk_booking_resources WHERE booking_id=%d", $id ) );

			$wpdb->delete(
				$wpdb->yith_wcbk_booking_resources,
				array(
					'booking_id' => $id,
				)
			);

			if ( $resource_ids ) {
				yith_wcbk_clear_resource_related_caches( $resource_ids );
			}
		}
	}
}
