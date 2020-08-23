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
 * Display flag form for a single contribution
 *
 * @type \WP_Comment $comment
 *
 * @version 1.4.0
 * @since 1.0.0
 */
?>

<form method="post" class="contribution-flag-form" id="flag-contribution-<?php echo esc_attr( $comment->comment_ID ); ?>">

	<p><?php esc_html_e( 'Something wrong with this post? Thanks for letting us know. If you can point us in the right direction...', 'woocommerce-product-reviews-pro' ); ?></p>

	<p class="form-row form-row-wide">
		<label for="comment_<?php echo $comment->comment_ID; ?>_flag_reason"><?php esc_html_e( 'This post was...', 'woocommerce-product-reviews-pro' ); ?></label>
		<input type="text" class="input-text input-flag-reason" name="flag_reason" id="comment_<?php echo esc_attr( $comment->comment_ID ); ?>_flag_reason">
	</p>

	<?php
	/**
	 * Fires before contribution flag submit button.
	 *
	 * @since 1.12.3
	 *
	 * @param \WP_Comment $comment
	 */
	do_action( 'wc_product_reviews_pro_before_flag_contribution_submit', $comment );
	?>

	<p class="wc-product-reviews-pro-form-submit-row form-row-wide">
		<button type="submit" class="button alignright"><?php esc_html_e( 'Flag for removal', 'woocommerce-product-reviews-pro' ); ?></button>
		<span class="clear"></span>
	</p>

	<input type="hidden" name="comment_id" value="<?php echo esc_attr( $comment->comment_ID ); ?>">
	<input type="hidden" name="action" value="flag_contribution">

</form>
