<?php
/**
 * Notice - Google Product Categories
 *
 * @package WC_Instagram/Admin/Notices
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<?php echo wp_kses_post( wc_instagram_get_notice_dismiss_link( 'wc_instagram_google_product_categories' ) ); ?>
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Instagram', 'woocommerce-instagram' ),
			esc_html_x( 'Now you can set the Google product category for each catalog or per product.', 'admin notice', 'woocommerce-instagram' )
		);
		?>
	</p>
</div>
