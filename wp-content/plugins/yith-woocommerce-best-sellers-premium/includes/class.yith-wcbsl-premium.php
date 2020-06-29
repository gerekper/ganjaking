<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers Premium
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Premium' ) ) {
    /**
     * YITH WooCommerce Best Sellers Premium
     *
     * @since 1.0.0
     */
    class YITH_WCBSL_Premium extends YITH_WCBSL {

        /**
         * Single instance of the class
         *
         * @var YITH_WCBSL_Premium
         * @since 1.0.0
         */
        protected static $_instance;


        private $_bestseller_limit;

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        protected function __construct() {
            parent::__construct();

            YITH_WCBSL_WPML_Integration();
        }

        public function get_limit() {
            if ( empty( $this->_bestseller_limit ) ) {
                $this->_bestseller_limit = get_option( 'yith-wcbsl-bestsellers-limit', 100 );
            }

            return $this->_bestseller_limit;
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
            register_widget( 'YITH_WCBSL_Bestsellers_Categories_Widget' );
        }
    }
}

/**
 * Unique access to instance of YITH_WCBSL class
 *
 * @deprecated since 1.1.0 use YITH_WCBSL() instead
 * @return \YITH_WCBSL_Premium
 * @since 1.0.0
 */
function YITH_WCBSL_Premium() {
    return YITH_WCBSL();
}