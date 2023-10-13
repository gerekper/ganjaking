<?php
/**
 * Admin View: Notice - Updating.
 *
 * @package WooCommerce Mix and Match Products\Admin\Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wc-mnm-message" class="wc-mnm-message notice info updated">
	<h2><?php esc_html_e( 'WooCommerce Mix and Match Products database update in progress', 'woocommerce-mix-and-match-products' ); ?></h2>

	<p><?php esc_html_e( 'WooCommerce Mix and Match Products is updating the database in the background. The database update process may take a little while, so please be patient.', 'woocommerce-mix-and-match-products' ); ?></p>
	<p class="submit">
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page'   => 'wc-status',
					'tab'    => 'action-scheduler',
					'status' => 'pending',
					's'      => 'wc_mnm_run_update',
				),
				admin_url( 'admin.php' )
			)
		);
		?>
		" class="button-secondary">
			<?php esc_html_e( 'View your progress', 'woocommerce-mix-and-match-products' ); ?>
		</a>
	</p>

</div>