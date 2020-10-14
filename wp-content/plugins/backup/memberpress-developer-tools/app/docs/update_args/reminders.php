<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'trigger_length' => array(
    'name' => __('Reminder Trigger Length', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '1',
    'required' => false,
    'desc' => __('The number of trigger_interval units for this reminder.', 'memberpress-developer-tools')
  ),
  'trigger_interval' => array(
    'name' => __('Reminder Trigger Inverval Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'days',
    'required' => false,
    'valid_values' => __('hours, days, weeks, months, years', 'memberpress-developer-tools'),
    'desc' => __('The interval type for this Reminder\'s trigger. Used in conjunction with trigger_length. If you wanted to send a Reminder 1 week before a Subscription expires then you\'d set trigger_length to 1, trigger_interval to "weeks", and trigger_timing to "before".', 'memberpress-developer-tools')
  ),
  'trigger_timing' => array(
    'name' => __('Reminder Trigger Timing', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'before',
    'required' => false,
    'desc' => __('Whether this reminder should happen before or after the event has occurred.', 'memberpress-developer-tools')
  ),
  'trigger_event' => array(
    'name' => __('Reminder Trigger Event', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'sub-expires',
    'required' => false,
    'valid_values' => __('sub-expires, cc-expires, member-signup, signup-abandoned, sub-renews', 'memberpress-developer-tools'),
    'desc' => __('The event which should trigger this Reminder to be sent out.', 'memberpress-developer-tools')
  ),
);

