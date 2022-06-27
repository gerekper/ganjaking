<?php
/**
 * A class for querying the products of a catalog.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_Query class.
 */
class WC_Instagram_Product_Catalog_Query extends WC_Product_Query {

	/**
	 * The catalog query vars.
	 *
	 * @var array
	 */
	protected $catalog_query_vars = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Criteria to query on in a format similar to WP_Query.
	 */
	public function __construct( $args = array() ) {
		$defaults = $this->get_default_catalog_query_vars();

		// Store the catalog query vars only.
		$this->catalog_query_vars = wp_parse_args( array_intersect_key( $args, $defaults ), $defaults );

		// Store the query vars only.
		$query_vars = wp_parse_args( $this->process_catalog_query_vars(), array_diff_key( $args, $defaults ) );

		// Force return the product IDs.
		$query_vars['return'] = 'ids';

		// Pagination not supported yet.
		$query_vars['paginate'] = false;

		parent::__construct( $query_vars );
	}

	/**
	 * Gets the catalog query vars.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_catalog_query_vars() {
		return $this->catalog_query_vars;
	}

	/**
	 * Valid query vars for products.
	 *
	 * These variables are directly passed to the query without modifying their values.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		$query_vars = array_merge(
			parent::get_default_query_vars(),
			array(
				'status'  => array( 'publish' ),
				'order'   => 'ASC',
				'orderby' => 'ID',
			)
		);

		/**
		 * Filters the default variables used for querying the products of a catalog.
		 *
		 * @since 3.0.0
		 *
		 * @param array $query_vars The query vars.
		 */
		return apply_filters( 'wc_instagram_product_catalog_default_query_vars', $query_vars );
	}

	/**
	 * Gets the default catalog query vars.
	 *
	 * These variables are related to the catalog and don't necessarily match any query variable.
	 * Some native query variables like `return`, `limit`, and `offset` are defined here to keep track of
	 * their original values, but they may be modified when generating the query in the method `process_catalog_query_vars()`.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	protected function get_default_catalog_query_vars() {
		/**
		 * Filters the default catalog query vars.
		 *
		 * @since 4.0.0
		 *
		 * @param array $catalog_query_vars The catalog query vars.
		 */
		return apply_filters(
			'wc_instagram_product_catalog_default_catalog_query_vars',
			array(
				'filter_by'             => 'products',
				'products_option'       => '',
				'product_cats_option'   => '',
				'product_cats'          => array(),
				'product_types_option'  => '',
				'product_types'         => array(),
				'virtual_products'      => '',
				'downloadable_products' => '',
				'stock_status'          => '',
				'include_product_ids'   => array(),
				'exclude_product_ids'   => array(),
				'return'                => 'objects',
				'limit'                 => - 1,
				'offset'                => '',
			)
		);
	}

	/**
	 * Processes custom query vars added by a product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function process_catalog_query_vars() {
		$catalog_query_vars = $this->get_catalog_query_vars();
		$query_vars         = array();

		/*
		 * Exclude products from the catalog.
		 * We also exclude the product IDs that must be present in the catalog because they are prepended in the list.
		 * This way we avoid duplicated products when paginating the catalog.
		 */
		$exclude_product_ids = $this->get_products_to_exclude();
		$include_product_ids = $this->get_products_to_include();

		if ( ! empty( $exclude_product_ids ) || ! empty( $include_product_ids ) ) {
			$query_vars['exclude'] = array_merge( $exclude_product_ids, $include_product_ids );
		}

		// Query offset.
		$query_vars['offset'] = $catalog_query_vars['offset'];

		// Fix the offset to include the specified product IDs at the beginning of the catalog.
		if ( '' !== $query_vars['offset'] && ! empty( $include_product_ids ) ) {
			$query_vars['offset'] = max( 0, $query_vars['offset'] - count( $include_product_ids ) );
		}

		// Query limit.
		$query_vars['limit'] = $catalog_query_vars['limit'];

		// Fix the limit to include the specified product IDs at the beginning of the catalog.
		if ( 0 < $query_vars['limit'] && 0 === $query_vars['offset'] && ! empty( $include_product_ids ) ) {
			$pending_process = ( count( $include_product_ids ) - $catalog_query_vars['offset'] );

			// Fetch included products only.
			if ( $pending_process >= $query_vars['limit'] ) {
				$query_vars['limit'] = 0;
			} else {
				// Fetch products from db until reaching the limit.
				$query_vars['limit'] -= $pending_process;
			}
		}

		if ( 'products' !== $catalog_query_vars['filter_by'] ) {
			// Clear empty taxonomy queries.
			$query_vars['tax_query'] = array_filter( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					$this->get_category_tax_query(),
				)
			);

			if ( 'custom' === $catalog_query_vars['filter_by'] ) {
				$query_vars = array_merge(
					$query_vars,
					array(
						'type'         => $this->get_type_query_var(),
						'stock_status' => $this->get_stock_status_query_var(),
						'virtual'      => $this->get_boolean_query_var( 'virtual_products' ),
						'downloadable' => $this->get_boolean_query_var( 'downloadable_products' ),
					)
				);
			}
		}

		/**
		 * Filters the variables used for querying the products of a catalog.
		 *
		 * @since 3.0.0
		 *
		 * @param array $query_vars         The query vars.
		 * @param array $catalog_query_vars The catalog query vars.
		 */
		return apply_filters( 'wc_instagram_product_catalog_query_vars', $query_vars, $catalog_query_vars );
	}

	/**
	 * Get products matching the current query vars.
	 *
	 * @since 3.0.0
	 *
	 * @return array An array of WC_Product objects or product IDs.
	 */
	public function get_products() {
		$query_vars          = $this->get_query_vars();
		$catalog_query_vars  = $this->get_catalog_query_vars();
		$include_product_ids = $this->get_products_to_include();
		$product_ids         = array();

		// Include products.
		if ( ! empty( $include_product_ids ) ) {
			if ( -1 === $catalog_query_vars['limit'] ) {
				$product_ids = $include_product_ids;
			} elseif ( $catalog_query_vars['offset'] < count( $include_product_ids ) ) {
				$length = $catalog_query_vars['limit'] - $query_vars['limit'];

				$product_ids = array_slice( $include_product_ids, $catalog_query_vars['offset'], $length );
			}
		}

		// Query the products only if needed.
		if ( 0 !== $query_vars['limit'] && ( 'products' !== $catalog_query_vars['filter_by'] || 'specific' !== $catalog_query_vars['products_option'] ) ) {
			$product_ids = array_merge( $product_ids, parent::get_products() );
		}

		return array_values( 'ids' === $catalog_query_vars['return'] ? $product_ids : array_filter( array_map( 'wc_get_product', $product_ids ) ) );
	}

	/**
	 * Gets the value for a boolean query var.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The query var key.
	 * @return bool|string
	 */
	protected function get_boolean_query_var( $key ) {
		$query_vars = $this->get_catalog_query_vars();

		return ( ! empty( $query_vars[ $key ] ) ? wc_string_to_bool( $query_vars[ $key ] ) : '' );
	}

	/**
	 * Gets the value for the 'type' query var.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_type_query_var() {
		$query_var  = array_keys( wc_get_product_types() );
		$query_vars = $this->get_catalog_query_vars();

		if ( ! empty( $query_vars['product_types_option'] ) ) {
			$product_types = $query_vars['product_types'];

			if ( 'specific' === $query_vars['product_types_option'] ) {
				$query_var = $product_types;
			} else {
				$query_var = array_values( array_diff( $query_var, $product_types ) );
			}
		}

		return $query_var;
	}

	/**
	 * Gets the value for the 'stock_status' query var.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	protected function get_stock_status_query_var() {
		$query_var  = '';
		$query_vars = $this->get_catalog_query_vars();

		if ( ! empty( $query_vars['stock_status'] ) && in_array( $query_vars['stock_status'], array( 'instock', 'outofstock' ), true ) ) {
			$query_var = $query_vars['stock_status'];
		}

		return $query_var;
	}

	/**
	 * Gets the taxonomy query for filtering the products by category.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_category_tax_query() {
		$query_vars = $this->get_catalog_query_vars();

		if ( empty( $query_vars['product_cats_option'] ) || empty( $query_vars['product_cats'] ) ) {
			return array();
		}

		return array(
			'taxonomy'         => 'product_cat',
			'field'            => 'term_id',
			'terms'            => $query_vars['product_cats'],
			'include_children' => true,
			'operator'         => ( 'specific' === $query_vars['product_cats_option'] ? 'IN' : 'NOT IN' ),
		);
	}

	/**
	 * Gets the product IDs to exclude.
	 *
	 * @since 4.1.7
	 *
	 * @return array
	 */
	protected function get_products_to_exclude() {
		$query_vars = $this->get_catalog_query_vars();

		if ( ! empty( $query_vars['exclude_product_ids'] ) && (
			'custom' === $query_vars['filter_by'] ||
			( 'products' === $query_vars['filter_by'] && 'all_except' === $query_vars['products_option'] )
		) ) {
			return $query_vars['exclude_product_ids'];
		}

		return array();
	}

	/**
	 * Gets the product IDs to include.
	 *
	 * @since 4.1.8
	 *
	 * @return array
	 */
	protected function get_products_to_include() {
		$query_vars = $this->get_catalog_query_vars();

		if ( ! empty( $query_vars['include_product_ids'] ) && (
			'custom' === $query_vars['filter_by'] ||
			( 'products' === $query_vars['filter_by'] && 'specific' === $query_vars['products_option'] )
		) ) {
			return $query_vars['include_product_ids'];
		}

		return array();
	}
}
