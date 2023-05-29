<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to manage WooCommerce product export with Polylang Pro XLIFF Exporter.
 *
 * @since 1.8
 */
class PLLWC_Translation_Export {
	/**
	 * Adds hooks.
	 *
	 * @since 1.8
	 *
	 * @return self
	 */
	public function init() {
		add_filter( 'pll_collect_post_ids', array( $this, 'collect_variations' ), 10, 2 );
		add_filter( 'pll_post_metas_to_export', array( $this, 'export_product_metas' ), 10, 2 );

		return $this;
	}

	/**
	 * Adds product variations to the exported posts.
	 *
	 * @since 1.8
	 *
	 * @param int[] $linked_ids Post ids attached to a post.
	 * @param int   $post_id    The post id the post we get other post from.
	 * @return int[]
	 */
	public function collect_variations( $linked_ids, $post_id ) {
		$product = wc_get_product( $post_id );

		if ( empty( $product ) ) {
			return $linked_ids;
		}

		if ( ! $product->is_type( 'variable' ) ) {
			return $linked_ids;
		}

		return array_merge( $linked_ids, $product->get_children() );
	}

	/**
	 * Exports translatable product metas.
	 *
	 * @since 1.8
	 *
	 * @param array $keys A recursive array containing nested meta sub keys to translate.
	 * @param int   $from ID of the source object.
	 * @return array Metas to export.
	 */
	public function export_product_metas( $keys, $from ) {
		if ( empty( wc_get_product( $from ) ) ) {
			return $keys;
		}

		return array_merge(
			$keys,
			array(
				'_button_text'           => 1,
				'_purchase_note'         => 1,
				'_variation_description' => 1,
			)
		);
	}
}
