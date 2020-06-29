<?php
/**
 * Newsletter custom template
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YPOP_INIT' ) && ! defined( 'WYSIJA' ) ) {
	exit;
} // Exit if accessed directly

$type_label        = YITH_Popup()->get_meta( $theme . '_label_position', $popup_id );
$email_label       = YITH_Popup()->get_meta( '_mailpoet-email-label', $popup_id );
$submit_label      = YITH_Popup()->get_meta( '_mailpoet-submit-label', $popup_id );
$placeholder_email = ( $type_label == 'placeholder' ) ? 'placeholder="' . esc_attr( $email_label ) . '"' : '';


$icon         = YITH_Popup()->get_meta( '_submit_button_icon', $popup_id );
$current_icon = ypop_get_html_icon( $icon );


?>
<div class="ypop-form-newsletter-wrapper">
<div class="message-box"></div>
<form method="post" action="#">
	<fieldset>
		<ul class="group">
			<li>
				<?php
				if ( $type_label == 'label' ) {
					echo '<label for="yit_mailpoet_newsletter_form_email">' . wp_kses_post( $email_label ) . '</label>'; }
				?>
				<div class="newsletter_form_email">
					<input type="text" <?php echo $placeholder_email; //phpcs:ignore ?> name="ypop_mailpoet_newsletter_form_email" id="ypop_mailpoet_newsletter_form_email" class="email-field text-field autoclear" />
				</div>
			</li>
			<li class="ypop-submit">
				<input type="hidden" name="ypop_mailpoet_newsletter_form_id" value="<?php echo esc_attr( $popup_id ); ?>"/>
				<input type="hidden" name="action" value="ypop_subscribe_mailpoet_user"/>

				<?php wp_nonce_field( 'ypop_mailpoet_newsletter_form_nonce', 'ypop_mailpoet_newsletter_form_nonce' ); ?>

				<button type="submit" class="btn submit-field mailpoet-subscription-ajax-submit"><?php echo wp_kses_post( $current_icon ) . ' ' . wp_kses_post( $submit_label ); ?></button>
			</li>
		</ul>
	</fieldset>
</form>
</div>
<?php
yit_enqueue_script( 'yit-mailpoet-ajax-send-form', YITH_YPOP_ASSETS_URL . '/js/mailpoet-ajax-subscribe.js', array( 'jquery' ), '', true );
wp_localize_script(
	'yit-mailpoet-ajax-send-form',
	'mailpoet_localization',
	array(
		'url'           => admin_url( 'admin-ajax.php' ),
		'error_message' => 'Ops! Something went wrong',
	)
);
