<?php
/**
 * FRONTEND
 */

class evorsi_frontend{

	public $invitee = false;
	public $invitees = false;
	public $invite_activated = false;

	public function __construct(){
		// General
		add_filter('evors_checking_status_text_ar',array($this, 'checking_status_texts'),10,1);


		// event top
		add_filter('evors_eventtop_show_content', array($this, 'eventtop_show_content'),10,2);
		
		add_filter('evors_rsvp_byauthor', array($this, 'eventcard_rsvp'), 10, 2);
		add_filter('evo_event_json_data', array($this, 'event_data'), 10, 2);
		add_filter('evo_event_run_json_onclick', array($this, 'run_js'), 10, 2);
		
		// event card
		add_filter('evors_eventcard_content_show', array($this, 'eventcard_content_filter'), 10, 2);
		add_filter('evors_eventcard_show_subtitle', array($this, 'EC_show_subtitle'), 10, 2);
		add_filter('evors_evc_user_rsvp_txt', array($this, 'after_subtitle'), 10,3);
		add_filter('evors_eventcard_show_remaining_rsvp_section', array($this, 'EC_show_remaining_section'), 10, 2);
		add_action('evors_eventcard_notshow_content', array($this, 'eventcard_notshow_content'), 10, 2);
		add_action('evors_eventcard_end_rsvp', array($this, 'eventcard_message_wall'), 10, 3);
		add_filter('evors_eventcard_selection_data_array', array($this, 'selection_data_array'), 10,1);
		

		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);
		add_action( 'eventon_enqueue_styles', array($this,'print_styles' ));
		add_action( 'eventon_enqueue_scripts', array($this,'print_scripts' ));

		// rsvp form		
		add_filter('evors_form_rsvp_type', array($this, 'evors_form_rsvp_type'), 10, 4);
		add_filter('evors_rsvp_form_args', array($this, 'form_args'), 10, 2);
		add_filter('evors_form_event_title', array($this, 'form_title'), 10, 3);
		add_filter('evors_form_event_subtitle', array($this, 'form_subtitle'), 10, 3);
		add_filter('evors_form_hidden_values', array($this, 'hidden_field'), 10, 4);
		add_action('evors_form_under_subtitle', array($this, 'form_under_subtitle'), 10, 3);

		// SAVING & Updating
			add_action('evors_rsvp_updated', array($this, 'update_rsvp'),10, 3);
			add_filter('evors_rsvp_form_message', array($this, 'form_message_check'),10,5);
		
		// event load content
		add_filter('evo_single_event_content_data', array($this, 'event_content'), 10,2);

		// EMAIL
		add_action('evors_attendee_notification_invitation', array($this, 'invitation_email'), 10, 3);
	}

	// GENERAL		
		function checking_status_texts($A){
			$A['created'] = evo_lang('created');
			$A['waiting'] = evo_lang('waiting');
			return $A;
		}

	// Styles
		function register_styles_scripts(){
			wp_register_style( 'evorsi_styles',EVORSI()->assets_path.'evorsi.css', '', EVORSI()->version);

				wp_register_script('evorsi_script',EVORSI()->assets_path.'evorsi.js',
					array('jquery','jquery-ui-core'),
					EVORSI()->version, true );

				wp_localize_script(
					'evorsi_script',
					'evorsi_ajax_script',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ) ,
						'postnonce' => wp_create_nonce( 'evorsi_nonce' )
					)
				);
		}
		function print_scripts(){	wp_enqueue_script('evorsi_script');		}
		function print_styles(){	wp_enqueue_style( 'evorsi_styles');		}

	// PRE LOAD DATA to event content
		function event_data($A, $event_id){
			if(!isset($_REQUEST['invite'])) return $A;

			$A['invite'] = $_REQUEST['invite'];
			return $A;
		}
		function run_js($bool, $EVENT){
			if(!isset($_REQUEST['invite'])) return $bool;
			return true;
		}

	// AJAX
		function event_content($array, $EVENT){
			if(!isset($_REQUEST['invite'])) return $array;
			if(!$EVENT->check_yn('evors_rsvp')) return $array;
			if(!$EVENT->check_yn('evorsi_invitees')) return $array;			
			
			$Is = new EVORSI_Invitees($EVENT);

			$I = $Is->is_invited($_REQUEST['invite']); 
			if(!$I) return $args;

			$array['json_temp_evorsi_wall'] = EVO()->temp->get('evorsi_wall');
			$array['json_temp_evorsi_notice'] = EVO()->temp->get('evorsi_notice');

			$array['mm']['msgs'] = $Is->get_all_messages('public', $I);

			return $array;
		}

	// EVENT CARD & EVENT TOP		
		// EVNET TOP
			// hide event top content if invitee enabled
			function eventtop_show_content($bool, $RSVP){
				if(!$RSVP->event->check_yn('evorsi_invitees')) return $bool;

				return false;
			}

	// EVENT CARD
		function selection_data_array($A){
			if($this->invitee === false) return $A;

			$A['invite'] = $_REQUEST['invite'];
			$A['cap'] = $this->invitee->count();

			// if this is the first time, then rsvp is empty
			$A['invite_status'] = $this->invitee->get_rsvp_status()? 'na':'first_time';

			return $A;
		}
		// show subtitle override
			function EC_show_subtitle($B, $EV){
				if(!$this->invite_activated) return $B;
				return ($this->invitee === false)? false: true;
			}

		// after subtitle
			function after_subtitle($T, $RSVP, $RR){

				// Initial checks
				if(!$RR) return $T;
				if(!$this->invite_activated) return $T;

				$invitee_name = $RR->full_name();

				$MSG = $RR->checkin_status_normal()?
					($RR->get_rsvp_status() == 'n'? 
						evo_lang('Sorry to hear you can not make it to the event!'): 
						evo_lang('We look forward to seeing you at the event!')):
					evo_lang('We look forward to seeing you at our event! Please let us know your attendance!');
				return  '<i>'. $invitee_name .'</i>, '. $MSG ;
			}

		// show remaingin tickets section
			function EC_show_remaining_section($B,$EV){
				return ($this->invite_activated)? false: $B;				
			}

		// hide RSVP if only invitees can RSVP
			function eventcard_notshow_content($RSVP, $EVENT){				
				// if invitee only RSVP active
				if($this->invite_activated ){
					if(!isset($_REQUEST['invite']) || !$RSVP->is_invited($_REQUEST['invite'])){
						evo_lang_e('Only invited guests are allowed to RSVP!');			
					}
				}
			}

		// filter get_rsvp_id() function and if there is an invitee link return the invitee ID
		// RUN on evo event load
		function eventcard_rsvp($bool, $EVORS_Event){
			$this->RSVP = $EVORS_Event;

			if(!$this->invitees){
				$this->invitees = new EVORSI_Invitees($EVORS_Event->event->ID);

				if(!$this->invitees->is_invitee_active()) return $bool;

				$this->invite_activated = true;
				if(!isset($_REQUEST['invite'])) return $bool;	

				if( $II = $this->invitees->is_invited($_REQUEST['invite']) ){
					$this->invitee = $II;
					return $II->rsvp_id;
				}
			}

			return $bool;
		}

		// should rsvp content show filter
		function eventcard_content_filter($B, $RSVP){
			if(!$this->invite_activated) return $B;

			if($this->invitee === false) return false;
			if(!isset($_REQUEST['invite'])) return false;
			return true;
			
		}
		// event card message wall content
			function eventcard_message_wall($RSVP, $RR){
				if($this->invitee === false) return false;

				if(!$this->invitees) $this->invitees = new EVORSI_Invitees($RSVP->event->ID);

				// pre checks
					$RSVP = $this->invitees;
					if(!$RSVP->is_invitee_active() ) return false;
					if(!$RSVP->is_invited($_REQUEST['invite'])) return false;
					if(!$RR) return false;

					// if messaging is not enabled
					if(!$RSVP->is_messaging_on()) return false;

				// Number of messages to show on load in the wall
					$no_messages = apply_filters('evorsi_msg_wall_messages',2);

				$HELP = new evo_helper();
				?>
				<div class='evors_section evorsi_message' >
					<div class="evorsi_message_wall_outter marb10">
						<h4 class='evo_h4'><?php evo_lang_e('Message Wall');?></h4>
						<div class="evorsi_message_wall mart5" data-s='<?php echo $no_messages;?>'></div>
					</div>

					<div class="evorsi_msg_form marb20">
						<p><span class='evorsi_msg_form_title'><?php echo $RSVP->is_invitee_wall()? 
							evo_lang('Post a message to wall or message host'): 
							evo_lang('Send a message to host');?></span></p>
						<input type="hidden" name='iid' value='<?php echo $RR->ID?>'/>
						<input type="hidden" name='eid' value='<?php echo $RSVP->event_id?>'/>
						<textarea class='marb10 evorsi_msgs_msg' cols="30" rows="3" placeholder="<?php evo_lang_e('Type your message here...');?>"></textarea>
						
						<p class='evorsi_msgform_bottom'><span class="evors_btn evorsi_send_msg" style='margin-right:10px'><?php evo_lang_e('Post Message');?></span>
						<span><?php
						echo $HELP->html_yesnobtn(
							array(
								'id'=>'visibility','input'=>true,
								'label'=> evo_lang('Post message to wall as well'),
							)
						);
						?>
						</span></p>
					</div>
				</div>
				<?php
			}

	// FORM
		// form rsvp type
			function evors_form_rsvp_type($type, $args, $eRSVP, $RR){
				if(!isset($args['invite'])) return $type;

				$I = $this->invitees = new EVORSI_Invitees($eRSVP->event);

				// checks
				if(!$I->is_invitee_active()) return $type;

				// set object values for later
				$this->invite_activated = true;

				// check if invited
				$II = $this->invitees->is_invited($args['invite']);
				if(!$II) return $type;

				$this->invitee = $II;
				
				return 'invitee';
			}

		// hidden fields
			function hidden_field($A, $args, $RSVP, $RR){
				if(!isset($args['invite_status'])) return $A;

				$A['invite_status'] = $args['invite_status'];
				return $A;
			}

		// Form args
			function form_args($args, $RSVP){

				if(!$this->invite_activated) return $args;

				$args['fname'] = $RSVP->first_name()? $RSVP->first_name():'';
				$args['lname'] = $RSVP->last_name()? $RSVP->last_name():'';
				$args['email'] = $RSVP->email()? $RSVP->email():'';

				return $args;
			}	
	
		// form title
			function form_title($title, $args, $RR){

				if(!$this->invite_activated) return $title;
				if(!$this->invitee) return $title;

				return evo_lang('You are invited to [event-name]');
			}
		// form subtitle
			function form_subtitle($subtitle, $RR, $args){
				if(!$this->invite_activated) return $subtitle;
				if(!$this->invitee) return $subtitle;
				return '';
			}

		// form under subtitle
			function form_under_subtitle($args, $eRSVP, $RR){
				if(!$this->invite_activated) return $args;
				if(!$this->invitee) return $args;

				echo "<p style='padding-bottom:10px'>" . evo_lang('Please use the form below to let us know your attendance!') . "</p>";
			}

	// SAVE & Update
		// upadating the rsvp
			function update_rsvp($post, $RR, $EVENT){
				if(!isset($post['rsvp_type'])) return false;
				if($post['rsvp_type'] != 'invitee') return false;

				// check if the rsvp status is normla
				if($RR->checkin_status_normal()) return false;

				// change the status 
				$RR->set_prop('status', 'check-in');

				// add a note
				$RR->create_note('Guest has responded. RSVP status: '. EVORS()->frontend->get_rsvp_status($post['rsvp']) );

				// send email to admin
				EVORS()->email->send_email( array(
					'rsvp_id'=> $RR->ID,
					'notice_title'=> evo_lang('A guest has replied to invitation'),
					'notice_message'=> evo_lang('You have recived a respond from the guest for the invitation!')
				),'notification');

				// Send event confirmation with QR etc. to attendee
				EVORS()->email->send_email(array(
					'rsvp_id'=> $RR->ID,
				), 'confirmation');
			}

		// Form Message
			function form_message_check($bool, $form_type, $RSVP, $RR, $post){
				if($RR->get_rsvp_type() != 'invitee') return $bool;
				if(!isset($post['invite_status'])) return $bool;
				
				// Success form message based on first time replying to invite or changing
				$_message = $post['invite_status'] == 'first_time'? 
					evo_lang('Thank you for responding to our invitation!'): 
					evo_lang('Thank you for making changes to your reservation!');

				ob_start();
				?>
				<div id='evorsvp_form' class='evors_forms'>
					<div class='rsvp_confirmation form_section' data-rsvpid='<?php echo $RR->ID;?>'>
						<b></b><h3 class="form_header update"><?php echo $_message;?></h3>
					</div>
				</div>
				<?php
				return ob_get_clean();
			}
	
	// EMAIL
		// build the invitation email using the action hook in attendee email
		function invitation_email($RR, $RSVP, $args){

			$I = new EVORSI_Invitee($RR->ID);

			ob_start();

			$args = $args;

			$file_location = EVO()->template_locator(
				'invitation_email.php', 
				EVORSI()->addon_data['plugin_path']."/templates/", 
				'templates/email/rsvp/'
			);

			include($file_location);
			
			echo ob_get_clean();

		}	
	
}