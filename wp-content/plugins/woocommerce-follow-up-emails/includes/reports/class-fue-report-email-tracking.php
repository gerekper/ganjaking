<?php

/**
 * Class FUE_Report_Email_Tracking
 */
class FUE_Report_Email_Tracking {

	private $fue;

	/**
	 * Class constructor
	 * @param Follow_Up_Emails $fue
	 */
	public function __construct( Follow_Up_Emails $fue ) {
		$this->fue = $fue;
	}

	/**
	 * Log either an open or a click event.
	 * @see FUE_Email_Tracking::add_event for the data structure of $data
	 *
	 * @param string    $event 'open', 'web_open' or 'click'
	 * @param array     $data Event data
	 */
	public function log_event( $event, $data ) {

		if ( in_array( $event, array( 'open', 'click', 'web_open' ) ) ) {
			require_once FUE_INC_DIR .'/lib/fue-utils/class-fue-user-agent.php';

			$args = array(
				'event'     => $event,
				'queue_id'  => $data['queue_id'],
				'email_id'  => $data['email_id'],
				'user_id'   => $data['user_id'],
				'user_email'=> $data['user_email']
			);

			$user_agent = new FUE_User_Agent();
			$client     = $user_agent->get();

			if ( $client ) {
				$data['client_name']    = $client->client;
				$data['client_version'] = $client->version;
				$data['client_type']    = $client->type;
			}

			if ( $event == 'click' ) {
				// also check for the target url uniqueness
				$args['target_url'] = $data['target_url'];
			}

			if ( count( $this->get_events( $args ) ) == 0 ) {
				$this->add_event( $data );
			}
		} else {
			do_action( 'fue_email_tracking_log_event', $event, $data );
		}

	}

	/**
	 * Add a tracking event to the database
	 *
	 * $args data structure:
	 *  - event: 'open' or 'click'
	 *  - queue_id: ID of the queue item or email_order
	 *  - email_id: ID of the FUE Email
	 *  - user_id: ID of the customer or 0 if not registered
	 *  - user_email: Email address of the recipient
	 *  - target_url: Target URL of the clicked link
	 *
	 * @param array $args
	 * @return int|bool Return false on error or the ID of the inserted row
	 */
	public function add_event( $args ) {
		$wpdb = $this->fue->wpdb;

		$defaults = array(
			'event'     => 'open',
			'queue_id'  => 0,
			'email_id'  => 0,
			'user_id'   => 0,
			'user_email'=> '',
			'target_url'=> '',
			'client_name'   => '',
			'client_version'=> '',
			'client_type'   => '',
			'user_ip'       => '',
			'user_country'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$insert = array(
			'event_type'        => $args['event'],
			'email_order_id'    => $args['queue_id'],
			'email_id'          => $args['email_id'],
			'user_id'           => $args['user_id'],
			'user_email'        => $args['user_email'],
			'target_url'        => $args['target_url'],
			'client_name'       => $args['client_name'],
			'client_version'    => $args['client_version'],
			'client_type'       => $args['client_type'],
			'user_ip'           => $args['user_ip'],
			'user_country'      => $args['user_country'],
			'date_added'        => current_time( 'mysql' )
		);
		$result = $wpdb->insert( $wpdb->prefix .'followup_email_tracking', $insert );

		if ( false === $result ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Retrieve tracking events from the database filtered by the $args array
	 *
	 * @param array $args
	 * @return array
	 */
	public function get_events( $args = array() ) {
		$wpdb = $this->fue->wpdb;

		$defaults = array(
			'event'     => 'open',
			'queue_id'  => false,
			'email_id'  => false,
			'user_id'   => false,
			'user_email'=> false,
			'target_url'=> false
		);
		$args = wp_parse_args( $args, $defaults );

		$sql = "SELECT * FROM {$wpdb->prefix}followup_email_tracking WHERE 1=1";
		$params = array();

		$sql .= " AND event_type = %s";
		$params[] = $args['event'];

		if ( $args['queue_id'] !== false ) {
			$sql .= " AND email_order_id = %d";
			$params[] = $args['queue_id'];
		}

		if ( $args['email_id'] !== false ) {
			$sql .= " AND email_id = %d";
			$params[] = $args['email_id'];
		}

		if ( $args['user_id'] !== false ) {
			$sql .= " AND user_id = %d";
			$params[] = $args['user_id'];
		}

		if ( $args['user_email'] !== false ) {
			$sql .= " AND user_email = %s";
			$params[] = $args['user_email'];
		}

		if ( $args['target_url'] !== false ) {
			$sql .= " AND target_url = %s";
			$params[] = $args['target_url'];
		}

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );

		return $results;
	}

}