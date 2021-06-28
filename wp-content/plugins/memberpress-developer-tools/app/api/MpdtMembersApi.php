<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtMembersApi extends MpdtBaseApi {

  protected function after_create($args, $request, $response) {
    $member_data = (object)$response->get_data();
    $mepr_user = new MeprUser($member_data->id);

    $valid_user_meta_keys = [];
    $user_meta = [];
    $valid_user_meta_keys = MeprHooks::apply_filters( 'mepr_developer_tools_member_valid_user_metas', $valid_user_meta_keys, $mepr_user );

    foreach ($args as $key => $value) {
      switch ($key) {
        case "address1":
          $user_meta['mepr-address-one'] = $value;
          break;
        case "address2":
          $user_meta['mepr-address-two'] = $value;
          break;
        case "city":
          $user_meta['mepr-address-city'] = $value;
          break;
        case "state":
          $user_meta['mepr-address-state'] = $value;
          break;
        case "zip":
          $user_meta['mepr-address-zip'] = $value;
          break;
        case "country":
          $user_meta['mepr-address-country'] = $value;
          break;
        default:
          if ( in_array( $key, $valid_user_meta_keys ) ) {
            $user_meta[$key] = $value;
          }
      }
    }

    if(!empty($user_meta)) {
      foreach($user_meta as $key => $val) {
        update_user_meta($member_data->id, $key, $val);
      }
    }

    if(isset($args['send_password_email']) && !empty($args['send_password_email'])) {
      $mepr_user->send_password_notification('new');
    }

    // Needed for autoresponders - call before txn is stored
    MeprHooks::do_action('mepr-signup-user-loaded', $mepr_user);

    if(isset($args['transaction']) && is_array($args['transaction'])) {
      $args['transaction']['member'] = $member_data->id; // hard code current member
      $transaction_request = new WP_REST_Request('POST');
      $transaction_request->set_body_params($args['transaction']);
      $transaction_api = new MpdtTransactionsApi();
      $transaction_response = $transaction_api->create_item($transaction_request);

      if(!is_wp_error($transaction_response) && isset($args['send_welcome_email']) && !empty($args['send_welcome_email'])) {
        $transaction_data = (object)$transaction_response->get_data();
        $transaction = new MeprTransaction($transaction_data->id);

        // Don't run mepr-signup action here, it already gets called in the MpdtTransactionsApi after_create() method

        // Send welcome email
        MeprUtils::send_signup_notices($transaction, true, false);
      }
    }

    // Refresh member object
    $get_req = new WP_REST_Request('GET');
    $get_req->set_url_params(array('id'=>$member_data->id));
    $data = $this->get_item($get_req);
    $response = rest_ensure_response($data);

    return $response;
  }

  /** Prepare the request variables before we map them and insert them into the database.
   *  We want to make sure that if a password wasn't set in the request that we just
   *  generate a random one here. This method is overridden from MpdtBaseApi.
   *
   *  @param $args This is the data that was passed in the request
   */
  protected function before_create($args, $request) {
    if(!isset($args['password'])) {
      $request->set_param('password', wp_generate_password(24));
    }

    return $request;
  }
}

