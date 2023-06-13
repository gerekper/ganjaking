<?php
/** 
 *	Integratin with virtual events
 * @version 0.1
 */	

class EVORE_Int_Vir{
	public function __construct(){
		add_action('evo_editevent_vir_after_event_end', array($this, 'event_edit_options'),10,1);
		add_filter('evo_eventcard_vir_after_details', array($this, 'end_content'),10,1);
	}

	public function event_edit_options($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_rev_after', 
					'value'=>		$EVENT->get_prop('_vir_rev_after'),
					'input'=>	true,
					'label'=> 	__('Show leave a review notice after event ends', 'evors'),
					'tooltip'=> __('This will show a leave review button after the live event is over. This will verify review settings to make sure if user needed to be loggedin to leave review.','evors'),				
				),				
			)
		);
	}

	// at the end content
	public function end_content($EV){

		if( !$EV->is_past ) return false;
		if( !$EV->EVENT->check_yn('_vir_rev_after')) return false;

		if( (EVO()->cal->check_yn('evore_only_logged','evcal_re') && is_user_logged_in() ) ||
			!EVO()->cal->check_yn('evore_only_logged','evcal_re') ){

			$user_ID = get_current_user_id();
			$user_name = $user_email ='';
			
			if(!empty($user_ID) && $user_ID && !empty($this->opt['evore_prefil']) && $this->opt['evore_prefil']=='yes' ){
				$user_info = get_userdata($user_ID);
				$user_name = $user_info->display_name;
				$user_email = $user_info->user_email;
			}
			echo "<div class='review_actions' style='padding-top:10px;'><a class='evcal_btn new_review_btn' data-username='{$user_name}' data-useremail='{$user_email}' data-uid='{$user_ID}' data-eventname='".get_the_title($EV->EVENT->ID)."'>".evo_lang('Write a Review')."</a></div>";
		}

		
	}

}

new EVORE_Int_Vir();