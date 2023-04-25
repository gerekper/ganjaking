<?php
/**
 * Legacy Meta Compatibility - Read child items from meta until DB is updated.
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Legacy_Meta Class.
 *
 * Adds back-compatibility for 1.x post metas until DB is updated.
 */
class WC_MNM_Legacy_Meta {

	public static function init() {
		add_filter( 'wc_mnm_child_items', array( __CLASS__, 'read_items_from_meta' ), 0, 2 );
		add_filter( 'woocommerce_product_get_packing_mode', array( __CLASS__, 'read_packing_mode_from_meta' ), 0, 2 );
		add_filter( '_wc_mnm_backcompat_product_get_packing_mode', array( __CLASS__, 'read_packing_mode_from_meta' ), 0, 2 );
		add_filter( 'woocommerce_product_get_content_source', array( __CLASS__, 'read_content_source_from_meta' ), 0, 2 );
		add_filter( 'woocommerce_product_get_child_category_ids', array( __CLASS__, 'read_child_category_ids_from_meta' ), 0, 2 );
	}

	/**
	 * Read the child items from _mnm_data post meta
	 *
	 * @param  WC_MNM_Child_Item[]
	 * @param  WC_Product_Mix_and_Match  $product
	 * @return WC_MNM_Child_Item[]
	 */
	public static function read_items_from_meta( $child_items, $product ) {

		$legacy_items = WC_MNM_Helpers::cache_get( $product->get_id(), 'child_items_legacy' );

		if ( null === $legacy_items ) {

			$legacy_items = array();

			$meta = get_post_meta( $product->get_id(), '_mnm_data', true );

			if ( ! empty( $meta ) && function_exists( '_prime_post_caches' ) ) {
				_prime_post_caches( array_keys( $meta ) );
			}

			$meta = is_array( $meta ) && ! empty( $meta ) ? $meta : array();

			foreach ( $meta as $item_key => $item_data ) {
				$item_data = array_merge( $item_data, array( 'container_id' => $product->get_id() ) );

				$legacy_item = new WC_MNM_Child_Item( $item_data, $product );

				if ( $legacy_item->exists() && $legacy_item->is_visible() ) {
					$legacy_items[ 'product-' . $item_key ] = $legacy_item;
				}
			}

			WC_Mix_and_Match_Helpers::cache_set( $product->get_id(), $legacy_items, 'child_items_legacy' );

		}

		return $legacy_items;
	}

	/**
	 * Set packing mode based on 1.0x style meta.
	 *
	 * @param  string $mode
	 * @param  WC_Product_Mix_and_Match  $product
	 * @return string
	 */
	public static function read_packing_mode_from_meta( $mode, $product ) {

		$is_virtual           = wc_string_to_bool( get_post_meta( $product->get_id(), '_virtual', true ) );
		$per_product_shipping = wc_string_to_bool( get_post_meta( $product->get_id(), '_mnm_per_product_shipping', true ) );

		if ( $is_virtual && $per_product_shipping ) {
			$mode = 'separate';
		} else if ( $is_virtual && ! $per_product_shipping ) {
			$mode = 'virtual';
		} else if ( ! $is_virtual && $per_product_shipping ) {
			$mode = 'separate_plus';
		} else if ( ! $is_virtual && ! $per_product_shipping ) {
			$mode = 'together';
		}

		return $mode;
	}


	/**
	 * Set content source based on mini-extension meta.
	 *
	 * @param  string $source
	 * @param  WC_Product_Mix_and_Match  $product
	 * @return string
	 */
	public static function read_content_source_from_meta( $source, $product ) {

		if ( 'yes' === $product->get_meta( '_mnm_use_category' ) ) {
			$source = 'categories';
		}

		return $source;
	}


	/**
	 * Set catagories based on mini-extension meta.
	 *
	 * @param  int|string[] $categories
	 * @param  WC_Product_Mix_and_Match  $product
	 * @return array
	 */
	public static function read_child_category_ids_from_meta( $categories, $product ) {

		$new_categories = array();
		$old_categories = $product->get_meta( '_mnm_product_cat' );

		if ( is_integer( $old_categories ) ) {

			$new_categories[] = $old_categories;

		} elseif ( is_array( $old_categories ) ) {

			foreach( $old_categories as $slug ) {

				$cat = get_term_by( 'slug', $slug, 'product_cat' );

				if ( $cat instanceof WP_Term ) {
					$new_categories[] = $cat->term_id;
				}

			}

		}

		return $new_categories;
	}

}
WC_MNM_Legacy_Meta::init();
