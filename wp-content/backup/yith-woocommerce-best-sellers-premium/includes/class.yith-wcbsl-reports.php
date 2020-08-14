<?php

/**
 * Reports class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

require_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

if ( !class_exists( 'YITH_WCBSL_Reports' ) ) {
    /**
     * Install class.
     * for first activation of plugin
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0.0
     */
    class YITH_WCBSL_Reports extends WC_Admin_Report {

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {

        }

        /**
         * Check if a product is a Bestseller
         *
         * @access public
         * @return bool
         *
         * @param $prod_id int id of product
         * @param $range   string the range of bestseller search
         * @param $args    array args passed to get_best_sellers
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function check_is_bestseller( $prod_id, $range = 'ever', $args = array() ) {
            $bestsellers = $this->get_best_sellers( $range, $args );
            if ( !empty( $bestsellers ) ) {
                foreach ( $bestsellers as $bestseller ) {
                    if ( $bestseller->product_id == $prod_id )
                        return true;
                }
            }

            return false;
        }


        public function get_best_sellers( $range = 'ever', $args = array() ) {
            $filter_range = false;
            if ( $range != 'ever' ) {
                $range_args = isset( $args[ 'range_args' ] ) ? $args[ 'range_args' ] : array();
                $this->calculate_current_range( $range, $range_args );
            }

            $limit = isset( $args[ 'limit' ] ) ? $args[ 'limit' ] : 100;

            $order_report_data_array = array(
                'data'         => array(
                    '_product_id' => array(
                        'type'            => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function'        => '',
                        'name'            => 'product_id'
                    ),
                    '_qty'        => array(
                        'type'            => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function'        => 'SUM',
                        'name'            => 'order_item_qty'
                    )
                ),
                'where_meta'   => array(
                    array(
                        'type'       => 'order_item_meta',
                        'meta_key'   => '_line_subtotal',
                        'meta_value' => '0',
                        'operator'   => '>'
                    )
                ),
                'order_by'     => 'order_item_qty DESC',
                'group_by'     => 'product_id',
                'limit'        => $limit,
                'query_type'   => 'get_results',
                'filter_range' => $filter_range,
                'order_types'  => wc_get_order_types( 'order-count' ),

            );

            if ( $limit == -1 )
                unset( $order_report_data_array[ 'limit' ] );

            // create the unique transient name for this request
            $transient_name = strtolower( get_class( $this ) );
            $arguments      = $range;
            if ( !empty( $args ) ) {
                foreach ( $args as $key => $value ) {
                    $arguments .= strtolower( $key ) . $value;
                }
            }

            $transient_name = $transient_name . md5( $arguments );

            $best_sellers = get_transient( $transient_name );

	        $is_debug = defined( 'YITH_WCBSL_DEBUG' ) ? YITH_WCBSL_DEBUG : false;

            if ( !$best_sellers || $is_debug) {
                // delete transient
                delete_transient( strtolower( get_class( $this ) ) );
                // get data
                $best_sellers = $this->get_order_report_data( $order_report_data_array );

                // Filter Existing products
                foreach ( $best_sellers as $key => $bs_product ) {
                    $product = wc_get_product( absint( $bs_product->product_id ) );
                    if ( !$product || apply_filters( 'yith_wcbs_remove_best_seller', false, $product, $bs_product ) )
                        unset( $best_sellers[ $key ] );
                }

                $best_sellers = apply_filters( 'yith_wcbs_get_best_sellers', $best_sellers, $range, $args );

                // set the transient, with expiration one hour
                set_transient( $transient_name, $best_sellers, 3600 );
            }

            return $best_sellers;
        }


        /**
         * Get the current range and calculate the start and end dates
         *
         * @param  string $current_range
         */
        public function calculate_current_range( $current_range, $args = array() ) {

            switch ( $current_range ) {

                case 'yith_custom' :
                    if ( isset( $args[ 'start_date' ] ) && isset( $args[ 'end_date' ] ) ) {
                        $this->start_date = strtotime( sanitize_text_field( $args[ 'start_date' ] ) );
                        $this->end_date   = strtotime( 'midnight', strtotime( sanitize_text_field( $args[ 'end_date' ] ) ) );
                    }

                    if ( !$this->end_date ) {
                        $this->end_date = current_time( 'timestamp' );
                    }

                    $interval = 0;
                    $min_date = $this->start_date;

                    while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
                        $interval++;
                    }

                    // 3 months max for day view
                    if ( $interval > 3 ) {
                        $this->chart_groupby = 'month';
                    } else {
                        $this->chart_groupby = 'day';
                    }
                    break;

                case 'custom' :
                    $this->start_date = strtotime( sanitize_text_field( $_GET[ 'start_date' ] ) );
                    $this->end_date   = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET[ 'end_date' ] ) ) );

                    if ( !$this->end_date ) {
                        $this->end_date = current_time( 'timestamp' );
                    }

                    $interval = 0;
                    $min_date = $this->start_date;

                    while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
                        $interval++;
                    }

                    // 3 months max for day view
                    if ( $interval > 3 ) {
                        $this->chart_groupby = 'month';
                    } else {
                        $this->chart_groupby = 'day';
                    }
                    break;

                case 'year' :
                    $this->start_date    = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
                    $this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
                    $this->chart_groupby = 'month';
                    break;

                case 'last_month' :
                    $first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
                    $this->start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
                    $this->end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
                    $this->chart_groupby     = 'day';
                    break;

                case 'month' :
                    $this->start_date    = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
                    $this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
                    $this->chart_groupby = 'day';
                    break;

                case 'yesterday':
                    $this->start_date    = strtotime( '-1 DAY midnight', current_time( 'timestamp' ) );
                    $this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
                    $this->chart_groupby = 'day';
                    break;

                case 'today':
                    $this->start_date    = strtotime( 'midnight', current_time( 'timestamp' ) );
                    $this->end_date      = strtotime( '+1 DAY midnight', current_time( 'timestamp' ) );
                    $this->chart_groupby = 'day';
                    break;

                case '7day' :
                    $this->start_date    = strtotime( '-6 days', current_time( 'timestamp' ) );
                    $this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
                    $this->chart_groupby = 'day';
                    break;
            }

            // Group by
            switch ( $this->chart_groupby ) {

                case 'day' :
                    $this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';
                    $this->chart_interval = ceil( max( 0, ( $this->end_date - $this->start_date ) / ( 60 * 60 * 24 ) ) );
                    $this->barwidth       = 60 * 60 * 24 * 1000;
                    break;

                case 'month' :
                    $this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date)';
                    $this->chart_interval = 0;
                    $min_date             = $this->start_date;

                    while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
                        $this->chart_interval++;
                    }

                    $this->barwidth = 60 * 60 * 24 * 7 * 4 * 1000;
                    break;
            }
        }
    }
}