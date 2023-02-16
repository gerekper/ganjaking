<?php
/**
 * Data log for Reminders
 */

class EVORM_Log{

	public $log_type = 'reminders';

	public function add($task, $result, $note=''){
		$logs = $this->get_logs();

		$result = $result? 'Good': 'Bad';
		
		$rand = rand(1000,9999);

		$logs[time().'-'.$rand] = $task.': Result('.$result.') '.(empty($note)?'':'- '.$note);

		$this->save_logs( $logs);
	}

	public function display(){
		if(isset($_REQUEST['page']) && $_REQUEST['page']=='eventon' && isset($_REQUEST['tab']) && 
			$_REQUEST['tab']=='evcal_rs' 
			&& isset($_REQUEST['task']) && $_REQUEST['task']=='logs'
		){
			
			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:250px; overflow-y:auto'>";
			$log = $this->get_logs();

			if($log){
				$log = array_filter($log);

				if(sizeof($log)==0){ 
					echo 'No Log!'; 
				}else{
					echo "<b>Data Log</b><br/>";
					foreach($log as $time=>$data){
						$time = explode('-', $time);
						echo date('Y-m-d h:i:s',$time[0]).": ". $data."<br/>";
					}
				}				
			}else{
				echo 'No Log!';
			}		
			echo "</div>";
		}

		// flush all the data logs
		if(isset($_REQUEST['page']) && $_REQUEST['page']=='eventon' && isset($_REQUEST['tab']) && 
			$_REQUEST['tab']=='evcal_rs' 
			&& isset($_REQUEST['task']) && $_REQUEST['task']=='flushlogs'
		){
			$this->flush_logs();

			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:250px; overflow-y:auto'>";
			echo "All detail logs has been trashed!";
			echo "</div>";
		}
	}

	private function save_logs($thislogs){
		$logs = get_option('_evo_datalogs');

		$logs[$this->log_type] = $thislogs;

		update_option('_evo_datalogs', $logs);
	}
	private function get_logs(){
		$logs = get_option('_evo_datalogs');

		if( empty($logs)) return array();
		if(!isset($logs[$this->log_type])) return array();

		return $logs[$this->log_type];
	}
	private function flush_logs(){
		$logs = get_option('_evo_datalogs');

		if( empty($logs)) return true;
		if(!isset($logs[$this->log_type])) return true;

		unset($logs[$this->log_type]);

		update_option('_evo_datalogs', $logs);
	}
}