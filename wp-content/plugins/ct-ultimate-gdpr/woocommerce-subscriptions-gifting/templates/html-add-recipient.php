<?php
/**
 * Add recipient details
 *
 * @package WooCommerce Subscriptions Gifting/Templates
 * @version 2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<fieldset>
	<input type="checkbox" id="gifting_<?php echo esc_attr( $id ); ?>_option" class="woocommerce_subscription_gifting_checkbox <?php echo esc_attr( implode( ' ', $checkbox_field_args['class'] ) ); ?>" style="<?php echo esc_attr( implode( '; ', $checkbox_field_args['style_attributes'] ) ); ?>" value="gift" <?php checked( $checkbox_field_args['checked'] ); ?> <?php disabled( $checkbox_field_args['disabled'] ); ?> />
	<label for="gifting_<?php echo esc_attr( $id ); ?>_option">
		<?php echo esc_html( apply_filters( 'wcsg_enable_gifting_checkbox_label', get_option( WCSG_Admin::$option_prefix . '_gifting_checkbox_text', __( 'This is a gift', 'woocommerce-subscriptions-gifting' ) ) ) ); ?>
	</label>
	<div class="wcsg_add_recipient_fields <?php echo esc_attr( implode( ' ', $container_css_class ) ); ?>" style="<?php echo esc_attr( implode( ' ', $container_style_attributes ) ); ?>">
		<?php echo $nonce_field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="form-row form-row <?php echo esc_attr( implode( ' ', $email_field_args['class'] ) ); ?>" style="<?php echo esc_attr( implode( '; ', $email_field_args['style_attributes'] ) ); ?>">
			<label for="recipient_email[<?php echo esc_attr( $id ); ?>]">
				<?php esc_html_e( "Recipient's Email Address:", 'woocommerce-subscriptions-gifting' ); ?>
			</label>
			<input data-recipient="<?php echo esc_attr( $email ); ?>" type="email" class="input-text recipient_email" name="recipient_email[<?php echo esc_attr( $id ); ?>]" id="recipient_email[<?php echo esc_attr( $id ); ?>]" placeholder="<?php echo esc_attr( $email_field_args['placeholder'] ); ?>" value="<?php echo esc_attr( $email ); ?>"/>
		</p>
		<?php do_action( 'wcsg_add_recipient_fields' ); ?>
	</div>
</fieldset>
