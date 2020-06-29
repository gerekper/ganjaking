<?php

function wc_cvo_login_url( $atts, $content = '' ) {
	if ( is_null( $content ) || empty( $content ) ) {
		$content = __( 'Login' );
	}
	$account_page_id = wc_get_page_id('myaccount');
	$url = get_permalink($account_page_id);
	return '<a href="' . $url . '">' . $content . '</a>';
}

function wc_cvo_register_url( $atts, $content = '' ) {
	if ( is_null( $content ) || empty( $content ) ) {
		$content = __( 'Register' );
	}
	
	$account_page_id = wc_get_page_id('myaccount');
	$url = get_permalink($account_page_id);
	
	return '<a href="' . $url . '">' . $content . '</a>';
}

function wc_cvo_forgot_password_link( $atts, $content = '' ) {
	if ( is_null( $content ) || empty( $content ) ) {
		$content = __( 'Forgot Your Password' );
	}

	return '<a href="' . esc_url( wp_lostpassword_url() ) . '">' . $content . '</a>';
}


//function wc_cv_logon_form

function wc_cvo_logon_form( $atts, $content = '' ) {
	global $error;

	$args = shortcode_atts( array($atts), array() );
	$args['echo'] = false;

	$html = '';
	if ( isset( $_GET['logon'] ) && $_GET['logon'] == 'failed' ) {
		$html = '<div class="logon-failed">' . __( 'Logon Failed' ) . '</div>';
	}

	$args['redirect_to'] = '';
	$html .= wcvo_login_fields( $args );
	$html .= '<script type="text/javascript">jQuery(document).ready(function($) { $("form.cart").attr("action", ""); $("#wp-submit").click(function(event) {  event.preventDefault(); $("form.cart").submit();  return false; }); });</script>';
	return $html;
}

function wcvo_login_fields(  ) {
	ob_start();
	do_action( 'woocommerce_login_form_start' ); ?>

			<p class="form-row form-row-wide">
				<label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login' ); ?>
				<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />
				<label for="rememberme" class="inline">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
				</label>
			</p>
			<p class="lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' );
	
	return ob_get_clean();
	return $form;
}

add_shortcode( 'woocommerce_logon_link', 'wc_cvo_login_url' );
add_shortcode( 'woocommerce_register_link', 'wc_cvo_register_url' );
add_shortcode( 'woocommerce_forgot_password_link', 'wc_cvo_forgot_password_link' );
add_shortcode( 'woocommerce_logon_form', 'wc_cvo_logon_form' );
