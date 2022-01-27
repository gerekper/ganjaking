<?php
/**
 * Class to handle the Google product categories.
 *
 * @package WC_Instagram
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Google_Product_Categories class.
 */
class WC_Instagram_Google_Product_Categories {

	/**
	 * Google product categories.
	 *
	 * @var array
	 */
	protected static $categories = array();

	/**
	 * Loads the categories.
	 *
	 * @since 3.3.0
	 */
	protected static function load_categories() {
		$categories = include WC_INSTAGRAM_PATH . '/data/google-product-categories.php';

		if ( has_filter( 'woocommerce_instagram_google_product_categories' ) ) {
			wc_deprecated_hook( 'woocommerce_instagram_google_product_categories', '3.7.0', 'wc_instagram_google_product_categories' );

			/**
			 * Filters the Google product categories.
			 *
			 * @since      3.3.0
			 * @deprecated 3.7.0
			 *
			 * @param array $categories The Google product categories.
			 */
			$categories = apply_filters( 'woocommerce_instagram_google_product_categories', $categories );
		}

		/**
		 * Filters the Google product categories.
		 *
		 * @since 3.7.0
		 *
		 * @param array $categories The Google product categories.
		 */
		self::$categories = apply_filters( 'wc_instagram_google_product_categories', $categories );
	}

	/**
	 * Gets all categories.
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public static function get_categories() {
		if ( empty( self::$categories ) ) {
			self::load_categories();
		}

		return self::$categories;
	}

	/**
	 * Gets all the categories that doesn't have a parent one.
	 *
	 * @since 3.3.0
	 *
	 * @return array
	 */
	public static function get_top_categories() {
		$categories = self::get_categories();

		$top_categories = get_option( 'wc_instagram_google_product_top_categories', array() );

		if ( empty( $top_categories ) ) {
			// Filter top categories.
			$top_categories = array_filter( array_keys( $categories ), 'self::is_top_category' );

			// Reset indices.
			$top_categories = array_values( $top_categories );

			update_option( 'wc_instagram_google_product_top_categories', $top_categories );
		}

		return $top_categories;
	}

	/**
	 * Gets the children of the specified category.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return array
	 */
	public static function get_children( $category_id ) {
		$categories = self::get_categories();

		if ( ! isset( $categories[ $category_id ], $categories[ $category_id ]['children'] ) ) {
			return array();
		}

		return array_map( 'intval', $categories[ $category_id ]['children'] );
	}

	/**
	 * Gets the parent of the specified category.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return int|false The parent category ID. False on failure.
	 */
	public static function get_parent( $category_id ) {
		$categories = self::get_categories();

		if ( ! isset( $categories[ $category_id ], $categories[ $category_id ]['parent'] ) ) {
			return false;
		}

		return intval( $categories[ $category_id ]['parent'] );
	}

	/**
	 * Gets the specified category has a parent.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return bool.
	 */
	public static function has_parent( $category_id ) {
		return ( false !== self::get_parent( $category_id ) );
	}

	/**
	 * Gets all parents of the specified category.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return array
	 */
	public static function get_parents( $category_id ) {
		$parents = array();
		$parent  = $category_id;

		do {
			$parent = self::get_parent( $parent );

			if ( $parent ) {
				$parents[] = $parent;
			}
		} while ( $parent );

		return array_reverse( $parents );
	}

	/**
	 * Gets all the options available of the parent $category_id provided.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return array
	 */
	public static function get_sibling_titles( $category_id ) {
		$parent_id    = self::get_parent( $category_id );
		$category_ids = ( $parent_id ? self::get_children( $parent_id ) : self::get_top_categories() );

		return self::get_titles( $category_ids );
	}

	/**
	 * Gets the title of a category.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return string
	 */
	public static function get_title( $category_id ) {
		$categories = self::get_categories();

		if ( ! isset( $categories[ $category_id ], $categories[ $category_id ]['title'] ) ) {
			return '';
		}

		return $categories[ $category_id ]['title'];
	}

	/**
	 * Gets the titles for the specified categories.
	 *
	 * Returns an array in pairs [category_id => title].
	 *
	 * @since 3.3.0
	 *
	 * @param array $category_ids Array of category IDs.
	 * @return array
	 */
	public static function get_titles( $category_ids ) {
		$titles = array();

		foreach ( $category_ids as $category_id ) {
			$titles[ $category_id ] = self::get_title( $category_id );
		}

		return $titles;
	}

	/**
	 * Gets the specified category is a top level category (has no parents).
	 * This method is used by `get_top_level_categories` as callback in the array filter.
	 *
	 * @since 3.3.0
	 *
	 * @param int $category_id The category ID.
	 * @return bool
	 */
	private static function is_top_category( $category_id ) {
		return ! self::has_parent( $category_id );
	}
}
