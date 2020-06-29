<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'title' => array(
    'name' => __('Membership Title', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required', 'memberpress-developer-tools'),
    'desc' => __('The title of the Membership.', 'memberpress-developer-tools')
  ),
  'price' => array(
    'name' => __('Membership Price', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('The base price for the Membership.', 'memberpress-developer-tools')
  ),
  'period' => array(
    'name' => __('Membership Period', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '1',
    'required' => false,
    'desc' => __('The Period for the Membership billing cycles. For one-time payments leave this set to 1. If you wanted to bill the customer once a quarter, you would set this to 3 and the period_type to months. There are some limitations depending on the period_type.', 'memberpress-developer-tools')
  ),
  'period_type' => array(
    'name' => __('Membership Period Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'lifetime',
    'required' => false,
    'desc' => __('The billing period type. For non-recurring payments this should always be set to "lifetime". Other possible values are "weeks", "months" and "years". There are some limitations depending on the period.', 'memberpress-developer-tools')
  ),
  'signup_button_text' => array(
    'name' => __('Signup Button Text', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'Sign Up',
    'required' => false,
    'desc' => __('The text shown on the submit button shown on this Membership\'s registration form.', 'memberpress-developer-tools')
  ),
  'limit_cycles' => array(
    'name' => __('Enable Limited Billing Cycles', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether or not to limit the billing cycles for recurring membership payments. If set to true, MemberPress will cancel the recurring payments after the limit_cycles_num amount has been reached.', 'memberpress-developer-tools')
  ),
  'limit_cycles_num' => array(
    'name' => __('Limited Billing Cycles Number', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '2',
    'required' => false,
    'desc' => __('If limit_cycles is true, then this number is used to determine how many recurring billings should happen before MemberPress automatically cancels the Subscription. If set to 1, the Subscription will be cancelled immediately after the first payment has come through, however the member will maintain access until their Transaction has expired.', 'memberpress-developer-tools')
  ),
  'limit_cycles_action' => array(
    'name' => __('Limited Billing Cycles Action', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'expire',
    'required' => false,
    'valid_values' => __('expire or lifetime', 'memberpress-developer-tools'),
    'desc' => __('If limit_cycles is enabled, then this will determine the Member\'s access after their Subscription has reached its limit_cycles_num limit and has been cancelled. If set to "expire" then the Member will lose access after their last Transaction expires. If set to "lifetime", the Member will be granted lifetime access.', 'memberpress-developer-tools')
  ),
  'trial' => array(
    'name' => __('Enable Trial Period', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether or not there will be a trial period on this Membership. Trial periods ONLY work on auto-recurring payments.', 'memberpress-developer-tools')
  ),
  'trial_days' => array(
    'name' => __('Trial Period Days', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => false,
    'desc' => __('How many days the trial period should last before the regular billing cycles begin.', 'memberpress-developer-tools')
  ),
  'trial_amount' => array(
    'name' => __('Trial Period Amount', 'memberpress-developer-tools'),
    'type' => 'decimal',
    'default' => '0.00',
    'required' => false,
    'desc' => __('If a trial period is enabled, this specifies how much the user will pay for the trial period.', 'memberpress-developer-tools')
  ),
  'group' => array(
    'name' => __('Group ID', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => false,
    'desc' => __('The ID of a MemberPress Group which this Membership should belong to. Defaults to no Group.', 'memberpress-developer-tools')
  ),
  'group_order' => array(
    'name' => __('Group Order', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '0',
    'required' => false,
    'desc' => __('If the Membership belongs to a Group, this will determine its placement on the Group page and is also used to determine the Upgrade/Downgrade paths.', 'memberpress-developer-tools')
  ),
  'is_highlighted' => array(
    'name' => __('Group Pricing Box - Highlighted', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether or not to highlight this Membership in the Group. This adds extra CSS classes to make the Group pricing/features box stand out from the others.', 'memberpress-developer-tools')
  ),
  //'who_can_purchase' => array(
  //), //=> array(),
  'pricing_title' => array(
    'name' => __('Group Pricing Box - Title', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The title that shows at the top of the pricing box on a Group page.', 'memberpress-developer-tools')
  ),
  //'pricing_show_price' => array(
  //  'name' => __('Group Pricing Box - Show Price', 'memberpress-developer-tools'),
  //  'type' => 'bool',
  //  'default' => 'true',
  //  'required' => false,
  //  'desc' => __('Show the price or not on this Membership\'s pricing box on the Group page.', 'memberpress-developer-tools')
  //),
  'pricing_display' => array(
    'name' => __('Group Pricing Box - Price Display', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'auto',
    'required' => false,
    'desc' => __('How to display the price on this Membership\'s pricing box on the Group page.', 'memberpress-developer-tools')
  ),
  'pricing_heading_txt' => array(
    'name' => __('Group Pricing Box - Heading Text', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A heading to show on this pricing box. Shows below price, but above features/benefits list.', 'memberpress-developer-tools')
  ),
  'pricing_footer_txt' => array(
    'name' => __('Group Pricing Box - Footer Text', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('Show a message at the foot of the pricing box. Shows right above the purchase button.', 'memberpress-developer-tools')
  ),
  'pricing_button_txt' => array(
    'name' => __('Group Pricing Box - Button Text', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The text of the purchase button.', 'memberpress-developer-tools')
  ),
  'pricing_benefits' => array(
    'name' => __('Group Pricing Box - Features/Benefits List', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of strings. Each string will show as a line-item on the pricing box on the Group page.', 'memberpress-developer-tools')
  ),
  'register_price' => array(
    'name' => __('Register Price String', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A custom pricing string for this Membership. This is only used if register_price_action is set to "custom".', 'memberpress-developer-tools')
  ),
  'register_price_action' => array(
    'name' => __('Register Price Action', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'default',
    'required' => false,
    'valid_values' => __('default, custom, or hidden', 'memberpress-developer-tools'),
    'desc' => __('Whether to use the default pricing string generated by MemberPress, a custom pricing string, or to hide the pricing string completely.', 'memberpress-developer-tools')
  ),
  'thank_you_page_enabled' => array(
    'name' => __('Enable Custom Thank You Page Message', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('If set to true, the content on the Thank You page will be overridden with the message contained in thank_you_message value.', 'memberpress-developer-tools')
  ),
  'thank_you_message' => array(
    'name' => __('Thank You Page Message', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('A string containing the Custom Thank You Page Message which is shown on the Thank You page anytime this Membership is purchased. thank_you_page_enabled must be set to true before this will be shown.', 'memberpress-developer-tools')
  ),
  'custom_login_urls_enabled' => array(
    'name' => __('Enable Custom Login URLs', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('If set to true, you can modify the login redirection by using the custom_login_urls_default and/or the custom_login_urls to control where a member is taken to after logging in.', 'memberpress-developer-tools')
  ),
  'custom_login_urls_default' => array(
    'name' => __('Default Login Redirection URL For This Membership', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The URL the member will be redirected to by deafult when logging in if they have purchased this Membership. This overrides the global Login Redirect URL set in MemberPress -> Options.', 'memberpress-developer-tools')
  ),
  'custom_login_urls' => array(
    'name' => __('Custom Login Redirect URLs', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('An array of standard objects. Each object should have \$obj->url and \$obj->count. url is the URL the user should be redirected to when logging in, and count is the number of times they\'ve logged in. So for example, if you wanted to take your user to a specific landing page on their 4th login attempt, you would set the url to the landing page, and the count to 4.', 'memberpress-developer-tools')
  ),
  'expire_type' => array(
    'name' => __('Membership Expiration Type', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'none',
    'required' => false,
    'valid_values' => __('none, delay, or fixed', 'memberpress-developer-tools'),
    'desc' => __('If the Membership is a one-time payment (not auto-recurring), then you can set if/when it should expire. A value of none means it will be good for a lifetime. A value of delay means it expires at some delayed point in the future (example: 6 months). And a value of fixed means it will expire on a fixed date in the future (example December 31st 2019). Note: this does not work on recurring Memberships.', 'memberpress-developer-tools')
  ),
  'expire_after' => array(
    'name' => __('Expire Membership After', 'memberpress-developer-tools'),
    'type' => 'integer',
    'default' => '1',
    'required' => false,
    'desc' => __('If the expire_type is set to "delay", use this to determine how many expire_unit\'s before the Membership expires. This should be a number (example "6", for 6 days/months/weeks/years).', 'memberpress-developer-tools')
  ),
  'expire_unit' => array(
    'name' => __('Expire Membership Unit', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'days',
    'required' => false,
    'valid_values' => __('days, weeks, months, or years', 'memberpress-developer-tools'),
    'desc' => __('If the expire_type is set to "delay", use this to set the unit for the expire_after value. For example, if expire_after was set to "6" and this was set to "months", then the Membership would expire 6 months after the date the Member registered.', 'memberpress-developer-tools')
  ),
  'expire_fixed' => array(
    'name' => __('Expire Membership Fixed Date', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('If the expire_type is set to "fixed", then set this to the string of the date on which this Membership should expire. You can use any date format which PHP\'s strtotime() function can understand.', 'memberpress-developer-tools')
  ),
  'tax_exempt' => array(
    'name' => __('Tax Exempt', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Should this Membership be exempt from taxes or not.', 'memberpress-developer-tools')
  ),
  'allow_renewal' => array(
    'name' => __('Allow Renewals', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Allow Members to renew one-time payments before they have expired. Note: this only works on one-time payment Memberships which have a "delay" expire_type value.', 'memberpress-developer-tools')
  ),
  'access_url' => array(
    'name' => __('Membership Access URL', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('Use this to add a URL which should be used as the page the member can access the content for this Membership subscription.', 'memberpress-developer-tools')
  ),
  //'emails' => array(
  //), //=> array(),
  'simultaneous_subscriptions' => array(
    'name' => __('Allow Simultaneous Subscriptions', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Allow Members to have multiple active Subscriptions to this Product simultaneously. Note: Do not use this if allow_renewal is set to true.', 'memberpress-developer-tools')
  ),
  'use_custom_template' => array(
    'name' => __('Use Custom Template', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Whether or not to use a Custom Page Template for this Membership page.', 'memberpress-developer-tools')
  ),
  'custom_template' => array(
    'name' => __('Custom Page Template Path', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('The relative path to your template file.', 'memberpress-developer-tools')
  ), //=> '',
  'customize_payment_methods' => array(
    'name' => __('Customize Payment Methods', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('Allow Payment Methods to be disabled on this Membership. A disabled payment method cannot be used by a Member at checkout.', 'memberpress-developer-tools')
  ),
  'custom_payment_methods' => array(
    'name' => __('Custom Payment Methods', 'memberpress-developer-tools'),
    'type' => 'array',
    'default' => '[]',
    'required' => false,
    'desc' => __('If customize_payment_methods is true, then this must be an array of the valid payment method ID\'s for this Membership. Note: If filtering valid payment methods, this MUST have at least one gateway ID.', 'memberpress-developer-tools')
  ),
  //'customize_profile_fields' => array(
  //), //=> false,
  //'custom_profile_fields' => array(
  //), //=> array()
);

