<?php


if ( ! class_exists( 'LoginPress_Login_Redirect_Main' ) ) :

	class LoginPress_Login_Redirect_Main {

		/**
		* @since  1.0.0
		* @access public
		* @var    string variable
		*/
		public $version = LOGINPRESS_REDIRECT_VERSION;

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
			$this->_includes();
		}

		public function _hooks(){

			add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
			add_action( 'plugins_loaded',         array( $this, 'textdomain' ), 30 );
			add_filter( 'loginpress_settings_tab', array( $this, 'loginpress_login_redirects_tab' ), 10, 1 );
			add_filter( 'loginpress_settings_fields', array( $this, 'loginpress_login_redirects_settings_array' ), 10, 1 );
			add_filter( 'loginpress_login_redirects', array( $this, 'loginpress_login_redirects_callback' ), 10, 1 );

			add_action( 'admin_init',   array( $this, 'init_addon_updater' ), 0 );
			add_action( 'admin_footer', array( $this, 'loginpress_autocomplete_js' ) );
			add_action( 'wp_ajax_loginpress_login_redirects_update', array( $this, 'login_redirects_update_user_meta' ) );
			add_action( 'wp_ajax_loginpress_login_redirects_delete', array( $this, 'login_redireects_delete_user_meta' ) );
			add_action( 'wp_ajax_loginpress_login_redirects_role_update', array( $this, 'login_redirects_update_role' ) );
			add_action( 'wp_ajax_loginpress_login_redirects_role_delete', array( $this, 'login_redirects_delete_role' ) );
			add_action( 'loginpress_login_redirect_script',  array( $this, 'login_redirect_script_html' ) );
		}

		/**
	     * LoginPress Addon updater
	     *
	     */
	    public function init_addon_updater() {
        if( class_exists( 'LoginPress_AddOn_Updater' ) ) {
          //echo 'Exists';
          $updater = new LoginPress_AddOn_Updater( 2341, LOGINPRESS_REDIRECT_ROOT_FILE, $this->version );
        }
	    }

		/**
		 * [_includes include files]
		 *
		 * @since 1.0.0
		 */
		function _includes() {

			include_once( LOGINPRESS_REDIRECT_DIR_PATH . 'classes/class-redirects.php' );
		}
		/**
    * Load Languages
		* @since 1.0.0
		* @version  1.1.1
    */
    public function textdomain() {

			load_plugin_textdomain( 'loginpress-login-redirects', false, LOGINPRESS_REDIRECT_PLUGIN_ROOT . '/languages/' );
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

			wp_enqueue_style( 'loginpress_login_redirect_stlye', plugins_url( '../assets/css/style.css', __FILE__ ), array(), LOGINPRESS_REDIRECT_VERSION );

			wp_enqueue_script( 'loginpress_login_redirect_js', plugins_url( '../assets/js/autologin.js', __FILE__ ), array( 'jquery' ), LOGINPRESS_REDIRECT_VERSION );

		}

		/**
		* [loginpress_login_redirects_tab Setting tab for Login Redirects.]
		* @param  [array] $loginpress_tabs [ Tabs of free version ]
		* @return [array]                	[ Login Redirects tab ]
		*/
		public function loginpress_login_redirects_tab( $loginpress_tabs ) {
			$_login_redirects_tab = array(
				array(
					'id'    => 'loginpress_login_redirects',
					'title' => __( 'Login Redirects', 'loginpress-login-redirects' ),
					'desc'  => sprintf( __( '%1$s%3$sSpecific User%4$s %5$sSpecific Roles%4$s%2$s ', 'loginpress-login-redirects' ), '<div class="loginpress-redirects-tab-wrapper">', '</div>', '<a href="#loginpress_login_redirect_users" class="loginpress-redirects-tab loginpress-redirects-active">', '</a>', '<a href="#loginpress_login_redirect_roles" class="loginpress-redirects-tab">' )
				),
			);
			$login_redirects_tab = array_merge( $loginpress_tabs, $_login_redirects_tab );
			return $login_redirects_tab;
		}

		/**
		* [loginpress_login_redirects_settings_array Setting Fields for Login Redirects.]
		* @param  [array] $setting_array [ Settings fields of free version ]
		* @return [array]                [ Login Redirects settings fields ]
		*/
		public function loginpress_login_redirects_settings_array( $setting_array ) {

			$_login_redirects_settings = array(
				array(
					'name'              => 'login_redirects',
					'label'             => __( 'Search Username', 'loginpress-login-redirects' ),
					'desc'              => __( 'Search Username for apply redirects on that.', 'loginpress-login-redirects' ),
					'type'              => 'login_redirect',
				),
			);
			$login_redirects_settings = array( 'loginpress_login_redirects' => $_login_redirects_settings );
			return( array_merge( $login_redirects_settings, $setting_array ) );
		}

		/**
		 * A callback function that will show a search field under Login Redirect tab.
		 *
		 * @since   1.0.0
		 * @return [string] html
		 */
		function loginpress_login_redirects_callback( $args ) {

			$html = '<input type="text" name="loginpress_redirect_user_search" id="loginpress_redirect_user_search" value="" placeholder="Search by typing Username..." />';
			$html .= '<input type="text" name="loginpress_redirect_role_search" id="loginpress_redirect_role_search" value="" placeholder="Search by typing Role..." />';

			return $html;
		}

		/**
		 * A callback function that will show search result under the search field.
		 *
		 * @since   1.0.0
		 * @version 1.1.5
		 * @return [string] html
		 */
		function login_redirect_script_html() {

			/**
			 * Check to apply the script only on the LoginPress Settings page.
			 *
			 * @since 1.1.5
			 */
			if ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) !== 'loginpress-settings' ) {
				return;
			}
			$html = '<table id="loginpress_login_redirect_users" class="loginpress_login_redirect_users">
      <tr>
      <th class="loginpress_user_id">' . esc_html__( 'User ID', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_log_userName">' . esc_html__( 'Username', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_log_email">' . esc_html__( 'Email', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_login_redirect">' . esc_html__( 'Login URL', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_logout_redirect">' . esc_html__( 'Logout URL', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_action">' . esc_html__( 'Action', 'loginpress-login-redirects' ) . '</th>
      </tr>';


			$args = array(
				'blog_id'				 => $GLOBALS['blog_id'],
				'meta_query'     => array(
					'relation' 	 => 'OR',
					array( 'key' => 'loginpress_login_redirects_url' ),
					array( 'key' => 'loginpress_logout_redirects_url' )
					)
			 );

			$user_query = new WP_User_Query( $args );
			// get_results w.r.t 'meta_key' => 'loginpress_login_redirects_url' || 'loginpress_logout_redirects_url'.
			$autologin_user = $user_query->get_results();
			// Check for results.
			if ( ! empty( $autologin_user ) ) {
				// loop through each user.
				foreach ( $autologin_user as $user ) {
					// get all the user's data.
					$user_info                       = get_userdata( $user->ID );
					$loginpress_user_redirects_nonce = wp_create_nonce( 'loginpress-user-redirects-nonce' );
					$html .= '<tr id="loginpress_redirects_user_id_' . $user->ID . '" data-login-redirects-user="' . $user->ID . '"><td>' . $user_info->ID . '</td><td class="loginpress_user_name">' . $user_info->user_login . '</td><td >' . $user_info->user_email . '<input type="hidden" class="loginpress__user-redirects_nonce" name="loginpress__user-redirects_nonce" value="' . $loginpress_user_redirects_nonce . '"></td><td class="loginpress_login_redirects_url"><span class="autologin-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span><input type="text" value="' . get_user_meta( $user->ID, 'loginpress_login_redirects_url', true ) . '" id="loginpress_login_redirects_url"/></td><td class="loginpress_logout_redirects_url"><span class="autologin-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span><input type="text" value="' . get_user_meta( $user->ID, 'loginpress_logout_redirects_url', true ) . '" id="loginpress_logout_redirects_url"/></td><td><input type="button" class="button loginpress-user-redirects-update" value="' . esc_html__( 'Update', 'loginpress-login-redirects' ) . '" /> <input type="button" class="button loginpress-user-redirects-delete" value="' . esc_html__( 'Delete', 'loginpress-login-redirects' ) . '" /></td></tr>';
				}
			} else {
						$html .= '';
			}

			$html .= '</table>';

			$html .= '<table id="loginpress_login_redirect_roles" class="loginpress_login_redirect_roles">
      <tr>
      <th class="loginpress_user_id">' . esc_html__( 'No', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_log_userName">' . esc_html__( 'Role', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_login_redirect">' . esc_html__( 'Login URL', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_logout_redirect">' . esc_html__( 'Logout URL', 'loginpress-login-redirects' ) . '</th>
      <th class="loginpress_action">' . esc_html__( 'Action', 'loginpress-login-redirects' ) . '</th>
      </tr>';

			$login_redirect_role = get_option( 'loginpress_redirects_role' );

			// Check for results.
			if ( ! empty( $login_redirect_role ) ) {
				// loop through each user.
				foreach ( $login_redirect_role as $role => $value ) {


					$loginpress_role_redirects_nonce = wp_create_nonce( 'loginpress-role-redirects-nonce' );

					$html .= '<tr id="loginpress_redirects_role_' . $role . '" data-login-redirects-role="' . $role . '"><td>' . $role . '</td><td class="loginpress_user_name">' . $role . '<input type="hidden" class="loginpress__role-redirects_nonce" name="loginpress__role-redirects_nonce" value="' . $loginpress_role_redirects_nonce . '"></td><td class="loginpress_login_redirects_url"><span class="autologin-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span><input type="text" value="' . $value['login'] . '" id="loginpress_login_redirects_url"/></td><td class="loginpress_logout_redirects_url"><span class="autologin-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span><input type="text" value="' . $value['logout'] . '" id="loginpress_logout_redirects_url"/></td><td><input type="button" class="button loginpress-redirects-role-update" value="' . esc_html__( 'Update', 'loginpress-login-redirects' ) . '" /> <input type="button" class="button loginpress-redirects-role-delete" value="' . esc_html__( 'Delete', 'loginpress-login-redirects' ) . '" /></td></tr>';
				}
			} else {
					$html .= '';
			}

			$html .= '</table>';

			echo $html;
		}

		/**
		 * [loginpress_autocomplete_js Get the users list and Saved it in footer that will use for autocomplete in search]
		 *
		 * @since 1.0.0
		 * @version 1.1.5
		 */
		function loginpress_autocomplete_js() {

			/**
			 * Check to apply the script only on the LoginPress Settings page.
			 *
			 * @since 1.1.5
			 */
			$current_screen = get_current_screen();
			if ( isset( $current_screen->base ) && ( 'toplevel_page_loginpress-settings' !== $current_screen->base ) ) {
				return;
			}

			$users = get_users();

			if ( $users ) :
				foreach ( $users as $k => $user ) :
					$source[ $k ]['ID'] = $user->data->ID;
					$source[ $k ]['label'] = $user->data->user_login;
					$source[ $k ]['user_email'] = $user->data->user_email;
			  endforeach; ?>
			  <script type="text/javascript">
			  jQuery(document).ready(function($){

				var posts = <?php echo json_encode( array_values( $source ) ); ?>;

				if ( jQuery( 'input[name="loginpress_redirect_user_search"]' ).length > 0 ) {
				  jQuery( 'input[name="loginpress_redirect_user_search"]' ).autocomplete( {
					source: posts,
					minLength: 1,
					select: function( event, ui ) {

					  var id    = ui.item.ID;
						var name  = ui.item.label;
						var email = ui.item.user_email;
					  if ( $( '#loginpress_redirects_user_id_' + id ).length == 0 ) {
							var get_html = '<tr id="loginpress_redirects_user_id_' + id + '" data-login-redirects-user="' + id + '"><td>' + id + '</td><td class="loginpress_user_name">' + name + '</td><td >' + email + '<input type="hidden" class="loginpress__user-redirects_nonce" name="loginpress__user-redirects_nonce" value="<?php echo wp_create_nonce( 'loginpress-user-redirects-nonce' ); ?>"></td><td class="loginpress_login_redirects_url"><span class="autologin-sniper"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ) ?>" /></span><input type="text" value="" id="loginpress_login_redirects_url"/></td><td class="loginpress_logout_redirects_url"><span class="autologin-sniper"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ) ?>" /></span><input type="text" value="" id="loginpress_logout_redirects_url"/></td><td><input type="button" class="button loginpress-user-redirects-update" value="<?php esc_html_e( 'Update', 'loginpress-login-redirects' ); ?>" /> <input type="button" class="button loginpress-user-redirects-delete" value="<?php esc_html_e( 'Delete', 'loginpress-login-redirects' ); ?>" /></td></tr>';

  						// get_html.appendTo('#autologin_users');
  						if ( $( '#loginpress_redirects_user_id_' + id ).length == 0 ) {
  						   $('#loginpress_login_redirect_users').append( get_html );
  						}

					  } else {
  						$( '#loginpress_redirects_user_id_'+id ).addClass('loginpress_user_highlighted');
  						setTimeout(function(){
  						$( '#loginpress_redirects_user_id_'+id ).removeClass('loginpress_user_highlighted');
  						}, 3000 );
					  }
					} // !select.
				  });
				}
			  });
			  </script>
				<?php
		  endif;

			global $wp_roles;

	    $allroles = $wp_roles->roles;
			foreach ( $allroles as $k => $value ) {

				$role[ $k ]['role']  = esc_attr( $k );
				$role[ $k ]['label'] = translate_user_role( $value['name'] ); // returns localized name. v1.1.2
			} ?>
			<script type="text/javascript">
			jQuery(document).ready( function($) {

			var posts = <?php echo json_encode( array_values( $role ) ); ?>;

			if ( jQuery( 'input[name="loginpress_redirect_role_search"]' ).length > 0 ) {
				jQuery( 'input[name="loginpress_redirect_role_search"]' ).autocomplete( {

				source: posts,
				minLength: 1,
				select: function( event, ui ) {

					var name = ui.item.label;
					var role = ui.item.role;
					if ( $( '#loginpress_redirects_role_' + role ).length == 0 ) {
						var get_html = '<tr id="loginpress_redirects_role_' + role + '" data-login-redirects-role="' + role + '"><td>' + role + '</td><td class="loginpress_user_name">' + name + '<input type="hidden" class="loginpress__role-redirects_nonce" name="loginpress__role-redirects_nonce" value="<?php echo wp_create_nonce( 'loginpress-role-redirects-nonce' ); ?>"></td><td class="loginpress_login_redirects_url"><span class="autologin-sniper"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ) ?>" /></span><input type="text" value="" id="loginpress_login_redirects_url"/></td><td class="loginpress_logout_redirects_url"><span class="autologin-sniper"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ) ?>" /></span><input type="text" value="" id="loginpress_logout_redirects_url"/></td><td><input type="button" class="button loginpress-redirects-role-update" value="<?php esc_html_e( 'Update', 'loginpress-login-redirects' ); ?>" /> <input type="button" class="button loginpress-redirects-role-delete" value="<?php esc_html_e( 'Delete', 'loginpress-login-redirects' ); ?>" /></td></tr>';

						// get_html.appendTo('#autologin_users');
						if ( $('#loginpress_redirects_role_' + role ).length == 0 ) {
							 $('#loginpress_login_redirect_roles').append( get_html );
						}

					} else {
						$( '#loginpress_redirects_role_' + role ).addClass( 'loginpress_user_highlighted' );
						setTimeout(function(){
						$( '#loginpress_redirects_role_' + role ).removeClass( 'loginpress_user_highlighted' );
						}, 3000 );
					}
				} // !select.
				});
			}
			});
			</script>
			<?php
		}


		/**
		 * [Ajax function that update the user meta after creating autologin code]
		 *
		 * @since   1.0.0
		 * @return [string] [url with autologin code]
		 */
		public function login_redirects_update_user_meta() {

			check_ajax_referer( 'loginpress-user-redirects-nonce' , 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$user_id 						= esc_html( $_POST['id'] );
			$loginpress_logout 	= esc_url( $_POST['logout'] );
			$loginpress_login 	= esc_url( $_POST['login'] );

			$this->loginpress_update_redirect_url( $user_id, 'loginpress_login_redirects_url', $loginpress_login );
			$this->loginpress_update_redirect_url( $user_id, 'loginpress_logout_redirects_url', $loginpress_logout );

			echo $this->loginpress_get_redirect_url( $user_id, 'loginpress_login_redirects_url' );
			echo $this->loginpress_get_redirect_url( $user_id, 'loginpress_logout_redirects_url' );
			wp_die();
		}

		public function login_redirects_update_role() {

			check_ajax_referer( 'loginpress-role-redirects-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$loginpress_logout 	= esc_url( $_POST['logout'] );
			$loginpress_login 	= esc_url( $_POST['login'] );
			$role 							= esc_html( $_POST['role'] );
			$check_role 				= get_option( 'loginpress_redirects_role' );
			$add_role 					= array( $role => array( 'login' => $loginpress_login, 'logout' => $loginpress_logout ) );

			if ( $check_role && ! in_array( $role, $check_role ) ) {
				$redirect_roles = array_merge( $check_role, $add_role );
			} else {
				$redirect_roles = $add_role;
			}

			update_option( "loginpress_redirects_role", $redirect_roles, true );
			wp_die();
		}

		/**
		 * [Ajax function that delete the user meta after click on delete user autologin button]
		 *
		 * @since   1.0.0
		 * @return [type] [description]
		 */
		public function login_redireects_delete_user_meta() {

			check_ajax_referer( 'loginpress-user-redirects-nonce' , 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$user_id = esc_html( $_POST['id'] );

			$this->loginpress_delete_redirect_url( $user_id, 'loginpress_login_redirects_url' );
			$this->loginpress_delete_redirect_url( $user_id, 'loginpress_logout_redirects_url' );
			echo 'deleted';
			wp_die();
		}

		public function login_redirects_delete_role() {

			check_ajax_referer( 'loginpress-role-redirects-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'No cheating, huh!' );
      }

			$role 			= esc_html( $_POST['role'] );
			$check_role = get_option( 'loginpress_redirects_role' );

			if ( isset( $check_role[ $role ] ) ) {

				$check_role[$role] = null;

				$check_role = array_filter( $check_role );

				update_option( "loginpress_redirects_role", $check_role, true );
			}
			wp_die();
		}

		/**
		 * Get user meta.
		 * @param  int $user_id [ID of the use]
		 * @param string $option [user meta key]
		 * @return string $redirect_url [URL]
		 * @since 1.0.1
		 */
		public function loginpress_get_redirect_url( $user_id, $option ) {

			if ( ! is_multisite() ) {
				$redirect_url = get_user_meta( $user_id, $option, true );
			} else {
				$redirect_url = get_user_option( $option, $user_id );
			}

			return $redirect_url;
		}

		/**
		 * Update user meta.
		 * @param  int $user_id [ID of the use]
		 * @param string $option [user meta key]
		 * @param string $value [user meta value]
		 * @return string          URL
		 * @since 1.0.1
		 */
		public function loginpress_update_redirect_url( $user_id, $option, $value ) {

			if ( ! is_multisite() ) {
				update_user_meta( $user_id, $option, $value );
			} else {
				update_user_option( $user_id, $option, $value, true );
			}
		}

		/**
		 * Delete user meta.
		 * @param  int $user_id [ID of the use]
		 * @param string $option [user meta key]
		 * @return string          URL
		 * @since 1.0.1
		 */
		public function loginpress_delete_redirect_url( $user_id, $option ) {

			if ( ! is_multisite() ) {
				delete_user_meta( $user_id, $option );
			} else {
				delete_user_option( $user_id, $option, true );
			}
		}

		/**
		* Main Instance
		*
		* @since 1.0.0
		* @static
		* @see loginPress_redirect_login_loader()
		* @return object Main instance of the Class
		*/
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
endif;
