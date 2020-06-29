<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  "cancel_subscription" => (object)array(
    'name'   => __('Cancel Subscription', 'memberpress-developer-tools'),
    'desc'   => __('Cancel a given subscription.', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id/cancel',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1,
      'custom_response' => array(
        'message' => __('The subscription was successfully cancelled.', 'memberpress-developer-tools')
      )
    )
  ),
  "expire_subscription" => (object)array(
    'name'   => __('Expire Subscription', 'memberpress-developer-tools'),
    'desc'   => __('Expire all unexpired transactions associated with the Subscription.', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id/expire',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1,
      'custom_response' => array(
        'message' => __('This subscription is now expired.', 'memberpress-developer-tools')
      )
    )
  ),
);

