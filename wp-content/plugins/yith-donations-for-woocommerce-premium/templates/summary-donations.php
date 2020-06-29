<?php
if( !defined( 'ABSPATH' ) )
    exit;

$start_date =    '';
$end_date   =   '';
switch( $summary_from ){

    case 'day'  :

        $order_today    =   date( 'Y-m-d' );
        $start_date =   strtotime( sanitize_text_field( $order_today ) );
        $end_date   =   strtotime( 'midnight', strtotime( sanitize_text_field( $order_today ) ) );

        if( !$end_date )
            $end_date   =   current_time('timestamp');

        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;

    case 'year':
        $start_date    = strtotime( date( 'Y-01-01', current_time('timestamp') ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );

        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;

    case 'week' :
        $start_date    = strtotime( '-6 days', current_time( 'timestamp' ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;

    case 'month'    :
        $start_date    = strtotime( date( 'Y-m-01', current_time('timestamp') ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;

    case 'last_month'   :
        $first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
        $start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
        $end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;

    default :
        $start_date =   $end_date   =   '';
        $string_summ =  get_option( 'ywcds_widget_text_'.$summary_from );
        break;
}

$start_date =   $start_date !=   ''  ?   date( 'Y-m-d', $start_date )   :   '';
$end_date   =   $end_date   !=  ''  ?    date('Y-m-d', strtotime( '+1 DAY', $end_date ) ) : '';


$orders_id  =   ywcds_get_donations_orders( $start_date, $end_date );
$donation_id    =   get_option('_ywcds_donation_product_id');
$total  =   0.0;


foreach( $orders_id as $order_id ){
/*
 * @var WC_Order $order
 */
    $order =    wc_get_order( $order_id  );

   foreach( $order->get_items() as $items ) {

      if(  $items['product_id']   == $donation_id  ){

          $total += $order->get_line_total( $items, $include_tax == 'on' );
      }
   }

}

?>
<div class="ywcds_message">
    <div class="ywcds_text"><?php echo $string_summ;?></div>
    <div class="ywcds_amount">

        <?php

        $cifra=  str_split(  $total  )    ;


        echo '<span class="symbol">'.get_woocommerce_currency_symbol().'</span>';

        foreach(  $cifra  as $symbol )
        {
            if( is_numeric( $symbol ) ){
                echo '<span class="digit">'.$symbol.'</span>';
            }
            else
                echo '<span class="separator">'.$symbol.'</span>';
        }
        ?>
    </div>
</div>