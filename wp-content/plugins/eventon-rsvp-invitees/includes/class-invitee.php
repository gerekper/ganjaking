<?php
/**
 * Invitee Object extends RSVP CPT object
 */

class EVORSI_Invitee extends EVO_RSVP_CPT{
	public $debug = array();
	
	// check whether this RSVP is an invitee or a regular rsvp
	function is_invitee(){
		return $this->get_prop('rsvp_type') && $this->get_prop('rsvp_type') == 'invitee'? true: false;
	}
	function get_status(){
		return $this->get_prop('status')? $this->get_prop('status'):'created';
	}
	function get_full_name(){
		return ($this->get_prop('last_name'))? $this->get_prop('last_name') .', '.$this->get_prop('first_name'):''. $this->get_prop('first_name');
	}
	function get_data(){
		return $this->pmv;
	}
	function get_email(){
		return $this->email();
	}
	function verify_email($email){
		if(!$this->get_email()) return false;
		return $this->get_email() == $email ? true: false;
	}

	function get_invite_link($rsvp_status=''){
		$link_append = '';
		if(!empty($rsvp_status)){
			$link_append = '&r='.$rsvp_status;
		}

		$event_link = get_permalink($this->event_id());
		$pre = strpos($event_link, '/?')!== false ? '&': '?';
		return $event_link. $pre . 'invite='. base64_encode($this->get_email().'-'.$this->rsvp_id) . $link_append;
	}

	function get_msgs(){
		$msg = $this->get_prop('msgs');
		if(!$msg) return false;
		return $msg;
	}
	function get_msgs_count(){
		$M = $this->get_msgs();
		if(!$M) return 0;
		return count($M);
	}
	function get_json_msgs(){
		$M = $this->get_msgs();
		if(!$M) return false;

		$now = current_time('timestamp');
		$EVO_Help = new evo_helper();

		$_M = array();
		foreach($M as $f=>$v){
			if(!isset($v['t'])) continue;
			$_M['msgs'][$f] = $v;
			$_M['msgs'][$f]['time'] = $EVO_Help->get_human_time( $now - $f);
		}
		return $_M;
	}

	function delete_msg($msg_time){
		$M = $this->get_msgs();
		if(!$M) return false;

		if(!isset($M[$msg_time])) return false;
		unset($M[$msg_time]);

		$this->set_prop('msgs', $M);
		return true;
	}

	function save_new_msg($_m, $_n, $visibility = 'private'){
		$msg = $this->get_prop('msgs');

		if(!$msg) $msg = array();

		$now = current_time('timestamp');
		$msg[$now] = array(
			't'=> $_m,
			'n'=> $_n,
			'v'=> $visibility,
		);

		$this->set_prop('msgs', $msg);

		return $this->get_json_msgs();
	}
}