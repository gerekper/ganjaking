<?php
/**
 * Referral Form
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.6
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( $enabled ) : ?>

	<div class="yith-wcaf yith-wcaf-set-referrer woocommerce">

		<?php
		$info_message = apply_filters( 'yith_wcaf_set_referrer_message', __( 'Did anyone suggest our site to you?', 'yith-woocommerce-affiliates' ) . ' <a href="#" class="show-referrer-form">' . __( 'Click here to enter his/her affiliate code', 'yith-woocommerce-affiliates' ) . '</a>' );

		if ( apply_filters( 'yith_wcaf_show_message_wc_print_notice', true ) ) {
			wc_print_notice( $info_message, 'notice' );
		} else {
			echo $info_message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>

		<form class="referrer-form" method="post" style="display:none">

			<p class="form-row form-row-first">
				<input type="text" name="referrer_code" class="input-text" placeholder="<?php esc_attr_e( 'Affiliate code', 'yith-woocommerce-affiliates' ); ?>" value="<?php echo esc_attr( $affiliate ); ?>" <?php echo ( $permanent_token && $affiliate ) ? 'readonly="readonly"' : ''; ?> />
			</p>

			<p class="form-row form-row-last">
				<input type="submit" class="button" name="set_referrer" value="<?php esc_attr_e( 'Set Affiliate', 'yith-woocommerce-affiliates' ); ?>" <?php echo ( $permanent_token && $affiliate ) ? 'disabled="disabled"' : ''; ?> />
			</p>

			<div class="clear"></div>

		</form>
	</div>

<?php endif; ?>
