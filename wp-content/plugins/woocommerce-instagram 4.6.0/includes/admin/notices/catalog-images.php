<?php
/**
 * Notice - Catalog Images
 *
 * @package WC_Instagram/Admin/Notices
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<?php echo wp_kses_post( wc_instagram_get_notice_dismiss_link( 'wc_instagram_catalog_images' ) ); ?>
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Instagram', 'woocommerce-instagram' ),
			esc_html_x( 'Now you can choose to include all the product images or just the featured image in the catalogs.', 'admin notice', 'woocommerce-instagram' )
		);
		?>
	</p>
</div>
