<?php
/**
 * FUE API Reports Class
 *
 * Handles requests to the /reports endpoint
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API_Reports extends FUE_API_Resource {

	/** @var string $base the route base */
	protected $base = '/reports';

	/** @var FUE_Admin_Report instance */
	private $report;

	/**
	 * Register the routes for this class
	 *
	 * GET /reports
	 * GET /reports/emails
	 * GET /reports/users
	 * GET /reports/excludes
	 *
	 * @since 4.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /reports
		$routes[ $this->base ] = array(
			array( array( $this, 'get_reports' ),     FUE_API_Server::READABLE ),
		);

		# GET /reports/emails
		$routes[ $this->base . '/emails'] = array(
			array( array( $this, 'get_emails_report' ), FUE_API_Server::READABLE ),
		);

		# GET /reports/emails/clicks
		$routes[ $this->base . '/emails/clicks'] = array(
			array( array( $this, 'get_email_clicks_report' ), FUE_API_Server::READABLE ),
		);

		# GET /reports/emails/opens
		$routes[ $this->base . '/emails/opens'] = array(
			array( array( $this, 'get_email_opens_report' ), FUE_API_Server::READABLE ),
		);

		# GET /reports/emails/ctor
		$routes[ $this->base . '/emails/ctor'] = array(
			array( array( $this, 'get_email_ctor_report' ), FUE_API_Server::READABLE ),
		);

		# GET /reports/users
		$routes[ $this->base . '/users'] = array(
			array( array( $this, 'get_users_report' ), FUE_API_Server::READABLE ),
		);

		# GET /reports/excludes
		$routes[ $this->base . '/excludes'] = array(
			array( array( $this, 'get_excludes_report' ), FUE_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get a simple listing of available reports
	 *
	 * @since 2.1
	 * @return array
	 */
	public function get_reports() {

		return array(
			'reports' => array(
				'emails',
				'emails/clicks',
				'emails/opens',
				'emails/ctor',
				'users',
				'excludes'
			)
		);
	}

	/**
	 * Get the emails report
	 *
	 * @param array $fields unused
	 * @param array $filter
	 * @param int $page
	 * @return array
	 * @since 4.1
	 */
	public function get_emails_report( $fields = null, $filter = array(), $page = 1 ) {

		// check user permissions
		$check = $this->validate_request();

		if ( is_wp_error( $check ) )
			return $check;

		$args = array(
			'type'      => 'emails',
			'page'      => $page,
			'limit'     => ( !empty( $filter['limit'] ) ) ? absint( $filter['limit'] ) : get_option( 'posts_per_page' )
		);

		$email_reports  = FUE_Reports::get_reports( $args );
		$total_rows     = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");

		$report_data    = array();

		foreach ( $email_reports as $email_report ) {
			$sent       = FUE_Reports::count_email_sends( $email_report->email_id );
			$opened     = FUE_Reports::count_event_occurences( $email_report->email_id, 'open' );
			$clicked    = FUE_Reports::count_event_occurences( $email_report->email_id, 'click' );
			$email      = new FUE_Email( $email_report->email_id );

			$report = array(
				'email'         => $email_report->email_name,
				'email_trigger' => $email_report->email_trigger,
				'num_sent'      => $sent,
				'num_opens'     => $opened,
				'num_clicks'    => $clicked
			);

			$report_data[] = $report;
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $report_data ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return array( 'emails' => apply_filters( 'fue_api_report_response', $report_data, $email_reports, $fields, $this->server ) );
	}

	/**
	 * Get the email opens report
	 *
	 * @since 4.1
	 * @param null $fields unused
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_email_opens_report( $fields = null, $filter = array(), $page = 1 ) {
		$report_data    = array();
		$args           = array(
			'event_type'    => 'open',
			'page'          => $page,
			'limit'         => ( !empty( $filter['limit'] ) ) ? absint( $filter['limit'] ) : get_option('posts_per_page')
		);

		if ( !empty($filter['date_from']) && !empty($filter['date_to']) ) {
			$args['range'] = array(
				'from'  => get_date_from_gmt( $this->server->parse_datetime( $filter['date_from'] ) ),
				'to'    => get_date_from_gmt( $this->server->parse_datetime( $filter['date_to'] ) )
			);
		}

		$reports        = FUE_Reports::get_tracking_events( $args );
		$total_rows     = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");

		foreach ( $reports as $report ) {
			$report_data[] = array(
				'queue_id'  => $report->email_order_id,
				'email_id'  => $report->email_id,
				'user_id'   => $report->user_id,
				'email'     => $report->user_email,
				'date_added'=> $report->date_added
			);
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $report_data ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return array( 'opens' => apply_filters( 'fue_api_report_response', $report_data, $reports, $fields, $this->server ) );
	}

	/**
	 * Get the email clicks report
	 *
	 * @since 4.1
	 * @param null $fields unused
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_email_clicks_report( $fields = null, $filter = array(), $page = 1 ) {
		$report_data    = array();
		$args           = array(
			'event_type'    => 'click',
			'page'          => $page,
			'limit'         => ( !empty( $filter['limit'] ) ) ? absint( $filter['limit'] ) : get_option('posts_per_page')
		);

		if ( !empty($filter['date_from']) && !empty($filter['date_to']) ) {
			$args['range'] = array(
				'from'  => get_date_from_gmt( $this->server->parse_datetime( $filter['date_from'] ) ),
				'to'    => get_date_from_gmt( $this->server->parse_datetime( $filter['date_to'] ) )
			);
		}

		$reports        = FUE_Reports::get_tracking_events( $args );
		$total_rows     = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");

		foreach ( $reports as $report ) {
			$report_data[] = array(
				'queue_id'  => $report->email_order_id,
				'email_id'  => $report->email_id,
				'user_id'   => $report->user_id,
				'email'     => $report->user_email,
				'target_url'=> $report->target_url,
				'date_added'=> $report->date_added
			);
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $report_data ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return array( 'clicks' => apply_filters( 'fue_api_report_response', $report_data, $reports, $fields, $this->server ) );
	}

	/**
	 * Get the email CTOR report
	 *
	 * @since 4.1
	 * @param null $fields unused
	 * @return array
	 */
	public function get_email_ctor_report( $fields = null ) {
		$report_data    = array();
		$reports        = FUE_Reports::get_top_emails_by( 'ctor', 50 );

		foreach ( $reports as $report ) {
			$report_data[] = array(
				'email_id'  => $report['email_id'],
				'clicks'    => $report['clicks'],
				'opens'     => $report['opens'],
				'ctor'      => $report['ctor']
			);
		}

		return array( 'ctor' => apply_filters( 'fue_api_report_response', $report_data, $reports, $fields, $this->server ) );
	}

	/**
	 * Get user reports
	 * @param null $fields unused
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_users_report( $fields = null, $filter = array(), $page = 1 ) {
		$args = array(
			'type'      => 'users',
			'page'      => $page,
			'limit'     => (!empty($filter['limit'])) ? absint($filter['limit']) : get_option('posts_per_page')
		);

		$user_reports   = FUE_Reports::get_reports( $args );
		$report_data    = array();
		$total_rows     = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");

		foreach ($user_reports as $report) {
			if (empty($report->email_address)) continue;

			$name       = $report->customer_name;
			$sent       = absint( FUE_Reports::count_emails_sent( null, null, $report->email_address ) );
			$opened     = absint( FUE_Reports::count_opened_emails( array('user_email' => $report->email_address) ) );
			$clicked    = absint( FUE_Reports::count_total_email_clicks( array( 'user_email' => $report->email_address) ) );

			if ($report->user_id != 0 && empty( $name ) ) {
				$wp_user = new WP_User($report->user_id);
				$name = $wp_user->first_name . ' ' . $wp_user->last_name;
			}

			$report_data[] = array(
				'id'    => absint( $report->user_id ),
				'name'  => apply_filters( 'fue_report_customer_name', $name, $report ),
				'sent'  => $sent,
				'opens' => $opened,
				'clicks'=> $clicked
			);
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $report_data ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return array( 'users' => apply_filters( 'fue_api_report_response', $report_data, $user_reports, $fields, $this->server ) );
	}

	/**
	 * Get all the entries in the excludes list
	 *
	 * @param null $fields
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_excludes_report( $fields = null, $filter = array(), $page = 1 ) {
		$args = array(
			'type'      => 'excludes',
			'page'      => $page,
			'limit'     => (!empty($filter['limit'])) ? absint($filter['limit']) : get_option('posts_per_page')
		);
		$exclude_reports    = FUE_Reports::get_reports( $args );
		$exclude_data       = array();
		$total_rows         = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");

		foreach ( $exclude_reports as $report ) {
			$exclude_data[] = array(
				'email_name'    => $report->email_name,
				'email_address' => $report->email,
				'date'          => $this->server->format_datetime( $report->date_added, true )
			);
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $exclude_data ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return array( 'optouts' => apply_filters( 'fue_api_report_response', $exclude_data, $exclude_reports, $fields, $this->server ) );

	}

	/**
	 * Verify that the current user has permission to view reports
	 *
	 * @since 4.1
	 * @see FUE_API_Resource::validate_request()
	 * @param null $id unused
	 * @param null $type unused
	 * @param null $context unused
	 * @return bool true if the request is valid and should be processed, false otherwise
	 */
	protected function validate_request( $id = null, $type = null, $context = null ) {

		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {

			return new WP_Error( 'fue_api_user_cannot_read_report', __( 'You do not have permission to read this report', 'follow_up_emails' ), array( 'status' => 401 ) );

		} else {

			return true;
		}
	}
}
