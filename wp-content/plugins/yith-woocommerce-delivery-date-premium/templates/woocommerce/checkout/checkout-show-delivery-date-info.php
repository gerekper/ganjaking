<?php
if( !defined('ABSPATH')){
	exit;
}
?>
<span class="ywcdd_message">
   <?php _e('Your order will be shipped to carrier on','yith-woocommerce-delivery-date')?>
	<strong><?php echo $shipping_date?></strong>
</span>
<span class="ywcdd_message">
   <?php _e('You should receive the package on','yith-woocommerce-delivery-date')?>
	<strong><?php echo $delivery_date?></strong>
</span>