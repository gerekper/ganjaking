<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Customer pre-order date changed notification email
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email );

$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
$billing_email = $pre_wc_30 ? $order->billing_email : $order->get_billing_email();
$billing_phone = $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone();?>

<?php if ( $availability_date ) : ?>
	<p><?php
/* translators: 1: availability date */
printf( __( 'Your pre-order release date has been changed. The new release date is %s. Your order details are shown below for your reference.', 'wc-pre-orders' ), $availability_date ); ?></p>
<?php else : ?>
	<p><?php _e( "Your pre-order release date has been changed. Your order details are shown below for your reference.", 'wc-pre-orders' ); ?></p>
<?php endif; ?>

<?php if ( $message ) : ?>
	<blockquote><?php echo wpautop( wptexturize( $message ) ); ?></blockquote>
<?php endif; ?>

<?php do_action( 'woocommerce_email_before_order_table', $order, false, $plain_text, $email ); ?>

<h2><?php echo __( 'Order:', 'wc-pre-orders' ) . ' ' . $order->get_order_number(); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'wc-pre-orders' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'wc-pre-orders' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'wc-pre-orders' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $pre_wc_30 ? $order->email_order_items_table() : wc_get_email_order_items( $order ); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, false, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, false, $plain_text, $email ); ?>

<h2><?php _e( 'Customer details', 'wc-pre-orders' ); ?></h2>

<?php if ( $billing_email ) : ?>
	<p><strong><?php _e( 'Email:', 'wc-pre-orders' ); ?></strong> <?php echo $billing_email; ?></p>
<?php endif; ?>
<?php if ( $billing_phone ) : ?>
	<p><strong><?php _e( 'Tel:', 'wc-pre-orders' ); ?></strong> <?php echo $billing_phone; ?></p>
<?php endif; ?>

<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
