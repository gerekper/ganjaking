<?php
/**
 * Google Calendar
 * @version 1.1
 */
class evosy_google{
	public function __construct(){
		include_once(__DIR__.'/google/Google/autoload.php');
		$this->client = new Google_Client();
		$this->options = get_option('evcal_options_evosy_1');
	}

	function get_events($calendar_id){

		$options = $this->options;
		$client = $this->client;

		// make sure API and calendar ID is provided
		if(empty($options['evosy_gg_apikey']) ) return false;

		$client->setApplicationName('My Calendar');
		$client->setDeveloperKey($options['evosy_gg_apikey']);

      	$status = 'good';
      	$message = '';

		try{
			$cal = new Google_Service_Calendar($client);
		}
		catch(Google_Service_Exception $e){
			$status = 'bad';
		  	$message = $e->getMessage();
		}
		catch(Exception $e){
			$status = 'bad';
		  	$message = $e->getMessage();
		}

		// did not make a connection
		if( empty($cal) || $status == 'bad'){
			return array(
				'message'=>$message,
				'status'=>'bad'
			);
		}
		

		$params = array(
			'singleEvents'=>true,
			'orderBy'=>'startTime',
			'timeMin'=> date(DateTime::ATOM),// only pull events starting today
			//'maxResults'=>7 // only use this to limit number of events pulled
		);

		$events = $cal->events->listEvents($calendar_id, $params);

		$calTimeZone = $events->timeZone;
		date_default_timezone_set($calTimeZone);

		$calendar_events = $events->getItems();

		if(empty($calendar_events))		return "No events available for fetching from google calendar!";

		// attach event data to this array
		$_fetchedEvent = array();

		// for each event
		// data sources
		// https://developers.google.com/google-apps/calendar/v3/reference/events#resource
		
		foreach($calendar_events as $event){

			//print_r($event);
			$event_id = $event->id;

			// append event data to array
				$_fetchedEvent[$event_id]['id'] = $event_id;
				$_fetchedEvent[$event_id]['name'] = $event->summary;
				
				if(isset($event->description)){
					$_fetchedEvent[$event_id]['description'] = $event->description;
				}else{
					$_fetchedEvent[$event_id]['description'] = '';
				}

				if(isset($event->location))
					$_fetchedEvent[$event_id]['location'] = $event->location;

				$eventDateStr = $event->start->dateTime;
				$_fetchedEvent[$event_id]['start_time'] = (!empty($eventDateStr))?
					$eventDateStr : $event->start->date;

				if(isset($event->end)){
					$_fetchedEvent[$event_id]['end_time'] = (!empty($event->end->dateTime)? $event->end->dateTime: 
						$event->end->date);

					// check if endtime is in yyyy-mm-dd format
					$endtime_len = strlen($_fetchedEvent[$event_id]['end_time']);
					if($endtime_len == 10){
						$new_end_date = date('Y-m-d', strtotime($_fetchedEvent[$event_id]['end_time']. ' -1 day'));

						$_fetchedEvent[$event_id]['end_time'] = $new_end_date;
					}
				}

				$_fetchedEvent[$event_id]['link'] = $event->htmlLink;

				if(isset($event->organizer))
					$_fetchedEvent[$event_id]['organizer'] = $event->organizer->displayName;

		}

		return $_fetchedEvent;
	}

	function create_event(){
		$client = $this->client;
		$options = $this->options;
		$index = '';

		if(empty($options['evosy_gg_apikey']) || empty($options['evosy_gg_calid'.$index])) return false;

		$client->setApplicationName('EventON Sync');
		$client->setAccessType('offline');
		$client->setClientId('686649725767-8uffbg7p41ebhjc8ne35muqe0ssjc8g0.apps.googleusercontent.com');
		$client->setClientSecret('O7Syfj-s2tnvl57jJGxuPati');
		$url = get_admin_url().'admin.php?page=evo-sync&tab=evosy_3';
		$client->setRedirectUri('http://dev2.myeventon.com');
		$client->setDeveloperKey($options['evosy_gg_apikey'.$index]);
		$client->setScopes(array('https://www.googleapis.com/auth/calendar.readonly'));		

		$calendar_id = $options['evosy_gg_calid'];

		if (isset($_GET['logout'])) {
           unset($_SESSION['token']);
        }

        

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            print_r($_SESSION['token']);
            print_r( $client->getAccessToken());

            //header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
            //header('Location: ' . $url);
        }

        if (!isset($_SESSION['token'])) {

			$authUrl = $client->createAuthUrl();
			//echo $authUrl;
			//echo"<script>window.open('".$authUrl."', 'width=710,height=555,left=160,top=170')</script>";

			print "Connect Me!";
	    }

        if (isset($_SESSION['token'])) {
           $client->setAccessToken($_SESSION['token']);

        	$cal = new Google_Service_Calendar($client);
        	$authUrl = $client->createAuthUrl();
        	//header("Location: ".$authUrl);


        	$event = new Event();
          	$event->setSummary("test title");
          	$event->setLocation("test location");
          	$start = new EventDateTime();
          	$start->setDateTime('02-02-2017 09:25:00:000 -05:00');
          	$event->setStart($start);
          	$end = new EventDateTime();
          	$end->setDateTime('02-02-2017 10:25:00:000 -05:00');

          	$createdEvent = $cal->events->insert('primary', $event);

          	echo $createdEvent->getId();
		}

        /*
		$event = new Google_Service_Calendar_Event(array(
		  'summary' => 'Google I/O 2015',
		  'location' => '800 Howard St., San Francisco, CA 94103',
		  'description' => 'A chance to hear more about Google\'s developer products.',
		  'start' => array(
		    'dateTime' => '2017-02-20T09:00:00-07:00',
		    'timeZone' => 'America/Los_Angeles',
		  ),
		  'end' => array(
		    'dateTime' => '2017-02-20T17:00:00-07:00',
		    'timeZone' => 'America/Los_Angeles',
		  ),
		  'recurrence' => array(
		    'RRULE:FREQ=DAILY;COUNT=2'
		  ),
		  'attendees' => array(
		    array('email' => 'lpage@example.com'),
		    array('email' => 'sbrin@example.com'),
		  ),
		  'reminders' => array(
		    'useDefault' => FALSE,
		    'overrides' => array(
		      array('method' => 'email', 'minutes' => 24 * 60),
		      array('method' => 'popup', 'minutes' => 10),
		    ),
		  ),
		));
		*/
	}
}