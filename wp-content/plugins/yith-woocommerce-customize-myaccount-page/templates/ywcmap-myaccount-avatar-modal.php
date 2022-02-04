<?php
/**
 * MY ACCOUNT TEMPLATE AVATAR MODAL
 *
 * @since 3.0.0
 * @package YITH WooCommerce Customize My Account Page
 * @var boolean $has_custom_avatar
 */

defined( 'YITH_WCMAP' ) || exit;

?>
<div id="yith-wcmap-avatar">
	<div class="avatar-modal-overlay"></div>
	<div class="avatar-modal-wrapper">
		<div class="avatar-modal-wrapper-region">
			<div class="avatar-modal">
				<div class="avatar-modal-close"><i class="fa fa-times"></i></div>
			</div>
		</div>
	</div>
</div>
<script type="text/template" id="tmpl-ywcmap-avatar-modal-content">
	<div class="avatar-modal-content">
		<h3><?php echo esc_attr_x( 'Your avatar', 'Avatar modal title', 'yith-woocommerce-customize-myaccount-page' ); ?></h3>

		<?php do_action( 'yith_wcmap_before_avatar_upload_form' ); ?>

		<div class="avatar-select cols-wrapper">
			<div class="current-avatar single-col">
				<?php echo get_avatar( get_current_user_id(), 150 ); ?>
			</div>
			<div class="new-avatar single-col">
				<form enctype="multipart/form-data" method="post">
					<label for="ywcmap_custom_avatar">
						<img src="<?php echo esc_url( YITH_WCMAP_ASSETS_URL . '/images/camera.svg' ); ?>" width="50" height="50"><br>
						<span><?php echo esc_html_x( 'Upload avatar', 'Avatar form label', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
					</label>
					<input type="file" name="ywcmap_custom_avatar" id="ywcmap_custom_avatar" accept="image/*">
				</form>
			</div>
		</div>
		<div class="avatar-actions cols-wrapper">
			<div class="reset-avatar single-col">
				<?php if ( $has_custom_avatar ) : ?>
					<a href="#" class="reset">
						<i class="fa fa-refresh"></i>
						<span><?php echo esc_html_x( 'Reset default', 'Reset avatar to the default one', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
					</a>
				<?php endif; ?>
				<a href="#" class="cancel">
					<?php echo esc_html_x( 'Cancel', 'Cancel custom avatar upload', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</a>
			</div>
			<div class="set-avatar single-col">
				<button class="button">
					<?php echo esc_html_x( 'Use it!', 'Confirm custom avatar upload', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</button>
			</div>
		</div>

		<?php do_action( 'yith_wcmap_after_avatar_upload_form' ); ?>
	</div>
</script>
