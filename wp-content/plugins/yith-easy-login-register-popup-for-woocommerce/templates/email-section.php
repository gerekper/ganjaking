<?php
/**
 * Login Register Popup Template
 * @package YITH Easy Login Register Popup for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

?>
<script type="text/template" id="tmpl-email-section" class="_iub_cs_skip">
	<?php if ( $header ): ?>
		<div class="yith-welrp-popup-header">
			<h4><?php echo esc_html( $header ); ?></h4>
		</div>
	<?php endif; ?>
	<div class="yith-welrp-popup-content">
		<h3>{{data.title}}</h3>
		<?php if ( $message ) : ?>
			<div class="yith-welrp-popup-text"><?php echo wp_kses_post( $message ); ?></div>
		<?php endif; ?>
		<form class="yith-welrp-form" method="POST" enctype="multipart/form-data">
			<p class="yith-welrp-form-field wide">
				<label for="user_login">
					<?php esc_html_e( $login_label ); ?>
					<input type="text" name="user_login" id="user_login" value="" placeholder="<?php esc_html_e( $login_placeholder ); ?>" required>
				</label>
			</p>
			<button type="submit" class="yith-welrp-submit-button"><?php echo esc_html( $button_label ); ?></button>
		</form>

		<?php if ( ! empty( $continue_as_guest ) ) : ?>

			<div class="yith-welrp-continue-as-guest">
				<p><?php echo esc_html( $continue_as_guest_text ); ?></p>
				<a href="<?php echo wc_get_checkout_url(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
					class="button"><?php echo esc_html( $continue_as_guest_button ); ?></a>
			</div>

		<?php endif; ?>

		<?php if ( ! empty( $social ) ): ?>

			<div class="yith-welrp-social-sep">
				<span><?php echo esc_html_x( 'or', 'Social line separator text', 'yith-easy-login-register-popup-for-woocommerce' ); ?></span>
			</div>

			<div class="yith-welrp-social-container cols-<?php echo (int) count( $social ); ?>">
				<?php foreach ( $social as $social_id => $social_data ): ?>
					<div class="yith-welrp-social">
						<div id="yith-welrp-<?php echo esc_attr( $social_id ); ?>-button">
							<span class="icon"><img src="<?php echo esc_url( $social_data['icon'] ) ?>" width="20px" height="20px" alt=""/></span>
							<span class="buttonText"><?php esc_html_e( $social_data['label'] ) ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</script>

