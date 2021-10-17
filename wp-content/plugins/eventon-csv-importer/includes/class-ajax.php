<?php
/**
 * AJAX functions for the CSV importer
 * @version 1.0
 */
class evocsv_ajax{
	// construct
		public function __construct(){
			$ajax_events = array(
				'evocsv_001'=>'evocsv_001',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
		}
	// import individual event
		public function evocsv_001(){

			if(!is_admin()) exit;

			if(!isset($_POST['events'])){
				$return_content = array(	'status'=>'No events submitted'	);				
				echo json_encode($return_content);		
				exit;
			}else{
				global $eventon_csv;
				$event_data = $_POST['events'];
				
				foreach($event_data as $event){

					$processedDATA = array();
					foreach($event as $MDK=>$MD){
						$processedDATA[$MDK] = urldecode($MD);
					}
					
					$status = $eventon_csv->admin->import_event($processedDATA);
				}
			}

			$return_content = array(
				'content'=> '',
				'event_id'=> $status,
				'status'=> ($status=='failed'?'bad':'success')
			);				
			echo json_encode($return_content);		
			exit;
		}

	// supporting stuff
		function sanitize_csv_field($value){
			return '"' . addslashes(str_replace('"',"'",$value)) . '"';
		}
}
new evocsv_ajax();