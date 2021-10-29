<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpUsersController
{
  public static function load_hooks()
  {
    global $wafp_options;

    add_action('edit_user_profile', 'WafpUsersController::display_user_fields');
    add_action('edit_user_profile_update', 'WafpUsersController::update_user_fields');
    add_action('admin_enqueue_scripts', 'WafpUsersController::enqueue_scripts');
    add_filter('manage_users_columns', 'WafpUsersController::add_affiliate_to_user_column');
    add_filter('manage_users_custom_column', 'WafpUsersController::modify_user_affiliate_row', 10, 3);
    add_action('wp_ajax_wafp_resend_welcome_email', 'WafpUsersController::resend_welcome_email_callback');
    //add_action('wp_ajax_wafp_load_affiliate_datatable', 'WafpUsersController::load_affiliate_datatable_callback');
    add_action('admin_head', 'WafpUsersController::resend_welcome_email_js');
    add_action('user_register', 'WafpUsersController::affiliate_registration_actions');
    add_action('wp_ajax_wafp_affiliate_search', 'WafpUsersController::affiliate_search');
    add_action('delete_user', 'WafpUsersController::delete_user');
  }

  public static function enqueue_scripts($hook)
  {
    if($hook == 'user-edit.php' || $hook == 'profile.php') {
      wp_enqueue_style( 'wafp-user-profile', WAFP_CSS_URL.'/admin-user-profile.css', array(), WAFP_VERSION );
      wp_enqueue_script( 'wafp-user-profile', WAFP_JS_URL.'/admin-user-profile.js', array('jquery'), WAFP_VERSION );
    }
  }

  public static function display_login_form()
  {
    global $wafp_options, $wafp_blogurl;

    extract($_POST);

    $redirect_to = ( (isset($redirect_to) and !empty($redirect_to) )?$redirect_to:WafpUtils::dashboard_url() );
    $redirect_to = apply_filters( 'wafp-login-redirect-url', $redirect_to );

    if(!empty($wafp_options->login_page_id) and $wafp_options->login_page_id > 0)
    {
      $login_url = WafpUtils::login_url();
      $login_delim = WafpAppController::get_param_delimiter_char($login_url);
      $forgot_password_url = "{$login_url}{$login_delim}action=forgot_password";
    }
    else
    {
      $login_url = "{$wafp_blogurl}/wp-login.php";
      $forgot_password_url = "{$wafp_blogurl}/wp-login.php?action=lostpassword";
    }

    $signup_url = WafpUtils::signup_url();

    if(WafpUtils::is_user_logged_in())
      require( WAFP_VIEWS_PATH . '/shared/already_logged_in.php' );
    else
    {
      if( !empty($wafp_process_login_form) and !empty($errors) )
        require( WAFP_VIEWS_PATH . "/shared/errors.php" );

      require( WAFP_VIEWS_PATH . '/shared/login_form.php' );
    }
  }

  public static function process_login_form() {
    global $wafp_options;

    $errors = WafpUser::validate_login($_POST,array());
    $errors = apply_filters('wafp-validate-login', $errors);

    extract($_POST);

    if(empty($errors)) {
      $creds = array();
      $creds['user_login'] = $log;
      $creds['user_password'] = $pwd;
      $creds['remember'] = isset($rememberme);

      $user = wp_signon($creds, false);

      if(!$user instanceof WP_User) {
        $errors[] = __('Login failed. Please double check your username and password.', 'affiliate-royale', 'easy-affiliate');
        $_POST['errors'] = $errors;
        return;
      }

      $redirect_to = ((!empty($redirect_to))?$redirect_to:WafpUtils::dashboard_url());

      WafpUtils::wp_redirect($redirect_to);
      exit;
    }
    else {
      $_POST['errors'] = $errors;
    }
  }

  public static function display_signup_form() {
    global $wafp_options, $wafp_blogurl;

    $process = WafpAppController::get_param('wafp-process-form');

    $redirect_to = ( (isset($redirect_to) and !empty($redirect_to) )?$redirect_to:WafpUtils::dashboard_url() );
    $redirect_to = apply_filters( 'wafp-login-redirect-url', $redirect_to );

    if(empty($process)) {
      if(WafpUtils::is_user_logged_in())
        require( WAFP_VIEWS_PATH . '/shared/already_logged_in.php' );
      // As it turns out we want this to be disabled in most cases for security reasons
      //else if (!get_option('users_can_register'))
      //  require( WAFP_VIEWS_PATH . '/shared/no_registration.php' );
      else
        require( WAFP_VIEWS_PATH . '/shared/signup_form.php' );
    }
    else {
      self::process_signup_form();
    }
  }

  public static function process_signup_form() {
    global $wafp_options, $wafp_blogname;

    // Yeah, sometimes this method get's loaded multiple times (depending on the theme).
    // So these are static to not get tripped up by this
    static $errors, $user, $has_run; //$has_run is to prevent duplicate notifications

    if(!isset($errors)) {
      $errors = WafpUser::validate_signup($_POST,array());
      $errors = apply_filters('wafp-validate-signup', $errors);
    }

    extract($_POST);

    if(empty($errors)) {
      if(!isset($user)) {
        $new_password = $wafp_user_password;

        $user = new WafpUser();
        $user->set_field('user_login', $user_login);
        $user->set_field('user_email', $user_email);
        $user->set_first_name($user_first_name);
        $user->set_last_name($user_last_name);

        if(isset($_POST[WafpUser::$paypal_email_str]) && !empty($_POST[WafpUser::$paypal_email_str]))
          $user->set_paypal_email($_POST[WafpUser::$paypal_email_str]);

        if($wafp_options->show_address_fields) {
          $user->set_address_one($_POST[WafpUser::$address_one_str]);
          $user->set_address_two($_POST[WafpUser::$address_two_str]);
          $user->set_city($_POST[WafpUser::$city_str]);
          $user->set_state($_POST[WafpUser::$state_str]);
          $user->set_zip($_POST[WafpUser::$zip_str]);
          $user->set_country($_POST[WafpUser::$country_str]);
        }

        if($wafp_options->show_tax_id_fields) {
          $user->set_tax_id_us($_POST[WafpUser::$tax_id_us_str]);
          $user->set_tax_id_int($_POST[WafpUser::$tax_id_int_str]);
        }

        $user->set_password($wafp_user_password);
        $user->set_is_affiliate(1); // uh .. yeah, this is the affiliate signup page

        // Makin' it happen...
        $user->create();
      }

      if($user->get_id()) {
        // Yeah, we're going to record affiliate parent no matter what
        $affiliate_id = (isset($_COOKIE['wafp_click']))?$_COOKIE['wafp_click']:null;

        if(isset($affiliate_id) and !empty($affiliate_id)) {
          $user->set_referrer($affiliate_id);
          $user->store();
        }

        if(!isset($has_run) || !$has_run) {
          $user->send_account_notifications($user->get_password(), true, $wafp_options->welcome_email);
          $has_run = true;
        }

        do_action('wafp-process-signup', $user->get_id());

        require( WAFP_VIEWS_PATH . "/users/signup_thankyou.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/shared/signup_form.php' );
    }
  }

  public static function display_forgot_password_form() {
    global $wafp_options, $wafp_blogurl;

    $process = WafpAppController::get_param('wafp_process_forgot_password_form');

    if(empty($process))
      require( WAFP_VIEWS_PATH . '/users/forgot_password.php' );
    else
      self::process_forgot_password_form();
  }

  public static function process_forgot_password_form()
  {
    global $wafp_options;

    $errors = WafpUser::validate_forgot_password($_POST,array());

    extract($_POST);

    if(empty($errors))
    {
      $is_email = (is_email($wafp_user_or_email) and email_exists($wafp_user_or_email));

      $is_username = username_exists($wafp_user_or_email);

      $user = new WafpUser();

      // If the username & email are identical then let's rely on it as a username first and foremost
      if($is_username)
        $user->load_user_data_by_login( $wafp_user_or_email );
      else if($is_email)
        $user->load_user_data_by_email( $wafp_user_or_email );

      if($user->get_id())
      {
        $user->send_reset_password_requested_notification();

        require( WAFP_VIEWS_PATH . "/users/forgot_password_requested.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/users/forgot_password.php' );
    }
  }

  public static function display_reset_password_form($wafp_key,$wafp_screenname)
  {
    $user = new WafpUser();
    $user->load_user_data_by_login( $wafp_screenname );

  $loginURL = WafpUtils::login_url();

    if($user->get_id())
    {
      if($user->reset_form_key_is_valid($wafp_key))
        require( WAFP_VIEWS_PATH . '/users/reset_password.php' );
      else
        require( WAFP_VIEWS_PATH . '/shared/unauthorized.php' );
    }
    else
      require( WAFP_VIEWS_PATH . '/shared/unauthorized.php' );
  }

  public static function process_reset_password_form()
  {
    global $wafp_options;
    $errors = WafpUser::validate_reset_password($_POST,array());

    extract($_POST);

    if(empty($errors))
    {
      $user = new WafpUser();
      $user->load_user_data_by_login( $wafp_screenname );

      if($user->get_id())
      {
        $user->set_password_and_send_notifications($wafp_key, $wafp_user_password);

        require( WAFP_VIEWS_PATH . "/users/reset_password_thankyou.php" );
      }
      else
        require( WAFP_VIEWS_PATH . "/shared/unknown_error.php" );
    }
    else
    {
      require( WAFP_VIEWS_PATH . "/shared/errors.php" );
      require( WAFP_VIEWS_PATH . '/users/reset_password.php' );
    }
  }

  public static function display_user_fields( $wpuser )
  {
    global $wafp_options;
    $user = new WafpUser( $wpuser->ID );

    if( WafpUtils::is_logged_in_and_an_admin() and
        is_a($user, 'WafpUser') ) {
      // Override defaults
      $commission_override_enabled = false;
      $commissions = array("0.00");
      $commission_type = 'percentage';
      $recurring = true;

      if($obj = get_user_meta($user->get_id(), 'wafp_override', true)) {
        $commissions = $obj;
        if(is_numeric($commissions)) { array($commissions); } // For Backwards Compatibility
        $commission_override_enabled = true;
        $commission_type = get_user_meta($user->get_id(), 'wafp_commission_type', true);
        $recurring = $user->get_recurring();
      }

      if( isset($_POST[WafpUser::$is_affiliate_str]) )
        $is_affiliate = true;
      else
        $is_affiliate = $user->is_affiliate();

      if( isset($_POST[WafpUser::$is_blocked_str]) )
        $is_blocked = false;
      else
        $is_blocked = $user->is_blocked();

      if( isset($_POST[WafpUser::$blocked_message_str]) )
        $blocked_message = '';
      else
        $blocked_message = $user->get_blocked_message();

      if ($wafp_options->show_tax_id_fields) {
        if ( isset($_POST[WafpUser::$tax_id_us_str]) )
          $tax_id_us = $_POST[WafpUser::$tax_id_us_str];
        else
          $tax_id_us = $user->get_tax_id_us();

        if ( isset($_POST[WafpUser::$tax_id_int_str]) )
          $tax_id_int = $_POST[WafpUser::$tax_id_int_str];
        else
          $tax_id_int = $user->get_tax_id_int();
      }

      if($wafp_options->show_address_fields) {
        if ( isset($_POST[WafpUser::$address_one_str]) )
          $address_one = $_POST[WafpUser::$address_one_str];
        else
          $address_one = $user->get_address_one();

        if ( isset($_POST[WafpUser::$address_two_str]) )
          $address_two = $_POST[WafpUser::$address_two_str];
        else
          $address_two = $user->get_address_two();

        if ( isset($_POST[WafpUser::$city_str]) )
          $city = $_POST[WafpUser::$city_str];
        else
          $city = $user->get_city();

        if ( isset($_POST[WafpUser::$state_str]) )
          $state = $_POST[WafpUser::$state_str];
        else
          $state = $user->get_state();

        if ( isset($_POST[WafpUser::$zip_str]) )
          $zip = $_POST[WafpUser::$zip_str];
        else
          $zip = $user->get_zip();

        if ( isset($_POST[WafpUser::$country_str]) )
          $country = $_POST[WafpUser::$country_str];
        else
          $country = $user->get_country();
      }

      $affiliate = false;
      $affiliate_id = $user->get_referrer();

      if($affiliate_id)
        $affiliate = new WafpUser($affiliate_id);

      require(WAFP_VIEWS_PATH . "/users/admin_profile.php");
    }
  }

  public static function update_user_fields( $user_id ) {
    if( WafpUtils::is_logged_in_and_an_admin() ) {
      update_user_meta($user_id, WafpUser::$is_blocked_str, isset($_POST[WafpUser::$is_blocked_str]));
      update_user_meta($user_id, WafpUser::$blocked_message_str, stripslashes($_POST[WafpUser::$blocked_message_str]));

      if(isset($_POST[WafpUser::$referrer_str]) && !empty($_POST[WafpUser::$referrer_str])) {
        $user = WafpUtils::get_userdatabylogin($_POST[WafpUser::$referrer_str]);
        update_user_meta($user_id, WafpUser::$referrer_str, $user->ID);
      }

      //Handle the set_is_affiliate stuff -- allows our hook to be fired
      $wafp_user = new WafpUser($user_id);
      $wafp_user->set_is_affiliate(isset($_POST[WafpUser::$is_affiliate_str]));
      $wafp_user->store();

      if(isset($_POST['wafp_override_enabled']) ) {
        update_user_meta($user_id, 'wafp_override', json_decode(stripslashes($_POST['wafp_commissions_json'])));
        update_user_meta($user_id, 'wafp_commission_type', $_POST['wafp_commission_type']);
        update_user_meta($user_id, 'wafp_recurring', isset($_POST['wafp_recurring']));
      }
      else {
        delete_user_meta($user_id, 'wafp_override');
        delete_user_meta($user_id, 'wafp_commission_type');
        delete_user_meta($user_id, 'wafp_recurring');
      }

      if ( isset($_POST[WafpUser::$tax_id_us_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_us_str, $_POST[WafpUser::$tax_id_us_str]);

      if ( isset($_POST[WafpUser::$tax_id_int_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_int_str, $_POST[WafpUser::$tax_id_int_str]);

      if ( isset($_POST[WafpUser::$tax_id_int_str]) )
        update_user_meta($user_id, WafpUser::$tax_id_int_str, $_POST[WafpUser::$tax_id_int_str]);

      if ( isset($_POST[WafpUser::$address_one_str]) )
        update_user_meta($user_id, WafpUser::$address_one_str, $_POST[WafpUser::$address_one_str]);

      if ( isset($_POST[WafpUser::$address_two_str]) )
        update_user_meta($user_id, WafpUser::$address_two_str, $_POST[WafpUser::$address_two_str]);

      if ( isset($_POST[WafpUser::$city_str]) )
        update_user_meta($user_id, WafpUser::$city_str, $_POST[WafpUser::$city_str]);

      if ( isset($_POST[WafpUser::$state_str]) )
        update_user_meta($user_id, WafpUser::$state_str, $_POST[WafpUser::$state_str]);

      if ( isset($_POST[WafpUser::$zip_str]) )
        update_user_meta($user_id, WafpUser::$zip_str, $_POST[WafpUser::$zip_str]);

      if ( isset($_POST[WafpUser::$country_str]) )
        update_user_meta($user_id, WafpUser::$country_str, $_POST[WafpUser::$country_str]);
    }
  }

  public static function add_affiliate_to_user_column( $column ) {
    $column['wafp_is_affiliate'] = __('Is Affiliate', 'affiliate-royale', 'easy-affiliate');
    $column['wafp_affiliate'] = __('Affiliate Referrer', 'affiliate-royale', 'easy-affiliate');
    return $column;
  }

  public static function modify_user_affiliate_row( $val, $column_name, $user_id ) {
    if($column_name=='wafp_affiliate')
    {
      $wuser = new WafpUser($user_id);
      $affiliate_id = $wuser->get_referrer();

      if($affiliate_id)
      {
        $affiliate = new WafpUser($affiliate_id);

        if($affiliate != false)
          return "<a href=\"" . admin_url("user-edit.php?user_id={$affiliate_id}&wp_http_referer=%2Fwp-admin%2Fusers.php") . "\">" . $affiliate->get_full_name() . "</a>";
      }

      return __('None', 'affiliate-royale', 'easy-affiliate');
    }
    else if($column_name=='wafp_is_affiliate')
    {
      $user = new WafpUser($user_id);

      return ($user->is_affiliate()?__('Yes', 'affiliate-royale', 'easy-affiliate'):__('No', 'affiliate-royale', 'easy-affiliate'));
    }

    return $val;
  }

  public static function resend_welcome_email_callback()
  {
    if( wp_verify_nonce( $_REQUEST['_wafp_nonce'], 'wafp-resend-welcome-email' ) )
    {
      if( WafpUtils::is_logged_in_and_an_admin() )
      {
        $user = new WafpUser($_REQUEST['uid']);
        $user->send_account_notifications( '', false, true );
        _e('Message Sent', 'affiliate-royale', 'easy-affiliate');
        die();
      }
      _e('Unauthorized to resend message', 'affiliate-royale', 'easy-affiliate');
      die();
    }
    _e('Cannot resend message', 'affiliate-royale', 'easy-affiliate');
    die();
  }

  public static function resend_welcome_email_js()
  {
    ?>
    <script type="text/javascript" >
      jQuery(document).ready(function() {
        jQuery('.wafp-resend-welcome-email').click( function() {
          jQuery('.wafp-resend-welcome-email-loader').show();

          var data = {
            action: 'wafp_resend_welcome_email',
            uid: jQuery(this).attr('user-id'),
            _wafp_nonce: jQuery(this).attr('wafp-nonce')
          };

          jQuery.post(ajaxurl, data, function(response) {
            jQuery('.wafp-resend-welcome-email-loader').hide();
            jQuery('.wafp-resend-welcome-email-message').text(response);
          });
        });
      });
    </script>
    <?php
  }

  /*
  public static function load_affiliate_datatable_callback()
  {
    if( wp_verify_nonce( $_REQUEST['_wafp_nonce'], 'wafp_load_affiliate_datatable' ) )
    {
      if( WafpUtils::is_logged_in_and_an_admin() )
      {
        echo WafpUser::affiliate_datatable();
        die();
      }
      _e('Unauthorized', 'affiliate-royale');
      die();
    }
    _e('Unauthorized', 'affiliate-royale');
    die();
  }
  */

  public static function display_affiliates_list()
  {
    $aff_table = new WafpAffiliatesTable();
    $aff_table->prepare_items();

    require WAFP_VIEWS_PATH . '/affiliates/list.php';
  }

  public static function affiliate_registration_actions($user_id) {
    global $wafp_options;

    $user = new WafpUser($user_id);

    //Set this user's referring affiliate if any
    if(!current_user_can('remove_users') && isset($_COOKIE['wafp_click']) && (int)$_COOKIE['wafp_click'] > 0) {
      $user->set_referrer((int)$_COOKIE['wafp_click']);
      $user->store();
    }

    // Let's set user to be an affiliate automatically
    //Adding no_wafp_aff as a way for other plugins to omit the automatic affiliate creation if they want
    if($wafp_options->make_new_users_affiliates && !$user->get_is_affiliate() && !isset($_REQUEST['no_wafp_aff'])) {
      $user->set_is_affiliate(true);
      $user->store();
      $user->send_account_notifications( $user->get_password(), $wafp_options->welcome_email, $wafp_options->welcome_email );
    }
  }

  public static function affiliate_search() {
    if ( !current_user_can( 'list_users' ) )
      die( '-1' );

    $s = $_GET['q']; // is this slashed already?

    $s = trim( $s );
    if ( strlen( $s ) < 2 )
      die; // require 2 chars for matching

    $users = get_users( array( 'search' => "*$s*", 'meta_key' => 'wafp_is_affiliate', 'meta_value' => 1 ) );
    require(WAFP_VIEWS_PATH . '/users/affiliate_search.php');
    die;
  }

  /**
   * reassign referrers to parent if exist, other blank out child referrers
   *
   * @return void
   * @author Brad Van Skyhawk
   **/
  public static function delete_user($user_id) {
     $key = WafpUser::$referrer_str;

    // Get the children
    $children = get_users( array( 'fields' => 'ID',  'meta_key' => $key, 'meta_value' => $user_id ) );
    if ( $children ) {
      // Get the parent
      $parent_id = get_user_meta( $user_id, $key, true );

      // Reassign children to parent
      foreach ( $children as $child_id ) {
        update_user_meta( $child_id, $key, $parent_id );
      }
    }
  }
}
