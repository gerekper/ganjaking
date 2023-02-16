<?php
/**
 * Sync third party events settings page content
 * @version 0.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosy_settings{
	function __construct(){
		$this->options = get_option('evcal_options_evosy_1');
		$this->opt_evcal = get_option('evcal_options_evcal_1');
		echo $this->content();
	}

	// settings page content
		function content(){

			global $eventon, $eventon_sy;

			// Settings Tabs array
				$evcal_tabs = array(
					'evosy_1'=>__('General Settings','eventon'), 
					//'evosy_fb'=>__('Facebook','eventon'), 
					'evosy_gc'=>__('Google Calendar','eventon'), 
				);	
			
			$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evosy_1';

			$LOG = new EVOSY_Log();
			$LOG->display();

			
			// Update or add options
				if( isset($_POST['evosy_noncename']) && isset( $_POST ) ){				
					if ( wp_verify_nonce( $_POST['evosy_noncename'], AJDE_EVCAL_BASENAME ) ){

						foreach($_POST as $pf=>$pv){
							$pv = (is_array($pv))? $pv: (htmlspecialchars ($pv) );
							$evcal_options[$pf] = $pv;					
						}
						update_option('evcal_options_'.$focus_tab, $evcal_options);
						$_POST['settings-updated']='Successfully updated values.';
					
					//nonce check	
					}else{
						die( __( 'Action failed. Please refresh the page and retry.', 'eventon' ) );
					}	
				}
				?>
				<div class="wrap" id='evcal_settings'>
					<div id='eventon'><div id="icon-themes" class="icon32"></div></div>
					<h2><?php _e('Sync Events Settings','eventon');?> </h2>
					<h2 class='nav-tab-wrapper' id='meta_tabs'>
						<?php					
							foreach($evcal_tabs as $nt=>$ntv){							
								$evo_notification='';
								echo "<a href='?page=evosy&tab=".$nt."' class='nav-tab ".( ($focus_tab == $nt)? 'nav-tab-active':null)."' evcal_meta='evosy_1'>".$ntv.$evo_notification."</a>";
							}			
						?>
					</h2>	
				<div class='evo_settings_box'>		
			<?php		
			$updated_code = (isset($_POST['settings-updated']))? '<div class="updated fade"><p>'.$_POST['settings-updated'].'</p></div>':null;
			echo $updated_code;
					
			//TABS	
			switch ($focus_tab):

case "evosy_1":		
	?>
	<form method="post" action=""><?php settings_fields('evosy_field_group'); 
		wp_nonce_field( AJDE_EVCAL_BASENAME, 'evosy_noncename' );
	?>
	<div id="evosy_1" class="evcal_admin_meta evcal_focus">		
		<div class="inside">
		<?php	

			$evcal_opt= get_option('evcal_options_evosy_1');
			$__evo_admin_email = get_option('admin_email');

			// Default select tax #1 and #2
			$default_tax_fields = array();

			

		// ARRAY
			$cutomization_pg_array = array(
				
				array(
					'id'=>'evosy_gg_x',
					'name'=>__('Connection Settings for Google Calendar','eventon'),
					'tab_name'=>__('Google Calendar Connectivity','eventon'),
					'icon'=>'google-plus-square',
					'display'=>'show',
					'fields'=>array(
						array('id'=>'evosy_gg_apikey','type'=>'text','name'=>'Google API Key','legend'=>'API Key for your google developer account'),							
						array('id'=>'evosy_notif','type'=>'note',
							'name'=>'<span style="padding: 20px;margin: -10px -20px;display: block;background-color: #fdebcb;"><b>GUIDE: how to get API key: (FYI these steps may vary as google make changes)<br/><br/>
							Step 1:</b> Go to this wizard page <a href="https://console.developers.google.com/start/api?id=calendar" target="_blank">this wizard</a> and create or select existing project.<br/>
							<b>Step 2:</b> Once the API is enabled, click go to Credentials. and setup Credentialsfor the project by following instructions on the page.</br>
							<b>Step 3:</b> Once the Credentials are created, click on Credentials (key icon) on left side menu and select <b>Create Credentials</b> > API Key. This will give out the API key on a lightbox form.</br>
							<b>Step 4:</b> Go to the google calendar settings you want to fetch events. Under sharing settings make sure PUBLIC sharing is <b>ENABLED</b>.<br/><br/>

							<i>IMPORTANT: If these instructions were not followed correctly it will throw errors and will NOT pull any events. This will NOT fetch any past events.</i></span>'
						),
					
				)),
				array(
					'id'=>'evosy_gg',
					'name'=>__('Sync Settings for Google Calendar','eventon'),
					'tab_name'=>__('Google Calendar','eventon'),
					'icon'=>'google-plus-square',
					'fields'=>$this->settings_google()
				),	
				array(
					'id'=>'evosy_fb_x',
					'name'=>__('Connection Settings for Facebook','eventon'),
					'tab_name'=>__('Facebook Connectivity','eventon'),
					'icon'=>'facebook-square',					
					'fields'=>array(
						array('id'=>'evosy_notif','type'=>'note',
							'name'=>"
								<span style='padding: 30px;margin: -10px -20px;display: block;background-color:#ec7777; color:white;font-size:14px'>Due to facebook changes in their privacy policy and their API, facebook syncing is not working. As much as we hate it, we have decided to stop pursuing facebook API based event fetching. </span>
							"),
									
						
				)),
				/*array(
					'id'=>'evosy1',
					'name'=>__('Sync Settings for Facebook','eventon'),
					'tab_name'=>__('Facebook','eventon'),
					'icon'=>'facebook-square',					
					'fields'=>$this->settings_facebook()
				),*/
												
			);				
			
			$updated_code = (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? '<div class="updated fade"><p>'.__('Settings Saved','eventon').'</p></div>':null;
			echo $updated_code;
				
			print_ajde_customization_form($cutomization_pg_array, $evcal_opt);
		?>				
		</div>				
	</div>	
	<div class='evo_diag'>
		<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes','eventon') ?>" /><br/><br/>
		<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
	</div>		
	</form>
	</div>			
	<?php  
break;

// facebook tab
case 'evosy_fb':
	
	$options = $this->options;
	echo "<div class='evo_settings_page_loader'></div>";
	echo "<div class='postbox'><div class='inside'>";

	$data_check = true;

	// check if required facebook information is saved
	if(empty($options['evosy_fb_appid']) || empty($options['evosy_fb_secret'])	){
		echo "<p>".__('Facebook connectivity information is required!') . "</p>";
		$data_check = false;
	}

	if(empty($options['evosy_fb_uids'])  && empty($options['evosy_fb_eventids'])){
		echo "<p>".__('Facebook page ID or event ID required!') . "</p>";
		$data_check = false;
	}
	
	// importing method
	if($data_check ){
		if( !empty($options['evosy_sync_method_fb']) && $options['evosy_sync_method_fb']=='manual' || empty($options['evosy_sync_method_fb'])){
			?>
			<h2><?php _e('Manual Facebook Syncing','eventon');?></h2>
			
			<div class='evosy_initiate_fetching'>
				<p><?php _e('Click the button below to run the manual facebook event import process to fetch events from sources saved in facebook settings.','eventon');?></p>

				<p class='evosy_initiation '><a class='evo_admin_btn btn_prime evosync_fetch_init' data-source='facebook'><?php _e('Start Fetching');?></a> <i></i></p>
			</div>
			
			<?php 
			$this->fetching_settings_html('facebook');

		}else{ // auto

			echo "<h2>".__('Facebook fetching events is schedule to run automatically').": ".$options['evosy_sync_method_fb']."</h2>";

			$next_run = wp_next_scheduled( 'evosy_schedule_action_fb' );
			if(!empty($next_run)){	

				$time_format = 'Y-m-d H:i:s';
				$next_run_time = get_date_from_gmt(date('Y-m-d H:i:s',$next_run), $time_format);

				echo "<p class='evosy_next_run'><b>".__('Next Run').":</b> ". $next_run_time . ' ('. $eventon_sy->functions->time_since( time(), $next_run) . ')</p>';			
			}

			$cron_data = $eventon_sy->cron->get_cron_data('evosy_schedule_action_fb');

			echo "<p class='evosy_schedule_actions'><a class='evo_admin_btn btn_prime evosy_run_cron' data-id='evosy_schedule_action_fb' data-sig='{$cron_data['sig']}' data-time='{$cron_data['time']}'>".__('Run Now'). "</a>";
			echo "<i></i></p>";
		}
	}


	echo "</div>";
	echo "</div>";

break;

// Google Calendar Tab
	case 'evosy_gc':
		$options = $this->options;

		echo "<div class='evo_settings_page_loader'></div>";
		echo "<div class='postbox'><div class='inside'>";

		$data_check = true;

		// check if required facebook information is saved
		if(empty($options['evosy_gg_apikey'])){
			echo "<p>".__('Google Calendar connectivity information is required!','eventon') . "</p>";
			$data_check = false;
		}

		if(empty($options['evosy_gg_calid1']) ){
			echo "<p>".__('Google Calendar ID required!','eventon') . "</p>";
			$data_check = false;
		}
		
		// importing method
		if($data_check){
			if(  !empty($options['evosy_sync_method_gg']) && $options['evosy_sync_method_gg']=='manual' || empty($options['evosy_sync_method_gg'])){
				?>
				<h2><?php _e('Manual Google Calendar Syncing','evosy');?></h2>
				
				<div class='evosy_initiate_fetching'>
					<p><?php _e('Click the button below to run the manual google calendar event import process to fetch events from sources saved in google calendar settings.','evosy');?></p>

					<p class='evosy_initiation '><a class='evo_admin_btn btn_prime evosync_fetch_init' data-source='google'><?php _e('Start Fetching','evosy');?></a> <i></i></p>
				</div>
				<?php
				$this->fetching_settings_html('google');

			}else{ // auto

				echo "<h2>".__('Google Calendar fetching events is schedule to run automatically','evosy').": ".$options['evosy_sync_method_gg']."</h2>";

				$next_run = wp_next_scheduled( 'evosy_schedule_action_gg' );
				if(!empty($next_run)){			
					echo "<p class='evosy_next_run'><b>".__('Next Run').":</b> ". date('F j, Y g:i a',$next_run) . ' ('. $eventon_sy->functions->time_since( time(), $next_run) . ')</p>';			
				}

				$cron_data = $eventon_sy->cron->get_cron_data('evosy_schedule_action_gg');

				echo "<p class='evosy_schedule_actions'><a class='evo_admin_btn btn_prime evosy_run_cron' data-id='evosy_schedule_action_gg' data-sig='{$cron_data['sig']}' data-time='{$cron_data['time']}'>".__('Run Now','evosy'). "</a>";
				echo "<i></i></p>";


			}
		}

		echo "</div>";
		echo "</div>";
	break;
					
			endswitch;
			echo "</div>";

		} // end content()

	// Fteching HTML setting for all external sources
		function fetching_settings_html($source = 'facebook'){
			global $ajde;

			$s_attr = EVOSY()->functions->source_abbre_name($source);
			$sync_for_delete = ( $source=='google' && evo_settings_check_yn( EVOSY()->options, 'evosy_sync_deletenonexists_gg') )?'yes':'no';


			?>
			<div class="evosy_fetched_events" data-source='<?php echo $source;?>' data-syncdel='<?php echo $sync_for_delete;?>'>
				<div class="evosy_json_msg" style='display:none' data-msg='<?php echo json_encode(array(
					'M001'=> __('Importing fetched events, completed!','evosy'),
					'M002'=> __('No new events are selected for importing!','evosy'),
					'M003'=> __('--> Syncing for deleted events on Google Cal!','evosy'),
					'M004'=> __('--> Synced for deleted events completed!','evosy'),
				));?>'></div>
				<p class="fetching_msg gathering_msg"><?php _e('Gathering sources to fetch data...','evosy');?></p>
				<p class="fetching_msg fetchingdata_msg"><?php _e('Fetching process initiated...','evosy');?></p>
				<p class="fetching_msg processing_msg"><?php _e('Processing fetched data...','evosy');?></p>

				<div class="status progressive_status" style='display:none; padding-top:20px'>
					<p class='information'><b><?php _e('Fetching Events Status Report','evosy');?></b> -- 
						<span class='com'><?php _e('Completed:','evosy');?> <b>0</b></span> 
						<span class='fai'><?php _e('Failed:','evosy');?> <b>0</b></span> 
						<span class='fet'><?php _e('Fetched Events:','evosy');?> <b>0</b></span> 
						<a class='evo_admin_btn btn_triad detailed_report' style='margin-left:30px'><?php _e('Detailed Report','evosy');?></a></p>
					<div class="status_inside detailed_status" style='display:none'></div>
				</div>

				<div class="import_status progressive_status" style='display:none'>
					<p class='information'><b><?php _e('Events Processing Status Report','evosy');?></b> -- 
						<span class='com'><?php _e('Completed:','evosy');?> <b>0</b></span> 
						<span class='fai'><?php _e('Failed:','evosy');?> <b>0</b></span> 
						<span class='ski'><?php _e('Skipped:','evosy');?> <b>0</b></span> 
						<a class='evo_admin_btn btn_triad detailed_report' style='margin-left:30px'><?php _e('Detailed Report','evosy');?></a>
					</p>
				</div>
				
				<div class="final_status progressive_status" style='display:none'>
					<p class='information'></p>
				</div>
				
				<div class='evosy_upon_fetched_show' style='display:none'>

					<div class='evosy_above_table_notice' style='opacity:0.6'>
					<?php	

						echo "<p><b>NOTE:</b> ".__('If the fetched event time is incorrect you can alter that during importing stage by enabling respective settings in Sync settings.','evosy')."</br>";	

						$sync_imported = evo_settings_check_yn($this->options,'evosy_sync_imported_'.$s_attr)? '':'<code>NOT</code>';
						
						?><?php _e('Already imported events will','evosy');?> <?php echo $sync_imported;?> <?php _e('be synced! (You can change this from sync General Settings)','evosy');?><p>

					</div>
					<div class="evosy_action_buttons" style='display:none'>
						<a class='evo_admin_btn btn_triad deselect'><?php _e('Deselect All','evosy');?></a>
						<a class='evo_admin_btn btn_triad select'><?php _e('Select All','evosy');?></a>
						<span class='evosy_initiation processing'><a class='evo_admin_btn btn_prime process' data-source='<?php echo $source;?>' data-sync='<?php echo evo_settings_check_yn($this->options,'evosy_sync_imported_'.$s_attr)?'yes':'no';?>'><?php _e('Process','evosy');?></a> <i></i></span>
					</div>
					<?php	

					// create the table headers using AJDE library
					$ajde->wp_admin->start_table_header('fetched_events', 
						EVOSY()->functions->fetched_events_table_fields(), 
						array(
							'display'=>'none',
							'width'=>array(	'status'=>75	)
						)
					);
					$ajde->wp_admin->table_footer();
					?>
				</div>
			</div>
			<?php
		}

	// ARRAY for facebook calendar
		function settings_facebook(){
			$output = array();
			$output[] = array('id'=>'evosy_fb_uids','type'=>'textarea',
				'name'=>__('Publicly shared Organization or page ID(s)<br/> <i>(NOTE: Separate multiple IDs by commas. These facebook pages must be publicly shared with no restrictions or created by above facebook app)</i>','evosy'), 
				'legend'=>__('Facebook page name or facebook page IDs only. Do not paste individual event IDs in here. These pages should be either, created by the facebook API app or pages. Make sure these facebook pages are published as well. This field should not contain URLs.','evosy'));			

			$output[] = array('id'=>'evosy_fb_eventids','type'=>'textarea',
				'name'=>__('Individual facebook event ID(s) <br/><i>(NOTE: Separate multiple event ids by commas. These events must be publicly shared events or created by above facebook app)</i>','evosy'),
				'legend'=>__('Facebook event ID can be found from the URL to the facebook event page. eg. https://www.facebook.com/ events/836033933132047/ The Event IDS MUST be either create by above APP or public events.','evosy'));

			$output[] = array('id'=>'evosy_notif','type'=>'note',
				'name'=>'<span style="padding: 20px;margin: -10px -20px;display: block;background-color: #fdebcb;"><b>GUIDE: How to find a facebook page ID</b><br/>Go to the facebook page you want to pull events from. Go to <b>About</b> on left sidebar, under <b>More Info</b> you should see page ID (for pages you own or loggedin to)<br/><b>IMPORTANT</b>: Facebook events may not be visible due to privacy, age restricted events, or geographic restrictions.</span>
					<span style="padding: 10px 20px;margin: 0px -20px -8px;display: block;background-color: #ffe1da; color:#f9613e">
					<b>Important! </b> Adding too many facebook pages and/or facebook event IDs to fetch events from, may cause slowness in your webserver! 
					</span>
				'
			);			
			
			$output[] = array('id'=>'evosy_sync_method_fb',
				'type'=>'dropdown',
				'name'=>__('Import Method','evosy'),
				'options'=>$this->get_array_parts('import_method')
			);
			$output[] = array('id'=>'evosy_notif','type'=>'note',
				'name'=>'<span style="padding: 10px 20px;margin: -8px -20px -8px;display: block;background-color: #ffe1da; color:#f9613e">
					<b>Important! </b> Scheduled auto sync method rely on WordPress cron jobs to perform the sync function. These cron jobs may not be performed until the website is visited.</span>
				'
			);	
			$output[] = array('id'=>'evosy_post_status_fb',
				'type'=>'dropdown',
				'name'=>__('Default Status for Imported Events','evosy'),'width'=>'full',
				'options'=>array(
					'draft'=>'Draft',
					'publish'=>'Publish',
					'private'=>'Private'
				)
			);
			$output[] = array('id'=>'evosy_sync_imported_fb',
				'type'=>'yesno',
				'name'=>__('Sync already imported events','evosy'),
				'legend'=>__('This will sync already imported events with newly fetched events.','evosy')
			);			

			$output[] = array('id'=>'evosy_img_fb',
				'type'=>'yesno',
				'name'=>__('Stop fetching images for events','evosy'),
				'legend'=>__('This will stop fetching images from those external event sources.','evosy'));				
			$output[] = array('id'=>'evosy_adj_timezone_fb',
				'type'=>'yesno',
				'name'=>__('Adjust Fetched event time according to timezone','evosyevosy'),
				'legend'=>__('If you set this event time saved will be adjusted based on the timezone value passed in fetched event. Otherwise time will be saved just as it looks in fetched event time','evosyevosy')
			);
			$output[] = array('id'=>'evosy_disnamecheck_fb',
				'type'=>'yesno',
				'name'=>__('Disable existing event name check','evosy'),
				'legend'=>__('This will stop system from searching for events with same event name when fetching events.','evosy')
			);
			$output[] = array('id'=>'evosy_offset_fb',
				'type'=>'yesno',
				'name'=>__('Offset fetched events time','evosyevosy'),
				'legend'=>__('You can use this to offset the fetched event time to match a desired alternate time.','evosyevosy'),
				'afterstatement'=> 'evosy_offset_time_fb'
			);	
			$output[] = array('id'=>'evosy_offset_time_fb','type'=>'begin_afterstatement');
			$output[] = array('id'=>'evosy_offset_time_fb',
				'type'=>'text',
				'name'=>__('Enter the offset event time (in minutes) use +/- for offset','evosyevosy'),
			);
			$output[] = array('id'=>'evosy_offset_time_fb','type'=>'end_afterstatement');
			$output[] = array('id'=>'evosy_tix_uri_override_fb',
				'type'=>'yesno',
				'name'=>__('Use ticket url for learn more link','evosyevosy'),
				'legend'=>__('Setting this will force to use ticket URL for learn more link for the event instead of link to facebook event page','evosyevosy'),
			);

			$output[] = array('id'=>'evosy',
				'type'=>'subheader',
				'name'=>__('Event Type Category Settings','evosyevosy')
			);	

			// taxonomy names for array
				$_tax_names_array = evo_get_ettNames($this->opt_evcal);
				for($t=1; $t<3; $t++){
					$ab = ($t==1)? '':'_'.$t;
					$ett = get_terms('event_type'.$ab);
					$au_ett = array();

					// show option only if there are tax terms
					if(!empty($ett) && !is_wp_error($ett)){
						foreach($ett as $term){
							$au_ett[ $term->slug] = $term->name;
						}

						$output[] =array('id'=>'evosy_default_ett'.$t.'_fb',
							'type'=>'yesno',
							'name'=>'Assign '.$_tax_names_array[$t].' category term to imported events',
							'afterstatement'=>'evosy_default_ett'.$t.'_fb',
							'legend'=>'This will assign a selected '.$_tax_names_array[$t].' category term to imported event by default.');							
						$output[] =array('id'=>'evosy_default_ett'.$t.'_fb','type'=>'begin_afterstatement');
						$output[] =array('id'=>'evosy_val_ett'.$t.'_fb',
							'type'=>'dropdown',	
							'name'=>'Select default '.$_tax_names_array[$t].' term',
							'width'=>'full',
							'options'=>$au_ett,);
						$output[] =array('id'=>'evosy_default_ett'.$t.'_fb','type'=>'end_afterstatement');
					}
				}
			return $output;
		}


	// ARRAY for google calendar profiles and settings
		function settings_google(){
			$output = array();

			$output[] = array('id'=>'evosy_notif','type'=>'customcode','code'=> $this->code001());		

			// reused items
				$gg_api_tooltip = __('Cal ID is in the format of fhirehfuiher@group.calendar.google.com. You can find this in calendar settings. Make sure all calendar events are viewable in sharing settings to fetch events.','evosy');
			for($x=1; $x<= apply_filters('evosy_google_profiles',5); $x++){
				$output[] = array('id'=>'evosy_notif','type'=>'subheader','name'=>'Google Calendar ID #'.$x);
				$output[] = array('id'=>'evosy_gg_calid'.$x,'type'=>'text','name'=>'Calendar ID of the calendar to fetch events','legend'=>$gg_api_tooltip);	
			}
			$output[] = array('id'=>'evosy_sync_imported_gg',
				'type'=>'note',
				'name'=>'<span style="padding: 10px 20px;margin: -10px -20px -0px;display: block;background-color: #ffe1da; color:#f9613e">
					<b>Important! </b> Adding too many google calendar profiles to fetch events from, may cause slowness in your webserver! 
					</span><br/><a class="evo_admin_btn btn_triad" href="http://www.myeventon.com/documentation/add-google-calendar-profile-support/" target="_blank">How to add more google profiles</a>'
			);

			$output[] = array('id'=>'evosy_sync_method_gg',
				'type'=>'dropdown',
				'name'=>__('Import Method','evosy'),
				'options'=>$this->get_array_parts('import_method')
			);
			$output[] = array('id'=>'evosy_notif','type'=>'note',
				'name'=>'<span style="padding: 10px 20px;margin: -8px -20px -8px;display: block;background-color: #ffe1da; color:#f9613e">
					<b>Important! </b> Scheduled auto sync method rely on WordPress cron jobs to perform the sync function. These cron jobs may not be performed until the website is visited.</span>
				'
			);	
			$output[] = array('id'=>'evosy_post_status_gg',
				'type'=>'dropdown',
				'name'=>__('Default Status for Imported Events','evosy'),
				'width'=>'full',
				'options'=>array(
					'draft'=>	__('Draft','evosy'),
					'publish'=>	__('Publish','evosy'),
					'private'=>	__('Private','evosy')
				)
			);
			$output[] = array('id'=>'evosy_sync_imported_gg',
				'type'=>'yesno',
				'name'=>__('Sync already imported events','evosy'),
				'legend'=>__('This will sync already imported events with newly fetched events.','evosy')
			);
			$output[] = array('id'=>'evosy_sync_deletenonexists_gg',
				'type'=>'yesno',
				'name'=>__('Delete already synced events that were deleted from google calendar','evosy'),
				'legend'=>__('If already synced events were deleted from google calendar, this setting will delete those from the website as well when the next sync run is performed.','evosy')
			);			


			$output[] = array('id'=>'evosy_img_gg',
				'type'=>'yesno',
				'name'=>__('Stop fetching images for events','evosy'),
				'legend'=>__('This will stop fetching images from those external event sources.','evosy')
			);				
			$output[] = array('id'=>'evosy_adj_timezone_gg',
				'type'=>'yesno',
				'name'=>__('Adjust Fetched event time according to timezone','evosy'),
				'legend'=>__('If you set this event time saved will be adjusted based on the timezone value passed in fetched event. Otherwise time will be saved just as it looks in fetched event time','evosy')
			);
			$output[] = array('id'=>'evosy_disnamecheck_gg',
				'type'=>'yesno',
				'name'=>__('Disable existing event name check','evosy'),
				'legend'=>__('This will stop system from searching for events with same event name when fetching events.','evosy')
			);	
			$output[] = array('id'=>'evosy_offset_gg',
				'type'=>'yesno',
				'name'=>__('Offset fetched events time','evosy'),
				'legend'=>__('You can use this to offset the fetched event time to match a desired alternate time.','evosy'),
				'afterstatement'=> 'evosy_offset_time_gg'
			);	
			$output[] = array('id'=>'evosy_offset_time_gg','type'=>'begin_afterstatement');
				$output[] = array('id'=>'evosy_offset_time_gg',
					'type'=>'text',
					'name'=>__('Enter the offset event time (in minutes) use +/- for offset','evosy'),
				);
			$output[] = array('id'=>'evosy_offset_time_gg','type'=>'end_afterstatement');

			$output[] = array('id'=>'evosy','type'=>'subheader','name'=>__('Event Type Category Settings','evosy'));	
			// taxonomy names for array
				$_tax_names_array = evo_get_ettNames($this->opt_evcal);
				for($t=1; $t<3; $t++){
					$ab = ($t==1)? '':'_'.$t;
					$ett = get_terms('event_type'.$ab);
					$au_ett = array();

					// show option only if there are tax terms
					if(!empty($ett) && !is_wp_error($ett)){
						foreach($ett as $term){
							$au_ett[ $term->slug] = $term->name;
						}

						$output[] =array('id'=>'evosy_default_ett'.$t.'_gg',
							'type'=>'yesno',
							'name'=>'Assign '.$_tax_names_array[$t].' category term to imported events',
							'afterstatement'=>'evosy_default_ett'.$t.'_gg',
							'legend'=>'This will assign a selected '.$_tax_names_array[$t].' category term to imported event by default.');							
						$output[] =array('id'=>'evosy_default_ett'.$t.'_gg','type'=>'begin_afterstatement');
						$output[] =array('id'=>'evosy_val_ett'.$t.'_gg',
							'type'=>'dropdown',	
							'name'=>'Select default '.$_tax_names_array[$t].' term',
							'width'=>'full',
							'options'=>$au_ett,);
						$output[] =array('id'=>'evosy_default_ett'.$t.'_gg','type'=>'end_afterstatement');
					}
				}

			return $output;
		}

	// return partial arrays for the big settings array
		function get_array_parts($type){

			switch ($type) {
				case 'import_method':
					return apply_filters('evosy_import_method_settings', array(
						'manual'	=>__('Manual Import','evosy'),
						'daily'		=>__('Scheduled Daily','evosy'),
						'3days'		=>__('Scheduled Every 3 Days','evosy'),
						'5days'		=>__('Scheduled Every 5 Days','evosy'),
						'weekly'	=>__('Scheduled Weekly','evosy'),
						'monthly'	=>__('Scheduled Monthly','evosy'),
					));
				break;
				
			}
		}

	// Others
	function code001(){
		return sprintf( __("<a class='evo_admin_btn btn_triad' href='%s' target='_blank'>How to make calendar public and get calendar ID</a>",'evosy'),'http://www.myeventon.com/documentation/make-google-calendar-public-calendar-id/');
	}
}
new evosy_settings();
?>