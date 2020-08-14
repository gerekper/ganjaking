<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_shipping_date = get_option( 'ywcdd_ddm_enable_shipping_message', 'no' );
$show_delivery_date = get_option( 'ywcdd_ddm_enable_delivery_message', 'no' );


$last_shipping_date_string = '';
$time_limit_string         = '';
$delivery_date_string      = '';
if ( 'yes' == $show_shipping_date ) {
	$last_shipping_date_string = get_option( 'ywcdd_ddm_shipping_message', '' );

}
if ( 'yes' == $show_delivery_date ) {
	$delivery_date_string = get_option( 'ywcdd_ddm_delivery_message', '' );
	if ( empty( $time_limit ) ) {
		$delivery_date_string = get_option( 'ywcdd_ddm_time_limit_alternative_txt', '' );
	}
}



$bg_shipping = get_option( 'ywcdd_dm_customization_ready_bg', '#eff3f5' );
$bg_delivery = get_option( 'ywcdd_dm_customization_customer_bg', '#ffdea5' );

$icon_shipping = get_option('ywcdd_dm_customization_ready_icon', YITH_DELIVERY_DATE_ASSETS_URL.'images/truck.png' );
$icon_delivery = get_option('ywcdd_dm_customization_customer_icon', YITH_DELIVERY_DATE_ASSETS_URL.'images/clock.png' );

?>
<style>
    #ywcdd_info_shipping_date {
        background: <?php echo $bg_shipping;?>
    }

    #ywcdd_info_first_delivery_date {
        background: <?php echo $bg_delivery;?>
    }
    #ywcdd_info_shipping_date .ywcdd_shipping_icon{
        background-image: url(<?php echo $icon_shipping;?> );
    }
    #ywcdd_info_first_delivery_date .ywcdd_delivery_icon{
        background-image: url( <?php echo $icon_delivery;?> );
    }
</style>
<div id="ywcdd_info_single_product">
	<?php if ( isset( $last_shipping_date ) && ''!== $last_shipping_date_string   ):
		$last_shipping_date = wc_format_datetime( new WC_DateTime(date('Y-m-d',$last_shipping_date) , new DateTimeZone('UTC' ) ) );
		$last_shipping_date = "<span class='ywcdd_date_info shipping_date'>" .  $last_shipping_date  . '</span>';
		$last_shipping_date_string = str_replace( '{shipping_date}', $last_shipping_date, $last_shipping_date_string );
		?>
        <div id="ywcdd_info_shipping_date">
            <span class="ywcdd_shipping_icon"></span>
            <span class="ywcdd_shipping_message">
			    <?php echo $last_shipping_date_string; ?>
            </span>
        </div>
	<?php endif; ?>
	<?php if ( isset( $delivery_date ) && '' !== $delivery_date_string ):
		$delivery_date = wc_format_datetime( new WC_DateTime( date('Y-m-d',$delivery_date ), new DateTimeZone('UTC' ) ) );
		$delivery_date = "<span class='ywcdd_date_info delivery_date'>" . $delivery_date  . "</span>";

		$time_limit           = "<span class='ywcdd_date_info time_limit'>" . $time_limit . '</span>';
		$delivery_date_string = str_replace( '{delivery_date}', $delivery_date, $delivery_date_string );
		$delivery_date_string = str_replace( '{time_limit}', $time_limit, $delivery_date_string );
		?>
        <div id="ywcdd_info_first_delivery_date">
            <span class="ywcdd_delivery_icon"></span>
            <span class="ywcdd_delivery_message">
			    <?php echo $delivery_date_string; ?>
            </span>
        </div>
	<?php endif; ?>
</div>
