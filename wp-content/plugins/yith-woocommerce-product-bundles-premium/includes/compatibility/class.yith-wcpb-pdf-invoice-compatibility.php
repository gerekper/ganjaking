<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * YITH WooCommerce PDF Invoice Compatibility Class
 *
 * @class   YITH_WCPB_Pdf_Invoice_Compatibility
 * @package Yithemes
 * @since   1.1.4
 * @author  Yithemes
 *
 */
class YITH_WCPB_Pdf_Invoice_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Pdf_Invoice_Compatibility
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Pdf_Invoice_Compatibility
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
     * @access private
     */
    private function __construct() {
        // Commented: if de-commented the bundled items will not completely visible in the invoice
        //add_filter( 'yith_ywpi_get_order_items_for_invoice', array( $this, 'remove_bundled_items_in_invoice' ), 10, 2 );

        add_filter( 'yith_ywpi_line_discount', array( $this, 'remove_bundled_items_from_discount_in_invoice' ), 10, 2 );
    }

    public function remove_bundled_items_in_invoice( $order_items, $order ) {
        foreach ( $order_items as $order_item_key => $order_item ) {
            if ( !empty( $order_item[ 'bundled_by' ] ) )
                unset( $order_items[ $order_item_key ] );
        }

        return $order_items;
    }

    public function remove_bundled_items_from_discount_in_invoice( $discount, $item ) {
        if ( !empty( $item[ 'bundled_by' ] ) )
            return 0;

        return $discount;
    }

}
