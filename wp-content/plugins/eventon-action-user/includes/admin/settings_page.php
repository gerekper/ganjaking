<?php
/**
 * Action User settings page
 * @version 2.2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoau_settings{
	public function __construct(){
		$this->opt = get_option('evcal_options_evoau_1');
		$this->adminEmail = get_option('admin_email');
	}

	function content_email_body_instructions(){
		return "Supported email tags that can be used in email message body: <code>{event-edit-link}</code>,<code>{event-name}</code>,<code>{event-link}</code>,<code>{event-start-date}</code>, <code>{event-start-time}</code>, <code>{event-end-date}</code>, <code>{event-end-time}</code>, <code>{new-line}</code><br/><br/>IF available only tags: <code>{submitter-name}</code>,<code>{submitter-email}</code><br/> ** Name and Email fields must be enabled in event submission form for these variables to work.<br/>HTML codes also can be used inside email message body.";
	}

function content(){
	global $eventon, $eventon_au;
	
	// Settings Tabs array
	$evcal_tabs = apply_filters('evoau_settings_tabs',array(
		'evoau_1'=>__('General'), 
		'evoau_2'=>__('User Capabilities'),
	));	
	
	$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evoau_1';
	$settings_page_role = (isset($_POST['current_role']))? $_POST['current_role']: 'administrator';
		
	// Update or add options
		if( isset($_POST['evoau_noncename']) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST['evoau_noncename'], AJDE_EVCAL_BASENAME ) ){
				
				// update role caps
				if($focus_tab=='evoau_2'){				
					
					// User cap edit
					if(isset($_GET['object']) && isset($_GET['user_id'])){
						
						$current_edit_user = $_GET['user_id'];
						
						if(isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'] , 'evo_user_'.$current_edit_user) ){
							
							EVOAU()->admin->update_role_caps($current_edit_user, 'user','post');
							$_POST['settings-updated']='Capabilities updated successfully.';	
						}else{
							$_POST['settings-updated']='Could not verify required information.';
						}
						
					// role cap edit
					}else{				
						if($settings_page_role!='administrator'){
							//echo $role;
							EVOAU()->admin->update_role_caps($settings_page_role, 'role','post');
							
							$_POST['settings-updated']=$settings_page_role.' capabilities have been updated.';
						}else{
							$_POST['settings-updated']='You can not change Administrator capabilities.';
						}
					}
				}else{
					foreach($_POST as $pf=>$pv){
						$pv = (is_array($pv))? $pv: (htmlspecialchars ($pv) );
						$evcal_options[$pf] = $pv;					
					}

					$evcal_options = apply_filters('evoau_save_settings_optionvals', $evcal_options, $focus_tab);
					update_option('evcal_options_'.$focus_tab, $evcal_options);
					$_POST['settings-updated']='Successfully updated values.';
				}
				
			}else{
				die( __( 'Action failed. Please refresh the page and retry.', 'eventon' ) );
			}	
		}
?>
<div class="wrap" id='evcal_settings'>
	<h2><?php _e('Action User Settings','eventon');?></h2>
	<h2 class='nav-tab-wrapper' id='meta_tabs'>
		<?php					
			foreach($evcal_tabs as $nt=>$ntv){				
				if(($nt=='evoau_2' && current_user_can('administrator')) || $nt!='evoau_2'){
					$evo_notification='';
					echo "<a href='?page=action_user&tab=".$nt."' class='nav-tab ".( ($focus_tab == $nt)? 'nav-tab-active':null)."' evcal_meta='evcal_1'>".$ntv.$evo_notification."</a>";
				}
			}			
		?>		
	</h2>	
<div class='evo_settings_box'>		
<?php
	
	$updated_code = (isset($_POST['settings-updated']))? '<div class="updated fade"><p>'.$_POST['settings-updated'].'</p></div>':null;
	echo $updated_code;

	switch ($focus_tab):
	
	case "evoau_1":
		
		$eventon->load_ajde_backender();
		?>
		<form method="post" action=""><?php settings_fields('evoau_field_group'); 
			wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoau_noncename' );
		?>
		<div id="evoau_1" class="evcal_admin_meta evcal_focus">		
			<div class="inside">
				<?php	
					// GET form fields
						foreach($eventon_au->frontend->au_form_fields('additional') as $field=>$fn){
							$fieldar[$field]=$fn[0];
						}

					// Default select tax #1 and #2
						$default_tax_fields = array();
						$_tax_names_array = $eventon_au->frontend->tax_names;
						for($t=1; $t<3; $t++){
							$ab = ($t==1)? '':'_'.$t;
							$ett = get_terms('event_type'.$ab, array('hide_empty'=>false));
							
							$au_ett = array();

							// show option only if there are tax terms
							if(!empty($ett) && !is_wp_error($ett)){
								foreach($ett as $term){
									$au_ett[ $term->term_id] = $term->name;
								}

								$default_tax_fields[$t][1] =array('id'=>'evoau_set_def_ett'.$ab,'type'=>'yesno','name'=>'Set default '.$_tax_names_array[$t].' category tag for event submissions','afterstatement'=>'evoau_set_def_ett'.$ab,
									'legend'=>'This will assign a selected '.$_tax_names_array[$t].' category tag to the submitted event automatically.');							
								$default_tax_fields[$t][2] =array('id'=>'evoau_set_def_ett'.$ab,'type'=>'begin_afterstatement');
								$default_tax_fields[$t][3] =array('id'=>'evoau_def_ett_v'.$ab,'type'=>'dropdown',
											'name'=>'Select default '.$_tax_names_array[$t].' tag for submitted events',
											'width'=>'full',
											'options'=>$au_ett,
								);
								$default_tax_fields[$t][4] =array('id'=>'evoau_set_def_ett'.$ab,'type'=>'end_afterstatement');
							}
						}

						//print_r($default_tax_fields);

					// intergration with RSVP addon
						// reviewer addon
						if(is_plugin_active('eventon-reviewer/eventon-reviewer.php')){
							$evore_setting =array('id'=>'evoar_re_addon','type'=>'yesno','name'=>'Enable Event Reviews for submitted events by default','legend'=>'This will automatically set Review capability for events submitted.');
						}
											
				// ARRAY
					// load new settings values
					$this->opt = get_option('evcal_options_evoau_1');

					// disable notify submitter when event approved
					$dis_approval_notice = ( isset($this->opt['evoau_post_status']) && $this->opt['evoau_post_status']== 'publish')?  '<b>[ Disabled ]</b>':'';

					$cutomization_pg_array = apply_filters('evoau_settings',array(
						array(
							'id'=>'evoAU1',
							'name'=>'ActionUser General Settings',
							'tab_name'=>'General Settings',
							'display'=>'show',
							'icon'=>'inbox',
							'fields'=>array(

								array('id'=>'evoau_assignu','type'=>'yesno','name'=>'Assign logged-in user to event after successful event submission',),
								array('id'=>'evoau_ux','type'=>'yesno','name'=>'Set default user-interaction for event','afterstatement'=>'evoau_ux'),
									array('id'=>'evoau_ux','type'=>'begin_afterstatement'),
									array('id'=>'evoau_ux_val','type'=>'dropdown','name'=>'Select default event user-interaction','width'=>'full',
									'options'=>array(
										'1'=>'Slide Down',
										'3'=>'Lightbox',
										'4'=>'Single Event page',
										)
									),
									array('id'=>'evoau_ux','type'=>'end_afterstatement'),

								array('id'=>'evoau_form_nonce','type'=>'yesno','name'=>'Disable checking form nonce upon submission','legend'=>'If your form submissions throws a bad nonce error you can enable this to skip nonce checking.'),
								array('id'=>'evoau_eventdetails_textarea','type'=>'yesno','name'=>'Use basic textarea for event details box instead of WYSIWYG editor','legend'=>'If your theme have styles interfering with all WYSIWYG editors across site, this will switch event details to a basic text box instead of WYSIWYG editor.'),
								
								array(
									'id'=>'evoau_dis_permis_status',
									'type'=>'yesno',
									'name'=>'Disable overriding default event post status for users with publishing permission',
									'legend'=>'Setting this will stop overriding the above set default event post publish status, if the submitter have permission to publish events.'
								),

								// permission & restrictions
								array('type'=>'sub_section_open','name'=>__('Permissions & Restrictions' ,'evoau')),
								
									array('id'=>'evoau_access',
										'type'=>'yesno',
										'name'=>'Allow only logged-in users to submit events',
										'legend'=>'This will allow you to only give event submission form access to loggedin users. If a custom URL is set in eventON settings it will be used for login button.',
										'afterstatement'=>'evoau_access'
									),
										array('id'=>'evoau_access','type'=>'begin_afterstatement'),
										array('id'=>'evoau_access_role',
											'type'=>'yesno',
											'name'=>'Allow only users with "Submit New Events From Submission Form" permission, submit events',
											'legend'=>'Submit New Events From Submission Form -- permission can be set for user roles from Action User Settings > User Capabilities',
										),
										array('id'=>'evoau_access','type'=>'end_afterstatement'),

									array('id'=>'evoau_limit_submissions','type'=>'yesno','name'=>'Restrict only one event submission per user','legend'=>'This will restrict any user submit events only once. No more submissions message can be editted from EventON > Language > Action User'),
									array('id'=>'evoau_allow_img_up',
										'type'=>'yesno',
										'name'=>'Allow event image upload to non logged-in visitors (Not recommended)',
										'legend'=>'This will allow visitors to upload images to your site.',
										'afterstatement'=>'evoau_allow_img_up'
									),
										array('id'=>'evoau_allow_img_up','type'=>'begin_afterstatement'),
										array('id'=>'evoau_allow_img_up','type'=>'note','name'=>'Warning: By enabling this you consent to understand & accept the potential threats that can arise by allowing visitors without file upload permission to upload files to your site.'),
										array('id'=>'evoau_allow_img_up','type'=>'end_afterstatement'),

									/*array('id'=>'evoau_genGM','type'=>'yesno','name'=>'Generate google maps from submitted location address',),*/
								array('type'=>'sub_section_close'),


								array('type'=>'sub_section_open','name'=>__('Event Type Category Settings' ,'evoau')),

									(!empty($default_tax_fields[1])? $default_tax_fields[1][1]:null), 
									(!empty($default_tax_fields[1])? $default_tax_fields[1][2]:null), 
									(!empty($default_tax_fields[1])? $default_tax_fields[1][3]:null), 
									(!empty($default_tax_fields[1])? $default_tax_fields[1][4]:null),

									(!empty($default_tax_fields[2])? $default_tax_fields[2][1]:null), 
									(!empty($default_tax_fields[2])? $default_tax_fields[2][2]:null), 
									(!empty($default_tax_fields[2])? $default_tax_fields[2][3]:null), 
									(!empty($default_tax_fields[2])? $default_tax_fields[2][4]:null),

									array('id'=>'evoau_add_cats','type'=>'yesno','name'=>'Allow users to create new categories for event type tax','legend'=>'Users will be able to create their own custom categories for all event type taxonomies (categories) from the event submission form'),
								array('type'=>'sub_section_close'),

								array('type'=>'sub_section_open','name'=>__('Other Form Settings' ,'evoau')),

									array('id'=>'evoau_def_image','type'=>'image','name'=>'Set default image for submitted events','legend'=>'If default image is set, if the user did not upload an image this will be used for the event OR if the form does not support image field default image will be used.'),

									(!empty($evors_setting)? $evors_setting:null),								
									(!empty($evore_setting)? $evore_setting:null),	

								array('type'=>'sub_section_close'),


						)),						
						array(
							'id'=>'evoAU1a',
							'name'=>'Submission form fields',
							'tab_name'=>'Form Fields','icon'=>'briefcase',
							'fields'=>array(
								array('id'=>'evo_au_title','type'=>'text','name'=>'Default Form Header Text',),
								array('id'=>'evo_au_stitle','type'=>'text','name'=>'Default Form Subheader Text',),	
								
								array('id'=>'evoau_fields', 'type'=>'note','name'=>'Additional 
								fields for the event submission form: <i>(NOTE: Event Name, Start and End date/time are default fields)</i><br/><a href="'.get_admin_url().'admin.php?page=eventon&tab=evcal_2">Customize text for form field names</a>',
								),
								array('id'=>'evoau_fields', 'type'=>'rearrange',
									'fields_array'=>$this->fields_array(),
									'order_var'=> 'evoau_fieldorder',
									'selected_var'=> 'evoau_fields',
									'title'=>__('Fields for the Event Submission Form','evoau'),
								),
								array('id'=>'evoau_fields', 'type'=>'customcode',
									'code'=> $this->custom_code_001(),
								),
								
								array('id'=>'evoau_notif','type'=>'note','name'=>'** Name and email fields will not be visible in the form if user is loggedin already, but those fields will be populated with registered information.<br/><br/>** Category selection fields will not show on form if they do not have category tags. 
									<br/><br/>** Special event edit fields - will only appear in event manager 
									<br/><br/>** Event Access Password - will restrict access ONLY to single event page, until the correct password submitted
									<br/><br/>** <b>Featured Image</b> - User must be loggedin to submit featured image via form.
									<br/><br/>*** Additional notes for admin will show in event edit page under ActionUser box.'
									),
									
						)),
						array(
							'id'=>'evoAU1b',
							'name'=>'Submission form Settings',
							'tab_name'=>'Form Settings','icon'=>'briefcase',
							'fields'=>array(
								array('id'=>'evoau_hide_repeats','type'=>'yesno','name'=>__('Hide repeating event fields from frontend form','evoau') ),
								array('id'=>'evoau_allow_vir_enddate','type'=>'yesno','name'=>__('Allow to add virtual event end date/time [Beta]','evoau') ),


								array('id'=>'evoau_post_status','type'=>'dropdown','name'=>'Submitted event\'s default post status','width'=>'full',
									'options'=>array(
										'draft'=>'Draft',
										'publish'=>'Publish',
										'private'=>'Private'),
									'legend'=>'This will be override if the submitter have the user permission to publish events'
									),

								
								array(
									'id'=>'evoau001','type'=>'subheader',
									'name'=> __('Event Organizer Fields','evoau')
								),
									array(
										'id'=>'evoau_evoorg_new',
										'type'=>'yesno',
										'name'=>__('Allow creating new','evoau') 
									),	
									array(
										'id'=>'evoau_evoorg_list_hide',
										'type'=>'yesno',
										'name'=>__('Hide existing items list','evoau') 
									)
								,array(
									'id'=>'evoau001','type'=>'subheader',
									'name'=>'Event Location Fields'
								),
									array(
										'id'=>'evoau_evoloc_new',
										'type'=>'yesno',
										'name'=>__('Allow creating new','evoau') 
									),	
									array(
										'id'=>'evoau_evoloc_list_hide',
										'type'=>'yesno',
										'name'=>__('Hide existing items list','evoau') 
									),

								array('id'=>'evoau_html_content','type'=>'textarea','name'=>'Additional HTML field content','legend'=>'Type the HTML content to be used in the above HTML field inside the event submission form.'),
							)
						),
						array(
							'id'=>'evoAU2',
							'name'=>'Form Emailing Settings',
							'tab_name'=>'Emailing','icon'=>'envelope',
							'fields'=>array(								
								array('id'=>'evoau_notif','type'=>'yesno','name'=>'Notify admin upon new event submission','afterstatement'=>'evoau_notif'),
								array('id'=>'evoau_notif','type'=>'begin_afterstatement'),
									
									array('id'=>'evoau_ntf_admin_to','type'=>'text',
										'name'=>'Email address to send notification. (eg. you@domain.com)', 
										'legend'=>'You can add multiple email addresses seperated by commas to receive notifications of event submissions.','default'=>$this->adminEmail),
									array('id'=>'evoau_ntf_admin_from','type'=>'text',
										'name'=>'From eg. My Name &lt;myname@domain.com&gt; - Default will use admin email from this website', 'default'=>$this->adminEmail),
									array('id'=>'evoau_ntf_admin_subject','type'=>'text','name'=>'Email Subject line','default'=>'New Event Submission'),
									array('id'=>'evoau_ntf_admin_msg','type'=>'textarea','name'=>'Message body','default'=>'You have a new event submission!'),
									array('id'=>'evoau_001','type'=>'note','name'=>$this->content_email_body_instructions() ),
								array('id'=>'evoau_notif','type'=>'end_afterstatement'),								

								array('id'=>'evoau_notsubmitter','type'=>'yesno','name'=>'Notify submitter when they submit an event (if submitter email present)','afterstatement'=>'evoau_notsubmitter'),
								array('id'=>'evoau_notsubmitter','type'=>'begin_afterstatement'),

									array('id'=>'evoau_ntf_user_from','type'=>'text','name'=>'From eg. My Name &lt;myname@domain.com&gt; - Default will use admin email from this website', 'default'=>$this->adminEmail),

									array('id'=>'evoau_ntf_drf_subject','type'=>'text','name'=>'Email Subject line','default'=>'We have received your event!'),
									array('id'=>'evoau_ntf_drf_msg','type'=>'textarea','name'=>'Message body','default'=>'Thank you for submitting your event!', 'default'=>'Thank you for submitting your event!'),
									array('id'=>'evoau_001','type'=>'note','name'=>$this->content_email_body_instructions()),
									
								array('id'=>'evoau_notsubmitterAP','type'=>'end_afterstatement'),

								array('id'=>'evoau_notsubmitterAP','type'=>'yesno','name'=>'Notify submitter when their event is approved (if submitter email present) '.$dis_approval_notice,'afterstatement'=>'evoau_notsubmitterAP','legend'=>'If you set the submitted events to be saved as drafts, you can use this message notifications to let them know when their event is approved'),
								array('id'=>'evoau_notsubmitterAP','type'=>'begin_afterstatement'),

									/*array('id'=>'evoau_notif_edits','type'=>'yesno','name'=>__('Notify submitter if editted events approved','eventon'),
										'legend'=>"If published events are editted to draft, enabling this will notify submitter when editted event is approved"
									),
									*/

									array('id'=>'evoau_ntf_pub_from','type'=>'text','name'=>'From eg. My Name &lt;myname@domain.com&gt; - Default will use admin email from this website', 'default'=>$this->adminEmail),
									array('id'=>'evoau_ntf_pub_subject','type'=>'text','name'=>'Email Subject line','default'=>'We have approved your event!'),
									array('id'=>'evoau_ntf_pub_msg','type'=>'textarea','name'=>'Message body','default'=>'Thank you for submitting your event and we have approved it!'),
									array('id'=>'evoau_001','type'=>'note','name'=>$this->content_email_body_instructions()),
								array('id'=>'evoau_notsubmitterAP','type'=>'end_afterstatement'),
								array('id'=>'evoau_link','type'=>'dropdown','name'=>'Select notification email link type','width'=>'full',
									'options'=>array(
										'event'=>'Link to event',
										'learnmore'=>'Link to learn more link inside event',
										'other'=>'Other link, type below')									
								),
									array('id'=>'evoaun_link_other','type'=>'text','name'=>' Type other custom link you want to use in notification email','legend'=>"For this link to be included in the notification email, make sure to select Other Link as an option in above setting."),
								
						)),array(
							'id'=>'evoAU3',
							'name'=>'Front-end form notification Messages',
							'tab_name'=>'Front-end Messages','icon'=>'comments',
							'fields'=>array(																
								array('id'=>'evoaun_msg_f','type'=>'note','name'=>'Form success message and error message text can be editted from <u>EventON Settings > Language > Addon: Action User</u>',),
						)),
						array(
							'id'=>'evoAU5',
							'name'=>'Front-end User\'s Event ManagerSettings',
							'tab_name'=>'Event Manager','icon'=>'leaf',
							'fields'=>array(
								array('id'=>'evo_auem_editing','type'=>'yesno','name'=>'Allow frontend editing','legend'=>'This can be overridden per each event by action user settings box in event edit page'),
								array(
									'id'=>'evo_auem_deleting',
									'type'=>'yesno',
									'name'=>'Allow frontend deleting events',
									'legend'=>'This can be overridden per each event by action user settings box in event edit page'
								),
								array('id'=>'evoau_assigned_emanager',
									'type'=>'yesno',
									'name'=>'Allow event assigned users to see event in event manager',
									'legend'=>'With this enabled, when you assign a user to event from event edit page, those users will be able to see that event in frontend event manager',
									'afterstatement'=>'evoau_assigned_emanager'
								),
									array('id'=>'evoau_assigned_emanager','type'=>'begin_afterstatement'),
									array('id'=>'evoau_assigned_editing',
										'type'=>'yesno',
										'name'=>'Allow event assigned users to edit those events in event manager. (Override above)',
										'legend'=>'This will allow users assigned to the event to also edit those events from event manager'
									),array('id'=>'evoau_assigned_deleting',
										'type'=>'yesno',
										'name'=>'Allow event assigned users to delete those events in event manager. (Override above)',
										'legend'=>'This will allow users assigned to the event to also delete those events from event manager'
									),
									array('id'=>'evoau_assigned_emanager','type'=>'end_afterstatement'),

								array('id'=>'evoau_allow_editing',
									'type'=>'yesno',
									'name'=>'Allow user to edit certain fields',
									'legend'=>'Enabling this will control if user have capability to edit selected fields of an event',
									'afterstatement'=>'evoau_allow_editing'
								),
									array('id'=>'evoau_allow_editing','type'=>'begin_afterstatement'),
									array('id'=>'evoau_allow_edit_organizer',
										'type'=>'yesno',
										'name'=>__('Event organizer fields','eventon') 
									),
									array('id'=>'evoau_allow_edit_location',
										'type'=>'yesno',
										'name'=>__('Event location fields','eventon') 
									),
									array('id'=>'evoau_allow_editing','type'=>'end_afterstatement'),

								array(
									'id'=>'evoau_dis_datetime_editing',
									'type'=>'dropdown',
									'name'=>'Disable date time editing',
									'options'=>array(
										'def'=>'None',
										'all'=>'On all submitted events',
										'past'=>'Only on past events',
									),
									'legend'=>'Disable editing date and time on event manager events'
								),
								array('id'=>'evoau_edit_to_draft',
									'type'=>'yesno',
									'name'=>'Make event a draft if editted in event manager',
									'legend'=>'If the event was editted in event manager this will put that event into draft status pending approval and publish.',
								),

						)),
					));					
					
					

					$updated_code = (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? '<div class="updated fade"><p>Settings Saved</p></div>':null;
					echo $updated_code;
					$eventon->load_ajde_backender();
					print_ajde_customization_form($cutomization_pg_array, $this->opt);					
				?>				
			</div>				
		</div>	
		<div class='evo_diag'>
			<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" />
		</div>		
		</form>
		
<?php  
	break;
	
	// PERMISSIONS TAB
	case "evoau_2":
		
		if(current_user_can('administrator')):		
		$eventon->load_ajde_backender();
?>
		<form method="post" action=""><?php settings_fields('evoau_field_group'); wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoau_noncename' );
				?>
		<div id="evoau_2" class="evcal_admin_meta evcal_focus">		
			<div class='postbox'>
			<div class="inside">
				<?php					
					// Capabilities for Individual user
					if(isset($_GET['object']) && isset($_GET['user_id'])&& $_GET['object']=='user'):
						
						$this_user_id = $_GET['user_id'];
						$cur_edit_user = new WP_User( $_GET['user_id'] );
						
						if (!is_multisite() || current_user_can('manage_network_users')) {
							$anchor_start = '<a href="' . wp_nonce_url("user-edit.php?user_id={$this_user_id}", 
							  "evo_user_{$this_user_id}") .'" >';
							$anchor_end = '</a>';
						} else {
							$anchor_start = '';
							$anchor_end = '';
						}
						$user_info = ' <span style="font-weight: bold;">'.$anchor_start. $cur_edit_user->user_login; 
						if ($cur_edit_user->display_name!==$cur_edit_user->user_login) {
							$user_info .= ' ('.$cur_edit_user->display_name.')';
						}
						
						$user_info .= $anchor_end.'</span>';
						if (is_multisite() && is_super_admin($this_user_id)) {
							$user_info .= '  <span style="font-weight: bold; color:red;">'. 	esc_html__('Network Super Admin', 'eventon') .'</span>';
						}						
				?>
					<h3><?php _e('Capabilities for user','eventon');?> <?php echo $user_info;?></h3>
					<p><?php _e('Primary Role','eventon');?>: <b><?php echo $cur_edit_user->roles[0] ;?></b></p>				
					<h4><?php _e('EventON Capabilities','eventon');?></h4>					
					<em class="hr_line noexpand"></em>
					<div class='capabilities_list evo_backender_uix'>
						<?php						
							echo $eventon_au->admin->get_cap_list_admin($this_user_id, 'user');
						?>
					</div>				
				<?php					
					else:
				?>				
					<h3><?php _e('Select Role and set Capabilities for eventON','eventon');?></h3>
					<p><?php _e('Select Role','eventon');?> <select id='evoau_role_selector' name='current_role'>
					<?php
						global $wp_roles;
							
						$roles = $wp_roles->get_names();
						
						//print_r($roles);
						foreach($roles as $role=>$rolev){
							$selected = ($settings_page_role==$role)?'selected':null;
							echo "<option value='{$role}' {$selected}>{$rolev}</option>";
						}						
					?>
					</select></p>				
					<em class="hr_line noexpand"></em>
					<p class="evoau_msg" style='display:none; padding-bottom:20px'><?php _e('Loading..','eventon');?></p>
					<div class='capabilities_list evo_backender_uix'>
						<?php						
							$caps =  $eventon_au->admin->get_cap_list_admin($settings_page_role);
							echo $caps;
						?>
					</div>
					</p><b>NOTE: </b><i><?php _e('Primary Administrator capabilities can not be changed. Permissions for each user can be configured separately from WordPress > Users page, by clicking on "EventON Capabilities" under each user. Each user permissions will prevail user role permissions for EventON.','eventon');?></i></p>					
				<?php endif;?>				
				<br/>
				<h3><?php _e('Guide to Capabilities:','eventon');?></h3>
				<p><b><?php _e('publish events','eventon');?></b> - <?php _e('Allow user to publish a event','eventon');?></p>
				<p><b><?php _e('edit events','eventon');?></b> - <?php _e("Allow editing of the user's own events but does not grant publishing permission.",'eventon');?></p>
				<p><b><?php _e('edit others events','eventon');?></b> - <?php _e("Allows the user to edit everyone else's events but not publish.",'eventon');?></p>
				<p><b><?php _e('edit published events','eventon');?></b> - <?php _e("Allows the user to edit his own events that are published.",'eventon');?></p>
				
				<p><b><?php _e('delete events','eventon');?></b> - <?php _e("Grants the ability to delete events created by that user but not other.",'eventon');?></p>
				<p><b><?php _e('delete others events','eventon');?></b> - <?php _e("Capability to delete events created by other users.",'eventon');?></p>
				<p><b><?php _e('read private events','eventon');?></b> - <?php _e("Allow user to read private events.",'eventon');?></p>
				<p><b><?php _e('assign event terms','eventon');?></b> - <?php _e("Allows the user to assign event terms to allowed events.",'eventon');?></p>
				<p><b><?php _e('Submit New Events From Submission Form','eventon');?></b> - <?php _e("Permission to submit events from new event submission form.",'eventon');?></p>
				<p><b><?php _e('Upload Files','eventon');?></b> - <?php _e("Allow user to upload an image file for event image.",'eventon');?></p>
			</div>
			</div>
		</div>
		<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /></form>
<?php	
		endif;
	break;
	
	endswitch;
	echo "</div>";
	
}


// content
	public function custom_code_001(){
		ob_start();

		echo "<h4 class='acus_subheader'>". __('Fields that only appear in event manager edit event form','evoau') . "</h4><p>";
		foreach(apply_filters('evoau_editform_options_array',EVOAU()->frontend->au_form_fields('editonly')) 
			as $key=>$value
		){
			echo $value[0].', ';
		}

		echo "</p>";

		return ob_get_clean();
	}
	function fields_array(){
		$FIELDS = EVOAU()->frontend->au_form_fields('additional');

		foreach($FIELDS as $F=>$V){			
			$FF[$F]= !empty($V[6])?$V[6]:$V[0];
		}
		return $FF;
	}


}// end class

?>