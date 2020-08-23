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

// load required functions
require_once ( wc_product_reviews_pro()->get_plugin_path() . '/includes/functions/wc-product-reviews-pro-functions-product.php' );
require_once ( wc_product_reviews_pro()->get_plugin_path() . '/includes/functions/wc-product-reviews-pro-functions-contribution.php' );
require_once ( wc_product_reviews_pro()->get_plugin_path() . '/includes/functions/wc-product-reviews-pro-functions-template.php' );


// TODO remove this backport when Woocommerce 3.6.0 is the minimum required version {FN 2019-03-25}
if ( ! function_exists( 'wc_reviews_enabled' ) ) {

	/**
	 * Checks if reviews are enabled.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	function wc_reviews_enabled() {

		return 'yes' === get_option( 'woocommerce_enable_reviews' );
	}

}


// TODO remove this backport when Woocommerce 3.6.0 is the minimum required version {FN 2019-03-25}
if ( ! function_exists( 'wc_review_ratings_enabled' ) ) {

	/**
	 * Checks if reviews ratings are enabled.
	 *
	 * @since 3.6.0
	 * @return bool
	 */
	function wc_review_ratings_enabled() {

		return 'yes' === get_option( 'woocommerce_enable_reviews' )
			&& 'yes' === get_option( 'woocommerce_enable_review_rating' );
	}

}

// TODO remove this backport when Woocommerce 3.6.0 is the minimum required version {FN 2019-03-25}
if ( ! function_exists( 'wc_review_ratings_required' ) ) {

	/**
	 * Checks if review ratings are required.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	function wc_review_ratings_required() {

		return 'yes' === get_option( 'woocommerce_review_rating_required' );
	}

}
