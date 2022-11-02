<?php
/**
 * Eventon License class
 * @version 4.0.3
 */
class EVO_Product_lic extends EVO_Product{

	public $code;
	public $error_msg;

	public function __construct($slug){
		$this->slug = $slug;
		$this->init = false;
		$this->load();
	}
	
	// check purchase code correct format
		// @version 2.4
		public function purchase_key_format($key){				
			if(!strpos($key, '-'))	return false;

			if( $this->slug == 'eventon'){
				$str = explode('-', $key);

				$status = true;
				$status = $this->is_valid_format($key);

				$status = (strlen($str[1])==4 && strlen($str[2])==4 && strlen($str[3])==4 )? $status: false;

				$w1 = str_split($str[1]);
				//if($w1[0] == $w1[1] && $w1[1] == $w1[2] && $w1[2] == $w1[3]) $status = false;

				$w3 = str_split($str[3]);
				//if($w3[0] == $w3[1] && $w3[1] == $w3[2] && $w3[2] == $w3[3]) $status = false;
				
				return $status;
			}else{
				$str = explode('-', $key);
				return (strlen($str[1])==4 && strlen($str[2])==4 && strlen($str[3])==4 && strpos($str[0], 'EV')!== false)? true: false;
			}				
		}

		// Check for licekse key format is valid
			public function is_valid_format($key){
				$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
				return (bool) preg_match( $pattern, $key );
			}

	// Actions
		// deactivate
			function deactivate(){
				$this->set_prop('status','inactive');
				$this->force_package_update('dettach');	
				return true;			
			}
			function remote_deactivate($__data){
				$output = array();

				if($this->slug == 'eventon') return false;

				$url= $this->api_url_base(). 'request=deactivation&email='.$__data['email'].'&licence_key='.$__data['key'].'&instance=0&product_id='.$__data['product_id'];

				$request = wp_remote_get($url);

				if( is_wp_error($request)){
					$output['error_code'] = 30; return $output;
				}
				if($request['response']['code'] !==200){
					$output['error_code'] = 31; return $output;
				}
				
				$output['result'] = (!empty($request['body']))? json_decode($request['body']): $request; 				
				return $output;
			}

		// save license data only 
			function save_license_data(){
				if(empty($_POST['type'])) return false;

				if( $_POST['type'] == 'main'){
					$data_array = array(
						'envato_username'=>'envato_username',
						'envato_api_key'=>'envato_api_key',
						'key'=>'key',
					);
				}else
				if($_POST['type'] == 'addon'){
					$data_array = array(
						'instance'=>'instance',
						'email'=>'email',
						'key'=>'key',
						'ID'=>'product_id'
					);
				}

				foreach($data_array as $var=>$field){
					if(!isset($_POST[ $field ])) continue;
					$this->set_prop($var , $_POST[ $field ]);
				}

			}

	// ADDON license SUBCRIPTIONS		
		function has_valid_subscription(){
			if( $this->kriyathmakada() ){
				$next_payment = $this->get_prop('next_payment');
				if($next_payment ){
					if( EVO()->calendar->utc_time < $next_payment  ){
						return true;
					}
				}

				// if next payment is past deactivate and check for validation
				$this->deactivate();
				$result = $this->verify_active_subscription();

				if(isset($result['status']) && $result['status'] == 'good') return true;
			}
			
			return false;
		}

		// check from eventon server whether the eventon addon has a valid active subscription
		function verify_active_subscription(){
			$key = $this->get_prop('key');

			if( !$key) return false;
			
			$data = array();
			$data['key'] = $key;
			$data['request'] = 'verify_subscription';

			$output = array(	'error_code'=> 150, 'status'=>'bad'	);

			$url = $this->subscription_api_url($data);
		
			$response = wp_remote_get( $url);
				
			if ( is_wp_error( $response ) ){
				$output['error_code'] = 23; 
				$this->record(23,$data['request'], $result->get_error_message());
				return $output;
			}

			if ( $response['response']['code'] !== 200 ) {
				$output['error_code'] = 21;
				$this->record(21,$data['request']);
				return $output;
			}

			//$json = json_decode( wp_remote_retrieve_body( $response['body'] ) );
			$json = json_decode( $response['body'] ,true );

			if ( empty($json) || !$json || !isset($json['status'])){
				$output['error_code'] = 30; 
				$this->record(30,$data['request']);
				return $output;
			}

			if (  $json['status'] == 'bad' ){
				$output['error_remote_var'] = $json['error_var'];				
				$output['error_code'] = $json['code']; 
				$this->record($json['code'],$data['request']);
				return $output;
			}

			if(  $json['status'] == 'inactive' ){
				$output['error_code'] = 151; 
				$this->record(151,$data['request'], (isset($json['error_var'])? $json['error_var']:''));
				if(isset($json['error_var'])) $output['error_remote_var'] = $json['error_var'];	
				return $output;
			}

			if(  $json['status'] == 'active' ){
				$output['status'] = 'good';
				$this->record(155,$data['request'], EVO_Error()->error_code( 155 ));

				if( isset($json['next_payment'])){
					$this->set_prop('next_payment',$json['next_payment'] );
				}
				$this->set_prop('remote_validity','valid' );
				$this->evo_kriyathmaka_karanna();
			}	

			// output include
			// status, error_code, error_remote_var
			return $output;
		}

		function remote_deactivate_subscription(){
			$output = array();

			$data = array();
			$data['key'] = $this->get_prop('key');
			$data['request'] = 'deactivate_subscription';

			$output = array();
			$output['error_code'] = 150;
			$output['status'] = 'bad';

			$url = $this->subscription_api_url($data);
			$output['url'] = $url;

			$request = wp_remote_get($url);

			if( is_wp_error($request)){
				$output['error_code'] = 161; return $output;
			}
			if($request['response']['code'] !==200){
				$output['error_code'] = 161; return $output;
			}
			
			$json = json_decode( $request['body'] ,true );

			if ( empty($json) || !$json || !isset($json['status'])){
				$output['error_code'] = 161; 
				$this->record(161,$data['request']);
				return $output;
			}

			if (  $json['status'] == 'bad' ){
				$output['error_code'] = 161; 
				$this->record($json['code'],$data['request']);
				return $output;
			}

			if(  $json['status'] == 'inactive' || $json['status'] == 'good' ){
				$output['status'] = 'good';
				$output['error_code'] = 162; 
				$this->record(162,$data['request'], EVO_Error()->error_code( 162 ));
			}	

			return $output;
		}

		function subscription_api_url($data){
			$instance = base64_encode(get_site_url());				
			return $this->subscription_api_url_base() . 'request=' . $data['request'] .'&key='. $data['key'] .'&instance=' . $instance ;
		}
		function subscription_api_url_base(){
			//return 'http://localhost/EVO/wp-json/ajde/evo-subscription?';
			return 'http://www.myeventon.com/wp-json/ajde/evo-subscription?';
		}
		private function record($code, $task,$data=''){
			EVO_Error()->record_gen_log($task, $this->slug, $code, $data );
		}

	// Returns		
		// check if the license key was validated remotely
		public function remotely_validated(){			
			return ($this->get_prop('remote_validity') && $this->get_prop('remote_validity') == 'valid' && $this->get_prop('status') && $this->get_prop('status') =='active')?
				true: false;
		}
		public function get_license(){
			return $this->get_prop('key')? $this->get_prop('key'): false;
		}
		public function get_partial_license(){				
			$key = $this->get_license();
			if(!empty($key )){
				if($this->slug=='eventon'){
					$valid_key = $this->purchase_key_format($key);
					if($valid_key){
						$parts = explode('-', $key);
						return 'xxxxxxxx-xxxx-xxxx-xxxx-'.$parts[4];
					}else{
						$this->deactivate($slug);
						return 'n/a';
					}
				}else{
					// for addons
					return 'xxxxxxxx-xxxx-xxxx-xxxx-';
				}
			}else{return '--';}
		}
	
		// for AJAX
		// return api url 
			protected function get_api_url($args){
				$url = '';
				if($this->slug=='eventon'){	
					$code = trim( $args['key'] );
					$url = 'https://api.envato.com/v3/market/author/sale?code='. $code;				
					//$url = 'http://marketplace.envato.com/api/edge/'.$api_username.'/'.$api_key.'/verify-purchase:'.$args['key'].'.json';				
				}else{
					$instance = base64_encode(get_site_url());					
					$url= $this->api_url_base(). 'request=activation&email='.$args['email'].'&licence_key='.$args['key'].'&product_id='.$args['product_id'].'&instance='.$instance;
				}
				return $url;
			}

			function api_url_base(){
				return 'http://www.myeventon.com/woocommerce/?wc-api=software-api&';
				//return 'http://localhost/EVO/woocommerce/?wc-api=software-api&';
			}

		// remote validation 
			function remote_validation($args){				

				if(!isset($args['key'])) return false;

				$url = $this->get_api_url($args);

				$key = $args['key'];

				$output = array();
				$output['error_code'] = 11;
				$output['status'] = 'bad';
				$output['api_url'] = $url;

				if($this->slug == 'eventon'){
					$personalToken = 'gYLcQMz1UBTbY0ZfyoeAYzKGD6CGgnjP';
					$response = wp_remote_get( $url,
						array(
							'headers'=> array(
								'Authorization' => "Bearer ".$personalToken,
            					'User-Agent' => "Purchase code verification on ".get_site_url()
							),
							'timeout' => 20,
							'headers_data' => false,
						)
					);
				}else{ // addons
					$response = wp_remote_post( $url);
				}
				
				
				if ( is_wp_error( $response ) ){
					$output['error_code'] = 23; 
					$this->record(23,'remote_validation_failed', 'URL:'.$url);
					return $output;
				}

				if ( $response['response']['code'] !== 200 ) {
					$output['error_code'] = 21;
					$this->record(21,'remote_validation_failed', 'URL:'.$url);
					return $output;
				}

				$json = json_decode( $response['body'], true );
				
				//print_r($json);

				
				// for eventon main plugin
				if($this->slug == 'eventon'){	
					if ( ! $json || ! isset( $json['item'] ) ) {
						$output['error_code'] = 03; 
						return $output;
					}

					if( empty($json['item']) || !is_array($json['item'])){
						$output['error_code'] = 02; 
						return $output;
					}
					
					if( !empty($json['item']['id']) && $json['item']['id'] !='1211017'){
						$output['error_code'] = 22; 
						return $output;
					}

					// update other values
					if(!empty($json['buyer'])){ 
						$this->set_prop( 'buyer', $json['buyer']);
						$output['status'] = 'good';
					}

					$this->set_prop('remote_validity','valid' );

				}else{ 
				// for addons
					if ( !$json ){	
						$output['error_code'] = 30; return $output; 
					}
					
					if ( !empty( $json['error'] ) ){
						$output['error_remote_msg'] = $json['error'];
						if( isset( $json['additional info'])) $output['error_additional_info'] = $json['additional info'];
						$output['error_code'] = isset( $json['code'])?  $json['code']: 11; 
						$this->record($output['error_code'],'remote_validation_error', 'URL:'.$url);
						return $output;
					}

					// remote validated
					if( isset($json['activated']) && $json['activated'] == true){
						$this->set_prop('remote_validity','valid' );
						$this->set_prop('key',$key );
						$output['status'] = 'good';
						$this->dl_updater();
					}	
				}

				// output include
				// status, error_remote_msg, error_code, api_url
				return $output;
			}

		// auto validation checker
			function check_validity(){
				$rv = $this->get_prop('remote_validity');

				if($rv == 'valid') return true;
				if($rv == 'local'){

					if(!$this->get_prop('key')) return false;

					$args = array(
						'key'=> $this->get_prop('key'),
						'email'	=> $this->get_prop('email'),
						'product_id'	=> $this->get_prop('ID'),
						'instance'	=> $this->get_prop('instance'),
					);

					$remote_validate = $this->remote_validation($args);
				}
			}

		// download link updater
			function dl_updater(){
				if($this->has_update()){
					$PRODS = new evo_prods();
					$PRODS->get_remote_prods_data();
				}
			}

		// kriyathmaka karanna
			public function evo_kriyathmaka_karanna(){
				$this->set_prop('status', 'active');
				$this->force_package_update('append');	
			}
			// site eka ethulen withrak kriyathmana karanna
			public function evo_kriyathmaka_karanna_athulen(){
				$this->set_prop('status', 'active');
				$this->set_prop('remote_validity', 'local');
			}
	
}