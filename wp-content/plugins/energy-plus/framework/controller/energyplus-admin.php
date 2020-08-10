<?php

/**
* EnergyPlus Admin
*
* Actions/Filters and initial functions
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
*/


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class EnergyPlus_Admin {

  public static $api = array();
  public static $menu_hash = array();

  /**
  * Construct
  *
  * @since    1.0.0
  */

  public function __construct() {

    // Actions
    add_action( 'admin_init',                         'EnergyPlus_Admin::init' );
    add_action( 'admin_head',                         'EnergyPlus_Admin::admin_head', 10);
    add_action( 'in_admin_header',                    'EnergyPlus_Admin::in_admin_header', 10);
    add_action( 'admin_menu',                         'EnergyPlus_Admin::admin_menu' );
    add_action( 'admin_enqueue_scripts',              'EnergyPlus_Admin::styles' );
    add_action( 'energyplus_submenu',                 'EnergyPlus_Helpers::submenu', 10, 1);
    add_action( 'wp_ajax_energyplus_ajax',            'EnergyPlus_Ajax::run');
    add_action( 'wp_ajax_energyplus_settings',        'EnergyPlus_Settings::ajax');
    add_action( 'wp_ajax_energyplus_widgets',         'EnergyPlus_Widgets::ajax');
    add_action( 'wp_ajax_energyplus_settings_panels', 'EnergyPlus_Settings::ajax_panels_active');
    add_action( 'woocommerce_api_pagination_headers', 'EnergyPlus_Helpers::api_pagination', 10, 2);
    add_action( 'energyplus_need',                    'EnergyPlus_Helpers::need');

    // Filters
    add_filter( 'admin_title',                        'EnergyPlus_Admin::admin_title', 10, 1);
    add_filter( 'admin_body_class',                   'EnergyPlus_Admin::admin_body_class',10, 1 );
    add_filter( 'comment_edit_redirect',              'EnergyPlus_Admin::filter_comment_edit_redirect', 10, 2 );


  }

  /**
  * initial
  *
  * @since 1.0.0
  */

  public static function init() {

    global $pagenow;

    /* Check if WooCommerce is activated. */

    if (!class_exists('WooCommerce')) {
      return;
    }


    if ( "1" === EnergyPlus::option('feature-own_themes')) {
      $current_user    = wp_get_current_user();
      EnergyPlus::$theme = EnergyPlus::option('theme-' . intval( $current_user->ID ));

      if ( !EnergyPlus::$theme ) {
        EnergyPlus::$theme = EnergyPlus::option('theme', 'one');
      }

    } else {
      EnergyPlus::$theme = EnergyPlus::option('theme', 'one');
    }

    // i18n
    $mo_file = EnergyPlus_Framework. 'languages/' . get_locale() . '.mo';

    if (!file_exists($mo_file)) {
      $mo_file = EnergyPlus_Framework. 'languages/energyplus-' . get_locale() . '.mo';
    }

    if (!file_exists($mo_file)) {
      $mo_file = WP_LANG_DIR. '/plugins/energyplus-' . get_locale() . '.mo';
    }

    load_textdomain( 'energyplus', $mo_file );

    if ($pagenow === 'index.php'
    && !is_network_admin()
    && 0 === count($_GET)
    && (
      (self::is_admin(null) && ("1" === EnergyPlus::option( 'feature-use-administrator', "0" )))
      || (self::is_admin(null,'shop_manager') && ("1" === EnergyPlus::option( 'feature-use-shop_manager', "0" )) )
      || ("1" === EnergyPlus::option( 'feature-auto', "0" ))
      )
      && (self::is_admin($user) || is_admin($user, 'shop_manager'))
      )
      {
        wp_redirect( admin_url ( 'admin.php?page=energyplus&segment=' . EnergyPlus::option('reactors-tweaks-landing', 'dashboard') ) );
      }

    }

    /**
    * Title of admin panel
    *
    * @since  1.0.0
    */

    public static function admin_title() {
      return EnergyPlus_Events::get_title(0);
    }

    /**
    * admin_body_class filter
    *
    * @since  1.0.0
    */

    public static function admin_body_class( $classes ) {

      if (self::is_energyplus()) {
        $classes .= ' energyplus-engine';
      }

      if (self::is_full()) {
        $classes .= ' energyplus-full';
      } else {
        $classes .= ' energyplus-half';
      }

      $classes .= ' energyplus-theme-' . EnergyPlus::$theme;

      return "$classes energyplus-admin-" . esc_attr(EnergyPlus_Helpers::get('segment', 'dashboard')) .  " energyplus-action-" . esc_attr(EnergyPlus_Helpers::get('action', 'default'));
    }

    /**
    * Build and show admin menu
    *
    * @since  1.0.0
    */
    public static function get_menu( $args = array() ) {

      global $submenu;

      $energyplus_menu = EnergyPlus::option('menu', array());

      if (0 === count ($energyplus_menu) OR !is_array($energyplus_menu)) {

        EnergyPlus::option( 'menu', array (
          'energyplus-dashboard' => array( 'title' => __('Dashboard', 'energyplus'), 'segment' => 'dashboard', 'icon' => 'dashicons-admin-site', 'order' => -9, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-orders'    => array( 'title' => __('Orders', 'energyplus'), 'segment' => 'orders', 'icon' => 'dashicons-cart', 'order' => -8, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-products'  => array( 'title' => __('Products', 'energyplus'), 'segment' => 'products', 'icon' => 'dashicons-screenoptions', 'order' => -7, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-customers' => array( 'title' => __('Customers', 'energyplus'), 'segment' => 'customers', 'icon' => 'dashicons-admin-users', 'order' => -6, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-reports'   => array( 'title' => __('Reports', 'energyplus'), 'segment' => 'reports', 'icon' => 'dashicons-chart-pie', 'order' => -5, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-coupons'   => array( 'title' => __('Coupons', 'energyplus'), 'segment' => 'coupons', 'icon' => 'dashicons-carrot', 'order' => -4, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-reactors'   => array( 'title' => __('Reactors', 'energyplus'), 'segment' => 'reactors', 'icon' => 'dashicons-share-alt', 'order' => -4, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'energyplus-comments'  => array( 'title' => __('Comments', 'energyplus'), 'segment' => 'comments', 'icon' => 'dashicons-admin-comments', 'order' => -3, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
        ), 'set');

        $energyplus_menu = EnergyPlus::option('menu', array());
      }

      $output = array();

      if (isset( $args['settings'] )) {
        $energyplus_menu['energyplus-settings'] =  array( 'title' => __('Settings', 'energyplus'), 'segment' => 'settings', 'icon' => 'dashicons-admin-generic', 'order' => 999, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1));
      }

      if (!isset($energyplus_menu['energyplus-reactors']) && self::is_admin(null)) {
        $energyplus_menu['energyplus-reactors'] = array( 'title' => __('Reactors', 'energyplus'), 'segment' => 'reactors', 'icon' => 'dashicons-share-alt', 'order' => 6, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1));
      } else {
        if (!self::is_admin(null)) {
          unset($energyplus_menu['energyplus-reactors']);
        }
      }

      $next_index = 100;

      if (self::is_admin(null, 'administrator')) {
        $user_role = 'administrator';
      } elseif (self::is_admin(null, 'shop_manager')) {
        $user_role = 'shop_manager';
      } else {
        $user_role = 'other';
      }


      // Add all menus to EnergyPlus Menu
      foreach ($GLOBALS[ 'menu' ] as $key => $value) {
        if (isset($value[5]) && $value[5] !== "toplevel_page_energyplus") {
          $all_menu[$value[5]] = array(
            'title'=> $value[0],
            'admin_link' => $value[2],
            'active'=>null,
            'other' => '1'
          );

          if (isset($energyplus_menu[$value[5]]['roles'][$user_role])) {
            $all_menu[$value[5]]['active'] = $energyplus_menu[$value[5]]['roles'][$user_role];
            $all_menu[$value[5]]['roles'] = $energyplus_menu[$value[5]]['roles'];
          }

          if (!isset($energyplus_menu[$value[5]]['icon'])) {
            $all_menu[$value[5]]['icon'] = $value[6];
          } else {
            $all_menu[$value[5]]['icon'] = $energyplus_menu[$value[5]]['icon'];
          }

          ++$next_index;

          if (!isset($energyplus_menu[$value[5]]['order'])) {
            $all_menu[$value[5]]['order'] = $next_index;
          } else {
            $all_menu[$value[5]]['order'] = $energyplus_menu[$value[5]]['order'];
          }
        }
      }

      $menu = array_merge($energyplus_menu, $all_menu);

      foreach ($menu AS $_m_k => $_m)  {
        if (isset($_m['parent'])) {
          continue;
        }

        if (!isset($_m['roles'])) {
          $_m['roles'] = array('administrator' =>1, 'shop_manager'=>1);
        }

        if (!isset($_m['active'])) {
          $_m['active'] = 1;
        }

        if ('administrator' === $user_role && 1 === $_m['roles']['administrator']) {
          $_m['active'] = 1;
        } else if ('shop_manager' === $user_role && 1 === $_m['roles']['shop_manager']) {
          $_m['active'] = 1;
        } else {
          $_m['active'] = 0;
        }

        if (!isset($all_menu[$_m_k]) AND false === stripos($_m_k, 'energyplus-') AND false === stripos($_m_k, '0-')) {
          $_m['active'] = 0;
        }

        if (isset($_m['admin_link']) && isset($submenu[$_m['admin_link']])) {
          foreach ($submenu[$_m['admin_link']] AS $sublink_key => $sublink) {
            if (false === stripos($sublink[2], '.')) {
              $sublink[2] = 'admin.php?page='. $sublink[2];
            }

            if ( !empty( get_plugin_page_hook($sublink[2], $_m['admin_link'])) OR ('index.php' !== $sublink[2]  && file_exists( WP_PLUGIN_DIR . "/".$sublink[2] ) )) {
              $sublink[2] = admin_url ( 'admin.php?page=' . $sublink[2] );
            }

            if (false !== stripos($sublink[2], 'customize.php')) {
              $sublink[2] = 'customize.php';
            }

            if (false !== stripos($sublink[2], 'index.php')) {
              $sublink[2] = 'index.php?dashboard=yes';
            }

            self::$menu_hash[md5($sublink[2])] = array($sublink[2], strip_tags($_m['title']).' - '.  $sublink[0]);

            if (!self::is_full()) {
              $sublink[2] = EnergyPlus_Helpers::secure_url('frame', md5($sublink[2]), array('go' => md5($sublink[2]) ));
            }
            $_m["submenu"][$sublink_key]  = $sublink;

          }
        }

        if (isset($_m['admin_link'])) {
          self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);

          if ('index.php' === $_m['admin_link']) {
            $_m['admin_link'] = 'index.php?dashboard=yes';
          }
          if (isset($_m['other']) && false !== stripos($_m['admin_link'], '.')) {

            if ( !empty( get_plugin_page_hook($_m['admin_link'],"admin.php")) OR ( 'index.php' !== $_m['admin_link']  && file_exists( WP_PLUGIN_DIR . "/".$_m['admin_link'] ) )) {
              $_m['admin_link'] = admin_url ( 'admin.php?page=' . $_m['admin_link'] );
            } else {
              $_m['admin_link'] = $_m['admin_link'];
            }
          } else {
            //  if (isset($submenu[$_m['admin_link']])) {
            //    $_m['admin_link'] = 'javascript:;';
            //  } else {
            if (isset($_m['target']) && '_blank' === $_m['target']) {
              $_m['admin_link'] = $_m['admin_link'];
            } else if (false === stripos($_m['admin_link'], '.')) {
              self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
              $_m['admin_link'] = admin_url ( 'admin.php?page=' . $_m['admin_link'] );
            } else {
              self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
              $_m['admin_link'] = EnergyPlus_Helpers::secure_url('frame', md5($_m['admin_link']), array('go' => md5($_m['admin_link']) ));
            }
            //  }
          }

          if (!self::is_full() && isset($_m['other'])) {
            self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
            $_m['admin_link'] = EnergyPlus_Helpers::secure_url('frame', md5($_m['admin_link']), array('go' => md5($_m['admin_link']) ));
          }
        }

        $output[$_m_k] = $_m;

      }

      array_multisort(array_map(function($element) {
        return $element['order'];
      }, $output), SORT_ASC, $output);

      /* Check if WooCommerce is activated. */
      if (class_exists('WooCommerce')) {
        /* Badges */
        $output['energyplus-orders']['badge'] = wc_orders_count('on-hold') + wc_orders_count('processing') + wc_orders_count('pending');
      }

      $output['energyplus-comments']['badge'] = intval(wp_count_comments()->moderated);



      if ( isset($args['hash'])) {
        return;
      }

      return EnergyPlus_View::run('core/menu', array('_energyplus_menu' => $output));

    }


    /**
    * Register admin menu to Wordpress
    *
    * @since  1.0.0
    */

    public static function admin_menu() {
      add_menu_page( 'Energy+', 'Energy+', 'manage_woocommerce', 'energyplus', 'EnergyPlus_Admin::admin_page', 'dashicons-plus-alt' );
    }

    /**
    * Router for EnergyPlus sub panels
    *
    * @since  1.0.0
    */

    public static function admin_page() {

      switch (EnergyPlus_Helpers::get('segment', EnergyPlus::option('reactors-tweaks-landing', 'dashboard'))) {

        case "coupons":
        EnergyPlus_Coupons::run();
        break;

        case "orders":
        EnergyPlus_Orders::run();
        break;

        case "products":
        EnergyPlus_Products::run();
        break;

        case "customers":
        EnergyPlus_Customers::run();
        break;

        case "comments":
        EnergyPlus_Comments::run();
        break;

        case "reports":
        EnergyPlus_Reports::run();
        break;

        case "reactors":
        EnergyPlus_Reactors::run();
        break;

        case "settings":
        EnergyPlus_Settings::run();
        break;


        case "frame":

        if ($url = EnergyPlus_Helpers::get('in')) {

          if ( ! wp_verify_nonce( EnergyPlus_Helpers::get('_asnonce'),  'energyplus-segment--notifications' ) ) {
            die( esc_html__('Failed on security check', 'energyplus') );
          }

          $url = esc_url_raw( urldecode($url) );

          if (strpos($url, admin_url()) !== false && strpos($url, admin_url()) === 0) {
            EnergyPlus_Helpers::frame( EnergyPlus_Helpers::get('in') );
          } else {
            esc_html_e("Restricted Area.", 'energyplus');
            wp_die();
          }

        }  else {

          $go = sanitize_key(EnergyPlus_Helpers::get('go'));

          self::get_menu( array('hash'=> true) );

          if ( !isset(self::$menu_hash[$go]) ) {
            esc_html_e("Restricted Area.", 'energyplus');
            wp_die();
          }

          EnergyPlus_Helpers::frame( self::$menu_hash[$go][0] );
        }

        break;

        default:
        EnergyPlus_Dashboard::run();
        break;
      }

    }

    /**
    * Styles and scripts enqueue
    *
    * @since  1.0.0
    */

    public static function styles() {

      if ((self::is_full()) OR (!self::is_full() && self::is_energyplus())) {

        // Styles
        if (self::is_energyplus()) {
          wp_enqueue_style("energyplus-bootstrap", EnergyPlus_Public . "3rd/bootstrap/4.3.1/css/bootstrap.min.css");

        } else {
          wp_enqueue_style("energyplus-font",           "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese");
          wp_enqueue_style("energyplus-bootstrap-lite", EnergyPlus_Public . "css/bootstrap-lite.css", null, EnergyPlus_Version);
        }

        wp_enqueue_style("energyplus-fontawesome5", EnergyPlus_Public . "3rd/fontawesome/css/all.min.css");


        wp_enqueue_style("energyplus-shared",    EnergyPlus_Public . "css/shared.css", null, EnergyPlus_Version);

        // Themes
        if ('one-shadow' === EnergyPlus::$theme) {
          wp_enqueue_style("energyplus-theme-required",     EnergyPlus_Public . "css/theme-one.css", null, EnergyPlus_Version);
        }

        wp_enqueue_style("energyplus-theme",     EnergyPlus_Public . "css/theme-". esc_attr( EnergyPlus::$theme ) .".css", null, EnergyPlus_Version);

        if ( "1" === EnergyPlus::option('feature-own_themes')) {
          $colors = EnergyPlus::option('colors', EnergyPlus_Settings::colors()['ffffff'], 'get', true);
        } else {
          $colors = EnergyPlus::option('colors', EnergyPlus_Settings::colors()['ffffff'], 'get');
        }
        $colors_css = ":root{";
          foreach ($colors AS $k=>$v) {
            $colors_css .= esc_html("--$k: $v;");
          }
          $colors_css .= "}";

          if ("1" === EnergyPlus::option('reactors-tweaks-screenoptions', "0")) {
            $colors_css .= '#screen-meta-links {display: block !important;position: absolute !important;bottom: 15px;right: 5vw;top: unset !important;} #screen-meta {margin-top: -9px;}#screen-meta-links .show-settings {border-top: 1px solid #ccd0d4; border-radius:inherit;}';
          }

          wp_add_inline_style('energyplus-theme', $colors_css);

          if (file_exists( EnergyPlus_Dir . "/public/css/custom.css")) {
            wp_enqueue_style("energyplus-custom",    EnergyPlus_Public . "css/custom.css", null, EnergyPlus_Version);
          }

          // Fonts

          $fonts = array(
            'one'     => "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese",
            'one-shadow'     => "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese",
            'console' => "//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"
          );

          if (EnergyPlus::option('reactors-tweaks-font')) {
            $font = esc_attr(EnergyPlus::option('reactors-tweaks-font'));
            wp_enqueue_style("energyplus-font2",  "//fonts.googleapis.com/css?family=" . $font . ",700,800&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese");
            wp_add_inline_style('energyplus-font2', '#energyplus-theme,#notifications,#__A__Ajax_Notification,#energyplus-search-1--overlay {font-family: "'.str_replace(array(':400', '+'), array('', ' '), $font).'" !important; }');
          } else {
            wp_enqueue_style("energyplus-font",  $fonts[EnergyPlus::$theme]);
          }

          // Scripts
          wp_enqueue_script("energyplus-3rd",       EnergyPlus_Public . "js/energyplus-3rd.js", array('jquery','jquery-ui-sortable'), EnergyPlus_Version, TRUE);

          if (self::is_energyplus()) {
            wp_enqueue_script("energyplus-bootstrap", EnergyPlus_Public . "3rd/bootstrap/4.3.1/js/bootstrap.bundle.min.js", array("jquery",'jquery-ui-sortable'), EnergyPlus_Version, TRUE);
          }

          wp_enqueue_script("energyplus-admin",     EnergyPlus_Public . "js/energyplus-admin.js", array(), EnergyPlus_Version, TRUE);

          // Enqueue WP Media scripts for file uploads
          if ('settings' === EnergyPlus_Helpers::get('segment')) {
            //  wp_register_style('wp-admin');
            wp_enqueue_media();
          }

          if (EnergyPlus_Reactors::is_installed('energizer')) {
            Reactors__energizer__energizer::styles();
          }

          // Ajax & i18n for EnergyPlus
          $JSvars['ajax_url']                        = admin_url('admin-ajax.php');
          $JSvars['_admin_url']                      = admin_url();
          $JSvars['_asnonce']                        = wp_create_nonce( 'energyplus-segment--' . EnergyPlus_Helpers::get('segment', false));
          $JSvars['_asnonce_notifications']          = wp_create_nonce( 'energyplus-segment--notifications');
          $JSvars['_asnonce_search']                 = wp_create_nonce( 'energyplus-segment--search');
          $JSvars['refresh']                         = absint(EnergyPlus::option('feature-refresh', 10))*1000;
          $JSvars['i18n']                            = array('wait'=> esc_html__('Please wait', 'energyplus'), 'done'=> esc_html__('Done', 'energyplus'));
          $JSvars['reactors_tweaks_window_size']     = EnergyPlus::option('reactors-tweaks-window-size', '1090px');
          $JSvars['reactors_tweaks_adminbar_hotkey'] = intval(EnergyPlus::option('reactors-tweaks-adminbar-hotkey', 1));

          wp_localize_script('energyplus-admin', 'EnergyPlusGlobal', $JSvars);

        }



      }

      public static function is_full() {

        if ("1" === EnergyPlus::option( 'feature-full', "0" )) {
          EnergyPlus::option( 'feature-use-administrator', "1", 'set' );
          EnergyPlus::option( 'feature-use-shop_manager', "1", 'set' );
          delete_option('energyplus_feature-full');
        }

        if (is_network_admin()) {
          return false;
        }

        if (self::is_admin(null, 'administrator') && ("1" === EnergyPlus::option( 'feature-use-administrator', "0" ))) {
          return true;
        } elseif (self::is_admin(null, 'shop_manager') && ("1" === EnergyPlus::option( 'feature-use-shop_manager', "0" ))) {
          return true;
        } else {
          return false;
        }

        return false;
      }

      public static function in_admin_header() {

        if ((self::is_full()) OR (!self::is_full() && self::is_energyplus())) {
          echo EnergyPlus_View::run('header');
        }
      }

      /**
      * Is user in EnergyPlus page?
      *
      * @since  1.0.0
      */

      public static function is_energyplus() {
        return (isset($_GET["page"]) AND $_GET["page"]==='energyplus')?true:false;
      }


      /**
      * Check roles of user
      *
      * @since  1.0.0
      */

      public static function is_admin( $user, $role = 'administrator' ) {
        if ( ! is_object( $user ) ) {
          $user = wp_get_current_user();

        } else {
          //  $user = get_userdata( $user );
        }

        if ( ! $user || ! $user->exists() ) {
          return false;
        }

        return in_array( $role, $user->roles, true );
      }

      /**
      * Auto-start EnergyPlus
      *
      * @since  1.0.0
      */

      public static function admin_default_page($redirect_to, $request, $user ) {

        if( !isset( $user->user_login ) ) {
          return $redirect_to;
        }

        // Auto start
        if ("1" === EnergyPlus::option( 'feature-full', "0" ) && (self::is_admin($user) OR is_admin($user, 'shop_manager')) ) {
          return admin_url ( 'admin.php?page=energyplus' );
        } else {
          return $redirect_to;
        }
      }


      /**
      * Filter for redirection after comment update
      *
      * @since  1.0.0
      */

      public static function filter_comment_edit_redirect($location, $comment_id) {

        if (0 < stripos($_POST['referredby'], 'energyplus')) {
          return admin_url ( "edit-comments.php");
        } else {
          return $location;
        }
      }

      /**
      * Hides WP elements in EnergyPlus panel
      *
      * @since  1.0.0
      */

      public static function admin_head() {

        // When does a POST action, we refresh EnergyPlus page
        if ($_POST) {
          echo '<script>"use strict"; window.parent.refreshOnClose=1;</script>';
        }

        if ((self::is_full()) OR (!self::is_full() && self::is_energyplus())) {
          echo EnergyPlus_View::run('footer');
        }

        // Hides some WP styles when it is loaded from EnergyPlus iframe
        echo '<script>
        "use strict";
        var EnergyPlus_Window = 1; // Necessary global scope with unique prefix
        if (self!==top && window.parent.EnergyPlus_Window != null && window.parent.EnergyPlus_Window != undefined) {
          document.write("<style> \
          body{background: transparent} \
          html.wp-toolbar {padding-top: 0 !important;} \
          #wpbody { width: 100% !important; padding-left:0px !important; padding-top: 0px !important; } \
          .post-type-shop_order.post-php .page-title-action, .update-nag,#energyplus-header, .energyplus-header-top, .energyplus-header-top-container, #trig2, .__A__Site_Name {display:none !important;} \
          #adminmenuback,#adminmenuwrap,#screen-meta-links,#wpadminbar,#woocommerce-embedded-root,.woocommerce-layout__header{display: none !important;} \
          body:not(.energyplus-engine) #wpbody-content { margin-right: 0px; margin-left: 0px; padding-top: 0px; width:100%; } \
          body:not(.energyplus-engine).rtl #wpbody {margin-right: 0px !important} \
          .woocommerce-embed-page .wrap { padding-top:0px !important; } \
          .energyplus-theme-console.woocommerce-embed-page .wrap { padding-top:20px !important; } \
          .branch-5-4.auto-fold .block-editor-editor-skeleton {top:0px !important; left:0px !important;} \
          @media (max-width: 782px) { \
            #wpbody { padding-top: 0px; } \
            body:not(.energyplus-engine) #wpbody { padding-top: 0px !important; } \
            .woocommerce-table__table {width:88vw !important} \
            .woocommerce table.form-table .select2-container, .woocommerce table.form-table input[type=text], .select2-container{width:80vw !important; max-width:80vw !important;min-width:100px !important} \
            .woocommerce_order_items_wrapper {width:85vw !important; max-width:90vw !important} \
            .woocommerce-layout__primary { \
              margin-top: 0px; \
            } \
          } \
          @media (min-width: 782px) { \
            #footer, #wpcontent {margin-left : 0 !important;padding-left: 0 !important;} \
            .rtl #footer, .rtl #wpcontent {margin-right : 0 !important;padding-left: 0 !important;} \
            .rtl #wpcontent { margin-right: 0px; } \
            .woocommerce-embed-page .wrap {padding:00px 0px 0px 0px; width:90%} \
            .woocommerce-layout__primary { margin-left: 0; margin-top: 50px; } \
            .wrap { margin:0 auto !important; width: 90%; padding-top:10px } \
            body.auto-fold .edit-post-layout__content, .edit-post-header {margin-left:0px !important; left: 0 !important;} \
            .woocommerce-layout__primary{margin-top: 20px !important; padding-top:0px !important;} \
            .update-nag a {color: #353535 !important;} \
          } \
          \
          ::-webkit-scrollbar {width: 8px;height: 8px; background-color: rgb(245, 245, 245); }\
          ::-webkit-scrollbar:hover { background-color: rgba(0, 0, 0, 0.09); }\
          ::-webkit-scrollbar-thumb { background : rgb(230, 230, 230);-webkit-border-radius: 100px; } \
          ::-webkit-scrollbar-thumb:active { background : rgba(0,0,0,0.61); -webkit-border-radius: 100px; } \
          </style>");
          jQuery(document).ready(function(jQuery){if (jQuery(".inbrowser--loading", window.parent.document).length>0) { jQuery(".inbrowser--loading", window.parent.document).removeClass("d-flex").addClass("hidden").css("display", "none !important"); } jQuery(".button,.submitdelete").on("click",function() {window.parent.refreshOnClose=1;console.log("refreshOnClose")})});
        }</script>';

      }
    }
