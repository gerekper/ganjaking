<?php
/**
 * Admin View: Notice - Update.
 *
 * @package WooCommerce Mix and Match Products\Admin\Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'wc_mnm_update_action', 'do_update_db' ),
	'wc_mnm_update_action',
	'wc_mnm_update_action_nonce'
);

?>
<div class="notice notice-<?php echo esc_attr( $notice_type );?>">
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
