<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Variation Swatches and Photos.
 * Version tested: 3.0.9
 *
 * @since 1.1
 */
class PLLWC_Swatches {

	/**
	 * Constructor.
	 * Setups filters.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		// Attribute terms metas.
		add_filter( 'pll_copy_term_metas', array( $this, 'copy_term_metas' ), 10, 3 );
		if ( PLL()->options['media_support'] ) {
			add_filter( 'pll_translate_term_meta', array( $this, 'translate_swatches_photo' ), 10, 3 );
		}

		// Product metas.
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_product_metas' ) );
		add_filter( 'pllwc_translate_product_meta', array( $this, 'translate_product_meta' ), 10, 4 );
	}

	/**
	 * Synchronizes the attribute term metas.
	 * Hooked to the filter 'pll_copy_term_metas'.
	 *
	 * @since 1.1
	 *
	 * @param array $metas List of custom fields names.
	 * @param bool  $sync  True if it is synchronization, false if it is a copy.
	 * @param int   $from  Id of the product from which we copy informations.
	 * @return array
	 */
	public function copy_term_metas( $metas, $sync, $from ) {
		$key_names = array( '_swatches_id_type', '_swatches_id_color', '_swatches_id_photo' );
		$to_copy = array();

		foreach ( array_keys( get_term_meta( $from ) ) as $key ) {
			foreach ( $key_names as $name ) {
				if ( false !== strpos( $key, $name ) ) {
					$to_copy[] = $key;
				}
			}
		}

		return array_merge( $metas, $to_copy );
	}

	/**
	 * Translates the Swatches photo id.
	 * Required only if media are translated.
	 * Hooked to the filter 'pll_translate_term_meta'.
	 *
	 * @since 1.1
	 *
	 * @param int    $value Photo id.
	 * @param string $key   Meta key.
	 * @param string $lang  Language code.
	 * @return int
	 */
	public function translate_swatches_photo( $value, $key, $lang ) {
		if ( false !== strpos( $key, '_swatches_id_photo' ) ) {
			$to_value = pll_get_post( $value, $lang );
			$value = $to_value ? $to_value : $value;
		}
		return $value;
	}

	/**
	 * Adds metas to synchronize when saving a product.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.1
	 *
	 * @param array $metas List of custom fields names.
	 * @return array
	 */
	public function copy_product_metas( $metas ) {
		$to_sync = array(
			'_swatch_type_options',
			'_swatch_type',
		);
		return array_merge( $metas, array_combine( $to_sync, $to_sync ) );
	}

	/**
	 * Translates options in product metas.
	 * Hooked to the filter 'pllwc_translate_product_meta'.
	 *
	 * @since 1.1
	 *
	 * @param mixed  $value Meta value.
	 * @param string $key   Meta key.
	 * @param string $lang  Language of target.
	 * @param int    $from  Id of the source.
	 * @return mixed
	 */
	public function translate_product_meta( $value, $key, $lang, $from ) {
		if ( '_swatch_type_options' !== $key ) {
			return $value;
		}
		$product = wc_get_product( $from );
		if ( ! $product instanceof WC_Product_Variable ) {
			return $value;
		}

		$attr_terms = array();
		$data_store = PLLWC_Data_Store::load( 'product_language' );
		$orig_lang  = $data_store->get_language( $from );
		$attributes = $product->get_variation_attributes();

		foreach ( $attributes as $tax => $terms ) {
			foreach ( $terms as $slug ) {
				$attr_terms[ md5( $slug ) ] = array(
					'taxonomy' => $tax,
					'slug'     => $slug,
				);
			}
		}

		foreach ( $value as $i => $option ) {
			foreach ( $option['attributes'] as $k => $attr ) {
				if ( PLL()->options['media_support'] && $tr_id = pll_get_post( $attr['image'], $lang ) ) {
					$attr['image'] = $tr_id;
				}

				$terms = get_terms( $attr_terms[ $k ]['taxonomy'], array( 'slug' => $attr_terms[ $k ]['slug'], 'lang' => $orig_lang ) );
				$term = reset( $terms );
				$tr_term = get_term( pll_get_term( $term->term_id, $lang ) );
				$tr_k = md5( $tr_term->slug );
				unset( $value[ $i ]['attributes'][ $k ] );
				$value[ $i ]['attributes'][ $tr_k ] = $attr;
			}
		}

		return $value;
	}
}
