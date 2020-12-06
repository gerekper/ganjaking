<?php

if ( ! class_exists( 'LoginPress_HideLogin_Main' ) ) :

	class LoginPress_HideLogin_Main {

		/**
		* @since  1.0.0
		* @access public
		* @var    string variable
		*/
		public $version = LOGINPRESS_HIDE_VERSION;

		/**
		* @since  1.0.0
		* @access private
		* @var    bool
		*/
		private $wp_login_php;

		/**
		* Instance of this class.
		* @since    1.0.0
		* @var      object
		*/
		protected static $_instance = null;

		/* * * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		public function __construct() {

		$this->_hooks();
	}

	/**
	* Hook into actions and filters
	* @since  1.0.0
	*/
	private function _hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'loginpress_hidelogin_admin_action_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'textdomain' ), 30 );
		add_filter( 'loginpress_settings_tab', array( $this, 'loginpress_hidelogin_tab' ), 10, 1 );
		add_filter( 'loginpress_settings_fields', array( $this, 'loginpress_hidelogin_settings_array' ), 10, 1 );
		add_filter( 'loginpress_hidelogin', array( $this, 'loginpress_hidelogin_callback' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'init_addon_updater' ), 0 );

		$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
		$slug = isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "";

		if ( ! empty( $slug ) ) {
			add_action( 'plugins_loaded', array( $this, 'loginpress_hidelogin_loaded' ), 30 );
			add_action( 'wp_loaded', array( $this, 'loginpress_hidelogin_wp_loaded' ) );
			add_filter( 'site_url', array( $this, 'site_url' ), 10, 4 );
			add_filter( 'network_site_url', array( $this, 'network_site_url' ), 10, 3 );
			add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );
			add_action( 'wp_ajax_reset_login_slug', array( $this,'loginpress_hidelogin_reset_login_slug' ) );

			remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
		}
	}

		/**
		 * LoginPress Addon updater
		 *
		 */
		public function init_addon_updater() {
			if( class_exists( 'LoginPress_AddOn_Updater' ) ) {
			  //echo 'Exists';
			  $updater = new LoginPress_AddOn_Updater( 2162, LOGINPRESS_HIDE_ROOT_FILE, $this->version );
			}
		}

		/**
		 * @version 1.0.2
		 */
		function loginpress_hidelogin_reset_login_slug() {

			check_ajax_referer( 'loginpress-reset-login-nonce-nonce', 'security' );

		  if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
		  }
			// Handle request then generate response using WP_Ajax_Response
			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );

			$slug = isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "";
			$loginpress_hidelogin_new_val = array();
			foreach ( $loginpress_hidelogin as $key => $value ) {

				if ( 'rename_login_slug' == $key ) {
					$value = "";
				}
				// echo $key . '=>' . $value;
				$loginpress_hidelogin_new_val[$key] = $value;
			}

			update_site_option( "loginpress_hidelogin", $loginpress_hidelogin_new_val );
			wp_die();
		}

		/**
		* Load Languages
		* @since 1.0.0
		* @version 1.1.1
		*/
		public function textdomain() {

			load_plugin_textdomain( 'loginpress-hide-login', false, LOGINPRESS_HIDE_PLUGIN_ROOT . '/languages/' );
		}

		/**
		* [loginpress_hidelogin_tab Setting tab for HideLogin.]
		* @param  [array] $loginpress_tabs [ Tabs of free version ]
		* @return [array]                	[ HideLogin tab ]
		*/
		public function loginpress_hidelogin_tab( $loginpress_tabs ) {

			$_hidelogin_tab = array(
				array(
					'id'    => 'loginpress_hidelogin',
					'title' => __( 'Hide Login', 'loginpress-hide-login' ),
					'desc'  => $this->tab_desc(),
				),
			);
			$hidelogin_tab = array_merge( $loginpress_tabs, $_hidelogin_tab );

			return $hidelogin_tab;
		}

		/**
		* [loginpress_hidelogin_settings_array Setting Fields for HideLogin.]
		* @param  [array] $setting_array [ Settings fields of free version ]
		* @return [array]                [ HideLogin settings fields ]
		*/
		public function loginpress_hidelogin_settings_array( $setting_array ) {

			$_hidelogin_settings = array(
				array(
					'name'              => 'rename_login_slug',
					'label'             => __( 'Rename Login Slug', 'loginpress-hide-login' ),
					'default'           => __( 'mylogin', 'loginpress-hide-login' ),
					'desc'              => $this->rename_login_slug_desc(),
					'type'              => 'hidelogin',
					'sanitize_callback' => array( $this, 'sanitize_login_slug' ) 
				),
				array(
					'name'  						=> 'is_rename_send_email',
					'label' 						=> __( 'Send Email', 'loginpress-hide-login' ),
					'desc'  						=> $this->is_rename_send_email_desc(),
					'type'  						=> 'checkbox'
				),
				array(
					'name'              => 'rename_email_send_to',
					'label'             => __( 'Email Address', 'loginpress-hide-login' ),
					'default'						=> get_option( 'admin_email' ),
					'desc'              => 'Add email, Use comma ( , ) to add more than 1 recipients.',
					'type'              => 'email',
					'multiple'          => true,
					'sanitize_callback' => array( $this, 'sanitize_email' )
					// 'sanitize_callback' => 'sanitize_email'
				),
			);
			$hidelogin_settings = array( 'loginpress_hidelogin' => $_hidelogin_settings );
			return( array_merge( $hidelogin_settings, $setting_array ) );
		}

		/**
		 * Sanitize email address.
		 *
		 * @since 1.1.4
		 */
		function sanitize_email( $emails ) {

			$emails = explode( ',', $emails );

			foreach ( $emails as $email => $value ) {
				$emails[$email] = sanitize_email( $value );
			}

			$emails = implode( ',', $emails );

			return $emails;
		}

		/**
		 * Sanitize login url slug.
		 *
		 * Only alphanumaric characters and dashes are allowed
		 * string will transformed to lowercsae and spaces will
		 * remove.
		 *
		 * @return $slug
		 */
		function sanitize_login_slug( $slug ) {

			$slug = trim( $slug );
			$slug = preg_replace( '/[^A-Za-z0-9\.-]/', '', $slug );
			$slug = strtolower( $slug );

			if ( 'wp-admin' == $slug ) {
				$slug = '';
			}

			return $slug;
		}

		/**
		 * [_hidelogin_use_slashes check the trailings slashes]
		 * @return [bool] [true]
		 */
		private function _hidelogin_use_slashes() {

			return ( '/' === substr( get_option( 'permalink_structure' ), -1, 1 ) );
		}


		/**
		 * [_hidelogin_user_trailings description]
		 * @param  [string] $string [description]
		 * @return [string]
		 */
		private function _hidelogin_user_trailings( $string ) {

			return $this->_hidelogin_use_slashes()
			? trailingslashit( $string )
			: untrailingslashit( $string );
		}

		private function wp_template_loader() {
			global $pagenow;

			$pagenow = 'index.php';

			if ( ! defined( 'WP_USE_THEMES' ) ) {
				define( 'WP_USE_THEMES', true );
			}
			wp();
			if ( $_SERVER['REQUEST_URI'] === $this->_hidelogin_user_trailings( str_repeat( '-/', 10 ) ) ) {
				$_SERVER['REQUEST_URI'] = $this->_hidelogin_user_trailings( '/wp-login-php/' );
			}
			require_once( ABSPATH . WPINC . '/template-loader.php' );
			die;
		}

		/**
		 * [new_login_slug]
		 * @since 1.0.0
		 * @version 1.0.1
		 * @return [string] slug
		 */
		private function new_login_slug() {

			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$slug = isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "mylogin";
			
			return $slug;

		}

		/**
		 * [new_login_url description]
		 * @param  [string] $scheme
		 * @return [string] url
		 */
		public function new_login_url( $scheme = null ) {

			if ( get_option( 'permalink_structure' ) ) {

				return $this->_hidelogin_user_trailings( home_url( '/', $scheme ) . $this->new_login_slug() );
			} else {

				return home_url( '/', $scheme ) . '?' . $this->new_login_slug();
			}
		}

		/**
		* Main Instance
		*
		* @since 1.0.0
		* @static
		* @see loginPress_hidelogin_loader()
		* @return object Main instance of the Class
		*/
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
		/**
		* [tab_desc description of the tab 'loginpress settings']
		* @since 1.0.0
		* @return [string]
		*/
		public function tab_desc() {

			$html = '';

			if ( ! is_multisite() || is_super_admin() ) {

				$html .= __( 'Hide login lets you change the login page URL to anything you want. It will give a hard time to spammers who keep hitting to your login page. This is helpful for Brute force attacks. One caution to use this add-on is you need to remember the custom login url after you change it. We have an option to email your custom login url so you remember it.', 'loginpress-hide-login');

			} else if ( is_multisite() && is_super_admin() && is_plugin_active_for_network( LOGINPRESS_HIDE_PLUGIN_BASENAME ) ) {

				$html .= sprintf( __( 'To set a networkwide default, go to <a href="%s">Network Settings</a>.', 'loginpress-hide-login' ), network_admin_url( 'settings.php#whl-page-input' ) );
			}

			return $html;
		}

		/**
		* Displays a text field under the hidelogin tab.
		* @param array   $args settings field args.
		* @param array  $value settings field value.
		* @since 1.0.0
		* @version 1.0.2
		* @return string html
		*/
		public function loginpress_hidelogin_callback( $args, $value ) {

			$html                 = '';
			$size                 = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type                 = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder          = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$slug                 = isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "";
			$loginpress_reset_login_slug_nonce = wp_create_nonce('loginpress-reset-login-nonce-nonce');

			if ( get_option( 'permalink_structure' ) ) {

				$html .= trailingslashit( home_url() ) . sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );

			} else {

				$html .=  trailingslashit( home_url() ) . '?' .sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );

			}

			$html .= '<input type="button" class="button loginpress-hidelogin-slug" value="' . esc_html__( 'Generate Slug (Randomly)', 'loginpress-hide-login' ) . '" id="loginpress_create_new_hidelogin_slug" />';

			if ( "" !== $slug ) {

				$html .= '<input type="button" class="button button-primary" value="' . esc_html__( 'Reset Login Slug', 'loginpress-hide-login' ) . '" id="loginpress_reset_login_slug" />';
				$html .= '<input type="hidden" class="loginpress_reset_login_slug_nonce" name="loginpress_reset_login_slug_nonce" value="' . $loginpress_reset_login_slug_nonce . '">';

			}

			return $html;
		}

		/**
		 * [rename_login_slug_desc description of the field 'rename_login_slug']
		 * @since 	1.0.0
		 * @return [string]
		 */
		public function rename_login_slug_desc() {

			global $pagenow;
			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$slug 	= isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "";
			$check 	= isset( $loginpress_hidelogin['is_rename_send_email'] ) ? $loginpress_hidelogin['is_rename_send_email'] : "off";
			$html 	= '';
			
			if ( ! is_network_admin() && 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'loginpress-settings' && isset( $_GET['settings-updated'] ) && "" == $slug ) {

				$html .=  sprintf( __( 'Your default login page: %1$s. Bookmark this page!', 'loginpress-hide-login' ), '<strong><a href="'.home_url('/wp-login.php').'" target="_blank">'.home_url('/wp-login.php').'</a></strong>' );

			} else if( "" == $slug ) {

				$html .=  sprintf( __( 'Your default login page: %1$s. Bookmark this page!', 'loginpress-hide-login' ), '<strong><a href="'.home_url('/wp-login.php').'" target="_blank">'.home_url('/wp-login.php').'</a></strong>' );

			} else if ( ! is_network_admin() && 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'loginpress-settings' && isset( $_GET['settings-updated'] ) ) {

				$html .=  sprintf( __( 'Here is your login page now: %1$s. Bookmark this page!', 'loginpress-hide-login' ), '<strong><a href="'.$this->new_login_url().'" target="_blank">'.$this->new_login_url().'</a></strong>' );
				if ( 'on' == $check ) {
					$this->loginpress_send_notify_email();
				}

			} else {

				$html .= __( 'Rename your wp-login.php', 'loginpress-hide-login' );

			}

			return $html;
		}

		/**
		 * [rename_email_send_to_default return email]
		 * @return [string] [email]
		 * @since 1.0.0
		 */
		public function rename_email_send_to_default(){
			return get_option( 'admin_email' );
		}

		/**
		 * [loginpress_send_notify_email send email]
		 * @since 1.0.0
		 * @version 1.1.5
		 */
		public function loginpress_send_notify_email() {

			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$slug 		= isset( $loginpress_hidelogin['rename_login_slug'] ) ? $loginpress_hidelogin['rename_login_slug'] : "";
			$email 		= isset( $loginpress_hidelogin['rename_email_send_to'] ) ? $loginpress_hidelogin['rename_email_send_to'] : "";
			$headers 	= array( 'Content-Type: text/html; charset=UTF-8' );

			if ( ! empty( $slug ) && ! empty( $email )  ) {

				$home_url = home_url("/");
				$message = '';
				$message .= 'Email Notification from ' . $home_url . "<br />";
				$message .= 'Your New Login Slug is ' . $home_url . $slug . "<br />";
				$message .= 'Powered by LoginPress';

			/**
			* Use filter `loginpress_hide_login_email_notification` for return the hide login email notification.
			*
			* @param string $message default email notification string.
			* @param string $slug Newly created slug.
			* @since 1.1.5
			*/
				$email_body = apply_filters( 'loginpress_hide_login_email_notification', $message, $slug );
				// Escape JS
				$email_body = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', "", $email_body );

			/**
			 * Use filter `loginpress_hide_login_email_subject` for return the hide login email subject.
			 *
			 * @param string default email subject string.
			 * @since 1.1.5
			 */
				$subject    = apply_filters( 'loginpress_hide_login_email_subject', "Rename wp-login.php by LoginPress" );
			// Escape JS
				$subject    = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', "", $subject );

				wp_mail( trim( $email ), $subject, $email_body, $headers );
			}
		}

		/**
		 * [is_rename_send_email_desc description of the field 'is_rename_send_email']
		 * @since 	1.0.0
		 * @return [string]
		 */
		public function is_rename_send_email_desc() {

			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$is_send_email  			= isset( $loginpress_hidelogin['is_rename_send_email'] ) ? $loginpress_hidelogin['is_rename_send_email'] : "off";
			$email_send     			= isset( $loginpress_hidelogin['rename_email_send_to'] ) ? $loginpress_hidelogin['rename_email_send_to'] : "";

			$html = '';
			if ( 'off' !== $is_send_email && ! empty( $email_send ) ) {

				$html .=  esc_html__( 'Email will be sent to the address defined below.', 'loginpress-hide-login' );

			} else if ( 'off' !== $is_send_email && empty( $email_send ) ) {

				$html .=  esc_html__( 'Please write down the correct email for sending the email.', 'loginpress-hide-login' );

			}  else {

				$html .= esc_html__( 'Send email after changing the wp-login.php slug?', 'loginpress-hide-login' );

			}

			return $html;
		}

		/**
		 * [rename_email_send_to_desc description of the field 'rename_email_send_to']
		 * @since 	1.0.0
		 * @return [string]
		 */
		public function rename_email_send_to_desc() {
			
			$html 								= '';
			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$email_send 					= isset( $loginpress_hidelogin['rename_email_send_to'] ) ? $loginpress_hidelogin['rename_email_send_to'] : "";

			if ( '' !== $email_send ) {

				$html .=  esc_html__( 'Email sent.', 'loginpress-hide-login' );

			} else {

				$html .= esc_html__( 'Write a Email	Address where send the New generated URL', 'loginpress-hide-login' );

			}

			return $html;
		}

		/**
		 * [loginpress_hidelogin_loaded description]
		 * @return [type] [description]
		 * @since	1.0.0
		 */
		public function loginpress_hidelogin_loaded() {
		
			global $pagenow;

			if ( ! is_multisite() && ( strpos( $_SERVER['REQUEST_URI'], 'wp-signup' )  !== false || strpos( $_SERVER['REQUEST_URI'], 'wp-activate' ) )  !== false ) {

				wp_die( __( 'This feature is not enabled.', 'loginpress-hide-login' ) );

			}

			$request = wp_parse_url( $_SERVER['REQUEST_URI'] );

			if ( ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false || untrailingslashit( $request['path'] ) === site_url( 'wp-login', 'relative' ) ) && ! is_admin() ) {

				$this->wp_login_php = true;
				$_SERVER['REQUEST_URI'] = $this->_hidelogin_user_trailings( '/' . str_repeat( '-/', 10 ) );
				$pagenow = 'index.php';

			} elseif ( untrailingslashit( $request['path'] ) === home_url( $this->new_login_slug(), 'relative' ) || ( ! get_option( 'permalink_structure' ) && isset( $_GET[$this->new_login_slug()] ) && empty( $_GET[$this->new_login_slug()] ) ) ) {

				$pagenow = 'wp-login.php';

			}
		}

		/**
		 * [loginpress_hidelogin_wp_loaded miscellaneous]
		 * @since 1.0.0
		 * @version 1.2.3
		 */

		public function loginpress_hidelogin_wp_loaded() {

			global $pagenow;

			// limit wp-admin access.
			if ( is_admin() && ! is_user_logged_in() && ! defined( 'DOING_AJAX' ) && $pagenow !== 'admin-post.php' ) {
					// wp_die( __( 'Disabled Admin Access', 'loginpress-hide-login' ), 403 );
				// global $wp_query;
				// $wp_query->set_404();
				// status_header( 404 );
				// get_template_part( 404 );
				apply_filters( 'loginpress_hidelogin_wp_admin_redirect', wp_redirect( get_site_url() . '/404' ) );
				exit();
			}

			$request 							= wp_parse_url( $_SERVER['REQUEST_URI'] );
			$loginpress_hidelogin = get_option( 'loginpress_hidelogin' );
			$slug 								= isset( $loginpress_hidelogin['rename_login_slug'] ) ? '/' . $loginpress_hidelogin['rename_login_slug'] . '/' : "";
			$additional_slug 			= isset( $loginpress_hidelogin['rename_login_slug'] ) ? '/' . $loginpress_hidelogin['rename_login_slug'] : "";

			if ( $pagenow === 'wp-login.php' && $request['path'] !== $this->_hidelogin_user_trailings( $request['path'] ) && get_option( 'permalink_structure' ) ) {

				wp_safe_redirect( $this->_hidelogin_user_trailings( $this->new_login_url() )
				. ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );
				die;

			} elseif ( $this->wp_login_php ) {

				if ( ( $referer = wp_get_referer() ) && strpos( $referer, 'wp-activate.php' ) !== false && ( $referer = parse_url( $referer ) ) && ! empty( $referer['query'] ) ) {

					parse_str( $referer['query'], $referer );

					if ( ! empty( $referer['key'] ) && ( $result = wpmu_activate_signup( $referer['key'] ) ) && is_wp_error( $result ) && ( $result->get_error_code() === 'already_active' || $result->get_error_code() === 'blog_taken') ) {
						
						wp_safe_redirect( $this->new_login_url() . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );
					die;
					}
				}

				$this->wp_template_loader();

			} elseif ( ( $pagenow === 'index.php' && strpos( $_SERVER['REQUEST_URI'], $slug ) == true ) || 
			( $pagenow === 'index.php' && strpos( $_SERVER['REQUEST_URI'], $additional_slug ) == true ) || 
			( $pagenow === 'wp-login.php' ) ) {
				global $error, $interim_login, $action, $user_login;
				@require_once ABSPATH . 'wp-login.php';

				die;
			}
		}


		/**
		 * [site_url description]
		 * @param  $url, $path, $scheme, $blog_id
		 * @return url
		 * @since	 1.0.0
		 */
		public function site_url( $url, $path, $scheme, $blog_id ) {

			return $this->loginpress_filter_login_page( $url, $scheme );
		}

		/**
		 * [network_site_url description]
		 * @param  $url, $path, $scheme
		 * @return url
		 * @since	 1.0.0
		 */
		public function network_site_url( $url, $path, $scheme ) {

			return $this->loginpress_filter_login_page( $url, $scheme );
		}

		/**
		 * [wp_redirect description]
		 * @param  [string] $location [The path to redirect to.]
		 * @param  [string] $status   [Status code to use.]
		 * @return [string]           [url]
		 * @since	 1.0.0
		 */
		public function wp_redirect( $location, $status ) {

			return $this->loginpress_filter_login_page( $location );
		}

		/**
		 * [loginpress_filter_login_page description]
		 * @param  [string] $url
		 * @param  [string] $scheme
		 * @return [string]         [url]
		 * @since  1.0.0
		 */
		public function loginpress_filter_login_page( $url, $scheme = null ) {

			if ( strpos( $url, 'wp-login.php' ) !== false ) {

				if ( is_ssl() ) {
					$scheme = 'https';
				}
				
				$args = explode( '?', $url );
				if ( isset( $args[1] ) ) {
					parse_str( $args[1], $args );
					$url = add_query_arg( $args, $this->new_login_url( $scheme ) );
				} else {
					$url = $this->new_login_url( $scheme );
				}
			}
			return $url;
		}

		function loginpress_hidelogin_admin_action_scripts( $hook ) {

			if ( "toplevel_page_loginpress-settings" == $hook ) {

				wp_register_style( 'loginpress-admin-hidelogin', plugins_url( 'assets/css/style.css', dirname( __FILE__ ) ), array(), LOGINPRESS_HIDE_VERSION );
				wp_enqueue_style( 'loginpress-admin-hidelogin' );
			}

			wp_enqueue_script( 'loginpress-admin-hidelogin', plugins_url( 'assets/js/required-action.js', dirname( __FILE__ ) ), array( 'jquery' ), LOGINPRESS_HIDE_VERSION );

			wp_localize_script(
				'loginpress-admin-hidelogin', 'loginpress_hidelogin_local', array(
					'admin_url' => admin_url( 'admin.php?page=loginpress-settings' ),
				)
			);

		}

	} //End of Class.

endif;