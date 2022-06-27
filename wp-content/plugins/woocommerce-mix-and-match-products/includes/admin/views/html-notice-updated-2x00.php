<?php
/**
 * Admin View: Notice - 2.0 Updated.
 *
 * @package WooCommerce Mix and Match Products\Admin\Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$cleanup_url = wp_nonce_url(
	add_query_arg( 'wc_mnm_update_action', 'do_2x00_cleanup_legacy_child_meta' ),
	'do_2x00_cleanup_legacy_child_meta',
	'wc_mnm_update_action_nonce'
);

?>
<div id="wc-mnm-message" class="wc-mnm-message notice info updated">

	<a class="wc-mnm-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-mnm-hide-notice', 'update' ), 'wc_mnm_hide_notices', '_wc_mnm_notice_nonce' ) ); ?>>"><?php esc_html_e( 'Dismiss', 'woocommerce-mix-and-match-products' ); ?></a>

	<h2><?php esc_html_e( 'WooCommerce Mix and Match Products 2.0 database update complete', 'woocommerce-mix-and-match-products' ); ?></h2>

    <p><?php esc_html_e( 'Thank you for updating to the latest version! If everything has gone smoothly, you may optionally clean up the data from previous versions.', 'woocommerce-mix-and-match-products' ); ?></p>

    <p class="submit">
        <a href="<?php echo esc_url( $cleanup_url ); ?>" class="button-primary">
            <?php esc_html_e( 'Clean up old data', 'woocommerce-mix-and-match-products' ); ?>
		</a>
    </p>

</div>
