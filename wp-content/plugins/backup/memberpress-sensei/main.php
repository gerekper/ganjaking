<?php
/*
Plugin Name: MemberPress + Sensei
Plugin URI: http://www.memberpress.com/
Description: Synchronizes MemberPress Memberships with Sensei Courses.
Version: 1.0.2
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-sensei
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPSENSEI_PLUGIN_SLUG', plugin_basename(__FILE__));
  define('MPSENSEI_PLUGIN_NAME', dirname(MPSENSEI_PLUGIN_SLUG));
  define('MPSENSEI_PATH', WP_PLUGIN_DIR.'/'.MPSENSEI_PLUGIN_NAME);
  define('MPSENSEI_URL', plugins_url('/'.MPSENSEI_PLUGIN_NAME));
  define('MPSENSEI_EDITION', 'memberpress-sensei');
  define('MPSENSEI_DBOPS_NAME', 'mepr_sensei_options');

  function mepr_sensei_show_menu_page() {
    if(!class_exists('WooThemes_Sensei_Course')) {
      echo '<div class="wrap"><p style="font-weight:bold;color:red;">Sensei is not installed, or is not activated in your Plugins page.</p></div>';
      return;
    }

    $memberships  = get_posts(array('numberposts' => -1, 'post_type' => 'memberpressproduct', 'post_status' => 'publish'));
    $courses      = WooThemes_Sensei_Course::get_all_courses();
    $map          = get_option(MPSENSEI_DBOPS_NAME, array());

    ?>
      <div class="wrap">
        <h1><?php _e('Sync Memberships with Sensei Courses', 'memberpress-sensei'); ?></h1>

        <?php if(isset($_GET['mpsensei_saved'])): ?>
          <div id="message" class="updated notice notice-success below-h2">
            <p><?php _e('Options have been saved', 'memberpress-sensei'); ?>
          </div>
        <?php endif; ?>

        <div id="mpsensei_wrap">
          <?php if(!empty($memberships) && !empty($courses)): ?>
            <?php foreach($memberships as $m): ?>
              <form action="" method="post">
                <p><strong><?php echo $m->post_title; ?></strong></p>
                <div class="mpsensei_courses_list">
                  <?php foreach($courses as $c): ?>
                    <input type="checkbox" name="mpsensei_map[<?php echo $m->ID; ?>][<?php echo $c->ID; ?>]" <?php checked(isset($map[$m->ID][$c->ID])); ?> />
                    <label><?php echo $c->post_title; ?></label><br/>
                  <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
                <br/>
                <input type="submit" name="mpsensei_save" value="<?php _e('Save', 'memberpress-sensei'); ?>" />
              </form>
          <?php else: ?>
            <p><?php _e('No Memberships or Courses have been created', 'memberpress-sensei'); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php
  }

  function mepr_sensei_menu_page() {
    add_submenu_page('memberpress', __('Sync w/Sensei', 'memberpress', 'memberpress-sensei'), __('Sync w/Sensei', 'memberpress', 'memberpress-sensei'), 'administrator', MPSENSEI_EDITION, 'mepr_sensei_show_menu_page');
  }
  add_action('mepr_menu', 'mepr_sensei_menu_page');

  function mepr_sensei_save_options() {
    if(!isset($_POST['mpsensei_save']) || !isset($_POST['mpsensei_map'])) {
      return;
    }

    if(empty($_POST['mpsensei_map'])) {
      update_option(MPSENSEI_DBOPS_NAME, array());
    }
    else {
      update_option(MPSENSEI_DBOPS_NAME, $_POST['mpsensei_map']);
    }

    wp_redirect(admin_url('admin.php?page='.MPSENSEI_EDITION.'&mpsensei_saved=true'));
  }
  add_action('admin_init', 'mepr_sensei_save_options');

  function mepr_sensei_sync($txn) {
    if(!class_exists('WooThemes_Sensei_Utils')) { return; } //Sensei not installed?

    $map = get_option(MPSENSEI_DBOPS_NAME, array());

    if(empty($map)) { return; } //Nothing to sync?

    $user = new MeprUser($txn->user_id);

    if((int)$user->ID <= 0) { return; } // No user?

    $subscriptions = $user->active_product_subscriptions('ids', true);

    if(empty($subscriptions)) { return; } //No subscriptions?

    //Loop through the member's subscriptions and start them on any associated courses
    foreach($subscriptions as $s) {
      if(isset($map[$s]) && !empty($map[$s])) {
        foreach($map[$s] as $course => $garbage) {
          WooThemes_Sensei_Utils::user_start_course($user->ID, $course);
        }
      }
    }
  }
  add_action('mepr-txn-store', 'mepr_sensei_sync');

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPSENSEI_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPSENSEI_EDITION,
    MPSENSEI_PLUGIN_SLUG,
    'mpsensei_license_key',
    __('MemberPress + Sensei', 'memberpress-sensei'),
    __('Sensei Integration for MemberPress.', 'memberpress-sensei')
  );
}
