<?php
/**
 * Frontend
 * @version 0.1
 */

class evodp_frontend{
	public function __construct(){
		add_filter('evotx_stop_selling', array($this, 'stop_selling') , 10,2);
		add_filter('evotx_single_prod_price', array($this, 'get_ticket_dynamic_price') , 10, 2);
		add_filter('evotx_single_prod_striked_price', array($this, 'striked_price') , 10, 3);
		add_filter('evotx_single_prod_label_add', array($this, 'label_add') , 10, 3);
		add_filter('woocommerce_before_calculate_totals', array($this, 'custom_cart_price') );
	}

	function is_dp_active($event_id){
		$dp_status = get_post_meta($event_id, '_evodp_activate', true);
		return !empty($dp_status) && $dp_status=='yes' ? true: false;
	}

	function get_ticket_dynamic_price($price_html, $object){

		// check if dp activated
		if( !evo_check_yn($object->epmv, '_evodp_activate')) return $price_html;

		$new_price = $this->get_event_dynamic_price($price_html, $object->event_id, false, false);	

		return $new_price;
	}

	function striked_price($return, $price_html, $object){
		// check if dp activated
		if( !evo_check_yn($object->epmv, '_evodp_activate')) return $return;

		if(!evo_check_yn($object->epmv, '_evodp_show_regularp')) return '';

		$new_price = $this->get_event_dynamic_price($price_html, $object->event_id, false, false);

		if($new_price != $price_html ) return $price_html;

		return $return;
	}
	function label_add($return, $price_html, $object){
		// check if dp activated
		if( !evo_check_yn($object->epmv, '_evodp_activate')) return $return;

		//if(!evo_check_yn($object->epmv, '_evodp_show_regularp')) return $return;

		$new_price = $this->get_event_dynamic_price($price_html, $object->event_id, false, false);

		if($new_price != $price_html ){

			if( !empty($object->epmv[ '_evodp_tbp_msg']) ){
				return $object->epmv['_evodp_tbp_msg'][0];
			}
		}

		return $return;
	}


	// price for the cart
	function custom_cart_price($cart_object){
		$cart = WC()->cart;
		foreach ( $cart->cart_contents as $cart_item_key=>$item ){
			//print_r($item);
			//if(empty($item['evost_eventid'])) continue;
			if(empty($item['evotx_event_id_wc'])) continue;

			$event_id = $item['evotx_event_id_wc'];

			if( $this->is_dp_active($event_id)){
				$custom_price =  $this->get_event_dynamic_price($item['data']->get_price(), $event_id, false, false);

				$item['data']->set_price($custom_price);
				//$item['data']->price = $custom_price;
			}
		}
	}

	function get_event_dynamic_price($price, $event_id, $format_price = false, $number_format = true){

		$event = new EVO_Event($event_id);

		if( !$event->check_yn('_evodp_activate' )) return $price;

		$member_price = $this->event_ticket_member_price($event_id);
		$__woo_currencySYM = get_woocommerce_currency_symbol();
		
		if( $event->check_yn( '_evodp_time_pricing') && $event->get_prop('_evodp_prices') ){
			$tbp = $event->get_prop('_evodp_prices');

			if(sizeof($tbp)>0){
				
				
				$fnc = new evodp_fnc();
				$rightnow = $fnc->get_local_unix_now();
				
				foreach($tbp as $data){
					if($data[0] <= $rightnow && $data[1] >= $rightnow){

						// special loggedin user price
						if(is_user_logged_in() && (!empty($data['mp']) || $member_price) ){
							$mp = !empty($data['mp'])? $data['mp']: ($member_price? $member_price: $data['p']);
							return ($format_price? $__woo_currencySYM:'') . ($number_format? number_format($mp,2): $mp);
						}
						return ($format_price? $__woo_currencySYM:'') . ($number_format? number_format($data['p'],2): $data['p']);
					}
				}
			}			
		}

		return (is_user_logged_in() && $member_price) ? ($format_price? $__woo_currencySYM:'') . ($number_format? number_format($member_price,2): $member_price): $price;
	} 

	// special member price
		function event_ticket_member_price($event_id){

			$event = new EVO_Event($event_id);

			//print_r($data);
			if( $event->check_yn( '_evodp_member_pricing') && $event->get_prop('_evodp_member_def_price') ){
				return $event->get_prop('_evodp_member_def_price');
			}else{
				return false;
			}
		}

	// stop selling times and result
	function stop_selling($val, $object){
		if($val == true) return true;

		$event = new EVO_Event($object->event_id);
					
		// if ticket available for sale
		if( !$event->check_yn('_evodp_activate' )) return false;

		// is ticket unavailable slots enabled and have values
		if( $event->check_yn( '_evodp_unavailables') && $event->get_prop('_evodp_una') ){
			$una = $event->get_prop('_evodp_una');

			if(sizeof($una)>0){
				//date_default_timezone_set('UTC');
				
				$fnc = new evodp_fnc();

				$rightnow = $fnc->get_local_unix_now();
				foreach($una as $times){
					if($times[0] <= $rightnow && $times[1] >= $rightnow){
						return true;
					}
				}
			}
		}

		return false;
	}

	
	
}