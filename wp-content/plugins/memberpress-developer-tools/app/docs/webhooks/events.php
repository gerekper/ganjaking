<?php
return array(
  'all' => (object)array(
    'label' => __('<b>All Events</b>', 'memberpress-developer-tools'),
    'desc'  => __('Send a webhook for any of the events listed below.', 'memberpress-developer-tools'),
    'type'  => 'mixed'
  ),

  /***** Events for Members *****/
  'member-added' => (object)array(
    'label' => __('Member Added', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a new member registers but before their payment details are accepted.', 'memberpress-developer-tools'),
    'type'  => 'member'
  ),
  'member-signup-completed' => (object)array(
    'label' => __('Member Signup Completed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a new member completes the signup and their payment is accepted.', 'memberpress-developer-tools'),
    'type'  => 'member'
  ),
  'member-account-updated' => (object)array(
    'label' => __('Member Account Info Updated', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a member updates his/her account information.', 'memberpress-developer-tools'),
    'type'  => 'member'
  ),
  'member-deleted' => (object)array(
    'label' => __('Member Deleted', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a member is deleted from the system.', 'memberpress-developer-tools'),
    'type'  => 'member'
  ),
  'login' => (object)array(
    'label' => __('Member Logged In', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any member logs in.', 'memberpress-developer-tools'),
    'type'  => 'member'
  ),

  /***** Events for Subscriptions *****/
  'subscription-created' => (object)array(
    'label' => __('Subscription Created', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a subscription is created.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-paused' => (object)array(
    'label' => __('Subscription Paused', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a subscription is paused.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-resumed' => (object)array(
    'label' => __('Subscription Resumed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a subscription is resumed.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-stopped' => (object)array(
    'label' => __('Subscription Stopped', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a subscription is cancelled.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-upgraded' => (object)array(
    'label' => __('Subscription Upgraded', 'memberpress-developer-tools'),
    'desc'  => __('DEPRECATED - Please use Subscription Upgraded to (Recurring | Non-Recurring) instead.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-downgraded' => (object)array(
    'label' => __('Subscription Downgraded', 'memberpress-developer-tools'),
    'desc'  => __('DEPRECATED - Please use Subscription Downgraded to (Recurring | Non-Recurring) instead.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-upgraded-to-one-time' => (object)array(
    'label' => __('Subscription Upgraded to Non-Recurring', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a user upgrades their Subscription and the new Subscription is non-recurring (one-time payment).', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'subscription-upgraded-to-recurring' => (object)array(
    'label' => __('Subscription Upgraded to Recurring', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a user upgrades their Subscription and the new Subscription is auto-recurring (payments happen automatically each payment cycle).', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-downgraded-to-one-time' => (object)array(
    'label' => __('Subscription Downgraded to Non-Recurring', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a user downgrades their Subscription and the new Subscription is non-recurring (one-time payment).', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'subscription-downgraded-to-recurring' => (object)array(
    'label' => __('Subscription Downgraded to Recurring', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a user downgrades their Subscription and the new Subscription is auto-recurring (payments happen automatically each payment cycle).', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),
  'subscription-expired' => (object)array(
    'label' => __('Subscription Expired', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a subscription expires.', 'memberpress-developer-tools'),
    'type'  => 'subscription'
  ),

  /***** Events for Transactions *****/
  'transaction-completed' => (object)array(
    'label' => __('Transaction Completed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a transaction has completed on MemberPress.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'transaction-refunded' => (object)array(
    'label' => __('Transaction Refunded', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any transaction is refunded.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'transaction-failed' => (object)array(
    'label' => __('Transaction Failed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any transaction fails.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'transaction-expired' => (object)array(
    'label' => __('Transaction Expired', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any transaction expires.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'offline-payment-pending' => (object)array(
    'label' => __('Offline Payment Pending', 'memberpress-developer-tools'),
    'desc'  => __('Sent when "Admin must manually complete transactions" option is enabled on an Offline Payment gateway, and a user has just completed a signup with an offline payment method.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'offline-payment-complete' => (object)array(
    'label' => __('Offline Payment Complete', 'memberpress-developer-tools'),
    'desc'  => __('Sent when "Admin must manually complete transactions" option is enabled on an Offline Payment gateway, and an admin has manually marked a transaction as completed.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'offline-payment-refunded' => (object)array(
    'label' => __('Offline Payment Refunded', 'memberpress-developer-tools'),
    'desc'  => __('Sent when an Admin manually marks an offline transaction as refunded.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  // Recurring Transactions
  'recurring-transaction-completed' => (object)array(
    'label' => __('Recurring Transaction Completed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a transaction associated with a recurring subscription completes.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'renewal-transaction-completed' => (object)array(
    'label' => __('Renewal Transaction Completed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when the 2nd+ transaction associated with a recurring subscription completes. This does not fire for the first transaction in a recurring subscription, only the 2nd or later payments will trigger this event.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'recurring-transaction-failed' => (object)array(
    'label' => __('Recurring Transaction Failed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a recurring transaction fails. Because recurring transactions typically involve a 3rd party gateway it\'s good to know when a payment has failed on the gateway\'s end.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'recurring-transaction-expired' => (object)array(
    'label' => __('Recurring Transaction Expired', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any transaction associated with a subscription expires. This event will not indicate that a recurring subscription is expiring, just that a transaction associated with it is expiring. If you\'re looking through a subscription expiration event, try \'subscription-expired\'.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  // Non-Recurring Transactions
  'non-recurring-transaction-completed' => (object)array(
    'label' => __('Non-Recurring Transaction Completed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a non-recurring transaction has completed on MemberPress.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'non-recurring-transaction-expired' => (object)array(
    'label' => __('Non-Recurring Transaction Expired', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any transaction not associated with a subscription expires.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),

  /***** Events from Reminders *****/
  'after-member-signup-reminder' => (object)array(
    'label' => __('After Member Registers', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any After Member Signup reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'after-signup-abandoned-reminder' => (object)array(
    'label' => __('After Signup Abandoned', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any After Member Signup Abandoned reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'before-sub-expires-reminder' => (object)array(
    'label' => __('Before Subscription Expires', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any Before Subscription Expires reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'transaction' //These are txns yo
  ),
  'after-sub-expires-reminder' => (object)array(
    'label' => __('After Subscription Expires', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any After Subscription Expires reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'transaction' //These are txns yo
  ),
  'before-sub-renews-reminder' => (object)array(
    'label' => __('Before Subscription Renews', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any Before Subscription Renews reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'transaction' //These are txns yo
  ),
  'after-cc-expires-reminder' => (object)array(
    'label' => __('After Credit Card Expires', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any After Credit Card Expires reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'subscription' //These are subs yo
  ),
  'before-cc-expires-reminder' => (object)array(
    'label' => __('Before Credit Card Expires', 'memberpress-developer-tools'),
    'desc'  => __('Sent when any Before Credit Card Expires reminder fires.', 'memberpress-developer-tools'),
    'type'  => 'subscription' //These are subs yo
  ),

  /** Events for Corporate Accounts */
  'sub-account-added' => (object)array(
    'label' => __('Sub Account Added', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a sub account is added by the corporate account.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
  'sub-account-removed' => (object)array(
    'label' => __('Sub Account Removed', 'memberpress-developer-tools'),
    'desc'  => __('Sent when a sub account is removed by the corporate account.', 'memberpress-developer-tools'),
    'type'  => 'transaction'
  ),
);
