<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  "refund_transaction" => (object)array(
    'name'   => __('Refund Transaction', 'memberpress-developer-tools'),
    'desc'   => __('Refund a given transaction.', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id/refund',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1,
      'custom_response' => array(
        'message' => __('The transaction was successfully refunded.', 'memberpress-developer-tools')
      )
    )
  ),
  "refund_transaction_and_cancel" => (object)array(
    'name'   => __('Refund Transaction & Cancel Subscription', 'memberpress-developer-tools'),
    'desc'   => __('Refund a given transaction and cancel its associated subscription.', 'memberpress-developer-tools'),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id/refund_and_cancel',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1,
      'custom_response' => array(
        'message' => __('The transaction was successfully refunded and it\'s associated subscription was cancelled.', 'memberpress-developer-tools')
      )
    )
  ),
);

