<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Custom Order Status
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCCOS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCCOS_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since 1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCCOS_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCCOS_Frontend
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @access public
         * @since 1.0.0
         */
        protected function __construct() {
        }

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCCOS_Frontend
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }
    }
}
/**
 * Unique access to instance of YITH_WCCOS_Frontend class
 *
 * @return YITH_WCCOS_Frontend|YITH_WCCOS_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WCCOS_Frontend() {
    return YITH_WCCOS_Frontend::get_instance();
}