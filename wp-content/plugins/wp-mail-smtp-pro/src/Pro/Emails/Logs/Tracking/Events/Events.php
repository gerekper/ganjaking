<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events;

use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;

/**
 * Email events class.
 *
 * @since 2.9.0
 */
class Events {

	/**
	 * Whether the events DB tables exist.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_valid_db() {

		global $wpdb;

		static $is_valid = null;

		// Return cached value only if tables already exists.
		if ( $is_valid === true ) {
			return true;
		}

		$events_table = Tracking::get_events_table_name();
		$links_table  = Tracking::get_links_table_name();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$events_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $events_table ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$links_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s;', $links_table ) );

		$is_valid = $events_exists && $links_exists;

		return $is_valid;
	}

	/**
	 * Delete events connected to the provided email log id list.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email_log_ids A string of Email Log IDs to be deleted.
	 */
	public function delete_events( $email_log_ids ) {

		global $wpdb;

		if ( ! $this->is_valid_db() ) {
			return;
		}

		$log_ids_array = explode( ',', (string) $email_log_ids );
		$log_ids_array = array_map( 'intval', $log_ids_array );
		$placeholders  = implode( ', ', array_fill( 0, count( $log_ids_array ), '%d' ) );

		// Delete the DB rows of events connected to the provided email log ids list.
		$events_db_table = Tracking::get_events_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$events_db_table} WHERE email_log_id IN ( {$placeholders} )",
				$log_ids_array
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare

		// Delete the DB rows of links connected to the provided email log ids list.
		$links_db_table = Tracking::get_links_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$links_db_table} WHERE email_log_id IN ( {$placeholders} )",
				$log_ids_array
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
	}

	/**
	 * Delete all events from the DB.
	 *
	 * @since 2.9.0
	 */
	public function delete_all_events() {

		global $wpdb;

		// Truncate events DB tables.
		if ( $this->is_valid_db() ) {
			$events_db_table = Tracking::get_events_table_name();
			$links_db_table  = Tracking::get_links_table_name();

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "TRUNCATE TABLE `$events_db_table`;" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "TRUNCATE TABLE `$links_db_table`;" );
		}
	}
}
