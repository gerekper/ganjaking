<?php
/** 
 * EVOST - ajax
 * @version 1.2.1
 */
class EVOST_ajax{
	public function __construct(){
		$ajax_events = array(
			'evost_get_seats_data'=>'evost_get_seats_data',
			'evost_refresh_seat_map'=>'refresh_seat_map',
			'evost_seat_cart_preview'=>'evost_seat_cart_preview',			
			'evost_seat_direct_add_cart'=>'direct_add_to_cart',			
			'evost_remove_seat_from_cart'=>'evost_remove_seat_from_cart',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evotx_helper();
		$this->postdata = $this->help->sanitize_array($_POST);
	}

	// Seats data
		function evost_get_seats_data(){
			if( !isset($this->postdata['eid'])){ 
				echo json_encode(array('status'=>'bad', 'content'=>'Missing Data')); exit; 
			}

			extract($this->postdata);

			$SEATS = new EVOST_Seats_Json($eid,$wcid, $ri);

			$out = array(		
				'status'=>'good',		
				's' => 		$SEATS->get_seat_settings(),
				'j'=> 		$SEATS->__j_get_all_sections( false, true),
				'j_cart'=> 	$SEATS->_get_cart_seats_for_events(),
				'view'=>	$SEATS->get_frontend_seats_view( $eid, $wcid )		
			);

			if( !empty($l)) evo_set_global_lang( $l);

			// append ticekt structure for lightbox
			if( !empty($type) && $type == 'lb'){

				ob_start();
				?>
				<div class='evotx_ticket_purchase_section evo_runningajax'>
					<div class="evost_seat_map_section">
						<?php echo EVOST()->frontend->print_init_html_content();?>
					</div>
					<?php 
					echo EVOTX()->frontend->print_ticket_footer_content( $SEATS->event );
					?>
				</div>
				<?php 
				$re = ob_get_clean();

				$out['structure'] = $re;
			}

			wp_send_json( $out );
		}

	//direct add to cart
		function direct_add_to_cart(){
			$output = array('status'=>'good', 'msg'=>'');
			
			extract($this->postdata);
			extract($event_data);

			$Helper = new evotx_helper();
			$ST = new EVOST_Seats_Seat($eid, $wcid, $seat_slug);

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

			$event_data['qty'] = 1;

			$TIX = new evotx_event($eid, '', 0, $wcid);

			$add_to_cart = $TIX->add_ticket_to_cart($data);

			if($add_to_cart)
				echo $add_to_cart; exit;
			
		}

	// Preview seat data before addin to cart
		function evost_seat_cart_preview(){
			$output = array('status'=>'good', 'msg'=>'');

			extract($this->postdata);

			// validation
			if( !isset($event_data['eid'])){
				$output['status'] = 'bad';
				$output['msg'] = 'Missing Event ID';
				echo json_encode($output); exit;
			}

			$Helper = $this->help;

			extract($event_data);
			
			$ST = new EVOST_Seats_Seat($eid, $wcid, $seat_slug);
			$ST2 = new EVOST_Seats_Json($eid, $wcid);

			// process by seat type
				$ST->_localize_seat_slug($seat_slug);				

			$output['j'] = $ST2->__j_get_all_sections();

			// validate if at lease 1 seat available for sale
				$can_add = $ST->is_seat_available(1);
			 
			
			if(!$can_add){
				$output['status'] = 'bad';				
				$output['msg'] = evo_lang('Seat not available at the moment');
				echo json_encode($output); exit;
			}

			ob_start();

			// set evo lang for ajax instance
				if(isset($event_data['l']))	EVO()->lang = $event_data['l'];

			?><p class="evost_tix_title"><?php evo_lang_e('Preview Seat');?></p><?php

			// pluggability 
				$plug = apply_filters('evost_seat_preview', false, $ST);
				if($plug) echo $plug;


			// Ticket Meta data - seat information
				if($ST->seat_type=='seat'){
					$Helper->custom_item_meta( evo_lang('Seat'), $ST->get_item_prop('number'));
				}else{
					$Helper->custom_item_meta( evo_lang('Seat'), $ST->get_item_prop('section_name') .' #'. $seat_slug);

					if( $ST->seat_type == 'unaseat'){
						$Helper->custom_item_meta( evo_lang('Seat Type'), evo_lang('Unassigned Seating'));
					}
					if( $ST->seat_type == 'booseat'){
						$Helper->custom_item_meta( evo_lang('Seat Type'), evo_lang('Booth'));
					}					
				}	



			// base price for seat
				$seat_price = apply_filters('evost_seat_base_price',  $ST->get_price(), $ST);
				$Helper->base_price_html( $seat_price );		

			// seat add to cart and cancel button
				echo "<div class='evotx_add_to_cart_bottom'>";

					// ticket quantity fields
					if($ST->seat_type=='seat'){
						$Helper->ticket_qty_one_hidden(  );
					}else{
						$Helper->ticket_qty_html( $ST->get_max_capacity() );
					}

					// other inclusions
					do_action('evost_seat_prev_before_total_price', $ST);

					$Helper->total_price_html( $seat_price ,'evost_total_price' );
					$Helper->add_to_cart_btn_html( 'evotx_addtocart', array(), array(
						'name'=> __('Cancel Seat'),
						'class'=>'evcal_btn evost_cancel_seat_preview',
						'data'=> array('type'=> $type, 'slug'=> $seat_slug)
					));	

				echo "</div>";


			// print evotx_other_data 
				echo "<div class='evotx_addon_data'>";
				$Helper->print_select_data_element( array(
					'class'=>'evotx_other_data evost',
					'data'=> array(
						'seat_slug'=> $seat_slug,
						'seat_number'=> $ST->get_seat_number(),
						'seat_type'=> $type
					)
				) );
				echo "</div>";

			// update tx_data values
				$event_data['seat_slug'] = $seat_slug;
				$event_data['seat_number'] = $ST->get_seat_number();
				$event_data['seat_type'] = $type;

			$output['view'] = ob_get_clean();
			echo json_encode($output); exit;		
		}
	
	// remove the selected seat from cart
		function evost_remove_seat_from_cart(){
			$status = true;

			// remove seat ticket from wc cart
			$removed = WC()->cart->remove_cart_item($this->postdata['key']);

			// make seat available
			if( isset($this->postdata['seat_slug']) && isset($this->postdata['event_data']) ){
				extract( $this->postdata['event_data']);
				$SEAT = new EVOST_Seats_Seat($eid, $wcid, $this->postdata['seat_slug']);
				$SEAT->make_available();
			}
			
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