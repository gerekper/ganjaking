<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_YWBC_Manage_Barcodes' ) ) {

    /**
     *
     * @class   YITH_YWBC_Manage_Barcodes
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     */
    class YITH_YWBC_Manage_Barcodes
    {
        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;

        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        protected function __construct() {
            $this->init_hooks();

        }
        /**
         * Initialize all hooks used by the plugin affecting the back-end behaviour
         */
        public function init_hooks() {
            /**
             * Show the order barcode on emails
             */
            if ( YITH_YWBC()->show_on_email_all || YITH_YWBC()->show_on_email_completed ) {
                add_action( 'woocommerce_email_footer', array(
                    $this,
                    'show_on_emails'
                ) );
            }
            /**
             * Show the product barcode on emails
             */
            if ( YITH_YWBC()->show_product_barcode_on_email ) {

                add_action( 'woocommerce_email_header', array(
                    $this,
                    'enable_product_barcode_in_email'
                ), 5 );

                add_action( 'woocommerce_email_footer', array(
                    $this,
                    'disable_product_barcode_in_email'
                ) );
            }
        }

        /**
         * show the order barcode on emails
         *
         * @param WC_Email $email
         */
        public function show_on_emails( $email ) {

            //  Check if only on completed order should be shown the barcode and
            //  this is not the case

            if ( ! is_object ( $email ) ){
                return;
            }

            if ( YITH_YWBC()->show_on_email_completed && ( 'customer_completed_order' != $email->id ) ) {
                return;
            }

            //  Check if the barcode should be shown...
            if ( ! YITH_YWBC()->show_on_email_completed && ! YITH_YWBC()->show_on_email_all ) {
                return;
            }

            if ( ! isset( $email ) || !isset($email->object) ) {
                return;
            }

            //  Only for email related to an order...
            if ( ! $email->object instanceof WC_Order ) {
                return;
            }



            //  Display the barcode...

            $order = $email->object;
            ob_start();

            include( YITH_YWBC_ASSETS_DIR . '/css/ywbc-style.css' );
            $css = ob_get_clean();

            YITH_YWBC()->show_barcode( yit_get_prop( $order, 'id' ), true, $css );
        }

        /**
         * Show the product barcode on emails
         */
        public function enable_product_barcode_in_email() {
            add_action( 'woocommerce_order_item_meta_start', array(
                $this,
                'show_product_barcode_in_order_email'
            ), 10, 3 );
        }

        public function show_product_barcode_in_order_email( $item_id, $item, $order ) {

            $product_id = ( $item[ "variation_id" ] ? $item[ "variation_id" ] : $item[ "product_id" ] );

            echo '<div class="ywbc-email-product-barcode-container">' . do_shortcode( '[yith_render_barcode id="' . $product_id . '"]' ) . '</div>';
        }

        public function disable_product_barcode_in_email() {

            remove_action( 'woocommerce_order_item_meta_start', array(
                $this,
                'show_product_barcode_in_order_email'
            ), 10 );
        }
    }
}
YITH_YWBC_Manage_Barcodes::get_instance();
