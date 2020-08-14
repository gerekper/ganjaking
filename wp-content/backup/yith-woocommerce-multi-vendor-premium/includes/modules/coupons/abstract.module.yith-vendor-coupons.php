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
 * @class      YITH_Vendor_Coupons
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Abstract_Vendor_Coupons' ) ) {

    /**
     * YITH_Meta_Box_Coupon_Data Class
     */
    abstract class YITH_Abstract_Vendor_Coupons extends WC_Meta_Box_Coupon_Data {

        /**
         * Main instance
         */
        protected static $_instance = null;

        public function __construct(){
            /* Coupon Management */
            add_filter( 'woocommerce_coupon_discount_types', array( $this, 'coupon_discount_types' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_vendor_coupon_meta_boxes' ), 35 );

            /* Filter coupon list */
            add_action( 'request', array( $this, 'filter_coupon_list' ) );
            add_filter( 'wp_count_posts', array( $this, 'vendor_count_coupons' ), 10, 3 );

            $this->extra_construct_action();
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Commissions Main instance
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance( $class ) {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new $class();
            }

            return self::$_instance;
        }

        /**
         * Vendor Coupon Management
         *
         * @Author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.2
         * @internal param \The $coupon_types coupon types
         * @return array The new coupon types list
         */
        public function add_vendor_coupon_meta_boxes(){
            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                remove_meta_box( 'woocommerce-coupon-data', 'shop_coupon', 'normal' );
                add_meta_box( 'woocommerce-coupon-data', __( 'Coupon Data', 'yith-woocommerce-product-vendors' ), 'YITH_Vendor_Coupons::output', 'shop_coupon', 'normal', 'high' );
            }
        }

        /**
         * Manage vendor taxonomy bulk actions
         *
         * @Author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.2
         * @param $coupon_types The coupon types
         * @return array The new coupon types list
         */
        public function coupon_discount_types( $coupon_types ){
            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                $to_unset = apply_filters( 'yith_wc_multi_vendor_coupon_types', array() );
                foreach( $to_unset as $coupon_type_id ){
                    unset( $coupon_types[ $coupon_type_id ] );
                }
            }

            return $coupon_types;
        }

        /**
         * Only show vendor's coupon
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @param  arr $request Current request
         *
         * @return arr          Modified request
         * @since  1.0
         */
        public function filter_coupon_list( $request ) {
            global $typenow;

            $vendor = yith_get_vendor( 'current', 'user' );

            if ( is_admin() && $vendor->is_valid() && $vendor->has_limited_access() && 'shop_coupon' == $typenow ) {
                $request[ 'author__in' ] = $vendor->admins;
            }

            return $request;
        }

        /**
         * Filter the post count for vendor
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @param $counts   The post count
         * @param $type     Post type
         * @param $perm     The read permission
         *
         * @return arr  Modified request
         * @since    1.3
         * @use wp_post_count action
         */
        public function vendor_count_coupons( $counts, $type, $perm ) {
            $vendor = yith_get_vendor( 'current', 'user' );

            if ( $vendor->is_valid() && $vendor->has_limited_access() && 'shop_coupon' == $type ) {
                $args = array(
                    'post_type'     => $type,
                    'author__in'    => $vendor->get_admins()
                );

                /**
                 * Get a list of post statuses.
                 */
                $stati = get_post_stati();

                // Update count object
                foreach ( $stati as $status ) {
                    $args['post_status'] = $status;
                    $posts               = get_posts( $args );
                    $counts->$status     = count( $posts );
                }
            }

            return $counts;
        }

        /**
         * Override this method to add other action to __construct
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function extra_construct_action(){}
    }
}
