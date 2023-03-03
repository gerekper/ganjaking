<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package WooCommerce Mix and Match Products\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$needs_cleanup = 'yes' === get_transient( 'wc_mnm_show_2x00_cleanup_legacy_child_meta' );

$dismiss_url = wp_nonce_url(
	add_query_arg( array( 'wc-mnm-hide-notice' => $needs_cleanup ? 'cleanup' : 'update' ), remove_query_arg( 'wc_mnm_update_action' ) ),
	'wc_mnm_hide_notices',
	'_wc_mnm_notice_nonce'
);

$cleanup_url = wp_nonce_url(
	add_query_arg( 'wc_mnm_update_action', 'do_2x00_cleanup_legacy_child_meta' ),
	'wc_mnm_update_action',
	'wc_mnm_update_action_nonce'
);

?>
<div id="wc-mnm-message" class="wc-mnm-message notice info updated">
	<a class="wc-mnm-message-close notice-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>>"><?php esc_html_e( 'Dismiss', 'woocommerce-mix-and-match-products' ); ?></a>

	<h2><?php esc_html_e( 'WooCommerce Mix and Match Products database update complete', 'woocommerce-mix-and-match-products' ); ?></h2>

	<?php if ( $needs_cleanup ) { ?>
	
		<p><?php esc_html_e( 'Thank you for updating to the latest version! If everything has gone smoothly, you may optionally clean up the data from previous versions.', 'woocommerce-mix-and-match-products' ); ?></p>

		<p class="submit">
			<a href="<?php echo esc_url( $cleanup_url ); ?>" class="button-primary">
				<?php esc_html_e( 'Clean up old data', 'woocommerce-mix-and-match-products' ); ?>
			</a>
		</p>

	<?php } else { ?>
		<p><?php esc_html_e( 'Thank you for updating to the latest version!', 'woocommerce-mix-and-match-products' ); ?></p>
	<?php } ?>


</div>
