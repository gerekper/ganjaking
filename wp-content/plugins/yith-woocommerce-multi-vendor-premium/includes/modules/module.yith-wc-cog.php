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
 * @package    Yithemes
 * @since      Version 1.11.4
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WooCommerce_Cost_Of_Goods_Support' ) ) {

    /**
     * YITH_WooCommerce_Cost_Of_Goods_Support Class
     */
    class YITH_WooCommerce_Cost_Of_Goods_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * WC_COG instance
         */
        public $cog = null;

        /**
         * WC_COG_Admin instance
         */
        public $cog_admin = null;

        /**
         * WC_COG_Admin-Orders instance
         */
        public $cog_orders = null;

        /**
         * WC_COG_Admin_Products instance
         */
        public $cog_products = null;

        /**
         * show cog info to vendor
         */
        public $show_cog_info = true;

        /**
         * show cog info to vendor
         */
        public $exclude_cog_in_commission = false;

        /**
         * @var bool order item meta name
         */
        public $cog_oder_item_meta_name = '_commission_included_cost_of_goods';

        /**
         * Construct
         */
        public function __construct(){
            /**
             * Object initializzation
             */
            $this->cog = wc_cog();
            $this->cog_admin = $this->cog->get_admin_instance();

            if( $this->cog_admin instanceof WC_COG_Admin ){
                $this->cog_orders   = $this->cog_admin->get_orders_instance();
                $this->cog_products = $this->cog_admin->get_products_instance();
            }

            /**
             * Privacy option
             */
            $this->show_cog_info = 'yes' == get_option( 'yith_wpv_show_cog_info', 'yes' );

            /**
             * Exclude cog in commission calculation
             */
            $this->exclude_cog_in_commission = 'no' == get_option( 'yith_wpv_include_cog', 'yes' );


            // Add cost of goods in vendor suborder - Checkout
            add_action( 'yith_wcmv_checkout_order_processed', array( $this->cog, 'set_order_cost_meta' ), 10, 1 );

            // Add options for WC COG under commissions table
            add_filter( 'yith_wpv_panel_commissions_options', array( $this, 'add_wc_cog_options' ), 20, 2 );

            if( $this->show_cog_info ){
                // Add Cost Of Goods column header to commission details page
                add_action( 'yith_wcmv_admin_order_item_headers', array( $this->cog_orders, 'add_order_item_cost_column_headers' ) );

                // Add cost of goods value in line item row in commission details page
                add_action( 'yith_wcmv_admin_order_item_values', array( $this->cog_orders, 'add_order_item_cost' ), 10, 3 );
            }

            if( $this->exclude_cog_in_commission ){
                add_filter( 'yith_wcmv_get_line_total_amount_for_commission', array( $this, 'exclude_cog_from_commission' ), 10, 4 );
            }

            // Commission note message
            add_filter( 'yith_wcmv_new_commission_note', array( $this, 'new_commission_note' ) );

            // Add cog order item meta
            add_action( 'yith_wcmv_add_extra_commission_order_item_meta', array( $this, 'add_cog_order_item_meta' ), 10, 1 );

            // Exclude cog order item meta to parent/child order sync
            add_filter( 'yith_wcmv_order_item_meta_no_sync', array( $this, 'order_item_meta_no_sync' ) );

            // Add cog msssage in order details page
            add_filter( 'yith_wcmv_order_details_page_commission_message', array( $this, 'order_details_page_commission_message' ), 10, 2 );

            $vendor = yith_get_vendor( 'current', 'user' );
            if( $vendor->is_valid() && $vendor->has_limited_access() && $this->cog_admin instanceof WC_COG_Admin ){
                // Remove cost field to simple products under the 'General' tab
                remove_action( 'woocommerce_product_options_pricing', array( $this->cog_products , 'add_cost_field_to_simple_product' ) );

                // Remove cost field to variable products under the 'General' tab
                remove_action( 'woocommerce_product_options_sku', array( $this->cog_products, 'add_cost_field_to_variable_product' ) );

                // Remove in quick edit
                remove_action( 'woocommerce_product_quick_edit_end',  array( $this->cog_products, 'render_quick_edit_cost_field' ) );

                // Remove in bulk edit
                remove_action( 'woocommerce_product_bulk_edit_end', array( $this->cog_products, 'add_cost_field_bulk_edit' ) );
            }
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

        /**
         * Add cog options to commissions tab
         *
         * @param $options
         * @param $tab
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_wc_cog_options( $options, $tab ){
            $new_options = array();
            $new_options['commissions'] = array(
                'wc_cog_options_start'          => array(
                    'type'  => 'sectionstart',
                ),

                'wc_cog_options_title'          => array(
                    'title' => __( 'WooCommerce Cost of Goods', 'yith-woocommerce-product-vendors' ),
                    'type'  => 'title',
                    'desc'  => '',
                ),

                'wc_cog_default_handling' => array(
                    'title'             => __( 'Cost of goods handling', 'yith-woocommerce-product-vendors' ),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                    'desc'              => __( 'Include cost of goods in commission calculations', 'yith-woocommerce-product-vendors' ),
                    'desc_tip'          => __( 'Decide whether vendor commissions have to be calculated including cost of goods value or not.', 'yith-woocommerce-product-vendors' ),
                    'id'                => 'yith_wpv_include_cog',
                ),

                'wc_cog_show_info' => array(
                    'title'             => __( 'Show Cost of goods information', 'yith-woocommerce-product-vendors' ),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                    'desc'              => __( 'Show cost of goods information in commission details page', 'yith-woocommerce-product-vendors' ),
                    'id'                => 'yith_wpv_show_cog_info',
                ),

                'wc_cog_options_end'          => array(
                    'type'  => 'sectionend',
                ),
            );

            $to_return['commissions'] = array_merge( $options['commissions'], $new_options['commissions'] );
            return $to_return;
        }

        /**
         * Exclude cog cost to commissions
         *
         * @param $line_total
         * @param $order
         * @param $item
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function exclude_cog_from_commission( $line_total, $order, $item, $item_id ){
            $item_total_cost = wc_get_order_item_meta( $item_id, '_wc_cog_item_total_cost', true );

            if( ! empty( $item_total_cost ) ){
                $line_total = $line_total - $item_total_cost;
            }

            return $line_total;
        }

        /**
         * Add cog info to commissions note
         *
         * @param $msg
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function new_commission_note( $msg ){
            $cog = $this->exclude_cog_in_commission ? _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' ) : _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' );
            $msg = sprintf( '%s:<br>* %s <em>%s</em>',
                $msg,
                _x( 'cost of goods', 'part of: cost of goods included or cost of goods excluded', 'yith-woocommerce-product-vendors'  ),
                $cog
            );
            return $msg;
        }

        /**
         * Add cog order item meta
         *
         * @param $item_id
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_cog_order_item_meta( $item_id ){
            wc_add_order_item_meta( $item_id, $this->cog_oder_item_meta_name, $this->exclude_cog_in_commission ? 'no' : 'yes' );
        }

        /**
         * no sync for cog order item meta
         *
         * @param $no_sync
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function order_item_meta_no_sync( $no_sync ){
            $no_sync[] = $this->cog_oder_item_meta_name;
            return $no_sync;
        }

        /**
         * Order details commission message
         *
         * @param $msg
         * @param $item_id
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function order_details_page_commission_message( $msg, $item_id ){
            $commission_included_cog = wc_get_order_item_meta( $item_id, $this->cog_oder_item_meta_name, true );
            if( $commission_included_cog !== '' ){
                $cog = 'yes' == $commission_included_cog    ? _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ) : _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' );
                $msg = sprintf( '%s <small><em>- %s <strong>%s</strong></em></small>', $msg, _x( 'cost of goods', 'part of: cost of goods included or cost of goods excluded', 'yith-woocommerce-product-vendors'  ), $cog);
            }

            return $msg;
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_WooCommerce_Cost_Of_Goods_Support
 * @since  1.11.4
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WooCommerce_Cost_Of_Goods_Support' ) ) {
    function YITH_WooCommerce_Cost_Of_Goods_Support() {
        return YITH_WooCommerce_Cost_Of_Goods_Support::instance();
    }
}

YITH_WooCommerce_Cost_Of_Goods_Support();
