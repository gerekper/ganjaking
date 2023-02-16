<?php
/**
Virtual Events Integration - RSVP
*/

class EVORS_Virtual_Events extends evors_front{

	public $user_has_rsvped = false;
	public $rsvp_go = false;
	public $can_rsvp_now = false;

	function __construct(){
		// ADMIN
		add_action('evo_editevent_vir_before_after_event', array($this, 'event_edit_options'),10,1);
		add_action('evo_editevent_vir_after_event_end', array($this, 'after_event_end'),10,1);
		add_action('evovp_editevent_vir_pre_event_end', array($this, 'pre_event_end'),10,1);

		// FRONT
		add_action('evo_vir_initial_setup', array($this, 'initial_setup'),10, 1);
		add_filter('evo_eventcard_vir_details_bool', array($this, 'card_vir_show'),10, 2);
		add_filter('evo_eventcard_virtual_livenow_html', array($this, 'card_livenow_html'),10,2);
		add_filter('evo_eventcard_vir_after_details', array($this, 'end_content'),10,1);
		add_filter('evo_eventcard_virtual_after_content', array($this, 'post_content'),10,2);
		add_filter('evo_eventcard_vir_txt_cur', array($this, 'pre_text'),10,3);

		// virtual plus
		add_filter('evovp_show_signin_box', array($this, 'signin_box'),10,3);
		add_filter('evovp_signin_user', array($this, 'signin_user'),10,3);
		add_filter('evovp_eventcard_virtual_pre_content', array($this, 'pre_event_content'),10,2);

		add_action('eventonrs_confirmation_email', array($this, 'confirmation_email_include'), 10, 2);
	}

// ADMIN
	public function event_edit_options($EVENT){
			
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_after_rsvp', 
					'value'=>		$EVENT->get_prop('_vir_after_rsvp'),
					'input'=>	true,
					'label'=> 	__('User must RSVP to view virtual event information', 'evors'),
					'tooltip'=> __('Virtual event information will only appear to user after they rsvped.','evors'),
					'afterstatement' =>'_vir_after_rsvp_as'
				),
				array(
					'type'=>'begin_afterstatement',
					'value'=>	$EVENT->get_prop('_vir_after_rsvp'),
					'id'=>	'_vir_after_rsvp_as',
				),
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_hide_rsvpcount', 
					'value'=>		$EVENT->get_prop('_vir_hide_rsvpcount'),
					'input'=>	true,
					'label'=> 	__('Hide RSVP guest count and checked-in count', 'evors'),
					'tooltip'=> __('This will hide guest count and checked-in guest count next to live now button.','evors')
				),
				array(
					'type'=>'end_afterstatement',
				),
			)
		);			
	}

	public function pre_event_end($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_preevent_rsvp', 
					'value'=>		$EVENT->get_prop('_vir_preevent_rsvp'),
					'input'=>	true,
					'label'=> 	__('User must RSVP to view pre-event information', 'evors'),
					'tooltip'=> __('Pre-event information will only appear to users that have rsvped.','evors'),
					'tooltip_position'=>'L',
				),				
			)
		);
	}
	public function after_event_end($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_afterevent_rsvp', 
					'value'=>		$EVENT->get_prop('_vir_afterevent_rsvp'),
					'input'=>	true,
					'label'=> 	__('User must RSVP to view after event information', 'evors'),
					'tooltip'=> __('After event information will only appear to users that have rsvped.','evors'),
					'tooltip_position'=>'L',
				),				
			)
		);
	}
// FRONT
	public function initial_setup($EV){

		$this->rsvp_go = false;
		$this->can_rsvp_now = false;
		$this->user_has_rsvped = false;

		// check if rsvp enabled for this event
		if( !$EV->EVENT->check_yn('evors_rsvp')) return;

		//if( $EVENT->get_prop('_vir_show') != 'after_rsvp') return false; // depreciated
		if( !$this->check_rsvp_to_vir( $EV->event)) return false; 		
		if($EV->is_past) return false;

		$this->load_rsvp_event( $EV->EVENT);

		if(!$this->RSVP->is_rsvp_active() ) return false;

		$this->rsvp_go = true;

		// if users can rsvp now
		if( $this->RSVP->can_user_rsvp() && $this->RSVP->has_space_to_rsvp())
			$this->can_rsvp_now = true;

		// if current user has rsvped
		if($this->oneRSVP){
			$user_rsvp_status = apply_filters('evors_user_existing_rsvp_status',$this->oneRSVP->get_rsvp_status());
			if($user_rsvp_status && $user_rsvp_status != 'n') $this->user_has_rsvped = true;

			// if in waitlist
			if( $this->oneRSVP->status() == 'waitlist') $this->user_has_rsvped = false;
		}
	}
	public function check_rsvp_to_vir($EE){
		// if user must rest to see vir OR when to show is set to after rsvp
		return ( $EE->check_yn('_vir_after_rsvp') || $EE->get_prop('_vir_show') == 'after_rsvp') ? true:  false; 
	}
	public function good_to_go($EVENT){
		if( !empty($EVENT) && !$EVENT->check_yn('evors_rsvp')) return false;
		if( !$this->rsvp_go ) return false;
		return true;
	}

	// pre event text
	public function pre_text($text, $EVENT, $ismod){
		if(  !$this->good_to_go( $EVENT) ) return $text;
		
		return '';
	}

	// html adds for live now in eventcard
	public function card_livenow_html($html ,$EVENT){
		
		if(  !$this->good_to_go($EVENT) ) return $html;

		// show RSVP guest and checked in count
			if( !$EVENT->check_yn('_vir_hide_rsvpcount') ){

				$guest_list = $this->RSVP->GET_rsvp_list();
				
				$guests_count = $checked_count = 0;
				foreach($guest_list['y'] as $data){
					$guests_count++;
					if($data['status'] == 'checked') $checked_count++;
				}
								
				$html .= "<span class='evo_live_now_tag evotx_virtual_guests'>{$guests_count} ". evo_lang('Guests') ."</span>";
				if( $checked_count>0) 
					$html .="<span class='evo_live_now_tag evotx_virtual_checked'>{$checked_count} ". evo_lang('Signed in'). "</span>";
				
			}

		return $html;
	}
	

	// at the end content
	public function end_content($EV){

		if( $EV->EVENT->virtual_type() == 'jitsi' && $EV->_is_user_moderator) return false;

		if( !$this->good_to_go($EV->EVENT) ) return false;

		$content = '';

		if( $this->can_rsvp_now){
			// if user has rsvped
			if( $this->user_has_rsvped){
				$content.= "<span class='evors_vir_after hasrsvped evo_vir_confim' style=''>". evo_lang('You have RSVPed to this event') ."!</span>";
			}else{
				$link = evo_login_url( get_permalink() );

				// if user is not moderator
				if( !$EV->_is_user_moderator){
					$content.= "<span class='evors_vir_after' data-vir_rsvp='y' style=''>". evo_lang('Please RSVP to join this event');

					if( !$EV->current_user) $content.= evo_lang('Or') . " <a class='evcal_btn' href='{$link}'>". evo_lang('Log-in'). "</a>";

					$content.= "</span>";
				}
			}
		}else{
			$content.= "<span class='evors_vir_after' style=''>". evo_lang('RSVP for this event is closed now') ."!</span>";
		}

		if( !empty($content)) echo "<div style='padding-top:10px'>". $content. "</div>";
	}

	// if user must rsvped to view virtual details
	function card_vir_show($bool, $EV){
		// exception for jitsi moderator
		if( $EV->EVENT->virtual_type() == 'jitsi' && $EV->_is_user_moderator) return $bool;

		if( !$this->rsvp_go) return $bool;		

		return ( $bool &&  $this->rsvp_go && $this->user_has_rsvped) ? true : false;

	}

	// post event content
	public function post_content($content, $EVENT){
		
		if(!$EVENT->check_yn('_vir_afterevent_rsvp')) return $content;

		// if user must rsvped to see post content and has not rsvped
		if( $this->user_has_rsvped) return $content;

		return  evo_lang('Event has already taken place');
	}

// virtual plus 
	public function pre_event_content($html, $EV){
		if( $EV->event->check_yn('_vir_preevent_rsvp')){
			return $this->user_has_rsvped ? $html : '';
		}

		return $html;
	}
	public function signin_user($classdata, $EE, $PP){

		if( !$this->check_rsvp_to_vir($EE) ) return $classdata;

		$current_user = wp_get_current_user();
		if(!$current_user) return $classdata;

		$RSVP = new EVORS_Event( $EE);

		// get users rsvp id
		$rsvp_id = $RSVP->get_rsvp_id_by_author( $current_user->ID);
		if(!$rsvp_id) return $classdata;

		update_post_meta($rsvp_id, 'signin', 'y');

		$classdata['force'] = 'yy';

		return $classdata;


	}
	public function signin_box($bool, $EE, $current_user){
		if( !$this->check_rsvp_to_vir($EE) ) return $bool;

		// check if current user has signed in
		$RSVP = new EVORS_Event( $EE);

		if( $RSVP->is_user_signedin( $current_user->ID )) return false;

		return true;
	}


// EMAIL
	function confirmation_email_include( $oneRSVP, $RSVP){


		// if show after rsvp or it virtual info is always visible
		if( $this->check_rsvp_to_vir($RSVP->event) || $RSVP->event->get_prop('_vir_show') == 'always'){

			$user_rsvp_status = false;
			if($oneRSVP){
				$user_rsvp_status = apply_filters('evors_user_existing_rsvp_status',$oneRSVP->get_rsvp_status());
			}

			if($user_rsvp_status){
				$link = $RSVP->event->virtual_url();
				?>
				<p style="font-weight:bold;color:#303030; text-transform:uppercase; font-size:18px;  padding-bottom:0px; margin-bottom:0px; line-height:110%;"><?php evo_lang_e('Virtual Event Access');?> </p>
				<p style="font-style:italic;color:#afafaf; font-size:14px; margin:0 0 10px 0; padding-bottom:10px;"><?php evo_lang_e('Link');?>: <a href='<?php echo $link;?>'><?php echo $link;?></a><?php echo ($v_pass = $RSVP->event->get_virtual_pass() ) ? ' / '.evo_lang('Pass').' '.$v_pass:'';?></p>


				<?php
			}
		}

	}
}
new EVORS_Virtual_Events();