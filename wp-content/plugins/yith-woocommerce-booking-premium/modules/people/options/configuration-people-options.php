<?php
/**
 * All Bookings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'configuration-people' => array(
		'configuration-people-list' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBK_Post_Types::PERSON_TYPE,
			'wp-list-style' => 'classic',
		),
	),
);

