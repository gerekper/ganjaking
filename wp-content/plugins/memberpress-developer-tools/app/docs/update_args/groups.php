<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  'title' => array(
    'name' => __('Group Title', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('This is the title of the Group.', 'memberpress-developer-tools')
  ),
  'content' => array(
    'name' => __('Group Content', 'memberpress-developer-tools'),
    'type' => 'text',
    'default' => '',
    'required' => false,
    'desc' => __('This content will appear above the Group pricing table. You can customize the position of the pricing table simply by including the shortcode: [mepr-group-price-boxes] in the content here wherever you want.', 'memberpress-developer-tools')
  ),
  'pricing_page_disabled' => array(
    'name' => __('Disable Pricing Page', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('This will disable the Group pricing page when set to true. When visitors try to go to the Group pricing page they\'ll get a 404 error if this is set to true.', 'memberpress-developer-tools')
  ),
  'is_upgrade_path' => array(
    'name' => __('Is this Group an Upgrade Path?', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('When set to true, MemberPress may apply a proration when users switch subscriptions within the group.', 'memberpress-developer-tools')
  ),
  'group_theme' => array(
    'name' => __('Group Theme', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => 'false',
    'required' => false,
    'valid_values' => "'".implode(__("' or '", 'memberpress-developer-tools'), MeprGroup::group_themes(false,true))."'",
    'desc' => __('When set to true, MemberPress may apply a proration when users switch subscriptions within the group.', 'memberpress-developer-tools')
  ),
  'page_button_class' => array(
    'name' => __('Custom Button CSS Class', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('This class will be added to the buttons of the pricing page.', 'memberpress-developer-tools')
  ),
  'page_button_highlighted_class' => array(
    'name' => __('Custom Higlighted Button CSS Class', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('This class will be added to any highlighted buttons on the pricing page.', 'memberpress-developer-tools')
  ),
  'page_button_disabled_class' => array(
    'name' => __('Custom Disabled Button CSS Class', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'desc' => __('This class will be added to any disabled buttons on the pricing page.', 'memberpress-developer-tools')
  ),
  'alternate_group_url' => array(
    'name' => __('Alternate Group URL', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => false,
    'valid_values' => __('Empty or a valid URL', 'memberpress-developer-tools'),
    'desc' => __('If not left blank then this url will override the current group pricing page URL.', 'memberpress-developer-tools')
  ),
  'use_custom_template' => array(
    'name' => __('Use custom page template', 'memberpress-developer-tools'),
    'type' => 'bool',
    'default' => 'false',
    'required' => false,
    'desc' => __('If checked then MemberPress will attempt to load the page with a custom page template.', 'memberpress-developer-tools')
  ),
  'custom_template' => array(
    'name' => __('Custom page template', 'memberpress-developer-tools'),
    'type' => 'string',
    'default' => '',
    'required' => __('Required if \'use_custom_template\' is true', 'memberpress-developer-tools'),
    'valid_values' => __('Any valid page template', 'memberpress-developer-tools'),
    'desc' => __('If \'use_custom_template\' is checked then this value will be used as the page template for the Group pricing page.', 'memberpress-developer-tools')
  ),
);

