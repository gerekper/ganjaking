<?php
/**
 * Admin class for messages
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Notifier' ) ) {
    /**
     * Notifier class.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Notifier {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Notifier
         * @since 1.0.0
         */
        private static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Messages_Manager_Admin
         * @since 1.0.0
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        private function __construct() {
            add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
            add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
        }


        /**
         * Loads WC Mailer
         *
         * @return void
         * @since 1.0
         */
        public function load_wc_mailer() {
            add_action( 'yith_wcmbs_new_member_notification', array( 'WC_Emails', 'send_transactional_email' ), 10 );
            add_action( 'yith_wcmbs_membership_expiring_notification', array( 'WC_Emails', 'send_transactional_email' ), 10 );
            add_action( 'yith_wcmbs_membership_cancelled_notification', array( 'WC_Emails', 'send_transactional_email' ), 10 );
            add_action( 'yith_wcmbs_membership_expired_notification', array( 'WC_Emails', 'send_transactional_email' ), 10 );
        }

        /**
         * add email classes to woocommerce
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
			require_once YITH_WCMBS_DIR . '/includes/emails/abstract.yith-wcmbs-email.php';
            $emails[ 'YITH_WCMBS_Welcome_Mail' ]   = include YITH_WCMBS_DIR . '/includes/emails/class.yith-wcmbs-welcome-email.php';
            $emails[ 'YITH_WCMBS_Cancelled_Mail' ] = include YITH_WCMBS_DIR . '/includes/emails/class.yith-wcmbs-cancelled-email.php';
            $emails[ 'YITH_WCMBS_Expiring_Mail' ]  = include YITH_WCMBS_DIR . '/includes/emails/class.yith-wcmbs-expiring-email.php';
            $emails[ 'YITH_WCMBS_Expired_Mail' ]  = include YITH_WCMBS_DIR . '/includes/emails/class.yith-wcmbs-expired-email.php';

            return $emails;
        }

    }
}

/**
 * Unique access to instance of YITH_WCMBS_Notifier class
 *
 * @return \YITH_WCMBS_Notifier
 * @since 1.0.0
 */
function YITH_WCMBS_Notifier() {
    return YITH_WCMBS_Notifier::get_instance();
}