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

if ( ! class_exists( 'YITH_Reports' ) ) {


    class YITH_Reports extends WC_Admin_Reports {

        /** @protected array Main Instance */
        protected static $_instance = null;

        protected $_report_path;

        public function __construct() {
            $this->_report_path = YITH_WPV_PATH . 'includes/reports/';

            /* === Filter WC Admin Reports === */
            add_filter( 'woocommerce_admin_reports', array( $this, 'set_wc_reports' ), 20 );

            /* === Filter WC Orders Reports === */
            add_filter( 'woocommerce_reports_get_order_report_data_args', array( $this, 'filter_report_get_order_args' ) );
            add_filter( 'woocommerce_json_search_found_products', array( $this, 'json_filter_report_products' ) );
            add_filter( 'woocommerce_report_sales_by_category_get_products_in_category', array( $this, 'filter_report_products_in_category' ), 10, 2 );

            /* === Filter WC Stock Reports === */
            add_filter( 'woocommerce_report_low_in_stock_query_from', array( $this, 'filter_report_stock_query_from' ) );
            add_filter( 'woocommerce_report_out_of_stock_query_from', array( $this, 'filter_report_stock_query_from' ) );
            add_filter( 'woocommerce_report_most_stocked_query_from', array( $this, 'filter_report_stock_query_from' ) );

            /* === Filter WC Customer List Reports === */
            add_filter( 'wc_admin_reports_path', array( $this, 'wc_admin_reports_path' ), 10, 3 );
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
         * Check if the current page is a wc report page
         *
         * @return bool
         */
        public function is_report_page(){
            return ! empty( $_GET['page'] ) && 'wc-reports' == $_GET['page'];
        }

        /**
         * Set vendors reports by capabilities
         *
         * @param $wc_reports The reports array
         * @since 1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return array The new reports array
         */
        public function set_wc_reports( $wc_reports ) {
            $vendor     = yith_get_vendor( 'current', 'user' );
            $callback   = array( $this, 'load_report' );

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                $not_enabled = apply_filters( 'yith_vendor_not_allowed_reports', array(
                        'customers' => false,
                        'taxes'     => false,
                        'orders'    => array( 'coupon_usage' ),
                    )
                );

                /**
                 * Remove reports from WooCommerce Subscription
                 */
                if( class_exists( 'WCS_Admin_Reports' ) ){
                    $not_enabled['subscriptions'] = false;
                }

                foreach( $not_enabled as $section => $sub_reports ){
                    if( ! empty( $sub_reports ) ){
                        foreach( $sub_reports as $key => $sub_report ){
                            if( isset( $wc_reports[ $section ][ 'reports' ][ $sub_report ] ) ){
                                unset( $wc_reports[ $section ][ 'reports' ][ $sub_report ] );
                            }
                        }
                    }
                    else {
                        if( isset( $wc_reports[ $section ] ) ){
                            unset( $wc_reports[ $section ] );
                        }
                    }
                }
                // Change Sales-by_date Report Callback
                $wc_reports['orders']['reports']['sales_by_date']['callback'] = $callback;

                //Enable this report for all users
                $wc_reports['commissions'] = array(
                    'title'   => __( 'Commissions', 'yith-woocommerce-product-vendors' ),
                    'reports' => array(
                        'sale_commissions' => array(
                            'title'       => __( 'Sale Commissions', 'yith-woocommerce-product-vendors' ),
                            'description' => '',
                            'hide_title'  => false,
                            'callback'    => $callback
                        )
                    )
                );
            }

            else {
                $reports = array(
                    'vendors' => array(
                        'title'   => __( 'Vendors', 'yith-woocommerce-product-vendors' ),
                        'reports' => array(
                            "vendors_sales"      => array(
                                'title'       => __( 'Vendor Sales', 'yith-woocommerce-product-vendors' ),
                                'description' => '',
                                'hide_title'  => true,
                                'callback'    => $callback
                            ),

                            "vendors_registered" => array(
                                'title'       => __( 'Registered Vendors', 'yith-woocommerce-product-vendors' ),
                                'description' => '',
                                'hide_title'  => true,
                                'callback'    => $callback
                            ),

                            "commissions_by_vendor" => array(
                                'title'       => __( 'Commissions by Vendor', 'yith-woocommerce-product-vendors' ),
                                'description' => '',
                                'hide_title'  => true,
                                'callback'    => $callback
                            ),
                        )
                    )
                );
                $wc_reports = array_merge( $wc_reports, $reports );
            }
            return $wc_reports;
        }

        /**
         * Get a report from our reports subfolder
         */
        public function load_report( $name ) {
            $class = 'YITH_Report_' . $name;
            $name  = 'class.yith-report-' . sanitize_title( str_replace( '_', '-', $name ) ) . '.php';

            if ( file_exists( $this->_report_path . $name ) ) {
                include_once( $this->_report_path . $name );
            } else if ( ! class_exists( $class ) ) {
                return;
            }

            $report = new $class();
            $report->output_report();
        }

        /**
         * Output an export link
         */
        public function get_export_button() {
            $current_range = $this->get_current_date_range();
            ?>
            <a
                href="#"
                download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
                class="export_csv"
                data-export="chart"
                data-xaxes="<?php _e( 'Date', 'yith-woocommerce-product-vendors' ); ?>"
                data-groupby="<?php echo isset( $this->chart_groupby ) ? $this->chart_groupby : 'day'; ?>"
                >
                <?php _e( 'Export CSV', 'yith-woocommerce-product-vendors' ); ?>
            </a>
        <?php
        }

        /**
         * Set reports args
         *
         * @param $args The query args
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return array The new query args
         */
        public function filter_report_get_order_args( $args ){
            $vendor = yith_get_vendor( 'current', 'user' );

            /**
             * Check for report: If no report selected set report to default value "Sales by Date"
             */
            $report = 'sales_by_date';

            if( isset( $_GET['report'] ) ){
                $report = $_GET['report'];
            }

            elseif( isset( $_GET['tab'] ) ) {
                //Tab with one report don't have report field in query args
                $report = $_GET['tab'];
            }

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                if ( 'sale_commissions' == $report || 'commissions' == $report || 'sales_by_product' == $report || 'sales_by_category' == $report ) {
                    $orders = $vendor->get_orders();

                    $args['where'] = array(
                        array(
                            'key'      => 'posts.ID',
                            'operator' => 'in',
                            'value'    => ! empty( $orders ) ? $orders : -1
                        )
                    );
                }

                elseif ( 'sales_by_product' == $report ) {

                    $filter = false;
                    $products = $vendor->get_products();

                    $filtered_where_meta = array(
                        'type'       => 'order_item_meta',
                        'meta_key'   => '_product_id',
                        'meta_value' => ! empty( $products ) ? $products : -1,
                        'operator'   => 'in'
                    );

                    //no products filter active
                    if( ! isset( $_GET['product_ids'] ) ) {
                        $filter = true;
                    }

                    //products filter active
                    else {
                        //Top Sellers
                        if ( ! isset( $args['where_meta'] ) ) {
                            $filter = true;
                        }

                        //Top Earners and Top Freebies
                        elseif ( isset( $args['data'] ) && isset( $args['data']['_qty'] ) && isset( $args['data']['_product_id'] ) && isset( $args['where_meta'] ) ) {
                            $top_earners = array(
                                'type'       => 'order_item_meta',
                                'meta_key'   => '_line_subtotal',
                                'meta_value' => '0',
                                'operator'   => '>'
                            );

                            $top_freebies = array(
                                'type'       => 'order_item_meta',
                                'meta_key'   => '_line_subtotal',
                                'meta_value' => '0',
                                'operator'   => '='
                            );

                            if ( in_array( $top_earners, $args['where_meta'] ) || in_array( $top_freebies, $args['where_meta'] ) ) {
                                $filter = true;
                            }
                        }
                    }

                    if( $filter ){
                        $args['where_meta'][] = $filtered_where_meta;
                    }
                }

                elseif( 'sales_by_categories' == $report ){

                }
            }

            elseif ( $vendor->is_super_user() ) {
                //Orders Report
                $orders_report = array(
                    'sales_by_date',
                    'sales_by_product',
                    'sales_by_category',
                    'coupon_usage',
                    'orders'
                );

                if ( in_array( $report, $orders_report ) || 'customers' == $report || 'customer_list' == $report ) {

                    $group_by_refund_id = isset( $args['group_by'] ) && 'refund_id' == $args['group_by'];

	                if( $group_by_refund_id  ){

		                $date_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';
		                $first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );

		                $date_query = array(
			                '7day'       => array(
				                array(
					                'after' => '1 week ago'
				                )
			                ),
			                'month'      => array(
				                array(
					                'after'     => date( 'Y-m-01', current_time( 'timestamp' ) ),
					                'inclusive' => true,
				                ),
			                ),
			                'last_month' => array(
				                array(
					                'after'     => date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ),
					                'before'    => date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ),
					                'inclusive' => true,
				                ),
			                ),
			                'year'       => array(
				                array(
					                'after'     => date( 'Y-01-01', current_time( 'timestamp' ) ),
					                'inclusive' => true,
				                )
			                ),
			                'custom'     => array(
				                array(
					                'after'     => ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '',
					                'before'    => ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '',
					                'inclusive' => true,
				                )
			                )
		                );

		                $parent_order = get_posts(
			                array(
				                'post_type'         => 'shop_order',
				                'post_parent'       => 0,
				                'post_status'       => -1,
				                'fields'            => 'ids',
				                'posts_per_page'    => -1,
                                'date_query' => $date_query[ $date_range ]
			                )
		                );

		                if( $parent_order ){
			                $args['data']['post_parent'] = array(
				                'type'     => 'post_data',
				                'function' => '',
				                'name'     => 'parent_order_id'
			                );

			                if( ! array_search( 'refunded', $args['parent_order_status'] ) ) {
				                $args['parent_order_status'][] = 'refunded';
                            }

			                $args['where'] = array(
				                array(
					                'key'      => 'posts.post_parent',
					                'operator' => 'IN',
					                'value'    => $parent_order
				                ),
			                );
		                }
	                }

	                else {
		                $args['where'] = array(
			                array(
				                'key'      => 'posts.post_parent',
				                'operator' => '=',
				                'value'    => 0
			                ),
		                );
                    }
                }
            }
            return $args;
        }

        /**
         * Filter the products in category
         *
         * @param $product_ids The products ids
         * @param $category_id The product_cat term id
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return array The product ids array
         */
        public function filter_report_products_in_category( $product_ids, $category_id ) {
            $vendor = yith_get_vendor( 'current', 'user' );

            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                $args                = $vendor->get_query_products_args();
                $args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id',
                    'terms'    => $category_id
                );
                $product_ids         = $vendor->get_products( $args );
            }

            return $product_ids;
        }

        /**
         * Set product reports by vendor
         *
         * @param $products The products array to filter
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return array The new query args
         */
        public function json_filter_report_products( $products ){
            $vendor = yith_get_vendor( 'current', 'user' );
            $filtered_product = array();

            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                foreach( $vendor->get_products() as $product_id ){
                    if( isset( $products[ $product_id ] ) ){
                        $filtered_product[ $product_id ] = $products[ $product_id ];
                    }
                }
                $products = $filtered_product;
            }
            return $products;
        }

        /**
         * Get the allowed vendors post id
         *
         * @param $query_from The query FROM
         *
         * @return array The post id
         */
        public function filter_report_stock_query_from( $query_from ) {
            $vendor = yith_get_vendor( 'current', 'user' );

            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                $product_ids = implode( ",", $vendor->get_products() );
                $product_ids = ! empty( $product_ids ) ? $product_ids : -1;
                $query_from .= "AND posts.ID IN ({$product_ids})";
            }

            return $query_from;
        }

         /**
         * Get the date ranges
         *
         * @return array
         */
        public function get_ranges(){
            return array(
                'year'       => __( 'Year', 'yith-woocommerce-product-vendors' ),
                'last_month' => __( 'Last Month', 'yith-woocommerce-product-vendors' ),
                'month'      => __( 'This Month', 'yith-woocommerce-product-vendors' ),
                '7day'       => __( 'Last 7 Days', 'yith-woocommerce-product-vendors' )
            );
        }

        /**
         * Get the date query args for WP_Query
         *
         * @param $start_date   The start date
         * @param $end_date     The end date
         *
         * @return array The query args
         */
        public function get_wp_query_date_args( $start_date, $end_date ) {
            return array(
                'date_query' => array(
                    'after'     => array(
                        'year'  => date( 'Y', $start_date ),
                        'month' => date( 'n', $start_date ),
                        'day'   => date( 'j', $start_date )
                    ),
                    'before'    => array(
                        'year'  => date( 'Y', $end_date ),
                        'month' => date( 'n', $end_date ),
                        'day'   => date( 'j', $end_date )
                    ),
                    'inclusive' => true
                )
            );
        }

        /**
         * Get hte current date range
         *
         * @return string The current range
         */
        public function get_current_date_range(){
            $current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

            if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
                $current_range = '7day';
            }
            return $current_range;
        }

        public function wc_admin_reports_path( $path, $name, $class ){
            if( 'customer-list' == $name ){
                $path = YITH_WPV_PATH . 'includes/reports/class.yith-report-customer-list.php';
            }
            return $path;
        }
        /**
         * Select2 args for reports
         *
         * @return array
         * @since 1.2.0
         * @author Andrea Grillo <andrea.grillo@tyithemes.com>
         */
        public function get_select2_args(){
            $select2_args  =array(
                'class'             => "wc-product-search",
                'style'             => "width:203px;",
                'name'              => "vendor_ids",
                'data-placeholder'  => __( 'Search for a vendor&hellip;', 'yith-woocommerce-product-vendors' ),
                'data-action'       => "yith_json_search_vendors",
            );
            return $select2_args;
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return YITH_Vendors_Report
 * @since  1.0
 */
if ( ! function_exists( 'YITH_Reports' ) ) {
    /**
     * @return YITH_Reports
     */
    function YITH_Reports() {
        return YITH_Reports::instance();
    }
}

YITH_Reports();
