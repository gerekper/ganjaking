<?php
/**
 * Evo-rsvp post type based rsvp object
 * @version 2.6.2
 */

class EVO_RSVP_CPT{
	public $pmv = false;
	public $rsvp_id= false;
	public $ID= false;
	public function __construct($rsvp_id){

		if(!is_numeric($rsvp_id)) return false;

		$pt = get_post_type($rsvp_id);
		if(!$pt || $pt != 'evo-rsvp') return false;

		
		$this->rsvp_id = $this->ID = (int)$rsvp_id;
		

		$this->load_rsvp_data();
	}

	function event_id(){
		return $this->get_prop('e_id');
	}
	function repeat_interval(){
		$r = $this->get_prop('repeat_interval');
		return $r? (int)$r: 0;
	}
	function first_name(){
		return $this->get_prop('first_name');
	}
	function last_name(){
		return $this->get_prop('last_name');
	}
	function full_name(){
		$LN = $this->last_name();
		return $this->first_name(). ($LN? ' '.$LN:'');
	}
	function email(){
		return $this->get_prop('email');
	}
	function count(){
		$c = $this->get_prop('count');
		if(!$c) return 1;
		return (int)$c;
	}
	function get_updates(){
		$u = $this->get_prop('updates');
		return $u && $u=='yes'? true:false;
	}
	function status(){
		$st = $this->get_prop('status');
		if(!$st) return false;
		return $st;
	}
	function checkin_status(){
		$st = $this->get_prop('status');
		if(!$st) return 'check-in';
		return $st;
	}

	// whether checking status is check-in or checked and nothing else
	function checkin_status_normal(){
		$ST = $this->checkin_status();
		return in_array($ST, array('check-in','checked')) ? true: false;
	}
	// yes no maybe
	function get_rsvp_status(){
		$st = $this->get_prop('rsvp');
		if(!$st || empty($st)) return false;
		return $st;
	}

	// attachments
	function get_attachments($type = 'path'){
		$attachments = array();

		for($x=1; $x<= EVORS()->frontend->addFields; $x++){

			if( !EVO()->cal->check_yn('evors_addf'.$x,'evcal_rs') ) continue;
			if( !$this->get_prop('evors_addf'.$x.'_1') ) continue;

			if( EVO()->cal->get_prop('evors_addf'.$x.'_2','evcal_rs') != 'file' ) continue;

			$media_id = $this->get_prop('evors_addf'.$x.'_1');

			$path = get_attached_file( $media_id);
			if(!$path) continue;

			$attachments[] = $path;

		}

		return $attachments;
	}

	// return the rsvp type , normal/invitee/waitlist
	function get_rsvp_type(){
		$T = $this->get_prop('rsvp_type');
		return $T? $T: 'normal';
	}
	function edit_post_link(){
		return get_admin_url().'post.php?post='.$this->rsvp_id.'&action=edit';	
	}
	public function trans_rsvp_status($lang=''){
		$status = $this->get_rsvp_status();
		if(!$status) return;

		$_sta = array(
			'y'=>array('Yes', 'evoRSL_003'),
			'n'=>array('No', 'evoRSL_005'),
			'm'=>array('Maybe', 'evoRSL_004'),
		);

		$lang = (!empty($lang))? $lang : (!empty(EVO()->lang)? EVO()->lang: 'L1');
		return EVORS()->lang($_sta[$status][1], $_sta[$status][0], $lang);
	}

	// NOTES
		function create_note( $note, $author=''){
			$notes = $this->get_notes();

			if(!$notes) $notes = array();
			$note_id = rand(100000,999990);
			
			// current time as unix value
				$date = current_time('timestamp');
				if(empty($date)) $date = time();
			
			$notes[$note_id]['date'] = $date;
			$notes[$note_id]['note'] = $note;

			if(!empty($author)){
				$UID = $author;
			}else{
				$UID = get_current_user_id();
				$UID = !$UID? 'na': $UID;
			}
			
			$notes[$note_id]['author'] = $UID;

			$this->set_prop('_notes',$notes);
		}
		function get_notes(){
			return $this->get_prop('_notes');
		}
		function delete_note($note_id){
			$notes = $this->get_notes();
			if(!$notes) return true;
			if(!isset($notes[$note_id])) return true;

			unset($notes[$note_id]);
			$this->set_prop('_notes',$notes);
			return true;
		}

	// general getters
	function get_prop($field){
		if(!$this->pmv) return false;
		if(empty($this->pmv[$field])) return false;
		if(!isset($this->pmv[$field])) return false;
		if(!isset($this->pmv[$field][0])) return maybe_unserialize($this->pmv[$field]);
		return maybe_unserialize($this->pmv[$field][0]);
	}
	// return blank if empty instead of false
	function get_prop_($field){
		$f = $this->get_prop($field);
		return $f? $f: '';
	}

	function set_prop($field, $value){
		update_post_meta( $this->rsvp_id, $field, $value);
		$this->pmv[$field][0] = $value; // update local value
	}
	function load_rsvp_data(){
		$this->pmv = get_post_meta($this->rsvp_id);
	}

}