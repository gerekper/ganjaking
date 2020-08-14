<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendor_Vacation
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendor_Vacation' ) ) {

    /**
     * YITH_Vendor_Vacation Class
     */
    class YITH_Vendor_Vacation {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
            add_filter( 'yith_wcmv_panel_admin_tabs', array( $this, 'add_seller_vacation_tab' ) );

            /* Filter Product in loop */
	        if( 'no' == get_option( 'yith_wpv_show_vendors_products_on_vacation', 'no' )  ){
		        add_filter( 'yith_wcmv_to_exclude_terms_in_loop', array( $this, 'check_vendors_in_vacation' ), 20, 1 );
	        }

            /* Hide add to cart button */
            add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_loop_add_to_cart' ), 10, 2 );
            add_action( 'wc_get_template',   array( $this, 'hide_single_add_to_cart' ), 30, 5  );

            /* Nielsen Theme Compatibility */
            add_filter( 'yith_loop_add_to_cart_hide_button', array( $this, 'nielsen_theme_support' ) );

            /* Add to cart validation on vacation */
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'avoid_add_to_cart' ), 10, 3 );

            add_action( 'woocommerce_single_product_summary', array( $this, 'add_vacation_template' ), 25 );

        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Vendor_Vacation Main instance
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

        /**
         * Main plugin Instance
         *
         * @param $tabs The vendor admin tabs
         *
         * @return array vendor admin tabs
         *
         * @since  1.7
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_seller_vacation_tab( $tabs ){
            $tabs['vendor-vacation'] = __( 'Vacation', 'yith-woocommerce-product-vendors' );
            return $tabs;
        }

        public function vendor_is_on_vacation( $vendor = false ){
            if( ! $vendor ){
                $vendor = yith_get_vendor( 'current', 'user' );
            }

            $vacation_start = strtotime( $vendor->vacation_start_date );
            $vacation_end   = strtotime( $vendor->vacation_end_date );
            $today          = strtotime( date( 'Y-m-d', time() ) );
            return $today >= $vacation_start && $today <= $vacation_end;
        }

        /**
         * Check if vendor is on vacation
         *
         * @param $to_exclude   term ids to exclude
         * @return              array term ids to exclude
         *
         * @since    1.7
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function check_vendors_in_vacation( $to_exclude ){
            if( YITH_Vendors()->frontend->is_vendor_page() ){
                return $to_exclude;
            }

            $vendors_ids = YITH_Vendors()->get_vendors( array( 'vacation_selling' => 'disabled', 'fields' => 'ids' ) );
            $on_vacation = array();
            if ( ! empty( $vendors_ids ) ) {
                foreach( $vendors_ids as $vendor_id ){
                    $vendor = yith_get_vendor( $vendor_id, 'vendor' );
                    if( $vendor->is_on_vacation() ){
                        $on_vacation[] = $vendor->id;
                    }
                }
            }
            return ! empty( $on_vacation ) ? array_merge( $to_exclude, $on_vacation) : $to_exclude;
        }

        /**
         * Change single add to cart template
         *
         * @param $located
         * @param $template_name
         * @param $args
         * @param $template_path
         * @param $default_path
         *
         * @return string Located file
         *
         * @since    1.7
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function hide_single_add_to_cart( $located, $template_name, $args, $template_path, $default_path ) {
            if ( is_singular( 'product' ) && preg_match( '/single-product\/add-to-cart\/(\S+).php/', $template_name ) ) {
                $vendor = yith_get_vendor( 'current', 'product' );

                if ( $vendor->is_on_vacation() && 'disabled' == $vendor->vacation_selling ) {
                    $located = wc_locate_template( 'single-product/store-vacation.php', WC()->template_path(), YITH_WPV_TEMPLATE_PATH . '/woocommerce/' );
                }
            }
            return $located;
        }

        /**
         * Add vacation part to add to cart template
         *
         * @return void
         *
         * @since    1.7
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_vacation_template(){
            if( is_singular( 'product' ) ){
                $vendor = yith_get_vendor( 'current', 'product' );
                $vendor->is_on_vacation() && 'enabled' == $vendor->vacation_selling && yith_wcpv_get_template( 'store-vacation', array( 'vendor' => $vendor ), 'woocommerce/single-product' );
            }
        }

        /**
         * Add vacation part to add to cart template
         *
         * @param $add_to_cart  Add to cart html
         * @param $product      Current product in loop
         *
         * @return string       Add to cart html
         *
         * @since    1.7
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function hide_loop_add_to_cart( $add_to_cart, $product ){
            $vendor = yith_get_vendor( $product, 'product' );
            return $vendor->is_valid() && $vendor->is_on_vacation() && 'disabled' == $vendor->vacation_selling ? '' : $add_to_cart;
        }

        /**
         * Add vacation part to add to cart template
         *
         * @param $product_id   The product id
         * @param $quantity     Product Quantity
         *
         * @return bool         validation result
         *
         * @since    1.7
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function avoid_add_to_cart( $validate, $product_id, $quantity ){
            $vendor = yith_get_vendor( $product_id, 'product' );
            return $vendor->is_valid() && $vendor->is_on_vacation() && 'disabled' == $vendor->vacation_selling ? false : $validate;
        }

        /**
         * Nielsen theme shop loop compatibility
         *
         * @param $check        The filter value for add to cart/show details value
         *
         * @return bool         validation result
         *
         * @since    1.8.3
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function nielsen_theme_support( $check ){
            global $product;
            $vendor = yith_get_vendor( $product, 'product' );
            return $vendor->is_valid() && $vendor->is_on_vacation() && 'disabled' == $vendor->vacation_selling ? true : $check;
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Vendor_Vacation
 * @since  1.7
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Vendor_Vacation' ) ) {
    function YITH_Vendor_Vacation() {
        return YITH_Vendor_Vacation::instance();
    }
}

YITH_Vendor_Vacation();
