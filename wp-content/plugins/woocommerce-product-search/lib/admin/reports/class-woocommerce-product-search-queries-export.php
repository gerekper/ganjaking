<?php
/**
 * class-woocommerce-product-search-queries-export.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 4.5.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queries export.
 */
class WooCommerce_Product_Search_Queries_Export {

	/**
	 * Init hook to catch export file generation request.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
	}

	/**
	 * Catch request to generate export file.
	 */
	public static function wp_init() {
		if ( self::is_export_request() ) {
			if ( wp_verify_nonce( $_REQUEST['wps-queries-export'], 'export' ) ) {
				self::export();
			}
		}
	}

	/**
	 * Returns true for export request.
	 *
	 * @return boolean
	 */
	public static function is_export_request() {
		return isset( $_REQUEST['wps-queries-export'] ) && isset( $_REQUEST['action'] );
	}

	/**
	 * Renders the export file.
	 */
	public static function export() {
		global $wpdb;
		if ( !headers_sent() ) {
			$charset = get_bloginfo( 'charset' );
			$now     = date( 'Y-m-d-H-i-s', time() );
			header( 'Content-Description: File Transfer' );
			if ( !empty( $charset ) ) {
				header( 'Content-Type: text/csv; charset=' . $charset );
			} else {
				header( 'Content-Type: text/csv' );
			}
			header( "Content-Disposition: attachment; filename=\"queries-$now.csv\"" );

			$columns = array(
				esc_html__( 'Query', 'woocommerce-product-search' ),
				esc_html__( 'Hits', 'woocommerce-product-search' ),
				esc_html__( 'ID', 'woocommerce-product-search' ),
				esc_html__( 'First', 'woocommerce-product-search' ),
				esc_html__( 'Last', 'woocommerce-product-search' )
			);
			printf( implode( ',', $columns ) );
			printf( "\n" );

			$query = self::get_query();
			$rows = $wpdb->get_results( $query, ARRAY_A );

			foreach( $rows as $row ) {
				$entry = array(
					!empty( $row['query'] ) ? $row['query'] : '',
					!empty( $row['hits'] ) ? $row['hits'] : '',
					!empty( $row['query_id'] ) ? $row['query_id'] : '',
					!empty( $row['min_date'] ) ? $row['min_date'] : '',
					!empty( $row['max_date'] ) ? $row['max_date'] : ''
				);
				printf( implode( ',', $entry ) );
				printf( "\n" );
			}
			die;
		} else {
			wp_die( 'ERROR: headers already sent' );
		}
	}

	/**
	 * Get query string.
	 *
	 * @return string
	 */
	private static function get_query() {

		global $wpdb;

		require_once 'class-woocommerce-product-search-report-queries.php';

		$search_query = isset( $_REQUEST['search_query'] ) ? trim( $_REQUEST['search_query'] ) : '';

		$search_query_mode  = !empty( $_REQUEST['search_query_mode'] ) ? $_REQUEST['search_query_mode'] : 'startswith';
		switch( $search_query_mode ) {
			case 'startswith' :
			case 'exact' :
			case 'contains' :
				break;
			default :
				$search_query_mode = 'startswith';
		}

		$query_results = isset( $_REQUEST['query_results'] ) ? intval( $_REQUEST['query_results'] ) : WooCommerce_Product_Search_Report_Queries::QUERY_RESULTS_DEFAULT;
		$start_date = !empty( $_REQUEST['start_date'] ) ? strtotime( $_REQUEST['start_date'] ) : false;
		$end_date   = !empty( $_REQUEST['end_date'] ) ? strtotime( $_REQUEST['end_date'] ) : false;

		$orderby = isset( $_REQUEST['orderby'] ) ? strtolower( $_REQUEST['orderby'] ) : 'hits';
		switch( $orderby ) {
			case 'hits' :
			case 'query' :
			case 'query_id' :
			case 'min_date' :
			case 'max_date' :
				break;
			default :
				$orderby = 'hits';
		}

		$order = isset( $_REQUEST['order'] ) ? strtoupper( $_REQUEST['order'] ) : 'DESC';
		switch( $order ) {
			case 'ASC' :
			case 'DESC' :
				break;
			default :
				$order = 'DESC';
		}

		$hit_table = WooCommerce_Product_Search_Controller::get_tablename( 'hit' );
		$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );

		$conditions = array();
		$values     = array();

		if ( !empty( $search_query ) ) {
			switch( $search_query_mode ) {
				case 'startswith' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = $wpdb->esc_like( $search_query ) . '%';
					break;
				case 'exact' :
					$conditions[] = 'q.query = %s';
					$values[]     = $search_query;
					break;
				case 'contains' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = '%' . $wpdb->esc_like( $search_query ) . '%';
					break;
			}
		}

		if ( $start_date !== false ) {
			$conditions[] = 'h.date >=  %s ';
			$values[]     = date( 'Y-m-d', $start_date );
		}
		if ( $end_date !== false ) {
			$conditions[] = 'h.date <=  %s ';
			$values[]     = date( 'Y-m-d', $end_date );
		}
		switch( $query_results ) {
			case WooCommerce_Product_Search_Report_Queries::QUERY_RESULTS_ONLY :
				$conditions[] = 'h.count > %d';
				$values[]     = 0;
				break;
			case WooCommerce_Product_Search_Report_Queries::QUERY_RESULTS_NONE :
				$conditions[] = 'h.count = %d';
				$values[]     = 0;
				break;
		}

		$where = '';
		if ( count( $conditions ) > 0 ) {
			$where = 'WHERE ' . implode( ' AND ', $conditions );
		}

		$query =
			"SELECT SQL_CALC_FOUND_ROWS q.query, q.query_id, MIN(h.date) min_date, MAX(h.date) max_date, COUNT(DISTINCT h.ip) hits ".
			"FROM $query_table q " .
			"LEFT JOIN $hit_table h ON q.query_id = h.query_id " .
			"$where " .
			"GROUP BY q.query_id " .
			"ORDER BY $orderby $order ";

		if ( count( $values ) > 0 ) {
			$query = $wpdb->prepare( $query, $values );
		}

		return $query;
	}
}

WooCommerce_Product_Search_Queries_Export::init();
