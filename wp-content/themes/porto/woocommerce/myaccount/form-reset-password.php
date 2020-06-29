<?php
/**
 * Lost password reset form.
 *
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

wc_print_notices(); ?>

<div class="featured-box featured-box-primary align-left">
	<div class="box-content">
		<?php do_action( 'woocommerce_before_reset_password_form' ); ?>
		<form method="post" class="woocommerce-ResetPassword lost_reset_password">
			<p><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'porto') ); ?></p><?php // @codingStandardsIgnoreLine ?>

			<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label for="password_1"><?php esc_html_e( 'New password', 'porto' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" autocomplete="new-password" />
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label for="password_2"><?php esc_html_e( 'Re-enter new password', 'porto' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" />
			</p>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<div class="clear"></div>

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<p class="woocommerce-form-row form-row clearfix">
				<a class="pt-left back-login" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Click here to login', 'porto' ); ?></a>
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button btn-lg pt-right" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
		</form>
		<?php do_action( 'woocommerce_after_reset_password_form' ); ?>
	</div>
</div>
