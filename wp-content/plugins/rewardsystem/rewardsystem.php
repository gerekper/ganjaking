<?php

/*
 * Plugin Name: SUMO Reward Points
 * Plugin URI:
 * Description: SUMO Reward Points is a WooCommerce Loyalty Reward System using which you can Reward your Customers using Reward Points for Purchasing Products, Writing Reviews, Sign up on your site etc
 * Version:25.6
 * Author: Fantastic Plugins
 * Author URI:http://fantasticplugins.com
 * WC tested up to: 4.5.2
 */

if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if( ! class_exists( 'FPRewardSystem' ) ) {

    final class FPRewardSystem {
        /*
         * Version
         */

        public $version = '25.6' ;

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
            if( is_null( self::$_instance ) ) {
                self::$_instance = new self() ;
            }
            return self::$_instance ;
        }

        /* Cloning has been forbidden */

        public function __clone() {
            _doing_it_wrong( __FUNCTION__ , __( 'You are not allowed to perform this action!!!' , SRP_LOCALE ) , $this->version ) ;
        }

        /*
         * Unserialize the class data has been forbidden
         */

        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__ , __( 'You are not allowed to perform this action!!!' , SRP_LOCALE ) , $this->version ) ;
        }

        /*
         * Reward System Constructor
         */

        public function __construct() {
            /* Include once will help to avoid fatal error by load the files when you call init hook */
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' ) ;

            $this->header_already_sent_problem() ;
            if( ! $this->check_if_woocommerce_is_active() )
                return ;

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
            add_action( 'admin_enqueue_scripts' , array( $this , 'admin_enqueue_script' ) ) ;
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

            if( is_multisite() && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                if( is_admin() ) {
                    add_action( 'init' , array( 'FPRewardSystem' , 'woocommerce_dependency_warning_message' ) ) ;
                }
                return false ;
            } else if( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                if( is_admin() ) {
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
            echo "<div class='error'><p> SUMO Reward Points requires WooCommerce Plugin should be Active !!! </p></div>" ;
        }

        /*
         * Prepare the constants value array.
         */

        public function list_of_constants() {
            $protocol = 'http://' ;

            if( isset( $_SERVER[ 'HTTPS' ] ) && ($_SERVER[ 'HTTPS' ] == 'on' || $_SERVER[ 'HTTPS' ] == 1) || isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) && $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] == 'https' ) {
                $protocol = 'https://' ;
            }
            $list_of_constants = apply_filters( 'fprewardsystem_constants' , array(
                'SRP_VERSION'         => $this->version ,
                'SRP_PLUGIN_FILE'     => __FILE__ ,
                'SRP_LOCALE'          => 'rewardsystem' ,
                'SRP_FOLDER_NAME'     => 'rewardsystem' ,
                'SRP_PROTOCOL'        => $protocol ,
                'SRP_ADMIN_URL'       => admin_url( 'admin.php' ) ,
                'SRP_ADMIN_AJAX_URL'  => admin_url( 'admin-ajax.php' ) ,
                'SRP_PLUGIN_BASENAME' => plugin_basename( __FILE__ ) ,
                'SRP_PLUGIN_DIR_URL'  => plugin_dir_url( __FILE__ ) ,
                'SRP_PLUGIN_PATH'     => untrailingslashit( plugin_dir_path( __FILE__ ) ) ,
                'SRP_PLUGIN_URL'      => untrailingslashit( plugins_url( '/' , __FILE__ ) ) ,
                    ) ) ;
            if( is_array( $list_of_constants ) && ! empty( $list_of_constants ) ) {
                foreach( $list_of_constants as $constantname => $value ) {
                    $this->define_constant( $constantname , $value ) ;
                }
            }
        }

        /*
         * Define Constant
         * @param string $name
         * @param string|bool $value
         */

        protected function define_constant( $name , $value ) {
            if( ! defined( $name ) ) {
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

            if( is_admin() )
                $this->include_admin_files() ;

            include('includes/frontend/class-fp-referral-log.php') ;
            include('includes/frontend/class-fpmemberlevel-percentage.php') ;
            include('includes/frontend/tab/modules/class-rs-fpsms-frontend.php') ;

            include_once('includes/frontend/rs_jquery.php') ;
            include_once('includes/class-reward-points-orders.php') ;
            include_once('includes/class-fp-award-points-for-purchase-and-actions.php') ;
            if( get_option( 'rs_enable_earned_level_based_reward_points' ) == 'yes' )
                include('includes/frontend/class-rs-fpfreeproduct-frontend.php') ;
        }

        /*
         * Include Admin Files
         */

        public function include_admin_files() {
            include_once('includes/admin/class-admin-enqueues.php') ;
            if( isset( $_GET[ 'page' ] ) && ($_GET[ 'page' ] == 'rewardsystem_callback') )
                include_once('assets/css/rewardsystem-settings-styles.php') ;

            include_once('assets/js/rs_section_expand.php') ;
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
            if( ( ! is_admin() || defined( 'DOING_AJAX' )) && allow_reward_points_for_user( get_current_user_id() ) ) {
                include_once('includes/frontend/class-fp-rewardsystem-frontend-ajax.php') ;
                include_once('includes/frontend/class-rs-rewardsystem-shortcodes.php') ;
                include_once('includes/frontend/class-fp-rewardsystem-frontend-assets.php') ;

                $ModulesId   = modules_file_name() ;
                $ModuleValue = get_list_of_modules() ;
                foreach( $ModulesId as $filename ) {
                    $ModuleToExclude = array( 'fpcoupon' , 'fpsendpoints' , 'fppointexpiry' , 'fpreset' , 'fpreportsincsv' , 'fpimportexport' , 'fpemailexpiredpoints' ) ;
                    if( $ModuleValue[ $filename ] == 'yes' && ! in_array( $filename , $ModuleToExclude ) )
                        include SRP_PLUGIN_PATH . '/includes/frontend/tab/modules/class-rs-' . $filename . '-frontend.php' ;
                }

                include('includes/frontend/tab/class-rs-fprsmessage-frontend.php') ;
                include('includes/frontend/tab/class-rs-fprsadvanced-frontend.php') ;

                include('includes/frontend/class-simple-product.php') ;
                include('includes/frontend/class-variable-product.php') ;
                if( class_exists( 'BuddyPress' ) && (get_option( 'rs_reward_action_activated' ) == 'yes') )
                    include('includes/frontend/compatibility/class-rs-fpbuddypress-compatibility.php') ;
                if( class_exists( 'BuddyPress' ) && (get_option( 'rs_reward_action_activated' ) == 'yes') )
                    include('includes/frontend/compatibility/class-rs-fpwcbooking-compatabilty.php') ;
            }
            RS_Main_Function_for_Background_Process::init() ;
        }

        public function set_up_rs_cron( $schedules ) {
            $interval = ( int ) get_option( 'rs_mail_cron_time' ) ;
            if( get_option( 'rs_mail_cron_type' ) == 'minutes' ) {
                $interval = $interval * 60 ;
            } else if( get_option( 'rs_mail_cron_type' ) == 'hours' ) {
                $interval = $interval * 3600 ;
            } else if( get_option( 'rs_mail_cron_type' ) == 'days' ) {
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
            if( wp_next_scheduled( 'rscronjob' ) == false && get_option( 'rs_email_activated' , 'no' ) == 'yes' )
                wp_schedule_event( time() , 'rshourly' , 'rscronjob' ) ;
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

            if( wp_next_scheduled( 'rs_send_mail_before_expiry' ) ) {
                return ;
            }

            if( "yes" == get_option( 'rs_email_template_expire_activated' ) ) {
                wp_schedule_event( time() , 'rs_hourly' , 'rs_send_mail_before_expiry' ) ;
            } else {
                wp_unschedule_event( time() , 'rs_hourly' , 'rs_send_mail_before_expiry' ) ;
            }
        }

        public function rewardgateway() {
            if( get_option( 'rs_gateway_activated' ) == 'yes' ) {
                include('includes/admin/class_rewardgateway.php') ;
                add_action( 'plugins_loaded' , 'init_reward_gateway_class' ) ;
            }
        }

        public function init_hooks() {
            global $wpdb ;
            $redirect   = true ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;

            if( (get_option( 'rs_upgrade_success' ) != 'yes' ) && ( ! RSInstall::rs_check_table_exists( $table_name )) && (get_option( 'rs_new_update_user' ) != true) && RS_Main_Function_for_Background_Process::fp_rs_upgrade_file_exists() ) {
                register_activation_hook( __FILE__ , array( 'RS_Main_Function_for_Background_Process' , 'set_transient_for_product_update' ) ) ;
                $redirect = false ;
            }

            add_action( 'init' , array( $this , 'compatibility_for_woocommerce_pdf_invoices' ) ) ;

            register_activation_hook( __FILE__ , array( 'RSInstall' , 'install' ) ) ;

            if( $redirect )
                register_activation_hook( __FILE__ , array( 'FPRewardSystem' , 'sumo_reward_points_welcome_screen_activate' ) ) ;
        }

        public function compatibility_for_woocommerce_pdf_invoices() {
            //Include show/hide earned redeemed message
            if( is_admin() && class_exists( 'WooCommerce_PDF_Invoices' ) )
                include('includes/woocommerce-pdf-invoices-packing-slips.php') ;
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

            if( function_exists( 'determine_locale' ) ) {
                $locale = determine_locale() ;
            } else {
                $locale = is_admin() ? get_user_locale() : get_locale() ;
            }

            $locale = apply_filters( 'plugin_locale' , $locale , SRP_LOCALE ) ;

            unload_textdomain( SRP_LOCALE ) ;

            load_textdomain( SRP_LOCALE , WP_LANG_DIR . '/rewardsystem/rewardsystem-' . $locale . '.mo' ) ;

            load_plugin_textdomain( SRP_LOCALE , false , dirname( plugin_basename( __FILE__ ) ) . '/languages' ) ;
        }

        /*
         * Load the Default JAVASCRIPT and CSS
         */

        public function reward_system_load_default_enqueues( $screen_ids ) {

            $newscreenids = get_current_screen() ;
            if( isset( $_GET[ 'page' ] ) && ($_GET[ 'page' ] == 'rewardsystem_callback' ) ) {
                $array[] = $newscreenids->id ;
                return $array ;
            }
            return $screen_ids ;
        }

        // welcome page register css file
        public function admin_enqueue_script() {
            global $post ;
            if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'sumo-reward-points-welcome-page' ) {
                wp_register_style( 'wp_reward_welcome_page' , SRP_PLUGIN_URL . "/assets/css/rewardpoints_welcome_page_style.css" ) ;
                wp_enqueue_style( 'wp_reward_welcome_page' ) ;
            }
            if( isset( $_GET[ 'page' ] ) && ($_GET[ 'page' ] == 'rewardsystem_callback') ) {
                wp_register_script( 'admin_settings_js' , SRP_PLUGIN_URL . "/assets/js/sumo-admin-settings-design.js" ) ;
                wp_enqueue_script( 'admin_settings_js' ) ;
            }
            $sumo_bookings_check = false ;
            if( isset( $post->ID ) && isset( $post->post_type ) ) {
                if( $post->post_type == 'product' ) {
                    $sumo_bookings_check = is_sumo_booking_active( $post->ID ) ;
                }
            }
            $localize_script = array(
                'ajaxurl'              => SRP_ADMIN_AJAX_URL ,
                'rs_unsubscribe_email' => wp_create_nonce( 'unsubscribe-mail' ) ,
                'reset_confirm_msg'    => __( 'Are you sure want to Reset?' , SRP_LOCALE ) ,
                'field_ids'            => '#_rewardsystem_assign_buying_points[type=text],#_rewardsystempoints[type=text],#_rewardsystempercent[type=text],'
                . '#_referralrewardsystempoints[type=text],#_referralrewardsystempercent[type=text],#_socialrewardsystempoints_facebook[type=text],'
                . '#_socialrewardsystempercent_facebook[type=text],#_socialrewardsystempoints_twitter[type=text],'
                . '#_socialrewardsystempercent_twitter[type=text],#_socialrewardsystempoints_google[type=text],#_socialrewardsystempercent_google[type=text],'
                . '#_socialrewardsystempoints_vk[type=text],#_socialrewardsystempercent_vk[type=text],#rs_max_earning_points_for_user[type=text],'
                . '#rs_earn_point[type=text],#rs_earn_point_value[type=text],#rs_redeem_point[type=text],#rs_redeem_point_value[type=text],'
                . '#rs_fixed_max_redeem_discount[type=text],#rs_global_reward_points[type=text],#rs_global_referral_reward_point[type=text],'
                . '#rs_global_reward_percent[type=text],#rs_global_referral_reward_percent[type=text],#rs_referral_cookies_expiry_in_days[type=text],'
                . '#rs_referral_link_limit[type=text],'
                . '#rs_referral_cookies_expiry_in_min[type=text],#rs_referral_cookies_expiry_in_hours[type=text],'
                . '#_rs_select_referral_points_referee_time_content[type=text],#rs_percent_max_redeem_discount[type=text],#rs_point_to_be_expire[type=number],'
                . '#rs_fixed_max_earn_points[type=text],#rs_percent_max_earn_points[type=text],#rs_reward_signup[type=text],'
                . '#rs_reward_product_review[type=text],#rs_referral_reward_signup[type=text],#rs_reward_points_for_login[type=text],'
                . '#rs_reward_user_role_administrator[type=text],#rs_reward_user_role_editor[type=text],#rs_reward_user_role_author[type=text],'
                . '#rs_reward_user_role_contributor[type=text],#rs_reward_user_role_subscriber[type=text],#rs_reward_user_role_customer[type=text],'
                . '#rs_reward_user_role_shop_manager[type=text],#rs_reward_addremove_points[type=text],#rs_percentage_cart_total_redeem[type=text],'
                . '#rs_first_time_minimum_user_points[type=text],#rs_minimum_user_points_to_redeem[type=text],#rs_minimum_redeeming_points[type=text],'
                . '#rs_maximum_redeeming_points[type=text],#rs_minimum_cart_total_points[type=text],#rs_percentage_cart_total_redeem_checkout[type=text],'
                . '#rs_local_reward_points[type=text],#rs_local_reward_percent[type=text],#rs_local_referral_reward_point[type=text],'
                . '#rs_local_referral_reward_percent[type=text],#rs_local_reward_points_facebook[type=text],#rs_local_reward_percent_facebook[type=text],'
                . '#rs_local_reward_points_twitter[type=text],#rs_local_reward_percent_twitter[type=text],#rs_local_reward_points_google[type=text],'
                . '#rs_local_reward_percent_google[type=text],#rs_local_reward_points_vk[type=text],#rs_local_reward_percent_vk[type=text],'
                . '#rs_global_social_facebook_reward_points[type=text],#rs_global_social_facebook_reward_percent[type=text],'
                . '#rs_global_social_twitter_reward_points[type=text],#rs_global_social_twitter_reward_percent[type=text],'
                . '#rs_global_social_google_reward_points[type=text],#rs_global_social_google_reward_percent[type=text],'
                . '#rs_global_social_vk_reward_points[type=text],#rs_global_social_vk_reward_percent[type=text],'
                . '#rs_global_social_facebook_reward_points_individual[type=text],#rs_global_social_facebook_reward_percent_individual[type=text],'
                . '#rs_global_social_twitter_reward_points_individual[type=text],#rs_global_social_twitter_reward_percent_individual[type=text],'
                . '#rs_global_social_google_reward_points_individual[type=text],#rs_global_social_google_reward_percent_individual[type=text],'
                . '#rs_global_social_vk_reward_points_individual[type=text],#rs_global_social_vk_reward_percent_individual[type=text],'
                . '#earningpoints[type=text],#rs_minimum_edit_userpoints[type=text],#rs_minimum_userpoints[type=text],#redeemingpoints[type=text],'
                . '#rs_mail_cron_time[type=text],#rs_point_voucher_reward_points[type=text],#rs_point_bulk_voucher_points[type=text],'
                . '#rs_minimum_points_encashing_request[type=text],#rs_maximum_points_encashing_request[type=text],#_reward_points[type=text],'
                . '#_reward_percent[type=text],#_referral_reward_points[type=text],#_referral_reward_percent[type=text],#rs_category_points[type=text],'
                . '#rs_category_percent[type=text],#referral_rs_category_points[type=text],#referral_rs_category_percent[type=text],'
                . '#social_facebook_rs_category_points[type=text],#social_facebook_rs_category_percent[type=text],'
                . '#social_twitter_rs_category_points[type=text],#social_twitter_rs_category_percent[type=text],#social_google_rs_category_points[type=text],'
                . '#social_google_rs_category_percent[type=text],#social_vk_rs_category_points[type=text],#social_vk_rs_category_percent[type=text]' ,
                'sumo_booking'         => $sumo_bookings_check ,
                    ) ;
            $deps            = array() ;
            wp_enqueue_script( 'adminscripts' , SRP_PLUGIN_URL . "/assets/js/adminscripts.js" , $deps , SRP_VERSION ) ;
            wp_localize_script( 'adminscripts' , 'adminscripts_params' , $localize_script ) ;
        }

        public static function srp_scripts() {
            wp_enqueue_script( 'srpscripts' , SRP_PLUGIN_DIR_URL . "assets/js/srpscripts.js" , array( 'jquery' ) , SRP_VERSION ) ;
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
