<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="bookings_export" class="booking-export woocommerce_options_panel panel wc-metaboxes-wrapper show_if_booking">
	<?php
	$product_id = $bookable_product->get_ID();
	?>
	<p><?php esc_html_e( 'Click the "Export" button to export this product, it\'s settings, as well as any applicable resources, persons, and global availability rules. This will create a ZIP file that will then be downloaded to your local device to help WooCommerce support troubleshoot your bookings setup, if needed.', 'woocommerce-bookings' ); ?></p>
	<?php
	$export_link = wp_nonce_url( get_edit_post_link( $product_id ) . '&action=export_product_with_global_rules', 'export_product_with_global_rules' );
	?>
	<p><a class="button button-primary" href="<?php echo esc_url( $export_link ) ?>" style="display: inline-block; margin: 0 0 10px"><?php esc_html_e( 'Export', 'woocommerce-bookings' ); ?></a></p>
</div>
