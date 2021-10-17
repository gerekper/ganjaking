<?php
/**
 * Admin Settings for ICS importer
 * @version 0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//require_once 'lib2/IcalParser.php';
//use om\IcalParser;
//EVOICS()->parser = new IcalParser();



class EVOICS_settings{
	function __construct(){
		$this->fnc = EVOICS()->fnc;
		$this->options = EVOICS()->fnc->options;	
		
		echo $this->content();
	}
	function content(){
		global $eventon;

		$eventon->load_ajde_backender();

		// Settings Tabs array
		$tabs = array(
			'evoics_1'=>__('General Settings','eventon'), 
			'evoics_2'=>__('Process ICS File','eventon'),  			
		);

		$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evoics_1';

		// Update or add options
			if( isset($_POST['evoics_noncename']) && isset( $_POST ) ){				
				if ( wp_verify_nonce( $_POST['evoics_noncename'], AJDE_EVCAL_BASENAME ) ){

					foreach($_POST as $pf=>$pv){
						$pv = (is_array($pv))? $pv: (htmlspecialchars ($pv) );
						$evo_options[$pf] = $pv;					
					}
					update_option('evcal_options_'.$focus_tab, $evo_options);
					$_POST['settings-updated']='Successfully updated values.';
				
				//nonce check	
				}else{
					die( __( 'Action failed. Please refresh the page and retry.', 'eventon' ) );
				}	
			}
		?>
		<div class="wrap" id='evoics_settings'>
			<div id='eventon'><div id="icon-themes" class="icon32"></div></div>
			<h1><?php _e('Settings for Importing Events','eventon');?> </h1>
			<h2 class='nav-tab-wrapper' id='meta_tabs'>
				<?php					
					foreach($tabs as $nt=>$ntv){	
						echo "<a href='?page=evoics&tab=".$nt."' class='nav-tab ".( ($focus_tab == $nt)? 'nav-tab-active':null)."' evo_meta='evoics_2'>".$ntv."</a>";
					}			
				?>
			</h2>	
		<div class='metabox-holder evo_settings_box'>		
		<?php			
		$updated_code = (isset($_POST['settings-updated']))? '<div class="updated fade"><p>'.$_POST['settings-updated'].'</p></div>':null;
		echo $updated_code;
				
		//TABS	
		switch ($focus_tab):	
		
		// Import step 			
			case "evoics_1":
				
				?>
				<form method="post" action=""><?php settings_fields('evoics_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoics_noncename' );
				
				echo "<div id='evoics_1' class='evcal_admin_meta evcal_focus'>";
				?>
				<div class="inside">					
				<?php
					// ARRAY
					$cutomization_pg_array = array(
						array(
							'id'=>'EVOICSa',
							'name'=>'ICS Function Settings','display'=>'show',
							'tab_name'=>'Settings','icon'=>'gears',
							'fields'=>array(
								array('id'=>'EVOICS_status_publish',
									'type'=>'yesno',
									'name'=>'Publish imported events. (By default imported events will be saved as drafts)'
								),
								array('id'=>'evoics_timezone_method',
									'type'=>'dropdown',
									'name'=>'Event processing timezone method',
									'legend'=>'Which timezone method to use when processing events from ics file',
									'options'=>array(
										'none'=>'None, provided timezone from ics file',
										'wp'=>'WordPress timezone',
										'utc'=>'UTC timezone',
									)
								),
								array('id'=>'EVOICS_auto_allday_dis',
									'type'=>'yesno',
									'name'=>'Disable auto detect all day events based on ICS event time',
									'legend'=>'Timezone for the website need to be set as timezone string in wordpress settings. If not you can manually adjust times after import.'
									),
								array('id'=>'EVOICS_dupli_check',
									'type'=>'yesno',
									'name'=>'Enable duplication event name check during importing',
									'legend'=>'This will check for existing events with same name to avoid creating duplicate events.'
									),
								array(
									'id'=>'evoics_sync_fetched',
									'type'=>'yesno',
									'name'=>'Sync already imported events if event UID matches in the ICS file'
								),array(
									'id'=>'evoics_import_past',
									'type'=>'yesno',
									'name'=>'Import past events as well from ICS file',
									'legend'=> __('By default the system will import only upcoming events based on event start time. Enabling this option will make sure all the events, including those from past are also imported','evoics'),
								),
								array('id'=>'evoics_import_type',
									'type'=>'dropdown',
									'name'=>'Import Method',
									'options'=>array(
										'manual_file'=>'Manual import by uploading ICS File',
										'manual_link'=>'Manual import from ICS file URL',
										'schedule_daily'=>'Schedule import from ICS file URL - daily',
										'schedule_weekly'=>'Schedule import from ICS file URL - weekly',
										'schedule_monthly'=>'Schedule import from ICS file URL - monthly',
									)
								),array(
									'id'=>'evoics_file_url',
									'type'=>'text',
									'name'=>'ICS File URL - ONLY If you are using ICS file from external source',
									'default'=>'eg. http://www.google.com/ics/'
								),array(
									'id'=>'evoicenote',
									'type'=>'note',
									'name'=>'<b>NOTE:</b> If you are having trouble importing using URL, please make sure <code>allow_url_fopen</code> is enabled in your PHP configurations on the server',
								),
						)),
					);					
					

					$options_values = get_option('evcal_options_evoics_1');

					echo (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? 
						'<div class="updated fade"><p>Settings Saved</p></div>':null;
					
					print_ajde_customization_form($cutomization_pg_array, $options_values);
				?>

				</div>
				</div>
				<div class='evo_diag'>
					<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
					<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
				</div>
				</form>
				<?php
			break;
			case "evoics_2":				
				echo "<div id='evoics_2' class='postbox'><div class='inside'>";
				$steps = (!isset($_GET['steps']))?'ichi':$_GET['steps'];	
				echo $this->import_content($steps);
				echo "</div></div>";
			break;
		endswitch;
		echo "</div>";
	}

	// import
		function import_content($step){
			global $evoics;

			switch ($step) {
				// reading file and showing results
				case 'ni':
					$this->display_events();
				break;
				case 'ichi':					
					ob_start();

					
					// /$crons = _get_cron_array();
					// /print_r($crons);

					?>
					<form action='<?php echo admin_url()."admin.php?page=evoics&tab=evoics_2&steps=ni";?>' method='post' enctype='multipart/form-data'><?php

					switch ($this->options['evoics_import_type']){
						case 'manual_link':
							?><h2><?php _e('Import Using External ICS file URL','eventon');?></h2>
					 			
					 		<input type="hidden" name='file_type' value='file_link'>
					 		<?php wp_nonce_field( $evoics->plugin_path, 'eventon_ics_noncename' );?>

					 		<?php if(!empty($this->options['evoics_file_url'])):?>
							<p><?php _e('Importing external Events From','eventon');?>: <?php echo $this->options['evoics_file_url'];?></p>
							<p><input type='submit' name='' class='btn_prime evo_admin_btn' value='Start External Importing'/></p>
							<?php else:?>
								<p><?php _e('You must type the complete http URL of the ICS file to import events in Generat Settings','eventon');?></p>
							<?php endif;?>
							</form>
					 		<?php
						break;
						case 'schedule_daily':
						case 'schedule_weekly':
						case 'schedule_monthly':

							?><h2><?php _e('Importing Events from ICS file Scheduled to run automatically!','eventon');?></h2>
							
							<?php 
							$evo_cron = new evo_cron();
							
							$next_run = wp_next_scheduled( 'evoics_schedule_action' );
							//$next_run = $evo_cron->next_run('evoics_schedule_action');

							$logs = $evo_cron->get_log('evoics');
							if($logs):
							?>
							<p><?php _e('Past scheduled event imports that were performed will get recorded into a log and can be viewed from below','eventon');?></p>
							<p><a id='evoics_view_jobdetails' class='btn_prime evo_admin_btn'><?php _e('View Performed Import Job Details','eventon');?></a></p>
							<div class="evoics_log" style='display:none'>
								<?php
								foreach($logs as $log){
									if(empty($log['details']) && $log['time']) continue;
									echo "<p><b>".__('Time','eventon').":</b> ". date('Y-m-d H:s:i',$log['time']) ." <b>". __('Details','eventon'). ":</b> ".$log['details']."</p>";
								}
								?>
							</div>
							<?php endif;?>

					 		<p><?php _e('Schedule is set to','eventon'); echo ': ';?><code> 
					 			<?php echo  str_replace('schedule_', '', $this->options['evoics_import_type'])?></code>
					 		</p>	 

					 		<?php 
					 		if($next_run):

					 			$nonce = wp_create_nonce('evoics_'.date('Y-m-d',time()));
					 			
					 			$time_format = 'Y-m-d H:i:s';
					 			$next_run_time = get_date_from_gmt(date('Y-m-d H:i:s',$next_run), $time_format);

					 		?>
					 		<p><b><?php _e('Next Run','eventon');?>:</b> <?php echo $next_run_time . ' ('. EVO()->calendar->helper->time_since( time(), $next_run) . ')';?></p>
					 		<?php endif;?>

							<p class='evoics_schedule_actions'><a class='evoics_run_cron btn_prime evo_admin_btn' data-nonce="<?php echo $nonce;?>"><?php _e('Run Now','eventon');?></a> <i></i></p>
							</form>
					 		<?php
						break;
						default:
							echo "<h2>".__('Import Using ICS File','eventon')."</h2>";
							echo "<p>".__('Select the properly formated ICS file with events to process before importing.','eventon')."</p>";
								settings_fields('eventon_ics_field_grp'); 
								wp_nonce_field( $evoics->plugin_path, 'eventon_ics_noncename' );
							?>
							<input type="hidden" name='file_type' value='file_link'>
							<span style='background-color: #f7d5a8;padding: 10px 15px;border-radius: 8px; display: block;'>
								<input id="evoics_ics_file" type='file' name='events_ics_file'/>
							</span>
							<p><input type='submit' name='' class='btn_prime evo_admin_btn' value='Upload .ICS file'/></p>
							</form>
							<?php
						break;
					}
				 	
					?><div class='evoics_guidelines_section'><?php
					
					$this->fnc->print_guidelines();

					?></div>
					<?php

					echo ob_get_clean();
					
				break;
			}
		}

	// display fetched events list
		function display_events(){
			global $evoics, $ajde;

			// verify nonce
			if( !$this->ics_verify_nonce_post( 'eventon_ics_noncename'))	return false;
			$file = '';
			$events = array();

			$ITY = $this->options['evoics_import_type'];
			
			// get the file URL
				if( !empty($ITY) && $ITY=='manual_link'){
					if(empty($this->options['evoics_file_url'])){
						$this->log['error'][] = 'No file URL found, Please try again!.';
						$this->print_messages();
						$this->import_content('ichi');
						return;
					}
					$file = $this->fnc->upload_file($this->options['evoics_file_url'],'','ics');
					if($file && !empty($file[1])){
						$file = $file[1];
					}else{
						$this->log['error'][] = 'File upload did not work. Please try again later!.';
						$this->print_messages();
						$this->import_content('ichi');
						return;
					}
			// manual file upload
				}elseif(
					empty($ITY) || 
					( !empty($ITY) && $ITY=='manual_file' )
				){
					if (empty($_FILES['events_ics_file']['tmp_name'])) {
						$this->log['error'][] = 'No file uploaded, Please try again!.';
						$this->print_messages();
						$this->import_content('ichi');
						return;
					}

					$file = $_FILES['events_ics_file']['tmp_name'];
				}

				//print_r($_FILES);
			
			// load uploaded file content	
			$time_start = microtime(true);		
			if(!empty($file)){								
				$events = $this->fnc->get_events_from_ics($file);
			}		
						
			//print_r($events);
			
			$COUNT = count($events);
			
			
			// if no items present on processed
			if(empty($file)){
				echo "<p style='padding:4px 10px; background-color:#F9E5E1'>".__('Uploaded or file url is missing the ICS file.','evoics').'</p>';
				return false;
			}

			if($COUNT==0)
				echo "<p style='padding:4px 10px; background-color:#F9E5E1'>".__('IMPORTANT! We could not process any events from the ICS file provided by you. Either the ICS file is not properly built or you have no items in the ICS file. Please make sure you have constructed the ICS file according the the guidelines.','eventon')."</p>";

			if($COUNT==0) return false;

			echo "<h2>".__('Verify Processed Events & Import','eventon')."</h2>";
			echo "<p>".__('Please look through the events processed from the uploaded ICS file and select the ones you want to import into your website calendar.','eventon'). '<br/>Processed <b>'.$COUNT.'</b> items total.'."</p>";


			echo "<div class='evoics_data_section'>

				<p id='select_row_options'>
					<a class='deselect btn_triad evo_admin_btn'><span></span>Deselect All</a> <a class='select btn_triad evo_admin_btn'><span></span>Select All</a> <input id='evoics_import_selected' style='display:none; float:right' type='submit' class='btn_prime evo_admin_btn' value='".__('Import Selected Events','eventon')."'/>
					<a id='evoics_import_selected_items' class='btn_prime evo_admin_btn'><span></span>IMPORT</a>
				</p>

				<div id='evoics_import_progress' style='display:none'>
					<p class='bar'><span></span></p>
					<p class='text'><em class='processed'>0</em> out of <i>".$COUNT."</i> processed. <b class='loading'></b>
						<span class='failed' style='display:none'><em></em> Failed</span>
						<span class='skipped' style='display:none'><em></em> Skipped</span>
					</p>					
				</div>

				<div id='evoics_import_results' style='display:none'>
					<p class='results'><b></b>Import completed!</p>
	
					<p class='results_stats'>
						<span class='good'>Imported<em>0</em></span> 
						<span class='skipped'>Skipped<em>0</em></span>
						<span class='bad'>Failed<em>0</em></span>
					</p>
					<p><a class='view_imported_events' href='".admin_url()."edit.php?post_type=ajde_events'>View all imported events</a></p>
				</div>

				<p id='evoics_import_errors' style='display:none'>Error</p>
			
			<div id='evoics_fetched_events'>";
				settings_fields('eventon_ics_field_grp'); 
				wp_nonce_field( $evoics->plugin_path, 'eventon_ics_noncename' );


			$ajde->wp_admin->start_table_header('evoics_events', 
				array(
					'status'=>__('Status','eventon'),
					'event_name'=>__('Event Name','eventon'),
					'description'=>__('Description','eventon'),
					'start_date_time'=>__('Start Date & Time','eventon'),
					'end_date_time'=>__('End Date & Time','eventon'),
					'location'=>__('Location','eventon')
				), 
				array(
					'width'=>array(		)
				)
			);

		 	//print_r($events);
			
			$process_events = $this->fnc->process_fetched_events($events);
			//print_r($process_events);

			// for each fetched events
			foreach($process_events as $index=>$event){

				// skip the events without status
				if(!isset($event['status'])) continue;

				// event times
				$startTime = $event['evcal_allday']=='yes'?'All Day': $event['event_start_time'];
				$endTime = $event['evcal_allday']=='yes'?'All Day': $event['event_end_time'];


				$_desc_hover = !empty($event['DESCRIPTION']) ?"<span class='desc_text'>".
								eventon_get_normal_excerpt( $event['DESCRIPTION'] ,50)
								."...</span>":'';
				$hidden_fields = '';
				$ajde->wp_admin->table_row(
					array(						
						'status'=> $this->hidden_fields($event, $index). 
							"<input class='input_status evoics_event_data_row' type='hidden' name='events[{$index}][status]' value='". $event['status']."'/>".
							"<span class='status ".$event['status']. ($event['status']=='as'?' ss':'') . "' title='". ($event['status']=='as'? 'Already Synced':'Selected'). "'></span>",
						'event_name'=> "<span>".$event['event_name']."</span>",
						'description'=> "<span class='desc_box'><span class='desciption ".(!empty($event['DESCRIPTION'])?'check':'bar')."'></span>{$_desc_hover}</span>",
						'start_date_time'=> '<span class="event_start_date" data-i="'.$index.'">' .$event['event_start_date']."</span><span class='event_start_time' data-i='".$index."'>".$startTime .'</span>',
						'end_date_time'=> "<span class='event_end_date' data-i='".$index."'>". $event['event_end_date']."</span><span class='event_end_time' data-i='".$index."'>".$endTime .'</span>',
						'location'=> "<span class='".(!empty($event['LOCATION'])?'check':'bar')." eventon_ics_icons' title='".(!empty($event['LOCATION'])? $event['LOCATION']:'')."'></span>"
					),
					array(
						'tr_classes'=> array($event['status'], ($event['status']=='as'?'ss':''))
					)
				);
			}	

			$ajde->wp_admin->table_footer();

			echo "</div>";
			echo "</div>";
		}

		// throw input and textfields hidden fields
			function hidden_fields($ics_data, $count){	
				global $evoics;

				// for input that need to appear as textarea field 
				$textarea_fields = apply_filters('evoics_hidden_field_textarea',array('event_description'));
				
				$output = '';
				foreach($this->fnc->get_all_fields() as $field){
					if(empty( $ics_data[$field])) continue;

					if(in_array($field, $textarea_fields)){

						$f_val = '';
						if(!empty($ics_data[$field]) ){
							$f_val = $ics_data[$field];
							$f_val = str_replace('\n', '<br/>', $f_val);
							$f_val = stripslashes($f_val);
						}

						$output .= "<textarea class='evoics_event_data_row' style='display:none' name='events[{$count}][{$field}]'>". $f_val ."</textarea>";
					}else{
						$output .= "<input class='evoics_event_data_row' type='hidden' name='events[{$count}][{$field}]' ". 'value="'. ( addslashes($ics_data[$field]) ).'"/>';
					}	
				}

				// extra hidden fields
				foreach( apply_filters('evoics_hidden_field_extra', 
					array('repeat_freq','repeat_gap','repeat_num','repeat_until') ) 
					as $field){
					if(empty( $ics_data[$field])) continue;

					$output .= "<input class='evoics_event_data_row' type='hidden' name='events[{$count}][{$field}]' ". 'value="'. ( addslashes($ics_data[$field]) ).'"/>';
				}

				return $output;
			}

	    /** function to verify wp nonce and the $_POST array submit values	 */
			function ics_verify_nonce_post($post_field){
				global $_POST, $evoics;

				if(isset( $_POST ) && !empty($_POST[$post_field]) && $_POST[$post_field]  ){
					if ( wp_verify_nonce( $_POST[$post_field],  $evoics->plugin_path )){
						return true;
					}else{	
						$this->log['error'][] =__("Could not verify submission. Please try again.",'eventon');
						$this->print_messages();
						return false;	}
				}else{	
					$this->log['error'][] =__("Could not verify submission. Please try again.",'eventon');
					$this->print_messages();
					return false;	
				}
			}

		/** Print the messages for the ics settings	 */
			function print_messages(){
				if (!empty($this->log)) {
					
					if (!empty($this->log['error'])): ?>
					
					<div class="error">
						<?php foreach ($this->log['error'] as $error): ?>
							<p class=''><?php echo $error; ?></p>
						<?php endforeach; ?>
					</div>			
					<?php endif; ?>
					
					
					<?php if (!empty($this->log['notice'])): ?>
					<div class="updated fade">
						<?php foreach ($this->log['notice'] as $notice): ?>
							<p><?php echo $notice; ?></p>
						<?php endforeach; ?>
					</div>
					<?php endif; 
								
					$this->log = array();
				}
			}
			

}
new EVOICS_settings();