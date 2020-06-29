<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\WordPress\Wrapper;

/**
 * The permalink watcher.
 */
class WPSEO_Woocommerce_Permalink_Watcher {

	/**
	 * Registers the hooks.
	 *
	 * @codeCoverageIgnore
	 */
	public function register_hooks() {
		add_filter( 'wpseo_post_types_reset_permalinks', [ $this, 'filter_product_from_post_types' ] );
		add_action( 'update_option_woocommerce_permalinks', [ $this, 'reset_woocommerce_permalinks' ], 10, 2 );
	}

	/**
	 * Filters the product post type from the post type.
	 *
	 * @param array $post_types The post types to filter.
	 *
	 * @return array The filtered post types.
	 */
	public function filter_product_from_post_types( $post_types ) {
		unset( $post_types['product'] );

		return $post_types;
	}

	/**
	 * Resets the indexables for WooCommerce based on the changed permalink fields.
	 *
	 * @param array $old The old value.
	 * @param array $new The new value.
	 */
	public function reset_woocommerce_permalinks( $old, $new ) {
		$changed_options = array_diff( $old, $new );

		if ( array_key_exists( 'product_base', $changed_options ) ) {
			$this->reset_permalink_indexables( 'post', 'product' );
		}

		if ( array_key_exists( 'attribute_base', $changed_options ) ) {
			$attribute_taxonomies = $this->get_attribute_taxonomies();

			foreach ( $attribute_taxonomies as $attribute_name ) {
				$this->reset_permalink_indexables( 'term', $attribute_name );
			}
		}

		if ( array_key_exists( 'category_base', $changed_options ) ) {
			$this->reset_permalink_indexables( 'term', 'product_cat' );
		}

		if ( array_key_exists( 'tag_base', $changed_options ) ) {
			$this->reset_permalink_indexables( 'term', 'product_tag' );
		}
	}

	/**
	 * Retrieves the taxonomies based on the attributes.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array The taxonomies.
	 */
	protected function get_attribute_taxonomies() {
		$taxonomies = [];
		foreach ( wc_get_attribute_taxonomies() as $attribute_taxonomy ) {
			$taxonomies[] = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
		}

		$taxonomies = array_filter( $taxonomies );

		return $taxonomies;
	}

	/**
	 * Resets the permalinks of the indexables.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $type    The type of the indexable.
	 * @param string $subtype The subtype.
	 */
	protected function reset_permalink_indexables( $type, $subtype ) {
		Wrapper::get_wpdb()->update(
			Model::get_table_name( 'Indexable' ),
			[
				'permalink' => null,
			],
			[
				'object_type'     => $type,
				'object_sub_type' => $subtype,
			]
		);
	}
}
