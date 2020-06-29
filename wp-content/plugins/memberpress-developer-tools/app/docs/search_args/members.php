<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'search' => array(
    'description'        => __('Limit results to those matching a string. Can only search by email or username.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'sanitize_text_field',
  )
  // TODO: We'll get to this eventually
  //'membership' => array(
  //  'description'        => __('Limit results to members who are active on a specific membership.'),
  //  'type'               => 'integer',
  //  'sanitize_callback'  => 'absint',
  //),
);

