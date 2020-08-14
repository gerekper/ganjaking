<?php
/*
Author: YITH / Mementor AS  (www.mementor.no)
*/

if (!defined('ABSPATH')) {
    exit;
}


if ( ! class_exists ( 'YITH_Documents_Bulk' ) ) {

    /**
     * Implements features of YITH_Documents_Bulk
     *
     * @class   YITH_Documents_Bulk
     */

    class YITH_Documents_Bulk {

        protected static $instance;

        /**
         * Returns single instance of the class
         *
         */
        public static function get_instance() {
            if ( is_null ( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {
            
            add_filter( 'bulk_actions-edit-shop_order', array( $this, 'yith_ywpi_documents_bulk_actions' ) );

            add_filter('handle_bulk_actions-edit-shop_order', array( $this, 'yith_ywpi_documents_bulk_actions_handler' ) , 10, 3 );

        }

        /* Add bulk actions */
        function yith_ywpi_documents_bulk_actions( $bulk_actions ){

            $bulk_actions['generate_invoices'] = esc_html__('Generate Invoices', 'yith-woocommerce-pdf-invoice');
            $bulk_actions['generate_packing_slip'] = esc_html__('Generate Packing Slip', 'yith-woocommerce-pdf-invoice');
            $bulk_actions['regenerate_invoices'] = esc_html__('Regenerate Invoices', 'yith-woocommerce-pdf-invoice');
            $bulk_actions['regenerate_packing_slip'] = esc_html__('Regenerate Packing Slip', 'yith-woocommerce-pdf-invoice');

            return $bulk_actions;
        }

        /* Handle bulk actions */
        function yith_ywpi_documents_bulk_actions_handler( $redirect_to, $doaction, $post_ids ){

            if ( $doaction === 'generate_invoices' ) {
                if ( count( $post_ids ) > 0 ) {
                    foreach ( $post_ids as $post_id ) {
                        $this->yith_ywpi_documents_bulk_generate_invoice( $post_id );
                    }
                }
            }
            if ($doaction === 'generate_packing_slip') {
                if ( count( $post_ids ) > 0 ) {
                    foreach ( $post_ids as $post_id ) {
                        $this->yith_ywpi_documents_bulk_generate_packing_slip( $post_id );
                    }
                }
            }
            if ( $doaction === 'regenerate_invoices' ) {
                if ( count( $post_ids ) > 0 ) {
                    foreach ( $post_ids as $post_id ) {
                        $this->yith_ywpi_documents_bulk_regenerate_invoice( $post_id );
                    }
                }
            }
            if ($doaction === 'regenerate_packing_slip') {
                if ( count( $post_ids ) > 0 ) {
                    foreach ( $post_ids as $post_id ) {
                        $this->yith_ywpi_documents_bulk_regenerate_packing_slip( $post_id );
                    }
                }
            }

            return $redirect_to;
        }

        /* Bulk generate invoices */
        function yith_ywpi_documents_bulk_generate_invoice( $post_id ){
            $object = new YITH_WooCommerce_Pdf_Invoice();
            $object->create_document( $post_id, 'invoice' );
        }

        /* Bulk generate packing slips */
        function yith_ywpi_documents_bulk_generate_packing_slip( $post_id ){
            $object = new YITH_WooCommerce_Pdf_Invoice();
            $object->create_document( $post_id, 'packing-slip' );
        }

        /* Bulk regenerate invoices */
        function yith_ywpi_documents_bulk_regenerate_invoice( $post_id ){
            $object = new YITH_WooCommerce_Pdf_Invoice();
            $object->regenerate_document( $post_id, 'invoice', 'pdf'  );
        }

        /* Bulk regenerate invoices */
        function yith_ywpi_documents_bulk_regenerate_packing_slip( $post_id ){
            $object = new YITH_WooCommerce_Pdf_Invoice();
            $object->regenerate_document( $post_id, 'packing-slip', 'pdf'  );
        }

    }
}