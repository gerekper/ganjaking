<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * YITH WooCommerce Waiting List Compatibility Class
 *
 * @class   YITH_WCPB_Waiting_List_Compatibility
 * @since   1.3.1
 * @author  YITH
 */
class YITH_WCPB_Waiting_List_Compatibility {

    /** @var YITH_WCPB_Waiting_List_Compatibility */
    private static $instance;

    private $products_to_check = array();

    /**
     * @return YITH_WCPB_Waiting_List_Compatibility
     */
    public static function get_instance() {
        return !is_null( self::$instance ) ? self::$instance : self::$instance = new self();
    }

    /**
     * YITH_WCPB_Waiting_List_Compatibility constructor.
     */
    private function __construct() {
        add_action( 'woocommerce_product_object_updated_props', array( $this, 'item_stock_status_change' ), 10, 2 );
        add_action( 'woocommerce_update_product', array( $this, 'item_stock_status_change_update' ), 10, 2 );
    }

    /**
     * @param WC_Product $product
     */
    public function item_stock_status_change( $product, $updated_props ) {
        if ( in_array( 'stock_status', $updated_props, true ) && $product && $product->is_type( array_keys( yith_wcpb_get_allowed_product_types() ) ) ) {
            $this->add_product_to_check( $product->get_id() );
        }
    }

    /**
     * @param int        $product_id
     * @param WC_Product $product
     */
    public function item_stock_status_change_update( $product_id, $product = false ) {
        if ( !$product ) {
            // the $product param of the 'woocommerce_update_product' action is available since WooCommerce 3.7
            $product = wc_get_product( $product_id );
        }

        if ( $product && $this->is_product_to_check( $product_id ) && ( $bundle_products = yith_wcpb_get_bundle_products_by_item( $product ) ) ) {
            foreach ( $bundle_products as $id ) {
                $product = wc_get_product( $id );
                if ( $product && $product->is_type( 'yith_bundle' ) ) {
                    do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
                }
            }
            $this->remove_product_to_check( $product_id );
        }
    }


    /**
     * @param int $product_id
     */
    public function add_product_to_check( $product_id ) {
        $this->products_to_check = array_unique( array_merge( $this->products_to_check, array( absint( $product_id ) ) ) );
    }

    /**
     * @param int $product_id
     */
    public function remove_product_to_check( $product_id ) {
        $this->products_to_check = array_diff( $this->products_to_check, array( absint( $product_id ) ) );
    }

    /**
     * @param int $product_id
     * @return bool
     */
    public function is_product_to_check( $product_id ) {
        return in_array( $product_id, $this->products_to_check );
    }


}
