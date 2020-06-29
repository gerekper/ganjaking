<?php
/**
 * Lost password confirmation text.
 *
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( esc_html__( 'Password reset email has been sent.', 'woocommerce' ) );
?>

<div class="featured-box align-left">
	<div class="box-content">
		<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

		<p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'woocommerce' ) ) ); ?></p>

		<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
	</div>
</div>
