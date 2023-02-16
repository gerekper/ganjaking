<?php 
/**
 * Cron Jobs
 * @version 0.1
 */
class EVORM_Cron{

	public $timenow;
	private $EVO_Cron;
	public function __construct(){
		$this->EVO_Cron = new evo_cron();
		$this->run_cron();			
	}

	function run_cron(){
		$fnc = new evorm_fnc();
		$reminders = $fnc->get_reminders_prop();

		if($reminders and sizeof($reminders)>0){

			foreach($reminders as $event_id=>$types){
				// pre or post types
				foreach($types as $field_variable=>$status){

					$var = $fnc->_process_field_var($field_variable);
					if( is_array($var)){
						$addon = $var['addon'];
						$var = $var['var']; //evorm_pre_2						
					}

					$cron_hook = 'evorm_reminder_'.$event_id.$field_variable;
					if($status == 'created'){
						add_action($cron_hook, array($this, 'perform_cron'), 1, 2);
					}else{
						$this->delete_cron($cron_hook);
					}					
				}				
			}
		}
	}

	function perform_cron($event_id, $field_variable){
		
		$fnc = new evorm_fnc();
		$log = new EVORM_Log();

		$var = $fnc->_process_field_var($field_variable);
		$addon = $var['addon'];
		$var = $var['var']; //evorm_pre_2
		

		// stop if the reminder has been set already
		$reminder_status = $fnc->get_reminder_prop($event_id, $field_variable);

		if( $reminder_status == 'completed') return false;

		$cron_hook = 'evorm_reminder_'.$event_id.$field_variable;

		// send email
		$result = $fnc->send_email($event_id, $var, $addon);
		
		// record in the log	
		$log->add("Sent-reminder", $result, "Event({$event_id}) FVar({$field_variable})"); // record in log

		$status = $result ? 'completed':'attempted';

		// update the reminders status to not send reminder again
		$fnc->set_reminder_prop($event_id, $field_variable, $status);

		// Delete the one time cron job from happening again
		$this->delete_cron($cron_hook);

	}

	// DELETING and removing cron jobs
		function delete_cron($cron_hook){
			$cron_data = $this->EVO_Cron->get_cron_data($cron_hook);
			if(isset($cron_data['sig'])) $this->EVO_Cron->delete_cron($cron_hook, $cron_data['sig']);
		}
		function delete_cron_job_from_reminder_OBJ($event_id, $field_variable){
			$fnc = new evorm_fnc();
			$fnc->set_reminder_prop($event_id, $field_variable, 'created');
		}


	// schedule is set when event is saved
	function schedule_reminders($event_id, $field_variable){		
		$fnc = new evorm_fnc();

		// if invalid field variable provided
		if( strpos($field_variable, '-') === false) return false;
		
		// addon
			$varAR = $fnc->_process_field_var($field_variable);
			$addon = $varAR['addon'];
			$var = $varAR['var']; //evorm_pre_2			
		
		$log = new EVORM_Log();
		$cron_hook = 'evorm_reminder_'.$event_id.$field_variable; // _evorm_pre_2-tx

		$cron_data = $this->EVO_Cron->get_cron_data($cron_hook);
		//$cron_timestamp = wp_next_scheduled( $cron_hook );
		$cron_timestamp = $cron_data? $cron_data['time']:false;

		
		$datetime = new evo_datetime();
		$datetime->set_timezone(); // set correct timezone
		
		$time = $this->get_reminder_time($event_id, $varAR);

		if(!$time) return false; // if reminder offset time is not saved

		$timenow = time();

		// schedule is not set
		if($cron_timestamp == false){

			$check_enable = ($addon == 'tx')? EVOTX()->check_tx_prop($var): EVORS()->check_rsvp_prop($var);
			$check_time = ($addon == 'tx')? EVOTX()->get_tx_prop($var.'_time'): EVORS()->get_rsvp_prop($var.'_time');
			$check_msg = ($addon == 'tx')? EVOTX()->get_tx_prop($var.'_message'): EVORS()->get_rsvp_prop($var.'_message');

			if($check_enable && 
				$check_time && 
				$check_msg &&
				$time >= $timenow
			){				
				
				// record if the reminder status is not created
				$reminder_status = $fnc->get_reminder_prop($event_id, $field_variable);
				if( $reminder_status != 'created'){
					wp_schedule_single_event($time, $cron_hook, array($event_id, $field_variable));
					$log->add("Reminder-created", true, "Event({$event_id}) Var({$var}) Hook({$cron_hook}) " );
					$fnc->set_reminder_prop($event_id, $field_variable, 'created');
				}	

			}else{
				$reason = '';
				if(!$check_enable) $reason .= 'Not enabled';
				if(!$check_time) $reason .= 'No Time';
				if(!$check_msg) $reason .= 'No Msg';
				if( $time < $timenow) $reason .= 'Time Past';

				$log->add("Reminder-deleted", true, $reason. " - Event({$event_id}) Var({$var}) Hook({$cron_hook}) " );
				$this->delete_cron($cron_hook);
			}
			
			
		}else{ // schedule is already set
			// check if it already ran			
			$reminder_prop = $fnc->get_reminder_prop($event_id, $field_variable);

			// there is another cron event with same cron hook on different time
			if( $cron_timestamp != $time){
				$this->delete_cron($cron_hook);
				wp_schedule_single_event($time, $cron_hook, array($event_id,$field_variable));
				$fnc->set_reminder_prop($event_id, $field_variable, 'created');

				$log->add("Reminder-deleted-&-created", true, "Event({$event_id}) Var({$var}) Hook({$cron_hook}) " );
			}else{
				// if cron reminder was already completed or attempted
				if($reminder_prop && $reminder_prop !='created'){
					$this->delete_cron($cron_hook);
					$log->add("Reminder-deleted", true, "Event({$event_id}) Var({$var}) Hook({$cron_hook}) " );
				}else{// no record of this cron reminder exists in reminders object
					$this->delete_cron($cron_hook);
					wp_schedule_single_event($time, $cron_hook, array($event_id,$field_variable));
					$fnc->set_reminder_prop($event_id, $field_variable, 'created');
					$log->add("Reminder-created", true, "Event({$event_id}) Var({$var}) Hook({$cron_hook}) " );
				}
			}
			
		}
	}

	// unschedule and trash disabled reminders for events
	function unschedule_reminders($event_id, $field_variable){
		$fnc = new evorm_fnc();
		$log = new EVORM_Log();

		$cron_hook = 'evorm_reminder_'.$event_id. $field_variable;
		
		// delete existing crons
		$this->delete_cron($cron_hook);
		//wp_clear_scheduled_hook($cron_hook);
		//$fnc->set_reminder_prop($event_id, $field_variable, 'removed');
		$fnc->trash_reminder($event_id, $field_variable);
		$log->add("Reminder-unscheduled", true, "Event({$event_id}) Var({$field_variable}) Hook({$cron_hook})");
	}

	// get the time for reminder cron jobs
	// $var used only to see if its pre or post
	function get_reminder_time($event_id, $varAR){

		$var = isset($varAR['var'])? $varAR['var']: false;;
		$addon = isset($varAR['addon'])? $varAR['addon']: false;

		$is_pre_reminder = (strpos($var, 'pre') === false) ? false: true;

		// based on pre or post fetch correct event start or end time
		// if var has pre then use start
		$time_field = $is_pre_reminder? 'evcal_srow':'evcal_erow';
		$event_time = get_post_meta($event_id, $time_field, true);


		$datetime = new evo_datetime();
		$offset = $datetime->get_UTC_offset();
		//$offset = 0;

		$unix_event_time = $event_time - $offset;


		// validation
		if(!$var || !$addon ) return $unix_event_time;
		
		$reminder_offset_time = ($addon == 'tx')? (int)EVOTX()->get_tx_prop($var.'_time'): (int)EVORS()->get_rsvp_prop($var.'_time');

		// return false is no reminder offset time is saved
		if(empty($reminder_offset_time) || $reminder_offset_time == 0) return false; 

		$reminder_offset_time = $reminder_offset_time *60;
	

		// based on pre/post add/substract reminder time offset
		return ( $is_pre_reminder )? 
			$unix_event_time - $reminder_offset_time: 
			$unix_event_time + $reminder_offset_time;
	}
}