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
 * WooCommerce Points And Rewards integration.
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Integration_Points_And_Rewards {


	/**
	 * Hooks into plugin.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// ensure only allowed contributions are counted towards earned review points
		add_filter( 'wc_points_rewards_review_post_comments_args',    array( $this, 'review_get_comments_args' ), 10, 2 );
		add_filter( 'wc_points_rewards_review_approve_comments_args', array( $this, 'review_get_comments_args' ), 10, 2 );

		// ensure points are only added for allowed contribution types
		add_filter( 'wc_points_rewards_post_add_product_review_points',    array( $this, 'review_add_product_review_points' ), 10, 2 );
		add_filter( 'wc_points_rewards_approve_add_product_review_points', array( $this, 'review_add_product_review_points' ), 10, 2 );
	}


	/**
	 * Filters the get_comments arguments when a comment is posted or approved.
	 *
	 * This ensures that only allowed contribution types are counted towards previously awarded points
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $args the get_comments array of arguments
	 * @return array
	 */
	public function review_get_comments_args( $args ) {

		/**
		 * Filters the array of contribution types which should award points.
		 *
		 * @since 1.0.6
		 *
		 * @param string[] $contribution_types the array of contribution types, default review only
		 */
		$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

		return array_merge( $args, array( 'type' => $comment_types ) );
	}


	/**
	 * Filters if points should be added for a particular comment ID on posting or approving a review.
	 *
	 * This ensures that points are only rewarded for the review contribution type but allows users to filter the types if needed.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param bool $add_points true if points should be awarded for this contribution (default), false otherwise
	 * @param int $comment_id the comment ID
	 * @return bool true if points should be awarded for this contribution, false otherwise
	 */
	public function review_add_product_review_points( $add_points, $comment_id ) {

		// bail if there is an issue with retrieving the comment object
		if ( $comment = get_comment( $comment_id ) ) {

			/**
			 * Filters the array of contribution types which should award points.
			 *
			 * @since 1.0.6
			 *
			 * @param string[] $contribution_types the array of contribution types, default review only
			 */
			$comment_types = apply_filters( 'wc_product_reviews_pro_review_points_contribution_types', array( 'review' ) );

			$add_points = in_array( $comment->comment_type, $comment_types, false );
		}

		return $add_points;
	}



}
