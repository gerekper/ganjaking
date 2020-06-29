<?php
/**
 * Notice - API Changes
 *
 * @package WC_Instagram/Admin/Notices
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Instagram', 'woocommerce-instagram' ),
			wp_kses_post(
				sprintf(
					/* translators: %s: settings page URL */
					_x( 'Due to important changes in the Instagram API, it\'s necessary to <a href="%s">reconnect your account</a>.', 'admin notice', 'woocommerce-instagram' ),
					esc_url( wc_instagram_get_settings_url() )
				)
			)
		);
		?>
	</p>
</div>
