<?php
/**
 * Login form
 *
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	return;
}
?>

<form method="post" class="login global-login"<?php echo ! $hidden ? '' : ' style="display:none;"'; ?>>
	<div class="featured-box align-left">
		<div class="box-content">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<?php
			if ( $message ) {
				echo '<p>' . strip_tags( $message ) . '</p>'; // @codingStandardsIgnoreLine
			}
			?>

			<p class="form-row form-row-first">
				<label for="username"><?php esc_html_e( 'Username or email', 'porto' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>

			<p class="form-row form-row-last">
				<label for="password"><?php esc_html_e( 'Password', 'porto' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row clearfix">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="button" name="login" value="<?php esc_attr_e( 'Login', 'porto' ); ?>"><?php esc_html_e( 'Login', 'porto' ); ?></button>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
				<label for="rememberme" class="woocommerce-form__label woocommerce-form__label-for-checkbox inline woocommerce-form-login__rememberme">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e( 'Remember me', 'porto' ); ?>
				</label>
			</p>
			<p class="lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'porto' ); ?></a>
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</div>
	</div>
</form>
