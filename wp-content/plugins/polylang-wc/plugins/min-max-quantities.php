<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Min/Max Quantities.
 * Version tested: 2.4.3.
 *
 * Synchronizes the product metas and term metas.
 *
 * @since 1.1
 */
class PLLWC_Min_Max_Quantities {

	/**
	 * Constructor.
	 * Setups filters.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_product_metas' ) );
		add_filter( 'pll_copy_term_metas', array( $this, 'copy_term_metas' ) );
		add_action( 'add_meta_boxes', array( $this, 'set_global_post_id' ), 1, 2 );
	}

	/**
	 * Synchronizes the product metas.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.1
	 *
	 * @param array $metas List of custom fields names.
	 * @return array
	 */
	public function copy_product_metas( $metas ) {
		$to_sync = array(
			'min_max_rules',
			'allow_combination',
			'group_of_quantity',
			'maximum_allowed_quantity',
			'minimum_allowed_quantity',
			'minmax_cart_exclude',
			'minmax_category_group_of_exclude',
			'minmax_do_not_count',
			'variation_group_of_quantity',
			'variation_maximum_allowed_quantity',
			'variation_minimum_allowed_quantity',
			'variation_minmax_cart_exclude',
			'variation_minmax_category_group_of_exclude',
			'variation_minmax_do_not_count',
		);

		return array_merge( $metas, array_combine( $to_sync, $to_sync ) );
	}

	/**
	 * Synchronizes the term metas.
	 * Hooked to the filter 'pll_copy_term_metas'.
	 *
	 * @since 1.1
	 *
	 * @param array $metas List of term metas names.
	 * @return array
	 */
	public function copy_term_metas( $metas ) {
		return array_merge( $metas, array( 'group_of_quantity' ) );
	}

	/**
	 * Sets global `$post_id` to avoid fatal error with Min/Max Quantities.
	 * Even if this global is not the official `$post_ID`.
	 *
	 * @see https://github.com/polylang/polylang-wc/issues/627.
	 *
	 * @since 1.8
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Current post object.
	 * @return void
	 */
	public function set_global_post_id( $post_type, $post ) {
		global $post_id;

		if (
			'post-new.php' === $GLOBALS['pagenow']
			&& isset( $_GET['from_post'], $_GET['new_lang'], $_GET['_wpnonce'] )
			&& 'product' === $post_type
			&& wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'new-post-translation' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			&& $post instanceof WP_Post
		) {
			$post_id = $post->ID; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}
}
