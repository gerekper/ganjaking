<?php
if( !defined( 'ABSPATH')){
    exit( 'Direct access forbidden.' );
}
if( !class_exists( 'YITH_Frontend_Manager_For_Name_Your_Price')){

    class YITH_Frontend_Manager_For_Name_Your_Price{
        /**
         * Main instance
         */
        private static $_instance = null;

        public function __construct()
        {
            if( defined( 'YWCNP_PREMIUM' )) {

                $name_your_price_admin = YITH_WC_Name_Your_Price_Premium_Admin::get_instance();

                add_action( 'wp_enqueue_scripts', array( $name_your_price_admin, 'include_premium_scripts' ),99 );
                add_action( 'wp_enqueue_scripts', array( $name_your_price_admin, 'include_premium_styles' ),99 );
                add_action('yith_wcfm_product_save', array( $name_your_price_admin, 'save_product_nameyourprice_meta' ), 30, 2 );

            }
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Frontend_Manager_For_Name_Your_Price Main instance
         *
         * @since  1.7
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_For_Vendor
 * @since  1.9
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Frontend_Manager_For_Name_Your_Price' ) ) {
    function YITH_Frontend_Manager_For_Name_Your_Price() {
        return YITH_Frontend_Manager_For_Name_Your_Price::instance();
    }
}