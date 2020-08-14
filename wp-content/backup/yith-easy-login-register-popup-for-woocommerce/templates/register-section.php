<?php
/**
 * Login Register Popup Template
 * @package YITH Easy Login Register Popup for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-register-section">
	<?php if ( $header ): ?>
		<div class="yith-welrp-popup-header">
			<h4><?php echo esc_html( $header ); ?></h4>
		</div>
	<?php endif; ?>
	<div class="yith-welrp-popup-content">
		<div class="yith-welrp-user-info">
			<# if ( data.avatar ) { #>
			<div class="yith-welrp-user-avatar">{{{data.avatar}}}</div>
			<# } #>
			<div class="yith-welrp-user-data">
				<span>{{data.user_login}}</span>
				<br>
				<?php esc_html_e( 'Want to use a different email address?', 'yith-easy-login-register-popup-for-woocommerce' ); ?>
				<a href="#" class="yith-welrp-go-back"><?php echo esc_html_x( 'Go back', 'Go back link text', 'yith-easy-login-register-popup-for-woocommerce' ); ?></a>
			</div>
		</div>

		<h3>{{data.title}}</h3>
		<# if ( data.message ) { #>
		<div class="yith-welrp-popup-text">{{{data.message}}}</div>
		<# } #>
		<form class="yith-welrp-form register" method="POST" enctype="multipart/form-data">
			<# if( data.email_field ) { #>
			<p class="yith-welrp-form-field wide">
				<label for="reg_password">
					<?php esc_html_e( 'Set an email for this account:', 'yith-easy-login-register-popup-for-woocommerce' ); ?>
					<input type="email" name="reg_email" id="reg_email" value="" placeholder="<?php esc_html_e( 'Enter email here', 'yith-easy-login-register-popup-for-woocommerce' ); ?>" required>
				</label>
			</p>
			<# } #>
			<p class="yith-welrp-form-field wide">
				<label for="reg_password">
					<?php esc_html_e( $password_label ) ?>
					<span class="yith-welrp-password-container">
                        <input type="password" name="reg_password" id="reg_password" value="" placeholder="<?php esc_html_e( $password_placeholder ); ?>" minlength="5" required>
                        <span class="yith-welrp-password-eye"></span>
                    </span>
				</label>
			</p>
			<?php if ( $repeat_password ) : ?>
				<p class="yith-welrp-form-field wide">
					<label for="reg_password_2">
						<?php esc_html_e( $repeat_password_label ); ?>
						<span class="yith-welrp-password-container">
                            <input type="password" name="reg_password_2" id="reg_password_2" value="" placeholder="<?php esc_html_e( $repeat_password_placeholder ); ?>" minlength="5" required>
                            <span class="yith-welrp-password-eye"></span>
                        </span>
					</label>
				</p>
			<?php endif; ?>
			<?php if ( $policy_enabled ): ?>
				<p class="yith-welrp-form-field wide">
					<label for="terms_policy" class="checkbox-label">
						<input type="checkbox" name="terms_policy" id="terms_policy" value="yes" <?php echo $policy_checked ? 'checked' : ''; ?>>
						<?php echo wp_kses_post( $policy_label ); ?>
					</label>
				</p>
			<?php endif; ?>
			<?php if ( $enabled_reCaptcha ): ?>
				<div id="g-recaptcha"></div>
			<?php endif; ?>
			<button type="submit" class="yith-welrp-submit-button">{{data.button_label}}</button>
			<input type="hidden" name="user_login" value="{{data.user_login}}">
			<input type="hidden" name="action" value="register">
		</form>
	</div>
</script>

