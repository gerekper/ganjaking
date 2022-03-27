<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Events as TrackingEvents;
use WPMailSMTP\WP;

/**
 * Class Collection
 *
 * @since 1.5.0
 */
class EmailsCollection implements \Countable, \Iterator {

	/**
	 * @since 1.5.0
	 *
	 * @var int Default number of log entries per page.
	 */
	const PER_PAGE = 10;

	/**
	 * @since 1.7.0
	 *
	 * @var array List of available email log statuses.
	 */
	const STATUSES = [
		Email::STATUS_DELIVERED,
		Email::STATUS_WAITING,
		Email::STATUS_SENT,
		Email::STATUS_UNSENT,
	];

	/**
	 * @since 1.7.0
	 *
	 * @var array List of searchable content columns.
	 */
	const SEARCHABLE = [
		'people',
		'headers',
		'content',
	];

	/**
	 * @since 1.7.0
	 *
	 * @var array List of sortable columns.
	 */
	CONST SORTABLE = array(
		'date_sent',
		'subject',
		'status',
	);

	/**
	 * @since 1.9.0
	 *
	 * @var int Number of log entries per page.
	 */
	public static $per_page;

	/**
	 * @since 1.5.0
	 *
	 * @var array List of all Email instances.
	 */
	private $list = array();

	/**
	 * @since 1.5.0
	 *
	 * @var array List of current collection instance parameters.
	 */
	private $params;

	/**
	 * @since 1.5.0
	 *
	 * @var int Used for \Iterator when iterating through Queue in loops.
	 */
	private $iterator_position = 0;

	/**
	 * Collection constructor.
	 *      $emails = new EmailsCollection( [ 'status' => true ] )
	 *
	 * @since 1.5.0
	 *
	 * @param array $params
	 */
	public function __construct( array $params = array() ) {

		$this->set_per_page();
		$this->params = $this->process_params( $params );
	}

	/**
	 * Set the per page attribute to the screen options value.
	 *
	 * @since 1.9.0
	 */
	protected function set_per_page() {

		$per_page = (int) get_user_meta(
			get_current_user_id(),
			'wp_mail_smtp_log_entries_per_page',
			true
		);

		if ( $per_page < 1 ) {
			$per_page = self::PER_PAGE;
		}

		self::$per_page = $per_page;
	}

	/**
	 * Verify, sanitize, and populate with default values
	 * all the passed parameters, which participate in DB queries.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search processing.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function process_params( $params ) {

		$params    = (array) $params;
		$processed = [];

		/*
		 * WHERE.
		 */
		// Single ID.
		if ( ! empty( $params['id'] ) ) {
			$processed['id'] = intval( $params['id'] );
		}

		// Single message ID.
		if ( ! empty( $params['message_id'] ) ) {
			$processed['message_id'] = sanitize_text_field( $params['message_id'] );
		}

		// Multiple IDs.
		if (
			! empty( $params['ids'] ) &&
			is_array( $params['ids'] )
		) {
			$processed['ids'] = array_unique( array_filter( array_map( 'intval', array_values( $params['ids'] ) ) ) );
		}

		// Status.
		if (
			isset( $params['status'] ) &&
			in_array( $params['status'], self::STATUSES, true )
		) {
			$processed['status'] = intval( $params['status'] );
		}

		// Search.
		if (
			! empty( $params['search']['place'] ) &&
			is_string( $params['search']['place'] ) &&
			in_array( strtolower( $params['search']['place'] ), self::SEARCHABLE, true )
		) {
			$processed['search']['place'] = strtolower( sanitize_key( $params['search']['place'] ) );
		}

		if ( ! empty( $params['search']['term'] ) ) {
			$processed['search']['term'] = sanitize_text_field( wp_unslash( $params['search']['term'] ) );
		}

		/*
		 * LIMIT.
		 */
		if ( ! empty( $params['offset'] ) ) {
			$processed['offset'] = intval( $params['offset'] );
		}

		if ( ! empty( $params['per_page'] ) ) {
			$processed['per_page'] = intval( $params['per_page'] );
		}

		/*
		 * ORDER.
		 */
		if (
			! empty( $params['order'] ) &&
			is_string( $params['order'] ) &&
			in_array( strtoupper( $params['order'] ), [ 'ASC', 'DESC' ], true )
		) {
			$processed['order'] = strtoupper( sanitize_key( $params['order'] ) );
		}

		if (
			! empty( $params['orderby'] ) &&
			in_array( $params['orderby'], self::SORTABLE, true )
		) {
			$processed['orderby'] = sanitize_key( $params['orderby'] );
		}

		/*
		 * Sent date.
		 */
		if ( ! empty( $params['date'] ) ) {
			if ( is_string( $params['date'] ) ) {
				$params['date'] = array_fill( 0, 2, $params['date'] );
			} elseif ( is_array( $params['date'] ) && count( $params['date'] ) === 1 ) {
				$params['date'] = array_fill( 0, 2, $params['date'][0] );
			}

			// We pass array and treat it as a range from:to.
			if ( is_array( $params['date'] ) && count( $params['date'] ) === 2 ) {
				$date_start = WP::get_day_period_date( 'start_of_day', strtotime( $params['date'][0] ), 'Y-m-d H:i:s', true );
				$date_end   = WP::get_day_period_date( 'end_of_day', strtotime( $params['date'][1] ), 'Y-m-d H:i:s', true );

				if ( ! empty( $date_start ) && ! empty( $date_end ) ) {
					$processed['date'] = [ $date_start, $date_end ];
				}
			}
		}

		// Merge missing values with defaults.
		return wp_parse_args(
			$processed,
			$this->get_default_params()
		);
	}

	/**
	 * Get the list of default params for a usual query.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search defaults.
	 *
	 * @return array
	 */
	protected function get_default_params() {

		return array(
			'offset'   => 0,
			'per_page' => self::$per_page,
			'order'    => 'DESC',
			'orderby'  => 'date_sent',
			'search'   => array(
				'place' => 'people',
				'term'  => '',
			),
		);
	}

	/**
	 * Get the SQL-ready string of WHERE part for a query.
	 * Later we might implement something more useful and robust.
	 * For now we are leaving it as simple as possible.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search support. Refactored the method.
	 *
	 * @return string
	 */
	private function build_where() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		global $wpdb;

		$where = [ '1=1' ];

		/*
		 * Shortcut single ID or multiple IDs.
		 */
		if (
			! empty( $this->params['id'] ) ||
			! empty( $this->params['message_id'] ) ||
			! empty( $this->params['ids'] )
		) {
			if ( ! empty( $this->params['id'] ) ) {
				$where[] = $wpdb->prepare( 'id = %d', $this->params['id'] );
			} elseif ( ! empty( $this->params['message_id'] ) ) {
				$where[] = $wpdb->prepare( 'message_id = %s', $this->params['message_id'] );
			} elseif ( ! empty( $this->params['ids'] ) ) {
				$where[] = 'id IN (' . implode( ',', $this->params['ids'] ) . ')';
			}

			// When some ID(s) defined - we should ignore all other possible filtering options.
			return implode( ' AND ', $where );
		}

		/*
		 * Status.
		 */
		if ( isset( $this->params['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %d', $this->params['status'] );
		}

		/*
		 * Search.
		 */
		if ( ! empty( $this->params['search']['term'] ) && ! empty( $this->params['search']['place'] ) ) {
			switch ( $this->params['search']['place'] ) {
				case 'people':
					$where[] = $wpdb->prepare( 'people LIKE %s', '%' . $wpdb->esc_like( $this->params['search']['term'] ) . '%' );
					break;

				case 'headers':
					$where[] = '(' .
					           $wpdb->prepare(
						           'subject LIKE %s',
						           '%' . $wpdb->esc_like( $this->params['search']['term'] ) . '%'
					           )
					           . ' OR ' .
					           $wpdb->prepare(
						           'headers LIKE %s',
						           '%' . $wpdb->esc_like( $this->params['search']['term'] ) . '%'
					           )
					           . ')';
					break;

				case 'content':
					$where[] = '(' .
					           $wpdb->prepare(
						           'content_plain LIKE %s',
						           '%' . $wpdb->esc_like( $this->params['search']['term'] ) . '%'
					           )
					           . ' OR ' .
					           $wpdb->prepare(
						           'content_html LIKE %s',
						           '%' . $wpdb->esc_like( $this->params['search']['term'] ) . '%'
					           )
					           . ')';
					break;
			}
		}

		/*
		 * Sent date.
		 */
		if (
			! empty( $this->params['date'] ) &&
			is_array( $this->params['date'] ) &&
			count( $this->params['date'] ) === 2
		) {
			$where[] = $wpdb->prepare(
				'( date_sent >= %s AND date_sent <= %s )',
				$this->params['date'][0],
				$this->params['date'][1]
			);
		}

		return implode( ' AND ', $where );
	}

	/**
	 * Get the SQL-ready string of ORDER part for a query.
	 * Order is always in the params, as per our defaults.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	private function build_order() {

		return 'ORDER BY ' . $this->params['orderby'] . ' ' . $this->params['order'];
	}

	/**
	 * Get the SQL-ready string of LIMIT part for a query.
	 * Limit is always in the params, as per our defaults.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	private function build_limit() {

		return 'LIMIT ' . $this->params['offset'] . ', ' . $this->params['per_page'];
	}

	/**
	 * Count the number of DB records according to filters.
	 * Do not retrieve actual records.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public function get_count() {

		$table = Logs::get_table_name();

		$where = $this->build_where();

		return (int) WP::wpdb()->get_var(
			"SELECT COUNT(id) FROM $table
			WHERE {$where}"
		);
	}

	/**
	 * Get the list of DB records.
	 * You can either use array returned there OR iterate over the whole object,
	 * as it implements Iterator interface.
	 *
	 * @since 1.5.0
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\EmailsCollection
	 */
	public function get() {

		$table = Logs::get_table_name();

		$where = $this->build_where();
		$limit = $this->build_limit();
		$order = $this->build_order();

		$data = WP::wpdb()->get_results(
			"SELECT * FROM $table
			WHERE {$where}
			{$order}
			{$limit}"
		);

		if ( ! empty( $data ) ) {
			// As we got raw data we need to convert each row to Email.
			foreach ( $data as $row ) {
				$this->list[] = new Email( $row );
			}
		}

		return $this;
	}

	/**
	 * Delete emails one by one to be able to count how many were actually deleted.
	 *
	 * @since 1.5.0
	 *
	 * @return int Number of deleted emails.
	 */
	public function delete() {

		$ids = '';

		if ( ! empty( $this->params['ids'] ) ) {
			$ids = implode( ',', $this->params['ids'] );
		} elseif ( ! empty( $this->params['id'] ) ) {
			$ids = $this->params['id'];
		}

		if ( ! empty( $ids ) ) {
			$table = Logs::get_table_name();

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$result = (int) WP::wpdb()->query( "DELETE FROM $table WHERE id IN ( $ids )" );

			// Delete attachments.
			( new Attachments() )->delete_attachments( $ids );

			// Delete tracking events.
			( new TrackingEvents() )->delete_events( $ids );

			return $result;
		}

		return 0;
	}

	/*********************************************************************************************
	 * ****************************** \Counter interface method. *********************************
	 *********************************************************************************************/

	/**
	 * Count number of Record in a Queue.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function count() {

		return count( $this->list );
	}

	/*********************************************************************************************
	 * ****************************** \Iterator interface methods. *******************************
	 *********************************************************************************************/

	/**
	 * Rewind the Iterator to the first element.
	 *
	 * @since 1.5.0
	 */
	#[\ReturnTypeWillChange]
	public function rewind() {

		$this->iterator_position = 0;
	}

	/**
	 * Return the current element.
	 *
	 * @since 1.5.0
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email|null Return null when no items in collection.
	 */
	#[\ReturnTypeWillChange]
	public function current() {

		return $this->valid() ? $this->list[ $this->iterator_position ] : null;
	}

	/**
	 * Return the key of the current element.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	#[\ReturnTypeWillChange]
	public function key() {

		return $this->iterator_position;
	}

	/**
	 * Move forward to next element.
	 *
	 * @since 1.5.0
	 */
	#[\ReturnTypeWillChange]
	public function next() {

		++ $this->iterator_position;
	}

	/**
	 * Checks if current position is valid.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function valid() {

		return isset( $this->list[ $this->iterator_position ] );
	}
}
