<?php

namespace ACA\WC\Filtering\ShopOrder;

use AC;
use ACA\WC\Column;
use ACA\WC\Filtering\ShopOrder;
use WP_Query;

/**
 * @property Column\ShopOrder\ProductCategories $column
 */
class ProductTaxonomy extends ShopOrder {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( AC\Column $column, $taxonomy ) {
		$this->taxonomy = $taxonomy;

		parent::__construct( $column );
	}

	public function get_filtering_data() {
		$options = [];

		$terms = get_terms( [
			'taxonomy' => $this->taxonomy,
		] );

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return [
			'options' => $options,
		];
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_join', [ $this, 'join_by_order_itemmeta' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'filter_by_wc_product_ids' ], 10, 2 );
		add_filter( 'posts_groupby', [ $this, 'groupby_wc_product_ids' ] );

		return $vars;
	}

	public function filter_by_wc_product_ids( $where, WP_Query $query ) {
		if ( $query->is_main_query() ) {

			$alias = $this->get_meta_alias();
			$product_ids = implode( ',', $this->get_products_for_category( $this->get_filter_value() ) );

			$where .= "AND om_{$alias}.meta_value IN ({$product_ids}) AND om_{$alias}.meta_key = '_product_id'";
		}

		return $where;
	}

	private function get_products_for_category( $cat_id ) {
		$products = get_posts( [
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => get_post_stati(),
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy' => $this->taxonomy,
					'field'    => 'term_id',
					'terms'    => $cat_id,
				],
			],
		] );

		return $products;
	}

}