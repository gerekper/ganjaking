<?php
/**
 * A class for querying the products of a catalog.
 *
 * @package WC_Instagram/Product Catalog
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
		$this->catalog_query_vars = wp_parse_args( $args, $this->get_default_catalog_query_vars() );

		$args = $this->process_catalog_query_vars();

		// Force return the product IDs.
		$args = wp_parse_args( array( 'return' => 'ids' ), $args );

		parent::__construct( $args );
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
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		$query_vars = array_merge(
			parent::get_default_query_vars(),
			array(
				'status' => array( 'publish' ),
				'limit'  => -1,
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
	 * Gets the default allowed catalog query vars.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	protected function get_default_catalog_query_vars() {
		return array(
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
		$query_vars = $this->get_catalog_query_vars();
		$return_ids = ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] );
		$filter_by  = $query_vars['filter_by'];

		// Only specific products, there is no need to execute the query.
		if ( 'products' === $filter_by && 'specific' === $query_vars['products_option'] ) {
			$product_ids = $query_vars['include_product_ids'];
		} else {
			$product_ids = parent::get_products();

			// Include products.
			if ( ! empty( $query_vars['include_product_ids'] ) && 'custom' === $filter_by ) {
				$product_ids = array_unique( array_merge( $product_ids, $query_vars['include_product_ids'] ) );
			}

			// Exclude products.
			if ( ! empty( $query_vars['exclude_product_ids'] ) && (
				'custom' === $filter_by ||
				( 'products' === $filter_by && 'all_except' === $query_vars['products_option'] )
			) ) {
				$product_ids = array_diff( $product_ids, $query_vars['exclude_product_ids'] );
			}
		}

		return array_values( $return_ids ? $product_ids : array_filter( array_map( 'wc_get_product', $product_ids ) ) );
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
}
