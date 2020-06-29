<?php
/**
 * YITH WCWTL Importer Step: Import Done columns
 *
 * @since   1.6.0
 * @package YITH WooCommerce Waiting List
 */

defined( 'YITH_WCWTL' ) || exit;
?>
<div>
	<h2><?php esc_html_e( 'Import completed!', 'yith-woocommerce-waiting-list' ); ?></h2>
	<p><?php echo sprintf( __( 'You have successfully imported users to the waiting list of product %s', 'yith-woocommerce-waiting-list' ), $product->get_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
</div>
