<?php
/**
 * My Account > Recent deposits.
 *
 * @package WC_Account_Funds/Templates/My_Account
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.WP.I18n.TextDomainMismatch
$deposit_columns = array(
	'order-number' => __( 'Order', 'woocommerce' ),
	'order-date'   => __( 'Date', 'woocommerce' ),
	'order-status' => __( 'Status', 'woocommerce' ),
	'order-total'  => wc_get_account_funds_name(),
);
// phpcs:enable WordPress.WP.I18n.TextDomainMismatch
?>
<h2><?php esc_html_e( 'Recent Deposits', 'woocommerce-account-funds' ); ?></h2>
<table class="shop_table shop_table_responsive my_account_deposits">
	<thead>
		<tr>
			<?php foreach ( $deposit_columns as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_account_funds_recent_deposit_items_data' ); ?>
	</tbody>
</table>
