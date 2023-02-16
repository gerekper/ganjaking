<?php
/**
 * Waitlist for RSVP frontend
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
		
		//event card
		add_filter('evors_remain_rsvp_output',array($this,'eventcard_remaining_rsvp'),10,2);
		add_filter('evors_eventcard_html_srem',array($this,'evc_spots_rem'),10,3);
		add_filter('evors_evc_user_rsvp_txt',array($this,'evc_user_txt'),10,3);

		// form
		add_filter('evors_form_rsvp_type', array($this, 'rsvp_type'),10,3);
		add_filter('evors_form_success_msg_header', array($this, 'form_header_text'),10,2);
		add_action('evors_form_under_subtitle', array($this, 'undersubtitle'),10,3);
		add_action('evors_form_success_msg_end', array($this, 'evors_form_success_msg_end'),10,1);

		// save & update rsvp
		add_action('evors_new_rsvp_saved', array($this, 'save_new_rsvp'),10,4);
		add_filter('evors_rsvp_updated_before', array($this, 'before_updated'),10,4);
		add_filter('evors_rsvp_form_message', array($this, 'form_message_check'),10,4);
		add_action('evors_rsvp_updated', array($this, 'updated'),10,3);

		// email
		add_filter('evors_admin_notification_args', array($this, 'new_rsvp_admin_notification'),10,1);
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
				return 'nocap';
			}

		// event card display 
			function evc_spots_rem($html, $EV, $RR){


				if(!$this->WL_on) return $html;

				$WL = new EVORSW_Waitlist($EV);


				// get the wait list size
				$WL_count =  $WL->get_waitlist_size();			

				if(!$WL->is_waitlist_active()) return $html;

				// if the user has rsvped & waitlist is active
				if($RR) return '';

				$_html_wl_count = ($WL_count )? '<span class="evorsw_wl_size">' . '<i style="font-weight:bold">'. $WL_count . '</i>'. evo_lang('Waitlist Size'). '</span>': evo_lang('Waitlist is empty');
				$_txt = "<span class='evorsw_wl_status'>" . ( $RR ? "<i class='fa fa-check-circle'></i>" . evo_lang('You are in our waitlist!') :  '' ) ."</span>";

				$O = "<div class='evors_section evors_remaining_spots evors_waitlist_remaining_spots'><p class='remaining_count'>";
				$O .= "<span class='evorsw_wl_info'>" . $_html_wl_count . $_txt . "</span>";
				$O .= '</p></div>';

				return $O;
			}

		// evc user rsvp based text
			function evc_user_txt($T, $RSVP, $RR){
				if(!$this->WL_on) return $T;
				if(!$this->WL) return $T;

				if(!$this->WL->is_waitlist_active()) return $T;

				if($RR && $RR->get_rsvp_type()=='normal') return $T;

				$__a = "<em class='evorsw_wl_notice'>" . evo_lang('All spaces are reserved') . "</em>";

				return $RR? evo_lang('You are in our waitlist')
					:$__a. "<em style='display:block;font-style:normal'>". evo_lang('Make sure to add yourself to waitlist!') . "</em>";
			}

	// form
		function form_header_text($T, $RR){
			if($RR->get_rsvp_type() != 'waitlist') return $T;

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
				$WL->offer_space_to_waitlist( $RR->count() );
			}

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
			$notice_message = isset($args['notice_message'])? $args['notice_message']:'';
			$notice_message .= ' - '. evo_lang('Added to waitlist!');

			$args['notice_message'] = $notice_message;
			return $args;

		}

}
new EVORSW_Front();