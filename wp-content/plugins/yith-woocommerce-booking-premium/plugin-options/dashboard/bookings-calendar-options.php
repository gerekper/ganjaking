<?php
/**
 * Bookings Calendar options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'dashboard-bookings-calendar' => array(
		'dashboard-bookings-calendar-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcbk_panel_render_calendar_page',
		),
	),
);
