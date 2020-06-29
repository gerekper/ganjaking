<?php
/**
 * FUE API Emails Class
 *
 * Handles requests to the /emails endpoint
 *
 * @author      75nineteen
 * @since       4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API_Emails extends FUE_API_Resource {

	/** @var string $base the route base */
	protected $base = '/emails';

	/**
	 * Register the routes for this class
	 *
	 * GET /emails
	 *
	 * @since 4.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET/POST /emails
		$routes[ $this->base ] = array(
			array( array( $this, 'get_emails' ),    FUE_API_Server::READABLE ),
			array( array( $this, 'create_email' ),  FUE_API_SERVER::CREATABLE | FUE_API_Server::ACCEPT_DATA ),
		);

		$routes[ $this->base .'/types' ] = array(
			array( array( $this, 'get_email_types' ), FUE_API_Server::READABLE )
		);

		# GET/POST/DELETE /emails/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_email' ),    FUE_API_SERVER::READABLE ),
			array( array( $this, 'edit_email' ),   FUE_API_SERVER::EDITABLE | FUE_API_SERVER::ACCEPT_DATA ),
			array( array( $this, 'delete_email' ), FUE_API_SERVER::DELETABLE ),
		);

		# GET /emails/templates
		$routes[ $this->base . '/templates' ] = array(
			array( array( $this, 'get_templates' ), FUE_API_Server::READABLE )
		);

		# POST /emails/<id>/send
		$routes[ $this->base .'/send/(?P<id>\d+)' ] = array(
			array( array( $this, 'send_manual_email' ), FUE_API_Server::EDITABLE | FUE_API_Server::ACCEPT_DATA )
		);

		return $routes;
	}

	/**
	 * Get a list of the registered email types
	 *
	 * @since 4.4
	 * @return array
	 */
	public function get_email_types() {
		return Follow_Up_Emails::instance()->get_email_types();
	}

	/**
	 * Get a simple listing of available emails
	 *
	 * @since 4.1
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_emails( $filter = array(), $page = 1 ) {
		$filter['paged']        = $page;
		$filter['limit']        = ( !empty( $filter['limit'] ) ) ? absint( $filter['limit'] ) : get_option('posts_per_page');
		$filter['post_status']  = array( FUE_Email::STATUS_ACTIVE, FUE_Email::STATUS_ARCHIVED, FUE_Email::STATUS_INACTIVE );
		$filter['posts_per_page'] = $filter['limit'];
		unset( $filter['limit'] );

		if ( !empty( $filter['type'] ) ) {
			$filter['tax_query'][] = array(
				'taxonomy'  => 'follow_up_email_type',
				'field'     => 'slug',
				'terms'     => $filter['type']
			);
			unset($filter['type']);
		}

		if ( !empty( $filter['campaign'] ) ) {
			$campaign = $filter['campaign'];
			unset($filter['campaign']);

			$filter['tax_query'][] = array(
				'taxonomy'  => 'follow_up_email_campaign',
				'field'     => 'slug',
				'terms'     => $campaign
			);
		}

		if ( !empty( $filter['status'] ) ) {
			$filter['post_status'] = $this->fix_status_string( $filter['status'] );
			unset( $filter['status'] );
		} else {

		}

		$filter['fields'] = 'ids';

		/**
		 * fue_get_emails is not really useful here since it uses get_posts
		 * and we need to get the total number of rows that only WP_Query provides
		 */
		$filter['nopaging'] = false;
		$filter['post_type'] = 'follow_up_email';

		$query  = new WP_Query( $filter );
		$result = array();

		foreach ( $query->posts as $email_id ) {
			$result[] = $this->get_email( $email_id );
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $query->posts ) == 1,
			'total'       => $query->found_posts,
			'total_pages' => $query->max_num_pages
		);
		$this->server->add_pagination_headers( $query );

		return $result;

	}

	/**
	 * Get a single email
	 *
	 * @since 4.1
	 * @param int $id
	 * @param array $fields
	 * @return array
	 */
	public function get_email( $id, $fields = array() ) {
		// validate the email ID
		$id = $this->validate_request( $id, 'follow_up_email', 'read' );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$email = new FUE_Email( $id );

		$email_data = array(
			'id'            => $email->id,
			'created_at'    => $email->post->post_date,
			'type'          => $email->get_type(),
			'template'      => $email->template,
			'name'          => $email->name,
			'subject'       => $email->subject,
			'message'       => $email->message,
			'status'        => $this->fix_status_string( $email->status, true ),
			'trigger'       => $email->trigger,
			'trigger_string'=> $email->get_trigger_string(),
			'interval'      => $email->interval,
			'duration'      => $email->duration,
			'always_send'   => $email->always_send,
			'product_id'    => $email->product_id,
			'category_id'   => $email->category_id,
			'campaigns'     => wp_get_object_terms( $email->id, 'follow_up_email_campaign', array('fields' => 'slugs') ),
			'requirements'  => ( !empty( $email->conditions ) ) ? $email->conditions : array()
		);

		return array( 'email' => apply_filters( 'fue_api_email_response', $email_data, $email, $fields, $this->server ) );
	}

	/**
	 * Create a new follow-up email
	 *
	 * @since 4.1
	 * @param array $data
	 * @return array
	 * @throws FUE_API_Exception
	 */
	public function create_email( $data ) {
		$data   = apply_filters( 'fue_api_create_email_data', $data, $this );

		if ( ! empty( $data['status'] ) ) {
			$data['status'] = $this->fix_status_string( $data['status'] );
		}
		if ( ! empty( $data['interval'] ) ) {
			$data['interval_num'] = $data['interval'];
			unset( $data['interval'] );
		}
		if ( ! empty( $data['duration'] ) ) {
			$data['interval_duration'] = $data['duration'];
			unset( $data['duration'] );
		}

		$id = fue_create_email( $data );

		// Checks for an error in the email creation.
		if ( is_wp_error( $id ) ) {
			throw new FUE_API_Exception( 'fue_api_cannot_create_email', $id->get_error_message(), 400 );
		}

		do_action( 'fue_api_created_email', $id, $data );

		$this->server->send_status( 201 );

		return $this->get_email( $id );
	}

	/**
	 * Edit an email
	 *
	 * @since 4.1
	 * @param int $id
	 * @param array $data
	 * @return array
	 * @throws FUE_API_Exception
	 */
	public function edit_email( $id, $data ) {
		// validate the email ID
		$id = $this->validate_request( $id, 'follow_up_email', 'edit' );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		if ( !isset($data['id']) && !isset($data['ID']) ) {
			$data['id'] = $id;
		}

		if ( !empty( $data['status'] ) ) {
			$data['status'] = $this->fix_status_string( $data['status'] );
		}

		$data = apply_filters( 'fue_api_edit_email_data', $data, $this );

		$id = fue_update_email( $data );

		// Checks for an error in the email creation.
		if ( is_wp_error( $id ) ) {
			throw new FUE_API_Exception( 'fue_api_cannot_edit_email', $id->get_error_message(), 400 );
		}

		do_action( 'fue_api_edited_email', $id, $data );

		$this->server->send_status( 201 );

		return $this->get_email( $id );
	}

	/**
	 * Delete an email
	 * @param int $id
	 * @return array
	 */
	public function delete_email( $id ) {
		$id = $this->validate_request( $id, 'follow_up_email', 'delete' );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		do_action( 'fue_api_delete_email', $id, $this );

		return $this->delete( $id, 'follow_up_email' );
	}

	/**
	 * Get a list of the installed templates
	 * @return array
	 */
	public function get_templates() {
		$templates  = fue_get_installed_templates();
		$output     = array();

		foreach ( $templates as $template ) {
			$tpl = new FUE_Email_Template( $template );

			$output[] = array(
				'template' => array(
					'id'        => basename( $template ),
					'name'      => $tpl->name,
					'sections'  => $tpl->get_sections()
				)
			);
		}

		return $output;
	}

	/**
	 * Send a manual email to the specified recipients.
	 *
	 * $data keys:
	 *  - send_type: `email` or `subscribers` are supported by default.
	 *  - recipient_email: Email address of the recipient if `send_type` is `email`
	 *  - schedule_email: `1` to enable scheduling. Defaults to `0`
	 *  - sending_schedule_date: Required if `schedule_email` is enabled. RFC3339 date/time format
	 *  - send_again: `1` to send the same email again after a certain period of time. Defaults to `0`
	 *  - interval: Integer used in scheduling the second email
	 *  - interval_duration: Valid values are `minutes`, `hours`, `days`, `weeks`, `months` or `years`.
	 *  - tracking: Google Analytics tracking code that will be appended to URLs in the email
	 *
	 * @param int $id
	 * @param array $data
	 * @return array
	 */
	public function send_manual_email( $id, $data ) {
		$id = $this->validate_request( $id, 'follow_up_email', 'delete' );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		if ( empty( $data['send_type'] ) ) {
			return new WP_Error( 'fue_api_missing_callback_param', __('send_type is a required field', 'follow_up_emails') );
		}

		$send_type  = $data['send_type'];
		$recipients = array(); //format: array(user_id, email_address, name)

		if ( $send_type == 'email' && !empty( $data['recipient_email'] ) ) {
			$key = '0|'. $data['recipient_email'] .'|';
			$recipients[$key] = array( 0, $data['recipient_email'], '' );
		} elseif ( $send_type == 'subscribers' ) {
			$list = (!empty($data['email_list'])) ? $data['email_list'] : '';
			$subscribers = fue_get_subscribers( array('list' => $list ) );

			foreach ( $subscribers as $subscriber ) {
				$key = '0|'. $subscriber .'|';
				$recipients[ $key ] = array( 0, $subscriber, '' );
			}
		}

		$recipients = apply_filters( 'fue_manual_email_recipients', $recipients, $data );

		if (! empty($recipients) ) {
			$email = new FUE_Email( $id );

			$schedule_email = (isset($data['schedule_email']) && $data['schedule_email'] == 1) ? true : false;
			$schedule_date  = '';
			$schedule_hour  = '';
			$schedule_minute= '';
			$schedule_ampm  = '';

			$send_again         = (isset($data['send_again']) && $data['send_again'] == 1) ? true : false;
			$interval           = '';
			$interval_duration  = '';

			if ( $send_again ) {
				$interval           = absint( $data['interval'] );
				$interval_duration  = $data['interval_duration'];
			}

			if ( $schedule_email ) {
				if ( empty( $data['sending_schedule_date'] ) ) {
					return new WP_Error( 'send_manual_error', __('Cannot schedule an email without passing a valid `sending_schedule_date`', 'follow_up_emails') );
				}

				$timestamp      = get_date_from_gmt( $this->server->parse_datetime( $data['sending_schedule_date'] ), 'U' );
				$schedule_date  = date( 'm/d/Y', $timestamp );
				$schedule_hour  = date( 'h', $timestamp );
				$schedule_minute= date( 'i', $timestamp );
				$schedule_ampm  = date( 'a', $timestamp );
			}

			$args = apply_filters( 'fue_manual_email_args', array(
				'email_id'          => $id,
				'recipients'        => $recipients,
				'subject'           => $email->subject,
				'message'           => $email->message,
				'tracking'          => $data['tracking'],
				'schedule_email'    => $schedule_email,
				'schedule_date'     => $schedule_date,
				'schedule_hour'     => $schedule_hour,
				'schedule_minute'   => $schedule_minute,
				'schedule_ampm'     => $schedule_ampm,
				'send_again'        => $send_again,
				'interval'          => $interval,
				'interval_duration' => $interval_duration
			), $data );

			$queue_item_ids = FUE_Sending_Scheduler::queue_manual_emails( $args );
			$response       = array();
			$fue_api        = Follow_Up_Emails::instance()->api;

			foreach ( $queue_item_ids as $queue_id ) {
				$response[] = $fue_api->FUE_API_Queue->get_queue_item( $queue_id );
			}

			return $response;

		}

		return array();
	}

	/**
	 * Normalize the status string by appending the 'fue-' prefix if necessary
	 *
	 * @param string $status
	 * @param bool $trim Whether to trim or append the 'fue-' prefix
	 * @return string
	 */
	protected function fix_status_string( $status, $trim = false ) {
		if ( $trim ) {
			$status = ltrim( $status, 'fue-' );
		} else {
			if ( strpos( $status, 'fue-' ) === false )
				$status = 'fue-'. $status;
		}


		return $status;
	}
}
