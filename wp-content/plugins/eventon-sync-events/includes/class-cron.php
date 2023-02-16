<?php
/**
 * Sync Addon Cron jobs
 * @version 1.1
 */

class evosy_cron{
	public function __construct(){
		$this->options = get_option('evcal_options_evosy_1');

		add_action('evosy_schedule_action_gg', array($this, 'fetching_action_gg'));		
		add_action('evosy_schedule_action_fb', array($this, 'fetching_action_fb'));	

		add_filter('cron_schedules',array($this,'my_cron_schedules'));	
	}

	// schedule for each fetching source
		function schedule_jobs(){
			global $eventon_sy;
			foreach($eventon_sy->functions->sources() as $name=>$abbr){

				$cron_hook = 'evosy_schedule_action_'.$abbr;
				$timestamp = wp_next_scheduled( $cron_hook );

				// if schedule is not set
				if( $timestamp == false ) {
					// scheule is set up via settings
					if( !empty($this->options['evosy_sync_method_'.$abbr]) 
						&& $this->options['evosy_sync_method_'.$abbr] !='manual'){
						
						$time = $this->options['evosy_sync_method_'.$abbr];
						wp_schedule_event( time(), $time, $cron_hook );
					}else{
						wp_clear_scheduled_hook($cron_hook);
					}		   	
				}else{ // schedule is already set

					if( !empty($this->options['evosy_sync_method_'.$abbr]) 
						&& $this->options['evosy_sync_method_'.$abbr] !='manual'
					){
						$cron_data = $this->get_cron_data($cron_hook);

						// if cron schedule not same as sync saved schadule
						if(!empty($cron_data['schedule']) && $cron_data['schedule'] != $this->options['evosy_sync_method_'.$abbr]){
							wp_clear_scheduled_hook($cron_hook);

							// recreate the cron job
							$time = $this->options['evosy_sync_method_'.$abbr];
							wp_schedule_event( time(), $time, $cron_hook );
						}
					}else{
						wp_clear_scheduled_hook($cron_hook);
					}
				}
			}
		}
		function fetching_action_gg(){
			$this->auto_fetching('google');
		}
		function fetching_action_fb(){			
			$this->auto_fetching('facebook');
		}

	// custom schedule
		function my_cron_schedules($schedules){
		   
		    if(!isset($schedules["3days"])){
		        $schedules["3days"] = array(
		            'interval' => 60*60*24*3,
		            'display' => __('Once every 3 days'));
		    }
		    if(!isset($schedules["5days"])){
		        $schedules["5days"] = array(
		            'interval' => 60*60*24*5,
		            'display' => __('Once every 5 days'));
		    }
		    if(!isset($schedules["weekly"])){
		        $schedules["weekly"] = array(
		            'interval' => 60*60*24*7,
		            'display' => __('Once every week'));
		    }
		    return $schedules;
		}

	// cron auto fetching events
		function auto_fetching($source){
			global $eventon_sy;
			$options = $this->options;
			$fnc = $eventon_sy->functions;

			// get sources
			$sources = $fnc->get_sources($source);
			$fetched_events = array();
			$s_attr = $fnc->source_abbre_name($source);

			$LOG = new EVOSY_Log();

			if(!$s_attr) return false;

			// if valid sources returned
			if(!empty($sources['output']) && sizeof($sources['output'])>0  && $sources['status']=='good'){
				
				
				foreach($sources['output'] as $stream){

					// fetch events
					$fetched_events = $fnc->fetch_events_stream($source, $stream);

					if( !empty($fetched_events) && $fetched_events['status']=='good' && !empty($fetched_events['events']) && sizeof($fetched_events['events'])>0){

						// pre process fetched events to meet eventon values
						$fetched_events = $fnc->pre_process_fetched_events($fetched_events['events'], $source);
						
						$processed_event_ids = array();

						// run through each event
						foreach($fetched_events as $event_id=>$event_data){


							// if event is already imported and imported event sync if off skip the event
							if(isset($event_data['status']) && $event_data['status'] == 'as' && isset($event_data['importedid']) && !evo_settings_check_yn($options, 'evosy_sync_imported_'.$s_attr) ) continue;

							// for already synced events
							if( isset($event_data['status']) && $event_data['status'] == 'as' && isset($event_data['importedid'])){

								$processed_event_ids[] = $event_data['importedid'];
								$fnc->update_event_description($event_data);
								$fnc->save_event_post_data($event_data['importedid'], $event_data, 'update');
							}else{
								
								if(empty($event_data['name'])) continue;
								
								if($new_event_id = $fnc->create_post($event_data) ){

									$processed_event_ids[] = $new_event_id;

									$fnc->save_event_post_data($new_event_id, $event_data);

									// import notice to event
									$field = $source=='facebook'? 'evosy_fb':'evosy_gg';
									$fnc->create_custom_fields($new_event_id, $field, $event_id);
									$fnc->create_custom_fields($new_event_id, '_stream_id', $stream['id']);
								}
							}

						}

						// record in log
						if(sizeof($processed_event_ids)>0){
							$events_ids = implode(', ', $processed_event_ids);
							$LOG->record('Processed events: '.$events_ids);
						}

					}
				}
			}
		}

	// return the cron data for a cron hook
		function get_cron_data($cron_hook){
			$crons = get_option('cron');

			if(!is_array($crons)) return false;

			$cron_job = array();

			foreach($crons as $time=>$cron){
				if(!is_array($cron)) continue;
				foreach ( $cron as $hook => $dings ) {

					if($hook != $cron_hook) continue;

					foreach ( $dings as $sig => $data ) {

						$cron_job = array(
							'time'=>$time,
							'sig'=>$sig,
							'schedule'=>(!empty($data['schedule'])? $data['schedule']:''),
							'interval'=>(!empty($data['interval'])? $data['interval']:'')
						);

					}
				}
			}

			return $cron_job;
	 	}

 	// perform a cron manually
	 	function run_cron($hookname, $sig){
	 		$crons = _get_cron_array();
			foreach ( $crons as $time => $cron ) {
				// for matching cron hook
				if ( isset( $cron[ $hookname ][ $sig ] ) ) {

					$args = $cron[ $hookname ][ $sig ]['args'];
					delete_transient( 'doing_cron' );
					wp_schedule_single_event( time() - 1, $hookname, $args );
					spawn_cron();
					return true;
				}
			}
			return false;

	 	}
}