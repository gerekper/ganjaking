<?php

/**
 * Loop Rating
 *
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_woocommerce_loop;

if ( ( function_exists( 'wc_review_ratings_enabled' ) && ! wc_review_ratings_enabled() ) || ( ! function_exists( 'wc_review_ratings_enabled' ) && 'no' === get_option( 'woocommerce_enable_review_rating' ) ) ) {
	return;
}

?>

<?php if ( $rating_html = porto_get_rating_html( $product ) ) : ?>

<div class="rating-wrap">
	<div class="rating-content"><?php echo porto_filter_output( $rating_html ); ?></div>
</div>

<?php endif; ?>
