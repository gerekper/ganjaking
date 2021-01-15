<?php
/**
 * weLaunch Themecheck Class
 *
 * @class weLaunch_Core
 * @version 3.5.0
 * @package weLaunch Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_ThemeCheck', false ) ) {

	/**
	 * Class weLaunch_ThemeCheck
	 */
	class weLaunch_ThemeCheck {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since   1.0.0
		 * @var     string
		 */
		protected $version = '1.0.0';

		/**
		 * Instance of this class.
		 *
		 * @since    1.0.0
		 * @var      object
		 */
		protected static $instance = null;

		/**
		 * Instance of the weLaunch class.
		 *
		 * @since    1.0.0
		 * @var      object
		 */
		protected static $welaunch = null;

		/**
		 * Details of the embedded weLaunch class.
		 *
		 * @since    1.0.0
		 * @var      object
		 */
		protected static $welaunch_details = null;

		/**
		 * Slug for various elements.
		 *
		 * @since   1.0.0
		 * @var     string
		 */
		protected $slug = 'welaunch_themecheck';

		/**
		 * Initialize the plugin by setting localization, filters, and administration functions.
		 *
		 * @since     1.0.0
		 */
		private function __construct() {
			if ( ! class_exists( 'ThemeCheckMain' ) ) {
				return;
			}

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_action( 'themecheck_checks_loaded', array( $this, 'disable_checks' ) );
			add_action( 'themecheck_checks_loaded', array( $this, 'add_checks' ) );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 * @return    object    A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 * @return    object    A single instance of this class.
		 */
		public static function get_welaunch_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$welaunch && weLaunch_Core::$as_plugin ) {
				self::$welaunch = new weLaunchFramework();
				self::$welaunch->init();
			}

			return self::$welaunch;
		}

		/**
		 * Return the weLaunch path info, if had.
		 *
		 * @param array $php_files Array of files to check.
		 *
		 * @since     1.0.0
		 * @return    object    A single instance of this class.
		 */
		public static function get_welaunch_details( $php_files = array() ) {
			if ( null === self::$welaunch_details ) {
				foreach ( $php_files as $php_key => $phpfile ) {

					// phpcs:ignore Generic.Strings.UnnecessaryStringConcat
					if ( false !== strpos( $phpfile, 'class' . ' weLaunchFramework {' ) ) {
						self::$welaunch_details               = array(
							'filename' => weLaunch_Core::strtolower( basename( $php_key ) ),
							'path'     => $php_key,
						);
						self::$welaunch_details['dir']        = str_replace( basename( $php_key ), '', $php_key );
						self::$welaunch_details['parent_dir'] = str_replace( basename( self::$welaunch_details['dir'] ) . '/', '', self::$welaunch_details['dir'] );
					}
				}
			}
			if ( null === self::$welaunch_details ) {
				self::$welaunch_details = false;
			}

			return self::$welaunch_details;
		}

		/**
		 * Disable Theme-Check checks that aren't relevant for ThemeForest themes
		 *
		 * @since    1.0.0
		 */
		public function disable_checks() {
			global $themechecks;

			/** $checks_to_disable = array(
			 *    'IncludeCheck',
			 *    'I18NCheck',
			 *    'AdminMenu',
			 *    'Bad_Checks',
			 *    'MalwareCheck',
			 *    'Theme_Support',
			 *    'CustomCheck',
			 *    'EditorStyleCheck',
			 *    'IframeCheck',
			 * );
			 * foreach ( $themechecks as $keyindex => $check ) {
			 *    if ( $check instanceof themecheck ) {
			 *        $check_class = get_class( $check );
			 *        if ( in_array( $check_class, $checks_to_disable ) ) {
			 *            unset( $themechecks[$keyindex] );
			 *        }
			 *    }
			 * }
			 */
		}

		/**
		 * Disable Theme-Check checks that aren't relevant for ThemeForest themes
		 *
		 * @since    1.0.0
		 */
		public function add_checks() {
			global $themechecks;

			// load all the checks in the checks directory.
			$dir = 'checks';
			foreach ( glob( dirname( __FILE__ ) . '/' . $dir . '/*.php' ) as $file ) {
				require_once $file;
			}
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since     1.0.1
		 */
		public function enqueue_admin_styles() {
			$screen = get_current_screen();
			if ( 'appearance_page_themecheck' === $screen->id ) {
				wp_enqueue_style( $this->slug . '-admin-styles', weLaunch_Core::$url . 'inc/themecheck/css/admin.css', array(), $this->version );
			}
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @since     1.0.1
		 */
		public function enqueue_admin_scripts() {

			$screen = get_current_screen();

			if ( 'appearance_page_themecheck' === $screen->id ) {
				wp_enqueue_script(
					$this->slug . '-admin-script',
					weLaunch_Core::$url . 'inc/themecheck/js/admin' . weLaunch_Functions::is_min() . '.js',
					array( 'jquery' ),
					$this->version,
					true
				);

				if ( ! isset( $_POST['themename'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification

					$intro  = '';
					$intro .= '<h2>weLaunch Theme-Check</h2>';
					$intro .= '<p>Extra checks for weLaunch to ensure you\'re ready for marketplace submission to marketplaces.</p>';

					$welaunch_check_intro['text'] = $intro;

					wp_localize_script( $this->slug . '-admin-script', 'welaunch_check_intro', $welaunch_check_intro );
				}
			}
		}
	}

	weLaunch_ThemeCheck::get_instance();
}
