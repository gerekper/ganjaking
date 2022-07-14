<?php
if( !class_exists( 'UPWDefaultOptions' ) ){

class UPWDefaultOptions{

	function __contstruct(){
		
	}

	/* get a global option */
	function userpro_woocommerce_get_option( $option ) {
		$userpro_woocommerce_default_options = $this->userpro_woocommerce_default_options();
		$settings = get_option('userpro_woocommerce');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $userpro_woocommerce_default_options[$option];
				}
				break;
	
		}
	}
	
	/* set a global option */
	function userpro_woocommerce_set_option($option, $newvalue){
		$settings = get_option('userpro_woocommerce');
		$settings[$option] = $newvalue;
		update_option('userpro_woocommerce', $settings);
	}

	function userpro_woocommerce_default_options(){
		$array = array();
		/* Order Tab Default options */
		$array['upw_hide_orders'] = 'n';
		$array['upw_order_tab_text'] = 'My orders';
		$array['upw_billing_address_tab_text'] = 'Billing address';
		$array['upw_shipping_address_tab_text'] = 'Shipping address';
		$array['upw_hide_orders_header'] = 'n';
		$array['upw_hide_completed_orders'] = 'n';
		$array['upw_purchases_code']='';
		/* Purchase Tab Default Options */
		$array['upw_hide_purchases'] = 'n';
		$array['upw_userprologin'] = 'n';
		$array['upw_purchase_tab_text'] = 'My Purchases';
		$array['upw_hide_load_more'] = 'n';
		$array['upw_products_per_page'] = '6';
		$array['upw_total_products_show'] = '12';
		$array['upw_show_wishlist'] = 'n';
		$array['upw_wishlist_tab_text'] = 'My Wishlist';
		return apply_filters('userpro_woocommerce_default_options_array', $array);
	}
}
	new UPWDefaultOptions();
}
