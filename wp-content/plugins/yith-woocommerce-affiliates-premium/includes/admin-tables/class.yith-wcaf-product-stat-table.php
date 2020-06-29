<?php
/**
 * Product Rate Table class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Product_Stat_Table' ) ) {
	/**
	 * WooCommerce Product Stat Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Product_Stat_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Product_Stat_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'product',     //singular name of the listed records
				'plural'   => 'product',    //plural name of the listed records
				'ajax'     => false        //does this table support ajax?
			) );
		}

		/* === COLUMNS METHODS === */

		/**
		 * Print default column content
		 *
		 * @param $item        mixed Item of the row
		 * @param $column_name string Column name
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return esc_html( $item[ $column_name ] );
			} else {
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Print column for product thumb
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_thumb( $item ) {
			$column = '';

			$product_id = $item['ID'];
			$product    = wc_get_product( $product_id );

			if ( ! $product ) {
				return '';
			}

			$column = $product->get_image( array( 32, 32 ) );

			return $column;
		}

		/**
		 * Print column for product name
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_product( $item ) {
			$column = '';

			$product_id = $item['ID'];
			$product    = wc_get_product( $product_id );

			if ( ! $product ) {
				return '';
			}

			$actions = array(
				'ID'               => $product_id,
				'view'             => sprintf( '<a href="%s">%s</a>', get_the_permalink( $product_id ), __( 'View', 'yith-woocommerce-affiliates' ) ),
				'view_commissions' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'page'        => 'yith_wcaf_panel',
																									 'tab'         => 'commissions',
																									 '_product_id' => $product_id
				), admin_url( 'admin.php' ) ) ), __( 'View commissions', 'yith-woocommerce-affiliates' ) )
			);

			$column .= sprintf( '<a href="%s">%s</a>%s', get_edit_post_link( $product_id ), $product->get_title(), $this->row_actions( $actions ) );

			return apply_filters( 'yith_wcaf_product_column', $column, $product_id, 'stats' );
		}

		/**
		 * Print column for product generated commissions
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_sales( $item ) {
			$column = '';

			$column = wc_price( $item['commissions'] );

			return $column;
		}

		/**
		 * Print column for product generated commissions refunds
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_refunds( $item ) {
			$column = '';

			$column = wc_price( $item['refunds'] );

			return $column;
		}

		/**
		 * Print column for product generated clicks
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_clicks( $item ) {
			$column = '';

			$column = ! empty( $item['clicks'] ) ? $item['clicks'] : __( 'N/A', 'yith-woocommerce-affiliates' );

			return $column;
		}

		/**
		 * Print column for product generated conversions
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_conversions( $item ) {
			$column = '';

			$column = ! empty( $item['conversions'] ) ? $item['conversions'] : __( 'N/A', 'yith-woocommerce-affiliates' );

			return $column;
		}

		/**
		 * Print column for product medium conversion time
		 *
		 * @param $item mixed Single row item
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_conversion_time( $item ) {
			$column = '';

			$column = ! empty( $item['conversion_time'] ) ? human_time_diff( time(), time() + $item['conversion_time'] ) : __( 'N/A', 'yith-woocommerce-affiliates' );

			return $column;
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 1.0.0
		 */
		public function get_columns() {
			$columns = array(
				'thumb'           => sprintf( '<span class="wc-image" data-tip="%s"></span>', __( 'Image', 'yith-woocommerce-affiliates' ) ),
				'product'         => __( 'Product', 'yith-woocommerce-affiliates' ),
				'sales'           => __( 'Commissions', 'yith-woocommerce-affiliates' ),
				'refunds'         => __( 'Refunds', 'yith-woocommerce-affiliates' ),
				'clicks'          => __( 'Clicks', 'yith-woocommerce-affiliates' ),
				'conversions'     => sprintf( '<span class="tips" data-tip="%s">%s</span>', __( 'Number of products sold following a click', 'yith-woocommerce-affiliates' ), __( 'Conversions', 'yith-woocommerce-affiliates' ) ),
				'conversion_time' => __( 'Conv. time', 'yith-woocommerce-affiliates' )
			);

			return $columns;
		}

		/**
		 * Returns column to be sortable in table
		 *
		 * @return array Array of sortable columns
		 * @since 1.0.0
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'product'         => array( 'post_title', false ),
				'sales'           => array( 'commissions', true ),
				'refunds'         => array( 'refunds', false ),
				'clicks'          => array( 'clicks', false ),
				'conversions'     => array( 'conversions', false ),
				'conversion_time' => array( 'conversion_time', false )
			);

			return $sortable_columns;
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function prepare_items() {
			global $wpdb;

			$affiliate_id = isset( $_REQUEST['_affiliate_id'] ) ? intval( $_REQUEST['_affiliate_id'] ) : false;
			$from         = ! empty( $_REQUEST['_from'] ) ? date( 'Y-m-d 00:00:00', strtotime( trim( $_REQUEST['_from'] ) ) ) : false;
			$to           = ! empty( $_REQUEST['_to'] ) ? date( 'Y-m-d 23:59:59', strtotime( trim( $_REQUEST['_to'] ) ) ) : false;

			$per_page               = 10;
			$current_page           = $this->get_pagenum();
			$total_items_per_status = wp_count_posts( 'product' );
			$total_items            = $total_items_per_status->publish + $total_items_per_status->draft + $total_items_per_status->future;

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// prepare first view, for click stats (grouped by product id)
			$query_view_1 = "SELECT
                 		      COUNT(yc.ID) AS click_count,
                 		      AVG(yc.conv_time) AS conversion_time,
                		      p.ID AS product_id
            			     FROM {$wpdb->posts} AS p
                		     LEFT JOIN {$wpdb->yith_clicks} AS yc ON yc.link LIKE CONCAT('%', p.post_name, '%')
            			     WHERE yc.link <> '' AND p.post_type = 'product'";

			if ( ! empty( $affiliate_id ) ) {
				$query_view_1 .= $wpdb->prepare( ' AND yc.affiliate_id = %d', $affiliate_id );
			}

			if ( ! empty( $from ) ) {
				$query_view_1 .= $wpdb->prepare( ' AND yc.click_date >= %s', $from );
			}

			if ( ! empty( $to ) ) {
				$query_view_1 .= $wpdb->prepare( ' AND yc.click_date <= %s', $to );
			}

			$query_view_1 .= ' GROUP BY p.post_name';

			// prepare second view, for commissions stats (grouped by product id)
			$query_view_2 = "SELECT
                		      im.meta_value AS product_id,
                              SUM( yc.amount ) AS salses,
                		      SUM( yc.refunds ) AS refunds
            			     FROM {$wpdb->yith_commissions} AS yc
             			     LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON im.order_item_id = yc.line_item_id
             			     LEFT JOIN {$wpdb->posts} AS p ON p.ID = im.meta_value
            			     WHERE ( im.meta_key = '_product_id' || im.meta_key = '_variation_id' ) AND im.meta_value <> 0";

			if ( ! empty( $affiliate_id ) ) {
				$query_view_2 .= $wpdb->prepare( ' AND yc.affiliate_id = %d', $affiliate_id );
			}

			if ( ! empty( $from ) ) {
				$query_view_2 .= $wpdb->prepare( ' AND yc.created_at >= %s', $from );
			}

			if ( ! empty( $to ) ) {
				$query_view_2 .= $wpdb->prepare( ' AND yc.created_at <= %s', $to );
			}

			$query_view_2 .= ' GROUP BY im.meta_value';

			// prepare third view, for conversions (grouped by product id)
			$query_view_3 = "SELECT
    						  COUNT( im.order_item_id ) AS conversion_count,
    						  im.meta_value AS product_id
							 FROM {$wpdb->yith_clicks} AS yc
							 LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS i ON yc.order_id = i.order_id
							 LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON im.order_item_id = i.order_item_id
							 WHERE ( im.meta_key = '_product_id' || im.meta_key = '_variation_id' ) AND im.meta_value <> '0' AND im.meta_value <> ''";

			if ( ! empty( $affiliate_id ) ) {
				$query_view_3 .= $wpdb->prepare( ' AND yc.affiliate_id = %d', $affiliate_id );
			}

			if ( ! empty( $from ) ) {
				$query_view_3 .= $wpdb->prepare( ' AND yc.click_date >= %s', $from );
			}

			if ( ! empty( $to ) ) {
				$query_view_3 .= $wpdb->prepare( ' AND yc.click_date <= %s', $to );
			}

			$query_view_3 .= ' GROUP BY im.meta_value';

			// prepare general query, for table items (uses previous views for left join)
			$query = "SELECT
    	 			   p.*,
    				   v1.click_count AS clicks,
    				   v1.conversion_time AS conversion_time,
    				   v3.conversion_count AS conversions,
    				   v2.salses AS commissions,
    				   v2.refunds AS refunds
    				  FROM {$wpdb->posts} AS p
    				  LEFT JOIN ( {$query_view_1} ) AS v1 ON v1.product_id =  p.ID
				      LEFT JOIN ( {$query_view_2} ) AS v2 ON v2.product_id = p.ID
				      LEFT JOIN ( {$query_view_3} ) AS v3 ON v3.product_id = p.ID
					  WHERE p.post_type = 'product'";

			// sets LIMIT and ORDER BY clauses in general query
			$limit   = $per_page;
			$offset  = ( ( $current_page - 1 ) * $per_page );
			$orderby = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'commissions';
			$order   = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC';

			$query .= sprintf( ' ORDER BY %s %s', $orderby, $order );
			$query .= sprintf( ' LIMIT %s, %s', $offset, $limit );

			// get table items
			$this->items = $wpdb->get_results( $query, ARRAY_A );

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}