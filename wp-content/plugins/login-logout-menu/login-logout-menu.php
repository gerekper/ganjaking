<?php
/**
* Plugin Name:    Login Logout Menu
* Plugin URI:     http://WPBrigade.com/wordpress/plugins/login-logout-menu/
* Description:    Login Logout Menu is a handy plugin which allows you to add login, logout, register and profile menu items in your selected menu.
* Version:        1.1.0
* Author:         WPBrigade
* Author URI:     https://WPBrigade.com/
* Text Domain:    login-logout-menu
* Domain Path:    /languages
*
* @package loginpress
* @category Core
* @author WPBrigade
**/

if ( !class_exists( 'Login_Logout_Menu' ) ) :

  /**
  *
  */
  class Login_Logout_Menu {

    /**
    * @var string
    * @since 1.0.0
    */
    public $version = '1.1.0';

    /**
    * @var The single instance of the class
    * @since 1.0.0
    */
    protected static $_instance = null;

    /** * * * * * * * *
    * Class constructor
    * @since 1.0.0
    * * * * * * * * * */
    public function __construct() {

      $this->define_constants();
      $this->_hooks();
    }

    /**
    * Define Login Logout Menu Constants
    * @since 1.0.0
    */
    private function define_constants() {

      $this->define( 'LOGIN_LOGOUT_MENU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
      $this->define( 'LOGIN_LOGOUT_MENU_DIR_PATH',        plugin_dir_path( __FILE__ ) );
      $this->define( 'LOGIN_LOGOUT_MENU_DIR_URL',         plugin_dir_url( __FILE__ ) );
      $this->define( 'LOGIN_LOGOUT_MENU_ROOT_PATH',       dirname( __FILE__ ) . '/' );
      $this->define( 'LOGIN_LOGOUT_MENU_VERSION',         $this->version );
      $this->define( 'LOGIN_LOGOUT_MENU_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
    }

    /**
    * Hook into actions and filters
    * @since  1.0.0
    */
    private function _hooks() {

      add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
      add_action( 'admin_head-nav-menus.php', array( $this, 'admin_nav_menu' ) );
      add_filter( 'wp_setup_nav_menu_item', array( $this, 'login_logout_setup_menu' ) );
      add_filter( 'wp_nav_menu_objects', array( $this, 'login_logout_menu_objects' ) );
    }

    /**
    * Main Instance
    *
    * @since 1.0.0
    * @static
    * @see login_logout_menu_loader()
    * @return Main instance
    */
    public static function instance() {
      if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }


    /**
    * Load Languages
    * @since 1.0.0
    */
    public function textdomain() {

      $plugin_dir =  dirname( plugin_basename( __FILE__ ) ) ;
      load_plugin_textdomain( 'login-logout-menu', false, $plugin_dir . '/languages/' );
    }

    /* Registers Login/Logout/Register Links Metabox */
    function admin_nav_menu() {
      add_meta_box( 'login_logout_menu', __( 'Login Logout Menu', 'login-logout-menu' ), array( $this, 'admin_nav_menu_callback' ), 'nav-menus', 'side', 'default' );
    }

    /* Displays Login/Logout/Register Links Metabox */
    function admin_nav_menu_callback(){

      global $nav_menu_selected_id;

      $elems = array(
        '#loginpress-login#'	      => __( 'Log In', 'login-logout-menu' ),
        '#loginpress-logout#'	    => __( 'Log Out', 'login-logout-menu' ),
        '#loginpress-loginlogout#' => __( 'Log In', 'login-logout-menu' ) . ' | ' . __( 'Log Out', 'login-logout-menu' ),
        '#loginpress-register#'    => __( 'Register', 'login-logout-menu' ),
        '#loginpress-profile#'     => __( 'Profile', 'login-logout-menu' )
      );
      $logitems = array(
        'db_id' => 0,
        'object' => 'bawlog',
        'object_id',
        'menu_item_parent' => 0,
        'type' => 'custom',
        'title',
        'url',
        'target' => '',
        'attr_title' => '',
        'classes' => array(),
        'xfn' => '',
      );

      $elems_obj = array();
      foreach ( $elems as $value => $title ) {
        $elems_obj[ $title ] 		= (object) $logitems;
        $elems_obj[ $title ]->object_id	= esc_attr( $value );
        $elems_obj[ $title ]->title	= esc_attr( $title );
        $elems_obj[ $title ]->url	= esc_attr( $value );
      }

      $walker = new Walker_Nav_Menu_Checklist( array() );
      ?>
      <div id="login-links" class="loginlinksdiv">

        <div id="tabs-panel-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
          <ul id="login-linkschecklist" class="list:login-links categorychecklist form-no-clear">
            <?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $elems_obj ), 0, (object) array( 'walker' => $walker ) ); ?>
          </ul>
        </div>

        <p class="button-controls">
          <span class="list-controls hide-if-no-js">
            <a href="javascript:void(0);" class="help" onclick="jQuery( '#login-logout-menu-help' ).toggle();"><?php _e( 'Help', 'login-logout-menu' ); ?></a>
            <span class="hide-if-js" id="login-logout-menu-help"><br /><a name="login-logout-menu-help"></a>
              <?php
              echo '&#9725;' . esc_html__( 'To redirect user after login/logout/register just add a relative link after the link\'s keyword, example :', 'login-logout-menu' ) . ' <br /><code>#loginpress-loginlogout#index.php</code>.';
              echo '<br /><br />&#9725;' . esc_html__( 'You can also use', 'login-logout-menu' ) . ' <code>%current-page%</code> ' . esc_html__( 'to redirect the user on the current visited page after login/logout/register, example :', 'login-logout-menu' ) . ' <code>#loginpress-loginlogout#%current-page%</code>.<br /><br />';
              echo sprintf( __( 'To get plugin support contact us on <a href="%1$s" target="_blank">plugin support forum</a> or <a href="%2$s" target="_blank">contact us page</a>.', 'login-logout-menu'), 'https://wpbrigade.com/wordpress/plugins/login-logout-menu/', 'https://wpbrigade.com/contact/' ) . '<br /><br />';
                ?>
              </span>
            </span>

            <span class="add-to-menu">
              <input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'login-logout-menu' ); ?>" name="add-login-links-menu-item" id="submit-login-links" />
              <span class="spinner"></span>
            </span>
          </p>

        </div>
        <?php

      }

      /**
       * Show Login || Logout Menu item for front end.
       *
       * @since 1.0.0
       * @param object $menu_item The menu item object.
       */
      function login_logout_setup_title( $title ) {

        $titles = explode( '|', $title );

        if ( ! is_user_logged_in() ) {
          return esc_html( isset( $titles[0] ) ? $titles[0] : $title );
        } else {
          return esc_html( isset( $titles[1] ) ? $titles[1] : $title );
        }
      }

      /**
       * Filters a navigation menu item object. Decorates a menu item object with the shared navigation menu item properties on front end.
       *
       * @since 1.0.0
       * @param object $menu_item The menu item object.
       */
      function login_logout_setup_menu( $item ) {

        global $pagenow;

        if ( $pagenow != 'nav-menus.php' && ! defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#loginpress' ) != '' ) {

          $item_url = substr( $item->url, 0, strpos( $item->url, '#', 1 ) ) . '#';
          $item_redirect = str_replace( $item_url, '', $item->url );

          if ( $item_redirect == '%current-page%' ) {
            $item_redirect = $_SERVER['REQUEST_URI'];
          }

          switch ( $item_url ) {
            case '#loginpress-loginlogout#' :

            $item_redirect = explode( '|', $item_redirect );

            if ( count( $item_redirect ) != 2 ) {
              $item_redirect[1] = $item_redirect[0];
            }

            if ( is_user_logged_in() ) {

              $item->url = wp_logout_url( $item_redirect[1] );
            } else {

              $item->url = wp_login_url( $item_redirect[0] );
            }

            $item->title = $this->login_logout_setup_title( $item->title ) ;
            break;

            case '#loginpress-login#' :

            if ( is_user_logged_in() ) {
              return $item;
            }

            $item->url = wp_login_url( $item_redirect );
            break;

            case '#loginpress-logout#' :
            if ( ! is_user_logged_in() ) {
              return $item;
            }

            $item->url = wp_logout_url( $item_redirect );
            break;

            case '#loginpress-register#' :

            if ( is_user_logged_in() ) {
              return $item;
            }

            $item->url = wp_registration_url();
            break;

            case '#loginpress-profile#' :
            if ( ! is_user_logged_in() ) {
              return $item;
            }

            if ( function_exists('bp_core_get_user_domain') ) {
              $url = bp_core_get_user_domain( get_current_user_id() );
            } else if ( function_exists('bbp_get_user_profile_url') ) {
              $url = bbp_get_user_profile_url( get_current_user_id() );
            } else if ( class_exists( 'WooCommerce' ) ) {
              $url = get_permalink( get_option('woocommerce_myaccount_page_id') );
            } else {
              $url = get_edit_user_link();
            }

            $item->url = esc_url( $url );
            break;
          }
          $item->url = esc_url( $item->url );
        }
        return $item;
      }


      function login_logout_menu_objects( $sorted_menu_items ) {

        foreach ( $sorted_menu_items as $menu => $item ) {
          if ( strstr( $item->url, '#loginpress' ) != '' ) {
            unset( $sorted_menu_items[ $menu ] );
          }
        }
        return $sorted_menu_items;
      }


      /**
      * Define constant if not already set
      * @param  string $name
      * @param  string|bool $value
      */
      private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
          define( $name, $value );
        }
      }

    }

  endif;


  /**
  * Returns the main instance of WP to prevent the need to use globals.
  *
  * @since  1.0.0
  * @return Login_Logout_Menu
  */
  function login_logout_menu_loader() {
    return Login_Logout_Menu::instance();
  }

  // Call the function
  login_logout_menu_loader();
