<?php
/**
 * @package Polylang-WC
 */

/**
 * Filters the reports as some need to combine all languages
 * and other need to be filtered per language.
 *
 * @since 0.1
 */
class PLLWC_Admin_Reports {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 * Setup filters.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		// Combines products per language.
		add_filter( 'woocommerce_reports_get_order_report_query', array( $this, 'report_query' ) );
		add_filter( 'woocommerce_reports_get_order_report_data', array( $this, 'report_data' ) );

		// Sales by category.
		add_filter( 'woocommerce_report_sales_by_category_get_products_in_category', array( $this, 'get_products_in_category' ), 10, 2 );
		add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );

		// Filters stock queries per language.
		add_filter( 'woocommerce_report_low_in_stock_query_from', array( $this, 'stock_query' ) );
		add_filter( 'woocommerce_report_out_of_stock_query_from', array( $this, 'stock_query' ) );
		add_filter( 'woocommerce_report_most_stocked_query_from', array( $this, 'stock_query' ) );
	}

	/**
	 * Filters report queries to combine all languages.
	 * Hooked to the filter 'woocommerce_reports_get_order_report_query'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $query Array of SQL clauses.
	 * @return string[]
	 */
	public function report_query( $query ) {
		global $wpdb;

		// Top sellers & top earners.
		if ( false !== strpos( $query['select'], 'order_item_qty' ) || false !== strpos( $query['select'], 'order_item_total' ) ) {
			$lang = PLLWC_Admin::get_preferred_language();

			/*
			 * The query always returns the first created product in a translation group ( unknown language ).
			 * Thus we need to make sure to get the correct language thanks to the woocommerce_reports_get_order_report_data filter.
			 * FIXME never return a product which has no translation.
			 */
			$query['join']    .= " INNER JOIN {$wpdb->term_relationships} AS pll_tr ON order_item_meta__product_id.meta_value = pll_tr.object_id";
			$query['join']    .= " INNER JOIN {$wpdb->term_taxonomy} AS pll_tt ON pll_tr.term_taxonomy_id = pll_tt.term_taxonomy_id";
			$query['where']   .= $wpdb->prepare( ' AND pll_tt.taxonomy = %s', $this->data_store->get_tax_translations() );
			$query['where']   .= $wpdb->prepare( ' AND %s = %s', $lang, $lang ); // Hack to be sure to get one query per filtered language to pass into woocommerce_reports_get_order_report_data filter.
			$query['group_by'] = 'GROUP BY pll_tr.term_taxonomy_id';
		}

		// Sparkline.
		if ( false !== strpos( $query['select'], 'sparkline' ) ) {
			$pattern = "#order_item_meta__product_id.meta_value = '([0-9]+)'#";
			if ( preg_match( $pattern, $query['where'], $matches ) ) {
				$ids = array();

				foreach ( $this->data_store->get_translations( $matches[1] ) as $tr_id ) {
					$ids[] = $tr_id;
				}

				if ( ! empty( $ids ) ) {
					$ids            = array_unique( $ids );
					$ids            = array_map( 'absint', $ids );
					$replace        = "order_item_meta__product_id.meta_value IN ('" . implode( "','", $ids ) . "')";
					$query['where'] = (string) preg_replace( $pattern, $replace, $query['where'] );
				}
			}
		}

		// Sales by product.
		if ( false !== strpos( $query['select'], 'order_item_count' ) || false !== strpos( $query['select'], 'order_item_amount' ) ) {
			$pattern = "#order_item_meta__product_id_array.meta_value IN \('([\',0-9]+)'\)#";
			if ( preg_match( $pattern, $query['where'], $matches ) ) {
				$ids = array();

				foreach ( array_map( 'absint', explode( "','", $matches[1] ) ) as $id ) {
					foreach ( $this->data_store->get_translations( $id ) as $tr_id ) {
						$ids[] = $tr_id;
					}
				}

				if ( ! empty( $ids ) ) {
					$ids            = array_unique( $ids );
					$ids            = array_map( 'absint', $ids );
					$replace        = "order_item_meta__product_id_array.meta_value IN ('" . implode( "','", $ids ) . "')";
					$query['where'] = (string) preg_replace( $pattern, $replace, $query['where'] );
				}
			}
		}

		return $query;
	}

	/**
	 * Makes sure that the products returned by WC_Admin_Report::get_order_report_data() are in the expected language.
	 * It's necessary as the filtered report_query() does not give any warranty on the product language.
	 *
	 * @since 0.1
	 *
	 * @param array $results Array of products returned by WC_Admin_Report::get_order_report_data().
	 * @return array
	 */
	public function report_data( $results ) {
		if ( is_array( $results ) ) {
			foreach ( $results as $key => $result ) {
				if ( ! empty( $result->product_id ) ) {
					$results[ $key ]->product_id = $this->data_store->get( $result->product_id, PLLWC_Admin::get_preferred_language() );
				}
			}
		}

		return $results;
	}

	/**
	 * Combines all translations of a product for a given category.
	 * Hooked to the filter 'woocommerce_report_sales_by_category_get_products_in_category'.
	 *
	 * @since 0.1
	 *
	 * @param int[] $product_ids Not used.
	 * @param int   $category_id Product category id.
	 * @return int[]
	 */
	public function get_products_in_category( $product_ids, $category_id ) {
		$term_ids = array();

		foreach ( pll_get_term_translations( $category_id ) as $tr_id ) {
			$term_ids[] = get_term_children( $tr_id, 'product_cat' );
			$term_ids[] = $tr_id;
		}

		$terms = get_objects_in_term( $term_ids, 'product_cat' );
		return is_array( $terms ) ? array_map( 'intval', $terms ) : array();
	}

	/**
	 * Filters the list of categories per language in Sales by category.
	 * Hooked to the filter 'terms_clauses'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $clauses    SQL clauses.
	 * @param string[] $taxonomies Not used.
	 * @param array    $args       WP_Term_Query arguments.
	 * @return string[] Modified SQL clauses
	 */
	public function terms_clauses( $clauses, $taxonomies, $args ) {
		// The query is already filtered when the admin language filter is active.
		if ( empty( $args['object_ids'] ) && isset( $_GET['report'] ) && 'sales_by_category' === $_GET['report'] && empty( PLL()->curlang ) && ( empty( $_GET['lang'] ) || 'all' === $_GET['lang'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification
			// Sets the language from the current locale or the default language.
			$lang = PLL()->model->get_language( PLLWC_Admin::get_preferred_language() );
			if ( $lang ) {
				return PLL()->model->terms_clauses( $clauses, $lang );
			}
		}
		return $clauses;
	}

	/**
	 * Filters the stock queries per language.
	 *
	 * @since 0.1
	 *
	 * @param string $query_from Part of the SQL query (FROM, JOIN, WHERE clauses).
	 * @return string
	 */
	public function stock_query( $query_from ) {
		return str_replace( 'WHERE 1=1', $this->data_store->join_clause( 'posts' ) . ' WHERE 1=1' . $this->data_store->where_clause( PLLWC_Admin::get_preferred_language() ), $query_from );
	}
}
