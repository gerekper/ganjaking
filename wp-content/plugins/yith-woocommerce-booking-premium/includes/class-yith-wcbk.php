<?php
/**
 * Class YITH_WCBK
 * Main Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK' ) ) {
	/**
	 * Class YITH_WCBK
	 * Main Class
	 */
	class YITH_WCBK {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBK
		 */
		private static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = YITH_WCBK_VERSION;

		/**
		 * Admin instance
		 *
		 * @var YITH_WCBK_Admin
		 */
		public $admin;

		/**
		 * Frontend instance
		 *
		 * @var YITH_WCBK_Frontend
		 */
		public $frontend;

		/**
		 * Orders instance
		 *
		 * @var YITH_WCBK_Orders
		 */
		public $orders;

		/**
		 * Booking helper instance
		 *
		 * @var YITH_WCBK_Booking_Helper
		 */
		public $booking_helper;

		/**
		 * Notes instance instance
		 *
		 * @var YITH_WCBK_Notes
		 */
		public $notes;

		/**
		 * Notifier instance
		 *
		 * @var YITH_WCBK_Emails
		 */
		public $notifier;

		/**
		 * Settings instance
		 *
		 * @var YITH_WCBK_Settings
		 */
		public $settings;

		/**
		 * Exporter instance
		 *
		 * @var YITH_WCBK_Exporter
		 */
		public $exporter;

		/**
		 * Endpoints instance
		 *
		 * @var YITH_WCBK_Endpoints
		 */
		public $endpoints;

		/**
		 * Integrations instance
		 *
		 * @var YITH_WCBK_Integrations
		 */
		public $integrations;

		/**
		 * Language instance
		 *
		 * @var YITH_WCBK_Language
		 */
		public $language;

		/**
		 * WP Compatibility instance
		 *
		 * @var YITH_WCBK_WP_Compatibility
		 */
		public $wp;

		/**
		 * Background processes instance
		 *
		 * @var YITH_WCBK_Background_Processes
		 */
		public $background_processes;

		/**
		 * Theme instance
		 *
		 * @var YITH_WCBK_Theme
		 */
		public $theme;

		/**
		 * Builders instance
		 *
		 * @var YITH_WCBK_Builders
		 */
		public $builders;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Magic __get method for backwards compatibility. Maps legacy vars to new getters.
		 *
		 * @param string $key Key name.
		 *
		 * @return mixed
		 */
		public function __get( string $key ) {
			$value = null;

			switch ( $key ) {
				case 'notifier':
					$value = $this->emails();
					break;
				case 'maps':
					$value = $this->maps();
					break;
				case 'person_type_helper':
					$value = $this->person_type_helper();
					break;
				case 'google_calendar_sync':
					$value = $this->google_calendar_sync();
					break;
				case 'externals':
					$value = function_exists( 'yith_wcbk_booking_externals' ) ? yith_wcbk_booking_externals() : false;
					break;
				case 'extra_cost_helper':
					$value = class_exists( 'YITH_WCBK_Extra_Cost_Helper' ) ? YITH_WCBK_Extra_Cost_Helper::get_instance() : false;
					break;
				case 'search_form_helper':
					$value = class_exists( 'YITH_WCBK_Search_Form_Helper' ) ? YITH_WCBK_Search_Form_Helper::get_instance() : false;
					break;
				default:
					return null;
			}

			yith_wcbk_doing_it_wrong( __CLASS__ . '::' . $key, 'This property of the main Booking class should not be accessed directly.', '4.0.0' );

			return $value;
		}

		/**
		 * YITH_WCBK constructor.
		 */
		private function __construct() {
			$this->modules(); // Modules need to be the first thing loaded, to handle Premium version correctly.

			YITH_WCBK_Install::get_instance();

			YITH_WCBK_Post_Types::init();
			YITH_WCBK_Shortcodes::init();
			YITH_WCBK_Assets::get_instance();
			YITH_WCBK_AJAX::get_instance();

			if ( $this->is_request( 'admin' ) || $this->is_request( 'rest' ) ) {
				$this->admin = yith_wcbk_admin();
			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend = yith_wcbk_frontend();
			}

			$this->emails();

			$this->background_processes = YITH_WCBK_Background_Processes::get_instance();
			$this->booking_helper       = YITH_WCBK_Booking_Helper::get_instance();
			$this->orders               = YITH_WCBK_Orders::get_instance();
			$this->notes                = YITH_WCBK_Notes::get_instance();
			$this->settings             = YITH_WCBK_Settings::get_instance();
			$this->exporter             = YITH_WCBK_Exporter::get_instance();
			$this->endpoints            = YITH_WCBK_Endpoints::get_instance();
			$this->language             = YITH_WCBK_Language::get_instance();
			$this->integrations         = YITH_WCBK_Integrations::get_instance();
			$this->wp                   = YITH_WCBK_WP_Compatibility::get_instance();

			$this->theme = YITH_WCBK_Theme::get_instance();

			$this->builders = YITH_WCBK_Builders::get_instance();

			YITH_WCBK_REST_Server::get_instance();

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_filter( 'user_has_cap', array( $this, 'user_has_capability' ), 10, 3 );
			add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );
		}

		/**
		 * Return the Modules class instance.
		 *
		 * @return YITH_WCBK_Modules
		 */
		public function modules() {
			return YITH_WCBK_Modules::get_instance();
		}

		/**
		 * Return the Emails class instance.
		 *
		 * @return YITH_WCBK_Emails
		 */
		public function emails() {
			return YITH_WCBK_Emails::get_instance();
		}

		/**
		 * Return the Maps class instance.
		 *
		 * @return YITH_WCBK_Maps|false
		 */
		public function maps() {
			if ( class_exists( 'YITH_WCBK_Maps' ) && yith_wcbk_is_google_maps_module_active() ) {
				return YITH_WCBK_Maps::get_instance();
			}

			return false;
		}

		/**
		 * Return the Person Type Helper class instance.
		 *
		 * @return YITH_WCBK_Person_Type_Helper|false
		 */
		public function person_type_helper() {
			if ( class_exists( 'YITH_WCBK_Person_Type_Helper' ) && yith_wcbk_is_people_module_active() ) {
				return YITH_WCBK_Person_Type_Helper::get_instance();
			}

			return false;
		}

		/**
		 * Return the Google Calendar Sync class instance.
		 *
		 * @return YITH_WCBK_Google_Calendar_Sync|false
		 */
		public function google_calendar_sync() {
			if ( class_exists( 'YITH_WCBK_Google_Calendar_Sync' ) && yith_wcbk_is_google_calendar_module_active() ) {
				return YITH_WCBK_Google_Calendar_Sync::get_instance();
			}

			return false;
		}

		/**
		 * Checks if a user has a certain capability.
		 *
		 * @param array $allcaps All capabilities.
		 * @param array $caps    Capabilities.
		 * @param array $args    Arguments.
		 *
		 * @return array The filtered array of all capabilities.
		 * @since 2.1.4
		 */
		public function user_has_capability( $allcaps, $caps, $args ) {
			if ( isset( $caps[0] ) ) {
				switch ( $caps[0] ) {
					case 'view_booking':
						$user_id = isset( $args[1] ) ? intval( $args[1] ) : false;
						$booking = isset( $args[2] ) ? yith_get_booking( $args[2] ) : false;

						$can = $user_id && $booking && $booking->get_user_id() === $user_id;
						$can = apply_filters( 'yith_wcbk_user_can_view_booking', $can, $user_id, $booking );

						if ( $can ) {
							$allcaps['view_booking'] = true;
						}
						break;
				}
			}

			return $allcaps;
		}

		/**
		 * What type of request is this?
		 *
		 * @param string $type admin, ajax, cron or frontend.
		 *
		 * @return bool
		 * @since 2.1.17
		 */
		public function is_request( $type ) {
			$is_request = false;
			switch ( $type ) {
				case 'admin':
					$is_request = is_admin();
					break;
				case 'rest':
					$is_request = WC()->is_rest_api_request();
					break;
				case 'ajax':
					$is_request = defined( 'DOING_AJAX' );
					break;
				case 'cron':
					$is_request = defined( 'DOING_CRON' );
					break;
				case 'frontend':
					$is_request = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
					break;
			}

			return apply_filters( 'yith_wcbk_is_request', $is_request, $type );
		}

		/**
		 * Load the privacy class
		 */
		public function load_privacy() {
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'class-yith-wcbk-privacy.php';
		}


		/**
		 * Load Plugin Framework
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Register Widgets
		 */
		public function register_widgets() {
			if ( 'widget' === get_option( 'yith-wcbk-booking-form-position', 'default' ) ) {
				register_widget( 'YITH_WCBK_Product_Form_Widget' );
			}
		}

		/**
		 * Declare support for WooCommerce features.
		 *
		 * @since 4.4.0
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				$features = array( 'custom_order_tables', 'cart_checkout_blocks' );

				$init = defined( 'YITH_WCBK_INIT' ) ? YITH_WCBK_INIT : false;
				$init = defined( 'YITH_WCBK_EXTENDED_INIT' ) ? YITH_WCBK_EXTENDED_INIT : $init;

				foreach ( $features as $feature ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( $feature, $init, true );
				}
			}
		}

	}
}

/**
 * Unique access to instance of YITH_WCBK class
 *
 * @return YITH_WCBK
 */
function yith_wcbk() {
	return YITH_WCBK::get_instance();
}
