<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists( 'YITH_FUNDS_Compatibility')){

    class YITH_FUNDS_Compatibility{

        protected static $_instance;

        public function __construct()
        {
           /**YITH PDF INVOICE*/
            if( defined('YITH_YWPI_INIT')){
             
                require_once( 'class.yith-funds-yith-pdf-invoice-compatibility.php' );
                YITH_Funds_YITH_PDF_Invoice_Compatibility();
            }
            
            /**YITH CUSTOMIZE MY ACCOUNT*/
            if( defined('YITH_WCMAP_PREMIUM') ){
                require_once( 'class.yith-funds-yith-customize-account.php' );
                YITH_Funds_YITH_Customize_Account_Compatibility();
            }

            
            /** Aelia Currency Switcher */
            if( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
                require_once( 'class.yith-funds-aelia-currency-switcher.php' );
            }

            /**WooCommerce MultiLingual*/
            if( class_exists( 'woocommerce_wpml' ) ){
	            require_once( 'class.yith-funds-wcml.php' );
            }
	        /**YITH MultiVendor*/
	        if( defined( 'YITH_WPV_PREMIUM' ) ){
		        require_once( 'yith-woocommerce-multi-vendor/class.yith-funds-multi-vendor.php' );
	        }
        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @return YITH_FUNDS_Compatibility unique access
         */
        public static function get_instance(){

            if( is_null( self::$_instance ) ){

                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }
}

/**
 * @return YITH_FUNDS_Compatibility
 */
function YITH_FUNDS_Compatibility(){

    return  YITH_FUNDS_Compatibility::get_instance();
}