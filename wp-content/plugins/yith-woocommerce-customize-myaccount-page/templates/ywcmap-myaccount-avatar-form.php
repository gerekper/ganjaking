<?php
/**
 * MY ACCOUNT TEMPLATE AVATAR FORM
 *
 * @since 2.2.0
 * @package YITH WooCommerce Customize My Account Page
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<div id="yith-wcmap-avatar-form" data-width="400" data-height="280">
	<h3><?php esc_html_e( 'Upload your avatar', 'yith-woocommerce-customize-myaccount-page' ); ?></h3>
	<i class="fa fa-close close-form"></i>
	<form enctype="multipart/form-data" method="post">
		<p>
			<input type="file" name="ywcmap_user_avatar" id="ywcmap_user_avatar" accept="image/*">
		</p>
		<p class="submit">
			<input type="submit" class="button"
				value="<?php esc_html_e( 'Upload', 'yith-woocommerce-customize-myaccount-page' ); ?>">
		</p>
		<input type="hidden" name="action" value="wp_handle_upload">
		<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'wp_handle_upload' ); ?>">
	</form>
	<form enctype="multipart/form-data" method="post">
		<p class="submit" style="margin-top: 15px;">
			<input type="submit" class="button"
				value="<?php esc_html_e( 'Reset to default', 'yith-woocommerce-customize-myaccount-page' ); ?>">
		</p>
		<input type="hidden" name="action" value="ywcmap_reset_avatar">
	</form>
</div>
