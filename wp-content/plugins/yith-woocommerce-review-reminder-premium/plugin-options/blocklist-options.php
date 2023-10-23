<?php
/**
 * Blocklist tab
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'blocklist' => array(
		'review_reminder_blocklist_unsubscribed_users' => array(
			'type'   => 'custom_tab',
			'action' => 'ywrr_blocklist',
		),
	),
);
