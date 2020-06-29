<?php
/**
 * My Account > Deposit item data
 *
 * @package WC_Account_Funds
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<tr class="order">
	<td class="order-number" data-title="<?php esc_attr_e( 'Order Number', 'woocommerce-account-funds' ); ?>">
		<a href="<?php echo esc_url( $deposit['order_url'] ); ?>">
			#<?php echo esc_html( $deposit['order_number'] ); ?>
		</a>
	</td>
	<td class="order-date" data-title="<?php esc_attr_e( 'Date', 'woocommerce-account-funds' ); ?>">
		<time datetime="<?php echo esc_attr( date( 'c', strtotime( $deposit['order_date'] ) ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $deposit['order_date'] ) ) ); ?></time>
	</td>
	<td class="order-status" data-title="<?php esc_attr_e( 'Status', 'woocommerce-account-funds' ); ?>">
		<?php echo esc_html( $deposit['order_status_name'] ); ?>
	</td>
	<td class="order-total" data-title="<?php esc_attr_e( 'Amount Funded', 'woocommerce-account-funds' ); ?>">
		<?php echo wc_price( $deposit['funded'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>
</tr>
