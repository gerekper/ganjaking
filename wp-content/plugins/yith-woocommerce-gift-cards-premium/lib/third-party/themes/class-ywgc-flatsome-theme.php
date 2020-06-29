<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YWGC_Flatsome_Theme' ) ) {

    /**
     *
     * @class   YWGC_Flatsome_Theme
     *
     * @since   1.0.0
     * @author  yithemes
     */
    class YWGC_Flatsome_Theme
    {
        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;
        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null ( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
        public function __construct() {

            remove_action( 'woocommerce_product_thumbnails', array( YITH_YWGC_Frontend_Premium::get_instance(), 'yith_ywgc_display_gift_card_form_preview_below_image' ) );

            add_action( 'flatsome_after_product_images', array( YITH_YWGC_Frontend_Premium::get_instance(), 'yith_ywgc_display_gift_card_form_preview_below_image' ) );

        }
    }
}
YWGC_Flatsome_Theme::get_instance ();