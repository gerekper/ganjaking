<?php
/**
 * Single Product Rating
 *
 * @author      YITH <plugins@yithemes.com>
 * @package     YITH\yit-woocommerce-advanced-reviews\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$YWAR_AdvancedReview = YITH_YWAR();// phpcs:ignore WordPress.NamingConventions

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$product_id   = yit_get_prop( $product, 'id' );
$review_count = $YWAR_AdvancedReview->get_reviews_count( $product_id );// phpcs:ignore WordPress.NamingConventions
$rating_count = $review_count;
$average      = $YWAR_AdvancedReview->get_average_rating( $product_id );// phpcs:ignore WordPress.NamingConventions

/** APPLY_FILTERS: yith_ywar_display_rating_stars_condition
*
* Filter the condition of displaying the rating template.
*
* @param bool  $rating_count > 0 Check if the rating is more than 0.
* @param float $rating_count     Default count of the rating.
*/
if ( apply_filters( 'yith_ywar_display_rating_stars_condition', $rating_count > 0, $rating_count ) ) : ?>

		<div class="woocommerce-product-rating">
			<?php /* translators: s: average */ ?>
			<div class="star-rating" title="<?php printf( esc_html__( 'Rated %s out of 5', 'yith-woocommerce-advanced-reviews' ), esc_attr( $average ) ); ?>">
			<span style="width:<?php echo( esc_html( ( $average / 5 ) * 100 ) ); ?>%">
				<?php /* translators: 1: span 2: span */ ?>
				<strong class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'yith-woocommerce-advanced-reviews' ), '<span>', '</span>' ); ?>
				<?php /* translators: s: rating_count */ ?>
				<?php printf( esc_attr( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'yith-woocommerce-advanced-reviews' ) ), '<span class="rating">' . esc_attr( $rating_count ) . '</span>' ); ?>
			</span>
			</div>

			<?php if ( comments_open() ) : ?>
				<?php
					/** APPLY_FILTERS: ywar_customer_reviews_text
					 *
					 * Filter the default text of the review template.
					 *
					 * @param string  $text         Default plugin label.
					 * @param float $review_count Count the number of reviews.
					 */
				?>
				<?php /* translators: s: review_count */ ?>
				<?php $customer_reviews_text = apply_filters( 'ywar_customer_reviews_text', _n( '%s customer review', '%s customer reviews', $review_count, 'yith-woocommerce-advanced-reviews' ), $review_count ); ?>
				<?php
					/** APPLY_FILTERS: yith_ywar_review_section_id
					 *
					 * Filter the default href to be focused on when clicking in the link.
					 */
				?>
				<a href="<?php echo apply_filters( 'yith_ywar_review_section_id', '#reviews' ); ?>" class="woocommerce-review-link" rel="nofollow">
				(<?php printf( esc_attr( $customer_reviews_text ), '<span class="count">' . esc_attr( $review_count ) . '</span>' ); ?>)</a><?php endif ?>


		</div>
<?php endif; ?>
