<?php
/**
 * Action User settings page
 * @version 2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoau_settings{
	public function __construct(){

		add_action('admin_init', array($this, 'admin_init'));		

		$this->adminEmail = get_option('admin_email');
	}
	function admin_init(){
		// settings
			add_filter('eventon_settings_tabs',array($this, 'evoau_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evoau_1',array($this, 'evoau_tab_content' ));		
			add_filter('evo_save_settings_optionvals',array($this, 'settings_saved' ), 10, 2);		
	}
	function evoau_tab_array($evcal_tabs){
		$evcal_tabs['evoau_1']='Action User';		
		return $evcal_tabs;
	}

	function evoau_tab_content(){
		EVO()->load_ajde_backender();
		?>
		<form method="post" action=""><?php settings_fields('evoau_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
		<div id="evoau_1" class="evcal_admin_meta evcal_focus">		
			<div class="inside">
				<?php	
					// GET form fields
						foreach(EVOAU()->frontend->au_form_fields('additional') as $field=>$fn){
							$fieldar[$field]=$fn[0];
						}

					// Default select tax #1 and #2
						$default_tax_fields = array();
						$_tax_names_array = EVOAU()->frontend->tax_names;
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
						EVO()->cal->load_more('evoau_1');
						//$option_values = EVO()->cal->get_op('evoau_1');
						$option_values = get_option('evcal_options_evoau_1','evoau_1');


					// disable notify submitter when event approved
						$evoau_post_status = EVO()->cal->get_prop('evoau_post_status');
						$dis_approval_notice = ( $evoau_post_status == 'publish')?  '<b>[ Disabled ]</b>':'';

					// Run the array
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


						// User Capabilities
						array(
							'id'=>'evoau_usercap',
							'name'=>'User Capabilities',
							'tab_name'=>'User Capabilities','icon'=>'user',
							'fields'=>array(
								
								array('id'=>'evoau_fields', 'type'=>'customcode',
									'code'=> $this->custom_code_user_cap(),
								),	
						)),
					));					
					

					// PROCESS

					EVO()->load_ajde_backender();
					EVO()->evo_admin->settings->print_ajde_customization_form(
						$cutomization_pg_array, 
						$option_values
					);					
				?>				
			</div>				
		</div>	
		<div class='evo_diag'>
			<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" />
		</div>		
		</form>
		
	<?php 
	}

	// settings for email body content
		function content_email_body_instructions(){
			return "Supported email tags that can be used in email message body: <code>{event-edit-link}</code>,<code>{event-name}</code>,<code>{event-link}</code>,<code>{event-start-date}</code>, <code>{event-start-time}</code>, <code>{event-end-date}</code>, <code>{event-end-time}</code>, <code>{new-line}</code><br/><br/>IF available only tags: <code>{submitter-name}</code>,<code>{submitter-email}</code><br/> ** Name and Email fields must be enabled in event submission form for these variables to work.<br/>HTML codes also can be used inside email message body.";
		}

	// @since 2.4.1
	function settings_saved( $evcal_options, $focus_tab){

		if( $focus_tab == 'evoau_1'){
			$evcal_options = apply_filters('evoau_save_settings_optionvals', $evcal_options, $focus_tab);
		}

		return $evcal_options;
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
	public function custom_code_user_cap(){
		ob_start();
		$help = new evo_helper();
		$userid = isset($_REQUEST['uid']) ? (int)$_REQUEST['uid'] : null;

		echo "<div style='min-height:600px'>";
		
		// passed on button data
			$btn_data = array(
				'lbvals'=> array(
					'lbc'=>'config_user_capabilities',
					't'=>__('User Capabilities Manager','evoau'),
					'ajax'=>'yes',
					'd'=> array(					
						'userid'=> $userid,
						'action'=> 'evoau_load_capability',
						'uid'=>'evoau_get_user_cap_manager',
						'load_lbcontent'=>true
					)
				)
			);

		if( !empty( $userid )): 
			echo "<p class='evopadb20'>" . __('User capabilities for individual user can be further editted by clicking on the red button below.','evoau') .'</p>';
		?>

			<p class='evopadb20 '><span class='evo_admin_btn evolb_trigger btn_red' <?php echo $help->array_to_html_data($btn_data);?>  style='margin-right: 10px'><?php echo  __('Open Capabilities for User ID','evoau') .' #'. $userid ;?></span></p>

		<?php endif;?>

		<?php 
		// change data
		$btn_data['lbvals']['d']['userid'] = null;

		echo "<p class=''>" . __('Please use the User Capabilities Manager to edit individual user role capabilities.','evoau') .'</p>';
		?>
		<p class='evopadb50 '><span class='evo_admin_btn evolb_trigger ' <?php echo $help->array_to_html_data($btn_data);?>  style='margin-right: 10px'><?php echo  __('User Capabilities Manager','evoau');?></span></p>

		
		
		<script type="text/javascript">
			jQuery(document).ready(function($){

			// role selector
			$('body').on('change','.evoau_role_selector', function(){
				
				var data_arg = {
					action:'evoau_load_capability',
					role:$(this).val()
				};

				$(this).evo_admin_get_ajax({
					'lightbox_key': 'config_user_capabilities',
					'ajaxdata': data_arg,
					'uid':'evoau_reload_usercap_manager',
				});

				return;
			});

			});		
		</script>

		</div>
		<?php



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


new evoau_settings();

?>