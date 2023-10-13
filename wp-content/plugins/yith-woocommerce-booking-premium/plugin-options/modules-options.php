<?php
/**
 * Add-ons Modules options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'modules' => array(
		'modules-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcbk_print_modules_tab',
		),
	),
);
