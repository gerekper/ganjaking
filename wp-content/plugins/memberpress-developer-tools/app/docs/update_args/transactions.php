<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'trans_num' => array(
    'name' => __('Transaction Number', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'mp-txn-xxx...',
    'required' => false,
    'desc' => __('The unique Transaction Number.', 'memberpress-developer-tools')
  ),
  'amount' => array(
    'name' => __('Transaction Sub-Total', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('The base price for the Transaction (not including tax)', 'memberpress-developer-tools')
  ),
  'total' => array(
    'name' => __('Transaction Total', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('The total price for this Transaction (including tax)', 'memberpress-developer-tools')
  ),
  'tax_amount' => array(
    'name' => __('Tax Amount', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('Use this to manually set the tax amount for this Transaction', 'memberpress-developer-tools')
  ),
  'tax_rate' => array(
    'name' => __('Tax Rate', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.000',
    'required' => false,
    'desc' => __('Use this to manually set the tax rate for this Transaction. Can use up to 3 decimal places.', 'memberpress-developer-tools')
  ),
  'tax_desc' => array(
    'name' => __('Tax Amount', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A brief description of the tax applied. Example: UK VAT (20%)', 'memberpress-developer-tools')
  ),
  'member' => array(
    'name' => __('Member ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The Member\'s WordPress User ID.', 'memberpress-developer-tools')
  ),
  'membership' => array(
    'name' => __('Membership ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The Membership ID associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'coupon' => array(
    'name' => __('Coupon ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => false,
    'desc' => __('The Coupon ID associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'status' => array(
    'name' => __('Transaction Status', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'pending',
    'required' => false,
    'valid_values' => __('pending, complete, failed, or refunded', 'memberpress-developer-tools'),
    'desc' => __('The status of this Transaction. Can be "pending", "complete", "failed", or "refunded". Must be set to "complete" for the member to be considered active on the Membership.', 'memberpress-developer-tools')
  ),
  'response' => array(
    'name' => __('Transaction Response', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A place where you can store Payment Gateway POST or GET responses for later reference.', 'memberpress-developer-tools')
  ),
  'gateway' => array(
    'name' => __('Gateway ID', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'manual',
    'required' => false,
    'valid_values' => __('manual, free, or the ID of any live Gateway setup in your MemberPress Options', 'memberpress-developer-tools'),
    'desc' => __('The Payment Gateway to use for this Transaction.', 'memberpress-developer-tools')
  ),
  'subscription' => array(
    'name' => __('Subscription ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => false,
    'desc' => __('The ID of the Recurring Subscription CPT associated with this Transaction.', 'memberpress-developer-tools')
  ),
  'created_at' => array( //BLAIR I ADDED THIS ONE
    'name' => __('Created At Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => __('Current Timestamp', 'memberpress-developer-tools'),
    'required' => false,
    'desc' => __('The date the Transaction was created. This should be in a MySQL datetime format. All dates stored in the database should be in UTC timezone.', 'memberpress-developer-tools')
  ),
  'expires_at' => array( //BLAIR I ADDED THIS ONE
    'name' => __('Expires At Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '0000-00-00 00:00:00',
    'required' => false,
    'desc' => __('The date the Transaction will expire on. This should be in a MySQL datetime format. All dates stored in the database should be in UTC timezone. Note: Leave at default to create a Transaction that last\'s a lifetime (aka never expires).', 'memberpress-developer-tools')
  ),
  'send_welcome_email' => array(
    'name' => __('Send Welcome Email', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('When set to true this will trigger a welcome email to send to the new member. If the user has already received a welcome email, and the membership this transaction is for does not have its own welcome email configured, then no email will be sent.', 'memberpress-developer-tools')
  ),
  'send_receipt_email' => array(
    'name' => __('Send Receipt Email', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('When set to true this will trigger a transaction receipt email to be sent.', 'memberpress-developer-tools')
  ),
);
