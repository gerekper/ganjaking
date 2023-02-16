<?php
/**
 * CRON job for ICS Importer
 */

class evoics_cron extends evo_cron{

	public $options = array();

	public function __construct(){
		$this->options = get_option('evcal_options_evoics_1');

		add_action('evoics_schedule_action', array($this, 'fetching_action'));	
		add_filter('cron_schedules',array($this,'my_cron_schedules'));		
	}

	// schedule fetching events
	function schedule_jobs(){
		$cron_hook = 'evoics_schedule_action';
		$timestamp = wp_next_scheduled( $cron_hook );

		if( !is_array($this->options) || !isset($this->options['evoics_import_type'])) return false;

		$time = $this->options['evoics_import_type'];

		
		$schedule_frequency = str_replace('schedule_', '', $time);

		// if schedule is not set
		if( $timestamp == false ) {
			// scheule is set up via settings
			if( !empty($this->options['evoics_import_type']) 
				&& $this->options['evoics_import_type'] !='manual_file'
				&& $this->options['evoics_import_type'] !='manual_link'
				&& !empty($this->options['evoics_file_url'])
			){
				
				wp_schedule_event( time(), $schedule_frequency, $cron_hook );
			}else{
				wp_clear_scheduled_hook($cron_hook);
			}		   	
		}else{ // schedule is already set

			if( !empty($this->options['evoics_import_type']) 
				&& $this->options['evoics_import_type'] !='manual_file'
				&& $this->options['evoics_import_type'] !='manual_link'
				&& !empty($this->options['evoics_file_url'])
			){	
				$cron_data = $this->get_cron_data($cron_hook);

				// if cron schedule not same as sync saved schadule
				if(!empty($cron_data['schedule']) && $cron_data['schedule'] != $schedule_frequency){
					wp_clear_scheduled_hook($cron_hook);

					// recreate the cron job					
					wp_schedule_event( time(), $schedule_frequency, $cron_hook );
				}
			}else{
				wp_clear_scheduled_hook($cron_hook);
			}
		}
	}

	// auto fethcing events
	function fetching_action(){
		if(!empty($this->options['evoics_file_url']) && $this->options['evoics_import_type'] !='manual_file' && $this->options['evoics_import_type'] !='manual_link'){

			// get events from ICS file
			$events = EVOICS()->fnc->get_events_from_ics($this->options['evoics_file_url']);

			$log = array();
			$log['time']= time();

			if(!empty($events) && sizeof($events)>0){
				$process_events = EVOICS()->fnc->process_fetched_events($events);

				$good = $bad = 0;
				foreach($process_events as $index=>$event){
					$status = EVOICS()->fnc->import_event($event);

					( $status !== false)? $good++: $bad++;
				}	

				$log['details'] = __('Imported: '.$good. " Failed: {$bad}",'eventon');
			}else{
				$log['details'] = __('No events found in ICS file','eventon');
			}

			// record cron log
			$this->record_log($log,'evoics');
		}		
	}

	// custom schedule
		function my_cron_schedules($schedules){
		   
		    if(!isset($schedules["weekly"])){
		        $schedules["weekly"] = array(
		            'interval' => 60*60*24*7,
		            'display' => __('Once every week'));
		    }
		    return $schedules;
		}
	
}