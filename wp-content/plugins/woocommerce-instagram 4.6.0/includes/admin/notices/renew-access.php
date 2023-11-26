<?php
/**
 * Notice - Renew Access
 *
 * @package WC_Instagram/Admin/Notices
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><strong><?php esc_html_e( 'WooCommerce Instagram', 'woocommerce-instagram' ); ?></strong> &#8211; <?php echo esc_html_x( 'The credentials for accessing the Instagram API have expired or are no longer valid. It is necessary to renew them manually by reconnecting your account.', 'admin notice', 'woocommerce-instagram' ); ?></p>
	<p class="submit">
		<a href="<?php echo esc_url( wc_instagram_get_authorization_url( 'connect' ) ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Renew access', 'woocommerce-instagram' ); ?>
		</a>
	</p>
</div>
