<?php
/**
 * Admin ajax functions
 * @version 2.5.5
 */

class evors_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'the_ajax_evors_a1'=>'get_attendees_list',
			'the_ajax_evors_a2'=>'sync_rsvp_count',
			'the_ajax_evors_a5'=>'evoRS_admin_resend_emails',
			'the_ajax_evors_a6'=>'evoRS_admin_custom_confirmation',
			'the_ajax_evors_a9'=>'emailing_rsvp_admin',
			'evorsadmin_attendee_info'=>'get_attendee_info',

		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	
	// GET list of attendees for event
		function get_attendees_list(){
			global $eventon_rs;
			$status = 0;
			ob_start();

				$ri = !empty($_POST['ri'])? $_POST['ri']: '0';
				$RSVP = new EVORS_Event($_POST['e_id'], $ri);

				$event_pmv = get_post_custom($_POST['e_id']);			

				//$ri_count_active = $RSVP->is_ri_count_active();

				
				//echo $ri=='0'?'t':'y';
				//$ri = ($ri == '0' && $ri_count_active)? 	'0':'all'; // repeat interval
				
				$__checking_status_text = EVORS()->frontend->get_trans_checkin_status();


				$RSVP_LIST = $RSVP->GET_rsvp_list('all');

				
				echo "<div class='evors_list'>";
				echo "<p class='header'>RSVP Status: YES</p>"; /***/
				if(!empty($RSVP_LIST['y']) && count($RSVP_LIST['y'])>0){
					foreach($RSVP_LIST['y'] as $_id=>$rsvp){
						echo $this->each_attendee_data_row($_id, $rsvp, $__checking_status_text);
					}
				}else{
					echo "<p>".__('No Attendees found.','eventon')."</p>";
				}

				echo "<p class='header'>RSVP Status: MAYBE</p>"; /***/
				if(!empty($RSVP_LIST['m']) && count($RSVP_LIST['m'])>0){
					foreach($RSVP_LIST['m'] as $_id=>$rsvp){
						echo $this->each_attendee_data_row($_id ,$rsvp, $__checking_status_text);
					}
				}else{	echo "<p>".__('No Attendees found.','eventon')."</p>";	}	


				echo "<p class='header'>RSVP Status: NO</p>"; /***/
				if(!empty($RSVP_LIST['n']) && count($RSVP_LIST['n'])>0){
					foreach($RSVP_LIST['n'] as $_id=>$rsvp){
						echo "<div class='evors_rsvp_no_attendees'>";
						echo $this->each_attendee_data_row($_id ,$rsvp, $__checking_status_text);
						echo "</div>";
					}
				}else{	echo "<p>".__('No Attendees found.','eventon')."</p>";	}			

				echo "</div>";

			$output = ob_get_clean();
			echo json_encode(array(
				'content'=> $output,
				'status'=>$status
			));				
			exit;
		}

		function each_attendee_data_row($_id, $rsvp, $text){
			ob_start();
			
			$phone = !empty($rsvp['phone'])? $rsvp['phone']:false;
			$status_var = (!empty($rsvp['status']))? $rsvp['status']:'check-in';
			$_status = isset($text[$status_var]) ? $text[$status_var] : $status_var;

			$checkable = in_array($status_var, array('checked','check-in'))? true:false;

			?>
			<p data-rsvpid='<?php echo $_id;?>'>
				<em class='evorsadmin_rsvp' title='<?php _e('Click for more information','eventon');?>'><?php echo '#'.$_id;?></em>
				<?php echo ' '. $rsvp['name'];?> 				
				
				<span class='checkin <?php echo $checkable?'evors_trig_checkin ':''; echo $status_var;?>' data-rsvp_id='<?php echo $_id;?>' data-status='<?php echo $status_var;?>' data-nonce="<?php echo wp_create_nonce(AJDE_EVCAL_BASENAME);?>"><?php echo $_status;?></span>
				
				<span><?php echo $rsvp['count'];?></span>

				<i><?php echo $rsvp['email'].( $phone? ' PHONE:'.$phone:'');?></i>
				
				<?php 
				// if RSVP have other names show those as well
				if($rsvp['names']!= 'na' && is_array($rsvp['names'])):

					$names = array_filter($rsvp['names']);
				?>
					<span class='other_names'><?php 
						echo implode(', ', $names);
					?></span>
				<?php endif;?>
			</p>
			<?php
			return ob_get_clean();
		}
		function get_attendee_info(){
			global $eventon_rs;

			$optRS = EVORS()->evors_opt;
			$rpmv = get_post_custom($_POST['rsvpid']);
			$rsvpArray = array('y'=>'Yes','m'=>'Maybe','n'=>'No');

			ob_start();
			?>
				<p class='name'><?php echo (!empty($rpmv['first_name'])? $rpmv['first_name'][0]:'').' '.(!empty($rpmv['last_name'])? $rpmv['last_name'][0]:'');?> (#<?php echo $_POST['rsvpid'];?>)</p>				
			<?php

			$array = array(
				'rsvp'=>__('RSVP Status','eventon'),
				'email'=>__('Email Address','eventon'),
				'phone'=>__('Phone Number','eventon'),				
				'e_id'=>__('Event','eventon'),
				'repeat_interval'=>__('Event Date','eventon'),
				'count'=>__('Spaces Reserved','eventon'),
				'names'=>__('Additional Attendees','eventon'),
				'updates'=>__('Receive Event Updates','eventon'),
			);

			// additional fields
				for($x=1; $x<= EVORS()->frontend->addFields; $x++){
					if(evo_settings_val('evors_addf'.$x, $optRS) && !empty($optRS['evors_addf'.$x.'_1'])){
						if($optRS['evors_addf'.$x.'_2']=='html') continue;
						$array['evors_addf'.$x.'_1'] = $optRS['evors_addf'.$x.'_1'];
					}
				}

			foreach($array as $key=>$val){
				if(!empty($rpmv[$key])){
					$value = $rpmv[$key][0];

					switch($key){
						case 'rsvp':
							$value = $rsvpArray[$value];
						break;
						case 'e_id':
							$value = get_the_title($value);
						break;
						case 'repeat_interval':
							$event_pmv = get_post_custom($rpmv['e_id'][0]);
							$saved_ri = !empty($rpmv['repeat_interval'])? $rpmv['repeat_interval'][0]:0;
							$datetime = new evo_datetime();
							$repeatIntervals = (!empty($event_pmv['repeat_intervals'])? unserialize($event_pmv['repeat_intervals'][0]): false);
							$time = $datetime->get_correct_event_repeat_time( $event_pmv,$saved_ri);
							$value = $datetime->get_formatted_smart_time($time['start'], $time['end'], $event_pmv);
						break;
						case 'names':
							$value = implode(', ', unserialize($value) );
						break;						
					}		
					echo "<p><em>{$val}</em>".$value."</p>";
				}
			}

			// checking status
				$checkinSTATUS = $_checkinST = (!empty($rpmv['status']))? $rpmv['status'][0]:'check-in';
				$status = EVORS()->frontend->get_checkin_status($checkinSTATUS);
				echo "<p class='status' data-rsvpid='{$_POST['rsvpid']}' data-status='{$checkinSTATUS}'><em>".__('Checkin Status','eventon').'</em>'.$status.'</p>';

			// edit this attendee information
				echo "<p class='action'><a href='".admin_url('post.php?post='.$_POST['rsvpid'].'&action=edit')."' class='evo_admin_btn'>".__('Edit Attendee Info','eventon')."</p>";

			$return_content = array(
				'status'=>'0',
				'content'=>ob_get_clean()
			);			
			echo json_encode($return_content);		
			exit;
		}

	// SYNC count
		function sync_rsvp_count(){
			$status = 0;
			$e_id = $_POST['e_id'];

			$RSVP = new EVORS_Event( $e_id );

			$synced = $RSVP->sync_rsvp_count();
				ob_start();
			?>
				<p><b><?php echo $synced['y']; ?></b><span>YES</span></p>
				<p><b><?php echo $synced['m'];?></b><span>Maybe</span></p>
				<p><b><?php echo $synced['n'];?></b><span>No</span></p>
				<div class='clear'></div>	
			<?php

			$return_content = array(
				'content'=> ob_get_clean(),
				'status'=>$status
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// resend confirmation
		function evoRS_admin_resend_emails(){
			
			$rsvp_id = $_POST['rsvp_id'];			
			$rsvp_pmv = get_post_custom($rsvp_id);
			$T = isset($_POST['T'])? $_POST['T']: 'confirmation';

			$args['rsvp_id'] = $rsvp_id;
			$args['first_name'] = (!empty($rsvp_pmv['first_name']))?$rsvp_pmv['first_name'][0]:null;
			$args['last_name'] = (!empty($rsvp_pmv['last_name']))?$rsvp_pmv['last_name'][0]:null;
			$args['email'] = (!empty($rsvp_pmv['email']))?$rsvp_pmv['email'][0]:null;
			$args['e_id'] = (!empty($rsvp_pmv['e_id']))?$rsvp_pmv['e_id'][0]:null;
			$args['rsvp'] = (!empty($rsvp_pmv['rsvp']))?$rsvp_pmv['rsvp'][0]:null;
			$args['repeat_interval'] = (!empty($rsvp_pmv['repeat_interval']))?$rsvp_pmv['repeat_interval'][0]:0;

			$send_mail = EVORS()->email->send_email($args, $T);

			$return_content = array(
				'status'=>'0',
				'send'=> ($send_mail?'sent':'no')
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// send custom emails
		function evoRS_admin_custom_confirmation(){
			
			$rsvp_id = sanitize_text_field($_POST['rsvp_id']);			
			$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']): 'confirmation';

			if( !isset($_POST['email'])){
				echo json_encode(array(
					'status'=>'bad',
					'result'=>'Missing email'
				));exit;
			} 


			$RR = new EVO_RSVP_CPT( $rsvp_id);	

			$rsvp_pmv = get_post_custom($rsvp_id);

			$args['rsvp_id'] = $rsvp_id;
			$args['first_name'] = $RR->first_name();
			$args['last_name'] = $RR->last_name();
			$args['email'] = sanitize_text_field( $_POST['email'] );
			$args['e_id'] = $RR->event_id();
			$args['rsvp'] = $RR->get_rsvp_status();
			$args['repeat_interval'] = $RR->repeat_interval();
			$args['method'] = 'manual';

			$args['return_details']= true;
			

			$args['attachments']= $RR->get_attachments();


			$send_mail = EVORS()->email->send_email($args, $type);

			$return_content = array(
				'status'=>'0',
				'result'=>$send_mail
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// emaling attendees
		function emailing_rsvp_admin(){

			$eid = $_POST['eid'];
			$type = $_POST['type'];
			$att_status = isset($_POST['att_status'])? $_POST['att_status']: 'all'; // attendee status
			$RI = !empty($_POST['repeat_interval'])? $_POST['repeat_interval']:'all'; // repeat interval
			$EMAILED = $_message_addition = false;
			$emails = array();

			$RSVP = new EVORS_Event($eid, $RI);
			$guests = $RSVP->GET_rsvp_list('normal', $att_status);

			// email attendees list to someone
			if($type=='someone' || $type == 'someonenot' ){

				$attending = $type =='someone'? true: false;

				$emails = explode(',', str_replace(' ', '', htmlspecialchars_decode($_POST['emails'])));
				
				if(is_array($guests) && isset($guests['y']) && count($guests['y'])>0){
					ob_start();
					
					$datetime = new evo_datetime();
					$epmv = get_post_custom($eid);
					$eventdate = $datetime->get_correct_formatted_event_repeat_time($epmv, ($RI=='all'?'0':$RI));

					// All the supported fields
					$emailfields = apply_filters('evors_email_someone_fields', array(
						'count'=>'Count',
						'lname'=>'Last Name',
						'fname'=>'First Name',
						'email'=>'Email',
					));


					echo "<p>". ($attending? 'Guests Attending to':'Guests Not-attending to' )."  ".get_the_title($eid)." on ".$eventdate['start']."</p>";

					echo "<div>";
					//echo "<table style='padding-top:15px; width:100%;text-align:left'><thead><tr>";
					foreach($emailfields as $fieldnames){
						//echo "<p>".$fieldnames."</p>";
					}
					//echo "</tr></thead><tbody>";

					$rsvp_type = $attending? 'y':'n';

					// Foreach guest name
					foreach($guests[$rsvp_type] as $guest){
						echo "<div>";

						foreach($emailfields as $field=>$v){
							echo "<span style='padding-right:5px;'>". ($field == 'count'? 'x':'') . (!empty($guest[$field])? $guest[$field]:'') . ($field == 'count'? ' -':'') . "</span>";
						}

						echo "</div>";
					}
					//echo "</tbody></table>";
					$_message_addition = ob_get_clean();
				}

			}elseif($type=='coming'){
				foreach(array('y','m') as $rsvp_status){
					if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
						foreach($guests[$rsvp_status] as $guest){
							if(!isset($guest['email'])) continue;
							$emails[] = $guest['email'];
						}
					}
				}
			}elseif($type=='notcoming'){
				if(is_array($guests) && isset($guests['n']) && count($guests['n'])>0){
					foreach($guests['n'] as $guest){
						$emails[] = $guest['email'];
					}
				}
			}elseif($type=='all'){
				foreach(array('y','m','n') as $rsvp_status){
					if(is_array($guests) && isset($guests[$rsvp_status]) && count($guests[$rsvp_status])>0){
						foreach($guests[$rsvp_status] as $guest){
							$emails[] = $guest['email'];
						}
					}
				}
			}

			// emaling
			$EMAILED = array();
			if($emails){				
				$messageBODY = "<div style='padding:15px'>".
					(!empty($_POST['message'])? 
						html_entity_decode(stripslashes($_POST['message'])).'<br/><br/>':'' ).
					($_message_addition ? $_message_addition:'') . 
					"</div>";

				$messageBODY = EVORS()->email->get_evo_email_body($messageBODY);
				$from_email = EVORS()->email->get_from_email_address();
			
				$args = array(
					'html'=>'yes',
					'type'=> ($type == 'someone'? 'regular':'bcc'),
					'to'=> $emails,
					'subject'=>$_POST['subject'],
					'from'=>$from_email,
					'from_email'=>$from_email,
					'from_name'=>EVORS()->email->get_from_email_name(),
					'message'=>$messageBODY,
					'return_details'=> true
				);

				$helper = new evo_helper();
				$EMAILED = $helper->send_email($args);
			}			

			$return_content = array(
				'status'=> ( $EMAILED['result'] ? '0' :'did not go'),
				'other'=>$args,
				'error'=> (isset($EMAILED['error']) ? $EMAILED['error']: '')
			);
			
			echo json_encode($return_content);		
			exit;
		}

}
new evors_admin_ajax();