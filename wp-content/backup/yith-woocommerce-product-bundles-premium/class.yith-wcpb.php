<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPB' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPB' ) ) {
    /**
     * YITH WooCommerce Product Bundles
     *
     * @since 1.0.0
     */
    class YITH_WCPB {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPB
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCPB_VERSION;

        /**
         * @var YITH_WCPB_Admin|YITH_WCPB_Admin_Premium
         */
        public $admin;

        /**
         * @var YITH_WCPB_Frontend|YITH_WCPB_Frontend_Premium
         */
        public $frontend;

        /**
         * @var YITH_WCPB_Compatibility|YITH_WCPB_Compatibility_Premium
         */
        public $compatibility;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCPB|YITH_WCPB_Premium
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        public function __construct() {

            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            // Class admin
            if ( is_admin() )
                $this->admin = YITH_WCPB_Admin();

            // Class frontend
            $this->frontend = YITH_WCPB_Frontend();

            $this->compatibility = YITH_WCPB_Compatibility::get_instance();
        }


        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( !empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCPB class
 *
 * @return YITH_WCPB|YITH_WCPB_Premium
 * @since 1.0.0
 */
function YITH_WCPB() {
    return YITH_WCPB::get_instance();
}