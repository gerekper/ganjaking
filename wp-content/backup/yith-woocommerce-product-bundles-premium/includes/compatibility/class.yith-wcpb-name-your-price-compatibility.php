<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * YITH WooCommerce Name Your Price Compatibility Class
 *
 * @class   YITH_WCPB_Name_Your_Price_Compatibility
 * @package Yithemes
 * @since   1.2.20
 * @author  Yithemes
 */
class YITH_WCPB_Name_Your_Price_Compatibility {

    /** @var YITH_WCPB_Name_Your_Price_Compatibility */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Name_Your_Price_Compatibility
     */
    public static function get_instance() {
        return !is_null( self::$instance ) ? self::$instance : self::$instance = new self;
    }

    /**
     * YITH_WCPB_Name_Your_Price_Compatibility constructor.
     */
    protected function __construct() {
        add_filter( 'ywcnp_product_types', array( $this, 'add_bundle_type' ) );
        add_filter( 'ywcnp_is_name_your_price', array( $this, 'is_name_your_price' ), 10, 2 );
    }

    /**
     * @param array $types
     * @return array
     */
    public function add_bundle_type( $types ) {
        $types[] = 'yith_bundle';
        return $types;
    }

    /**
     * @param bool                   $result
     * @param WC_Product_Yith_Bundle $product
     * @return mixed
     */
    public function is_name_your_price( $result, $product ) {
        if ( $product && $product->is_type( 'yith_bundle' ) ) {
            $result = !$product->per_items_pricing && $product->get_meta( '_is_nameyourprice' );
        }

        return $result;
    }
}

/**
 * Unique access to instance of YITH_WCPB_Name_Your_Price_Compatibility class
 *
 * @return YITH_WCPB_Name_Your_Price_Compatibility
 * @since 1.2.20
 */
function YITH_WCPB_Name_Your_Price_Compatibility() {
    return YITH_WCPB_Name_Your_Price_Compatibility::get_instance();
}