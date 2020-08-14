<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Bulk Edit Products
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBEP' ) ) {
    /**
     * YITH WooCommerce Bulk Edit Products
     *
     * @since 1.0.0
     */
    class YITH_WCBEP {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCBEP
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCBEP_VERSION;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCBEP|YITH_WCBEP_Premium
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @return mixed| YITH_WCBEP_Admin
         * @since 1.0.0
         */
        protected function __construct() {

            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            // Class admin
            if ( is_admin() ) {
                YITH_WCBEP_Admin();
            }
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
 * Unique access to instance of YITH_WCBEP class
 *
 * @return YITH_WCBEP|YITH_WCBEP_Premium
 * @since 1.0.0
 */
function YITH_WCBEP() {
    return YITH_WCBEP::get_instance();
}