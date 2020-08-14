<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/** @var $yith_wc_email YITH_WC_Email_New_Order */
/** @var $vendor YITH_Vendor */

$tax_credited_to_vendor = 'vendor' == get_option( 'yith_wpv_commissions_tax_management', 'website' );
$currency               = array( 'currency' => yith_wcmv_get_order_currency( $order ) );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $yith_wc_email ); ?>

<p><?php printf( __( 'You have received an order from %s. The order is as follows:', 'yith-woocommerce-product-vendors' ), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false, $yith_wc_email ); ?>

<h2><?php printf( __( 'Order #%s', 'yith-woocommerce-product-vendors'), $order_number ); ?> (<?php printf( '<time datetime="%s">%s</time>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ); ?>)</h2>

<style>
    #vendor-table, #vendor-table th, #vendor-table td{border:2px solid #eee !important;}
    #vendor-table-shipping {margin-bottom: 15px;}
</style>

<table id="vendor-table" cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee; border-collapse: collapse" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'yith-woocommerce-product-vendors' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Qty', 'yith-woocommerce-product-vendors' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'yith-woocommerce-product-vendors' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Commission', 'Email: commission rate column', 'yith-woocommerce-product-vendors' ); ?></th>
			<?php if( $tax_credited_to_vendor ) : ?>
                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Tax', 'Email: tax amount column', 'yith-woocommerce-product-vendors' ); ?></th>
			<?php endif; ?>
            <th scope="col" style="text-align:left; border: 1px solid #eee;">
                <?php $earnings_text = _x( 'Earnings', 'Email: commission amount column', 'yith-woocommerce-product-vendors' ); ?>
                <?php if( $tax_credited_to_vendor ) : ?>
                    <?php $earnings_text .= ' '; ?>
                    <?php $earnings_text .= _x( '(inc. taxes)', 'Email: commission amount column', 'yith-woocommerce-product-vendors' ); ?>
                <?php endif; ?>
                <?php echo $earnings_text ?>
            </th>
		</tr>
	</thead>
    <?php echo $vendor->email_order_items_table( $order, false, true ); ?>
</table>

<?php $shipping_fee_ids = YITH_Commissions()->get_commissions( array( 'order_id' => yit_get_prop( $order, 'id' ), 'status' => 'all', 'type' => 'shipping' )  ); ?>

<?php if( ! empty( $shipping_fee_ids ) ) : ?>
    <h3><?php _ex( 'Shipping Fee', 'Email: Title before the Shipping fee list', 'yith-woocommerce-product-vendors' ); ?></h3>
    <table id="vendor-table-shipping" cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee; border-collapse: collapse" border="1" bordercolor="#eee">
        <thead>
        <tr>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Shipping Method', 'Email: shièpèing method column', 'yith-woocommerce-product-vendors' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Rate', 'Email: commission rate column', 'yith-woocommerce-product-vendors' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _ex( 'Amount', 'Email: commission amount column', 'yith-woocommerce-product-vendors' ); ?></th>
        </tr>
        </thead>
        <?php
        $line_items_shipping = $order->get_items( 'shipping' );
        foreach ( $shipping_fee_ids as $shipping_fee_id ) : ?>
            <?php
            $shipping_fee = YITH_Commission( $shipping_fee_id );
            if( ! empty( $shipping_fee ) ) : ?>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;">
                    <?php
                    $shipping_method = isset( $line_items_shipping[ $shipping_fee->line_item_id ] ) ? $line_items_shipping[ $shipping_fee->line_item_id ] : null;
                    if( ! empty( $shipping_method ) ){
	                    /** @var $shipping_method WC_Order_Item_Shipping */
	                    echo $shipping_method->get_name();
	                    $link = '<a href="' . $shipping_fee->get_view_url( 'admin' ) . '">' . $shipping_fee->id . '</a>';
	                    echo '<br/><small>' . _x( 'Commission id:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $link . '</small>';
                    }
                    ?>
                </td>
            <?php endif;?>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;">
                <?php echo $shipping_fee->get_rate( 'display' );?>
            </td>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;">
                <?php echo $shipping_fee->get_amount( 'display', $currency ); ?>
            </td>
        <?php endforeach; ?>
        <?php
        if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			} ?>
    </table>
<?php endif; ?>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, false, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $yith_wc_email ); ?>

<?php do_action( 'woocommerce_email_footer', $yith_wc_email ); ?>