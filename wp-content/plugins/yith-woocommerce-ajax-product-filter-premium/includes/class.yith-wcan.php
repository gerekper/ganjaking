<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN' ) ) {
    /**
     * YITH WooCommerce Ajax Navigation
     *
     * @since 1.0.0
     */
    class YITH_WCAN {
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
         * @var bool Check for old WooCommerce/WordPress Version
         * @since 3.0
         */
        public $current_wc_version  = false;
        public $is_wc_older_2_1     = false;
        public $is_wc_older_2_6     = false;
        public $is_wp_older_4_7     = false;

        /**
         * @var string filtered term fields
         * Before WooCommerce 2.6 product attribute use term_id for filter.
         * From WooCommerce 2.6 use slug instead.
         * @since 3.0.0
         */
        public $filter_term_field = 'slug';

        /**
         * Constructor
         *
         * @return mixed|YITH_WCAN_Admin|YITH_WCAN_Frontend
         * @since 1.0.0
         */
        public function __construct() {

            $this->version = YITH_WCAN_VERSION;
            
            /**
             * WooCommerce Version Check
             */
            $this->current_wc_version = WC()->version;
            $this->is_wc_older_2_1    = version_compare( $this->current_wc_version, '2.1', '<' );
            $this->is_wc_older_2_6    = version_compare( $this->current_wc_version, '2.6', '<' );
            
            /**
             * WordPress Version Check
             */
            global $wp_version;
            $this->is_wp_older_4_7 = version_compare( $wp_version, '4.7-RC1', '<' );

            if( $this->is_wc_older_2_6 ){
                $this->filter_term_field = 'term_id';
            }
            

            /* Load Plugin Framework */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* Register Widget */
            add_action( 'widgets_init', array( $this, 'registerWidgets' ) );

            $this->required();

            $this->init();

            // Support to Ultimate Member plugin
            if( class_exists( 'UM_API' ) ){
                add_action( 'init', array( $this, 'ultimate_member_support' ), 0 );
            }
        }

        /**
         * Load and register widgets
         *
         * @access public
         * @since  1.0.0
         */
        public function registerWidgets() {
            $widgets = apply_filters( 'yith_wcan_widgets', array(
                    'YITH_WCAN_Navigation_Widget',
                    'YITH_WCAN_Reset_Navigation_Widget',
                )
            );

            foreach( $widgets as $widget ){
                register_widget( $widget );
            }
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
		 * Main plugin Instance
		 *
		 * @return YITH_Vendors Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {

            if( class_exists( 'YITH_WCAN_Premium' ) ){
                //Premium Class
                if( is_null( YITH_WCAN_Premium::$_instance ) ){
                    YITH_WCAN_Premium::$_instance = new YITH_WCAN_Premium();
                }

                return YITH_WCAN_Premium::$_instance;
            }

            // Base Class
            else {
                 //Premium Class
                if( is_null( YITH_WCAN::$_instance ) ){
                    YITH_WCAN::$_instance = new YITH_WCAN();
                }

                return YITH_WCAN::$_instance;
            }
		}



        /**
         * Load required files
         *
         * @since 1.4
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function required(){
            $required = apply_filters( 'yith_wcan_required_files', array(
                    'includes/functions.yith-wcan.php',
                    'includes/class.yith-wcan-admin.php',
                    'includes/class.yith-wcan-frontend.php',
                    'widgets/class.yith-wcan-navigation-widget.php',
                    'widgets/class.yith-wcan-reset-navigation-widget.php',
                )
            );

            foreach( $required as $file ){
                file_exists( YITH_WCAN_DIR . $file ) && require_once( YITH_WCAN_DIR . $file );
            }
        }

        public function init() {
	        if ( is_admin() ) {
                $this->admin = new YITH_WCAN_Admin( $this->version );
            }
            else {
                $this->frontend = new YITH_WCAN_Frontend( $this->version );
            }
        }

        /**
         * Get choosen attribute args
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  2.9.3
         * @return array
         */
        public function get_layered_nav_chosen_attributes(){
            $chosen_attributes = array();
            if( $this->is_wc_older_2_6 ){
                global $_chosen_attributes;
                $chosen_attributes = $_chosen_attributes;
            }

            else {
                $chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
            }
            return $chosen_attributes;
        }

        /**
         * Support to ultimate members functions
         * 
         * The method set_predefined_fields call a WP_Query that generate
         * an issue with shop filtered query. Move this step to init with priority 2 
         * instead of 1
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  3.0.9
         * @return void
         */
        public function ultimate_member_support(){
            global $ultimatemember;
            if( $ultimatemember ){
                remove_action('init',  array($ultimatemember->builtin, 'set_predefined_fields'), 1);
                add_action('init',  array($ultimatemember->builtin, 'set_predefined_fields'), 2);
            }
        }

    }
}