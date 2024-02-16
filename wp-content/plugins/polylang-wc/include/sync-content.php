<?php
/**
 * @package Polylang-WC
 */

/**
 * Smart copies WooCommerce blocks.
 *
 * @since 1.2
 */
class PLLWC_Sync_Content {

	/**
	 * Constructor.
	 * Setup filters.
	 *
	 * @since 1.2
	 */
	public function __construct() {
		add_filter( 'pll_translate_blocks', array( $this, 'translate_blocks' ), 10, 2 );
	}

	/**
	 * Translate blocks.
	 * Hooked to the filter 'pll_translate_blocks'.
	 *
	 * @since 1.2
	 *
	 * @param array  $blocks An array of blocks arrays.
	 * @param string $lang   Target language.
	 * @return array
	 */
	public function translate_blocks( $blocks, $lang ) {
		foreach ( $blocks as $k => $block ) {
			switch ( $block['blockName'] ) {
				case 'woocommerce/handpicked-products':
					/** @var PLLWC_Product_Language_CPT */
					$data_store = PLLWC_Data_Store::load( 'product_language' );
					$products = array();

					foreach ( $block['attrs']['products'] as $id ) {
						$products[] = $data_store->get( $id, $lang );
					}

					$blocks[ $k ]['attrs']['products'] = $products;
					$blocks[ $k ]['innerContent'][0] = $blocks[ $k ]['innerHTML'] = preg_replace( '#ids="\d+(,\d+)*"#', 'ids="' . implode( ',', $products ) . '"', $block['innerHTML'] );
					break;

				case 'woocommerce/product-category':
				case 'woocommerce/product-best-sellers':
				case 'woocommerce/product-new':
				case 'woocommerce/product-top-rated':
				case 'woocommerce/product-on-sale':
					if ( ! empty( $block['attrs']['categories'] ) ) {
						$categories = array();

						foreach ( $block['attrs']['categories'] as $id ) {
							$categories[] = pll_get_term( $id, $lang );
						}

						$blocks[ $k ]['attrs']['categories'] = $categories;
						$blocks[ $k ]['innerContent'][0] = $blocks[ $k ]['innerHTML'] = preg_replace( '#category="\d+(,\d+)*"#', 'category="' . implode( ',', $categories ) . '"', $block['innerHTML'] );
					}
					break;

				case 'woocommerce/products-by-attribute':
					$terms = array();

					foreach ( $block['attrs']['attributes'] as $n => $attributes ) {
						$tr_id = pll_get_term( $attributes['id'], $lang );
						$blocks[ $k ]['attrs']['attributes'][ $n ]['id'] = $tr_id;
						$terms[] = $tr_id;
					}

					$blocks[ $k ]['innerContent'][0] = $blocks[ $k ]['innerHTML'] = preg_replace( '#terms="\d+(,\d+)*"#', 'terms="' . implode( ',', $terms ) . '"', $block['innerHTML'] );
					break;

				case 'woocommerce/featured-product':
					if ( empty( $block['innerBlocks'] ) ) {
						break;
					}

					/** @var PLLWC_Product_Language_CPT */
					$data_store = PLLWC_Data_Store::load( 'product_language' );
					$tr_id      = $data_store->get( $block['attrs']['productId'], $lang );

					if ( empty( $tr_id ) ) {
						break;
					}

					$product = wc_get_product( $tr_id );

					if ( empty( $product ) ) {
						break;
					}

					$blocks[ $k ]['attrs']['productId'] = $tr_id;

					$tr_link = $product->get_permalink();

					if ( empty( $tr_link ) || ! is_string( $tr_link ) ) {
						break;
					}

					// Translates the URL in the button.
					$this->translate_button_link( $blocks[ $k ]['innerBlocks'][0], $tr_link );
					break;

				case 'woocommerce/featured-category':
					if ( empty( $block['innerBlocks'] ) ) {
						break;
					}

					$tr_id = pll_get_term( $block['attrs']['categoryId'], $lang );

					if ( empty( $tr_id ) ) {
						break;
					}

					$blocks[ $k ]['attrs']['categoryId'] = $tr_id;

					$tr_link = get_term_link( $tr_id );

					if ( empty( $tr_link ) || ! is_string( $tr_link ) ) {
						break;
					}

					// Translates the URL in the button.
					$this->translate_button_link( $blocks[ $k ]['innerBlocks'][0], $tr_link );
					break;

				case 'woocommerce/reviews-by-product':
					/** @var PLLWC_Product_Language_CPT */
					$data_store = PLLWC_Data_Store::load( 'product_language' );

					$tr_id = $data_store->get( $block['attrs']['productId'], $lang );

					$blocks[ $k ]['attrs']['productId'] = $tr_id;
					$blocks[ $k ]['innerContent'][0] = $blocks[ $k ]['innerHTML'] = preg_replace( '#data-product-id="\d+"#', 'data-product-id="' . $tr_id . '"', $block['innerHTML'] );
					break;

				case 'woocommerce/reviews-by-category':
					$categories = array();

					foreach ( $block['attrs']['categoryIds'] as $id ) {
						$tr_id = pll_get_term( $id, $lang );
						if ( $tr_id ) {
							$categories[] = $tr_id;
						}
					}

					if ( ! empty( $categories ) ) {
						$blocks[ $k ]['attrs']['categoryIds'] = $categories;
						$blocks[ $k ]['innerContent'][0] = $blocks[ $k ]['innerHTML'] = preg_replace( '#data-category-ids="\d+(,\d+)*"#', 'data-category-ids="' . implode( ',', $categories ) . '"', $block['innerHTML'] );
					}
					break;

				case 'woocommerce/product-tag':
					$tags = array();

					foreach ( $block['attrs']['tags'] as $id ) {
						$tr_id = pll_get_term( $id, $lang );
						if ( $tr_id ) {
							$tags[] = $tr_id;
						}
					}

					if ( ! empty( $tags ) ) {
						$blocks[ $k ]['attrs']['tags'] = $tags;
					}
					break;
			}
		}

		return $blocks;
	}

	/**
	 * Translates the HTML link code inside the block depending on the block version.
	 *
	 * @since 1.9.4
	 *
	 * @param array  $innerblock The block to search into and to modify.
	 * @param string $link       The translated link for replacing.
	 * @return void
	 */
	private function translate_button_link( array &$innerblock, string $link ) {
		if ( ! empty( $innerblock['innerBlocks'] ) ) {
			$this->translate_link( $innerblock['innerBlocks'][0], $link ); // Since WC 6.4.0.
		} else {
			$this->translate_link( $innerblock, $link ); // If the block was created in WC < 6.4.0.
		}
	}

	/**
	 * Translates one version of the HTML link code inside the block.
	 *
	 * @since 1.9.4
	 *
	 * @param array  $innerblock The block to search into and to modify.
	 * @param string $link       The translated link for replacing.
	 * @return void
	 */
	private function translate_link( array &$innerblock, string $link ) {
		$dom = new DOMDocument();
		$dom->loadHTML( $innerblock['innerHTML'] );
		$tags = $dom->getElementsByTagName( 'a' );

		if ( empty( $tags[0] ) ) {
			return;
		}

		$href = $tags[0]->getAttribute( 'href' );

		$innerblock['innerContent'][0] = $innerblock['innerHTML'] = str_replace( $href, $link, $innerblock['innerHTML'] );
	}
}
