<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers;

defined( 'ABSPATH' ) or exit;

/**
 * Product helper class.
 *
 * @since 2.0.0
 */
class Product_Helper {


	/**
	 * Gets hierarchical product categories.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Product $product the product object
	 * @return \WP_Term[]
	 */
	public static function get_hierarchical_categories( \WC_Product $product, string $order = 'ASC' ): array {

		if ( $parent_id = $product->get_parent_id() ) {
			$product_id = $parent_id;
		} else {
			$product_id = $product->get_id();
		}

		return wc_get_product_terms( $product_id, 'product_cat', [ 'orderby' => 'parent', 'order' => $order ] );
	}


	/**
	 * Gets the category hierarchy up to 5 levels deep for the passed product.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Product $product the product object
	 * @return string the category hierarchy or an empty string
	 */
	public static function get_category_hierarchy( \WC_Product $product ): string {

		$categories = self::get_hierarchical_categories( $product, 'DESC' );

		if ( ! is_array( $categories ) || empty( $categories ) ) {
			return '';
		}

		$child_term = $categories[0];

		return trim( self::get_category_parents( $child_term->term_id ), '/' );
	}


	/**
	 * Builds the category hierarchy recursively.
	 *
	 * Inspired by {@see get_category_parents()} in WordPress core.
	 *
	 * @since 2.0.0
	 *
	 * @param int $term_id the category term ID
	 * @param string $separator the term separator
	 * @param array $visited the visited term IDs
	 * @return string|array|\WP_Error|\WP_Term
	 */
	private static function get_category_parents( int $term_id, string $separator = '/', array $visited = [] ) {

		$chain  = '';
		$parent = get_term( $term_id, 'product_cat' );

		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$name = $parent->name;

		if ( $parent->parent && ( $parent->parent !== $parent->term_id ) && ! in_array( $parent->parent, $visited, true ) && count( $visited ) < 4 ) {

			$visited[] = $parent->parent;

			$chain .= self::get_category_parents( $parent->parent, $separator, $visited );
		}

		$chain .= $name . $separator;

		return $chain;
	}


	/**
	 * Returns the identifier for a given product.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Product|int $product the product object or ID
	 * @return string the product identifier, either its SKU or `#<id>`
	 */
	public static function get_product_identifier( $product ): string {

		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return '';
		}

		if ( $product->get_sku() ) {

			$identifier = $product->get_sku();

		} else {

			if ( $parent_id = $product->get_parent_id() ) {
				$product_id = $parent_id;
			} else {
				$product_id = $product->get_id();
			}

			$identifier = '#' . $product_id;
		}

		return $identifier;
	}


	/**
	 * Returns a comma separated list of variation attributes for a given variation or variable product.
	 *
	 * For a variable product, the default variation attributes ar returned.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Product|int $product the product object or ID
	 * @return string comma-separated list of variation attributes
	 */
	public static function get_product_variation_attributes( $product ): string {

		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return '';
		}

		$variant = '';

		if ( $product->is_type( 'variation' ) ) {

			$variant = implode( ',', array_values( $product->get_variation_attributes() ) );

		} elseif ( $product->is_type( 'variable' ) ) {

			$variant = implode( ', ', array_values( $product->get_default_attributes() ) );
		}

		return $variant;
	}


	/**
	 * Gets the list type for the current screen.
	 *
	 * @since 2.0.0
	 *
	 * @return string the list type for the current screen
	 */
	public static function get_list_type(): string {

		$list_type = '';

		if ( is_search() ) {

			$list_type = __( 'Search', 'woocommerce-google-analytics-pro' );

		} elseif ( is_product_category() ) {

			$list_type = __( 'Product category', 'woocommerce-google-analytics-pro' );

		} elseif ( is_product_tag() ) {

			$list_type = __( 'Product tag', 'woocommerce-google-analytics-pro' );

		} elseif ( is_archive() ) {

			$list_type = __( 'Archive', 'woocommerce-google-analytics-pro' );

		} elseif ( is_single() ) {

			$list_type = __( 'Related/Up sell', 'woocommerce-google-analytics-pro' );

		} elseif ( is_cart() ) {

			$list_type = __( 'Cross sell (cart)', 'woocommerce-google-analytics-pro' );
		}

		/**
		 * Filters the list type for the current screen.
		 *
		 * @since 1.0.0
		 *
		 * @param string $list_type the list type for the current screen
		 */
		return apply_filters( 'wc_google_analytics_pro_list_type', $list_type );
	}


}
