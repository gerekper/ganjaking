<?php
/**
 * Wishlist ask an estimate form
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist             \YITH_WCWL_Wishlist wishlist
 * @var $ask_an_estimate_text string Ask an estimate label
 * @var $ask_estimate_url     string Ask an estimate destination url
 * @var $additional_info      bool Whether to show Additional info textarea in Ask an estimate form
 * @var $ask_an_estimate_icon string Icon to use for Ask an Estimate button icon
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="ask_an_estimate_popup">
	<form action="<?php echo esc_url( $ask_estimate_url ) ?>" method="post" class="wishlist-ask-an-estimate-popup">
		<div class="yith-wcwl-popup-content">
			<?php if ( apply_filters( 'yith_wcwl_show_popup_heading_icon_instead_of_title', true, 'fa-envelope-open-o' ) ): ?>
				<i class="fa <?php echo esc_attr( apply_filters( 'yith_wcwl_ask_and_estimate_popup_heading_icon_class', 'fa-envelope-open-o' ) ); ?> heading-icon"></i>
			<?php else: ?>
				<h3><?php esc_html_e( 'Ask for an estimate for this list', 'yith-woocommerce-wishlist' ); ?></h3>
			<?php endif; ?>

			<p class="popup-description">
				<?php esc_html_e( 'Ask for an estimate for this list:', 'yith-woocommerce-wishlist' ); ?>
			</p>

			<?php if ( ! is_user_logged_in() ): ?>
				<p class="form-row form-row-wide">
					<label for="reply_email"><?php echo esc_html( apply_filters( 'yith_wcwl_ask_estimate_reply_mail_label', __( 'Your email', 'yith-woocommerce-wishlist' ) ) ); ?></label>
					<input type="email" value="" name="reply_email" id="reply_email">
				</p>
			<?php endif; ?>

			<?php
			if ( ! empty( $ask_an_estimate_fields ) ) {
				foreach ( $ask_an_estimate_fields as $field_id => $field ) {
					woocommerce_form_field( $field_id, $field );
				}
			}
			?>

			<p class="form-row form-row-wide">
				<?php if ( ! empty( $additional_info_label ) ): ?>
					<label for="additional_notes"><?php echo esc_html( $additional_info_label ); ?></label>
				<?php endif; ?>
				<textarea id="additional_notes" name="additional_notes"></textarea>
			</p>
		</div>

		<div class="yith-wcwl-popup-footer">
			<input type="hidden" name="ask_an_estimate" value="<?php echo esc_attr( $wishlist->get_token() ); ?>"/>
			<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup alt" id="ask_an_estimate">
				<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', $ask_an_estimate_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( apply_filters( 'yith_wcwl_ask_an_estimate_text', $ask_an_estimate_text ) ); ?>
			</button>
		</div>
	</form>
</div>
