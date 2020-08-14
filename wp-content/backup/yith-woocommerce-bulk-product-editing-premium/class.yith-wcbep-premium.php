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

if ( !class_exists( 'YITH_WCBEP_Premium' ) ) {
    /**
     * YITH WooCommerce Bulk Edit Products PREMIUM
     *
     * @since 1.0.0
     */
    class YITH_WCBEP_Premium extends YITH_WCBEP {

        /**
         * @var YITH_WCBEP_Compatibility
         */
        public $compatibility;

        /**
         * Constructor
         *
         * @return mixed| YITH_WCBEP_Admin
         * @since 1.0.0
         */
        protected function __construct() {
            parent::__construct();

            if ( is_admin() ) {
                /**
                 * load the compatibility class at plugins_loaded -> priority 15
                 * to make sure all yith plugins are already loaded
                 * @since  1.1.25
                 */
                add_action( 'plugins_loaded', array( $this, 'load_compatibility_class' ), 15 );
            }
        }


        /**
         * Load the compatibility class
         * @since  1.1.25
         */
        public function load_compatibility_class(){
            $this->compatibility = YITH_WCBEP_Compatibility();
        }
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Premium class
 *
 * @deprecated since 1.2.1 use YITH_WCBEP() instead
 * @return YITH_WCBEP_Premium
 * @since 1.0.0
 */
function YITH_WCBEP_Premium() {
    return YITH_WCBEP();
}