<?php
/** 
 *	Calendar WP Data Querying
 *	@version 3.0
 */

class Evo_Calendar_Query{



// not using
function query_db($wp_args){

	$SC = $this->shortcode_args;
	extract($SC);

	$event_list_array = $featured_events = array();

	$wp_arguments= (!empty($wp_arguments))?$wp_arguments: $this->wp_arguments;			
	$is_user_logged_in = is_user_logged_in();

	// RUN WP_QUERY
	global $wpdb;
	$ev = $wpdb->get_results( $wpdb->prepare(
		"SELECT   wp_posts.* FROM wp_posts  WHERE 1=1  AND wp_posts.post_type = 'ajde_events' AND ((wp_posts.post_status = 'publish'))  ORDER BY wp_posts.menu_order ASC"
	));

	if( count($ev)>0){
		date_default_timezone_set('UTC');

		//shortcode driven hide_past value OR hide past events value set via settings
			$sc_hide_past = $hide_past == 'yes'? true:false;
			$_settings_hide_past = evo_settings_check_yn($this->evopt1 ,'evcal_cal_hide_past');
			$cal_hide_past = $evcal_cal_hide_past = ($sc_hide_past)? 'yes':
				( (!empty($this->evopt1['evcal_cal_hide_past']))? $this->evopt1['evcal_cal_hide_past']: 'no');

		// override past event cut-off
			if(!empty($SC['pec'])){

				if( $SC['pec']=='cd'){
					// this is based on local time
					$current_time = strtotime( date("m/j/Y", current_time('timestamp')) );
				}else{
					// this is based on UTC time zone
					$current_time = current_time('timestamp');
				}

			}else{
				// Define option values for the front-end
				$cur_time_basis = (!empty($this->evopt1['evcal_past_ev']) )? $this->evopt1['evcal_past_ev'] : null;

				//date_default_timezone_set($tzstring);
				if($_settings_hide_past && $cur_time_basis=='today_date'){
					// this is based on local time
					$current_time = strtotime( date("m/j/Y", current_time('timestamp')) );
				}else{
					// this is based on UTC time zone
					$current_time = current_time('timestamp');
				}
			}
			


			$this->current_time = $current_time;

		// current year month
			$range_start = !empty($focus_start_date_range)? $focus_start_date_range: $this->current_time;
			$__current_year = date('Y', (int)$range_start);
			$__current_month = date('n', (int)$range_start);

			$range_data = array(
				'start'=> $focus_start_date_range, 
				'end'=> $focus_end_date_range,
				'start_year'=> date('Y', (!empty($focus_start_date_range)? $focus_start_date_range: $this->current_time)),
				'start_month'=> date('n', (!empty($focus_start_date_range)? $focus_start_date_range: $this->current_time)),
				'end_year'=> date('Y', (!empty($focus_end_date_range)? $focus_end_date_range: $this->current_time)),
				'end_month'=> date('n', (!empty($focus_end_date_range)? $focus_end_date_range: $this->current_time)),
			);

		foreach($ev as $eve){
			$EVENT = new EVO_Event( $eve->ID ,'','',true, $eve);
			$p_id = $EVENT->ID;					
			$ev_vals = $EVENT->get_data();


			// if event set to exclude from calendars
			if( $EVENT->check_yn('evo_exclude_ev')) continue;	

			// Show event only for logged in user filtering
				if( $EVENT->check_yn('_onlyloggedin') && !$is_user_logged_in ) continue;

			// initial values
				$row_start = $EVENT->get_start_time();
				$row_end = $EVENT->get_end_time();

				$evcal_event_color_n= $EVENT->get_prop_val('evcal_event_color_n',0);
				$_is_featured = $EVENT->is_featured();

			
			// REPEATING EVENTS
			if($EVENT->is_repeating_event()){

				// get saved repeat intervals for repeating events
				$repeat_intervals = $EVENT->get_repeats();

				// if repeat intervals are saved
				if(!empty($repeat_intervals) && is_array($repeat_intervals)){

					// featured events only
					if($only_ft =='yes' && !$EVENT->is_featured()) continue;
					if($hide_ft =='yes' && $EVENT->is_featured()) continue;
						
					$virtual_dates=array();
					
					// each repeating interval times
					foreach($repeat_intervals as $index => $interval){

						$EVENT->ri = $index;															

						$E_start_unix = (int)$interval[0];
						$E_end_unix = (int)$interval[1];
						$term_ar = 'rm';

						$event_year = date('Y', $E_start_unix);
						$event_month = date('n', $E_start_unix);

						$_is_event_current = $EVENT->is_current_event( ($hide_past_by=='ee'?'end':'start'), $current_time );
						$_is_event_inrange = $EVENT->is_event_in_date_range( $range_data['start'],$range_data['end'] );
						
						// hide past event set - past events set to hide
							if($cal_hide_past =='yes' && !$_is_event_current) continue;

							if(!$_is_event_inrange ) continue;

							if(in_array($p_id, $this->events_processed)){
								if($hide_mult_occur=='yes' && $show_repeats=='no') continue;
							}
							
						// make sure same repeat is not shown twice
							if( in_array($E_start_unix, $virtual_dates)) continue;


						$virtual_dates[] = $E_start_unix;
						$event_list_array[] = $this->_convert_to_readable_eventdata(array(
							'ID'=> $EVENT->ID,
							'event_id' => $p_id,
							'event_start_unix'=> (int)$E_start_unix,
							'event_end_unix'=> (int)$E_end_unix,
							'event_title'=>get_the_title(),
							'event_color'=>$evcal_event_color_n,
							'event_type'=>$term_ar,
							'event_past'=> ($_is_event_current? 'no':'yes' ),
							'event_pmv'=>$ev_vals,
							'event_repeat_interval'=>$index,
							'ri'=>$index,
						), $EVENT);

						if($EVENT->is_featured() )	$featured_events[]=$p_id;
						$this->events_processed[]=$p_id;									

					}// endforeeach


				// does not have repeat intervals saved
				}else{// OLD WAY --- each repeating instance	OLD WAY - DEP v2.8

					$__run_occurance_check = (($this->is_upcoming_list && $this->_hide_mult_occur) || $hide_mult_occur=='yes')? true:false;

					$frequency = $EVENT->get_prop_val('evcal_rep_freq',1);
					$repeat_gap_num = $EVENT->get_prop_val('evcal_rep_gap',1);
					$repeat_num = $EVENT->get_prop_val('evcal_rep_num',1);
					
					for($x=0; $x<=($repeat_num); $x++){

						$feature='no';

						$repeat_multiplier = ((int)$repeat_gap_num) * $x;

						// Get repeat terms for different frequencies
						switch($frequency){
							// Additional frequency filters
							case has_filter("eventon_event_frequency_{$frequency}"):
								$terms = apply_filters("eventon_event_frequency_{$frequency}", $repeat_multiplier);
								$term = $terms['term'];
								$term_ar = $terms['term_ar'];
							break;
							case 'yearly':
								$term = 'year';	$term_ar = 'ry';
								$feature = ($_is_featured!='no')?'yes':'no';
							break;

							// MONTHLY
							case 'monthly':

								$term = 'month';	$term_ar = 'rm';
								$feature = ($_is_featured!='no')?'yes':'no';

							break;
							case 'weekly':
								$term = 'week';	$term_ar = 'rw';

							break;
							default: $term = $term_ar = ''; break;
						}

						$E_start_unix = strtotime('+'.$repeat_multiplier.' '.$term, $row_start);
						$E_end_unix = strtotime('+'.$repeat_multiplier.' '.$term, $row_end);

						// check if only featured events to show
						if( (($only_ft && $_is_featured=='yes') || !$only_ft) && ( $hide_ft && $_is_featured=='no' || !$hide_ft)){

							$future_event = eventon_is_future_event($current_time, $E_start_unix, $E_end_unix, $evcal_cal_hide_past, $hide_past_by);
							$fe = ( (!empty($this->shortcode_args['el_type']))? true: $future_event );

							$me = eventon_is_event_in_daterange($E_start_unix,$E_end_unix, $focus_month_beg_range,$focus_month_end_range, $this->shortcode_args);
							$event_past = eventon_is_event_past($current_time, $E_start_unix, $E_end_unix, $hide_past_by)?'yes':'no';


							if($fe && $me){
								if($__run_occurance_check && !in_array($p_id, $this->events_processed) ||!$__run_occurance_check){

									$event_list_array[] = array(
										'event_id' => $p_id,
										'event_start_unix'=> (int)$E_start_unix,
										'event_end_unix'=> (int)$E_end_unix,
										'event_title'=>get_the_title(),
										'event_color'=>$evcal_event_color_n,
										'event_type'=>$term_ar,
										'event_past'=>$event_past,
										'event_pmv'=>$ev_vals,
										'event_repeat_interval'=>'0'
									);

									if($feature!='no'){
										$featured_events[]=$p_id;
									}
								}
								$this->events_processed[]=$p_id;
							}
						}
					} // end for statement

				} 
			}else{ // Non recurring event
				

				// featured events check
					if($only_ft =='yes' && !$EVENT->is_featured()) continue;
					if($hide_ft =='yes' && $EVENT->is_featured()) continue;

				// event start year and month
					$event_year = date('Y', $row_start );
					$event_month = date('n', $row_start );
				
				$_is_event_current = $EVENT->is_current_event( ($hide_past_by=='ee'?'end':'start'), $current_time );
				
				$_is_event_inrange = $EVENT->is_event_in_date_range( $range_data['start'],$range_data['end'] );
								
				// past event and range check
					if($cal_hide_past=='yes' && !$_is_event_current) continue;
					if(!$_is_event_inrange ) continue;

				// hide multiple occurance check
					if($hide_mult_occur=='yes' && in_array($EVENT->ID, $this->events_processed) ) continue;


					$event_list_array[] = $this->_convert_to_readable_eventdata(array(
						'ID'=> $EVENT->ID,
						'event_id' => $EVENT->ID,
						'event_start_unix'=> (int)$row_start,
						'event_end_unix'=> (int)$row_end,
						'event_title'=> get_the_title(),
						'event_color'=> $evcal_event_color_n,
						'event_type'=>'nr',
						'event_past'=> ($_is_event_current? 'no':'yes' ),
						'event_pmv'=>$ev_vals,
						'event_repeat_interval'=>'0',
						'ri'=>'0'
					), $EVENT);

					if($EVENT->is_featured()) $featured_events[]= $EVENT->ID;
					$this->events_processed[]= $EVENT->ID;
			}


			// set featured events list aside
			$this->_featured_events = $featured_events;
		}
	}

	return $event_list_array;
}

	
}