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
 * @class      YITH_WCCustomerOrderExport_Support
 * @package    Yithemes
 * @since      Version 1.9.8
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCCustomerOrderExport_Support' ) ) {

    /**
     * YITH_WCCustomerOrderExport_Support Class
     */
    class YITH_WCCustomerOrderExport_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
            add_action( 'load-edit.php', array( $this, 'customer_order_csv_export'), 5  );
            add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'export_order_headers' ) );
            add_filter( 'wc_customer_order_csv_export_order_row', array( $this, 'export_order_row_one_row_per_item'), 10,3 );
        }

        /**
         * Add Vendor order to $_POST array
         *
         * @return void
         *
         * @since  1.9.8
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */    
        public function customer_order_csv_export(){
            global $typenow;

            if ( 'shop_order' == $typenow ) {

                // get the action
                $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
                $action = $wp_list_table->current_action();

                // return if not processing our actions
                if ( ! in_array( $action, array( 'download_to_csv', 'mark_exported_to_csv', 'mark_not_exported_to_csv' ) ) ) {
                    return;
                }

                // security check
                check_admin_referer( 'bulk-posts' );
                $_request_post = array();

                // make sure order IDs are submitted
                if ( isset( $_REQUEST['post'] ) ) {
                    $order_ids = array_map( 'absint', $_REQUEST['post'] );
                    $_request_post = $order_ids;
                }

                // return if there are no orders to export
                if ( empty( $order_ids ) ) {
                    return;
                }

                foreach( $order_ids as $order_id ){
                    $suborder_ids = YITH_Vendors()->orders->get_suborder( $order_id );
                    if( $suborder_ids ){
                        $_request_post = array_merge( $_request_post, $suborder_ids );
                    }

                    $_REQUEST['post'] = $_request_post;
                }
            }
        }

        /**
         * Add post_author_id and post_parent_id to order list
         *
         * @return mixed The porder data array
         *
         * @since  1.9.8
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function export_order_row_one_row_per_item( $order_data, $order, $WC_Customer_Order_CSV_Export_Generator ){
            $order_id = yit_get_prop( $order, 'id' );
            $post_author = get_post_field( 'post_author', $order_id );
            $order_data[0]['order_author'] = $post_author;
            $order_data[0]['parent_order'] = wp_get_post_parent_id( $order_id );
            return $order_data;
        }

        /**
         * Add post_author and post_parent CSV Header 
         *
         * @return mixed The CSV Headers data array
         *
         * @since  1.9.8
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function export_order_headers( $headers ){
            $headers['order_author'] = 'order_author';
            $headers['parent_order'] = 'parent_order';
            return $headers;
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_WCCustomerOrderExport_Support Main instance
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
 * @return /YITH_WCCustomerOrderExport_Support
 * @since  1.9.8
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WCCustomerOrderExport_Support' ) ) {
    function YITH_WCCustomerOrderExport_Support() {
        return YITH_WCCustomerOrderExport_Support::instance();
    }
}

YITH_WCCustomerOrderExport_Support();
