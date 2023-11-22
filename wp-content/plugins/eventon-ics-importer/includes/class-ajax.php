<?php
/**
 * AJAX functions for the CSV importer
 * @version 2.0
 */


class EVOICS_ajax{
	// construct
		public function __construct(){
			$ajax_events = array(
				'evoics_001'=>'import_one_event',
				'evoics_002'=>'evoics_002',
				'evoics_process_file'=>'process_file',
				'evoics_process_uploaded_ics'=>'process_ics_file',
				'evoics_more_options'=>'more_import_options',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
		}

	// process ics file
		function process_file(){

			ob_start();

			?>
			<div id='evoics_import_content' class='evoics_import_content '>
			<?php

			$import_type = EVO()->cal->get_prop('evoics_import_type','evoics_1');

			switch ($import_type){
				case 'manual_link':
					$file_url = EVO()->cal->get_prop('evoics_file_url','evoics_1');

					echo "<form id='evoics_import_content_form'>";
					?><h2><?php _e('Import Using External ICS file URL','eventon');?></h2>
			 			
			 		<?php wp_nonce_field( EVOICS()->plugin_path, 'eventon_ics_noncename' );

			 			EVO()->elements->print_hidden_inputs(array(
							'file_type'=> 'file_link',
							'action'=>'evoics_process_uploaded_ics'
						));
			 		?>

			 		<?php if( $file_url ):?>

						<p><?php _e('Importing external Events From','eventon');?>: <?php echo $file_url;?></p>
						
						<?php

						echo EVO()->elements->print_trigger_element(array(
							'uid'=>'evoics_import_remote_file_go',
							'lb_class'=>'evoics_import',
							'lb_load_new_content'=> true,		
							'lb_loader'=>true,	
							'load_new_content_id'=> 'evoics_import_content_form',		
							'title'=> __('Start External Importing','evoics'),
						), 'trig_form_submit');

					// NO file url saved
					else:?>
						<p><?php _e('You must type the complete http URL of the ICS file to import events in Generat Settings','eventon');?></p>
					<?php endif;?>					
			 		<?php
			 		echo "</form>";

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

			 		<p><?php _e('Schedule is set to','eventon'); echo ': ';?><code> <?php echo  str_replace('schedule_', '', $import_type)?></code></p>	 

			 		<?php 
			 		if($next_run):

			 			$nonce = wp_create_nonce('evoics_'.date('Y-m-d',time()));
			 			
			 			$time_format = 'Y-m-d H:i:s';
			 			$next_run_time = get_date_from_gmt(date('Y-m-d H:i:s',$next_run), $time_format);

			 		?>
			 		<p><b><?php _e('Next Run','eventon');?>:</b> <?php echo $next_run_time . ' ('. EVO()->calendar->helper->time_since( time(), $next_run) . ')';?></p>
			 		<?php endif;?>

			 		<?php 
			 			echo EVO()->elements->print_trigger_element(array(
			 				'title'=> __('Run Now','evoics'),
			 				'uid'=>'evoics_cron_run',
			 				'lb_class'=> 'evoics_import',
			 				'lb_loader'=> true,
			 				'lb_hide_message'=> 5000,
			 				'ajax_data'=>array(
			 					'action'=>'evoics_002',
			 					'nonce'=> $nonce
			 				)
			 			),'trig_ajax');

			 		
				break;
				default:
					echo "<form id='evoics_import_content_form'>";
					echo "<h2>".__('Import Using ICS File','eventon')."</h2>";
					echo "<p>".__('Select the properly formated ICS file with events to process before importing.','eventon')."</p>";
						
						//settings_fields('eventon_ics_field_grp'); 
						wp_nonce_field( EVOICS()->plugin_path, 'eventon_ics_noncename' );

						EVO()->elements->print_hidden_inputs(array(
							'file_type'=> 'file_link',
							'action'=>'evoics_process_uploaded_ics'
						));
					?>
					<span class='evoics_ics_file_holder evomarb20' style=''>
						<input id="evoics_ics_file" type='file' data-file_type='.ics' name='events_ics_file'/>
					</span>
					<?php 
					/**/
					?>
					<p><a class='evoics_triger_fileup btn_prime evo_admin_btn'>Upload .ICS file</a></p>
					</form>
					<?php
				break;
			}

			echo "</div>";
				 	


			wp_send_json(array(
				'content'=> ob_get_clean(),
				'status'=>'good'
			)); wp_die();
		}

	// process the uploaded ics file
		function process_ics_file(){

			$EVOICS_Fnc = EVOICS()->fnc;
			$file = '';
			$events = array();

			ob_start();

			//print_r($_FILES);

			$import_type = EVO()->cal->get_prop('evoics_import_type','evoics_1');

			$time_start = microtime(true);		

			// get the file URL
				if( !empty($import_type) && $import_type=='manual_link'){

					$remote_events = EVOICS_Fnc::_get_remote_events();

					// error handling
					if( $remote_events == 'no_file'){
						wp_send_json(array(
							'msg'=> __('No ICS file URL saved in settings.'),
							'status'=>'bad'
						)); wp_die();
					}

					if( $remote_events == 'no_remote'){
						wp_send_json(array(
							'msg'=> __('Can not access the ICS file remotely. Make sure ICS file URL is correct.'),
							'status'=>'bad'
						)); wp_die();
					}

					$events = $remote_events;
				}

			// manual upload
				if( empty($import_type) ||	( !empty($import_type) && $import_type=='manual_file' )){

					// check for file
					if (empty($_FILES['events_ics_file']['tmp_name'])) {
						wp_send_json(array(
							'msg'=> __('No file uploaded, Please try again!.'),
							'status'=>'bad'
						)); wp_die();
					}

					$file = $_FILES['events_ics_file']['tmp_name'];

					$events = EVOICS_Fnc::get_events_from_ics($file);
				}

			
			// print the imported events from the ICS file
				$EVOICS_Fnc->print_imported_events( $events);

			wp_send_json(array(
				'content'=> ob_get_clean(),
				'status'=>'good',
				'd2'=> $events
			)); wp_die();
			
		}

	// more import options
		function more_import_options(){
			ob_start();

			echo "<div class='evopadl20 evopadr20'>";
			echo "<p>". __('Select these additional options, which will be applied to all the processed events from the ICS file.','evoics')."</p>";
			
			echo EVO()->elements->get_element(array(
				'type'=>'yesno',
				'id'=>'evo_hide_endtime',
				'label'=>__('Hide End time')
			));
			echo EVO()->elements->get_element(array(
				'type'=>'dropdown',
				'id'=>'_evcal_exlink_option',
				'name'=>__('Event User Interaction'),
				'options'=> array(
					'1'=>'Slide Down EventCard',
					'X'=> 'Do nothing',					
					'3'=>'Open as Popup window',
					'4'=>'Open event page'
				)
			));

			echo EVO()->elements->get_element(array(
				'type'=>'dropdown',
				'id'=>'evoics_time_mod',
				'name'=>__('Change Event Times by'),
				'options'=> array(
					'0'=> 'No Change',
					'-180'=> '-180 min (-3 hr)',
					'-120'=> '-120 min (-2 hr)',
					'-60'=> '-60 min (-1 hr)',
					'-30'=> '-30 min',					
					'+30'=> '+30 min',
					'+60'=> '+60 min (+1 hr)',
					'+120'=> '+120 min (+2 hr)',
					'+180'=> '+180 min (+3 hr)',
				),
				'value'=>'0',
				'legend'=>'- values will reduce the time, and + values will increase the time. Use this to adjust incorrections in the processed event times.'
			));
			
			echo "</div>";

			wp_send_json( array(
				'status'=>'good',
				'sp_content'=> ob_get_clean(),
				'sp_content_foot'=>'<span class="evoics_trig_more_options evo_btn evo_admin_btn">Apply Changes</span>'
			)); wp_die();
		}

	// run manual cron
		function evoics_002(){

			if(!wp_verify_nonce($_POST['nonce'], 'evoics_'.date('Y-m-d',time()) ) ){ echo 'Nonce Failed'; exit;}
			
			$cron_data = EVOICS()->cron->get_cron_data('evoics_schedule_action');
			$cron_result = EVOICS()->cron->run_cron('evoics_schedule_action', $cron_data['sig']);
			
			$return_content = array(
				'msg'=> $cron_result ? __('Successfully performed the scheduled fetching of events.','evoics') : __('Could not run the fetching of events, try again later.','evoics'),
				'status'=>($cron_result?'good':'bad')
			);		
			wp_send_json($return_content); wp_die();

		}

	// import individual event
		public function import_one_event(){

			if(!is_admin()) exit;

			$status = 'success';
			$event_link = '';

			if(!isset($_POST['events'])){
				$return_content = array('status'=>'No events submitted'	);				
				echo json_encode($return_content);		
				exit;
			}else{
				$event_list = $_POST['events'];
				
				foreach($event_list as $eventRow){

					$processedDATA = array();
					foreach($eventRow as $MDK=>$MD){
						$processedDATA[$MDK] = urldecode($MD);
					}

					// skipp dont sync events
					if(isset($processedDATA['status']) && $processedDATA['status']=='ns'){
						$return_content = array('status'=>'skipped'	);				
						echo json_encode($return_content);		
						exit;
					}
					
					$event_id = EVOICS()->fnc->import_event($processedDATA);
					$event_link =  get_edit_post_link($event_id);
				}
			}

			$return_content = array(
				'event_link'=> $event_link,
				'status'=> $status
			);				
			echo json_encode($return_content);		
			exit;
		}

	// supporting stuff
		function sanitize_csv_field($value){
			return '"' . addslashes(str_replace('"',"'",$value)) . '"';
		}
}
new EVOICS_ajax();