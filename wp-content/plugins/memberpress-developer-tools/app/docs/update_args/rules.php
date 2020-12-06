<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'title' => array(
    'name' => __('Title', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The title of Rule you are creating.', 'memberpress-developer-tools')
  ),
  'rule_type' => array(
    'name' => __('Rule Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'all',
    'required' => false,
    'valid_values' => __('all, custom, all_tax_* (* = TAXONOMY), all_* (* = CPT), single_* (* = CPT), parent_* (* = CPT), category, tag, partial, or ?', 'memberpress-developer-tools'), //BLAIR TODO -- I'm missing the '#^tax_(.*?)\|\|cpt_(.*?)$# one cuz I wasn't sure what it was
    'desc' => __('The type of Rule you are creating.', 'memberpress-developer-tools')
  ),
  'rule_content' => array(
    'name' => __('Rule Content', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The ID of the rule_type being protected. For example, if you protect a Single Page, then you would set this to the ID of that page. If you protect an entire category, then you would set this to the ID of that category.', 'memberpress-developer-tools')
  ),
  'is_rule_content_regex' => array(
    'name' => __('Use Regular Expression', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether to use Regular Expression matching on "custom" (aka Custom URI) rule_type\'s.', 'memberpress-developer-tools')
  ),
  'authorized_memberships' => array(
    'name' => __('Memberships With Access', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of Membership ID\'s which grant the Member(s) active on the Membership(s) access to the Content being protected by this Rule.', 'memberpress-developer-tools')
  ),
  'authorized_members' => array(
    'name' => __('Members With Access', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of User ID\'s which grant the Member(s) access to the Content being protected by this Rule.', 'memberpress-developer-tools')
  ),
  'authorized_roles' => array(
    'name' => __('User Roles With Access', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of User Role slugs which grant the Member(s) with those Role(s) access to the Content being protected by this Rule.', 'memberpress-developer-tools')
  ),
  'authorized_capabilities' => array(
    'name' => __('User Capabilities With Access', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of User Capability slugs which grant the Member(s) with those Capability(s) access to the Content being protected by this Rule.', 'memberpress-developer-tools')
  ),
  'drip_enabled' => array(
    'name' => __('Enable Dripping', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether Dripping should be enabled on this Rule.', 'memberpress-developer-tools')
  ),
  'drip_amount' => array(
    'name' => __('Dripping Amount', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '1',
    'required' => false,
    'desc' => __('The number of units to delay Dripping by.', 'memberpress-developer-tools')
  ),
  'drip_unit' => array(
    'name' => __('Dripping Unit', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'days',
    'required' => false,
    'valid_values' => __('days, weeks, months, or years', 'memberpress-developer-tools'),
    'desc' => __('The unit to Drip the Content by.', 'memberpress-developer-tools')
  ),
  'drip_after' => array(
    'name' => __('Dripping Base Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'registers',
    'required' => false,
    'valid_values' => __('registers, fixed, rule-products (based on the date the Member purchased ANY of the Memberships defined in authorized_memberships), or any single Membership ID (based on the signup date the Member purchased the Membership with this ID)', 'memberpress-developer-tools'),
    'desc' => __('The date to base the Dripping off of.', 'memberpress-developer-tools')
  ),
  'drip_after_fixed' => array(
    'name' => __('Dripping Fixed Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('If drip_after is set to "fixed", then this is required and must be a valid date in the future which PHP\'s strtotime() function can interpret.', 'memberpress-developer-tools')
  ),
  'expires_enabled' => array(
    'name' => __('Enable Content Expiration', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether Content Expiration should be enabled on this Rule.', 'memberpress-developer-tools')
  ),
  'expires_amount' => array(
    'name' => __('Content Expiration Amount', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '1',
    'required' => false,
    'desc' => __('The number of units to delay Content Expiration by.', 'memberpress-developer-tools')
  ),
  'expires_unit' => array(
    'name' => __('Content Expiration Unit', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'days',
    'required' => false,
    'valid_values' => __('days, weeks, months, or years', 'memberpress-developer-tools'),
    'desc' => __('The unit to Expire the Content by.', 'memberpress-developer-tools')
  ),
  'expires_after' => array(
    'name' => __('Content Expiration Base Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'registers',
    'required' => false,
    'valid_values' => __('registers, fixed, rule-products (based on the date the Member purchased ANY of the Memberships defined in authorized_memberships), or any single Membership ID (based on the signup date the Member purchased the Membership with this ID)', 'memberpress-developer-tools'),
    'desc' => __('The date to base the Content Expiration off of.', 'memberpress-developer-tools')
  ),
  'expires_after_fixed' => array(
    'name' => __('Content Expiration Fixed Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('If expires_after is set to "fixed", then this is required and must be a valid date in the future which PHP\'s strtotime() function can interpret.', 'memberpress-developer-tools')
  ),
  'unauth_excerpt_type' => array(
    'name' => __('Unauthorized Excerpt Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'default',
    'required' => false,
    'valid_values' => __('default, hide, more (aka everything before the &lt;!--more--&gt; tag), excerpt, or custom', 'memberpress-developer-tools'),
    'desc' => __('The type of excerpt from the protected Content to show to unauthorized visitors. If set to anything other than "default" this will override the global behavior set in the MemberPress Options for any Content protected by this Rule.', 'memberpress-developer-tools')
  ),
  'unauth_excerpt_size' => array(
    'name' => __('Unauthorized Excerpt Size', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '100',
    'required' => false,
    'desc' => __('This is only used if unauth_exceprt_type is set to "custom". It will show X characters of the actual protected Content as the excerpt to unauthorized visitors.', 'memberpress-developer-tools')
  ),
  'unauth_message_type' => array(
    'name' => __('Unauthorized Message Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'default',
    'required' => false,
    'valid_values' => __('default, hide, or custom', 'memberpress-developer-tools'),
    'desc' => __('The type of message to show to unauthorized users trying to view Content protected by this Rule. If set to anything other than "default" this will override the global behavior set in the MemberPress Options for any Content protected by this Rule.', 'memberpress-developer-tools')
  ),
  'unauth_message' => array(
    'name' => __('Unauthorized Message', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('If the unauth_message_type is set to "custom", then this string will be shown as the message which unauthorized users will see when they try to view Content protected by this Rule.', 'memberpress-developer-tools')
  ),
  'unauth_login' => array(
    'name' => __('Show/Hide Login Form', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'default',
    'required' => false,
    'valid_values' => __('default, show, or hide', 'memberpress-developer-tools'),
    'desc' => __('Whether or not to show/hide the Login Form, or leave at the default global settings in the MemberPress Options.', 'memberpress-developer-tools')
  ), // => 'default',
  //'auto_gen_title' => array(
  //), // => true
);

