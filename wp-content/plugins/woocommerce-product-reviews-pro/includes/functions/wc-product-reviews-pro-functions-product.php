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
 * Global functions for handling products contribution data.
 *
 * @since 1.12.0
 */


/**
 * Returns the contribution count for a product, filtered by contribution type(s).
 *
 * @since 1.12.0
 *
 * @param \WP_Post|\WC_Product|int $product product object, ID or post
 * @param string|string[] $contribution_type contribution types or type
 * @return int
 */
function wc_product_reviews_pro_get_contributions_count( $product, $contribution_type = array() ) {

	return \WC_Product_Reviews_Pro_Products::get_product_contributions_count( $product, $contribution_type );
}


/**
 * Returns a user's review count on particular product.
 *
 * @since 1.8.0
 *
 * @param int|\WP_User $user_id user ID or object
 * @param int|\WC_Product|\WP_Post $product_id product ID, object or post
 * @return int
 */
function wc_product_reviews_pro_get_user_review_count( $user_id, $product_id ) {

	return \WC_Product_Reviews_Pro_Products::get_user_review_count_for_product( $user_id, $product_id );
}


/**
 * Returns the highest rating count for a product.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Product|\WP_Post $product_id product ID, object or post
 * @return int
 */
function wc_product_reviews_pro_get_highest_rating( $product_id ) {

	return \WC_Product_Reviews_Pro_Products::get_product_highest_rating( $product_id );
}


/**
 * Returns the lowest rating count for a product.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Product|\WP_Post $product_id product ID, object or post
 * @return int
 */
function wc_product_reviews_pro_get_lowest_rating( $product_id ) {

	return \WC_Product_Reviews_Pro_Products::get_product_lowest_rating( $product_id );
}
