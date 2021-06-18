<?php

	/* Remove woocommerce fields */
	function userpro_admin_woo_sync_erase(){
		$fields = get_option('userpro_fields');
		$builtin = get_option('userpro_fields_builtin');
		
		$woo = get_option('userpro_fields_woo');
		
		foreach($fields as $k => $arr){
			if (in_array($k, array_keys($woo))){
				unset($fields[$k]);
			}
		}
		
		foreach($builtin as $k => $arr){
			if (in_array($k, array_keys($woo))){
				unset($builtin[$k]);
			}
		}
		
		update_option('userpro_fields', $fields);
		update_option('userpro_fields_builtin', $builtin);
		
		delete_option('userpro_update_woosync');
	
	}

	/* Setup woocommerce fields */
	function userpro_admin_woo_sync(){
		
		$fields = get_option('userpro_fields');
		$builtin = get_option('userpro_fields_builtin');
		
		$woo_billing['billing_first_name'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing First Name'
		);
		$woo_billing['billing_last_name'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Last Name'
		);
		$woo_billing['billing_company'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Company'
		);
		$woo_billing['billing_address_1'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Address 1'
		);
		$woo_billing['billing_address_2'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Address 2'
		);
		$woo_billing['billing_city'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing City'
		);
		$woo_billing['billing_postcode'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Postcode'
		);
		$woo_billing['billing_country'] = array(
			'woo' => 1,
			'type' => 'select',
			'label' => 'Billing Country',
			'options' => userpro_filter_to_array('country'),
			'placeholder' => 'Select your Country'
		);
		$woo_billing['billing_state'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing State'
		);
		$woo_billing['billing_email'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing E-mail'
		);
		$woo_billing['billing_phone'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Billing Phone'
		);
		
		$woo_shipping['shipping_first_name'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping First Name'
		);
		$woo_shipping['shipping_last_name'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping Last Name'
		);
		$woo_shipping['shipping_company'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping Company'
		);
		$woo_shipping['shipping_address_1'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping Address 1'
		);
		$woo_shipping['shipping_address_2'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping Address 2'
		);
		$woo_shipping['shipping_city'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping City'
		);
		$woo_shipping['shipping_postcode'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping Postcode'
		);
		$woo_shipping['shipping_country'] = array(
			'woo' => 1,
			'type' => 'select',
			'label' => 'Shipping Country',
			'options' => userpro_filter_to_array('country'),
			'placeholder' => 'Select your Country'
		);
		$woo_shipping['shipping_state'] = array(
			'woo' => 1,
			'type' => 'text',
			'label' => 'Shipping State'
		);
		
		$woo = $woo_billing+$woo_shipping;
		$all_fields = $woo+$fields;
		$all_builtin = $woo+$builtin;
		
		update_option('userpro_fields_woo', $woo);
		update_option('userpro_fields', $all_fields);
		update_option('userpro_fields_builtin', $all_builtin);
		update_option("userpro_update_woosync", 1);

	}