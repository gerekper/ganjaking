<?php
/**
 * EventON Ticket corresponding to WC order
 * CPT evo-tix 
 * @version 0.1
 */

class evotx_tix{

	public $evo_tix_id='';

// Ticket Creation and alteration
	function create_tickets_for_order($order_id){
		if(empty($order_id)) return false; 	

		$order = new WC_Order( $order_id );	
		$items = $order->get_items();

		$TA = new EVOTX_Attendees();
		$EH = new evo_helper();

		$TH = get_post_meta($order_id, '_tixholders', true);
			$TH = $TA->_process_ticket_holders( $TH); // process old and new methods of saved ticket holders

		if ( sizeof( $items ) <= 0 ) return false;

		// check if order already have tickets created
	    	$order_tix = get_post_meta($order_id, '_order_tix', true);
	    	if($order_tix == 'created') return false;

	    // initials
	    	$order_has_event_tickets = false;
	    	$order_ticket_numbers = array();

	    // event instance value
	    	$_event_instance = 1;
		    $_cart_events = array();

	    // EACH Order item 
			foreach ($items as $item_id => $item) {	

				// check order item is a event
				$event_id = get_post_meta( $item['product_id'], '_eventid', true); 
			    if(empty($event_id)) continue;	

			    // order item has event tickets
			    $order_has_event_tickets = true;	

			    $ri = $this->get_ri_from_itemmeta($item);

			    // order purchaser information
			    $TP = $TA->get_ticket_purchaser($order);

			    // add event to cart events array
	        		if(!in_array($event_id, $_cart_events)){		        			
	        			$_event_instance=1;
	        			$_cart_events[] = $event_id;
	        		}else{
	        			$_event_instance++;		        			
	        		}

			    // Item type
			    	$type = 'Normal';
					if(!empty($item['variation_id'])){
						$_product = new WC_Product_Variation($item['variation_id'] );
	        			foreach($_product->get_variation_attributes( ) as $f=>$v){	$type = $v;	}
	        		}        	

			    // create event ticket for each order item qty
			    for($Q=0; $Q<$item['qty']; $Q++){

			    	if($created_tix_id = $EH->create_posts(array(
						'post_type'=>'evo-tix',
						'post_status'=>'publish',
						'post_title'=>'TICKET '.date('M d Y @ h:i:sa', time()),
						'post_content'=>''
					))){

			    		// set ticket number
			    		$ticket_number = $created_tix_id.'-'.$order_id.'-'.( !empty($item['variation_id'])? $item['variation_id']: $item['product_id']) . 'T'. $Q;
			    		$order_ticket_numbers[] = $ticket_number;

			    		// if additional ticket holders saved get ticket holder for this ticket
			    		$_this_TH = $TH? $TA->__filter_ticket_holder($TH, $event_id, $ri, $Q): $TP;

			    		// get order Item meta values and save to evo-tix post - for easy access	        	
						// save ticket data	
						foreach( apply_filters('evotx_tix_save_field_meta', array(
							'name'			=> isset($_this_TH['name'])? $_this_TH['name']: $TP['name'],
							'email'			=> isset($_this_TH['email'])? $_this_TH['email']: $TP['email'],
							'qty'			=> 1,
							'cost'			=>$order->get_line_subtotal($item),
							'type'			=>$type,
							'ticket_ids'	=> array($ticket_number=>'check-in'),							
							'wcid'			=>$item['product_id'],
							'tix_status'	=>'none',
							'status'		=>'check-in',
							'_eventid'		=>$event_id,
							'_orderid'		=>$order_id,
							'_customerid'	=>$TP['customer_id'],
							'_order_item_id'=>$item_id,
							'_ticket_number'=> $ticket_number,
							'_ticket_number_index' =>$Q, // save the index to fetch correct ticket holder
							'_ticket_number_instance' => $_event_instance, // instance of event for same event in cart with different cart item meta values
							'repeat_interval'=>$ri
						), $item) as $field=>$value){
							$EH->create_custom_meta($created_tix_id, $field, $value);
						}
					}
			    }			    
			}

		// if order has event tickets
			if( $order_has_event_tickets){
				update_post_meta($order_id, '_order_type','evotix');	
				update_post_meta($order_id, '_order_tix','created'); 
			}
		// add all ticket numbers for this order
			if(count($order_ticket_numbers)>0){
				update_post_meta($order_id, '_tixids', $order_ticket_numbers);
			}
	}

// Tickets based on WC Order
	// event details
		function get_event_id_by_product_id($product_id){
			$event_id = get_post_meta($product_id, '_eventid',true);
			if(empty($event_id)){
				$product_id = wp_get_post_parent_id($product_id);
				$event_id = get_post_meta($product_id, '_eventid',true);

				return ($event_id)? $event_id: false;
			}
			return ($event_id)? $event_id: false;
		}

	// other
	function get_ticket_variation_id($ticket_number){
		$tt = explode('-', $ticket_number);

		$product_id = wp_get_post_parent_id( (int)$tt[2]);
		return ( !$product_id)? false: (int)$tt[2];

	}

	function get_ticket_numbers_for_order($order_id){
		$ticket_ids = get_post_meta($order_id, '_tixids', true);
		return $ticket_ids? $ticket_ids: false;
	}

	function get_evotix_id_by_product_order($order_id, $product_id, $complete=false){
		$ticket_ids = get_post_meta($order_id, '_tixids', true); // returns Array ( [0] => 1837-1836-1831 [1] => 1838-1836-1830 )

		if(empty($ticket_ids)) return false;

		//print_r($ticket_ids);

		foreach($ticket_ids as $ticket_number){
			$tt = explode('-', $ticket_number);

			if($tt[1]==$order_id &&  $tt[2]==$product_id){
				return $complete? $ticket_number : $tt[0];
			}
		}
		return false;
	}
	function get_ticket_number_by_productorder($order_id, $product_id){
		return $this->get_evotix_id_by_product_order($order_id, $product_id, true);
	}
	function get_product_id_by_ticketnumber($ticket_number){
		$tt = explode('-', $ticket_number);
		return (int)$tt[2];
	}
	function get_evotix_id_by_ticketnumber($ticket_number){
		$tt = explode('-', $ticket_number);
		return (int)$tt[0];
	}
	
	function get_ticket_purchaser_info($ticket_number){
		$tt = explode('-', $ticket_number);
		$evotix_meta = get_post_custom($tt[0]);

		return (!empty($evotix_meta['name'])? $evotix_meta['name'][0]:'').' '.
			(!empty($evotix_meta['email'])? $evotix_meta['email'][0]:'');
	}

// TICKET HOLDER
	function get_order_ticket_holders($order_id){
		$order_ticket_holders = get_post_meta($order_id, '_tixholders', true);
		return ($order_ticket_holders)? $order_ticket_holders: false;
		// returns array(event_id=> array(names))
	}
	function get_ticket_holders_forevent($event_id, $ticket_holders_array){
		if(!is_array($ticket_holders_array)) return false;

		if(!isset($ticket_holders_array[$event_id])) return false;

		return array_filter($ticket_holders_array[$event_id]);
	}

// Ticket Quantity related
	function fix_incorrect_qty($evotix_id){
		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);
		if($ticket_ids){
			$qty = count($ticket_ids);
			update_post_meta($evotix_id, 'qty', $qty);
		}
	}

// GETTER
	// from ticket number
	// @ 1.7
		function get_data_from_ticket_number($TN){
			$TN = explode('-', $TN);
			$output = array();
			$output['evotix_id'] = $TN[0];
			$output['order_id'] = $TN[1];
			$output['wcid'] = $TN[2];
			if(strpos($TN[2], 'T')!== false){
				$T = explode('T', $TN[2]);
				$output['wcid'] = $T[0];
			}
			return $output;
		}

// Ticket status related
	function get_ticket_numbers_by_evotix($evotix_id, $return_type = 'array'){
		$output = '';
		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);

		if($ticket_ids){
			$output = $ticket_ids;
		}else{// if ticket IDs were saved on older method
			$tids = get_post_meta($evotix_id, 'tid',true);

			if(empty($tid)) return false;

			$tids =   explode(',',$tids);
			$data = array();
			foreach($tids as $ids){
				$data[$ids] = 'check-in'; 
			}

			update_post_meta($evotix_id, 'ticket_ids',$data);
			$output =  $data;
		}

		if($return_type=='array'){
			return $output;
		}else{
			// comma separated string
			$str = ''; $index = 1;
			foreach($output as $key=>$val){
				$str .= ($index== count($output)? $key: $key.', ');
				$index++;
			}
			return $str;
		}

	}

	function get_checkin_status_text($status, $lang=''){
		global $evotx;
		$evopt = $evotx->opt2;
		$lang = (!empty($lang))? $lang : 'L1';

		if($status=='check-in'){
			return (!empty($evopt[$lang]['evoTX_003x']))? $evopt[$lang]['evoTX_003x']: 'check-in';
		}elseif($status=='refunded'){
			return evo_lang('refunded', $lang);
		}else{
			return (!empty($evopt[$lang]['evoTX_003y']))? $evopt[$lang]['evoTX_003y']: 'checked';
		}
	}
	function get_other_status($status=''){
		$new_status = ($status=='check-in')? 'checked':'check-in';
		$new_status_lang = $this->get_checkin_status_text($new_status);

		return array($new_status, $new_status_lang);
	}
	function checked_count($evotix_id){
		$status = get_post_meta($evotix_id, 'status',true);
		$qty = get_post_meta($evotix_id, 'qty',true);
		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);

		if($ticket_ids){
			$count = array_count_values($ticket_ids);
			$count['checked'] = ( !empty($count['checked'] )? $count['checked'] : 0);
			$count['qty'] = !empty($qty)? $qty:1;
			return $count; // Array ( [check-in] => 2 )
		}else{
			$status =  (!empty($status))? $status: 'check-in';
			return array($status=>'1', 'qty'=>(!empty($qty)? $qty:1) );
		}
	}
	function get_ticket_status_by_ticket_number($ticket_number){
		$tixNum = explode('-', $ticket_number);
		$evotix_id = $tixNum[0];

		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);

		if(!empty($ticket_ids) ){
			if(array_key_exists($ticket_number, $ticket_ids)){
				return $ticket_ids[$ticket_number];
			}else{
				return 'check-in';
			}
		}else{
			$status = get_post_meta($evotix_id, 'status',true);
			return (!empty($status))? $status: 'check-in';
		}
	}
	// change ticket number status
	// @updated 1.7.8
	function change_ticket_number_status($new_status, $ticket_number, $evotix_id=''){
		// get the evo-tix post ID
		if(empty($evotix_id)){
			$evotix_id = explode('-', $ticket_number);
			$evotix_id = $evotix_id[0];
		}

		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);
		if($ticket_ids){
			//unset($ticket_ids[$ticket_number]);
			
			// make sure ticket id is in the saved data
			if(array_key_exists($ticket_number, $ticket_ids)){
				$ticket_ids[$ticket_number]= $new_status;
			}else{
				return false;
			}		
			update_post_meta($evotix_id, 'ticket_ids',$ticket_ids);					
		}else{
			update_post_meta($evotix_id, 'status',$new_status);						
		}
	}
	// return ticket numbers if there are other tickets in the same order
	function get_other_tix_order($ticket_number){
		$tixNum = explode('-', $ticket_number);
		$evotix_id = $tixNum[0];
		$ticket_ids = get_post_meta($evotix_id, 'ticket_ids',true);

		unset($ticket_ids[$ticket_number]);

		return $ticket_ids;
	}
	

// SUPPORTIVE
	// get repeat interval of an order item from event time
	    	function get_ri_from_itemmeta($item){
	    		if( isset($item['_event_ri'])) return $item['_event_ri']; // since 1.6.9

	    		$item_meta = (!empty($item['Event-Time'])? $item['Event-Time']: false);
		    	$ri = 0;
		    	
		    	if($item_meta){
		    		if(strpos($item_meta, '[RI')!== false){
		    			$ri__ = explode('[RI', $item_meta);
				    	$ri_ = explode(']', $ri__[1]);
				    	$ri = $ri_[0];
		    		}
		    	}

		    	return $ri;
	    	}
	public function get_prop($field){
		return get_post_meta($this->evo_tix_id, $field, true);
	}
}