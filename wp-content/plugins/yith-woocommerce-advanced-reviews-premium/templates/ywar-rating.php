<?php
/**
 * Single Product Rating
 *
 * @author      YITH
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;
$YWAR_AdvancedReview = YITH_YWAR();

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
    return;
}

$product_id = yit_get_prop($product, 'id');
$review_count = $YWAR_AdvancedReview->get_reviews_count( $product_id );
$rating_count = $review_count;
$average      = $YWAR_AdvancedReview->get_average_rating( $product_id );

if ( apply_filters( 'yith_ywar_display_rating_stars_condition', $rating_count > 0, $rating_count ) ) : ?>

        <div class="woocommerce-product-rating">
            <div class="star-rating" title="<?php printf( esc_html__( 'Rated %s out of 5', 'yith-woocommerce-advanced-reviews' ), $average ); ?>">
			<span style="width:<?php echo( ( $average / 5 ) * 100 ); ?>%">
				<strong class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %s5%s', 'yith-woocommerce-advanced-reviews' ), '<span>', '</span>' ); ?>
                <?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'yith-woocommerce-advanced-reviews' ), '<span class="rating">' . $rating_count . '</span>' ); ?>
			</span>
            </div>

            <?php if ( comments_open() ) : ?>
                <?php $customer_reviews_text = apply_filters( 'ywar_customer_reviews_text', _n( '%s customer review', '%s customer reviews', $review_count, 'yith-woocommerce-advanced-reviews' ), $review_count ); ?>
                <a href="#reviews" class="woocommerce-review-link" rel="nofollow">
                (<?php printf( $customer_reviews_text, '<span class="count">' . $review_count . '</span>' ); ?>
                )</a><?php endif ?>


        </div>
<?php endif; ?>