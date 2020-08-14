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
 * @class      YITH_Cost_Of_Goods_Support
 * @package    Yithemes
 * @since      Version 1.11.4
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Cost_Of_Goods_Support' ) ) {

    /**
     * YITH_WooCommerce_Cost_Of_Goods_Support Class
     */
    class YITH_Cost_Of_Goods_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * YITH_COG instance
         */
        public $yith_cog = null;

        /**
         * YITH_COG_Admin instance
         */
        public $yith_cog_admin = null;

        /**
         * YITH_COG_Admin_Premium instance
         */
        public $yith_cog_admin_premium = null;

        /**
         * YITH_COG_Orders instance
         */
        public $yith_cog_orders = null;

        /**
         * show cog info to vendor
         */
        public $show_yith_cog_info = true;

        /**
         * show cog info to vendor
         */
        public $exclude_yith_cog_in_commission = false;

        /**
         * @var bool order item meta name
         */
        public $yith_cog_oder_item_meta_name = '_commission_included_yith_cost_of_goods';

        /**
         * Construct
         */
        public function __construct(){
            /**
             * Object initialization
             */
            $this->yith_cog = YITH_COG::instance();
            $this->yith_cog_admin =  $this->yith_cog->admin;

            if( $this->yith_cog_admin instanceof YITH_COG_Admin ){
                $this->yith_cog_orders   = YITH_COG_Orders::get_instance();
            }

            /**
             * Privacy option
             */
            $this->show_yith_cog_info = 'yes' == get_option( 'yith_wpv_show_cog_info', 'yes' );

            /**
             * Exclude cog in commission calculation
             */
            $this->exclude_yith_cog_in_commission = 'no' == get_option( 'yith_wpv_include_cog', 'yes' );


            // Add cost of goods in vendor suborder - Checkout
            add_action( 'yith_wcmv_checkout_order_processed', array( $this->yith_cog_orders, 'set_order_cost_meta' ), 10, 1 );

            // Add options for WC COG under commissions table
            add_filter( 'yith_wpv_panel_commissions_options', array( $this, 'add_yith_cog_options' ), 20, 2 );


            if( $this->show_yith_cog_info ){
                // Add Cost Of Goods column header to commission details page
                add_action( 'yith_wcmv_admin_order_item_headers', array( $this->yith_cog_orders, 'yith_cog_order_item_header' ), 10, 1 );

                // Add cost of goods value in line item row in commission details page
                add_action( 'yith_wcmv_admin_order_item_values', array( $this->yith_cog_orders, 'yith_cog_order_item_value' ), 10, 3 );
            }


            if( $this->exclude_yith_cog_in_commission ){
                add_filter( 'yith_wcmv_get_line_total_amount_for_commission', array( $this, 'exclude_cog_from_commission' ), 10, 4 );
            }

            // Commission note message
            add_filter( 'yith_wcmv_new_commission_note', array( $this, 'new_commission_note' ) );

            // Add cog order item meta
            add_action( 'yith_wcmv_add_extra_commission_order_item_meta', array( $this, 'add_cog_order_item_meta' ), 10, 1 );

            // Exclude cog order item meta to parent/child order sync
            add_filter( 'yith_wcmv_order_item_meta_no_sync', array( $this, 'order_item_meta_no_sync' ) );

            // Add cog message in order details page
            add_filter( 'yith_wcmv_order_details_page_commission_message', array( $this, 'order_details_page_commission_message' ), 10, 2 );


            $vendor = yith_get_vendor( 'current', 'user' );
            if( $vendor->is_valid() && $vendor->has_limited_access() && $this->yith_cog_admin instanceof YITH_COG_Admin ){
                // Remove cost field to simple products under the 'General' tab
                remove_action( 'woocommerce_product_options_pricing', array( $this->yith_cog_admin , 'add_cost_field_to_simple_product' ) );

                // Remove cost field to variable products under the 'General' tab
                remove_action( 'woocommerce_product_options_sku', array( $this->yith_cog_admin, 'add_cost_field_to_variable_product' ) );

                // Remove in quick edit
                remove_action( 'woocommerce_product_quick_edit_end',  array( $this->yith_cog_admin, 'render_quick_edit_cost_field' ) );

                // Remove in bulk edit
                remove_action( 'woocommerce_product_bulk_edit_end', array( $this->yith_cog_admin, 'bulk_actions_edit_product' ) );
            }
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Cost_Of_Goods_Support Main instance
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
        public function add_yith_cog_options( $options, $tab ){
            $new_options = array();
            $new_options['commissions'] = array(
                'yith_cog_options_start'          => array(
                    'type'  => 'sectionstart',
                ),

                'yith_cog_options_title'          => array(
                    'title' => __( 'YITH WooCommerce Cost of Goods', 'yith-woocommerce-product-vendors' ),
                    'type'  => 'title',
                    'desc'  => '',
                ),

                'yith_cog_default_handling' => array(
                    'title'             => __( 'YITH Cost of Goods handling', 'yith-woocommerce-product-vendors' ),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                    'desc'              => __( 'Include cost of goods in commission calculations', 'yith-woocommerce-product-vendors' ),
                    'desc_tip'          => __( 'Decide whether vendor commissions have to be calculated including cost of goods value or not.', 'yith-woocommerce-product-vendors' ),
                    'id'                => 'yith_wpv_include_cog',
                ),

                'yith_cog_show_info' => array(
                    'title'             => __( 'Show YITH Cost of Goods information', 'yith-woocommerce-product-vendors' ),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                    'desc'              => __( 'Show cost of goods information in commission details page', 'yith-woocommerce-product-vendors' ),
                    'id'                => 'yith_wpv_show_cog_info',
                ),

                'yith_cog_options_end'          => array(
                    'type'  => 'sectionend',
                ),
            );

            $to_return['commissions'] = array_merge( $options['commissions'], $new_options['commissions'] );
            return $to_return;
        }

        /**
         * Exclude yith cog cost to commissions
         *
         * @param $line_total
         * @param $order
         * @param $item
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function exclude_cog_from_commission( $line_total, $order, $item, $item_id ){
            $item_total_cost = wc_get_order_item_meta( $item_id, '_yith_cog_item_cost', true );

            if( ! empty( $item_total_cost ) ){
                $line_total = $line_total - $item_total_cost;
            }

            return $line_total;
        }

        /**
         * Add yith cog info to commissions note
         *
         * @param $msg
         * @return mixed
         * @since  1.11.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function new_commission_note( $msg ){
            $yith_cog = $this->exclude_yith_cog_in_commission ? _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' ) : _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' );
            $msg = sprintf( '%s:<br>* %s <em>%s</em>',
                $msg,
                _x( 'cost of goods', 'part of: cost of goods included or cost of goods excluded', 'yith-woocommerce-product-vendors'  ),
                $yith_cog
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
            wc_add_order_item_meta( $item_id, $this->yith_cog_oder_item_meta_name, $this->exclude_yith_cog_in_commission ? 'no' : 'yes' );
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
            $no_sync[] = $this->yith_cog_oder_item_meta_name;
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
            $commission_included_yith_cog = wc_get_order_item_meta( $item_id, $this->yith_cog_oder_item_meta_name, true );
            if( $commission_included_yith_cog !== '' ){
                $yith_cog = 'yes' == $commission_included_yith_cog    ? _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ) : _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' );
                $msg = sprintf( '%s <small><em>- %s <strong>%s</strong></em></small>', $msg, _x( 'cost of goods', 'part of: cost of goods included or cost of goods excluded', 'yith-woocommerce-product-vendors'  ), $yith_cog);
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
if ( ! function_exists( 'YITH_Cost_Of_Goods_Support' ) ) {
    function YITH_Cost_Of_Goods_Support() {
        return YITH_Cost_Of_Goods_Support::instance();
    }
}

YITH_Cost_Of_Goods_Support();
