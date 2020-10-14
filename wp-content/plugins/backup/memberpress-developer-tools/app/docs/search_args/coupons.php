<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'code' => array(
    'description'        => __('Grab the coupon with the given code.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'sanitize_text_field',
  ),
  // TODO: We'll get to this eventually
  //'membership' => array(
  //  'description'        => __('List all of the coupons for a given membership.'),
  //  'type'               => 'integer',
  //  'sanitize_callback'  => 'absint',
  //),
);

