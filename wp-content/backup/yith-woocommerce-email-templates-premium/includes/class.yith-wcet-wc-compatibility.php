<?php
/**
 * Email Template Helper
 *
 * @author  Yithemes
 * @package YITH WooCommerce Email Templates
 * @version 1.2.0
 */

defined( 'YITH_WCET' ) || exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCET_WC_Compatibility' ) ) {
    /**
     * YITH_WCET_WC_Compatibility class.
     * The class manage all the admin behaviors.
     *
     * @since    1.2.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCET_WC_Compatibility {

        /**
         * Single instance of the class
         *
         * @var YITH_WCET_WC_Compatibility
         * @since 1.2.0
         */
        protected static $instance;

        public $templates;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCET_WC_Compatibility
         * @since 1.2.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @access public
         */
        public function __construct() {
            add_filter( 'yith_wcet_templates', array( $this, 'get_templates' ) );

            /* for retrocompatibility with YITH Plugins */
            add_action( 'yith_wcet_email_header', array( $this, 'yith_wcet_email_header' ), 10, 2 );
            add_action( 'yith_wcet_email_footer', array( $this, 'yith_wcet_email_footer' ) );
        }

        public function yith_wcet_email_header( $email_heading, $mail_type ) {
            do_action( 'woocommerce_email_header', $email_heading, $mail_type );
        }

        public function yith_wcet_email_footer( $mail_type ) {
            do_action( 'woocommerce_email_footer', $mail_type );
        }

        public function get_templates( $templates ) {
            if ( version_compare( WC()->version, '2.5.0', '<' ) ) {
                $old_templates = array(
                    'emails/admin-cancelled-order.php',
                    'emails/admin-new-order.php',
                    'emails/customer-completed-order.php',
                    'emails/customer-invoice.php',
                    'emails/customer-new-account.php',
                    'emails/customer-note.php',
                    'emails/customer-processing-order.php',
                    'emails/customer-reset-password.php',
                    'emails/email-addresses.php',
                );

                $templates = array_unique( array_merge( $templates, $old_templates ) );
            }

            return $templates;
        }
    }
}

/**
 * Unique access to instance of YITH_WCET_WC_Compatibility class
 *
 * @return YITH_WCET_WC_Compatibility
 *
 * @since 1.2.0
 */
function YITH_WCET_WC_Compatibility() {
    return YITH_WCET_WC_Compatibility::get_instance();
}
