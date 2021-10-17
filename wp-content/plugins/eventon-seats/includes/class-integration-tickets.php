<?php
/**
 * Event seats tickets addon integration
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON-st/classes
 * @version     1.0
 */
class evost_tickets{
	
	function __construct(){

		// frontend
		add_filter('evotx_single_product_temp', array($this, 'frontend_temp'), 10,2);
		add_action('woocommerce_before_cart', array($this, 'before_cart'), 10);
		add_action('woocommerce_before_checkout_form', array($this, 'before_cart'), 10);

		add_action('eventontx_tix_post_table', array($this, 'evo_tix_table_row'), 10, 4);
		add_action('evotix_confirmation_email_data', array($this, 'seat_info_tix_confirmation_email'), 10, 6);

		add_filter('evotx_get_attendees_for_event', array($this, 'seats_on_attendees'), 10, 2);
		
		// adding to cart AJAX
		add_filter('evotx_add_ticket_to_cart_before', array($this, 'before_adding_to_cart'), 10, 3);
		add_filter('evotx_add_cart_item_meta', array($this, 'adding_to_cart'), 10, 4);
		add_filter('evotx_ticket_added_cart_ajax_data', array($this, 'ajax_return'), 10, 3);
		add_filter('evotx_cart_session_item_values', array($this,'cart_session_item_data'), 10, 1 );
		add_filter('evotx_ticket_item_price_for_cart', array($this, 'cart_ticket_price'),10,4);
		
		
		// after added to cart
		add_action('evotx_after_ticket_added_to_cart', array($this, 'after_added'), 10, 3);
		add_action('evotx_cart_tickets_updated', array($this, 'cart_seats_updated'), 10, 2);

		// Cart view
		add_filter('evotx_ticket_item_meta_data',array($this,'cart_ticket_meta_data'),1,3);
		add_filter('evotx_cart_item_quantity',array($this,'cart_item_quantity'),1,4);
		add_action('evotix_cart_item_validation', array($this, 'cart_validation'), 10, 3);

		// checkout
		add_action('evotx_cart_ticket_removed',array($this,'update_removed_cart_items'),1,2);
		add_filter('evotx_checkout_addnames_other_vars', array( $this, 'add_ticket_infor_adds'),10,3);

		
		add_action('evotx_checkout_create_order_line_item',array($this,'order_item_meta_update_new'),1,4);
		add_filter('evotx_adjust_orderitem_ticket_stockother', array($this, 'adjust_ticket_item_stock'), 10, 7);
		add_filter('evotx_order_item_meta_slug_replace', array($this, 'ticket_meta_slug_replace'),10,1);
		add_filter('evotx_tix_save_field_meta', array( $this, 'tix_meta_values'),10,2);

		// hidden meta fields from backend
		add_filter('evotx_hidden_order_itemmeta', array($this,'hide_order_item_metafields'),10,1);

	}

	// FRONTEND
		function before_cart(){

			$OPT = EVOST()->opt;
			$hide_cart_timer = !empty($OPT['_evost_hide_cart_exp']) && $OPT['_evost_hide_cart_exp']=='yes'? true: false;

			// if set to not show cart timer
			if($hide_cart_timer) return false;

			$items = WC()->cart->get_cart();
			if(!$items) return false;

			$cart_has_seat = false;
			$exp_time = 0;

			foreach($items as $cart_item_key=>$values){
				if(empty($values['evost_data'])) continue;
				if(isset($values['evost_data'])) $cart_has_seat = true;	

				$ST = new EVOST_Expirations($values['evotx_event_id_wc'], $values['product_id']);

				$exp_time = $ST->get_seat_expiration_time($values['evost_data']['seat_slug'], $cart_item_key);	
			}

			if(!$cart_has_seat) return false;

			$_exp_time = $exp_time - time();
			if($_exp_time<0) $_exp_time = 0;


			echo "<p class='evost_cart_timer' data-s='".$_exp_time."'>".evo_lang('Your seats will expire in').' <b>'. $ST->get_human_time( $_exp_time) .'</b></p>';
		}

		function frontend_temp($boolean, $event){

			$seats = new EVOST_Expirations($event->ID, $event->wcid);
			add_filter('evo_frontend_lightbox', array($this, 'lightbox'),10,1);
			
			if($seats->event->check_yn('_enable_seat_chart')  && $seats->event->get_prop('_evost_sections') && !$seats->event->is_repeating_event()){				
				// /print_r(get_post_meta(1,'aaX'));
				//print_r($seats->expirations);
				//print_r(get_option('_evost_expiration'));
				$output = $seats->frontend_init();
				return $output;
			}

			return $boolean;

		}
		function lightbox($array){
			$array['evost_lightbox']= array(
				'id'=>'evost_lightbox',
				'CLclosebtn'=> 'evost_lightbox',
				'CLin'=> 'evost_lightbox_body',
			);
			return $array;
		}

	// CART
		// verify before adding to cart
			function before_adding_to_cart($boolean, $EVENT, $DATA){
				if( !isset($DATA['event_data'])) return $boolean;
				if( !isset($DATA['event_data']['seat_slug'])) return $boolean;

				$SEATS = new EVOST_Seats_Seat($EVENT, $DATA['event_data']['wcid'], $DATA['event_data']['seat_slug']);

				if(!$SEATS->is_seat_available(1)){
					$output['status'] = 'bad';				
					$output['msg'] = 'Seat not available at the moment';
					return json_encode($output); 
				}
				return $boolean;
			}

		// adding seat data to cart
			function adding_to_cart($cart_item_data, $EVENT, $def_price, $DATA){
				if( !isset($DATA['event_data'])) return $cart_item_data;
				if( !isset($DATA['event_data']['seat_slug'])) return $cart_item_data;

				$cart_item_data['evost_data']['seat_slug'] = $DATA['event_data']['seat_slug'];
				$cart_item_data['evost_data']['seat_number'] = $DATA['event_data']['seat_number'];
				$cart_item_data['evost_data']['seat_type'] = $DATA['event_data']['seat_type'];
				//$cart_item_data['_evost_seat_slug'] = $DATA['event_data']['seat_slug'];
				//$cart_item_data['_evost_seat_number'] = $DATA['event_data']['seat_number'];
				//$cart_item_data['_evost_seat_type'] = $DATA['event_data']['seat_type'];

				$SEATS = new EVOST_Seats_Seat($EVENT, $DATA['event_data']['wcid'], $DATA['event_data']['seat_slug']);	

				$cart_item_data['_evost_seat_price'] = $SEATS->get_price();

				return $cart_item_data;
			}
			function cart_session_item_data($array){
				$array[] = 'evost_data';
				$array[] = '_evost_seat_price';
				return $array;
			}
			function cart_ticket_price($boolean, $def_price, $session_data, $values){
				if (array_key_exists( '_evost_seat_price', $values ) ){
					return $values['_evost_seat_price'];
		        }
		        return $boolean;
			}
		// ajax after addin to cart 
			function ajax_return($ajaxdata, $EVENT, $DATA){
				if( !isset($DATA['event_data'])) return $ajaxdata;
				if( !isset($DATA['event_data']['wcid'])) return $ajaxdata;
				if( !isset($DATA['event_data']['seat_slug'])) return $ajaxdata;

				$SEATS = new EVOST_Seats_Json($EVENT, $DATA['event_data']['wcid']);
				
				$ajaxdata['j'] = $SEATS->__j_get_all_sections();			
				$ajaxdata['j_cart'] = $SEATS->_get_cart_seats_for_events();				

				return $ajaxdata;
			}
		// after added to cart
			function after_added($cart_item_key, $EVENT, $DATA){
				if( !isset($DATA['event_data'])) return false;
				if( !isset($DATA['event_data']['wcid'])) return false;
				if( !isset($DATA['event_data']['seat_slug'])) return false;

				$SEATS = new EVOST_Seats_Seat($EVENT, $DATA['event_data']['wcid'], $DATA['event_data']['seat_slug']);

				// put seat on temp hold while the seat is in cart
				$SEATS->add_seat_temphold( $cart_item_key, $DATA['qty'], $SEATS->seat_slug);	

				// multiple seats adding to cart for unaseats
				$seat_type = $SEATS->_get_seat_type_by_slug( $SEATS->seat_slug );

				if($seat_type != 'seat'){
					// if seat qty was updated make those changes in expirations
					$SEATS->unaseat_set_cart_expirations( $cart_item_key, $SEATS->seat_slug, $DATA['cart_qty']);						
				}

				// update expirations for all existsing seats in cart
				$SEATS->reset_all_cart_seat_expirations();
			}

	// IN CART 
		function cart_seats_updated($cart_item_key, $cart_item){
			if(!isset($cart_item['evost_data'])) return;
			if(!isset($cart_item['evost_data']['seat_type']) && !isset($cart_item['evost_data']['seat_slug'])) return;

			$SEAT = new EVOST_Seats_Seat($cart_item['evotx_event_id_wc'], $cart_item['product_id'], $cart_item['evost_data']['seat_slug']);

			$SEAT->_get_seat_type_by_slug($cart_item['evost_data']['seat_slug']); 
			if($SEAT->seat_type == 'seat') return; // skip regular seats
			
			// update qty with unaseat values
			$SEAT->unaseat_match_cart_qty($cart_item_key, $cart_item['evost_data']['seat_slug'],$cart_item['quantity']);

			// update expirations qty
			$SEAT->unaseat_set_cart_expirations($cart_item_key, $cart_item['evost_data']['seat_slug'],$cart_item['quantity']);

		}

	// CART VIEW
		function cart_ticket_meta_data($data, $values, $EVENT){
			if(!isset($values['evost_data'])) return $data;
			if(isset($values['evost_data']['seat_slug'])){
				$add = (isset($values['evost_data']['seat_type']) && $values['evost_data']['seat_type']=='unaseat')? 
					evo_lang('Unassigned Seating at').' ':'';
				$data['evost_data'] = array(evo_lang('Seat'), $add . $values['evost_data']['seat_number']);
			}
			return $data;
		}

		function cart_item_quantity($boolean, $product, $cart_item_key, $cart_item ){
			if(!isset($cart_item['evost_data'])) return $boolean;
	   		if(empty($cart_item['_evost_seat_price']) ) return $boolean;

	   		$seat_slug = $cart_item['evost_data']['seat_slug'];
	   		
	   		$SEAT = new EVOST_Seats_Seat( $cart_item['evotx_event_id_wc'], $cart_item['product_id'], $seat_slug);

	   		//echo date('Y-m-d H:i',$SEAT->get_seat_expiration_time( $seat_slug, $cart_item_key) ).'</br> '.date('Y-m-d H:i',time());
	   		//delete_option('_evost_expiration');
	   		//print_r($SEAT->expirations);
	   		//print_r($cart_item);
	   		
	   		$product_quantity = woocommerce_quantity_input( array(
				'input_name'  => "cart[{$cart_item_key}][qty]",
				'input_value' => $cart_item['quantity'],
				'max_value'   => $SEAT->get_max_capacity(),
				'min_value'   => '0',
			), '', false );

			return $product_quantity;   		
	   	}

	   	// validate cart seats, for each cart item
	   	function cart_validation($cart_item_key, $cart_item, $event_id){
	   		if(!isset($cart_item['evost_data'])) return false;
			if(!isset($cart_item['evost_data']['seat_slug']) ) return false;

			$seat_slug = $cart_item['evost_data']['seat_slug'];
			$SEAT = new EVOST_Seats_Seat( $event_id, $cart_item['product_id'], $seat_slug);
				
			// if seat not available or ticket sale is not active
			$stock_available = $SEAT->is_seat_available(  $cart_item['quantity']);
			if( !$stock_available){
				WC()->cart->remove_cart_item($cart_item_key);
				wc_add_notice( 'Ticket removed from cart, seat no longer available for sale!', 'error' );
			}

			// check if seat time has expired
			$seat_expired =  $SEAT->has_seat_expired($cart_item_key,  $seat_slug);
			if($seat_expired){
				// restock the seat				
				WC()->cart->remove_cart_item($cart_item_key);
				wc_add_notice( 'Ticket removed from cart due to seat expiration time!', 'error' );
				$SEAT->restock_temphold_seat($cart_item_key, $cart_item['quantity'], $seat_slug);
			}else{
				// reset timer
				$SEAT->reset_expiration_time_from_cart($cart_item_key,$seat_slug, $cart_item['quantity']);
			}		
		}
	// CHECKOUT
		// add custom data as meta data to order item, when order is processed	    
			function order_item_meta_update_new($item, $cart_item_key, $values, $order){
				if(!isset($values['evost_data'])) return false;

				if(isset($values['evost_data']['seat_number']) ){	  
					// convert to a proper seat number
					$item->add_meta_data('Seat-Number', $values['evost_data']['seat_number'] ,true);
		        }		
		        if(isset($values['evost_data']['seat_slug']) )
		        	$item->add_meta_data('_evost_seat_slug', $values['evost_data']['seat_slug'],true);

			}

		// If item removed from cart
			function update_removed_cart_items($cart_item_key, $cart_session_data){	
				if(!isset($cart_session_data['evost_data'])) return false;
				if( !isset($cart_session_data['evost_data']['seat_slug'])) return false;
				
				$seat_slug = $cart_session_data['evost_data']['seat_slug'];

		        // ticket with seat removed from cart
		        $SEAT = new EVOST_Seats_Seat($cart_session_data['evotx_event_id_wc'], $cart_session_data['wcid'], $seat_slug);

		        $SEAT->restock_temphold_seat($cart_item_key, $cart_session_data['quantity'], $seat_slug);    
		    }
		// additional ticekt information at the checkout additions from VO
			function add_ticket_infor_adds( $O, $V, $EVENT){
				
				if(!isset($V['evost_data'])) return $O;
				if(!isset( $V['evost_data']['seat_number'] ) ) return $O;

				$O .= "<span style='display:block'><b>". evo_lang('Seat Number') .':</b> '.$V['evost_data']['seat_number'] ."</span>";		
				return $O;
			}

		// For each seat in the order
		// Adjust seat stock during checkout and for other order statuses eg. cancel order, completed order etc.
		function adjust_ticket_item_stock($boolean, $TIX_EVENT, $order, $item_id, $item, $type, $stage){
			
    		$seat_slug = wc_get_order_item_meta($item_id ,'_evost_seat_slug'); 

    		if(!$seat_slug) return $boolean;

    		$SEAT = new EVOST_Seats_Seat($TIX_EVENT, $item['product_id'], $seat_slug);
    		
    		// delete seat expiration data
    		$SEAT->delete_seat_expiration($seat_slug);

    		if($SEAT->seat_type == 'seat'){
    			$SEAT->adjust_stock($type); // make seat uva

    			// order notes
    			if($type == 'reduce'){

    				// checkout on the cart
    				if( $stage == 'cart'){
    					if($SEAT->is_seat_slug_exists()){
    						$order->update_status('pending','Seat already purchased by another customer');
    					}
    				}

	    			$order->add_order_note( sprintf( 
						__( 'Event: (%s) seat %s made unavailable.', 'woocommerce' ), 
						$TIX_EVENT->get_title(), $SEAT->get_seat_number()) 
	    			);
	    		// increase
	    		}else{
	    			$order->add_order_note( sprintf( 
						__( 'Event: (%s) seat %s made available.', 'woocommerce' ), 
						$TIX_EVENT->get_title(), $SEAT->get_seat_number()) );
	    		}
    			
    		}else{
    			// UNA is already reduced when added to cart
    			// if restock then 
    			if($type != 'reduce') 
    				$SEAT->una_restock_seat($item['qty']); // restock 
    		}
    		
    		return $boolean;   			    	
		}

		function ticket_meta_slug_replace($array){
			$array['Seat-Number'] = evo_lang('Seat Number');
			return $array;
		}

		// save ticket item meta custom values for faster retrieve of data
		// Save values for "evo-tix" CPT
		function tix_meta_values($array, $item){
			if(!empty($item['Seat-Number'])) $array['_seat_number'] = $item['Seat-Number'];
			if(!empty($item['Seat-Number'])) $array['Seat-Number'] = $item['Seat-Number']; // before v1.0
			if(!empty($item['_evost_seat_slug'])) $array['_evost_seat_slug'] = $item['_evost_seat_slug'];
			// seat slug is saved as "seat_id" in version < 1.0
			
			return $array;
		}

// OTHER
	// order item meta fields that are hidden from view
		function hide_order_item_metafields($array){
			$array[] = '_evost_seat_slug';
			return $array;
		}

	// show seat information on attendee information
		function seats_on_attendees($array, $event_id){
			if(!isset($array['id'])) return $array;
			$evo_tix_id = $array['id'];

			$seat_num = get_post_meta($evo_tix_id, 'Seat-Number',true);
			if( !$seat_num) return $array;

			$array['oD']['seat_number'] = $seat_num;
			$array['oDD']['seat_slug'] = get_post_meta($evo_tix_id,'_evost_seat_slug',true);

			return $array;
		}

	// show ticket seat number information for evo-tix cpt
	function evo_tix_table_row($post_id, $ticketItem_meta, $event_id, $ET){
		if(!empty($ticketItem_meta['Seat-Number'])): ?>
		
		<?php
			/*
			$EVENT = new EVO_Event( $event_id);

			$wcid = $ET->get_prop('wcid');

			$SEATS = new EVOST_Seats_Json($event_id,$wcid);

			$seat_slug = $ET->get_prop('_evost_seat_slug');
			$ssl = explode('-', $seat_slug);

			foreach($SEATS->seats_data as $section=>$sd){
				if( $sd['type'] == 'una') continue;


			}

			print_r($ticketItem_meta);

			print_r($SEATS->seats_data);
			*/
		?>
		<tr><td><?php _e('Seat Number','eventon');?>: </td>
		<td><?php echo $ticketItem_meta['Seat-Number'][0];?></td></tr>

		<?php endif;
	}

	// Show seat number information for confirmation ticket email
	function seat_info_tix_confirmation_email($ticket_item_id, $ticket_pmv, $styles, $ticket_number, $tix_holder_index,	$event_id){

		// check if seat number is present in evo-tix
		if(!empty($ticket_pmv['Seat-Number']) ):

			$evotx_tix = new evotx_tix();
			$product_id = $evotx_tix->get_product_id_by_ticketnumber($ticket_number);

			// get Seat slug
				$seat_slug = isset($ticket_pmv['_evost_seat_slug'])? $ticket_pmv['_evost_seat_slug'][0]: 
					( isset($ticket_pmv['seat_id'])? $ticket_pmv['seat_id'][0]: false);

			// if no seat slug 
			if(!$seat_slug) return false;


			$ST = new EVOST_Seats_Seat($event_id,$product_id, $seat_slug );

			$sections = get_post_meta($event_id, '_evost_sections', true);
			$readable_seat = $ST->get_readable_seat_number();

			// validate readable seat data
			$additions = '';
			if(!empty($readable_seat['section']) && !empty($readable_seat['seat'])){
				$additions = ' - '. 
					evo_lang('Section') . ': '. $readable_seat['section'] . (!empty($readable_seat['section_name'])? ' ('.$readable_seat['section_name'].')':'') .', '. 
					evo_lang('Row') .': '. $readable_seat['row'] .', '.
					evo_lang('Seat') .': '. $readable_seat['seat'];
			}

			// append data
			$readable_seat_info = $ticket_pmv['Seat-Number'][0]. $additions;
		?>
		
		<div>
			<p style="<?php echo $styles['005'].$styles['pb5'].$styles['pt10'];?>"><?php echo $readable_seat_info;?></p>
			<p style="<?php echo $styles['004'].$styles['pb5'];?>"><?php echo evo_lang( 'Seat Information');?></p>
		</div>
	
		<?php endif;
	}
}
new evost_tickets();