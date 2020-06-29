<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'event' => array(
    'description'        => __('Limit results to those matching a specific named event.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'absint',
  ),
);
