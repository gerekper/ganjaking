<?php
/**
 * Schedule tab
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'schedule' => array(
		'review_reminder_schedule_mail' => array(
			'type'   => 'custom_tab',
			'action' => 'ywrr_schedulelist',
		),
	),
);
