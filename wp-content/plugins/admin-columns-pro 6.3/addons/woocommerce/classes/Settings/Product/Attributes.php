<?php

namespace ACA\WC\Settings\Product;

use AC;
use AC\View;

class Attributes extends AC\Settings\Column {

	/**
	 * @var int
	 */
	private $product_taxonomy_display;

	protected function set_name() {
		$this->name = 'product_attributes';
	}

	protected function define_options() {
		return [ 'product_taxonomy_display' ];
	}

	public function create_view() {
		$attributes = $this->get_attribute_options();

		if ( ! $attributes ) {
			return false;
		}

		$attributes = [ '' => __( 'All attributes', 'codepress-admin-columns' ) ] + $attributes;

		$select = $this->create_element( 'select', 'product_taxonomy_display' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $attributes );

		return new View( [
			'label'   => __( 'Show Single', 'codepress-admin-columns' ),
			'tooltip' => __( 'Display a single attribute.', 'codepress-admin-columns' ) . ' ' . __( 'Only works for taxonomy attributes.', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	/**
	 * @return array
	 */
	public function get_attributes_taxonomy_labels() {
		global $wc_product_attributes;

		$attributes = [];

		foreach ( $wc_product_attributes as $name => $taxonomy ) {
			$attributes[ $name ] = $taxonomy->attribute_label;
		}

		return $attributes;
	}

	/**
	 * @return array
	 */
	private function get_raw_attributes() {
		global $wpdb;

		$results = wp_cache_get( 'attributes', $this->column->get_type() );

		if ( false === $results ) {

			$results = $wpdb->get_col( "
				SELECT {$wpdb->postmeta}.meta_value 
				FROM {$wpdb->postmeta} 
				WHERE meta_key = '_product_attributes'
			" );

			wp_cache_add( 'attributes', $results, $this->column->get_type() );
		}

		if ( ! $results ) {
			return [];
		}

		return array_map( 'unserialize', $results );
	}

	/**
	 * @return array
	 */
	public function get_attributes_custom_labels() {
		$attributes = [];

		foreach ( $this->get_raw_attributes() as $atts ) {
			foreach ( $atts as $key => $attr ) {
				if ( empty( $attr['is_taxonomy'] ) ) {
					$attributes[ $key ] = $attr['name'];
				}
			}
		}

		return $attributes;
	}

	/**
	 * @return array|false
	 */
	private function get_attribute_options() {

		$custom = $this->get_attributes_custom_labels();
		$taxonomies = $this->get_attributes_taxonomy_labels();

		if ( ! $custom && ! $taxonomies ) {
			return false;
		}

		if ( ! $taxonomies ) {
			return $custom;
		}

		if ( ! $custom ) {
			return $taxonomies;
		}

		return [
			[
				'title'   => 'Taxonomies',
				'options' => $taxonomies,
			],
			[
				'title'   => 'Custom',
				'options' => $custom,
			],
		];
	}

	/**
	 * @return int
	 */
	public function get_product_taxonomy_display() {
		return $this->product_taxonomy_display;
	}

	/**
	 * @param int $product_taxonomy_display
	 *
	 * @return $this
	 */
	public function set_product_taxonomy_display( $product_taxonomy_display ) {
		$this->product_taxonomy_display = $product_taxonomy_display;

		return $this;
	}

}