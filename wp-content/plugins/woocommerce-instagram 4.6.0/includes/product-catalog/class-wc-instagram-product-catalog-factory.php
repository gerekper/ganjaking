<?php
/**
 * Product Catalog Factory.
 *
 * A factory for creating product catalog objects.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Product_Catalog_Factory.
 */
class WC_Instagram_Product_Catalog_Factory {

	/**
	 * Gets the product catalog.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $product_catalog Product catalog object, ID, or slug.
	 * @return WC_Instagram_Product_Catalog|false
	 */
	public static function get_catalog( $product_catalog ) {
		$catalog_id = self::get_id( $product_catalog );

		if ( false === $catalog_id ) {
			return false;
		}

		try {
			return new WC_Instagram_Product_Catalog( $catalog_id );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Gets the product catalog ID depending on what was passed.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $product_catalog Product catalog object, ID, or slug.
	 * @return int|false The product catalog ID. False on failure.
	 */
	public static function get_id( $product_catalog ) {
		if ( is_numeric( $product_catalog ) ) {
			return (int) $product_catalog;
		} elseif ( $product_catalog instanceof WC_Instagram_Product_Catalog ) {
			return $product_catalog->get_id();
		} elseif ( ! empty( $product_catalog->ID ) ) {
			return $product_catalog->ID;
		} elseif ( is_string( $product_catalog ) ) {
			return self::get_id_by_slug( $product_catalog );
		}

		return false;
	}

	/**
	 * Gets the product catalog ID by slug.
	 *
	 * @since 4.0.0
	 *
	 * @param string $slug The product catalog slug.
	 * @return int|false The product catalog ID. False on failure.
	 */
	public static function get_id_by_slug( $slug ) {
		if ( empty( $slug ) ) {
			return false;
		}

		$posts = get_posts(
			array(
				'post_type'      => 'wc_instagram_catalog',
				'post_status'    => 'publish',
				'name'           => $slug,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		return ( ! empty( $posts ) ? $posts[0] : false );
	}
}
