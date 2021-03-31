<?php
/**
 * WordPress Admin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers;

defined( 'ABSPATH' ) or exit;

/**
 * The Messages controller class.
 *
 * @since 1.0.0
 */
class Messages {


	/** @var string the unread status */
	const STATUS_UNREAD = 'unread';

	/** @var string the read status */
	const STATUS_READ = 'read';

	/** @var string the deleted status */
	const STATUS_DELETED = 'deleted';

	/** @var string the message status meta key */
	const META_KEY_STATUS = '_skyverge_wordpress_plugin_admin_message_status';


	/** @var string route namespace */
	protected $namespace = 'skyverge/v1';

	/** @var string route */
	protected $rest_route = 'messages';


	/**
	 * Registers the REST routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace, "/{$this->rest_route}", [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace, "/{$this->rest_route}", [
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_items' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_route . '/(?P<message_id>[a-z0-9-_]+)',
			[
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_route . '/(?P<message_id>[a-z0-9-_]+)',
			[
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				],
			]
		);
	}


	/**
	 * Gets the item schema.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'messages',
			'type'       => 'object',
			'properties' => [
				'id'          => [
					'description' => __( 'Unique message ID', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'publishedAt' => [
					'description' => __( 'Publish date', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'expiredAt'   => [
					'description' => __( 'Expiration date', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'status'      => [
					'description' => __( 'Message status for the current user', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'enum'        => [ 'read', 'unread' ],
					'readonly'    => true,
				],
			],
		];
	}


	/**
	 * Checks if the user has permission to get items.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function get_items_permissions_check() {

		return current_user_can( 'manage_woocommerce' );
	}


	/**
	 * Gets the formatted message items.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_items() {

		try {
			$messages = $this->get_all_messages();
		} catch ( \Exception $exception ) {
			return new \WP_Error( 'error_fetching_messages', $exception->getMessage() );
		}

		if ( ! empty( $messages['messages'] ) ) {
			$messages = $messages['messages'];
		}

		foreach ( $messages as $key => $message ) {

			// check if the message is expired
			if ( $this->is_message_expired( $message ) ) {

				unset( $messages[ $key ] );
				continue;
			}

			// get the message status for the current user
			$status = $this->get_message_status_for_user( $message['id'] );

			// check if the message was deleted
			if ( 'deleted' === $status ) {

				unset( $messages[ $key ] );
				continue;

			} else {

				$messages[ $key ]['status'] = $status;
			}
		}

		return rest_ensure_response( [ 'messages' => array_values( $messages ) ] );
	}


	/**
	 * Gets all of the available messages.
	 *
	 * These messages will be filtered on the frontend based on status and display rules.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function get_all_messages() {

		$data_url = 'https://dashboard-assets.skyverge.com/messages/messages.json';

		/**
		 * Filters the messages JSON file URL.
		 *
		 * @since 1.0.0
		 *
		 * @param string $data_url default URL
		 */
		$data_url = (string) apply_filters( 'sv_wordpress_plugin_admin_messages_data_url', $data_url );

		$response = wp_remote_get( $data_url );

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( ! is_string( $body ) || 200 !== $code ) {
			throw new \Exception( 'Could not retrieve remote messages data', $code ? $code : 404 );
		}

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			throw new \Exception( 'Remote messages data is invalid', 500 );
		}

		return $data;
	}


	/**
	 * Determines if a message is expired.
	 *
	 * Checks the message data for an explicit expiration date.
	 * If none exists, then it calculates the expiration: 30 days from the publish date.
	 *
	 * @since 1.0.0
	 *
	 * @param array $message_data message data
	 * @return bool
	 */
	protected function is_message_expired( array $message_data ) {

		// get explicitly defined expiration date
		if ( ! empty( $message_data['expiredAt'] ) ) {
			try {
				$expiration_date = new \DateTime( $message_data['expiredAt'] );
			} catch ( \Exception $e ) {}
		}

		// calculate expiration date based on the publish date
		if ( empty( $expiration_date ) && ! empty( $message_data['publishedAt'] ) ) {
			try {
				$publish_date = new \DateTime( $message_data['publishedAt'] );

				$expiration_date = $publish_date->add( new \DateInterval( 'P30D' ) );
			} catch ( \Exception $e ) {}
		}

		// evaluate expiration date
		return ! empty( $expiration_date ) && new \DateTime() > $expiration_date;
	}


	/**
	 * Gets the status of the given message ID for the current API user.
	 *
	 * Defaults to "unread" if the meta is not set. This method does not force a set of known statuses and will return
	 * whatever is stored in user meta so that new statuses can be added and controlled by the frontend in the future.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message_id message ID to check
	 * @return string
	 */
	protected function get_message_status_for_user( $message_id ) {

		$status = self::STATUS_UNREAD;

		if ( $stored_status = get_user_meta( get_current_user_id(), self::META_KEY_STATUS . "_{$message_id}", true ) ) {
			$status = $stored_status;
		}

		return $status;
	}


	/**
	 * Checks if the user has permission to update an item.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function update_item_permissions_check() {

		return current_user_can( 'manage_woocommerce' );
	}


	/**
	 * Updates an item.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {

		$required_fields = [
			'message_id',
			'status',
		];

		foreach ( $required_fields as $required_field ) {

			if ( empty( $request->get_param( $required_field ) ) ) {
				return new \WP_Error( 'required_field_missing', __( 'A required field is missing or empty', 'sv-wordpress-plugin-admin' ), [ 'field' => $required_field ] );
			}
		}

		$message_id = sanitize_text_field( $request->get_param( 'message_id' ) );
		$status     = sanitize_text_field( $request->get_param( 'status' ) );

		update_user_meta( get_current_user_id(), self::META_KEY_STATUS . "_{$message_id}", $status );

		$response_data = [
			'id'     => $message_id,
			'status' => $status,
		];

		return rest_ensure_response( $response_data );
	}


	/**
	 * Updates multiple items.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_items( $request ) {

		$required_fields = [
			'ids',
			'status',
		];

		foreach ( $required_fields as $required_field ) {

			if ( empty( $request->get_param( $required_field ) ) ) {
				return new \WP_Error( 'required_field_missing', __( 'A required field is missing or empty', 'sv-wordpress-plugin-admin' ), [ 'field' => $required_field ] );
			}
		}

		$message_ids = $request->get_param( 'ids' );
		$status      = sanitize_text_field( $request->get_param( 'status' ) );

		if ( ! is_array( $message_ids ) ) {

			$message_ids = [ $message_ids ];
		}

		foreach ( $message_ids as $message_id ) {

			$message_id = sanitize_text_field( $message_id );

			update_user_meta( get_current_user_id(), self::META_KEY_STATUS . "_{$message_id}", $status );
		}

		$response_data = [
			'ids'    => $message_ids,
			'status' => $status,
		];

		return rest_ensure_response( $response_data );
	}


	/**
	 * Checks if the user has permission to delete an item.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function delete_item_permissions_check() {

		return current_user_can( 'manage_woocommerce' );
	}


	/**
	 * Deletes an item.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request full details about the request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_item( $request ) {

		$message_id = sanitize_text_field( $request->get_param( 'message_id' ) );

		update_user_meta( get_current_user_id(), self::META_KEY_STATUS . "_{$message_id}", self::STATUS_DELETED );

		$response_data = [
			'id'     => $message_id,
			'status' => self::STATUS_DELETED,
		];

		return rest_ensure_response( $response_data );
	}


}
