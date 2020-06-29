<?php
if( !defined('ABSPATH')){
	exit;
}
global  $post;

$order = wc_get_order( $post );
$customer_id = $order->get_customer_id();

if( $customer_id ):
	$customer = new YITH_YWF_Customer( $customer_id );
	$total_funds =  apply_filters( 'yith_show_funds_used_into_order_currency', $customer->get_funds(), $order->get_id() );
	$total_funds_for_display = wc_price( $total_funds, array( 'currency' => $order->get_currency() ) );
	$customer_full_name = $order->get_formatted_billing_full_name();
	?>
<div id="yith_account_fund_meta_box">
	<p><?php echo $customer_full_name;?><small><?php echo sprintf( __('(available funds %s)', 'yith-woocommerce-account-funds'), $total_funds_for_display );?></small></p>

        <label for="yith_add_funds" class="screen-reader-text"><?php _e( 'Add fund' ,'yith-woocommerce-account-funds' );?></label>
        <input type="number" id="yith_add_funds" min="0" placeholder="<?php _e('Edit funds','yith-woocommerce-account-funds');?>">
        <input type="hidden" id="yith_customer_id" value="<?php echo $customer_id;?>">
        <button type="button" class="ywf_add_funds button"><?php echo __('Add', 'yith-woocommerce-account-funds' );?></button>
</div>
<?php
endif;