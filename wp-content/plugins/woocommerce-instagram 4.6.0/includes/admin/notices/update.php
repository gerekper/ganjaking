<?php
/**
 * Notice - Update
 *
 * @package WC_Instagram/Admin/Notices
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_wc_instagram', 'true', wc_instagram_get_settings_url() ),
	'wc_instagram_db_update',
	'wc_instagram_db_update_nonce'
);

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><strong><?php esc_html_e( 'WooCommerce Instagram', 'woocommerce-instagram' ); ?></strong> &#8211; <?php echo esc_html_x( 'We need to update your store database to the latest version.', 'admin notice', 'woocommerce-instagram' ); ?></p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Run the updater', 'woocommerce-instagram' ); ?>
		</a>
	</p>
</div>
<script type="text/javascript">
	jQuery( '.wc-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( _x( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'admin notice', 'woocommerce-instagram' ) ); ?>' ); // jshint ignore:line
	});
</script>
