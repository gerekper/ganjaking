<?php


add_filter('woocommerce_dynamic_pricing_process_product_discounts', 'product_bundles_woocommerce_dynamic_pricing_process_product_discounts', 99, 5);

function product_bundles_woocommerce_dynamic_pricing_process_product_discounts($process, $product, $module_name, $module, $cart_item){
	if (!$process){
		return $process;
	}

	if (function_exists('wc_pb_get_bundled_cart_item_container')) {

		$bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item );

		if ( $bundle_container_item ) {
			$bundled_item_id = $cart_item['bundled_item_id'];
			$bundled_item    = $bundle_container_item['data']->get_bundled_item( $bundled_item_id );
			if ( !$bundled_item->is_priced_individually() ) {
				$process = false;
			}
		}
	}

	return $process;


}