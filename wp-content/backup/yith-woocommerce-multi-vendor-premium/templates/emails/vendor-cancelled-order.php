<?php
/**
 * Admin cancelled order email
 *
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php
$billing_first_name = yit_get_prop( $order, 'billing_first_name' );
$billing_last_name = yit_get_prop( $order, 'billing_last_name' );
$order_date = yit_get_prop( $order, 'date_created' );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $yith_wc_email ); ?>

<p><?php printf( __( 'The order #%d from %s has been cancelled. The order was as follows:', 'yith-woocommerce-product-vendors' ), $order_number, $billing_first_name . ' ' . $billing_last_name ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false, $yith_wc_email ); ?>

<h2><?php printf( __( 'Order #%s', 'yith-woocommerce-product-vendors'), $order_number ); ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order_date ) ), date_i18n( wc_date_format(), strtotime( $order_date ) ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'yith-woocommerce-product-vendors' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Qty', 'yith-woocommerce-product-vendors' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'yith-woocommerce-product-vendors' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Commission', 'Email: commission rate column', 'yith-woocommerce-product-vendors' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Earnings', 'Email: commission amount column', 'yith-woocommerce-product-vendors' ); ?></th>
		</tr>
	</thead>
    <?php echo $vendor->email_order_items_table( $order, false, true ); ?>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false, $yith_wc_email, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, false, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_footer', $yith_wc_email ); ?>
