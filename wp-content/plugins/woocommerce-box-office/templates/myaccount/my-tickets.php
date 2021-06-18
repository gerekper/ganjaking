<?php
/**
 * My Tickets.
 *
 * Shows list of tickets customer has on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-box-office/myaccount/my-tickets.php.
 *
 * HOWEVER, on occasion WooCommerce Box Office will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Automattic/WooCommerce
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tickets     = wc_box_office_get_tickets_by_user( get_current_user_id(), 'all' );
$has_tickets = count( $tickets ) > 0;
?>

<?php if ( $has_tickets ) : ?>

	<table class="woocommerce-MyAccount-my-tickets shop_table shop_table_responsive">
		<thead>
			<tr>
				<th class="ticket-product"><span class="nobr"><?php esc_html_e( 'Product', 'woocommerce-box-office' ); ?></span></th>
				<th class="ticket-order"><span class="nobr"><?php esc_html_e( 'Order', 'woocommerce-box-office' ); ?></span></th>
				<th class="ticket-actions"><span class="nobr">&nbsp;</span></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $tickets as $ticket ) : ?>
			<?php
			$ticket  = wc_box_office_get_ticket( $ticket );
			$order   = $ticket->order;
			$product = $ticket->product;

			if ( ! is_a( $ticket->product, 'WC_Product' ) ) {
				continue;
			}
			?>
			<tr>
				<td class="ticket-product">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
				</td>
				<td class="ticket-order">
					<?php
					/**
					 * In case a ticket created from admin without an order.
					 */
					?>
					<?php if ( is_a( $order, 'WC_Order' ) ) : ?>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php echo esc_html( $order->get_order_number() ); ?></a>
					<?php endif; ?>
				</td>
				<td class="ticket-actions">
					<a href="<?php echo esc_url( wcbo_get_my_ticket_url( $ticket->id ) ); ?>" class="button woocommerce-Button"><?php esc_html_e( 'View or Edit', 'woocommerce-box-office' ); ?></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go Shop', 'woocommerce-box-office' ) ?>
		</a>
		<?php esc_html_e( 'No ticket has been purchased yet.', 'woocommerce-box-office' ); ?>
	</div>
<?php endif; ?>
