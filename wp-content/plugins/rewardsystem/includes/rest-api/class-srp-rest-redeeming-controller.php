<?php

defined( 'ABSPATH' ) || exit;

/**
 * SRP_REST_Redeeming_Controller
 */
class SRP_REST_Redeeming_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Table name for Functionality.
	 *
	 * @var string
	 */
	protected $funct_table_name;

	/**
	 * Table name for display.
	 *
	 * @var string
	 */
	protected $display_table_name;

	/**
	 * Reward System Redeeming controller constructor.
	 */
	public function __construct() {
		$this->namespace          = 'wc-srp/v1';
		$this->rest_base          = 'redeeming';
		$this->funct_table_name   = 'rspointexpiry';
		$this->display_table_name = 'rsrecordpoints';

		add_filter( 'user_has_cap', array( $this, 'user_has_cap' ), 10, 3 );
	}

	/**
	 * Register the routes for reward system.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_items' ),
					'permission_callback' => array( $this, 'create_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'rewardsystem' ),
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
			)
		);
	}

	/**
	 * User has capability
	 *
	 * @param  array $allcaps capabilities.
	 * @param  array $caps caps.
	 * @param  array $args arguments.
	 * @return WP_Error|boolean
	 */
	public function user_has_cap( $allcaps, $caps, $args ) {
		if ( ! isset( $caps[0] ) ) {
			return $allcaps;
		}

		global $wpdb;
				$db = &$wpdb;
		$user_id    = isset( $args[1] ) ? absint( $args[1] ) : 0;
		$table_id   = isset( $args[2] ) ? absint( $args[2] ) : 0;
		switch ( $caps[0] ) {
			case 'rs_redeeming_update':
				if ( $user_id && $table_id ) {
					$table_data = $db->get_row( $db->prepare( "SELECT * FROM {$db->prefix}{$this->display_table_name} WHERE userid = %d and id=%d", $user_id, $table_id ), ARRAY_A );
					if ( srp_check_is_array( $table_data ) ) {
						$allcaps[ $caps[0] ] = true;
					}
				}
				break;
			case 'rs_redeeming_read':
			case 'rs_redeeming_write':
				$allcaps[ $caps[0] ] = true;
				break;
		}
		return $allcaps;
	}

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'rs_redeeming_read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'rewardsystem' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create single/multiple items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_items_permissions_check( $request ) {
		if ( isset( $request['user_id'] ) && ! current_user_can( 'rs_redeeming_read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'rewardsystem' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( isset( $request['id'] ) && ! current_user_can( 'rs_redeeming_read', $request['id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'rewardsystem' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$table_id = (int) $request['id'];
		global $wpdb;
				$db = &$wpdb;
		$table_data = $db->get_row( $db->prepare( "SELECT * FROM {$db->prefix}{$this->display_table_name} WHERE id = %d", $table_id ), ARRAY_A );
		if ( ! srp_check_is_array( $table_data ) ) {
			return;
		}

		$data     = $this->prepare_item_for_response( $table_data, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Get a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		global $wpdb;
				$db        = &$wpdb;
		$display_table_ids = $db->get_col( "SELECT id FROM {$db->prefix}{$this->display_table_name}" );
		if ( ! srp_check_is_array( $display_table_ids ) ) {
			return array();
		}

		$objects = array();
		foreach ( $display_table_ids as $id ) {
						$db1 = &$wpdb;
			$table_datas     = $db1->get_results( $db1->prepare( "SELECT * FROM {$db1->prefix}{$this->display_table_name} where id=%s", $id ), ARRAY_A );
			$table_datas     = isset( $table_datas[0] ) ? $table_datas[0] : array();
			$data            = $this->prepare_item_for_response( $table_datas, $request );
			$objects[]       = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $objects );
	}

	/**
	 * Create single/multiple items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_items( $request ) {
		$user_id    = isset( $request['user_id'] ) ? absint( $request['user_id'] ) : 0;
		$user       = get_user_by( 'ID', $user_id );
		$reason     = isset( $request['reason'] ) ? $request['reason'] : '';
		$usedpoints = isset( $request['usedpoints'] ) ? $request['usedpoints'] : 0;
		if ( ! is_object( $user ) || ! $usedpoints || ! $reason ) {
			return;
		}

		if ( ! class_exists( 'RSPointExpiry' ) ) {
			include_once SRP_PLUGIN_PATH . 'includes/class-fp-award-points-for-purchase-and-actions.php';
		}

		RSPointExpiry::perform_calculation_with_expiry( $usedpoints, $user_id );

		$table_args = array(
			'user_id'     => $user_id,
			'usedpoints'  => $usedpoints,
			'checkpoints' => 'MRP',
			'date'        => '999999999999',
			'reason'      => $reason,
		);

		RSPointExpiry::record_the_points( $table_args );

		$result = array(
			'status'  => 'success',
			'message' => __( 'Points Redeemed Successfully', 'rewardsystem' ),
		);

		$data = $this->prepare_response_for_collection( $result );

		return rest_ensure_response( $data );
	}

	/**
	 * Get the item data.
	 *
	 * @return array
	 */
	protected function get_item_data( $table_id ) {
		global $wpdb;
				$db = &$wpdb;
		$table_data = $db->get_row( $db->prepare( "SELECT * FROM {$db->prefix}{$this->display_table_name} WHERE id = %d", $table_id ), ARRAY_A );
		if ( ! srp_check_is_array( $table_data ) ) {
			return array();
		}
				/**
		 * Hook:srp_redeeming_rest_api_item_data.
		 *
		 * @since 28.5
		 */
		$data = apply_filters(
			'srp_redeeming_rest_api_item_data',
			array(
				'table_id'          => isset( $table_data['id'] ) ? $table_data['id'] : 0,
				'earnedpoints'      => isset( $table_data['earnedpoints'] ) ? $table_data['earnedpoints'] : 0,
				'usedpoints'        => isset( $table_data['usedpoints'] ) ? $table_data['usedpoints'] : 0,
				'expiredpoints'     => 0,
				'userid'            => isset( $table_data['userid'] ) ? $table_data['userid'] : 0,
				'earneddate'        => isset( $table_data['earneddate'] ) ? $table_data['earneddate'] : 0,
				'expirydate'        => isset( $table_data['expirydate'] ) ? $table_data['expirydate'] : 0,
				'checkpoints'       => isset( $table_data['checkpoints'] ) ? $table_data['checkpoints'] : '',
				'orderid'           => isset( $table_data['orderid'] ) ? $table_data['orderid'] : 0,
				'totalearnedpoints' => isset( $table_data['totalearnedpoints'] ) ? $table_data['totalearnedpoints'] : 0,
				'totalredeempoints' => isset( $table_data['totalredeempoints'] ) ? $table_data['totalredeempoints'] : 0,
				'reasonindetail'    => isset( $table_data['reasonindetail'] ) ? $table_data['reasonindetail'] : '',
			)
		);

		return $data;
	}

	/**
	 * Prepare a single item output for response.
	 *
	 * @param $table_data table data.
	 * @param WP_REST_Request       $request Request object.
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $table_data, $request ) {
		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->get_item_data( $table_data['id'] );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		/**
		 * Hook:srp_redeeming_rest_api_response.
		 *
		 * @since 28.5
		 */
		return apply_filters( 'srp_redeeming_rest_api_response', $response, $table_data, $request );
	}

	/**
	 * Get the item's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->display_table_name,
			'type'       => 'object',
			'properties' => array(
				'table_id'          => array(
					'description' => __( 'Unique identifier for the resource.', 'rewardsystem' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'earnedpoints'      => array(
					'description' => __( 'Earned Points', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'usedpoints'        => array(
					'description' => __( 'Used Points', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'expiredpoints'     => array(
					'description' => __( 'Expired Points', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'userid'            => array(
					'description' => __( 'User ID', 'rewardsystem' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'earneddate'        => array(
					'description' => __( 'Earned Date', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'expirydate'        => array(
					'description' => __( 'Expiry Date', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'checkpoints'       => array(
					'description' => __( 'Checkpoints', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'orderid'           => array(
					'description' => __( 'Order ID', 'rewardsystem' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'totalearnedpoints' => array(
					'description' => __( 'Total Earned Points', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'totalredeempoints' => array(
					'description' => __( 'Total Redeem Points', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'reasonindetail'    => array(
					'description' => __( 'Reason in Detail', 'rewardsystem' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
