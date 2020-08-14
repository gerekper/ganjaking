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
 * @class      YITH_WooCommerce_Cost_Of_Goods_Support
 * @package    YIThemes
 * @since      Version 1.11.4
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WordPress_Yoast_SEO_Support' ) ) {

    /**
     * YITH_WooCommerce_Cost_Of_Goods_Support Class
     */
    class YITH_WordPress_Yoast_SEO_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
	        add_action( 'wpseo_register_extra_replacements', array( $this, 'register_plugin_replacements' ) );
        }

	    /**
	     * Register a var replacement for vendor name
	     *
	     * @author Alessio Torrisi <alessio.torrisi@yithemes.com>
	     * @return void
	     */
        public function register_plugin_replacements() {
	        wpseo_register_var_replacement( '%%vendor_name%%', 'YITH_WordPress_Yoast_SEO_Support::retrieve_vendor_name', 'basic', __( 'This is the name of the vendor product', 'yith_woocommerce_product_vendors' ) );
        }

	    /**
	     * Get the vendor name
	     *
	     * @author Alessio Torrisi <alessio.torrisi@yithemes.com>
	     * @return string Store name
	     */
	    public static function retrieve_vendor_name( $var, $post ) {

		    if( isset( $post->ID ) ){
			    $vendor = yith_get_vendor( $post->ID, 'product' );
			    $var = $vendor->is_valid() ? $vendor->name : $var;
		    }

			return $var;
	    }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_WooCommerce_Cost_Of_Goods_Support Main instance
         *
         * @since  1.11.4
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
 * @return /YITH_WordPress_Yoast_SEO_Support
 * @since  1.11.4
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WordPress_Yoast_SEO_Support' ) ) {
    function YITH_WordPress_Yoast_SEO_Support() {
        return YITH_WordPress_Yoast_SEO_Support::instance();
    }
}

YITH_WordPress_Yoast_SEO_Support();
