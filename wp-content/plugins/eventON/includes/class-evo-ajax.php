<?php
/**
 * EventON Ajax Handlers
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Functions/AJAX
 * @version     4.1
 */

class evo_ajax{
	/**
	 * Hook into ajax events
	 */

	private $helper;

	public function __construct(){
		$ajax_events = array(
			'init_load'=>'init_load',			
			'ajax_events_data'=>'ajax_events_data',			
			'the_ajax_hook'=>'main_ajax_call',			
			'load_event_content'=>'load_event_content',
			'load_single_eventcard_content'=>'load_single_eventcard_content',
			'ics_download'=>'eventon_ics_download',			
			'export_events_ics'=>'export_events_ics',
			'search_evo_events'=>'search_evo_events',
			'get_local_event_time'=>'get_local_event_time',
			'refresh_now_cal'=>'refresh_now_cal',
			'record_mod_joined'=>'record_mod_joined',
			'refresh_elm'=>'refresh_elm',
			'gen_trig_ajax'=>'gen_trig_ajax',

		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			$prepend = ( in_array($ajax_event, array('the_ajax_hook','evo_dynamic_css','the_post_ajax_hook_3','the_post_ajax_hook_2')) )? '': 'eventon_';
			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		$this->helper = new evo_helper();
	}	


	// Initial load
		function callback($name, $nonce){
			$name = str_replace('eventon_', '', $name);
			if(!wp_verify_nonce( $nonce, 'rest_'.EVO()->version)) return false;
			return call_user_func_array(array($this, $name),array(true) );
		}
		function init_load($return = false){
			// get global cals on init
			global $EVOAJAX;	

			// init load calendar events
			$CALS = array();
			if(isset($_POST['cals']) && is_array($_POST['cals'])){
				foreach($_POST['cals'] as $calid=>$CD){
					if(!isset( $CD['sc'])) continue;

					$SC = $this->helper->sanitize_array( $CD['sc'] );

					$CALS[$calid]['sc'] = $SC;

					EVO()->calendar->process_arguments( $SC );

					// get events for the calendar
					$E = EVO()->calendar->_generate_events(
						'both',	apply_filters('evo_init_ajax_wparg_additions',array())
					);

					//print_r($$CD);
					$CALS[$calid]['json'] = $E['data'];
					$CALS[$calid]['html'] = $E['html'];
					//$CALS[$calid]['debug'] = date('Y-m-d H:i', $CD['sc']['focus_start_date_range']).'-'.date('Y-m-d H:i', $CD['sc']['focus_end_date_range']);
				}
			}

			$global = isset($_POST['global'])? 
				$this->helper->sanitize_array( $_POST['global'] ): array();

			$R =  apply_filters('evo_init_ajax_data', array(
				'cal_def'=> EVO()->calendar->helper->get_calendar_defaults(),
				'temp'=> EVO()->temp->get_init(),
				'dms' => array(
					'd'=> EVO()->cal->get_all_day_names(),
					'd3'=> EVO()->cal->get_all_day_names('three'),
					'd1'=> EVO()->cal->get_all_day_names('one'),
					'm'=> EVO()->cal->_get_all_month_names(),
					'm3'=> EVO()->cal->_get_all_month_names('short'),
				),		
				'cals'=> apply_filters('evo_init_ajax_cals',$CALS),		
				'txt'=> array(
					'no_events'=> evo_lang_get('evcal_lang_noeve','No Events'),
					'all_day'=> evo_lang_get('evcal_lang_allday','All Day'),
					'event_completed'=> evo_lang('Event Completed'),
				), // language translated texts for client side
				'terms'=> array(),
			), $global);

			if($return){ return $R; }else{ echo json_encode($R);	}
			exit;

		}

	// General ajax call - added 3.1
		public function gen_trig_ajax(){

			$PP = $this->helper->sanitize_array( $_POST );

			if(!wp_verify_nonce($PP['nn'], 'eventon_nonce')) {echo 'Evo Nonce Failed!'; exit;}
		
			echo json_encode(
				apply_filters('evo_ajax_general_send_results', array('status'=>'good'), $PP)
			);exit;			
		}

	// Primary function to load event data 
		function main_ajax_call(){
			$shortcode_args = $focused_month_num = $focused_year = '';
			$status = 'GOOD';

			$SC = isset($_POST['shortcode']) ? $this->helper->sanitize_array( $_POST['shortcode'] ): array();
			$ajaxtype = isset($_POST['ajaxtype'])? sanitize_text_field( $_POST['ajaxtype'] ): '';

			extract($SC);

			if(empty($number_of_months)){ return false; exit; }
			if($number_of_months < 1){ return false; exit; }

			EVO()->calendar->shortcode_args = $SC;
							
			// date range calculation
				if( isset($SC['focus_start_date_range']) && isset($SC['focus_end_date_range']) ){
					$focus_start_date_range = (int)$SC['focus_start_date_range'];
					$focus_end_date_range = (int)$SC['focus_end_date_range'];
				}

				$calendar_type = 'default';
				// event list with more than one month
				if( $SC['number_of_months']==1){

					// calculate new date range if calendar direction is changing
					if($_POST['direction'] !='none'){
						if(!empty($fixed_month) && !empty($fixed_year)){
							$fixed_year = ($_POST['direction']=='next')? 
								(($fixed_month==12)? $fixed_year+1:$fixed_year):
								(($fixed_month==1)? $fixed_year-1:$fixed_year);

							$fixed_month = ($_POST['direction']=='next')?
								(($fixed_month==12)? 1:$fixed_month+1):
								(($fixed_month==1)? 12:$fixed_month-1);													

							$DD = new DateTime();
							$DD->setTimezone( EVO()->calendar->timezone0 );
							$DD->setDate($fixed_year,$fixed_month,1 );
							$DD->setTime(0,0,0);

							$SC['fixed_month'] = $fixed_month;
							$SC['fixed_year'] = $fixed_year;
							$SC['focus_start_date_range'] = $DD->format('U');
							$DD->modify('last day of this month');
							$DD->setTime(23,59,59);
							$SC['focus_end_date_range'] = $DD->format('U');							
						}
					}else{ // not switching months

						// Going to today
						if($ajaxtype == 'today' || $ajaxtype == 'jumper'){
							$SC['focus_start_date_range']='';
							$SC['focus_end_date_range']='';	
							EVO()->calendar->shortcode_args = $SC;						
							EVO()->calendar->shell->set_calendar_range();	
							$SC = EVO()->calendar->shortcode_args;
						}
					}

				}else{	$calendar_type = 'list';	}
														

			// set calendar shortcode values
				$SC = apply_filters('eventon_ajax_arguments',$SC, $_POST, $ajaxtype);		
				ksort($SC);
				extract($SC);

				EVO()->calendar->shortcode_args = $SC; // set arguments to the calendar object
					
			// GET calendar header month year values
				$calendar_month_title = get_eventon_cal_title_month($fixed_month, $fixed_year, $lang);
						
			// Calendar content		
				$content = EVO()->evo_generator->_generate_events();

			// RETURN VALUES
				echo json_encode( apply_filters('evo_ajax_query_returns', array(
					'status'=> 					$status,
					'json'=> 					$content['data'],	
					'html'=>					$content['html'],				
					'cal_month_title'=>			$calendar_month_title,
					'SC'=> 	$SC,
					'debug' => array(
						's' => date('y-m-d h:i:s', $focus_start_date_range),
						'e' => date('y-m-d h:i:s', $focus_end_date_range),
					),
					
				), 
				$SC, $content) );exit;
		}

	// AJAX Events data
		function ajax_events_data(){
			$postdata = $this->helper->sanitize_array( $_POST );

			$SC = isset($postdata['shortcode']) ? $postdata['shortcode']: array();
			EVO()->calendar->shortcode_args = $SC;
		}

	// Now Calendar
		public function refresh_now_cal(){

			$PP = $this->helper->sanitize_array( $_POST );

			$calnow = new Evo_Calendar_Now();

			$SC = isset($PP['SC']) ? $PP['SC']: array();
			
			$defA = isset($PP['defA']) ? $PP['defA'] : array();

			$args = array_merge($defA, $SC);

			$calnow->process_a( $args );

			ob_start();
			$calnow->get_body( true );
			$html = ob_get_clean();

			echo json_encode(array(
				'status'=>'good',
				'html'=> $html,
			)); exit;
		}

	// refresh elements
		public function refresh_elm(){

			$PP = $this->helper->sanitize_array( $_POST );

			echo json_encode($this->get_refresh_elm_data( $PP )); exit;			
		}

		//get ajax refresh element's data array
		public function get_refresh_elm_data($PP, $type ='ajax'){
			$response = array();

			if(isset($PP['evo_data']) && is_array($PP['evo_data']) ){
				
				foreach($PP['evo_data'] as $ekey=>$classes){

					$ee = explode('_', $ekey);
					$EVENT = new EVO_Event($ee[0], '', (int)$ee[1] );

					foreach( $classes as $classnm=>$classdata){

						$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri][$classnm] = apply_filters('evo_ajax_refresh_elm', 
							array(
								'html'=> '',
								'data'=> $classdata,
							), $EVENT, $classnm, $classdata, $type, $PP
						);

						$response['status'] = 'good';
					}

					$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri] = apply_filters('evo_ajax_refresh_event_elms', 
						$response['evo_data'][ 'event_'.$EVENT->ID.'_'.$EVENT->ri],
						$EVENT , $classes, $type, $PP
					);
					
				}									
			}
			return $response;
		}

	// record moderator joined for virtual events
		public function record_mod_joined(){			

			if(!isset($_POST['eid'])) return false;
			if(!isset($_POST['ri'])) return false;
			if(!isset($_POST['nonce'])) return false;
			if(!isset($_POST['joined'])) return false;

			if(!wp_verify_nonce($_POST['nonce'], 'eventon_nonce')) {echo 'nonce failed'; exit;}

			$postdata = $this->helper->sanitize_array( $_POST );

			$EVENT = new EVO_Event( (int)$postdata['eid'], (int)$postdata['eid'] );

			// joined in or left
			$joined = ($postdata['joined'] == 'yes') ? 'in': 'left';

			$EVENT->record_mod_joined($joined);

			echo json_encode(array(
				'status'=>'good',
				'msg'=> ( $joined =='in' ? __('Recorded as moderator joined','eventon') : 
						__('Recorded as moderator left','eventon') )
			)); exit;
		}


	// Load single event content
	// @2.6.13
		function load_event_content(){

			if(!isset($_POST['eid'])) return false;
			if(!isset($_POST['nonce'])) return false;

			if(!wp_verify_nonce($_POST['nonce'], 'eventon_nonce')) {echo 'nonce failed'; exit;} // nonce verification

			$postdata = $this->helper->sanitize_array( $_POST );
			
			$EVENT = new EVO_Event($postdata['eid']);
			echo json_encode(
				apply_filters('evo_single_event_content_data',array(), $EVENT)
			);exit;
		}

	// load single eventcard content
	// @ 2.9.2
		public function load_single_eventcard_content(){
			$postdata = $this->helper->sanitize_array( $_POST );

			$event_id = (int) $postdata['event_id'];
			$ri = (int) $postdata['ri'];

			$SC = array();
			
			$SC = isset($postdata['SC']) ? $postdata['SC'] : array();
			$lang = isset($SC['lang'])? $SC['lang']:'L1';

			$SC['show_exp_evc'] = 'yes';

			$event_data = EVO()->calendar->get_single_event_data( $event_id, $lang, $ri, $SC);

			if($event_data && is_array($event_data)) $event_data = $event_data[0];

			echo json_encode(array(
				'status'=>'good',
				'html'=> $event_data['content']
			)); exit;
		}

	// OUTPUT: json headers
		private function json_headers() {		header( 'Content-Type: application/json; charset=utf-8' );	}

	// for event post repeat intervals 
	// @return converted unix time stamp on UTC timezone
		public function repeat_interval(){
			$date_format = $_POST['date_format'];
		}

	

	// ICS file generation for add to calendar buttons
		function eventon_ics_download(){

			if( !isset( $_GET['event_id'])) return false;

			$event_id = (int)( sanitize_text_field( $_GET['event_id']) );
			$ri = isset($_GET['ri'])? (int)( sanitize_text_field($_GET['ri']) ) : 0;

			$EVENT = new EVO_Event($event_id,'',$ri);
			$EVENT->get_event_post();
			
			// Location information
				$lDATA = $EVENT->get_location_data();

				$location = $location_address = '';
				if($lDATA){
					if($lDATA['name']) $location_name = $lDATA['name'];
					if($lDATA['location_address']) $location_address = $lDATA['location_address'];
					$location = ($location_name? $location_name . ' ':'') . ($location_address?$location_address:'');
					$location = $this->esc_ical_text( stripslashes($location) );
				}
			
			$name = $summary = $EVENT->get_title();

			// summary for ICS file			
				$content = (!empty($EVENT->content))? $EVENT->content:'';
				if(!empty($content)){
					$content = strip_tags($content);
					$content = str_replace(']]>', ']]&gt;', $content);
					$summary = wp_trim_words($content, 50, '[..]');
				}		
			
			$uid = uniqid();

			// start and end time
				//$dDATA = $EVENT->get_non_adjusted_times();
				$dDATA = $EVENT->get_utc_adjusted_times();

				$format =  $EVENT->is_all_day() ? 'Ymd' : 'Ymd\THi';

				$start = date_i18n( $format, $dDATA['start'] );
				$end = date_i18n( $format, $dDATA['end'] );				
				
				$time = current_time('timestamp');
				$year = date('Y', $time);

			//$slug = strtolower(str_replace(array(' ', "'", '.'), array('_', '', ''), $name));
			$slug = $EVENT->post_name;
						
			header("Content-Type: text/Calendar; charset=utf-8");
			header("Content-Disposition: inline; filename={$slug}.ics");


			echo "BEGIN:VCALENDAR\r\n";
			echo "VERSION:2.0\r\n";
			echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
			//echo "METHOD:REQUEST\n"; // requied by Outlook
			echo "BEGIN:VEVENT\n";
			
			echo "UID:{$uid}\n"; // required by Outlok
			echo "DTSTAMP:".date_i18n('Ymd\THis')."\n"; // required by Outlook
			
			$_ending = $EVENT->is_all_day() ? '': '00Z'; // 00 is for seconds

			echo "DTSTART:". 	$start .$_ending . "\n";
			echo "DTEND:".	$end .$_ending . "\n";

			// timezone
			if($tz = $EVENT->get_timezone_key()){
				echo "TZID:". $tz. "\r\n";
			}


			echo "LOCATION:{$location}\n";
			echo "SUMMARY:".html_entity_decode( $this->esc_ical_text($name))."\n";
			echo "DESCRIPTION: ".$this->esc_ical_text($summary)."\\n" . ($EVENT->is_virtual() ? $EVENT->virtual_url() : $EVENT->get_permalink() ) . "\n";

			echo "URL:" . ($EVENT->is_virtual() ? $EVENT->virtual_url() : $EVENT->get_permalink() ) . "\n";

			// plug @+3.1
			do_action('evo_event_ics_content', $EVENT);

			echo "END:VEVENT\n";
			echo "END:VCALENDAR";
			exit;

			/*
				// DAY LIGHT SAVING
				echo "BEGIN:VTIMEZONE\r\n";
				echo "TZID:". get_option('timezone_string'). "\r\n";
				echo "LAST-MODIFIED:". $this->sanitize_unix( $time ) . "\r\n";
				echo "BEGIN:STANDARD\r\n";
				echo "DTSTART:".$year."1104T020000\r\n";
				echo "TZOFFSETFROM:-0400\r\n";
				echo "TZOFFSETTO:-0500\r\n";
				echo "TZNAME:EST\r\n";
				echo "END:STANDARD\r\n";
				echo "BEGIN:DAYLIGHT\r\n";
				echo "DTSTART:".$year."0311T020000\r\n";
				echo "TZOFFSETFROM:-0500\r\n";
				echo "TZOFFSETTO:-0400\r\n";
				echo "TZNAME:EDT\r\n";
				echo "END:DAYLIGHT\r\n";
				echo "END:VTIMEZONE\r\n";
			*/

		}
			// 8932480932T0302Z format
			function sanitize_unix($unix){
				$t = explode('Z', $unix);
				$u = explode('T', $t[0]);

				$a = (int)$u[0];
				$b = isset($u[1]) ? (int)$u[1]:0;

				if(strlen($a)<6) $a = sprintf('%06d', $a);
				if(strlen($b)<6) $b = sprintf('%06d', $b);

				return $a.'T'. $b .'Z';
			}
			function esc_ical_text( $text='' ) {
				
			    $text = str_replace("\\", "", $text);
			    $text = str_replace("\r", "\r\n ", $text);
			    $text = str_replace("\n", "\r\n ", $text);
			    $text = str_replace(",", "\, ", $text);
			    $text = EVO()->calendar->helper->htmlspecialchars_decode($text);
			    return $text;
			}

	// download all event data as ICS
		function export_events_ics(){
			global $eventon;

			if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Nonce Security Failed.');

			$events = EVO()->calendar->get_all_event_data(array(
				'hide_past'=>'yes'
			));
			
			if(!empty($events)):
				$slug = 'eventon_events';
				header("Content-Type: text/Calendar; charset=utf-8");
				header("Content-Disposition: inline; filename={$slug}.ics");
				echo "BEGIN:VCALENDAR\n";
				echo "VERSION:2.0\n";
				echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
				echo "CALSCALE:GREGORIAN\n";
				echo "METHOD:PUBLISH\n";

				// EACH EVENT
				foreach($events as $event_id=>$event){

					$event_name = $description = EVO()->calendar->helper->htmlspecialchars_decode($event['name']);
					$location =  '';

					if(!empty($event['details'])){
						$content = strip_tags($event['details']);
						$content = str_replace(']]>', ']]&gt;', $content);
						$description = wp_trim_words($content, 50, '[..]');
					}

					$description = $this->esc_ical_text($description) ."\\n" . !empty( $event['permalink'] ) ? $event['permalink']:'';					

					// location 
						$Locterms = wp_get_object_terms( $event_id, 'event_location' );
						$location_name = $locationAddress = '';
						if ( $Locterms && ! is_wp_error( $Locterms ) ){
							$location_name = $Locterms[0]->name;
							$termMeta = evo_get_term_meta('event_location',$Locterms[0]->term_id, '', true);
							$locationAddress = !empty($termMeta['location_address'])? 
								$termMeta['location_address']:
								(!empty($event['location_address'])? $event['location_address']:'');
						}
						$location = (!empty($location_name)? $location_name:'').' '. (!empty($locationAddress)? $locationAddress:'');

					$uid = uniqid();

					echo "BEGIN:VEVENT\n";
					echo "UID:{$uid}\n"; // required by Outlok
					echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
					echo "DTSTART:" . $this->helper->get_ics_format_from_unix($event['start']) ."\n"; 
					echo "DTEND:" . $this->helper->get_ics_format_from_unix($event['end']) ."\n";
					if(!empty($location)) echo "LOCATION:". $this->esc_ical_text($location) ."\n";
					echo "SUMMARY:". $event_name ."\n";
					if(!empty($description)) echo "DESCRIPTION: ". $description ."\n";
					echo "END:VEVENT\n";

					// repeating instances
						if(!empty($event['repeats']) && is_array($event['repeats'])){
							foreach( $event['repeats'] as $interval=>$repeats){
								if($interval==0) continue;

								$uid = uniqid();
								echo "BEGIN:VEVENT\n";
								echo "UID:{$uid}\n"; // required by Outlok
								echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
								echo "DTSTART:" . $this->helper->get_ics_format_from_unix($repeats[0]) ."\n"; 
								echo "DTEND:" . $this->helper->get_ics_format_from_unix($repeats[1]) ."\n";
								if(!empty($location)) echo "LOCATION:". $this->esc_ical_text($location) ."\n";
								echo "SUMMARY:". $event_name ."\n";
								if(!empty($summary)) echo "DESCRIPTION: ". $description ."\n";
								echo "END:VEVENT\n";
							}
						}

				}
				echo "END:VCALENDAR";
				exit;

			endif;
		}

	// get event time based on local time on browswr
		function get_local_event_time(){
			$datetime = new evo_datetime();
			$offset = $datetime->get_UTC_offset();
			$brosweroffset = (int)$_POST['browser_offset'] *60;
			echo $brosweroffset.' '.$offset.' '.$object->evvals['evcal_srow'][0];

			$newunix = $object->evvals['evcal_srow'][0] + ($offset + $brosweroffset);
			echo date('Y-m-d h:ia', $newunix);
		}


	// Search results for ajax search of events from search box
	function search_evo_events(){
		
		$postdata = $this->helper->sanitize_array( $_POST );

		$searchfor = isset($postdata['search']) ? $postdata['search'] :'';
		$shortcode = isset($postdata['shortcode']) ? $postdata['shortcode']: array();
	
		$searchfor = str_replace("\'",'', $searchfor);

		// if search all events regardless of date
		if( !empty($shortcode['search_all'] ) && $shortcode['search_all']=='yes'){
			$DD = EVO()->calendar->DD;
			$DD->modify('first day of this month'); $DD->setTime(0,0,0);
			$DD->modify('-15 years');
			
			$__focus_start_date_range = $DD->format('U');
			
			$DD->modify('+30 years');
			$__focus_end_date_range = $DD->format('U');
		
		}else{
			$current_timestamp = current_time('timestamp');

			// restrained time unix
				$number_of_months = !empty($shortcode['number_of_months'])? $shortcode['number_of_months']:12;
				$month_dif = '+';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
				$__focus_start_date_range = $current_timestamp;
				$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
		}
		

		// Add extra arguments to shortcode arguments			
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
				's'=>$searchfor,
				'search_all'=> (isset($shortcode['search_all'])? $shortcode['search_all']:'no')
			);

			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;

			// merge passed shortcode values
				if(!empty($shortcode))
					$args= wp_parse_args($shortcode, $args);

			EVO()->calendar->process_arguments($args);

			$content = EVO()->calendar->get_calendar_header(
				array(
					'date_header'=>false,
					'sortbar'=>false,
					'range_start'=>$__focus_start_date_range,
					'range_end'=>$__focus_end_date_range,
					'header_title'=>'',
					'send_unix'=>true
				)
			);

			$content .= EVO()->calendar->_generate_events('html');
			
			$content .= EVO()->calendar->body->get_calendar_footer();
			
			echo json_encode(array(
				'content'=>$content,
				'range'=> date('Y-m-d', $__focus_start_date_range).' '.date('Y-m-d', $__focus_end_date_range)
			));
			exit;

	}
	/* dynamic styles */
		/*function eventon_dymanic_css(){
			//global $foodpress_menus;
			require('admin/inline-styles.php');
			exit;
		}*/

}
