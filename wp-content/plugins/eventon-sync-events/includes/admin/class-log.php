<?php
/**
 * Sync Report log Object
 */

class EVOSY_Log{
	private $EE;
	public function __construct(){
		//$this->EE = new EVO_Error();
	}

	function record($info){
		$this->record_('evosy',$info);
	}

	// @deprecated after evo 2.6.8
		private function get_log($type){
			$log = get_option('evo_data_log');
			if(!$log) return false;
			if(!isset($log[$type])) return false;
			return $log[$type];
		}
		private function record_($type, $information, $save = true){
			$log = array();		
			if($save) $log = get_option('evo_data_log');
			
			$rand = rand(1000,9999);

			if(!$save){
				$this->error_logs[$type][time().'-'.$rand] = $information;
			}else{
				$log = empty($log)? array(): $log;
				$log[$type][time().'-'.$rand] = $information;
				update_option('evo_data_log', $log);
			}
		}

	function display(){
		

		if( isset($_REQUEST['page']) && $_REQUEST['page']=='evosy'&& isset($_REQUEST['task']) && $_REQUEST['task']=='log'
		){
			
			$log = $this->get_log('evosy');
			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:250px; overflow-y:auto'>";
			
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

	}
}