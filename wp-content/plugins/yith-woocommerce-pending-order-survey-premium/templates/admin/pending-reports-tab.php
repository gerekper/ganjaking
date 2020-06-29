<?php

$tot_pending_order = get_option( 'ywcpos_count_pending_order', 0 );
$tot_email_send = get_option( '_ywcpos_tot_email_send' ,0 );
$tot_rec_order = get_option( 'ywcpos_count_order_rec', 0 );

if ( $tot_email_send != 0 ) {
    $rate_conversion = number_format( 100 * $tot_rec_order / $tot_email_send, 2, '.', '' ).' %' ;
}else {
    $rate_conversion =  '0.00 %' ;
}
?>
<div class="wrap">
    <h2><?php _e('Reports', 'yith-woocommerce-pending-order-survey') ?> </h2>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <table class="ywcpos-reports" cellpadding="10" cellspacing="0">
                    <tbody>
                        <tr>
                            <th width="20%"><?php _e('Pending orders','yith-woocommerce-pending-order-survey') ?></th>
                            <td><?php echo $tot_pending_order ?></td>
                        </tr>

                        <tr>
                            <th><?php _e('Emails sent','yith-woocommerce-pending-order-survey') ?></th>
                            <td><?php echo $tot_email_send ?></td>
                        </tr>


                        <tr>
                            <th><?php _e('Recovered orders','yith-woocommerce-pending-order-survey') ?></th>
                            <td><?php echo $tot_rec_order ?></td>
                        </tr>

                        <tr>
                            <th><?php _e('Conversion rate','yith-woocommerce-pending-order-survey') ?></th>
                            <td><?php echo $rate_conversion ?></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <br class="clear">
    </div>
</div>