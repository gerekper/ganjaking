<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of PREMIUM version of YWAU plugin
 *
 * @class   YITH_WooCommerce_Pdf_Invoice_Privacy
 * @package Yithemes
 * @since   1.7.2
 * @author  Daniel Sanchez Saez
 */
if ( ! class_exists( 'YITH_WooCommerce_Pdf_Invoice_Privacy' ) ) {

    class YITH_WooCommerce_Pdf_Invoice_Privacy extends YITH_Privacy_Plugin_Abstract
    {

        /**
         * Init - hook into events.
         */
        public function __construct()
        {

            /**
             * GDRP privacy policy content
             */
            parent::__construct( esc_html_x( 'YITH Pdf Invoice for WooCommerce premium', 'Privacy Policy Content', 'yith-woocommerce-pdf-invoice' ) );

            /**
             * GDRP to export order personal data
             */

            add_filter( 'woocommerce_privacy_export_order_personal_data_props', array( $this, 'woocommerce_privacy_export_order_personal_data_props_call_back' ), 10, 1 );

            add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array( $this, 'woocommerce_privacy_export_order_personal_data_prop_call_back' ), 10, 3 );


            /**
             * GDRP to erase order personal data
             */

            add_filter( 'woocommerce_privacy_erase_order_personal_data', array( $this, 'woocommerce_privacy_erase_order_personal_data_call_back' ), 10, 2 );

        }

        /**
         * Add privacy policy content for the privacy policy page.
         *
         * @since 1.7.2
         */
        public function get_privacy_message( $section ) {

            $privacy_content_path = YITH_YWPI_VIEWS_PATH . '/privacy/html-policy-content-' . $section . '.php';

            if ( file_exists( $privacy_content_path ) ) {

                ob_start();

                include $privacy_content_path;

                return ob_get_clean();

            }

            return '';

        }

        /**
         * GDPR erase order_metas to the filter hook of WooCommerce to erase personal order data associated with an email address.
         *
         * @since 1.7.2
         *
         * @param  boolean $erasure_enabled.
         * @param  object $order.
         * @return boolean
         */
        function woocommerce_privacy_erase_order_personal_data_call_back( $erasure_enabled, $order )
        {

            if ( $erasure_enabled ){

                $array_props = apply_filters( 'yith_wc_additional_uploads_array_props_to_erase', array(
                    '_billing_vat_ssn',
                    '_billing_vat_number',
                ), $erasure_enabled, $order );

                foreach ( $array_props as $prop ){

                    $aux = yit_get_prop( $order, $prop, true );
                    if ( $aux )
                        update_post_meta( $order->get_id(), $prop, wp_privacy_anonymize_data( 'text', $aux ) );

                }

            }

            return $erasure_enabled;

        }

        /**
         * GDPR add order_meta to the filter hook of WooCommerce to export personal order data associated with an email address.
         *
         * @since 1.7.2
         *
         * @param  array $array_meta_to_export meta_orders.
         * @return array
         */
        function woocommerce_privacy_export_order_personal_data_props_call_back( $array_meta_to_export )
        {

            $array_meta_to_export[ '_billing_vat_ssn' ] = esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' );
            $array_meta_to_export[ '_billing_vat_number' ] = esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' );

            return $array_meta_to_export;

        }

        /**
         * GDPR retrieve the value order_meta to add to the filer hook of WooCommerce to export personal order data associated with an email address.
         *
         * @since 1.7.2
         *
         * @param  string $value value of meta_order.
         * @param  string $prop meta_order
         * @param  object $order
         * @return string
         */
        function woocommerce_privacy_export_order_personal_data_prop_call_back( $value, $prop, $order )
        {

            $array_props = apply_filters( 'yith_wc_additional_uploads_array_props_to_export', array(
                '_billing_vat_ssn',
                '_billing_vat_number',
            ), $order );

            if ( in_array( $prop, $array_props ) ){

                $value .= yit_get_prop( $order, $prop, true );

            }

            return $value;

        }

    }

}

new YITH_WooCommerce_Pdf_Invoice_Privacy();
