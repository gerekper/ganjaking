<?php
/**
 * Notice - Instagram Shopping
 *
 * @package WC_Instagram/Admin/Notices
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

$settings_url = wc_instagram_get_settings_url( wc_instagram_is_connected() ? array( 'catalog_id' => 'new' ) : array() );

?>
<div id="message" class="updated woocommerce-message">
	<?php echo wp_kses_post( wc_instagram_get_notice_dismiss_link( 'wc_instagram_shopping' ) ); ?>
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Instagram', 'woocommerce-instagram' ),
			esc_html_x( 'Now you can use Instagram Shopping by uploading your product catalogs to Facebook.', 'admin notice', 'woocommerce-instagram' )
		);
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( wc_instagram_get_notice_dismiss_url( 'wc_instagram_shopping', $settings_url ) ); ?>" class="button-primary">
			<?php esc_html_e( 'Add catalog', 'woocommerce-instagram' ); ?>
		</a>

		<?php
		printf(
			'<a class="button-secondary docs" href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/woocommerce-instagram/' ),
			esc_attr_x( 'View WooCommerce Instagram documentation', 'aria-label: documentation link', 'woocommerce-instagram' ),
			esc_html__( 'Read more', 'woocommerce-instagram' )
		);
		?>
	</p>
</div>
