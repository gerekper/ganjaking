<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_Section_Dashboard' ) ) {

    class YITH_Frontend_Manager_Section_Dashboard extends YITH_WCFM_Section {

        /**
         * Constructor method
         *
         * @return \YITH_Frontend_Manager_Section
         * @since 1.0.0
         */
        public function __construct() {
            $this->id                    = 'dashboard';
            $this->_default_section_name = _x( 'Dashboard', '[Frontend]: Dashboard menu item', 'yith-frontend-manager-for-woocommerce' );
            add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'set_dashboard_first_menu_item' ), 99 );

            parent::__construct();
        }

        /* === SECTION METHODS === */

        /**
         * Print section
         *
         * To be extended on sub classes
         *
         * @author Antonio La Rocca <antonio.larocca@yithemes.com>
         * @return void
         * @since  1.0.0
         */
        public function print_section( $subsection = '', $section = '', $atts = array() ) {
            
            if( ! is_user_logged_in() ){
                return false;
            }

            if( $this->is_enabled() ) {

                global $wpdb;

                $required = array( 'class-wc-admin-report.php', 'class-wc-report-sales-by-date.php' );

                foreach( $required as $file ){
                    yith_wcfm_include_woocommerce_core_file( $file, 'includes/admin/reports' );
                }

                $sales_by_date = new WC_Report_Sales_By_Date();
	            $sales_by_date->start_date = strtotime(date('Y-m-01', current_time('timestamp')));
                $sales_by_date->end_date = current_time('timestamp');
                $sales_by_date->chart_groupby = 'day';
                $sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';
                $report_data = $sales_by_date->get_report_data();

                // Get top seller
                $query = array();
                $query['fields'] = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id FROM {$wpdb->posts} as posts";
                $query['join'] = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
                $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
                $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
                $query['where'] = "WHERE posts.post_type IN ( '" . implode("','", wc_get_order_types('order-count')) . "' ) ";
                $query['where'] .= "AND posts.post_status IN ( 'wc-" . implode("','wc-", apply_filters('woocommerce_reports_order_statuses', array('completed', 'processing', 'on-hold'))) . "' ) ";
                $query['where'] .= "AND order_item_meta.meta_key = '_qty' ";
                $query['where'] .= "AND order_item_meta_2.meta_key = '_product_id' ";
                $query['where'] .= "AND posts.post_date >= '" . date('Y-m-01', current_time('timestamp')) . "' ";
                $query['where'] .= "AND posts.post_date <= '" . date('Y-m-d H:i:s', current_time('timestamp')) . "' ";
                $query['groupby'] = "GROUP BY product_id";
                $query['orderby'] = "ORDER BY qty DESC";
                $query['limits'] = "LIMIT 1";

                $top_seller = $wpdb->get_row(implode(' ', apply_filters('woocommerce_dashboard_status_widget_top_seller_query', $query)));

                // Counts
                $on_hold_count = 0;
                $processing_count = 0;

                foreach (wc_get_order_types('order-count') as $type) {
                    $counts = (array)wp_count_posts($type);
                    $on_hold_count += isset($counts['wc-on-hold']) ? $counts['wc-on-hold'] : 0;
                    $processing_count += isset($counts['wc-processing']) ? $counts['wc-processing'] : 0;
                }

                // Get products using a query - this is too advanced for get_posts :(
                $stock = absint(max(get_option('woocommerce_notify_low_stock_amount'), 1));
                $nostock = absint(max(get_option('woocommerce_notify_no_stock_amount'), 0));
                $transient_name = 'wc_low_stock_count';
                $lowinstock_count = apply_filters('yith_wcfm_low_stock_count_transient', get_transient($transient_name));

                if (false === $lowinstock_count) {
                    $query_from = apply_filters('woocommerce_report_low_in_stock_query_from', "FROM {$wpdb->posts} as posts
                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                        INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
                        WHERE 1=1
                        AND posts.post_type IN ( 'product', 'product_variation' )
                        AND posts.post_status = 'publish'
                        AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
                        AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
                        AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
                    ");
                    $lowinstock_count = absint($wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};"));
                    if (apply_filters('yith_wcfm_save_stock_transient', true)) {
                        set_transient($transient_name, $lowinstock_count, DAY_IN_SECONDS * 30);
                    }
                }

                $transient_name = 'wc_outofstock_count';
                $outofstock_count = apply_filters('yith_wcfm_outofstock_count_transient', get_transient($transient_name));

                if (false === $outofstock_count) {
                    $query_from = apply_filters('woocommerce_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                        INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
                        WHERE 1=1
                        AND posts.post_type IN ( 'product', 'product_variation' )
                        AND posts.post_status = 'publish'
                        AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
                        AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'
                    ");

                    $outofstock_count = absint($wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};"));
                    if (apply_filters('yith_wcfm_save_stock_transient', true)) {
                        set_transient($transient_name, $outofstock_count, DAY_IN_SECONDS * 30);
                    }
                }

	            $atts = apply_filters( 'yith_wcfm_print_dashboard_section_args', array(
			            'report_data'      => $report_data,
			            'processing_count' => $processing_count,
			            'on_hold_count'    => $on_hold_count,
			            'lowinstock_count' => $lowinstock_count,
			            'outofstock_count' => $outofstock_count,
			            'labels' => array(
				            'net_sales'       => __( 'Net sales this month', 'yith-frontend-manager-for-woocommerce' ),
				            'process_orders'  => __( 'Awaiting process orders', 'yith-frontend-manager-for-woocommerce' ),
				            'on_hold_orders'  => __( 'On-hold orders', 'yith-frontend-manager-for-woocommerce' ),
				            'low_stock_level' => __( 'Low stock level', 'yith-frontend-manager-for-woocommerce' ),
				            'out_of_stock'    => __( 'Products out of stock', 'yith-frontend-manager-for-woocommerce' )
			            ),
		            )
	            );

                yith_wcfm_get_template($subsection, $atts, 'sections/' . $section);
            }

            else {
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
        }

        /**
         * Print shortcode function
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function print_shortcode( $atts = array(), $content = '', $tag ) {
            $section = $subsection = str_replace( $this->get_shortcodes_prefix(), '', $tag );
            $atts['current_user'] = wp_get_current_user();

            if ( empty( $section ) ) {
                $section = $subsection = ! empty( $atts['section'] ) ? $atts['section'] : $this->id;
            }

            $this->print_section( $subsection, $section, $atts );
        }

        /**
         * Set dahboard to first menu item
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return array Sections array for navigation menu
         * @since 1.0.0
         */
        final public function set_dashboard_first_menu_item( $sections ) {
            $key  = $this->id;
            if( isset( $sections[ $key ] ) ){
                $temp = array( $key => $sections[ $key ] );
                unset( $sections[ $key ] );
                $sections = $temp + $sections;
            }

            return $sections;
        }
    }
}

