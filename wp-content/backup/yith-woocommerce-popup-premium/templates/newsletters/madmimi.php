<?php
/**
 * Newsletter custom template
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

$type_label = YITH_Popup()->get_meta( $theme . '_label_position', $popup_id );


$email_label       = YITH_Popup()->get_meta( '_madmimi-email-label', $popup_id );
$submit_label      = YITH_Popup()->get_meta( '_madmimi-submit-label', $popup_id );
$placeholder_email = ( $type_label == 'placeholder' ) ? 'placeholder="' . esc_attr( $email_label ) . '"' : '';

$icon         = YITH_Popup()->get_meta( '_submit_button_icon', $popup_id );
$current_icon = ypop_get_html_icon( $icon );

$add_privacy         = YITH_Popup()->get_meta( '_madmimi-add-privacy-checkbox', $popup_id );
$privacy_label       = YITH_Popup()->get_meta( '_madmimi-privacy-label', $popup_id );
$privacy_description = YITH_Popup()->get_meta( '_madmimi-privacy-description', $popup_id );

$icon         = YITH_Popup()->get_meta( '_submit_button_icon', $popup_id );
$current_icon = ypop_get_html_icon( $icon );
?>
<div class="ypop-form-newsletter-wrapper">
<div class="message-box"></div>
<form method="post" action="#" id="ypop-madmimi">
	<fieldset>
		<ul class="group">
			<li>
				<?php
				if ( $type_label == 'label' ) {
					echo '<label for="yit_madmimi_newsletter_form_email">' . wp_kses_post( $email_label ) . '</label>'; }
				?>

				<div class="newsletter_form_email">
					<input type="text" <?php echo $placeholder_email; //phpcs:ignore ?> name="yit_madmimi_newsletter_form_email" id="yit_madmimi_newsletter_form_email" class="email-field text-field autoclear" />
				</div>

			</li>
			<?php if ( 'yes' == $add_privacy ) : ?>
				<li>
					<div class="ypop-privacy-wrapper">
						<p class="form-row"
						   id="ypop_privacy_description_row"><?php echo ypop_replace_policy_page_link_placeholders( $privacy_description ); //phpcs:ignore ?></p>
						<p class="form-row" id="ypop_privacy_row">
							<input type="checkbox" <?php echo $placeholder_email; //phpcs:ignore ?> name="ypop-privacy"
								   id="ypop-privacy" required>
							<label for="ypop-privacy"
								   class=""><?php echo ypop_replace_policy_page_link_placeholders( $privacy_label ); //phpcs:ignore ?>
								<abbr class="required" title="required">*</abbr></label>

						</p>
					</div>
				</li>
			<?php endif ?>
			<li class="ypop-submit">
				<input type="hidden" name="yit_madmimi_newsletter_form_id" value="<?php echo esc_attr( $popup_id ); ?>"/>
				<input type="hidden" name="action" value="ypop_subscribe_madmimi_user"/>
				<?php wp_nonce_field( 'yit_madmimi_newsletter_form_nonce', 'yit_madmimi_newsletter_form_nonce' ); ?>
				<button type="submit" class="btn submit-field madmimi-subscription-ajax-submit"><?php echo wp_kses_post( $current_icon ) . ' ' . wp_kses_post( $submit_label ); ?></button>
			</li>
		</ul>
	</fieldset>
</form>
</div>
<?php
yit_enqueue_script( 'yit-madmimi-ajax-send-form', YITH_YPOP_ASSETS_URL . '/js/madmimi-ajax-subscribe.js', array( 'jquery' ), '', true );
wp_localize_script(
	'yit-madmimi-ajax-send-form',
	'madmimi_localization',
	array(
		'url'           => admin_url( 'admin-ajax.php' ),
		'error_message' => 'Ops! Something went wrong',
	)
);
?>
