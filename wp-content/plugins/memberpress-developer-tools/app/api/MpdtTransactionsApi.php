<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtTransactionsApi extends MpdtBaseApi {

  protected function register_more_routes() {
    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/(?P<id>[\d]+)/refund', array(
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'refund_transaction'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array()
      ),
    ) );

    register_rest_route( $this->utils->namespace, '/' . $this->utils->base . '/(?P<id>[\d]+)/refund_and_cancel', array(
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'refund_and_cancel'),
        'permission_callback' => array($this, 'rest_permissions_check'),
        'args'                => array()
      ),
    ) );
  }

  protected function before_create($args, $request) {
    // If no expiration date is passed in, default to lifetime
    if(!isset($args['expires_at'])) {
      $request->set_param('expires_at', '0000-00-00 00:00:00');
    }

    return $request;
  }

  protected function after_create($args, $request, $response) {
    $transaction_data = (object)$response->get_data();
    $transaction = new MeprTransaction($transaction_data->id);

    //Fire mepr-signup event for Corporate Accounts
    //Run before the other events that get triggered during the signup notices
    MeprHooks::do_action('mepr-signup', $transaction);

    if(isset($args['send_welcome_email']) && !empty($args['send_welcome_email'])) {
      MeprUtils::send_signup_notices($transaction, true, false);
    }

    if(isset($args['send_receipt_email']) && !empty($args['send_receipt_email'])) {
      MeprUtils::send_transaction_receipt_notices($transaction);
    }

    return $response;
  }

  /**
   * Refund a transaction
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function refund_transaction( $request ) {
    $transaction = $this->prepare_item_for_database( $request );

    try {
      if(!($transaction instanceof MeprTransaction)) {
        throw new Exception(__('There was a problem retrieving this transaction.', 'memberpress-developer-tools'));
      }

      if($transaction->status!='complete') {
        throw new Exception(__('The transaction must have a status of \'complete\' before it can be refunded.', 'memberpress-developer-tools'));
      }

      $id = $transaction->refund();

      if(false === $id) {
        throw new Exception(__('There was a problem refunding your transaction.', 'memberpress-developer-tools'));
      }
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
      'message' => __('The transaction was successfully refunded.', 'memberpress-developer-tools'),
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
   * Refund a Transaction & Cancel its associated Subscription
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function refund_and_cancel( $request ) {
    $transaction = $this->prepare_item_for_database( $request );

    try {
      if(!($transaction instanceof MeprTransaction)) {
        throw new Exception(__('There was a problem retrieving this transaction.', 'memberpress-developer-tools'));
      }

      if($transaction->status!='complete') {
        throw new Exception(__('The transaction must have a status of \'complete\' before it can be refunded.', 'memberpress-developer-tools'));
      }

      $subscription = $transaction->subscription();
      if(!empty($subscription)) {
        $subscription->cancel();
      }

      $id = $transaction->refund();

      if(false === $id) {
        throw new Exception(__('There was a problem refunding your transaction.', 'memberpress-developer-tools'));
      }
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
      'message' => __('The transaction was successfully refunded and it\'s associated subscription was cancelled.', 'memberpress-developer-tools'),
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

