<?php

if ( ! class_exists( 'LoginPress_Attempts' ) ) :

	/**
	 * LoginPress_Attempts
	 */
	class LoginPress_Attempts {

		/**
		 * Variable that Check for LoginPress Key.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $attempts_settings;

		/*
		* * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		public function __construct() {

			$this->attempts_settings = get_option( 'loginpress_limit_login_attempts' );
			$this->_hooks();
		}

		/** * * * * * *
		 * Action hooks.
		 * * * * * * * */
		public function _hooks() {
			// add_action( 'wp_login_failed', array( $this, 'llla_login_failed' ), 999,1  );
			add_action( 'wp_loaded', array( $this, 'llla_wp_loaded' ) );
			add_action( 'init', [ $this, 'llla_check_xml_request' ] );
			add_filter( 'authenticate', array( $this, 'llla_login_attempts_auth' ), 21, 3 );
			$disable_xml_rpc = isset( $this->attempts_settings['disable_xml_rpc_request'] ) ? $this->attempts_settings['disable_xml_rpc_request'] : '';
			if ( 'on' === $disable_xml_rpc ) {
				$this->disable_xml_rpc();
			}
		}

		/**
		 * Check Auth if request coming from xmlrpc.
		 *
		 * @return void
		 */
		public function llla_check_xml_request() {

			global $pagenow;
			if ( 'xmlrpc.php' === $pagenow ) {
				$this->llla_wp_loaded();
			}
		}
		/**
		 * Disable xml rpc request
		 *
		 * @since 1.3.0
		 * @return void
		 */
		public function disable_xml_rpc() {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}
		/**
		 * Attempts Login Authentication.
		 *
		 * @param  [object] $user
		 * @param  [string] $username
		 * @param  [string] $password
		 * @since 1.0.0
		 */

		function llla_login_attempts_auth( $user, $username, $password ) {

			if ( $user instanceof WP_User ) {
				return $user;
			}

			// Is username or password field empty?
			if ( empty( $username ) || empty( $password ) ) {

				if ( is_wp_error( $user ) ) {
					return $user;
				}

				$error = new WP_Error();

				if ( empty( $username ) ) {
					$error->add( 'empty_username', $this->limit_query() );
				}

				if ( empty( $password ) ) {
					$error->add( 'empty_password', $this->limit_query() );
				}

				return $error;
			}

			if ( ! empty( $username ) && ! empty( $password ) ) {

				$error = new WP_Error();
				global $pagenow, $wpdb;
				$table = "{$wpdb->prefix}loginpress_limit_login_details";
				$ip    = $this->get_address();

				$whitelisted_ip = $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE `ip` = '{$ip}' AND `whitelist` = 1" );

				if ( $whitelisted_ip >= 1 ) {
					return;
				} else {
					$error->add( 'llla_error', $this->limit_query( $username, $password ) );
				}

				return $error;
			}

		}

		/**
		 * Die WordPress login on blacklist or lockout.
		 *
		 * @since  1.0.0
		 */
		function llla_wp_loaded() {
			global $pagenow, $wpdb;
			$table             = "{$wpdb->prefix}loginpress_limit_login_details";
			$ip                = $this->get_address();
			$last_attempt_time = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s ORDER BY `datentime` DESC", $ip ) );
			if ( $last_attempt_time ) {
				$last_attempt_time = $last_attempt_time->datentime;
			}

			$blacklist_check = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s AND `blacklist` = 1", $ip ) );

			if ( 'xmlrpc.php' == $pagenow && ( $this->llla_time() || $blacklist_check >= 1 ) ) {
				echo __( $this->loginpress_lockout_error( $last_attempt_time ), 'loginpress-limit-login-attempts' );
				wp_die( '', 403 );
			}

			// limit wp-admin access.
			if ( is_admin() && $blacklist_check >= 1 ) {
				wp_die( __( 'You are not allowed to access admin panel', 'loginpress-limit-login-attempts' ), 403 );
			}

			if ( $pagenow === 'wp-login.php' && get_option( 'permalink_structure' ) && $blacklist_check >= 1 ) {
				wp_die( __( 'You are not allowed to access admin panel', 'loginpress-limit-login-attempts' ), 403 );
			}

			if ( $pagenow === 'wp-login.php' && $this->llla_time() ) {
				wp_die( __( $this->loginpress_lockout_error( $last_attempt_time ), 'loginpress-limit-login-attempts' ), 403 );
			}
		}

		public function user_limit_check() {
			global $wpdb;
			$table        = "{$wpdb->prefix}loginpress_limit_login_details";
			$ip           = $this->get_address();
			$current_time = current_time( 'timestamp' );
			$gate         = $this->gateway();

			$attempts_allowed  = isset( $this->attempts_settings['attempts_allowed'] ) ? $this->attempts_settings['attempts_allowed'] : '';
			$lockout_increase  = isset( $this->attempts_settings['lockout_increase'] ) ? $this->attempts_settings['lockout_increase'] : '';
			$minutes_lockout   = isset( $this->attempts_settings['minutes_lockout'] ) ? intval( $this->attempts_settings['minutes_lockout'] ) : '';
			$last_attempt_time = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s ORDER BY `datentime` DESC", $ip ) );

			if ( $last_attempt_time ) {
				$last_attempt_time = $last_attempt_time->datentime;
			}

			$lockout_time = $current_time - ( $minutes_lockout * 60 );
			$attempt_time = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s AND `datentime` > %s", $ip, $lockout_time ) );

			return array(
				'attempts_allowed'  => $attempts_allowed,
				'lockout_increase'  => $lockout_increase,
				'minutes_lockout'   => $minutes_lockout,
				'last_attempt_time' => $last_attempt_time,
				'lockout_time'      => $lockout_time,
				'attempt_time'      => $attempt_time,
			);
		}

		/**
		 * Callback for error message 'llla_error'
		 *
		 * @param  [type] $username
		 * @param  [type] $password
		 * @return [srting] $error.
		 * @since 1.0.0
		 */
		public function limit_query( $username, $password ) {

			global $wpdb;
			$table        = "{$wpdb->prefix}loginpress_limit_login_details";
			$ip           = $this->get_address();
			$current_time = current_time( 'timestamp' );
			$gate         = $this->gateway();
			$error        = new WP_Error();

			$attempts_allowed  = isset( $this->attempts_settings['attempts_allowed'] ) ? $this->attempts_settings['attempts_allowed'] : '';
			$lockout_increase  = isset( $this->attempts_settings['lockout_increase'] ) ? $this->attempts_settings['lockout_increase'] : '';
			$minutes_lockout   = isset( $this->attempts_settings['minutes_lockout'] ) ? intval( $this->attempts_settings['minutes_lockout'] ) : '';
			$last_attempt_time = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s ORDER BY `datentime` DESC", $ip ) );

			if ( $last_attempt_time ) {
				$last_attempt_time = $last_attempt_time->datentime;
			}
			$lockout_time = $current_time - ( $minutes_lockout * 60 );

			$attempt_time = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s AND `datentime` > %s", $ip, $lockout_time ) );
			if ( $attempt_time < $attempts_allowed ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (ip, username, password, datentime, gateway) values (%s, %s, %s, %s, %s)", $ip, $username, $password, $current_time, $gate ) );
				return $this->loginpress_attemps_error( $attempt_time );
			} else {
				return $this->loginpress_lockout_error( $last_attempt_time );
			}
		}

		/**
		 * Lockout error message.
		 *
		 * @return [string] [Custom error message]
		 * @since 1.0.0
		 */
		public function loginpress_lockout_error( $last_attempt_time ) {

			$current_time = current_time( 'timestamp' );
			$time         = $current_time - $last_attempt_time;
			$count        = $time / 60 % 60;    // to get minutes
			$minutes_set  = isset( $this->attempts_settings['minutes_lockout'] ) ? intval( $this->attempts_settings['minutes_lockout'] ) : '';
			if ( $count < $minutes_set ) {
				$remain          = $minutes_set - $count;
				$lockout_message = __( "<strong>ERROR:</strong> Too many failed attempts. You are locked out for {$remain} minutes.", 'loginpress-limit-login-attempts' );
			}

			return $lockout_message;
		}

		/**
		 * LoginPress Limit Login Attemps Time Checker.
		 *
		 * @return boolean
		 * @since 1.0.0
		 */
		function llla_time() {

			global $wpdb;
			$table        = "{$wpdb->prefix}loginpress_limit_login_details";
			$ip           = $this->get_address();
			$current_time = current_time( 'timestamp' );

			$attempts_allowed = isset( $this->attempts_settings['attempts_allowed'] ) ? $this->attempts_settings['attempts_allowed'] : '';
			$lockout_increase = isset( $this->attempts_settings['lockout_increase'] ) ? $this->attempts_settings['lockout_increase'] : '';
			$minutes_lockout  = isset( $this->attempts_settings['minutes_lockout'] ) ? intval( $this->attempts_settings['minutes_lockout'] ) : '';

			$lockout_time = $current_time - ( $minutes_lockout * 60 );
			$attempt_time = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$wpdb->prefix}loginpress_limit_login_details` WHERE `ip` = %s AND `datentime` > %s AND `whitelist` = 0", $ip, $lockout_time ) );
			if ( $attempt_time < $attempts_allowed ) {
				return false;
			} else {
				return true;
			}
		}


		/**
		 * [loginpress_attemps_error Attempts error message]
		 *
		 * @return [string] [Custom error message]
		 * @since 1.0.0
		 */
		public function loginpress_attemps_error( $count ) {

			$attempts_allowed = isset( $this->attempts_settings['attempts_allowed'] ) ? intval( $this->attempts_settings['attempts_allowed'] ) : '';

			$ramains = $attempts_allowed - $count - 1;

			$lockout_message = sprintf( __( '%1$sERROR:%2$s You have only %3$s attempts', 'loginpress-limit-login-attempts' ), '<strong>', '</strong>', $ramains );
			return $lockout_message;
		}

		/**
		 * Check the gateway.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		function gateway() {

			if ( isset( $_POST['woocommerce-login-nonce'] ) ) {
				$gateway = esc_html__( 'WooCommerce', 'loginpress-limit-login-attempts' );
			} elseif ( isset( $GLOBALS['wp_xmlrpc_server'] ) && is_object( $GLOBALS['wp_xmlrpc_server'] ) ) {
				$gateway = esc_html__( 'XMLRPC', 'loginpress-limit-login-attempts' );

			} else {
				$gateway = esc_html__( 'WP Login', 'loginpress-limit-login-attempts' );

			}

			return $gateway;
		}

		/**
		 * Get correct remote address
		 *
		 * @param string $type_name
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function get_address( $type_name = '' ) {

			$ipaddress = '';
			if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) ) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			} elseif ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			} elseif ( ! empty( $_SERVER['HTTP_FORWARDED'] ) ) {
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			} else {
				$ipaddress = 'UNKNOWN';
			}

			return $ipaddress;
		}
	}

endif;

new LoginPress_Attempts();

