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
 * Renders guest form to watch a review for replies
 *
 * @since 1.8.0
 */
?>
<div class="u-columns" id="guest_login">

	<div class="u-column1 col-1">

		<h2><?php esc_html_e( 'Continue as a Guest', 'woocommerce-product-reviews-pro' ); ?></h2>

		<form class="woocomerce-form woocommerce-form-guest guest" method="post">

			<?php
            /**
             * Fires before guest form starts
             *
             * @since 1.8.0
             */
            do_action( 'wc_product_reviews_pro_guest_form_start' );
            ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="guest_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="guest_email" id="guest_email" value="<?php if ( ! empty( $_POST['guest_email'] ) ) echo esc_attr( $_POST['guest_email'] ); ?>" />
			</p>

			<?php
            /**
             * Fires before guest form action button
             *
             * @since 1.8.0
             */
            do_action( 'wc_product_reviews_pro_guest_form' );
            ?>

			<p class="form-row">
				<?php wp_nonce_field( 'wc-product-review-pro-guest', 'wc-product-review-pro-guest-nonce' ); ?>
				<input type="button" class="woocommerce-Button button guest-watch" name="guest" value="<?php esc_attr_e( 'Continue', 'woocommerce-product-reviews-pro' ); ?>" data-comment-id="" />
			</p>

			<?php
            /**
             * Fires after guest form ends
             *
             * @since 1.8.0
             */
            do_action( 'wc_product_reviews_pro_guest_form_end' );
            ?>

			<input type="hidden" id="guest-comment-id" value="" />

		</form>

	</div>

</div>
