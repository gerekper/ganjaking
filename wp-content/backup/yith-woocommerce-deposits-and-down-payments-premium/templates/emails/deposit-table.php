<?php
/**
 * Deposit table (HTML)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
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
	exit; // Exit if accessed directly
}
?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
	<tr>
		<th class="td" scope="col" style="text-align:left;"><?php _e( 'Suborder', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
		<th class="td" scope="col" style="text-align:left;"><?php _e( 'Product', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
		<th class="td" scope="col" style="text-align:left;"><?php _e( 'Status', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
		<th class="td" scope="col" style="text-align:left;"><?php _e( 'Total', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$items = $parent_order->get_items( 'line_item' );
	$total_paid = 0;
	$total_to_pay = 0;

	if( ! empty( $items ) ):
		foreach( $items as $item_id => $item ):
			if( ! isset( $item['deposit'] ) || ! $item['deposit'] ) {
				continue;
			}

			$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : $parent_order->get_product_from_item( $item );;
			$suborder = wc_get_order( $item['full_payment_id'] );

			if( ! $product || ! $suborder || in_array( $suborder->get_status(), array( 'completed', 'processing', 'cancelled' ) ) ){
				continue;
			}

			//$paid = $parent_order->get_item_total( $item, true );
			$paid = $parent_order->get_line_total( $item, true );
			$paid += in_array( $suborder->get_status(), array( 'processing', 'completed' ) ) ? $suborder->get_total() : 0;
			$to_pay = in_array( $suborder->get_status(), array( 'processing', 'completed' ) ) ? 0 : $suborder->get_total();

			$total_paid += $paid;
			$total_to_pay += $to_pay;
			?>
			<tr>
				<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php printf( '#%d', $suborder->get_order_number() ) ?>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php printf( '<a href="%s">%s</a>', $product->get_permalink(), $item['name'] ) ?>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php echo  wc_get_order_status_name( $suborder->get_status() ) ?>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php printf( '%s (of %s)', wc_price( $paid, array( 'currency' => $suborder->get_currency() ) ), wc_price( $paid + $to_pay, array( 'currency' => $suborder->get_currency() ) ) ) ?>
				</td>
			</tr>
			<?php
		endforeach;
	endif;
	?>
	</tbody>
	<tfoot>
	<tr>
		<th class="td" scope="col" colspan="3" style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; text-align:left; border-top-width: 4px;"><?php _e( 'Total paid:', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
		<td class="td" scope="col" style="text-align:left; border-top-width: 4px;"><?php echo wc_price( $total_paid, array( 'currency' => $parent_order->get_currency() ) ); ?></td>
	</tr>
	<tr>
		<th class="td" scope="col" colspan="3" style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; text-align:left;"><?php _e( 'Total to be paid:', 'yith-woocommerce-deposits-and-down-payments' ); ?></th>
		<td class="td" scope="col" style="text-align:left;"><?php echo wc_price( $total_to_pay, array( 'currency' => $parent_order->get_currency() )); ?></td>
	</tr>
	</tfoot>
</table>