<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'event' => array(
    'name' => __('Event', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'login',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('This is a string "slug" for your event type. We recommend using a naming convention such as "login" or "subscription-changed" etc.', 'memberpress-developer-tools')
  ),
  'args' => array(
    'name' => __('Arguments', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('Any arguments that will be useful for you when recalling this event at some point in the future. For example, when a reminder is sent out in MemberPress we store the ID of the Reminder Custom Post Type as an argument.', 'memberpress-developer-tools')
  ),
  'evt_id' => array(
    'name' => __('Event ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => 0,
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('An ID for the event. For example, if your evt_id_type was set to "users", then this would be the integer ID for the User account.', 'memberpress-developer-tools')
  ),
  'evt_id_type' => array(
    'name' => __('Event ID Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'users',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The type associated with the evt_id. So for example if your evt_id was the id of a Transaction, then the id type should be "transactions"', 'memberpress-developer-tools')
  ),
);
