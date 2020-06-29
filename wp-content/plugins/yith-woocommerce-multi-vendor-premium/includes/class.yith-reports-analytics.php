<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Reports_Analytics' ) ) {


    class YITH_Reports_Analytics {

        /** @protected array Main Instance */
        protected static $_instance = null;

        public function __construct() {
	        /* === WooCommerce Admin Support === */

	        /* Remove WooCommerce Admin for Vendors */
	        add_action( 'woocommerce_analytics_menu_capability', array( $this, 'remove_analytics_menu_for_vendors' ) );

	        /* Orders Report */
	        add_filter( 'woocommerce_analytics_clauses_where', array( $this, 'analytics_clauses_where' ), 10, 2 );
        }

        /**
         * Main YITH_Reports Instance
         *
         * @static
         *
         * @return YITH_Vendors_Report Main instance
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( ! isset( self::$_instance ) || is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

	    /**
	     * Remove the WooCommerce admin bar for vendors
	     *
	     * @param string $capability User capability used to show the WooCommerce admin bar
	     * @return string the allowed capability
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function remove_analytics_menu_for_vendors( $capability ){
		    $vendor = yith_get_vendor( 'current', 'user' );
		    if( $vendor->is_valid() && $vendor->has_limited_access() ){
			    $capability = false;
		    }
		    return $capability;
	    }

	    /**
	     * Filter the WooCommerce admin report to remove vendor's information
	     *
	     * @param array  $clauses The original arguments for the request.
	     * @param string $context The data store context.
	     *
	     * @return array the filtered SQL clauses
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     */
	    public function analytics_clauses_where( $clauses, $context ){
		    global $wpdb;
		    $clauses[] = "AND {$wpdb->prefix}wc_order_stats.parent_id = 0 AND {$wpdb->prefix}wc_order_stats.parent_id NOT IN( SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_key = '_created_via' AND {$wpdb->postmeta}.meta_value = 'yith_wcmv_vendor_suborder' )";
		    return $clauses;
	    }
    }
}

/**
 * Main instance of plugin
 *
 * @return YITH_Reports_Analytics
 * @since  1.0
 */
if ( ! function_exists( 'YITH_Reports_Analytics' ) ) {
    /**
     * @return YITH_Reports_Analytics
     */
    function YITH_Reports_Analytics() {
        return YITH_Reports_Analytics::instance();
    }
}

YITH_Reports_Analytics();
