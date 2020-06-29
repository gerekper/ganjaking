<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WAPO' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WAPO' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation
     *
     * @since 1.0.0
     */
    class YITH_WAPO {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version;

        /**
         * Frontend object
         *
         * @var string
         * @since 1.0.0
         */
        public $frontend = null;

        /**
         * Admin object
         *
         * @var string
         * @since 1.0.0
         */
        public $admin = null;

        /**
         * Main instance
         *
         * @var string
         * @since 1.4.0
         */
        protected static $_instance = null;


        /**
         * Check if YITH Multi Vendor is installed
         *
         * @var boolean
         * @since 1.0.0
         */
        public static $is_vendor_installed;

        /**
         * Check if WPML is installed
         *
         * @var boolean
         * @since 1.0.0
         */
        public static $is_wpml_installed;


        /**
         * Constructor
         *
         * @return mixed|YITH_WAPO_Admin|YITH_WAPO_Frontend
         * @since 1.0.0
         */
        public function __construct() {

            $this->version = YITH_WAPO_VERSION;

            YITH_WAPO::$is_vendor_installed = function_exists('YITH_Vendors');

            global $sitepress;
            YITH_WAPO::$is_wpml_installed = ! empty( $sitepress );

            /* Load Plugin Framework */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
            add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

            $this->create_tables();
            $this->required();
            $this->init();

            add_filter( 'sanitize_text_field', array( $this, 'my_sanitize_text_field' ), 10, 2 );

            // Prevent skip add-ons with values already in the product details area of the product name
            add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

            // Divi ET Builder Module integration
            add_action( 'et_builder_ready', array( $this, 'divi_et_builder_module_integration' ) );

        }

        /**
         * Fix: Uploaded files link in order details
         */
        function my_sanitize_text_field( $filtered, $str ) {
            if ( is_string( $str ) && strpos( $str, 'uploads' ) ) { return $str; }
            else { return $filtered; }
        }

        /**
		 * Load plugin framework
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
		}
        
        /**
         * Load Privacy
         */  
        function load_privacy() {
            require_once( YITH_WAPO_DIR . 'includes/classes/class.yith-wapo-privacy.php' );
        }

        /**
		 * Main plugin Instance
		 *
		 * @return YITH_WAPO Main instance
		 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
		 */
		public static function instance() {

            if( is_null( YITH_WAPO::$_instance ) ){
                YITH_WAPO::$_instance = new YITH_WAPO();
            }

            return YITH_WAPO::$_instance;
		}

        public static function create_tables() {

            $yith_wapo_db_version = apply_filters( 'yith_wapo_db_version', get_option( 'yith_wapo_db_version' ) );

            if ( YITH_WAPO_DB_VERSION != $yith_wapo_db_version ) {

                YITH_WAPO_Group::create_tables();
                YITH_WAPO_Type::create_tables();
                update_option( 'yith_wapo_db_version', YITH_WAPO_DB_VERSION );

            }
            
        }


        /**
         * Load required files
         *
         * @since 1.4
         * @return void
         * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
         */
        public function required(){
            $required = apply_filters( 'yith_wapo_required_files',
                array(
                    'includes/classes/class.yith-wapo-admin.php',
                    'includes/classes/class.yith-wapo-frontend.php',
                    'includes/functions/yith-wapo-database.php'
                )
            );

            if( YITH_WAPO::$is_wpml_installed ) {
                $required[] = 'includes/classes/class.yith-wapo-wpml.php';
            }

            foreach( $required as $file ){
                file_exists( YITH_WAPO_DIR . $file ) && require_once( YITH_WAPO_DIR . $file );
            }
        }

        public function init() {

            if ( is_admin() ) {
                $this->admin = new YITH_WAPO_Admin( $this->version );
            }

            $this->frontend = new YITH_WAPO_Frontend( $this->version );

        }

        /**
         * @return bool
         */
        private function is_quick_view() {
            return ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'yit_load_product_quick_view' || $_REQUEST['action'] == 'yith_load_product_quick_view'  || $_REQUEST['action'] == 'ux_quickview' ) ) ? true : false;
        }

        /**
         * @return mixed|void
         */
        public static function getAllowedProductTypes() {

            return apply_filters( 'yith_wapo_product_type_list', array( 'simple', 'variable', 'grouped', 'bundle', 'booking', 'subscription', 'variable-subscription' ) );

        }

        /**
         * @return null|YITH_Vendor
         */
        public static function get_current_multivendor() {

            if ( YITH_WAPO::$is_vendor_installed && is_user_logged_in() ) {

                $vendor = yith_get_vendor( 'current', 'user' );

                if ( $vendor->is_valid() ) {
                    return $vendor;
                }

            }

            return null;
        }

        /**
         * @param $id
         * @param string $obj
         * @return null|YITH_Vendor
         */
        public static function get_multivendor_by_id( $id , $obj='vendor' ) {

            if( YITH_WAPO::$is_vendor_installed ) {

                $vendor = yith_get_vendor( $id, $obj );

                if( $vendor->is_valid() ) {
                    return $vendor;
                }

            }

            return null;
        }

        /**
         * @return bool
         */
        public static function is_plugin_enabled_for_vendors() {
            return get_option('yith_wpv_vendors_option_advanced_product_options_management') == 'yes';
        }

        function divi_et_builder_module_integration() {
            if ( class_exists( 'ET_Builder_Module' ) ) {
                include YITH_WAPO_DIR . 'includes/integrations/class.divi-et-builder_module.php';
            }
        }

    }
}
