<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtCouponsApi extends MpdtBaseApi {
  /**
   *  @param $args This is the data that was passed in the request
   */
  protected function before_create($args, $request) {
    if( isset($args['should_expire']) && $args['should_expire'] &&
        isset($args['expires_on']) && !empty($args['expires_on']) &&
        !is_numeric($args['expires_on']) ) {
      $request->set_param('expires_on', strtotime($args['expires_on']));
    }

    if( isset($args['should_start']) && $args['should_start'] &&
        isset($args['starts_on']) && !empty($args['starts_on']) &&
        !is_numeric($args['starts_on']) ) {
      $request->set_param('starts_on', strtotime($args['starts_on']));
    }

    if(!isset($args['coupon_code']) || empty($args['coupon_code'])) {
      $request->set_param('coupon_code', strtoupper(uniqid()));
    }

    return $request;
  }
}

