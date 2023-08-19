<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACA\WC\Column;
use ACA\WC\Filtering;
use ACA\WC\Filtering\ShopOrder;
use WP_Query;

/**
 * @property Column\ShopOrder\Product $column
 */
class Product extends ShopOrder {

	public function __construct( Column\ShopOrder\Product $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		switch ( $this->column->get_product_property() ) {

			case 'title' :
				add_filter( 'posts_join', [ $this, 'join_by_order_itemmeta' ], 10, 2 );
				add_filter( 'posts_where', [ $this, 'filter_by_wc_product_title' ], 10, 2 );
				add_filter( 'posts_groupby', [ $this, 'groupby_wc_product_ids' ] );

				break;
			case 'sku' :
				add_filter( 'posts_join', [ $this, 'join_by_order_itemmeta' ], 10, 2 );
				add_filter( 'posts_join', [ $this, 'join_by_postmeta' ], 10, 2 );
				add_filter( 'posts_where', [ $this, 'filter_by_wc_product_sku' ], 10, 2 );
				add_filter( 'posts_groupby', [ $this, 'groupby_wc_product_ids' ] );

				break;
		}

		return $vars;
	}

	public function filter_by_wc_product_title( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$alias = $this->get_meta_alias();

			$where .= $wpdb->prepare( "AND om_{$alias}.meta_value = %d AND (om_{$alias}.meta_key = '_product_id' OR om_{$alias}.meta_key = '_variation_id')", $this->get_filter_value() );
		}

		return $where;
	}

	public function filter_by_wc_product_sku( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$alias = $this->get_meta_alias();

			$where .= $wpdb->prepare( "AND pm_{$alias}.meta_value = %s AND pm_{$alias}.meta_key = '_sku'", get_post_meta( $this->get_filter_value(), '_sku', true ) );
		}

		return $where;
	}

	public function get_filtering_data() {
		return [
			'options' => $this->get_all_ordered_products(),
		];
	}

	public function register_settings() {
		$this->column->add_setting( new Filtering\Settings\ShowVariableProducts( $this->column ) );
	}

	private function show_variations() {
		$setting = $this->column->get_setting( 'filter' );

		if ( ! $setting instanceof Filtering\Settings\ShowVariableProducts ) {
			return true;
		}

		return 'on' === $setting->get_filter_allow_variations();
	}

	/**
	 * @since 1.3.2
	 */
	private function get_all_ordered_products() {
		global $wpdb;

		$join_condition = "om.meta_key = '_product_id'";

		if ( $this->show_variations() ) {
			$join_condition = "om.meta_key = '_variation_id' OR om.meta_key = '_product_id'";
		}

		switch ( $this->column->get_product_property() ) {
			case 'sku':
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID as id, pm.meta_value as value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND  ( {$join_condition} ) )
					INNER JOIN {$wpdb->postmeta} pm ON ( p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value != '' )
					ORDER BY pm.meta_value;"
				);

				break;
			case 'id':
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID as id, p.ID as value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND  ( {$join_condition} ) )
					ORDER BY p.ID;"
				);

				break;
			case 'title':
			default:
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID AS id, p.post_title AS value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND  ( {$join_condition} ) )
					ORDER BY post_title;"
				);

				break;
		}

		if ( ! $values ) {
			return [];
		}

		$products = [];
		foreach ( $values as $value ) {
			$products[ $value->id ] = $value->value;
		}

		return $products;
	}

}