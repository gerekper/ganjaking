<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Reviews Pro Products handler.
 *
 * @since 1.12.0
 */
class WC_Product_Reviews_Pro_Products {


	/** @var array memoization for contribution count per product by contribution type */
	private static $contribution_count_for_product = array();

	/** @var array memoization for user review count per product */
	private static $user_review_count_for_product = array();


	/**
	 * Returns the product ID (helper method).
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WP_Post|\WC_Product $product a product ID, object or post
	 * @return int
	 */
	private static function get_product_id( $product ) {

		if ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} elseif ( $product instanceof \WP_Post ) {
			$product_id = $product->ID;
		} else {
			$product_id = $product;
		}

		return is_numeric( $product_id ) ? (int) $product_id : 0;
	}


	/**
	 * Returns the user ID (helper method).
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WP_User $user a user ID or object
	 * @return int
	 */
	private static function get_user_id( $user ) {

		if ( $user instanceof \WP_User ) {
			$user_id = $user->ID;
		} else {
			$user_id = $user;
		}

		return is_numeric( $user_id ) ? (int) $user_id : 0;
	}


	/**
	 * Returns a user's review count for a given product.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WP_User $user a user ID or user object
	 * @param int|\WC_Product|\WP_Post $product a product ID, object or post
	 * @return int
	 */
	public static function get_user_review_count_for_product( $user, $product ) {
		global $wpdb;

		$review_count = 0;
		$user_id      = self::get_user_id( $user );
		$product_id   = self::get_product_id( $product );

		if ( $user_id > 0 && $product_id > 0 ) {

			if ( ! isset( self::$user_review_count_for_product[ $user_id ][ $product_id ] ) ) {

				$review_count = $wpdb->get_var( $wpdb->prepare("
					SELECT COUNT(comment_ID) FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND user_id = %d
					AND comment_type = 'review'
					AND comment_approved != 'trash'
				", $product_id, $user_id ) );

				if ( is_numeric( $review_count ) ) {
					self::$user_review_count_for_product[ $user_id ][ $product_id ] = $review_count;
				}

			} else {

				$review_count = self::$user_review_count_for_product[ $user_id ][ $product_id ];
			}
		}

		return is_numeric( $review_count ) ? (int) $review_count : 0;
	}


	/**
	 * Returns the contribution count for a product, filtered by contribution type(s).
	 *
	 * @since 1.12.0
	 *
	 * @param \WP_Post|\WC_Product|int $product product object, ID or post
	 * @param string|string[] $types contribution types
	 * @return int
	 */
	public static function get_product_contributions_count( $product, $types = array() ) {
		global $wpdb;

		$count      = null;
		$product_id = self::get_product_id( $product );

		if ( $product_id > 0 ) {

			$cache_key = $transient_key = '';

			if ( is_string( $types ) ) {
				$cache_key = $transient_key = $types;
			} elseif( is_array( $types ) ) {
				$cache_key = implode( ',', $types );
			}

			// get count for all contributions regardless of type
			if ( '' === $cache_key || empty( $types ) ) {
				$cache_key     = 'any';
				$transient_key = 'contributions';
			}

			// we record a transient if we are requesting either all or one specific contribution type
			if ( '' !== $transient_key ) {
				$count = get_transient( "wc_product_reviews_pro_{$transient_key}_count_{$product_id}" );
			}

			if ( ! is_numeric( $count ) ) {

				if ( ! isset( self::$contribution_count_for_product[ $product_id ][ $cache_key ] ) ) {

					$select = $wpdb->prepare( "
						SELECT COUNT(comment_ID) 
						FROM $wpdb->comments 
						WHERE comment_post_ID = %d 
						AND comment_approved = 1
					", (int) $product_id );

					if ( 'any' !== $cache_key && ! empty( $types ) ) {

						$where_types  = implode( ', ', array_fill( 0, count( (array) $types ), '%s' ) );
						$select      .= $wpdb->prepare( " 
							AND comment_type IN({$where_types})
						", (array) $types );
					}

					$count = self::$contribution_count_for_product[ $product_id ][ $cache_key ] = $wpdb->get_var( $select );

				} else {

					$count = self::$contribution_count_for_product[ $product_id ][ $cache_key ];
				}

				// we can set a transient if we are requesting either all or one specific contribution type
				if ( '' !== $transient_key ) {
					set_transient( "wc_product_reviews_pro_{$transient_key}_count_{$product_id}", (int) $count, YEAR_IN_SECONDS );
				}
			}
		}

		return (int) $count;
	}


	/**
	 * Returns either the highest or the lowest rating for the given product.
	 *
	 * @since 1.12.0
	 *
	 * @param string $type must be either 'lowest' or 'highest'
	 * @param int|\WC_Product|\WP_Post $product a product ID, object or post
	 * @return int
	 */
	private static function get_product_rating( $type, $product ) {
		global $wpdb;

		$rating = 0;

		if ( in_array( $type, array( 'lowest', 'highest' ), true ) ) {

			$product_id = self::get_product_id( $product );

			if ( $product_id > 0 ) {

				$rating = get_transient( "wc_product_reviews_pro_{$type}_rating_{$product_id}" );

				if ( ! is_numeric( $rating ) ) {

					if ( 'highest' === $type ) {

						$rating = $wpdb->get_var($wpdb->prepare("
							SELECT MAX(meta_value) FROM $wpdb->commentmeta
							LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
							WHERE comment_post_ID = %d
							AND comment_approved = '1'
							AND comment_type = 'review'
							AND meta_key = 'rating'
							AND meta_value > 0
						", $product_id));

					} elseif ( 'lowest' === $type ) {

						$rating = $wpdb->get_var( $wpdb->prepare("
							SELECT MIN(meta_value) FROM $wpdb->commentmeta
							LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
							WHERE comment_post_ID = %d
							AND comment_approved = '1'
							AND comment_type = 'review'
							AND meta_key = 'rating'
							AND meta_value > 0
						", $product_id ) );
					}

					if ( is_numeric( $rating ) ) {
						set_transient( "wc_product_reviews_pro_{$type}_rating_{$product_id}", $rating, YEAR_IN_SECONDS );
					}
				}
			}
		}

		return is_numeric( $rating ) ? $rating : 0;
	}


	/**
	 * Returns the highest rating for a product.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WC_Product|\WP_Post $product product object, ID or post
	 * @return int
	 */
	public static function get_product_highest_rating( $product ) {

		return self::get_product_rating( 'highest', $product );
	}


	/**
	 * Returns the lowest rating for a product.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WC_Product|\WP_Post $product product object, ID or post
	 * @return int
	 */
	public static function get_product_lowest_rating( $product ) {

		return self::get_product_rating( 'lowest', $product );
	}


	/**
	 * Clears transients set for a product.
	 *
	 * @since 1.12.0
	 *
	 * @param int|\WP_Post|\WC_Product $product product object, ID or post
	 */
	public static function clear_transients( $product ) {

		$product_id = self::get_product_id( $product );

		if ( $product_id > 0 ) {

			$transients = array(
				"wc_product_reviews_pro_lowest_rating_{$product_id}",
				"wc_product_reviews_pro_highest_rating_{$product_id}",
				"wc_product_reviews_pro_contributions_count_{$product_id}"
			);

			foreach ( wc_product_reviews_pro_get_contribution_types() as $contribution_type ) {

				$transients[] = "wc_product_reviews_pro_{$contribution_type}_count_{$product_id}";
			}

			foreach ( $transients as $transient ) {

				delete_transient( $transient );
			}
		}

		// clear cached data too
		self::$contribution_count_for_product = array();
		self::$user_review_count_for_product  = array();
	}


}
