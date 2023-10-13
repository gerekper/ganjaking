<?php
/**
 * Vendor - All Bookings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'vendor-all-bookings' => array(
		'vendor-all-bookings-list' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBK_Post_Types::BOOKING,
			'wp-list-style' => 'classic',
		),
	),
);

