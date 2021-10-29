<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpAppController {
  public static function load_hooks() {
    add_filter('the_content', 'WafpAppController::page_route', 100);
    add_action('admin_enqueue_scripts', 'WafpAppController::load_shared_admin_scripts');
    add_action('wp_enqueue_scripts', 'WafpAppController::load_scripts', 1);
    add_action('wp_head', 'WafpAppController::load_dynamic_css', 100);
    add_action('admin_init', 'WafpAppController::install'); // DB upgrade is handled automatically here now
    add_action('init', 'WafpAppController::parse_standalone_request', 1);
    //Because we're setting the nonce in a cookie -- this has to be here otherwise Headers already sent errors will occur
    add_action('template_redirect', 'WafpNonceModel::setup_nonce');
    add_action('template_redirect', 'WafpAppController::record_generic_affiliate_link');
    add_action('menu_order', 'WafpAppController::admin_menu_order');
    add_action('custom_menu_order', 'WafpAppController::admin_menu_order');
    add_action('admin_notices', 'WafpAppController::configure_options_warning');
    add_action('wp_ajax_wafp_delete_transaction', 'WafpTransactionsController::delete_transaction');
    add_action('wp_ajax_wafp_delete_commission', 'WafpTransactionsController::delete_commission');
    add_action('wp_ajax_wafp_delete_subscription', 'WafpSubscriptionsController::delete_subscription');
    add_action('wp_dashboard_setup', 'WafpAppController::add_dashboard_widgets');
  }

  public static function load_shared_admin_scripts() {
    global $wp_version;

    wp_enqueue_style( 'wafp-fontello-affiliate-royale',
                      WAFP_VENDOR_URL.'/fontello/css/affiliate-royale.css',
                      array(), WAFP_VERSION );

    // If we're in 3.8 now then use a font for the admin image
    if( version_compare( $wp_version, '3.8', '>=' ) ) {
      wp_enqueue_style( 'wafp-menu-styles', WAFP_CSS_URL.'/menu-styles.css',
                        array('wafp-fontello-affiliate-royale'), WAFP_VERSION );
    }

    wp_enqueue_style('wafp-admin-shared', WAFP_CSS_URL . '/admin-shared.css', array(), WAFP_VERSION);
    wp_enqueue_script('esaf-tooltip', WAFP_JS_URL.'/tooltip.js', array('jquery','wp-pointer'), WAFP_VERSION);
  }

  public static function setup_menus() {
    add_action('admin_menu', 'WafpAppController::menu');
  }

  /********* INSTALL PLUGIN ***********/
  public static function install() {
    global $wafp_db;

    $wafp_db->upgrade();
  }

  public static function menu() {
    // global $activation_type;

    $logo_url = WAFP_IMAGES_URL . "/affiliate_royale_mini_logo_32.png";

    self::admin_separator();

    $wafp_main_menu_hook = add_menu_page(
       __('Affiliate Royale', 'affiliate-royale', 'easy-affiliate'),
       __('Affiliate Royale', 'affiliate-royale', 'easy-affiliate'),
       'administrator', 'affiliate-royale',
       'WafpReportsController::overview',
       $logo_url, 775877 );

    $wafp_reports_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Reports', 'affiliate-royale', 'easy-affiliate'),
      __('Reports', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale',
      'WafpReportsController::overview' );

    $wafp_affiliates_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Affiliates', 'affiliate-royale', 'easy-affiliate'),
      __('Affiliates', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-affiliates',
      'WafpUsersController::display_affiliates_list' );

    $wafp_clicks_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Clicks', 'affiliate-royale', 'easy-affiliate'),
      __('Clicks', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-clicks',
      'WafpClicksController::route' );

    $wafp_transactions_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Transactions', 'affiliate-royale', 'easy-affiliate'),
      __('Transactions', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-transactions',
      'WafpTransactionsController::route' );

    $wafp_subscriptions_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Subscriptions', 'affiliate-royale', 'easy-affiliate'),
      __('Subscriptions', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-subscriptions',
      'WafpSubscriptionsController::route' );

    $wafp_payments_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Payments', 'affiliate-royale', 'easy-affiliate'),
      __('Payments', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-payments',
      'WafpPaymentsController::route' );

    $wafp_links_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Links &amp; Banners', 'affiliate-royale', 'easy-affiliate'),
      __('Links &amp; Banners', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-links',
      'WafpLinksController::route' );

    $wafp_pay_affiliates_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Pay Affiliates', 'affiliate-royale', 'easy-affiliate'),
      __('Pay Affiliates', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-pay-affiliates',
      'WafpPaymentsController::route' );

    $wafp_options_menu_hook = add_submenu_page(
      'affiliate-royale',
      __('Options', 'affiliate-royale', 'easy-affiliate'),
      __('Options', 'affiliate-royale', 'easy-affiliate'),
      'administrator', 'affiliate-royale-options',
      'WafpOptionsController::route' );

    if( !defined('AFFILIATE_ROYALE_LICENSE_KEY') and
        !defined('MEMBERPRESS_LICENSE_KEY') and
        class_exists('WafpUpdateController') )
    {
      $wafp_activate_menu_hook = add_submenu_page(
        'affiliate-royale',
        __('Activate', 'affiliate-royale', 'easy-affiliate'),
        __('Activate', 'affiliate-royale', 'easy-affiliate'),
        'administrator', 'affiliate-royale-activate',
        'WafpUpdateController::route' );
    }

    add_action("admin_print_scripts-{$wafp_reports_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_clicks_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_transactions_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_subscriptions_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_payments_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_pay_affiliates_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_affiliates_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_links_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_scripts-{$wafp_options_menu_hook}", 'WafpAppController::load_admin_scripts');

    if(isset($wafp_activate_menu_hook)) {
      add_action("admin_print_scripts-{$wafp_activate_menu_hook}", 'WafpAppController::load_admin_scripts');
    }

    add_action('admin_print_scripts-users.php', 'WafpAppController::load_admin_scripts');
    add_action('admin_print_scripts-user-edit.php', 'WafpAppController::load_admin_scripts');
    add_action('admin_print_scripts-profile.php', 'WafpAppController::load_admin_scripts');
    add_action('admin_print_scripts-index.php', 'WafpAppController::load_admin_scripts');

    add_action("admin_print_styles-{$wafp_reports_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_clicks_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_transactions_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_subscriptions_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_pay_affiliates_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_payments_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_affiliates_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_links_menu_hook}", 'WafpAppController::load_admin_styles');
    add_action("admin_print_styles-{$wafp_options_menu_hook}", 'WafpAppController::load_admin_styles');

    if(isset($wafp_activate_menu_hook))
      add_action("admin_print_styles-{$wafp_activate_menu_hook}", 'WafpAppController::load_admin_styles');

    add_action('admin_print_styles-users.php', 'WafpAppController::load_admin_styles');
    add_action('admin_print_styles-user-edit.php', 'WafpAppController::load_admin_styles');
    add_action('admin_print_styles-profile.php', 'WafpAppController::load_admin_styles');
    add_action('admin_print_styles-index.php', 'WafpAppController::load_admin_styles');

    add_action("admin_print_scripts-{$wafp_main_menu_hook}", 'WafpAppController::load_admin_scripts');
    add_action("admin_print_styles-{$wafp_main_menu_hook}", 'WafpAppController::load_admin_styles');
  }

  /**
   * Add a separator to the WordPress admin menus
   */
  public static function admin_separator() {
    // Prevent duplicate separators when no core menu items exist
    if ( !current_user_can( 'manage_options' ) )
      return;

    global $menu;
    $menu[] = array( '', 'read', 'separator-affiliate-royale', '', 'wp-menu-separator affiliate-royale' );
  }

  /**
   * Move our custom separator above our admin menu
   *
   * @param array $menu_order Menu Order
   * @return array Modified menu order
   */
  public static function admin_menu_order( $menu_order ) {
    if( !$menu_order )
      return true;

    if( !is_array( $menu_order ) )
      return $menu_order;

    // Initialize our custom order array
    $new_menu_order = array();

    // Menu values
    $second_sep   = 'separator2';
    $custom_menus = array( 'separator-affiliate-royale', 'affiliate-royale' );

    // Loop through menu order and do some rearranging
    foreach( $menu_order as $item ) {

      // Position Affiliate Royale menus above appearance
      if( $second_sep == $item ) {

        // Add our custom menus
        foreach( $custom_menus as $custom_menu ) {
          if( array_search( $custom_menu, $menu_order ) ) {
            $new_menu_order[] = $custom_menu;
          }
        }

        // Add the appearance separator
        $new_menu_order[] = $second_sep;

      // Skip our menu items down below
      }
      elseif( !in_array( $item, $custom_menus ) ) {
        $new_menu_order[] = $item;
      }
    }

    // Return our custom order
    return $new_menu_order;
  }

  public static function display_activation_form()
  {
    return;
    // global $wafp_update;
    // require(WAFP_VIEWS_PATH . '/shared/activation_form.php');
  }

  // Routes for wordpress pages -- we're just replacing content here folks.
  public static function page_route($content)
  {
    global $post, $wafp_options;
    $wafp_current_user = WafpUtils::get_currentuserinfo();

    //Setup the $current_post and account for non-singular views
    if(in_the_loop())
      $current_post = get_post(get_the_ID());
    else
      $current_post = $post;

    //Fix for lots of things probably, but mostly the lack of this check was causing issues in OptimizePress
    if(!isset($current_post->ID) || !$current_post->ID) { return $content; }

    //WARNING the_content CAN be run more than once per page load
    //so this static var prevents stuff from happening twice
    //like cancelling a subscr or resuming etc...
    static $already_run = array();
    static $new_content = array();
    static $content_length = array();
    //Init this posts static values
    if(!isset($new_content[$current_post->ID]) || empty($new_content[$current_post->ID])) {
      $already_run[$current_post->ID] = false;
      $new_content[$current_post->ID] = '';
      $content_length[$current_post->ID] = -1;
    }

    if($already_run[$current_post->ID] && strlen($content) == $content_length[$current_post->ID])
      return $new_content[$current_post->ID];

    $content_length[$current_post->ID] = strlen($content);
    $already_run[$current_post->ID] = true;

    if(apply_filters('wafp-stop-page-route', false)) {
      $new_content[$current_post->ID] = $content;
      return $new_content[$current_post->ID];
    }

    switch($current_post->ID)
    {
      case $wafp_options->dashboard_page_id:
        if(post_password_required($current_post)) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        $action = self::get_param('action', 'home');
        // Start output buffering -- we want to return the output as a string
        ob_start();

        if($wafp_current_user)
        {
          if(isset($_POST['become_affiliate_submit'])) // (see views/dashboard/become.php)
          {
            $wafp_current_user->set_is_affiliate(true);
            $wafp_current_user->store();
          }

          if( $wafp_current_user->is_affiliate() and
              !$wafp_current_user->is_blocked() )
          {
            $errors = $wafp_current_user->check_forced_account_info();
            $show_links = (bool)WafpLink::get_count();
            if(!empty($errors))
              WafpDashboardController::display_info_forced($errors);
            else if($action=='home' or empty($action))
              WafpDashboardController::display_dashboard();
            else if($action=='links' and $show_links)
              WafpDashboardController::display_links();
            else if($action=='stats')
              WafpDashboardController::display_stats();
            else if($action=='referrals' and $wafp_options->dash_show_genealogy)
              WafpDashboardController::display_referrals();
            else if($action=='payments')
              WafpDashboardController::display_payments();
            else if($action=='account')
              WafpDashboardController::display_account();
          }
          else if( $wafp_current_user->is_blocked() ) {
            WafpDashboardController::display_blocked_affiliate();
          }
          else {
            WafpDashboardController::display_become_affiliate(); //Added by Paul (shows if not affiliate)
          }
        }
        else
        {
          $loginURL = WafpUtils::login_url();
          require( WAFP_VIEWS_PATH . "/shared/unauthorized.php" );
        }

        // Pull all the output into this variable
        $content .= ob_get_contents();
        // End and erase the output buffer (so we control where it's output)
        ob_end_clean();
        break;
      case $wafp_options->login_page_id:
        if( post_password_required($current_post) ) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        ob_start();
        $action = self::get_param('action');

        if( $action and $action == 'forgot_password' )
          WafpUsersController::display_forgot_password_form();
        else if( $action and $action == 'wafp_process_forgot_password' )
          WafpUsersController::process_forgot_password_form();
        else if( $action and $action == 'reset_password')
          WafpUsersController::display_reset_password_form(self::get_param('mkey'),urldecode(self::get_param('u')));
        else if( $action and $action == 'wafp_process_reset_password_form')
          WafpUsersController::process_reset_password_form();
        else
          WafpUsersController::display_login_form();

        $content .= ob_get_contents();
        ob_end_clean();
        break;
      case $wafp_options->signup_page_id:
        if( post_password_required($current_post) ) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        ob_start();
        WafpUsersController::display_signup_form();
        $content .= ob_get_contents();
        ob_end_clean();
        break;
    }

    $new_content[$current_post->ID] = $content;
    return $new_content[$current_post->ID];
  }

  public static function load_scripts()
  {
    self::enqueue_wafp_scripts();
  }

  public static function load_admin_styles() {
    wp_enqueue_style( 'affiliate-royale',  WAFP_CSS_URL . '/affiliate-royale.css', array() );
    wp_enqueue_style( 'thickbox' );
  }

  public static function load_admin_scripts()
  {
    global $wafp_options;

    self::enqueue_wafp_scripts();
    wp_enqueue_script( 'affiliate-royale-admin', WAFP_JS_URL . '/affiliate-royale-admin.js', array('jquery','jquery-flot','affiliate-royale') );
    wp_localize_script( 'affiliate-royale-admin',
                        'WafpCommissions',
                        array( 'commission_level' => __('Commission Level', 'affiliate-royale', 'easy-affiliate'),
                               'referrer' => __('Affiliate', 'affiliate-royale', 'easy-affiliate'),
                               'commission_type' => __('Commission Type', 'affiliate-royale', 'easy-affiliate'),
                               'currency_symbol' => $wafp_options->currency_symbol,
                               'commission_type_percentage' => __('Percentage', 'affiliate-royale', 'easy-affiliate'),
                               'commission_type_fixed' => __('Fixed Amount', 'affiliate-royale', 'easy-affiliate'),
                               'commission_percentage' => __('Commission', 'affiliate-royale', 'easy-affiliate') ) );
    wp_enqueue_script( 'media-upload' );
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_script( 'suggest' );
  }

  public static function enqueue_wafp_scripts()
  {
    global $wafp_blogurl, $wafp_options, $post;

    if( is_admin() xor (
          is_object($post) and (
            $post->ID == $wafp_options->dashboard_page_id or
            $post->ID == $wafp_options->signup_page_id or
            $post->ID == $wafp_options->login_page_id or
            preg_match('~\[wafp_~',$post->post_content) ) ) ) {
      wp_enqueue_style( 'wafp-fontello-affiliate-royale',
                        WAFP_VENDOR_URL.'/fontello/css/affiliate-royale.css',
                        array(), WAFP_VERSION );
      wp_enqueue_style( 'clipboardtip', WAFP_CSS_URL . '/tooltipster.bundle.min.css', null, WAFP_VERSION );
      wp_enqueue_style( 'clipboardtip-borderless', WAFP_CSS_URL . '/tooltipster-sideTip-borderless.min.css', array('clipboardtip'), WAFP_VERSION );
      wp_enqueue_style( 'affiliate-royale',  WAFP_CSS_URL . '/affiliate-royale.css', array('wafp-fontello-affiliate-royale','clipboardtip','clipboardtip-borderless') );

      wp_enqueue_script( 'excanvas', WAFP_JS_URL . '/excanvas.min.js', array() );
      wp_enqueue_script( 'jquery-flot', WAFP_JS_URL . '/jquery.flot.min.js', array('excanvas','jquery'), '0.7' );
      //wp_enqueue_script( 'jquery-clippy', WAFP_JS_URL . '/jquery.clippy.js', array('jquery') );
      wp_enqueue_script( 'clipboard-js', WAFP_JS_URL . '/clipboard.min.js', null, WAFP_VERSION );
      wp_enqueue_script( 'jquery-tooltipster', WAFP_JS_URL . '/tooltipster.bundle.min.js', array('jquery'), WAFP_VERSION );
      wp_enqueue_script( 'affiliate-royale', WAFP_JS_URL . '/affiliate-royale.js', array('jquery','jquery-flot','clipboard-js','jquery-tooltipster') );
      wp_enqueue_script( 'jquery' );
    }

    //Load the leaderboard CSS
    if(isset($post->post_content) && strstr($post->post_content, 'wafp_leaderboard') !== false)
      wp_enqueue_style('affiliate-royale-leaderboard-css', WAFP_CSS_URL . '/leaderboard.css');
  }

  // The tight way to process standalone requests dogg...
  public static function parse_standalone_request() {
    global $wafp_options;

    $plugin     = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    $action     = isset($_REQUEST['action'])?$_REQUEST['action']:'';
    $controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';

    $request_uri = $_SERVER['REQUEST_URI'];

    // TRIM PARAMS FROM REQUEST_URI
    $request_uri = preg_replace('#\?.*#','',$_SERVER['REQUEST_URI']);
    preg_match('#^https?://[^/]+(/.*)?#', home_url(), $matches);
    $pre_slug = isset($matches[1])?$matches[1]:'';

    if(!empty($plugin) && $plugin == 'wafp' && !empty($controller) && !empty($action)) {
      self::standalone_route($controller, $action);
      exit;
    }
    else if(isset($_POST) && isset($_POST['wafp_process_login_form'])) {
      WafpUsersController::process_login_form();
    }
    else if($wafp_options->pretty_affiliate_links &&
            preg_match('#^'.$pre_slug.'/([^/]*)/([^/]*)/?$#', $request_uri, $matches) &&
            isset($matches[1]) && isset($matches[2]) && !empty($matches[2]) &&
            ($affiliate_id = WafpUser::get_aff_id_from_string($matches[1])) &&
            ($link = WafpLink::get_link_from_slug($matches[2]))) {
      WafpLinksController::redirect_link($link->rec->id, $affiliate_id);
    }
  }

  // Routes for standalone / ajax requests
  public static function standalone_route($controller, $action) {
    if($controller == 'links') {
      if($action == 'redirect') { //Deprecated
        // accept an id or slug for the link
        if($link = WafpLink::get_link_from_slug(WafpAppController::get_param('l'))) {
          // accept an id or username for the affiliate
          $affiliate_id = username_exists(urldecode(WafpAppController::get_param('a')));

          if(!$affiliate_id) {
            $affiliate_id = urldecode(WafpAppController::get_param('a'));
          }

          WafpLinksController::redirect_link($link->rec->id, $affiliate_id);
        }
      }
      else if($action == 'pixel') { //Deprecated
        WafpLinksController::track_link(urldecode(WafpAppController::get_param('a')));
      }
      else if($action == 'delete') { //Ajax request
        WafpLinksController::delete_link(WafpAppController::get_param('lid'));
      }
    }
    else if($controller=='transactions') {
      if($action == 'track') {
        // Translate parameters for iDevAffiliate URL compatibility...
        if(isset($_REQUEST['idev_saleamt']) and !empty($_REQUEST['idev_saleamt'])) {
          $_REQUEST['amount'] = $_REQUEST['idev_saleamt'];
        }

        if(isset($_REQUEST['idev_ordernum']) and !empty($_REQUEST['idev_ordernum'])) {
          $_REQUEST['order_id'] = $_REQUEST['idev_ordernum'];
        }

        if(!isset($_REQUEST['order_id'])) {
          $_REQUEST['order_id'] = uniqid();
        }

        WafpTransactionsController::track($_REQUEST['amount'],
                                          $_REQUEST['order_id'],
                                          $_REQUEST['prod_id'],
                                          $_REQUEST['aff_id'],
                                          $_REQUEST['subscr_id'],
                                          '',
                                          $_REQUEST['timeout'],
                                          $_REQUEST['clear']);
      }
    }
    else if($controller == 'reports') {
      if($action == 'admin_affiliate_stats') {
        WafpReportsController::admin_affiliate_stats(WafpAppController::get_param('period'));
      }
      else if($action == 'admin_affiliate_top') {
        WafpReportsController::admin_affiliate_top(WafpAppController::get_param('period'), WafpAppController::get_param('wafpage'));
      }
      else if($action == 'admin_affiliate_payments') {
        WafpPaymentsController::admin_affiliates_owed(WafpAppController::get_param('period'));
      }
    }
    else if($controller == 'payments') {
      if($action=='paypal_bulk_file') {
        WafpPaymentsController::admin_paypal_bulk_file(WafpAppController::get_param('id'));
      }
    }
    else if($controller == 'dashboard') {
      if($action=='dashboard_affiliate_stats') {
        WafpDashboardController::display_stats(WafpAppController::get_param('period'), false);
      }
    }
    else {
      do_action('wafp_process_route');
    }
  }

  public static function load_language() {
    $path_from_plugins_folder = WAFP_PLUGIN_NAME . '/i18n/';

    load_plugin_textdomain( 'affiliate-royale', false, $path_from_plugins_folder );
  }

  public static function load_dynamic_css() {
    global $post, $wafp_options;

    if( is_object($post) and (
          $post->ID == $wafp_options->dashboard_page_id or
          $post->ID == $wafp_options->signup_page_id or
          $post->ID == $wafp_options->login_page_id ) ) {
      ?>
        <style type="text/css">
          #wafp-dash-wrapper {
            color: #333;
            width: 100%;
          }

          .wafp-clipboard {
            padding: 2px 0 2px 0;
            width: 100px;
            display: inline-block;
          }

          .icon-clipboardjs {
            cursor: pointer;
          }
        </style>
        <script type="text/javascript">
          jQuery(document).ready(function($) {
            /* Set up the clippies! */
            $('.icon-clipboardjs').each(function(i, el) {
              var $el = $(el),
                copy_text = 'Copy to Clipboard',
                copied_text = 'Copied!',
                copy_error_text = 'Oops, Copy Failed!',
                clipboard = new ClipboardJS(el),
                instance = $el
                  .tooltipster({
                    theme: 'tooltipster-borderless',
                    content: copy_text,
                    trigger: 'custom',
                    triggerClose: {
                      mouseleave: true,
                      touchleave: true
                    },
                    triggerOpen: {
                      mouseenter: true,
                      touchstart: true
                    }
                  })
                  .tooltipster('instance');

                clipboard
                  .on('success', function(e) {
                    instance
                      .content(copied_text)
                      .one('after', function(){
                        instance.content(copy_text);
                      });
                  })
                  .on('error', function(e) {
                    instance
                      .content(copy_error_text)
                      .one('after', function(){
                        instance.content(copy_text);
                      });
                  });
              });
          });
        </script>
      <?php
    }
  }

  // Utility function to grab the parameter whether it's a get or post
  public static function get_param($param, $default='')
  {
    if(!isset($_REQUEST) or empty($_REQUEST) or !isset($_REQUEST[$param]))
      return $default;

    return $_REQUEST[$param];
  }

  public static function get_param_delimiter_char($link)
  {
    return ((preg_match("#\?#",$link))?'&':'?');
  }

  public static function configure_options_warning()
  {
    global $wafp_options;

    if(!$wafp_options->setup_complete)
      require(WAFP_VIEWS_PATH . '/shared/must_configure.php');
  }

  public static function add_dashboard_widgets()
  {
    if(!WafpUtils::is_admin())
      return;

    wp_add_dashboard_widget('ar_weekly_stats_widget', 'Affiliate Royale Weekly Stats', 'WafpAppController::weekly_stats_widget');

    // Globalize the metaboxes array, this holds all the widgets for wp-admin

    global $wp_meta_boxes;

    // Get the regular dashboard widgets array
    // (which has our new widget already but at the end)

    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

    // Backup and delete our new dashbaord widget from the end of the array

    $ar_weekly_stats_widget_backup = array('ar_weekly_stats_widget' => $normal_dashboard['ar_weekly_stats_widget']);
    unset($normal_dashboard['ar_weekly_stats_widget']);

    // Merge the two arrays together so our widget is at the beginning

    $sorted_dashboard = array_merge($ar_weekly_stats_widget_backup, $normal_dashboard);

    // Save the sorted array back into the original metaboxes

    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
  }

  public static function weekly_stats_widget()
  {
    $stats = WafpReport::last_n_days_stats();
    require(WAFP_VIEWS_PATH . '/reports/weekly_stats.php');
  }

  public static function record_generic_affiliate_link() {
    global $wafp_options;

    if(isset($_REQUEST['aff'])) {
      $id_or_login = urldecode($_REQUEST['aff']);
      $aff_id = 0;

      if(is_numeric($id_or_login)) {
        WafpLink::track($id_or_login);
        $aff_id = $id_or_login;
      }
      else {
        $affiliate = new WafpUser();
        $affiliate->load_user_data_by_login( $id_or_login );
        $aff_id = $affiliate->get_id();

        if(isset($affiliate->userdata[ WafpUser::$id_str ])) {
          WafpLink::track($aff_id);
        }
      }

      if(isset($_REQUEST['p'])) {
        if(!is_numeric($_REQUEST['p'])) {
          $link = WafpLink::get_link_from_slug(esc_html($_REQUEST['p']));
          $link_id = $link->rec->id;
        }
        else
          $link_id = $_REQUEST['p'];

        WafpLinksController::redirect_link($link_id, $affiliate_id); //$affiliate_id is never set? And what is the $_REQUEST['p'] stuff anyways?
      }
      elseif($wafp_options->custom_default_redirect) {
        WafpUtils::wp_redirect( apply_filters('wafp_affiliate_target_url', $wafp_options->custom_default_redirect_url, $aff_id) );
        exit;
      }
    }
  }
}
