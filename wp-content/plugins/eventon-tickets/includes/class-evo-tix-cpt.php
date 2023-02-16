<?php
/**
 * evo-tix post type object
 * @version 2.0
 */

class EVO_Evo_Tix_CPT{

	private $pmv = '';
	private $post = '';
	public $id;
	public $ticket_number = '';
	public $type = 'evo-tix';

	public function __construct($id, $load_props = true, $load_post = false, $post=''){

		if( strpos($id, '-') !== false ) {
			$tt = explode('-', $id);
			$this->id = $id = (int)$tt[0];
		}

		if(!is_numeric($id)) return;

		$this->id = $id;
		if($load_props) $this->load_props();
		if( $load_post) $this->load_post($post);
	}

	public function load_props(){
		$this->pmv = function_exists('get_metadata_raw')?  
			get_metadata_raw( 'post', $this->id, '', true) : 
			get_metadata( 'post', $this->id, '', true );
	}

	public function load_post($post=''){
		if( !empty($post)){
			$this->post = $post;
		}else{
			$this->post = get_post( $this->id);
		}		
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

	// return encrypt ticket number if enabled
	public function get_enc_ticket_number($ticket_number = ''){
		if( empty($ticket_number)) $ticket_number = $this->get_ticket_number();

		if( EVO()->cal->check_yn('evoqr_encrypt_dis','evcal_1') ) return $ticket_number;

		return base64_encode( $ticket_number);
	}

	public function get_checked_count(){}

	// return ticket status
	public function get_status(){

		$order_status = $this->get_order_status();

		if( $order_status != 'wc-completed' && $order_status != 'wc-refunded'){
			return 'NA';
		}		

		$legacy_status = $this->__get_ticket_id_status();

		if($legacy_status) return $legacy_status;

		return $this->get_prop('status');
	}

	public function refund(){

		$this->set_status('refunded');
		$this->__update_ticket_id_status('refunded');
	}

	public function restock(){
		// if ticket already has check-in or checked status leave it be
		if( $this->get_status() != 'refunded') return;

		$this->__update_ticket_id_status('check-in');
		$this->set_status('check-in');
	}

	public function set_status($new_status){
		$this->set_prop('status', $new_status);
		$this->__update_ticket_id_status($new_status);
	}

	public function __update_ticket_id_status($new_status){
		$ticket_number = $this->get_ticket_number();
		$ticket_ids = $this->get_ticket_ids_array();

		if($ticket_ids && isset($ticket_ids[ $ticket_number ] )){

			$ticket_ids[ $ticket_number ] = $new_status;
			$this->set_prop('ticket_ids', $ticket_ids);
		}
	}
	public function __get_ticket_id_status(){
		$ticket_number = $this->get_ticket_number();
		$ticket_ids = $this->get_ticket_ids_array();

		return isset($ticket_ids[$ticket_number]) ? 
			$ticket_ids[$ticket_number] : false;
	}



	
	public function get_qty(){
		return $this->get_prop('qty');
	}
	public function get_ticket_ids_array(){
		return unserialize($this->get_prop('ticket_ids'));
	}
	// check whether there are more than one ticket ids for this ticket post
	public function is_many_ticket_ids(){
		$ids = $this->get_ticket_ids_array();
		return count($ids) > 1? true: false;
	}
	public function get_event_id(){
		return $this->get_prop('_eventid');
	}
	public function get_order_id(){
		return $this->get_prop('_orderid');
	}
	public function get_order_status(){
		return get_post_status( $this->get_order_id() );
	}
	public function get_order_item_id(){
		return $this->get_prop('_order_item_id');
	}

	public function get_date($format = 'Y-m-d'){
		if( empty( $this->post)) $this->load_post();
		return get_the_date( $format, $this->post);
	}


	public function get_ticketholder_name(){
		return $this->get_prop('name');
	}
	public function get_ticketholder_email(){
		return $this->get_prop('email');
	}

	// ticket event instance index
	public function get_ticket_number_instance(){
		$inst = $this->get_prop('_ticket_number_instance');
		return $inst ? $inst : 1;
	}
	// ticket quantity index
	public function get_ticket_number_index(){
		$inst = $this->get_prop('_ticket_number_index');
		return $inst ? $inst : 0;
	}

	public function get_order_item_lang(){
		$item_id = $this->get_order_item_id();

		if($item_id){
			$lang = wc_get_order_item_meta( $item_id, '_evo_lang', true);
			if($lang) return $lang;
		}

		return EVO()->lang;
	}

	public function get_prop($field){
		if( empty($this->pmv) ) $this->load_props();
		if(!isset($this->pmv[ $field])) return false;
		return $this->pmv[ $field][0];
	}

	// added v2.0
	public function set_prop( $field, $value){
		update_post_meta($this->id, $field,$value);		
	}
	
}