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

if ( ! $approved ) {
	return;
}

$review_author_data = YITH_YWAR()->get_review_author( $review->ID );

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

$product   = wc_get_product( $product_id );
$asset_url = YITH_YWAR_ASSETS_URL . '/images/featured-review.png';
if ( $product ) {
	?>

	<li style="list-style: none; margin-bottom: 10px; border: 1px solid #e4e1e3; border-radius: 4px; " itemprop="review"
		itemscope itemtype="http://schema.org/Review" id="li-comment-<?php echo esc_attr( $review->ID ); ?>"
		class="clearfix <?php echo esc_attr( $classes ); ?>">

		<div style="padding: 10px;" id="comment-<?php echo esc_attr( $review->ID ); ?>"
			class="woocommerce comment_container clearfix <?php echo esc_attr( $classes ); ?>">
			<?php if ( $featured ) : ?>
				<img class="featured-badge" src="<?php echo esc_attr( $asset_url ); ?>">
			<?php endif; ?>

			<div class="ywar-author-avatar" style="float: right;">
				<?php
				if ( $user && ! $review_author_data['is_modified_user'] ) :
					/** APPLY_FILTERS: woocommerce_review_gravatar_size
					*
					* Filter the size of the avatar image.
					*
					* @param float $size Default plugin size (40).
					*/
					echo get_avatar( $user->ID, apply_filters( 'woocommerce_review_gravatar_size', '40' ) );
				else :
					/** APPLY_FILTERS: woocommerce_review_gravatar_size
					*
					* Filter the size of the avatar image.
					*
					* @param float $size Default plugin size (40).
					*/
					echo get_avatar( $review_author_data['display_email'], apply_filters( 'woocommerce_review_gravatar_size', '40' ) );
				endif;
				?>
			</div>

			<div style="padding: 10px;" class="comment-text clearfix <?php echo esc_attr( $classes ); ?>">

				<?php if ( ! $review->post_parent && $rating && 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) : ?>

					<span><a
							href="<?php echo esc_attr( $product->get_permalink() ); ?>"><?php echo esc_attr( $product->get_name() ); ?></a></span>

					<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating"
						<?php /* translators: d: $rating */ ?>
						title="<?php echo sprintf( esc_html__( 'Rated %d out of 5', 'yith-woocommerce-advanced-reviews' ), esc_attr( $rating ) ); ?>">
						<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong itemprop="ratingValue">
							<?php echo '&nbsp;' . esc_attr( $rating ); ?></strong> <?php esc_html_e( 'out of 5', 'yith-woocommerce-advanced-reviews' ); ?>
						</span>
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
						<strong
							itemprop="author"><?php echo wp_kses( apply_filters( 'yith_ywar_review_author_data', $review_author_data['display_name'], $review_author_data, $user, $review ), 'post' ); ?></strong>
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
						<time itemprop="datePublished"
							datetime="<?php echo esc_attr( mysql2date( 'c', $review->post_date ) ); ?>"><?php echo esc_attr( $review_date ); ?></time>
					</p>

				<?php endif; ?>

				<?php
				/** DO_ACTION: ywar_woocommerce_review_before_comment_text
				 *
				 * Adds an action in the review shortcode template before comment text.
				 *
				 * @param obj $review Obj of the review.
				 */
				do_action( 'ywar_woocommerce_review_before_comment_text', $review );
				?>

				<div itemprop="description" class="description ywar-description">
					<?php
					/** APPLY_FILTERS: yith_advanced_reviews_review_content
					 *
					 * Filter the review obj shown in the shortcode.
					 *
					 * @param obj $review Obj of the review.
					 */
					?>
					<p><?php echo wp_kses( apply_filters( 'yith_advanced_reviews_review_content', $review ), 'post' ); ?></p>
				</div>

				<?php
				/** DO_ACTION: ywar_woocommerce_review_after_comment_text
				 *
				 * Adds an action in the review shortcode template after comment text.
				 *
				 * @param obj $review Obj of the review.
				 */
				do_action( 'ywar_woocommerce_review_after_comment_text', $review );
				?>

				<?php

				$thumbnail_div = '';

				$review_thumbnails = get_post_meta( $review->ID, YITH_YWAR_META_THUMB_IDS, true );

				if ( isset( $review_thumbnails ) && is_array( $review_thumbnails ) && ( count( $review_thumbnails ) > 0 ) ) {

					$thumbnail_div = '<div class="ywar-review-thumbnails review_thumbnail horizontalRule">';

					foreach ( $review_thumbnails as $thumb_id ) {

						if ( is_int( $thumb_id ) ) {

							$file_url    = apply_filters( 'yith_ywar_uploaded_file_url', wp_get_attachment_url( $thumb_id ), $thumb_id );
							$image_thumb = apply_filters( 'yith_ywar_uploaded_image_thumb', wp_get_attachment_image_src( $thumb_id, array( 100, 100 ), true ), $thumb_id );

							$thumbnail_div .= "<a href='$file_url' data-rel=\"prettyPhoto[review-gallery-{$review->ID}]\"><img class=\"ywar_thumbnail\" src='{$image_thumb[0]}' width='70px' height='70px'></a>";
						} else {
							$thumbnail_div = '<a></a>';
						}
					}
					$thumbnail_div .= '</div>';

				}

				echo wp_kses( $thumbnail_div, 'post' );


				?>

			</div>
		</div>
	</li>
	<?php
}
?>
