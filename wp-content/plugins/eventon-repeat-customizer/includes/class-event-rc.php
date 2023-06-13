<?php 
/**
EventON Repeat Customizer - Events Extension Class
*/

class EVORC_Event{

	public function __construct($EVENT, $RI= 0){
		if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT, '', $RI);

		if(!$EVENT) return;

		$this->event = $EVENT;
		$this->event_id = $this->ID = $EVENT->ID;
		$this->ri = $RI;	
	}

	function is_repeat_has_data($field = ''){
		$RD = $this->event->get_prop('_repeat_data');
		if(empty($RD)) return false;
		if($this->ri == 0) return false;

		if(!empty($field)){
			return (isset($RD[ $this->ri]) && isset($RD[ $this->ri][$field]) ) ? $RD[ $this->ri][$field] : false;
		}
		return true;
	}
	function get_all_repeat_data(){
		return $this->event->get_prop('_repeat_data');
	}

	function save_one_repeat_data($data){
		$RD = $this->event->get_prop('_repeat_data');

		if(!is_array($RD)) $RD = array();
		if( empty($this->ri)) return false;


		$RD[ $this->ri ] = $data;

		$this->event->set_prop( '_repeat_data', $RD);
	}
}