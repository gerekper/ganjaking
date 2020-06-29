<?php
/**
 * Register Form 2
 *
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $porto_settings;
?>

		<h2 class="heading-primary font-weight-normal">Create an Account</h2>

		<div class="featured-box align-left">
			<div class="box-content">

				<h2><?php esc_html_e( 'PERSONAL INFORMATION', 'porto' ); ?></h2>

				<form method="post" class="register">

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e( 'Username', 'porto' ); ?> <span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
						</p>

					<?php endif; ?>

					<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
						<label for="reg_email"><?php esc_html_e( 'Email address', 'porto' ); ?> <span class="required">*</span></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php echo ! empty( $_POST['email'] ) ? esc_attr( $_POST['email'] ) : ''; ?>" />
					</p>

					<h2><?php esc_html_e( 'LOGIN INFORMATION', 'porto' ); ?></h2>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row <?php echo 'full' == $porto_settings['reg-form-info'] ? 'form-row-first' : 'form-row-wide'; ?>">
							<label for="reg_password"><?php esc_html_e( 'Password', 'porto' ); ?> <span class="required">*</span></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
						</p>
						<?php if ( isset( $porto_settings['reg-form-info'] ) && 'full' == $porto_settings['reg-form-info'] ) : ?>
							<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-last">
								<label for="reg_password2"><?php esc_html_e( 'Confirm Password', 'porto' ); ?> <span class="required">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="confirm_password" id="reg_password2" />
							</p>
						<?php endif; ?>

					<?php endif; ?>

					<!-- Spam Trap -->
					<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php esc_html_e( 'Anti-spam', 'porto' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" autocomplete="off" /></div>

					<?php do_action( 'woocommerce_register_form' ); ?>
					<?php do_action( 'register_form' ); ?>

					<div class="woocommerce-FormRow form-row clearfix">
						<p class="required pull-right"><?php esc_html_e( '* Required Fields', 'porto' ); ?></p>
					</div>

					<div class="woocommerce-FormRow form-row clearfix">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

						<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="pull-left"><i class="fas fa-angle-double-left"></i> <?php esc_html_e( 'Back', 'porto' ); ?></a>
						<input type="submit" class="woocommerce-Button button pull-right" name="register" value="<?php esc_attr_e( 'Submit', 'porto' ); ?>" />
					</div>

					<div class="clear"></div>
					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		</div>
