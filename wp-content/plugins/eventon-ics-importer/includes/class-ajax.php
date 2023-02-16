<?php
/**
 * AJAX functions for the CSV importer
 * @version 1.0
 */
class EVOICS_ajax{
	// construct
		public function __construct(){
			$ajax_events = array(
				'evoics_001'=>'evoics_001',
				'evoics_002'=>'evoics_002',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
		}

	// run manual cron
		function evoics_002(){

			if(!wp_verify_nonce($_POST['nonce'], 'evoics_'.date('Y-m-d',time()) ) ){ echo 'Nonce Failed'; exit;}
			
			$cron_data = EVOICS()->cron->get_cron_data('evoics_schedule_action');
			$cron_result = EVOICS()->cron->run_cron('evoics_schedule_action', $cron_data['sig']);

			if($cron_result){
				$output = "<span><b class='evo_circular_checkmark'></b>".__('Successfully performed the scheduled fetching of events.','eventon') ."</span>";
			}else{
				$output = "<span class='bad'>".__('Could not run the fetching of events, try again later.','eventon') ."</span>";
			}
			$return_content = array(
				'content'=>$output,
				'status'=>($cron_result?'good':'bad')
			);		
			echo json_encode($return_content); exit;

		}

	// import individual event
		public function evoics_001(){

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