<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class MpdnAppCtrl {
  public function __construct() {
    $this->load_hooks();
  }

  public function load_hooks() {
    // Back end stuff
    add_action('admin_menu',  array($this, 'menu'));
    add_action('admin_init',  array($this, 'save_admin_page'));

    // App hooks
    add_action('mepr-signup',                     array($this, 'capture_mepr_signups'));
    add_action('mepr-save-account',               array($this, 'capture_mepr_account_save'));
    add_action('personal_options_update',         array($this, 'capture_wp_profile_update'));
    add_action('edit_user_profile_update',        array($this, 'capture_wp_profile_update'));
    add_action('wp_login',                        array($this, 'capture_logins'), 11, 2);
  }

  public function menu() {
    $page_title = 'MeprToolbox';
    $exists = $this->toplevel_menu_exists($page_title);

    if(!$exists) {
      add_menu_page(
        $page_title . ' - Display Name',
        $page_title,
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page'),
        'dashicons-hammer'
      );
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Display Name',
        'Display Name',
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page')
      );
    }
    else {
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Display Name',
        'Display Name',
        'manage_options',
        'mepr-toolbox-display-name',
        array($this, 'admin_page')
      );
    }
  }

  public function toplevel_menu_exists($title) {
    global $menu;

    foreach($menu as $item) {
      if(strtolower($item[0]) == strtolower($title)) {
        return true;
      }
    }

    return false;
  }

  public function admin_page() {
    $selected = get_option('mpdn_display_name_type', 'full_name');
    $checked = get_option('mpdn_force_update', false);
    $checked2 = get_option('mpdn_force_profile_save', true);
    include(MPDNVIEWSPATH . '/admin/admin_page.php');
  }

  public function save_admin_page() {
    if(!isset($_POST['mpdn-admin-page-submit'])) { return; }

    $selected = $_POST['mpdn-set-to'];
    $checked = isset($_POST['mpdn-force-update']);
    $checked2 = isset($_POST['mpdn-force-profile-save']);

    update_option('mpdn_display_name_type', $selected);
    update_option('mpdn_force_update', $checked);
    update_option('mpdn_force_profile_save', $checked2);
  }

  public function get_display_name($user) {
    global $user_ID;
    $field = get_option('mpdn_display_name_type', 'full_name');

    $old_user_ID = $user_ID;
    $user_ID = $user->ID;

    $display_name = $tmp = do_shortcode('[mepr-account-info field="' . $field . '"]');
    $tmp = str_replace(array('.',','), array('',''), $tmp);

    if(empty(trim($tmp))) {
      $display_name = $user->user_login;
    }

    $user_ID = $old_user_ID;

    return $display_name;
  }

  public function save_display_name($user) {
    if(isset($user->ID) && (int)$user->ID > 0) {
      if(MeprUtils::is_mepr_admin($user->ID)) { return; }

      $display_name = $this->get_display_name($user);

      $user->display_name = $display_name;
      $user->store();
      //Set the nickname to the display name also, why not?
      update_user_meta($user->ID, 'nickname', $display_name);
    }
  }

  public function capture_mepr_signups($txn) {
    $user = new MeprUser($txn->user_id);
    $this->save_display_name($user);
  }

  public function capture_mepr_account_save($user) {
    $this->save_display_name($user);
  }

  public function capture_wp_profile_update($user_id) {
    $checked2 = get_option('mpdn_force_profile_save', true);

    if($checked2) {
      if(MeprUtils::is_mepr_admin($user_id)) { return; }

      $user = new MeprUser($user_id);
      $display_name = $this->get_display_name($user); // Set artificially or it doesn't work
      $_POST['display_name'] = $_REQUEST['display_name'] = $display_name;
      $_POST['nickname'] = $_REQUEST['nickname'] = $display_name;
      $this->save_display_name($user);
    }
  }

  public function capture_logins($user_login, $wp_user) {
    $checked = get_option('mpdn_force_update', false);

    if($checked && !MeprUtils::is_mepr_admin($wp_user->ID)) {
      $user = new MeprUser($wp_user->ID);
      $this->save_display_name($user);
    }
  }
}
