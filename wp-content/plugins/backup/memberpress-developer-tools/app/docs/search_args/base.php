<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// The format of this array is determined by WP-API
return array(
  'page' => array(
    'description'        => __('Current page of the collection.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'default'            => 1,
    'sanitize_callback'  => 'absint',
  ),
  'per_page' => array(
    'description'        => __('Maximum number of items to be returned in result set.', 'memberpress-developer-tools'),
    'type'               => 'integer',
    'default'            => 10,
    'sanitize_callback'  => 'absint',
  ),
  'search' => array(
    'description'        => __('Limit results to those matching a string.', 'memberpress-developer-tools'),
    'type'               => 'string',
    'sanitize_callback'  => 'sanitize_text_field',
  ),
);

