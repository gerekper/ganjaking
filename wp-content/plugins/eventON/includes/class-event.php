<?php
/**
 * Event Class for one event
 * @version 4.1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('EVO_Data_Store')) exit;

class EVO_Event extends EVO_Data_Store{
	public $event_id;
	public $ID;
	public $ri = 0;
	public $l = 'L1';

	// deprecating
	// $this->event_data
	private $pmv =''; // deprecated

	public $tax = array();
	private $DD;

	public $post_title ='';
	public $post_name = '';

	public $duration;
	public $vir_duration = false; // duration till virtual end time
	public $start_unix_raw;
	public $start_unix;

	public $utc_offset = 0; // utc offset for the event times
	public $utcoff = 0;

	public function __construct($event_id, $event_pmv='', $ri = 0, $force_data_set = true, $post=false){
		
		$this->event_id = $this->ID = (int)$event_id;
		$this->post_type = 'ajde_events';		
		$this->meta_array_key = '_edata';		
		
		if($force_data_set){			
			$this->set_event_data($event_pmv);
		} 		
		
		$this->localize_edata();
		$this->ri = $ri;

		// common date object
		$this->DD = new DateTime();
		$this->DD->setTimezone( EVO()->calendar->timezone0);

		// set event post to class if available
		if($post !== false){
			$this->author = $post->post_author;
			$this->post_date = $post->post_date;
			$this->content = $post->post_content;
			$this->excerpt = $post->post_excerpt;
			$this->post_name = $post->post_name;
			$this->post_title = $post->post_title;
		}

		$this->event_data = $this->meta_data;

		$this->_process_eventtimes();
	}

	// event building @+2.6.10
		function set_lang($lang){ $this->l = $lang;}

	// permalinks
		// @~ 2.6.7
		function get_permalink($ri= '' , $l = ''){
			$event_link = get_permalink($this->event_id);

			$ri = (empty($ri) && $ri !== 0)? 
				( $this->ri == 0? 0: $this->ri): $ri;

			$l = (empty($l))? $this->l: $l;

			if($ri==0 && $l=='L1') return $event_link;

			$append = 'ri-'. $ri.'.l-'. $l;

			$permalink_last = substr($event_link, -1);
				$event_link = ($permalink_last == '/')? substr($event_link, 0,-1): $event_link;


			// processing
			$event_link = $this->_process_link( $event_link, $append, 'var');
			
			//$event_link = htmlentities($event_link, ENT_QUOTES | ENT_HTML5);

			return apply_filters('evo_event_permalink',$event_link, $this);
		}

		function get_ux_link(){
			$exlink_option = $this->get_prop('_evcal_exlink_option');	
		}

	// title
		function get_title(){
			if(!empty($this->post_title)) return apply_filters('evodata_title', $this->post_title, $this);
			return apply_filters('evodata_title', get_the_title($this->ID) , $this);
		}
		function get_subtitle(){
			return apply_filters('evodata_subtitle', $this->get_prop('evcal_subtitle'), $this);
		}

		// @+ 2.6.12
		function edit_post_link(){
			return get_admin_url().'post.php?post='.$this->ID.'&action=edit';	
		}

		// @+ 2.8.4 
		function get_event_uniqid(){
			return $this->ID.'_'.$this->ri;
		}
	
	// time and date related

		// Process event times on the load
			public function _process_eventtimes(){
				
				$start = $this->get_prop('evcal_srow');
				$end = $this->get_prop('evcal_erow')? $this->get_prop('evcal_erow'): $this->get_prop('evcal_srow');

				if($this->is_repeating_event() ){
					$repeat_interval = (int)$this->ri;
					$intervals = $this->get_prop('repeat_intervals');

					if(sizeof($intervals)>0 ){
						$start = isset($intervals[$repeat_interval][0])? $intervals[$repeat_interval][0]: $intervals[0][0];
						$end = isset($intervals[$repeat_interval][1])? $intervals[$repeat_interval][1]:$intervals[0][1];
					}				
				}

				$this->start_unix = $this->start_unix_raw = $this->_year_month_long_filter( (int)$start ,'start');
				$this->end_unix = $this->_year_month_long_filter( (int)$end, 'end');
				$this->duration = (int)$this->end_unix - (int)$this->start_unix;

				// if virtual end time set
				if($vir_end = $this->is_virtual_end() ){
					$this->vir_duration = (int)$vir_end - (int)$start;
				}


				// return unix offset of event time from utc 0
				// get event offset from utc 0
				$tz_string = $this->get_timezone_key();
				$HELP = new evo_helper();
				$this->utc_offset = $this->utcoff = $HELP->get_timezone_offset( $tz_string , $this->start_unix );
				
				// if settings set to use utf offset
				if( EVO()->calendar->is_utcoff ){	
					$this->start_unix =  $this->start_unix + $this->utc_offset;
				}

				return;
			}

		// load a repeat instance times info the object
			public function load_repeat($ri){
				$this->ri = $ri;
				if( !$this->is_repeating_event() ) return;

				$repeat_interval = (int)$this->ri;
				$intervals = $this->get_prop('repeat_intervals');

				if(!is_array($intervals)) return;
				if( sizeof($intervals) == 0) return;

				$start = isset($intervals[$repeat_interval][0])? $intervals[$repeat_interval][0]: $intervals[0][0];
				$end = isset($intervals[$repeat_interval][1])? $intervals[$repeat_interval][1]:$intervals[0][1];

				$this->start_unix = $this->start_unix_raw = (int)$start;
				$this->end_unix = (int)$end;
				$this->duration = (int)$end - (int)$start;
				
				if( EVO()->calendar->is_utcoff ){
					// return unix offset of event time from utc 0 -- get event offset from utc 0
					$tz_string = $this->get_timezone_key();
					$HELP = new evo_helper();
					$this->utc_offset = $this->utcoff = $HELP->get_timezone_offset( $tz_string , $this->start_unix );

					$this->start_unix =  $this->start_unix + $this->utc_offset;
				}
			}
		// switch object times back to original event times
			public function load_init_eventtimes(){
				$this->ri = 0;
				$this->_process_eventtimes();
			}


		// current and future
		function is_current_event( $cutoff='end', $current_time = '', $utc = false){
			if(empty($current_time)){
				$current_time = EVO()->calendar->get_current_time();
			}

			$event_start_time = ($utc) ? $this->start_unix: $this->start_unix_raw;
			$event_time = $cutoff == 'end' ?  $event_start_time + $this->duration : $event_start_time;
			return $event_time > $current_time? true: false;
		}		
		
		// if the event is live right now
		function is_event_live_now($CT=''){
			if(empty($CT)) $CT = EVO()->calendar->get_current_time();

			$end = $this->start_unix + $this->duration;

			$bool =  (  $CT >= $this->start_unix && $CT <= $end) ? true : false;
			//return $bool;

			return apply_filters('evodata_vir_live', $bool, $this, $this->start_unix, $end, $CT);
		}

		// @~ 2.8
		function is_past_event($cutoff = 'end'){			
			$is_current = $this->is_current_event($cutoff);
			return $is_current? false: true;
		}
		// +3.0.6 
		public function is_future_event( ){
			return $this->start_unix > EVO()->calendar->get_current_time() ? true: false;
		}
		// this checked if event start time is less than current time - added 3.1
		public function is_event_started(){
			return $this->start_unix < EVO()->calendar->get_current_time() ? true : false;
		}

		function is_all_day(){
			return $this->check_yn('evcal_allday');
		}
		function is_hide_endtime(){
			return $this->check_yn('evo_hide_endtime');
		}

		// @+2.8
		function is_event_in_date_range($S=0, $E=0, $start='' ,$end='' , $utc = false){
			if(empty($start) && empty($end) ){
				$start = ($utc) ? $this->start_unix: $this->start_unix_raw;
				$end = $start + $this->duration;
			}
			return EVO()->calendar->shell->is_in_range( $S, $E, $start, $end);
		}

		function seconds_to_start_event($CT = ''){			
			if(empty($CT)) $CT = EVO()->calendar->get_current_time() ;

			$t = $this->start_unix - $CT;

			return ($t<=0) ? false: $t;
		}

		public function is_virtual_end(){
			if( !$this->check_yn('_evo_virtual_endtime')) return false;
			$vir_end = $this->get_prop('_evo_virtual_erow');
			if( !$vir_end) return false;
			return $vir_end;		
		}

	// DATE TIME
		// primary function to get event start end unix with repeat interval adjusted
		// run on __construct
		function get_start_end_times($custom_ri='', $return_type = 'both', $utcoff = false){
			
			$start = $this->start_unix_raw;
			$end = $this->start_unix_raw + $this->duration;
			

			if($this->is_repeating_event() ){
				$repeat_interval = !empty($custom_ri)? (int)$custom_ri: (int)$this->ri;
				$intervals = $this->get_prop('repeat_intervals');

				if(sizeof($intervals)>0 ){
					$start = isset($intervals[$repeat_interval][0])? $intervals[$repeat_interval][0]: $intervals[0][0];
					$end = isset($intervals[$repeat_interval][1])? $intervals[$repeat_interval][1]:$intervals[0][1];
				}				
			}

			// if return utc offsetted time
			if( $utcoff){
				$start = $start + $this->utc_offset;
				$end = $end + $this->utc_offset;
			}

			if($return_type == 'both'){
				return array(
					'start'=> $this->_year_month_long_filter($start, 'start'),
					'end'=> $this->_year_month_long_filter($end, 'end')
				);
			}

			if( $return_type == 'start') return $this->_year_month_long_filter($start, 'start');
			if( $return_type == 'end') return $this->_year_month_long_filter($end, 'end');

			
		}

		// @+ 2.6.10 @updated 3.1.6
		function get_start_time($utc = false){

			if( !$utc) return $this->start_unix_raw;

			if( $this->start_unix == $this->start_unix_raw ) return $this->start_unix + $this->utc_offset;

			return $this->start_unix;
		}
		function get_end_time($utc = false){

			if(!$utc) return $this->start_unix_raw + $this->duration;

			if( $this->start_unix == $this->start_unix_raw ) return $this->start_unix + $this->utc_offset + $this->duration;

			return $this->start_unix + $this->duration;
		}


		// updated 3.1.2
		// return event start/ end time for initial or custom repeat with utc offset
		// will auto use utcoff if not passed and enabled in settings
		function get_event_time($type='start', $custom_ri='', $utcoff = true){
			$utcoff  = ( $utcoff != false )? EVO()->calendar->is_utcoff: $utcoff;
			return 	$this->get_start_end_times($custom_ri, $type , $utcoff);			
		}


		// if its year long or month long event return correct start end unix
		// @+ 2.8
		function _year_month_long_filter($unix, $type='start'){
			if(empty($unix)) return $unix;

			if($this->is_year_long()){

				$this->DD->setTimestamp($unix);
				($type == 'start')? $this->DD->modify( 'first day of january this year') : 
					$this->DD->modify( 'last day of december this year');
				($type == 'start')? $this->DD->setTime(0,0,0): $this->DD->setTime(23,59,59);

				return $this->DD->format('U');
			}else{
				if($this->is_month_long()){
					$this->DD->setTimestamp($unix);
					($type == 'start') ? $this->DD->modify('first day of this month'):$this->DD->modify('last day of this month');
					($type == 'start')? $this->DD->setTime(0,0,0): $this->DD->setTime(23,59,59);
					return $this->DD->format('U');
				}else{
					return $unix;
				}
			}
		}		

		function get_formatted_smart_time($custom_ri=''){
			$wp_time_format = get_option('time_format');
			$wp_date_format = get_option('date_format');

			$times = $this->get_start_end_times($custom_ri);

			$start_ar = eventon_get_formatted_time($times['start']);
			$end_ar = eventon_get_formatted_time($times['end']);
			$_is_allday = $this->check_yn('evcal_allday');
			$hideend = $this->check_yn('evo_hide_endtime');

			if(!is_array($start_ar) || !is_array($end_ar)) return false;

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

		// return start and end time in array after adjusting time to UTC offset 
		// based on site timezone passed via event edit
		// UPDATED: 3.1.3
		function get_utc_adjusted_times(){			
			// use raw start time to calculate UTC offset time
			$start = $this->start_unix_raw + $this->utc_offset;

			return $new_times = array(
				'start'=> $start, 
				'start_dst'=> false,
				'end'=> $start + $this->duration,
				'end_dst'=> false,
			);			
		}
		// return none adjusted event times
		// added @4.0.6
		function get_non_adjusted_times(){			
			$start = $this->start_unix_raw;

			return $new_times = array(
				'start'=> $start, 
				'start_dst'=> false,
				'end'=> $start + $this->duration,
				'end_dst'=> false,
			);			
		}

		// return readable evo translated date time for unix
		// added 3.0.3 / updated 4.0.7
		function get_readable_formatted_date($unix, $format='', $check_all_day = false){			

			$datetime = new evo_datetime();
			if($this->is_all_day() && $check_all_day){

				return $datetime->__get_lang_formatted_timestr(
					EVO()->calendar->date_format, 
					eventon_get_formatted_time( $unix )
				). 
				' ('.evo_lang_get('evcal_lang_allday','All Day').')';
				
			}else{

				if(empty($format)) $format = EVO()->calendar->date_format.' '.EVO()->calendar->time_format;

				return $datetime->__get_lang_formatted_timestr(
					$format, 
					eventon_get_formatted_time( $unix )
				);
			}
		}

		// updated 4.0.7
		private function date($dateformat, $array){	
			$datetime = new evo_datetime();
			return $datetime->__get_lang_formatted_timestr($dateformat, $array);
		}

		function get_addto_googlecal_link($location_name='', $location_address=''){

			$event_times = $this->get_utc_adjusted_times();

			$format =  $this->is_all_day() ? 'Ymd' : 'Ymd\THi';
			$format_e =  $this->is_all_day() ? '' : '00Z';

			// modify end unix for google cal
			$modified_end_unix = $this->is_all_day() ? $event_times['end'] + 86400: $event_times['end'];

			$start = date_i18n( $format , $event_times['start'] ). $format_e;
			$end = date_i18n( $format , $modified_end_unix ). $format_e;
				
			if( !empty($location_name)) $location_name = urlencode($location_name);
			if( !empty($location_address)) $location_address = urlencode($location_address);
			
			$title = urlencode($this->post_title);
			$excerpt = !empty($this->excerpt)? $this->excerpt: $this->post_title;

			return '//www.google.com/calendar/event?action=TEMPLATE&amp;text='.$title.'&amp;dates='.$start.'/'.$end.'&amp;ctz='. $this->get_timezone_key() .'&amp;details='.( urlencode($excerpt) ).'&amp;location='.$location_name.$location_address;
		}

		// timezone
		function get_timezone_key($use_default = false){

			$this_tzo = $this->get_prop('_evo_tz');

			if($this_tzo) return $this_tzo;

			if( EVO()->cal->check_yn('evo_tzo_all','evcal_1') || $use_default ){
				return EVO()->cal->get_prop('evo_global_tzo','evcal_1');
			}

			return 'UTC';
		}

	// repeating events
		function is_repeating_event(){

			if(!$this->check_yn('evcal_repeat')) return false;
			if(empty($this->meta_data['repeat_intervals'])) return false;

			$repeats = unserialize($this->meta_data['repeat_intervals'][0]);

			if(!is_array($repeats)) return false;
			if(count($repeats)==1) return false;

			return true;
		}
		function get_repeats(){
			if(empty($this->meta_data['repeat_intervals'])) return false;
			return unserialize($this->meta_data['repeat_intervals'][0]);
		}
		function get_repeats_count(){
			if(!$this->check_yn('evcal_repeat')) return false;
			if(empty($this->meta_data['repeat_intervals'])) return false;

			return count(unserialize($this->meta_data['repeat_intervals'][0])) -1;
		}

		function is_repeat_index_exists( $index){
			$repeats = $this->get_repeats();
			if(!$repeats) return false;

			if(!isset( $repeats[ $index ])) return false;
			return $repeats[ $index ];
		}

		// next repeat instance that is current (not past)
		function get_next_current_repeat($current_ri_index, $check_by = 'start'){
			$repeats = $this->get_repeats();
			if(!$repeats) return false;

			$current_time = EVO()->calendar->current_time0;
			
			$return = false;
			
			foreach($repeats as $index=>$repeat){
				if($index<= $current_ri_index) continue;

				// check if start time of repeat is current
				if($check_by == 'start' && $repeat[0]>=  $current_time) $return = true;
				if($check_by != 'start' && $repeat[1]>=  $current_time) $return = true;

				if($return)	return array('ri'=>$index, 'times'=>$repeat);
			}
			return false;
		}

		function get_repeat_interval($key){
			$repeats = $this->get_repeats();
			if(!$repeats) return false;
				
			$all_repeats = count($repeats)-1;

			if($key == 'last'){
				return end($repeats);
			}

			if($key == 'first'){
				return $repeats[0];
			}

			foreach($repeats as $index=>$repeat){
				if($index< $key) continue;
				if($index == $key)	return $repeat;						
			}
			return false;
		}

		// return the repeat header html
		// added @version 4.1.2
		function get_repeat_header_html(){
			if( !$this->is_repeating_event() ) return false;			

			$repeat_count = $this->get_repeats_count();

			// if there is only one time range in the repeats that means there are no repeats
			if($repeat_count == 0) return false;
			
			$date = new evo_datetime();

			$ev_vals = $this->get_data();
			$ri = $this->ri;

			global $EVOLANG;

			ob_start();

			// show relative repeat instance 
				$title_adds = '';
				if( $this->check_yn('_evo_rep_series')){
					$title_adds = '- ' . evo_lang('Event').' '. ($ri +1) . ' / '. 
					($this->get_repeats_count() +1);				
				}

			echo "<div class='evose_repeat_header'><p><span class='title'>".
				evo_lang('This is a repeating event'). $title_adds . "</span>";
			echo "<span class='ri_nav'>";

			// previous link
			if($ri>0){ 

				$prev_unixs = $this->is_repeat_index_exists( $ri -1 );

				if($prev_unixs && isset($prev_unixs[0]) && $prev_unixs[0] > 0){

					$prev_unix = (int)$prev_unixs[0];

					$text = '';

					if($this->is_year_long()){
						$text = date('Y', $prev_unix);
					}elseif( $this->is_month_long() ){
						$text = $date->get_readable_formatted_date( $prev_unix, 'F, Y');
					}elseif($this->is_all_day()){
						$text = $date->get_readable_formatted_date( $prev_unix, EVO()->calendar->date_format);						
					}else{
						$text = $date->get_readable_formatted_date( $prev_unix );
					}


					$prev_link = $this->get_permalink( ($ri-1), $this->L);
					
					echo "<a href='{$prev_link}' class='prev' title='{$text}'><b class='fa fa-angle-left'></b><em>{$text}</em></a>";
				}				

			}

			// next link 
			if($ri<$repeat_count){
				$ri = (int)$ri;

				$next_unixs = $this->is_repeat_index_exists( $ri +1 );

				if($next_unixs && isset($next_unixs[0])){

					$next_unix = (int)$next_unixs[0];

					$text = '';

					if($this->is_year_long()){
						$text = date('Y', $next_unix);
					}elseif( $this->is_month_long() ){
						$text = $date->get_readable_formatted_date( $next_unix, 'F, Y');
					}elseif($this->is_all_day()){
						$text = $date->get_readable_formatted_date( $next_unix, EVO()->calendar->date_format);
					}else{
						$text = $date->get_readable_formatted_date( $next_unix );
					}

					//print_r($next); 
					$next_link = $this->get_permalink( ($ri+1), EVO()->lang );

					echo "<a href='{$next_link}' class='next' title='{$text}'><em>{$text}</em><b class='fa fa-angle-right'></b></a>";

				}				
				
			}
			
			echo "</span><span class='clear'></span></p></div>";

			return ob_get_clean();
		}

	// Taxonomy @+2.8.1 @~2.8.5
		function get_tax_ids(){
			global $wpdb;

			if(count($this->tax)>0) return $this->tax;

			$OUT = array();

			$R = $wpdb->get_results( $wpdb->prepare(
				"SELECT term_taxonomy_id FROM {$wpdb->prefix}term_relationships WHERE object_id=%d", $this->ID
			));

			if($R && count($R)>0){
				foreach($R as $B){
					
					$Q1 = $wpdb->prepare(
						"SELECT t.term_id, t.taxonomy, t.description, tt.name
						FROM {$wpdb->prefix}term_taxonomy AS t
						INNER JOIN {$wpdb->prefix}terms AS tt ON (tt.term_id = t.term_id )
						WHERE t.term_taxonomy_id=%d", $B->term_taxonomy_id
					);
					$R1 = $wpdb->get_results( $Q1);

					if( count($R1) == 0) continue;

					foreach($R1 as $C){
						$O = $wpdb->prepare("SELECT op.option_value FROM {$wpdb->prefix}options AS op WHERE op.option_name ='evo_et_taxonomy_%d'", $C->term_id);
						$O1 = $wpdb->get_results( $O);

						if($O1 && count($O1)>0) $OUT[$C->taxonomy][$B->term_taxonomy_id] = unserialize( $O1[0]->option_value );

						$OUT[$C->taxonomy][$B->term_taxonomy_id]['description'] = $C->description;
						$OUT[$C->taxonomy][$B->term_taxonomy_id]['name'] = $C->name;
					}					
				}
			}

			//print_r($OUT);
			$this->tax = $OUT;
			return $OUT;
		}

	// GENERAL GET
		function is_year_long(){
			return $this->check_yn('evo_year_long');
		}
		function is_month_long(){
			if($this->is_year_long()) return false; // 
			return $this->check_yn('_evo_month_long');
		}
		
		function is_featured(){	 return apply_filters('evodata_featured', $this->check_yn('_featured') , $this);		}
		function is_completed(){ return apply_filters('evodata_completed', $this->check_yn('_completed') , $this);		}
		function is_cancelled(){ 
			$S = $this->get_event_status();
			return $S == 'cancelled'? true:false;
		}
		function get_event_status(){
			$S = apply_filters('evodata_event_status', $this->get_prop('_status'), $this);

			if( $this->check_yn('_cancel') ) return 'cancelled';
			return $S? $S : 'scheduled';
		}
		function get_event_status_l18n($S=''){
			$A = $this->get_status_array();

			if(empty($S)) $S = $this->get_event_status();
			return isset($A[ $S ]) ? $A[ $S ]: $S;
		}
		function get_event_status_lang($S=''){
			$A = $this->get_status_array('front');

			if(empty($S)) $S = $this->get_event_status();
			return isset($A[ $S ]) ? $A[ $S ]: $S;
		}
		function get_status_reason(){
			$S = $this->get_event_status();

			if($S == 'scheduled') return false;
			if($S == 'cancelled') $S = 'cancel';
			return apply_filters('evodata_event_status_reason', $this->get_prop('_'. $S . '_reason'), '_'. $S . '_reason', $this);
		}

		function get_status_array($end = 'back'){
			return EVO()->cal->get_status_array( $end);
		}

		public function get_attendance_mode(){
			$AM = $this->get_prop('_attendance_mode');

			// if the event other settings say its online
			if($this->is_virtual() || $this->get_event_status() == 'movedonline'){
				if( $AM =='offline' || !$AM) $AM = 'online';
			}else{
				if( !$AM) $AM = 'offline';
			}			

			return $AM;
		}
		public function get_attendance_mode_lang($end = 'back'){
			$AM = $this->get_attendance_mode();

			$modes = EVO()->cal->get_attendance_modes($end);
			return $modes[ $AM ];
		}
		public function is_mixed_attendance(){
			$AM = $this->get_attendance_mode();
			return $AM == 'mixed' ? true: false;
		}

	// Virtual Event
		public function get_virtual_url(){
			return apply_filters('evodata_vir_url',$this->get_prop('_vir_url') , $this);
		}
		public function get_virtual_pass(){
			return apply_filters('evodata_vir_pass',$this->get_prop('_vir_pass') , $this);
		}
		function is_virtual(){
			if(!$this->check_yn('_virtual') ) return false;

			if( !$this->is_virtual_data_ready()) return false;
			return true;
		}
		// checks whether required virtual information is present -- @version 4.0.6
		public function is_virtual_data_ready(){
			$good = true;

			if( !$this->get_virtual_url() && !$this->get_prop('_vir_embed')) $good = false;
			return $good;
		}

		// if the event is virtual and physical
		function is_virtual_hybrid(){
			$AM = $this->get_attendance_mode();

			if( $AM == 'mixed'){
				return $this->is_virtual() ? true: false;
			}
			return false;
		}
		function virtual_type(){
			return $this->get_prop('_virtual_type');
		}
		function virtual_url(){
			$url = $this->get_virtual_url();
			if(!$url) return false;

			if( $this->check_yn('_vir_nohiding')) return $url;

			$event_link = get_the_permalink($this->event_id);
			$append = 'event_access';
			
			$event_link = $this->_process_link( $event_link, $append, 'var');			

			return $event_link;
		}
		function get_vir_url(){
			if(!$this->is_virtual()) return false;
			$url = $this->get_virtual_url();
			if(!$url) return false;

			$VT = $this->get_prop('_virtual_type');
			
			if($VT == 'youtube_live'){
				$url = (strpos($url, '/') === false)? 'https://www.youtube.com/channel/'. $url .'/live': $url;
			}

			return $url;
		}

		// @+ 2.9.2
		public function is_vir_after_content(){
			if(!$this->is_virtual()) return false;
			if( !$this->get_prop('_vir_after_content')) return false;

			$when = (int)$this->get_prop('_vir_after_content_when');
			$current_time = EVO()->calendar->get_current_time();

			$event_end_time = $this->get_event_time('end');

			if( $event_end_time + $when > $current_time ) return false;

			return $this->get_prop('_vir_after_content');
		}
		// check if virtual event has ended using event end time
		public function is_vir_event_ended(){
			return $this->is_past_event() ?  apply_filters('evodata_vir_ended', true, $this): false;			
		}
		
		public function record_mod_joined($joined = 'in'){
			return $this->set_prop('_mod_joined', $joined);
		}

		// if event is starting in 30 minutes
			public function is_event_starting_soon($time = 30){
				$current_time = EVO()->calendar->get_current_time();

				$event_start_time = $this->get_event_time('start');

				return $current_time < $event_start_time && $current_time >= ($event_start_time - ($time*60)) ? true : false;
			}


		// return jitsi json saved data
		// @+ 3.1
		public function get_jitsi_json($type ='guest'){
			
			$this->localize_edata('_evojitsi');

			$json = array();

			foreach(array('microphone', 'camera', 'closedcaptions', 
			        'fodeviceselection', 'hangup', 'profile', 
			        'livestreaming', 'etherpad', 'settings', 
			        'videoquality', 'filmstrip', 'feedback',  
			        'tileview', 'videobackgroundblur', 'download') as $g){
				$json[] = $g;
			}

			foreach(array(
				'_raise_hand','_sharedvideo','_recording','_mute-everyone','_shortcuts','_stats','_feedback','_desktop','_raise_hand','_invite','_fullscreen'
			) as $f){
				if($type == 'mod'){
					$json[] = substr( $f, 1);
					continue;
				}
				if( !$this->echeck_yn($f) ) continue;
				$json[] = substr( $f, 1);
			}

			return json_encode($json);
		}
	
	

	// EVENT DATA
		// @updated 2.9
		// localize meta_array_data (edata) for the event object to be used
		function localize_edata($meta_array_key = ''){	
			$this->load_meta_array( $meta_array_key );
		}
		function get_all_edata(){
			return $this->meta_array_data;
		}
		function get_eprop($field){
			return $this->get_array_meta( $field);
		}
		function echeck_yn($field){
			return $this->check_yn_array_meta( $field);
		}
		function set_eprop($field, $value, $update = true, $localize = false){
			$this->set_array_meta( $field, $value, '', $update);
			if($localize)	$this->localize_edata();
		}
		function save_eprops($meta_array_key = ''){
			$this->save_array_meta($meta_array_key);
		}
		function delete_eprop($field, $update = false){
			$this->delete_array_meta( $field, $this->meta_array_key, $update);
		}
		function del_mul_eprop($array, $update_meta = true){
			if(!is_array($array)) return false;

			foreach($array as $f){	$this->delete_eprop( $f );	}

			if($update_meta) $this->save_meta($this->ID, $this->meta_array_key, $this->meta_array_data);
		}

	// event post meta values
		private function set_event_data($meta_data = ''){
			if(array_key_exists('EVO_props', $GLOBALS) ){
				global $EVO_props;
				if(isset($EVO_props[$this->event_id])){
					$this->meta_data = $EVO_props[$this->event_id];
					return true;
				}				
			}

			// if meta data not passed, load meta
			if(empty($meta_data)){
				$this->load_all_meta();
			}else{
				// if meta data passed, set it for object meta
				$this->meta_data = $meta_data;
			}

			$GLOBALS['EVO_props'][$this->event_id] = $this->meta_data;
		}

		// update the local event data object with newly pulled values
		// @+2.6.13
		public function relocalize_event_data(){
			$this->meta_data =  $this->load_all_meta();
			$GLOBALS['EVO_props'][$this->event_id] = $this->meta_data;
		}
		public function reglobalize_event_data_from_local(){
			$GLOBALS['EVO_props'][$this->event_id] = $this->meta_data;
		}

		// pass event pmv value to private pmv and update globalized event PMV array 
		// @+2.6.11
		function globalize_event_pmv(){
			$GLOBALS['EVO_props'][$this->event_id] = $this->meta_data;
		}

		function get_data(){ return $this->meta_data;}
		
		// return null if the field is empty instead of false ver.2.8
		function get_prop_null($field){
			$F = $this->get_meta($field);
			return $F? $F: null; 
		}
		// return a sent value of the field if empty
		function get_prop_val($field, $val){
			$F = $this->get_meta($field);
			return $F? $F: $val; 
		}

		function set_prop($field, $value, $update = true, $update_obj = false){

			$this->set_meta( $field, $value, $update);

			// update the global event data with new property
			if($update_obj)	$this->reglobalize_event_data_from_local();
		}


		// v2.9
		function del_mul_prop($A){
			if(!is_array($A)) return false;
			foreach($A as $f) $this->del_prop( $f );
		}
		function set_global(){
			$data = array(	'id'=>$this->ID,'pmv'=>$this->meta_data	);
			$GLOBALS['EVO_Event'] = (object)$data;
		}
		// not initiated on load
		function get_event_post(){
			$this->load_post();
		}
		function get_start_unix(){	return (int)$this->get_prop('evcal_srow');	}
		function get_end_unix(){	return (int)$this->get_prop('evcal_erow');	}


	// LOCATION
		function is_hide_location_info(){
			//return EVO()->calendar->is_user_logged_in;
			$is_user_logged_in = EVO()->calendar->is_user_logged_in;

			$hide_location_info = false;
			$hide_location_info = ($this->check_yn('evo_access_control_location') && !$is_user_logged_in) ? true: false;

			$hide_location_info = (evo_settings_check_yn(EVO()->calendar->evopt1,'evo_hide_location') && !$is_user_logged_in)? true: $hide_location_info;
			return $hide_location_info;
		}
		function get_location_term_id($type='id'){ // @+2.8
			$location_terms = wp_get_post_terms($this->ID, 'event_location');
			if ( $location_terms && ! is_wp_error( $location_terms ) ){
				return ($type == 'id')? (int)$location_terms[0]->term_id: $location_terms[0];
			}
			return false;
		}
		public function get_location_data(){

			$location_term = apply_filters('evodata_location_term', $this->get_location_term_id('all'), $this);
			//$location_term = get_term( $location_term_id,'event_location' );

			if ( $location_term && ! is_wp_error( $location_term ) ){

				$output = array();

				$output['location_term_id'] = (int)$location_term->term_id;
				
				// check location term meta values on new and old
				$LocTermMeta = evo_get_term_meta( 'event_location', (int)$location_term->term_id);
				
				// location name
					$output['name'] = stripslashes( $location_term->name );
					$output['location_name'] = stripslashes( $location_term->name );

				// URL
					$output['location_url'] = get_term_link($location_term,'event_location');

				// description
					$output['location_description'] = !empty($location_term->description)? $location_term->description:'';

				// meta values
				foreach(array(
					'location_address','location_lat','location_lon',
					'location_img_id'=>'evo_loc_img',
					'location_link'=>'evcal_location_link',
					'location_city','location_state','location_country',
					'location_link_target'=>'evcal_location_link_target',
					'location_getdir_latlng',
					'location_type'
				) as $I=>$key){	
					$K = is_integer($I)? $key: $I;				
					$output[$K] = (empty($LocTermMeta[$key]))? '': $LocTermMeta[$key];
				}			

				// latlng
				if(!empty($output['location_lat']) && !empty($output['location_lon'])){
					$output['location_latlng'] = $output['location_lat'].','.$output['location_lon'];
				}	

				// link target
				if(empty($output['location_link_target'])) $output['location_link_target'] = 'no';

				return $output;
				
			}else{
				return false;
			}
		}
		public function get_location_name(){
			$location_term = wp_get_post_terms($this->ID, 'event_location', array('fields'=>'names'));
			
			if ( $location_term && ! is_wp_error( $location_term ) && is_array($location_term) ){

				return $location_term[0];
			}else{
				return false;
			}
		}

	// Organizer
		function get_organizer_term_id($type='id'){ // @+2.8
			$O_terms = wp_get_post_terms($this->ID, 'event_organizer');
			if ( $O_terms && ! is_wp_error( $O_terms ) ){
				return ($type == 'id')? (int)$O_terms[0]->term_id: $O_terms[0];
			}
			return false;
		}
		function get_organizer_data(){
			$O_term = apply_filters('evodata_organizer_term', $this->get_organizer_term_id('all'), $this);
			if($O_term && !is_wp_error( $O_term)){
				$R = array();

				$org_term_meta = evo_get_term_meta( 'event_organizer', (int)$O_term->term_id);
				
				$R['organizer'] = $O_term;
				$R['organizer_term'] = $O_term;
				$R['organizer_term_id'] = (int)$O_term->term_id;
				$R['organizer_name'] = $O_term->name;
				$R['organizer_description'] = $O_term->description;

				$organizer_meta = $this->get_organizer_social_meta_array();
				$organizer_meta['organizer_img_id'] = 'evo_org_img';
				$organizer_meta['organizer_contact'] = 'evcal_org_contact';
				$organizer_meta['organizer_address'] = 'evcal_org_address';
				$organizer_meta['organizer_link'] = 'evcal_org_exlink';
				$organizer_meta['organizer_link_target'] = '_evocal_org_exlink_target';

				// meta values
				foreach($organizer_meta as $I=>$key){	
					$K = is_integer($I)? $key: $I;				
					$R[$K] = (empty($org_term_meta[$key]))? '': $org_term_meta[$key];
				}

				return $R;
			}else{
				return false;
			}
		}
		function get_organizer_social_meta_array(){
			return apply_filters('evo_organizer_archive_page_social', array(
					'twitter'=>'evcal_org_tw',
					'facebook'=>'evcal_org_fb',
					'linkedin'=>'evcal_org_ln',
					'youtube'=>'evcal_org_yt'
				));
		}

	// Event color
	// @+ 3.0.7
		public function get_hex(){
			return apply_filters('evodata_hex', $this->get_prop('evcal_event_color'), $this);
		}

	// image data
	// updated 3.1.5
		public function get_image_id(){
			$_id = $this->get_prop('_thumbnail_id');
			if( $_id <= 0 ) $_id = false;
			return apply_filters('evodata_image', $_id , $this);
		}

		public function get_image_urls(){
			$id = $this->get_image_id();

			if(!$id){

				EVO()->cal->set_cur('evcal_1');
				
				// check to see if default image set
				if( EVO()->cal->check_yn('evcal_default_event_image_set') && EVO()->cal->get_prop('evcal_default_event_image')){

					$def_img_url = EVO()->cal->get_prop('evcal_default_event_image');
						
					$RR = array();
					foreach( apply_filters('evodata_image_sizes', array(
						'full','medium','thumbnail' ), $this
					) as $v){
						$out[ $v ] = $def_img_url;
					}

					return $RR;
				}
				return false;
			}

			if( empty($id)) return false;
			if( $id == 0) return false;

			$out = array();

			foreach( apply_filters('evodata_image_sizes', array(
				'full','medium','thumbnail' ), $this
			) as $v){
				$dd = wp_get_attachment_image_src( (int)$id, $v );
				if(empty($dd)) continue;
				$out[ $v ] = $dd[0];
				
				if($v == 'full'){
					$out[ $v .'_w' ] = $dd[1];
					$out[ $v .'_h' ] = $dd[2];
				}
			}

			$out[ 'id' ] = $id; // also include the image id

			return $out;
		}

	// Custom Field data
		function get_custom_data($index){
			return apply_filters('evodata_custom_data', array(
				'value'=> $this->get_prop("_evcal_ec_f".$index."a1_cus"),
				'valueL'=> $this->get_prop("_evcal_ec_f".$index."a1_cusL"),
				'target'=> $this->get_prop("_evcal_ec_f".$index."_onw"),
			), $this, $index);
		}

	// Single event JSON data
		function get_event_data_for_gmap( ){

			$evopt1 = EVO()->calendar->evopt1;

			$sin_event_evodata = apply_filters('evosin_evodata_vals',array(
				'mapformat'=> ((!empty($evopt1['evcal_gmap_format'])) ? $evopt1['evcal_gmap_format']:'roadmap'),
				'mapzoom'=> ( ( !empty($evopt1['evcal_gmap_zoomlevel']) ) ? $evopt1['evcal_gmap_zoomlevel']:'12' ),
				'mapscroll'=> ( !evo_settings_val('evcal_gmap_scroll' ,$evopt1)?'true':'false'),
				'evc_open'=>'yes',
				'mapiconurl'=> ( !empty($evopt1['evo_gmap_iconurl'])? $evopt1['evo_gmap_iconurl']:''),
				'maps_load'=> (!EVO()->calendar->google_maps_load ? 'yes':'no'),
			));
			return  $sin_event_evodata ;
		}

	// event field dynamic tag processing
	// added v 4.0.3
		public function process_dynamic_tags($VV){
			if( strpos($VV, '{') !== false){

				$DTT = new evo_datetime();

				if( strpos($VV, '{startdate}') !== false ){
					$VV = str_replace('{startdate}', 
						$DTT->get_readable_formatted_date( $this->start_unix, EVO()->calendar->date_format ),
						$VV );
				}
				if( strpos($VV, '{enddate}') !== false ){
					$VV = str_replace('{enddate}', 
						$DTT->get_readable_formatted_date( $this->end_unix, EVO()->calendar->date_format ),
						$VV );
				}
				if( strpos($VV, '{SD}') !== false ){
					$VV = str_replace('{SD}', 
						$DTT->get_readable_formatted_date( $this->start_unix, EVO()->calendar->date_format ),
						$VV );
				}
				if( strpos($VV, '{ED}') !== false ){
					$VV = str_replace('{ED}', 
						$DTT->get_readable_formatted_date( $this->end_unix, EVO()->calendar->date_format ),
						$VV );
				}
				if( strpos($VV, '{eventid}') !== false ){
					$VV = str_replace('{eventid}', 
						$this->ID,
						$VV );
				}
				if( strpos($VV, '{startunix}') !== false ){
					$VV = str_replace('{startunix}', 
						$this->start_unix,
						$VV );
				}if( strpos($VV, '{endunix}') !== false ){
					$VV = str_replace('{endunix}', 
						$this->end_unix,
						$VV );
				}
			}

			return $VV;
		}

	// supportive
		// process link
		function _process_link($event_link, $append, $var){
			$help = new evo_helper();

			return $help->process_link( $event_link, $var, $append);
		}


}