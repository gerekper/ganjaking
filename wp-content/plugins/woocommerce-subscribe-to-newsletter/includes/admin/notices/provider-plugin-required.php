<?php
/**
 * Notice - Provider plugin required
 *
 * @package WC_Newsletter_Subscription/Admin/Notices
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

$provider = wc_newsletter_subscription_get_provider();

if ( ! $provider || ! method_exists( $provider, 'get_plugin_url' ) ) {
	return;
}

$message = sprintf(
	/* translators: %s plugin name */
	_x( 'The newsletter provider <strong>"%1$s"</strong> requires the WordPress plugin <strong>"%2$s"</strong> to work.', 'admin notice', 'woocommerce-subscribe-to-newsletter' ),
	esc_html( $provider->get_name() ),
	esc_html( $provider->get_plugin_name() )
);

?>
<div id="wc-newsletter-subscription-notice-provider-plugin-required" class="error woocommerce-message">
	<p>
		<strong><?php esc_html_e( 'WooCommerce Subscribe to Newsletter', 'woocommerce-subscribe-to-newsletter' ); ?></strong> &#8211; <?php echo wp_kses_post( $message ); ?>
		<a href="<?php echo esc_url( $provider->get_plugin_url() ); ?>" target="_blank">
			<?php esc_html_e( 'Visit plugin site.', 'woocommerce-subscribe-to-newsletter' ); ?>
		</a>
	</p>
</div>
