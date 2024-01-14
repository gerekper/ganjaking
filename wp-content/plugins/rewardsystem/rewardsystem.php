<?php
/**
 * Plugin Name: SUMO Reward Points
 * Plugin URI:
 * Description: SUMO Reward Points is a WooCommerce Loyalty Reward System using which you can Reward your Customers using Reward Points for Purchasing Products, Writing Reviews, Sign up on your site etc
 * Version:29.8.0
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com
 * Tested up to: 6.4.2
 * WC tested up to: 8.4.0
 *
 * @package Rewardsystem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'FPRewardSystem' ) ) {

	/**
	* Class FPRewardSystem.
	*/
	final class FPRewardSystem {
		/**
		 * Version
		 *
		 * @var string.
		 */
		public $version = '29.8.0';

		/**
		 * Single Instance of the class
		 *
		 * @var string.
		 */
		protected static $_instance = null;

		/**
		 * Variable to get from email
		 *
		 * @var string.
		 */
		public static $rs_from_email_address;

		/**
		 * Variable to get from name.
		 *
		 * @var string.
		 */
		public static $rs_from_name;

		/**
		 * Variation IDs
		 *
		 * @var int.
		 */
		public static $variation_ids;

		/**
		 * Load Reward System Class in Single Instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning has been forbidden
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to perform this action!!!', 'rewardsystem' ), esc_html( $this->version ) );
		}

		/**
		 * Unserialize the class data has been forbidden
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to perform this action!!!', 'rewardsystem' ), esc_html( $this->version ) );
		}

		/**
		 * Reward System Constructor
		 */
		public function __construct() {
			// Include once will help to avoid fatal error by load the files when you call init hook.
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			$this->header_already_sent_problem();
			if ( ! $this->check_if_woocommerce_is_active() ) {
				return;
			}

			$this->list_of_constants();
			// Improvement made for translation in V24.5.
			add_action( 'init', array( $this, 'rs_translate_file' ) );
			// Compatability for GDPR Compliance.
			include_once 'includes/gdpr/class-srp-privacy.php';
			include_once 'includes/class-rs-install.php';

			$this->include_files();

			include 'includes/frontend/class-rs-menu-query.php';

			add_action( 'wp_enqueue_scripts', array( $this, 'srp_scripts' ) );
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

			// Footable.js Console Error in Flatsome theme overcomes in V24.0.4.
			include_once 'includes/frontend/class-frontend-enqueues.php';

			add_action( 'init', array( 'RSFrontendEnqueues', 'init' ) );

			$this->init_hooks();
			/* Load WooCommerce Enqueue Script to Load the Script and Styles by filtering the WooCommerce Screen IDS */
			add_filter( 'woocommerce_screen_ids', array( $this, 'reward_system_load_default_enqueues' ), 9, 1 );

			$this->reward_gateway();
		}

		/**
		 * Function to Prevent Header Error that says You have already sent the header.
		 */
		public function header_already_sent_problem() {
			ob_start();
		}

		/**
		 * Function to check wheather Woocommerce is active or not
		 */
		public function check_if_woocommerce_is_active() {

			if ( is_multisite() && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( is_admin() ) {
					add_action( 'admin_notices', array( 'FPRewardSystem', 'woocommerce_dependency_warning_message' ) );
				}
				return false;
			} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( is_admin() ) {
					add_action( 'admin_notices', array( 'FPRewardSystem', 'woocommerce_dependency_warning_message' ) );
				}
				return false;
			}
			return true;
		}

		/**
		 * Warning Message When woocommerce is not active.
		 */
		public static function woocommerce_dependency_warning_message() {
			echo wp_kses_post( "<div class='error'><p> SUMO Reward Points requires WooCommerce Plugin should be Active !!! </p></div>" );
		}

		/**
		 * Prepare the constants value array.
		 */
		public function list_of_constants() {
			$protocol = 'http://';

			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' === sanitize_title( $_SERVER['HTTPS'] ) || 1 == sanitize_title( $_SERVER['HTTPS'] ) ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == sanitize_title( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) {
				$protocol = 'https://';
			}

			/**
			 * Hook:fp_rewardsystem_constants.
			 *
			 * @since 1.0.0
			 */
			$list_of_constants = apply_filters(
				'fp_rewardsystem_constants',
				array(
					'SRP_VERSION'         => $this->version,
					'SRP_PLUGIN_FILE'     => __FILE__,
					'SRP_FOLDER_NAME'     => 'rewardsystem',
					'SRP_PROTOCOL'        => $protocol,
					'SRP_ADMIN_URL'       => admin_url( 'admin.php' ),
					'SRP_ADMIN_AJAX_URL'  => admin_url( 'admin-ajax.php' ),
					'SRP_PLUGIN_BASENAME' => plugin_basename( __FILE__ ),
					'SRP_PLUGIN_DIR_URL'  => plugin_dir_url( __FILE__ ),
					'SRP_PLUGIN_PATH'     => untrailingslashit( plugin_dir_path( __FILE__ ) ),
					'SRP_PLUGIN_URL'      => untrailingslashit( plugins_url( '/', __FILE__ ) ),
				)
			);

			if ( is_array( $list_of_constants ) && ! empty( $list_of_constants ) ) {
				foreach ( $list_of_constants as $constantname => $value ) {
					$this->define_constant( $constantname, $value );
				}
			}
		}

		/**
		 * Define Constant
		 *
		 * @param string      $name Constant name.
		 * @param string|bool $value Constant value.
		 */
		protected function define_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include Files
		 */
		public function include_files() {
			include_once 'includes/srp-common-functions.php';

			// Abstract classes.
			include_once 'includes/abstract/abstract-srp-post.php';

			include_once 'includes/class-srp-register-post-type.php';

			// Entity.
			include_once 'includes/entity/class-srp-birthday.php';
			include_once 'includes/entity/class-srp-promotional.php';

			// WP_List Table Files.
			include_once 'includes/admin/wpliststable/class_wp_list_table_for_newgift_voucher.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_view_gift_voucher.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_for_nominee_user_list.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_for_users.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_master_log.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_referral_table.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_view_log_user.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_view_referral_table.php';
			include_once 'includes/admin/wpliststable/class_rs_birthday_reward_table.php';
			// Bonus List Table.
			include_once 'includes/admin/wpliststable/class_wp_list_table_bonus_log.php';
			include_once 'includes/admin/wpliststable/class_wp_list_table_view_user_bonus_log.php';
			// Anniversary List Table.
			include_once 'includes/admin/wpliststable/class_wp_list_table_anniversary_log.php';

			include_once 'includes/frontend/compatibility/rewardpoints_wc2point6.php';
			include_once 'includes/class_wpml_support.php';
			include_once 'includes/class-fp-product-datas.php';
			include_once 'includes/class-rs-date-time.php';
			include_once 'includes/class-srp-query.php';

			include_once 'includes/class-srp-coupon-validator.php';
			include_once 'includes/class-srp-coupon-handler.php';
			include_once 'includes/class-srp-cron-handler.php';

			include_once 'includes/class-rs-points-data.php';

			// Instances.
			include_once 'action-scheduler/class-rs-action-scheduler-instances.php';

			if ( is_admin() ) {
				$this->include_admin_files();
			}

			include 'includes/frontend/class-fp-referral-log.php';
			include 'includes/frontend/class-fpmemberlevel-percentage.php';
			include 'includes/frontend/tab/modules/class-rs-fpsms-frontend.php';

			include_once 'includes/frontend/rs_jquery.php';
			include_once 'includes/class-reward-points-orders.php';
			include_once 'includes/class-fp-award-points-for-purchase-and-actions.php';
			if ( get_option( 'rs_enable_earned_level_based_reward_points' ) == 'yes' ) {
				include 'includes/frontend/class-rs-fpfreeproduct-frontend.php';
			}

			include 'includes/frontend/class-rs-fpbonuspoints-frontend.php';
			include 'includes/frontend/class-rs-fpanniversarypoints-frontend.php';
		}

		/**
		 * Include Admin Files
		 */
		public function include_admin_files() {

			include_once 'includes/admin/class-admin-enqueues.php';

			include_once 'includes/admin/class-fp-rewardsystem-admin-assets.php';
			include_once 'includes/admin/class-reward-system-tab-management.php';
			include_once 'includes/admin/class-fp-srp-admin-ajax.php';

			// Product/Category settings.
			include_once 'includes/admin/settings/class-simple-product-settings.php';
			include_once 'includes/admin/settings/class-variable-product-settings.php';
			include_once 'includes/admin/settings/class-category-product-settings.php';

			include 'includes/admin/wc_class_encashing_wplist.php';
			include 'includes/admin/wc_class_send_point_wplist.php';
		}

		/**
		 * Plugins Loaded.
		 */
		public function plugins_loaded() {
			if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && allow_reward_points_for_user( get_current_user_id() ) ) {

				include_once 'includes/frontend/class-fp-rewardsystem-frontend-ajax.php';
				include_once 'includes/frontend/class-rs-rewardsystem-shortcodes.php';
				include_once 'includes/frontend/class-fp-rewardsystem-frontend-assets.php';

				$module_value = get_list_of_modules();
				foreach ( modules_file_name() as $filename ) {
					$module_to_exclude = array( 'fpcoupon', 'fpsendpoints', 'fppointexpiry', 'fpreset', 'fpreportsincsv', 'fpimportexport', 'fpemailexpiredpoints', 'fpbonuspoints', 'fpanniversarypoints', 'fppromotional' );
					if ( isset( $module_value[ $filename ] ) && 'yes' === $module_value[ $filename ] && ! in_array( $filename, $module_to_exclude ) ) {
						include SRP_PLUGIN_PATH . '/includes/frontend/tab/modules/class-rs-' . $filename . '-frontend.php';
					}
				}

				include 'includes/frontend/tab/class-rs-fprsmessage-frontend.php';
				include 'includes/frontend/tab/class-rs-fprsadvanced-frontend.php';

				include 'includes/frontend/class-rs-simple-product-messages.php';
				include 'includes/frontend/class-variable-product.php';
				if ( class_exists( 'BuddyPress' ) && ( 'yes' === get_option( 'rs_reward_action_activated' ) ) ) {
					include 'includes/frontend/compatibility/class-rs-fpbuddypress-compatibility.php';
				}
				if ( class_exists( 'BuddyPress' ) && ( 'yes' === get_option( 'rs_reward_action_activated' ) ) ) {
					include 'includes/frontend/compatibility/class-rs-fpwcbooking-compatabilty.php';
				}
			}

			RS_Action_Scheduler_Instances::instance();
		}

		/**
		 * Include Reward Gateway Settings.
		 */
		public function reward_gateway() {
			if ( 'yes' === get_option( 'rs_gateway_activated' ) ) {
				include 'includes/admin/class_rewardgateway.php';
				add_action( 'plugins_loaded', 'init_reward_gateway_class' );
			}
		}

		/**
		 * Add Activation Hooks.
		 */
		public function init_hooks() {
			add_action( 'init', array( $this, 'compatibility_for_woocommerce_pdf_invoices' ) );

			add_action( 'woocommerce_init', array( 'RSInstall', 'check_version' ) );
			register_activation_hook( __FILE__, array( 'RSInstall', 'install' ) );
			register_activation_hook( __FILE__, array( 'FPRewardSystem', 'sumo_reward_points_welcome_screen_activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'flush_rules' ) );

			add_action( 'init', array( $this, 'load_rest_api' ) );
			// HPOS Compatibility.
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		}

		/**
		 * HPOS Compatibility.
		 *
		 * @since 3.3.0
		 */
		public function declare_hpos_compatibility() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', SRP_PLUGIN_FILE, true );
			}
		}

		/**
		 * Flush Rules.
		 */
		public function flush_rules() {
			// Update flush option for my reward menu.
			update_option( 'rs_flush_rewrite_rules', 1 );

			wp_clear_scheduled_hook( 'srp_birthday_cron' );
			wp_clear_scheduled_hook( 'rscronjob' );
			wp_clear_scheduled_hook( 'rs_send_mail_before_expiry' );
		}

		/**
		 * Include file for compatability file for PDF Invoice.
		 */
		public function compatibility_for_woocommerce_pdf_invoices() {
			if ( is_admin() && class_exists( 'WooCommerce_PDF_Invoices' ) ) {
				include 'includes/woocommerce-pdf-invoices-packing-slips.php';
			}
		}

		/**
		 * Welcome page function.
		 */
		public static function sumo_reward_points_welcome_screen_activate() {
			set_transient( '_welcome_screen_activation_redirect_reward_points', true, 30 );
		}

		/**
		 * Translate File
		 */
		public function rs_translate_file() {

			if ( function_exists( 'determine_locale' ) ) {
				$locale = determine_locale();
			} else {
				$locale = is_admin() ? get_user_locale() : get_locale();
			}

			/**
			 * Hook:plugin_locale.
			 *
			 * @since 1.0
			 */
			$locale = apply_filters( 'plugin_locale', $locale, 'rewardsystem' );

			unload_textdomain( 'rewardsystem' );

			load_textdomain( 'rewardsystem', WP_LANG_DIR . '/rewardsystem/rewardsystem-' . $locale . '.mo' );

			load_plugin_textdomain( 'rewardsystem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Load the Default JAVASCRIPT and CSS.
		 *
		 * @param array $screen_ids Screen IDs.
		 */
		public function reward_system_load_default_enqueues( $screen_ids ) {
			if ( isset( $_GET['page'] ) && ( 'rewardsystem_callback' === sanitize_text_field( $_GET['page'] ) ) ) {
				$array[] = get_current_screen()->id;
				return $array;
			}

			return $screen_ids;
		}

		/**
		 * Load the Scripts.
		 */
		public static function srp_scripts() {
			wp_enqueue_script( 'srpscripts', SRP_PLUGIN_DIR_URL . 'assets/js/srpscripts.js', array( 'jquery' ), SRP_VERSION, false );
			wp_localize_script(
				'srpscripts',
				'srpscripts_params',
				array(
					'ajaxurl'             => SRP_ADMIN_AJAX_URL,
					'enable_option_nonce' => wp_create_nonce( 'earn-reward-points' ),
					'checked_alert_msg'   => get_option( 'rs_alert_msg_in_acc_page_when_checked' ),
					'unchecked_alert_msg' => get_option( 'rs_alert_msg_in_acc_page_when_unchecked' ),
				)
			);
		}

		/**
		 * Load REST API.
		 */
		public function load_rest_api() {
			include 'includes/rest-api/class-srp-rest-server.php';
		}
	}

	FPRewardSystem::instance();
}
