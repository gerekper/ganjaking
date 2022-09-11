<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Brands.
 * Version tested: 1.6.24
 *
 * @since  1.6
 *
 * @link https://woocommerce.com/fr-fr/products/brands/
 */
class PLLWC_Brands {

	/**
	 * PLLWC_Brands constructor.
	 *
	 * @since 1.6
	 */
	public function __construct() {
		add_filter( 'pll_get_taxonomies', array( $this, 'add_taxonomy' ), 10, 2 );
		remove_action( 'product_brand_add_form_fields', array( $GLOBALS['WC_Brands_Admin'], 'add_thumbnail_field' ) );
		add_action( 'product_brand_add_form_fields', array( $this, 'add_product_brand_fields' ) );
		add_filter( 'pll_copy_term_metas', array( $this, 'copy_term_metas' ), 10, 3 );

	}


	/**
	 * Synchronizes the thumbnail term meta.
	 * Hooked to the filter 'pll_copy_term_metas'.
	 *
	 * @since 1.6
	 *
	 * @param array $metas List of custom fields names.
	 * @param bool  $sync  True if it is synchronization, false if it is a copy.
	 * @param int   $from  Id of the product from which we copy informations.
	 * @return array
	 */
	public function copy_term_metas( $metas, $sync, $from ) {
		$term = get_term( $from );
		if ( $term instanceof WP_Term && 'product_brand' === $term->taxonomy ) {
			return array_merge( $metas, array( 'thumbnail_id' ) );
		}
		return $metas;
	}

	/**
	 * Add Product Brand taxonomy to list of translated taxonomies
	 *
	 * @since 1.6
	 *
	 * @param string[] $taxonomies List of taxonomy names.
	 * @param bool     $is_settings True when displaying the list of custom taxonomies in Polylang settings.
	 *
	 * @return string[] List of taxonomy names.
	 */
	public function add_taxonomy( $taxonomies, $is_settings ) {
		if ( $is_settings ) {
			unset( $taxonomies['product_brand'] );
		} else {
			$taxonomies['product_brand'] = 'product_brand';
		}

		return $taxonomies;
	}

	/**
	 * Allow thumbnail duplication on WC Brands. Remove the original thumbnail field, re-add it with original term_id.
	 *
	 * @since   1.6
	 * @return void
	 */
	public function add_product_brand_fields() {

		if ( isset( $_GET['taxonomy'], $_GET['from_tag'], $_GET['new_lang'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification
			// remove the original "thumbnail field" add form.
			remove_action( 'product_brand_add_form_fields', array( $GLOBALS['WC_Brands_Admin'], 'add_thumbnail_field' ) );

			// Retrieve the source language term_id.
			$term = get_term( (int) $_GET['from_tag'], 'product_brand' );  // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( ! empty( $term ) ) {
			// Add the edit "thumbnail field" with pre-filled with the source language term id.
			$GLOBALS['WC_Brands_Admin']->edit_thumbnail_field( $term, 'product_brand' );
		} else {
			// display a form field to add a thumbnail.
			$GLOBALS['WC_Brands_Admin']->add_thumbnail_field();
		}
	}
}
