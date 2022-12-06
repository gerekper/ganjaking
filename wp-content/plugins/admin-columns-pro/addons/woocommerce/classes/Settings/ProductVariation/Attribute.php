<?php

namespace ACA\WC\Settings\ProductVariation;

use AC;
use AC\View;
use ACA\WC\Type\ProductAttribute;

class Attribute extends AC\Settings\Column {

	const NAME = 'variation_attribute';

	/**
	 * @var string
	 */
	private $variation_attribute;

	protected function define_options() {
		return [ self::NAME => '' ];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Attribute', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	private function get_attributes_meta_keys() {
		global $wpdb;

		$attributes = wp_cache_get( 'attributes', $this->column->get_type() );

		if ( false === $attributes ) {

			$attributes = $wpdb->get_col( "
				SELECT DISTINCT pm.meta_key 
				FROM {$wpdb->postmeta} AS pm
				INNER JOIN {$wpdb->posts} AS pp ON pp.ID = pm.post_id
				WHERE pp.post_type = 'product_variation' AND pm.meta_key LIKE 'attribute_%'
			" );

			wp_cache_add( 'attributes', $attributes, $this->column->get_type() );
		}

		if ( ! $attributes ) {
			return [];
		}

		return $attributes;
	}

	/**
	 * @return ProductAttribute|null
	 */
	public function get_product_attribute() {
		return $this->get_variation_attribute()
			? new ProductAttribute( $this->get_variation_attribute() )
			: null;
	}

	private function remove_attribute_prefix( $meta_key ) {
		return preg_replace( "/^attribute_/", '', $meta_key );
	}

	protected function get_display_options() {
		$taxonomy_options = [];
		$custom_options = [];

		foreach ( $this->get_attributes_meta_keys() as $meta_key ) {
			$attribute = new ProductAttribute( $this->remove_attribute_prefix( $meta_key ) );

			if ( $attribute->is_taxonomy() ) {
				$taxonomy_options[ $attribute->get_name() ] = $attribute->get_label();
			} else {
				$custom_options[ $attribute->get_name() ] = $attribute->get_label();
			}
		}

		if ( ! $taxonomy_options && ! $custom_options ) {
			return [];
		}

		if ( ! $custom_options ) {
			return $taxonomy_options;
		}

		if ( ! $taxonomy_options ) {
			return $custom_options;
		}

		return [
			'Taxonomy' => [
				'title'   => __( 'Global Attributes', 'codepress-admin-columns' ),
				'options' => $taxonomy_options,
			],
			'Custom'   => [
				'title'   => __( 'Product Attributes', 'codepress-admin-columns' ),
				'options' => $custom_options,
			],
		];
	}

	/**
	 * @return string
	 */
	public function get_variation_attribute() {
		return $this->variation_attribute;
	}

	/**
	 * @param string $variation_attribute
	 */
	public function set_variation_attribute( $variation_attribute ) {
		$this->variation_attribute = $variation_attribute;
	}

}