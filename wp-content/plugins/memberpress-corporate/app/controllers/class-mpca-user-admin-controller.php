<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_User_Admin_Controller {
  public function __construct() {
    add_action( 'mepr_extra_profile_fields', array( $this, 'display_fields' ) );
    add_action( 'mepr_user_account_saved', array( $this, 'save_user' ) );
    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
  }

  public function enqueue_scripts() {
    wp_enqueue_style('settings_table', MEPR_URL . '/css/settings_table.css');
  }

  public function display_fields($user) {
    // Instantiate helper for use in view template
    $helper = new MPCA_Admin_Helper();

    // Setup view template variables
    $meta_type = get_user_meta( $user->ID, 'mpca_member_type', true);
    $meta_limit = get_user_meta( $user->ID, 'mpca_member_sub_account_limit', true);
    $meta_parent_id = get_user_meta( $user->ID, 'mpca_member_parent_id', true);

    // Get a list of the user's subscriptions
    $subscriptions = $user->subscriptions();

    require(MeprView::file('/mpca-edit-user-template'));
  }

  public function save_user($user) {
    if ( !isset($_POST['mpca']) ) {
      return;
    }

    $mpca_data = $_POST['mpca'];

    foreach($mpca_data as $d) {
      $old_account = MPCA_Corporate_Account::find_corporate_account_by_obj_id($d['obj_id'], $d['obj_type']);

      // Is it a sub account? If yes, bail out
      // Sub accounts should not be convertible to a Corporate account
      $is_sub_account = MPCA_Corporate_Account::is_obj_sub_account($d['obj_id'], $d['obj_type']);
      if($is_sub_account) continue;

      if(empty($old_account)) {
        if(isset($d['is_corporate'])) {  // create
          $d['user_id'] = $user->ID;
          $new_account = new MPCA_Corporate_Account();
          $new_account->load_from_array($d);
          $new_account->store();
        } else {
          // do nothing
        }
      } else {
        if(isset($d['is_corporate'])) {  // update
          $old_account->status = 'enabled';
          $old_account->num_sub_accounts = $d['num_sub_accounts'];
          $old_account->store();
        } else {                  // disable
          $old_account->disable();
        }
      }
    }
  }
}
