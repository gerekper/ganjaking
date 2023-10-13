<?php
/**
 * Services Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_get_booking_service' ) ) {
	/**
	 * Get the booking service
	 *
	 * @param int|YITH_WCBK_Service $service Service ID or object.
	 * @param WP_Term               $term    The term.
	 *
	 * @return YITH_WCBK_Service|false
	 * @deprecated 4.0.0
	 */
	function yith_get_booking_service( $service, $term = null ) {
		yith_wcbk_deprecated_function( 'yith_get_booking_service', '4.0.0', 'yith_wcbk_get_service' );

		return yith_wcbk_is_services_module_active() ? yith_wcbk_get_service( $service ) : false;
	}
}
