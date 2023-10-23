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
            add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_custom_query_var' ), 10, 2 );

            parent::__construct();
        }

        /* === SECTION METHODS === */

        /**
         * Print section
         *
         * To be extended on sub classes
         *
         * @author YITH <plugins@yithemes.com>
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

                $transient_name = 'wc_low_stock_count';

                // Retrieve All Product types 
                $product_types = wc_get_product_types();
                if( !isset( $product_types['variation'] ) ) {
                    $product_types = array_keys( $product_types );
                    $product_types[] = 'variation';
                }

				/**
				 * APPLY_FILTERS: yith_wcfm_low_stock_count_transient
				 *
				 * Filter transient
				 *
				 * @param mixed $lowinstock_count Value of transient
				 * @return mixed Value of transient.
				 */
                $lowinstock_count = apply_filters('yith_wcfm_low_stock_count_transient', get_transient($transient_name) );

                if ( false === $lowinstock_count ) {

                    $lowinstock_products = wc_get_products( array(
                        'type' => $product_types,
                        'limit' => -1,
                        'return' => 'ids',
                        'yith_wcfm_count' => 'lowinstock',

                    ) );

                    $lowinstock_count = count( $lowinstock_products );

					/**
					 * APPLY_FILTERS: yith_wcfm_save_stock_transient
					 *
					 * Filter for save stock transient
					 *
					 * @param bool $save_transient True/false for set stock transient
					 * @return bool
					 */
                    if ( apply_filters('yith_wcfm_save_stock_transient', true) ) {
                        set_transient($transient_name, $lowinstock_count, DAY_IN_SECONDS * 30);
                    }
                }

                $transient_name = 'wc_outofstock_count';
				/**
				 * APPLY_FILTERS: yith_wcfm_outofstock_count_transient
				 *
				 * Filter transient
				 *
				 * @param mixed $outofstock_count Value of transient
				 * @return mixed Value of transient.
				 */
                $outofstock_count = apply_filters('yith_wcfm_outofstock_count_transient', get_transient($transient_name) );

                if ( false === $outofstock_count ) {

                    $outofstock_products = wc_get_products( array(
                        'type' => $product_types,
                        'limit' => -1,
                        'return' => 'ids',
                        'yith_wcfm_count' => 'outofstock',

                    ) );

                    $outofstock_count = count( $outofstock_products );


                    /**
					 * APPLY_FILTERS: yith_wcfm_save_stock_transient
					 *
					 * Filter for save stock transient
					 *
					 * @param bool $save_transient True/false for set stock transient
					 * @return bool
					 */
                    if ( apply_filters( 'yith_wcfm_save_stock_transient', true) ) {
                        set_transient($transient_name, $outofstock_count, DAY_IN_SECONDS * 30);
                    }
                }
				/**
				 * APPLY_FILTERS: yith_wcfm_print_dashboard_section_args
				 *
				 * Filter dashboard section args
				 *
				 * @param array $atts Dashboard section args
				 * @return array
				 */
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
                $section_id = $this->get_id();
				/**
				 * DO_ACTION: yith_wcmf_before_print_section
				 *
				 * Before print section.
				 *
				 * @param string $section_id The section id
				 * @param string $section The section
				 * @param string $subsection The subsection
				 * @param YITH_Frontend_Manager_Section $class the frontend manager section class
				 *
				 */
                do_action( 'yith_wcmf_before_print_section', $section_id, $section, $subsection, $this );
                yith_wcfm_get_template($subsection, $atts, 'sections/' . $section);
				/**
				 * DO_ACTION: yith_wcmf_after_print_section
				 *
				 * After print section.
				 *
				 * @param string $section_id The section id
				 * @param string $section The section
				 * @param string $subsection The subsection
				 * @param YITH_Frontend_Manager_Section $class the frontend manager section class
				 *
				 */
                do_action( 'yith_wcmf_after_print_section', $section_id, $section, $subsection, $this );
            }

            else {
				/**
				 * DO_ACTION: yith_wcfm_print_section_unauthorized
				 *
				 * Print anauthorized section.
				 *
				 * @param string $section_id The section id
				 *
				 */
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
        }

        /**
         * Print shortcode function
         *
         * @return void
         * @since 1.0.0
         */
        public function print_shortcode( $atts = array(), $content = '', $tag = '' ) {
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

        /**
         * Handle a custom 'customvar' query var to get products with the 'customvar' meta.
         * @param array $query - Args for WP_Query.
         * @param array $query_vars - Query vars from WC_Product_Query.
         * @return array modified $query
         */
        public function handle_custom_query_var( $query, $query_vars ) {
            if ( ! empty( $query_vars['yith_wcfm_count'] ) ) {

                $nostock = absint(max(get_option('woocommerce_notify_no_stock_amount'), 0));
                $stock = absint(max(get_option('woocommerce_notify_low_stock_amount'), 1));


                switch ( $query_vars['yith_wcfm_count'] ) {

                    case 'outofstock':

                        $query['meta_query'][] = array(
                            'key' => '_manage_stock',
                            'value' => esc_attr( 'yes' ),
                        );
                        $query['meta_query'][] = array(
                            'key' => '_stock',
                            'value' => esc_attr( $nostock ),
                            'compare' => '<='
                        );

                        break;

                    case 'lowinstock':

                        $query['meta_query'][] = array(
                            'key' => '_manage_stock',
                            'value' => esc_attr( 'yes' ),
                        );
                        $query['meta_query'][] = array(
                            'key' => '_stock',
                            'value' => esc_attr( $stock ),
                            'compare' => '<='
                        );
                        $query['meta_query'][] = array(
                            'key' => '_stock',
                            'value' => esc_attr( $nostock ),
                            'compare' => '>'
                        );

                        break;
                }
            }

            return apply_filters('yith_wcfm_query_vars_for_product_query', $query, $query_vars );
        }
    }
}

