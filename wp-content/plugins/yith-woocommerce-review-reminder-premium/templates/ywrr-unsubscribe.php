<?php
/**
 * Unsubscribe template file
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Unsubscribe page shortcode template
 *
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH
 */

wc_print_notices();

$getted        = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
$decoded_email = urldecode( base64_decode( $getted['email'] ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
$decoded_id    = urldecode( base64_decode( $getted['id'] ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

?>
<div>
	<p class="form-row form-row-wide">
		<?php
		/* translators: %s email */
		printf( esc_html__( 'If you don\'t want to receive any more review reminders, please retype your email address: %s', 'yith-woocommerce-review-reminder' ), '<b>' . esc_attr( $decoded_email ) . '</b>' )
		?>
	</p>
	<p class="form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'yith-woocommerce-review-reminder' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email" />
	</p>
	<p class="form-row form-row-wide">
		<button type="button" class="button ywrr-unsubscribe"><?php esc_html_e( 'Unsubscribe', 'yith-woocommerce-review-reminder' ); ?></button>
		<input type="hidden" name="account_id" id="account_id" value="<?php echo esc_attr( $decoded_id ); ?>" />
		<input type="hidden" name="email_hash" id="email_hash" value="<?php echo esc_attr( $getted['email'] ); ?>" />
		<input type="hidden" name="email_type" id="email_type" value="ywrr_unsubscribe" />
	</p>
</div>
<p style="display: none;" class="return-to-shop form-row form-row-wide"><a class="button wc-backward" href="<?php echo esc_url( get_home_url() ); ?>"><?php esc_html_e( 'Back To Home Page', 'yith-woocommerce-review-reminder' ); ?></a></p>
