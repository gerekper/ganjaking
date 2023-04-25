<?php

namespace WPMailSMTP\Pro\Emails\Logs\Reports;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Logs;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\ClickLinkEvent;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\OpenEmailEvent;

/**
 * Email report query.
 *
 * @since 3.0.0
 */
class Report {

	/**
	 * Report params.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $params;

	/**
	 * Report params before parsing.
	 * Can be useful for duplicate report.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $raw_params;

	/**
	 * Stats totals count.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $stats_totals = null;

	/**
	 * Stats totals count grouped by subject.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $stats_by_subject = null;

	/**
	 * Stats totals count grouped by date.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $stats_by_date = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params Report params.
	 */
	public function __construct( $params = [] ) {

		$this->raw_params = $params;
		$this->params     = $this->process_params( $params );
	}

	/**
	 * Process report params.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params Report params.
	 *
	 * @return array
	 */
	private function process_params( $params ) {

		$params    = (array) $params;
		$processed = [];

		// Date.
		if ( ! empty( $params['date'] ) ) {
			$processed['date'] = $this->parse_date_param( $params['date'] );
		}

		// Search.
		if ( ! empty( $params['search'] ) ) {
			$processed['search'] = sanitize_text_field( $params['search'] );
		}

		// Order.
		if (
			! empty( $params['order'] ) &&
			is_string( $params['order'] ) &&
			in_array( strtoupper( $params['order'] ), [ 'ASC', 'DESC' ], true )
		) {
			$processed['order'] = strtoupper( $params['order'] );
		}

		$allowed_order_by = [ 'subject', 'total', 'sent', 'delivered', 'unsent', 'open_count', 'click_count' ];

		if ( ! empty( $params['orderby'] ) && in_array( $params['orderby'], $allowed_order_by, true ) ) {
			$processed['orderby'] = $params['orderby'];
		}

		// Merge missing values with defaults.
		return wp_parse_args(
			$processed,
			$this->get_default_params()
		);
	}

	/**
	 * Parse date param.
	 *
	 * @since 3.0.0
	 *
	 * @param array $date Dates array in format 'Y-m-d'.
	 *
	 * @return array|bool
	 */
	private function parse_date_param( $date ) {

		$date = array_filter( array_values( (array) $date ) );

		if ( empty( $date ) ) {
			return false;
		}

		if ( count( $date ) === 1 ) {
			$date = array_fill( 0, 2, $date[0] );
		}

		if ( count( $date ) !== 2 ) {
			return false;
		}

		try {
			$date_start = \DateTime::createFromFormat( 'Y-m-d', $date[0] );
			$date_end   = \DateTime::createFromFormat( 'Y-m-d', $date[1] );
		} catch ( \Exception $e ) {
			return false;
		}

		return [
			'from' => $date_start->setTime( 0, 0 ),
			'to'   => $date_end->setTime( 23, 59, 59 ),
		];
	}

	/**
	 * Get the list of default params for a usual query.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private function get_default_params() {

		return [
			'order'   => 'DESC',
			'orderby' => 'total',
		];
	}

	/**
	 * Returns all report params or single param by key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Param key.
	 *
	 * @return mixed
	 */
	public function get_params( $key = null ) {

		if ( ! is_null( $key ) ) {
			return isset( $this->params[ $key ] ) ? $this->params[ $key ] : false;
		}

		return $this->params;
	}

	/**
	 * Returns all report raw params.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	public function get_raw_params() {

		return $this->raw_params;
	}

	/**
	 * Returns from date param.
	 *
	 * @since 3.0.0
	 *
	 * @return \DateTime|false
	 */
	public function get_from_date() {

		return ! empty( $this->params['date'] ) ? $this->params['date']['from'] : false;
	}

	/**
	 * Returns to date param.
	 *
	 * @since 3.0.0
	 *
	 * @return \DateTime|false
	 */
	public function get_to_date() {

		return ! empty( $this->params['date']['to'] ) ? $this->params['date']['to'] : false;
	}

	/**
	 * Returns date interval between from and to date in days.
	 *
	 * @since 3.0.0
	 *
	 * @return integer
	 */
	public function get_date_range() {

		if ( ! $this->get_to_date() || ! $this->get_from_date() ) {
			return 0;
		}

		return $this->get_from_date()->diff( $this->get_to_date() )->days;
	}

	/**
	 * Get the totals count.
	 *
	 * @since 3.0.0
	 *
	 * @return array|null
	 */
	public function get_stats_totals() {

		if ( ! is_null( $this->stats_totals ) ) {
			return $this->stats_totals;
		}

		global $wpdb;

		$logs_table = Logs::get_table_name();
		$select     = $this->build_select();
		$join       = $this->build_join();
		$where      = $this->build_where();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->stats_totals = $wpdb->get_row(
			"SELECT {$select} FROM {$logs_table} as logs {$join} WHERE {$where}",
			\ARRAY_A
		);
		// phpcs:enable

		return $this->stats_totals;
	}

	/**
	 * Get the totals count grouped by date.
	 *
	 * @since 3.0.0
	 *
	 * @return array|null
	 */
	public function get_stats_by_date() {

		if ( ! is_null( $this->stats_by_date ) ) {
			return $this->stats_by_date;
		}

		global $wpdb;

		$logs_table = Logs::get_table_name();
		$select     = 'CAST(logs.date_sent AS DATE) as day, ' . $this->build_select();
		$join       = $this->build_join();
		$where      = $this->build_where();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->stats_by_date = $wpdb->get_results(
			"SELECT {$select} FROM {$logs_table} as logs {$join} WHERE {$where} GROUP BY day",
			\ARRAY_A
		);
		// phpcs:enable

		return $this->stats_by_date;
	}

	/**
	 * Get the totals count grouped by subject.
	 *
	 * @since 3.0.0
	 *
	 * @return array|null
	 */
	public function get_stats_by_subject() {

		if ( ! is_null( $this->stats_by_subject ) ) {
			return $this->stats_by_subject;
		}

		global $wpdb;

		$logs_table = Logs::get_table_name();
		$select     = 'logs.subject as subject, ' . $this->build_select();
		$join       = $this->build_join();
		$where      = $this->build_where();
		$order      = $this->build_order();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->stats_by_subject = $wpdb->get_results(
			"SELECT {$select} FROM {$logs_table} as logs {$join} WHERE {$where} GROUP BY subject {$order}",
			\ARRAY_A
		);
		// phpcs:enable

		return $this->stats_by_subject;
	}

	/**
	 * Get the totals count grouped by date and prepared for chart.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_stats_by_date_chart_data() {

		$results = $this->get_stats_by_date();

		if ( ! is_array( $results ) ) {
			return [];
		}

		if ( empty( $this->get_from_date() ) || empty( $this->get_to_date() ) ) {
			return $results;
		}

		$results = array_combine( array_column( $results, 'day' ), $results );

		$period = new \DatePeriod( $this->get_from_date(), new \DateInterval( 'P1D' ), $this->get_to_date() );

		// Fill DB results with empty entries where there's no data.
		foreach ( $period as $value ) {
			$date = $value->format( 'Y-m-d' );

			$results[ $date ] = array_merge(
				[
					'day'         => $date,
					'total'       => 0,
					'unsent'      => 0,
					'sent'        => 0,
					'delivered'   => 0,
					'open_count'  => 0,
					'click_count' => 0,
				],
				array_key_exists( $date, $results ) ? $results[ $date ] : []
			);
		}

		ksort( $results );

		return $results;
	}

	/**
	 * Get total emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_total_count( $item ) {

		return intval( $item['sent'] ) + intval( $item['delivered'] ) + intval( $item['unsent'] );
	}

	/**
	 * Get sent emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_sent_count( $item ) {

		return intval( $item['sent'] ) + intval( $item['delivered'] );
	}

	/**
	 * Get confirmed emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_confirmed_count( $item ) {

		return intval( $item['delivered'] );
	}

	/**
	 * Get unconfirmed emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_unconfirmed_count( $item ) {

		return intval( $item['sent'] );
	}

	/**
	 * Get failed emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_unsent_count( $item ) {

		return intval( $item['unsent'] );
	}

	/**
	 * Get opened emails count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_open_count( $item ) {

		return intval( $item['open_count'] );
	}

	/**
	 * Get click links count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_click_count( $item ) {

		return intval( $item['click_count'] );
	}

	/**
	 * Get sent emails percent.
	 *
	 * @since 3.8.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_sent_percent_count( $item ) {

		return $this->get_percentage( $this->get_sent_count( $item ), $item['total'] );
	}

	/**
	 * Get confirmed emails percent.
	 *
	 * @since 3.8.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_confirmed_percent_count( $item ) {

		return $this->get_percentage( $this->get_confirmed_count( $item ), $item['total'] );
	}

	/**
	 * Get unconfirmed emails percent.
	 *
	 * @since 3.8.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_unconfirmed_percent_count( $item ) {

		return $this->get_percentage( $this->get_unconfirmed_count( $item ), $item['total'] );
	}

	/**
	 * Get failed emails percent.
	 *
	 * @since 3.8.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_unsent_percent_count( $item ) {

		return $this->get_percentage( $this->get_unsent_count( $item ), $item['total'] );
	}

	/**
	 * Get opened emails percent.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_open_percent_count( $item ) {

		return $this->get_percentage( $this->get_open_count( $item ), $item['total'] );
	}

	/**
	 * Get click links percent.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats item.
	 *
	 * @return int
	 */
	public function get_click_percent_count( $item ) {

		return $this->get_percentage( $this->get_click_count( $item ), $item['total'] );
	}

	/**
	 * Get the percent of a count relative to the total.
	 *
	 * @since 3.8.0
	 *
	 * @param int $count Count to get the percent of.
	 * @param int $total Total count.
	 *
	 * @return int
	 */
	private function get_percentage( $count, $total ) {

		return ! empty( $total ) ? intval( $count / $total * 100 ) : 0;
	}

	/**
	 * Get the SQL-ready string of SELECT part for a query.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function build_select() {

		global $wpdb;

		$select = $wpdb->prepare(
			'COUNT(DISTINCT logs.id) as total,
			COUNT(DISTINCT CASE WHEN logs.status = %d THEN logs.id ELSE NULL END) as unsent,
			COUNT(DISTINCT CASE WHEN logs.status = %d THEN logs.id ELSE NULL END) as sent,
			COUNT(DISTINCT CASE WHEN logs.status = %d THEN logs.id ELSE NULL END) as delivered',
			Email::STATUS_UNSENT,
			Email::STATUS_SENT,
			Email::STATUS_DELIVERED
		);

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_tracking() ) {
			$select .= $wpdb->prepare(
				', COUNT(DISTINCT CASE WHEN events.event_type = %s THEN events.email_log_id ELSE NULL END) as open_count,
				COUNT(DISTINCT CASE WHEN events.event_type = %s THEN events.email_log_id ELSE NULL END) as click_count',
				OpenEmailEvent::get_type(),
				ClickLinkEvent::get_type()
			);
		}

		return trim( $select );
	}

	/**
	 * Get the SQL-ready string of JOIN part for a query.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function build_join() {

		$join = '';

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_tracking() ) {
			$events_table = Tracking::get_events_table_name();

			$join .= " LEFT JOIN {$events_table} as events ON events.email_log_id = logs.id";
		}

		return trim( $join );
	}

	/**
	 * Get the SQL-ready string of WHERE part for a query.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function build_where() {

		global $wpdb;

		$where = [ '1=1' ];

		// Search by subject.
		if ( ! empty( $this->params['search'] ) ) {
			$where[] = $wpdb->prepare(
				'subject LIKE %s',
				'%' . $wpdb->esc_like( $this->params['search'] ) . '%'
			);
		}

		// Sent date.
		if ( ! empty( $this->params['date'] ) ) {
			$where[] = $wpdb->prepare(
				'( date_sent >= %s AND date_sent <= %s )',
				$this->get_from_date()->format( 'Y-m-d H:i:s' ),
				$this->get_to_date()->format( 'Y-m-d H:i:s' )
			);
		}

		// Exclude waiting emails from reports.
		$where[] = $wpdb->prepare( 'status != %d', Email::STATUS_WAITING );

		return implode( ' AND ', $where );
	}

	/**
	 * Get the SQL-ready string of ORDER part for a query.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function build_order() {

		return 'ORDER BY ' . $this->params['orderby'] . ' ' . $this->params['order'];
	}
}
