<?php
/**
 * TIcket frontend and backend supporting functions
 * @version 2.0.3
 */
class evotx_functions{
	private $evohelper;
	function __construct(){
		$this->EH = new evo_helper();
	}

// ORDER Related
	// get order status from order ID
		function get_order_status($orderid=''){
			if(empty($orderid)) return false;
			$orderstatus = get_post_status($orderid);
			$orderstatus = str_replace('wc-', '', $orderstatus);
			$orderstatus = str_replace('-', ' ', $orderstatus);

			return $orderstatus;
		}
		function is_order_complete($orderid){
			return ($this->get_order_status($orderid)=='completed')? true: false;
		}
// CHECKING TICKET STATUS related
	// get proper ticket status name I18N
		function get_checkin_status($status, $lang=''){
			global $evotx;
			$evopt = $evotx->opt2;
			$lang = (!empty($lang))? $lang : 'L1';

			if($status=='check-in'){
				return (!empty($evopt[$lang]['evoTX_003x']))? $evopt[$lang]['evoTX_003x']: 'check-in';
			}else{
				return (!empty($evopt[$lang]['evoTX_003y']))? $evopt[$lang]['evoTX_003y']: 'checked';
			}
		}
		function get_statuses_lang($lang=''){
			global $evotx;
			$evopt = $evotx->opt2;
			$lang = (!empty($lang))? $lang : 'L1';

			return array(
				'check-in'=> ((!empty($evopt[$lang]['evoTX_003x']))? $evopt[$lang]['evoTX_003x']: 'check-in'),
				'checked'=> ((!empty($evopt[$lang]['evoTX_003y']))? $evopt[$lang]['evoTX_003y']: 'checked'),
			);
		}

	// check if an order have event tickets
		public function does_order_have_tickets($order_id){
			$meta = get_post_meta($order_id, '_tixids', true);
			return (!empty($meta))? true: false;
		}		

// TICKET related
		// get additional ticket holder array
		// **maybe deprecated
			function get_ticketholder_names($event_id, $ticketholder_array=''){
				if(empty($ticketholder_array)) return false;

				if(!isset($ticketholder_array[$event_id])) return false;

				$ticket_holder = array_filter($ticketholder_array[$event_id]);
				if(empty($ticket_holder)) return false;
				return $ticket_holder;
			}
		// GET product type by product ID
			public function get_product_type($product_id){

				$product = wc_get_product($product_id);
				return $product->get_type();
			}
		
		// alter initial WC order if they are event ticket orders
			function alt_initial_event_order($order_id){
				$order = new WC_Order( $order_id );	
			    $items = $order->get_items();

			    $evtix_update = false;
			    foreach ($items as $item) {	
			    	$eid = get_post_meta( $item['product_id'], '_eventid', true);  	
			    	if(empty($eid)) continue;

			    	if(!$evtix_update){
			    		update_post_meta($order_id, '_order_type','evotix');
			    		$evtix_update = true;	
			    	}
			    	  
			    }
			}
		
		// get ticket item id from ticket id
			function get_tiid($ticket_id){
				$tix = explode('-', $ticket_id);
				return $tix[0];
			}
		
		// corrected ticket IDs
			function correct_tix_ids($t_pmv, $ticket_item_id){
				$tix = explode(',', $t_pmv['tid'][0]);
				foreach($tix as $tt){
					$ticket_ids[$tt] = 'check-in';
				}				
				update_post_meta($ticket_item_id, 'ticket_ids',$ticket_ids);
			}
	
		// ADD NEW matching woocommerce product
			function add_new_woocommerce_product($post_id){
				$user_ID = get_current_user_id();
				$title = $this->get_ticket_product_title($post_id);
				if(!$title) return false;

				$post = array(
					'post_author' => $user_ID,
					'post_content' => (!empty($_REQUEST['_tx_desc']))? $_REQUEST['_tx_desc']: "Event Ticket",
					'post_status' => "publish",
					'post_title' => $title,
					'post_type' => "product"
				);

				// create woocommerce product
				$woo_post_id = wp_insert_post( $post );
				if($woo_post_id){
					
					//wp_set_object_terms( $woo_post_id, $product->model, 'product_cat' );
					wp_set_object_terms($woo_post_id, $_REQUEST['tx_product_type'], 'product_type');
					

					update_post_meta( $post_id, 'tx_woocommerce_product_id', $woo_post_id);
					$this->save_product_meta_values($woo_post_id, $post_id);

					// add category 
					$this->assign_woo_cat($woo_post_id);

					// copy featured event image
					// $ft_img_id = get_post_thumbnail_id($post_id);
					// if(!empty($ft_img_id)) set_post_thumbnail( $woo_post_id, $ft_img_id );

					return $woo_post_id;
				}

				return false;
			}
		// Save woocommerce product meta values
			function save_product_meta_values($woo_post_id, $post_id, $method='post'){


				$update_metas = array(	
					'_sku'=>'_sku',
					'_regular_price'=>'_regular_price',					
					'_price'=>'_price',
					'_sale_price'=>'_sale_price',
					'_virtual'=>'yes',
					'_stock_status'=>'_stock_status',
					'_sold_individually'=>'_sold_individually',
					'_manage_stock'=>'_manage_stock',
					'_stock'=>'_stock',
					'_backorders'=>'_backorders',
					'evotx_price'=>'_regular_price',
					'_tx_desc'=>'_tx_desc',
					'_tx_text'=>'_tx_text',
					'_tx_subtiltle_text'=>'_tx_subtiltle_text',
					'_eventid'=>$post_id,
				);

				// when post array not existing
				$values = $_POST;
				if($method == 'ondemand'){
					$values = get_post_meta($post_id);
				}

				//$WC_product = wc_get_product($woo_post_id);

				foreach($update_metas as $umeta=>$umetav){
					if($umeta == '_regular_price' || $umeta == '_sale_price'|| $umeta == '_price'){

						if($umeta == '_regular_price'){
							if(empty($values[$umetav])) continue;
							$price = str_replace("$","",$values[$umetav]);
							update_post_meta($woo_post_id, $umeta,  $price);
							update_post_meta($woo_post_id, '_price', $price );
						}elseif($umeta == '_sale_price'){
							if(empty($values[$umetav])){
								delete_post_meta($woo_post_id, '_sale_price');
							}else{
								$price = str_replace("$","",$values[$umetav]);
								update_post_meta($woo_post_id, $umeta,  $price);
								update_post_meta($woo_post_id, '_price', $price );
							}
							
						}else{
							if(empty($values[$umetav])) continue;
							update_post_meta($woo_post_id, $umeta, str_replace("$","",$values[$umetav]) );
						}
					}else if($umeta == '_eventid'){
						update_post_meta($woo_post_id, $umeta, $post_id);
					}else if($umeta == '_virtual'){
						update_post_meta($woo_post_id, $umeta, $umetav);
					}else if($umeta == 'evotx_price'){
						$__price = (!empty($values[$umetav]))? $values[$umetav]: ' ';
						update_post_meta($post_id, $umeta, $__price);
					}else if($umeta == '_stock_status'){

						$_stock_status = (!empty($values[$umetav]) && $values[$umetav]=='yes')? 
							'outofstock': 'instock';
						update_post_meta( $woo_post_id, '_stock_status', $_stock_status);	

					}else if($umeta == '_sku'){

						// if no sku provided generate random number for sku
						$sku = (!empty($values[$umetav]))? 
							str_replace(' ', '', $values[$umetav])
							: 'sku_'.rand(2000,4000);
						update_post_meta($woo_post_id, $umeta, $sku);
					}else{
						if(isset($values[$umetav]))
							update_post_meta($woo_post_id, $umeta, $values[$umetav]);
					}
				}

				// visibility
					update_post_meta( $woo_post_id, 'catalog_visibility', 'hidden');				

				// save event image as WC product ft image
					if(isset($values['_tix_image_id'])){
						set_post_thumbnail($woo_post_id, $values['_tix_image_id']);
					}


			}
		// create and assign woocommerce product category for foodpress items
			function assign_woo_cat($post_id){

				// check if term exist
				$terms = term_exists('Ticket', 'product_cat');
				if(!empty($terms) && $terms !== 0 && $terms !== null){
					wp_set_post_terms( $post_id, $terms, 'product_cat' );
				}else{
					// create term
					$new_termid = wp_insert_term(
					  	'Ticket', // the term 
					  	'product_cat',
					  	array(	'slug'=>'ticket')
					);

					// assign term to woo product
					wp_set_post_terms( $post_id, $new_termid, 'product_cat' );
				}				
			}

// Event Ticket POST

	// FOR an EVENT: return customer tickets array by event id and product id
	// Main function to get all attendees 
	// @ will be deprecating
	// @updated 1.7
		function get_customer_ticket_list($event_id, $wcid='', $ri='', $sep_by= 'event_time', $entries = -1, $WP_Arg=''){
			global $post;
			$existing_post = $post;

			$customer_ = array();

			$e_pmv = get_post_custom($event_id);
			if(empty($wcid)) 
				$wcid = (!empty($e_pmv['tx_woocommerce_product_id']))? $e_pmv['tx_woocommerce_product_id'][0]:null;

			if(!$wcid) return false;
			
			$w_pmv = get_post_custom($wcid);
			$ri_count_active = $this->is_ri_count_active($e_pmv, $w_pmv);

			// get all ticket items matching product id and event id			
			if(empty($WP_Arg)){
				// Meta query
					if(empty($wcid)){
						$meta_query = array(
							array('key' => '_eventid','value' => $event_id,'compare' => '=')
						);
					}else{
						$meta_query = array(
							'relation' => 'AND',
							array('key' => 'wcid','value' => $wcid,'compare' => '='),
							array('key' => '_eventid','value' => $event_id,'compare' => '='),
						);
					}

				// Separate output by order status default values
					if($sep_by=='order_status'){
						$customer_= array('completed'=> 0, 'pending'=>0,'refunded'=>0,'total'=>0,'checked'=>0);
					}
				//print_r($meta_query);
				
				$wp_arg = array(
					'posts_per_page'=> $entries,
					'post_type'=>'evo-tix',
					'meta_query' => $meta_query
				);
			}else{
				$wp_arg  = $WP_Arg;
			}

			$ticketItems = new WP_Query($wp_arg);

			if($ticketItems->have_posts()):
				while($ticketItems->have_posts()): $ticketItems->the_post();
					$tiid = $ticketItems->post->ID;
					$tii_meta = get_post_custom($tiid);

					$order_id = !empty($tii_meta['_orderid'])? $tii_meta['_orderid'][0]: false;
					$orderOK = false; 
					$order_status = $billing_address = $phone = $company = 'n/a';

					if( get_post_status($order_id) === false) continue;				

					if(
						(
							$ri_count_active && 
							((!empty($tii_meta['repeat_interval']) && $tii_meta['repeat_interval'][0]==$ri)
								|| ( empty($tii_meta['repeat_interval']) && $ri==0)
							)
						)
						|| !$ri_count_active 
						|| $ri=='all'
					){

						$evotx_tix = new evotx_tix();

						// return data arranged based on order status
						if($sep_by=='order_status'){
							if(!$order_id) continue;

							$order = new WC_Order( $order_id );
							$order_status = $order->get_status();

							$order_status = (in_array($order_status, array('on-hold','processing') )?'pending': $order_status);
							$customer_[$order_status] = (!empty($customer_[$order_status])? 
								$customer_[$order_status]+$tii_meta['qty'][0]: $tii_meta['qty'][0]);

							// checked tickets value

								$st_count = $evotx_tix->checked_count($ticketItems->post->ID);
								
								if( !empty($st_count['checked']) ){
									$customer_['checked'] = $customer_['checked'] + $st_count['checked'];
								}

							$customer_['total'] = !empty($customer_['total'])? $customer_['total']+$tii_meta['qty'][0]: $tii_meta['qty'][0];

						}elseif($sep_by=='customer_order_status'){
							if(!$order_id) continue;

							$order = new WC_Order( $order_id );
							$order_status = $order->get_status();

							$order_status = (in_array($order_status, array('on-hold','processing') )?'pending': $order_status);
							
							// Get ticket numbers for the post
							$ticketids = $evotx_tix->get_ticket_numbers_by_evotix($ticketItems->post->ID);
							
							$order_ticket_holders = get_post_meta($order_id, '_tixholders', true);
							if(!empty($order_ticket_holders))
								$order_ticket_holders = $this->get_ticketholder_names($event_id, $order_ticket_holders);

							// tickets
								$tix = array();
								$uu = 0;
								foreach($ticketids as $tixnumber=>$status){
									$tix[$tixnumber] = array(
										'status'=>$status,
										'name'=> isset($order_ticket_holders[$uu])? $order_ticket_holders[$uu]:''
									);	
									$uu++;
								}

							$customer_[$order_status][$tiid] = array(
								'name'=>$tii_meta['name'][0],
								'tiid'=>$tiid,
								'tids'=>$ticketids,
								'tickets'=>$tix,
								'email'=>$tii_meta['email'][0],
								'type'=>$tii_meta['type'][0],					
								'qty'=>$tii_meta['qty'][0],		
							);

						}else{// seprate by event time
						
							if($order_id){

								$order = new WC_Order( $order_id );
								$order_status = $order->get_status();
								$orderOK = ($order_status=='completed')? true:false;
								$billing_address = '"'.$order->get_billing_address_1().' '.
									$order->get_billing_address_2().' '.
									$order->get_billing_city().' '.
									$order->get_billing_state().' '.
									$order->get_billing_postcode().' '.
									$order->get_billing_country().'"';
								$phone = $order->get_billing_phone();
								$company = $order->get_billing_company();
							}

							// event time for the ticket
							$event_time = $this->get_event_time($e_pmv, (!empty($tii_meta['repeat_interval'])? $tii_meta['repeat_interval'][0]:0));
							$event_time = $event_time;

							// get ticket numbers
							$ticketids = $evotx_tix->get_ticket_numbers_by_evotix($ticketItems->post->ID);

							// tickets			
								$TA = new EVOTX_Attendees();
								$order_ticket_holders = $TA->_get_tickets_for_order($order_id);
								

							$customer_[$event_time][$tiid] = array(
								'name'=>$tii_meta['name'][0],
								'tiid'=>$tiid,
								'tids'=>$ticketids,
								'tickets'=> (isset($order_ticket_holders[$event_id])? $order_ticket_holders[$event_id]:array()),
								'email'=>$tii_meta['email'][0],						
								'type'=>$tii_meta['type'][0],					
								'qty'=>$tii_meta['qty'][0],
								'order_status' =>	$order_status,
								'company'	=>$company,
								'address'=>$billing_address	,
								'phone'=>$phone,
								'postdata'=>get_the_date('Y-m-d'),
								'orderid'=>(!empty($order_id)? $order_id:'')
							);
						}
					}
				endwhile;
				wp_reset_postdata();
			endif;

			// reset wp query to existing post
				if($existing_post){
					$GLOBALS['post'] = $existing_post;
					setup_postdata($existing_post);
				}

			return (count($customer_)>0)? $customer_: false;
		}

	// EVENT TIMES
		function get_event_time($event_pmv='', $repeat_interval=0, $event_id=''){
			$event_pmv = (!empty($event_pmv))? $event_pmv : 
				(!empty($event_id)? get_post_custom($event_id): false );

			$datetime = new evo_datetime();

			// get unix start and end for correct interval
			$unixtime = $datetime->get_correct_event_repeat_time($event_pmv, $repeat_interval);
			return $datetime->get_formatted_smart_time($unixtime['start'], $unixtime['end'],$event_pmv);

			//return $datetime->get_correct_formatted_event_repeat_time($event_pmv,$repeat_interval );
			// return array(start, end)
		}
		function get_unix_times($epmv, $ri=0){
			$datetime = new evo_datetime();
			return $datetime->get_correct_event_repeat_time($epmv,$ri );
		}
		// return true if the event is a current event and not a past event
		// @depreacted in event class
		function is_currentEvent($eventPMV,$ri=0, $cutoff = 'end'){
			date_default_timezone_set('UTC');	
			$current_time = current_time('timestamp');
			$evodate = new evo_datetime();
			$event_time = $evodate->get_int_correct_event_time($eventPMV,$ri,$cutoff);
			return $event_time>$current_time? true: false;
		}

	// CHECK functions
		// show remaining stock or not
		// @deprecating
			function show_remaining_stock($epmv, $woometa){
				$_show_remain_tix = evo_check_yn($epmv, '_show_remain_tix');
				if(!$_show_remain_tix) return false;

				$_manage_stock = evo_check_yn($woometa, '_manage_stock');

				return ($_show_remain_tix && !$_manage_stock)? false: true;

			}
		// whos coming
			function show_whoscoming($event_pmv){				
				return (!empty($event_pmv['_tx_show_guest_list'])
					&& $event_pmv['_tx_show_guest_list'][0] == 'yes')? true:false;
			}
		// check if repeat interval is activate
		// @deprecated
			function is_ri_count_active($event_pmv, $woometa=''){
				 return (
					!empty($woometa['_manage_stock']) && $woometa['_manage_stock'][0]=='yes'
					&& !empty($event_pmv['_manage_repeat_cap']) && $event_pmv['_manage_repeat_cap'][0]=='yes'
					&& !empty($event_pmv['evcal_repeat']) && $event_pmv['evcal_repeat'][0] == 'yes' 
					&& !empty($event_pmv['ri_capacity']) 
				)? true:false;
			}
			
		// check if event have ticket left
			function event_has_tickets($eventPMV, $woometa, $repeat_interval=0){

				// check if tickets are enabled for the event
					if(!evo_check_yn($eventPMV, 'evotx_tix')) return false;

				// if tickets set to out of stock 
					if(!empty($woometa['_stock_status']) && $woometa['_stock_status'][0]=='outofstock') return false;
				
				// if manage capacity separate for Repeats
				$ri_count_active = $this->is_ri_count_active($eventPMV, $woometa);
				if($ri_count_active){
					$ri_capacity = unserialize($eventPMV['ri_capacity'][0]);
						$capacity_of_this_repeat = 
							(isset($ri_capacity[ $repeat_interval ]) )? 
								$ri_capacity[ $repeat_interval ]
								:0;
						return ($capacity_of_this_repeat==0)? false : $capacity_of_this_repeat;
				}else{
					// check if overall capacity for ticket is more than 0
					$manage_stock = (!empty($woometa['_manage_stock']) && $woometa['_manage_stock'][0]=='yes')? true:false;
					$stock_count = (!empty($woometa['_stock']) && $woometa['_stock'][0]>0)? $woometa['_stock'][0]: false;
					
					// return correct
					if($manage_stock && !$stock_count){
						return false;
					}elseif($manage_stock && $stock_count){	return $stock_count;
					}elseif(!$manage_stock){ return true;}
				}
			}

		// check if the event tickets is set to stop selling X minuted before it closes
		// @deprecating in class event tickets
			function stop_selling_now($eventPMV,$ri=0){

				if(!empty($eventPMV['_xmin_stopsell']) ){
					//date_default_timezone_set('UTC');	
					$current_time = current_time('timestamp');
					$evodate = new evo_datetime();
					
					$event_start_time = $evodate->get_int_correct_event_time($eventPMV,$ri,'start');

					$timeBefore = (int)($eventPMV['_xmin_stopsell'][0])*60;	

					$cutoffTime = $event_start_time -$timeBefore;

					// /echo date('m-d h:i',$cutoffTime).' '.date('m-d h:i',$current_time);

					return ($cutoffTime < $current_time)? true: false;
				}else{
					return false;
				}
			}
		// get event tickets remaining tickets in stock
			function get_tix_instock($woometa){
				if(!empty($woometa['_manage_stock']) && $woometa['_manage_stock'][0]=='yes'){
					return !empty($woometa['_stock'])? $woometa['_stock'][0]: false;
				}else{ return false;}
			}

// GETTING
	function get_event_cpt_meta_fields(){
		return array('_tx_img_text',
				'evotx_tix', 
				'_show_remain_tix', 
				'remaining_count', 
				'_manage_repeat_cap', 
				'_tix_image_id', 
				'_allow_inquire',
				'_tx_inq_email',
				'_tx_inq_subject',
				'_xmin_stopsell',
				'_tx_show_guest_list');
	}
	
// SUPPORTIVE
		function get_author_id() {
			$current_user = wp_get_current_user();
	        return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
	    }	
	    function get_event_post_date() {
	        return date('Y-m-d H:i:s', time());        
	    }

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

	    // update capacity of repeat instance
			function update_repeat_capacity($adjust, $ri, $eid, $epmv=''){
				if(empty($epmv)) $epmv = get_post_custom($eid);

				if(	evo_check_yn($epmv,'_manage_repeat_cap') &&
					evo_check_yn($epmv,'evcal_repeat') &&
					!empty($epmv['repeat_intervals']) && 
					!empty($epmv['ri_capacity'])
				){
					
					// repeat capacity values for this event
					$ri_capacity = unserialize($epmv['ri_capacity'][0]);

					// repeat capacity for this repeat  interval
					$capacity_for_this_event = $ri_capacity[$ri];
					$new_capacity = $capacity_for_this_event + ( (int)$adjust );

					$ri_capacity[$ri] = ($new_capacity>=0)? $new_capacity:0;

					// save the adjusted repeat capacity
					update_post_meta($eid, 'ri_capacity',$ri_capacity);
					return true;
				}else{ return false;}
			}

	    // get product title
	    function get_ticket_product_title($post_id=''){

	    	$post = get_post($post_id);
	    	$_date_addition = '';

	    	// wc prodduct name structure
	    	$structure = !empty(EVOTX()->evotx_opt['evotx_wc_prodname_structure'])? EVOTX()->evotx_opt['evotx_wc_prodname_structure']: "Ticket: {event_name} {event_start_date} - {event_end_date}";

	    	$__sku = !empty($_REQUEST['_sku'])? '('. $_REQUEST['_sku'].') ':'';
	    	$structure = str_replace('{sku}', $__sku, $structure);

	    	$structure = str_replace('{event_name}', $post->post_title, $structure);

	    	$event_start = !empty($_POST['evcal_start_date'])? $_POST['evcal_start_date']:'';
	    	if(empty($event_start) && !empty($_POST['event_start_date_x'])) 
	    		$event_start = $_POST['event_start_date_x'];
	    	$structure = str_replace('{event_start_date}', $event_start, $structure);

	    	$event_end = !empty($_POST['evcal_end_date'])? $_POST['evcal_end_date']:'';
	    	if(empty($event_end) && !empty($_POST['event_end_date_x'])) 
	    		$event_start = $_POST['event_end_date_x'];
	    	$structure = str_replace('{event_end_date}', $event_end, $structure);
				
			return $structure;
	    }
}