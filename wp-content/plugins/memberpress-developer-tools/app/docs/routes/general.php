<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

$wp_user = wp_get_current_user();
return array(
  "me" => (object)array(
    'name'   => __('Me', 'memberpress-developer-tools'),
    'desc'   => __('Test MemberPress REST endpoint Authentication. Returns a status of 200 on success and 401 on failure.', 'memberpress-developer-tools'),
    'method' => 'GET',
    'url'    => rest_url('mp/v1/me'),
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => array('success' => true, 'data' => array('username' => $wp_user->user_login))
  ),
  "webhooks/subscribe" => (object)array(
    'name'   => __('Webhooks Subscribe', 'memberpress-developer-tools'),
    'desc'   => __('Subscribe to any or all of MemberPress Developer Tools webhook(s). Returns a 409 Error on failure.', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url('mp/v1/webhooks/subscribe'),
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => array(
      'url' => array(
        'name' => __('URL', 'memberpress-developer-tools'),
        'type' => 'string',
        'required' => __('Required', 'memberpress-developer-tools'),
        'desc' => __('The url where the webhook will send the request.', 'memberpress-developer-tools')
      ),
      'event' => array(
        'name' => __('Event', 'memberpress-developer-tools'),
        'type' => 'string',
        'required' => __('Required', 'memberpress-developer-tools'),
        'valid_values' => '"all", "' . implode('", "', $registered_events) . '"',
        'desc' => __('The Webhook will be triggered by and send data for this Event.', 'memberpress-developer-tools')
      ),
    ),
    'sample_request' => array('url'=>'https://example.com', 'event'=>'transaction-completed'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => array('success' => true, 'data' => array('id' => 1324))
  ),
  "webhooks/unsubscribe/:id" => (object)array(
    'name'   => __('Webhooks Unsubscribe', 'memberpress-developer-tools'),
    'desc'   => __('Unsubscribe from any webhook. Returns a 409 Error on failure.', 'memberpress-developer-tools'),
    'method' => 'DELETE',
    'url'    => rest_url('mp/v1/webhooks/unsubscribe/:id'),
    'auth'   => false,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('None', 'memberpress-developer-tools'),
    'resp'   => array('success' => true)
  ),
);
