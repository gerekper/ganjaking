<?php
/**
 * Handle general Cron functions for eventon and its addons
 * @since  2.6.1
 */

class evo_cron{

	public function __construct(){

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

	 // get cron hooks
	 // @added 2.6.6
	 	function get_all_cron_hooks(){
	 		$crons = get_option('cron');
	 		if(!is_array($crons)) return false;

	 		$cron_hooks = array();

	 		foreach($crons as $time=>$cron){
				if(!is_array($cron)) continue;
				foreach ( $cron as $hook => $dings ) {
					$cron_hooks[$hook]['dings'] = $dings;
					$cron_hooks[$hook]['time'] = $time;
				}
			}

	 		return $cron_hooks;
	 	}

	// next_run
		function next_run($hookname){
			$crons = _get_cron_array();

			if($crons){
				foreach($crons as $time =>$cron){
					foreach($cron as $hook=>$dings){
						if($hook == $hookname) return $time;
					}
				}
			}
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

	// delete cron
		function delete_cron($hookname, $sig=''){
			$next_run = $this->next_run( $hookname );

			$crons = _get_cron_array();

			if(empty($sig)){
				$cron_data = $this->get_cron_data($hookname);
				$sig = isset($cron_data['sig'])? $cron_data['sig']: null;
			}

			if(empty($sig)) return false;

			if ( isset( $crons[ $next_run ][ $hookname ][ $sig ] ) ) {
				$args = $crons[ $next_run ][ $hookname ][ $sig ]['args'];
				wp_unschedule_event( $next_run, $hookname, $args );
				return true;
			}
						
			return false;
		}

	// cron log creation
		function record_log($data, $key){
			$logs = get_option('evo_cron_logs');

			$logs = !empty($logs)? $logs: array();

			$logs[$key][] = $data;
			update_option('evo_cron_logs', $logs);
		}
		function get_log($key){
			$logs = get_option('evo_cron_logs');
			if(empty($logs[$key])) return false;
			return $logs[$key];
		}
}