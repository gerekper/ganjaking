<?php
/**
 * Notice - Update 2.3.7
 *
 * @package WC_Extension/Admin/Notices
 * @since   2.3.7
 */

defined( 'ABSPATH' ) || exit;

$log_url    = WC_Account_Funds_Updater_2_3_7::get_log_file_url();
$update_url = wp_nonce_url(
	add_query_arg( 'wc_account_funds_2_3_7_update', 'true', $log_url ),
	'wc_account_funds_2_3_7_update',
	'wc_account_funds_2_3_7_update_nonce'
);

?>
<div id="message" class="updated woocommerce-message">
	<h3><?php echo esc_html_x( 'WooCommerce Account Funds &#8211; Action required', 'admin notice', 'woocommerce-account-funds' ); ?></h3>

	<p><?php echo wp_kses_post( _x( "Due to an issue in a previous version of Account Funds, we've detected some orders paid with the <strong>Account Funds</strong> payment gateway in which the funds were not deducted from the customer account properly.", 'admin notice', 'woocommerce-account-funds' ) ); ?></p>
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: %s: log URL */
				_x( 'See the affected orders <a href="%s">here</a>.', 'admin notice', 'woocommerce-account-funds' ),
				esc_url( $log_url )
			)
		);
		?>
	</p>

	<p><?php echo esc_html_x( "This update script can automatically deduct the funds from the affected customers' accounts. Alternatively, you can skip the process and update the customers' funds manually.", 'admin notice', 'woocommerce-account-funds' ); ?></p>

	<p class="submit">
		<a href="<?php echo esc_url( add_query_arg( 'action', 'deduct', $update_url ) ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Deduct funds from customers', 'woocommerce-account-funds' ); ?>
		</a>

		<?php
		printf(
			'<a class="button-secondary" href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'action', 'skip', $update_url ) ),
			esc_html__( 'Skip the process', 'woocommerce-account-funds' )
		);
		?>
	</p>
</div>
<script type="text/javascript">
	jQuery( '.wc-update-now' ).on( 'click', function() {
		return window.confirm( '<?php echo esc_js( _x( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'admin notice', 'woocommerce-account-funds' ) ); ?>' ); // jshint ignore:line
	});
</script>
