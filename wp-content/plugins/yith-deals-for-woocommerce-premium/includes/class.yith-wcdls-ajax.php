<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Deals for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_WCDLS_Ajax' ) ) {
    /**
     * YITH_WCDLS_Ajax
     *
     * @since 1.0.0
     */
    class YITH_WCDLS_Ajax
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCDLS_Ajax
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCDLS_Ajax
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {

        }
    }
}