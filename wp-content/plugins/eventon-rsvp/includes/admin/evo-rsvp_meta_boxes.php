<?php
/**
 * Meta boxes for evo-rsvp
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/evo-rsvp
 * @version     2.8.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evors_meta_boxes{
	public function __construct(){
		add_action( 'add_meta_boxes', array($this, 'evoRS_meta_boxes') );
		add_action( 'eventon_save_meta', array($this, 'evoRS_save_meta_data'), 10 , 2 );
		add_action( 'save_post', array($this, 'evoRS_save_rsvp_meta_data'), 1 , 2 );

		// Debug reminders
		//$fnc = new evorm_fnc();
		//$result = $fnc->send_email(13, 'evorm_pre_1');

	}
	// Initiate
		function evoRS_meta_boxes(){
			add_meta_box('evors_mb1',__('RSVP Event','evors'), array($this, 'evors_metabox_content'),'ajde_events', 'normal', 'high');
			add_meta_box('evors_mb1',__('RSVP Event','evors'), array($this, 'evoRS_metabox_rsvp'),'evo-rsvp', 'normal', 'high');
			add_meta_box('evors_mb3',__('RSVP Email','evors'), array($this, 'evoRS_notifications_box2'),'evo-rsvp', 'side', 'default');
			add_meta_box('evors_mb4',__('RSVP Notes','evors'), array($this, 'evors_notes'),'evo-rsvp', 'side', 'default');
			
			do_action('evoRS_add_meta_boxes'); // pluggable function
		}	
	
// RSVP Notes
	function evors_notes(){
		global $post;

		$RR = new EVO_RSVP_CPT($post->ID);

		$notes = $RR->get_notes();
		?><div class='evors_notes'><?php
		if(!$notes):
		?>
			<p><?php _e('There are no notes','evors');?></p>
		<?php
		else:

			$EVO_Help = new evo_helper();
			$now = current_time('timestamp');
			
			// notes must be in an array format
			if(is_array($notes)){

				//print_r($notes);
				
				rsort($notes); // reverse the order so recent notes show on top
				foreach($notes as $note){
					$author = isset($note['author']) && $note['author']!= 'na'? $note['author']: false;
					if($author){
						$A = get_user_by('ID',$author);
						$AN = 'by ' . $A->display_name;
					}else{
						$AN = '';
					}

					$timestamp = (int)$note['date'];
					$human_time = $EVO_Help->get_human_time( $now - $timestamp);
					?>
					<p>
						<span class='note'><?php echo $note['note'];?></span>
						<span class='date'>On <?php echo  $human_time . ' ' . __('ago') . ' ' . $AN;?></span>
					</p>
					<?php
				}
			}
			
		endif;
		?></div><?php
	}

// notification email box
	function evoRS_notifications_box2(){
		global $post;

		$RR = new EVO_RSVP_CPT($post->ID);

		$__notification_to_email = (!empty(EVORS()->evors_opt['evors_notfiemailto']) )?
						htmlspecialchars_decode (EVORS()->evors_opt['evors_notfiemailto']):get_bloginfo('admin_email');

		?>
		<h3><?php _e('Re-send Emails','evors');?></h3>
		<div class='evoRS_resend_conf'>
			<div class='evoRS_rc_in'>				
				<?php if($RR->get_rsvp_type()=='normal'):?>
					<p><i><?php echo sprintf(__('Re-send <b>Customer Confirmation</b> email again to %s.','evors'),$RR->email());?></i></p>
					<a class='button evors_resend_email' data-rsvpid='<?php echo $RR->ID;?>' data-t='confirmation'><?php _e('Re-send Email','evors');?></a> 
				<?php endif;?>

				<p><i><?php echo sprintf(__('Re-send <b>Admin Notification</b> email to %s.','evors'),$__notification_to_email);?></i></p>
				<a class='button evors_resend_email' data-rsvpid='<?php echo $RR->ID;?>' data-t='notification'><?php _e('Re-send Email','evors');?></a>
				<p class='message' style='display:none'><?php _e('Email resend action performed!','evors');?></p>
			</div>
		</div>
		
		<?php if($RR->get_rsvp_type()=='normal'):?>
		<div class='evoRS_resend_conf'>
			<div class='evoRS_rc_in'>
				<p><i><?php _e('Send RSVP Emails to other email addresses using below fields. <br/>NOTE: you can send to multiple email address separated by commas.','evors');?></i></p>
				<p class='field'><input type='text' placeholder='Comma separated email addresses' style="width:100%" /></p>
				<p class='field'>
					<select name='type'>
						<option value='confirmation'><?php _e('Confirmation Email','evors');?></option>
						<option value='notification'><?php _e('Admin Notification Email','evors');?></option>
					</select>
					</p>
				<a id='evoRS_custom_email' class='button' data-rsvpid='<?php echo $RR->ID;?>' data-empty='<?php _e('Email field can not be empty!','evors');?>' ><?php _e('Send Email','evors');?></a>
				<p class='message' style='display:none'><?php _e('Email send action performed!','evors');?></p>
			</div>
		</div>
		<?php endif;?>
		<?php

		do_action('evors_rsvppost_confirmation_end',$RR );
	}

// META box for evo-rsvp post page
	function evoRS_metabox_rsvp(){
		global $post, $ajde, $pagenow;
		
		$RR = new EVO_RSVP_CPT($post->ID);

		$RSVP = new EVORS_Event( $RR->event_id(), $RR->repeat_interval());
		
		$pmv = $RR->pmv;
		$optRS = EVORS()->evors_opt;
		
		//$what = $eventon_rs->frontend->send_email(array(
		//	'e_id'=>1335,
		//), 'digest');

		// Debug email templates
			if(isset($_GET['debug']) && $_GET['debug']):

				$email_type = isset($_REQUEST['type'])? $_REQUEST['type']: 'confirmation_email';
				$l = isset($_REQUEST['l'])? $_REQUEST['l']: ''; // template location
				$notice_type = isset($_REQUEST['notice_type'])? $_REQUEST['notice_type']: 'new_rsvp';
				$notice_data = isset($_REQUEST['notice_data'])? $_REQUEST['notice_data']: '';

				$tt = EVORS()->email->_get_email_body(
					apply_filters('evors_preview_email_arg', array(
						'e_id'=> $RR->event_id(),
						'rsvp_id'=> $post->ID,
						'notice_type'=> $notice_type, 
						'notice_title'=> evo_lang('Testing title'), 
						'notice_message'=> evo_lang('Testing notice message'),
						'notice_data'=>$notice_data,
						'password'=>'trgtrgtr'
					)), 
					$email_type,
					$l
				);
				echo $tt;

			endif;
		
		// get translated check-in status
			$checkin_status = EVORS()->frontend->get_checkin_status($RR->checkin_status());			
			wp_nonce_field( plugin_basename( __FILE__ ), 'evorsvp_nonce' );
		?>	
		<div class='eventon_mb' style='margin:-6px -12px -12px'>
		<div style='background-color:#ECECEC; padding:15px;'>
			<div style='background-color:#fff; border-radius:8px;'>

			<table id='evors_rsvp_tb' width='100%' class='evo_metatable'>	

			<?php
				$RSVP_status_array = EVORS()->rsvp_array_;
				$RSVP_status_array[''] = '-';

				$table_rows = array(
					'rsvp_id'=>array(
						'type'=>'normal',
						'name'=> __('RSVP #','evors'),
						'value'=>$post->ID
					),
					'rsvp'=>array(
						'type'=> 'select',
						'name'=> __('RSVP Status','evors'),
						'options'=> $RSVP_status_array,
					),
					'checkin_status'=>array(
						'type'=>'checkin_status',
					),
					'first_name'=>array(
						'type'=>'normal',
						'name'=> __('First Name','evors'),
						'required'=>true,
						'editable'=>true,
						'value'=> (!empty($pmv['first_name']) ? $pmv['first_name'][0]:'')
					),'last_name'=>array(
						'type'=>'normal',
						'name'=> __('Last Name','evors'),
						'required'=>true,
						'editable'=>true,
						'value'=> (!empty($pmv['last_name']) ? $pmv['last_name'][0]:'')
					),'email'=>array(
						'type'=>'normal',
						'name'=> __('Email Address','evors'),
						'required'=>true,
						'editable'=>true,
						'value'=> (!empty($pmv['email']) ? $pmv['email'][0]:'')
					),'count'=>array(
						'type'=>'normal',
						'name'=> __('Count','evors'),
						'editable'=>true,
						'value'=> (!empty($pmv['count']) ? $pmv['count'][0]:'')
					),'phone'=>array(
						'type'=>'normal',
						'name'=> __('Phone','evors'),
						'editable'=>true,
						'value'=> (!empty($pmv['phone']) ? $pmv['phone'][0]:'')
					),
				);

				foreach($table_rows as $key=>$data){
					switch($data['type']){
						case 'normal':
							echo "<tr><td>". $data['name'] .":". (!empty($data['required']) && $data['required']? '*':'') ." </td><td>";
							if(!empty($data['editable']) && $data['editable']){
								echo "<input type='text' name='{$key}' value='".$data['value']."'/>";
							}else{
								echo $data['value'];
							}
							echo "</td></tr>";
						break;
						case 'select':
							?><tr><td><?php echo $data['name'];?>: </td>
							<td><select name='<?php echo $key;?>'>
							<?php 
								foreach($data['options'] as $rsvpOptions=>$rsvpV){
									echo "<option ".( (!empty($pmv[$key]) && $rsvpOptions==$pmv[$key][0])? 'selected="selected"':'')." value='{$rsvpOptions}'>{$rsvpV}</option>";
								}
							?>
							</select>
							</td></tr>
							<?php
						break;
						case 'checkin_status':

							?>
							<tr><td><?php _e('Checkin to Event Status','evors');?>: </td><td>
								<?php if( $RR->get_rsvp_status() == 'n'):?>
									<span class='rsvp_ch_st rsvp_n' ><?php evo_lang_e('Not Coming')?></span>
								<?php else:?>
									<span class='rsvp_ch_st evors_trig_checkin <?php echo $RR->checkin_status();?>' data-status='<?php echo $RR->checkin_status();?>' data-nonce="<?php echo wp_create_nonce(AJDE_EVCAL_BASENAME);?>" data-rsvp_id='<?php echo $post->ID;?>'><?php echo $checkin_status;?></span>
								<?php endif;?>

							<?php do_action('evors_admin_rsvp_cpt_checkinstatus',$RR);?>
							</td></tr>
							<?php 
						break;
					}
				}
			?>
				
				<tr><td><?php _e('Receive Email Updates','evors');?>: </td>
					<td><?php echo $ajde->wp_admin->html_yesnobtn(array(
						'id'=>'updates','input'=>true,
						'default'=>( $RR->get_updates() ? 'yes':'no' )
					));?></td></tr>
				<tr><td><?php _e('Event','evors');?>: </td>
					<td><?php 
						// event for rsvp
						if(empty($pmv['e_id'])){
							$events = get_posts(array('posts_per_page'=>-1, 'post_type'=>'ajde_events'));
							if($events && count($events)>0 ){
								echo "<select name='e_id'>";
								foreach($events as $event){
									echo "<option value='".$event->ID."'>".get_the_title($event->ID)."</option>";
								}
								echo "</select>";
							}
							wp_reset_postdata();
						}else{
							echo '<a href="'.get_edit_post_link($pmv['e_id'][0]).'">'.get_the_title($pmv['e_id'][0]).'</a></td></tr>';
						}

				// REPEATING interval
				if($pagenow!='post-new.php' && !empty($pmv['e_id'])){

					$saved_ri = (!empty($pmv['repeat_interval']) && $pmv['repeat_interval'][0]!='0')?
						$pmv['repeat_interval'][0]:'0';
					$event_pmv = get_post_custom($pmv['e_id'][0]);
					$datetime = new evo_datetime();
					?>
					<tr><td><?php _e('Event Date','evors');?>: </td>
					<td>
					<?php 
					$repeatIntervals = (!empty($event_pmv['repeat_intervals'])? unserialize($event_pmv['repeat_intervals'][0]): false);

					// If the event has repeating instances
					if($repeatIntervals && count($repeatIntervals)>0){
								
						echo "<select name='repeat_interval'>";
						$x=0;
						$wp_date_format = get_option('date_format');
						foreach($repeatIntervals as $interval){
							$time = $datetime->get_int_correct_event_time($event_pmv,$x);
							echo "<option value='".$x."' ".( $saved_ri == $x?'selected="selected"':'').">".date($wp_date_format.' h:i:a',$time)."</option>"; $x++;
						}
						echo "</select>";
					}else{
					// not a repeating event
						$time = $datetime->get_correct_event_repeat_time( $event_pmv,$saved_ri);
						echo $datetime->get_formatted_smart_time($time['start'], $time['end'], $event_pmv);
					}
					?></td></tr>
					<?php
				}

				// additional fields
				for($x=1; $x<= EVORS()->frontend->addFields; $x++){
					// if fields is activated and name of the field is not empty
					if(evo_settings_val('evors_addf'.$x, $optRS) && !empty($optRS['evors_addf'.$x.'_1'])){
						

						// If the field is allowed for the event
						if($RSVP->_show_none_AF()) continue;
						if(!$RSVP->_can_show_AF('AF'.$x)) continue;

						$FIELDTYPE = !empty($optRS['evors_addf'.$x.'_2'])? $optRS['evors_addf'.$x.'_2']:'text';
						$FIELDNAME = !empty($optRS['evors_addf'.$x.'_1'])? $optRS['evors_addf'.$x.'_1']:'Field';
						$FIELDVAL = (!empty($pmv['evors_addf'.$x.'_1']))? $pmv['evors_addf'.$x.'_1'][0]: '-';

						echo "<tr>";

						switch ($FIELDTYPE) {
							case 'text':
								echo "<td>".$FIELDNAME."</td>
								<td><input type='text' name='evors_addf".$x."_1' value='".$FIELDVAL."'/></td>";
								break;	
							case 'checkbox':
								echo "<td>". html_entity_decode($FIELDNAME) ."</td>
								<td>";

								$field_name = 'evors_addf'.$x.'_1';
								echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>$field_name,
									'input'=>true,
									'default'=>((!empty($pmv[$field_name]) && $pmv[$field_name][0]=='yes')? 'yes':'no' )
								));
								echo "</td>";
								break;	
							case 'html':
								echo "<td>".html_entity_decode($FIELDNAME)."</td>
								<td>".$FIELDVAL."</td>";
								break;	
							case 'textarea':
								echo "<td>".$FIELDNAME."</td>
								<td><textarea style='width:100%' name='evors_addf".$x."_1'>".$FIELDVAL."</textarea></td>";
								break;
							case 'dropdown':
								echo "<td>".$FIELDNAME."</td>
								<td><select name='evors_addf{$x}_1'>";
									$OPTIONS = EVORS()->frontend->get_additional_field_options($optRS['evors_addf'.$x.'_4']);
									foreach($OPTIONS as $slug=>$options ){
										echo "<option ".(!empty($pmv['evors_addf'.$x.'_1']) && $slug==$pmv['evors_addf'.$x.'_1'][0]?'selected="selected"':'')." value='{$slug}'>{$options}</option>";
									}
								echo "</select></td>";
								break;
							case 'file':
								echo "<td>".$FIELDNAME."</td><td>";

								if( $FIELDVAL != '-'){
									$url = wp_get_attachment_url( $FIELDVAL );
									if($url) $FIELDVAL = $url;
								}

								echo $FIELDVAL . "</td>";
								
								
								break;


							case has_action("evors_additional_field_evorsvp_cpt_{$FIELDTYPE}"):		
								do_action("evors_additional_field_evorsvp_cpt_{$FIELDTYPE}", $FIELDNAME, $FIELDVAL);
							break;
						}
						echo "</tr>";
					}
				}?>

				<?php
				// addional guest names
				$names = !empty($pmv['names'])? unserialize($pmv['names'][0]): false;
				if($names){
					echo "<tr><td>".__('Other Attendee Names','evors').": </td><td>";
					foreach($names as $name){
						echo "<p>".$name . "</p>";
					}
					echo "</td></tr>";
				}
				?>
				
				<tr><td><?php _e('Additional Notes','evors');?>: </td>
					<td><textarea style='width:100%' type='text' name='additional_notes'><?php echo !empty($pmv['additional_notes'])?$pmv['additional_notes'][0]:'';?></textarea></td></tr>

				<?php
				// plugabble hook
				if(!empty($pmv['e_id']))	
					do_action('eventonrs_rsvp_post_table',$post->ID, $pmv, $RR);
				?>
			</table>
			</div>
		</div>
		</div>
		<?php
	}

// SAVE values for evo-rsvp post 
	function evoRS_save_rsvp_meta_data($post_id, $post){
		if($post->post_type!='evo-rsvp')
			return;
			
		// Stop WP from clearing custom fields on autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)	return;

		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX)	return;
		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( isset($_POST['evorsvp_nonce']) && !wp_verify_nonce( $_POST['evorsvp_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}

		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )	return;	

		global $pagenow;
		$_allowed = array( 'post-new.php', 'post.php' );
		if(!in_array($pagenow, $_allowed)) return;
		
		global $eventon_rs;

		$fields = array(
			'count', 'first_name','last_name','email','phone',
			'rsvp','updates','e_id','repeat_interval',
			'additional_notes'
		);

		// Append additional fields
			for($x = 1; $x <= $eventon_rs->frontend->addFields; $x++){
				$fields[]= 'evors_addf'.$x.'_1';
			}


		// Pluggable action to perform for special fields
			do_action('evors_save_other_metadata', $post_id);

		// Foreach field save data
			foreach($fields as $field){
				if(!empty($_POST[$field])){
					update_post_meta( $post_id, $field, $_POST[$field] );
				}else{
					if($field!='e_id')
						delete_post_meta($post_id, $field);
				}
			}

		// sync event rsvp count
			global $eventon_rs;
			if(!empty($_POST['e_id'])){
				$RSVP = new EVORS_Event( $_POST['e_id'] );
				$RSVP->sync_rsvp_count();
			}
	}

// RSVP meta box for EVENT posts
	function evors_metabox_content(){

		global $post, $eventon_rs, $eventon, $ajde;

		$optRS = EVORS()->evors_opt;
		
		$eventID = $post->ID;

		wp_nonce_field( plugin_basename( __FILE__ ), 'evors_nonce' );

		$help = new evo_helper();
		$EVENT = new EVORS_Event( EVO()->evo_admin->metaboxes->EVENT->ID );
		$pmv = $EVENT->event->get_data();

		// Before event RSVP plug
		do_action('evors_admin_rsvp_event_options_before', $EVENT);

		ob_start();

		$evors_rsvp = $EVENT->event->get_prop('evors_rsvp');
		$evors_show_rsvp = $EVENT->event->get_prop('evors_show_rsvp');
		$evors_show_whos_coming = $EVENT->event->get_prop('evors_show_whos_coming');
		$evors_add_emails = $EVENT->event->get_prop('evors_add_emails');

		?>
		<div class='evo_metabox eventon_mb'>
		<div class="evors">
			<p class='yesno_leg_line ' style='padding:10px'>
				<?php echo eventon_html_yesnobtn(array('var'=>$evors_rsvp, 'attr'=>array('afterstatement'=>'evors_details'))); ?>
				<input type='hidden' name='evors_rsvp' value="<?php echo ($evors_rsvp=='yes')?'yes':'no';?>"/>
				<label for='evors_rsvp'><?php _e('Allow visitors to RSVP to this event','evors')?></label>
			</p>
			<div id='evors_details' class='evors_details evomb_body ' <?php echo ( $evors_rsvp=='yes')? null:'style="display:none"'; ?>>		
				
				<div class="evors_stats">			
					<?php
						// initial values
						$synced = $EVENT->total_rsvp_counts();
						$evors_capacity_count = $EVENT->event->get_prop('evors_capacity_count'); 

					?>
					<p class='y'><b><?php echo $synced['y']; ?></b><span><?php _e('YES','evors');?></span></p>
					<p class='m'><b><?php echo $synced['m'];?></b><span><?php _e('Maybe','evors');?></span></p>
					<p class='n'><b><?php echo $synced['n'];?></b><span><?php _e('No','evors');?></span></p>

					<?php do_action('evors_admin_eventedit_stats_end', $EVENT );?>

				</div>
				
				<?php 
				// stat bar
					if($evors_capacity_count):?>
						<div class='evors_stats_bar'>
							<p><span class='yes' style='width:<?php echo (int)(($synced['y']/$evors_capacity_count)*100);?>%'></span><span class='maybe' style='width:<?php echo (int)(($synced['m']/$evors_capacity_count)*100);?>%'></span><span class='no' style='width:<?php echo (int)(($synced['n']/$evors_capacity_count)*100);?>%'></span></p>
						</div>
						<?php do_action('evors_admin_eventedit_statbar_end',$EVENT, $synced, $evors_capacity_count );?>
				<?php endif;?>
				
				
				<div class='evo_negative_25'>
				<?php

					$btn_data = array(
						'lbvals'=> array(
							'lbc'=>'config_rsvp_data',
							't'=>__('Configure Event RSVP Settings','eventon'),
							'ajax'=>'yes',
							'd'=> array(					
								'eid'=> $eventID,
								'action'=> 'evors_get_event_rsvp_settings',
								'uid'=>'evo_get_rsvp_settings',
								'load_lbcontent'=>true
							)
						)
					);

				?><p class='pad20'><span class='evo_btn evolb_trigger' <?php echo $help->array_to_html_data($btn_data);?>  style='margin-right: 10px'><?php _e('Configure RSVP Settings','eventon');?></span></p>

				<?php	do_action('evors_admin_before_settings', $EVENT);	?>

				<table width='100%' class='eventon_settings_table'>					
					<?php

						// pluggable function for addons
						do_action('evors_event_metafields', $EVENT, $optRS);
					?>
					<tr><td colspan='2' style=''><p style='opacity:0.7'><i><?php _e('NOTE: All text strings that appear for RSVP section on eventcard can be editted via myEventon > languages','evors');?></i></p></td></tr>
				</table>
				</div>
				
				<div class='evors_info_actions'>				
					<p class='actions evo_metabox_action_buttons_area'>
						<?php 
							$btn_data = array(
								'lbvals'=> array(
									'lbc'=>'evors_view_attendees',
									't'=>__('View Attendees','eventon'),
									'ajax'=>'yes',
									'd'=> array(					
										'e_id'=> $eventID,
										'action'=> 'the_ajax_evors_a1',
										'uid'=>'evors_view_attendees',
										'load_lbcontent'=>true
									)
								)
							);

						?>
						<span class='evo_btn evolb_trigger' <?php echo $help->array_to_html_data($btn_data);?>><i class='fa fa-users'></i> <?php _e('View Attendees','evors');?></span>

						<?php
						// DOWNLOAD CSV link 
							$exportURL = add_query_arg(array(
							    'action' => 'the_ajax_evors_f3',
							    'e_id' => $eventID,     // cache buster
							), admin_url('admin-ajax.php'));

						?>
						<a class='evo_btn download' href="<?php echo $exportURL;?>" title='<?php _e('Download (CSV)','evors');?>'><i class='fa fa-download'></i> <?php _e('Download (CSV)','evors');?></a> 
						
						<a id='evors_SY' class='evo_btn' data-e_id='<?php echo $eventID;?>' class=' sync' title='<?php _e('Sync Count','evors');?>'><i class='fa fa-undo'></i> <?php _e('Sync Count','evors');?></a> 
						
						<?php 
							$btn_data = array(
								'lbvals'=> array(
									'lbc'=>'evors_emailing',
									't'=>__('Email Attendees','eventon'),
									'ajax'=>'yes',
									'd'=> array(					
										'e_id'=> $eventID,
										'action'=> 'the_ajax_evors_a8',
										'uid'=>'evors_emailing',
										'load_lbcontent'=>true
									)
								)
							);

						?>
						<span class='evo_btn evolb_trigger' <?php echo $help->array_to_html_data($btn_data);?>><i class='fa fa-envelope'></i> <?php _e('Emailing','evors');?></span>
												
					</p>
					
						

					<p id='evors_message' style='display:none'></p>
					
				</div>
			</div>
		</div>
		</div>
		<?php
		echo ob_get_clean();
	}

	// print out the yes no value HTML for meta box for RSVP
		function html_yesno_fields($var, $label, $pmv , $guide='', $afterstatement='', $id='', $as_type='class'){
			global $eventon;

			$val = (!empty($pmv[$var]))? $pmv[$var][0]:null;
			?>
			<p class='yesno_leg_line '>
				<?php echo eventon_html_yesnobtn(array('id'=>$id, 'var'=>$val, 'attr'=>array('afterstatement'=>$afterstatement,'as_type'=>$as_type)) ); ?>					
				<input type='hidden' name='<?php echo $var;?>' value="<?php echo ($val=='yes')?'yes':'no';?>"/>
				<label for='<?php echo $var;?>'><?php _e($label,'evors')?><?php echo !empty($guide)? $eventon->throw_guide($guide, '',false):'';?></label>
			</p>
			<?php
		}

/** Save the menu data meta box. **/
	function evoRS_save_meta_data($arr, $post_id){
		$fields = apply_filters('evors_event_metafield_names', array( 'evors_rsvp' ), $post_id);

		foreach($fields as $field){
			if(!empty($_POST[$field])){
				update_post_meta( $post_id, $field, $_POST[$field] );
			}else{
				delete_post_meta($post_id, $field);
			}
		}
			
	}
}
new evors_meta_boxes();