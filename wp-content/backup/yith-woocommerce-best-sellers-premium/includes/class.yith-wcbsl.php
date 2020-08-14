<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL' ) ) {
    /**
     * YITH WooCommerce Best Sellers
     *
     * @since 1.0.0
     */
    class YITH_WCBSL {

        /**
         * Single instance of the class
         *
         * @var YITH_WCBSL
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCBSL_VERSION;

        /**
         * Plugin object
         *
         * @var string
         * @since 1.0.0
         */
        public $obj = null;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCBSL|YITH_WCBSL_Premium
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @return mixed| YITH_WCBSL_Admin | YITH_WCBSL_Frontend
         * @since 1.0.0
         */
        protected function __construct() {

            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            // Add widget for Best Sellers
            add_action( 'widgets_init', array( $this, 'register_widgets' ) );

            // Class admin
            if ( is_admin() ) {
                YITH_WCBSL_Admin();
            } // Class frontend
            else {
                YITH_WCBSL_Frontend();
            }
        }

        public function get_limit() {
            return 100;
        }

        /**
         * register Widget for Best Sellers
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_widgets() {
            register_widget( 'YITH_WCBSL_Bestsellers_Widget' );
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
 * Unique access to instance of YITH_WCBSL class
 *
 * @return YITH_WCBSL|YITH_WCBSL_Premium
 * @since 1.0.0
 */
function YITH_WCBSL() {
    return YITH_WCBSL::get_instance();
}