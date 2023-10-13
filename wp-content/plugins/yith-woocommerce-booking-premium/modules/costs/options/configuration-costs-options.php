<?php
/**
 * All Bookings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'configuration-costs' => array(
		'configuration-costs-list' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBK_Post_Types::EXTRA_COST,
			'wp-list-style' => 'classic',
		),
	),
);

