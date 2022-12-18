<?php
/**
 * Single Product Rating
 *
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( ( function_exists( 'wc_review_ratings_enabled' ) && ! wc_review_ratings_enabled() ) || ( ! function_exists( 'wc_review_ratings_enabled' ) && 'no' === get_option( 'woocommerce_enable_review_rating' ) ) ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

?>

<div class="woocommerce-product-rating">
	<div class="star-rating" title="<?php echo esc_attr( $average ); ?>">
		<span style="width:<?php echo ( 100 * ( $average / 5 ) ); ?>%">
			<?php /* translators: %s: Rating value */ ?>
			<strong class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'porto' ), '', '' ); ?>
		</span>
	</div>
	<?php if ( comments_open() ) : ?>
		<?php //phpcs:disable ?>
		<?php if ( $rating_count > 0 ) : ?>
			<?php /* translators: %s: Review count */ ?>
			<div class="review-link"><a href="<?php echo porto_is_ajax() ? esc_url( get_the_permalink() ) : ''; ?>#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s customer review', '%s customer reviews', (int) $review_count, 'woocommerce' ), '<span class="count">' . ( (int) $review_count ) . '</span>' ); ?></a>|<a href="<?php echo porto_is_ajax() ? esc_url( get_the_permalink() ) : ''; ?>#review_form" class="woocommerce-write-review-link" rel="nofollow"><?php esc_html_e( 'Add a review', 'woocommerce' ); ?></a></div>
		<?php else : ?>
			<div class="review-link noreview">
				<a href="<?php echo porto_is_ajax() ? esc_url( get_the_permalink() ) : ''; ?>#review_form" class="woocommerce-write-review-link" rel="nofollow">( <?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?> )</a>
			</div>
		<?php endif; ?>
		<?php //phpcs:enable ?>
	<?php endif; ?>
</div>
