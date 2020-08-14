<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCCOS_Integrations
 * @since   1.1.6
 *
 */
class YITH_WCCOS_Integrations {

    /** @var \YITH_WCCOS_Integrations */
    private static $_instance;

    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    private function __construct() {
        $integrations = array( 'wpml', 'multi-vendor', 'order-tracking' );

        foreach ( $integrations as $integration ) {
            $file_url             = "class.yith-wccos-{$integration}-integration.php";
            $this->{$integration} = require_once( $file_url );
        }
    }

}

/**
 * Unique access to instance of YITH_WCCOS_Integrations class
 *
 * @return YITH_WCCOS_Integrations
 */
function YITH_WCCOS_Integrations() {
    return YITH_WCCOS_Integrations::get_instance();
}