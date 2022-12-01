<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships REST API controller.
 *
 * @since 1.11.0
 */
abstract class Controller extends \WC_REST_Posts_Controller {


	/** @var string REST API version supported by the controller, e.g. v1, v2... */
	protected $version = 'v1';

	/** @var string REST API object name (e.g. Membership Plan or User Membership) */
	protected $object_name;

	/** @var string default datetime format for datetime payload properties */
	protected $datetime_format = 'Y-m-d\TH:i:s';


	/**
	 * Memberships object REST API controller constructor.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {

		$this->public      = false;
		$this->object_name = __( 'Memberships object', 'woocommerce-memberships' );
	}


	/**
	 * Returns the version of the REST API supported.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_version() {

		return $this->version;
	}


	/**
	 * Returns the full REST namespace used by the controller.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_namespace() {

		return $this->namespace;
	}


	/**
	 * Returns the current WooCommerce root namespace.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	protected function get_woocommerce_namespace() {

		return "wc/{$this->version}";
	}


	/**
	 * Returns the REST base appended to the namespace.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_rest_base() {

		return $this->rest_base;
	}


	/**
	 * Returns the related post type.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_post_type() {

		return $this->post_type;
	}


	/**
	 * Gets a controller's related post object matching the current post type given an object identifier.
	 *
	 * @since 1.13.0
	 *
	 * @param int $id post ID
	 * @return \WP_Post|null
	 */
	protected function get_post_object( $id ) {

		$post = $id ? get_post( $id ) : null;

		return $post instanceof \WP_Post && $post->post_type === $this->post_type ? $post : null;
	}


	/**
	 * Gets a controller's related object given an identifier.
	 *
	 * @since 1.13.0
	 *
	 * @param mixed $id object identifier
	 * @return null|object
	 */
	abstract protected function get_object( $id );


	/**
	 * Gets a customer user by a common identifier.
	 *
	 * @since 1.13.0
	 *
	 * @param int|string|\WP_User $id user ID, email, login or object
	 * @return \WP_User|null
	 */
	protected function get_customer_user( $id ) {

		$customer = $id;

		if ( is_numeric( $id ) ) {
			$customer = get_user_by( 'id', (int) $id  );
		} elseif ( is_string( $id ) ) {
			if ( is_email( $id ) ) {
				$customer = get_user_by( 'email', $id );
			} else {
				$customer = get_user_by( 'login', $id );
			}
		}

		return $customer instanceof \WP_User ? $customer : null;
	}


	/**
	 * Prepares a collection with paginated results.
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_REST_Request $request
	 * @param array $collection collection of response objects
	 * @param \WP_Query $posts_query query results
	 * @param array $query_args
	 * @return \WP_REST_Response
	 */
	protected function prepare_response_collection_paginated( $request, $collection, $posts_query, $query_args ) {

		$page        = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 ) {

			unset( $query_args['paged'] );

			$count_query = new \WP_Query();

			$count_query->query( $query_args );

			$total_posts = $count_query->found_posts;
		}

		$max_pages = (int) ceil( $total_posts / (int) $query_args['posts_per_page'] );
		$response  = rest_ensure_response( $collection );

		$response->header( 'X-WP-Total',      $total_posts );
		$response->header( 'X-WP-TotalPages', $max_pages );

		$request_params = $request->get_query_params();

		if ( ! empty( $request_params['filter'] ) ) {
			unset( $request_params['filter']['posts_per_page'], $request_params['filter']['paged'] );
		}

		$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {

			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );

			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $page ) {

			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}


	/**
	 * Gets an error response for requests containing an invalid ID.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post|int|mixed $post post object of the unexpected type
	 * @return \WP_Error
	 */
	protected function get_invalid_id_error_response( $post ) {

		if ( $post instanceof \WP_Post ) {
			/* translators: Placeholders: %1$s - post ID, %2$s - membership object name (e.g. Membership Plan or User Membership) */
			$error_message = sprintf( __( 'Object with ID %1$s is not a valid %2$s.', 'woocommerce-memberships' ), (int) $post->ID, $this->object_name );
		} else {
			/* translators: Placeholder: %s - membership object name (e.g. Membership Plan or User Membership) */
			$error_message = sprintf( __( '%s invalid or not found.', 'woocommerce-memberships' ), $this->object_name );
		}

		return new \WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", $error_message, array( 'status' => 404 ) );
	}


	/**
	 * Gets a memberships response item for REST API consumption.
	 *
	 * @see \WC_REST_Posts_Controller::get_item()
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return array|\WP_Error|\WP_REST_Response response object or error object
	 */
	public function get_item( $request ) {

		$post = $this->get_post_object( (int) $request['id'] );

		if ( ! $post ) {
			$response = $this->get_invalid_id_error_response( $post );
		} else {
			$response = $this->prepare_item_for_response( $post, $request );
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Deletes a memberships item upon REST API request.
	 *
	 * @see \WC_REST_Posts_Controller::delete_item()
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_Error|\WP_REST_Response response object or error object
	 */
	public function delete_item( $request ) {

		$post = $this->get_post_object( (int) $request['id'] );

		if ( ! $post ) {

			$response = $this->get_invalid_id_error_response( $post );

		} else {

			$request->set_param( 'context', 'edit' );

			$object   = $this->get_object( $post->ID );
			$previous = $this->prepare_item_for_response( $post, $request );
			$success  = (bool) wp_delete_post( $post->ID, true );

			if ( $success ) {

				$response = new \WP_REST_Response( array(
					'deleted'  => $success,
					'previous' => $previous->get_data(),
				) );

				/**
				 * Fires after a membership object is deleted via the REST API.
				 *
				 * @since 1.13.0
				 *
				 * @param \WP_Post|object $post the related object or post object
				 * @param \WP_REST_Response $response the response data
				 * @param \WP_REST_Request $request the request sent to the API
				 */
				do_action( "woocommerce_rest_delete_{$this->post_type}_object", null !== $object ? $object : $post, $response, $request );

			} else {

				/* translators: Placeholder: %s - object name (e.g. "User Membership") */
				$response = new \WP_Error( "woocommerce_api_cannot_delete_{$this->post_type}", sprintf( __( 'This %s cannot be deleted.', 'woocommerce-memberships' ), $this->object_name ), array( 'status' => 500 ) );
			}
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Prepares memberships object meta data for a response item.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_User_Membership|\WC_Memberships_Membership_Plan $object membership object
	 * @return array associative array of formatted meta data
	 */
	protected function prepare_item_meta_data( $object ) {
		global $wpdb;

		$formatted = array();
		$raw_meta  = $wpdb->get_results( $wpdb->prepare("
			SELECT * FROM $wpdb->postmeta
			WHERE post_id  = %d
		", $object->get_id() ) );

		if ( ! empty( $raw_meta ) ) {

			$post_type        = $this->get_post_type();
			$wp_internal_keys = array(
				'_edit_lock',
				'_edit_last',
				'_wp_old_slug',
			);

			if ( 'wc_membership_plan' === $post_type ) {
				$object_name = 'membership_plan';
			} elseif ( 'wc_user_membership' === $post_type ) {
				$object_name = 'user_membership';
			} else {
				$object_name = $post_type;
			}

			/**
			 * Filters the list of meta data keys to exclude from REST API responses.
			 *
			 * @since 1.11.0
			 *
			 * @param array $excluded_keys keys to exclude from memberships item meta data list
			 * @param \WC_Memberships_User_Membership|\WC_Memberships_Membership_Plan $object memberships object
			 */
			$excluded_keys = apply_filters( "wc_memberships_rest_api_{$object_name}_excluded_meta_keys", array_merge( $object->get_meta_keys(), $wp_internal_keys ), $object );

			foreach( $raw_meta as $meta_object ) {

				if ( empty( $excluded_keys ) || ! in_array( $meta_object->meta_key, $excluded_keys, true ) ) {

					$formatted[] = array(
						'id'    => (int) $meta_object->meta_id,
						'key'   => (string) $meta_object->meta_key,
						'value' => $meta_object->meta_value,
					);
				}
			}
		}

		return $formatted;
	}


	/**
	 * Gets the date format to be used in datetime properties.
	 *
	 * @since 1.19.1
	 *
	 * @return string
	 */
	protected function get_datetime_format() {

		return $this->datetime_format;
	}


}
