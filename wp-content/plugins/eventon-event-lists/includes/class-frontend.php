<?php
/**
 * Event Lists Ext. Addon Front end
 * @version 1.0
 */
class evoel_frontend{

	private $shortcode_atts = array();

	public function __construct(){
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action('evo_addon_styles', array($this, 'styles') );
		add_filter('evo_cal_above_header_btn', array($this,'above_header'),10,2);

		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);

		add_filter('evo_generate_events_filter_proceed', array($this, 'generate_events_filter_proceed'), 10,2);
		add_filter('evo_generate_events_results', array($this, 'generate_events'), 10,3);
		add_filter('eventon_ajax_arguments', array($this, 'ajax_arguments'), 10,3);

	}
	
	// INIT CAL
		public function getCAL($atts, $type=''){
			// INIT
			EVOEL()->is_running_el = true;
			$this->only_el_actions();
			add_filter('eventon_shortcode_defaults', array(EVOEL()->shortcodes,'add_shortcode_defaults'), 10, 1);
			$atts['calendar_type'] = 'el';

			// shortcode pre process			
				if(!isset($atts['show_limit']))	$atts['show_limit'] = 'no';
				if(!isset($atts['sep_month']))	$atts['sep_month'] = 'no';
				if(!isset($atts['event_count_list']))	$atts['event_count_list'] = 'no';
				if(!isset($atts['show_limit_ajax']))	$atts['show_limit_ajax'] = 'no';

				// conditioning
				if($atts['sep_month']=='yes')	$atts['show_limit_ajax'] =  'no';
				if( $atts['sep_month'] == 'no' && $atts['event_count_list'] == 'no') $atts['show_limit']='no';
				if( $atts['sep_month'] == 'yes' && $atts['event_count_list'] == 'yes'&& $atts['show_limit']=='yes')
					$atts['show_limit_ajax'] =  'yes';


			// HEADER
				$O = EVO()->calendar->_get_initial_calendar( $atts , array(
					'date_header'=>false,
					'sort_bar'=>true,
					'header_title'=> (isset($atts['el_title'])? $atts['el_title']:''),
					'_classes_evcal_list'=> ' el_cal',
				));	

			// CLOSE
				EVOEL()->is_running_el = false;
				$this->remove_only_el_actions();
				remove_filter('eventon_shortcode_defaults', array(EVOEL()->shortcodes,'add_shortcode_defaults'));

			return $O;			
		}

	// BEFORE INIT - set date range correctly
		function evo_init_ajax_before(){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'el') return;


			$DD = new DateTime();
			$DD->setTimezone( EVO()->calendar->timezone0 );	

			$current_timestamp = EVO()->calendar->current_time;

			// Calendar Date range calculation			
			// CUT OFF time calculation
				if($SC['el_type'] != 'dr'){ // not date range
					//fixed time list
					if(!empty($SC['pec']) && $SC['pec']=='ft'){
						$_D = (!empty($SC['fixed_date']))? $SC['fixed_date']:date("j", $current_timestamp);
						$_M = (!empty($SC['fixed_month']))? $SC['fixed_month']:date("m", $current_timestamp);
						$_Y = (!empty($SC['fixed_year']))? $SC['fixed_year']:date("Y", $current_timestamp);

						$DD->setDate($_Y, $_M, $_D);
						$DD->setTime(0,0,0);

					// current date cd
					}else if(!empty($SC['pec']) && $SC['pec']=='cd'){
						$DD->setTimestamp( EVO()->calendar->current_time );
						$DD->setTime(0,0,0);
					// current time - ct
					}else{
						$DD->setTimestamp( EVO()->calendar->current_time );
					}

					// reset arguments
					$SC['fixed_date']= $SC['fixed_month']= $SC['fixed_year']='';
				}
				// restrained time unix
					$number_of_months = (!empty($SC['number_of_months']))? (int)($SC['number_of_months']):1;
					$month_dif = ($SC['el_type']=='ue')? '+':'-';					

			// upcoming events list 
				if($SC['el_type']=='ue'){

					$__focus_start_date_range = $DD->format('U');

					$DD->modify('+'.( (int)$number_of_months - 1 ).' months');
					$DD->modify('last day of this month');
					$DD->setTime(23,59,59);

					$__focus_end_date_range =  $DD->format('U');
				
			// date range list
				}elseif($SC['el_type'] == 'dr'){

					$start = !empty($SC['start_range'])? strtolower($SC['start_range']): false;
					$end = !empty($SC['end_range'])? strtolower($SC['end_range']): false;


					// START TIME
					if(strpos($start, '/') !== false){
						$__focus_start_date_range = strtotime($start);
					}elseif($start=='today'){
						$__focus_start_date_range = strtotime( date("m/j/Y", $current_timestamp).'00:00:00' );
					}elseif($end=='rightnow'){
						$__focus_start_date_range = $current_timestamp;
					}else{
						$__focus_start_date_range = $current_timestamp;
					}

					// +/- days & +/- months
						if(strpos($start, 'days') !== false || strpos($start, 'months') !== false){
							$__focus_start_date_range = strtotime($start, $__focus_start_date_range);
						}

					// END TIME
					if(strpos($end, '/') !== false){
						$__focus_end_date_range = strtotime($end);
					}elseif($end=='today'){
						$__focus_end_date_range = strtotime( date("m/j/Y", $current_timestamp).' 23:59:59' );
					}elseif($end=='rightnow'){
						$__focus_end_date_range = $current_timestamp;
					}else{
						$__focus_end_date_range = strtotime('+1 month', $current_timestamp);
					}

					// +/- days & +/- months
						if(strpos($end, 'days') !== false || strpos($end, 'months') !== false){
							$__focus_end_date_range = strtotime($end, $current_timestamp);
						}

					// calculate number of months
						if( $number_of_months > 1){
							$__focus_end_date_range = strtotime(
								'+'.( (int)$number_of_months ).' months', 
								$__focus_start_date_range);
							$SC['number_of_months'] = (int)$number_of_months;

						// calculate number of months based on end range time
						}else{
							$_unix_between_range = $__focus_start_date_range - $__focus_end_date_range;						
							$SC['number_of_months'] = ((int)abs( $_unix_between_range/(60*60*24*30)) ) +1;
							if($SC['number_of_months'] == 0) $SC['number_of_months'] = 1;
						}



			// past events list -- el_type = pe
				}else{
					if(empty($SC['event_order'])) $SC['event_order']='DESC';

					$SC['hide_past']='no';
					
					$__focus_end_date_range =  $DD->format('U');


					$DD->modify('-'.$number_of_months.' months');
					$DD->modify('first day of this month');
					$DD->setTime(0,0,0);

					$__focus_start_date_range = $DD->format('U');


					// calculate number of months
					$min_date = min($__focus_start_date_range, $__focus_end_date_range);
					$max_date = max($__focus_start_date_range, $__focus_end_date_range);
					$i = 1;
					while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
					    $i++;
					}
					$SC['number_of_months'] = $i;

				}

			//echo 'from '.date('Y-m-d H/i/s', $__focus_start_date_range) .' to'. date('Y-m-d H/i/s', $__focus_end_date_range);

			$SC['focus_start_date_range'] = $__focus_start_date_range;	
			$SC['focus_end_date_range'] = $__focus_end_date_range;
			
			EVO()->calendar->update_shortcode_arguments($SC);

		}

	// Process ajax arguments for loading events
		function ajax_arguments($SC, $POST, $ajaxtype){
			if($SC['calendar_type'] != 'el') return $SC;

			return $SC;
		}

	// GENERATE Events Filter
		function generate_events_filter_proceed($BOOL, $SC){
			if($SC['calendar_type'] != 'el') return $BOOL;

			//if($SC['cal_init_nonajax'] == 'yes') return $BOOL;
			return false;
		}
		
		function generate_events($A, $event_list_array_raw, $calO){


			$EL = $A['raw_el'];
			$SC = EVO()->calendar->shortcode_args;
			extract($SC);

			$month_order = !empty($month_order)? $month_order: 'ASC';

			if($SC['calendar_type'] != 'el') return $A;
			if($number_of_months == 1) return $A;
			
			$DD = new DateTime();
			$DD->setTimezone( EVO()->calendar->timezone0 );
			$DD->setTimestamp( $month_order == 'ASC'? (int)$focus_start_date_range: (int)$focus_end_date_range);


			$content = '';
			$_EC = 0; // event count for all calendar
			$_ECM = 0; // event count for the month
			$_ESM = array(); // events for all calendar

			if($month_order == 'DESC') $number_of_months++;

			if($month_order == 'NO'){
				// EVENTS
				foreach($EL as $event){

					// hide multiple occur filter
					if($hide_mult_occur == 'yes' && array_key_exists($event['_ID'], $_ESM) ) continue;
					
					// count limit
					if( $event_count_list == 'yes'){ // per calendar
						if($show_limit == 'no' &&  $_EC == $event_count) continue;
						if($show_limit == 'yes' && $sep_month == 'yes' &&  $_EC == $event_count) continue;
						
					}else{ // per month
						// if event count per month met
						if($show_limit == 'no' && $event_count >0 && $_ECM >= $event_count) continue; 
					}

					$_ES[ $event['_ID'] ] = $_ESM[$event['_ID']] = $event;
					$_EC++; $_ECM++;
				}

				// if event count does not apply to all 
				if( $event_count_list == 'yes' && $sep_month == 'no'){
				}else{
					$_ES = EVO()->calendar->generate_event_data( $_ES );	
					$_HTML = EVO()->calendar->filtering->no_more_events_add($_ES);

					$content .= $_HTML;	
				}	
			}else{
				// ordered months


				// EACH MONTH			
				for($x=0; $x< $number_of_months ; $x++){


					// if count per cal met
					if($event_count_list == 'yes' && $show_limit == 'no' && $event_count != 0 && $_EC == $event_count) continue;


					// each month event count
					$_ECM = 0;

					// date range setup
						if($x>0){
							$DD->modify('first day of this month');
							$DD->setTime(0,0,0);
							$DD->modify( $month_order == 'ASC'? '+1 month':'-1 month');
						}

						if($month_order == 'DESC'){
							if($x==0){
								$DD->modify('first day of this month');
								$DD->setTime(0,0,0);
							}
						}

						$SU = $DD->format('U');
						$_m = $DD->format('n');
						$_y = $DD->format('Y');
							
						$DD->modify('last day of this month');

						$DD->setTime(23,59,59);	

						if($month_order == 'DESC'){
							// first month last day is end date range
							if($x==0){
								$DD->setTimestamp( (int)$focus_end_date_range);
							}
						}else{ // ASC jan, Feb
							if( $x+1 == $number_of_months){ // last month in list
								$DD->setTimestamp( (int)$focus_end_date_range);
							}
						}			

						
						
						$EU = $DD->format('U');				

						$_html = ''; $_ES = array();
					
					// EVENTS
					foreach($EL as $event){

						// hide multiple occur filter
						if($hide_mult_occur == 'yes' && array_key_exists($event['_ID'], $_ESM) ) continue;
						
						// count limit
						if( $event_count_list == 'yes' && $event_count != 0){ // per calendar
							if($show_limit == 'no' &&  $_EC == $event_count) continue;
							if($show_limit == 'yes' && $sep_month == 'yes' &&  $_EC == $event_count) continue;
							
						}else{ // per month
							// if event count per month met
							if($show_limit == 'no' && $event_count >0 && $_ECM >= $event_count) continue; 
						}

						
						// in range
						if(EVO()->calendar->shell->is_in_range(
							$SU, $EU,  (int)$event['event_start_unix'] , (int)$event['event_end_unix'] 
						)){

							$_ES[ $event['_ID'] ] = $_ESM[$event['_ID']] = $event;
							$_EC++; $_ECM++;
						}
					}


					// count for all cal with show more
					if($event_count_list == 'yes' && $show_limit == 'yes' ){
						if($sep_month == 'no'){
							continue;
						}else{
							if($_ECM == 0) continue;
						}
					} 


					// hide empty month filter				
					if( $hide_empty_months == 'yes' && $_ECM == 0) continue;

					// if event count does not apply to all 
					if( $event_count_list == 'yes' && $sep_month == 'no'){
					}else{
						$_ES = EVO()->calendar->generate_event_data( $_ES );	
						$_HTML = EVO()->calendar->filtering->no_more_events_add($_ES);

						//$G = date('Y-m-d', $SU).'/'. date('Y-m-d', $EU);
						if( $sep_month == 'yes'){
							$content.= "<div class='evcal_month_line' data-d='eml_{$_m}_{$_y}'><p>".eventon_returnmonth_name_by_num($_m).($show_year == 'yes'? ' '.$_y:'') ."</p></div>";
							$content.= "<div class='sep_month_events". ($_EC==0?' no_event':''). "' data-d='eml_{$_m}_{$_y}'>";
							$content .= $_HTML;
							$content.= "</div>";
						}else{
							$content .= $_HTML;					
						}	
					}		
				}

			}

			// if event count to apply to entire calendar and not separating months
			if($event_count_list == 'yes' && $sep_month == 'no'){
				$_ES = EVO()->calendar->filtering->move_ft_to_top( $_ESM);
				$_ES = EVO()->calendar->generate_event_data( $_ES );	
				$content .= EVO()->calendar->filtering->no_more_events_add($_ES);
			}

			// when there are no events in entire calendar
			if( $_EC == 0 ){
				$content = "<div class='eventon_list_event no_events'><p class='no_events' >".EVO()->calendar->lang_array['no_event']."</p></div>";
			}

			// show more button for all events for the calendar
			if($event_count_list == 'yes' && $show_limit == 'yes' ){
				if($sep_month == 'no'){
					$_ES = EVO()->calendar->generate_event_data( $_ESM );
					$content = EVO()->calendar->filtering->no_more_events_add($_ES);
				}				
			}	


			return array(
				'html' => $content,
				'data' => $EL
			);

		}
	
	// remove go to today from event list which is not applicatable
		function above_header($array, $args){
			if(isset($args['cal_type']) && $args['cal_type'] == 'el'){ 
				unset($array['evo-gototoday-btn']);
			}
			return $array;
		}

	//	STYLES
		function styles(){
			ob_start();
			include_once(EVOEL()->plugin_path.'/assets/el_styles.css');
			echo ob_get_clean();
		}
		public function register_styles_scripts(){
			wp_register_style( 'evo_el_styles',EVOEL()->assets_path.'el_styles.css');
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));					
		}
		function print_styles(){
			wp_enqueue_style( 'evo_el_styles');	
		}
	// SUPPROT FUNCTIONS
		// ONLY for el calendar actions 
		public function only_el_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_el_actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoEL';
			return $name;
		}
		// add class name to calendar header for DV
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoEL', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}		
}