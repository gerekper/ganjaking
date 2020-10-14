<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtWebhooksApi {
  public function register_routes() {
    register_rest_route('mp/v1', '/me', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'show_me'),
        'permission_callback' => array($this, 'verify_permission'),
      ),
    ));
    register_rest_route('mp/v1', '/validate-login', array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'validate_login'),
        'permission_callback' => array($this, 'verify_permission'),
      ),
    ));
    register_rest_route('mp/v1', '/webhooks/subscribe', array(
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'webhook_subscribe'),
        'permission_callback' => array($this, 'verify_permission'),
      ),
    ));
    register_rest_route('mp/v1', '/events/poll', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'event_data'),
        'permission_callback' => array($this, 'verify_permission'),
      ),
    ));
    // They recommend not using authentication for the unsubscribe
    // See: https://zapier.com/developer/documentation/v2/rest-hooks/
    register_rest_route('mp/v1', '/webhooks/unsubscribe/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array($this, 'webhook_unsubscribe'),
        'permission_callback' => '__return_true'
      ),
    ));
  }

  /**
  * Authenticate via Basic Auth or API Key
  * @see register_rest_route, permission_callback
  * @return boolean
  */
  public function verify_permission() {
    if(is_user_logged_in()) {
      return current_user_can('remove_users');
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
  * Display authenticated user
  * @see register_rest_route('mp/v1', '/me', ...)
  * @param object[WP_REST_Request] $request
  * @return void Send a JSON response and die().
  */
  public function show_me($request) {
    if(is_user_logged_in()) {
      $me = wp_get_current_user()->display_name;
    }
    else {
      $me = MpdtUtils::get_authorization_header();
    }

    wp_send_json_success(array('username' => $me));
  }

  /**
   * Validate a set of login credentials (username/email and password)
   * @param object [WP_REST_Request] $request
   * @return void Send a JSON response and die().
   */
  public function validate_login( $request ) {

    $request_data = $request->get_params();

    $login = $request_data['login'];
    $password = wp_slash( $request_data['password'] );

    // First, check by username
    $user = get_user_by( 'login', $login );

    // No user with that username, so check email
    if ( false === $user ) {
      $user = get_user_by( 'email', $login );
    }

    $data = array(
      'validated' => $user && wp_check_password( $password, $user->data->user_pass, $user->ID )
    );

    wp_send_json_success( apply_filters( 'mpdt_validate_login_response_data', $data, $user ), 200 );
  }

  /**
  * Store Webhook and Event from Zapier
  * @see register_rest_route('mp/v1', '/webhooks/subscribe', ...)
  * @param object[WP_REST_Request] $request
  * @return void Send a JSON response and die().
  */
  public function webhook_subscribe($request) {
    $request_data = $request->get_params();
    if($this->validate_request($request_data, array('url', 'event'))) {
      $webhooks = get_option(MPDT_WEBHOOKS_KEY, false);
      $id = rand(1, 99999);
      $webhooks[$id] = $this->prepare_webhook($request_data);
      if(update_option(MPDT_WEBHOOKS_KEY, $webhooks)) {
        wp_send_json(array('success' => true, 'data' => array('id' => $id)), 201);
      }
      else {
        wp_send_json_error(array('message' => 'Invalid Request'), 409);
      }
    }
    else {
      wp_send_json_error(array('message' => 'Invalid Request'), 409);
    }
  }

  /**
  * Sample data for event
  * @see register_rest_route('mp/v1', '/events/poll', ...)
  * @param object[WP_REST_Request] $request
  * @return void Send a JSON response and die().
  */
  public function event_data($request) {
    $request_data = $request->get_params();
    if($this->validate_request($request_data, array('event', 'event_type'))) {
      $utils = MpdtUtilsFactory::fetch($request_data['event_type']);
      $event_data = $utils->get_event_data($request_data['event']);
      wp_send_json(array($event_data), 200);
    }
    else {
      wp_send_json_error(array('message' => 'Invalid Request'), 409);
    }
  }

  /**
  * Remove Webhook from Zapier
  * @see register_rest_route('mp/v1', '/webhooks/unsubscribe/(?P<id>[\d]+)', ...)
  * @param object[WP_REST_Request] $request
  * @return void Send a JSON response and die().
  */
  public function webhook_unsubscribe($request) {
    $request_data = $request->get_params();
    $webhooks = get_option(MPDT_WEBHOOKS_KEY, false);
    unset($webhooks[$request_data['id']]);
    if(update_option(MPDT_WEBHOOKS_KEY, $webhooks)) {
      wp_send_json_success();
    }
    else {
      wp_send_json_error(array('message' => 'Unable to unsubscribe'), 409);
    }
  }

  /**
  * Validate the request from zapier
  * @param array $request_data Parsed JSON params
  * @param array $validations Data to be validated
  * @return boolean
  */
  private function validate_request($request_data, $validations = array()) {
    foreach ($validations as $validation) {
      if(!isset($request_data[$validation]) || empty($request_data[$validation])) {
        return false;
      }
    }
    return true;
  }

  /**
  * Format Webhook data
  * @param array $request_data Parsed JSON params
  * @return array $webhook Formatted Webhook data
  */
  private function prepare_webhook($request_data) {
    $webhook = array(
      'url' => $request_data['url'],
      'events' => array(
        $request_data['event'] => 'on'
      )
    );

    return $webhook;
  }
}
