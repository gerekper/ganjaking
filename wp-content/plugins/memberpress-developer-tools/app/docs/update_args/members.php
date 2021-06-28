<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'first_name' => array(
    'name' => __('First Name', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The User\'s First Name.', 'memberpress-developer-tools')
  ),
  'last_name' => array(
    'name' => __('Last Name', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The User\'s Last Name.', 'memberpress-developer-tools')
  ),
  'email' => array(
    'name' => __('Email Address', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The User\'s Email Address.', 'memberpress-developer-tools')
  ),
  'username' => array(
    'name' => __('Username', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The username for this User. If you\'re using email addresses as the Username, then both username and email should be set to the same string.', 'memberpress-developer-tools')
  ),
  'password' => array(
    'name' => __('Plaintext Password', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A plaintext password which will be hashed and stored with this user. If creating and a password is not provided then a random password will be automatically generated for the member on create. If updating and a password is not provided then it will remain as is.', 'memberpress-developer-tools')
  ),
  //'add_to_membership' => array(
  //  'name' => __('Add to Membership', 'memberpress-developer-tools'),
  //  'type' => 'bool',
  //  'default' => 'false',
  //  'required' => false,
  //  'desc' => __('If this is set then a transaction will be added to MemberPress for this member for a given membership.', 'memberpress-developer-tools')
  //),
  //'membership' => array(
  //  'name' => __('Membership', 'memberpress-developer-tools'),
  //  'type' => 'integer',
  //  'default' => 'null',
  //  'required' => __('Required if add_to_membership is true', 'memberpress-developer-tools'),
  //  'desc' => __('The id of the membership we\'re adding the member to.', 'memberpress-developer-tools')
  //),
  'transaction' => array(
    'name' => __('Transaction', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('Works only when creating a new Member. This is an array conforming to the documentation for the "Create Transaction" API route (except the "member" parameter will be ignored in favor of the id of the newly created member). This will create an initial transaction for the member to add them to a specific membership.', 'memberpress-developer-tools')
  ),
  'send_welcome_email' => array(
    'name' => __('Send Welcome Email', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Works only when creating a new Member and if a transaction has been specified. When set to true this will trigger a welcome email to send to the new member. If the user has already received a welcome email, and the membership this transaction is for does not have its own welcome email configured, then no email will be sent.', 'memberpress-developer-tools')
  ),
  'send_password_email' => array(
    'name' => __('Send Password Email', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Works only when creating a new Member. When set to true this will trigger a standard WordPress password recovery email to be sent to the new member.', 'memberpress-developer-tools')
  ),
  'address1' => array(
    'name' => __('Address 1', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address street line 1 of the user.', 'memberpress-developer-tools')
  ),
  'address2' => array(
    'name' => __('Address 2', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address street line 2 of the user.', 'memberpress-developer-tools')
  ),
  'city' => array(
    'name' => __('City', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address city of the user.', 'memberpress-developer-tools')
  ),
  'state' => array(
    'name' => __('State', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address state of the user.', 'memberpress-developer-tools')
  ),
  'zip' => array(
    'name' => __('Zipcode', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address zip code of the user.', 'memberpress-developer-tools')
  ),
  'country' => array(
    'name' => __('Country', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The address country code of the user.', 'memberpress-developer-tools')
  ),
);
