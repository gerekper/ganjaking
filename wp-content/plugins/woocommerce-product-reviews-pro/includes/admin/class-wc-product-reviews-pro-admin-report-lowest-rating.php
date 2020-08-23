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
 * Lowest rating report
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Admin_Report_Lowest_Rating extends \WC_Admin_Report {


	/**
	 * Output the report
	 *
	 * @since 1.0.0
	 */
	public function output_report() {

		$products = $this->get_products();

		include( wc_product_reviews_pro()->get_plugin_path() . '/includes/admin/views/html-report-product-reviews.php' );
	}


	/**
	 * Get the rated products
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_products() {
		global $wpdb;

		$reviewed_products = array();

		$results = $wpdb->get_results("
			SELECT p.ID, c.review_count, c2.highest_rating, c2.lowest_rating
			FROM $wpdb->posts AS p
			LEFT JOIN ( SELECT comment_post_ID, COUNT(comment_ID) AS review_count
				FROM $wpdb->comments
				WHERE comment_type = 'review'
				AND comment_approved = '1'
				GROUP BY comment_post_ID
			) AS c ON ( c.comment_post_ID = p.ID )
			JOIN ( SELECT comment_post_ID, MAX(meta_value) AS highest_rating, MIN(meta_value) AS lowest_rating
				FROM $wpdb->comments c
				LEFT JOIN $wpdb->commentmeta cm
				ON cm.comment_id = c.comment_ID
				WHERE comment_type = 'review'
				AND meta_key = 'rating'
				AND comment_approved = '1'
				GROUP BY comment_post_ID
			) AS c2 ON( c2.comment_post_ID = p.ID )
			WHERE p.post_type = 'product'
			AND c.review_count > 0
			GROUP BY p.ID
			ORDER BY c2.lowest_rating ASC
		");

		if ( ! empty( $results ) ) {

			foreach ( $results as $key => $result ) {

				$product = wc_get_product( $result->ID );
				$reviewed_products[ $key ] = $product;
				$reviewed_products[ $key ]->review_count   = $result->review_count;
				$reviewed_products[ $key ]->highest_rating = $result->highest_rating;
				$reviewed_products[ $key ]->lowest_rating  = $result->lowest_rating;
				$reviewed_products[ $key ]->average_rating = $product->get_average_rating();
			}

			usort( $reviewed_products, array( $this, 'compare_rating' ) );
		}

		return $reviewed_products;
	}


	/**
	 * Helper to compare product ratings
	 *
	 * @since 1.0.0
	 * @param $a
	 * @param $b
	 * @return int
	 */
	public function compare_rating( $a, $b ) {
		return strcmp( $a->average_rating * 100, $b->average_rating * 100 );
	}


}
