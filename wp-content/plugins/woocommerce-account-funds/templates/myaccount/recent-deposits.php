<?php
/**
 * My Account > Recent deposits
 *
 * @package WC_Account_Funds
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;

?>
<h2><?php esc_html_e( 'Recent Deposits', 'woocommerce-account-funds' ); ?></h2>
<table class="shop_table shop_table_responsive my_account_deposits">
	<thead>
		<tr>
			<th class="order-number"><span class="nobr"><?php esc_html_e( 'Order', 'woocommerce-account-funds' ); ?></span></th>
			<th class="order-date"><span class="nobr"><?php esc_html_e( 'Date', 'woocommerce-account-funds' ); ?></span></th>
			<th class="order-total"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-account-funds' ); ?></span></th>
			<th class="order-status"><span class="nobr"><?php esc_html_e( 'Amount Funded', 'woocommerce-account-funds' ); ?></span></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_account_funds_recent_deposit_items_data' ); ?>
	</tbody>
</table>
