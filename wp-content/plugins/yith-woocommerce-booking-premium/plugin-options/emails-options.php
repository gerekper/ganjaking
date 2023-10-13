<?php
/**
 * Emails tab.
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

return array(
	'emails' => array(
		'emails-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcbk_print_emails_tab',
		),
	),
);
