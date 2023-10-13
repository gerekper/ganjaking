<?php
/**
 * Availability Rules options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$the_tab = array(
	'configuration-availability-rules' => array(
		'availability-rules-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcbk_print_global_availability_rules_tab',
		),
	),
);

return apply_filters( 'yith_wcbk_panel_availability_rules_options', $the_tab );
