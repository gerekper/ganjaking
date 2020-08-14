<?php
/**
 * Email for user notification
 *
 * @author  Yithemes
 * @package yith-advanced-refund-system-for-woocommerce.premium\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$body         = ! empty( $email->email_body ) ? $email->email_body : '';
$request_id   = ! empty( $email->request_id ) ? $email->request_id : '';
$request      = new YITH_Refund_Request( $request_id );
$order        = wc_get_order( $request->order_id );
$order_url    = $order->get_view_order_url();
$order_link   = '<a href="' . $order_url . '">#' . $request->order_id . '</a>';
$request_link = '<a href="' . esc_attr( $request->get_view_request_url() ) . '">#' . $request_id . '</a>';
$customer     = new WP_User( $request->customer_id );

$coupon_amount = ! empty( $email->coupon_amount ) ? $email->coupon_amount : '';
$coupon_code = ! empty( $email->coupon_code ) ? $email->coupon_code : '';
$coupon_expiry_date = ! empty( $email->coupon_expiry_date ) ? $email->coupon_expiry_date : '';

do_action( 'woocommerce_email_header', $email_heading, $email );

echo '<p>';
$body = str_replace(
    array(
        '{customer_name}',
	    '{order_number}',
	    '{request_number}',
	    '{coupon_amount}'
    ),
    array(
	    ucwords( $customer->display_name ),
        $order_link,
	    $request_link,
	    ! empty( $coupon_amount ) ? wc_price( $coupon_amount ) : ''
    ),
    $body
);
echo $body;
echo '</p>';

// If Coupon email, show the coupon code.

if ( ! empty( $coupon_code ) ) {
	?>
	<div class="ywcars_coupon_box">
		<span class="ywcars_coupon"><?php echo $coupon_code; ?></span>
        <div class="ywcars_coupon_description">
            <div><?php echo esc_html_x( 'Value:', 'Coupon amount', 'yith-advanced-refund-system-for-woocommerce' ) . ' ' . wc_price( $coupon_amount, array( 'currency' => $order->get_currency() ) ) ;
                ?></div>
	        <?php if ( $coupon_expiry_date ) : ?>
                <?php $date = date( get_option( 'date_format' ), $coupon_expiry_date ) ?>
                <div><?php echo esc_html_x( 'Expires:', 'Coupon expiration date', 'yith-advanced-refund-system-for-woocommerce' ) . ' ' . $date; ?></div>
	        <?php endif; ?>
        </div>
	</div>
	<?php
}

do_action( 'woocommerce_email_footer' );