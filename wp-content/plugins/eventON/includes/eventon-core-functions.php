<?php
/**
 * EventON Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Functions
 * @version     4.1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// include core functions
require EVO_ABSPATH. 'includes/evo-conditional-functions.php';

// TAXONOMIES
	// check whether custom fields are activated and have values set ready
	function eventon_is_custom_meta_field_good($number, $opt=''){
		$opt = (!empty($opt))? $opt: get_option('evcal_options_evcal_1');
		return ( !empty($opt['evcal_af_'.$number]) 
			&& $opt['evcal_af_'.$number]=='yes'
			&& !empty($opt['evcal_ec_f'.$number.'a1']) 
			&& !empty($opt['evcal__fai_00c'.$number])  )? true: false;
	}

	// GET activated event type count
	function evo_verify_extra_ett($evopt=''){

		$evopt = (!empty($evopt))? $evopt: get_option('evcal_options_evcal_1');

		$count=array();
		for($x=3; $x <= evo_max_ett_count(); $x++ ){
			if(!empty($evopt['evcal_ett_'.$x]) && $evopt['evcal_ett_'.$x]=='yes'){
				$count[] = $x;
			}else{	break;	}
		}
		return $count;
	}
	// this return the count for each event type that are activated in accordance
	function evo_get_ett_count($evopt=''){
		$evopt = (!empty($evopt))? $evopt: get_option('evcal_options_evcal_1');

		$maxnum = evo_max_ett_count();
		$count=2;
		for($x=3; $x<= $maxnum; $x++ ){
			if(!empty($evopt['evcal_ett_'.$x]) && $evopt['evcal_ett_'.$x]=='yes'){
				$count = $x;
			}else{
				break;
			}
		}
		return $count;
	}
	// return the maximum allowed event type taxonomies
	function evo_max_ett_count(){
		return apply_filters('evo_event_type_count',5);
	}

	// this will return the count for custom meta data fields that are active
	function evo_calculate_cmd_count($evopt=''){
		$evopt = (!empty($evopt))? $evopt: get_option('evcal_options_evcal_1');

		$count=0;
		for($x=1; $x<evo_max_cmd_count(); $x++ ){
			if(!empty($evopt['evcal_af_'.$x]) && $evopt['evcal_af_'.$x]=='yes' && !empty($evopt['evcal_ec_f'.$x.'a1'])){
				$count = $x;
			}else{
				break;
			}
		}
		return $count;
	}
	function evo_retrieve_cmd_count($evopt=''){
		global $eventon;
		$opt = $eventon->frontend->evo_options;
		$evopt = (!empty($evopt))? $evopt: $opt;
		
		if(!empty($evopt['cmd_count']) && $evopt['cmd_count']==0){
			return $evopt['cmd_count'];
		}else{
			$new_c = evo_calculate_cmd_count($evopt);

			$evopt['cmd_count']=$new_c;
			//update_option('evcal_options_evcal_1', $evopt);

			return $new_c;
		}
	}
	// return maximum custom meta data field count for event
	// @version 2.3.11
	function evo_max_cmd_count(){
		return apply_filters('evo_max_cmd_count', 11);
	}


	// GET event type names
	function evo_get_ettNames($options=''){
		$output = array();

		$options = (!empty($options))? $options: get_option('evcal_options_evcal_1');
		for( $x=1; $x< (evo_get_ett_count($options)+1); $x++){
			$ab = ($x==1)? '':$x;
			$output[$x] = (!empty($options['evcal_eventt'.$ab]))? $options['evcal_eventt'.$ab]:'Event Type '.$ab;
		}
		return $output;
	}
	// updated @v4.1
	function evo_get_localized_ettNames($lang='', $options='', $options2=''){
		$output = array();

		$options = (!empty($options))? $options: EVO()->calendar->evopt1;
		$options2 = (!empty($options2))? $options2: EVO()->calendar->evopt2;
		
		if(!empty($lang)){
			$_lang_variation = $lang;
		}else{
			$shortcode_arg = EVO()->calendar->shortcode_args;
			$_lang_variation = (!empty($shortcode_arg['lang']))? $shortcode_arg['lang']:'L1';
		}

		
		// foreach event type upto activated event type categories
		for( $x=1; $x< (evo_get_ett_count($options)+1); $x++){
			$ab = ($x==1)? '':$x;

			$_tax_lang_field = 'evcal_lang_et'.$x;

			// check on eventon language values for saved name
			$lang_name = (!empty($options2[$_lang_variation][$_tax_lang_field]))? 
				stripslashes($options2[$_lang_variation][$_tax_lang_field]): null;

			// conditions
			if(!empty($lang_name)){
				$output[$x] = $lang_name;
			}else{
				$output[$x] = (!empty($options['evcal_eventt'.$ab]))? $options['evcal_eventt'.$ab]: __('Event Type','eventon').' '.$ab;
			}			
		}
		return $output;
	}

	// GET  event custom taxonomy field names
	function eventon_get_event_tax_name($tax, $options=''){
		$output ='';

		$options = (!empty($options))? $options: get_option('evcal_options_evcal_1');
		if($tax =='et'){
			$output = (!empty($options['evcal_eventt']))? $options['evcal_eventt']:'Event Type';
		}elseif($tax=='et2'){
			$output = (!empty($options['evcal_eventt2']))? $options['evcal_eventt2']:'Event Type 2';
		}
		return $output;
	}

	// GET  event custom taxonomy field names -- FOR FRONT END w/ Lang
	function eventon_get_event_tax_name_($tax, $lang='', $options='', $options2=''){
		$output ='';

		$options = (!empty($options))? $options: get_option('evcal_options_evcal_1');
		$options2 = (!empty($options2))? $options2: get_option('evcal_options_evcal_2');
		$_lang_variation = (!empty($lang))? $lang:'L1';

		$_tax = ($tax =='et')? 'evcal_eventt': 'evcal_eventt2';
		$_tax_lang_field = ($tax =='et')? 'evcal_lang_et1': 'evcal_lang_et2';


		// check for language first
		if(!empty($options2[$_lang_variation][$_tax_lang_field]) ){
			$output = stripslashes($options2[$_lang_variation][$_tax_lang_field]);
		
		// no lang value -> check set custom names
		}elseif(!empty($options[$_tax])) {		
			$output = $options[$_tax];
		}else{
			$output = ($tax =='et')? 'Event Type': 'Event Type 2';
		}

		return $output;
	}

	// taxonomy term meta functions
	// @version 2.4.7
	function evo_get_term_meta($tax, $termid, $options='', $secondarycheck= false){
		$termmetas = !empty($options)? $options: EVO()->calendar->get_tax_meta();

		if( empty($termmetas[$tax][$termid])){
			if($secondarycheck){
				$secondarymetas = get_option( "taxonomy_".$termid);
				return (!empty($secondarymetas)? $secondarymetas: false);
			}else{ return false;}
		} 
		return $termmetas[$tax][$termid];
	}
	function evo_save_term_metas($tax, $termid, $data, $options=''){
		if(empty($termid)) return false;
		if(!is_array($data) ) return false;
		
		$termmetas = !empty($options)? $options: get_option( "evo_tax_meta");
		
		if(!empty($termmetas) && is_array($termmetas) && !empty($termmetas[$tax][$termid])){
			$oldvals = $termmetas[$tax][$termid];
			$newvals = array_merge($oldvals, $data);
			$newvals = array_filter($newvals);
			$termmetas[$tax][$termid] = $newvals;
		}else{
			$termmetas[$tax][$termid] = $data;
		}
		return update_option('evo_tax_meta', $termmetas);
	}

// DATE & TIME
	// get string between { }
	function _evo_get_string_between($string, $start, $end){
	   	
	   	$translate = '';
	   	$constants = array();
		$s1 = explode('{', $string);


		foreach($s1 as $s2){
			$po = strpos($s2, '}');
			if( $po === false){
				$translate .= $s2;
			}else{
				$e1 = explode('}', $s2);
				$translate .= '{}'. $e1[1];
				$constants[] = $e1[0];
			}
		}

		if(empty($translate)) $translate = $string;

		return array(
			'constants'=>$constants,
			'translate'=>$translate
		);

	}


	// pretty time on event card
	function eventon_get_langed_pretty_time($unixtime, $dateformat){

		$rt = _evo_get_string_between($dateformat, '{','}');
		extract($rt);

		$datest = str_split($translate);

		
		// process
			$__output = '';
			$__new_dates = array();

			// full month name
			if(in_array('F', $datest)){
				$num = date('n', $unixtime);
				$_F = eventon_return_timely_names_('month_num_to_name',$num,'full');
				$__new_dates['F'] = $_F;
			}

			// 3 letter month name
			if(in_array('M', $datest)){
				$num = date('n', $unixtime);
				$_M = eventon_return_timely_names_('month_num_to_name',$num,'three');
				$__new_dates['M'] = $_M;
			}

			//full day name
			if(in_array('l', $datest)){
				$num = date('l', $unixtime);
				$_l = eventon_return_timely_names_('day',$num, 'full');
				$__new_dates['l'] = $_l;
			}

			//3 letter day name
			if(in_array('D', $datest)){
				$num = date('N', $unixtime);
				$_D = eventon_return_timely_names_('day_num_to_name',$num, 'three');
				$__new_dates['D'] = $_D;
			}

			// am pm values
			if(in_array('a', $datest)){
				$ampm_val = date('a', $unixtime);
				$_ampm = eventon_return_timely_names_('ampm',$ampm_val); 
				$__new_dates['a'] = $_ampm;
			}

		// process values
		$cn = 0;
		foreach($datest as $date_part){

			
			if($date_part == '}') continue;
			if( $date_part == '{'){
				$__output .= $constants[$cn];
				$cn++;
				continue;
			}
			
	
			if(is_array($__new_dates) && array_key_exists($date_part, $__new_dates)){
				$__output .= $__new_dates[$date_part];
			}else{
				$__output .= date($date_part, $unixtime);
			}
		}


		return $__output;
	}

	// RETURN: formatted event time in multiple formats
	function eventon_get_formatted_time($row_unix, $lang=''){
		/*
				D = Mon - Sun
			1	j = 1-31
				l = Sunday - Saturday
			3	N - day of week 1 (monday) -7(sunday)
				S - st, nd rd
			5	n - month 1-12
				F - January - Decemer
			7	t - number of days in month
				z - day of the year
			9	Y - 2000
				g = hours
			11	i = minute
				a = am/pm
			13	M = Jan - Dec
				m = 01-12
			15	d = 01-31
				H = hour 00 - 23
			17	A = AM/PM
				y = yea in 2 digits
				G = 24h format 0-23
		*/

		// validate
		if(empty($row_unix)) return false;

		$DD = new DateTime('now', EVO()->calendar->timezone0);
		$DD->setTimestamp( $row_unix);
				
		$key = array('D','j','l','N','S','n','F','t','z','Y','g','i','a','M','m','d','H', 'A', 'y','G');
		
		$date = explode('-',$DD->format( 'D-j-l-N-S-n-F-t-z-Y-g-i-a-M-m-d-H-A-y-G' ));
		
		foreach($date as $da=>$dv){
			// month name
			if($da==6){
				$output[$key[$da]]= eventon_return_timely_names_('month_num_to_name',$date[5]); 
			}else if($da==2){
				
				// day name - full day name
				$output[$key[$da]]= eventon_return_timely_names_('day',$date[2]); 
			
			// 3 letter month name
			}else if($da==13){
				$output[$key[$da]]= eventon_return_timely_names_('month_num_to_name',$date[5],'three'); 

			// 3 letter day name
			}else if($da==0){
				$output[$key[$da]]= eventon_return_timely_names_('day_num_to_name',$date[3],'three'); 
			}

			// am pm
			else if($da==12){				
				$output[$key[$da]]= eventon_return_timely_names_('ampm',$date[12]); 
			}else if( $da==17){				
				$output[$key[$da]]= eventon_return_timely_names_('ampm',$date[17]); 
			}else{
				$output[$key[$da]]= $dv;
			}
		}	
		//print_r($output);
		return $output;
	}

	/*	return date value and time values from unix timestamp */
	function eventon_get_editevent_kaalaya($unix, $dateformat='', $timeformat24=''){
				
		// in case of empty date format provided
		// find it within system
		$DT_format = eventon_get_timeNdate_format();
		

		$DD = new DateTime(null, EVO()->calendar->timezone0);
		$DD->setTimestamp( (int)$unix );

		$dateformat = (!empty($dateformat))? $dateformat: $DT_format[1];
		$timeformat24 = (!empty($timeformat24))? $timeformat24: $DT_format[2];
		
		$date = $DD->format( $dateformat );		
		
		$timestring = ($timeformat24)? 'H-i': 'g-i-A';
		$times_val = $DD->format( $timestring );
		$time_data = explode('-',$times_val);		
		
		$output = array_merge( array($date), $time_data);
		
		return $output;
	}

	/**
	 * GET event UNIX time from date and time format $_POST values
	 * @updated 4.0.6
	 */
	function eventon_get_unix_time($data='', $date_format='', $time_format=''){
		
		$help = new evo_helper();
		$postdata = $help->sanitize_array( $_POST );

		$data = (!empty($data))? $data : $postdata;

		$unix_s_0 = $unix_e_0 = '';
				
		// check if start and end time are present
		if(!empty($data['evcal_end_date']) && !empty($data['evcal_start_date'])){
			// END DATE
			$__evo_end_date =(empty($data['evcal_end_date']))?
				$data['evcal_start_date']: $data['evcal_end_date'];
			
			// date format
			$_wp_date_format = (!empty($date_format))? $date_format: 
				( (isset($postdata['_evo_date_format']))? $postdata['_evo_date_format']
					: get_option('date_format')
				);
			
			$_is_24h = (!empty($time_format) && $time_format=='24h')? true:
				( (isset($postdata['_evo_time_format']) && $postdata['_evo_time_format']=='24h')? 
					true: false
				); // get default site-wide date format
				
							
			$TZ = EVO()->calendar->timezone0;
			$DD = new DateTime( null, $TZ);

			// ---
			// START UNIX
			$unix_start =0;
			if( !empty($data['evcal_start_date']) ){
												
				$datetime = DateTime::createFromFormat( $_wp_date_format, $data['evcal_start_date'] , $TZ );
				//$unix_start = $datetime->format('U');

				if(!empty($data['evcal_start_time_hour'])){
					// hour conversion to 24h
					$time_hour = (int)$data['evcal_start_time_hour'];

					if( !empty($data['evcal_st_ampm']) ){

						$start_ampm = strtolower( $data['evcal_st_ampm'] );

						if( $start_ampm =='pm' && $time_hour != 12) $time_hour += 12; 
						if( $start_ampm =='pm' && $time_hour == 12) $time_hour = 12; 
						if( $start_ampm =='am' && $time_hour == 12) $time_hour = 0; 
						
					}

					$DD->setDate( $datetime->format('Y'), $datetime->format('m'), $datetime->format('d'))
					->setTime( $time_hour, $data['evcal_start_time_min']);				

					$unix_start = $DD->format('U');
				}
			}


			
			// ---
			// END TIME UNIX
			$unix_end =0;
			if( !empty($data['evcal_end_date']) ){				
				
				$datetime = DateTime::createFromFormat( $_wp_date_format, $__evo_end_date , $TZ );
				$unix_end = $datetime->format('U');

				if( !empty($data['evcal_end_time_hour'])  ){
					// hour conversion to 24h
					$time_hour = (int)$data['evcal_end_time_hour'];

					if( !empty($data['evcal_et_ampm']) ){

						$end_ampm = strtolower( $data['evcal_et_ampm'] );

						if( $end_ampm =='pm' && $time_hour != 12) $time_hour += 12; 
						if( $end_ampm =='pm' && $time_hour == 12) $time_hour = 12; 
						if( $end_ampm =='am' && $time_hour == 12) $time_hour = 0; 
					}

					$DD->setDate( $datetime->format('Y'), $datetime->format('m'), $datetime->format('d'))
						->setTime( $time_hour, $data['evcal_end_time_min']);
					
					$unix_end = $DD->format('U');						
				}	
			}


			$unix_end =(!empty($unix_end) )?$unix_end:$unix_start;

			// virtual end date - @4.0.3
			$unix_vir_end = $unix_end;
			if( !empty( $data['event_vir_date_x'])){
				
				$datetime = DateTime::createFromFormat( $_wp_date_format, $data['event_vir_date_x'] , $TZ );
				$unix_vir_end = $datetime->format('U');			

				if( !empty($data['_vir_hour'])  ){

					// hour conversion to 24h
					$time_hour = (int)$data['_vir_hour'];

					if( !empty($data['_vir_ampm']) ){
						if( $data['_vir_ampm']=='pm' && $time_hour != 12) $time_hour += 12; 
						if( $data['_vir_ampm']=='pm' && $time_hour == 12) $time_hour = 12; 
						if( $data['_vir_ampm']=='am' && $time_hour == 12) $time_hour = 0; 
					}

					$DD->setDate( $datetime->format('Y'), $datetime->format('m'), $datetime->format('d'))
						->setTime( $time_hour, $data['_vir_minute']);
					
					$unix_vir_end = $DD->format('U');						
				}
			}
			
		}else{
			// if no start or end present
			$unix_start = $unix_end = $unix_vir_end = time();
		}
		// output the unix timestamp
		$output = array(
			'unix_start'	=>$unix_start,
			'unix_end'		=>$unix_end,
			'unix_vir_end'	=> $unix_vir_end
		);	


		return $output;
	}

	function evo_date_parse_from_format($format, $date) {
	  	$dMask = array(
	    'H'=>'hour',
	    'i'=>'minute',
	    's'=>'second',
	    'y'=>'year',
	    'm'=>'month',
	    'd'=>'day'
	  	);
	  	$format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY); 
	  	$date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY); 
	  	foreach ($date as $k => $v) {
	    if ($dMask[$format[$k]]) $dt[$dMask[$format[$k]]] .= $v;
	  	}
	  	return $dt;
	}

	/*
	return jquery and HTML UNIVERSAL date format for the site
	@version 2.1.19
	*/
	function eventon_get_timeNdate_format($evcal_opt='', $force_wp_format = false){
		
		if(empty($evcal_opt))	$evcal_opt = EVO()->cal->get_op('evcal_1');		
		
		if( (!empty($evcal_opt) && $evcal_opt['evo_usewpdateformat']=='yes') || $force_wp_format){
					
			/** get date formate and convert to JQ datepicker format**/	
			$wp_date_format = get_option('date_format');
			$format_str = str_split($wp_date_format);
			
			foreach($format_str as $str){
				switch($str){							
					case 'j': $nstr = 'd'; break;
					case 'd': $nstr = 'dd'; break;	
					case 'D': $nstr = 'D'; break;	
					case 'l': $nstr = 'DD'; break;	
					case 'm': $nstr = 'mm'; break;
					case 'M': $nstr = 'M'; break;
					case 'n': $nstr = 'm'; break;
					case 'F': $nstr = 'MM'; break;							
					case 'Y': $nstr = 'yy'; break;
					case 'y': $nstr = 'y'; break;
					case 'S': $nstr = '-'; break;
											
					default :  $nstr = ''; break;							
				}
				$jq_date_format[] = (!empty($nstr))? ($nstr=='-'?'':$nstr) :$str;
				
			}
			$jq_date_format = implode('',$jq_date_format);
			$evo_date_format = $wp_date_format;
		}else{
			$jq_date_format ='yy/mm/dd';
			$evo_date_format = 'Y/m/d';
		}		
		
		// time format
		$wp_time_format = get_option('time_format');
		
		$hr24 = (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)?true:false;
		
		return array(
			$jq_date_format, 
			$evo_date_format,
			$hr24
		);
	}

	/*
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	function _evo_dateformat_PHP_to_jQueryUI($php_format){
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}

	// Convert moment time
	function evo_convert_php_moment($format){
		 $replacements = array(
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no 
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
       	);

       	$OP = '';
       	$LL = str_split($format);

       	$C = 0;
       	foreach($LL as $L){
       		// if previous character is \ skip this character
       		if( $C>1 && $LL[$C-1] == "\\"){
       			$OP .= $L; 
       			$C++;
       			continue;
       		}
       		if( array_key_exists($L, $replacements)){
       			$OP .= $replacements[$L];
       		}else{
       			$OP .= $L;
       		}
       		$C++;
       	}

       	return $OP;
       	//$momentFormat = strtr($format, $replacements);
    	//return $momentFormat;
	}

	// get single letter month names
	function eventon_get_oneL_months($lang_options){
		if(!empty($lang_options)) {$lang_options = $lang_options;}
		else{
			$opt = get_option('evcal_options_evcal_2');
			$lang_options = $opt && is_array($opt) ? $opt['L1']: array();
		}

		$__months = array('J','F','M','A','M','J','J','A','S','O','N','D');
		$count = 1;
		$output = array();

		foreach($__months as $month){
			$output[] = (!empty($lang_options['evo_lang_1Lm_'.$count]))? $lang_options['evo_lang_1Lm_'.$count]: $month;
			$count++;
		}
		return $output;
	}

	// get long month names
	function evo_get_long_month_names($lang_options){
		if(!empty($lang_options)) {$lang_options = $lang_options;}
		else{
			$opt = get_option('evcal_options_evcal_2');
			$lang_options = $opt && is_array($opt) ? $opt['L1']: array();
		}

		$__months = array('january','february','march','april','may','june','july','august','september','october','november','december');
		$count = 1;
		$output = array();

		foreach($__months as $month){
			$output[] = (!empty($lang_options['evcal_lang_'.$count]))? $lang_options['evcal_lang_'.$count]: $month;
			$count++;
		}
		return $output;
	}


	/*
	function to return day names and month names in correct language
	type: day, month, month_num_to_name, day_num_to_name
	*/
	function eventon_return_timely_names_($type, $data, $len='full', $lang=''){
		
		$eventon_day_names = array(
		1=>'monday','tuesday','wednesday','thursday','friday','saturday','sunday');
		$eventon_month_names = array(1=>'january','february','march','april','may','june','july','august','september','october','november','december');
		$eventon_ampm = array(1=>'am', 'pm');
				
		$output ='';
		
		// lower case the data values
		$original = $data;
		$data = strtolower($data);
		
		$evo_options = !empty(EVO()->calendar->evopt2)?
				EVO()->calendar->evopt2: get_option('evcal_options_evcal_2');
			$shortcode_arg = EVO()->calendar->shortcode_args;
		
		// check which language is called for
		$evo_options = (!empty($evo_options))? $evo_options: get_option('evcal_options_evcal_2');
		
		// check for language preference
		$_lang_variation = (!empty($lang))? $lang: evo_get_current_lang();
		//$_lang_variation = strtoupper($_lang_variation);

		// day name
		if($type=='day'){
			
			//global $eventon_day_names;
			$text_num = array_search($data, $eventon_day_names); // 1-7
					
			if($len=='full'){			
				
				$option_name_prefix = 'evcal_lang_day';
				$_not_value = $eventon_day_names[ $text_num];				
			
			// 3 letter day names
			}else if($len=='three'){
				
				$option_name_prefix = 'evo_lang_3Ld_';
				$_not_value = substr($eventon_day_names[ $text_num], 0 , 3);
			}
		
		// day number to name
		}else if($type=='day_num_to_name'){
		
			$text_num = $data; // 1-7
			
			if($len=='full'){	
				$option_name_prefix = 'evcal_lang_day';
				$_not_value = !empty($eventon_month_names[ $text_num])?
					$eventon_day_names[ $text_num]:'';
			
			// 3 letter day names
			}else if($len=='three'){				
				$option_name_prefix = 'evo_lang_3Ld_';
				$_not_value = substr($eventon_day_names[ $text_num], 0 , 3);	
			}
					
		// month names
		}else if($type=='month'){
			//global $eventon_month_names;
			$text_num = array_search($data, $eventon_month_names); // 1-12
			
			if($len == 'full'){
			
				$option_name_prefix = 'evcal_lang_';
				$_not_value = !empty($eventon_month_names[ $text_num])?
					$eventon_month_names[ $text_num]:'';
				
			}else if($len=='three'){
			
				$option_name_prefix = 'evo_lang_3Lm_';
				$_not_value = !empty($eventon_month_names[ $text_num])?
					substr($eventon_month_names[ $text_num], 0 , 3):'';
				
			}
		
		// month number to name
		}else if($type=='month_num_to_name'){
			
			//global $eventon_month_names;
			$text_num = $data; // 1-12
			
			if($len == 'full'){
				$option_name_prefix = 'evcal_lang_';
				$_not_value = !empty($eventon_month_names[ $text_num])? 
					$eventon_month_names[ $text_num]:'';

			}else if($len=='three'){
				$option_name_prefix = 'evo_lang_3Lm_';
				$_not_value = !empty($eventon_month_names[ $text_num])?
					substr($eventon_month_names[ $text_num], 0 , 3):'';
			}
		// am pm
		}else if($type=='ampm'){
			$text_num = $data; 
			
			$option_name_prefix = 'evo_lang_';
			$_not_value = $original;
		}
		
		$output = (!empty($evo_options[$_lang_variation][$option_name_prefix.$text_num]))? 
					$evo_options[$_lang_variation][$option_name_prefix.$text_num]
					: $_not_value;

		return $output;
	}
	

	function eventon_get_event_day_name($day_number){
		return eventon_return_timely_names_('day_num_to_name',$day_number);
	}

	// return month and year numbers from current month and difference
	function eventon_get_new_monthyear($current_month_number, $current_year, $difference){
		
		$month_num = $current_month_number + $difference;

		// /echo $current_month_number.' '.$month_num.' --';
		
		$count = ($difference>=0)? '+'.$difference: '-'.$difference;


		$time = mktime(0,0,0,$current_month_number,1,$current_year);
		$new_time = strtotime($count.'month ', $time);
		
		$new_time= explode('-',date('Y-n', $new_time));
		
		
		$ra = array(
			'month'=>$new_time[1], 'year'=>$new_time[0]
		);
		return $ra;
	}

	if( !function_exists ('ajde_evcal_formate_date')){
		function ajde_evcal_formate_date($date,$return_var){	
			$srt = strtotime($date);
			$f_date = date($return_var,$srt);
			return $f_date;
		}
	}

	// perform tasks to all past events
	// @updated 4.0.3
		function evo_process_to_past_events(){
			global $eventon;

			EVO()->cal->set_cur('evcal_1');

			// trash posts
			$trash = EVO()->cal->check_yn('evcal_move_trash');

			// mark as complete
			$completed = EVO()->cal->check_yn('evcal_mark_completed');

			// no options set
			if(!$completed && !$trash) return;


			$events = new WP_Query(array(
				'post_type'=>'ajde_events',
				'posts_per_page'=>-1
			));

			// no events
			if(!$events->have_posts()) return false;

			$rightnow = EVO()->calendar->utc_time;

			while($events->have_posts()): $events->the_post();
				$EV = new EVO_Event( $events->post->ID);
				$event_id = $EV->ID;

				if(!$event_id) continue;

				// trash repeat events when last repeat is past
				if( $EV->check_yn( 'evcal_repeat') ){

					$last_repeat = $EV->get_repeat_interval('last');

					if(!is_array($last_repeat)) continue;
					if( $last_repeat[1] >= $rightnow) continue;
				}

				// skip year and month long events
				if( $EV->check_yn( 'evo_year_long') ) continue;
				if( $EV->check_yn( '_evo_month_long') ) continue;
				
				$row_end = ( $EV->get_prop('evcal_erow') )? 
					$EV->get_prop('evcal_erow') :
					( $EV->get_prop('evcal_srow')? $EV->get_prop('evcal_srow') :false);

				if(!$row_end) continue;
				if($row_end >= $rightnow) continue;

				// Verify the post type - added 5/19/15
				$event = get_post($event_id, 'ARRAY_A');
				if($event['post_type']!='ajde_events') continue;
				

				// Auto Trash
				if($trash){
					do_action('evo_before_trashing_event', $event_id);

					$old_status = $event['post_status'];

					$event['post_status']='trash';
					wp_update_post($event);

					do_action('evo_after_trashing_event', $event_id);
				}

				// auto complete
				if($completed){
					$EV->set_prop('_completed','yes');
				}
												

			endwhile;
			wp_reset_postdata();
		}



	/* repeat interval generation when saving event post */
	// Updated: 4.0.3
		function eventon_get_repeat_intervals($unix_S, $unix_E){

			$help = new evo_helper();
			$postdata = $help->sanitize_array($_POST);

			// initial values
			$repeat_type = $postdata['evcal_rep_freq'];
			$repeat_count = (isset($postdata['evcal_rep_num']))? $postdata['evcal_rep_num']: 1;
			$repeat_gap = (isset($postdata['evcal_rep_gap']))? $postdata['evcal_rep_gap']: 1;
			
			$weekly_repeat_by = (isset($postdata['evp_repeat_rb_wk']))? $postdata['evp_repeat_rb_wk']: 'sing';
			$weekly_repeat_days = (isset($postdata['evo_rep_WKwk']))? $postdata['evo_rep_WKwk']: null;

			$monthly_repeat_by = (isset($postdata['evp_repeat_rb']))? $postdata['evp_repeat_rb']: 'dom';
			$monthly_wom = (isset($postdata['evo_repeat_wom']))? $postdata['evo_repeat_wom']: null;
			
			$errors = array();
			$data = '';

			// array data
			$day_names = array( 0=>"Sunday", 1=>"Monday", 2=>"Tue", 3=>"Wednesday", 4=>"Thursday", 5=>"Friday", 6=>"Saturday" );
			$week_names = array('1'=>"First",  '2'=>"Second", '3'=>"Third", '4'=>"Fourth", '5'=>"Fifth", '-1'=>"Last" );

			// event duraiton
			$event_duration = $unix_E - $unix_S;
			if($event_duration<0) $event_duration = 1;


			$repeat_intervals = array();

			// switch statement	
				$term = 'days';	
				switch($repeat_type){
					case 'daily':	$term = 'days';	break;
					case 'hourly':	$term = 'hours';	break;
					case 'monthly':	$term = 'month';	break;
					case 'yearly':	$term = 'year';	break;
					case 'weekly':	$term = 'week';	break;
					case 'custom':	$term = 'week';	break;
				}

			// custom repeating 
			if($repeat_type=='custom'&& !empty($postdata['repeat_intervals'])){
				
				$_post_repeat_intervals = $postdata['repeat_intervals'];

				// initials
				$_is_24h = (!empty($postdata['_evo_time_format']) && $postdata['_evo_time_format']=='24h')? true:false;
				$_wp_date_format = $postdata['_evo_date_format'];

				$DD = new DateTime(null, EVO()->calendar->timezone0);

				// make sure repeats are saved along with initial times for event
				$numberof_repeats = count($_post_repeat_intervals);
							
				$count = 0;
				// each repeat interval
				if($numberof_repeats>0){

					// create for each added 
					foreach($_post_repeat_intervals as $field => $interval){

						// initial repeat value
						if($field==0){
							if( $unix_S != $interval[0] &&	$unix_E != $interval[1]) continue;
						}
						
						// for intervals that were added as new
						if(isset($interval['type']) && isset($interval['type'])=='dates'){
							
							// a alternative default date format to be used
							$def_date_format =  strpos($interval[0], '/') === false? 'Y-m-d':'Y/m/d';

							$time_format = $_is_24h ? 'H:i':'g:ia';

							$datetime_start = DateTime::createFromFormat($def_date_format.' '. $time_format, $interval[0], EVO()->calendar->timezone0 );
							$datetime_end = DateTime::createFromFormat($def_date_format.' '. $time_format, $interval[1], EVO()->calendar->timezone0 );
							
							$repeat_intervals[] = array(
								$datetime_start->format('U'),$datetime_end->format('U')
							);
							$count .=$field.' ';
						}else{
							$count .=$field.' ';
							$repeat_intervals[] = array($interval[0],$interval[1]);
						}
					}// end foreach

				} // end if

				// append Initial event date values to repeat dates array
					if( $numberof_repeats == 0) $repeat_intervals[] = array($unix_S,$unix_E);

					// if event times are not in repeats array add them
					if( $numberof_repeats >0 ){

						$event_exists = false;
						foreach($repeat_intervals as $ris){
							if( $ris[0] == $unix_S && $ris[1] == $unix_E){
								$event_exists = true;
							}
						}
						if(!$event_exists){
							array_unshift($repeat_intervals, array($unix_S,$unix_E) );
						}
					}


				// filter to remove duplicates			
					$repeat_intervals = array_map('unserialize', array_unique(array_map('serialize', $repeat_intervals)));

				// sort repeating dates
				asort($repeat_intervals);

			}else{

				$DD = EVO()->calendar->DD;
				$DD->setTimestamp( $unix_S );

				// event hour and minutes based off event start time
				$event_hour = $DD->format('H');
				$event_min = $DD->format('i');


				// default week of month using event start date
				$def_wom = '1';
				if( empty($monthly_wom)){
					$DDD = clone $DD;
					$DDD->modify('first day of this month');

					$def_wom = intval($DD->format('W')) - intval($DDD->format('W')) ;
				}


				// default day of the week
				$def_dow = '1';
				if(empty($weekly_repeat_days)){
					$def_dow = $DD->format('w');
				}

				// for each repeat times
				$count = 1; $debug = '';
				for($x =0; $x < $repeat_count; $x++){


					// Reused variables
						$Names = array( 0=>"Sun", 1=>"Mon", 2=>"Tue", 3=>"Wed", 4=>"Thu", 5=>"Fri", 6=>"Sat" );
						$dif_s_e = $unix_E - $unix_S;

					// for day of week monthly repeats
					if($repeat_type == 'monthly' && $monthly_repeat_by=='dow' && isset($postdata['evo_rep_WK'])  ){
						
						// add original event time
						if( $x == 0) $repeat_intervals[ ] = array($unix_S, $unix_E);

						$DD->modify('first day of this month');

						if($x>0) $DD->modify("+{$repeat_gap} month");				
						

						// for each day of week
						$monthly_repeat_days = $postdata['evo_rep_WK'];					
						$monthly_repeat_days = is_array($monthly_repeat_days) ? $monthly_repeat_days : explode(',', $monthly_repeat_days);	


						if(!empty($monthly_repeat_days)){

							foreach($monthly_repeat_days as $d){

								if(empty($d)) continue;							

								$day_name = $day_names[ str_replace('_', '', $d)  ];

								// if week of month set
								if( !empty($monthly_wom)){

									$monthly_wom = is_array($monthly_wom)? $monthly_wom:  explode(',', $monthly_wom);									
									foreach($monthly_wom as $week_om){

										$DD->modify( $week_names[ $week_om ] .' '. $day_name. ' of '. $DD->format('F Y'));
										$DD->setTime( $event_hour, $event_min);
										$new_unix_S = $DD->format('U');

										// Add the new unix values
										if($new_unix_S < $unix_S) continue;
										
										$repeat_intervals[] = array( 
											$new_unix_S, 
											($new_unix_S + $event_duration) 
										);

										$debug .= $week_names[ $week_om ] .' '. $day_name. ' of '. $DD->format('F Y').'-'.$DD->format('Y-m-d').'/'. "{$x} {$week_om} {$d}".'//';
									}

								// no week of month set = default to first
								}else{

									$DD->modify( $week_names[ $def_wom ] .' '. $day_name. ' of '. $DD->format('F Y'));
									$DD->setTime( $event_hour, $event_min);

									$new_unix_S = $DD->format('U');

									// Add the new unix values
									if($new_unix_S < $unix_S) continue;
									if( !eventon_repeat_interval_exists($repeat_intervals,$new_unix_S, ($new_unix_S + $event_duration) ) )
										$repeat_intervals[] = array( $new_unix_S, ($new_unix_S + $event_duration) );

								}
							}
						}
						

					}elseif($repeat_type == 'weekly' && $weekly_repeat_by=='dow' ){

						// add original event time
						if( $x == 0) $repeat_intervals[] = array($unix_S, $unix_E);

						if($x>0) $DD->modify("+{$repeat_gap} week");


						// for each day of the week
						if(!empty($weekly_repeat_days) || $weekly_repeat_days == 0){
							
							$day_of_week = is_array($weekly_repeat_days) ? $weekly_repeat_days: explode(',', $weekly_repeat_days);
							
							foreach($day_of_week as $d){
								$day_name = $day_names[ str_replace('_', '', $d) ];

								$DD->modify( "{$day_name} this week");
								$DD->setTime( $event_hour, $event_min);
								

								$new_unix_S = $DD->format('U');

								// Add the new unix values
								if($new_unix_S < $unix_S) continue;
								if( !eventon_repeat_interval_exists($repeat_intervals,$new_unix_S, ($new_unix_S + $event_duration) ) )
									$repeat_intervals[] = array( $new_unix_S, ($new_unix_S + $event_duration) );
							}
						// day of the week not provided = use default from start unix
						}else{
							$day_name = $day_names[ $def_dow ];

							$DD->modify( "{$day_name} this week");
							$DD->setTime( $event_hour, $event_min);

							$new_unix_S = $DD->format('U');

							// Add the new unix values
							if($new_unix_S < $unix_S) continue;
							if( !eventon_repeat_interval_exists($repeat_intervals,$new_unix_S, ($new_unix_S + $event_duration) ) )
								$repeat_intervals[] = array( $new_unix_S, ($new_unix_S + $event_duration) );
						}

					
					// not month or weekly day of the week methods
					}else{

						// add original event times
						if( $x == 0) $repeat_intervals[] = array($unix_S, $unix_E);

						$DD->modify( "+{$repeat_gap} {$term}");

						$new_unix_S = $DD->format('U');

						// Add the new unix values
						if($new_unix_S < $unix_S) continue;
						if( !eventon_repeat_interval_exists($repeat_intervals,$new_unix_S, ($new_unix_S + $event_duration) ) )
							$repeat_intervals[] = array( $new_unix_S, ($new_unix_S + $event_duration) );

					}				
				}// each repeat count

				//update_post_meta(1,'aaa', $debug);
			}

			//echo $debug;
			//$data .= $DD->format("Y-m-j") ." {$x} /";	// debuging purpose
			//update_post_meta(580,'aa', $debug);
			//return array_merge($repeat_intervals, $errors);
			return _eventon_repeat_unique($repeat_intervals);
		}

	// debug
		function _eventon_debug_eventon_get_repeat_intervals($S, $E){
			$_POST = array(
				'evcal_rep_freq'=>'monthly',
				'evcal_rep_num'=>8,
				'evcal_rep_gap'=>1,
				'evp_repeat_rb'=>'dow',
				'evo_repeat_wom'=> -1,
				'evo_rep_WK'=>6
			);
			print_r(eventon_get_repeat_intervals($S, $E));
		}

	// check if exact same repeat interval doesnt exist 
	// @version 2.5
	function _eventon_repeat_unique($arr){
		
		$new_a = array();
		$keys = array();

		foreach($arr as $ind=>$D){

			$key = $D[0].'_'.$D[1];
			if( in_array($key, $keys)) continue;

			$keys[] = $key;

			$new_a[] = $D;
		}

		return $new_a;
	}

	function eventon_repeat_interval_exists($multiarray, $start, $end){
		foreach($multiarray as $repeat){
			if($repeat[0] == $start && $repeat[1] == $end) return true;
		}
		return false;
	}


// LANGUAGE
	// Get the current eventON language value
	function evo_get_current_lang(){
		$lang = 'L1';

		global $EVOLANG; 
		if(!empty($EVOLANG)) return $EVOLANG;
		if(!empty(EVO()->evo_generator->shortcode_args['lang'])) return EVO()->evo_generator->shortcode_args['lang'];


		if(!empty(EVO()->lang)) return EVO()->lang;

		return $lang;
	}

	// @+ 2.6.10
	function evo_set_global_lang($lang){
		$GLOBALS['EVOLANG'] = $lang;

		EVO()->lang = $lang; // @+ 2.6.11
	}

	/** return custom language text saved in settings **/
	// use evo_lang_get()
	function eventon_get_custom_language($evo_options, $field, $default_val, $lang=''){
			
		// check which language is called for
		$evo_options = (!empty($evo_options))? $evo_options: get_option('evcal_options_evcal_2');
		
		// check for language preference
		if(!empty($lang)){
			$_lang_variation = $lang;
		}else{
			$_lang_variation = evo_get_current_lang(); // @~2.6.10
		}
		
		$new_lang_val = (!empty($evo_options[$_lang_variation][$field]) )?
			($evo_options[$_lang_variation][$field]): 
			$default_val;
			
		return $new_lang_val;
	}

	function eventon_process_lang_options($options){
		$new_options = array();
		
		foreach($options as $f=>$v){
			$new_options[$f]= stripslashes($v);
		}
		return $new_options;
	}

	// @version 2.2.28 @updated 2.6.10
	// self sufficient language translattion
	// faster translation
		function evo_lang($text, $lang='', $language_options=''){
			
			$language_options = (!empty($language_options))? $language_options: EVO()->frontend->evo_lang_opt;
			$shortcode_arg = EVO()->evo_generator->shortcode_args;

			// conditional correct language 
			$lang = (!empty($lang))? $lang:
				(!empty(EVO()->lang) ? EVO()->lang:
					( !empty($shortcode_arg['lang'])? $shortcode_arg['lang']: 'L1')
				);


			$field_name = evo_lang_texttovar_filter($text);

			return !empty($language_options[$lang][$field_name])? $language_options[$lang][$field_name]:$text;
		}
	// this function with directly echo the values
		function evo_lang_e($text, $lang='', $language_options=''){
			echo evo_lang($text, $lang, $language_options='');
		}

	// Convert the text string for language into correct escapting variable name
		function evo_lang_texttovar_filter($text){
			$field_name = str_replace(' ', '-',  strtolower($text));
			$field_name = str_replace('.', '',  $field_name);
			$field_name = str_replace(':', '',  $field_name);
			$field_name = str_replace(',', '',  $field_name);
			$field_name = str_replace('[', '',  $field_name);
			$field_name = str_replace(']', '',  $field_name);
			return $field_name;
		}
	// get eventon language using variable
	// 2.3.16
		function evo_lang_get($var, $default, $lang='', $language_options=''){
			$language_options = (!empty($language_options))? $language_options: EVO()->frontend->evo_lang_opt;
			$shortcode_arg = EVO()->evo_generator->shortcode_args;
			
			// conditional correct language 
			$lang = (!empty($lang))? $lang:
				(!empty(EVO()->lang) ? EVO()->lang:
					( !empty($shortcode_arg['lang'])? $shortcode_arg['lang']: 'L1')
				);

				//echo $lang;
			$new_lang_val = (!empty($language_options[$lang][$var]) )?
				stripslashes($language_options[$lang][$var]): $default;				
			return $new_lang_val;
		}
		//@+2.8
		function evo_lang_get_e($V, $def, $L='', $OPT=''){
			echo evo_lang_get($V, $def, $L, $OPT);
		}


// CALENDAR PARTS
	/*
		RETURN calendar header with month and year data
		string - should be m, Y if empty
	*/
		function get_eventon_cal_title_month($month_number, $year_number, $lang=''){
			
			$evopt = get_option('evcal_options_evcal_1');
			
			$string = !empty($evopt['evcal_header_format'])? 
				$evopt['evcal_header_format']:'m, Y';

			$str = str_split($string, 1);
			$new_str = '';
			
			foreach($str as $st){
				switch($st){
					case 'm':
						$new_str.= eventon_return_timely_names_('month_num_to_name',$month_number, 'full', $lang);
						
					break;
					case 'Y':
						$new_str.= $year_number;
					break;
					case 'y':
						$new_str.= substr($year_number, -2);
					break;
					default:
						$new_str.= $st;
					break;
				}
			}		
			return $new_str;
		}

	if( !function_exists ('returnmonth')){
		function returnmonth($n){
			$timestamp = mktime(0,0,0,$n,1,2013);
			return date('F',$timestamp);
		}
	}
	if( !function_exists ('eventon_returnmonth_name_by_num')){
		function eventon_returnmonth_name_by_num($n){
			return eventon_return_timely_names_('month_num_to_name', $n);
		}
	}
	/*	eventON return font awesome icons names*/
	function get_eventON_icon($var, $default, $options_value=''){

		$options_value = (!empty($options_value))? $options_value: EVO()->cal->get_op('evcal_1');
		return (!empty( $options_value[$var]))? $options_value[$var] : $default;
	}
	// Return a excerpt of the event details
	function eventon_get_event_excerpt($text, $excerpt_length, $default_excerpt='', $title=true){
		global $eventon;
		
		$content='';
		
		if(empty($default_excerpt) ){
		
			$words = explode(' ', $text);

			if(count($words)> $excerpt_length)
				$words = array_slice($words, 0, $excerpt_length, true);

			$content = implode(' ', $words);
			$content = strip_shortcodes($content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);
		}else{
			$content = $default_excerpt;
		}		
		
		$titletx = ($title)? '<h3 class="padb5 evo_h3">' . eventon_get_custom_language($eventon->evo_generator->evopt2, 'evcal_evcard_details','Event Details').'</h3>':null;
		
		$content = '<div class="event_excerpt" style="display:none">'.$titletx.'<p>'. $content . '</p></div>';
		
		return $content;
	}	
	// @+2.8
	function eventon_get_normal_excerpt($string, $excerpt_length, $default_excerpt = ''){
		$content='';

		if(!empty($default_excerpt)) return $default_excerpt;

		$words = explode(' ', $string);

		if(count($words)> $excerpt_length)
			$words = array_slice($words, 0, $excerpt_length, true);

		$content = implode(' ', $words);

		$content = strip_shortcodes($content);
		$content = str_replace(']]>', ']]&gt;', $content);
		$content = strip_tags($content);

		return $content;
	}


// SORTING
	function cmp_esort_startdate($a, $b){
		return $a["event_start_unix"] - $b["event_start_unix"];
	}
	function cmp_esort_enddate($a, $b){
		return $a["event_end_unix"] - $b["event_end_unix"];
	}
	function cmp_esort_title($a, $b){
		return strcmp($a["event_title"], $b["event_title"]);
	}
	function cmp_esort_color($a, $b){
		return strcmp($a["event_color"], $b["event_color"]);
	}

// TEMPLATE
	/**
	 * Given a path, this will convert any of the subpaths into their corresponding tokens.
	 */
	function evo_tokenize_path( $path, $path_tokens ) {
		// Order most to least specific so that the token can encompass as much of the path as possible.
		uasort(
			$path_tokens,
			function ( $a, $b ) {
				$a = strlen( $a );
				$b = strlen( $b );

				if ( $a > $b ) {
					return -1;
				}

				if ( $b > $a ) {
					return 1;
				}

				return 0;
			}
		);

		foreach ( $path_tokens as $token => $token_path ) {
			if ( 0 !== strpos( $path, $token_path ) ) {
				continue;
			}

			$path = str_replace( $token_path, '{{' . $token . '}}', $path );
		}

		return $path;
	}

	/**
	 * Given a tokenized path, this will expand the tokens to their full path.
	 *
	 * @since 4.1.2
	 */
	function evo_untokenize_path( $path, $path_tokens ) {
		foreach ( $path_tokens as $token => $token_path ) {
			$path = str_replace( '{{' . $token . '}}', $token_path, $path );
		}
		return $path;
	}
	/**
	 * Fetches an array containing all of the configurable path constants to be used in tokenization.
	 *
	 * @return array The key is the define and the path is the constant.
	 */
	function evo_get_path_define_tokens() {
		$defines = array(
			'ABSPATH',
			'WP_CONTENT_DIR',
			'WP_PLUGIN_DIR',
			'WPMU_PLUGIN_DIR',
			'PLUGINDIR',
			'WP_THEME_DIR',
		);

		$path_tokens = array();
		foreach ( $defines as $define ) {
			if ( defined( $define ) ) {
				$path_tokens[ $define ] = constant( $define );
			}
		}

		return apply_filters( 'eventon_get_path_define_tokens', $path_tokens );
	}
	/** Get template part (for templates like the event-loop). */
	function evo_get_template_part( $slug, $name='', $preurl=''){
		eventon_get_template_part($slug, $name, $preurl);
	}
	function eventon_get_template_part( $slug, $name = '' , $preurl='') {
		$cache_key = sanitize_key( implode( '-', array( 'template-part', $slug, $name, EVO()->version ) ) );
		$template  = (string) wp_cache_get( $cache_key, 'eventon' );

				
		if($preurl){
			$template =$preurl."/{$slug}-{$name}.php";
		}

		if( !$template){
			if ( $name ) {
				$template = locate_template(
					array(
						"{$slug}-{$name}.php",
						EVO()->template_path() . "{$slug}-{$name}.php",
					)
				);

				if ( ! $template ) {
					$fallback = EVO()->plugin_path() . "/templates/{$slug}-{$name}.php";
					$template = file_exists( $fallback ) ? $fallback : '';
				}
			}

			if ( ! $template ) {
				// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/eventon/slug.php.
				$template = locate_template(
					array(
						"{$slug}.php",
						EVO()->template_path() . "{$slug}.php",
					)
				);
			}

			// Don't cache the absolute path so that it can be shared between web servers with different paths.
			$cache_path = evo_tokenize_path( $template, evo_get_path_define_tokens() );

			evo_set_template_cache( $cache_key, $cache_path );
		} else {
			// Make sure that the absolute path to the template is resolved.
			$template = evo_untokenize_path( $template, evo_get_path_define_tokens() );
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'evo_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}

		/*

		else{
			// Look in yourtheme/slug-name.php and yourtheme/eventon/slug-name.php
			if ( $name ){
				$childThemePath = get_stylesheet_directory();
				$template = locate_template( array ( 
					"{$slug}-{$name}.php", 
					TEMPLATEPATH."/". EVO()->template_url . $slug .'-'. $name .'.php',
					$childThemePath."/". EVO()->template_url . $slug .'-'. $name .'.php',
					"{EVO()->template_url}{$slug}-{$name}.php" )
				);
			}

			// Get default slug-name.php
			if ( !$template && $name && file_exists( AJDE_EVCAL_PATH . "/templates/{$slug}-{$name}.php" ) )
				$template = AJDE_EVCAL_PATH . "/templates/{$slug}-{$name}.php";

			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/eventon/slug.php
			if ( !$template )
				$template = locate_template( array ( "{$slug}.php", "{EVO()->template_url}{$slug}.php" ) );			
		}
		
		if ( $template )	load_template( $template, false );
		*/
	}

	/**
	 * Add a template to the template cache.
	 *
	 * @since 4.3.0
	 * @param string $cache_key Object cache key.
	 * @param string $template Located template.
	 */
	function evo_set_template_cache( $cache_key, $template ) {
		wp_cache_set( $cache_key, $template, 'eventon' );

		$cached_templates = wp_cache_get( 'cached_templates', 'eventon' );
		if ( is_array( $cached_templates ) ) {
			$cached_templates[] = $cache_key;
		} else {
			$cached_templates = array( $cache_key );
		}

		wp_cache_set( 'cached_templates', $cached_templates, 'eventon' );
	}

	/**
	 * Clear the template cache.
	 * @since 4.3.0
	 */
	function evo_clear_template_cache() {
		$cached_templates = wp_cache_get( 'cached_templates', 'eventon' );
		if ( is_array( $cached_templates ) ) {
			foreach ( $cached_templates as $cache_key ) {
				wp_cache_delete( $cache_key, 'eventon' );
			}

			wp_cache_delete( 'cached_templates', 'eventon' );
		}
	}

// GENERAL
	// GET EVENT
	function get_event($the_event){	global $eventon;}

	

	/* Initiate capabilities for eventON */
	function eventon_init_caps(){
		global $wp_roles;

		//print_r($wp_roles);
		
		if ( class_exists('WP_Roles') )
			if ( ! isset( $wp_roles ) )
				$wp_roles = new WP_Roles();
		
		$capabilities = eventon_get_core_capabilities();
		
		foreach( $capabilities as $cap_group ) {
			foreach( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	// for style values
	function eventon_styles($default, $field, $options){	
		return (!empty($options[$field]))? $options[$field]:$default;
	}
	// PAGING functions
	// return archive event page id set in previous version or in settigns
	function evo_get_event_page_id(){
		
		$page_id = EVO()->cal->get_prop('evo_event_archive_page_id', 'evcal_1');

		if($page_id){
		 	$id = $page_id;
		}else{
			$id = get_option('eventon_events_page_id');
			$id = !empty($id)? $id: false;
		}

		// check if this post exist
		if($id)	$id = (get_post_status( $id ))? $id: false;

		return $id;
	}
	// get event archive page template name
	function evo_get_event_template($opt){
		$opt == (!empty($opt))? $opt: evo_get_options('1');
		$ptemp = $opt['evo_event_archive_page_template'];

		if(empty($ptemp) || $ptemp=='archive-ajde_events.php' ){
		 	$template = 'archive-ajde_events.php';
		}else{
			$template =$ptemp;
		}
		return $template;
	}
	function evo_archive_page_content(){}

	// EVENT COLOR
		/** Return integer value for a hex color code **/
		function eventon_get_hex_val($color){
		    if ($color[0] == '#')
		        $color = substr($color, 1);

		    if (strlen($color) == 6)
		        list($r, $g, $b) = array($color[0].$color[1],
		                                 $color[2].$color[3],
		                                 $color[4].$color[5]);
		    elseif (strlen($color) == 3)
		        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		    else
		        return false;

		    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		    $val = (int)(($r+$g+$b)/3);			
		    return $val;
		}

		// get hex color in correct format (with #)
		function eventon_get_hex_color($pmv, $defaultColor='', $opt=''){

			$pure_hex_val = '';

			if(!empty($pmv['evcal_event_color'])){
				// remove #
				$pure_hex_val = str_replace('#','',$pmv['evcal_event_color'][0]);
				$pure_hex_val = substr($pure_hex_val, 0,6);
			}else{	// if there are no event colors saved

				if(!empty($defaultColor)){
					$pure_hex_val = $defaultColor;
				}else{
					$opt = (!empty($opt))? $opt: EVO()->calendar->evopt1;
					$pure_hex_val = ( !empty($opt['evcal_hexcode'])? $opt['evcal_hexcode']: '4bb5d8');
				}				
			}
			return '#'.$pure_hex_val;
		}


// SUPPORT FUNCTIONS
	

	// Login link
	// @since 2.6.5 / 3.1
		function evo_login_url($permalink =''){
			if( $link = EVO()->cal->get_prop('evo_login_link', 'evcal_1')) return $link;
			return wp_login_url($permalink);
		}

	// Link Related
		// convert link to acceptable link
  			function evo_format_link($url){

  				if( EVO()->cal->check_yn('evo_card_http_filter','evcal_1') ) return $url;

				$scheme = is_ssl() ? 'https' : 'http';
				
	            $url = str_replace(array('http:','https:'), '', $url);

	            if ( substr( $url, 0, 2 ) === '//' ){
	            	$url = $scheme. ':' . $url;
	            }else{
	            	$url = $scheme. '://' . $url;
	            }

	            return $url;
			}

	// Generate location latLon from address
		function eventon_get_latlon_from_address($address){
			
			$lat = $lon = '';

			// google maps API is required
			$gmap_api = EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1');
			if(!$gmap_api) return false;
			
			$address = str_replace(" ", "+", $address);
			$address = urlencode($address);
			
			// URL to call the cords
			$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$gmap_api;

			$response = wp_remote_get($url);

			$response = wp_remote_retrieve_body( $response );
			if(!$response) return false;

			$RR = json_decode($response);

			return array(
		        'lat' => $RR->results[0]->geometry->location->lat,
		        'lng' => $RR->results[0]->geometry->location->lng,
		    );
					    
		}
	// Returns a proper form of labeling for custom post type
	/** Function that returns an array containing the IDs of the products that are on sale. */
		if( !function_exists ('eventon_get_proper_labels')){
			function eventon_get_proper_labels($sin, $plu){
				return array(
				'name' => 			_x($plu, 'post type general name' , 'eventon'),
				'singular_name' => 	_x($sin, 'post type singular name' , 'eventon'),
				'add_new' => 		sprintf(__('Add New %s' , 'eventon'), $sin),
				'add_new_item' => 	sprintf(__('Add New %s' , 'eventon'), $sin),
				'edit_item' => 		sprintf(__('Edit %s', 'eventon'), $sin),
				'new_item' => 		sprintf(__('New %s' , 'eventon'), $sin),
				'all_items' => 		sprintf(__('All %s' , 'eventon'), $plu),
				'view_item' => 		sprintf(__('View %s' , 'eventon'), $sin),
				'search_items' => 	sprintf(__('Search %s' , 'eventon'), $plu),
				'not_found' =>  	sprintf(__('No %s found' , 'eventon'), $plu),
				'not_found_in_trash' => sprintf(__('No %s found in Trash' , 'eventon'), $plu), 
				'parent_item_colon' => '',
				'menu_name' => 		_x($plu, 'admin menu', 'eventon')
			  );
			}
		}
	
	/** Clean variables */
		function eventon_clean( $var ) {
			return sanitize_text_field( $var );
		}
	// Get capabilities for Eventon - these are assigned to admin during installation or reset
		function eventon_get_core_capabilities(){
			$capabilities = array();

			$capabilities['core'] = apply_filters('eventon_core_capabilities',array(
				"manage_eventon"
			));
			
			$capability_types = array( 'eventon' );

			foreach( $capability_types as $capability_type ) {

				$capabilities[ $capability_type ] = array(

					// Post type
					"publish_{$capability_type}",
					"publish_{$capability_type}s",
					"edit_{$capability_type}",
					"edit_{$capability_type}s",
					"edit_others_{$capability_type}s",
					"edit_private_{$capability_type}s",
					"edit_published_{$capability_type}s",

					"read_{$capability_type}s",
					"read_private_{$capability_type}s",
					"delete_{$capability_type}",
					"delete_{$capability_type}s",
					"delete_private_{$capability_type}s",
					"delete_published_{$capability_type}s",
					"delete_others_{$capability_type}s",					

					// Terms
					"assign_{$capability_type}_terms",
					"manage_{$capability_type}_terms",
					"edit_{$capability_type}_terms",
					"delete_{$capability_type}_terms",
					
					"upload_files"
				);
			}
			return $capabilities;
		}
	// currency codes for paypal
		function evo_get_currency_codes(){
			return array(
				'AED' => __( 'United Arab Emirates dirham', 'eventon' ),
				'AFN' => __( 'Afghan afghani', 'eventon' ),
				'ALL' => __( 'Albanian lek', 'eventon' ),
				'AMD' => __( 'Armenian dram', 'eventon' ),
				'ANG' => __( 'Netherlands Antillean guilder', 'eventon' ),
				'AOA' => __( 'Angolan kwanza', 'eventon' ),
				'ARS' => __( 'Argentine peso', 'eventon' ),
				'AUD' => __( 'Australian dollar', 'eventon' ),
				'AWG' => __( 'Aruban florin', 'eventon' ),
				'AZN' => __( 'Azerbaijani manat', 'eventon' ),
				'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'eventon' ),
				'BBD' => __( 'Barbadian dollar', 'eventon' ),
				'BDT' => __( 'Bangladeshi taka', 'eventon' ),
				'BGN' => __( 'Bulgarian lev', 'eventon' ),
				'BHD' => __( 'Bahraini dinar', 'eventon' ),
				'BIF' => __( 'Burundian franc', 'eventon' ),
				'BMD' => __( 'Bermudian dollar', 'eventon' ),
				'BND' => __( 'Brunei dollar', 'eventon' ),
				'BOB' => __( 'Bolivian boliviano', 'eventon' ),
				'BRL' => __( 'Brazilian real', 'eventon' ),
				'BSD' => __( 'Bahamian dollar', 'eventon' ),
				'BTC' => __( 'Bitcoin', 'eventon' ),
				'BTN' => __( 'Bhutanese ngultrum', 'eventon' ),
				'BWP' => __( 'Botswana pula', 'eventon' ),
				'BYR' => __( 'Belarusian ruble (old)', 'eventon' ),
				'BYN' => __( 'Belarusian ruble', 'eventon' ),
				'BZD' => __( 'Belize dollar', 'eventon' ),
				'CAD' => __( 'Canadian dollar', 'eventon' ),
				'CDF' => __( 'Congolese franc', 'eventon' ),
				'CHF' => __( 'Swiss franc', 'eventon' ),
				'CLP' => __( 'Chilean peso', 'eventon' ),
				'CNY' => __( 'Chinese yuan', 'eventon' ),
				'COP' => __( 'Colombian peso', 'eventon' ),
				'CRC' => __( 'Costa Rican col&oacute;n', 'eventon' ),
				'CUC' => __( 'Cuban convertible peso', 'eventon' ),
				'CUP' => __( 'Cuban peso', 'eventon' ),
				'CVE' => __( 'Cape Verdean escudo', 'eventon' ),
				'CZK' => __( 'Czech koruna', 'eventon' ),
				'DJF' => __( 'Djiboutian franc', 'eventon' ),
				'DKK' => __( 'Danish krone', 'eventon' ),
				'DOP' => __( 'Dominican peso', 'eventon' ),
				'DZD' => __( 'Algerian dinar', 'eventon' ),
				'EGP' => __( 'Egyptian pound', 'eventon' ),
				'ERN' => __( 'Eritrean nakfa', 'eventon' ),
				'ETB' => __( 'Ethiopian birr', 'eventon' ),
				'EUR' => __( 'Euro', 'eventon' ),
				'FJD' => __( 'Fijian dollar', 'eventon' ),
				'FKP' => __( 'Falkland Islands pound', 'eventon' ),
				'GBP' => __( 'Pound sterling', 'eventon' ),
				'GEL' => __( 'Georgian lari', 'eventon' ),
				'GGP' => __( 'Guernsey pound', 'eventon' ),
				'GHS' => __( 'Ghana cedi', 'eventon' ),
				'GIP' => __( 'Gibraltar pound', 'eventon' ),
				'GMD' => __( 'Gambian dalasi', 'eventon' ),
				'GNF' => __( 'Guinean franc', 'eventon' ),
				'GTQ' => __( 'Guatemalan quetzal', 'eventon' ),
				'GYD' => __( 'Guyanese dollar', 'eventon' ),
				'HKD' => __( 'Hong Kong dollar', 'eventon' ),
				'HNL' => __( 'Honduran lempira', 'eventon' ),
				'HRK' => __( 'Croatian kuna', 'eventon' ),
				'HTG' => __( 'Haitian gourde', 'eventon' ),
				'HUF' => __( 'Hungarian forint', 'eventon' ),
				'IDR' => __( 'Indonesian rupiah', 'eventon' ),
				'ILS' => __( 'Israeli new shekel', 'eventon' ),
				'IMP' => __( 'Manx pound', 'eventon' ),
				'INR' => __( 'Indian rupee', 'eventon' ),
				'IQD' => __( 'Iraqi dinar', 'eventon' ),
				'IRR' => __( 'Iranian rial', 'eventon' ),
				'IRT' => __( 'Iranian toman', 'eventon' ),
				'ISK' => __( 'Icelandic kr&oacute;na', 'eventon' ),
				'JEP' => __( 'Jersey pound', 'eventon' ),
				'JMD' => __( 'Jamaican dollar', 'eventon' ),
				'JOD' => __( 'Jordanian dinar', 'eventon' ),
				'JPY' => __( 'Japanese yen', 'eventon' ),
				'KES' => __( 'Kenyan shilling', 'eventon' ),
				'KGS' => __( 'Kyrgyzstani som', 'eventon' ),
				'KHR' => __( 'Cambodian riel', 'eventon' ),
				'KMF' => __( 'Comorian franc', 'eventon' ),
				'KPW' => __( 'North Korean won', 'eventon' ),
				'KRW' => __( 'South Korean won', 'eventon' ),
				'KWD' => __( 'Kuwaiti dinar', 'eventon' ),
				'KYD' => __( 'Cayman Islands dollar', 'eventon' ),
				'KZT' => __( 'Kazakhstani tenge', 'eventon' ),
				'LAK' => __( 'Lao kip', 'eventon' ),
				'LBP' => __( 'Lebanese pound', 'eventon' ),
				'LKR' => __( 'Sri Lankan rupee', 'eventon' ),
				'LRD' => __( 'Liberian dollar', 'eventon' ),
				'LSL' => __( 'Lesotho loti', 'eventon' ),
				'LYD' => __( 'Libyan dinar', 'eventon' ),
				'MAD' => __( 'Moroccan dirham', 'eventon' ),
				'MDL' => __( 'Moldovan leu', 'eventon' ),
				'MGA' => __( 'Malagasy ariary', 'eventon' ),
				'MKD' => __( 'Macedonian denar', 'eventon' ),
				'MMK' => __( 'Burmese kyat', 'eventon' ),
				'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'eventon' ),
				'MOP' => __( 'Macanese pataca', 'eventon' ),
				'MRO' => __( 'Mauritanian ouguiya', 'eventon' ),
				'MUR' => __( 'Mauritian rupee', 'eventon' ),
				'MVR' => __( 'Maldivian rufiyaa', 'eventon' ),
				'MWK' => __( 'Malawian kwacha', 'eventon' ),
				'MXN' => __( 'Mexican peso', 'eventon' ),
				'MYR' => __( 'Malaysian ringgit', 'eventon' ),
				'MZN' => __( 'Mozambican metical', 'eventon' ),
				'NAD' => __( 'Namibian dollar', 'eventon' ),
				'NGN' => __( 'Nigerian naira', 'eventon' ),
				'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'eventon' ),
				'NOK' => __( 'Norwegian krone', 'eventon' ),
				'NPR' => __( 'Nepalese rupee', 'eventon' ),
				'NZD' => __( 'New Zealand dollar', 'eventon' ),
				'OMR' => __( 'Omani rial', 'eventon' ),
				'PAB' => __( 'Panamanian balboa', 'eventon' ),
				'PEN' => __( 'Sol', 'eventon' ),
				'PGK' => __( 'Papua New Guinean kina', 'eventon' ),
				'PHP' => __( 'Philippine peso', 'eventon' ),
				'PKR' => __( 'Pakistani rupee', 'eventon' ),
				'PLN' => __( 'Polish z&#x142;oty', 'eventon' ),
				'PRB' => __( 'Transnistrian ruble', 'eventon' ),
				'PYG' => __( 'Paraguayan guaran&iacute;', 'eventon' ),
				'QAR' => __( 'Qatari riyal', 'eventon' ),
				'RON' => __( 'Romanian leu', 'eventon' ),
				'RSD' => __( 'Serbian dinar', 'eventon' ),
				'RUB' => __( 'Russian ruble', 'eventon' ),
				'RWF' => __( 'Rwandan franc', 'eventon' ),
				'SAR' => __( 'Saudi riyal', 'eventon' ),
				'SBD' => __( 'Solomon Islands dollar', 'eventon' ),
				'SCR' => __( 'Seychellois rupee', 'eventon' ),
				'SDG' => __( 'Sudanese pound', 'eventon' ),
				'SEK' => __( 'Swedish krona', 'eventon' ),
				'SGD' => __( 'Singapore dollar', 'eventon' ),
				'SHP' => __( 'Saint Helena pound', 'eventon' ),
				'SLL' => __( 'Sierra Leonean leone', 'eventon' ),
				'SOS' => __( 'Somali shilling', 'eventon' ),
				'SRD' => __( 'Surinamese dollar', 'eventon' ),
				'SSP' => __( 'South Sudanese pound', 'eventon' ),
				'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'eventon' ),
				'SYP' => __( 'Syrian pound', 'eventon' ),
				'SZL' => __( 'Swazi lilangeni', 'eventon' ),
				'THB' => __( 'Thai baht', 'eventon' ),
				'TJS' => __( 'Tajikistani somoni', 'eventon' ),
				'TMT' => __( 'Turkmenistan manat', 'eventon' ),
				'TND' => __( 'Tunisian dinar', 'eventon' ),
				'TOP' => __( 'Tongan pa&#x2bb;anga', 'eventon' ),
				'TRY' => __( 'Turkish lira', 'eventon' ),
				'TTD' => __( 'Trinidad and Tobago dollar', 'eventon' ),
				'TWD' => __( 'New Taiwan dollar', 'eventon' ),
				'TZS' => __( 'Tanzanian shilling', 'eventon' ),
				'UAH' => __( 'Ukrainian hryvnia', 'eventon' ),
				'UGX' => __( 'Ugandan shilling', 'eventon' ),
				'USD' => __( 'United States (US) dollar', 'eventon' ),
				'UYU' => __( 'Uruguayan peso', 'eventon' ),
				'UZS' => __( 'Uzbekistani som', 'eventon' ),
				'VEF' => __( 'Venezuelan bol&iacute;var', 'eventon' ),
				'VES' => __( 'Bol&iacute;var soberano', 'eventon' ),
				'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'eventon' ),
				'VUV' => __( 'Vanuatu vatu', 'eventon' ),
				'WST' => __( 'Samoan t&#x101;l&#x101;', 'eventon' ),
				'XAF' => __( 'Central African CFA franc', 'eventon' ),
				'XCD' => __( 'East Caribbean dollar', 'eventon' ),
				'XOF' => __( 'West African CFA franc', 'eventon' ),
				'XPF' => __( 'CFP franc', 'eventon' ),
				'YER' => __( 'Yemeni rial', 'eventon' ),
				'ZAR' => __( 'South African rand', 'eventon' ),
				'ZMW' => __( 'Zambian kwacha', 'eventon' ),
			);
		}


// DEPRECATING
	// GET time for ICS adjusted for unix
	// @deprecating moved to EVO_Event() v 2.6.7		
	function evo_get_adjusted_utc($unix, $sep= true){
		
		$datetime = new evo_datetime();
		
		$unix = $unix - $datetime->get_UTC_offset();

		if(!$sep) return $unix;
		
		$new_timeT = date("Ymd", $unix);
		$new_timeZ = date("Hi", $unix);
		return $new_timeT.'T'.$new_timeZ.'00Z';
	}

	// added 2.2.21
	// get eventon settings option values
		function get_evoOPT($num, $field){
			$opt = get_option('evcal_options_evcal_'.$num);
			return (!empty($opt[$field]))? $opt[$field]: false;
		}
		function save_evoOPT($num, $field, $value){
			$opt = get_option('evcal_options_evcal_'.$num);
			$opt_ar = (!empty($opt))? $opt: array();

			$opt_ar[$field]= $value;
			update_option('evcal_options_evcal_'.$num, $opt_ar);
		}
		// get the entire options array
		// @since 2.2.24
		function get_evoOPT_array($num=''){
			$num = !empty($num)? $num: 1;
			return get_option('evcal_options_evcal_'.$num);
		}		

		// @since v2.5.6
		function evo_get_option_val($field_name='', $options_key=''){
			if(empty($field_name)) return false;

			$options_key = !empty($options_key)? $options_key: 'evcal_options_evcal_1';
			$OPT = EVO()->evo_get_options($options_key);

			if(empty($OPT[$field_name])) return false;

			return stripslashes($OPT[$field_name]);
		}
		function evo_has_option_val($field_name='', $options_key=''){
			if(empty($field_name)) return false;

			$options_key = !empty($options_key)? $options_key: 'evcal_options_evcal_1';
			$OPT = EVO()->evo_get_options($options_key);

			if(empty($OPT[$field_name])) return false;

			return true;
		}

	// GET SAVED VALUES
	// meta value check and return
	function check_evo_meta($meta_array, $fieldname){
		return (!empty($meta_array[$fieldname]))? true:false;
	}
	function evo_meta($meta_array, $fieldname, $slashes=false){
		return (!empty($meta_array[$fieldname]))? 
			($slashes? stripcslashes($meta_array[$fieldname][0]): $meta_array[$fieldname][0])
			:null;
	}
	// updated @2.5.5
	function evo_meta_yesno($meta_array, $fieldname, $check_value='yes', $yes_value='yes', $no_value='no'){	
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname][0] == $check_value)? $yes_value:$no_value;
	}
	// added @2.5
	// get values from post meta field
		function evo_var_val($array, $field){
			return !empty($array[$field])? $array[$field][0] : null;
		}
	// @added 2.5.2
		function evo_settings_value($array, $field){
			return !empty($array[$field])? $array[$field] : false;
		}
	
	/**
	 * check wether meta field value is not empty and equal to yes
	 * @param  $meta_array array of post meta fields
	 * @param  $fieldname  field name as a string
	 * @return boolean   
	 * @since 2.2.20          
	 */
	function evo_check_yn($meta_array, $fieldname){
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname][0]=='yes')? true:false;
	}
	// @added 2.5
	function evo_settings_check_yn($meta_array, $fieldname){
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname]=='yes')? true:false;
	}
	// this will return true or false after checking if eventon settings value = yes
	function evo_settings_val($fieldname, $options, $not = false){
		if($not){
			return ( empty($options[$fieldname]) || (!empty($options[$fieldname]) && $options[$fieldname]=='no') )? true:false;
		}else{
			return ( is_array($options) && !empty($options[$fieldname]) && $options[$fieldname]=='yes' )? true:false;
		}
	}
	
	// get options for eventon settings
	//	tab ending = 1,2, etc. rs for rsvp
	// deprecating @2.9.2
	function evo_get_options($tab_ending){			
		return get_option('evcal_options_evcal_'.$tab_ending);
	}

	// if the calendar is set to hidden
	// @version 2.3.21
	function evo_cal_hidden(){
		global $eventon;

		$options = $eventon->frontend->evo_options;
		return (!empty($options['evcal_cal_hide']) && $options['evcal_cal_hide']=='yes')? true: false;
	}

	// get URL
	// get url with variables added
		function EVO_get_url($baseurl, $args){
			$str = '';
			foreach($args as $f=>$v){ $str .= $f.'='.$v. '&'; }
			if(strpos($baseurl, '?')!== false){
				return $baseurl.'&'.$str;
			}else{
				return $baseurl.'?'.$str;
			}
		}

	// create data attributes for HTML elements
	function EVO_get_data_attrs($array){
		$output = '';
		foreach($array as $key=>$val){
			$output .= 'data-'.$key.'="'.$val .'" ';
		}
		return $output;
	}

	if(!function_exists('date_parse_from_format')){
		function date_parse_from_format($_wp_format, $date){
			
			$date_pcs = preg_split('/ (?!.* )/',$_wp_format);
			$time_pcs = preg_split('/ (?!.* )/',$date);
			
			$_wp_date_str = preg_split("/[\s . , \: \- \/ ]/",$date_pcs[0]);
			$_ev_date_str = preg_split("/[\s . , \: \- \/ ]/",$time_pcs[0]);
			
			$check_array = array(
				'Y'=>'year',
				'y'=>'year',
				'm'=>'month',
				'n'=>'month',
				'M'=>'month',
				'F'=>'month',
				'd'=>'day',
				'j'=>'day',
				'D'=>'day',
				'l'=>'day',
			);
			
			foreach($_wp_date_str as $strk=>$str){
				
				if($str=='M' || $str=='F' ){
					$str_value = date('n', strtotime($_ev_date_str[$strk]));
				}else{
					$str_value=$_ev_date_str[$strk];
				}
				
				if(!empty($str) )
					$ar[ $check_array[$str] ]=$str_value;		
				
			}
			
			$ar['hour']= date('H', strtotime($time_pcs[1]));
			$ar['minute']= date('i', strtotime($time_pcs[1]));			
			
			return $ar;
		}
	}

	if( !function_exists('date_parse_from_format') ){
		function date_parse_from_format($format, $date) {
		  $dMask = array(
			'H'=>'hour',
			'i'=>'minute',
			's'=>'second',
			'y'=>'year',
			'm'=>'month',
			'd'=>'day'
		  );
		  $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY); 
		  $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY); 
		  foreach ($date as $k => $v) {
			if ($dMask[$format[$k]]) $dt[$dMask[$format[$k]]] .= $v;
		  }
		  return $dt;
		}
	}

// DEPRECATING end

	

?>