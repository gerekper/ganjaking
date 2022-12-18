<?php
/**
 * The template to display the reviewers star rating in reviews
 *
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $comment;
$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

if ( $rating && ( ( function_exists( 'wc_review_ratings_enabled' ) && wc_review_ratings_enabled() ) || ( ! function_exists( 'wc_review_ratings_enabled' ) && 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) ) ) { ?>

	<div class="star-rating" title="<?php echo esc_attr( $rating ); ?>">
		<span style="width:<?php echo (float) $rating / 5 * 100; ?>%">
			<?php
			/* translators: %s: Rating value */
			printf( esc_html__( '%s out of 5', 'woocommerce' ), '<strong>' . $rating . '</strong>' );
			?>
		</span>
	</div>

	<?php
}
