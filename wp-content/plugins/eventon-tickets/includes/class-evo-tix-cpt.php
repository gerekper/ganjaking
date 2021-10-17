<?php
/**
 * evo-tix post type object
 * @version 1.9.3
 */

class EVO_Evo_Tix_CPT{

	private $pmv = '';
	public $id;
	public $type = 'evo-tix';

	public function __construct($id){

		if(!is_numeric($id)) return;

		$this->id = $id;
		$this->load_props();
	}

	public function load_props(){
		$this->pmv = function_exists('get_metadata_raw')?  get_metadata_raw( 'post', $this->id, '', true) : get_metadata( 'post', $this->id, '', true );
	}

	public function get_prop($field){
		if(!isset($this->pmv[ $field])) return false;
		return $this->pmv[ $field][0];
	}
	public function get_repeat_interval(){
		$ri = $this->get_prop('repeat_interval');
		return ($ri && $ri>0) ? $ri : 0; 
	}
	public function get_props(){
		return $this->pmv;
	}
	public function get_ticket_number(){
		return $this->get_prop('_ticket_number');
	}
	public function get_event_id(){
		return $this->get_prop('_eventid');
	}
	public function get_order_id(){
		return $this->get_prop('_orderid');
	}
	public function get_order_item_id(){
		return $this->get_prop('_order_item_id');
	}

	public function get_order_item_lang(){
		$item_id = $this->get_order_item_id();

		if($item_id){
			$lang = wc_get_order_item_meta( $item_id, '_evo_lang', true);
			if($lang) return $lang;
		}

		return EVO()->lang;
	}
	
}