<?php
!defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Privacy' ) ) {
    /**
     * Class YITH_WCMBS_Privacy
     * Privacy Class
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Privacy extends YITH_Privacy_Plugin_Abstract {

        /**
         * YITH_WCBK_Privacy constructor.
         */
        public function __construct() {
            parent::__construct( _x( 'YITH WooCommerce Membership', 'Privacy Policy Content', 'yith-woocommerce-membership' ) );
        }

        public function get_privacy_message( $section ) {
            $privacy_content_path = YITH_WCMBS_TEMPLATE_PATH . '/privacy/html-policy-content-' . $section . '.php';
            if ( file_exists( $privacy_content_path ) ) {
                ob_start();
                include $privacy_content_path;
                return ob_get_clean();
            }
            return '';
        }
    }
}

new YITH_WCMBS_Privacy();