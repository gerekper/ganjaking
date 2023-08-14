<?php

namespace SearchWP\Debug;

use SearchWP\Query;
use SearchWP\Settings;

/**
 * Watcher collects SearchWP Debug data.
 *
 * @since 4.2.9
 */
class Watcher {

	/**
	 * Storage for queries that happened during this request.
	 *
	 * @since 4.2.9
     *
	 * @var Query[]
	 */
	private $queries = [];

	/**
	 * Storage for the log entries recorded during this request.
	 *
	 * @since 4.2.9
	 *
	 * @var array
	 */
	private $logs = [];

	/**
	 * Init.
	 *
	 * @since 4.2.9
	 */
	public function init() {

		if ( self::is_enabled() ) {
			$this->hooks();
		}
	}

	/**
	 * Hooks.
	 *
	 * @since 4.2.9
	 */
	public function hooks() {

		add_action( 'searchwp\query\ran', function( $query ) {
			$this->queries[ $query->get_id() ] = $query;
		} );

		add_action( 'searchwp\debug\log', function( $log ) {
			$this->logs[] = $log;
		}, 1, 2 );
	}

	/**
	 * Check if current user can run the Watcher.
	 *
	 * @since 4.2.9
	 */
	public static function is_enabled() {

		if ( ! apply_filters( 'searchwp\debug', Settings::get( 'debug', 'boolean' ) ) ) {
			return false;
		}

		if ( ! apply_filters( 'searchwp\debug\watcher', true ) ) {
			return false;
		}

		if ( ! current_user_can( Settings::get_capability() ) ) {
			return false;
		}

		return true;
	}

	/**
     * Getter for queries.
     *
     * @since 4.2.9
     *
	 * @return Query[]
	 */
	public function get_queries() {

		return $this->queries;
	}

	/**
     * Getter for logs.
	 *
     * @since 4.2.9
     *
	 * @return array
	 */
	public function get_logs() {

		return $this->logs;
	}
}
