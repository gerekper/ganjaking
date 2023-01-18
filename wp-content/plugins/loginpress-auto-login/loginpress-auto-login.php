<?php
/**
 * Plugin Name: LoginPress - Auto Login
 * Plugin URI: https://loginpress.pro/
 * Description: LoginPress - Auto Login is the best Login plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to login without Username and Password.
 * Version: 2.0.0
 * Author: WPBrigade
 * Author URI: https://www.WPBrigade.com/
 * Text Domain: loginpress-auto-login
 * Domain Path: /languages
 *
 * @package loginpress
 * @category Core
 * @author WPBrigade
 */

if ( ! class_exists( 'LoginPress_AutoLogin' ) ) :

	final class LoginPress_AutoLogin {

		/**
		 * @since  1.0.0
		 * @access public
		 * @var    string variable
		 */
		public $version = '2.0.0';

		/*
		 * * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		function __construct() {

			$this->_hooks();
			$this->define_constants();
			$this->_includes();
		}

		/**
		 * Hook into actions and filters
		 *
		 * @since  1.0.0
		 */
		function _hooks() {

			add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
			add_action( 'plugins_loaded', array( $this, 'textdomain' ), 30 );
			add_filter( 'loginpress_settings_tab', array( $this, 'loginpress_autologin_tab' ), 10, 1 );
			add_filter( 'loginpress_settings_fields', array( $this, 'loginpress_autologin_settings_array' ), 10, 1 );
			add_filter( 'loginpress_autologin', array( $this, 'loginpress_autologin_callback' ), 10, 1 );
      add_action( 'admin_init', array( $this, 'init_addon_updater' ), 0 );
			add_action( 'admin_footer', array( $this, 'loginpress_autocomplete_js' ) );
			add_action( 'wp_ajax_loginpress_autologin', array( $this, 'autologin_update_user_meta' ) );
			add_action( 'wp_ajax_loginpress_autologin_delete', array( $this, 'autologin_delete_user_meta' ) );
			add_action( 'loginpress_autologin_script',  array( $this, 'autologin_script_html' ) );
		}

		/**
		 * [_includes include files]
		 *
		 * @since 1.0.0
		 */
		function _includes() {

			include_once( LOGINPRESS_AutoLogin_DIR_PATH . 'classes/class-user-login.php' );
		}

		/**
		 * Define LoginPress AutoLogin Constants
		 *
		 * @since 1.0.0
		 */
		private function define_constants() {

			$this->define( 'LOGINPRESS_AutoLogin_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'LOGINPRESS_AutoLogin_DIR_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'LOGINPRESS_AutoLogin_DIR_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'LOGINPRESS_AutoLogin_ROOT_PATH',  dirname( __FILE__ ) . '/' );
			$this->define( 'LOGINPRESS_AutoLogin_ROOT_FILE', __FILE__ );
			$this->define( 'LOGINPRESS_AUTOLOGIN_VERSION', $this->version );
			$this->define( 'LOGINPRESS_AUTOLOGIN_PLUGIN_ROOT', dirname( plugin_basename( __FILE__ ) ) );
		}

    /**
     * LoginPress Addon updater
     *
     */
    public function init_addon_updater() {
        if( class_exists( 'LoginPress_AddOn_Updater' ) ) {
          //echo 'Exists';
          $updater = new LoginPress_AddOn_Updater( 2324, __FILE__, $this->version );
        }
      }

		/**
		 * Load CSS and JS files at admin side on loginpress-settings page only.
		 *
		 * @param  string the Page ID
		 * @return void
		 * @since  1.0.0
		 */
		function _admin_scripts( $hook ) {

			if ( $hook != 'toplevel_page_loginpress-settings' ) {
				return;
			}

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_enqueue_style( 'loginpress_autologin_stlye', plugins_url( 'css/style.css', __FILE__ ), array(), LOGINPRESS_AUTOLOGIN_VERSION );

			wp_enqueue_script( 'loginpress_autologin_js', plugins_url( 'js/autologin.js', __FILE__ ), array(), LOGINPRESS_AUTOLOGIN_VERSION );

		}

		/**
		* Load Languages
		* @since 1.0.3
		*/
		public function textdomain() {

			load_plugin_textdomain( 'loginpress-auto-login', false, LOGINPRESS_AUTOLOGIN_PLUGIN_ROOT . '/languages/' );
		}

		/**
		 * Adding a tab for AutoLogin at LoginPress Settings Page.
		 *
		 * @param  [array] $loginpress_tabs [ Tabs of free version ]
		 * @return [array]                   [ AutoLogin tab ]
		 * @since  1.0.0
		 */
		function loginpress_autologin_tab( $loginpress_tabs ) {

			$autologin_tab = array(
				array(
					'id'    => 'loginpress_autologin',
					'title' => __( 'Auto Login', 'loginpress-auto-login' ),
					'desc'  => sprintf( __( 'Autologin lets you (Adminstrator) generates a unique URL for your certain users who you don\'t want to provide a password to login into your site. You will get a list of all the users who you have given auto generated login links. You can disable someones access by deleting that user from the list. %3$s %3$s WordPress Login Screen is customizable through %1$s WordPress Customizer %2$s.', 'loginpress-auto-login' ), '<a href="' . admin_url( 'admin.php?page=loginpress' ) . '">', '</a>', '<br />' ),
				),
			);
			$loginpress_pro_templates = array_merge( $loginpress_tabs, $autologin_tab );
			return $loginpress_pro_templates;
		}

		/**
		 * [Array of the Setting Fields for AutoLogin.]
		 *
		 * @param  [array] $setting_array [ Settings fields of free version ]
		 * @return [array]                [ AutoLogin settings fields ]
		 * @since  1.0.0
		 */
		public function loginpress_autologin_settings_array( $setting_array ) {
			$_autologin_settings = array(
				array(
					'name'              => 'loginpress_autologin',
					'label'             => __( 'Search Username', 'loginpress-auto-login' ),
					'desc'              => __( 'Search Username for making a login magic link for that user.', 'loginpress-auto-login' ),
					'type'              => 'autologin',
				),
			);
			$_autologin_settings = array(
				'loginpress_autologin' => $_autologin_settings,
			);
			return( array_merge( $_autologin_settings, $setting_array ) );
		}

		/**
		 * A callback function that will show a search field under AutoLogin tab.
		 *
		 * @since   1.0.0
		 * @version 1.0.2
		 * @return [string] html
		 */
		function loginpress_autologin_callback( $args ) {

			$loginpress_user_autologin_nonce = wp_create_nonce( 'loginpress-user-autologin-nonce' );
			$html = '<input type="text" name="loginpress_autologin_search" id="loginpress_autologin_search" value="" placeholder="Search by typing Username..." />';
			$html .= '<input type="hidden" class="loginpress__search-autologin_nonce" name="loginpress__search-autologin_nonce" value="' . $loginpress_user_autologin_nonce . '">';
			return $html;
		}

		/**
		 * A callback function that will show search result under the search field.
		 *
		 * @since   1.0.0
		 * @version 1.0.8
		 * @return [string] html
		 */
		function autologin_script_html() {
			/**
			 * Check to apply the script only on the LoginPress Settings page.
			 *
			 * @since 1.0.8
			 */
			if ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) !== 'loginpress-settings' ) {
				return;
			}
			$html = '<table id="loginpress_autologin_users" class="loginpress_autologin_users">
      <tr>
      <th class="loginpress_user_id">' . esc_html__( 'User ID', 'loginpress-auto-login' ) . '</th>
      <th class="loginpress_log_userName">' . esc_html__( 'Username', 'loginpress-auto-login' ) . '</th>
      <th class="loginpress_log_email">' . esc_html__( 'Email', 'loginpress-auto-login' ) . '</th>
      <th class="loginpress_log_url">' . esc_html__( 'Autologin URL', 'loginpress-auto-login' ) . '</th>
      <th class="loginpress_action">' . esc_html__( 'Action', 'loginpress-auto-login' ) . '</th>
      </tr>';

			$user_query = new WP_User_Query(
				array(
					'meta_key' => 'loginpress_autologin_code',
				)
			);
			// get_results w.r.t 'meta_key' => 'loginpress_autologin_code'.
			$autologin_user = $user_query->get_results();
			// Check for results.
			if ( ! empty( $autologin_user ) ) {
				// loop through each user.
				foreach ( $autologin_user as $user ) {
					// get all the user's data.
					$user_info = get_userdata( $user->ID );
					$loginpress_user_autologin_nonce = wp_create_nonce( 'loginpress-user-autologin-nonce' );
					$html .= '<tr id="loginpress_user_id_' . $user->ID . '" data-autologin="' . $user->ID . '"><td>' . $user_info->ID . '</td><td class="loginpress_user_name">' . $user_info->user_login . '</td><td >' . $user_info->user_email . '<input type="hidden" class="loginpress__user-autologin_nonce" name="loginpress__user-autologin_nonce" value="' . $loginpress_user_autologin_nonce . '"></td><td class="loginpress_autologin_code"><span class="autologin-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span><p>' . home_url() . '/?loginpress_code=' . get_user_meta( $user->ID, 'loginpress_autologin_code', true ) . '</p></td><td><input type="button" class="button loginpress-new-link" value="' . esc_html__( 'New', 'loginpress-auto-login' ) . '" id="loginpress_create_new_link" /> <input type="button" class="button loginpress-del-link" value="' . esc_html__( 'Delete', 'loginpress-auto-login' ) . '" id="loginpress_delete_link" /></td></tr>';
				}
			} else {
						$html .= esc_html__( 'No user found', 'loginpress-auto-login' );
			}

			$html .= '</table>';

			echo $html;
		}


    /**
     * Initialize the plugin updater class.
     *
     * @return void
     */
    // public function init_plugin_updater() {

    //   // Require the updater class, if not already present.
    //   if ( class_exists( 'LOGINPRESS_PRO_SL_Plugin_Updater' ) )  {

    //     // Retrieve our license key from the DB.
    //     $license_key = LoginPress_Pro::get_registered_license_key();
    //     //var_dump($license_key);
    //     // Setup the updater.
    //     $edd_updater = new LOGINPRESS_PRO_SL_Plugin_Updater( LoginPress_Pro::LOGINPRESS_SITE_URL, LOGINPRESS_PRO_UPGRADE_PATH, array(
    //         'version'   => LOGINPRESS_AUTOLOGIN_VERSION,
    //         'license'   => $license_key,
    //         'item_id'   => '2324',
    //         'author'  => 'captian',
    //         'beta'    => false
    //       )
    //     );

    //     //var_dump($edd_updater);

    //   }
    // }

		/**
		 * [loginpress_autocomplete_js Get the users list and Saved it in footer that will use for autocomplete in search]
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 */
		function loginpress_autocomplete_js() {

			/**
			 * Check to apply the script only on the LoginPress Settings page.
			 *
			 * @since 1.0.8
			 */
			$current_screen = get_current_screen();
			if ( isset( $current_screen->base ) && ( 'toplevel_page_loginpress-settings' !== $current_screen->base ) ) {
				return;
			}

			$users = get_users();

			if ( $users ) :
				foreach ( $users as $k => $user ) {
					$source[ $k ]['ID'] = $user->data->ID;
					$source[ $k ]['label'] = $user->data->user_login;
					$source[ $k ]['user_email'] = $user->data->user_email;
				}
				 ?>
			  <script type="text/javascript">
			  jQuery(document).ready(function($){

				// Generate random string.
				function loginpress_create_new_link() {
				  var autoLoginString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

				  var result = "";
				  while ( result.length < 30 ) {
					result += autoLoginString.charAt( Math.floor( Math.random() * autoLoginString.length ) );
				  }

				  return result;
				}

				var posts = <?php echo json_encode( array_values( $source ) ); ?>;

				if ( jQuery( 'input[name="loginpress_autologin_search"]' ).length > 0 ) {
	      	var _nonce = $('.loginpress__search-autologin_nonce').val();
				  jQuery( 'input[name="loginpress_autologin_search"]' ).autocomplete({
					source: posts,
					minLength: 1,
					select: function(event, ui) {
					  // console.log(ui.item.label);
					  var id = ui.item.ID;
					  var code = loginpress_create_new_link();
					  if ( $( '#loginpress_user_id_' + id ).length == 0 ) {
  						$.ajax({
    						url: ajaxurl,
    						type: 'POST',
    						data: 'code=' + code + '&id=' + id + '&action=loginpress_autologin' + '&security=' + _nonce,
    						success: function( response ) {
      						var get_html = '<tr id="loginpress_user_id_'+id+'" data-autologin="'+id+'"><td>'+id+'</td><td class="loginpress_user_name">'+ui.item.label+'</td><td>'+ui.item.user_email+'<input type="hidden" class="loginpress__user-autologin_nonce" name="loginpress__user-autologin_nonce" value="<?php echo wp_create_nonce( 'loginpress-user-autologin-nonce' ); ?>"></td><td class="loginpress_autologin_code"><span class="autologin-sniper"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" /></span><p>' + response + '</p></td><td><input type="button" class="button loginpress-new-link" value="<?php esc_html_e( 'New', 'loginpress-auto-login' ); ?>" id="loginpress_create_new_link" /> <input type="button" class="button loginpress-del-link" value="<?php esc_html_e( 'Delete', 'loginpress-auto-login' ); ?>" id="loginpress_delete_link" /></td></tr>';

      						// get_html.appendTo('#autologin_users');
      						if ( $('#loginpress_user_id_' + id + '').length == 0 ) {
      						   $('#loginpress_autologin_users').append( get_html );
      						}
    						}  // !success.
  						}); // !ajax.
					  } else {
  						$( '#loginpress_user_id_' + id ).addClass('loginpress_user_highlighted');
  						setTimeout(function(){
  						$( '#loginpress_user_id_' + id ).removeClass('loginpress_user_highlighted');
  						},3000 );
					  }
					} // !select.
				  });
				}
			  });
			  </script>
				<?php
		  endif;
		}

		/**
		 * [Ajax function that update the user meta after creating autologin code]
		 *
		 * @since   1.0.0
		 * @version 1.0.4
		 * @return [string] [url with autologin code]
		 */
		public function autologin_update_user_meta() {

			check_ajax_referer( 'loginpress-user-autologin-nonce' , 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$loginpress_code = esc_html( $_POST['code'] );
			$user_id = esc_html( $_POST['id'] );

			update_user_meta( $user_id, 'loginpress_autologin_code', $loginpress_code );

			echo home_url() . '/?loginpress_code=' . get_user_meta( $user_id, 'loginpress_autologin_code', true );
			wp_die();
		}

		/**
		 * [Ajax function that delete the user meta after click on delete user autologin button]
		 *
		 * @since   1.0.0
		 * @version 1.0.2
		 * @return [type] [description]
		 */
		public function autologin_delete_user_meta() {

			check_ajax_referer( 'loginpress-user-autologin-nonce' , 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$user_id = esc_html( $_POST['id'] );

			delete_user_meta( $user_id, 'loginpress_autologin_code' );
			echo 'deleted';
		}
		/**
		 * Define constant if not already set
		 *
		 * @param  string      $name
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
* Check if LoginPress Pro is install and active.
*
* @since 1.0.0
*/
function lp_al_instance() {

  if ( ! file_exists( WP_PLUGIN_DIR . '/loginpress-pro/loginpress-pro.php' ) ) {
    add_action( 'admin_notices' , 'lp_al_install_pro' );
    return;
  }

  if ( ! class_exists( 'LoginPress_Pro' ) ) {
    add_action( 'admin_notices', 'lp_al_activate_pro' );
    return;
	}


	// if ( defined( 'LOGINPRESS_PRO_VERSION' ) ) {
	// 	$addons = get_option( 'loginpress_pro_addons' );
	//
	// 	if ( LOGINPRESS_PRO_VERSION < '3.0' ) {
	// 		// If PRO version is still old
	// 		add_action( 'admin_notices' , 'lp_auto_login_depricated' );
	// 	} else if ( ( LOGINPRESS_PRO_VERSION >= '3.0.0' ) && ( ! empty( $addons ) ) && ( $addons['auto-login']['is_active'] ) ) {
	// 		// If PRO addon and the same plugin both active
	// 		add_action( 'admin_notices' , 'lp_auto_login_depricated_remove' );
	// 		return;
	// 	}
	// }

  // Call the function
	new LoginPress_AutoLogin();
}

add_action( 'plugins_loaded', 'lp_al_instance', 25 );


/**
* Notice if LoginPress Pro is not install.
*
* @since 1.0.0
*/
function lp_al_install_pro() {
  $class = 'notice notice-error is-dismissible';
  $message = __( 'Please Install LoginPress Pro to use "LoginPress Auto Login" add-on.', 'loginpress-auto-login' );

  printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

/**
* Notice if LoginPress Pro is not activate.
*
* @since 1.0.0
*/
function lp_al_activate_pro() {

  $action = 'activate';
  $slug   = 'loginpress-pro/loginpress-pro.php';
  $link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

  printf('<div class="notice notice-error is-dismissible">
  <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Auto Login required LoginPress Pro activation &mdash; ', 'loginpress-auto-login' ), $link, esc_html__( 'Click here to activate LoginPress Pro', 'loginpress-auto-login' ) );
}

// /**
// * Notice plugin is depricated and upgrade PRO.
// *
// * @since 1.0.6
// */
// function lp_auto_login_depricated() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Auto Login Plugin is depricated, please upgrade to LoginPress Pro 3.0 &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }

// /**
// * Notice plugin is depricated and remove.
// *
// * @since 1.0.6
// */
// function lp_auto_login_depricated_remove() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Auto Login Plugin is depricated, you can remove it. &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }
