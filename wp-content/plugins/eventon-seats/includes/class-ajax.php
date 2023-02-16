<?php
/** 
 * EVOST - ajax
 * @version 0.1
 */
class EVOST_ajax{
	public function __construct(){
		$ajax_events = array(
			'evost_get_seats_data'=>'evost_get_seats_data',
			'evost_get_seat_layout'=>'evost_get_seat_layout',
			'evost_refresh_seat_map'=>'refresh_seat_map',
			'evost_seat_cart_preview'=>'evost_seat_cart_preview',			
			'evost_seat_direct_add_cart'=>'direct_add_to_cart',			
			'evost_remove_seat_from_cart'=>'evost_remove_seat_from_cart',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// Seats data
		function evost_get_seats_data(){
			if( empty($_POST['event_id'])){ echo json_encode(array('status'=>'bad', 'content'=>'Missing Data')); exit; }

			$SEATS = new EVOST_Seats_Json($_POST['event_id'],$_POST['wcid']);

			echo json_encode(array(		
				'status'=>'good',		
				's' => $SEATS->get_seat_settings(),
				'j'=> $SEATS->__j_get_all_sections(),
				'j_cart'=> $SEATS->_get_cart_seats_for_events(),
				'view'=>	$SEATS->get_frontend_seats_view($_POST['event_id'], $_POST['wcid'])		
			));
			exit;
		}


	//direct add to cart
		function direct_add_to_cart(){
			$output = array('status'=>'good', 'msg'=>'');
			extract($_POST);

			$Helper = new evotx_helper();
			$ST = new EVOST_Seats_Seat($eventid, $product_id, $seat_slug);

			// no direct add to cart for unassigned seating
				if($ST->seat_type !='seat'){
					$output['status'] = 'bad';				
					$output['msg'] = evo_lang('Cannot add unassigned seats direct to cart');
					echo json_encode($output); exit;
				}
			// based on seat type
				$can_add = $ST->is_seat_available(1);
				
			if(!$can_add){
				$output['status'] = 'bad';				
				$output['msg'] = evo_lang('Seat not available at the moment');
				echo json_encode($output); exit;
			}

			// evotx_data values
				$evotx_data = array();

				$evotx_data['event_data']['eid'] = $eventid;
				$evotx_data['event_data']['wcid'] = $product_id;
				$evotx_data['event_data']['l'] = EVO()->lang;
				$evotx_data['event_data']['seat_slug'] = $seat_slug;
				$evotx_data['event_data']['seat_number'] = $ST->get_seat_number();
				$evotx_data['event_data']['seat_type'] = $type;
				$evotx_data['msg_interaction']['redirect'] = 'nonemore';

			$DATA = $Helper->get_add_to_cart_evotx_data_ar($evotx_data);

			$DATA['qty'] = 1;

			$TIX = new evotx_event($eventid, '', 0, $product_id);

			$add_to_cart = $TIX->add_ticket_to_cart($DATA);

			if($add_to_cart)
				echo $add_to_cart; exit;
			
		}

	// Preview seat data before addin to cart
		function evost_seat_cart_preview(){
			$output = array('status'=>'good', 'msg'=>'');

			extract($_POST);

			// validation
			if( empty($eventid)){
				$output['status'] = 'bad';
				$output['msg'] = 'Missing Event ID';
				echo json_encode($output); exit;
			}

			$Helper = new evotx_helper();
			
			$ST = new EVOST_Seats_Seat($eventid, $product_id, $seat_slug);
			$ST2 = new EVOST_Seats_Json($eventid, $product_id);

			// process by seat type
				$ST->_localize_seat_slug($seat_slug);				

			$output['j'] = $ST2->__j_get_all_sections();

			$can_add = $ST->is_seat_available(1); // *** need checking qty for unaseat
			
			if(!$can_add){
				$output['status'] = 'bad';				
				$output['msg'] = evo_lang('Seat not available at the moment');
				echo json_encode($output); exit;
			}

			ob_start();

			// set evo lang for ajax instance
				if(isset($data['event_data']['l'])){
					EVO()->lang = $data['event_data']['l'];
				}


			// evotx_data values
				$evotx_data = array();

				$evotx_data['event_data']['eid'] = $eventid;
				$evotx_data['event_data']['wcid'] = $product_id;
				$evotx_data['event_data']['l'] = EVO()->lang;
				$evotx_data['event_data']['seat_slug'] = $seat_slug;
				$evotx_data['event_data']['seat_number'] = $ST->get_seat_number();
				$evotx_data['event_data']['seat_type'] = $type;
				$evotx_data['msg_interaction']['redirect'] = 'nonemore';

			?><p class="evost_tix_title"><?php evo_lang_e('Preview Seat');?></p><?php

			// pluggability 
				$plug = apply_filters('evost_seat_preview', false, $ST);
				if($plug) echo $plug;

			// base price for seat
				$seat_price = apply_filters('evost_seat_base_price',  $ST->get_price(), $ST);

			
			$Helper->base_price_html( $seat_price );

			// show seat information
				if($ST->seat_type=='seat'){
					$Helper->custom_item_meta( evo_lang('Seat'), $ST->get_item_prop('number'));
				}else{
					$Helper->custom_item_meta( evo_lang('Seat'), $ST->get_item_prop('section_name'));
				}			

			echo "<div class='evotx_add_to_cart_bottom'>";

				if($ST->seat_type=='seat'){
					$Helper->ticket_qty_one_hidden(  );
				}else{
					$Helper->ticket_qty_html( $ST->get_max_capacity() );
				}

				$Helper->total_price_html( $seat_price ,'evost_total_price' );
				$Helper->add_to_cart_btn_html( 'evotx_addtocart');				
				//$Helper->print_add_to_cart_data($evotx_data);

			echo "</div>";

			$output['event_data'] = $Helper->get_add_to_cart_evotx_data_ar($evotx_data);
			$output['view'] = ob_get_clean();
			echo json_encode($output); exit;		
		}
	
	// remove the selected seat from cart
		function evost_remove_seat_from_cart(){
			$status = true;

			$removed = WC()->cart->remove_cart_item($_POST['key']);
			
			if($removed){			
				$msg = __('Seat successfully removed from cart!','eventon');
			}else{
				$msg = __('Seat could not be removed from cart, please try later!','eventon');
				$status = false;
			}	
			
			echo json_encode(array(
				'status'=> ($status?'good':'bad'),
				'message'=> (!empty($msg)? $msg: null)
			));
			exit;
		}

	// refresh the seat map
		function refresh_seat_map(){
			$SEAT = new EVOST_Seats_Json( $_POST['eventid'], $_POST['wcid']);  
			echo json_encode(array(
				'j'=> $SEAT->__j_get_all_sections(),
				'j_cart'=> $SEAT->_get_cart_seats_for_events(false),
			));
			exit;
		}
	
}
new EVOST_ajax();