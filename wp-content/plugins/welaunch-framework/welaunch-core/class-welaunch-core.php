<?php
/**
 * weLaunch Core Class
 *
 * @class weLaunch_Core
 * @version 4.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Core', false ) ) {

	/**
	 * Class weLaunch_Core
	 */
	class weLaunch_Core {

		/**
		 * Class instance.
		 *
		 * @var object
		 */
		public static $instance;

		/**
		 * Project version
		 *
		 * @var project string
		 */
		public static $version;

		/**
		 * Project directory.
		 *
		 * @var project string.
		 */
		public static $dir;

		/**
		 * Project URL.
		 *
		 * @var project URL.
		 */
		public static $url;

		/**
		 * Base directory path.
		 *
		 * @var string
		 */
		public static $welaunch_path;

		/**
		 * Absolute direction path to WordPress upload directory.
		 *
		 * @var null
		 */
		public static $upload_dir = null;

		/**
		 * Full URL to WordPress upload directory.
		 *
		 * @var string
		 */
		public static $upload_url = null;

		/**
		 * Set when weLaunch is run as a plugin.
		 *
		 * @var bool
		 */
		public static $is_plugin = true;

		/**
		 * Indicated in_theme or in_plugin.
		 *
		 * @var string
		 */
		public static $installed = '';

		/**
		 * Set when weLaunch is run as a plugin.
		 *
		 * @var bool
		 */
		public static $as_plugin = false;

		/**
		 * Set when weLaunch is embedded within a theme.
		 *
		 * @var bool
		 */
		public static $in_theme = false;

		/**
		 * Set when weLaunch Pro plugin is loaded and active.
		 *
		 * @var bool
		 */
		public static $pro_loaded = false;

		/**
		 * Pointer to updated google fonts array.
		 *
		 * @var array
		 */
		public static $google_fonts = array();

		/**
		 * List of files calling weLaunch.
		 *
		 * @var array
		 */
		public static $callers = array();

		/**
		 * Nonce.
		 *
		 * @var string
		 */
		public static $wp_nonce;

		/**
		 * Pointer to _SERVER global.
		 *
		 * @var null
		 */
		public static $server = null;

		/**
		 * Pointer to the thirdparty fixes class.
		 *
		 * @var null
		 */
		public static $third_party_fixes = null;

		/**
		 * weLaunch Welcome screen object.
		 *
		 * @var null
		 */
		public static $welcome = null;

		/**
		 * weLaunch Appsero object.
		 *
		 * @var null
		 */
		public static $appsero = null;

		/**
		 * weLaunch Insights object.
		 *
		 * @var null
		 */
		public static $insights = null;

		/**
		 * Creates instance of class.
		 *
		 * @return weLaunch_Core
		 * @throws Exception Comment.
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();

				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Class init.
		 */
		private function init() {

			self::$server = array(
				'SERVER_SOFTWARE' => '',
				'REMOTE_ADDR'     => weLaunch_Helpers::is_local_host() ? '127.0.0.1' : '',
				'HTTP_USER_AGENT' => '',
			);
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
				self::$server['SERVER_SOFTWARE'] = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
			}
			if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
				self::$server['REMOTE_ADDR'] = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			}
			if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
				self::$server['HTTP_USER_AGENT'] = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
			}
			// phpcs:enable

			self::$dir = trailingslashit( wp_normalize_path( dirname( realpath( __FILE__ ) ) ) );

			weLaunch_Functions_Ex::generator();

			$plugin_info = weLaunch_Functions_Ex::is_inside_plugin( __FILE__ );

			self::$installed = class_exists( 'weLaunch_Framework_Plugin' ) ? 'plugin' : 'in_plugin';
			self::$is_plugin = class_exists( 'weLaunch_Framework_Plugin' );
			self::$as_plugin = true;
			self::$url       = trailingslashit( dirname( $plugin_info['url'] ) );
			// if ( isset( $plugin_info['slug'] ) && ! empty( $plugin_info['slug'] ) ) {
			// 	$client->slug = $plugin_info['slug'];
			// }
			// $client->type = 'plugin';		

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$url = apply_filters( 'welaunch/url', self::$url );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$dir = apply_filters( 'welaunch/dir', self::$dir );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$is_plugin = apply_filters( 'welaunch/is_plugin', self::$is_plugin );

			if ( ! function_exists( 'current_time' ) ) {
				require_once ABSPATH . '/wp-includes/functions.php';
			}

			$upload_dir       = wp_upload_dir();
			self::$upload_dir = $upload_dir['basedir'] . '/welaunch/';
			self::$upload_url = str_replace( array( 'https://', 'http://' ), '//', $upload_dir['baseurl'] . '/welaunch/' );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$upload_dir = apply_filters( 'welaunch/upload_dir', self::$upload_dir );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$upload_url = apply_filters( 'welaunch/upload_url', self::$upload_url );

		}

		/**
		 * Code to execute on framework __construct.
		 *
		 * @param object $parent Pointer to weLaunchFramework object.
		 * @param array  $args Global arguments array.
		 */
		public static function core_construct( $parent, $args ) {
			self::$third_party_fixes = new weLaunch_ThirdParty_Fixes( $parent );

			weLaunch_ThemeCheck::get_instance();

		}

		/**
		 * Autoregister run.
		 *
		 * @throws Exception Comment.
		 */
		private function includes() {
			if ( class_exists( 'weLaunch_Pro' ) && isset( weLaunch_Pro::$dir ) ) {
				self::$pro_loaded = true;
			}

			require_once dirname( __FILE__ ) . '/inc/classes/class-welaunch-path.php';
			require_once dirname( __FILE__ ) . '/inc/classes/class-welaunch-functions-ex.php';
			require_once dirname( __FILE__ ) . '/inc/classes/class-welaunch-helpers.php';
			
			require_once dirname( __FILE__ ) . '/inc/classes/class-welaunch-instances.php';
			weLaunch_Functions_Ex::register_class_path( 'weLaunch', dirname( __FILE__ ) . '/inc/classes' );
			weLaunch_Functions_Ex::register_class_path( 'weLaunch', dirname( __FILE__ ) . '/inc/welcome' );
			spl_autoload_register( array( $this, 'register_classes' ) );

			self::$welcome = new weLaunch_Welcome();
			new weLaunch_Rest_Api_Builder( $this );

			$support_hash = md5( md5( weLaunch_Functions_Ex::hash_key() . '-welaunch' ) . '-support' );
			add_action( 'wp_ajax_nopriv_' . $support_hash, array( 'weLaunch_Helpers', 'support_args' ) );
			add_action( 'wp_ajax_' . $support_hash, array( 'weLaunch_Helpers', 'support_args' ) );
			$hash_arg = md5( trailingslashit( network_site_url() ) . '-welaunch' );
			add_action( 'wp_ajax_nopriv_' . $hash_arg, array( 'weLaunch_Helpers', 'hash_arg' ) );
			add_action( 'wp_ajax_' . $hash_arg, array( 'weLaunch_Helpers', 'hash_arg' ) );
			add_action( 'wp_ajax_welaunch_support_hash', array( 'weLaunch_Functions', 'support_hash' ) );

			add_filter( 'welaunch/tracking/options', array( 'weLaunch_Helpers', 'welaunch_stats_additions' ) );
		}

		/**
		 * Register callback for autoload.
		 *
		 * @param string $class_name name of class.
		 */
		public function register_classes( $class_name ) {
			$class_name_test = weLaunch_Core::strtolower( $class_name );

			if ( strpos( $class_name_test, 'welaunch' ) === false ) {
				return;
			}

			if ( ! class_exists( 'weLaunch_Functions_Ex' ) ) {
				require_once weLaunch_Path::get_path( '/inc/classes/class-welaunch-functions-ex.php' );
			}

			if ( ! class_exists( $class_name ) ) {
				// Backward compatibility for extensions sucks!
				if ( 'weLaunch_Instances' === $class_name ) {
					require_once weLaunch_Path::get_path( '/inc/classes/class-welaunch-instances.php' );
					require_once weLaunch_Path::get_path( '/inc/lib/welaunch-instances.php' );

					return;
				}

				// Load weLaunch APIs.
				if ( 'weLaunch' === $class_name ) {
					require_once weLaunch_Path::get_path( '/inc/classes/class-welaunch-api.php' );

					return;
				}

				// weLaunch extra theme checks.
				if ( 'weLaunch_ThemeCheck' === $class_name ) {
					require_once weLaunch_Path::get_path( '/inc/themecheck/class-welaunch-themecheck.php' );

					return;
				}

				if ( 'weLaunch_Welcome' === $class_name ) {
					require_once weLaunch_Path::get_path( '/inc/welcome/class-welaunch-welcome.php' );

					return;
				}

				$mappings = array(
					'weLaunchFrameworkInstances'  => 'weLaunch_Instances',
					'welaunchCoreEnqueue'         => '',
					'welaunchCorePanel'           => 'weLaunch_Panel',
					'welaunchCoreEnqueue'         => 'weLaunch_Enqueue',
					'weLaunch_Abstract_Extension' => 'weLaunch_Extension_Abstract',
				);
				$alias    = false;
				if ( isset( $mappings[ $class_name ] ) ) {
					$alias      = $class_name;
					$class_name = $mappings[ $class_name ];
				}

				// Everything else.
				$file = 'class.' . $class_name_test . '.php';

				$class_path = weLaunch_Path::get_path( '/inc/classes/' . $file );

				if ( ! file_exists( $class_path ) ) {
					$class_file_name = str_replace( '_', '-', $class_name );
					$file            = 'class-' . $class_name_test . '.php';
					$class_path      = weLaunch_Path::get_path( '/inc/classes/' . $file );
				}

				if ( file_exists( $class_path ) && ! class_exists( $class_name ) ) {
					require_once $class_path;
				}
				if ( class_exists( $class_name ) && ! empty( $alias ) && ! class_exists( $alias ) ) {
					class_alias( $class_name, $alias );
				}
			}

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'welaunch/core/includes', $this );
		}

		/**
		 * Hooks to run on instance creation.
		 */
		private function hooks() {
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'welaunch/core/hooks', $this );
		}

		/**
		 * Action to run on WordPress heartbeat.
		 *
		 * @return bool
		 */
		public static function is_heartbeat() {
			// Disregard WP AJAX 'heartbeat'call.  Why waste resources?
			if ( isset( $_POST ) && isset( $_POST['_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_nonce'] ) ), 'heartbeat-nonce' ) ) {
				if ( isset( $_POST['action'] ) && 'heartbeat' === sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {

					// Hook, for purists.
					if ( has_action( 'welaunch/ajax/heartbeat' ) ) {
						// phpcs:ignore WordPress.NamingConventions.ValidHookName
						do_action( 'welaunch/ajax/heartbeat' );
					}

					return true;
				}

				return false;
			}

			// Buh bye!
			return false;
		}

		/**
		 * Helper method to check for mb_strtolower or to use the standard strtolower.
		 *
		 * @param string $str String to make lowercase.
		 *
		 * @return string
		 */
		public static function strtolower( $str ) {
			if ( function_exists( 'mb_strtolower' ) && function_exists( 'mb_detect_encoding' ) ) {
				return mb_strtolower( $str, mb_detect_encoding( $str ) );
			} else {
				return strtolower( $str );
			}
		}
	}

	/*
	 * Backwards comparability alias
	 */
	class_alias( 'weLaunch_Core', 'welaunch-core' );
}
