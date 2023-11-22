<?php
/** 
 * Invitees for event
 * 
 */

class EVORSI_Invitees extends EVORS_Event{
	public $debug;
		
	function is_invited($invite_code = ''){
		if(empty($invite_code)) return false;
		$I = $invite_code;
		$I = base64_decode($I);

		if(strpos($I, '-') === false) return false;
		$I_ = explode('-', $I);

		// 0 - email 1 - rsvp invitee id

		$II = new EVORSI_Invitee($I_[1]);

		if(!$II->verify_email($I_[0]) ) return false;

		return $II;

	}

	// whether messaging is allowed on the eventcard
	function is_messaging_on(){
		return $this->event->check_yn('_evorsi_messaging');
	}

	// check if invitee wall is enabled
	function is_invitee_wall(){
		return $this->event->check_yn('_evorsi_invitee_wall');
	}

	function is_invitee_active(){
		return $this->event->check_yn('evorsi_invitees');
	}	
	function has_invitees(){
		return $this->run_wp_query()? true: false;
	}
	function get_invitees(){
		return $this->run_wp_query();
	}
	function get_stats(){
		$I = $this->get_invitees();

		$stats = array(
			'invited'=>0, 'attending'=>0, 'not-attending'=>0
		);
		if($I){
			foreach($I as $in){
				$II = new EVORSI_Invitee($in->ID);

				$s = $II->get_status();
				$s = in_array($s, array('created','email-sent','email-opened'))? 'invited': $s;
				$stats[$s] = isset($stats[$s])? (int)$stats[$s]+ $II->count(): $II->count();
			}
		}
		return array('stats'=>$stats);
	}


	function get_invitees_data(){

		$invitees = array();

		if($I = $this->get_invitees()):		

			foreach($I as $in):
				$II = new EVORSI_Invitee($in->ID);

				$D = $II->get_data();

				$invitees[$in->ID]['name'] = $II->get_full_name();
				$invitees[$in->ID]['rsvp_status'] = $II->get_status();
				$invitees[$in->ID]['e_id'] = $this->event->ID;

				// general props
				foreach(array(
					'first_name',
					'last_name',
					'email',
					'count',
					'status'
				) as $k){
					$invitees[$in->ID][$k] = $II->get_prop($k);
				}

				$invitees[$in->ID]['extra'] = (int)$invitees[$in->ID]['count']>1? ((int)$invitees[$in->ID]['count']) -1: '';

				$invitees[$in->ID]['link'] = $II->get_invite_link();
				$invitees[$in->ID]['edit_link'] = get_edit_post_link( $in->ID );

				// load other data
				// additional fields
				EVO()->cal->set_cur('evcal_rs');
				for($x=1; $x <= EVORS()->frontend->addFields; $x++){
					if(!EVO()->cal->check_yn('evors_addf'.$x) ) continue;

					// if show no AF for event
					if($this->_show_none_AF()) continue;

					// show only certain AF for event
					if( !$this->_can_show_AF('AF'.$x) ) continue;

					$VALUE = $II && $II->get_prop('evors_addf'.$x.'_1')? $II->get_prop('evors_addf'.$x.'_1'):'-';
					$FIELDNAME = EVO()->cal->get_prop('evors_addf'.$x.'_1')? EVO()->cal->get_prop('evors_addf'.$x.'_1'): 'field';

					$invitees[$in->ID]['other_data'][$FIELDNAME] = $VALUE;

				}

				// Messages
				$invitees[$in->ID]['msg_c'] = $II->get_msgs_count();

			endforeach;
		endif;

		return array('rows'=>$invitees);
	}

	function get_all_messages($visibility='public', $RR=false){
		$M = new WP_Query(array(
			'posts_per_page'=>-1,
			'post_type'=>'evo-rsvp',
			'meta_query'=>array(
				array(
					'key'	=>'e_id',
					'value'	=>$this->event_id
				),array(
					'key'	=> 'msgs',
					'compare'	=> 'EXISTS'
				)
			)
		));
		if(!$M->have_posts()) return false;

		$now = current_time('timestamp');
		$EVO_Help = new evo_helper();

		$Ms = array();
		foreach($M->posts as $post){
			$mm = get_post_meta($post->ID, 'msgs');
			if(!$mm && !is_array($mm)) continue;

			foreach($mm[0] as $time=>$data){

				if(!isset($data['v'])) continue;
					
				// skip if visibility is not equal asked, or 
				if($data['v'] != $visibility && ( $RR && $RR->ID != $post->ID)) continue;

				$Ms[$time] = $data;
				$Ms[$time]['tm'] = $EVO_Help->get_human_time( $now - $time);
				$Ms[$time]['c'] = $data['n'] == 'admin'? 'admin':'guest';
				$Ms[$time]['n'] = $data['n'] == 'admin'? evo_lang('Host'): $data['n'];
			}
		}

		return $Ms;
	}

	function run_wp_query(){
		$I = new WP_Query(array(
			'posts_per_page'=>-1,
			'post_type'=>'evo-rsvp',
			'meta_query'=>array(
				array(
					'key'	=>'e_id',
					'value'	=>$this->event_id
				),array(
					'key'	=> 'rsvp_type',
					'value'	=> 'invitee'
				)
			)
		));
		if(!$I->have_posts()) return  false;

		return $I->posts;
	}

	// debugging and errors
		function log($error){
			$this->debug[] = $error;
		}
		function get_error(){
			return $this->debug;
		}
}