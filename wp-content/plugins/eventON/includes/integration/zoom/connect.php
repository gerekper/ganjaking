<?php
/**
 * Zoom Connect functions
 * @version 4.4.5
 */

require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

class EVO_Zoom_Run{

	private $api_url = 'https://api.zoom.us/v2/';

	public static $OAuth_revalidate_attempts = 0;

	public $zoom_api_key;
	public $zoom_api_secret;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

		$this->zoom_api_key = EVO()->cal->get_prop('_evo_zoom_key','evcal_1');
		$this->zoom_api_secret = EVO()->cal->get_prop('_evo_zoom_secret','evcal_1');
	}

	protected function sendRequest($calledFunction, $data, $request = "GET" ) {
        //$request_url = 'https://api.zoom.us/v2/users/me/meetings';
        $initialRequest = $request;
        $request_url = $this->api_url . $calledFunction;
        $bearerToken    = $this->getBearerToken();

        $args        = array(
        	'timeout' => 30,
			'headers' => array(
				'Authorization' => 'Bearer ' . $bearerToken,
				'Content-Type'  => 'application/json'
			)
		);

        // retrive validation
	        if ( $request == "GET" ) {
				$args['body'] = ! empty( $data ) ? $data : array();
				$request      = wp_remote_get( $request_url, $args );
			} elseif ( $request == "DELETE" ) {
				$args['body']   = ! empty( $data ) ? json_encode( $data ) : array();
				$args['method'] = "DELETE";
				$request        = wp_remote_request( $request_url, $args );
			} elseif ( $request == "PATCH" ) {
				$args['body']   = ! empty( $data ) ? json_encode( $data ) : array();
				$args['method'] = "PATCH";
				$request        = wp_remote_request( $request_url, $args );
			} elseif ( $request == "PUT" ) {
				$args['body']   = ! empty( $data ) ? json_encode( $data ) : array();
				$args['method'] = "PUT";
				$request        = wp_remote_request( $request_url, $args );
			} else {
				$args['body']   = ! empty( $data ) ? json_encode( $data ) : array();
				$args['method'] = "POST";
				$request        = wp_remote_post( $request_url, $args );
			}

		if ( !is_wp_error( $request ) ) {
			$responseCode = wp_remote_retrieve_response_code( $request );
			$response = wp_remote_retrieve_body( $request );

			if( $responseCode == 401 && $this->is_oauth_active()){

				// retry access token only twice
				if ( self::$OAuth_revalidate_attempts <= 2 ) {

					\evozoomoauth\ZOOM_S2SOAuth::get_instance()->run_access_token_process();
					self::$OAuth_revalidate_attempts ++;

					//resend the request after regenerating access token
						return $this->sendRequest( $calledFunction, $data, $initialRequest );

				}else{
					self::$OAuth_revalidate_attempts = 0;
						
				}

			}

			return json_decode($response);
		}else{
			return false;
		}	
	}

	// S2S access
		private function getBearerToken() {			
			$OauthData = EVO()->cal->get_prop('_evo_zoom_oauth_data','evcal_1');
			
			if ( ! empty( $OauthData ) ) {
				return $OauthData->access_token;
			} else {
				return $this->generateJWTKey();
			}

		}

	//function to generate JWT
	    private function generateJWTKey() {
	        $key = $this->zoom_api_key;
	        $secret = $this->zoom_api_secret;

	        if ( empty( $secret ) ) {	return false; }
	        
	        $token = array(
	            "iss" => $key,
	            "exp" => time() + 3600 //60 seconds as suggested
	        );
	        return JWT::encode( $token, $secret, 'HS256' );
	    }
	
	// Users
	    public function listUsers( $page = 1 ) {
			$listUsersArray                = array();
			$listUsersArray['page_size']   = 300;
			$listUsersArray['page_number'] = absint( $page );
			$listUsersArray                = $listUsersArray;

			return $this->sendRequest( 'users', $listUsersArray, "GET" );
		}

	// Meetings
		public function create_meeting( $data = array() ) {
	        $start_unix = $data['start_unix'];
			$start_time = gmdate( "Y-m-d\TH:i:s", $start_unix );
	        
	        $createAMeetingArray = array();
	        	       
	        $createAMeetingArray['topic']      = $data['meetingTopic'];
	        $createAMeetingArray['agenda']     = ! empty( $data['agenda'] ) ? $data['agenda'] : "";
	        $createAMeetingArray['type']       = ! empty( $data['type'] ) ? $data['type'] : 2; //Scheduled
	        $createAMeetingArray['start_time'] = $start_time;
	        $createAMeetingArray['timezone']   = $data['timezone'];
	        $createAMeetingArray['password']   = ! empty( $data['password'] ) ? $data['password'] : "";
	        $createAMeetingArray['duration']   = ! empty( $data['duration'] ) ? $data['duration'] : 60;
	        $createAMeetingArray['settings']   = $this->get_settings_array( $data );

	        if ( ! empty( $createAMeetingArray ) ) {
				return $this->sendRequest( 'users/' . $data['userId'] . '/meetings', $createAMeetingArray, "POST" );
			} else {
				return;
			}
	    }

	    public function update_meeting( $update_data = array() ) {
			$start_unix = $update_data['start_unix'];
			$start_time = gmdate( "Y-m-d\TH:i:s", $start_unix );

			$updateMeetingInfoArray = array();

			$updateMeetingInfoArray['topic']      = $update_data['meetingTopic'];
			$updateMeetingInfoArray['agenda']     = ! empty( $update_data['agenda'] ) ? $update_data['agenda'] : "";
			$updateMeetingInfoArray['type']       = ! empty( $update_data['type'] ) ? $update_data['type'] : 2; //Scheduled
			$updateMeetingInfoArray['start_time'] = $start_time;
			$updateMeetingInfoArray['timezone']   = $update_data['timezone'];
			$updateMeetingInfoArray['password']   = ! empty( $update_data['password'] ) ? $update_data['password'] : "";
			$updateMeetingInfoArray['duration']   = ! empty( $update_data['duration'] ) ? $update_data['duration'] : 60;
			$updateMeetingInfoArray['settings']   = $this->get_settings_array( $update_data );

			if ( ! empty( $updateMeetingInfoArray ) ) {
				return $this->sendRequest( 'meetings/' . $update_data['meeting_id'], $updateMeetingInfoArray, "PATCH" );

			} else {
				return;
			}
		}

		public function getUserInfo( $user_id ) {
			$getUserInfoArray = array();
			$getUserInfoArray = apply_filters( 'evo_zoom_getUserInfo', $getUserInfoArray );

			return $this->sendRequest( 'users/' . $user_id, $getUserInfoArray );
		}

		function get_settings_array( $data){
			if ( ! empty( $data['alternative_host_ids'] ) ) {
				if ( count( $data['alternative_host_ids'] ) > 1 ) {
					$alternative_host_ids = implode( ",", $data['alternative_host_ids'] );
				} else {
					$alternative_host_ids = $data['alternative_host_ids'][0];
				}
			}

			return array(
				'meeting_authentication'  => (!empty( $data['_evoz_mtg_auth'] ) && $data['_evoz_mtg_auth']=='yes' ) 	? true : false,
				'join_before_host'  => (!empty( $data['_evoz_jbh'] ) && $data['_evoz_jbh']=='yes' ) 	? true : false,
				'host_video'        => (!empty( $data['_evoz_hv'] ) && $data['_evoz_hv']=='yes' )	? true : false,
				'waiting_room'      => (!empty( $data['_evoz_ewr'] ) && $data['_evoz_ewr']=='yes' ) 	? true : false,
				'participant_video' => (!empty( $data['_evoz_pv'] ) && $data['_evoz_pv']=='yes' ) 	? true : false,
				'mute_upon_entry'   => (!empty( $data['_evoz_mpoj'] ) && $data['_evoz_mpoj']=='yes' )	? true : false,
				'enforce_login'     => !empty( $data['option_enforce_login'] ) ? true : false,
				'auto_recording'    => !empty( $data['_evoz_arec'] ) ? $data['_evoz_arec'] : "none",
				'alternative_hosts' => isset( $alternative_host_ids ) ? $alternative_host_ids : ""
			);
		}

	  	public function listMeetings( $host_id ) {
			$listMeetingsArray              = array();
			$listMeetingsArray['page_size'] = 300;
			$listMeetingsArray              = $listMeetingsArray;
			return $this->sendRequest( 'users/' . $host_id . '/meetings', $listMeetingsArray, "GET" );
		}

		public function delete_meeting( $meeting_id ) {
			$deleteAMeetingArray = array();
			return $this->sendRequest( 'meetings/' . $meeting_id, $deleteAMeetingArray, "DELETE" );
		}

		function is_oauth_active(){
			return ( !EVO()->cal->get_prop('_evo_zoom_oauth_id','evcal_1') && !EVO()->cal->get_prop('_evo_zoom_oauth_cid','evcal_1') && !EVO()->cal->get_prop('_evo_zoom_oauth_csecret','evcal_1') ) ? false : true;
		}
}

//$ZM = new EVO_Zoom_Run();


$data = array(
	'meetingTopic'=>'Testing 001',
	'userId'=>'me',
	'timezone'=>'UTC',
	'start_date'=> date('Y-m-d h:i:s', strtotime('tomorrow'))
);
//$ZM->listUsers( );



