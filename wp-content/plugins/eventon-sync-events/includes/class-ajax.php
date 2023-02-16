<?php
/** 
 * Admin AJAX
 * @version 0.1
 */
class evosync_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'sync_get_streams'=>'sync_get_streams',
			'sync_fetch_from_source'=>'sync_fetch_from_source',
			'sync_process_events'=>'sync_process_events',
			'sync_delete_synced'=>'sync_delete_synced',
			'sync_run_cron_job'=>'sync_run_cron_job',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// fetch streams from source
		function sync_get_streams(){
			$output = array();
			$status = 'bad';
			
			// Get sources
				if(!empty($_POST['source'])){
					global $eventon_sy;

					$sources = $eventon_sy->functions->get_sources($_POST['source']);
					if(!empty($sources['output']) && sizeof($sources['output'])>0  && $sources['status']=='good'){
						$output = $sources['output'];
						$status = 'good';
					}
				}
			
			$return_content = array(
				'content'=> $output,
				'status'=>$status
			);		
			echo json_encode($return_content); exit;
		}
	// fetch events from source
		function sync_fetch_from_source(){
			global $eventon_sy;

			$fetched_events = array();
			$status = 'bad';
			$message = '';

			// no source provided
			if(empty($_POST['source'])){ echo json_encode(array('status'=>'bad','content'=>'Source missing!')); exit;}

			$fetched_events = $eventon_sy->functions->fetch_events_stream($_POST['source'], $_POST);

			// get HTML representation of fetched events
				//print_r($fetched_events);
				$content = '';
				if( !empty($fetched_events) && $fetched_events['status']=='good' && !empty($fetched_events['events']) && count($fetched_events['events'])>0){
					//print_r($fetched_events);
					$content = $eventon_sy->functions->process_fetched_events_data_array(
						$fetched_events['events'], 
						$_POST['source'], 
						$_POST['id']
					);
					$status = 'good';
				}else{					
					if(!empty($fetched_events['message']))
						$message = $fetched_events['message'];
				}
				

			$return_content = array(
				'html'=>$content,
				'status'=>$status,
				'message'=> $message,
				'events'=>( (is_array($fetched_events['events']) && sizeof($fetched_events['events'])>0 ) ? count($fetched_events['events']):0)
			);		
			echo json_encode($return_content); exit;
		}

	// Import the events into the site
		function sync_process_events(){
			
			$status = 'bad';
			$message = '';
			$event_id = '';

			// verify nonce for the form
			//if( !$eventon_sy->functions->verify_nonce_post( 'eventon_sy_noncename')){
			//	$message = __('Nonce verification failed','evosy');
			//}

			// verify event post data exists
			if(empty($_POST['event_data'])){	
				$message = __('No event present for importing','evosy');
			}else{
				$fnc = EVOSY()->functions;

				$event = $_POST['event_data'];
				$eventdata = array();
				foreach($event as $field=>$data){
					if(is_array($data) ){
						$eventdata['event_meta'] = $data;
					}else{
						$eventdata[$field] = urldecode($data);
					}					
					
				}				

				// sync already imported events
				if(!empty($eventdata['importedid']) && $eventdata['status']=='as' ){
					$fnc->update_event_description($eventdata);
					$fnc->save_event_post_data($eventdata['importedid'], $eventdata, 'update');
					$status = 'good';
					$event_id = (int)$eventdata['importedid'];
				}

				if($eventdata['status'] == 'ss'){
					if($new_event_id = $fnc->create_post($eventdata) ){
						
						$fnc->save_event_post_data($new_event_id, $eventdata);

						// import notice to event
						$field = (!empty($eventdata['source']) && $eventdata['source']=='facebook')? 'evosy_fb':'evosy_gg';
						$fnc->create_custom_fields($new_event_id, $field, $eventdata['id']);
						$fnc->create_custom_fields($new_event_id, '_stream_id', $eventdata['stream_id']);

						$event_id = $new_event_id;
						$status = 'good';
					}else{
						$message = __('Could not create post for event','evosy').' :'. $eventdata['id'];
					}
				}
			}

			$return_content = array(
				'event_id'=>	empty($event_id)? 'na': $event_id,
				'event_link'=> 	empty($event_id)?'na':get_edit_post_link($event_id),
				'message'=> 	$message,
				'status'=>		$status
			);		
			echo json_encode($return_content); exit;
		}

	// delete synced events, if deleted in source as well
		function sync_delete_synced(){

			$events = $_POST['events'];

			$deleted_count = EVOSY()->functions->delete_synced_events( $events );
			echo json_encode(array( 'status'=>'good', 'count'=>$deleted_count)); exit;
		}

	// run a cron job manually
		function sync_run_cron_job(){
			$cron_hook = wp_unslash($_POST['id']);
			$cron_sig = wp_unslash($_POST['sig']);

			$cron_result = EVOSY()->cron->run_cron($cron_hook, $cron_sig);

			if($cron_result){
				$output = "<span class='good'><b class='evo_circular_checkmark'></b>".__('Successfully performed the scheduled fetching of events.','evosy') ."</span>";
			}else{
				$output = "<span class='bad'>".__('Could not run the fetching of events, try again later.','evosy') ."</span>";
			}

			$return_content = array(
				'content'=>$output,
				'status'=>($cron_result?'good':'bad')
			);		
			echo json_encode($return_content); exit;
		}


}
new evosync_admin_ajax();