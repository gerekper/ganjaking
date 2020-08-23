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
 * Display Review contributions.
 *
 * Legacy template to support WooCommerce versions older than v3.0.
 *
 * @type \WP_Comment $comment The comment.
 * @type \WC_Contribution $contribution The contribution.
 *
 * @since 1.7.0
 * @version 1.13.0
 */

$title          = $contribution->get_title();
$rating         = $contribution->get_rating();
$rating_enabled = $rating && wc_review_ratings_enabled();

?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php // Display the karma markup.
		wc_product_reviews_pro_contribution_karma( $contribution ); ?>

		<div class="comment-text">

			<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '', get_comment_author() ); ?>

			<?php if ( $title || $rating_enabled ) : ?>

				<h3 class="contribution-title review-title">

					<?php if ( $rating_enabled ) : ?>

						<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'woocommerce-product-reviews-pro' ), $rating ) ); ?>">
							<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%;">
								<?php /* translators: Placeholder: %d contribution rating */
								printf( __( '<strong itemprop="ratingValue">%d</strong> out of 5', 'woocommerce-product-reviews-pro' ), esc_attr( $rating ) ) ; ?>
							</span>
						</span>

					<?php endif; ?>

					<?php if ( $title ) : ?>

						<span itemprop="name"> <?php echo esc_html( $title ); ?></span>

					<?php endif; ?>

				</h3>

			<?php endif; ?>

			<?php // Display the meta markup.
			wc_product_reviews_pro_contribution_meta( $contribution ); ?>

			<?php wc_product_reviews_pro_review_qualifiers( $contribution ); ?>

			<div itemprop="reviewBody" class="description"><?php comment_text(); ?></div>

			<?php // Display the attachments.
			wc_product_reviews_pro_contribution_attachments( $contribution ); ?>

			<?php // Display the actions markup.
			wc_product_reviews_pro_contribution_actions( $contribution ); ?>

			<?php wc_product_reviews_pro_contribution_flag_form( $comment ); ?>

		</div>
	</div>
