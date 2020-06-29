<?php
/**
 * Main class
 *
 * @author Yithemes
 * @package YITH WooCommerce Custom Order Status
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCCOS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCCOS' ) ) {
    /**
     * YITH WooCommerce Custom Order Status
     *
     * @since 1.0.0
     */
    class YITH_WCCOS {

        /**
         * Single instance of the class
         *
         * @var YITH_WCCOS
         * @since 1.0.0
         */
        protected static $_instance;

        /** @var YITH_WCCOS_Admin|YITH_WCCOS_Admin_Premium */
        public $admin;

        /** @var YITH_WCCOS_Frontend|YITH_WCCOS_Frontend_Premium */
        public $frontend;


        /**
         * Returns single instance of the class
         *
         * @return YITH_WCCOS|YITH_WCCOS_Premium
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @return YITH_WCCOS
         * @since 1.0.0
         */
        protected function __construct() {

            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            $this->admin    = YITH_WCCOS_Admin();
            $this->frontend = YITH_WCCOS_Frontend();
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
 * Unique access to instance of YITH_WCCOS class
 *
 * @return YITH_WCCOS|YITH_WCCOS_Premium
 * @since 1.0.0
 */
function YITH_WCCOS() {
    return YITH_WCCOS::get_instance();
}