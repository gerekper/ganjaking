<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to manage WooCommerce product import with Polylang Pro XLIFF Importer.
 *
 * @since 1.8
 */
class PLLWC_Translation_Import {
	/**
	 * Adds hooks.
	 *
	 * @since 1.8
	 *
	 * @return self
	 */
	public function init() {
		add_action( 'pll_after_post_import', array( $this, 'process_variations' ), 10, 2 );
		add_filter( 'wp_insert_post_data', array( $this, 'set_variations_post_status' ) );

		return $this;
	}

	/**
	 * Processes imported posts to translate parent ID for variation products.
	 * Not done by Polylang Pro because `WC_Product_Variation` and `WC_Product_Variable` don't share the same post type.
	 *
	 * @since 1.8
	 *
	 * @param PLL_Language $target_language      The targeted language for import.
	 * @param array        $imported_objects_ids The imported object ids of the import.
	 * @return void
	 */
	public function process_variations( $target_language, $imported_objects_ids ) {
		$args = array(
			'type'    => 'variation',
			'limit'   => count( $imported_objects_ids ),
			'include' => $imported_objects_ids,
		);
		$variations = wc_get_products( $args );

		if ( empty( $variations ) ) {
			return;
		}

		$data_store = PLLWC_Data_Store::load( 'product_language' );

		foreach ( $variations as $variation ) {
			$tr_variation = $data_store->get( $variation->get_id(), $target_language->slug );
			$tr_variation = wc_get_product( (int) $tr_variation );

			if ( empty( $tr_variation ) ) {
				return;
			}

			$parent    = $tr_variation->get_parent_id();
			$tr_parent = $data_store->get( $parent, $target_language->slug );

			if ( empty( $tr_parent ) ) {
				continue;
			}

			$tr_variation->set_parent_id( $tr_parent );
			$tr_variation->save();
		}
	}

	/**
	 * Sets the `post_status` to `publish` for product variations, otherwise
	 * the variation is not accessible in backoffice, even if the parent is a draft.
	 *
	 * @since 1.8
	 *
	 * @param array $data An array of slashed, sanitized, and processed post data.
	 * @return array Filtered post data.
	 */
	public function set_variations_post_status( $data ) {
		if ( 'product_variation' === $data['post_type'] ) {
			$data['post_status'] = 'publish';
		}

		return $data;
	}
}
