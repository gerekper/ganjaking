<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package WooCommerce Mix and Match Products\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wc-mnm-message" class="wc-mnm-message notice info updated">
	<a class="wc-mnm-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-mnm-hide-notice', 'update' ), 'wc_mnm_hide_notices', '_wc_mnm_notice_nonce' ) ); ?>>"><?php esc_html_e( 'Dismiss', 'woocommerce-mix-and-match-products' ); ?></a>

	<h2><?php esc_html_e( 'WooCommerce Mix and Match Products database update complete', 'woocommerce-mix-and-match-products' ); ?></h2>
	<p><?php esc_html_e( 'Thank you for updating to the latest version!', 'woocommerce-mix-and-match-products' ); ?></p>

</div>
