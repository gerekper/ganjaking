<?php
/**
 * Login Register Popup Template
 * @package YITH Easy Login Register Popup for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-login-section">
	<?php if ( $header ): ?>
		<div class="yith-welrp-popup-header">
			<h4><?php echo esc_html( $header ); ?></h4>
		</div>
	<?php endif; ?>
	<div class="yith-welrp-popup-content">
		<h3>{{data.title}}</h3>
		<div class="yith-welrp-user-info">
			<# if ( data.avatar ) { #>
			<div class="yith-welrp-user-avatar">{{{data.avatar}}}</div>
			<# } #>
			<div class="yith-welrp-user-data">
				<span>{{data.user_login}}</span>
				<br><span>{{data.user_email}}</span>
				<br><?php esc_html_e( 'Not your account?', 'yith-easy-login-register-popup-for-woocommerce' ); ?>
				<a href="#" class="yith-welrp-go-back"><?php echo esc_html_x( 'Go back', 'Go back link text', 'yith-easy-login-register-popup-for-woocommerce' ); ?></a>
			</div>
		</div>
		<# if ( data.message ) { #>
		<div class="yith-welrp-popup-text">{{{data.message}}}</div>
		<# } #>
		<form class="yith-welrp-form" method="POST" enctype="multipart/form-data">
			<p class="yith-welrp-form-field wide">
				<label for="user_password">
					<?php esc_html_e( $password_label ); ?>
					<span class="yith-welrp-password-container">
                        <input type="password" name="user_password" id="user_password" value="" placeholder="<?php esc_html_e( $password_placeholder ); ?>" required>
                        <span class="yith-welrp-password-eye"></span>
                    </span>
				</label>
			</p>
			<p class="yith-welrp-form-field left">
				<label for="remeberme" class="checkbox-label">
					<input type="checkbox" name="remeberme" id="remeberme" value="yes" <?php echo $remember_checked ? 'checked' : ''; ?>>
					<?php esc_html_e( $remember_label ) ?>
				</label>
			</p>
			<p class="yith-welrp-form-field right">
				<a href="#" class="yith-welrp-lost-password">
					<?php echo apply_filters ( 'yith_welrp_lost_password_text', esc_html_x( 'Forgot your password?', 'Reset password section link', 'yith-easy-login-register-popup-for-woocommerce' ) ); ?>
				</a>
			</p>

			<button type="submit" class="yith-welrp-submit-button"><?php echo esc_html( $button_label ); ?></button>
			<input type="hidden" name="user_login" value="{{data.user_login}}">
			<input type="hidden" name="action" value="login">
		</form>
	</div>
</script>

