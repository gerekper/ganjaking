<?php
/**
 * Lost password form
 *
 * @version     3.5.2
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
?>

<div class="row">
	<div class="col-lg-6 offset-lg-3">

		<?php wc_print_notices(); ?>

		<div class="featured-box featured-box-primary align-left">
			<div class="box-content">
				<?php do_action( 'woocommerce_before_lost_password_form' ); ?>

				<form method="post" class="woocommerce-ResetPassword lost_reset_password">

					<?php if ( version_compare( $porto_woo_version, '2.6', '>=' ) ) : ?>
						<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'porto' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="user_login"><?php esc_html_e( 'Username or email', 'porto' ); ?></label>
							<input class="input-text" type="text" name="user_login" id="user_login" autocomplete="username" />
						</p>

						<div class="clear"></div>

						<?php do_action( 'woocommerce_lostpassword_form' ); ?>

						<p class="woocommerce-form-row form-row clearfix">
							<?php if ( ( 'lost_password' ) === $args['form'] ) : ?>
								<a class="pt-left back-login" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Click here to login', 'porto' ); ?></a>
							<?php endif; ?>
							<input type="hidden" name="wc_reset_password" value="true" />
							<button type="submit" class="woocommerce-Button button btn-lg pt-right" value="<?php esc_attr_e( 'Reset Password', 'porto' ); ?>"><?php esc_html_e( 'Reset Password', 'porto' ); ?></button>
						</p>

						<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

					<?php else : ?>
						<?php if ( 'lost_password' === $args['form'] ) : ?>

							<p><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'porto' ) ); ?></p>

							<p class="form-row form-row-wide"><label for="user_login"><?php esc_html_e( 'Username or email', 'porto' ); ?></label> <input class="input-text" type="text" name="user_login" id="user_login" /></p>

						<?php else : ?>

							<p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'porto' ) ); ?></p>

							<p class="form-row form-row-first">
								<label for="password_1"><?php esc_html_e( 'New password', 'porto' ); ?> <span class="required">*</span></label>
								<input type="password" class="input-text" name="password_1" id="password_1" />
							</p>

							<p class="form-row form-row-last">
								<label for="password_2"><?php esc_html_e( 'Re-enter new password', 'porto' ); ?> <span class="required">*</span></label>
								<input type="password" class="input-text" name="password_2" id="password_2" />
							</p>

							<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
							<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

						<?php endif; ?>

						<div class="clear"></div>

						<?php do_action( 'woocommerce_lostpassword_form' ); ?>

						<p class="form-row clearfix">
							<?php if ( ( 'lost_password' ) === $args['form'] ) : ?>
								<a class="pt-left back-login" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Click here to login', 'porto' ); ?></a>
							<?php endif; ?>
							<input type="hidden" name="wc_reset_password" value="true" />
							<input type="submit" class="button btn-lg pt-right" value="<?php echo 'lost_password' === $args['form'] ? __( 'Reset Password', 'porto' ) : __( 'Save', 'porto' ); ?>" />
						</p>

						<?php wp_nonce_field( $args['form'] ); ?>
					<?php endif; ?>

				</form>

				<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
			</div>
		</div>

	</div>
</div>
