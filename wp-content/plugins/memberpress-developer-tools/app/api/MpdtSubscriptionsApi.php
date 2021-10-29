<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtSubscriptionsApi extends MpdtBaseApi {
  protected function register_more_routes() {
    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/(?P<id>[\d]+)/cancel', array(
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'cancel_subscription'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array()
      ),
    ) );

    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/(?P<id>[\d]+)/expire', array(
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'expire_subscription'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array()
      ),
    ) );
  }

  protected function before_create($args, $request) {
    // Zapier strips out the preceeding 0's which fails validation
    if(isset($args['cc_exp_month']) && $args['cc_exp_month'] < 10) {
      $request->set_param('cc_exp_month', '0' . (int)$args['cc_exp_month']); // add the preceeding 0
    }

    return $request;
  }

  /**
   * Cancel a Subscription
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function cancel_subscription( $request ) {
    $subscription = $this->prepare_item_for_database( $request );

    try {
      if(!($subscription instanceof MeprSubscription)) {
        throw new Exception(__('There was a problem retrieving this subscription.', 'memberpress-developer-tools'));
      }

      if($subscription->status!='active') {
        throw new Exception(__('The subscription must have a status of \'active\' before it can be cancelled.', 'memberpress-developer-tools'));
      }

      $subscription->cancel();
      $id = $subscription->id;
    }
    catch(Exception $e) {
      return new WP_Error( 'mp_db_create_error', $e->getMessage() . "\n\n" . $e->getTraceAsString()  );
    }

    if(is_wp_error($id)) {
      return $id;
    }

    $get_req = new WP_REST_Request('GET');
    $get_req->set_url_params(compact('id'));
    $data = array(
      'message' => __('The subscription was successfully cancelled.', 'memberpress-developer-tools'),
      'data' => $this->get_item( $get_req )
    );

    $response = rest_ensure_response( $data );

    if(is_wp_error($response)) {
      return $response;
    }

    $response->set_status( 200 );

    return $response;
  }


  /**
   * Expire a Subscription
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function expire_subscription( $request ) {
    $subscription = $this->prepare_item_for_database( $request );

    try {
      if(!($subscription instanceof MeprSubscription)) {
        throw new Exception(__('There was a problem retrieving this subscription.', 'memberpress-developer-tools'));
      }

      $subscription->expire_txns();

      $id = $subscription->id;
    }
    catch(Exception $e) {
      return new WP_Error( 'mp_db_create_error', $e->getMessage() . "\n\n" . $e->getTraceAsString()  );
    }

    if(is_wp_error($id)) {
      return $id;
    }

    $get_req = new WP_REST_Request('GET');
    $get_req->set_url_params(compact('id'));
    $data = array(
      'message' => __('This subscription is now expired.', 'memberpress-developer-tools'),
      'data' => $this->get_item( $get_req )
    );

    $response = rest_ensure_response( $data );

    if(is_wp_error($response)) {
      return $response;
    }

    $response->set_status( 200 );

    return $response;
  }

}
