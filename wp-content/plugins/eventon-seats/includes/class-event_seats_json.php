<?php
/**
 * JSON for seats
 * @version 1.2
 */

class EVOST_Seats_Json extends EVOST_Expirations{
	public function __construct($EVENT, $wcid, $ri=0){
		parent::__construct($EVENT, $wcid, $ri);
		$this->set();
	}

	//get all seat map json data
	function __j_get_all_sections($is_admin = true){
		$sections = $this->seats_data;
		if(!is_array($sections)) return false;
		
		$j = array();

		// Check all event seats for expiration time, and if expired, restock seat
		$this->run_all_seat_expiration_check();

		// check with event attendance > seat avialable match attendance count
			$attendees = $this->get_tickets_by_section();

		// reload sections data
		//$this->reload_seats_data();
		$sections = $this->seats_data;
		//print_r($sections);

		foreach($sections as $section_id=>$section){

			// localize section
			$this->set_section( $section_id );

			$section_attendees = false;
			if( isset($attendees[ $section_id ] )) $section_attendees = $attendees[$section_id];

			$def_price = 0;

			// for assigned seating section
			if( isset($section['rows']) && sizeof($section['rows'])>0){
				foreach($section['rows'] as $row_id=>$row){
					foreach($row as $seat_id=>$seat){
						if(!is_array($seat)) continue;
						if( in_array($seat_id, array('row_index', 'row_price','row_id'))) continue;
						
						$j[$section_id]['rows'][$row_id]['seats'][$seat_id] = $seat;
						$j[$section_id]['rows'][$row_id]['seats'][$seat_id]['status'] = 
							isset($seat['status'])? $seat['status'] : 'av';

						// old method
						if(isset($seat['number']) && !preg_match("/[a-z]/i",$seat['number']) ){
							$j[$section_id]['rows'][$row_id]['seats'][$seat_id]['id'] = $seat['number'];
							$j[$section_id]['rows'][$row_id]['seats'][$seat_id]['number'] = $seat['id'];
						}
					}
					if(isset($row['row_index'])) $j[$section_id]['rows'][$row_id]['row_index'] = $row['row_index'];
					if(isset($row['row_price'])) $j[$section_id]['rows'][$row_id]['row_price'] = $row['row_price'];
					$j[$section_id]['rows'][$row_id]['row_id'] = $row_id;

					// update default price with row price value
					if(isset($row['row_price'])) $def_price = $row['row_price'];
				}
			}

			// calculate available capacity for non seats @since 1.2
				if(isset($section['type']) && $section['type'] != 'def' && $section['type'] != 'aoi'){

					$capacity = $section['capacity'];

					$sold = 0;
					$inprogress = 0;

					if( $section_attendees ){
						foreach( $section_attendees  as $TN=>$TD){
							if( $TD['oS'] == 'completed') $sold++;
							if( $TD['oS'] != 'completed') $inprogress++;
						}
					}

					$j[$section_id]['available'] = $capacity - $sold - $inprogress;
					$j[$section_id]['inprogress'] = $inprogress;
				}


			// filter values
			foreach($section as $key=>$data){
				if( in_array($key, array('rows'))) continue;

				if($key=='ang') $data = (empty($data)|| $data=='NaN') ? '0':$data;
				$j[$section_id][$key] = $data;
			}	

			// old method compatibility
				if(!isset($section['type'])) 	$j[$section_id]['type'] = 'def';	
				if(!isset($section['section_id'])) 	$j[$section_id]['section_id'] = $section_id;	
				$def_price = ( isset($section['def_price']))? $section['def_price']: $def_price;

				$j[$section_id]['def_price'] = $def_price;
		}

		// save all new values as one batch		
		return $j;
	}


	// return an array of seats in cart for this event
		function _get_cart_seats_for_events($reset_user_seats = true){

			$seats = array();
			$count = 0;
			$TX_Helper = new evotx_helper();

			$exp_time = 0;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				if(empty($values['evost_data'])) continue;
				if(empty($values['evost_data']['seat_slug'])) continue;
				if(empty($values['evotx_event_id_wc'])) continue;
				if( $values['evotx_event_id_wc'] != $this->event->ID) continue;
				if($values['product_id'] != $this->wcid) continue; // show only seats for this event

				//$seats['debug'] = $this->event->ID.' '.$values['evotx_event_id_wc'];

				$seat_slug = $values['evost_data']['seat_slug'];

				// Event seats in cart is refreshed so check expiration and reset timer
					$exp_time = $this->get_seat_expiration_time($seat_slug, $cart_item_key);
					
					// if seat expiration time is past remove seat from cart
					if($reset_user_seats && $exp_time && $exp_time > time()){
						// reset the expiration time to initial duration
						$exp_time = $this->reset_expiration_time($cart_item_key,$seat_slug, $values['quantity']);
					}

				// BUILD
				$this->_localize_seat_slug( $seat_slug );
				$seats['seat'][$cart_item_key]['seat_type'] = $this->seat_type;
				$seats['seat'][$cart_item_key]['seat_qty'] = $values['quantity'];

				if( $this->seat_type == 'seat'){

					$price = $TX_Helper->convert_to_currency( $this->get_item_prop('price') );

					$this->process_seat_slug($seat_slug);
					$seats['seat'][$cart_item_key]['seat_slug'] = $seat_slug;
					$seats['seat'][$cart_item_key]['section'] = $this->get_section_letter_by_id();
					$seats['seat'][$cart_item_key]['row'] = $this->get_row_letter_by_id();
					$seats['seat'][$cart_item_key]['seat_number'] = $this->get_item_prop('number');
					$seats['seat'][$cart_item_key]['price'] = html_entity_decode($price) ;


				}else{ // unassigned seats
					$this->set_section($this->seat_slug);
					$seats['seat'][$cart_item_key]['seat_slug'] = $seat_slug;
					$seats['seat'][$cart_item_key]['seat_number'] = $this->get_item_prop('section_name');
					$seats['seat'][$cart_item_key]['section'] = $this->get_item_prop('section_name');
					$seats['seat'][$cart_item_key]['price'] = html_entity_decode($TX_Helper->convert_to_currency($this->get_item_prop('def_price') * $values['quantity']) );
				}

				
				// pluggable for other seat data in cart
				$seats = apply_filters('evost_seats_in_cart_json', $seats, $values, $cart_item_key, $this);

				$count += (int)$values['quantity'];	
			}
			
			$_exp_time = $exp_time - time();
			if($_exp_time<0) $_exp_time = 0;
			
			$seats['total_seats'] = $count;
			$seats['exp_time'] = $this->get_human_time( $_exp_time);
			$seats['exp_time_s'] =  $_exp_time;
			return $seats;
		}

	// get seats is current users cart
		function get_user_seats_in_cart(){
			$your_tickets = array();

			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				if(empty($values['_evost_seat_slug'])) continue;
				if($values['product_id'] != $this->wcid) continue; // show only seats for this event
				$your_tickets[$cart_item_key] = $values['_evost_seat_slug'];
			}
			return $your_tickets;
		}

	
}