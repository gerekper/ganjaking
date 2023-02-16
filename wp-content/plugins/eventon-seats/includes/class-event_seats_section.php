<?php
/**
 * Unassigned Seat
 */

class EVOST_Seats_Una extends EVOST_Seats{
	public $section_id = false;
	public function __construct($EVENT, $wcid, $section_id){
		parent::__construct($EVENT, $wcid);
		$this->section_id = $section_id;
		$this->set_section($this->section_id);
	}

	function get_capacity(){
		return $this->get_item_prop('capacity');
	}
	function get_name(){
		return $this->get_item_prop('section_name');
	}
	function get_price(){
		return $this->get_item_prop('def_price');
	}
	function is_seats_available($qty){
		$cap =  $this->get_capacity();
		return ( $cap  && $cap> $qty)? true: false;
	}

	// Seat Expirations
		function set_expiration($cart_item_keys){
			$this->expirations =  get_option('_evost_expiration');
			$cart_item_key = $cart_item_keys[0];

			$expirations[$this->event->ID][$this->section_id][$cart_item_key] = $this->seat_expiration_time();
			update_option('_evost_expiration', $expirations);

		}

		function seat_expiration_time(){
			$addition = !empty(EVOST()->frontend->opt_tx['evost_session_time'])? 
					(EVOST()->frontend->opt_tx['evost_session_time'] * 60 ):
					(10*60);
			return time() + $addition;
		}
}