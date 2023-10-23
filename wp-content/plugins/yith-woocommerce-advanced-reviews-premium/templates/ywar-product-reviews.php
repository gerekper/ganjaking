<?php
/**
 * Display single product reviews for YITH WooCommerce Advanced Reviews
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ywar-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @author        YITH <plugins@yithemes.com>
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 * @version       1.2.0
 */

/** DO_ACTION: ywar_init_product_reviews_template
 *
 * Adds an action when calling the review template.
 */
do_action( 'ywar_init_product_reviews_template' );

global $product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;// Exit if accessed directly.
}

$product_id = yit_get_prop( $product, 'id' );
if ( ! comments_open( $product_id ) ) {
	return;
}

$YWAR_AdvancedReview = YITH_YWAR();// phpcs:ignore WordPress.NamingConventions
$reviews_count       = is_array( $YWAR_AdvancedReview->get_product_reviews_by_rating( $product_id ) ) || is_object( $YWAR_AdvancedReview->get_product_reviews_by_rating( $product_id ) ) ? count( $YWAR_AdvancedReview->get_product_reviews_by_rating( $product_id ) ) : $YWAR_AdvancedReview->get_product_reviews_by_rating( $product_id );// phpcs:ignore WordPress.NamingConventions
?>
<?php
/** DO_ACTION: yith_advanced_reviews_before_reviews
 *
 * Adds an action in the review template out of the content div.
 *
 * @param float $product_id ID of the product.
 */
do_action( 'yith_advanced_reviews_before_reviews', $product_id );
?>

<div id="reviews" class="ywar-review-content">
	<?php
	/** DO_ACTION: yith_advanced_reviews_before_reviews
	 *
	 * Adds an action in the review template just inside of the content div.
	 */
	do_action( 'yith_advanced_reviews_review_container_start' );
	?>
	<div id="comments">
		<h2>
		<?php
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $reviews_count ) {
			/* translators: 1: reviews_count 2: get_the_title() */
			printf( esc_attr( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $reviews_count, 'yith-woocommerce-advanced-reviews' ) ), esc_attr( $reviews_count ), esc_attr( get_the_title() ) );
		} else {
			esc_html_e( 'Reviews', 'yith-woocommerce-advanced-reviews' );
		}
		?>
		</h2>

		<?php if ( $reviews_count ) : ?>

			<?php
			/** DO_ACTION: yith_advanced_reviews_before_review_list
			 *
			 * Adds an action just before the review list.
			 *
			 * @param obj $product Obj of the product.
			 */
				do_action( 'yith_advanced_reviews_before_review_list', $product );
			?>

			<ol class="commentlist">

			</ol>

			<?php
			/** DO_ACTION: yith_advanced_reviews_after_review_list
			 *
			 * Adds an action just after the review list.
			 *
			 * @param obj $product Obj of the product.
			 */
			do_action( 'yith_advanced_reviews_after_review_list', $product );
			?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'yith-woocommerce-advanced-reviews' ); ?></p>

		<?php endif; ?>
	</div>

	<?php
	$can_submit = $YWAR_AdvancedReview->user_can_submit_review( $product_id );// phpcs:ignore WordPress.NamingConventions
	?>

	<div
		id="review_form_wrapper" <?php echo ! $can_submit ? 'style="display:none"' : ''; ?>>
		<div id="review_form">
			<?php
			$commenter = wp_get_current_commenter();

			$comment_form = array(
				'title_reply'          => $reviews_count ? '<span class="review_label">' . esc_html__( 'Add a review', 'yith-woocommerce-advanced-reviews' ) . '</span>' : esc_html__( 'Be the first to review', 'yith-woocommerce-advanced-reviews' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
				'title_reply_to'       => esc_html__( 'Write a reply', 'yith-woocommerce-advanced-reviews' ),
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'fields'               => array(
					/** APPLY_FILTERS: yith_ywar_name_field_label
					*
					* Filter the default name label in the product reviews template.
					*/
					'author' => '<p class="comment-form-author"><label for="author">' . esc_html( apply_filters( 'yith_ywar_name_field_label', __( 'Name', 'yith-woocommerce-advanced-reviews' ) ) ) . ' <span class="required">*</span></label> ' .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
					/** APPLY_FILTERS: yith_ywar_email_field_label
					 *
					 * Filter the label for the email field in the product reviews template.
					 *
					 * @param string $label Label.
					 *
					 * @return string.
					 */
					'email'  => '<p class="comment-form-email"><label for="email">' . esc_html( apply_filters( 'yith_ywar_email_field_label', __( 'Email', 'yith-woocommerce-advanced-reviews' ) ) ) . ' <span class="required">*</span></label> ' .
						'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
				),
				'label_submit'         => esc_html__( 'Submit', 'yith-woocommerce-advanced-reviews' ),
				'logged_in_as'         => '',
				'comment_field'        => '',
			);

			$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your Review', 'yith-woocommerce-advanced-reviews' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

			$comment_form['comment_field'] .= '<input type="hidden" name="action" value="submit-form" />';
			/** APPLY_FILTERS: woocommerce_product_review_comment_form_args
			*
			* Filter the default HTML of the form in the product review template.
			*
			* @param $comment_form Entire HTML of the form.
			*/
			comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>
	</div>

	<?php if ( ! $can_submit ) : ?>

		<p class="woocommerce-verification-required">
		<?php
			/** APPLY_FILTERS: ywar_product_reviews_submit_reviews_denied_text
			 *
			 * Filter the default message when submitting a review as non-logged users.
			 *
			 * @param string $text      Default plugin message.
			 * @param float $product_id ID of the product.
			 */
			echo wp_kses( apply_filters( 'ywar_product_reviews_submit_reviews_denied_text', esc_html__( 'Only logged in customers who have purchased this product may write a review.', 'yith-woocommerce-advanced-reviews' ), $product_id ), 'post' );
		?>
			<?php if ( $YWAR_AdvancedReview->reviews_edit_enabled() && $YWAR_AdvancedReview->customer_reviews_for_product( $product_id ) ) : // phpcs:ignore WordPress.NamingConventions ?>
				<a href="#" class="edit-my-reviews"
					data-product-id="<?php echo esc_html( $product_id ); ?>"><?php esc_html_e( 'Edit my reviews', 'yith-woocommerce-advanced-reviews' ); ?></a>
			<?php endif; ?>
		</p>

	<?php endif; ?>

	<div class="clear"></div>
</div>
