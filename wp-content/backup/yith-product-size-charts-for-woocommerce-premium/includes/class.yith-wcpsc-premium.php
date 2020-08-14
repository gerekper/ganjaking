<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Premium' ) ) {
    /**
     * YITH Product Size Charts for WooCommerce Premium
     *
     * @since 1.0.0
     */
    class YITH_WCPSC_Premium extends YITH_WCPSC {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @return mixed| YITH_WCPSC_Admin | YITH_WCPSC_Frontend
         * @since 1.0.0
         */
        public function __construct() {
            parent::__construct();

            YITH_WCPSC_Compatibility();
        }
    }
}

/**
 * Unique access to instance of YITH_WCPSC_Premium class
 *
 * @deprecated since 1.1.0 use YITH_WCPSC() instead
 * @return YITH_WCPSC_Premium
 * @since      1.0.0
 */
function YITH_WCPSC_Premium() {
    return YITH_WCPSC();
}