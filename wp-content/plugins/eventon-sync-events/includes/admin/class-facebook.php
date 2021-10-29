<?php
/**
 * facebook events class
 * @version 1.1
 */


// SDK v 5
//require __DIR__ . '/Facebook/php-graph-sdk-5.x/src/Facebook/autoload.php';




// SDK v 4.4
require __DIR__ . '/Facebook/facebook-php-sdk-v4-4.0.23/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;

class evosy_facebook{
	public function __construct(){

	}

	function __get_fb_events($api_id, $api_secret, $fb_pages_ar, $facebook_session=0){
		$fb = new \Facebook\Facebook([
			'app_id' => $api_id,
		  	'app_secret' => $api_secret,
		  	'default_graph_version' => 'v2.10',
		]);

		try {
		  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
		  // If you provided a 'default_access_token', the '{access-token}' is optional.
		  $response = $fb->get('/me', '{access-token}');
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		$me = $response->getGraphUser();
		echo 'Logged in as ' . $me->getName();

	}

	function get_fb_events($api_id, $api_secret, $fb_pages_ar, $facebook_session=0){
		if (empty($api_id) || empty($api_secret) || empty($fb_pages_ar))
	      	return false;

	    // initialize
	   	FacebookSession::setDefaultApplication($api_id,$api_secret);

	   	//if (!$facebook_session)  	$facebook_session = FacebookSession::newAppSession();

	    $facebook_session = new FacebookSession($api_id.'|'.$api_secret);

	    try {
		  	$facebook_session->validate();
		  	//echo '<div class="updated" style="color:#222222; font-weight:700; font-size:1em; padding:10px">Settings Saved</div>';	

		} catch (FacebookRequestException $ex) {
		  	// Session not valid, Graph API returned an exception with the reason.
		  	return $ex->getMessage();
		} catch (\Exception $ex) {
		  	// Graph API returned info, but it may mismatch the current app or have expired.
		  	return $ex->getMessage();
		}

	    //print_r($facebook_session);

	   	$ret = array();

	   	foreach ($fb_pages_ar as $key => $value) {
	      	if ($value!='') {

	      		//$events_sec = new FacebookRequest( $facebook_session, 'GET', '/'.$value.'/events');

	      		try {
	      			$fb_request = (new FacebookRequest( $facebook_session, 
	      				'GET', 
	      				'/'.$value.'/events?fields=id,end_time,cover,description,name,start_time&since='
                		. strtotime('now') . '&until=' . strtotime('+3 year')
                		. '&limit=100'
	      			)) ->execute();
	      			$events = $fb_request->getGraphObjectList();
	      			//print_r($fb_request);
	      		}

	      		catch(Facebook\Exceptions\FacebookResponseException $e) {
	      			return $ex->getMessage();
	      		}
	      		catch (FacebookRequestException $ex){
	      			return $ex->getMessage();
	      		} catch (\Exception $ex) {
				  	return $ex->getMessage();
				}


				$count = 1;
	         	foreach ($events as $graphobject) {

		            $event_id = $graphobject->getProperty('id');

		            $event = $this->get_fb_event($api_id, $api_secret, $event_id, $facebook_session);

		            //print_r($event);
		            // if event info returned
		            if($event && is_array($event) && !empty($event[$event_id]) && sizeof($event[$event_id])>0){
		            	$ret = array_merge($ret, $event);
		            }    

		            $count++; 
		           	
	         	}
	      	}
	   	}
	   	return $ret;
	}
	function get_fb_event($fb_app_id, $fb_secret, $event_id, $facebook_session=0){
		
		// check for required information provided
		if (empty($fb_app_id) || empty($fb_secret) || empty($event_id))
      		return false;

      	$status = 'good';
      	$message = '';

	    // accept both event id and https://www.facebook.com/events/<event_id>
	   		$event_id=preg_replace('/^.*facebook.com\/events\//','',$event_id);
	   		$event_id=preg_replace('/\/.*$/','',$event_id);

	   	$fields = array("fields"=>"id,name,place,start_time,end_time,event_times,timezone,description,cover,ticket_uri,type,category,owner");
	   			   	
	   	FacebookSession::setDefaultApplication($fb_app_id,$fb_secret);

	   	if (!$facebook_session)   	$facebook_session = FacebookSession::newAppSession();
	   	
	   	$__event_bok = '/'.$event_id;
	   	
	   	// Request individual facebook data
	   	try{
	   		$event_sec = (new FacebookRequest( $facebook_session, 'GET', $__event_bok, $fields) )->execute();
	   	}catch (FacebookRequestException $ex) {
		  	//echo $ex->getMessage();
		  	$status = 'bad';
		  	$message = $ex->getMessage();
		} catch (\Exception $ex) {
			$status = 'bad';
		  	$message = $ex->getMessage();
		}
		
		// did not make a connection
		if( empty($event_sec) || $status == 'bad'){
			return array(
				'message'=>$message,
				'status'=>'bad'
			);
		}

	   	$event = $event_sec->getGraphObject()->asArray();

	   	//print_r($event);

	   	// image for the fetched event
		   	if (isset($event['cover']) && !empty($event['cover'])) {
		      	$event['event_picture_url']=$event['cover']->source;
		   	} else {
		   		$picture_sec = new FacebookRequest( $facebook_session, 'GET', '/'.$event_id.'/picture', array ('redirect' => false,'type' => 'normal'));
		      	$picture = $picture_sec->execute()->getGraphObject()->asArray();
		      	if (!empty($picture) && is_object($picture) && $picture->url) {
		         	$event['event_picture_url']=$picture->url;
		      	}
		   	}

		// Get Location data for the fetched event
			if(isset($event['place']) && !empty($event['place']) ){
				$event['location'] = property_exists($event['place'],'name')?
					$event['place']->name:'';

				if(property_exists($event['place'],'location')){
					$location = $event['place']->location;
					$address = property_exists($location,'street')? $location->street.' ':'';
					$address .= property_exists($location,'city')? $location->city.' ':'';
					$address .= property_exists($location,'state')? $location->state.' ':'';
					$address .= property_exists($location,'zip')? $location->zip.' ':'';

					$event['location_address'] = !empty($address)? $address:'';

					$event['location_lat'] = property_exists($location,'latitude')?
						$location->latitude:'';
					$event['location_lon'] = property_exists($location,'longitude')?
						$location->longitude:'';
				}

				unset($event['place']);				
			}

		// organizer
			if(isset($event['owner']) && !empty($event['owner']) ){
				$event['organizer'] = property_exists($event['owner'],'name')?
					$event['owner']->name:'';
			}

		// if there is no end time send
		if( !isset($event['end_time']) || empty($event['end_time'])){
			return __('Event End time was not given','evosy');
		}

	   	$STRLEN = strlen($event['end_time']);
	   	$DATEFORMAT = ($STRLEN>10)? 'Y-m-d\TH:i:sO': 'Y-m-d';

	   	$myDateTime = DateTime::createFromFormat($DATEFORMAT, $event['end_time']);

	   	// validate date time object
	   	if( is_bool($myDateTime)) return __('Bad event end time.','evosy');

	   	$eventEnds = $myDateTime->format('U');

	   	// if the event has repeat events correct initial event times
			if( isset($event['event_times'])){
				$last_repeat = end($event['event_times']);
				$event['end_time'] = $last_repeat->end_time;
			}

	   	$event['link'] = 'https://www.facebook.com/events/'.$event_id;

	   	// filter to check if the event time range is not in the past
	   	if($eventEnds > time()){
	   		$event['status'] = $status;
	      	return array($event_id=>$event);
	   	}else{
	   		return __('This is a past event','eventon');
      	}
	}


}