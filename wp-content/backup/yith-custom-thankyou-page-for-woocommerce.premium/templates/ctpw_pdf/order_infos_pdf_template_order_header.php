<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH Custom ThankYou Page for Woocommerce
 */

/**
 * PDF Order Header Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/ctpw_pdf/order_infos_pdf_template_order_header.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<p class="woocommerce-thankyou-order-received"><?php echo wp_kses_post( apply_filters( 'woocommerce_thankyou_order_received_text_yctpw_pdf', esc_html__( 'Thank you. Your order has been received.', 'yith-custom-thankyou-page-for-woocommerce' ), $order ) ); ?></p>
<h2 class="order_details_title">
	<?php
	// APPLY_FILTER ctpw_order_details_title: change the Order Details Table title.
	echo wp_kses_post( apply_filters( 'ctpw_order_details_title', esc_html__( 'Order details', 'yith-custom-thankyou-page-for-woocommerce' ) ) );
	?>
</h2>
<table class="order_details">
	<thead>
	<tr class="woocommerce-order-overview__order order">
		<td class="order"><?php esc_html_e( 'Order:', 'yith-custom-thankyou-page-for-woocommerce' ); ?></td>
		<td class="date"><?php esc_html_e( 'Date:', 'yith-custom-thankyou-page-for-woocommerce' ); ?></td>
		<td class="total"><?php esc_html_e( 'Total:', 'yith-custom-thankyou-page-for-woocommerce' ); ?></td>
		<?php if ( $order->get_payment_method_title() ) : ?>
			<td class="woocommerce-order-overview__payment-method method">
				<?php esc_html_e( 'Payment method:', 'yith-custom-thankyou-page-for-woocommerce' ); ?>
			</td>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td class="order"><?php echo wp_kses_post( $order->get_order_number() ); ?></td>
		<td class="date">
			<strong><?php echo wp_kses_post( date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) ) ); ?></strong>
		</td>
		<td class="total"><strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></td>
		<?php if ( $order->get_payment_method_title() ) : ?>
			<td class="woocommerce-order-overview__payment-method method">
				<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
			</td>
		<?php endif; ?>
	</tr>
	</tbody>
</table>
<hr/>
