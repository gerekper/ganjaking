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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API\Controller;

use SkyVerge\WooCommerce\Memberships\API\Controller;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plans REST API handler.
 *
 * @since 1.12.0
 */
class Membership_Plans extends Controller {


	/**
	 * Membership Plans REST API constructor.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {

		parent::__construct();

		$this->rest_base   = 'plans';
		$this->post_type   = 'wc_membership_plan';
		$this->object_name = __( 'Membership Plan', 'woocommerce-memberships' );
	}


	/**
	 * Gets a membership plan from a valid identifier.
	 *
	 * @since 1.13.0
	 *
	 * @param string|int|\WP_Post $id membership plan ID or slug
	 * @return \WC_Memberships_Membership_Plan
	 */
	protected function get_object( $id ) {

		$plan = wc_memberships_get_membership_plan( $id );

		return $plan instanceof \WC_Memberships_Membership_Plan ? $plan : null;
	}


	/**
	 * Registers membership plans WP REST API routes.
	 *
	 * @see \SkyVerge\WooCommerce\Memberships\REST_API::register_routes()
	 *
	 * @since 1.11.0
	 */
	public function register_routes() {

		// endpoint: 'wc/v2/memberships/plans/' => list all membership plans
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		), true );

		// endpoint: 'wc/v2/memberships/plans/<id>' => get a specific membership plan
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args'   => array(
				'id' => array(
					'description' => __( 'Unique identifier of a membership plan.', 'woocommerce-memberships' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		), true );
	}


	/**
	 * Returns the available query parameters for collections.
	 *
	 * @since 1.11.0
	 *
	 * @return array associative array
	 */
	public function get_collection_params() {

		$params = parent::get_collection_params();

		unset( $params['order'], $params['orderby'], $params['before'], $params['after'] );

		/**
		 * Filters the membership plans collection params for REST API queries.
		 *
		 * @since 1.11.0
		 *
		 * @param array $params associative array
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_membership_plans_collection_params', $params );
	}


	/**
	 * Prepares query args for items collection query.
	 *
	 * @since 1.11.0
	 *
	 * @param array|\WP_REST_Request $request request object (with array access)
	 * @return array
	 */
	private function prepare_items_query_args( $request ) {

		$query_args      = array(
			'post_type'      => $this->post_type,
			'offset'         => $request['offset'],
			'paged'          => $request['page'],
			'post__in'       => $request['include'],
			'post__not_in'   => $request['exclude'],
			'posts_per_page' => $request['per_page'],
			'orderby'        => isset( $request['orderby'] ) ? $request['orderby'] : 'post_date',
			'order'          => isset( $request['order'] )   ? $request['order']   : 'DESC',
		);

		// default to publish plans only, otherwise filter by plan status
		if ( ! empty( $request['post_status'] ) ) {
			$query_args['post_status'] = $request['post_status'];
		} elseif( ! empty( $request['status'] ) ) {
			$query_args['post_status'] = $request['status'];
		} else {
			$query_args['post_status'] = 'publish';
		}

		// filter by plan name
		if ( ! empty( $request['name'] ) ) {
			$query_args['name'] = $request['name'];
		} elseif ( ! empty( $request['slug'] ) ) {
			$query_args['name'] = $request['slug'];
		}

		/**
		 * Filters the WP API query arguments for membership plans.
		 *
		 * This filter's name follows the WooCommerce core pattern.
		 * @see \WC_REST_Posts_Controller::get_items()
		 *
		 * @since 1.11.0
		 *
		 * @param array $args associative array of query args
		 * @param \WP_REST_Request $request request object
		 */
		return (array) apply_filters( "woocommerce_rest_{$this->post_type}s_query_args", $query_args, $request );
	}


	/**
	 * Gets a collection of User Membership items.
	 *
	 * @see \WC_REST_Posts_Controller::get_items()
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_REST_Response response object
	 */
	public function get_items( $request ) {

		$collection  = array();
		$query_args  = $this->prepare_items_query_args( $request );
		$posts_query = new \WP_Query( $this->prepare_items_query( $query_args, $request ) );

		if ( ! empty( $posts_query->posts ) ) {

			foreach ( $posts_query->posts as $post  ) {

				if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', $post->ID ) ) {
					continue;
				}

				if ( $membership_plan = wc_memberships_get_membership_plan( $post ) ) {

					$response_data = $this->prepare_item_for_response( $membership_plan, $request );
					$collection[]  = $this->prepare_response_for_collection( $response_data );
				}
			}
		}

		return $this->prepare_response_collection_paginated( $request, $collection, $posts_query, $query_args );
	}


	/**
	 * Returns membership plan data for API responses.
	 *
	 * @since 1.11.0
	 *
	 * @param null|int|\WP_Post|\WC_Memberships_Membership_Plan $membership_plan membership plan
	 * @param null|\WP_REST_Response optional response object
	 * @return array associative array of data
	 */
	public function get_formatted_item_data( $membership_plan, $request = null ) {

		if ( is_numeric( $membership_plan ) || $membership_plan instanceof \WP_Post ) {
			$membership_plan = wc_memberships_get_membership_plan( $membership_plan );
		}

		if ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

			// ensures this is always a standard membership plan object, which may be filtered later
			$membership_plan    = new \WC_Memberships_Membership_Plan( $membership_plan->post );
			$access_length_type = $membership_plan->get_access_length_type();
			$datetime_format    = $this->get_datetime_format();
			$data               = [
				'id'                        => $membership_plan->get_id(),
				'name'                      => $membership_plan->get_name(),
				'slug'                      => $membership_plan->get_slug(),
				'status'                    => $membership_plan->post->post_status,
				'access_method'             => $membership_plan->get_access_method(),
				'access_product_ids'        => $membership_plan->get_product_ids(),
				'access_length_type'        => $membership_plan->get_access_length_type(),
				'access_length'             => $membership_plan->get_access_length_in_seconds(),
				'access_start_date'         => 'fixed' === $access_length_type ? $membership_plan->get_local_access_start_date( $datetime_format ) : null,
				'access_start_date_gmt'     => 'fixed' === $access_length_type ? $membership_plan->get_access_start_date( $datetime_format ) : null,
				'access_end_date'           => 'fixed' === $access_length_type ? $membership_plan->get_local_access_end_date( $datetime_format ) : null,
				'access_end_date_gmt'       => 'fixed' === $access_length_type ? $membership_plan->get_access_end_date( $datetime_format ) : null,
				'date_created'              => wc_memberships_format_date( $membership_plan->post->post_date, $datetime_format ),
				'date_created_gmt'          => wc_memberships_format_date( $membership_plan->post->post_date_gmt, $datetime_format ),
				'date_modified'             => wc_memberships_format_date( $membership_plan->post->post_modified, $datetime_format ),
				'date_modified_gmt'         => wc_memberships_format_date( $membership_plan->post->post_modified_gmt, $datetime_format ),
				'meta_data'                 => $this->prepare_item_meta_data( $membership_plan ),
			];

		} else {

			$data            = [];
			$membership_plan = null;
		}

		if ( $request ) {

			$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
			$fields  = $this->add_additional_fields_to_object( $data, $request );
			$data    = $this->filter_response_by_context( $fields, $context );
		}

		/**
		 * Filters the membership plan data for the REST API.
		 *
		 * @since 1.11.0
		 *
		 * @param array $data associative array of membership plan data
		 * @param null|\WC_Memberships_Membership_Plan $membership_plan membership plan object or null if undetermined
		 * @param null|\WP_REST_Request optional request object
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_membership_plan_data', $data, $membership_plan, $request );
	}


	/**
	 * Prepares an individual User Membership object data for API response.
	 *
	 * @since 1.11.0
	 *
	 * @param int|\WP_Post|\WC_Memberships_Membership_Plan $membership_plan membership plan object, ID or post object
	 * @param null|\WP_REST_Request $request WP API request, optional
	 * @return \WP_REST_Response response data
	 */
	public function prepare_item_for_response( $membership_plan, $request = null ) {

		if ( is_numeric( $membership_plan ) || $membership_plan instanceof \WP_Post ) {
			$membership_plan = wc_memberships_get_membership_plan( $membership_plan );
		}

		// build the response
		$response = rest_ensure_response( $this->get_formatted_item_data( $membership_plan, $request ) );

		// add additional links to the response
		$response->add_links( $this->prepare_links( $membership_plan, $request ) );

		/**
		 * Filters the data for a response.
		 *
		 * This filter's name follows the WooCommerce core pattern.
		 * @see \WC_REST_Posts_Controller::prepare_item_for_response()
		 *
		 * @since 1.11.0
		 *
		 * @param \WP_REST_Response $response the response object
		 * @param null|\WP_Post $post the membership plan post object
		 * @param \WP_REST_Request $request the request object
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $membership_plan ? $membership_plan->post : null, $request );
	}


	/**
	 * Prepares links to be added to membership plan objects.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan membership plan object
	 * @param \WP_REST_Request $request WP API request
	 * @return array associative array
	 */
	protected function prepare_links( $membership_plan, $request ) {

		$links = array();

		if ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

			$links = array(
				'self'       => array(
					'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $membership_plan->get_id() ) ),
				),
				'collection' => array(
					'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
				),
			);

			// a plan may be linked to products that grant access
			if ( $membership_plan->has_products() ) {

				$links['products'] = array();

				foreach ( $membership_plan->get_products() as $product ) {
					$links['products'][]['href'] = rest_url( sprintf( '/%s/products/%d', $this->get_woocommerce_namespace(), $product->get_id() ) );
				}
			}
		}

		/**
		 * Filters the membership plan item's links for WP API output.
		 *
		 * @since 1.11.0
		 *
		 * @param array $links associative array
		 * @param \WC_Memberships_Membership_Plan $membership_plan membership object
		 * @param null|\WP_REST_Request $request WP API request
		 * @param \SkyVerge\WooCommerce\Memberships\API\v2\Membership_Plans handler instance
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_user_membership_links', $links, $membership_plan, $request, $this );
	}


	/**
	 * Returns the membership plan REST API schema.
	 *
	 * @since 1.11.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		/**
		 * Filters the WP API membership plan schema.
		 *
		 * @since 1.11.0
		 *
		 * @param array associative array
		 */
		$schema = (array) apply_filters( 'wc_memberships_rest_api_membership_plan_schema', array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'membership_plan', // this will be used as the base for WP REST CLI commands
			'type'       => 'object',
			'properties' => array(
				'id'                        => array(
					'description' => __( 'Unique identifier of the membership plan.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'                      => array(
					'description' => __( 'Membership plan name.', 'woocommerce-membership' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'slug'                      => array(
					'description' => __( 'Membership plan slug.', 'woocommerce-membership' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'status'                    => array(
					'description' => __( 'Membership plan status.', 'woocommerce-membership' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'access_method'             => array(
					'description' => __( 'Membership plan access method.', 'woocommerce-memberships' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'access_product_ids'        => array(
					'description' => __( 'List of products that can grant access to the membership plan.', 'woocommerce-memberships' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type' => 'integer',
					),
				),
				'access_length_type'        => array(
					'description' => __( 'Duration type of the membership.', 'woocommerce-memberships' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'access_length'             => array(
					'description' => __( 'Membership plan access duration in seconds.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'access_start_date'         => array(
					'description' => __( 'The date when access will start, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'access_start_date_gmt'     => array(
					'description' => __( 'The date when access will start, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'access_end_date'           => array(
					'description' => __( 'The set date when access will end for fixed-length membership plans, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'access_end_date_gmt'       => array(
					'description' => __( 'The set date when access will end for fixed-length membership plans, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created'              => array(
					'description' => __( 'The date when the membership plan was created, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created_gmt'          => array(
					'description' => __( 'The date when the membership plan was created, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_modified'             => array(
					'description' => __( 'The date when the membership plan was last updated, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_modified_gmt'         => array(
					'description' => __( 'The date when the membership plan was last updated, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'meta_data'                 => array(
					'description' => __( 'Membership plan additional meta data.', 'woocommerce-memberships' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'description' => __( 'Meta ID.', 'woocommerce-memberships' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'key'    => array(
								'description' => __( 'Meta key.', 'woocommerce-memberships' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'value'  => array(
								'description' => __( 'Meta value.', 'woocommerce-memberships' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
			),
		) );

		return $this->add_additional_fields_schema( $schema );
	}


}
