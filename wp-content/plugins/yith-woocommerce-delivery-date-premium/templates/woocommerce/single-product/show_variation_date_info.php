<?php
/**
 * @version 2.1.21
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;

if ( ! $product || ! $product instanceof WC_Product ) {
	return;
}
?>
<div id="ywcdd_info_single_product_variation_wrap">
	<div id="ywcdd_info_single_product_variation">

	</div>
	<script type="text/template" id="tmpl-variation-ywcdd-date-info-template">
		<div id="ywcdd_info_shipping_date">
			<span class="ywcdd_shipping_icon"></span>
			<span class="ywcdd_shipping_message">{{{data.variation.ywcdd_last_shipping_info}}}</span>
		</div>
		<div id="ywcdd_info_first_delivery_date">
			<span class="ywcdd_delivery_icon"></span>
			<span class="ywcdd_delivery_message">{{{data.variation.ywcdd_delivery_info}}}</span>
		</div>
	</script>
</div>
