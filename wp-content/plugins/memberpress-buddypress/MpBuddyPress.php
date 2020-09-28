<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpBuddyPress {
  public $enabled_str                   = 'mepr_buddypress_enabled';
  public $default_membership_str        = 'mepr_buddypress_default_membership';
  public $default_groups_str            = 'mepr_buddypress_default_groups';
  public $membership_groups_enabled_str = '_mepr_buddypress_groups_enabled';
  public $membership_groups_str         = '_mepr_buddypress_groups';

  public function __construct() {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    if(is_plugin_active('buddypress/bp-loader.php')) {
      //MP Options tab
      add_action('mepr_display_options_tabs',         array($this, 'display_option_tab'));
      add_action('mepr_display_options',              array($this, 'display_option_fields'));
      add_action('mepr-process-options',              array($this, 'store_option_fields'));
      add_action('mepr-options-admin-enqueue-script', array($this, 'enqueue_options_page_scripts'));

      //MP Memberships Advanced tab
      add_action('mepr-product-advanced-metabox',     array($this, 'display_membership_options'));
      add_action('mepr-product-save-meta',            array($this, 'save_membership_options'));
      add_action('mepr-product-admin-enqueue-script', array($this, 'enqueue_products_page_scripts'));

      //BP Nav Items & Account page
      add_action('bp_setup_nav',                      array($this, 'setup_bp_nav'));
      add_filter('mepr-account-page-permalink',       array($this, 'change_account_page_url'));
      add_action('template_redirect',                 array($this, 'catch_account_page_and_redirect'));
      add_filter('mepr_is_account_page',              array($this, 'set_is_account_page_true'), 11, 2);

      //BP's signup form hook
      add_action('bp_core_signup_user',               array($this, 'capture_bp_signups'), 11, 5);

      //Sync BP Groups w/MP
      add_action('mepr-account-is-active',            array($this, 'sync_groups_from_txn'));
      add_action('mepr-account-is-inactive',          array($this, 'sync_groups_from_txn'));

      //Sync BP groups (manually from WP profile)
      if(is_admin()) {
        add_action('admin_enqueue_scripts',           array($this, 'enqueue_sync_groups_script'));
        add_action('show_user_profile',               array($this, 'display_sync_groups_button'), 20);
        add_action('edit_user_profile',               array($this, 'display_sync_groups_button'), 20);
        add_action('wp_ajax_mepr_bp_sync_groups',     array($this, 'sync_groups_ajax'));
      }

      //Auto activate BP users when they signup via MemberPress
      add_action('mepr-signup',                       array($this, 'activate_bp_profile'));

      //First and last as BP name
      add_action('init',                              array($this, 'first_and_last_name'));
      add_action('mepr-save-account',                 array($this, 'sync_name'));
    }
  }

  //Set name field to first and last name
  function first_and_last_name($force_run = false) {
    if(bp_is_active('xprofile')) {
      $user = MeprUtils::get_currentuserinfo();

      if($user && !empty($user->first_name)) {
        $run = get_user_meta($user->ID, 'bp_flname_sync', true);
        if(!$run || $force_run) {
          //Update BP field
          $name_data = new BP_XProfile_ProfileData(1, $user->ID); //ID 1 is the name field
          $name_data->value = trim($user->first_name . ' ' . $user->last_name);
          $name_data->save();

          //Update WP fields
          $user->display_name = trim($user->first_name . ' ' . $user->last_name);
          $user->store();
          update_user_meta($user->ID, 'nickname', $user->display_name); //might as well

          //Make sure we don't do this every page load
          update_user_meta($user->ID, 'bp_flname_sync', 1);
        }
      }
    }
  }

  function sync_name($user) {
    $this->first_and_last_name(true);
  }

//MP OPTIONS PAGE
  public function display_option_tab() {
    ?>
      <a class="nav-tab" id="buddypress" href="#"><?php _e('BuddyPress', 'memberpress-buddypress'); ?></a>
    <?php
  }

  public function display_option_fields() {
    $enabled            = get_option($this->enabled_str, 0);
    $default_membership = get_option($this->default_membership_str, 0);
    $default_groups     = maybe_unserialize(get_option($this->default_groups_str, array()));
    $memberships        = get_posts(array('numberposts' => -1, 'post_type' => MeprProduct::$cpt, 'post_status' => 'publish'));
    $groups             = (bp_is_active('groups'))?BP_Groups_Group::get(array('show_hidden' => true, 'type'=>'alphabetical', 'per_page' => 9999)):false;

    //Make sure it's an array
    if(empty($default_groups)) { $default_groups = array(); }

    ?>
      <div id="buddypress" class="mepr-options-hidden-pane">
        <h3><?php _e('BuddyPress Integration', 'memberpress-buddypress'); ?></h3>

        <input type="checkbox" id="mepr_bp_enabled" name="mepr_bp_enabled" <?php checked($enabled); ?> />
        <label for="mepr_bp_enabled" style="vertical-align:top;"><?php _e('Enable BuddyPress Integration', 'memberpress-buddypress'); ?></label>

        <div id="mepr_bp_options_area" class="mepr-hidden mepr-sub-box-white" style="margin-top:20px;">
          <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"> </div>

          <?php if(!bp_is_active('groups')): ?>
            <p><?php _e('BuddyPress Integration is activated. For further integration options - enable BuddyPress Groups.', 'memberpress-buddypress'); ?></p>
          <?php endif; ?>

          <?php //if(get_option('users_can_register')): // Commenting out so this can work with Email Invites BuddyBoss add-on which requires that "Anyone can register" be disabled ?>
            <label for="mepr_bp_default_free_membership"><?php _e('Default Free Membership', 'memberpress-buddypress'); ?>:</label>
            <br/>
            <select id="mepr_bp_default_free_membership" name="mepr_bp_default_free_membership">
              <option value="none"><?php _e('None', 'memberpress-buddypress'); ?></option>
              <?php foreach($memberships as $m): ?>
                <option value="<?php echo $m->ID; ?>" <?php selected($default_membership, $m->ID); ?>><?php echo $m->post_title; ?></option>
              <?php endforeach; ?>
            </select>
            <br/>
            <small><?php _e("If the user signs up via BuddyPress's signup page, then no payment can be collected. Therefore the member will get lifetime free access to the default Membership you choose here. If you need to charge your users, then we recommend that you disable signups via BuddyPress and instead force the users to signup via MemberPress instead.", 'memberpress-buddypress'); ?></small>
          <?php //endif; //Users can register ?>
          <?php if(bp_is_active('groups') && $groups['total']): ?>
            <?php //if(get_option('users_can_register')): //Show a spacer ?>
              <br/>
              <br/>
            <?php //endif; ?>
            <label for="mepr_bp_default_groups"><?php _e('Default Group(s) for ALL Members', 'memberpress-buddypress'); ?>:</label>
            <br/>
            <select id="mepr_bp_default_groups" name="mepr_bp_default_groups[]" multiple="multiple" style="width:98%;height:150px;">
              <?php foreach($groups['groups'] as $g): ?>
                <option value="<?php echo $g->id; ?>" <?php selected(in_array($g->id, $default_groups, false)); ?>><?php echo $g->name; ?></option>
              <?php endforeach; ?>
            </select>
            <br/>
            <small><?php _e("Hold the Control Key (Command Key on the Mac) in order to select or deselect multiple groups.", 'memberpress-buddypress'); ?><br/><?php _e("Select a default BuddyPress Group(s) that every member should be added to when signing up. Please note, ALL members are added to this group whether they're active and paid or not. Please see the per-Membership Groups if you want to add/remove members to/from Groups automatically based on their status.", 'memberpress-buddypress'); ?></small>
          <?php endif; //!empty groups ?>
        </div>
      </div>
    <?php
  }

  public function store_option_fields() {
    update_option($this->enabled_str, (isset($_POST['mepr_bp_enabled'])));
    update_option($this->default_membership_str, (isset($_POST['mepr_bp_default_free_membership']))?(int)$_POST['mepr_bp_default_free_membership']:0);
    update_option($this->default_groups_str, (!empty($_POST['mepr_bp_default_groups']))?(array)$_POST['mepr_bp_default_groups']:array());
  }

  public function enqueue_options_page_scripts($hook) {
    wp_enqueue_script('mepr-buddypress-options-js', MPBP_URL.'/admin_buddypress_options.js');
  }

//MP Memberships Page
  public function enqueue_products_page_scripts($hook) {
    wp_enqueue_script('mepr-buddypress-membership-options-js', MPBP_URL.'/admin_buddypress_membership_options.js');
  }

  public function display_membership_options($product) {
    $enabled                    = get_option($this->enabled_str, 0);
    $membership_groups_enabled  = (bool)get_post_meta($product->ID, $this->membership_groups_enabled_str, true);
    $membership_groups          = maybe_unserialize(get_post_meta($product->ID, $this->membership_groups_str, true));
    $groups                     = (bp_is_active('groups'))?BP_Groups_Group::get(array('show_hidden' => true, 'type'=>'alphabetical', 'per_page' => 9999)):false;

    if(!$enabled || !$groups) { return; }

    //Needs to be an array, but if not set it will be an empty string
    if(empty($membership_groups)) { $membership_groups = array(); }

    ?>
    <div id="mepr-buddypress" class="mepr-product-adv-item">
      <input type="checkbox" name="mepr_buddypress_membership_groups" id="mepr_buddypress_membership_groups" <?php checked($membership_groups_enabled); ?> />
      <label for="mepr_buddypress_membership_groups"><?php _e('BuddyPress Groups for this Membership', 'memberpress-buddypress'); ?></label>

      <?php MeprAppHelper::info_tooltip('meprbuddypress-groups-enabled',
                                        __('Enable BuddyPress Groups', 'memberpress-buddypress'),
                                        __('If enabled, and Groups are selected - members will be added to and removed from these groups based on their subscription status to this membership level. These Groups should be unique to this Membership and should also be different from the default Group set in the MemberPress Options for BuddyPress settings.', 'memberpress-buddypress'));
      ?>

      <div id="mepr_buddypress_membership_groups_area" class="mepr-hidden product-options-panel">
        <label for="mepr_bp_membership_groups"><?php _e('Default Group(s) for ALL Members', 'memberpress-buddypress'); ?>:</label>
        <br/>
        <select id="mepr_bp_membership_groups" name="mepr_bp_membership_groups[]" multiple="multiple" style="width:98%;height:150px;">
          <?php foreach($groups['groups'] as $g): ?>
            <option value="<?php echo $g->id; ?>" <?php selected(in_array($g->id, $membership_groups, false)); ?>><?php echo $g->name; ?></option>
          <?php endforeach; ?>
        </select>
        <br/>
        <small><?php _e('Hold the Control Key (Command Key on the Mac) in order to select or deselect multiple groups.', 'memberpress-buddypress'); ?></small>
      </div>
    </div>
    <?php
  }

  public function save_membership_options($product) {
    $enabled = get_option($this->enabled_str, 0);

    if(!$enabled || !bp_is_active('groups')) { return; }

    if(isset($_POST['mepr_buddypress_membership_groups'])) {
      update_post_meta($product->ID, $this->membership_groups_enabled_str, true);
      update_post_meta($product->ID, $this->membership_groups_str, (!empty($_POST['mepr_bp_membership_groups']))?(array)$_POST['mepr_bp_membership_groups']:array());
    }
    else {
      update_post_meta($product->ID, $this->membership_groups_enabled_str, false);
    }
  }

//BP's Signup Form Capture
  public function capture_bp_signups($user_id, $user_login, $user_password, $user_email, $usermeta) {
    $enabled            = get_option($this->enabled_str, 0);
    $default_membership = get_option($this->default_membership_str, 0);
    $default_groups     = maybe_unserialize(get_option($this->default_groups_str, array()));

    if(!$enabled) { return; }

    //Default Membership Handling
    if($default_membership) {
      $user = new MeprUser($user_id);
      $active_subs = $user->active_product_subscriptions('ids');
      $active_subs = (empty($active_subs))?array():$active_subs;

      if(!in_array($default_membership, $active_subs)) {
        $txn = new MeprTransaction();
        $txn->trans_num   = 'bp-'.uniqid();
        $txn->product_id  = $default_membership;
        $txn->status      = MeprTransaction::$complete_str;
        $txn->txn_type    = MeprTransaction::$payment_str;
        $txn->amount      = 0.00;
        $txn->created_at  = gmdate('c');
        $txn->expires_at  = MeprUtils::mysql_lifetime();
        $txn->user_id     = $user_id;
        $txn->gateway     = 'free';
        $txn->store();
      }
    }

    //Default Groups handling
    if(bp_is_active('groups') && !empty($default_groups)) {
      foreach($default_groups as $g_id) {
        groups_join_group($g_id, $user_id);
      }
    }
  }

  /**
   * Sync BP groups for the given transaction
   *
   * @param MeprTransaction $txn
   */
  public function sync_groups_from_txn($txn) {
    $this->sync_groups($txn->user_id);
  }

  /**
   * Sync BP groups for the given user
   *
   * @param int $user_id
   */
  public function sync_groups($user_id) {
    $enabled = get_option($this->enabled_str, 0);
    $user = new MeprUser($user_id);

    if(!$enabled || !bp_is_active('groups') || !class_exists('BP_Groups_Member') || empty($user->ID)) {
      return;
    }

    $all_product_groups = $this->get_all_product_groups();

    if(empty($all_product_groups)) {
      return;
    }

    $active_product_groups = $this->get_active_product_groups($user->ID); // Groups granted by active products
    $user_groups = bp_get_user_groups($user->ID, array('is_admin' => null, 'is_mod' => null)); // Current BP groups for user

    foreach($all_product_groups as $group_id) {
      $user_in_group = isset($user_groups[$group_id]);
      $user_should_be_in_group = in_array($group_id, $active_product_groups);

      if($user_should_be_in_group && !$user_in_group) {
        groups_join_group($group_id, $user->ID);
        $groups_member = new BP_Groups_Member($user->ID, $group_id);
        do_action('groups_member_after_save', $groups_member); // For the buddypress-group-email-subscription plugin
      }
      elseif(!$user_should_be_in_group && $user_in_group) {
        BP_Groups_Member::delete($user->ID, $group_id);
      }
    }

    $default_groups = maybe_unserialize(get_option($this->default_groups_str, array()));

    // Make sure the user still has the default Groups
    if(is_array($default_groups) && !empty($default_groups)) {
      foreach($default_groups as $g_id) {
        groups_join_group($g_id, $user->ID);
      }
    }
  }

  /**
   * Get all of the groups currently assigned to memberships
   *
   * @return array
   */
  private function get_all_product_groups() {
    $products = MeprCptModel::all('MeprProduct');
    $product_groups = array();

    foreach($products as $product) {
      $membership_groups_enabled  = (bool) get_post_meta($product->ID, $this->membership_groups_enabled_str, true);
      $membership_groups = maybe_unserialize(get_post_meta($product->ID, $this->membership_groups_str, true));

      if($membership_groups_enabled && is_array($membership_groups) && !empty($membership_groups)) {
        $product_groups = array_merge($product_groups, $membership_groups);
      }
    }

    $product_groups = array_unique(array_map('intval', $product_groups));

    return $product_groups;
  }

  /**
   * Get all of the the groups the user should be a member of, based on their active subscriptions
   *
   * @param  int   $user_id
   * @return array
   */
  private function get_active_product_groups($user_id) {
    $user = new MeprUser($user_id);
    $active_product_groups = array();

    if(!empty($user->ID)) {
      $active_product_ids = $user->active_product_subscriptions('ids', true);
      $active_product_ids = array_unique(array_map('intval', $active_product_ids));

      foreach($active_product_ids as $active_product_id) {
        $membership_groups_enabled  = (bool) get_post_meta($active_product_id, $this->membership_groups_enabled_str, true);
        $membership_groups = maybe_unserialize(get_post_meta($active_product_id, $this->membership_groups_str, true));

        if($membership_groups_enabled && is_array($membership_groups) && !empty($membership_groups)) {
          $active_product_groups = array_merge($active_product_groups, $membership_groups);
        }
      }
    }

    $active_product_groups = array_unique(array_map('intval', $active_product_groups));

    return $active_product_groups;
  }

  /**
   * Enqueue the sync groups script on the edit user page
   *
   * @param string $hook
   */
  public function enqueue_sync_groups_script($hook) {
    if(in_array($hook, array('profile.php', 'user-edit.php'))) {
      wp_enqueue_script('mepr-buddypress-sync-groups-js', MPBP_URL . '/admin_sync_groups.js', array('jquery'));
      wp_localize_script('mepr-buddypress-sync-groups-js', 'MeprBuddyPressSyncGroups', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mepr_bb_sync_groups')
      ));
    }
  }

  /**
   * Display a button to sync groups on the edit user page
   *
   * @param WP_User $user
   */
  public function display_sync_groups_button($user) {
    $enabled = get_option($this->enabled_str, 0);

    if(!$enabled || !bp_is_active('groups') || !MeprUtils::is_logged_in_and_an_admin()) {
      return;
    }

    ?>
    <h2><?php esc_html_e('MemberPress BuddyPress Groups', 'memberpress-buddypress'); ?></h2>
    <table class="form-table">
      <tr>
        <td colspan="2">
          <div>
            <button type="button" id="mepr-bb-sync-groups" class="button" data-user-id="<?php echo esc_attr($user->ID); ?>"><?php esc_html_e('Sync BuddyPress Groups', 'memberpress-buddypress'); ?></button>
            <span id="mepr-bb-sync-groups-status" style="display:none;margin-top:4px;"></span>
          </div>
          <p class="description"><?php esc_html_e('Sync the BuddyPress groups for this user based on their active subscriptions.', 'memberpress-buddypress'); ?></p>
        </td>
      </tr>
    </table>
    <?php
  }

  /**
   * Handle the Ajax request to sync groups from the WP edit user page
   */
  public function sync_groups_ajax() {
    if(!MeprUtils::is_post_request() || !isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
      wp_send_json_error(__('Bad request.', 'memberpress-buddypress'));
    }

    if(!MeprUtils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'memberpress-buddypress'));
    }

    if(!check_ajax_referer('mepr_bb_sync_groups', false, false)) {
      wp_send_json_error(__('Security check failed.', 'memberpress-buddypress'));
    }

    $user = new MeprUser((int) $_POST['user_id']);

    if(empty($user->ID)) {
      wp_send_json_error(__('User not found.', 'memberpress-buddypress'));
    }

    $this->sync_groups($user->ID);

    wp_send_json_success();
  }

//BP NAV AND ACCOUNT
  //Override Mepr Account page URL
  public function change_account_page_url($url) {
    global $bp;

    $current_user = MeprUtils::get_currentuserinfo();
    $enabled      = get_option($this->enabled_str, 0);
    $main_slug    = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'mp-membership');

    if($current_user !== false && $enabled) {
      $url = $bp->loggedin_user->domain . $main_slug . '/';
    }

    return $url;
  }

  public function catch_account_page_and_redirect() {
    global $bp;

    $mepr_options = MeprOptions::fetch();
    $current_post = MeprUtils::get_current_post();
    $current_user = MeprUtils::get_currentuserinfo();
    $enabled      = get_option($this->enabled_str, 0);
    $main_slug    = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'mp-membership');

    if($enabled && isset($current_post->ID) && $current_post->ID == $mepr_options->account_page_id) {
      if($current_user !== false) {
        MeprUtils::wp_redirect($bp->loggedin_user->domain . $main_slug . '/');
      }
      else {
        MeprUtils::wp_redirect($mepr_options->login_page_url('?redirect_to=' . urlencode($mepr_options->account_page_url())));
      }
    }
  }

  //For enqueue_scripts to work correctly still
  public function set_is_account_page_true($is_account_page, $post) {
    if(!$is_account_page && function_exists('bp_is_my_profile')) {
      $is_account_page = bp_is_my_profile();
    }

    return $is_account_page;
  }

  public function setup_bp_nav() {
    global $bp;

    $main_slug = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'mp-membership');

    //Parent
    bp_core_new_nav_item(
      array(
        'name' => MeprHooks::apply_filters('mepr-bp-info-main-nav-name', _x('Membership', 'ui', 'memberpress-buddypress')),
        'slug' => $main_slug,
        'position' => MeprHooks::apply_filters('mepr-bp-info-main-nav-position', 25),
        'show_for_displayed_user' => false,
        // 'screen_function' => array($this, 'membership_info'), //Not needed with subnav?
        'default_subnav_slug' => 'mp-info',
        'item_css_id' => 'mepr-bp-info'
      )
    );

    //Info Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Info', 'ui', 'memberpress-buddypress'),
        'slug' => 'mp-info',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_info'),
        'position' => 0,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-info'
      )
    );

    //Subscriptions Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Subscriptions', 'ui', 'memberpress-buddypress'),
        'slug' => 'mp-subscriptions',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_subscriptions'),
        'position' => 10,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-subscriptions'
      )
    );

    //Payments Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Payments', 'ui', 'memberpress-buddypress'),
        'slug' => 'mp-payments',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_payments'),
        'position' => 20,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-payments'
      )
    );
  }

  /* INFO TAB */
  public function membership_info() {
    // add_action('bp_template_title', array($this, 'membership_info_title'));
    add_action('bp_template_content', array($this, 'membership_info_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_info_title() {
    echo _x('Membership Info', 'ui', 'memberpress-buddypress');
  }

  public function membership_info_content() {
    $action = (isset($_REQUEST['action']))?$_REQUEST['action']:false;
    $acct_ctrl = new MeprAccountCtrl();

    // This is the same hook we call in MeprAccountCtrl.php
    ob_start();
    MeprHooks::do_action('mepr_account_nav_content', $action);
    $custom_content = ob_get_clean();

    if(empty($custom_content)) {
      ?>
        <!-- Hide password reset as BP has it's own -->
        <style>
          span.mepr-account-change-password {
            display:none !important;
          }
        </style>
      <?php
      $acct_ctrl->home();
    }
    else {
      echo $custom_content;
    }
  }

  /* SUBSCRIPTIONS TAB */
  public function membership_subscriptions() {
    // add_action('bp_template_title', array($this, 'membership_subscriptions_title'));
    add_action('bp_template_content', array($this, 'membership_subscriptions_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_subscriptions_title() {
    echo _x('Membership Subscriptions', 'ui', 'memberpress-buddypress');
  }

  public function membership_subscriptions_content() {
    $acct_ctrl = new MeprAccountCtrl();

    $action = (isset($_REQUEST['action']))?$_REQUEST['action']:false;

    switch($action) {
      case 'cancel':
        $acct_ctrl->cancel();
        break;
      case 'suspend':
        $acct_ctrl->suspend();
        break;
      case 'resume':
        $acct_ctrl->resume();
        break;
      case 'update':
        $acct_ctrl->update();
        break;
      case 'upgrade':
        $acct_ctrl->upgrade();
        break;
      default:
        $acct_ctrl->subscriptions();
    }
  }

  /* PAYMENTS TAB */
  public function membership_payments() {
    // add_action('bp_template_title', array($this, 'membership_payments_title'));
    add_action('bp_template_content', array($this, 'membership_payments_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_payments_title() {
    echo _x('Membership Payments', 'ui', 'memberpress-buddypress');
  }

  public function membership_payments_content() {
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->payments();
  }

  public function activate_bp_profile($txn) {
    $enabled            = get_option($this->enabled_str, 0);
    $default_groups     = maybe_unserialize(get_option($this->default_groups_str, array()));

    if(!$enabled) { return; }

    if(function_exists('bp_update_user_last_activity')) {
      delete_transient('bp_active_member_count'); // Delete transient to flush bp_core_get_active_member_count()
      bp_update_user_last_activity($txn->user_id);
    }

    //Default Groups handling
    if(bp_is_active('groups') && !empty($default_groups)) {
      foreach($default_groups as $g_id) {
        groups_join_group($g_id, $txn->user_id);
      }
    }
  }
} //End Class
