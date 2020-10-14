<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'group' => array(
    'description'        => __('Limit results to those matching a specific group.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'sanitize_callback'  => 'absint',
  ),
);

