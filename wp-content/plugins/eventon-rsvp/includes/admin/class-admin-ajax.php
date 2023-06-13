<?php
/**
 * Admin ajax functions
 * @version 2.8
 */

class evors_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'the_ajax_evors_a1'=>'get_attendees_list',
			'the_ajax_evors_a2'=>'sync_rsvp_count',
			'the_ajax_evors_a5'=>'evoRS_admin_resend_emails',
			'the_ajax_evors_a6'=>'evoRS_admin_custom_confirmation',
			'the_ajax_evors_a8'=>'emailing_form',
			'the_ajax_evors_a9'=>'emailing_rsvp_admin',
			'evorsadmin_attendee_info'=>'get_attendee_info',
			'evors_get_event_rsvp_settings'=>'get_rsvp_event_settings',
			'evors_save_event_rsvp_settings'=>'save_rsvp_event_settings',

		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
		$this->post_data = $this->help->sanitize_array( $_POST );
	}

	// rsvp event settings
		function get_rsvp_event_settings(){

			$post_data = $this->help->sanitize_array( $_POST);

			$EVENT = new EVO_Event( $post_data['eid'] );

			ob_start();

			include_once('view-event_settings.php');

			echo json_encode(array(
				'status'=>'good',
				'content'=> ob_get_clean()
			));exit;

		}
		function save_rsvp_event_settings(){

			$post_data = $this->help->sanitize_array( $_POST);
			$EVENT = new EVO_Event( $post_data['event_id'] );

			// save all values to event
			foreach($post_data as $key=>$val){
				$EVENT->set_prop( $key, $val);
			}

			// repeat capacities
			$capacity = 0;
			if( isset($post_data['ri_capacity_rs']) && is_array($post_data['ri_capacity_rs'])){
				foreach($post_data['ri_capacity_rs'] as $cap){
					$capacity = $capacity + ( (int)$cap);
				}

				if( $capacity > 0) $EVENT->set_prop( 'evors_capacity_count', $capacity);			
			}

			echo json_encode(array(
				'status'=>'good',
				'content'=> '',
				'msg'=> __('RSVP Event Values Saved Successfully!')
			));exit;

		}
	
	// GET list of attendees for event
		function get_attendees_list(){


			$status = 0;
			ob_start();

				$post_data = $this->post_data;

				$ri = 'all';
				if( isset($post_data['ri'] ) ) $ri = $post_data['ri'];
				if( isset($post_data['ri'] ) && $post_data['ri'] == '0' )  $ri = '0';

				$RSVP = new EVORS_Event($post_data['e_id'], $ri);

				$ri_count_active = $RSVP->is_ri_count_active();	

				// if repeat counts active -> show selector
				if( $ri_count_active && !isset($post_data['ri'] )  ){

					$datetime = new evo_datetime();
					$wp_date_format = get_option('date_format');	
					$repeats = $RSVP->event->get_repeats();

					$pmv = $RSVP->event->get_data();

					?>
					<div id='evors_view_attendees'>
						<p style='text-align:center'><label><?php _e('Select Repeating Instance of Event','evors');?></label> 
							<select name="" id="evors_event_repeatInstance">
								<option value="all"><?php _e('All Repeating Instances','evors');?></option>
								<?php
								$x=0;								
								foreach($repeats as $interval){
									$time = $datetime->get_correct_formatted_event_repeat_time($pmv,$x, $wp_date_format);
									echo "<option value='".$x."'>".$time['start']."</option>"; $x++;
								}
								?>
							</select>
						</p>
						<p style='text-align:center'><a id='evors_VA_submit' data-e_id='<?php echo $RSVP->event->ID;?>' class='evo_admin_btn btn_prime' ><?php _e('Submit','evors');?></a> </p>
					</div>
					<div id='evors_view_attendees_list'></div>
					<?php 

					$output = ob_get_clean();
					echo json_encode(array(
						'content'=> $output,
						'status'=>$status
					));				
					exit;
				}

				
				//echo $ri=='0'?'t':'y';
				//$ri = ($ri == '0' && $ri_count_active)? 	'0':'all'; // repeat interval
				
				$__checking_status_text = EVORS()->frontend->get_trans_checkin_status();


				$RSVP_LIST = $RSVP->GET_rsvp_list('all');

				// run ajax button data
				$data = array(
					'd'=> array(
						'uid'=>'evors_refresh_guest_list_lb',
						'lightbox_key'=>'evors_view_attendees',
						'ajaxdata'=> array(					
							'e_id'=> $post_data['e_id'],
							'ri'=> $ri,
							'action'=> 'the_ajax_evors_a1',
							'load_lbcontent'=> true
						)
					)
				);

				echo "<div class='evors_list' data-eid='{$post_data['e_id']}'>";

				echo "<div class='evors_list_actions pad5'>
					<span class='evo_trigger_ajax_run evo_btn' ". $this->help->array_to_html_data($data) ."><i class='fa fa-rotate marr5'></i> ".__('Refresh','evors') ."</span>
				</div>";

				echo "<p class='header'>". __('RSVP Status: YES','evors'). "</p>"; 
				if(!empty($RSVP_LIST['y']) && count($RSVP_LIST['y'])>0){
					foreach($RSVP_LIST['y'] as $_id=>$rsvp){
						echo $this->each_attendee_data_row($_id, $rsvp, $__checking_status_text);
					}
				}else{
					echo "<p>".__('No Attendees found.','eventon')."</p>";
				}

				echo "<p class='header'>". __('RSVP Status: MAYBE','evors')."</p>"; 
				if(!empty($RSVP_LIST['m']) && count($RSVP_LIST['m'])>0){
					foreach($RSVP_LIST['m'] as $_id=>$rsvp){
						echo $this->each_attendee_data_row($_id ,$rsvp, $__checking_status_text);
					}
				}else{	echo "<p>".__('No Attendees found.','eventon')."</p>";	}	


				echo "<p class='header'>". __('RSVP Status: NO','evors')."</p>"; 
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
				'status'=>$status,
				'list'=> $RSVP_LIST
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

			$optRS = EVORS()->evors_opt;

			$rsvp_id = (int)$this->post_data['rsvpid'];
			$event_id = (int)$this->post_data['eid'];

			$RSVP_POST = new EVO_RSVP_CPT( $rsvp_id );
			$RSVP = new EVORS_Event( $event_id);

			$rpmv = $RSVP_POST->pmv;
			
			$rsvpArray = array('y'=> __('Yes','evors'),'m'=>__('Maybe','evors'),'n'=>__('No','evors'));

			ob_start();


			?>
			<div class='evors_one_attendee_info'>
				<p class='name'><?php echo (!empty($rpmv['first_name'])? $rpmv['first_name'][0]:'').' '.(!empty($rpmv['last_name'])? $rpmv['last_name'][0]:'');?> (#<?php echo $_POST['rsvpid'];?>)</p>				
			<?php
			

			$array = array(
				'rsvp'=>__('RSVP Status','evors'),
				'email'=>__('Email Address','evors'),
				'phone'=>__('Phone Number','evors'),				
				'e_id'=>__('Event','evors'),
				'repeat_interval'=>__('Event Date','evors'),
				'count'=>__('Spaces Reserved','evors'),
				'names'=>__('Additional Attendees','evors'),
				'updates'=>__('Receive Event Updates','evors'),
			);

			
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

			// from from fields
				$form_fields = EVORS()->rsvpform->get_form_fields($RSVP, $RSVP_POST);

				foreach($form_fields as $key=>$fdata){
					extract( $fdata );
					$value = $RSVP_POST && $RSVP_POST->get_prop($key) ? $RSVP_POST->get_prop($key) :'-';
					echo "<p><em>{$name}</em>".$value."</p>";
				}

			// @since 2.8.4
				do_action('evors_attendee_info_lb_end', $RSVP_POST);

			// edit this attendee information
				echo "<p class='action'><a href='".admin_url('post.php?post='.$_POST['rsvpid'].'&action=edit')."' class='evo_admin_btn'>".__('Edit Attendee Info','eventon')."</p>";

			echo "</div>";

			$return_content = array(
				'status'=>'good',
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

			$synced = $RSVP->sync_rsvp_count('manual_sync');
				ob_start();
			?>
				<p><b><?php echo $synced['y']; ?></b><span>YES</span></p>
				<p><b><?php echo $synced['m'];?></b><span>Maybe</span></p>
				<p><b><?php echo $synced['n'];?></b><span>No</span></p>
				<div class='clear'></div>	
			<?php

			$return_content = array(
				'content'=> ob_get_clean(),
				'status'=>$status,
				'data'=> $synced
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
		function emailing_form(){
			$post_data = $this->post_data;

			$RSVP = new EVORS_Event($post_data['e_id']);
			$ri_count_active = $RSVP->is_ri_count_active();	

			ob_start();?>
			<div id='evors_emailing' class='pad20' style=''>
				<form>
				<?php 
				echo EVO()->elements->process_multiple_elements( array(
					array('type'=>'hidden','name'=>'action','value'=>'the_ajax_evors_a9'),
					array('type'=>'hidden','name'=>'eid','value'=>$RSVP->event->ID),
					array(
						'id'=>'evors_emailing_options','type'=>'dropdown',
						'name'=>__('Select Emailing Type','evors'),
						'options'=> apply_filters('evors_email_attendees_emailing_type', array(
							'coming'=>__('Email to Only Attending Guests','evors'),
							'notcoming'=>__('Email to Guests not Coming to Event','evors'),
							'all'=>__('Email to All Rsvped Guests','evors'),
							'someonenot'=>__('Share Not-coming List to Someone','evors'),
							'someone'=>__('Share Attendees List to Someone','evors'),
						), $RSVP)
					),array(
						'id'=>'evors_att_status','type'=>'dropdown',
						'name'=>__('Attendees Status','evors'),
						'options'=> apply_filters('evors_email_attendees_attedee_status', array(
							'all'=>__('All emails','evors'),
							'receive_updates'=>__('Only guests agreed to receive event updates','evors'),
						), $RSVP)
					)
				));
				
				// if repeat interval count separatly	
					$repeats = $RSVP->event->get_repeats();									
					if($ri_count_active && $repeats ){

						$datetime = new evo_datetime();
						$wp_date_format = get_option('date_format');
						$pmv = $RSVP->event->get_data();		

						if(count($repeats)>0){
							echo "<p><label>". __('Select Event Repeat Instance','evors')."</label> ";
							echo "<select name='repeat_interval' id='evors_emailing_repeat_interval'>
								<option value='all'>".__('All','evors')."</option>";																
							$x=0;								
							foreach($repeats as $interval){
								$time = $datetime->get_correct_formatted_event_repeat_time($pmv,$x, $wp_date_format);
								echo "<option value='".$x."'>".$time['start']."</option>"; $x++;
							}
							echo "</select>";
							echo EVO()->throw_guide("Select which instance of repeating events of this event you want to use for this emailing action.", '',false);
							echo "</p>";
						}
					}
				
				echo EVO()->elements->process_multiple_elements(  array(
					array(
						'type'=>'text','id'=>'emails','name'=>__('Email Addresses (separated by commas)','evors'),
						'row_style'=>'display:none'
					),
					array(
						'type'=>'text','id'=>'email_subject',
						'name'=>__('Subject for email','evors') . ' <abbr class="required" title="required">*</abbr>'
					),
					array(
						'type'=>'textarea','id'=>'email_content','name'=>__('Email message content','evors')  . ' <abbr class="required" title="required">*</abbr>'
					)
				));

				$btn_data = array(
					'd'=> array(
						'lightbox_key'=>'evors_emailing',
						'uid'=>'evors_email_attendees',
					)
				);

				?>
				
				<p><a class='evo_admin_btn btn_prime evors_submit_email_form' <?php echo $this->help->array_to_html_data($btn_data);?>><?php _e('Send Email','evors');?></a></p>
			</form>
			</div>
			
			<?php $emailing_content = ob_get_clean();

			$return_content = array(
				'status'=> 'good',
				'content'=>$emailing_content,
			);
			
			echo json_encode($return_content);		
			exit;

		}
		function emailing_rsvp_admin(){

			$post_data = $this->post_data;

			$eid = $post_data['eid'];
			$type = $post_data['evors_emailing_options'];
			$att_status = isset($post_data['evors_att_status'])? $post_data['evors_att_status']: 'all'; // attendee status
			$RI = !empty($post_data['repeat_interval'])? $post_data['repeat_interval']:'all'; // repeat interval
			$EMAILED = $_message_addition = false;
			$emails = array();

			$RSVP = new EVORS_Event($eid, $RI);
			$guests = $RSVP->GET_rsvp_list('normal', $att_status);

			// email attendees list to someone
			if($type=='someone' || $type == 'someonenot' ){

				$attending = $type =='someone'? true: false;

				$emails = explode(',', str_replace(' ', '', htmlspecialchars_decode($post_data['emails'])));
				
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

			// plug
			$emails = apply_filters('evors_email_attendees_emails_array', $emails, $RSVP, $post_data);

			// emaling
			$EMAILED = $args = array();
			if($emails){				
				$messageBODY = "<div style='padding:15px'>".
					(!empty($post_data['email_content'])? 
						html_entity_decode(stripslashes($post_data['email_content'])).'<br/><br/>':'' ).
					($_message_addition ? $_message_addition:'') . 
					"</div>";

				$messageBODY = EVORS()->email->get_evo_email_body($messageBODY);
				$from_email = EVORS()->email->get_from_email_address();
			
				$args = array(
					'html'=>		'yes',
					'type'=> 		($type == 'someone'? 'regular':'bcc'),
					'to'=> 			$emails,
					'subject'=>		$post_data['email_subject'],
					'from'=>		$from_email,
					'from_email'=>	$from_email,
					'from_name'=>	EVORS()->email->get_from_email_name(),
					'message'=>		$messageBODY,
					'return_details'=> true
				);

				$helper = new evo_helper();
				$EMAILED = $helper->send_email($args);
			}			

			$return_content = array(
				'status'=> 		( isset($EMAILED['result']) && $EMAILED['result'] ? 'good' :'bad'),
				'msg'=> 		( isset($EMAILED['result']) && $EMAILED['result'] ? __('Email Sent') : __('Could not send the email') ),
				'other'=>		$args,
				'error'=> 		(isset($EMAILED['error']) ? $EMAILED['error']: '')
			);
			
			echo json_encode($return_content);		
			exit;
		}

}
new evors_admin_ajax();