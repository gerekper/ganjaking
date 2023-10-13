<?php
/**
 * All Bookings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'vendor-services' => array(
		'vendor-services-list' => array(
			'type'          => 'taxonomy',
			'taxonomy'      => YITH_WCBK_Post_Types::SERVICE_TAX,
			'wp-list-style' => 'classic',
		),
	),
);

