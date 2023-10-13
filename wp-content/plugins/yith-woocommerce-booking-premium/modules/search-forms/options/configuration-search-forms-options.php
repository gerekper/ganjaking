<?php
/**
 * All Bookings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'configuration-search-forms' => array(
		'configuration-search-forms-list' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBK_Post_Types::SEARCH_FORM,
			'wp-list-style' => 'boxed',
		),
	),
);

