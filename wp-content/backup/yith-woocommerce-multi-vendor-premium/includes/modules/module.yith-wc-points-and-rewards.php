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
 * @class      YITH_WooCommerce_Points_And_Rewards_Support
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WooCommerce_Points_And_Rewards_Support' ) ) {

    /**
     * YITH_WooCommerce_Points_And_Rewards_Support Class
     */
    class YITH_WooCommerce_Points_And_Rewards_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
            add_action( 'woocommerce_order_status_pending_to_completed',    array( $this, 'prevent_double_points' ), 5, 1 );
            add_action( 'woocommerce_order_status_on-hold_to_completed',    array( $this, 'prevent_double_points' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_to_processing',    array( $this, 'prevent_double_points' ), 5, 1 );
            add_action( 'woocommerce_order_status_failed_to_completed',     array( $this, 'prevent_double_points' ), 5, 1 );
            add_action( 'woocommerce_order_status_processing',              array( $this, 'prevent_double_points' ), 5, 1 );
            add_action( 'woocommerce_payment_complete',                     array( $this, 'prevent_double_points' ), 5, 1 );
        }

        /**
         * Prevent double points from vendor suborder
         * 
         * If a vendor suborder change their status no points are assign to customer
         * 
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @param $order mixed WC_Order object or order id
         * @return void
         */
        public function prevent_double_points( $order ) {
            if ( ! is_object( $order ) ) {
                $order = new WC_Order( $order );
            }

            // bail for guest user
            if ( ! $order->get_user_id() ) {
                return;
            }

            $parent_order_id = wp_get_post_parent_id( yit_get_prop( $order, 'id' ) );

            if ( $parent_order_id != 0 ) {
                global $wc_points_rewards;
                remove_action( current_action(), array( $wc_points_rewards->order, 'add_points_earned' ) );
            }
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
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_WooCommerce_Points_And_Rewards_Support
 * @since  1.7
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WooCommerce_Points_And_Rewards_Support' ) ) {
    function YITH_WooCommerce_Points_And_Rewards_Support() {
        return YITH_WooCommerce_Points_And_Rewards_Support::instance();
    }
}

YITH_WooCommerce_Points_And_Rewards_Support();
