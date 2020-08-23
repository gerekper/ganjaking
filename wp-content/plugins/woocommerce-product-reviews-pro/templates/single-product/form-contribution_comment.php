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
 * Display comment form for contribution comments
 *
 * @type \WP_Comment $comment
 *
 * @since 1.0.0
 * @version 1.6.0
 */
?>

<div class="contribution-comment-form">

	<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" enctype="multipart/form-data" novalidate>

		<?php foreach ( wc_product_reviews_pro()->get_frontend_instance()->get_contribution_fields( 'contribution_comment' ) as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field ); ?>

		<?php endforeach; ?>

		<input type="hidden" name="comment_type" value="contribution_comment" />
		<input type="hidden" name="comment_post_ID" value="<?php the_ID(); ?>">
		<input type="hidden" name="comment_parent" value="<?php echo esc_attr( $comment->comment_ID ); ?>">
		<?php wp_comment_form_unfiltered_html_nonce(); ?>

		<?php
		/**
		 * Fires before contribution comment form submit button.
		 *
		 * @since 1.12.3
		 *
		 * @param \WP_Comment $comment
		 */
		do_action( 'wc_product_reviews_pro_before_contribution_comment', $comment );
		?>

		<p class="wc-product-reviews-pro-form-submit-row">
			<button type="submit" class="button"><?php esc_html_e( 'Save Comment', 'woocommerce-product-reviews-pro' ); ?></button>
		</p>

	</form>

</div>
