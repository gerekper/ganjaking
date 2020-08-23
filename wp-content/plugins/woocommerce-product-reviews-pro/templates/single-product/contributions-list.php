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

/**
 * Display contributions list
 *
 * @version 1.7.0
 * @since 1.0.0
 */

$filters        = wc_product_reviews_pro_get_current_comment_filters();
$current_type   = isset( $filters['comment_type'] ) ? $filters['comment_type'] : null;
$current_rating = isset( $filters['rating'] ) ? $filters['rating'] : null;

?>

<h2 id="contributions-list-title">
	<?php wc_product_reviews_pro_contributions_list_title( $current_type, wc_product_reviews_pro_get_comment_count( $comments, $current_type ), $current_rating ); ?>
</h2>


<div class="contributions-container">
	<?php if ( have_comments() ) : ?>

		<ol class="commentlist">
			<?php wp_list_comments( apply_filters( 'wc_product_reviews_pro_product_review_list_args', array(
					'callback'     => 'wc_product_reviews_pro_contributions',
					'max_depth'    => 2,
					'end-callback' => 'wc_product_reviews_pro_contribution_comment_form',
				) ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>

			<nav class="woocommerce-pagination">
				<?php paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) ); ?>
			</nav>

		<?php endif; ?>

	<?php else : ?>

		<p class="woocommerce-noreviews">
			<?php wc_product_reviews_pro_contributions_list_no_results_text( $current_type ); ?>
		</p>

	<?php endif; ?>
</div>
