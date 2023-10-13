<?php
/**
 * Class YITH_WCBK_People_Bookings
 * Handle booking for the People module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_People_Bookings' ) ) {
	/**
	 * YITH_WCBK_People_Bookings class.
	 */
	class YITH_WCBK_People_Bookings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_People_Bookings constructor.
		 */
		protected function __construct() {
			YITH_WCBK_People_Booking_Data_Extension::get_instance();

			// Booking data meta-box.
			add_action( 'yith_wcbk_booking_metabox_info_after_third_column', array( $this, 'show_people_in_edit_booking' ), 10, 1 );

			// Booking data.
			add_action( 'yith_wcbk_booking_get_booking_data_to_display', array( $this, 'filter_booking_data_to_display' ), 10, 4 );
		}

		/**
		 * Show people in edit-booking page.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function show_people_in_edit_booking( YITH_WCBK_Booking $booking ) {
			if ( $booking->has_persons() ) {
				yith_wcbk_get_module_view( 'people', 'meta-boxes/booking-people.php', compact( 'booking' ) );
			}
		}

		/**
		 * Filter booking data to display, to add people.
		 *
		 * @param array             $data    The data.
		 * @param string            $context The context (admin or frontend).
		 * @param array             $args    Arguments.
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_to_display( array $data, string $context, array $args, YITH_WCBK_Booking $booking ): array {
			$is_frontend = 'frontend' === $context;

			if ( $booking->has_persons() ) {
				$value = $booking->get_persons();
				if ( $booking->has_person_types() ) {
					$value .= ' (' . $booking->get_person_types_html( '{title}: {number}', ', ' ) . ')';
				}

				$data['people'] = array(
					'label'    => $is_frontend ? yith_wcbk_get_label( 'people' ) : __( 'People', 'yith-booking-for-woocommerce' ),
					'display'  => $value,
					'priority' => 80,
				);
			}

			return $data;
		}
	}
}
