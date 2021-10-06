<?php

/*
 * Plugin Name: SUMO Reward Points
 * Plugin URI:
 * Description: SUMO Reward Points is a WooCommerce Loyalty Reward System using which you can Reward your Customers using Reward Points for Purchasing Products, Writing Reviews, Sign up on your site etc
 * Version:26.9
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com
 * Tested up to: 5.8
 * WC tested up to: 5.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FPRewardSystem' ) ) {

	final class FPRewardSystem {
		/*
		 * Version
		 */

		public $version = '26.9' ;

		/*
		 * Single Instance of the class
		 */
		protected static $_instance = null ;

		/*
		 * Variable to get from email
		 */
		public static $rs_from_email_address ;

		/*
		 * Variable to get from name
		 */
		public static $rs_from_name ;

		/*
		 * Variation IDs
		 */
		public static $variation_ids ;

		/*
		 * Load Reward System Class in Single Instance
		 */

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self() ;
			}
			return self::$_instance ;
		}

		/* Cloning has been forbidden */

		public function __clone() {
			_doing_it_wrong( __FUNCTION__ , esc_html__( 'You are not allowed to perform this action!!!' , 'rewardsystem' ) , esc_html($this->version) ) ;
		}

		/*
		 * Unserialize the class data has been forbidden
		 */

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__ , esc_html__( 'You are not allowed to perform this action!!!' , 'rewardsystem' ) , esc_html($this->version) ) ;
		}

		/*
		 * Reward System Constructor
		 */

		public function __construct() {
			/* Include once will help to avoid fatal error by load the files when you call init hook */
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' ) ;

			$this->header_already_sent_problem() ;
			if ( ! $this->check_if_woocommerce_is_active() ) {
				return ;
			}

			$this->list_of_constants() ;
			// Improvement made for translation in V24.5.
			add_action( 'init' , array( $this , 'rs_translate_file' ) ) ;
			//Compatability for GDPR Compliance
			include_once('includes/gdpr/class-srp-privacy.php') ;

			// Set Email Template cron job.
			add_filter( 'cron_schedules' , array( $this , 'set_up_rs_cron' ) ) ;
			self::create_cron_job() ;

			// Set Point Expiry Cron
			add_filter( 'cron_schedules' , array( $this , 'set_point_expiry_cron' ) ) ;
			$this->create_point_expiry_cron() ;

			include_once('includes/class-rs-install.php') ;
			include_once('woocommerce-log/class-fp-woocommerce-log.php') ;
			include_once('backgroundprocess/rs-main-file-for-background-process.php') ;
			$this->include_files() ;
			include('includes/frontend/class-rs_menu_query.php') ;
			add_action( 'wp_enqueue_scripts' , array( $this , 'srp_scripts' ) ) ;
			add_action( 'plugins_loaded' , array( $this , 'include_frontend_files' ) ) ;

			/* Footable.js Console Error in Flatsome theme overcomes in V24.0.4 */
			include_once('includes/frontend/class-frontend-enqueues.php') ;
			add_action( 'init' , array( 'RSFrontendEnqueues' , 'init' ) ) ;

			$this->init_hooks() ;
			/* Load WooCommerce Enqueue Script to Load the Script and Styles by filtering the WooCommerce Screen IDS */
			add_filter( 'woocommerce_screen_ids' , array( $this , 'reward_system_load_default_enqueues' ) , 9 , 1 ) ;

			$this->rewardgateway() ;
		}

		/*
		 * Function to Prevent Header Error that says You have already sent the header.
		 */

		public function header_already_sent_problem() {
			ob_start() ;
		}

		/*
		 * Function to check wheather Woocommerce is active or not
		 */

		public function check_if_woocommerce_is_active() {

			if ( is_multisite() && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( is_admin() ) {
					add_action( 'init' , array( 'FPRewardSystem' , 'woocommerce_dependency_warning_message' ) ) ;
				}
				return false ;
			} else if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( is_admin() ) {
					add_action( 'init' , array( 'FPRewardSystem' , 'woocommerce_dependency_warning_message' ) ) ;
				}
				return false ;
			}
			return true ;
		}

		/*
		 * Warning Message When woocommerce is not active.
		 *
		 */

		public static function woocommerce_dependency_warning_message() {
			echo wp_kses_post("<div class='error'><p> SUMO Reward Points requires WooCommerce Plugin should be Active !!! </p></div>" );
		}

		/*
		 * Prepare the constants value array.
		 */

		public function list_of_constants() {
			$protocol = 'http://' ;

			if ( isset( $_SERVER[ 'HTTPS' ] ) && ( 'on' == sanitize_title($_SERVER[ 'HTTPS' ]) || 1 == sanitize_title($_SERVER[ 'HTTPS' ]) ) || isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) && 'https' == sanitize_title($_SERVER[ 'HTTP_X_FORWARDED_PROTO' ])) {
				$protocol = 'https://' ;
			}
			$list_of_constants = apply_filters( 'fprewardsystem_constants' , array(
				'SRP_VERSION'         => $this->version ,
				'SRP_PLUGIN_FILE'     => __FILE__ ,
				'SRP_FOLDER_NAME'     => 'rewardsystem' ,
				'SRP_PROTOCOL'        => $protocol ,
				'SRP_ADMIN_URL'       => admin_url( 'admin.php' ) ,
				'SRP_ADMIN_AJAX_URL'  => admin_url( 'admin-ajax.php' ) ,
				'SRP_PLUGIN_BASENAME' => plugin_basename( __FILE__ ) ,
				'SRP_PLUGIN_DIR_URL'  => plugin_dir_url( __FILE__ ) ,
				'SRP_PLUGIN_PATH'     => untrailingslashit( plugin_dir_path( __FILE__ ) ) ,
				'SRP_PLUGIN_URL'      => untrailingslashit( plugins_url( '/' , __FILE__ ) ) ,
					) ) ;
			if ( is_array( $list_of_constants ) && ! empty( $list_of_constants ) ) {
				foreach ( $list_of_constants as $constantname => $value ) {
					$this->define_constant( $constantname , $value ) ;
				}
			}
		}

		/*
		 * Define Constant
		 * @param string $name
		 * @param string|bool $value
		 */

		protected function define_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name , $value ) ;
			}
		}

		/*
		 * Include Files 
		 */

		public function include_files() {
			//welcome page include file
			include_once 'includes/admin/welcome.php' ;
			//WP_List Table Files
			include_once('includes/admin/wpliststable/class_wp_list_table_for_newgift_voucher.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_view_gift_voucher.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_for_nominee_user_list.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_for_users.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_master_log.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_referral_table.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_view_log_user.php') ;
			include_once('includes/admin/wpliststable/class_wp_list_table_view_referral_table.php') ;
			include_once('includes/frontend/compatibility/rewardpoints_wc2point6.php') ;
			include_once('includes/class_wpml_support.php') ;
			include_once('includes/class-fp-common-functions.php') ;
			include_once('includes/class-fp-product-datas.php') ;
			include_once('includes/class-rs-date-time.php') ;

			include_once('includes/class-rs-points-data.php') ;

			if ( is_admin() ) {
				$this->include_admin_files() ;
			}

			include('includes/frontend/class-fp-referral-log.php') ;
			include('includes/frontend/class-fpmemberlevel-percentage.php') ;
			include('includes/frontend/tab/modules/class-rs-fpsms-frontend.php') ;

			include_once('includes/frontend/rs_jquery.php') ;
			include_once('includes/class-reward-points-orders.php') ;
			include_once('includes/class-fp-award-points-for-purchase-and-actions.php') ;
			if ( get_option( 'rs_enable_earned_level_based_reward_points' ) == 'yes' ) {
				include('includes/frontend/class-rs-fpfreeproduct-frontend.php') ;
			}
		}

		/*
		 * Include Admin Files
		 */

		public function include_admin_files() {
			include_once('includes/admin/class-admin-enqueues.php') ;

			include_once('includes/admin/class-fp-rewardsystem-admin-assets.php') ;
			include_once('includes/admin/class-reward-system-tab-management.php') ;
			include_once('includes/admin/class-fp-srp-admin-ajax.php') ;

			//product/product category settings
			include_once('includes/admin/settings/class-simple-product-settings.php') ;
			include_once('includes/admin/settings/class-variable-product-settings.php') ;
			include_once('includes/admin/settings/class-category-product-settings.php') ;

			include('includes/admin/wc_class_encashing_wplist.php') ;
			include('includes/admin/wc_class_send_point_wplist.php') ;
		}

		/*
		 * Include Admin Files
		 */

		public function include_frontend_files() {
			if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && allow_reward_points_for_user( get_current_user_id() ) ) {
				include_once('includes/frontend/class-fp-rewardsystem-frontend-ajax.php') ;
				include_once('includes/frontend/class-rs-rewardsystem-shortcodes.php') ;
				include_once('includes/frontend/class-fp-rewardsystem-frontend-assets.php') ;

				$ModulesId   = modules_file_name() ;
				$ModuleValue = get_list_of_modules() ;
				foreach ( $ModulesId as $filename ) {
					$ModuleToExclude = array( 'fpcoupon' , 'fpsendpoints' , 'fppointexpiry' , 'fpreset' , 'fpreportsincsv' , 'fpimportexport' , 'fpemailexpiredpoints' ) ;
					if ( 'yes'  == $ModuleValue[ $filename ] && ! in_array( $filename , $ModuleToExclude ) ) {
						include SRP_PLUGIN_PATH . '/includes/frontend/tab/modules/class-rs-' . $filename . '-frontend.php' ;
					}
				}

				include('includes/frontend/tab/class-rs-fprsmessage-frontend.php') ;
				include('includes/frontend/tab/class-rs-fprsadvanced-frontend.php') ;

				include('includes/frontend/class-simple-product.php') ;
				include('includes/frontend/class-variable-product.php') ;
				if ( class_exists( 'BuddyPress' ) && ( 'yes'  == get_option( 'rs_reward_action_activated' ) ) ) {
					include('includes/frontend/compatibility/class-rs-fpbuddypress-compatibility.php') ;
				}
				if ( class_exists( 'BuddyPress' ) && ( 'yes'  == get_option( 'rs_reward_action_activated' ) ) ) {
					include('includes/frontend/compatibility/class-rs-fpwcbooking-compatabilty.php') ;
				}
			}
			SRP_Background_Process::init() ;
		}

		public function set_up_rs_cron( $schedules ) {
			$interval = ( int ) get_option( 'rs_mail_cron_time' ) ;
			if (  'minutes' == get_option( 'rs_mail_cron_type' ) ) {
				$interval = $interval * 60 ;
			} else if ( 'hours'  == get_option( 'rs_mail_cron_type' ) ) {
				$interval = $interval * 3600 ;
			} else if ( 'days' == get_option( 'rs_mail_cron_type' ) ) {
				$interval = $interval * 86400 ;
			}
			$schedules[ 'rshourly' ] = array(
				'interval' => $interval ,
				'display'  => 'RS Hourly'
					) ;
			return $schedules ;
		}

		public static function create_cron_job() {
			delete_option( 'rscheckcronsafter' ) ;
			if ( false == wp_next_scheduled( 'rscronjob' ) && 'yes' == get_option( 'rs_email_activated' , 'no' ) ) {
				wp_schedule_event( time() , 'rshourly' , 'rscronjob' ) ;
			}
		}

		/*
		 * Set point expiry cron.
		 */

		public function set_point_expiry_cron( $schedules ) {

			$schedules[ 'rs_hourly' ] = array(
				'interval' => 3600 ,
				'display'  => 'RS Hourly'
					) ;

			return $schedules ;
		}

		/*
		 * Create point expiry cron.
		 */

		public function create_point_expiry_cron() {

			if ( wp_next_scheduled( 'rs_send_mail_before_expiry' ) ) {
				return ;
			}

			if ( 'yes' == get_option( 'rs_email_template_expire_activated' ) ) {
				wp_schedule_event( time() , 'rs_hourly' , 'rs_send_mail_before_expiry' ) ;
			} else {
				wp_unschedule_event( time() , 'rs_hourly' , 'rs_send_mail_before_expiry' ) ;
			}
		}

		public function rewardgateway() {
			if ( 'yes' == get_option( 'rs_gateway_activated' ) ) {
				include('includes/admin/class_rewardgateway.php') ;
				add_action( 'plugins_loaded' , 'init_reward_gateway_class' ) ;
			}
		}

		public function init_hooks() {
			global $wpdb ;
			$redirect   = true ;
						$table_name = "{$wpdb->prefix}rspointexpiry" ;

			if ( ( 'yes' != get_option( 'rs_upgrade_success' ) ) && ( ! RSInstall::rs_check_table_exists( $table_name ) ) && ( true != get_option( 'rs_new_update_user' ) ) && SRP_Background_Process::fp_rs_upgrade_file_exists() ) {
				register_activation_hook( __FILE__ , array( 'SRP_Background_Process' , 'set_transient_for_product_update' ) ) ;
				$redirect = false ;
			}

			add_action( 'init' , array( $this , 'compatibility_for_woocommerce_pdf_invoices' ) ) ;

			register_activation_hook( __FILE__ , array( 'RSInstall' , 'install' ) ) ;

			if ( $redirect ) {
				register_activation_hook( __FILE__ , array( 'FPRewardSystem' , 'sumo_reward_points_welcome_screen_activate' ) ) ;
			}

			register_deactivation_hook( __FILE__ , array( $this , 'flush_rules' ) ) ;
		}

		public function flush_rules() {
			// Update flush option for my reward menu.  
			update_option( 'rs_flush_rewrite_rules' , 1 ) ;
		}

		public function compatibility_for_woocommerce_pdf_invoices() {
			//Include show/hide earned redeemed message
			if ( is_admin() && class_exists( 'WooCommerce_PDF_Invoices' ) ) {
				include('includes/woocommerce-pdf-invoices-packing-slips.php') ;
			}
		}

		// welcome page function
		public static function sumo_reward_points_welcome_screen_activate() {
			set_transient( '_welcome_screen_activation_redirect_reward_points' , true , 30 ) ;
		}

		/*
		 * Translate File
		 * 
		 */

		public function rs_translate_file() {

			if ( function_exists( 'determine_locale' ) ) {
				$locale = determine_locale() ;
			} else {
				$locale = is_admin() ? get_user_locale() : get_locale() ;
			}

			$locale = apply_filters( 'plugin_locale' , $locale , 'rewardsystem' ) ;

			unload_textdomain( 'rewardsystem' ) ;

			load_textdomain( 'rewardsystem' , WP_LANG_DIR . '/rewardsystem/rewardsystem-' . $locale . '.mo' ) ;

			load_plugin_textdomain( 'rewardsystem' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages' ) ;
		}

		/*
		 * Load the Default JAVASCRIPT and CSS
		 */

		public function reward_system_load_default_enqueues( $screen_ids ) {

			$newscreenids = get_current_screen() ;
			if ( isset( $_GET[ 'page' ] ) && ( 'rewardsystem_callback' == sanitize_text_field($_GET[ 'page' ]) ) ) {
				$array[] = $newscreenids->id ;
				return $array ;
			}
			return $screen_ids ;
		}

		public static function srp_scripts() {
			wp_enqueue_script( 'srpscripts' , SRP_PLUGIN_DIR_URL . 'assets/js/srpscripts.js' , array( 'jquery' ) , SRP_VERSION ) ;
			wp_localize_script( 'srpscripts' , 'srpscripts_params' , array(
				'ajaxurl'             => SRP_ADMIN_AJAX_URL ,
				'enable_option_nonce' => wp_create_nonce( 'earn-reward-points' ) ,
				'checked_alert_msg'   => get_option( 'rs_alert_msg_in_acc_page_when_checked' ) ,
				'unchecked_alert_msg' => get_option( 'rs_alert_msg_in_acc_page_when_unchecked' ) ,
			) ) ;
		}

	}

	FPRewardSystem::instance() ;
}
