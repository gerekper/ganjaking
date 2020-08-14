<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCCOS_PREMIUM' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Custom Order Status
 *
 * @class   YITH_WCCOS_Premium
 * @package YITH WooCommerce Custom Order Status
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'YITH_WCCOS_Premium' ) ) {
    /**
     * YITH WooCommerce Custom Order Status
     *
     * @since 1.0.0
     */
    class YITH_WCCOS_Premium extends YITH_WCCOS {
        /**
         * Single instance of the class
         *
         * @var YITH_WCCOS_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @return YITH_WCCOS_Premium
         * @since 1.0.0
         */
        protected function __construct() {

            parent::__construct();

            YITH_WCCOS_Integrations();

            YITH_WCCOS_Updates::get_instance();

            add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
        }

        /**
         * add email classes to WooCommerce
         *
         * @param array $emails
         *
         * @return array
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_email_classes( $emails ) {
            $emails[ 'YITH_WCCOS_Email' ] = include( YITH_WCCOS_DIR . '/includes/class.yith-wccos-email.php' );

            return $emails;
        }
    }
}

/**
 * Unique access to instance of YITH_WCCOS_Premium class
 *
 * @deprecated since 1.1.0 use YITH_WCCOS() instead
 * @return YITH_WCCOS_Premium
 * @since 1.0.0
 */
function YITH_WCCOS_Premium() {
    return YITH_WCCOS();
}