<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager' ) ){

    class YITH_Frontend_Manager{

        /**
         * Main Instance
         *
         * @var string
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main Admin Instance
         *
         * @var YITH_Frontend_Manager_Admin | YITH_Frontend_Manager_Admin_Premium
         * @since 1.0
         */
        public $backend = null;

        /**
         * Main GUI Instance
         *
         * @var YITH_Frontend_Manager_GUI | YITH_Frontend_Manager_GUI_Premium
         * @since 1.0
         */
        public $gui = null;

        /**
         * YITH Multi Vendor Integration
         *
         * @var mixed
         * @since 1.0
         */
        public $module_object = array();

        /**
         * Check if the plugin works on frontend or backend
         *
         * @var boolean
         * @since 1.0
         */
        public $is_admin;

        /**
         * Available Section
         *
         * @var mixed|Array
         * @since 1.0
         */
        public $available_sections = array();

        /**
         * Sections array
         *
         * @var array|mixed|void
         * @since 1.0.0.
         */
        protected $_sections = array();

        /**
         * Modules array
         *
         * @var array|mixed|void
         * @since 1.0.0.
         */
        protected $_modules = array();

        /**
         * Modules array
         *
         * @var array|mixed|void
         * @since 1.0.0.
         */
        public $third_party_modules = array();

        /**
         * Refresh Rewrite Rules Transient name
         *
         * @var string
         * @since 1.0
         */
        protected $_rewrite_rules_transient;

        /**
         * Check if WooCommerce run version 3 or greather
         *
         * @var string
         * @since 1.0.0
         */
        public $is_wc_3_0_or_greather;

	    /**
	     * Check if WooCommerce run version 3.2 or greather
	     *
	     * @var string
	     * @since 1.1.1
	     */
	    public $is_wc_3_2_or_greather;

	    /**
	     * Check if WooCommerce run version 3.2 or lower
	     *
	     * @var string
	     * @since 1.1.1
	     */
	    public $is_wc_3_2_or_lower;

	    /**
	     * Check if WooCommerce run version 3.3 or greather
	     *
	     * @var string
	     * @since 1.1.1
	     */
	    public $is_wc_3_3_or_greather;

	    /**
	     * Usr access capability
	     *
	     * @var string
	     * @since 1.2.2
	     */
	    public $access_capability = 'manage_woocommerce';

        /**
         * Construct
         */
        public function __construct() {
            $this->_rewrite_rules_transient = yith_wcfm_get_rewrite_rules_transient();
            $this->is_admin = is_admin() && ! wp_doing_ajax();
            $this->is_wc_3_0_or_greather = version_compare( WC()->version, '3.0', '>=' );
            $this->is_wc_3_2_or_greather = version_compare( WC()->version, '3.2', '>=' );
            $this->is_wc_3_2_or_lower = version_compare( WC()->version, '3.2', '<=' );
            $this->is_wc_3_3_or_greather = version_compare( WC()->version, '3.3', '>=' );

            /* === Classes Require === */
            $classes = apply_filters('yith_wcfm_required_classes', array(
                    'common' => array(
                        YITH_WCFM_CLASS_PATH . 'class.yith-frontend-manager-section.php',
                        YITH_WCFM_CLASS_PATH . 'class.yith-frontend-manager-media.php'
                    ),

                    'gui' => array(
                        YITH_WCFM_CLASS_PATH . 'class.yith-frontend-manager-gui.php'
                    ),

                    'backend' => array(
                        YITH_WCFM_CLASS_PATH . 'class.yith-frontend-manager-admin.php',
	                    YITH_WCFM_CLASS_PATH . 'functions.yith-frontend-manager-updates.php'
                    ),
                )
            );

            $this->_modules = array(
                /* === YITH WooCommerce Multi Vendor === */
                'YITH_Vendors' => array(
                    'class_path'             => YITH_WCFM_CLASS_PATH . 'module/multi-vendor/module.yith-multi-vendor.php',
                    'class_name'             => 'YITH_Frontend_Manager_For_Vendor',
                    'has_singleton_function' => true,
                    'context'                => 'common',
	                'check_if_exists'        => 'YITH_Vendors_Premium'
                ),

	            /* === YITH WooCommerce Audio and Video Content === */
                'YITH_WC_Audio_Video' => array(
	                'class_path'             => YITH_WCFM_CLASS_PATH . 'module/featured-audio-and-video/module.yith-featured-audio-and-video-content.php',
	                'class_name'             => 'YITH_Frontend_Manager_For_Featured_Audio_Video',
	                'has_singleton_function' => true,
	                'context'                => 'common',
                ),

	            /* === YITH WooCommerce Tab Manager === */
                'YITH_WC_Tab_Manager' => array(
                    'class_path'             => YITH_WCFM_CLASS_PATH . 'module/tab-manager/module.yith-tab-manager.php',
                    'class_name'             => 'YITH_Frontend_Manager_For_Tab_Manager',
                    'has_singleton_function' => true,
                    'context'                => 'frontend',
                    'check_if_exists'        => 'YWTM_Product_Tab'
                ),
                /* === YITH WooCommerce Tab Manager === */
                'YITH_WC_Name_Your_Price' => array(
                    'class_path'             => YITH_WCFM_CLASS_PATH . 'module/name-your-price/module.yith-name-your-price.php',
                    'class_name'             => 'YITH_Frontend_Manager_For_Name_Your_Price',
                    'has_singleton_function' => true,
                    'context'                => 'frontend',
                    'check_if_exists'        => 'YITH_WC_Name_Your_Price_Premium_Admin'
                ),

                /* === YITH Live Chat === */
                'YITH_Live_Chat' => array(
	                'class_path'             => YITH_WCFM_CLASS_PATH . 'module/live-chat/module.yith-live-chat.php',
	                'class_name'             => 'YITH_Frontend_Manager_Live_Chat',
	                'has_singleton_function' => true,
	                'context'                => 'common',
	                'check_if_exists'        => 'YITH_Livechat_Premium'
	            ),

                /* === YITH SMS_Notifications === */
                'YITH_WC_SMS_Notifications' => array(
	                'class_path'             => YITH_WCFM_CLASS_PATH . 'module/sms-notifications/module.yith-sms-notifications.php',
	                'class_name'             => 'YITH_Frontend_Manager_SMS_Notifications',
	                'has_singleton_function' => true,
	                'context'                => 'common',
	            ),

                 /* === YITH Auctions for WooCommerce === */
                'YITH_Auctions' => array(
                    'class_path'             => YITH_WCFM_CLASS_PATH . 'module/auctions/module.yith-auctions.php',
                    'class_name'             => 'YITH_WCFM_Auctions',
                    'has_singleton_function' => true,
                    'context'                => 'common',
                    'check_if_exists'        => 'YITH_Auctions_Premium'
                ),

	            /* === YITH WooCommerce Order Tracking === */
                'YITH_WooCommerce_Order_Tracking' => array(
	                'class_path'             => YITH_WCFM_CLASS_PATH . 'module/order-tracking/module.yith-order-tracking.php',
	                'class_name'             => 'YITH_Frontend_Manager_Order_Tracking',
	                'has_singleton_function' => true,
	                'context'                => 'frontend',
	                'check_if_exists'        => 'YITH_WooCommerce_Order_Tracking'
                ),
            );

			/**
			 * APPLY_FILTERS: yith_wcdm_third_party_modules
			 *
			 * Filter third party modules
			 *
			 * @param array $third_party_modules Third party modules.
			 *
			 * @return array
			 */
	        $this->third_party_modules = apply_filters( 'yith_wcdm_third_party_modules', $this->third_party_modules );
			/**
			 * APPLY_FILTERS: yith_wcfm_section_files
			 *
			 * Filter section files
			 *
			 * @param array $section_files The section files.
			 *
			 * @return array
			 */
	        $this->available_sections = apply_filters( 'yith_wcfm_section_files', $this->_get_section_files() );

            $skip_context = $this->is_admin ? 'gui' : 'backend';
            foreach( $classes as $context => $required ){
                if( $skip_context != $context ){
                    foreach( $required as $class ){
                        require_once( $class );
                    }
                }
            }
			/**
			 * DO_ACTION: yith_wcfm_after_load_common_classes
			 *
			 * Hook after load common classes.
			 *
			 */
            do_action( 'yith_wcfm_after_load_common_classes' );

            /* Load specific section class */
            foreach ( $this->available_sections as $class_name => $class_path ) {
               require_once( $class_path );
             }

            /* === Load YITH Plugin Framework === */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
            add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

            /* === Load Module === */
            add_action( 'init', array( $this, 'load_module' ), 15 );

            /* === Plugin Initializzation === */
            add_action( 'init', array( $this, 'init' ), 16 );

	        add_action( 'wp_ajax_yith_wcfm_flush_rewrite_rules', 'YITH_Frontend_Manager::regenerate_transient_rewrite_rule_transient' );
        }

        /**
         * Install sections
         *
         * @author YITH <plugins@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        protected function _install_sections(){
            $sections = array_keys( YITH_Frontend_Manager()->available_sections );

            if( $sections ){
                foreach( $sections as $section ){
                    $section_obj = new $section();
                    $this->_sections[ $section_obj->get_id() ] = $section_obj;
                }
            }
        }

        /**
         * Get section files
         *
         * @since  1.0
         * @access protected
         * @return array sections file to include
         */
        protected function _get_section_files(){
            $sections = array();
            foreach ( new DirectoryIterator( YITH_WCFM_SECTIONS_CLASS_PATH ) as $fileInfo ) {
                if( ! $fileInfo->isDot() && $fileInfo->isFile() ){
                    $fileName = $fileInfo->getFilename();
                    $className = ucwords( str_replace( array( 'class.', '.php', '-', 'yith' ), array( '', '', '_', 'YITH' ), $fileName ), '_' );

                    $sections[$className] = $fileInfo->getPathname();
                }
            }

            ksort( $sections );
            return $sections;
        }


        /**
         * Plugin Initializzation
         *
         * @since  1.0
         * @return void
         */
        public function init(){
            $this->_install_sections();

            if ( $this->is_admin ) {
                $this->backend = new YITH_Frontend_Manager_Admin();
            }

            else {
                $this->gui = new YITH_Frontend_Manager_GUI();
            }
        }

        /**
         * Load Module
         *
         * @since  1.0
         * @return void
         */
        public function load_module(){

            $modules = $this->_modules;

            if( ! empty( $this->third_party_modules ) ){
                $modules = array_merge( $modules, $this->third_party_modules );
            }

            $skip_context = $this->is_admin ? 'gui' : 'backend';

	        $modules = apply_filters( 'yith_wcfm_module_files', $modules );

            foreach ( $modules as $plugin_main_class => $module_information ){
                if( ! empty( $module_information[ 'context' ] ) && $skip_context == $module_information['context'] ) {
                    continue;
                }

                $check_if_exists = ! empty( $module_information['check_if_exists'] ) ? $module_information['check_if_exists'] : $plugin_main_class;

                if( class_exists( $check_if_exists ) ){
                    file_exists( $module_information['class_path'] ) && require_once( $module_information['class_path'] );
                    if( class_exists( $module_information['class_name'] ) ) {
                        if( ! empty( $module_information['has_singleton_function'] ) ){
                            $module_information['class_name']();
                        }

                        else {
                            new $module_information['class_name']();
                        }
                    }
                }
            }
        }

        /**
         * Load plugin framework
         *
         * @since  1.0
         * @return void
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( ! empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Declare support for WooCommerce features.
         *
         * @since 1.27.0
         */
        public function declare_wc_features_support() {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                $init = defined( 'YITH_WCFM_INIT' ) ? YITH_WCFM_INIT : false;
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $init, true );
            }
        }

        /**
         * Main plugin Instance
         *
         * @return YITH_Frontend_Manager|YITH_Frontend_Manager_Premium Main instance
         */
        public static function instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Get rewrite rules transient name
         *
         * @return string transient name
         */
        public function get_rewrite_rules_transient(){
            return $this->_rewrite_rules_transient;
        }

        /**
         * Return section array
         *
         * @return array!mixed
         */
        public function get_section(){
            return $this->_sections;
        }

        /**
         * check if this is free or premium version of YITH WCFM
         *
         * @since 1.0
         * @return bool true for free, false otherwise
         *
         */
        public function is_free(){
            return true;
        }

        /**
         * Force to regenerate rewrite rules
         *
         * @since 1.0
         * @return void
         */
        public static function regenerate_transient_rewrite_rule_transient(){
            set_site_transient( YITH_Frontend_Manager()->get_rewrite_rules_transient(), true );
            $current_action = current_action();

			/**
			 * APPLY_FILTERS: yith_wcfm_flush_rewrite_rules_send_die_if_ajax
			 *
			 * flush rewrite rules if ajax.
			 *
			 * @param bool $if_ajax Return true if doing ajax and $current_action is not yith_wpv_after_save_taxonomy.
			 * @param string  $current_action The current action.
			 * @return bool
			 */
            if( apply_filters( 'yith_wcfm_flush_rewrite_rules_send_die_if_ajax', wp_doing_ajax() && 'yith_wpv_after_save_taxonomy' != $current_action, $current_action ) ){
                wp_send_json( true, 200 );
            }
        }

	    /**
	     * Wrap for current_user_can( 'manage_woocommerce' )
	     *
	     * @since 1.2.2
	     *
	     * @return bool
	     */
	    public function current_user_can_manage_woocommerce_on_front(){
			/**
			 * APPLY_FILTERS: yith_wcfm_access_capability
			 *
			 * Filter capability for see if current user can manage WooCommerce on front.
			 *
			 * @param string $access_capability capability
			 * @param int  $user_id The current user id.
			 * @return string
			 */
		    return current_user_can( apply_filters( 'yith_wcfm_access_capability', $this->access_capability, get_current_user_id() ) );
	    }

	    /**
	     * Register a WPML string
	     *
	     * @since 2.0.0
	     * @param string $key
	     * @param string $value
	     * @return void
	     */
	    public function register_string_wpml( $key, $default_value = false ){
	    	$value = get_option( $key, $default_value );
		    do_action( 'wpml_register_single_string', YITH_WCFM_SLUG, "admin_text_{$key}", $value );
	    }

	    /**
	     * Get a WPML translated string
	     *
	     * @since 2.0.0
	     * @param string $key
	     * @param string $value
	     * @return void
	     */
	    public function get_string_wpml( $key, $value ){
		    $localized_label = apply_filters( 'wpml_translate_single_string', $value, YITH_WCFM_SLUG, "admin_text_{$key}" );
		    return $localized_label;
	    }
    }
}
