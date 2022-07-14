<?php
/**
 * Notice - Google Product Attributes
 *
 * @package WC_Instagram/Admin/Notices
 * @since   3.7.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<?php echo wp_kses_post( wc_instagram_get_notice_dismiss_link( 'wc_instagram_google_product_attributes' ) ); ?>
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Instagram', 'woocommerce-instagram' ),
			esc_html_x( 'Now you can include additional information such as color, size, gender, etc. in your catalogs by using the product attributes.', 'admin notice', 'woocommerce-instagram' )
		);
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( wc_instagram_get_notice_dismiss_url( 'wc_instagram_google_product_attributes', admin_url( 'edit.php?post_type=product&amp;page=product_attributes' ) ) ); ?>" class="button-primary">
			<?php esc_html_e( 'Add attribute', 'woocommerce-instagram' ); ?>
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
