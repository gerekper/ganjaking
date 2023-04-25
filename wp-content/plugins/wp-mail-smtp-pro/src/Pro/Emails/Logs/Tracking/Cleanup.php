<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking;

use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Events;
use WPMailSMTP\WP;

/**
 * Email tracking Cleanup Class.
 *
 * @since 3.8.0
 */
class Cleanup {

	/**
	 * Cleanup orphaned tracking events.
	 *
	 * @since 3.8.0
	 *
	 * @return bool Whether or not all orphaned tracking events are deleted.
	 */
	public function cleanup_tracking_events() {

		return $this->cleanup_tracking( Tracking::get_events_table_name() );
	}

	/**
	 * Cleanup orphaned tracking links.
	 *
	 * @since 3.8.0
	 *
	 * @return bool Whether or not all orphaned tracking links are deleted.
	 */
	public function cleanup_tracking_links() {

		return $this->cleanup_tracking( Tracking::get_links_table_name() );
	}

	/**
	 * Cleanup orphaned tracking events.
	 *
	 * @since 3.8.0
	 *
	 * @param string $tracking_table Tracking table to cleanup orphaned data.
	 *
	 * @return bool Returns `true` if all orphaned tracking data is removed or
	 *              if tracking DB is not valid.
	 *              Otherwise, returns `false`.
	 */
	private function cleanup_tracking( $tracking_table ) {

		if ( ! ( new Events() )->is_valid_db() ) {
			return true;
		}

		$wpdb       = WP::wpdb();
		$logs_table = Logs::get_table_name();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		$wpdb->query(
			$wpdb->prepare(
				'DELETE tracking
				FROM `%1$s` tracking
					LEFT JOIN `%2$s` email_logs
						ON tracking.email_log_id = email_logs.id
				WHERE email_logs.id IS NULL',
				$tracking_table,
				$logs_table
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder

		// Check if there are still tracking data to be removed.
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		$count_query = $wpdb->prepare(
			'SELECT COUNT(*)
			FROM `%1$s` tracking
				LEFT JOIN `%2$s` email_logs
					ON tracking.email_log_id = email_logs.id
			WHERE email_logs.id IS NULL',
			$tracking_table,
			$logs_table
		);
		// phpcs:enable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$count_results = absint( $wpdb->get_var( $count_query ) );

		if ( empty( $count_results ) ) {
			return true;
		}

		return false;
	}
}
