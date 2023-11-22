<?php
/** 
 * Invitee Manager - only in admin side
 */

class EVORSI_Manager{

	public function __construct(){
		$ajax_events = array(
			'evorsi_content'=>'content',
			'evorsi_form_submit'=>'submit_form',
			'evorsi_d_msg'=>'delete_msg',
			'evorsi_resent_invite'=>'resend_invite',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, 'nopriv') );
		}

		// ajax thats available to non logged in as well
		$ajax_events = array(
			'evorsi_get_msgs'=>'get_msgs',
			'evorsi_new_msg'=>'new_msg',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class) );
		}

	}

	// no priv
		function nopriv(){
			echo json_encode( array(
				'status'=>'nopriv','content'=> __('Login Needed')
			));exit;
		}

	// NEW & EDIT Invitees
		function submit_form(){	

			$s = $m = $c ='';
			$data = array();

			// process post values
			foreach($_POST as $k=>$v){
				if(in_array($k, array('action','type'))) continue;
				$data[$k] = $v;
			}

			// Adding the new rsvp invitee to DB
			if($_POST['type'] == 'new'){
				$Is = new EVORSI_Invitees($_POST['e_id']);
				
				if(  $RR_id = $Is->add_new_rsvp_post($data)){

					// intiate the rsvp object
					$RR = new EVO_RSVP_CPT($RR_id);

					$RR->create_note('Invitation Created','na');

					EVO()->cal->set_cur('evcal_rs');

					// send invitation email to attendee
					if(!EVO()->cal->check_yn('evors_dis_invitation_email')){
						$subject = EVO()->cal->get_prop('evors_invitation_e_subject');
						$result = EVORS()->email->send_email(
							array(
								'rsvp_id'=> $RR_id,
								'notice_type'=>'invitation',
								'notice_subject'=> ($subject? $subject: 'You are invited!'),
							), 'attendee_notification'
						);

						// if email went through fine
						if($result) $RR->set_prop('status','waiting');
					}

					// notify admin of new invitation creation
					EVORS()->email->send_email( array(
						'rsvp_id'=> $RR_id,
						'notice_title'=> evo_lang('New RSVP Invitation Sent Out'),
						'notice_message'=> evo_lang('New RSVP Invitation has been created and the email has been sent out to the invitee.')
					),
					'notification');

					$s = 'good';
				}else{
					$s = 'bad';
				}

			// save changes to old
			}else{			
				$I = new EVORSI_Invitee($_POST['invitee_id']);

				foreach($data as $k=>$v){
					$I->set_prop( $k, $v);
				}
				$s = 'good';

				$Is = new EVORSI_Invitees($_POST['e_id']);
			}
			
			echo json_encode( array(
				'status'=>$s,
				'json_invitee_rows_data'=>$Is->get_invitees_data()
			));exit;
		}

	// Resend the invitation
		function resend_invite(){
			$RR_id = (int)$_POST['RR_id'];

			$R = EVORS()->email->send_email(
				array(
					'rsvp_id'=> $RR_id,
					'notice_type'=>'invitation',
					'notice_subject'=> ($subject? $subject: 'You are invited!'),
				), 'attendee_notification'
			);
			
			echo json_encode( array(
				'status'=> ($R ? 'good':'bad'),
				'msg'=> ( $R ? __('Invitation Resend Successfully'): __('Could Not Resend, Try Again Later!')),
			));exit;
		}

	// messages
		function get_msgs(){
			$invitee_id = $_POST['invitee_id'];

			$I = new EVORSI_Invitee($invitee_id);
			$Ms = $I->get_json_msgs();

			$C = $Ms;
			if(!$Ms)	$C = 'No Messages';

			echo json_encode(array(
				'status'=>'good',
				'content'=>$C,				
			));
			exit;
		}

		// Posting new message on the wall or to host
		function new_msg(){
			if(!isset($_POST['invitee_id'])){echo json_encode(array('status'=>'bad')); exit;}

			$content = array();

			$invitee_id = $_POST['invitee_id'];
			$I = new EVORSI_Invitee($invitee_id);

			$type = $_POST['type'];
			$_end = isset($_POST['end'])? $_POST['end']:'front';
			$visibility = (isset($_POST['v'] ) && $_POST['v'] == 'yes')? 'public':'private';

			$m = $_POST['m'];

			// Whether message is from guest or admin
			if($type == 'admin'){
				$n = 'admin';
			} else{
				$n = $I->get_full_name();
			}
				
			$result = $I->save_new_msg($m, $n, $visibility);
			
			// if did not save 
			if(!$result){
				echo json_encode(array(
					'status'=>'bad',
					'content'=> array('msg'=>evo_lang('Message could not be saved, try again later!')),				
				));
				exit;
			}

			// EMAILS
				if($type == 'admin'){
					// notify guest
					EVORS()->email->send_email( array(
						'rsvp_id'=> $I->ID,
						'notice_title'=> evo_lang('New message from the host'),
						'notice_message'=> $m,
						'notice_data'=>'no'
					),
					'attendee_notification');
				// message from guest
				}else{
					// notify admin
					EVORS()->email->send_email( array(
						'rsvp_id'=> $I->ID,
						'notice_title'=> evo_lang('New message from').' '. $I->full_name(),
						'notice_message'=> $m,
					),
					'notification');
				}

			// Get all the messages
			if($_end == 'front'){
				$Is = new EVORSI_Invitees($I->event_id() );
				$content['msg_data']['msgs'] = $Is->get_all_messages( 'public', $I);
				$content['msg'] = evo_lang('Message successfully posted!');
			}else{
				$content['msg_data'] = $I->get_json_msgs();
				$content['msg'] = evo_lang('Message successfully posted!');
			}	

			echo json_encode(array(
				'status'=>'good',
				'content'=> $content,				
			));
			exit;
		}

		// Delete a message
		function delete_msg(){
			$invitee_id = $_POST['invitee_id'];
			$I = new EVORSI_Invitee($invitee_id);

			$D = $I->delete_msg( $_POST['i'] );

			if(!$D){ echo json_encode(array('status'=>'bad'));exit; }

			echo json_encode(array('status'=>'good','content'=>$I->get_json_msgs() ));exit;

		}
	
	// content
		// invitee manager content for admin
		function content(){
			$event_id = $_POST['e_id'];

			$IN = new EVORSI_Invitees($event_id);

			ob_start();

			?>
			<div class='evorsi_invitee_manager'>
				<div class="evorsi_head">
					<span class='evorsi_stats stats' ></span>
					<span class='buttons'>
						<?php /*<a class='evo_admin_btn btn_triad evorsi_export' data-eid='<?php echo $event_id;?>'><?php _e('Export List','evorsi');?></a>*/?>
						<a class='evo_admin_btn btn_prime evorsi_new ajde_popup_trig button_evo' data-eid='<?php echo $event_id;?>' data-popc='evorsi_lightbox_two' ><?php _e('Invite New','evorsi');?></a>
					</span>
				</div>
				<div class="evorsi_list">
					<div class="evorsi_list_header">
						<p class='n'><?php _e('Name');?></p>
						<p class='e'><?php _e('email');?></p>
						<p class='s'><?php _e('Status');?></p>
						<p class='a'><?php _e('Actions');?></p>
					</div>				
					<div class='evorsi_list_rows'></div>
				</div>
			</div>
			<?php

			echo json_encode( array(
				'status'=>'good',
				'content'=>ob_get_clean(),
				'json_stats_data'=> $IN->get_stats(),
				'json_stats_temp'=> EVO()->temp->get('evorsi_stats'),
				'json_invitee_rows_data'=> $IN->get_invitees_data(),
				'json_invitee_rows_temp'=> EVO()->temp->get('evorsi_invitee_rows'),
				'json_invitee_form_temp'=> EVO()->temp->get('evorsi_invitee_form'),
				'json_invitee_msgs_temp'=> EVO()->temp->get('evorsi_invitee_msgs')
			));exit;
		}
}
