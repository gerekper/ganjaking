<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Class WPSEO_WooCommerce_OpenGraph
 */
class WPSEO_WooCommerce_OpenGraph {

	/**
	 * WPSEO_WooCommerce_OpenGraph constructor.
	 */
	public function __construct() {
		add_filter( 'language_attributes', [ $this, 'product_namespace' ], 11 );
		add_filter( 'wpseo_opengraph_type', [ $this, 'return_type_product' ] );

		add_action( 'wpseo_add_opengraph_additional_images', [ $this, 'set_opengraph_image' ] );
	}

	/**
	 * Return 'product' when current page is, well... a product.
	 *
	 * @param string $type Passed on without changing if not a product.
	 *
	 * @return string
	 */
	public function return_type_product( $type ) {
		if ( is_singular( 'product' ) ) {
			return 'product';
		}

		return $type;
	}

	/**
	 * Adds the OpenGraph images.
	 *
	 * @param mixed $opengraph_image The OpenGraph image to use.
	 *
	 * @return bool True when images are added, false when they're not.
	 */
	public function set_opengraph_image( $opengraph_image ) {
		if ( is_product_category() ) {
			return $this->set_opengraph_image_product_category( $opengraph_image );
		}

		$product = wc_get_product( get_queried_object_id() );
		if ( ! is_object( $product ) ) {
			return false;
		}

		return $this->set_opengraph_image_product( $opengraph_image, $product );
	}

	/**
	 * Filter for the namespace, adding the OpenGraph namespace.
	 *
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/product/
	 *
	 * @param string $input The input namespace string.
	 *
	 * @return string
	 */
	public function product_namespace( $input ) {
		if ( is_singular( 'product' ) ) {
			$input = preg_replace( '/prefix="([^"]+)"/', 'prefix="$1 product: http://ogp.me/ns/product#"', $input );
		}

		return $input;
	}

	/**
	 * Set the OpenGraph image for a product category based on the category thumbnail.
	 *
	 * @param object $opengraph_image The OpenGraph image class.
	 *
	 * @return bool True on success, false on failure.
	 */
	protected function set_opengraph_image_product_category( $opengraph_image ) {
		$thumbnail_id = get_term_meta( get_queried_object_id(), 'thumbnail_id', true );
		if ( $thumbnail_id ) {
			$opengraph_image->add_image_by_id( $thumbnail_id );

			return true;
		}

		return false;
	}

	/**
	 * Set the OpenGraph images for a product based on its gallery image IDs.
	 *
	 * @param object     $opengraph_image The OpenGraph image class.
	 * @param WC_Product $product         The WooCommerce product.
	 *
	 * @return bool True on success, false on failure.
	 */
	protected function set_opengraph_image_product( $opengraph_image, WC_Product $product ) {
		// Don't add the gallery images if the user set a specific image for this product.
		if ( $this->is_opengraph_image_set_by_user( $product->get_id() ) ) {
			return true;
		}

		$img_ids = $product->get_gallery_image_ids();

		if ( is_array( $img_ids ) && $img_ids !== [] ) {
			foreach ( $img_ids as $img_id ) {
				$opengraph_image->add_image_by_id( $img_id );
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks whether users set a specific open graph image for a product.
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return bool Whether users set a specific open graph image for a product.
	 */
	protected function is_opengraph_image_set_by_user( $product_id ) {
		$indexable_repository = YoastSEO()->classes->get( 'Yoast\WP\SEO\Repositories\Indexable_Repository' );
		$indexable            = $indexable_repository->find_by_id_and_type( $product_id, 'post' );

		return $indexable->open_graph_image_source === 'set-by-user';
	}
}
