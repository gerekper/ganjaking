<?php
/**
 * FUE API Queue Class
 *
 * Handles requests to the /queue endpoint
 *
 * @author      75nineteen
 * @since       4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API_Queue extends FUE_API_Resource {

	/** @var string $base the route base */
	protected $base = '/queue';

	/** @var FUE_Sending_Scheduler */
	private $scheduler = null;

	/**
	 * Class constructor
	 * @param FUE_API_Server $server
	 * @return FUE_API_Queue
	 * @since 4.1
	 */
	public function __construct( FUE_API_Server $server ) {
		$this->scheduler = Follow_Up_Emails::instance()->scheduler;

		parent::__construct( $server );
	}

	/**
	 * Register the routes for this class
	 *
	 * GET /queue
	 *
	 * @since 4.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /queue
		$routes[ $this->base ] = array(
			array( array( $this, 'get_queue_items' ),    FUE_API_Server::READABLE ),
			array( array( $this, 'create_queue_item' ),  FUE_API_SERVER::CREATABLE | FUE_API_Server::ACCEPT_DATA ),
		);

		# GET /queue/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_queue_item' ),    FUE_API_SERVER::READABLE ),
			array( array( $this, 'edit_queue_item' ),   FUE_API_SERVER::EDITABLE | FUE_API_SERVER::ACCEPT_DATA ),
			array( array( $this, 'delete_queue_item' ), FUE_API_SERVER::DELETABLE ),
		);

		return $routes;
	}

	/**
	 * Get a simple listing of the items in the queue.
	 *
	 * Supported filters:
	 *  * user_id
	 *  * user_email
	 *  * order_id
	 *  * product_id
	 *  * email_id
	 *  * cart
	 *  * sent
	 *  * status
	 *  * date_sent_from RFC3339 date/time format
	 *  * date_sent_to
	 *  * date_scheduled_from
	 *  * date_scheduled_to
	 *  * limit
	 *
	 * @since 4.1
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_queue_items( $filter = array(), $page = 1 ) {
		$filter['page'] = $page;

		$args = array();
		$defaults = array(
			'fields'                => 'ids',
			'user_id'               => '',
			'user_email'            => '',
			'order_id'              => '',
			'product_id'            => '',
			'email_id'              => '',
			'cart'                  => '',
			'sent'                  => '',
			'status'                => '',
			'date_sent_from'        => '',
			'date_sent_to'          => '',
			'date_scheduled_from'   => '',
			'date_scheduled_to'     => '',
			'limit'                 => get_option( 'posts_per_page' ),
			'page'                  => 1
		);
		$filter = wp_parse_args( $filter, $defaults );

		foreach ( $filter as $key => $value ) {
			if ( $value !== '' ) {

				switch ( $key ) {

					case 'date_sent_from':
						$args['date_sent']['from'] = get_date_from_gmt( $this->server->parse_datetime( $value ) );
						break;

					case 'date_sent_to':
						$args['date_sent']['to'] = get_date_from_gmt( $this->server->parse_datetime( $value ) );
						break;

					case 'date_scheduled_from':
						$args['send_on']['from'] = get_date_from_gmt( $this->server->parse_datetime( $value ), 'U' );
						break;

					case 'date_scheduled_to':
						$args['send_on']['to'] = get_date_from_gmt( $this->server->parse_datetime( $value ), 'U' );
						break;

					default:
						$args[ $key ] = $value;

				}
			}
		}

		$items      = $this->scheduler->get_items( $args );
		$total_rows = Follow_Up_Emails::instance()->wpdb->get_var("SELECT FOUND_ROWS()");
		$results    = array();

		foreach ( $items as $item_id ) {
			$results[] = $this->get_queue_item( $item_id );
		}

		// set the pagination data
		$query = array(
			'page'        => $page,
			'single'      => count( $results ) == 1,
			'total'       => $total_rows,
			'total_pages' => ceil( $total_rows / $args['limit'] )
		);
		$this->server->add_pagination_headers( $query );

		return $results;
	}

	/**
	 * Get a single queue item
	 *
	 * @since 4.1
	 * @param int   $id
	 * @param array $fields
	 * @return array
	 */
	public function get_queue_item( $id, $fields = array() ) {

		$id = $this->validate_request( $id );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$item   = new FUE_Sending_Queue_Item( $id );
		$email  = Follow_Up_Emails::instance()->api->FUE_API_Emails->get_email( $item->email_id );

		if ( is_wp_error( $email ) ) {
			$email = null;
		}

		$data = array(
			'id'            => $item->id,
			'user_id'       => $item->user_id,
			'user_email'    => $item->user_email,
			'order_id'      => $item->order_id,
			'product_id'    => $item->product_id,
			'email_id'      => $item->email_id,
			'email'         => $email,
			'date_scheduled'=> $this->server->format_datetime( $item->send_on, true ),
			'is_cart'       => $item->is_cart,
			'is_sent'       => $item->is_sent,
			'date_sent'     => $this->server->format_datetime( $item->date_sent, true ),
			'email_trigger' => $item->email_trigger,
			'meta'          => $item->meta
		);

		return array( 'item' => apply_filters( 'fue_api_queue_response', $data, $item, $fields, $this->server ) );
	}

	/**
	 * Create a new queue item
	 *
	 * @since 4.1
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function create_queue_item( $data ) {

		if ( !current_user_can( 'manage_follow_up_emails' ) ) {
			return new WP_Error( 'fue_api_user_cannot_access_queue', __( 'You do not have permission to access this resource', 'follow_up_emails' ), array( 'status' => 400 ) );
		}

		$defaults = array(
			'user_id'       => 0,
			'user_email'    => '',
			'order_id'      => 0,
			'product_id'    => 0,
			'email_id'      => '',
			'send_date'     => '', // UTC RFC339
			'is_cart'       => 0,
			'is_sent'       => 1,
			'date_sent'     => '', // UTC RFC339
			'email_trigger' => '',
			'meta'          => array(),
			'status'        => 0 // 0=suspended; 1=active
		);

		$data   = wp_parse_args( $data, $defaults );
		$data   = apply_filters( 'fue_api_create_queue_data', $data, $this );

		// basic data validation
		if ( empty( $data['user_email'] ) ) {
			return new WP_Error( 'fue_api_missing_callback_param', sprintf( __( 'Missing parameter %s', 'follow_up_emails' ), 'user_email' ), array( 'status' => 400 ) );
		}

		if ( empty( $data['email_id'] ) ) {
			return new WP_Error( 'fue_api_missing_callback_param', sprintf( __( 'Missing parameter %s', 'follow_up_emails' ), 'email_id' ), array( 'status' => 400 ) );
		} else {
			$email = new FUE_Email( $data['email_id'] );

			if ( !$email->exists() ) {
				return new WP_Error( 'fue_api_invalid_email_id', __('Invalid email ID', 'follow_up_emails'), array('status' => 400 ) );
			}
		}

		$item   = new FUE_Sending_Queue_Item();
		$item->user_id          = $data['user_id'];
		$item->user_email       = $data['user_email'];
		$item->order_id         = $data['order_id'];
		$item->product_id       = $data['product_id'];
		$item->email_id         = $data['email_id'];
		$item->send_on          = get_date_from_gmt( $this->server->parse_datetime( $data['send_date'] ), 'U' );
		$item->is_cart          = ( $data['is_cart'] == 1 ) ? 1 : 0;
		$item->is_sent          = ( $data['is_sent'] == 1 ) ? 1 : 0;
		$item->date_sent        = get_date_from_gmt( $this->server->parse_datetime( $data['date_sent'] ) );
		$item->email_trigger    = $data['email_trigger'];
		$item->meta             = ( !is_array( $data['meta'] ) ) ? array() : $data['meta'];
		$item->status           = ( $data['status'] == 0 ) ? 0 : 1;

		$id = $item->save();


		// Checks for an error in the queue entry creation.
		if ( is_wp_error( $id ) ) {
			return new WP_Error( 'fue_api_cannot_create_queue', $id->get_error_message(), array( 'status' => 400 ) );
		}

		do_action( 'fue_api_created_queue', $id, $data );

		// if the queue's send date is in the future and if the status is 1 (active),
		// schedule if for sending
		if ( time() < $item->send_on && $item->status == 1 ) {
			$this->scheduler->schedule_email( $id, $item->send_on );
		}

		$this->server->send_status( 201 );

		return $this->get_queue_item( $id );
	}

	/**
	 * Edit a queue item
	 *
	 * @since 4.1
	 * @param int   $id
	 * @param array $data
	 * @return array
	 */
	public function edit_queue_item( $id, $data ) {
		// validate the ID
		$id = $this->validate_request( $id );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$item = new FUE_Sending_Queue_Item( $id );

		$data = apply_filters( 'fue_api_edit_queue_data', $data, $this );
		$schedule_changed = false;

		foreach ( $data as $field => $value ) {
			if ( $field == 'send_date' ) {
				$item->send_on = get_date_from_gmt( $this->server->parse_datetime( $value ), 'U' );
				$schedule_changed = true;
			} elseif ( $field == 'date_sent' ) {
				$item->date_sent = get_date_from_gmt( $this->server->parse_datetime( $value ) );
			} elseif ( $field == 'email_id' ) {
				$email = new FUE_Email( $value );

				if ( !$email->exists() ) {
					return new WP_Error( 'fue_api_invalid_email_id', __('Invalid email ID', 'follow_up_emails'), array('status' => 400 ) );
				}
			} else {
				if ( property_exists( $item, $field ) ) {
					$item->$field = $value;
				}
			}
		}

		$id = $item->save();

		if ( $schedule_changed && $item->status == 1 ) {
			// update the action-scheduler schedule
			$this->scheduler->unschedule_email( $item->id );
			$this->scheduler->schedule_email( $item->id, $item->send_on );
		}

		// Checks for an error in the saving process
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		do_action( 'fue_api_edited_queue', $id, $data );

		$this->server->send_status( 201 );

		return $this->get_queue_item( $id );
	}

	/**
	 * Delete a queue item
	 *
	 * @since 4.1
	 * @param int $id
	 * @return array
	 */
	public function delete_queue_item( $id ) {
		$id = $this->validate_request( $id );

		// Return the validate error.
		if ( is_wp_error( $id ) ) {
			return $id;
		}

		do_action( 'fue_api_delete_queue', $id, $this );

		$this->scheduler->delete_item( $id );

		$this->server->send_status( '202' );

		return array( 'message' => __( 'Deleted queue item', 'follow_up_emails' ) );
	}

	/**
	 * Validate the request by checking:
	 *
	 * 1) the ID is a valid integer
	 * 2) the ID returns a valid queue item
	 * 3) the current user has the proper permissions to read/edit/delete the object
	 *
	 * @since 4.1
	 * @param string|int $id the post ID
	 * @param string $type Unused
	 * @param string $context Unused
	 * @return int|WP_Error valid post ID or WP_Error if any of the checks fails
	 */
	protected function validate_request( $id, $type = null, $context = null ) {

		$id = absint( $id );

		// validate ID
		if ( empty( $id ) )
			return new WP_Error( "fue_api_invalid_queue_id", __( 'Invalid Queue ID', 'follow_up_emails' ), array( 'status' => 404 ) );


		$item = new FUE_Sending_Queue_Item( $id );

		// check that the resource exists
		if ( !$item->exists() ) {
			return new WP_Error( 'fue_api_invalid_queue_id', __('Invalid Queue ID', 'follow_up_emails'), array( 'status' => 404 ) );
		}

		if ( !current_user_can('manage_follow_up_emails') ) {
			return new WP_Error( "fue_api_user_cannot_access_queue", __( 'You do not have permission to access this resource', 'follow_up_emails' ), array( 'status' => 401 ) );
		}


		return $id;
	}

}