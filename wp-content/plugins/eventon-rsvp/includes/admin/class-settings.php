<?php
/**
 * RSVP settings
 */



class EVORS_Settings{
	
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));		
	}
	function admin_init(){
		// settings
			add_filter('eventon_settings_tabs',array($this, 'evoRS_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evcal_rs',array($this, 'evoRS_tab_content' ));		
	}

	function evoRS_tab_array($evcal_tabs){
			$evcal_tabs['evcal_rs']='RSVP';		
			return $evcal_tabs;
		}

		function user_roles(){
			$roles = array();
			foreach(get_editable_roles() as $role_name => $role_info){
				$roles[$role_name ] = translate_user_role($role_info['name']) ;
			}
			return $roles;
		}

		function evoRS_tab_content(){
			//global $eventon;
			EVO()->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evoau_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_csv" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php

					$site_name = get_bloginfo('name');
					$site_email = get_bloginfo('admin_email');

					$cutomization_pg_array = apply_filters('evors_settings_fields',array(
						array(
							'id'=>'evoRS1','display'=>'show',
							'name'=>'General RSVP Settings',
							'tab_name'=>'General',
							'fields'=>array(
								array('id'=>'evors_onlylogu','type'=>'yesno',
									'name'=>'Allow only logged-in users to submit RSVP',
									'afterstatement'=>'evors_onlylogu',
									'legend'=>'If a custom login URL is set via eventon settings that will be used for users to login to RSVP'
								),
									array('id'=>'evors_onlylogu','type'=>'begin_afterstatement'),
									array('id'=>'evors_rsvp_roles',
										'type'=>'checkboxes',
										'name'=>'Select only certain user roles with RSVPing capabilities (If not selected all logged-in users can RSVP)',
										'options'=>$this->user_roles(),
									),									
									array('id'=>'evors_onlylogu','type'=>'end_afterstatement'),	

								array('id'=>'evors_prefil',
									'type'=>'yesno',
									'name'=>'Pre-fill fields  if user is already logged-in (eg. first name, last name, email)',
									'legend'=>'If this option is activated, form will pre-fill fields (name & email) for logged-in users.',
									'afterstatement'=>'evors_prefil',
								),
									array('id'=>'evors_prefil','type'=>'begin_afterstatement'),
									array('id'=>'evors_prefil_block','type'=>'yesno','name'=>'Activate uneditable pre-filled fields','legend'=>'This will disable editing pre-filled data fields, when fields are pre-filled with loggedin user data eg. first name, last name, email.'),
									array('id'=>'evors_prefil','type'=>'end_afterstatement'),	

								array('id'=>'evors_reg_user','type'=>'yesno',
									'name'=>'Disable creating new account for new RSVP user',	
									'legend'=>'When new user RSVPed, an account will be created for them as subscriber role. This will help the user track their RSVPs and signin.'
								),
								
								array('id'=>'evors_orderby','type'=>'dropdown','name'=>'Order Attendees by ','legend'=>'Which field to use for ordering attendees in backend and frontend. If users are not entering last name first name would be a wise option for ordering.','options'=>array('def'=>'Last Name','fn'=>'First Name')),

								array('id'=>'evors_guestlist','type'=>'dropdown','name'=>'Show guest list as ','legend'=>'Whether to show full names or initials in event card for guest list - whos coming.','options'=>array('def'=>'Initials','fn'=>'Full Name')),
								
								array('id'=>'evors_guest_link','type'=>'yesno','name'=>'Link guests to matching user profile','legend'=>'Link guest name to user profile pages. This feature is only available for loggedin guests.', 'afterstatement'=>'evors_guest_link'),
									array('id'=>'evors_guest_link','type'=>'begin_afterstatement'),
									array('id'=>'evors_profile_link_structure','type'=>'text','name'=>'Custom Link structure for the guest user profile page link (This is appended to your base website URL)','default'=>'/profile/?user_id={user_id}'),
									array('id'=>'note','type'=>'note',
										'name'=>'You can use <code>{user_id}</code>, <code>{user_nicename}</code> in your link structure, which will be replaced with dynamic value. The above link structure must not contain your base website URL.<br/>
										NOTE: If you are using buddypress profiles you do not need to fill custom link structure.'),
									array('id'=>'evors_guest_link','type'=>'end_afterstatement'),	

								array('id'=>'evors_nonce_disable',
									'type'=>'yesno',
									'name'=>'Disable Nonce verification check upon new RSVP submission',
									'legend'=>'Enabling this will stop checking for nonce verification upon new RSVP submission.'
								),array('id'=>'evors_incard_form',
									'type'=>'yesno',
									'name'=>'Show RSVP form within EventCard instead of lightbox',
									'legend'=>'This will open all RSVP forms inside the EventCard as oppose to lightbox RSVP form.'
								),
								array('id'=>'evors_close_time','type'=>'dropdown',
									'name'=>'When to close RSVP to new RSVPs',
									'legend'=>'Set when to close RSVP for submissions. By default RSVP will close when event starts. You can also close RSVP X minutes before event start via each event edit page.',
									'options'=>array(
										'start'=>'When event starts',
										'end'=>'Allow until event ends',
										'never'=>'Never close RSVP, even after event ends',
									)),
								
								
								array('id'=>'evors_eventop','type'=>'subheader','name'=>'EventTop Data for RSVP.'),
									array('id'=>'evors_eventop_rsvp','type'=>'yesno','name'=>'Activate RSVPing with one-click from eventTop ONLY for logged-in users','legend'=>'This will show the normal RSVP option buttons for a logged-in user to RSVP to the event straight from the eventtop. This method will only capture user name, email and rsvp status only'),
									array('id'=>'evors_eventop_attend_count',
										'type'=>'yesno',
										'name'=>'Show attending guest count',
										'legend'=>'Show the attending guest count for an event on eventTOP'
									),array('id'=>'evors_eventop_notattend_count',
										'type'=>'yesno',
										'name'=>'Show not attending guest count',
										'legend'=>'This will show the count of guest not attending the event on eventTOP'
									),
									array('id'=>'evors_eventop_remaining_count',
										'type'=>'yesno',
										'name'=>'Show remaining spaces count',
										'legend'=>'Show the remaining spaces for this event on eventTOP'
									),
									array('id'=>'evors_eventop_soldout_hide',
										'type'=>'yesno',
										'name'=>'Do NOT show eventtop "RSVP Closed" or "No more spaces left" tag above event title, when rsvps are closed.'
									),


									array('id'=>'evors_eventop','type'=>'note','name'=>'NOTE: You can download all RSVPs for an event as CSV file from the event edit page under RSVP settings box.'),
									array('id'=>'evors_eventop','type'=>'customcode','code'=>'<a href="'.get_admin_url('','/admin.php?page=eventon&tab=evcal_5').'" class="evo_admin_btn btn_triad">RSVP Troubleshoot</a>'),

								array('id'=>'evors_eventop','type'=>'subheader','name'=>'ActionUser Event Manager RSVP settings'),
									array('id'=>'evorsau_csv_download',
										'type'=>'yesno',
										'name'=>'Allow front-end download of attendees list as CSV file',
										'legend'=>'With this loggedin users can download event attendees list as CSV from action user event manager.'
									),
									array('id'=>'evorsau_check_guest',
										'type'=>'yesno',
										'name'=>'Allow front-end checking guests',
										'legend'=>'This will allow loggedin users to check in guests from action user event manager.'
									),
									array('id'=>'evorsau_add_to_notification',
										'type'=>'yesno',
										'name'=>'Auto add event submitter email to receive notification emails upon new RSVP',
										'legend'=>'This will add the event submitter email (if available) into event to receive a notification email when a new RSVP is received from a customer.'
									)
						)),
						'evors_email'=> array(
							'id'=>'evoRS2','display'=>'',
							'name'=>'Email Templates',
							'tab_name'=>'Emails','icon'=>'envelope',
							'fields'=>array(
								array('id'=>'evcal_fcx','type'=>'note','name'=>'Supported Email Subject Dynamic Tags: <code>{event-name} {rsvp-id}</code>'),
								array('id'=>'evors_disable_emails','type'=>'yesno','name'=>'Disable sending all emails'),
								array('id'=>'evors_notif','type'=>'yesno','name'=>'Receive email notifications upon new RSVP receipt',
									'afterstatement'=>'evors_notif'),
								array('id'=>'evors_notif','type'=>'begin_afterstatement'),	

									array('id'=>'evcal_fcx','type'=>'note','name'=>'NOTE: This will send email notification upon new RSVP to the email specified below. Furthermore, you can also set additional email addresses to receive notifications on each event edit page.'),
									array('id'=>'evors_notfiemailfromN','type'=>'text','name'=>'"From" Name','default'=>$site_name),
									array('id'=>'evors_notfiemailfrom','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
									array('id'=>'evors_notfiemailto','type'=>'text','name'=>'"To" Email Address' ,'default'=>$site_email),

									array('id'=>'evors_notfiesubjest','type'=>'text','name'=>'Email Subject line','default'=>'New RSVP Notification'),
									array('id'=>'evors_notfiesubjest_update','type'=>'text','name'=>'Email Subject line (update)','default'=>'Update RSVP Notification'),	
									array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
									array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email template copy "eventon-rsvp/templates/notification_email.php" to  "yourtheme/eventon/templates/email/rsvp/notification_email.php.'),
								array('id'=>'evors_notif','type'=>'end_afterstatement'),

								array('id'=>'evors_digest','type'=>'yesno','name'=>'Receive daily digest emails for events (BETA)','afterstatement'=>'evors_digest'),
								array('id'=>'evors_digest','type'=>'begin_afterstatement'),	

									array('id'=>'evcal_fcx','type'=>'note','name'=>'NOTE: You can set which events with RSVP to receive the digest emails for, from the event edit page itself. Important: the scheduled daily email will only get sent out once someone visit your website.'),
									array('id'=>'evors_digestemail_fromN','type'=>'text','name'=>'"From" Name','default'=>$site_name),
									array('id'=>'evors_digestemail_from','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
									array('id'=>'evors_digestemail_to','type'=>'text','name'=>'"To" Email Address' ,'default'=>$site_email),

									array('id'=>'evors_digestemail_subjest','type'=>'text','name'=>'Email Subject line','default'=>'Digest Email for {event-name}'),
									
									array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
									array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email template copy "eventon-rsvp/templates/digest_email.php" to  "yourtheme/eventon/templates/email/rsvp/digest_email.php.'),
								array('id'=>'evors_digest','type'=>'end_afterstatement'),


								array('id'=>'evors_notif_e','type'=>'subheader','name'=>'Send out RSVP email confirmations to attendees'),		

								array('id'=>'evors_disable_confirmation',
									'type'=>'yesno',
									'name'=>'Disable sending out confirmation email to attendees who RSVP',
								),	
								array('id'=>'evors_disable_attendee_notifications',
									'type'=>'yesno',
									'name'=>'Disable all attendee notifications',
									'legend'=>'This will disable sending all attendee notification emails eg. When the attendee change thier RSVP status, or if there was a change to their rsvp etc.'
								),		
								array('id'=>'evors_disable_user_pass',
									'type'=>'yesno',
									'name'=>'Disable sending out new user password in confirmation email',
									'legend'=>'By default new user temp password will be sent out to user via confirmation email (if new user creation enabled) - this option will stop sending the password on confirmation email.'
								),			
								array('id'=>'evors_notfiemailfromN_e','type'=>'text','name'=>'"From" Name','default'=>$site_name),
								array('id'=>'evors_notfiemailfrom_e','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),

								array('id'=>'evors_notfiesubjest_e','type'=>'text','name'=>'Email Subject line','default'=>'[#rsvp_id] RSVP Confirmation'),
								array('id'=>'evors_notfi_update_subject','type'=>'text',
									'name'=>'Email Subject line (For RSVP updates email to attendee)','default'=>'[#rsvp_id] RSVP Update Confirmation'
								),
								array('id'=>'evors_email_subject_newuser','type'=>'text',
									'name'=>'Email Subject line (For new user pass)','default'=>'Your new password'),
								
								array('id'=>'evors_contact_link','type'=>'text','name'=>'Contact for help link' ,'default'=>site_url(), 'legend'=>'This will be added to the bottom of RSVP confirmation email sent to attendee'),

								array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
								array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email templates, copy default email templates from "eventon-rsvp/templates/" to  "yourtheme/eventon/templates/email/rsvp/ folder.'),
								

						)),
						array(
							'id'=>'evoRS3','display'=>'',
							'name'=>'RSVP form fields',
							'tab_name'=>'RSVP Form','icon'=>'inbox',
							'fields'=>$this->rsvp_form_fields()													
						)
					));			

					EVO()->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_rs'); 
					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
			</div>			
			</form>	
		<?php
		}

		// RSVP form fields
		function rsvp_form_fields(){
			global $eventon_rs;

			$fields = array(
				array('id'=>'evors_selection','type'=>'checkboxes','name'=>'Select RSVP status options for selection. <br/><b>NOTE:</b> Yes value is required. No value will show on change RSVP form regardless to allow users to cancel their reservation.', 
					'options'=>array(
						'm'=>'Maybe','n'=>'No',
				)),
				array('id'=>'evors_onlylog_chg','type'=>'yesno','name'=>'Allow only logged-in users see \'Change RSVP\' option','legend'=>'This will only show change RSVP options for the users that have loggedin to your site.','afterstatement'=>'evors_onlylog_chg'),
					array('id'=>'evors_onlylog_chg','type'=>'begin_afterstatement'),	
						array('id'=>'evors_change_hidden','type'=>'yesno','name'=>'Show \'Change RSVP\' option only for the users who have rsvp-ed for the event'),
					array('id'=>'evors_onlylog_chg','type'=>'end_afterstatement'),
				array('id'=>'evors_hide_change','type'=>'yesno','name'=>'Hide \'Change RSVP\' button','legend'=>'This will hide the Change rsvp button from eventcard, will override any other Change RSVP button options'),
				
				array('id'=>'evors_ffields','type'=>'checkboxes','name'=>'Select RSVP form fields to show in the form. <i>(** First , Last names, and Email are required)</i>',
					'options'=>array(
						'phone'=>'Phone Number',
						'count'=>'RSVP Count -- (If unckecked system will count as 1 RSVP)',
						'updates'=>'Receive Updates About Event -- (Acknowledge Checkbox field)',
						'names'=>'Other Guest Names -- (if RSVP count if more than 1)',
						'additional'=>'Additional Notes Field -- (visible only for NO option)',
						'captcha'=>'Verification Code'
				)),	
				array('id'=>'evors_hide_change','type'=>'note','name'=>'NOTE: "Additional Notes Field" will only show when a guest select NO as RSVP status. "Receive Updates About Event" will only be checked when emailing attendees.'),
			
				
				array('id'=>'evors_hide_change','type'=>'subheader','name'=>'Other Form Field Options'),
				
				array('id'=>'evors_terms','type'=>'yesno','name'=>'Activate Terms & Conditions for form','afterstatement'=>'evors_terms'),
					array('id'=>'evors_terms','type'=>'begin_afterstatement'),		
					array('id'=>'evors_terms_link','type'=>'text','name'=>'Link to Terms & Conditions'),
					array('id'=>'evors_terms_text','type'=>'note','name'=>'Text Caption for Terms & Conditions can be edited from EventON > Language > EventON RSVP'),
					array('id'=>'evors_terms','type'=>'end_afterstatement'),
			);

			// additional fields
				$field_additions = array();
				for($x=1; $x<= $eventon_rs->frontend->addFields; $x++){
					$field_additions =array(
						array('id'=>'evors_addf'.$x,'type'=>'yesno','name'=>'Additional Field #'.$x .' <code>[AF'.$x.']</code>','afterstatement'=>'evors_addf'.$x),
						array('id'=>'evors_addf'.$x,'type'=>'begin_afterstatement'),								
						array('id'=>'evors_addf'.$x.'_1','type'=>'text','name'=>'Field Name'),
						array('id'=>'evors_addf'.$x.'_ph','type'=>'text','name'=>'Field Placeholder Text',
							'legend'=>'Placeholder text is only visible for single line input text field and multiple line text box.'),
						array('id'=>'evors_addf'.$x.'_2','type'=>'dropdown','name'=>'Field Type','options'=> $this->_custom_field_types()),
						array(
							'id'=>'evors_addf'.$x.'_vis',
							'type'=>'dropdown',
							'name'=>'Visibility Type',
							'options'=> array(
								'def'=>__('Always', 'evors'),
								'yes'=>__('Only when user rsvp YES', 'evors'),
								'no'=>__('Only when user rsvp NO', 'evors'),
							)
						),
						array('id'=>'evors_addf'.$x.'_4','type'=>'text','name'=>'Option Values (only for Drop Down field, separated by commas)','default'=>'eg. cats,dogs',
							'legend'=>'Only set these values for field type = drop down. If these values are not provided for drop down field type it will revert as text field.'),
						array('id'=>'evors_addf'.$x.'_3','type'=>'yesno','name'=>'Required Field'),
						array('id'=>'evors_addf'.$x,'type'=>'end_afterstatement'),
					);
					$fields = array_merge($fields,$field_additions);
				}
			return $fields;
		}		

		// return an array list of supported different field types
		function _custom_field_types(){
			return apply_filters('evors_additional_field_types', array(
				'text'=>'Single Line Input Text Field', 
				'dropdown'=>'Drop Down Options', 
				'textarea'=>'Multiple Line Text Box',
				'checkbox'=>'Checkbox Line',
				'html'=>'Basic Text Line',
				'file'=>'Upload File Field',
				)
			);
		}
}

new EVORS_Settings();