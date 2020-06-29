<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Products Addons.
 *
 * @package  WC_Photography/Products_Addons
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Products_Addons {

	/**
	 * Initialize the admin.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'addons_object_types' ), 21 );
		add_action( 'woocommerce_product_addons_global_edit_objects', array( $this, 'global_edit_objects' ) );
		add_action( 'woocommerce_product_addons_global_edit_addons', array( $this, 'global_edit_addons' ), 10, 2 );
		add_filter( 'woocommerce_product_addons_global_post_terms', array( $this, 'global_post_terms' ) );
		add_filter( 'woocommerce_product_addons_global_insert_post_args', array( $this, 'global_insert_post_args' ), 10, 3 );
		add_filter( 'woocommerce_product_addons_global_display_term_names', array( $this, 'global_display_term_names' ), 10, 2 );
		add_filter( 'get_product_addons_product_terms', array( $this, 'addons_product_terms' ), 10, 2 );
		add_filter( 'get_product_addons_global_query_args', array( $this, 'global_query_args' ), 10, 2 );
		add_filter( 'woocommerce_product_addons_add_to_cart_product_types', array( $this, 'add_photography_product_type' ) );
	}

	/**
	 * Register object types for add-ons.
	 */
	public function addons_object_types() {
		register_taxonomy_for_object_type( 'images_collections', 'global_product_addon' );
	}

	/**
	 * Global Add-ons objects.
	 *
	 * @param array $objects
	 */
	public function global_edit_objects( $objects ) {
		echo '<optgroup label="' . __( 'Photography Collections', 'woocommerce-photography' ) . '">';

			$collections = get_terms( 'images_collections', array( 'hide_empty' => 0 ) );

			foreach ( $collections as $collection ) {
				echo '<option value="' . $collection->term_id . '" ' . selected( in_array( $collection->term_id, $objects ), true, false ) . '>' . __( 'Collection:', 'woocommerce-photography' ) . ' ' . $collection->name . '</option>';
			}

		echo '</optgroup>';
	}

	/**
	 * Global edit Add-ons.
	 *
	 * @param  array $post
	 * @param  array $objects
	 */
	public function global_edit_addons( $post, $objects ) {
		wp_set_post_terms( $post['ID'], $objects, 'images_collections', false );
	}

	/**
	 * Global post terms.
	 *
	 * @param  array $terms
	 *
	 * @return array
	 */
	public function global_post_terms( $terms ) {
		$terms[] = 'images_collections';

		return $terms;
	}

	/**
	 * Global insert_post() args.
	 *
	 * @param  array  $args
	 * @param  string $reference
	 * @param  array  $objects
	 *
	 * @return array
	 */
	public function global_insert_post_args( $args, $reference, $objects ) {
		$args['tax_input']['images_collections'] = $objects;

		return $args;
	}

	/**
	 * Global display term names.
	 *
	 * @param  array $term_names
	 * @param  array $objects
	 *
	 * @return array
	 */
	public function global_display_term_names( $term_names, $objects ) {
		foreach ( $objects as $object_id ) {
			$term = get_term_by( 'id', $object_id, 'images_collections' );
			if ( $term ) {
				$term_names[] = __( 'Collection:', 'woocommerce-photography' ) . ' ' . $term->name;
			}
		}

		return $term_names;
	}

	/**
	 * Add-ons product terms.
	 *
	 * @param  array  $terms
	 * @param  string $post_id
	 *
	 * @return array
	 */
	public function addons_product_terms( $terms, $post_id ) {
		$collections = wp_get_post_terms( $post_id, 'images_collections', array( 'fields' => 'ids' ) );

		$terms = array_merge( $terms, $collections );

		return $terms;
	}

	/**
	 * Global query args.
	 *
	 * @param  array $args
	 * @param  array $terms
	 *
	 * @return array
	 */
	public function global_query_args( $args, $terms ) {
		$args['tax_query']['relation'] = 'OR';
		$args['tax_query'][] = array(
			'taxonomy'         => 'images_collections',
			'field'            => 'id',
			'terms'            => $terms,
			'include_children' => false
		);

		return $args;
	}

	/**
	 * Tells Product Addons to redirect to the product URL when the "add to cart" URL is printed
	 */
	public function add_photography_product_type( $product_types ) {
		$product_types[] = 'photography';
		return $product_types;
	}

}

new WC_Photography_Products_Addons();
