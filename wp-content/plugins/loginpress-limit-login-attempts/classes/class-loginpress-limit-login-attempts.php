<?php

if ( ! class_exists( 'LoginPress_Limit_Login_Attempts_Main' ) ) :

	/**
	 * Main Class.
	 */
	class LoginPress_Limit_Login_Attempts_Main {

		/** * * * * * * * * *
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string variable
		 * * * * * * * * * * */
		public $version = LOGINPRESS_LIMIT_LOGIN_VERSION;

		/** * * * * * * * * *
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    bool
		 * * * * * * * * * * */
		private $wp_login_php;

		/** * * * * * * * * * * * *
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 * @var      object
		 * * * * * * * * * * * * * */
		protected static $_instance = null;

		/** * * * * * * * *
		 * Class constructor
		 * * * * * * * * * */
		public function __construct() {

			$this->_hooks();
			$this->_includes();
		}

		/** * * * * * *
		 * Action hooks.
		 * * * * * * * */
		public function _hooks() {

			add_action( 'admin_init', array( $this, 'init_addon_updater' ), 0 );
			add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
			add_action( 'plugins_loaded', array( $this, 'textdomain' ), 30 );
			add_filter( 'loginpress_settings_tab', array( $this, 'loginpress_limit_login_attempts_tab' ), 10, 1 );
			add_filter( 'loginpress_settings_fields', array( $this, 'loginpress_limit_login_attempts_settings_array' ), 10, 1 );
			add_action( 'loginpress_limit_login_attempts_log_script', array( $this, 'loginpress_limit_login_attempts_log_callback' ) );
			add_action( 'loginpress_limit_login_attempts_whitelist_script', array( $this, 'loginpress_limit_login_attempts_whitelist_callback' ) );
			add_action( 'loginpress_limit_login_attempts_blacklist_script', array( $this, 'loginpress_limit_login_attempts_blacklist_callback' ) );
		}

		/** * * * * * * * * * * * *
		 * LoginPress Addon updater
		 * * * * * * * * * * * * * */
		public function init_addon_updater() {
			if ( class_exists( 'LoginPress_AddOn_Updater' ) ) {
				// echo 'Exists';
				$updater = new LoginPress_AddOn_Updater( 2328, LOGINPRESS_LIMIT_LOGIN_ROOT_FILE, $this->version );
			}
		}

		/** * * * * * *
		 * include files
		 *
		 * @since 1.0.0
		 * * * * * * * */
		function _includes() {

			include_once LOGINPRESS_LIMIT_LOGIN_DIR_PATH . 'classes/class-attempts.php';
			include_once LOGINPRESS_LIMIT_LOGIN_DIR_PATH . 'classes/class-ajax.php';
		}

		/** * * * * * * *
		 * Load Languages
		 *
		 * @since 1.0.0
		 * @version 1.1.1
		 * * * * * * * * */
		public function textdomain() {

			load_plugin_textdomain( 'loginpress-limit-login-attempts', false, LOGINPRESS_LIMIT_LOGIN_PLUGIN_ROOT . '/languages/' );

		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 * Load CSS and JS files at admin side on loginpress-settings page only.
		 *
		 * @param  string the Page ID
		 * @return void
		 * @since  1.0.0
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
		function _admin_scripts( $hook ) {

			if ( $hook != 'toplevel_page_loginpress-settings' ) {
				return;
			}

			// wp_enqueue_script( 'jquery-ui-core' );
			// wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_enqueue_style( 'loginpress_limit_login_stlye', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/css/style.css', array(), LOGINPRESS_LIMIT_LOGIN_VERSION );

			wp_enqueue_style( 'loginpress_limit_datatables_stlye', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/css/jquery.dataTables.min.css', array(), LOGINPRESS_LIMIT_LOGIN_VERSION );

			wp_enqueue_script( 'loginpress_limit_datatables_js', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), LOGINPRESS_LIMIT_LOGIN_VERSION );

			wp_enqueue_script( 'loginpress_limit_main_js', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/js/main.js', array( 'loginpress_limit_datatables_js' ), LOGINPRESS_LIMIT_LOGIN_VERSION );

			// wp_enqueue_style( 'loginpress_limit_datatables_checkbox_stlye', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/css/dataTables.checkboxes.css', array(), LOGINPRESS_LIMIT_LOGIN_VERSION );
			// wp_enqueue_script( 'loginpress_limit_datatables_checkbox_script', LOGINPRESS_LIMIT_LOGIN_DIR_URL . 'assets/js/dataTables.checkboxes.min.js', array( 'jquery', 'loginpress_limit_datatables_js' ), LOGINPRESS_LIMIT_LOGIN_VERSION );
		}


		/** * * * * * * * * * * * * * * * * * *
		 * Setting tab for Limit Login Attempts.
		 *
		 * @param  [array] $loginpress_tabs [ Tabs of free version ]
		 * @return [array]                   [ Limit Login Attempts tab ]
		 * @since 1.0.0
		 * * * * * * * * * * * * * * * * * * * */
		public function loginpress_limit_login_attempts_tab( $loginpress_tabs ) {
			$_limit_login_tab = array(
				array(
					'id'    => 'loginpress_limit_login_attempts',
					'title' => __( 'Limit Login Attempts', 'loginpress-limit-login-attempts' ),
					'desc'  => sprintf( __( '%1$s%3$sSettings%4$s %5$sAttempt Details%4$s %6$sWhitelist%4$s %7$sBlacklist%4$s%2$s', 'loginpress-limit-login-attempts' ), '<div class="loginpress-limit-login-tab-wrapper">', '</div>', '<a href="#loginpress_limit_login_settings" class="loginpress-limit-login-tab loginpress-limit-login-active">', '</a>', '<a href="#loginpress_limit_login_log" class="loginpress-limit-login-tab">', '<a href="#loginpress_limit_login_whitelist" class="loginpress-limit-login-tab">', '<a href="#loginpress_limit_login_blacklist" class="loginpress-limit-login-tab">' ),
				),
			);
			$limit_login_tab  = array_merge( $loginpress_tabs, $_limit_login_tab );
			return $limit_login_tab;
		}

		/** * * * * * * * * * * * * * * * * * * * *
		 * Setting Fields for Limit Login Attempts.
		 *
		 * @param  [array] $setting_array [ Settings fields of free version ]
		 * @return [array]                [ Limit Login Attempts settings fields ]
		 * @since 1.0.0
		 * @version 1.0.1
		 * * * * * * * * * * * * * * * * * * * * * */
		public function loginpress_limit_login_attempts_settings_array( $setting_array ) {

			$_limit_login_settings = array(
				array(
					'name'    => 'attempts_allowed',
					'label'   => __( 'Attempts Allowed', 'loginpress-limit-login-attempts' ),
					'desc'    => __( 'How many attempts allows', 'loginpress-limit-login-attempts' ),
					'type'    => 'number',
					'min'     => 1,
					'default' => '4',
				),
				array(
					'name'    => 'minutes_lockout',
					'label'   => __( 'Minutes Lockout', 'loginpress-limit-login-attempts' ),
					'desc'    => __( 'How many minutes lockout.', 'loginpress-limit-login-attempts' ),
					'type'    => 'number',
					'min'     => 1,
					'default' => '20',
				),
				array(
					'name'     => 'ip_add_remove',
					'label'    => __( 'IP Address', 'loginpress-limit-login-attempts' ),
					'type'     => 'text',
					'callback' => [ $this, 'loginpress_ip_add_remove_callback' ],
				),
				array(
					'name'  => 'disable_xml_rpc_request',
					'label' => __( 'Disable XML RPC Request', 'loginpress-limit-login-attempts' ),
					'desc'  => __( 'The XMLRPC is a system that allows remote updates to WordPress from other applications.', 'loginpress-limit-login-attempts' ),
					'type'  => 'checkbox',
				),
				// array( // Future setting.
				// 'name'  => 'disable_xml_ping_back',
				// 'label' => __( 'Disable Ping Back', 'loginpress-limit-login-attempts' ),
				// 'desc'  => __( 'Disable xml rpc ping back request', 'loginpress-limit-login-attempts' ),
				// 'type'  => 'checkbox',
				// ),
				array(
					'name'  => 'delete_data',
					'label' => __( 'Remove Record On Uninstall', 'loginpress-limit-login-attempts' ),
					'desc'  => __( 'This tool will remove all LoginPress - Limit Login Attempts record upon uninstall.', 'loginpress-limit-login-attempts' ),
					'type'  => 'checkbox',

				),
			// array(
			// 'name'              => 'lockout_increase',
			// 'label'             => __( 'Lockout Increase lockout time to ', 'loginpress-limit-login-attempts' ),
			// 'desc'              => __( 'Description.', 'loginpress-limit-login-attempts' ),
			// 'type'              => 'number',
			// 'min'               => 0,
			// 'default'           => '3',
			// )
			);
			$limit_login_settings = array( 'loginpress_limit_login_attempts' => $_limit_login_settings );
			return( array_merge( $limit_login_settings, $setting_array ) );
		}

		/** * * * * * * * * * * * * *
		 * Callback for blacklist tab.
		 *
		 * @since 1.0.0
		 * @version 1.0.1
		 * * * * * * * * * * * * * * */
		function loginpress_limit_login_attempts_blacklist_callback() {
			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";

			// $myblacklist = $wpdb->get_results( "SELECT * FROM `{$table}` WHERE `blacklist` = 1" );
			$myblacklist = $wpdb->get_results( "SELECT DISTINCT ip,blacklist FROM {$table} WHERE `blacklist` = 1" );

			$html = '<table id="loginpress_limit_login_blacklist" class="display" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>IP</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>IP</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>';
			if ( $myblacklist ) {
				$loginpress_user_bl_nonce = wp_create_nonce( 'loginpress-user-llla-nonce' );
				foreach ( $myblacklist as $blacklist ) {
					$html .= '<tr>';
					$html .= '<td>' . $blacklist->ip . '</td>';
					$html .= '<td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear" /><input type="hidden" class="loginpress__user-bl_nonce" name="loginpress__user-bl_nonce" value="' . $loginpress_user_bl_nonce . '"></td>';
					$html .= '</tr>';
				}
			} else {
				// $html .= '<h2>Not Found</h2>';
			}
			$html .= '</tbody>
      </table>';
			echo $html;
		}

		/** * * * * * * * * * * * * *
		 * Callback for Whitelist tab.
		 *
		 * @since 1.0.0
		 * @version 1.0.1
		 * * * * * * * * * * * * * * */
		function loginpress_limit_login_attempts_whitelist_callback() {

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";

			$mywhitelist = $wpdb->get_results( "SELECT DISTINCT ip,whitelist FROM {$table} WHERE `whitelist` = 1" );

			$html = '<table id="loginpress_limit_login_whitelist" class="display" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>IP</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>IP</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>';
			if ( $mywhitelist ) {
				$loginpress_user_wl_nonce = wp_create_nonce( 'loginpress-user-llla-nonce' );
				foreach ( $mywhitelist as $whitelist ) {
					$html .= '<tr>';
					$html .= '<td data-whitelist-ip="' . $whitelist->ip . '">' . $whitelist->ip . '</td>';
					$html .= '<td><input class="loginpress-whitelist-clear button button-primary" type="button" value="Clear" /><input type="hidden" class="loginpress__user-wl_nonce" name="loginpress__user-wl_nonce" value="' . $loginpress_user_wl_nonce . '"></td>';
					$html .= '</tr>';
				}
			} else {
				// $html .= '<h2>Not Found</h2>';
			}
			$html .= '</tbody>
      </table>';
			echo $html;
		}

		/** * * * * * * * * * * * * * * *
		 * Callback for Attempts log Tab.
		 *
		 * @since 1.0.0
		 * @version 1.0.1
		 * * * * * * * * * * * * * * * * */
		function loginpress_limit_login_attempts_log_callback() {

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";

			// Get result from $table where IP's aren't blaclisted or whitelisted.
			$myresult = $wpdb->get_results( "SELECT *, (whitelist+blacklist) as list FROM `{$table}` HAVING list = 0" );

			$html = '
		<!--	<select id="loginpress_limit_bulk_blacklist">
				<option value="">Bulk Action</option>
				<option value="unlock">Unclock</option>
				<option value="white_list">White List</option>
				<option value="black_list">Black List</option>
			</select>
			<button id="loginpress_limit_bulk_blacklist_submit">Submit</button>
			<input type="hidden" id="loginpress__llla_bulk_nonce" name="loginpress__llla_bulk_nonce" value="' . wp_create_nonce( 'loginpress-llla-bulk-nonce' ) . '"> -->
			
			<table id="loginpress_limit_login_log" class="display" cellspacing="0" width="100%">
        <thead>
					<tr>
						<!--<th><input type="checkbox" name="select_all" value="1" id="example-select-all"></th> -->
            <th>IP</th>
            <th>Date & Time</th>
            <th>Username</th>
            <th>Password</th>
            <th>Gateway</th>
            <th>Action</th>
          </tr>
          </thead>
          <tfoot>
						<tr>
              <th>IP</th>
              <th>Date & Time</th>
              <th>Username</th>
              <th>Password</th>
              <th>Gateway</th>
              <th>Action</th>
          </tr>
        </tfoot>
        <tbody>';
			if ( $myresult ) {
				$loginpress_user_llla_nonce = wp_create_nonce( 'loginpress-user-llla-nonce' );
				foreach ( $myresult as $result ) {
					$html .= '<tr id="loginpress_attempts_id_' . $result->id . '" data-login-attempt-user="' . $result->id . '" data-ip="' . $result->ip . '">';
					$html .= '<!--<th></th>--><td>' . $result->ip . '</td>';
					$html .= '<td>' . date( 'm/d/Y H:i:s', $result->datentime ) . '</td>';
					$html .= '<td><span class="attempts-sniper"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span>' . $result->username . '</td>';
					$html .= '<td>' . $result->password . '</td>';
					$html .= '<td>' . $result->gateway . '</td>';
					$html .= '<td> <input class="loginpress-attempts-unlock button button-primary" type="button" value="Unlock" /> <input class="loginpress-attempts-whitelist button" type="button" value="Whitelist" /> <input class="loginpress-attempts-blacklist button" type="button" value="Blacklist" /> <input type="hidden" class="loginpress__user-llla_nonce" name="loginpress__user-llla_nonce" value="' . $loginpress_user_llla_nonce . '"></td>';
					$html .= '</tr>';

				}
			} else {
				// $html .= '<h2>Not Found</h2>';
			}
			$html .= '</tbody>
      </table>';

			echo $html;
		}

		/** * * * * * * * * *
		 * Main Instance
		 *
		 * @since 1.0.0
		 * @static
		 * @return object Main instance of the Class
		 * * * * * * * * * * */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Ip add or remove setting callback.
		 *
		 * @since 1.2.3
		 * @param array $args argument of setting.
		 * @return void
		 */
		public function loginpress_ip_add_remove_callback( $args ) {
			$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type        = isset( $args['type'] ) ? $args['type'] : 'text';
			$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

			$html  = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], '', $placeholder );
			$html .= sprintf( __( '<p class="description"><button class="button loginpress-attempts-whitelist add_white_list" data-action="white_list" type="button" data-nonce="' . wp_create_nonce( 'ip_add_remove' ) . '"> %1$s </button><button class="button loginpress-attempts-blacklist add_black_list" data-action="black_list" type="button" data-nonce="' . wp_create_nonce( 'ip_add_remove' ) . '"> %2$s </button></p>', 'loginpress-limit-login-attempts' ), 'WhiteList', 'BlackList' );

			echo $html;
		}
	}

endif;
