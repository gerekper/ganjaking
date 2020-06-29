<?php
/**
 * MailChimp for WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships-mailchimp/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Memberships\MailChimp\API\Request;
use SkyVerge\WooCommerce\Memberships\MailChimp\API\Response;

defined( 'ABSPATH' ) or exit;

/**
 * Main MailChimp API handler.
 *
 * @since 1.0.0
 */
class API extends Framework\SV_WC_API_Base {


	/** @var string MailChimp API Key */
	private $api_key;

	/** @var bool caches the service ping status */
	private $status_ping;

	/** @var array cached merge fields by audience list ID and query args */
	private $merge_fields = array();

	/** @var string[] cached notices sent to admin screens */
	private $admin_notices = array();


	/**
	 * Sets up the API handler.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->api_key          = $this->get_plugin()->get_api_key();
		$this->response_handler = "\SkyVerge\WooCommerce\Memberships\MailChimp\API\Response";
	}


	/**
	 * Returns the plugin instance (implements parent method).
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin main instance
	 */
	protected function get_plugin() {

		return wc_memberships_mailchimp();
	}


	/**
	 * Returns the MailChimp API Key to be used with MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @return null|string
	 */
	public function get_api_key() {

		return $this->api_key;
	}


	/**
	 * Tests whether the API key is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param null|string $api_key API key to check: if not set will test the one stored in option
	 * @return bool
	 */
	public function is_api_key_valid( $api_key = null ) {

		$valid   = false;
		$api_key = ! empty( $api_key ) ? $api_key : $this->get_api_key();

		// if there's no datacenter flag, this is likely not a valid key
		if ( '' !== $api_key && is_string( $api_key ) && ( $valid = (bool) strpos( $api_key, '-' ) ) ) {

			$valid = $this->ping_service( $api_key );

			set_transient( wc_memberships_mailchimp()->get_connected_status_transient_key(), $valid ? 1 : 0, DAY_IN_SECONDS );
		}

		return $valid;
	}


	/**
	 * Get the API URL.
	 *
	 * MailChimp determines the datacenter of the URL to connect to based on a code in the customer's API key.
	 * @link https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key API key to gather request URI for
	 * @return string
	 */
	public function get_api_url( $api_key = '' ) {

		$request_uri = 'https://<dc>.api.mailchimp.com/3.0';
		$api_key     = $api_key ? $api_key : $this->get_api_key();

		if ( ! empty( $api_key ) && is_string( $api_key ) ) {

			$datacenter  = substr( $api_key, strrpos( $api_key, '-' ) + 1 );
			$request_uri = str_replace( '<dc>', $datacenter, $request_uri );
		}

		return $request_uri;
	}


	/**
	 * Implements parent method (useful in IDE and testing).
	 *
	 * @since 1.0.0
	 *
	 * @param Request $request request object
	 * @return object|Response response object
	 */
	protected function perform_request( $request ) {

		return parent::perform_request( $request );
	}


	/**
	 * Perform a request to MailChimp API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args optional array of arguments
	 * @return Request
	 * @throws \Exception
	 */
	protected function get_new_request( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'method'  => 'GET',
			'path'    => '/',
			'api_key' => '',
		) );

		$api_key = ! empty( $args['api_key'] ) ? $args['api_key'] : $this->get_api_key();

		if ( ! $api_key ) {
			throw new Framework\SV_WC_API_Exception( 'No API key configured' );
		}

		$this->request_uri = $this->get_api_url( $api_key );

		// MailChimp needs the API key to be passed in headers when performing requests other than GET
		$this->request_headers['Authorization'] = 'apikey ' . $api_key;

		return new Request( $args['method'], $args['path'] );
	}


	/**
	 * Logs an error message to the plugin's log if debug mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message the message to log
	 */
	private function log_error_message( $message ) {

		if ( is_string( $message ) && $this->get_plugin()->is_debug_mode_enabled() ) {

			$this->get_plugin()->log( $message );
		}
	}


	/**
	 * Checks the response for errors after it's been parsed.
	 *
	 * @see \SkyVerge\WooCommerce\PluginFramework\v5_5_0\SV_WC_API_Base::do_post_parse_response_validation() parent method implementation
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function do_post_parse_response_validation() {

		$response = $this->get_response();

		if ( $response->has_api_error() && wc_memberships_mailchimp()->is_debug_mode_enabled() ) {

			$message = $response->get_error_title();

			if ( $response->get_error_detail() ) {
				$message .= ': ' . $response->get_error_detail();
			}

			throw new Framework\SV_WC_API_Exception( $message, $response->get_error_status() );
		}

		return true;
	}


	/**
	 * Pings MailChimp service for health status and connectivity.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/ping/
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key a specific API key to ping
	 * @param bool $force whether to discard the value cached in memory (default false)
	 * @return bool
	 */
	public function ping_service( $api_key = '', $force = false ) {

		if ( true === $force || null === $this->status_ping ) {

			try {

				$request = $this->get_new_request( array(
					'path'    => '/ping',
					'api_key' => $api_key,
				) );

				$response = $this->perform_request( $request );

				$this->status_ping = ! $response->has_api_error();

			} catch ( \Exception $e ) {

				$this->log_error_message( $e->getMessage() );

				$this->status_ping = false;
			}
		}

		return $this->status_ping;
	}


	/**
	 * Returns an object from MailChimp API (helper method).
	 *
	 * @since 1.0.0
	 *
	 * @param string $path API path for the object
	 * @param array $params request query parameters
	 * @return null|\stdClass
	 */
	private function get_object( $path, array $params ) {

		$object = null;

		try {

			$request = $this->get_new_request( array( 'path' => $path ) );

			$request->set_params( $params );

			$response = $this->perform_request( $request );

			if ( ! empty( $response->response_data ) && ! $response->has_api_error() ) {
				$object = $response->response_data;
			}

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );
		}

		return $object;
	}


	/**
	 * Returns an object collection from MailChimp API (helper method).
	 *
	 * @since 1.0.0
	 *
	 * @param string $object_name the object name that matches a response property
	 * @param string $path API path for the object
	 * @param array $params request query parameters
	 * @return \stdClass[] array of objects
	 */
	private function get_objects( $object_name, $path, array $params ) {

		$objects = array();

		try {

			$request = $this->get_new_request( array( 'path' => $path ) );

			$request->set_params( $params );

			$response = $this->perform_request( $request );

			if ( isset( $response->response_data->$object_name ) && ! $response->has_api_error() ) {
				$objects = $response->response_data->$object_name;
			}

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );
		}

		return $objects;
	}


	/**
	 * Returns MailChimp audience lists.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/
	 *
	 * @since 1.0.0
	 *
	 * @param array $args optional request arguments
	 * @return \stdClass[] array of objects
	 */
	public function get_lists( $args = array() ) {

		/** @see MailChimp pagination handling https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#pagination */
		$args = wp_parse_args( $args, array(
			'count'  => 9999, // just a very big number, unlikely anyone has this amount of audience lists or MailChimp allowing it
			'offset' => 0,
		) );

		return $this->get_objects( 'lists', '/lists', $args );
	}


	/**
	 * Returns a MailChimp audience list object.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID
	 * @param array $args optional request arguments
	 * @return null|\stdClass
	 */
	public function get_list( $list_id, $args = array() ) {

		return $this->get_object( "/lists/{$list_id}", $args );
	}


	/**
	 * Returns the merge tags of a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience to get merge tags for
	 * @param array $args optional request arguments
	 * @return \stdClass[] array of objects
	 */
	public function get_list_merge_fields( $list_id, $args = array() ) {

		/** @see MailChimp pagination handling https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#pagination */
		$args = wp_parse_args( $args, array(
			'count'  => 9999, // just a very big number, unlikely anyone has this amount of merge fields or MailChimp allowing it
			'offset' => 0,
		) );

		$list_id   = is_object( $list_id ) && isset( $list_id->id ) ? $list_id->id : $list_id;
		$cache_key = empty( $args ) ? 'no_args' : http_build_query( $args );

		if ( ! isset( $this->merge_fields[ $list_id ][ $cache_key ] ) ) {
			$this->merge_fields[ $list_id ][ $cache_key ] = $this->get_objects( 'merge_fields', "/lists/{$list_id}/merge-fields", $args );
		}

		return $this->merge_fields[ $list_id ][ $cache_key ];
	}


	/**
	 * Returns an individual MailChimp audience merge field object by its ID.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of the audience the merge field belongs to
	 * @param string $merge_field_id the merge field ID
	 * @param array $args optional request arguments
	 * @return null|\stdClass
	 */
	public function get_list_merge_field_by_id( $list_id, $merge_field_id, $args = array() ) {

		$found_merge_field = null;
		$merge_fields      = $this->get_list_merge_fields( $list_id, $args );

		if ( ! empty( $merge_fields ) ) {

			foreach ( $merge_fields as $merge_field ) {

				// keep this check loose as types might not match
				if (    isset( $merge_field->merge_id, $merge_field->tag )
				     && $merge_field_id == $merge_field->merge_id ) {

					$found_merge_field = $merge_field;
				}
			}
		}

		return $found_merge_field;
	}


	/**
	 * Returns an individual MailChimp audience merge field object by its tag.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of the audience the merge tag belongs to
	 * @param string $merge_field_tag the merge field tag
	 * @param array $args optional request arguments
	 * @return null|\stdClass
	 */
	public function get_list_merge_field_by_tag( $list_id, $merge_field_tag, $args = array() ) {

		$found_merge_field = null;
		$merge_fields      = $this->get_list_merge_fields( $list_id, $args );

		if ( ! empty( $merge_fields ) ) {

			foreach ( $merge_fields as $merge_field ) {

				// keep this check loose as types might not match
				if (    isset( $merge_field->merge_id, $merge_field->tag )
				     && $merge_field_tag == $merge_field->tag ) {

					$found_merge_field = $merge_field;
					break;
				}
			}
		}

		return $found_merge_field;
	}


	/**
	 * Checks whether a merge field exists for a given MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of the audience the merge tag belongs to
	 * @param string $merge_id the merge tag ID
	 * @param array $args optional request arguments
	 * @return false|int|string returns false if doesn't exist or the merge field tag if found (normally a string)
	 */
	public function merge_field_id_exists( $list_id, $merge_id, $args = array() ) {

		$merge_field = $this->get_list_merge_field_by_id( $list_id, $merge_id, $args );

		return is_object( $merge_field ) && isset( $merge_field, $merge_field->merge_id, $merge_field->tag ) ? $merge_field->tag : false;
	}


	/**
	 * Checks whether a merge field tag exists in a MailChimp audience, without knowing its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of a MailChimp audience
	 * @param string $merge_tag a merge field tag
	 * @param array $args optional request arguments
	 * @return false|string|int returns false if doesn't exist or the merge field ID if found (normally an integer)
	 */
	public function merge_field_tag_exists( $list_id, $merge_tag, $args = array() ) {

		$merge_field = $this->get_list_merge_field_by_tag( $list_id, $merge_tag, $args );

		return is_object( $merge_field ) && isset( $merge_field, $merge_field->merge_id, $merge_field->tag ) ? $merge_field->merge_id : false;
	}


	/**
	 * Creates a merge tag for a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience to create a merge tag for
	 * @param array $data Merge tag data
	 * @return null|int|string Returns null upon failure or the merge tag ID on success
	 */
	public function add_list_merge_field( $list_id, array $data ) {

		$result = null;

		try {

			$request = $this->get_new_request( array(
				'method' => 'POST',
				'path'   => "/lists/{$list_id}/merge-fields",
			) );

			$request->set_data( $data );

			$response = $this->perform_request( $request );

			// if merge field already exists response_data->status will be 400 and carry an error message in the response detail
			if ( isset( $response->response_data->merge_id ) ) {
				$result = $response->response_data->merge_id;
			} elseif ( $response->has_api_error() ) {
				$this->log_error_message( $response->get_error_detail() );
			}

		} catch ( \Exception $e ) {

			$message = $e->getMessage();

			if ( ! empty( $message ) && 400 === $e->getCode() && ! in_array( $message, $this->admin_notices, false ) ) {

				$plan_name = ! empty( $data['name'] ) ? '"' . $data['name'] . '"' : '';
				$tag_name  = ! empty( $data['tag'] )  ? '"' . $data['tag']  . '"' : '';

				$this->get_plugin()->get_message_handler()->add_error(
					/* translators: Placeholder: $1$s - merge tag text, $2$s - plan name, %3$s - error message text */
					sprintf( __( 'An error occurred with MailChimp while trying to set a merge tag %1$s for the plan %2$s: "%3$s"', 'woocommerce-memberships-mailchimp' ), $tag_name, $plan_name, $message )
				);

				$this->admin_notices[] = $message;
			}

			$this->log_error_message( $e->getMessage() );
		}

		// clear object cache, just in case
		$this->merge_fields = array();

		return is_string( $result ) || is_numeric( $result ) ? $result : null;
	}


	/**
	 * Adds a membership plan merge field.
	 *
	 * @since 1.0.0
	 *
	 * @param int $list_id MailChimp audience ID
	 * @param \WC_Memberships_Membership_Plan $plan membership plan object
	 * @param string $tag tag to force (optional, will default to modified plan slug)
	 * @return null|string the merge field tag on success, null on failure parsing the tag
	 */
	public function create_plan_merge_field( $list_id, \WC_Memberships_Membership_Plan $plan, $tag = '' ) {

		$tag    = '' !== $tag && is_string( $tag ) ? $tag : MailChimp_Lists::parse_merge_tag( $plan->get_slug() );
		$result = null;

		if ( ! empty( $tag ) && ! $this->merge_field_tag_exists( $list_id, $tag ) ) {

			$result = $this->add_list_merge_field( $list_id, array(
				'tag'    => $tag,
				'name'   => $plan->get_name(),
				'type'   => 'text',
				'public' => false,
			) );
		}

		return is_numeric( $result ) || is_string( $result ) ? $result : null;
	}


	/**
	 * Adds a membership status merge field.
	 *
	 * @since 1.0.0
	 *
	 * @param int $list_id MailChimp audience ID
	 * @param string $status the user membership status
	 * @param string $tag tag to force (optional, will default to modified plan slug)
	 * @return null|string false on failure, MailChimp merge ID on success
	 */
	public function create_status_merge_field( $list_id, $status, $tag = '' ) {

		$statuses    = wc_memberships_get_user_membership_statuses();
		$status_key  = 'wcm-' . $status;
		$status_name = isset( $statuses[ $status_key ]['label'] ) ? $statuses[ $status_key ]['label'] : ucwords( $status );
		$result      = null;

		if ( ! empty( $tag ) && ! $this->merge_field_tag_exists( $list_id, $tag ) ) {

			$result = $this->add_list_merge_field( $list_id, array(
				'tag'    => '' !== $tag && is_string( $tag ) ? $tag : MailChimp_Lists::parse_merge_tag( $tag ),
				'name'   => $status_name,
				'type'   => 'text',
				'public' => false,
			) );
		}

		return is_numeric( $result ) || is_string( $result ) ? $result : null;
	}


	/**
	 * Removes a merge tag from a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/merge-fields/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the list to delete the merge tag from
	 * @param string $merge_id ID of the merge tag to delete
	 * @return bool
	 */
	public function delete_list_merge_field( $list_id, $merge_id ) {

		try {

			$request = $this->get_new_request( array(
				'method' => 'DELETE',
				'path'   => "/lists/{$list_id}/merge-fields/{$merge_id}",
			) );

			$this->perform_request( $request );

			$result = true;

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );

			$result = false;
		}

		// clear object cache, just in case
		$this->merge_fields = array();

		return $result;
	}


	/**
	 * Returns the interest groups for a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience to return interest groups for
	 * @param array $args optional request arguments
	 * @return \stdClass[] array of objects
	 */
	public function get_list_interest_categories( $list_id, $args = array() ) {

		/** @see MailChimp pagination handling https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#pagination */
		$args = wp_parse_args( $args, array(
			'count'  => 9999, // just a very big number, unlikely anyone has this amount of interests or MailChimp allowing it
			'offset' => 0,
		) );

		return $this->get_objects( 'categories', "/lists/{$list_id}/interest-categories", $args );
	}


	/**
	 * Returns an interest group for a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/interests/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience to return interest groups for
	 * @param string $interest_category_id the ID of the interest group to fetch
	 * @param array $args optional request arguments
	 * @return null|\stdClass
	 */
	public function get_list_interest_category( $list_id, $interest_category_id, $args = array() ) {

		return $this->get_object( "/lists/{$list_id}/interest-categories/{$interest_category_id}", $args );
	}


	/**
	 * Returns an array of interests for a MailChimp audience interest category.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/interests/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience to return interest groups for
	 * @param string $interest_category_id the ID of the interest group to fetch
	 * @param array $args optional request arguments
	 * @return \stdClass[]
	 */
	public function get_list_interests_by_category( $list_id, $interest_category_id, $args = array() ) {

		/** @see MailChimp pagination handling https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#pagination */
		$args = wp_parse_args( $args, array(
			'count'  => 9999, // just a very big number, unlikely anyone has this amount of interests or MailChimp allowing it
			'offset' => 0,
		) );

		return $this->get_objects( 'interests', "/lists/{$list_id}/interest-categories/{$interest_category_id}/interests", $args );
	}


	/**
	 * Returns an interest within a MailChimp audience interest category.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/interest-categories/interests/
	 *
	 * @since 1.0.0
	 *
	 * @param $list_id audience ID
	 * @param $interest_category_id interest category ID
	 * @param $interest_id interest ID
	 * @param array $args arguments
	 * @return null|\stdClass
	 */
	public function get_list_interest_by_category( $list_id, $interest_category_id, $interest_id, $args = array() ) {

		return $this->get_object( "/lists/{$list_id}/interest-categories/{$interest_category_id}/interests/{$interest_id}", $args );
	}


	/**
	 * Syncs a set of Memberships users with MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @param MailChimp_List $list the MailChimp audience being used
	 * @param \WP_User[] $users WordPress users
	 * @return \stdClass|bool
	 */
	public function sync_list_members( MailChimp_List $list, array $users ) {

		$list_id    = $list->get_id();
		$operations = array();

		try {

			foreach ( $users as $user ) {

				if ( ! $user instanceof \WP_User || ! $this->get_plugin()->can_sync_user( $user ) ) {
					continue;
				}

				$subscriber_id = $this->parse_subscriber_id( $user );
				$member_data   = $list->get_member_data( $user );

				$operations[] = array(
					'method' => 'PUT',
					'path'   => "/lists/{$list_id}/members/{$subscriber_id}",
					'body'   => json_encode( $member_data ),
				);
			}

			// if there is nothing to sync, bail
			if ( empty( $operations ) ) {

				$result = true;

			} else {

				$request = $this->get_new_request( array(
					'method' => 'POST',
					'path'   => '/batches',
				) );

				$request->set_data( array(
					'operations' => $operations,
				) );

				$result = $this->perform_request( $request )->response_data;
			}

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );

			$result = false;
		}

		return $result;
	}


	/**
	 * Syncs a Memberships user with MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @param MailChimp_List $list the MailChimp audience being used
	 * @param \WP_User $user WordPress user object
	 * @return \stdClass|bool
	 */
	public function sync_list_member( MailChimp_List $list, \WP_User $user ) {

		$success = false;

		if ( $this->get_plugin()->can_sync_user( $user ) ) {

			$subscriber_id = $this->parse_subscriber_id( $user );
			$member_data   = $list->get_member_data( $user );

			if ( $this->is_list_member( $subscriber_id, $list->get_id() ) ) {
				$success = $this->update_list_member( $list->get_id(), $subscriber_id, $member_data );
			} else {
				$success = $this->add_list_member( $list->get_id(), $member_data );
			}
		}

		return $success;
	}


	/**
	 * Updates a MailChimp audience member's user membership status.
	 *
	 * @since 1.0.0
	 *
	 * @param MailChimp_List $list the MailChimp audience being used
	 * @param \WC_Memberships_User_Membership $user_membership user membership object
	 * @return \stdClass|bool
	 */
	public function update_list_member_membership_status( MailChimp_List $list, \WC_Memberships_User_Membership $user_membership ) {

		$success     = false;
		$merge_field = $list->get_plan_merge_field( $user_membership->get_plan_id() );

		if (    ! empty( $merge_field )
		     &&   is_array( $merge_field )
		     &&   $this->get_plugin()->can_sync_user( $user_membership->get_user_id() ) ) {

			$subscriber_id = $this->parse_subscriber_id( $user_membership );
			$status        = $user_membership->get_status();
			$merge_field   = current( $merge_field );
			$status        = empty( $status ) ? 'active' : $status;
			$member_data   = array(
				'merge_fields' => array(
					$merge_field => $status,
				),
			);

			// set the active status merge field
			$is_active_field = $list->get_active_status_merge_field();

			// avoid status transition to cause possible infinite loops
			add_filter( 'wc_memberships_expire_user_membership', '__return_false' );

			if ( ! empty( $is_active_field ) && is_array( $is_active_field ) ) {

				$is_active_field = current( $is_active_field );

				$member_data['merge_fields'][ $is_active_field ] = wc_memberships_is_user_active_member( $user_membership->get_user() ) ? 'yes' : 'no';
			}

			remove_filter( 'wc_memberships_expire_user_membership', '__return_false' );

			$success = $this->update_list_member( $list->get_id(), $subscriber_id, $member_data );
		}

		return $success;
	}


	/**
	 * Returns the subscribers (members) of a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of the audience to pull members from
	 * @param array $args optional request arguments
	 * @return \stdClass[] array of objects
	 */
	public function get_list_members( $list_id, $args = array() ) {

		/** passed arguments should include pagination parameters @see MailChimp pagination handling https://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#pagination */
		return $this->get_objects( 'members', "/lists/{$list_id}/members", $args );
	}


	/**
	 * Returns an individual MailChimp audience subscriber.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the MailChimp audience ID
	 * @param string $subscriber_id a subscriber identifier (email address, hashed value or user object)
	 * @param array $args optional request arguments
	 * @return null|\stdClass
	 */
	public function get_list_member( $list_id, $subscriber_id, $args = array() ) {

		$subscriber_id = $this->parse_subscriber_id( $subscriber_id );

		return $this->get_object( "/lists/{$list_id}/members/{$subscriber_id}", $args );
	}


	/**
	 * Adds a new subscriber to a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID to subscribe a new member to
	 * @param array $member_data the subscriber data
	 * @return \stdClass|bool
	 */
	public function add_list_member( $list_id, array $member_data ) {

		try {

			$request = $this->get_new_request( array(
				'method' => 'POST',
				'path'   => "/lists/{$list_id}/members",
			) );

			$request->set_data( $member_data );

			$response = $this->perform_request( $request );

			$result = $response->response_data;

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );

			$result = false;
		}

		return $result;
	}


	/**
	 * Updates a subscriber of a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the list the subscriber belongs to
	 * @param string $subscriber_id ID of the subscriber to update
	 * @param array $member_data subscriber data to update
	 * @return \stdClass|bool
	 */
	public function update_list_member( $list_id, $subscriber_id, array $member_data ) {

		$subscriber_hash = $this->parse_subscriber_id( $subscriber_id );

		try {

			$request = $this->get_new_request( array(
				'method' => 'PUT',
				'path'   => "/lists/{$list_id}/members/{$subscriber_hash}",
			) );

			$request->set_data( $member_data );

			$response = $this->perform_request( $request );
			$result   = $response->response_data;

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );

			$result = false;
		}

		return $result;
	}


	/**
	 * Unsubscribes a member from a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 * @see API::update_list_member()
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id ID of the audience the subscriber belongs to
	 * @param string $subscriber_id ID of the subscriber to update
	 * @return \stdClass|bool
	 */
	public function unsubscribe_list_member( $list_id, $subscriber_id ) {

		return $this->update_list_member( $list_id, $subscriber_id, array( 'status' => 'unsubscribed' ) );
	}


	/**
	 * Removes a subscriber from a MailChimp audience.
	 *
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the ID of an audience to delete a subscriber from
	 * @param string|\WP_User $subscriber_id the subscriber identifier
	 * @return bool
	 */
	public function delete_list_member( $list_id, $subscriber_id ) {

		$subscriber_hash = $this->parse_subscriber_id( $subscriber_id );
		$request_args    = array(
			'method' => 'DELETE',
			'path'   => "/lists/{$list_id}/members/{$subscriber_hash}"
		);

		try {

			$request = $this->get_new_request( $request_args );
			$success = $this->perform_request( $request );

		} catch ( \Exception $e ) {

			$this->log_error_message( $e->getMessage() );

			$success = false;
		}

		return (bool) $success;
	}


	/**
	 * Checks if a subscriber is an audience member.
	 *
	 * You can pass an optional argument 'status' to check if an audience member exists and has a specific status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subscriber_id a subscriber ID (email address, hashed value or user object)
	 * @param string|null $list_id the MailChimp audience ID: optional, the default list will be used if null
	 * @param array $args optional request arguments
	 * @return bool
	 */
	public function is_list_member( $subscriber_id, $list_id = null, $args = array() ) {

		$list_id     = null !== $list_id ? $list_id : MailChimp_Lists::get_current_list_id();
		$list_member = $this->get_list_member( $list_id, $this->parse_subscriber_id( $subscriber_id ), $args );
		$is_member   = false;

		if ( null !== $list_member && ! empty( $list_member->status ) && (int) $list_member->status !== 404 ) {

			$is_member = true;

			if ( isset( $args['status'] ) ) {
				$is_member = is_array( $args['status'] ) ? in_array( $list_member->status, $args['status'], true ) : $list_member->status === $args['status'];
			}
		}

		return $is_member;
	}


	/**
	 * Gets a specific batch operation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $batch_id batch ID to get
	 * @param array $args API args
	 * @return null|\stdClass
	 */
	public function get_batch_operation( $batch_id, $args = array() ) {

		return $this->get_object( "/batches/{$batch_id}", $args );
	}


	/**
	 * Parses a subscriber ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WP_User|string $subscriber an user object, email address or hashed subscriber ID
	 * @return string should consist of a MD5 hash corresponding to the MailChimp audience subscriber ID
	 */
	private function parse_subscriber_id( $subscriber ) {

		$subscriber_email = $subscriber;

		if ( ! is_string( $subscriber_email ) ) {

			$subscriber_email = '';

			if ( $subscriber instanceof \WC_Memberships_User_Membership ) {
				$subscriber = $subscriber->get_user();
			} elseif ( is_numeric( $subscriber ) ) {
				$subscriber = get_user_by( 'id', $subscriber );
			}

			if ( $subscriber instanceof \WP_User ) {
				$subscriber_email = $subscriber->user_email;
			}
		}

		return is_email( $subscriber_email ) ? $this->get_subscriber_hash( $subscriber_email ) : $subscriber_email;
	}


	/**
	 * Translates a MailChimp audience subscriber's email address into an MD5 hash.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subscriber_email an email address
	 * @return string MD5 hash
	 */
	private function get_subscriber_hash( $subscriber_email ) {

		return md5( strtolower( $subscriber_email ) );
	}


}
