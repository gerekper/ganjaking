<?php
/**
 * Notice - Updating
 *
 * @package WC_OD/Admin/Notices
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

$force_update_url = wp_nonce_url(
	add_query_arg( 'force_update_wc_od', 'true', wc_od_get_settings_url() ),
	'wc_od_force_db_update',
	'wc_od_force_db_update_nonce'
);

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'WooCommerce Order Delivery', 'woocommerce-order-delivery' ); ?></strong> &#8211; <?php echo esc_html_x( 'Your database is being updated in the background.', 'admin notice', 'woocommerce-order-delivery' ); ?>
		<a href="<?php echo esc_url( $force_update_url ); ?>">
			<?php esc_html_e( 'Taking a while? Click here to run it now.', 'woocommerce-order-delivery' ); ?>
		</a>
	</p>
</div>
