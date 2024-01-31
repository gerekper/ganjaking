<?php
/**
 * Eventon date time class.
 *
 * @class 		EVO_generator
 * @version		4.5.7
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class evo_datetime{		
	public $wp_time_format, $wp_date_format;
	/**	Construction function	 */
		public function __construct(){
			$this->wp_time_format = EVO()->calendar->time_format;
			$this->wp_date_format = EVO()->calendar->date_format;
		}

	// RETURN UNIX
		// return repeat interval correct unix time stamp for start OR end
			public function get_int_correct_event_time($post_meta, $repeat_interval, $time='start'){
				if(!empty($post_meta['repeat_intervals']) && $repeat_interval>0 ){	

					$repeat_interval = (int)$repeat_interval;

					$intervals = unserialize($post_meta['repeat_intervals'][0]);

					if(sizeof($intervals)>0 && isset($intervals[$repeat_interval])){
						return ($time=='start')? 
							$intervals[$repeat_interval][0]:
							$intervals[$repeat_interval][1];
					}else{
						return ($time=='start')? $post_meta['evcal_srow'][0]:$post_meta['evcal_erow'][0];
					}
					
				}else{
					// return raw start val else end val else 0
					if( isset( $post_meta['evcal_srow'] )) return $post_meta['evcal_srow'][0];
					if( isset( $post_meta['evcal_erow'] )) return $post_meta['evcal_erow'][0];
					return 0;
				}
			}
		// return just UNIX timestamps corrected for repeat intervals
			public function get_correct_event_repeat_time($post_meta, $repeat_interval=''){
				if(!empty($repeat_interval) && !empty($post_meta['repeat_intervals']) && $repeat_interval!='0'){
					$intervals = unserialize($post_meta['repeat_intervals'][0]);

					return array(
						'start'=> (isset($intervals[$repeat_interval][0])? 
							$intervals[$repeat_interval][0]:
							$intervals[0][0]),
						'end'=> (isset($intervals[$repeat_interval][1])? 
							$intervals[$repeat_interval][1]:
							$intervals[0][1]) ,
					);

				}else{// no repeat interval values saved
					$start = !empty($post_meta['evcal_srow'])? $post_meta['evcal_srow'][0] :0;
					return array(
						'start'=> $start,
						'end'=> ( !empty($post_meta['evcal_erow'])? $post_meta['evcal_erow'][0]: $start)
					);
				}
			}
		// get unix times for event
			function get_correct_event_time($event_id, $repeat_interval=0){
				$RIS = get_post_meta($event_id, 'repeat_intervals', true);
				$RI_on = get_post_meta($event_id, 'evcal_repeat', true);

				if(empty($RIS) || $RI_on =='no'){
					$start =  get_post_meta($event_id, 'evcal_srow', true);
					$end =  get_post_meta($event_id, 'evcal_erow', true);
					$end = !empty($end)? $end: $start;
					return array('start'=>$start, 'end'=>$end);
				}else{
					return array(
						'start'=> (isset($RIS[$repeat_interval][0])? 
							$RIS[$repeat_interval][0]:
							$RIS[0][0]),
						'end'=> (isset($RIS[$repeat_interval][1])? 
							$RIS[$repeat_interval][1]:
							$RIS[0][1]) ,
					);
				}
				
			}

	/*
	 * Return: array(start, end)
	 * Returns WP proper formatted corrected event time based on repeat interval provided
	 * u4.5.7 -- deprecating
	 */
		public function get_correct_formatted_event_repeat_time($post_meta, $repeat_interval='', $date_format='', $tz = ''){
			
			// get date and time formats
			$date_format = (!empty($date_format)? $date_format: get_option('date_format'));			
			$wp_time_format = $this->wp_time_format;

			if(!empty($repeat_interval) && !empty($post_meta['repeat_intervals']) && $repeat_interval!='0' ){
				$intervals = unserialize($post_meta['repeat_intervals'][0]);

				// if there arent repeating intervals saved
				if(!isset($intervals[$repeat_interval])) return false;

				$formatted_unix_s = eventon_get_formatted_time($intervals[$repeat_interval][0] , $tz );
				$formatted_unix_e = eventon_get_formatted_time($intervals[$repeat_interval][1] , $tz );

				return array(
					// this didnt work on tickets addon
					'start_'=> $this->__get_lang_formatted_timestr($date_format.' '.$wp_time_format, $formatted_unix_s),
					'end_'=> $this->__get_lang_formatted_timestr($date_format.' '.$wp_time_format, $formatted_unix_e),

					'start'=> date_i18n($date_format.' h:i:a',$intervals[$repeat_interval][0]),
					'end'=> date_i18n($date_format.' h:i:a',$intervals[$repeat_interval][1]),
				);

			}else{// no repeat interval values saved
				$start = !empty($post_meta['evcal_srow'])? date_i18n($date_format.' h:i:a', $post_meta['evcal_srow'][0]) :0;
				$start_row =  !empty($post_meta['evcal_srow'])? $post_meta['evcal_srow'][0]: time();
				$end_row =  !empty($post_meta['evcal_erow'])? $post_meta['evcal_erow'][0]: $post_meta['evcal_erow'][0];
				$end = ( !empty($post_meta['evcal_erow'])? date_i18n($date_format.' h:i:a',$post_meta['evcal_erow'][0]): $start);

				$formatted_unix_s = eventon_get_formatted_time($start_row , $tz );
				$formatted_unix_e = eventon_get_formatted_time($end_row , $tz );

				//echo $end_row.' '.$post_meta['evcal_srow'][0];
				return array(
					'start'=> $start,
					'end'=> $end,
					'start_'=> $this->__get_lang_formatted_timestr($date_format.' '.$wp_time_format, $formatted_unix_s),
					'end_'=> $this->__get_lang_formatted_timestr($date_format.' '.$wp_time_format, $formatted_unix_e),
				);
			}
		}

	// convert unix to lang formatted readable string
	// +3.0.3 u4.5.7
		public function get_readable_formatted_date($unix, $format = '', $tz = ''){

			if(empty($format)) $format = EVO()->calendar->date_format.' '.EVO()->calendar->time_format;

			return $this->__get_lang_formatted_timestr(
				$format, 
				eventon_get_formatted_time( $unix , $tz )
			);
			
		}

	

	// return a smarter complete date-time -translated and formatted to date-time string
	// 2.3.13 -- deprecating use EVO_Event()->get_formatted_smart_time();
		public function get_formatted_smart_time($startunix, $endunix, $epmv='', $event_id=''){

			$wp_time_format = get_option('time_format');
			$wp_date_format = get_option('date_format');

			if(empty($epmv) && empty($event_id)) return false;

			if(empty($epmv)) $epmv = get_post_meta($event_id);

			$start_ar = eventon_get_formatted_time($startunix);
			$end_ar = eventon_get_formatted_time($endunix);
			$_is_allday = (!empty($epmv['evcal_allday']) && $epmv['evcal_allday'][0]=='yes')? true:false;
			$hideend = (!empty($epmv['evo_hide_endtime']) && $epmv['evo_hide_endtime'][0]=='yes')? true:false;

			$output = '';

			// reused
				$joint = $hideend?'':' - ';

			// same year
			if($start_ar['y']== $end_ar['y']){
				// same month
				if($start_ar['n']== $end_ar['n']){
					// same date
					if($start_ar['j']== $end_ar['j']){
						if($_is_allday){
							$output = $this->date($wp_date_format, $start_ar) .' ('.evo_lang_get('evcal_lang_allday','All Day').')';
						}else{
							$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint. 
								(!$hideend? $this->date($wp_time_format, $end_ar):'');
						}
					}else{// dif dates
						if($_is_allday){
							$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.
								(!$hideend? $this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')':'');
						}else{
							$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint.
								(!$hideend? $this->date($wp_date_format.' '.$wp_time_format, $end_ar):'');
						}
					}
				}else{// dif month
					if($_is_allday){
						$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.
							(!$hideend? $this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')':'');
					}else{// not all day
						$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint.
							(!$hideend? $this->date($wp_date_format.' '.$wp_time_format, $end_ar):'');
					}
				}
			}else{
				if($_is_allday){
					$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.
						(!$hideend? $this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')':'');
				}else{// not all day
					$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar). $joint .
						(!$hideend? $this->date($wp_date_format.' '.$wp_time_format, $end_ar):'');
				}
			}
			return $output;	
		}


	// return datetime string for a given format using date-time data array
		public function date($dateformat, $array){	
			return $this->__get_lang_formatted_timestr($dateformat, $array);
			
		} 

	// return event date/time in given date format using date item array
	// added v4.0.7
		function __get_lang_formatted_timestr($dateform, $datearray){
			$time = str_split($dateform);
			$newtime = '';
			$count = 0;
			foreach($time as $timestr){
				// check previous chractor
					if( strpos($time[ $count], '\\') !== false ){ 
						//echo $timestr;
						$newtime .='';
					}elseif($count!= 0 &&  strpos($time[ $count-1 ], '\\') !== false ){
						$newtime .= $timestr;
					}else{
						$newtime .= (is_array($datearray) && array_key_exists($timestr, $datearray))? $datearray[$timestr]: $timestr;
					}
				
				$count ++;
			}
			return $newtime;
		}


	// Timezone 
	// @deprecating moved to EVO_Environment
		function get_UTC_offset(){
			$offset = (get_option('gmt_offset', 0) * 3600);

			$opt = EVO()->frontend->evo_options;
			$customoffset = !empty($opt['evo_time_offset'])? 
				(intval($opt['evo_time_offset'])) * 60:
				0;

			return $offset + $customoffset;
		}

		function get_local_unix_now(){
			return EVO()->calendar->utc_time;
		}
		function set_timezone(){
			$tzstring = $this->get_timezone_str();
			$tzstring = $tzstring == 'UTC+0'? 'UTC': $tzstring;
			date_default_timezone_set($tzstring);
		}
		
		function get_timezone_str(){
			$tzstring = get_option('timezone_string');

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( false !== strpos($tzstring,'Etc/GMT') )
				$tzstring = '';

			$current_offset='';
			if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
				$check_zone_info = false;
				if ( 0 == $current_offset )
					$tzstring = 'UTC+0';
				elseif ($current_offset < 0)
					$tzstring = 'UTC' . $current_offset;
				else
					$tzstring = 'UTC+' . $current_offset;
			}

			return $tzstring;
		}

}