<?php
/**
 * Product Bundle Class
 *
 * @author Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPB' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WC_Bundled_Item' ) ) {
    /**
     * Product Bundle Item Object
     *
     * @since 1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WC_Bundled_Item {

        public $item_id;

        public $product_id;
        public $product;

        private $quantity;

        /**
         * __construct
         *
         * @access public
         */
        public function __construct( $parent, $item_id ) {
            $this->item_id    = $item_id;
            $this->product_id = $parent->bundle_data[ $item_id ][ 'product_id' ];
            $this->product_id = YITH_WCPB()->compatibility->wpml->wpml_object_id( $this->product_id, 'product', true );

            $this->quantity = isset( $parent->bundle_data[ $item_id ][ 'bp_quantity' ] ) ? $parent->bundle_data[ $item_id ][ 'bp_quantity' ] : 1;

            $bundled_product = wc_get_product( $this->product_id );

            // if exist the product with $this->product_id
            if ( $bundled_product ) {
                $this->product = $bundled_product;
            }
        }

        /**
         * Return true if this->product is setted
         *
         * @return  boolean
         */
        public function exists() {

            return !empty( $this->product );
        }

        /**
         * Return this->product [or false if it not exist]
         *
         * @return  WC_Product
         */
        public function get_product() {
            return !empty( $this->product ) ? $this->product : false;
        }

        /**
         * Return this->quantity [or 0 if it's not setted]
         *
         * @return  int
         */
        public function get_quantity() {
            return !empty( $this->quantity ) ? $this->quantity : 0;
        }
    }
}