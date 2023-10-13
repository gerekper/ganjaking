<?php
/**
 * Vendor Calendar options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'vendor-calendar' => array(
		'vendor-calendar-tab' => array(
			'type'           => 'custom_tab',
			'action'         => 'yith_wcbk_panel_render_calendar_page',
			'show_container' => true,
		),
	),
);
