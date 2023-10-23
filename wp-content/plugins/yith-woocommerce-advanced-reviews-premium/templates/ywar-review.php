<?php
/**
 * Advanced Review  Template
 *
 * @author        YITH <plugins@yithemes.com>
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$review_author_data   = YITH_YWAR()->get_review_author( $review->ID );
$asset_badge_star_url = YITH_YWAR_ASSETS_URL . '/images/badge_star.svg';


$author = YITH_YWAR()->get_meta_value_author( $review->ID );

$user = isset( $author['review_user_id'] ) ? get_userdata( $author['review_user_id'] ) : null;

// If user is null we check if the review is verified owner as WooCommerce does for its comments.
if ( null === $user ) {
	$comment_id     = get_post_meta( $review->ID, YITH_YWAR_META_COMMENT_ID, true );
	$actual_comment = get_comment( $comment_id );

	if ( ! is_null( $actual_comment ) ) {

		$verified = wc_review_is_from_verified_owner( $comment_id );

		if ( $verified ) {

			$user = get_user_by( 'email', strtolower( $actual_comment->comment_author_email ) );
			$user = is_object( $user ) ? get_userdata( $user->ID ) : false;
		}
	}
}

if ( $user ) {
	$author_name = $user->display_name;
	// Add class name by author name.
	$classes = sprintf( '%s comment-author-%s', $classes, sanitize_html_class( $user->user_nicename, $author_name ) );
} elseif ( isset( $author['review_user_id'] ) ) {
	$author_name = $author['review_author'];
} else {
	$author_name = esc_html__( 'Anonymous', 'yith-woocommerce-advanced-reviews' );
}

$review->post_content = convert_smilies( $review->post_content );

?>

<li id="li-comment-<?php echo esc_attr( $review->ID ); ?>"
	class="clearfix <?php echo esc_attr( $classes ); ?>">

	<div id="comment-<?php echo esc_attr( $review->ID ); ?>" class="comment_container clearfix <?php echo esc_attr( $classes ); ?>">

		<?php
		if ( $user && ! $review_author_data['is_modified_user'] ) :
			/** APPLY_FILTERS: woocommerce_review_gravatar_size
			*
			* Filter the size of the avatar image.
			*
			* @param float $size Default plugin size (60).
			*/
			echo get_avatar( $user->ID, apply_filters( 'woocommerce_review_gravatar_size', '60' ) );
		else :
			/** APPLY_FILTERS: woocommerce_review_gravatar_size
			*
			* Filter the size of the avatar image.
			*
			* @param float $size Default plugin size (60).
			*/
			echo get_avatar( $review_author_data['display_email'], apply_filters( 'woocommerce_review_gravatar_size', '60' ) );
		endif;
		?>

		<?php if ( ! $review->post_parent && $rating && get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) : ?>

			<div class="star-rating"
				<?php /* translators: d: rating */ ?>
				title="<?php echo sprintf( esc_html__( 'Rated %d out of 5', 'yith-woocommerce-advanced-reviews' ), esc_attr( $rating ) ); ?>">
				<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong><?php echo esc_attr( $rating ); ?></strong> <?php esc_html_e( 'out of 5', 'yith-woocommerce-advanced-reviews' ); ?></span>
			</div>

		<?php endif; ?>

		<?php if ( '0' === $approved ) : ?>

			<p class="meta">
				<em><?php esc_html_e( 'Your comment is waiting for approval', 'yith-woocommerce-advanced-reviews' ); ?></em>
			</p>

		<?php else : ?>

			<p class="meta">
			<?php
				/** APPLY_FILTERS: yith_ywar_review_author_data
				 *
				 * Filter the default author data shown in the shortcode.
				 *
				 * @param string $review_author_data['display_name'] Display name of the author.
				 * @param array  $review_author_data                 Data of the author.
				 * @param obj    $user                               Obj of the user.
				 * @param obj    $review                             Obj of the review.
				 */
			?>
				<strong ><?php echo esc_attr( apply_filters( 'yith_ywar_review_author_data', $review_author_data['display_name'], $review_author_data, $user, $review ) ); ?></strong>
				<?php

				if ( $user && get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' ) {
					/** APPLY_FILTERS: ywar_wc_customer_bought_product_calls
					*
					* Filter the condition to show the verified owner string.
					*
					* @param bool $function Check if the user has bought the product or not.
					*/
					if ( apply_filters( 'ywar_wc_customer_bought_product_calls', wc_customer_bought_product( $user->user_email, $user->ID, $product_id ) ) ) {
						echo '<em class="verified">(' . esc_html__( 'verified owner', 'yith-woocommerce-advanced-reviews' ) . ')</em> ';
					}
				}

				?>
				<time datetime="<?php echo esc_attr( mysql2date( 'c', $review->post_date ) ); ?>"><?php echo esc_attr( $review_date ); ?></time>
			</p>

		<?php endif; ?>


		<?php if ( $featured ) : ?>
		<div class="ywar-featured-badge-container">
			<img class="featured-badge" src="<?php echo esc_attr( $asset_badge_star_url ); ?>">
			<span class="ywar-featured-badge-text"><?php echo esc_html__( 'FEATURED', 'yith-woocommerce-advanced-reviews' ); ?></span>
		</div>

		<?php endif; ?>

		<div class="comment-text clearfix <?php echo esc_attr( $classes ); ?>">

			<?php
			/** DO_ACTION: ywar_woocommerce_review_before_comment_text
			 *
			 * Adds an action in the review template before comment text.
			 *
			 * @param obj $review Obj of the review.
			 */
			do_action( 'ywar_woocommerce_review_before_comment_text', $review );
			?>

			<div class="description ywar-description">
				<?php
				/** APPLY_FILTERS: yith_advanced_reviews_review_content
				 *
				 * Filter the review obj shown in the shortcode.
				 *
				 * @param obj $review Obj of the review.
				 */
				?>
				<p><?php echo apply_filters( 'yith_advanced_reviews_review_content', $review ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</div>

			<?php
			/** DO_ACTION: ywar_woocommerce_review_after_comment_text
			 *
			 * Adds an action in the review template after comment text.
			 *
			 * @param obj $review Obj of the review.
			 */
			do_action( 'ywar_woocommerce_review_after_comment_text', $review );
			?>
		</div>
	</div>
</li>
