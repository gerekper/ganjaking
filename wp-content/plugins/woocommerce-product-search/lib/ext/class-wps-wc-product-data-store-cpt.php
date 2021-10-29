<?php
/**
 * class-wps-wc-product-data-store-cpt.php
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
 * @since 2.6.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Product_Data_Store_CPT' ) ) {

/**
 * WC Product Data Store Replacement.
 */
class WPS_WC_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	private static $limit = 0;
	private static $json_product_search = false;

	/**
	 * Filter the woocommerce_data_stores to install this class.
	 */
	public static function init() {
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'woocommerce_data_stores' ) );

		add_action( 'check_ajax_referer', array( __CLASS__, 'check_ajax_referer' ), 10, 2 );
	}

	/**
	 * Recognize WC JSON product search request.
	 *
	 * @param string $action
	 * @param boolean|int $result
	 */
	public static function check_ajax_referer( $action, $result ) {
		if ( $action === 'search-products' && $result !== false ) {
			$options = get_option( 'woocommerce-product-search', array() );
			$json_limit = isset( $options[WooCommerce_Product_Search::JSON_LIMIT] ) ? ( $options[WooCommerce_Product_Search::JSON_LIMIT] !== '' ? intval( $options[WooCommerce_Product_Search::JSON_LIMIT] ) : '' ) : WooCommerce_Product_Search::JSON_LIMIT_DEFAULT;
			self::$limit = $json_limit;
			self::$json_product_search = true;
		} else {
			self::$limit = 0;
			self::$json_product_search = false;
		}
	}

	/**
	 * Install this data store for products.
	 *
	 * @param array $stores
	 */
	public static function woocommerce_data_stores( $stores ) {

		if ( apply_filters( 'woocommerce_product_search_ext_product_data_store', WPS_EXT_PDS ) ) {
			if ( isset( $stores['product'] ) && $stores['product'] !== __CLASS__ ) {
				$stores['product'] = __CLASS__;
			}
		}
		return $stores;
	}

	/**
	 * Whether the request is for a JSON product search.
	 *
	 * @return boolean
	 */
	public static function is_json_product_search() {
		return self::$json_product_search;
	}

	/**
	 * Search product data for a term and return ids.
	 *
	 * @param  string     $term Search term.
	 * @param  string     $type Type of product.
	 * @param  bool       $include_variations Include variations in search or not.
	 * @param  bool       $all_statuses Should we search all statuses or limit to published.
	 * @param  null|int   $limit Limit returned results (added in WC 3.5.0)
	 * @param  null|array $include Keep specific results (added in WC 3.6.0) @since 2.13.0
	 * @param  null|array $exclude Discard specific results (added in WC 3.6.0) @since 2.13.0
	 *
	 * @return array of ids
	 */
	public function search_products( $term, $type = '', $include_variations = false, $all_statuses = false, $limit = null, $include = null, $exclude = null ) {
		global $wpdb;

		$options = get_option( 'woocommerce-product-search', array() );
		$auto_replace_json = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_JSON] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_JSON] : WooCommerce_Product_Search::AUTO_REPLACE_JSON_DEFAULT;

		if (
			self::$json_product_search && !$auto_replace_json ||
			!WooCommerce_Product_Search_Service::use_engine()
		) {
			return parent::search_products( $term, $type, $include_variations, $all_statuses, $limit );
		}

		$post_types    = $include_variations ? array( 'product', 'product_variation' ) : array( 'product' );
		$type_join     = '';
		$type_where    = '';
		$status_where  = '';
		$limit_query   = '';

		$post_statuses = apply_filters(
			'woocommerce_search_products_post_statuses',
			current_user_can( 'edit_private_products' ) ? array( 'private', 'publish' ) : array( 'publish' )
		);

		$term_groups = array_map( 'trim', explode( ' OR ', $term ) );

		$search_where   = '';
		$search_queries = array();

		global $wps_process_query;
		if ( self::$json_product_search || isset( $wps_process_query ) && !$wps_process_query ) {

			$_search_query = isset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] ) ? $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] : null;
			$_variations   = isset( $_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS] ) ? $_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS] : null;
			foreach ( $term_groups as $term_group ) {
				if ( strlen( $term_group ) > 0 ) {
					$_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] = $term_group;
					$_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS] = $include_variations ? 1 : 0;
					$post_ids = WooCommerce_Product_Search_Service::get_post_ids_for_request();
					if ( count( $post_ids ) === 0 ) {
						$post_ids = array( -1 );
					}
					$search_queries[] = " ( posts.ID IN ( " . implode( ',', array_map( 'intval', $post_ids ) ) . " ) ) ";
				}
			}

			if ( $_search_query !== null ) {
				$_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] = $_search_query;
			} else {
				unset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] );
			}
			if ( $_variations !== null ) {
				$_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS] = $_variations;
			} else {
				unset( $_REQUEST[WooCommerce_Product_Search_Service::VARIATIONS] );
			}
		}

		if ( count( $search_queries ) > 0 ) {
			$search_where = 'AND (' . implode( ') OR (', $search_queries ) . ')';
		}

		if ( ! empty( $include ) && is_array( $include ) ) {
			$search_where .= ' AND posts.ID IN(' . implode( ',', array_map( 'absint', $include ) ) . ') ';
		}

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$search_where .= ' AND posts.ID NOT IN(' . implode( ',', array_map( 'absint', $exclude ) ) . ') ';
		}

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
			$meta_join = "LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id";
			if ( 'virtual' === $type ) {
				$type_where = ' AND ( wc_product_meta_lookup.virtual = 1 ) ';
			} elseif ( 'downloadable' === $type ) {
				$type_where = ' AND ( wc_product_meta_lookup.downloadable = 1 ) ';
			}
		} else {
			$meta_join = "LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id";
			if ( $type && in_array( $type, array( 'virtual', 'downloadable' ), true ) ) {
				$type_join  = " LEFT JOIN {$wpdb->postmeta} postmeta_type ON posts.ID = postmeta_type.post_id ";
				$type_where = " AND ( postmeta_type.meta_key = '_{$type}' AND postmeta_type.meta_value = 'yes' ) ";
			}
		}

		if ( ! $all_statuses ) {
			$status_where = " AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
		}

		if ( self::$limit !== '' ) {
			$limit_query = self::$limit > 0 ? ' LIMIT ' . intval( self::$limit ) : '';
		} else {
			if ( $limit ) {
				$limit_query = $wpdb->prepare( ' LIMIT %d ', $limit );
			}
		}

		$query =
			"SELECT DISTINCT posts.ID AS product_id, posts.post_parent AS parent_id, posts.post_title AS post_title FROM {$wpdb->posts} posts " .
			"$meta_join " .
			"$type_join " .
			"WHERE posts.post_type IN ('" . implode( "','", $post_types ) . "') " .
			"$search_where " .
			"$status_where " .
			"$type_where " .
			"ORDER BY posts.post_parent ASC, posts.post_title ASC " .
			$limit_query;

		$search_results = $wpdb->get_results( $query );

		$product_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'product_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

		foreach ( $term_groups as $term_group ) {
			if ( is_numeric( $term_group ) ) {
				$post_id   = absint( $term_group );
				$post_type = get_post_type( $post_id );
				if ( 'product_variation' === $post_type && $include_variations ) {
					$product_ids[] = $post_id;
				} elseif ( 'product' === $post_type ) {
					$product_ids[] = $post_id;
				}
				$product_ids[] = wp_get_post_parent_id( $post_id );
			}
		}

		$product_ids = array_map( 'intval', $product_ids );
		$product_ids = array_unique( $product_ids );

		return wp_parse_id_list( $product_ids );
	}
}

WPS_WC_Product_Data_Store_CPT::init();

} else {
	wps_log_warning( 'Missing WooCommerce class: WC_Product_Data_Store_CPT' );
}
