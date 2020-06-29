<?php
/**
 * Lost password Popup Template
 * @package YITH Easy Login Register Popup for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-lost-password-section">
	<?php if ( $header ): ?>
		<div class="yith-welrp-popup-header">
			<h4><?php echo esc_html( $header ); ?></h4>
		</div>
	<?php endif; ?>
	<div class="yith-welrp-popup-content">
		<h3>{{{data.title}}}</h3>

		<# if( data.message ) { #>
		<div class="yith-welrp-popup-text">{{{data.message}}}</div>
		<# } #>
		<# if( data.action != 'lost-password-confirm' ) { #>
		<form class="yith-welrp-form lost_reset_password" method="POST" enctype="multipart/form-data">
			<# if( data.action == 'lost-password' ) { #>
			<p class="yith-welrp-form-field wide">
				<label for="user_login">
					<?php esc_html_e( $login_label ); ?>
					<input type="text" name="user_login" id="user_login" value="<?php echo $prefill_input ? '{{data.user_login}}' : ''; ?>" placeholder="<?php esc_html_e( $login_placeholder ); ?>" required>
				</label>
			</p>
			<# } else if( data.action == 'authenticate-lost-password' ) { #>
			<p class="yith-welrp-form-field wide">
				<label for="authentication_code">
					<?php esc_html_e( $code_input_label ); ?>
					<input type="text" name="authentication_code" id="authentication_code" value="" placeholder="<?php esc_html_e( $code_input_placeholder ); ?>" required>
				</label>
			</p>
			<input type="hidden" name="user_login" value="{{data.user_login}}">
			<# } else if( data.action == 'set-new-password' ) { #>
			<p class="yith-welrp-form-field wide">
				<label for="password_1">
					<?php esc_html_e( $password_label ); ?>
					<span class="yith-welrp-password-container">
                        <input type="password" name="new_password" id="password_1" value="" placeholder="<?php esc_html_e( $password_placeholder ); ?>" required>
                        <span class="yith-welrp-password-eye"></span>
                    </span>
				</label>
			</p>
			<?php if ( $repeat_password ) : ?>
				<p class="yith-welrp-form-field wide">
					<label for="password_2">
						<?php esc_html_e( $repeat_password_label ); ?>
						<span class="yith-welrp-password-container">
                        <input type="password" name="new_password_2" id="password_2" value="" placeholder="<?php esc_html_e( $repeat_password_placeholder ); ?>" required>
                        <span class="yith-welrp-password-eye"></span>
                    </span>
					</label>
				</p>
			<?php endif; ?>
			<input type="hidden" name="user_login" value="{{data.user_login}}">
			<# } #>
			<button type="submit" class="yith-welrp-submit-button">{{data.button_label}}</button>
			<input type="hidden" name="action" value="{{data.action}}">
		</form>
		<# } #>
		<div class="yith-welrp-footer-link-container">
			<# if( data.action == 'lost-password' ) { #>
			<a href="#" class="yith-welrp-go-back"><?php echo esc_html_x( '< Go back', 'Go back link text', 'yith-easy-login-register-popup-for-woocommerce' ); ?></a>
			<?php if ( $send_auth ): ?>
			<# } else if( data.action == 'authenticate-lost-password' ) { #>
				<a href="#" class="yith-welrp-send-auth" data-user-login="{{data.user_login}}"><?php echo esc_html( $send_auth_label ); ?></a>
			<?php endif; ?>
			<# } else if( data.action == 'lost-password-confirm' ) { #>
				<a href="#" class="yith-welrp-send-email" data-user-login="{{data.user_login}}"><?php echo esc_html( $send_email_label ); ?></a>
			<# } #>
		</div>
	</div>
</script>

