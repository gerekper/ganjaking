<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $post;

$tot_email_send = get_post_meta( $post->ID, '_ywcpos_send_count', true );
$tot_order_rec_by_email = get_post_meta( $post->ID, '_ywcpos_email_order_rec', true );
$tot_order_rec_by_email = empty( $tot_order_rec_by_email ) ? 0 : $tot_order_rec_by_email;

if( $tot_email_send != 0 && $tot_email_send != '' ){
    $conversion      = number_format( 100 * $tot_order_rec_by_email / $tot_email_send, 2, '.','' ).' %';
}
else {
    $conversion = '0.00%';
    $tot_email_send = 0;
    $tot_order_rec_by_email = 0;
}

?>
<div id="ywcpos_email_report-content">
   <div class="ywcpos_tot_send">
        <p><span class="label"><?php _e('Email sent','yith-woocommerce-pending-order-survey');?></span><span class="value"><?php echo
                $tot_email_send;?></span></p>
   </div>
    <div class="ywcpos_tot_rec">
        <p><span class="label"><?php _e('Recovered orders','yith-woocommerce-pending-order-survey');?></span><span class="value"><?php echo
                $tot_order_rec_by_email;?></span></p>
    </div>
    <div class="ywcpos_conv_rate">
        <p><span class="label"><?php _e('Conversion rate','yith-woocommerce-pending-order-survey');?></span><span class="value"><?php echo
                $conversion;?></span></p>
    </div>
</div>