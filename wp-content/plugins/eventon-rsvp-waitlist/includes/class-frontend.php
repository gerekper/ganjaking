<?php
/**
 * Waitlist for RSVP frontend
 * @version 1.0
 */

class EVORSW_Front{

	private $WL_on = false;
	private $WL = '';

	public function __construct(){

		// Initial load of the event
		add_action('evors_load_event', array($this, 'load_event'),10,1);

		// general
		add_filter('evors_checking_status_text_ar',array($this, 'checking_status_texts'),10,1);

		// eventtop
		add_filter('evors_eventtop_above_title',array($this, 'above_title'),10,3);
		add_filter('evors_eventtop_count_html',array($this, 'count_html'),10,2);
		
		//event card
		add_filter('evors_remain_rsvp_output',array($this,'eventcard_remaining_rsvp'),10,2);
		add_filter('evors_eventcard_html_srem',array($this,'eventcard_content'),10,3);
		add_filter('evors_evc_user_rsvp_txt',array($this,'evc_user_txt'),10,3);
		add_action('evors_eventcard_after_usertext',array($this,'evc_after_user_txt'),10,2);
		add_action('evors_rsvp_choice_btns_evc',array($this,'evc_choice_btns_evc'),10,3);
		
		// form
		add_filter('evors_form_rsvp_type', array($this, 'rsvp_type'),10,3);
		add_filter('evors_form_success_msg_header', array($this, 'form_header_text'),10,3);
		add_action('evors_form_under_subtitle', array($this, 'undersubtitle'),10,3);
		add_action('evors_form_success_msg_end', array($this, 'evors_form_success_msg_end'),10,1);


		// save & update rsvp
		add_action('evors_new_rsvp_before_save', array($this, 'before_save_new_rsvp'),10,2);
		add_action('evors_new_rsvp_saved', array($this, 'save_new_rsvp'),10,4);
		add_filter('evors_rsvp_updated_before', array($this, 'before_updated'),10,4);
		add_filter('evors_rsvp_form_message', array($this, 'form_message_check'),10,4);
		add_action('evors_rsvp_updated', array($this, 'updated'),10,3);
		add_action('evors_form_success_msg_updated_rsvp', array($this, 'updated_screen_ads'),10,2);
		add_filter('evors_updatersvp_n_to_y',array($this,'rsvp_status_changed'),10,4);

		// email
		add_filter('evors_admin_notification_args', array($this, 'new_rsvp_admin_notification'),10,1);
		add_filter('evors_preview_email_arg', array($this, 'new_rsvp_admin_notification'),10,1);
	}

	// GENERAL
		function checking_status_texts($A){
			$A['waitlist'] = evo_lang('waitlist');
			return $A;
		}
		function load_event($RSVP){
			$this->RSVP = $RSVP;
			$this->WL = new EVORSW_Waitlist($RSVP);
		}

	// EVENT TOP
		function above_title($O, $var, $EVENT){			
			if($this->WL_on){				
				return $var. "<span class='eventover waitlist'>".evo_lang('Get on the waitlist')."</span>";
			}
			return $O;
		}
		public function count_html($html, $class){
			if($this->WL_on){	
				return "<span class='evors_eventtop_data remaining_count evors_wl'>".evo_lang('Waitlist is Open')."</span>";
			}
			return $html;
		}

	// EVENT CARD
		// eventcard remaining_rsvp() function modification
			function eventcard_remaining_rsvp($cap, $RSVP){

				$this->WL_on = false;
				
				$WL = $this->WL = new EVORSW_Waitlist($RSVP);	

				// if waitlist is not active
				if(!$WL->is_waitlist_active()) return $cap;

				// compare remaining rsvp value
				if($cap == 'nocap'){	
					$this->WL_on = true; 
					return $cap; 
				}
				
				if($cap>0) return $cap;

				$this->WL_on = true;
				return 'wl';
			}

		// event card display 
			function eventcard_content($html, $EV, $RR){


				if(!$this->WL_on) return $html;

				$WL = new EVORSW_Waitlist($EV);


				// get the wait list size
				$WL_count =  $WL->get_waitlist_size();			

				if(!$WL->is_waitlist_active()) return $html;
				//return '';

				$_html_wl_count = ($WL_count )? 
					'<span class="evorsw_wl_size dfx fx_ai_c fx_1_1 fx_jc_sb">' . evo_lang('Current waitlist size'). '<i style="font-weight:bold">'. $WL_count . '</i></span>': 
					evo_lang('Waitlist is empty');
				
				/*
				$_txt = "<span class='evorsw_wl_status marl10'>" . ( $RR ? "<i class='fa fa-check-circle'></i>" . evo_lang('You are in our waitlist!') :  '' ) ."</span>";
				if( !$WL_count ) $_txt = '';
				*/

				$O = "<div class='evors_section evors_remaining_spots evors_waitlist_remaining_spots sec_shade ". ( !$WL_count ? 'wl_empty':'') ."' style='flex:1'>
					<p class='remaining_count' style='display:flex'>
						<i class='fa fa-clipboard-list marr10' style='font-size:36px;'></i>";
				$O .= "<span class='evorsw_wl_info dfx fx_ai_c fx_1_1'>" . $_html_wl_count .  "</span>";
				$O .= '</p></div>';

				return $O;
			}

		// evc user rsvp based text
			function evc_after_user_txt($RSVP, $RR){
				if(!$this->WL_on) return false;
				if(!$this->WL) return false;

				if(!$this->WL->is_waitlist_active()) return false;
				if($RR && $RR->get_rsvp_type()=='normal') return false;


				if( $RR){
					echo "<div class='wl_inlist sec_shade evors_section'><p style='font-size:16px;font-weight:bold'> <i class='fa fa-check evors_checkmark marr10' style='display:inline-block'></i>" . evo_lang('You are in our waitlist!') ."</p></div>";
				}else{
					echo "<div class='wl_addto sec_shade evors_section'><p style='display:flex;' class='fx_jc_sb fx_ai_c'>" . evo_lang('All spaces are reserved!') ." <a class='evors_trig_open_rsvp_form evcal_btn' data-val='y'>".evo_lang('Join the waitlist!')."</a></p></div>";
				}

				return;		
				
			}
			function evc_user_txt($T, $RSVP, $RR){
				//return $T;
				if(!$this->WL_on) return $T;
				if(!$this->WL) return $T;

				if(!$this->WL->is_waitlist_active()) return $T;

				if($RR && $RR->get_rsvp_type()=='normal') return $T;

				if( has_action('evors_eventcard_after_usertext')) return '';

				return $RR? 
					"<em class='evorsw_in_wl' ><i class='fa fa-check-circle marr10' style='display:inline-block'></i>" . evo_lang('You are in our waitlist!') . "</em>" :
					"<em class='evorsw_wl_notice'>" . evo_lang('All spaces are reserved') . "</em>
					<em class='evorsw_add_towl'>". evo_lang('Make sure to add yourself to waitlist!') . "</em>";
			}

			function evc_choice_btns_evc($html , $RR, $RSVP){
				if(!$this->WL_on) return $html;
				if(!$this->WL) return $html;

				if(!$this->WL->is_waitlist_active()) return $html;

				if($RR && $RR->get_rsvp_type()=='normal') return $html;

				return '';
				//return $html;
			}

	// form
		function form_header_text($T, $RR, $post){
			if($RR->get_rsvp_type() != 'waitlist') return $T;


			// if the rsvp was submitted as a normal rsvp before all spaces filled up
			if( $post['rsvp_type'] == 'normal') 
				return evo_lang('All available spaces are taken, but we added you to waitlist for [event-name]');

			return evo_lang('Successfully added to waitlist for [event-name]');
		}
		function rsvp_type($type, $args, $EV){
			// if updating form
			if(isset($args['formtype']) && $args['formtype']=='update') return $type;

			$WL = new EVORSW_Waitlist($EV);
			if(!$WL->is_waitlist_active()) return $type;
			if(!$WL->is_capacity_reached()) return $type;

			$this->WL_on = true;
			return 'waitlist';
		}

		// Under the form subtitle action
		function undersubtitle($args, $EV, $RR){
			if(!$this->WL_on) return false;
			$WL = new EVORSW_Waitlist($EV);
			if(!$WL->is_waitlist_active()) return false;
			
			if( !isset($args['formtype'])) return false;

			$_subtitle = '';
			
			// New form
			if($args['formtype'] =='submit')
				$_subtitle = evo_lang('All the spaces are filled, but you will be added to our waitlist!');
			if($args['formtype'] =='update' && $RR){
				if($RR->get_rsvp_type() == 'waitlist'){
					$_subtitle = evo_lang('You are in our waitlist!') . "<span class='evcal_btn evorsw_remove_wl' >" . evo_lang('Remove me from waitlist') . "</span>";
				}else{
					return false;
				}
			}

			?>
			<p class='evors_subtitle evorsw_form_subtitle'><?php echo $_subtitle; ?></p>
			<?php
		}

		// success message footer note
		function evors_form_success_msg_end($RR){
			if($RR->get_rsvp_type() != 'waitlist') return false;

			?><p><?php evo_lang_e('NOTE: You will be offered space only when all your party size spaces available');?></p><?php
		}

	// save new rsvp
		function rsvp_status_changed($proceed, $RSVP, $RSVP_POST, $remaining_rsvp){

			if( $remaining_rsvp == 'wl'){
				// add RSVP to waitlist
				$WL = new EVORSW_Waitlist($RSVP);				
				$WL->add_to_waitlist($RSVP_POST);

				return true;
			}

			return $proceed;
		}

		function before_save_new_rsvp($args, $eRSVP){

			// before saving validate space left
			$_count = (empty($args['count']))?1: $args['count'];
			$_count = (int)$_count;

			$remaining_rsvp_cap = $eRSVP->remaining_rsvp();

			if($remaining_rsvp_cap == 'wl') $args['rsvp_type'] = 'waitlist';

			return $args;
		}
		function save_new_rsvp($created_rsvp_id, $args, $RR, $eRSVP){

			// skip non waitlist type rsvps
			if($RR->get_rsvp_type() != 'waitlist') return false;

			$WL = new EVORSW_Waitlist($eRSVP);
			
			// add RSVP to waitlist
				$WL->add_to_waitlist($RR);
		}

		// before a form is updated
		// in case of a remove from WL
		function before_updated($bool, $post, $RR, $EVENT){
			if($post['formtype'] != 'wl-remove') return $bool;
			if(!isset($post['rsvpid'])) return $bool;

			$result = wp_trash_post( $post['rsvpid']);
			return 'removed';
		}

		// After a rsvp form have been udpated
		function updated($post, $RR, $EVENT){

			// if event has waitlist DISABLE	
			$eRSVP = new EVORS_Event( $EVENT, $EVENT->ri);
			$WL = new EVORSW_Waitlist($eRSVP, $EVENT->ri);

			
			if(!$WL->is_waitlist_active()) return false;

			$WL_count = $WL->get_waitlist_size();

			// if there is no waitlist
			if(!$WL_count) return false;

			// if updating rsvp >> NO
			if($RR->get_rsvp_status() == 'n'){

				// if waitlist guest updating
				if($RR->get_rsvp_type() == 'waitlist') return false;

				$WL->offer_space_to_waitlist( $RR->count() );
			}
		}

		// additions to rsvp updated success message screen. - @since 2.8.4
		function updated_screen_ads($RSVP_cpt, $RSVP){
			if($RSVP_cpt->get_rsvp_type() != 'waitlist') return false;

			?><h3 class="form_header notice"><?php evo_lang_e('All available spaces are taken, but we added you to waitlist!');?></h3><?php
		}

		// Form Message
			function form_message_check($bool, $form_type, $RSVP, $RR){
				if($form_type != 'wl-remove') return $bool;

				ob_start();
				?>
				<div id='evorsvp_form' class='evors_forms'>
					<div class='rsvp_confirmation form_section' data-rsvpid='<?php echo $RR->ID;?>'>
						<b></b>
						<h3 class="form_header update"><?php evo_lang_e('You are successfully removed from waitlist!');?></h3>
					</div>
				</div>
				<?php
				return ob_get_clean();
			}

	// email
		// admin notification
		// append added to waitlist message and return args
		function new_rsvp_admin_notification($args){	
			$RR = new EVO_RSVP_CPT($args['rsvp_id']);

			if( $RR->get_rsvp_type() == 'waitlist'){
				$notice_message = isset($args['notice_message'])? $args['notice_message'].' - ':'';
				$notice_message = $notice_message .  evo_lang('Added to waitlist!');

				$args['notice_message'] = $notice_message;

			}
			return $args;


		}

}
