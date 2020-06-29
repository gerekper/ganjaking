<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * YITH WooCommerce Dynamic Pricing and Discount Compatibility Class
 *
 * @class   YITH_WCPB_Dynamic_Compatibility
 * @package Yithemes
 * @since   1.0.21
 * @author  Yithemes
 *
 */
class YITH_WCPB_Dynamic_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Dynamic_Compatibility
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Role_Based_Compatibility
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
        add_filter( 'ywdpd_get_price_exclusion', array( $this, 'dynamic_bundle_exclusion' ), 10, 3 );
        add_filter( 'ywdpd_exclude_products_from_discount', array( $this, 'bundle_exclusion_from_discount' ), 10, 2 );
        add_filter( 'ywdpd_get_cart_item_quantities', array( $this, 'remove_bundle_quantities_in_cart' ), 10 );

    }

    /**
     * @param bool       $value
     * @param string     $price
     * @param WC_Product $product
     *
     * @return bool
     */
    public function dynamic_bundle_exclusion( $value, $price, $product ) {
        if ( $product && $product->is_type( 'yith_bundle' ) )
            return true;

        return $value;
    }

    /**
     * @param $value
     * @param $product
     *
     * @return bool
     */
    public function bundle_exclusion_from_discount( $value, $product ) {
        if ( $product && $product->is_type( 'yith_bundle' ) )
            return true;

        return $value;
    }

    /**
     * @param array $quantities
     *
     * @return array
     */
    public function remove_bundle_quantities_in_cart( $quantities ) {
        if ( !!$quantities && is_array( $quantities ) ) {
            $cart = WC()->cart->get_cart();


            foreach ( $cart as $cart_item_key => $values ) {
                $product = $values[ 'data' ];

                if ( isset( $values[ 'bundled_by' ] ) && isset( $quantities[ $product->get_stock_managed_by_id() ] ) )
                    unset( $quantities[ $product->get_stock_managed_by_id() ] );
            }
        }

        return $quantities;
    }

}

/**
 * Unique access to instance of YITH_WCPB_Dynamic_Compatibility class
 *
 * @return YITH_WCPB_Dynamic_Compatibility
 * @since 1.0.21
 */
function YITH_WCPB_Dynamic_Compatibility() {
    return YITH_WCPB_Dynamic_Compatibility::get_instance();
}