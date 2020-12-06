<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'member' => array(
    'description'        => __('Limit results to subscriptions of a specific member.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'sanitize_callback'  => 'absint',
  ),
  'membership' => array(
    'description'        => __('Limit results to subscriptions for a specific membership.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'sanitize_callback'  => 'absint',
  ),
  'coupon' => array(
    'description'        => __('Limit results to subscriptions created with a specific coupon.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'sanitize_callback'  => 'absint',
  ),
  'status' => array(
    'description'        => __('Limit results to subscriptions with a given status.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'sanitize_text_field',
  ),
  'gateway' => array(
    'description'        => __('Limit results to subscriptions created with a specific payment gateway.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'sanitize_text_field',
  ),
);

