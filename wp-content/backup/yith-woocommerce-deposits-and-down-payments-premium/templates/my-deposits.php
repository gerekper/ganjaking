<?php
/**
 * My deposit template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.1
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

?>

<div id="yith_wcdp_deposits_details" class="yith-wcdp-my-deposits">
	<h2>
		<?php
		// translators: 1. Deposit label.
		echo esc_html( apply_filters( 'yith_wcdp_my_deposit_title', sprintf( __( '%s Details', 'yith-woocommerce-deposits-and-down-payments' ), apply_filters( 'yith_wcdp_deposit_label', __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ) ) );
		?>
	</h2>
	<p>
		<?php
		// translators: 1. Deposit label.
		echo esc_html( apply_filters( 'yith_wcdp_my_deposit_text', sprintf( __( 'Some products in this order have been bought with %s. In order to complete the transaction and to ship the products, all remaining amounts have to be paid. Here follow details with owned balance for items in this order:', 'yith-woocommerce-deposits-and-down-payments' ), strtolower( apply_filters( 'yith_wcdp_deposit_label', __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ) ) ) );
		?>
	</p>
	<?php do_action( 'yith_wcdp_before_my_deposits_table', $order_id ); ?>
	<table class="shop_table shop_table_responsive order_details">
		<thead>
		<tr>
			<th class="order-id"><?php esc_html_e( 'Order', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
			<th class="product-name"><?php esc_html_e( 'Product', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
			<th class="order-status"><?php esc_html_e( 'Status', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
			<th class="order-paid"><?php esc_html_e( 'Paid', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
			<th class="order-to-pay"><?php esc_html_e( 'To be paid', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
			<th class="order-actions">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php if ( ! empty( $deposits ) ) : ?>
			<?php foreach ( $deposits as $deposit ) : ?>
				<tr>
					<td data-title="<?php esc_html_e( 'Order', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<a href="<?php echo esc_url( $deposit['suborder_view_url'] ); ?>">#<?php echo esc_html( $deposit['suborder_id'] ); ?></a>
					</td>
					<td class="product-name" data-title="<?php esc_html_e( 'Product', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<?php if ( ! empty( $deposit['product_list'] ) ) : ?>
						<ul>
							<?php foreach ( $deposit['product_list'] as $item ) : ?>
							<li><?php echo wp_kses_post( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</td>
					<td class="order-status" data-title="<?php esc_html_e( 'Status', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $deposit['order_status'] ) ); ?>
					</td>
					<td class="order-paid" data-title="<?php esc_html_e( 'Paid', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<?php echo wc_price( $deposit['order_paid'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( apply_filters( 'yith_wcdp_print_paid_details', true, $order_id ) ) : ?>
							<div class="details">
								<small><?php esc_html_e( 'Subtotal: ', 'yith-woocommerce-deposits-and-down-payments' ); ?> <?php echo wc_price( $deposit['order_subtotal'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>

								<?php if ( $deposit['order_discount'] ) : ?>
									<br>
									<small><?php esc_html_e( 'Discount: ', 'yith-woocommerce-deposits-and-down-payments' ); ?> <?php echo wc_price( -1 * $deposit['order_discount'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>
								<?php endif; ?>
								<?php if ( $deposit['order_shipping'] ) : ?>
									<br>
									<small><?php esc_html_e( 'Shipping: ', 'yith-woocommerce-deposits-and-down-payments' ); ?> <?php echo wc_price( $deposit['order_shipping'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>
								<?php endif; ?>
								<?php if ( $deposit['order_taxes'] ) : ?>
									<br>
									<small><?php esc_html_e( 'Taxes: ', 'yith-woocommerce-deposits-and-down-payments' ); ?> <?php echo wc_price( $deposit['order_taxes'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>
								<?php endif; ?>
							</div>
					<?php endif; ?>
					</td>
					<td class="order-to-pay" data-title="<?php esc_html_e( 'To be paid', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<?php echo wc_price( $deposit['order_to_pay'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
					<td class="order-actions" data-title="<?php esc_html_e( 'Actions', 'yith-woocommerce-deposits-and-down-payments' ); ?>">
						<?php
						if ( $deposit['actions'] ) {
							foreach ( $deposit['actions'] as $key => $action ) {
								echo '<a rel="nofollow" href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
							}
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<?php do_action( 'yith_wcdp_after_my_deposits_table', $order_id ); ?>
</div>
