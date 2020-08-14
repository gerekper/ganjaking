<?php
/**
 * REST API Registers controller
 * handles requests to the /registers endpoint.
 */

!defined( 'ABSPATH' ) && exit;

/**
 * REST API Registers controller class.
 */
class YITH_POS_REST_Registers_Controller extends WC_REST_Posts_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'yith-pos/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'registers';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'yith-pos-register';

    /**
     * Coupons actions.
     */
    public function __construct() {
        add_filter( "woocommerce_rest_{$this->post_type}_query", array( $this, 'query_args' ), 10, 2 );
    }

    /**
     * Register the routes for coupons.
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            'args'   => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the resource.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
    }

    /**
     * Query args.
     *
     * @param array           $args
     * @param WP_REST_Request $request
     * @return array
     */
    public function query_args( $args, $request ) {

        if ( isset( $request[ 'store' ] ) ) {
            if ( !empty( $args[ 'meta_query' ] ) ) {
                $args[ 'meta_query' ] = array();
            }

            $args[ 'meta_query' ][] = array(
                'key'   => '_store_id',
                'value' => $request[ 'store' ],
                'type'  => 'NUMERIC',
            );
        }

        return $args;
    }

    /**
     * Prepare a single register output for response.
     *
     * @param WP_Post         $post    Post object.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response $data
     */
    public function prepare_item_for_response( $post, $request ) {
        $_register = yith_pos_get_register( (int) $post->ID );
        $data      = $_register->get_data();

        $context  = !empty( $request[ 'context' ] ) ? $request[ 'context' ] : 'view';
        $data     = $this->add_additional_fields_to_object( $data, $request );
        $data     = $this->filter_response_by_context( $data, $context );
        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $post, $request ) );

        /**
         * Filter the data for a response.
         * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
         * prepared for the response.
         *
         * @param WP_REST_Response $response The response object.
         * @param WP_Post          $post     Post object.
         * @param WP_REST_Request  $request  Request object.
         */
        return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $post, $request );
    }

    /**
     * Get the Coupon's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => $this->post_type,
            'type'       => 'object',
            'properties' => array(
                'id'                       => array(
                    'description' => __( 'Unique identifier for the object.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'name'                     => array(
                    'description' => __( 'Register Name.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'status'                   => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'enabled'                  => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'scanner_enabled'          => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'guest_enabled'            => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'payment_methods'          => array(
                    'type'    => 'array',
                    'items'   => array(
                        'type' => 'integer',
                    ),
                    'context' => array( 'view', 'edit' ),
                ),
                'what_to_show'             => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'show_categories'          => array(
                    'type'    => 'array',
                    'items'   => array(
                        'type' => 'integer',
                    ),
                    'context' => array( 'view', 'edit' ),
                ),
                'show_products'            => array(
                    'type'    => 'array',
                    'items'   => array(
                        'type' => 'integer',
                    ),
                    'context' => array( 'view', 'edit' ),
                ),
                'how_to_show_in_dashboard' => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'visibility'               => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
                'visibility_cashiers'      => array(
                    'type'    => 'array',
                    'items'   => array(
                        'type' => 'integer',
                    ),
                    'context' => array( 'view', 'edit' ),
                ),
                'receipt_id'               => array(
                    'type'    => 'integer',
                    'context' => array( 'view', 'edit' ),
                ),
                'notes_enabled'            => array(
                    'type'    => 'string',
                    'context' => array( 'view', 'edit' ),
                ),
            ),
        );

        return $this->add_additional_fields_schema( $schema );
    }
}
