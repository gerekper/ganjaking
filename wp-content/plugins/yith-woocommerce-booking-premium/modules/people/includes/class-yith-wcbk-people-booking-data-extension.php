<?php
/**
 * Class YITH_WCBK_People_Booking_Data_Extension
 * Handle booking data for the People module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_People_Booking_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Resources_People_Data_Extension class.
	 */
	class YITH_WCBK_People_Booking_Data_Extension extends YITH_WCBK_Booking_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'csv_fields'         => array(
					'persons',
					'person_types',
				),
				'meta_keys_to_props' => array(
					'_persons'      => 'persons',
					'_person_types' => 'person_types',
					'_has_persons'  => 'has_persons',
				),
				'internal_meta_keys' => array(
					'_persons',
					'_person_types',
					'_has_persons',
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
			if ( $booking->has_person_types() ) {
				if ( ! empty( $_POST['yith_booking_person_type'] ) ) {
					$person_types      = $booking->get_person_types( 'edit' );
					$post_person_types = wc_clean( wp_unslash( $_POST['yith_booking_person_type'] ) );

					foreach ( $person_types as $key => $person_type ) {
						$person_type_id = $person_type['id'];
						if ( isset( $post_person_types[ $person_type_id ] ) ) {
							$person_types[ $key ]['number'] = absint( $post_person_types[ $person_type_id ] );
						}
					}
					$booking->set_person_types( $person_types );
				}
			} else {
				if ( isset( $_POST['yith_booking_persons'] ) ) {
					$persons = absint( $_POST['yith_booking_persons'] );
					$booking->set_persons( $persons );
				}
			}
			// phpcs:enable
		}

		/**
		 * Triggered before updating props.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		protected function before_updating_props( YITH_WCBK_Booking $booking ) {
			$changes = $booking->get_changes();

			if ( array_intersect( array( 'person_types' ), array_keys( $changes ) ) ) {
				if ( $booking->has_person_types() ) {
					$persons = array_reduce(
						wp_list_pluck( $booking->get_person_types( 'edit' ), 'number' ),
						function ( $acc, $number ) {
							return $acc + absint( $number );
						},
						0
					);
					$booking->set_persons( $persons );
				};
			}
		}

		/**
		 * Triggered before updating props.
		 *
		 * @param mixed             $value   The value.
		 * @param string            $prop    The prop.
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return mixed The sanitized value.
		 */
		protected function sanitize_prop_value_before_saving( $value, string $prop, YITH_WCBK_Booking $booking ) {
			switch ( $prop ) {
				case 'has_persons':
					$value = wc_bool_to_string( $value );
					break;
			}

			return $value;
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
			switch ( $field ) {
				case 'persons':
					$value = $booking->has_persons() ? $booking->get_persons() : '';
					break;
				case 'person_types':
					$value = '';
					if ( $booking->has_person_types() ) {
						foreach ( $booking->get_person_types() as $person_type ) {
							$id     = $person_type['id'] ?? false;
							$title  = ! ! ( $person_type['title'] ) ? $person_type['title'] : false;
							$number = $person_type['number'] ?? false;

							if ( false === $id || false === $title || ! $number ) {
								continue;
							}

							$person_type_title = get_the_title( $id );
							$title             = ! ! $person_type_title ? $person_type_title : $title;

							$value .= "$title($number) ";
						}
					}
					break;
			}

			return $value;
		}
	}
}
