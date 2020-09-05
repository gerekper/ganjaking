<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Cron.
 *
 * Doing some cronjobs.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.3.0
 */
final class Cron_Model {

	/**
	 * Adds Hooks for WordPress.
	 *
	 * @since 2.3.0
	 */
	public static function add_cron_hooks() {

		add_filter( 'cron_schedules', [ '\wpbuddy\rich_snippets\pro\Cron_Model', 'add_cron_schedules' ] );

		add_action( 'wpbuddy/rich_snippets/cron/2weeks', [ '\wpbuddy\rich_snippets\pro\Cron_Model', 'run_2weeks_cron' ] );
	}


	/**
	 * Adds cronjobs to WordPress.
	 *
	 * @since 2.3.0
	 */
	public static function add_cron() {

		if ( ! wp_next_scheduled( 'wpbuddy/rich_snippets/cron/2weeks' ) ) {
			wp_schedule_event( time(), '2weeks', 'wpbuddy/rich_snippets/cron/2weeks' );
		}
	}


	/**
	 * Removes cronjobs from WordPress.
	 *
	 * @since 2.3.0
	 */
	public static function remove_cron() {

		wp_clear_scheduled_hook( 'wpbuddy/rich_snippets/cron/daily' );
	}


	/**
	 * Runs a cronjob every 2 weeks.
	 *
	 * @since 2.3.0
	 */
	public static function run_2weeks_cron() {

		Admin_Rating_Controller::check_user_rating();
		Upgrade_Model::jhztgj();
	}


	/**
	 * Adds options to the schedule list.
	 *
	 * @param array $schedule_list
	 *
	 * @return array
	 * @since 2.3.0
	 *
	 */
	public static function add_cron_schedules( $schedule_list ) {

		if ( isset( $schedule_list['2weeks'] ) ) {
			return $schedule_list;
		}

		$schedule_list['2weeks'] = array(
			'interval' => WEEK_IN_SECONDS * 2,
			'display'  => _x( 'Every two weeks', 'cron schedule interval', 'rich-snippets-schema' ),
		);

		return $schedule_list;
	}

}
