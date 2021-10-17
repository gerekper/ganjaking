<?php
/**
 * Seat temp session
 */

class EVOST_Sesh extends EVOST_Seats{
	public $seat_id;
	public $event_id;

	public function __construct($EVENT, $wcid){
		parent::__construct($EVENT, $wcid);
		$this->set();
		$this->event_id = $this->event->ID;
	}

	function add_seat_to_sesh(){
		
	}
}