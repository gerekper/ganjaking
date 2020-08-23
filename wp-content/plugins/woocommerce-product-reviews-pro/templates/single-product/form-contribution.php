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
 * Display the contribution form for a single product
 *
 * @type string $type Contribution type
 * @type \WP_Comment $comment Comment object
 *
 * @since 1.0.0
 * @version 1.7.0
 */

global $product;

$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );

/**
 * Fires before contribution form with type $type
 *
 * @since 1.0.0
 */
do_action( "wc_product_reviews_pro_before_{$type}_form" );

if ( 'contribution_comment' === $type ) {

	/**
	 * Fires before comment contribution form for specific comment_ID
	 *
	 * @since 1.0.0
	 */
	do_action( "wc_product_reviews_pro_before_{$type}_{$comment->comment_ID}_form" );
}


?>

<?php if ( 'review' !== $type || get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

	<?php if ( 'contribution_comment' !== $type ) : ?>
	<noscript><h3 id="share-<?php echo esc_attr( $type ); ?>"><?php echo $contribution_type->get_call_to_action(); ?></h3></noscript>
	<?php endif; ?>

	<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" enctype="multipart/form-data" novalidate class="form-contribution form-<?php echo esc_attr( $type ); ?>">

		<?php if ( wc_product_reviews_pro_is_contribution_form_enabled( $type, $product ) ) : ?>

			<?php $fields = $contribution_type->get_fields(); ?>

			<?php if ( ! is_user_logged_in() && get_option( 'require_name_email' ) && ! get_option( 'comment_registration' ) ) : ?>

				<?php if ( ! isset( $fields['author'] ) ) : ?>
					<?php $fields['author'] = array( 'label' => __( 'Name', 'woocommerce-product-reviews-pro' ), 'required' => true ); ?>
				<?php endif; ?>

				<?php if ( ! isset( $fields['email'] ) ) : ?>
					<?php $fields['email'] = array( 'label' => __( 'Email', 'woocommerce-product-reviews-pro' ), 'required' => true ); ?>
				<?php endif; ?>

			<?php endif; ?>

			<?php foreach ( $fields as $key => $field ) : ?>

				<?php if ( in_array( $key, array( 'contribution_comment_comment', 'author', 'email' ), true ) ) : ?>
					<?php if ( 'contribution_comment' === $type ) : ?>
						<?php $field['id'] = isset( $field['id'] ) ? $field['id'] : $key . '-' . $comment->comment_ID; ?>
					<?php else : ?>
						<?php $field['id'] = isset( $field['id'] ) ? $field['id'] : $type . '_' . $key; ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php woocommerce_form_field( $key, $field, wc_product_reviews_pro_get_form_field_value( $key ) ); ?>

			<?php endforeach; ?>

			<?php if ( 'review' === $type ) : ?>
				<?php wc_product_reviews_pro_review_qualifiers_form_controls(); ?>
			<?php endif; ?>

			<input type="hidden" name="comment" value="<?php echo wp_create_nonce( 'contribution-content-input' ); ?>">
			<input type="hidden" name="comment_type" value="<?php echo esc_attr( $type ); ?>" />
			<input type="hidden" name="comment_post_ID" value="<?php the_ID(); ?>">

			<?php if ( 'contribution_comment' === $type ) : ?>
				<input type="hidden" name="comment_parent" value="<?php echo esc_attr( $comment->comment_ID ); ?>">
			<?php endif; ?>

			<?php if ( is_user_logged_in() && wc_product_reviews_pro_comment_notification_enabled() ) : ?>
				<input type="hidden" name="comment_author_ID" value="<?php echo esc_attr( get_current_user_id() ); ?>">
				<?php woocommerce_form_field( 'subscribe_to_replies', array( 'type' => 'checkbox', 'label' => __( 'Notify me of replies', 'woocommerce-product-reviews-pro' ) ) ); ?>
			<?php endif; ?>

			<?php wp_comment_form_unfiltered_html_nonce(); ?>

			<?php
			/**
			 * Fires before contribution form submit button.
			 *
			 * @since 1.12.3
			 */
			do_action( "wc_product_reviews_pro_before_{$type}_form_submit" );
			?>

			<p class="wc-product-reviews-pro-form-submit-row">
				<?php if ( ( 'review' === $type ) && is_user_logged_in() && ( wc_product_reviews_pro_get_user_review_count( get_current_user_id(), get_the_ID() ) > 0 ) ) : ?>
					<input type="hidden" id="comment_ID" value="">
					<button type="button" class="button update-review"><?php esc_html_e( 'Update Review', 'woocommerce-product-reviews-pro' ); ?></button>
				<?php else : ?>
					<button type="submit" class="button"><?php echo esc_html( $contribution_type->get_button_text() ); ?></button>
				<?php endif; ?>
				<span class="response"></span>
			</p>
			<?php
		else :
			esc_html_e( "Looks like you've already reviewed this product! Please contact the store if you’d like to update your review.", 'woocommerce-product-reviews-pro' );
		endif;
		?>

		<?php
		/**
		 * Fires at the end of the contribution form.
		 *
		 * @since 1.11.0
		 *
		 * @param string $type Type of contribution e.g. review, photo, video
		 */
		do_action( 'wc_product_reviews_pro_contribution_form_end', $type );
		?>

	</form>

	<?php if ( 'contribution_comment' === $type && ! is_user_logged_in() && get_option( 'comment_registration' ) ) : ?>
		<noscript>
			<style type="text/css">.form-contribution_comment { display: none; }</style>
			<p class="must-log-in"><?php printf( __( 'You must be <a href="%s">logged in</a> to join the discussion.', 'woocommerce-product-reviews-pro' ), esc_url( add_query_arg( 'redirect_to', urlencode( get_permalink( get_the_ID() ) ), wc_get_page_permalink( 'myaccount' ) . '#comment-' . $comment->comment_ID ) ) ); ?></p>
		</noscript>
	<?php endif; ?>

<?php else : ?>

	<p class="woocommerce-verification-required">
		<?php if ( ! is_user_logged_in() ) : ?>
			<?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce-product-reviews-pro' ); ?>
			<a href="#" class="js-wc-product-reviews-pro-show-login-modal"><?php esc_html_e( 'Log in now', 'woocommerce-product-reviews-pro' ); ?></a>
		<?php else : ?>
			<?php esc_html_e( 'Only customers who have purchased this product may leave a review.', 'woocommerce-product-reviews-pro' ); ?>
		<?php endif; ?>
	</p>

<?php endif;
