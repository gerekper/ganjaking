<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Points Log Class
 *
 * Access class for the Points Log
 *
 * @since 1.0
 */
class WC_Points_Rewards_Points_Log {

	/** @var int count of rows found from the query function */
	public static $found_rows = 0;


	/**
	 * Adds an entry to the points log table
	 *
	 * @since 1.0
	 * @param array $args the log entry arguments:
	 * + `user_id`        - int required customer identifier
	 * + `points`         - int required the points change, ie 10, -75, etc
	 * + `event_type`     - string required the event type slug
	 * + `user_points_id` - int optional user_points identifier, if this log entry is associated with a user points record
	 * + `order_id`       - int optional order identifier, if this log entry is associated with an order
	 * + `data`           - mixed optional data to associate with this event
	 * + `timestamp`      - string optional event timestamp in mysql format.  Defaults to the current time.
	 * @return boolean true if the record is created, false otherwise
	 */
	public static function add_log_entry( $args ) {

		global $wc_points_rewards, $wpdb;

		// required data column/value
		$data = array(
			'user_id'     => $args['user_id'],
			'points'      => $args['points'],
			'type'        => $args['event_type'],
			'date'        => isset( $args['timestamp'] ) && $args['timestamp'] ? $args['timestamp'] : current_time( 'mysql', 1 ),
		);

		// required data format
		$format = array(
			'%d',
			'%d',
			'%s',
			'%s',
		);

		// optional parameter: associated user points record
		if ( isset( $args['user_points_id'] ) && $args['user_points_id'] ) {
			$data['user_points_id'] = $args['user_points_id'];
			$format[] = '%d';
		}

		// optional parameter: associated order record
		if ( isset( $args['order_id'] ) && $args['order_id'] ) {
			$data['order_id'] = $args['order_id'];
			$format[] = '%d';
		}

		// optional parameter: associated arbitrary data
		if ( isset( $args['data'] ) && $args['data'] ) {
			$data['data'] = serialize( $args['data'] );
			$format[] = '%s';
		}

		// automatically associate this log entry with an admin user if in the admin
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$admin_user = wp_get_current_user();

			$data['admin_user_id'] = $admin_user->ID;
			$format[] = '%d';
		}

		// create the record
		return $wpdb->insert( $wc_points_rewards->user_points_log_db_tablename,
			$data,
			$format
		);
	}


	/**
	 * Gets point log entries based on $args
	 *
	 * @since 1.0
	 * @param array $args the query arguments
	 * @return array of log entry objects
	 */
	public static function get_points_log_entries( $args ) {

		global $wc_points_rewards, $wpdb;

		// special handling for searching by user
		if ( ! empty( $args['user'] ) ) {
			$args['where'][] = $wpdb->prepare( "{$wc_points_rewards->user_points_log_db_tablename}.user_id = %s", $args['user'] );
		}

		// special handling for searching by event type
		if ( ! empty( $args['event_type'] ) ) {
			$args['where'][] = $wpdb->prepare( "{$wc_points_rewards->user_points_log_db_tablename}.type = %s", $args['event_type'] );
		}

		$entries = array();

		foreach ( self::query( $args ) as $log_entry ) {

			// maybe unserialize the arbitrary data object
			$log_entry->data = maybe_unserialize( $log_entry->data );

			// Format the event date as "15 minutes ago" if the event took place in the last 24 hours, otherwise just show the date (timestamp on mouse hover)
			$timestamp = strtotime( $log_entry->date );
			$t_time = date_i18n( 'Y/m/d g:i:s A', $timestamp );
			$time_diff = current_time( 'timestamp', true ) - $timestamp;

			if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
				$h_time = sprintf( __( '%s ago', 'woocommerce-points-and-rewards' ), human_time_diff( $timestamp, current_time( 'timestamp', true ) ) );
			} else {
				$h_time = date_i18n( wc_date_format(), $timestamp );
			}

			$log_entry->date_display_human = $h_time;
			$log_entry->date_display       = $t_time;

			// retrieve the description
			$log_entry->description        = WC_Points_Rewards_Manager::event_type_description( $log_entry->type, $log_entry );

			$entries[] = $log_entry;
		}

		return $entries;
	}


	/**
	 * Returns all event types and their counts
	 *
	 * @since 1.0
	 * @return array of event types
	 */
	public static function get_event_types() {

		global $wc_points_rewards, $wpdb;

		$query = "SELECT type, COUNT(*) as count FROM {$wc_points_rewards->user_points_log_db_tablename} GROUP BY type ORDER BY type";

		$results = $wpdb->get_results( $query );

		// make a "human readable" name of the event type slug
		if ( is_array( $results ) ) {
			foreach ( $results as &$row ) {
				$row->name = ucwords( str_replace( '-', ' ', $row->type ) );
			}
		}

		return $results ? $results : array();
	}


	/**
	 * Query for point log entries based on $args
	 *
	 * @since 1.0
	 * @param array $args the query arguments
	 * @return array of log entry objects
	 */
	public static function query( $args ) {

		global $wc_points_rewards, $wpdb;

		// calculate found rows? (costly, but needed for pagination)
		$found_rows = '';
		if ( isset( $args['calc_found_rows'] ) && $args['calc_found_rows'] ) {
			$found_rows = 'SQL_CALC_FOUND_ROWS';
		}

		// distinct results?
		$distinct = '';
		if ( isset( $args['distinct'] ) && $args['distinct'] ) {
			$distinct = 'DISTINCT';
		}

		// returned fields
		$fields = "{$wc_points_rewards->user_points_log_db_tablename}.*";
		if ( ! empty( $args['fields'] ) && is_array( $args['fields'] ) ) {
			$fields .= ', ' . implode( ', ', $args['fields'] );
		}

		// joins
		$join = '';
		if ( ! empty( $args['join'] ) && is_array( $args['join'] ) ) {
			$join = implode( ' ', $args['join'] );
		}

		// where clauses
		$where = '';
		if ( ! empty( $args['where'] ) ) {
			$where = 'AND ' . implode( ' AND ', $args['where'] );
		}

		// group by
		$groupby = '';
		if ( ! empty( $args['groupby'] ) && is_array( $args['groupby'] ) ) {
			$groupby = implode( ', ', $args['groupby'] );
		}

		// order by
		$orderby = '';
		if ( ! empty( $args['orderby'] ) ) {

			// convert "really simple" format of simply a string
			if ( is_string( $args['orderby'] ) ) {
				$args['orderby'] = array( array( 'field' => $args['orderby'] ) );
			}

			// check if the 'simple' format is being used with a single column to order by
			list( $key ) = array_keys( $args['orderby'] );
			if ( is_string( $key ) ) {
				$args['orderby'] = array( $args['orderby'] );
			}

			foreach ( $args['orderby'] as $_orderby ) {
				if ( isset( $_orderby['field'] ) ) {
					$orderby .= empty( $orderby ) ? $_orderby['field'] : ', ' . $_orderby['field'];
					if ( isset( $_orderby['order'] ) ) {
						$orderby .= ' ' . $_orderby['order'];
					}
				}
			}

			if ( $orderby ) {
				$orderby = 'ORDER BY ' . $orderby;
			}
		}

		// query limits
		$limits = '';
		// allow a page and per_page to be provided, or simply per_page to limit to that number of results (defaults 'paged' to '1')
		if ( ! empty( $args['per_page'] ) && empty( $args['paged'] ) ) {
			$args['paged'] = 1;
		}

		if ( ! empty( $args['per_page'] ) ) {
			$limits = ' LIMIT ' . ( ( $args['paged'] - 1 ) * $args['per_page'] ) . ', ' . $args['per_page'];
		}

		// build the query
		$query = "SELECT $found_rows $distinct $fields FROM {$wc_points_rewards->user_points_log_db_tablename} $join WHERE 1=1 $where $groupby $orderby $limits";

		$results = $wpdb->get_results( $query );

		// record the found rows
		if ( isset( $args['calc_found_rows'] ) && $args['calc_found_rows'] ) {
			self::$found_rows = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		}

		$results = $results ? $results : array();

		return $results;
	}
}
