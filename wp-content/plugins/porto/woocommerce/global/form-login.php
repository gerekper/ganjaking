<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     7.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
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
				<label for="username"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>

			<p class="form-row form-row-last">
				<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="form-row clearfix">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="button wc-action-btn wc-action-sm p-t-sm p-b-sm px-5 text-uppercase<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
			</div>
			<div class="porto-checkbox d-inline-block">
				<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="porto-control-input" />
				<label for="rememberme" class="woocommerce-form__label woocommerce-form__label-for-checkbox inline woocommerce-form-login__rememberme porto-control-label">	
					<?php esc_html_e( 'Remember me', 'woocommerce' ); ?>
				</label>
			</div>
			<p class="lost_password d-inline-block ms-3">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</div>
	</div>
</form>