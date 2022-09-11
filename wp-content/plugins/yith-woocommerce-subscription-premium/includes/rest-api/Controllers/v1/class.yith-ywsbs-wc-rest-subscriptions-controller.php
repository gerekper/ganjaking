<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_YWSBS_REST_Subscriptions_Controller class
 *
 * @class   YITH_YWSBS_REST_Subscriptions_Controller
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

/**
 * REST API Subscriptions controller
 * handles requests to the /subscriptions endpoint.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
	// Exit if accessed directly.
}

/**
 * REST API Registers controller class.
 */
class YITH_YWSBS_REST_Subscriptions_Controller extends WC_REST_Posts_Controller {


	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-yith-ywsbs/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'subscriptions';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = YITH_YWSBS_POST_TYPE;


	/**
	 * Coupons actions.
	 */
	public function __construct() {
		add_filter( "woocommerce_rest_{$this->post_type}_query", array( $this, 'query_args' ), 10, 2 );

	}//end __construct()


	/**
	 * Register the routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array(
						$this,
						'get_items',
					),
					'permission_callback' => array(
						$this,
						'get_items_permissions_check',
					),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array(
					$this,
					'get_public_item_schema',
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'yith-woocommerce-subscription' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array(
						$this,
						'get_item',
					),
					'permission_callback' => array(
						$this,
						'get_item_permissions_check',
					),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array(
						$this,
						'update_item',
					),
					'permission_callback' => array(
						$this,
						'update_item_permissions_check',
					),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array(
						$this,
						'delete_item',
					),
					'permission_callback' => array(
						$this,
						'delete_item_permissions_check',
					),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'type'        => 'boolean',
							'description' => __( 'Whether to bypass trash and force deletion.', 'yith-woocommerce-subscription' ),
						),
					),
				),
				'schema' => array(
					$this,
					'get_public_item_schema',
				),
			)
		);

	}//end register_routes()


	/**
	 * Query args.
	 *
	 * @param  array           $args    Arguments.
	 * @param  WP_REST_Request $request Request.
	 * @return array
	 */
	public function query_args( $args, $request ) {
		// Set post_status.
		if ( isset( $request['status'] ) && 'any' !== $request['status'] ) {
			$args['meta_query'][] = array(
				'key'     => 'status',
				'value'   => $request['status'],
				'compare' => 'LIKE',
			);
		}

		if ( isset( $request['customer_id'] ) ) {
			if ( ! empty( $args['meta_query'] ) ) {
				$args['meta_query'] = array(); // phpcs:ignore
			}

			$args['meta_query'][] = array(
				'key'   => 'user_id',
				'value' => $request['customer_id'],
				'type'  => 'NUMERIC',
			);
		}

		if ( isset( $request['product_id'] ) ) {
			if ( ! empty( $args['meta_query'] ) ) {
				$args['meta_query'] = array(); // phpcs:ignore
			}

			$args['meta_query'][] = array(
				'key'   => 'product_id',
				'value' => $request['product_id'],
				'type'  => 'NUMERIC',
			);
		}

		return $args;

	}//end query_args()


	/**
	 * Update a single post.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( empty( $id ) || empty( $post->ID ) || $post->post_type !== $this->post_type ) {
			return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'ID is invalid.', 'yith-woocommerce-subscription' ), array( 'status' => 400 ) );
		}

		$subscription = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $subscription ) ) {
			return $subscription;
		}

		do_action( "woocommerce_rest_insert_{$this->post_type}", $post, $request, false );
		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $post, $request );
		return rest_ensure_response( $response );

	}//end update_item()


	/**
	 * Prepare a single order for create.
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_Error|YWSBS_Subscription $data Object.
	 */
	protected function prepare_item_for_database( $request ) {
		$id           = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$subscription = ywsbs_get_subscription( $id );
		$is_editable  = $subscription->can_be_editable( 'payment_date' ) && $subscription->can_be_editable( 'recurring_amount' );
		$schema       = $is_editable ? $this->get_item_schema_editable() : $this->get_item_schema();

		$read_only_properties = array_keys( array_filter( $schema['properties'], array( $this, 'filter_read_only_props' ) ) );
		$read_only            = array();
		foreach ( $read_only_properties as $read_only_property ) {
			if ( 'id' !== $read_only_property && isset( $request[ $read_only_property ] ) ) {
				$read_only[] = $read_only_property;
			}
		}

		if ( ! empty( $read_only ) ) {
			// translators: placeholder is the list of read-only fields.
			$error_message = sprintf( __( 'The following attributes cannot be changed: %s', 'yith-woocommerce-subscription' ), implode( ', ', $read_only ) );
			return new WP_Error( 'YITH_YWSBS_BAD_Request', $error_message, array( 'status' => 400 ) );
		}

		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		$gmt_date = $this->get_gmt_date();

		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				// convert the gmt date in local timestamp.
				if ( in_array( $key, $gmt_date, true ) ) {
					$value = get_date_from_gmt( $value, 'U' );
				}

				switch ( $key ) {
					case 'status':
						$subscription->update_status( $value, 'rest-api' );
						break;

					case 'customer_id':
						$subscription->set( 'user_id', $value );
						break;

					case 'currency':
						$subscription->set( 'order_currency', $value );
						break;

					case 'subscription_interval':
						$subscription->set( 'price_is_per', $value );
						break;

					case 'subscription_period':
						$subscription->set( 'price_time_option', $value );
						break;

					case 'subscription_length':
						$subscription->set( 'max_length', $value );
						break;

					case 'next_payment_date':
						$subscription->set( 'payment_due_date', $value );
						break;

					case 'billing':
					case 'shipping':
						$this->update_address( $subscription, $value, $key );
						break;

					case 'shipping_lines':
					case 'discount_total':
						$subscription->set( 'cart_discount', $value );
						break;

					case 'discount_tax':
						$subscription->set( 'cart_discount_tax', $value );
						break;

					case 'shipping_total':
						$subscription->set( 'order_shipping', $value );
						break;

					case 'shipping_tax':
						$subscription->set( 'order_shipping_tax', $value );
						break;

					case 'total':
						$subscription->set( 'subscription_total', $value );
						break;
					case 'meta_data':
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$subscription->set( $meta['key'], $meta['value'] );
							}
						}
						break;
					default:
						$subscription->set( $key, $value );
						break;
				}
			}
		}

		/*
		 * Filter the data for the insert.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param YWSBS_Subscription  $subscription      The order object.
		 * @param WP_REST_Request    $request    Request object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}", $subscription, $request );

	}


	/**
	 * Update address.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @param array              $posted       Posted content.
	 * @param string             $type         Billing or shipping.
	 */
	protected function update_address( $subscription, $posted, $type = 'billing' ) {
		foreach ( $posted as $key => $value ) {
			$subscription->set( "_{$type}_{$key}", $value );
		}
	}


	/**
	 * Only return writable props from schema.
	 *
	 * @param  array $schema Schema.
	 * @return boolean
	 */
	protected function filter_writable_props( $schema ) {
		return empty( $schema['readonly'] );

	}


	/**
	 * Only return writable props from schema.
	 *
	 * @param  array $schema Schema.
	 * @return boolean
	 */
	protected function filter_read_only_props( $schema ) {
		return ! empty( $schema['readonly'] );

	}


	/**
	 * Prepare a single subscription output for response.
	 *
	 * @param  WC_Data         $post    Object data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$this->request       = $request;
		$this->request['dp'] = is_null( $this->request['dp'] ) ? wc_get_price_decimals() : absint( $this->request['dp'] );
		$request['context']  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$subscription        = ywsbs_get_subscription( (int) $post->ID );
		$data                = $this->get_formatted_item_data( $subscription );
		$data                = $this->add_additional_fields_to_object( $data, $request );
		$data                = $this->filter_response_by_context( $data, $request['context'] );
		$response            = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post, $request ) );

		/*
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object Object data.
		 * @param WP_REST_Request  $request Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $post, $request );

	}


	/**
	 * Get formatted item data.
	 *
	 * @param YWSBS_Subscription $subscription Subscription instance.
	 *
	 * @return array
	 * @since  2.4.0
	 */
	protected function get_formatted_item_data( $subscription ) {
		$data           = $subscription->get_data();
		$format_decimal = array(
			'discount_total',
			'discount_tax',
			'shipping_total',
			'shipping_tax',
			'fee',
			'total',
			'line_subtotal',
			'line_subtotal_tax',
			'line_total',
			'line_tax',
			'order_total',
			'order_tax',
			'order_subtotal',
			'total',
		);
		$gmt_date       = $this->get_gmt_date();
		$format_date    = array(
			'date_created',
			'date_created_gmt',
			'date_modified',
			'date_modified_gmt',
		);
		$format_date    = array_merge( $gmt_date, $format_date );

		$fields = $this->get_fields_for_response( $this->request );

		$format_decimal = array_intersect( $format_decimal, $fields );
		$format_date    = array_intersect( $format_date, $fields );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$data[ $key ] = wc_format_decimal( $data[ $key ], $this->request['dp'] );
		}

		if ( isset( $data['line_tax_data'] ) && is_array( $data['line_tax_data'] ) ) {
			foreach ( $data['line_tax_data'] as $key => $val ) {
				if ( in_array( $key, array( 'subtotal', 'total' ), true ) ) {
					$data['line_tax_data'][ $key ] = wc_format_decimal( $val, $this->request['dp'] );
				}
			}
		}

		// Transform the local date in gmt.
		foreach ( $gmt_date as $key ) {
			$datetime = $data[ $key ];
			if ( ! empty( $data[ $key ] ) ) {
				$data[ $key ] = ywsbs_get_gmt_from_local_timestamp( $datetime );
			} else {
				$data[ $key ] = '';
			}
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$datetime = $data[ $key ];
			if ( ! empty( $data[ $key ] ) ) {
				$data[ $key ] = wc_rest_prepare_date_response( $datetime, false );
			} else {
				$data[ $key ] = '';
			}
		}

		if ( in_array( 'meta_data', $fields, true ) ) {
			$meta         = get_post_meta( $subscription->get_id() );
			$exclude_meta = $this->get_meta_data_to_remove();
			if ( $meta ) {
				foreach ( $meta as $key => $value ) {
					if ( empty( $value ) || in_array( $key, $exclude_meta, true ) ) {
						continue;
					}

					if ( is_array( $value ) ) {
						$value = array_map( 'maybe_unserialize', $value );
						$value = array_filter( $value );
						// check again if the array is empty.
						if ( empty( $value ) ) {
							continue;
						}
					}

					$data['meta_data'][] = array(
						'key'   => $key,
						'value' => array_filter( $value ),
					);
				}
			}
		}

		return $data;

	}

	/**
	 * Return the properties as json
	 *
	 * @return false|string
	 */
	public function get_json_properties_schema() {
		$schema = $this->get_item_schema();
		return wp_json_encode( $schema['properties'] );
	}

	/**
	 * Get the Subscription's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'                    => array(
					'description' => __( 'Unique identifier for the resource.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'status'                => array(
					'description' => __( 'Subscription status.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => 'pending',
					'enum'        => array_keys( ywsbs_get_status() ),
					'context'     => array(
						'view',
						'edit',
					),
				),
				'version'               => array(
					'description' => __( 'Version of YITH Subscription that was used to create the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'order_id'              => array(
					'description' => __( 'Main order id', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'customer_id'           => array(
					'description' => __( 'Subscription owner user ID. 0 for guests.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'default'     => 0,
					'context'     => array(
						'view',
						'edit',
					),
				),
				'customer_note'         => array(
					'description' => __( 'Note left by customer during checkout.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				),
				'currency'              => array(
					'description' => __( 'Currency used to create the subscription, in ISO format.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => get_woocommerce_currency(),
					'enum'        => array_keys( get_woocommerce_currencies() ),
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_created'          => array(
					'description' => __( "The subscription creation date, in the site's timezone.", 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_created_gmt'      => array(
					'description' => __( 'The subscription creation date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_modified'         => array(
					'description' => __( "The subscription last edit date, in the site's timezone.", 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_modified_gmt'     => array(
					'description' => __( 'The subscription last edit date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'start_date'            => array(
					'description' => __( 'The subscription start date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'next_payment_date'     => array(
					'description' => __( 'The subscription next payment date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'cancelled_date'        => array(
					'description' => __( 'The subscription cancelled date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'expired_date'          => array(
					'description' => __( 'The subscription expiration date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'end_date'              => array(
					'description' => __( 'The subscription end date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'expired_pause_date'    => array(
					'description' => __( 'The end date of the pause, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'created_via'           => array(
					'description' => __( 'Shows where the subscription was created.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => 'checkout',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'product_id'            => array(
					'description' => __( 'Product ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'mixed',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'variation_id'          => array(
					'description' => __( 'Variation ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'mixed',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'product_name'          => array(
					'description' => __( 'Product name.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				),
				'order_item_id'         => array(
					'description' => __( 'Order Item ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'quantity'              => array(
					'description' => __( 'Quantity ordered.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'subscription_interval' => array(
					'description' => __( 'Recurring interval.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'subscription_period'   => array(
					'description' => __( 'Recurring period.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'subscription_length'   => array(
					'description' => __( 'Subscription length.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'trial_interval'        => array(
					'description' => __( 'Trial interval.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'trial_period'          => array(
					'description' => __( 'Trial period.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'prices_include_tax'    => array(
					'description' => __( 'True the prices included tax during checkout.', 'yith-woocommerce-subscription' ),
					'type'        => 'boolean',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'fee'                   => array(
					'description' => __( 'Fee amount.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'discount_total'        => array(
					'description' => __( 'Total discount amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'discount_tax'          => array(
					'description' => __( 'Total discount tax amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'shipping_total'        => array(
					'description' => __( 'Total shipping amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'shipping_tax'          => array(
					'description' => __( 'Total shipping tax amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'line_subtotal'         => array(
					'description' => __( 'Line subtotal for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'line_subtotal_tax'     => array(
					'description' => __( 'Line subtotal tax for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'line_total'            => array(
					'description' => __( 'Line total for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'line_tax'              => array(
					'description' => __( 'Line total tax for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'line_tax_data'         => array(
					'description' => __( 'Total tax data for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'order_total'           => array(
					'description' => __( 'Total order for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'order_tax'             => array(
					'description' => __( 'Total order tax for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'order_subtotal'        => array(
					'description' => __( 'Subtotal amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'total'                 => array(
					'description' => __( 'Grand total.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'billing'               => array(
					'description' => __( 'Billing address.', 'yith-woocommerce-subscription' ),
					'type'        => 'object',
					'context'     => array(
						'view',
						'edit',
					),
					'properties'  => array(
						'first_name' => array(
							'description' => __( 'First name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'last_name'  => array(
							'description' => __( 'Last name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'company'    => array(
							'description' => __( 'Company name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_1'  => array(
							'description' => __( 'Address line 1', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_2'  => array(
							'description' => __( 'Address line 2', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'city'       => array(
							'description' => __( 'City name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'state'      => array(
							'description' => __( 'ISO code or name of the state, province or district.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'postcode'   => array(
							'description' => __( 'Postal code.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'country'    => array(
							'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'email'      => array(
							'description' => __( 'Email address.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'format'      => 'email',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'phone'      => array(
							'description' => __( 'Phone number.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'shipping'              => array(
					'description' => __( 'Shipping address.', 'yith-woocommerce-subscription' ),
					'type'        => 'object',
					'context'     => array(
						'view',
						'edit',
					),
					'properties'  => array(
						'first_name' => array(
							'description' => __( 'First name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'last_name'  => array(
							'description' => __( 'Last name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'company'    => array(
							'description' => __( 'Company name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_1'  => array(
							'description' => __( 'Address line 1', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_2'  => array(
							'description' => __( 'Address line 2', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'city'       => array(
							'description' => __( 'City name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'state'      => array(
							'description' => __( 'ISO code or name of the state, province or district.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'postcode'   => array(
							'description' => __( 'Postal code.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'country'    => array(
							'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'payment_method'        => array(
					'description' => __( 'Payment method ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'payment_method_title'  => array(
					'description' => __( 'Payment method title.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
					'readonly'    => true,
				),
				'paid_orders'           => array(
					'description' => __( 'Paid order list.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'shipping_data'         => array(
					'description' => __( 'Shipping lines data.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'description' => __( 'Item ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'integer',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
							),
							'method_title' => array(
								'description' => __( 'Shipping method name.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'method_id'    => array(
								'description' => __( 'Shipping method ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'instance_id'  => array(
								'description' => __( 'Shipping instance ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'total'        => array(
								'description' => __( 'Line total (after discounts).', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
							),
							'total_tax'    => array(
								'description' => __( 'Line total tax (after discounts).', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
							),
							'taxes'        => array(
								'description' => __( 'Line taxes.', 'yith-woocommerce-subscription' ),
								'type'        => 'array',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
								'items'       => array(
									'type'       => 'object',
									'properties' => array(
										'id'    => array(
											'description' => __( 'Tax rate ID.', 'yith-woocommerce-subscription' ),
											'type'        => 'integer',
											'context'     => array(
												'view',
												'edit',
											),
											'readonly'    => true,
										),
										'total' => array(
											'description' => __( 'Tax total.', 'yith-woocommerce-subscription' ),
											'type'        => 'string',
											'context'     => array(
												'view',
												'edit',
											),
											'readonly'    => true,
										),
									),
								),
							),
						),
					),
				),
				'delivery_schedules'    => array(
					'description' => __( 'Delivery schedule list', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
					'items'       => array(
						'id'             => array(
							'description' => __( 'Delivery schedule ID.', 'yith-woocommerce-subscription' ),
							'type'        => 'integer',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),
						'status'         => array(
							'description' => __( 'Delivery schedule status.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),
						'date_created'   => array(
							'description' => __( 'Delivery schedule date created.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),

						'scheduled_date' => array(
							'description' => __( 'Delivery schedule date.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
						),

						'sent_date'      => array(
							'description' => __( 'Delivered date.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'meta_data'             => array(
					'description' => __( 'Meta data.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'   => array(
								'description' => __( 'Meta key.', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'value' => array(
								'description' => __( 'Meta value.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'editable'              => array(
					'description' => __( 'If true, it is possible to edit the prices and recurring period.', 'yith-woocommerce-subscription' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );

	}


	/**
	 * Get the Subscription's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema_editable() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'                    => array(
					'description' => __( 'Unique identifier for the resource.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'status'                => array(
					'description' => __( 'Subscription status.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => 'pending',
					'enum'        => array_keys( ywsbs_get_status() ),
					'context'     => array(
						'view',
						'edit',
					),
				),
				'version'               => array(
					'description' => __( 'Version of YITH Subscription which created the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'order_id'              => array(
					'description' => __( 'Main order id', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'customer_id'           => array(
					'description' => __( 'User ID who owns the subscription. 0 for guests.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'default'     => 0,
					'context'     => array(
						'view',
						'edit',
					),
				),
				'customer_note'         => array(
					'description' => __( 'Note left by customer during checkout.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				),
				'currency'              => array(
					'description' => __( 'Currency used to create the subscription, in ISO format.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => get_woocommerce_currency(),
					'enum'        => array_keys( get_woocommerce_currencies() ),
					'context'     => array(
						'view',
						'edit',
					),
				),
				'date_created'          => array(
					'description' => __( "The subscription creation date, in the site's timezone.", 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_created_gmt'      => array(
					'description' => __( 'The subscription creation date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_modified'         => array(
					'description' => __( "The subscription last edit date, in the site's timezone.", 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'date_modified_gmt'     => array(
					'description' => __( 'The subscription last edit date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'start_date'            => array(
					'description' => __( 'The subscription start date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'next_payment_date'     => array(
					'description' => __( 'The subscription next payment date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'cancelled_date'        => array(
					'description' => __( 'The subscription cancelled date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'expired_date'          => array(
					'description' => __( 'The subscription expiration date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'end_date'              => array(
					'description' => __( 'The subscription end date, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'expired_pause_date'    => array(
					'description' => __( 'The end date of the pause, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'created_via'           => array(
					'description' => __( 'Shows where the subscription was created.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'default'     => 'checkout',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'product_id'            => array(
					'description' => __( 'Product ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'mixed',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'variation_id'          => array(
					'description' => __( 'Variation ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'mixed',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'product_name'          => array(
					'description' => __( 'Product name.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				),
				'order_item_id'         => array(
					'description' => __( 'Order Item ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'quantity'              => array(
					'description' => __( 'Quantity ordered.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'subscription_interval' => array(
					'description' => __( 'Recurring interval.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'subscription_period'   => array(
					'description' => __( 'Recurring period.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'subscription_length'   => array(
					'description' => __( 'Subscription length.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'trial_interval'        => array(
					'description' => __( 'Trial interval.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'trial_period'          => array(
					'description' => __( 'Trial period.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'prices_include_tax'    => array(
					'description' => __( 'True the prices included tax during checkout.', 'yith-woocommerce-subscription' ),
					'type'        => 'boolean',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'fee'                   => array(
					'description' => __( 'Fee amount.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'discount_total'        => array(
					'description' => __( 'Total discount amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'discount_tax'          => array(
					'description' => __( 'Total discount tax amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'shipping_total'        => array(
					'description' => __( 'Total shipping amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'shipping_tax'          => array(
					'description' => __( 'Total shipping tax amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'line_subtotal'         => array(
					'description' => __( 'Line subtotal for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'line_subtotal_tax'     => array(
					'description' => __( 'Line subtotal tax for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'line_total'            => array(
					'description' => __( 'Line total for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'line_tax'              => array(
					'description' => __( 'Line total tax for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'line_tax_data'         => array(
					'description' => __( 'Total tax data for the subscription item.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'order_total'           => array(
					'description' => __( 'Total order for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'order_tax'             => array(
					'description' => __( 'Total order tax for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'order_subtotal'        => array(
					'description' => __( 'Subtotal amount for the subscription.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'total'                 => array(
					'description' => __( 'Grand total.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'billing'               => array(
					'description' => __( 'Billing address.', 'yith-woocommerce-subscription' ),
					'type'        => 'object',
					'context'     => array(
						'view',
						'edit',
					),
					'properties'  => array(
						'first_name' => array(
							'description' => __( 'First name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'last_name'  => array(
							'description' => __( 'Last name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'company'    => array(
							'description' => __( 'Company name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_1'  => array(
							'description' => __( 'Address line 1', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_2'  => array(
							'description' => __( 'Address line 2', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'city'       => array(
							'description' => __( 'City name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'state'      => array(
							'description' => __( 'ISO code or name of the state, province or district.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'postcode'   => array(
							'description' => __( 'Postal code.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'country'    => array(
							'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'email'      => array(
							'description' => __( 'Email address.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'format'      => 'email',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'phone'      => array(
							'description' => __( 'Phone number.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'shipping'              => array(
					'description' => __( 'Shipping address.', 'yith-woocommerce-subscription' ),
					'type'        => 'object',
					'context'     => array(
						'view',
						'edit',
					),
					'properties'  => array(
						'first_name' => array(
							'description' => __( 'First name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'last_name'  => array(
							'description' => __( 'Last name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'company'    => array(
							'description' => __( 'Company name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_1'  => array(
							'description' => __( 'Address line 1', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'address_2'  => array(
							'description' => __( 'Address line 2', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'city'       => array(
							'description' => __( 'City name.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'state'      => array(
							'description' => __( 'ISO code or name of the state, province or district.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'postcode'   => array(
							'description' => __( 'Postal code.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
						'country'    => array(
							'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'payment_method'        => array(
					'description' => __( 'Payment method ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
				'payment_method_title'  => array(
					'description' => __( 'Payment method title.', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array(
						'view',
						'edit',
					),
					'arg_options' => array( 'sanitize_callback' => 'sanitize_text_field' ),
					'readonly'    => true,
				),
				'paid_orders'           => array(
					'description' => __( 'Paid order list.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
				),
				'shipping_data'         => array(
					'description' => __( 'Shipping lines data.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'description' => __( 'Item ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'integer',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
							),
							'method_title' => array(
								'description' => __( 'Shipping method name.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'method_id'    => array(
								'description' => __( 'Shipping method ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'instance_id'  => array(
								'description' => __( 'Shipping instance ID.', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'total'        => array(
								'description' => __( 'Line total (after discounts).', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
							),
							'total_tax'    => array(
								'description' => __( 'Line total tax (after discounts).', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
							),
							'taxes'        => array(
								'description' => __( 'Line taxes.', 'yith-woocommerce-subscription' ),
								'type'        => 'array',
								'context'     => array(
									'view',
									'edit',
								),
								'readonly'    => true,
								'items'       => array(
									'type'       => 'object',
									'properties' => array(
										'id'    => array(
											'description' => __( 'Tax rate ID.', 'yith-woocommerce-subscription' ),
											'type'        => 'integer',
											'context'     => array(
												'view',
												'edit',
											),
											'readonly'    => true,
										),
										'total' => array(
											'description' => __( 'Tax total.', 'yith-woocommerce-subscription' ),
											'type'        => 'string',
											'context'     => array(
												'view',
												'edit',
											),
											'readonly'    => true,
										),
									),
								),
							),
						),
					),
				),
				'delivery_schedules'    => array(
					'description' => __( 'Delivery schedule list', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
					'items'       => array(
						'id'             => array(
							'description' => __( 'Delivery schedule ID.', 'yith-woocommerce-subscription' ),
							'type'        => 'integer',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),
						'status'         => array(
							'description' => __( 'Delivery schedule status.', 'yith-woocommerce-subscription' ),
							'type'        => 'string',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),
						'date_created'   => array(
							'description' => __( 'Delivery schedule date created.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
							'readonly'    => true,
						),

						'scheduled_date' => array(
							'description' => __( 'Delivery schedule date.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
						),

						'sent_date'      => array(
							'description' => __( 'Delivered date.', 'yith-woocommerce-subscription' ),
							'type'        => 'date-time',
							'context'     => array(
								'view',
								'edit',
							),
						),
					),
				),
				'meta_data'             => array(
					'description' => __( 'Meta data.', 'yith-woocommerce-subscription' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'   => array(
								'description' => __( 'Meta key.', 'yith-woocommerce-subscription' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'value' => array(
								'description' => __( 'Meta value.', 'yith-woocommerce-subscription' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'editable'              => array(
					'description' => __( 'If true it is possible edit the prices and recurring period.', 'yith-woocommerce-subscription' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array(
						'view',
						'edit',
					),
					'readonly'    => true,
				),
			),

		);

		return $this->add_additional_fields_schema( $schema );

	}//end get_item_schema_editable()


	/**
	 * Return the list of gmt date to convert.
	 *
	 * @return array
	 */
	private function get_gmt_date() {
		return array(
			'start_date',
			'next_payment_date',
			'expired_date',
			'cancelled_date',
			'end_date',
			'expired_pause_date',
		);

	}//end get_gmt_date()


	/**
	 * Prepare links for the request.
	 *
	 * @param  WC_Data         $object  Object data.
	 * @param  WP_REST_Request $request Request object.
	 * @return array                   Links for the given post.
	 */
	protected function prepare_links( $object, $request ) {
		$subscription = ywsbs_get_subscription( $object->ID );

		if ( ! $subscription ) {
			return array();
		}

		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $subscription->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		if ( 0 !== (int) $subscription->get_user_id() ) {
			$links['customer'] = array(
				'href' => rest_url( sprintf( '/%s/customers/%d', 'wc/v3', $subscription->get_user_id() ) ),
			);
		}

		if ( 0 !== (int) $subscription->get_order_id() ) {
			$links['up'] = array(
				'href' => rest_url( sprintf( '/%s/orders/%d', 'wc/v3', $subscription->get_order_id() ) ),
			);
		}

		return $links;

	}

	/**
	 * List of meta data unnecessary inside rest result
	 */
	protected function get_meta_data_to_remove() {
		return apply_filters(
			'ywsbs_get_meta_data_to_remove_on_rest_result',
			array(
				'id',
				'status',
				'product_id',
				'variation_id',
				'product_name',
				'quantity',
				'order_id',
				'order_item_id',
				'line_subtotal',
				'line_total',
				'line_subtotal_tax',
				'line_tax',
				'line_tax_data',
				'order_total',
				'subscription_total',
				'order_tax',
				'order_subtotal',
				'order_shipping',
				'order_shipping_tax',
				'subscriptions_shippings',
				'payment_method',
				'payment_method_title',
				'order_currency',
				'user_id',
				'price_is_per',
				'price_time_option',
				'trial_time_option',
				'trial_per',
				'ywsbs_version',
				'delivery_schedules',
				'start_date',
				'payment_due_date',
				'expired_pause_date',
				'payed_order_list',
				'_billing_first_name',
				'_billing_last_name',
				'_billing_company',
				'_billing_address_1',
				'_billing_address_2',
				'_billing_city',
				'_billing_postcode',
				'_billing_country',
				'_billing_state',
				'_billing_email',
				'_billing_phone',
				'_shipping_first_name',
				'_shipping_last_name',
				'_shipping_company',
				'_shipping_address_1',
				'_shipping_address_2',
				'_shipping_city',
				'_shipping_postcode',
				'_shipping_country',
				'_shipping_state',
				'customer_note',
				'_edit_lock',
				'_edit_last',
			)
		);
	}

}//end class
