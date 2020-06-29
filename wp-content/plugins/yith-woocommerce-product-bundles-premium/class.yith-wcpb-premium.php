<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCPB_PREMIUM' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Product Bundles
 *
 * @class   YITH_WCPB_Premium
 * @package YITH WooCommerce Product Bundles
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! class_exists( 'YITH_WCPB_Premium' ) ) {
    /**
     * YITH WooCommerce Product Bundles
     *
     * @since 1.0.0
     */
    class YITH_WCPB_Premium extends YITH_WCPB {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPB_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        public function __construct() {
            parent::__construct();
        }
    }
}

/**
 * Unique access to instance of YITH_WCPB_Premium class
 *
 * @deprecated since 1.2.0 use YITH_WCPB instead
 * @return YITH_WCPB_Premium
 * @since 1.0.0
 */
function YITH_WCPB_Premium(){
    return YITH_WCPB();
}