<?php
/**
 * Class YITH_WCBK_Services_Bookings
 * Handle booking for the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Bookings' ) ) {
	/**
	 * YITH_WCBK_Services_Bookings class.
	 */
	class YITH_WCBK_Services_Bookings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Services_Bookings constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Services_Booking_Data_Extension::get_instance();

			// Booking data meta-box.
			add_action( 'yith_wcbk_booking_metabox_info_after_third_column', array( $this, 'show_services_in_edit_booking' ), 20, 1 );

			// Booking data.
			add_action( 'yith_wcbk_booking_get_booking_data_to_display', array( $this, 'filter_booking_data_to_display' ), 10, 4 );
		}

		/**
		 * Show services in edit-booking page.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function show_services_in_edit_booking( YITH_WCBK_Booking $booking ) {
			if ( $booking->get_service_ids( 'edit' ) ) {
				yith_wcbk_get_module_view( 'services', 'meta-boxes/booking-services.php', compact( 'booking' ) );
			}
		}

		/**
		 * Filter booking data to display, to add services.
		 *
		 * @param array             $data    The data.
		 * @param string            $context The context (admin or frontend).
		 * @param array             $args    Arguments.
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_to_display( array $data, string $context, array $args, YITH_WCBK_Booking $booking ): array {
			$is_admin       = 'admin' === $context;
			$is_frontend    = 'frontend' === $context;
			$split_services = $args['split_services'] ?? false;

			if ( $split_services ) {
				$additional_services = $booking->get_service_names( $is_admin, 'additional' );
				$included_services   = $booking->get_service_names( $is_admin, 'included' );
				if ( $additional_services ) {
					$data['additional-services'] = array(
						'label'    => $is_frontend ? yith_wcbk_get_label( 'additional-services' ) : __( 'Additional Services', 'yith-booking-for-woocommerce' ),
						'display'  => yith_wcbk_booking_services_html( $additional_services ),
						'priority' => 90,
					);
				}
				if ( $included_services ) {
					$data['included-services'] = array(
						'label'    => $is_frontend ? yith_wcbk_get_label( 'included-services' ) : __( 'Included Services', 'yith-booking-for-woocommerce' ),
						'display'  => yith_wcbk_booking_services_html( $included_services ),
						'priority' => 90,
					);
				}
			} else {
				$service_names = $booking->get_service_names( $is_admin );
				if ( $service_names ) {
					$data['services'] = array(
						'label'    => $is_frontend ? yith_wcbk_get_label( 'services' ) : __( 'Services', 'yith-booking-for-woocommerce' ),
						'display'  => yith_wcbk_booking_services_html( $service_names ),
						'priority' => 90,
					);
				}
			}

			return $data;
		}
	}
}
