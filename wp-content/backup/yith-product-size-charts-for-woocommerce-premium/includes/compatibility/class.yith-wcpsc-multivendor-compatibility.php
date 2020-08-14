<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Multivendor Compatibility Class
 *
 * @class   YITH_WCPSC_Multivendor_Compatibility
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCPSC_Multivendor_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPSC_Multivendor_Compatibility
     * @since 1.0.0
     */
    private static $_instance;

    /**
     * @var string The vendor taxonomy name
     */
    protected $_vendor_taxonomy_name = '';

    private $_size_charts_post_type = 'yith-wcpsc-wc-chart';

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPSC_Multivendor_Compatibility
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
        if ( !YITH_WCPSC_Compatibility::has_plugin( 'multi-vendor' ) ) {
            return;
        }

        add_action( 'add_meta_boxes', array( $this, 'manage_metaboxes' ), 11 );
    }

    /**
     * check if vendors can manage Size Charts Plugin
     *
     * @return   bool
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0
     */
    public function is_enabled_management_for_vendors() {
        return 'yes' == get_option( 'yith_wpv_vendors_option_size_charts_management', 'no' );
    }

    public function manage_metaboxes() {
        if ( !$this->is_enabled_management_for_vendors() ) {
            $vendor = yith_get_vendor( 'current', 'user' );

            if ( $vendor->is_valid() && $vendor->has_limited_access() && !current_user_can( 'manage_users' ) ) {
                remove_meta_box( 'yith-wcpsc-product-size-charts-metabox', null, 'normal' );
            }
        }
    }

}

/**
 * Unique access to instance of YITH_WCPSC_Multivendor_Compatibility class
 *
 * @return YITH_WCPSC_Multivendor_Compatibility
 * @since 1.0.0
 */
function YITH_WCPSC_Multivendor_Compatibility() {
    return YITH_WCPSC_Multivendor_Compatibility::get_instance();
}

YITH_WCPSC_Multivendor_Compatibility();