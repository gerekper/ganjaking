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
<div id="wc-mnm-message" class="wc-mnm-message notice info updated">

	<h2><?php esc_html_e( 'Mix and Match Database update required', 'woocommerce-mix-and-match-products' ); ?></h2>
	<p>
		<?php
			esc_html_e( 'WooCommerce Mix and Match Products has been updated! To keep things running smoothly, we have to update your database to the newest version.', 'woocommerce-mix-and-match-products' );
		?>
	</p>

	<p>
		<?php
			esc_html_e( 'WooCommerce Mix and Match Products 2.0 is a significant update, so we recommend you take a backup of your database first. If you are using any custom snippets or mini-extensions, you should also test them against 2.0 in staging before updating in production.', 'woocommerce-mix-and-match-products' );
		?>
	</p>

	<p class="submit">
		<a id="wc-mnm-do-update-db" href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Update Database', 'woocommerce-mix-and-match-products' ); ?>
		</a>
		<a href="<?php echo esc_url( WC_Mix_and_Match()->get_resource_url( 'updating' ) ); ?>" class="button-secondary">
			<?php esc_html_e( 'Learn more about updates', 'woocommerce-mix-and-match-products' ); ?>
		</a>
	</p>
</div>
