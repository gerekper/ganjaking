<?php
!defined( 'ABSPATH' ) && exit;  // Exit if accessed directly

/**
 * YITH WooCommerce Multi Vendor Compatibility Class
 *
 * @class   YITH_WCPB_Role_Based_Compatibility
 * @package Yithemes
 * @since   1.2.18
 * @author  Yithemes
 */
class YITH_WCPB_Multi_Vendor_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Multi_Vendor_Compatibility
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Multi_Vendor_Compatibility
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
        add_filter( 'yith_wcpb_select_product_box_args', array( $this, 'filter_products' ) );
    }

    /**
     * Filter args of the product select box to show only vendor products
     *
     * @param array $args
     * @return array
     */
    public function filter_products( $args ) {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( $vendor->is_valid() && $vendor->has_limited_access() ) {

            $args[ 'tax_query' ][] = array(
                'taxonomy' => $vendor->term->taxonomy,
                'field'    => 'slug',
                'terms'    => $vendor->slug,
            );

        }

        return $args;
    }

}

/**
 * Unique access to instance of YITH_WCPB_Multi_Vendor_Compatibility class
 *
 * @return YITH_WCPB_Multi_Vendor_Compatibility
 */
function YITH_WCPB_Multi_Vendor_Compatibility() {
    return YITH_WCPB_Multi_Vendor_Compatibility::get_instance();
}