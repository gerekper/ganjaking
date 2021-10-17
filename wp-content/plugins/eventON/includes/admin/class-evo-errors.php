<?php
/** 
 * EVO Error handling and recording log of activities
 * @version 2.6.1
 */

class EVO_Error{

	protected static $_instance = null;
	public $error_logs = array();

	// setup one instance of eventon
	public static function instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function record($type, $information, $save = true){
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
	public function save(){
		$log = get_option('evo_data_log');
		$log = empty($log)? $this->error_logs: array_merge_recursive($log,$this->error_logs);

		update_option('evo_data_log', $log);
	}


	public function record_gen_log( $task, $slug, $error_code='', $data='', $store= true){
		$this->record(
			'general', 
			$task.': ('. $slug .')'.
			( !empty($data)? ' - '.$data:'' ).
			( !empty($error_code) ? ' - CODE-->'.$error_code: ''),
			$store
		);
	}
	public function record_activation_loc($error_code){
		$slug = isset($_POST['slug'])? $_POST['slug']:false;
		$key = isset($_POST['key'])? $_POST['key']:false;
		$information = 'Locally activated: ('. $slug .') - KEY:'.$key.' - CODE-->'.$error_code;
		$this->record('general', $information);
	}
	public function record_activation_rem(){
		$slug = isset($_POST['slug'])? $_POST['slug']:false;
		$key = isset($_POST['key'])? $_POST['key']:false;
		$this->record('general', 'Remotely activated: ('. $slug .') - KEY:'.$key.' - Validation status return good.');
	}
	public function record_deactivation_loc($slug){
		$key = isset($_POST['key'])? $_POST['key']:false;
		$this->record('general', 'Locally deactivated: ('. $slug .') - KEY:'.$key);
	}
	public function record_deactivation_rem(){
		$slug = isset($_POST['slug'])? $_POST['slug']:false;
		$key = isset($_POST['key'])? $_POST['key']:false;
		$this->record('general', 'Remotely deactivated: ('. $slug .') - KEY:'.$key);
	}
	public function record_deactivation_fail($error_code){
		$slug = isset($_POST['slug'])? $_POST['slug']:false;
		$key = isset($_POST['key'])? $_POST['key']:false;
		$this->record('general', 'Remote deactivation failed: ('. $slug .') - KEY:'.$key.' - CODE-->'.$error_code);
	}
	public function get_log($type){
		$log = get_option('evo_data_log');

		if(!$log) return false;

		if(!isset($log[$type])) return false;

		return $log[$type];
	}

	public function display_log(){
		if(isset($_REQUEST['page']) && $_REQUEST['page']=='eventon' && isset($_REQUEST['tab']) && 
			$_REQUEST['tab']=='evcal_4' 
			&& isset($_REQUEST['task']) && $_REQUEST['task']=='log'
		){
			
			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:500px; overflow-y:auto;background-color: #5f5f5f;color: #fff;'>";
			$log = $this->get_log('general');

			if($log){
				$log = array_filter($log);

				if(sizeof($log)==0){ 
					echo 'No Log!'; 
				}else{
					echo "<b>Data Log</b><br/><i>This data is locally stored in your website wp_optinos table and is not shared with anyone.</i><br/>";

					echo "<br/><br/>";
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
			$_REQUEST['tab']=='evcal_4' 
			&& isset($_REQUEST['task']) && $_REQUEST['task']=='flushlogs'
		){
			$this->trash_all_logs();

			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:250px; overflow-y:auto'>";
			echo "All detail logs has been trashed!";
			echo "</div>";
		}
	}

	private function trash_all_logs(){
		delete_option('evo_data_log');
		$this->error_logs = '';
	}

	// error code decipher
		public function error_code($code=''){

			$code = empty($code)? 00: $code;
			
			$array = array(
				"00"=>'',
				'01'=>"No data returned from envato API",
				"02"=>'Your license could not be verified, please check your license key and try again.',
				"03"=>'Could not connect to envato API, please try later.',
				"04"=>'This license is already registered with a different site.',
				"05"=>'Your EventON version is older than 2.2.17.',
				"06"=>'Eventon license key not passed correct!',
				"07"=>'Could not deactivate eventON license from remote server',
				'08'=>'http request failed, connection time out. Please contact your web provider!',
				'09'=>'wp_remote_post() method did not work to verify licenses, trying a backup method now..',

				'10'=>'License key is not valid, please try again.',
				'11'=>'Could not verify license. Server not responding, please try again LATER!',
				'12'=>'Activated successfully and synced w/ eventon server!',
				'13'=>'Remote validation did not work, but we have activated the software within your site!',
				'14'=>'Required Information Missing',
				'15'=>'Revalidation Failed! Your license is not valid, please deactivate addon and re-enter correct license information.',
				'16'=>'Your license has been successfully activated remotely!',
				'17'=>'Your have a valid license!',

				'20'=>'Please try again later!',				
				'21'=>'Remote Server did not respond with OK',
				'22'=>'Purchase key is for a wrong software.',
				'23'=>'Could not establish connection with remote server or this server does not support wp_remote_post()',

				'30'=>'EventON API encountered difficulty connecting, try again later.',
				'31'=>'EventON API did not respond, try again later.',
				'32'=>'License sucessfully deactivated from the server. You may use the license on another website now.',
				'33'=>'Successfully deactivated your license from remote server',

				'100'=>'The email provided is invalid',
				'101'=>'Invalid license key or other information!',
				'102'=>'The purchase matching this addon is not complete yet',
				'103'=>'You have exceeded maxium number of activations! You can purchase additional licenses or deactivate license on other websites.',
				'103r'=>'You have exceeded maxium number of activations! You can purchase additional licenses or deactivate license on other websites. This license has been deactivated.',
				'104'=>'Could not activate the key',
				'105'=>'Invalid security key!',
				'106'=>'Invalid request!',

				'120'=>'Could not reach remote server for validation, however your license is activated locally. You can attempt to remote validate your license later',
				'121'=>'Could not reach remote server still, however your license is still activated locally and you can continue to use the addon.',
				
				'150'=>'We encountered trouble connecting to subscriptions server, please try later',
				'151'=>'You do not have a valid subscription!',
				'152'=>'Some of the required information was missing, please try later!',
				'154'=>'We could not find your account at myeventon, please contact us for further assistance!',
				'155'=>'Your subscription has been validated successfully!',
				'156'=>'You have a valid subscription!',
				
				'160'=>'Required fields missing for deactivation, however locally deactivated.',
				'161'=>'We could not deactivate from remote server. however subscription is deactivated locally.',
				'162'=>'Successfully deactivated subscription for this site from our server.',
			);
			return $array[$code];
		}
}


// initiation
if(!function_exists('EVO_Error')){
	function EVO_Error(){ return EVO_Error::instance();}
}