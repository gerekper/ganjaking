<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class MpdtBaseApi extends WP_REST_Controller {
  public $utils;

  public function __construct() {
    $this->utils = MpdtUtilsFactory::fetch_for_api(get_class($this));
  }

  public function register_routes() {
    register_rest_route( $this->utils->namespace, '/' . $this->utils->base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'get_items'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'create_item'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => $this->get_endpoint_args_for_item_schema(true),
      ),
    ) );
    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'get_item'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array(
          'context'           => array(
            'default'         => 'view',
          ),
        ),
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'update_item'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => $this->get_endpoint_args_for_item_schema(false),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array($this, 'delete_item'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array(
          'force'    => array(
            'default'      => false,
          ),
        ),
      ),
    ) );
    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/schema', array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => array( $this, 'get_public_item_schema' ),
      'permission_callback' => '__return_true'
    ) );

    $this->register_more_routes();
  }

  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_items( $request ) {
    $args = (array)$request->get_params();
    $items = $this->utils->get_data($args);
    $total_items = $this->utils->get_count($args);

    $args = $this->utils->prepare_data_args($args);

    $data = array();
    foreach( $items as $item ) {
      $itemdata = $this->prepare_item_for_response( $item, $request );
      $data[] = $this->prepare_response_for_collection( $itemdata );
    }

    // Not sure what rest_ensure_response is but I think it's how you get the response object?
    $response = rest_ensure_response( $data );

    $response->header( 'X-WP-Total', (int) $total_items );
    $max_pages = ceil( $total_items / $args['per_page'] );
    $response->header( 'X-WP-TotalPages', (int) $max_pages );

    $base = add_query_arg( $request->get_query_params(), rest_url( $this->utils->namespace.'/'.$this->utils->base ) );

    if ( $args['page'] > 1 ) {
      $prev_page = $args['page'] - 1;
      if ( $prev_page > $max_pages ) {
        $prev_page = $max_pages;
      }
      $prev_link = add_query_arg( 'page', $prev_page, $base );
      $response->link_header( 'prev', $prev_link );
    }

    if ( $max_pages > $args['page'] ) {
      $next_page = $args['page'] + 1;
      $next_link = add_query_arg( 'page', $next_page, $base );
      $response->link_header( 'next', $next_link );
    }

    return $response;
  }

  /**
   * Get one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_item( $request ) {
    //get parameters from request
    $args = $request->get_params();
    $item = $this->utils->get_data($args);

    if(empty($item)) {
      return new WP_Error(
        'mp_no_records_found',
        sprintf( __('There were no %s found with an id of %d', 'memberpress-developer-tools'), $this->utils->class_info->plural, $args['id'] )
      );
    }

    // get_data will return an array so just pull out the record
    if(is_array($item)) { $item = $item[0]; }

    $data = $this->prepare_item_for_response( $item, $request );

    return new WP_REST_Response( $data, 200 );
  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function create_item( $request ) {
    $args = $request->get_params();

    if(isset($args['id'])) {
      $request->set_param('id', null);
    }

    $request = $this->before_create($args, $request);

    // Should handle the same way if id isn't set
    $response = $this->update_item( $request );

    if(!is_wp_error($response)) {
      $response = $this->after_create($args, $request, $response);
    }

    return $response;
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function update_item( $request ) {
    $item = $this->prepare_item_for_database( $request );

    try {
      $item->validate();
      $id = $item->store();
    }
    catch(Exception $e) {
      error_log("MemberPress API Error: " . $e->getMessage());
      return new WP_Error( 'mp_db_create_error', $e->getMessage() . "\n\n" . $e->getTraceAsString()  );
    }

    if(is_wp_error($id)) {
      return $id;
    }

    $get_req = new WP_REST_Request('GET');
    $get_req->set_url_params(compact('id'));
    $data = $this->get_item( $get_req );

    $response = rest_ensure_response( $data );

    if(is_wp_error($response)) {
      return $response;
    }

    $response = $this->after_store($request, $response);

    $response->set_status( 200 );

    return $response;
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function delete_item( $request ) {
    $args = $request->get_params();
    $id = $args['id'];

    if(empty($id)) {
      return new WP_Error(
        'mp_db_delete_error',
        sprintf(__('The %s with an id of %d wasn\'t found.', 'memberpress-developer-tools'), $this->utils->class_info->singular, $args['id'])
      );
    }

    try {
      $item = $this->get_obj($id);
      $obj = $item->destroy();
    }
    catch(Exception $e) {
      return new WP_Error( 'mp_db_delete_error', $e->getMessage() );
    }

    if(is_wp_error($obj)) {
      return $obj;
    }

    return new WP_REST_Response( true, 200 );
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function rest_permissions_check( $request ) {
    if(is_user_logged_in()) {
      return current_user_can( 'remove_users' );
    }
    else {
      $auth_header = MpdtUtils::get_authorization_header();
      if(!empty($auth_header)) {
        return $auth_header === get_option('mpdt_api_key');
      }
    }
    return false;
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database( $request ) {
    $args = $request->get_params();

    $id = (isset($args['id']) ? $args['id'] : null) ;

    $data = $this->apply_accept_fields((array)$args);
    $item = $this->get_obj($id);
    $data = $this->prepare_vars((array)$data);

    // Do a reverse mapping before we load the object
    $rdata = $this->utils->map_vars((array)$data, true);
    $item->load_from_array($rdata);

    return $item;
  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response( $item, $request ) {
    // We don't need to do anything here ... mapping has already happened
    return $item;
  }

  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function get_collection_params() {
    return $this->utils->search_fields;
  }

  public function apply_accept_fields(Array $_post) {
    foreach($_post as $k => $field) {
      if(!isset($this->utils->accept_fields[$k])) {
        // that's right, what do you think would happen here?
        // We're killing the accept from the input ... booyah
        unset($_post[$k]);
      }
    }

    return $_post;
  }

  public function get_obj($id=null) {
    $r = new ReflectionClass($this->utils->model_class);
    $obj = $r->newInstanceArgs(array($id));

    return $obj;
  }

  // Runs right before mapping vars when creating and updating the object
  public function prepare_vars(Array $data) {
    return $data;
  }

  protected function is_associative_array($arr) {
    return (array_keys($arr) !== range(0, count($arr) - 1));
  }

  protected function get_attrs($arr) {
    if($this->is_associative_array($arr)) {
      return array_keys($arr);
    }

    return $arr;
  }

  // Override this to do some action before the item is created
  protected function before_create($args, $request) {
    // Nothing by default
    return $request;
  }

  // Override this to do some action after the item is created
  protected function after_create($args, $request, $response) {
    // Nothing by default
    return $response;
  }

  // Override this to do some action after the item is stored (both create and update)
  protected function after_store($request, $response) {
    // Nothing by default
    return $response;
  }

  // Override this to register additional API Endpoints
  protected function register_more_routes() {
    // Nothing by default
  }
}
