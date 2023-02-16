<?php
/**
 * Event seats expiration class
 */

class EVOST_Expirations extends EVOST_Seats{
	public $seat_id;
	public $event_id;
	//private $seat_type = false;
	public $expirations = array();
	public $event_exp = false; // expirations for this event

	public function __construct($EVENT, $wcid){
		parent::__construct($EVENT, $wcid);
		$this->set();
		$this->event_id = $this->event->ID;
	}

// localization of expiration data
	function set(){
		$this->expirations =  get_option('_evost_expiration');
		if(isset($this->expirations[$this->event->ID])) $this->event_exp = $this->expirations[$this->event->ID];
	}
	function update_exp($new_exp){
		update_option('_evost_expiration', $new_exp);
	}
	// update local expiration values to options
	function update_locals(){
		update_option('_evost_expiration', $this->expirations);
	}

// VALIDATE
	// check if a seat expiration time has passed
		function has_seat_expired($cart_item_key, $seat_slug){
			$seat_expiration_time = $this->get_seat_expiration_time($seat_slug, $cart_item_key);
			if(!$seat_expiration_time) return false; // there is not expiration time

			if($seat_expiration_time < time()) return true; // expiration time is passed
			return false;
		}

// ACTIONS
	// add seat to temp hold, when seat is added to cart
		function add_seat_temphold($cart_item_key, $qty, $seat_slug){
			$this->_get_seat_type_by_slug($seat_slug); // check what kind of a seat this is

			if( empty($this->seat_type)) return false;

			// set expiration times
			$expirations =  $this->expirations;
			$expirations[$this->event->ID][$seat_slug][$cart_item_key] = array(
				'time'=> $this->seat_expiration_time(),
				'qty'=>$qty
			);

			$this->expirations = $expirations;

			if( $this->seat_type == 'unaseat'){			
				// increase seat sold value temporarily
				$this->set_section($seat_slug);
				$this->una_adjust_sold('add', $qty);
				
			}else{
				// make the seat temp una
				$this->seat_slug = $seat_slug;
				$this->process_seat_slug();
				$this->make_tuav();
			}

			$this->update_exp($this->expirations); // save the changes

		}

	
	// when a temphold seat removed from cart, restock seat
		function restock_temphold_seat($cart_item_key, $qty, $seat_slug){
			//echo $cart_item_key.' '.$qty;
			// delete expiration
			$deleted = $this->delete_seat_expiration($seat_slug);
			if(!$deleted) return false; // if there are no expiration data for seat stop here

			$this->seat_type = $this->_localize_seat_slug($seat_slug); // check what kind of a seat this is

			// restock seat
			if( $this->seat_type == 'seat'){
				// skip sold seats
				if($this->get_item_prop('stauts') == 'uav') return false;
				$this->make_available();
			}else{
				$this->una_restock_seat($qty);
			}	
		}

	// CART ITEM KEY
		function get_cart_item_key_by_slug($seat_slug){
			$expirations = $this->expirations;
			if(count($expirations)<1) return false;
			if(!isset($expirations[$this->event_id])) return false;
			if(!isset($expirations[$this->event_id][$seat_slug])) return false;
			
			foreach($expirations[$this->event_id][$seat_slug] as $cart_key=>$data){
				return $cart_key;
			}
		}

	// GET EXPIRATIONS TIMES
		// get seat expiration time for a seat slug
		function get_seat_expiration_data($seat_slug){
			$expirations = $this->expirations;
			if(count($expirations)<1) return false;
			if(!isset($expirations[$this->event_id])) return false;
			if(!isset($expirations[$this->event_id][$seat_slug])) return false;
			return $expirations[$this->event_id][$seat_slug];
		}

		// return seat expiration time
		function get_seat_expiration_time($seat_slug, $cart_item_key=''){
			$this_exp = $this->get_seat_expiration_data($seat_slug);

			if(!$this_exp) return false;

			foreach($this_exp as $_cart_item_key=>$data){
				if(!empty($cart_item_key) && $cart_item_key != $_cart_item_key) continue;
				if(!isset($data['time'])) continue;
				return $data['time'];
			}			
			return false;
		}


		// get seat expiration time by seat slug
		function get_seat_expiration_by_slug($seat_slug){
			$this_exp = $this->get_seat_expiration_data($seat_slug);
			if(!$this_exp) return false;
			if(count($this_exp)<1) return false;

			if(is_array($this_exp)){
				foreach($this_exp as $_cart_item_key=>$data){
					if(!isset($data['time'])) continue;
					return $data['time'];
				}
			// old method	
			}else{
				return $this_exp;
			}		
			return false;
		}

	// RESET
		// reset expiration time
		function reset_expiration_time($cart_item_key, $seat_slug, $force_update = true){
			$this->expirations[$this->event_id][$seat_slug][$cart_item_key]['time'] = $this->seat_expiration_time();
			if($force_update) $this->update_exp($this->expirations);
			return $this->expirations[$this->event_id][$seat_slug][$cart_item_key]['time'];
		}

		function reset_expiration_time_from_cart($cart_item_key, $seat_slug, $qty){
			$this->expirations[$this->event_id][$seat_slug][$cart_item_key]['time'] = $this->seat_expiration_time();
			$this->expirations[$this->event_id][$seat_slug][$cart_item_key]['qty'] = $qty;
			$this->update_exp($this->expirations);
			return $this->expirations[$this->event_id][$seat_slug][$cart_item_key]['time'];
		}

		// reset all cart seat tickets expiration
		// when new seat added to cart and seats in cart refreshed on event
		function reset_all_cart_seat_expirations(){
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if(empty($cart_item['evotx_event_id_wc'])) continue;
				if(!isset($cart_item['evotx_repeat_interval_wc'])) continue;
				if(!isset($cart_item['_evost_seat_slug']) ) continue;

				$this->reset_expiration_time($cart_item_key,$cart_item['_evost_seat_slug'], false);
			}
			$this->update_exp($this->expirations);
		}

	// DELETE EXP
		// delete expiration time for this cart item seat slug
		function delete_seat_expiration( $seat_slug){
			$expirations =  $this->expirations;

			// requires
			if(count($expirations)<1) return false;
			if(!isset($expirations[$this->event_id])) return false;
			if(!isset($expirations[$this->event_id][$seat_slug])) return false;

			unset($expirations[$this->event_id][$seat_slug]);
			$this->expirations = $expirations;
			$this->update_locals();
			return true;
		}

	// check all seat expiration check for an event seats, DO NOT REST
	// when seats are loaded on front and backend
	function run_all_seat_expiration_check(){
		// get all existing expiraitons for this event
		$expirations = $this->expirations;


		if(is_array($expirations) && count($expirations)<1) return false;
		if(!isset($expirations[$this->event_id])) return false; // no expirations are saved

		//print_r($expirations);
		$seats_in_cart = $this->get_all_seats_in_cart();
		//print_r($seats_in_cart);
		//print_r($this->seats_data);

		// for all seats for this evetn
		foreach($this->seats_data as $section_id=>$section){
			$this->set_section($section_id); // localize

			// assigned seating
			if(isset($section['rows'])){

				foreach($section['rows'] as $row_id=>$row){
					$this->set_row($row_id); // localize

					// Each seat
					foreach($row as $seat_id=>$seat){
						if( in_array($seat_id, array('row_index', 'row_price'))) continue;

						$this->set_seat($seat_id); // localize
						$seat_slug = $section_id.'-'.$row_id.'-'.$seat_id;

						// skip sold tickets
						if($this->get_item_prop('status') == 'uav'){
							continue;
						} 
						

						// get seat expiration time for this seat if exists
						$seat_expiration_time = $this->get_seat_expiration_by_slug($seat_slug);


						//if(!$seat_expiration_time) continue; // if there are no expiration data continue

						// if seat is available but has expiration then remove from cart and remove expiration
						if($this->get_item_prop('status') == 'av'  ){
							
							// has expiration time => remove from cart and expiration
							if($seat_expiration_time){
								$cart_item_key = $this->get_cart_item_key_by_slug($seat_slug);
								$this->delete_seat_expiration($seat_slug);		
								WC()->cart->remove_cart_item($cart_item_key);
							}

							// if in users cart already
							if( count($seats_in_cart) > 0 && array_key_exists($seat_slug, $seats_in_cart) ){
								//echo $seat_slug.' '.$this->get_item_prop('status');
								$cart_item_key = $seats_in_cart[$seat_slug];
								//WC()->cart->remove_cart_item($cart_item_key);
							}
						}


						// for temporarily unavailable seats
						if($this->get_item_prop('status') == 'tuav' ){

							// if this seat does not have expiration time, make it available
							if(!$seat_expiration_time){
								$this->make_available();
							}

							//echo date('y-m-d h:i:s',$seat_expiration_time).' '.date('y-m-d h:i:s', time());
							//print_r($seats_in_cart);
							// if the tuav seat is not in users cart
							if( count($seats_in_cart) > 0 && !array_key_exists($seat_slug, $seats_in_cart) ){
								$cart_item_key = $this->get_cart_item_key_by_slug($seat_slug);
								WC()->cart->remove_cart_item($cart_item_key);
							}

						}

						// seat expiration has passed - restock seat & remove from cart
						if($seat_expiration_time && $seat_expiration_time < time()){
							// restock seat
							$this->make_available();
							
							// remove from cart and add notification
							$cart_item_key = $this->get_cart_item_key_by_slug($seat_slug);

							if($cart_item_key){
								if(WC()->cart->remove_cart_item($cart_item_key))
									wc_add_notice( 'Seat removed from cart, time expired!', 'error' );																		
							} 
							// remove expiration
							$this->delete_seat_expiration($seat_slug);												
						}
					}
				}
			// unassigned seating
			}else{ 

				
				if($section['type'] != 'una') continue;
				if(!isset($expirations[$this->event_id][$section_id])) continue;

				// each cart item in expiration
				foreach($expirations[$this->event_id][$section_id] as $cart_item_key=>$data){
					if(!isset($data['time'])) continue;
					if(!isset($data['qty'])) continue;

					// time expired
					if($data['time'] < time()){
						// remove expiration
						$this->delete_seat_expiration($section_id);

						// restock seat
						$this->set_section($section_id);
						$this->una_restock_seat($data['qty']);

						// remove seat from cart and add notification
						WC()->cart->remove_cart_item($cart_item_key);
						wc_add_notice( 'UNA Seat removed from cart, time expired!', 'error' );
					}
				}
			}
		}

		$this->update_locals();
	}

	

// unassigned seating	
	// check if expirations already exist in cart update expiration qty
	// if unaseat is in cart and adding more of same item to cart update qty
		function unaseat_set_cart_expirations($cart_item_key, $seat_slug, $qty){
			// get all existing expirations for this seat slug
			$this_exps = $this->get_seat_expiration_data($seat_slug);
			if(!$this_exps) return false;

			$this->_get_seat_type_by_slug($seat_slug); 

			// skip for regular seats
			if($this->seat_type=='seat') return false;
			
			// check if cart expiration already set
			if(isset($this_exps[$cart_item_key])){	
				$this->expirations[$this->event_id][$seat_slug][$cart_item_key]['qty'] = $qty;
				$this->update_locals();
			}
		}

	// UNA match cart seat qty to onhold, if seat already exists in cart expirations
		function unaseat_match_cart_qty($cart_item_key, $seat_slug, $new_cart_qty){
			$this_exps = $this->get_seat_expiration_data($seat_slug);
			if(!$this_exps) return false;
		
			// skip regular seats
			$this->_get_seat_type_by_slug($seat_slug);
			if($this->seat_type=='seat') return false; 

			if(isset($this_exps[$cart_item_key]) && isset($this_exps[$cart_item_key]['qty'])){
				$old_qty = $this_exps[$cart_item_key]['qty'];

				if($new_cart_qty == $old_qty) return false;				

				// adjust sold quantity for UNA, which would be on temp hold and released on expiration
				$change_qty = $old_qty - $new_cart_qty;
				$change_qty = $change_qty<0? (int)$change_qty*-1: $change_qty;
				$this->una_adjust_sold( ($new_cart_qty>$old_qty? 'add':'reduce'), $change_qty );

			}
		}
	

	function get_unaseats_onhold($section_id){
		$onhold = '0';
		if(!$this->event_exp) return '0';
		if(!isset($this->event_exp[$section_id])) return '0';
		return count($this->event_exp[$section_id]);
	}
	
	
// SUPPROTIVE
	// get the seat expiring time in seconds
	function seat_expiration_time(){
		global $evost;
		$addition = !empty(EVOST()->frontend->opt_tx['evost_session_time'])? 
				(EVOST()->frontend->opt_tx['evost_session_time'] * 60 ):
				(10*60);
		return time() + $addition;
	}
	// return time difference in d/h/m
		function get_human_time($time){

			$output = '';
			$minFix = $hourFix = $dayFix = 0;

			$day = $time/(60*60*24); // in day
			$dayFix = floor($day);
			$dayPen = $day - $dayFix;
			if($dayPen > 0)
			{
				$hour = $dayPen*(24); // in hour (1 day = 24 hour)
				$hourFix = floor($hour);
				$hourPen = $hour - $hourFix;
				if($hourPen > 0)
				{
					$min = $hourPen*(60); // in hour (1 hour = 60 min)
					$minFix = floor($min);
					$minPen = $min - $minFix;
					if($minPen > 0)
					{
						$sec = $minPen*(60); // in sec (1 min = 60 sec)
						$secFix = floor($sec);
					}
				}
			}
			$str = "";
			if($dayFix > 0)
				$str.= $dayFix." day ";
			if($hourFix > 0)
				$str.= $hourFix." hour ";
			if($minFix > 0)
				$str.= $minFix." min ";
			//if($secFix > 0)	$str.= $secFix." sec ";
			return $str;
		}
}