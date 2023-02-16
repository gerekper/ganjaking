<?php
/**
 * Admin
 * @version 0.1
 */
class evorm_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));		
	}

	function admin_init(){
		include_once('class-post_meta.php');

		add_filter('evors_settings_fields', array($this, 'settings_fields_rsvp'), 10, 1);
		add_filter('evotix_settings_page_content', array($this, 'settings_fields_tx'), 10, 1);
		add_action('evo_after_settings_saved', array($this, 'settings_saved'), 10, 3);	

		add_filter('evo_addons_details_list', array($this, 'addons_list_inclusion'), 10, 1);	
	}	

	// include reminders in addons list
		function addons_list_inclusion($array){
			$array['eventon-reminders']= array(
				'id'=>'EVORM',
				'name'=>'Reminders',
				'link'=>'http://www.myeventon.com/addons/reminders',
				'download'=>'http://www.myeventon.com/addons/reminders',
				'desc'=>'Set custom reminders for events'
			);

			return $array;
		}
	// when settings are saved
	// delete all scheduled cron jobs for reminders that were disabled
		function settings_saved($focus_tab, $current_section,  $prop){
			if( $focus_tab != 'evcal_rs' || $focus_tab != 'evcal_tx') return false;

			$CRON = new evo_cron();

			$cron_hooks = $CRON->get_all_cron_hooks();

			// if there are no scheduled cron hooks
			if(!$cron_hooks) return false;

			foreach(EVORM()->get_reminders() as $key=>$val){
				// if a reminder was turned off make sure to delete all cron jobs for that
				if( isset($prop['evorm_'.$key]) && $prop['evorm_'.$key] == 'no'){
					foreach($cron_hooks as $cron_hook=>$data){
						if( strpos($cron_hook, 'evorm_'. $key) !== false){
							$CRON->delete_cron( $cron_hook );
						}
					}
				}
			}
		}

	// settings fields
		function settings_fields_rsvp($array){	return $this->settings_fields($array, 'rs');		}
		function settings_fields_tx($array){	return $this->settings_fields($array, 'tx');		}

		function settings_fields($array, $addon){
			$logs = new EVORM_Log();
			$logs->display();
			
			$site_name = get_bloginfo('name');
			$site_email = get_bloginfo('admin_email');

			// Addon based fields
				$addon_name = $addon=='tx'? 'Tickets': 'RSVP';
				$dynamic_fields = $addon == 'tx'? '': "<code>{rsvp-yes-count}</code><code>{rsvp-no-count}</code>";
				if($addon == 'tx'){
					$guest_types = array(
						'completed'=>'All guests with completed orders',
						'pending'=>'All guests with pending orders'
					);

				}else{
					$guest_types = array(
						'all'=>'All Guests (Attending or not attending)',
						'coming'=>'All attending guests, including maybe',
						'notcoming'=>'All not-attending guests',
						'checkedguests'=>'All attending guests that are checked-in',
						'notcheckedguests'=>'All attending guests that have not checked-in (including maybe)',
					);
				}

			// constants
				$const_1 = 'If this option is activated, the selected user group will receive and auto scheduled email message at designated time before or after event.';
				

			$settings_fields = array();
			$settings_fields[] = array('id'=>'evorm','type'=>'note',
						'name'=>'<b>NOTE:</b> Reminder emails for '.$addon_name.' will use cron system to send automatically scheduled emails to selected guests. The cron method may not send the email precisely at that designated time. The auto reminder emails can be enabled within each event edit page under '.$addon_name.' settings.<br/><br/>
						
						<b>IMPORTANT:</b> If an already activated event reminder was disabled and saved, all the already scheduled reminder emails for that particular reminder type for events will be deleted.<br/><br/>

						<b>EMAILING:</b> If you are having issues receiving reminder emails, we recommend using a <a href="https://wordpress.org/plugins/search/smtp+plugin/" target="_blank">SMTP wordpress plugin</a>.<br/><br/>
							
							<b>Supported Dynamic Tags for Email Message</b>
							<span style="display:block; padding:10px 0">
								<code>{event-name}</code> 
								<code>{event-link}</code>'.$dynamic_fields.'								
							</span>
							Using these tags within the email message for reminders email will be replaced with dynamic content.'
					);
			$settings_fields[] = array('id'=>'evorm_from_name',
				'type'=>'text',
				'name'=>'"From" Name',
				'default'=>$site_name
			);
			$settings_fields[] = array('id'=>'evorm_from_email','type'=>'text',	'name'=>'"From" Email Address' ,'default'=>$site_email);


			foreach( EVORM()->get_reminders()	as $index=>$val ){

				$settings_fields[] = array(
					'id'=>			"evorm_{$index}",
					'type'=>		'yesno',
					'name'=>		$val['label'],
					'legend'=>		$const_1,
					'afterstatement'=>"evorm_{$index}",
				);

				$is_post = strpos($index, 'post') ===false? false:true;

				$settings_fields[] = array('id'=>"evorm_{$index}",'type'=>'begin_afterstatement');	
					$settings_fields[] = array('id'=>"evorm_{$index}_time",
						'type'=>'text',
						'name'=> $is_post? 'Amount of time (in minutes) after the event end time to send this email':'Amount of time (in minutes) before the event start time to send this email',
						'default'=>'eg. 240'
					);
					$settings_fields[] = array('id'=>"evorm_{$index}_group",
						'type'=>'dropdown',
						'name'=>'Select emailing group',
						'options'=>$guest_types
					);
					$settings_fields[] = array('id'=>"evorm_{$index}_subject",
						'type'=>'text',
						'name'=>'Email Subject line',
						'default'=>'New '. $addon_name. ' Notification'
					);
					$settings_fields[] = array('id'=>"evorm_{$index}_message",
						'type'=>'textarea',
						'name'=>'Email Message',
						'legend'=>'You can also include HTML content inside the message'
					);
					
				$settings_fields[] = array('id'=>"evorm_{$index}",'type'=>'end_afterstatement');

			};

			// preview of the email
			if(isset($_GET['preview'])){
				$settings_fields[] = array('id'=>'evorm_from_code','type'=>'customcode',	'code'=> $this->cstomcode());
			}
					
			
			$array[] = array(
				'id'=>'evors_reminders','display'=>'none',
				'name'=>'Reminders Settings for '. $addon_name,
				'tab_name'=>'Reminders','icon'=>'envelope',
				'fields'=> $settings_fields 			
			);

			return $array;
		}

	function cstomcode(){
		ob_start();
		$var = 'evorm_pre_1';
		$event_id = 34;
		$fnc = new evorm_fnc();
		$msg = $fnc->get_email_message($event_id, $var);

		$msg = $fnc->closetags($msg);
		$message_body = "<div style='padding:15px'>".html_entity_decode($msg). "</div>";
		echo $fnc->get_evo_email_body($message_body);

		return ob_get_clean();
	}
}