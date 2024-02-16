<?php
/**
 * EVO_generator class.
 *
 * @class 		EVO_generator
 * @version		4.5.8
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class EVO_generator extends EVO_Cal_Time{

	public $google_maps_load,
		$is_eventcard_open,
		$evopt1,
		$evopt2,
		$evcal_hide_sort;

	public $is_upcoming_list = false;
	public $is_eventcard_hide_forcer = false;
	public $_sc_hide_past = false; // shortcode hide past

	public $wp_arguments='';
	public $shortcode_args;
	public $cal_id ='';//@+2.8
	
	public $lang_array=array();

	public $current_event_ids = array();

	private $_hide_mult_occur = false;
	public	$events_processed = array();

	private $__apply_scheme_SEO = false;
	private $_featured_events = array();

	private $class_args = array(); 
	public $tax_meta = array();
	public $is_user_logged_in = false;

	// time date values
		public $GMT, $DD, $timezone, $timezone0, $current_time, $time_format, $date_format, $utc_time, $current_time0, $cal_tz_string,  $cal_utc_offset, $cal_tz, $cal_tz_gmt;
		private $utc_DD;
	
	public $filtering, $shell, $body, $helper, $EVENT, $cal_range_data, $help;

	public $__calendar_type;
	public $events_list = array();
	public $JSON_event_data = array();

	public $event_types = 3;
	public $debug = 1;

	// calendar attributes
	public $ID;


	/**	Construction function	 */
		public function __construct(){

			include_once('class-calendar-shell.php');
			include_once('class-shortcode-defaults.php');
			include_once('class-calendar-body.php');
			include_once('class-calendar-filtering.php');
			require_once('class-calendar-event-structure.php');

			$this->__calendar_type = 'default';
			$this->help = new evo_helper();

			/** set class wide variables **/
			$this->evopt1 = EVO()->cal->get_op('evcal_1');
			$this->evopt2 = EVO()->cal->get_op('evcal_2');

			$this->shortcode_args = array();

			$this->is_eventcard_open = EVO()->cal->check_yn('evo_opencard','evcal_1');			
			$this->evcal_hide_sort = EVO()->cal->check_yn('evcal_hide_sort','evcal_1'); // hide sort filtering options

			// load google maps api only on frontend
			add_action( 'init', array( $this, 'init' ) );

			// Datre and time
			$this->GMT = $G = get_option('gmt_offset');

			$this->current_time = current_time('timestamp');
			$this->time_format = get_option('time_format');
			$this->date_format = get_option('date_format');

			// get calendar utc offset @4.5.6
				$this->cal_tz_string = EVO()->cal->get_prop('evo_global_tzo','evcal_1') ? : 'UTC';				
				$this->cal_tz_gmt = $this->help->get_timezone_gmt( $this->cal_tz_string );			
				$this->cal_utc_offset = $this->help->_get_tz_offset_seconds( $this->cal_tz_string );
				$this->cal_tz = new DateTimeZone( $this->cal_tz_string );


			$this->DD = new DateTime();
			$this->timezone0 = new DateTimeZone( 'UTC' );

			// deprecating 4.5.9
				$tzstring = get_option( 'timezone_string' );
	    		
				// Remove old Etc mappings. Fallback to gmt_offset.
				if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
					$tzstring = '';
				}
				
			    if( empty( $tzstring ) ){    $tzstring = 'UTC';    }		    

				$this->timezone = new DateTimeZone( $tzstring );
				

				$this->utc_DD = new DateTime("now", $this->timezone0 );
				$this->utc_time = $this->utc_DD->format('U');

				$this->current_time0 = $this->utc_time;

				$this->DD->setTimezone( $this->timezone0 );
				$this->DD->setTimestamp( $this->current_time );

			// USER
			$this->is_user_logged_in = is_user_logged_in();
		
		}

	// INIT		
		function init(){			
			$this->filtering = new EVO_Cal_Filering();
			//$this->shell = new evo_cal_shell();
			$this->shell = new EVO_Calendar_Shortcode_Defaults($this);
			$this->body = new evo_cal_body();
			$this->helper = new evo_cal_help();		

			$this->shell->verify_eventtypes();
			$this->shell->reused();
		}

	// globals 
		// return current unix time
		function get_current_time(){
			return $this->current_time;
		}
	
	// WP OPTIONS for Calendar // @+2.8
		function get_opt1_prop($F, $DV = ''){
			if(!isset($this->evopt1[$F]) || empty($this->evopt1[$F])) return !empty($DV)? $DV: false;
			return $this->evopt1[$F];
		}
		public function get_tax_meta(){
			if(is_array($this->tax_meta) && count($this->tax_meta)<1) 
				$this->tax_meta = get_option( "evo_tax_meta");
			
			return $this->tax_meta;
		}

	// PARSE and process SHORTCODE arguments
		function process_arguments($args='', $set_date_range = true){

			EVO()->frontend->evo_on_page = true;
			
			// process args and strip invalid quotation marks
			if(is_array($args) && sizeof($args)>0 && !empty($args) ){
				foreach($args as $field=>$val){
					$args[$field] = str_replace('”', '', $val);
				}
			}

			$original_args = $args;

			// enqueue eventon styles and scripts to page at this time if not done so
			EVO()->frontend->load_evo_scripts_styles();

			$default_arguments = $this->shell->get_supported_shortcode_atts();


			// if there are arguments passed for processing
			if(!empty($args) && is_array($args)){

				// merge default values of shortcode
				$args = array_merge($default_arguments, $args);

				// check if shortcode arguments already set
				if(empty($this->shortcode_args)){
					$this->shortcode_args=$args;
				}else{
					$args =array_merge($this->shortcode_args,$args );
					$this->shortcode_args=$args;
				}

			// Args as empty value
			}else{				
				$this->shortcode_args=$default_arguments; // set global arguments
				$args = $default_arguments;
			}			

			// Do other things based on shortcode arguments
				// switching eventtop style default to 2 and legacy fix
					//if(!isset($original_args['eventtop_style'])) $args['eventtop_style'] = 0;

				// EventCard open by default evc_open value
					// whats saved on settings
					$_settings_evc = (!empty($this->evopt1['evo_opencard']) && $this->evopt1['evo_opencard']=='yes')? 'yes':'no';
					$_args_evc = (!empty($args['evc_open']) && $args['evc_open']=='yes')? 'yes':'no';

					// Settings value set to yes will be override by shortcode values
					$__evc = ($_args_evc=='yes')? 'yes':
						( ($_settings_evc=='yes' )? 'yes':'no' );

					// set the value that was calculated
					$args['evc_open'] = $__evc;

				// Set hide past value for shortcode hide past event variation
					$this->_sc_hide_past = (!empty($args['hide_past']) && $args['hide_past']=='yes')? true:false;

				// process WPML @u 4.5,5	
					if( has_filter( 'wpml_current_language ') || defined('ICL_LANGUAGE_CODE')){
						$my_current_lang = apply_filters( 'wpml_current_language', NULL );
						if( defined('ICL_LANGUAGE_CODE')) $my_current_lang = ICL_LANGUAGE_CODE; // backward compatibility

						if( !empty($my_current_lang)){
							$lang_count = apply_filters('eventon_lang_var_count', 3); // @version 2.2.24
							for($x=1; $x <= $lang_count; $x++){
								if(!empty($args['wpml_l'.$x]) && $args['wpml_l'.$x]== $my_current_lang ){
									$args['lang']='L'.$x;
								}
							}
						}						
					}

				// Evo language @+2.6.10
					evo_set_global_lang($args['lang']);

			// hide_past => event_past_future filter - v2.8
				if(isset($args['event_past_future']) && $args['event_past_future']=='future'){
					$args['hide_past']=='yes';
				}
				if(isset($args['hide_past']) && $args['hide_past']=='yes'  ){
					$args['event_past_future'] = 'future';
				}			
		
			// MAP values
				$args['mapscroll'] = ((!empty($this->evopt1['evcal_gmap_scroll']) && $this->evopt1['evcal_gmap_scroll']=='yes')?'false':'true');
				$args['mapformat'] = ((!empty($this->evopt1['evcal_gmap_format']))?$this->evopt1['evcal_gmap_format']:'roadmap');
				$args['mapzoom'] =((!empty($this->evopt1['evcal_gmap_zoomlevel']))?$this->evopt1['evcal_gmap_zoomlevel']:'12');
				$args['mapiconurl'] = ( !empty($this->evopt1['evo_gmap_iconurl'])? $this->evopt1['evo_gmap_iconurl']:'');
			// google maps load
				if($this->google_maps_load) $args['maps_load'] = 'yes';

			
			// tiles shortcode altering
				if(!empty($args['tiles']) && $args['tiles']=='yes' && $args['ux_val'] == '1'){
					$args['ux_val'] = 3;
				}
					

			// set processed argument values to class variable
			$this->shortcode_args = $args;

			// set calendar date range, if focus ranges are not passed
			if($set_date_range) $this->shell->set_calendar_range($args);

			// pluggable hook for the processed args
			$this->shortcode_args = apply_filters('eventon_process_after_shortcodes', $this->shortcode_args);

			$this->ID = $this->shortcode_args['cal_id'];

			return $this->shortcode_args;
		}

		// update a shortcode arguments value after it is set @+2.8
			function _get_sc($F){
				$A = $this->shortcode_args;
				if(!isset($A[$F])) return false;
				return $A[$F];
			}
			function _check_yn_sc($F){
				$V = $this->_get_sc($F);
				if(!$V ) return false;
				return $V =='yes'? true: false;
			}
			function _update_sc_args($F, $V){
				$A = $this->shortcode_args;
				$A[$F]= $V;
				$this->shortcode_args = $A;
			}
			function update_shortcode_arguments($new_args){
				$args = array_merge($this->shortcode_args, $new_args);
				$this->shortcode_args = $args;
				return $args;
			}

	// Support calendar
		function _cal_reset($type = 'start'){
			$A = $this->shortcode_args;
			if($type == 'start'){
				$this->_hide_mult_occur = ($A['hide_mult_occur'] == 'yes') ? true:false;
			}else{
				$this->_hide_mult_occur = false;
			}
			$this->events_processed = array();
			$this->JSON_event_data = array();
		}
		function get_calendar_footer(){
			return $this->body->get_calendar_footer();
		}
		// this is used in shell header as well as other headers
		function get_calendar_header($arguments){
			EVO()->frontend->load_evo_scripts_styles();		
			return $this->body->get_calendar_header($arguments);
		}

	// calendar pre-check @+2.8
		function calendar_pre_check(){
			if(EVO()->cal->check_yn('evcal_cal_hide','evcal_1') ) return false;
			if($this->body->calendar_nonlogged()) return false;
			return true;
		}

	// AJAX initial calendar @+2.8
		function _get_initial_calendar($atts=array(), $header_args='' ){
			
			// PROCESS SC
			$A = $this->process_arguments( $atts);				

			if(!EVO()->frontend->is_member_only($A)) return EVO()->frontend->nonMemberCalendar();

			EVO()->frontend->load_evo_scripts_styles();	
			
			if(!$this->calendar_pre_check()) return false;
			$this->_cal_reset();

			
			// Before date range set
			do_action('evo_ajax_cal_before_rangeset', $atts);


			// SET default range for one month // again
			$this->shell->set_calendar_range($atts);


			// before calendar process / after date range set
			do_action('evo_ajax_cal_before', $atts);	


			// PROCESS & extract the variable values
			$A = $this->shortcode_args;	extract($A);

			$O = '';

			// HEADER
				$header_args = array_merge(array(
					'focused_month_num'=>$fixed_month,
					'focused_year'=>$fixed_year,
					'_classes_calendar'=> ($cal_init_nonajax=='yes'? '':'ajax_loading_cal'),
					'initial_ajax_loading_html'=> ($cal_init_nonajax=='yes'? false:true),
					'date_header'=> ($number_of_months>1? false:true),
				), (!is_array($header_args)? array(): $header_args) );

				$O.= $this->body->get_calendar_header($header_args);		
			
			// update the languages array
			$this->reused();

			// if not via ajax init
			if($cal_init_nonajax =='yes'){
				$O .= $this->_generate_events('html');
			}
			
			$O .= $this->body->get_calendar_footer();

			$this->_cal_reset('end');
			return  $O;	
		}

	

	// ### Generate Events for all calendars - lists and single months
		// output - both, data, html
		function _generate_events( $output = 'both', $wp_argument_additions = array()){
			$A = $this->shortcode_args;
			$this->reused();
			extract($A);

			$content = '';

			// RESET 
				$this->events_processed = array();

			// Query events
			$event_list_array = $this->evo_get_wp_events_array( $wp_argument_additions );	

			$event_list_array = $this->filtering->move_important_events_up( $event_list_array );

			// apply event list filters in stages
			$event_list_array = $this->filtering->apply_filters_to_event_list($event_list_array,'past_future');


			$event_list_array_raw = $this->filtering->apply_filters_to_event_list($event_list_array,'pagination');
			$event_list_array = $this->filtering->apply_filters_to_event_list($event_list_array_raw,'event_count');

			
			// allow the events list to be altered before converting to html
			$event_list_array = apply_filters('evo_generate_events_before_process', $event_list_array); 


			// Start Date range 
				$DD = new DateTime();
				$DD->setTimezone( $this->cal_tz );
				$DD->setTimestamp( (int)$focus_start_date_range);

				$new_events_data = array();

			// searching all events
				$search_all = empty($search_all)? 'no': $search_all;


			// More than one month
			if( $number_of_months > 1 && $search_all == 'no'){

				$proceed = apply_filters('evo_generate_events_filter_proceed', true, $A);
				
				if($proceed){				

					$_ESA = array(); // all events in the calendar IDs
					$_ECM = 0;

					$events_by_month = array();

					// EACH MONTH				
					for($x=0; $x< $number_of_months ; $x++){

						if($x > 0){
							$DD->setTime(0,0,0);
							$DD->modify('first day of this month');	
							$DD->modify('+1 month');	
						}

						$SU = $DD->format('U');
						$_m = $DD->format('n');
						$_y = $DD->format('Y');
						
						$DD->modify('last day of this month');						
						$DD->setTime(23,59,59);
						$EU = $DD->format('U');
												
						$_html = ''; $_ES = array();

											
						$_EC = 0;


						// EVENTS
						foreach($event_list_array as $ind=>$event){

							if( !isset($event['_ID'])) continue;

							// hide multiple occur  across all months
							if($hide_mult_occur == 'yes' && in_array($event['_ID'], $_ESA) ) continue;
							// Event count per month filter
							if($event_count >0 && $_EC >= $event_count && $show_limit=='no') continue; 

							
							if($this->shell->is_in_range(
								$SU, $EU,  (int)$event['event_start_unix'] , (int)$event['event_end_unix'] 
							)){								
								$_ES[ $event['_ID'] ] = $new_events_data[ $ind ] = $event;
								$_ESA[] = $event['_ID'];
								$_EC++; $_ECM ++;
							}
						}

						// hide empty month filter
						if( $hide_empty_months == 'yes' && $_EC == 0) continue;

						$_ES = $this->generate_event_data(	$_ES	);
						$_HTML = $this->filtering->no_more_events_add($_ES);


						if( $sep_month == 'yes'){
							$content.= "<div class='evcal_month_line' data-d='eml_{$_m}_{$_y}'><p>".eventon_returnmonth_name_by_num($_m). ($show_year == 'yes'? ' '.$_y:'') ."</p></div>";
							$content.= "<div class='sep_month_events ". ($_EC==0?'no_event':''). "' data-d='eml_{$_m}_{$_y}'>";
							$content .= $_HTML;
							$content.= "</div>";
						}else{
							$content .= $_HTML;
						}
					}

					// for multiple months with none separate months
					if( ($sep_month == 'no' && empty($content) ) || empty($content) ){
						$content .= "<div class='eventon_list_event no_events'>".$this->helper->get_no_event_content() ."</div>";
					}
				}

				
			}else{ // return only individual events				
				$event_list_array = $this->filtering->apply_filters_to_event_list($event_list_array,'event_count');

				if($output != 'data' ){

					
					$new_events_data = $event_list_array;


					// GET: eventTop and eventCard for each event in order
					$event_data = $this->generate_event_data(
						$event_list_array, 	
						$focus_start_date_range
					);

					$_EC = count($event_list_array);
					$_m = $DD->format('n');
					$_y = $DD->format('Y');


					$_HTML = $this->filtering->no_more_events_add($event_data, $A);


					if( $sep_month == 'yes'){ // for event list with one month
						$content.= "<div class='evcal_month_line' data-d='eml_{$_m}_{$_y}'><p>".eventon_returnmonth_name_by_num($_m). ($show_year == 'yes'? ' '.$_y:'') ."</p></div>";
						$content.= "<div class='sep_month_events ". ($_EC==0?'no_event':''). "' data-d='eml_{$_m}_{$_y}'>";
						$content .= $_HTML;
						$content.= "</div>";
					}else{
						$content .= $_HTML;
					}
				}

			}


			$this->events_list = $this->JSON_event_data = $new_events_data;
				

			$RR = apply_filters('evo_generate_events_results', array(
				'html'=> $content,
				'data'=> $new_events_data,
				'raw_el'=>$event_list_array_raw,
			),
			$event_list_array_raw, $this );

			if( $output == 'data') return $RR['data'];
			if($output == 'html') return $RR['html'];
			return $RR;
		}

	/* GENERATE: EVENT LIST */
		function generate_events_list($atts){

			// Pre shortcode filtering
			// separate months set to yes if not specified on load
				if(!isset($atts['sep_month'])) $atts['sep_month'] = 'yes';
				if(!isset($atts['show_limit'])) $atts['show_limit'] = 'no';

				if($atts['show_limit'] =='yes' && $atts['sep_month'] == 'no' ) 
					$atts['show_limit'] ='no';

				if(isset($atts['hide_month_headers']) && $atts['hide_month_headers'] =='yes') 
					$atts['sep_month'] = 'no';

			// PROCESS SC
			$SC = $this->process_arguments( $atts);	

			if(!EVO()->frontend->is_member_only($SC)) return EVO()->frontend->nonMemberCalendar();

			EVO()->frontend->load_evo_scripts_styles();				
			
			if(!$this->calendar_pre_check()) return false;
			$this->_cal_reset();

			$A = $this->shortcode_args;
			extract($A);
			$content='';


			// HIDE or show multiple occurance of events in upcoming list
			$this->_hide_mult_occur = ($hide_mult_occur=='yes') ? true:false;

			// check if upcoming list calendar view
			if($number_of_months>1){
				$this->is_upcoming_list= true;
				$this->is_eventcard_open = false;
			}

			// HEADER
			$classes = ( $sep_month=='yes')? 'evcal_list_month':'';
			$content .= $this->body->get_calendar_header(array(
				'number_of_months'=>$number_of_months,
				'sortbar'=> ($hide_so == 'yes'? false: true),
				'date_header'=>false,
				'_html_evcal_list'=>true,
				'_classes_evcal_list'=> $classes,
				'_html_sort_section'=>true,
				'unique_classes'=>array('list_cal'),
				'search_btn'=> true
			));

			// reset the events list
			$this->events_processed = array();

			// BODY
			$content .= $this->_generate_events('html');			

			// FOOTER
			$content .= $this->body->calendar_shell_footer();

			// RESET calendar stuff
			if($this->is_upcoming_list)	$this->is_upcoming_list=false;	

			$this->_cal_reset('end');
			return $content;
		}
	
	/**
	 * WP_Query function to generate relavent events for a given month
	 * return events list within start - end date range for WP_Query arg.
	 * return array
	 */
		// RETURN array list of events
		// for a month by default but can change to set time line with args
			public function evo_get_wp_events_array(	$wp_argument_additions='', $shortcode_args='' ){

				$ecv = $SC = $this->shortcode_args;

				$this->reused();

				// WPQUery Arguments
					$wp_arguments_ = array (
						'post_type' 		=>'ajde_events' ,
						'post_status'		=>'publish',
						'posts_per_page'	=>-1 ,
						'order'				=>'ASC',
						'orderby' 			=> 'menu_order',
						'has_password'		=> FALSE
					);

					//search query addition
						if(!empty($ecv['s'])){
							$wp_arguments_ = array_merge($wp_arguments_, array('s'=>$ecv['s']));
						}

					// event order ASC or DESC
						if(isset($ecv['event_order'])) $wp_arguments_['order'] = $ecv['event_order'];

					// sort query 
						if( isset($ecv['sort_by'])){
							switch ($ecv['sort_by']) {
								case 'sort_posted':
									$wp_arguments_['orderby'] = 'date';
								case 'sort_menu_order':
									$wp_arguments_['orderby'] = 'menu_order';								
							}
						}

					$meta_query = array();

					// Meta query argument addition for language if enabled
						if(evo_settings_check_yn($this->evopt1,'evo_lang_corresp')){
							$meta_query[] = array(
								'key'     => '_evo_lang',
								'value'   => $ecv['lang']
							);
						}

					// if hide cancelled events = yes
						if( isset($ecv['hide_cancels']) && $ecv['hide_cancels'] == 'yes'){
							$meta_query[] = array(
								'key'     => '_status',
								'value'   => 'cancelled',
								'compare'=> '!='
							);
						}

					// virtual event filter at query level
						if( isset($ecv['event_virtual']) && $ecv['event_virtual'] != 'all'){
							$meta_query[] = array(
								'key'     => '_virtual',
								'value'   => $ecv['event_virtual'] == 'nvir' ? 'no':'yes',
							);
						}

					// event status filtering
						if( isset($SC['event_status']) && $SC['event_status'] != 'all' && $SC['hide_cancels'] == 'no'){
								
							$event_status_val = sanitize_text_field( $ecv['event_status'] );
							$event_status_val_ = array_filter( explode(',', $event_status_val) );

							if( count($event_status_val_)>1){
								$relation = isset($SC['filter_relationship']) ? 
									$SC['filter_relationship']: 
									'AND';

								$arr = array();
								foreach($event_status_val_ as $val){
									$arr[] = array(
										'key'     => '_status',
										'value'   => $val
									);
								}
								$arr['relation'] = $relation;
								$meta_query[] = $arr;

							}else{
								if( $event_status_val != 'all,'){
									$meta_query[] = array(
										'key'     => '_status',
										'value'   => $event_status_val
									);
								}
								
							}						
						}

					// query adds based on evo_settings_query_type value in settings
						if( $que = EVO()->cal->get_prop('evo_settings_query_type','evcal_1')){
							if( $que == 'this_year'){
								$getdate = getdate();
								$wp_arguments_['date_query'] = array(
							        array('year'  => $getdate["year"]),
							    );
							}
							if( $que == '12months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '12 months ago')
								);
							}
							if( $que == '6months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '6 months ago')
								);
							}
							if( $que == 'last_5months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '5 months ago')
								);
							}if( $que == 'last_4months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '4 months ago')
								);
							}if( $que == 'last_3months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '3 months ago')
								);
							}if( $que == 'last_2months'){
								$wp_arguments_['date_query'] = array(
									array( 'column' => 'post_date_gmt', 'after'  => '2 months ago')
								);
							}
							if( $que == 'this_month'){
								$getdate = getdate();
								$wp_arguments_['date_query'] = array(
									array( 'month' => $getdate["mon"])
								);
							}						

						}

					if( count($meta_query)>0 ){
						$wp_arguments_['meta_query'] = $meta_query;
					}

					$wp_arguments = (!empty($wp_argument_additions))?
						array_merge($wp_arguments_, $wp_argument_additions): $wp_arguments_;

				// apply other filters to wp argument
					$wp_arguments = $this->filtering->apply_evo_filters_to_wp_argument($wp_arguments);

					//print_r($wp_arguments);

				// hook for addons
					$wp_arguments = apply_filters('eventon_wp_query_args',$wp_arguments, array(), $ecv);

				$this->wp_arguments = $wp_arguments;

				//print_r($SC);
				
				// ========================
				// GET: list of events for wp argument
				$event_list_array = $this->wp_query_event_cycle(	$wp_arguments	);


				// @~ 2.6.12
				$event_list_array = apply_filters('eventon_wp_queried_events_list', $event_list_array, $ecv);

				// sort events by date and default values
				$event_list_array = $this->shell->evo_sort_events_array($event_list_array);

				return $event_list_array;
			}
		

		// RUN QUERY			
			public function wp_query_event_cycle( $wp_arguments ){

				$SC = $this->shortcode_args;
				extract($SC);

				$event_list_array = $featured_events = array();

				$wp_arguments= (!empty($wp_arguments))?$wp_arguments: $this->wp_arguments;			
				
				// RUN WP_QUERY
				$events = new WP_Query( $wp_arguments);	

				if ( $events->have_posts() ) :

					$D = new DateTime('now');
					$D->setTimezone( $this->cal_tz);
				
					if( !empty($focus_start_date_range)) $D->setTimestamp( $focus_start_date_range );

					$start_unix = $D->format('U');

					// start range values
						$this->cal_range_data = array(
							'start'=> $D->format('U'), 
							'start_year'=> $D->format('Y'),
							'start_month'=> $D->format('n')
						);

					// end range
						if( !empty($focus_end_date_range)) $D->setTimestamp( $focus_end_date_range );
						$this->cal_range_data['end'] = $D->format('U');
						$this->cal_range_data['end_year'] = $D->format('Y');
						$this->cal_range_data['end_month'] = $D->format('n');
										
					
					$event_list_array = $this->wp_query_event_cycle_filter( $events);
					

				endif;
				wp_reset_postdata();


				return $event_list_array;
			}

			// cycle through events list for corrct events to show @4.5.5
			public function wp_query_event_cycle_filter( $events){

				$SC = $this->shortcode_args;
				extract($SC);

				$event_list_array= $featured_events = array();
				
				// hide past validation
					$_hide_past = ( $event_past_future == 'future' ) ? true : false;
					$_cal_visible_range = $this->helper->get_cal_visible_range_start();

				$range_data = $this->cal_range_data;
				
				$count = 0;


				// each event
				while( $events->have_posts()): $events->the_post();

					$count ++;

					// disregard any non event posts called within wp_query
						if(apply_filters('evo_wp_query_post_type_if', true, $SC) == true && $events->post->post_type != 'ajde_events') continue;

					$EVENT = new EVO_Event( $events->post->ID ,'','',true, $events->post);
					$ev_vals = $EVENT->get_data();

					// if event set to exclude from calendars
						if( $EVENT->check_yn('evo_exclude_ev')) continue;										

					// Show event only for logged in user filtering
						if( $EVENT->check_yn('_onlyloggedin') && !$this->is_user_logged_in ) continue;

					// initial values
						$row_start = $EVENT->get_start_time();
						$row_end = $EVENT->get_end_time();

						$evcal_event_color_n= $EVENT->get_prop_val('evcal_event_color_n',0);
						$_is_featured = $EVENT->is_featured();
					

					// REPEATING EVENTS
					if($EVENT->is_repeating_event()){
						//continue;

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

								$EVENT->load_repeat( $index );	

								// raw unix values
								$E_start_unix = $EVENT->get_event_time('start', $index, false);
								$E_end_unix = $EVENT->get_event_time('end', $index, false);
								
								$term_ar = 'rm';

								$event_year = date('Y', $E_start_unix);
								$event_month = date('n', $E_start_unix);


								// using UTC0 time
								$_is_event_current = $EVENT->is_current_event( ($hide_past_by=='ee'?'end':'start') );								
								$_is_event_inrange = $EVENT->is_event_in_date_range( $range_data['start'],$range_data['end'] ,$E_start_unix,$E_end_unix, true );

								$_is_in_visible_range = $EVENT->is_in_visible_range( $_cal_visible_range );
								
								
								// hide past event set - past events set to hide
									if( !$_is_in_visible_range ) continue;
									if($_hide_past && !$_is_event_current) continue;
									if(!$_is_event_inrange ) continue;

									if(in_array( $EVENT->ID, $this->events_processed)){
										if($hide_mult_occur=='yes' && $show_repeats=='no') continue;
									}
									
								// make sure same repeat is not shown twice
									if( in_array($E_start_unix, $virtual_dates)) continue;


								$virtual_dates[] = $E_start_unix;
								$event_list_array[] = $this->_convert_to_readable_eventdata(array(
									'ID'=> $EVENT->ID,
									'event_id' => $EVENT->ID,
									'event_start_unix'=> (int)$E_start_unix,									
									'event_end_unix'=> (int)$E_end_unix,									
									'event_title'=>	$EVENT->get_title(),
									'event_color'=>$evcal_event_color_n,
									'event_type'=>$term_ar,
									'event_past'=> ($_is_event_current? 'no':'yes' ),
									'event_repeat_interval'=>$index,
									'ri'=>$index,
									'unix_start'=> $EVENT->start_unix,
									'unix_end'=> $EVENT->end_unix,
									'etx_type' => $EVENT->get_time_ext_type(),
									'event_pmv'=>$ev_vals,
								), $EVENT);

								if($EVENT->is_featured() )	$featured_events[] = $EVENT->ID;
								$this->events_processed[] = $EVENT->ID;									

							}// endforeeach
						}	
						
					}else{ // Non recurring event

						// featured events check
							if($only_ft =='yes' && !$EVENT->is_featured()) continue;
							if($hide_ft =='yes' && $EVENT->is_featured()) continue;

						// event start year and month
							$event_year = date('Y', $row_start );
							$event_month = date('n', $row_start );
						
						// using UTC0 time
						$_is_event_current = $EVENT->is_current_event( ($hide_past_by=='ee'?'end':'start'));

						$_is_event_inrange = $EVENT->is_event_in_date_range( 
							$range_data['start'],$range_data['end'],'','', true );

						$_is_in_visible_range = $EVENT->is_in_visible_range( $_cal_visible_range );


						// past event and range check
							if( !$_is_in_visible_range ) continue;

							if( $_hide_past && !$_is_event_current) continue;
							if(!$_is_event_inrange ) continue;

						// hide multiple occurance check
							if($hide_mult_occur=='yes' && in_array($EVENT->ID, $this->events_processed) ) continue;

							$event_list_array[] = $this->_convert_to_readable_eventdata( array(
								'ID'=> $EVENT->ID,
								'event_id' => $EVENT->ID,
								'event_start_unix'=> (int)$row_start,								
								'event_end_unix'=> (int)$row_end,
								'event_title'=> get_the_title(),
								'event_color'=> $evcal_event_color_n,
								'event_type'=>'nr',
								'event_past'=> ($_is_event_current? 'no':'yes' ),								
								'event_repeat_interval'=>'0',
								'ri'=>'0',
								'unix_start'=> $EVENT->start_unix,
								'unix_end'=> $EVENT->end_unix,
								'etx_type' => $EVENT->get_time_ext_type(),
								'event_pmv'=>$ev_vals,
							), $EVENT);


							if($EVENT->is_featured()) $featured_events[]= $EVENT->ID;
							$this->events_processed[]= $EVENT->ID;
					}					
					
				endwhile;

				$this->_featured_events = $featured_events;

				return $event_list_array;
			}

			

	/**	output single event data	 */
		public function get_single_event_data($event_id, $lang='', $repeat_interval='', $args=array()){

			$this->__calendar_type = 'single';

			// If language is set, pass in on to shortcode arg and global
			if(!empty($lang)){
				$this->shell->update_shortcode_args('lang', $lang);
				$args['lang'] = $lang;
				evo_set_global_lang($lang);
			}

			// GET Eventon files to load for single event
			EVO()->frontend->load_evo_scripts_styles();

			$calendar_defaults = $this->helper->get_calendar_defaults();	
			$this->is_eventcard_open= ($this->is_eventcard_hide_forcer) ? false:true;

			$EVENT = new EVO_Event($event_id, '', $repeat_interval);

			// set base start and end unix
				$event_start_unix = $EVENT->get_start_time();
				$event_end_unix = $EVENT->get_end_time();
			
			$this->process_arguments( $args );

			$event_array[] = $this->_convert_to_readable_eventdata(array(
				'ID' => $event_id,
				'event_id' => $event_id,
				'event_start_unix'=>$event_start_unix,
				'event_end_unix'=>$event_end_unix,
				'event_title'=>get_the_title($event_id),
				'event_color'=> $EVENT->get_meta('evcal_event_color_n'),
				'event_type'=>'nr',
				'event_repeat_interval'=> (!empty($repeat_interval)?$repeat_interval:0),
				'ri'=> (!empty($repeat_interval)?$repeat_interval:0),
				'event_pmv'=> $EVENT->get_data()
			), $EVENT);

			$month_int = date('n', time() );
			$data = array();

			$data =  $this->generate_event_data($event_array, '', $month_int);
			$this->__calendar_type = 'default'; // reset calendar type 

			return apply_filters('evo_single_event_data_return', $data, $EVENT );
		}

	// RETURN event times
	// 2.5.6 u4.5.7
		public function generate_time($args= array()){

			$output = array('start'=>'', 'end'=>'');

			if(!is_array($args)) return false;

			// start and end on same date
			if($args['eventstart']['j'] == $args['eventend']['j']){
				$output['start'] = $args['stime'];
				$output['end'] = $args['etime'];
			}else{
				// start date is past enddate = focus day
				if($args['eventstart']['j'] < $args['cdate'] && $args['eventend']['j'] == $args['cdate']){
					$output['start'] = '<i>('.$args['eventstart']['M'].' '.$args['eventstart']['j'].')</i>' . $args['stime'];
					$output['end'] = $args['etime'];

				// start day = focus day and end day in future
				}elseif($args['eventend']['j'] > $args['cdate'] && $args['eventstart']['j'] == $args['cdate']){
					$output['start'] = $args['stime'];
					$output['end'] = '<i>('.$args['eventend']['M'].' '.$args['eventend']['j'].')</i>' . $args['etime'];


				// both start day and end days are not focus day
				}elseif($args['eventend']['j'] != $args['cdate'] && $args['eventstart']['j'] != $args['cdate']){
					$output['start'] = '<i t="y">('.$args['eventstart']['M'].' '.$args['eventstart']['j'].')</i>' . $args['stime'];
					$output['end'] = '<i t="y">('.$args['eventend']['M'].' '.$args['eventend']['j'].')</i>' . $args['etime'];

				// start and end on focus day
				}elseif($args['eventstart']['j'] == $args['cdate'] && $args['eventend']['j'] == $args['cdate']){
					$output['start'] = $args['stime'];
					$output['end'] = $args['etime'];			
				}
			}

			

			return $output;
		}
	

	/** GENERATE individual event data	for event list array */
		public function generate_event_data(
			$event_list_array,
			$focus_month_beg_range='',
			$FOCUS_month_int='',
			$FOCUS_year_int='',
			$eventCardData= true
		){

			$months_event_array = array();

			$dateTime = new evo_datetime();				

			// Initial variables
				EVO()->cal->set_cur('evcal_1');

				$wp_time_format = get_option('time_format');
				$__shortC_arg = $SC = $this->shortcode_args; // calendar shortcode arguments
				$is_user_logged_in = $this->is_user_logged_in;

				// Language
				$cal_lang = EVO()->lang = evo_get_current_lang();
				

				$calendar_defaults = $this->helper->get_calendar_defaults();	
					$show_schema = $calendar_defaults['show_schema'];
					$show_jsonld = $calendar_defaults['show_jsonld'];
					$cal_hide_end_time = $calendar_defaults['hide_end_time'];
					$__feature_events = $calendar_defaults['ft_event_priority'];
					$calendar_ux_val = !$calendar_defaults['ux_val']? '0': $calendar_defaults['ux_val'];					

					// EVENT CARD open by default variables
					$_is_eventCardOpen = $calendar_defaults['eventcard_open'];
					if(!$_is_eventCardOpen) $_is_eventCardOpen = $this->is_eventcard_open? true:false;
					$eventcard_script_class = ($_is_eventCardOpen)? "gmaponload":null;
					$this->is_eventcard_open = false;
					$custom_meta_fields_count = evo_retrieve_cmd_count($this->evopt1);


					// hide event card if using tiles layout
					if( isset($SC['tiles']) && $SC['tiles'] == 'yes'){
						$_is_eventCardOpen = false;
					}			
				
				$event_tax_meta_options = get_option( "evo_tax_meta");
			
			// Number of activated taxnomonies v 2.2.15
				$_active_tax = evo_get_ett_count($this->evopt1);
			
			$__count=0;

			// get eventtop data layout
				$eventtop_fields = $this->helper->get_eventtop_fields_array();
				

			// EACH EVENT
			if(is_array($event_list_array) ){
			foreach($event_list_array as $event_):	

				// Intials
					$__repeatInterval = $this->helper->get_ri_for_event($event_);


					$EVO_Event = $EVENT = $this->EVENT = new EVO_Event($event_['event_id'], $event_['event_pmv'] , $__repeatInterval);

					$EVO_Event->get_event_post(); // load event post data

					$EVO_Event->set_lang( $cal_lang );
					$is_recurring_event = $EVO_Event->is_repeating_event();

					$event_ = $this->_convert_to_readable_eventdata($event_, $EVENT);

				// All event structure data
					$EventData = array();

					//$show_schema = false;
					$EventData['schema'] = $show_schema;
					$EventData['schema_jsonld'] = $show_jsonld;

				// Other Init
					$structure = new EVO_Cal_Event_Structure($EVENT);

					do_action('evo_load_event',$EVENT);
					
					$_eventcard = array();
					$__eventtop = array();

					$html_event_detail_card='';
					$_eventClasses = $_eventInClasses = array();
					$_eventAttr = $_eventInAttr = array();

					$__count++;
					$event_id = $EVENT->ID;
					//$event_start_unix 	= (int)$event_['event_start_unix'];
					$event_start_unix 	= $EVENT->start_unix;
					$event_end_unix 	= $EVENT->end_unix ;
					$event_type = $event_['event_type'];
					$ev_vals = $event_['event_pmv'];

					$EventData['event_start_unix'] = (int)$event_['event_start_unix'];
					$EventData['event_end_unix'] = (int)$event_['event_end_unix'];

					$EventData['event_title'] = $EVENT->post_title;			
			
					$_eventInClasses[] = $eventcard_script_class;


				// set how a single event would interact
					$event_ux_val = $event_ux_val_raw = $EVO_Event->get_prop('_evcal_exlink_option')? $EVO_Event->get_prop('_evcal_exlink_option'):1;
					
					$event_permalink = $EVO_Event->get_permalink( '' , $cal_lang );

					// if UX set to external link and link is not empty & set event link to external link
						if($event_ux_val==2 && $EVO_Event->get_prop('evcal_exlink')){
							$event_permalink = $EVO_Event->get_prop('evcal_exlink');
						}

					// Calendar UX overrides
						// if calendar ux set override event ux
						$event_ux_val = ($calendar_ux_val !='0')? $calendar_ux_val:
							( (!empty($SC['tiles']) && $SC['tiles']=='yes' && $event_ux_val==1)? 3:	$event_ux_val );

						// ~2.8.5
						$event_ux_val = apply_filters('evo_one_event_ux_val', $event_ux_val, $event_ux_val_raw, $EVO_Event, $this);

						// calendar ux = 2 open event as learn more link
						if($calendar_ux_val == '2'){
							// event learn more link
							if( $EVO_Event->get_prop('evcal_lmlink')) 	$event_permalink = $EVO_Event->get_prop('evcal_lmlink');
							
							// event external link
							if($EVO_Event->get_prop('evcal_exlink'))	$event_permalink = $EVO_Event->get_prop('evcal_exlink');
						}

						$EventData['event_permalink'] = apply_filters('evo_event_data_permalink', $event_permalink, $EVENT, $this);


					// if using bubble eventtop style override ux_val to be lightbox or open in single event page or external link
						if( isset($SC['eventtop_style']) && $SC['eventtop_style'] == 3 ){
							if( $event_ux_val == 1) $event_ux_val = 3;
						}

				// whether eventcard elements need to be included or not
					$card_for_cal = ($calendar_ux_val=='3' || $calendar_ux_val=='1' )? true: false; // whether calendar call for card

					$_event_card_on = ($card_for_cal || ($event_ux_val!= '4a' && $event_ux_val!= '4' && $event_ux_val!= '2' ) )? true:false;
						$_event_card_on = ($_is_eventCardOpen)? true: $_event_card_on;// if event card is forced to open then

						// set to display event card for event parts
						if(isset($SC['event_parts']) && $SC['event_parts'] =='yes')
							$_is_eventCardOpen = true;
						
						// open event as lightbox ajax
						if($calendar_ux_val === '3a') $_event_card_on = false;


						// override by whats passed to function
						$_event_card_on = (!$eventCardData)? false: $_event_card_on;

						// override if shortcode set to show eventcard
						if(isset($SC['show_exp_evc']) && $SC['show_exp_evc'] == 'yes') $_event_card_on = true;

					$html_tag = ($event_ux_val=='1')? 'div':'a';					
					$html_tag = ($_event_card_on)? 'a':$html_tag;

				// year/month long or not
					$__year_long_event = $EventData['year_long'] = $EVENT->is_year_long();
					$__month_long_event = $EventData['month_long'] = $__year_long_event? false: ( $EVENT->is_month_long()? true:0);

				// define variables
					$ev_other_data = $ev_other_data_top = $html_event_type_info= $_event_date_HTML= $html_event_type_2_info =''; $_is_end_date=true;

				// UNIX date values
					$DATE_start_val = $EventData['start_date_data'] = eventon_get_formatted_time( $event_start_unix , $EVENT->tz );

					//print_r($DATE_start_val);

					// if method could not convert unix to separate time items
					if(!$DATE_start_val) continue;

					$DATE_end_val = $EventData['end_date_data'] = eventon_get_formatted_time( $event_end_unix , $EVENT->tz );

					//echo $event_['event_start_unix'].' ';
					//echo "$EVENT->start_unix $EVENT->start_unix_raw $EVENT->utc_offset";

				// Event Status data
					$EventData['_status'] = $EVENT->get_event_status();
					$_eventInClasses['__featured'] 	= $EventData['featured'] =	$EVENT->is_featured();
					$_eventInClasses['_completed'] 	= $EventData['completed'] =	$EVENT->is_completed();
					$_eventInClasses['_cancel'] 	= $EventData['cancelled'] =	$EVENT->is_cancelled();

				
				// Unique ID generation
					$unique_varied_id = 'evc'.$event_start_unix.(uniqid()).$event_id;
					$unique_id = 'evc_'.$event_start_unix.$event_id;

				// All day event variables
					$_is_allday = 		$EventData['all_day'] = $EVO_Event->is_all_day();
					$_hide_endtime = 	$EventData['hide_end_time'] = $EVO_Event->is_hide_endtime();
					$evcal_lang_allday = $this->lang( 'evcal_lang_allday', 'All Day');

				// get processed event time u4.5.7
					$_event_date_HTML = $this->generate_time_(
						$EVENT,
						$focus_month_beg_range, 
						$FOCUS_month_int, 
						$cal_hide_end_time,
					);

				// HOOK for addons
					$_event_date_HTML= apply_filters('eventon_eventcard_date_html', $_event_date_HTML, $event_id);
					$EventData['event_date_html'] = $_event_date_HTML;

				// EACH DATA FIELD

					// EVENT TERMS						
						if(isset($eventtop_fields['used'])){
							$_tax_names_array = evo_get_localized_ettNames('',$this->evopt1,$this->evopt2);

							// foreach active tax
							for($b=1; $b<=$_active_tax; $b++){
								
								$__tax_slug = 'event_type'.($b==1?'':'_'.$b);
								$__tax_field = 'eventtype'.($b==1?'':$b);


								if(in_array($__tax_field, $eventtop_fields['used'] )  ){

									$evcal_terms =  wp_get_post_terms($event_id,$__tax_slug);
									
									if($evcal_terms){

										$__tax_name = $_tax_names_array[$b];

										$i=1;
										foreach($evcal_terms as $termA):

											// get tax data
											$term_name = $this->lang('evolang_'.$__tax_slug.'_'.$termA->term_id, $termA->name);
											$icon_str = $this->helper->get_tax_icon($__tax_slug , $termA->term_id , $this->evopt1);

											// tax term slug as class name
											$_eventInClasses[] = 'evo_'.$termA->slug;											

											$EventData[$__tax_field]['terms'][$termA->term_id]= array(
												's'=> $__tax_slug,
												'tn'=> $term_name,
												'id'=> $termA->term_id,
												'i'=> $icon_str,
												'add'=> (count($evcal_terms)!=$i? ',':'')
											);
											$EventData[$__tax_field]['tax_name'] = $__tax_name;
											$EventData[$__tax_field]['tax_index'] = $i;

											$i++;
										endforeach;
									}
								}
							}
						}


					// EVENT FEATUREd IMAGE
						$main_image = $EVENT->get_image_urls();


						$img_id = '';
						$img_src = $img_med_src = $img_thumb_src ='';

						if( $main_image && is_array($main_image)){
							if( isset($main_image['full']) ) $img_src = $main_image['full'];
							if( isset($main_image['medium']) ) $img_med_src = $main_image['medium'];
							if( isset($main_image['thumbnail']) ) $img_thumb_src = $main_image['thumbnail'];
							if( isset($main_image['id']) ) $img_id = $main_image['id'];
						}else{
							if(isset($calendar_defaults['image'])){
								$img_src = $img_med_src = $img_thumb_src = $calendar_defaults['image'];
							}
						}
						
						
						if(!empty($img_src) ){
							$img_src = $this->_convert_ssl_url( $img_src );
							$_eventcard['ftimage'] = array(
								'eventid'=>	$event_id,
								'img'=>		$img_src,
								'img_id'=>	$img_id,
								'main_image'=> $main_image,
								'hovereffect'=> !empty($this->evopt1['evo_ftimghover'])? $this->evopt1['evo_ftimghover']:null,
								'clickeffect'=> (!empty($this->evopt1['evo_ftimgclick']))? $this->evopt1['evo_ftimgclick']:null,
								'min_height'=>	(!empty($this->evopt1['evo_ftimgheight'])? $this->evopt1['evo_ftimgheight']: 400),
								'ftimg_sty'=> (!empty($this->evopt1['evo_ftimg_height_sty'])? $this->evopt1['evo_ftimg_height_sty']: 'minimized'),
							);

							$EventData['img_src'] = $img_src;
							$EventData['img_id'] = $img_id;

							// data for event top
							if((!empty($img_thumb_src) && !empty($__shortC_arg['show_et_ft_img']) && $__shortC_arg['show_et_ft_img']=='yes') ){
								$url_med_link = !empty($img_med_src) ? $img_med_src: $img_thumb_src;
								if($SC['calendar_type'] == 'live'){
									$url_med_link = $img_src; $show_time = true;
								}
								$show_time = false;

								$EventData['img_url_thumb'] = $this->_convert_ssl_url( $img_thumb_src );
								$EventData['img_url_med'] = $this->_convert_ssl_url( $url_med_link );
								$EventData['show_time'] = $show_time;
							}
						}


						if((!empty($img_thumb_src) && !empty($__shortC_arg['show_et_ft_img']) && $__shortC_arg['show_et_ft_img']=='yes') ){

							$show_time = false;

							$url_med_link = !empty($img_med_src) ? $img_med_src: $img_thumb_src;

							if($SC['calendar_type'] == 'live'){
								$url_med_link = $img_src; $show_time = true;
							}

							$__eventtop['ft_img'] = array(
								'url'=>			$this->_convert_ssl_url( $img_thumb_src ),
								'url_med'=>		$this->_convert_ssl_url( $url_med_link ),
								'url_full'=>	$img_src,
								'show_time'=> $show_time
							);
						}

					// EVENT DESCRIPTION						
						if(!empty($EVO_Event->content) ){
							$event_full_description = $EVO_Event->content;
						}else{
							$event_full_description = $EVENT->get_prop('evcal_description'); // OLD versions
						}
						
						$EventData['event_details'] ='';
						$EventData['event_excerpt'] ='';
						$EventData['event_excerpt_txt'] ='';

						if(!empty($event_full_description) ){
							$except = $EVENT->excerpt;
							$event_excerpt = eventon_get_event_excerpt($event_full_description, 30, $except);

							$_eventcard['eventdetails'] = array(
								'fulltext'=>$event_full_description,
								'excerpt'=>$event_excerpt,
							);

							$EventData['event_details'] = $event_full_description;
							$EventData['event_excerpt'] = $event_excerpt;
							$EventData['event_excerpt_txt'] = eventon_get_normal_excerpt($event_full_description, 30, $except);
						}

					// LOCATION
						$L = $EVENT->get_location_data();
						
						//$location_terms = wp_get_post_terms($event_id, 'event_location');
						$location_address = $location_name = $lonlat = $location_url = false;

						if( $L){
							unset($L['name']);
							$EventData['location'] = true;							
							$EventData = array_merge($EventData, $L);					
						}

						$EventData['location_hide'] = $EVENT->is_hide_location_info();	

						$_eventcard['locImg'] = array();	
						$_eventcard['location'] = array();	
						$_eventcard['time'] = array(
							'timetext'=>$_event_date_HTML['html_prettytime'],
							'timezone'=> $EVENT->get_prop('evo_event_timezone'),// tz custom text
							'date_times' => $_event_date_HTML,
							'focus_start' => $focus_month_beg_range,
							'_evo_tz'=> $EVENT->get_timezone_key(),
							'event_times'=>	$event_start_unix.'-'.$event_end_unix,
						);

					// GOOGLE maps
						if( isset($EventData['location']) && $SC['maps_load'] =='yes' && $EVENT->check_yn('evcal_gmap_gen') && !$EventData['location_hide'] ){
							$_eventcard['gmap'] = array(
								'id'=>$unique_varied_id, 
								'ltype'=> (isset($EventData['location_type']) ? $EventData['location_type']:'')
							);							
						}else{	$_eventInAttr['data-gmap_status'] = 'null';	}

						$_eventcard['getdirection'] = array();

					// Repeat series
						if($is_recurring_event && $EVENT->check_yn('_evcal_rep_series') ){
							$repeat_intervals = $EVENT->get_repeats();

							if($repeat_intervals){
								$future_intervals = array();

								foreach($repeat_intervals as $ri=>$interval){

									// make sure only future events based on current time is shown
									if( $interval[1] < $this->current_time )	continue;

									if($ri>$__repeatInterval)
										$future_intervals[$ri]= $interval;
								}
								if(count($future_intervals)>0){
									//print_r($future_intervals);
									$_eventcard['repeats'] = array(
										'event_permalink'=>	$EventData['event_permalink'],
										'repeat_interval' => $__repeatInterval,
										'future_intervals'=>$future_intervals,
										'date_format'=>$dateTime->wp_date_format,
										'time_format'=>$dateTime->wp_time_format,
										'clickable'=> $EVENT->check_yn('_evcal_rep_series_clickable'),
										'showendtime'=> $EVENT->check_yn('_evcal_rep_endt')
									);
								}
							}
						}

					// PAYPAL Code
						if( $EVENT->get_prop('evcal_paypal_item_price') && $this->evopt1['evcal_paypal_pay']=='yes'
							&& !empty($this->evopt1['evcal_pp_email'])){
							$_eventcard['paypal'] = array();
						}

					// Event Organizer
						$O = $EVENT->get_taxonomy_data('event_organizer');
						$hideOrganizer_from_eventCard = $EVENT->check_yn('evo_evcrd_field_org');

						if($O && !$hideOrganizer_from_eventCard){
							$EventData = array_merge($EventData, $O);
							$_eventcard['organizer'] = array();	
						}

					// Custom fields						
						$cmf_etop_data = array();

						for($x =1; $x<$custom_meta_fields_count+1; $x++){

							if( !empty($this->evopt1['evcal_ec_f'.$x.'a1']) && !empty($this->evopt1['evcal__fai_00c'.$x])){
								
								// check if hide this from frontend 
								if(empty($this->evopt1['evcal_ec_f'.$x.'a3']) || $this->evopt1['evcal_ec_f'.$x.'a3']=='no'){

									$event_custom_data = $EVENT->get_custom_data( $x);

									if( empty($event_custom_data['value'])) continue;

									$faicon = $this->evopt1['evcal__fai_00c'.$x];
									$visibility_type = !empty($this->evopt1['evcal_ec_f'.$x.'a4'])? $this->evopt1['evcal_ec_f'.$x.'a4']: 'all';

									// field name
									$def = $this->evopt1['evcal_ec_f'.$x.'a1']; // default custom meta field name
									$i18n_nam = eventon_get_custom_language( $this->evopt2,'evcal_cmd_'.$x, $def);


									// field value
									$_v = $event_custom_data['value'];
									//if( $this->evopt1['evcal_ec_f'.$x.'a2'] == 'textarea') $_v = 't';
									
									$_eventcard['customfield'.$x] = $cmf_etop_data[ 'cmd'.$x ] = array(
										'imgurl'=> $faicon,
										'x'=>$x,
										'field_name'=> $i18n_nam,
										'value'		=> $_v,
										'valueL'	=> $event_custom_data['valueL'],
										'_target'	=> $event_custom_data['target'],
										'type'=> $this->evopt1['evcal_ec_f'.$x.'a2'],
										'login_needed_message'=> ( (evo_settings_check_yn( $this->evopt1 , 'evcal_ec_f'.$x.'a5') && !$is_user_logged_in && $visibility_type=='loggedin')? 
											$this->helper->get_field_login_message(): '' ),
										'visibility_type'=> $visibility_type
									);
								}
							}
						}

						//print_r($_eventcard['customfield1']);

						$EventData['cmf_data'] = $cmf_etop_data;

					// LEARN MORE and ICS
						$_eventcard['learnmore'] = array();
						$_eventcard['addtocal'] = array();

					// Related Events
						if($this->helper->_is_card_field('relatedEvents')){
							$_eventcard['relatedEvents'] = array(
								'events'=> json_decode($EVENT->get_prop('ev_releated'))
							);
						}

					// Virtual Event details
						if($this->helper->_is_card_field('virtual')){
							$_eventcard['virtual'] = array();
						}

					// social share
						$_eventcard['evosocial']= array();

					// Health Guidance details
						if($this->helper->_is_card_field('health')){
							$_eventcard['health'] = array();
						}

					// EVENT COLOR
						// override event colors
						// hex color passed via wp_query and etc_override applied via _convert_to_readable_eventdata()
						$event_color = $event_['hex_color'];
						$EventData['color'] = '#'.str_replace('#', '', $event_color); // remove #
						if( $ev_grad = $EVENT->get_gradient() ){
							$EventData['bggrad'] = $ev_grad;
						}
			
				// open event data array for pluggable filters
					$EventData = apply_filters('evo_event_data_array', $EventData, $EVENT, $this);

				// BUILD EVENT TOP
					$eventtop_html = $eventop_fields_= '';
									
					$EventData['eventtop_fields'] = $eventtop_fields;
					$EventData['eventtop_day_block'] = true;
					$EventData['evvals'] = $ev_vals;
					$EventData['ri'] = $__repeatInterval;

										
					// CONSTRUCT event top html
					$eventtop_html =  $structure->get_event_top(  $EventData, $eventtop_fields );						
					$eventtop_html = apply_filters('eventon_eventtop_html',$eventtop_html);
					//$eventtop_html = '';


				// (---) hook for addons
					$html_info_line = apply_filters('eventon_event_cal_short_info_line', $eventtop_html);


				// BUILD EVENT CARD					
					$_eventcard = $_eventcard_old = apply_filters('eventon_eventcard_array', $_eventcard, $ev_vals, $event_id, $__repeatInterval, $EVENT);

					if( isset($SC['eventtop_style']) && $SC['eventtop_style'] == '3a' ){
						$_event_card_on = false;
					}

					if($_event_card_on && !empty($_eventcard) && count($_eventcard)>0){
						
						ob_start();

						echo "<div class='event_description evcal_eventcard ".( $_is_eventCardOpen?'open':null)."' ".( $_is_eventCardOpen? 'style="display:block"':'style="display:none"').">";

						// Get event card HTML content
						echo  $structure->get_event_card( 
							apply_filters('evo_eventcard_array_aftersorted', $_eventcard, $_eventcard_old, $EVENT) , 
							$EventData, $this->evopt1, $this->evopt2,
							(isset($SC['ep_fields']) ? $SC['ep_fields'] :'')
						);
						

						echo "</div>";
						$html_event_detail_card = ob_get_clean();
					}

					/** Trigger attributes **/
						$event_description_trigger =  "desc_trig";
						$_eventInAttr['data-gmtrig'] = (!empty($ev_vals['evcal_gmap_gen']) && $ev_vals['evcal_gmap_gen'][0]=='yes')? '1':'0';
					

					// if UX to be open in new window then use link to single event or that link					
						$_rest_href = '';			
						if($EVENT->get_prop('evcal_exlink') && $event_ux_val =='4'){
							$_rest_href = 'href="'.$EventData['event_permalink'] .'"';
						}
						$_eventInAttr['rest'][] = ($EVENT->get_prop('evcal_exlink') && $event_ux_val!='1' )?
							'data-exlk="1" '.$_rest_href	:'data-exlk="0"';

					// Event link target
						$_eventInAttr['rest'][] =  $EVENT->check_yn('_evcal_exlink_target')? 
							'target="_blank" rel="noopener noreferrer"':'';

				// SCHEME SEO		
					$__scheme_attributes = ($show_schema) ?"itemscope itemtype='http://schema.org/Event'":'';
					$__scheme_data = $structure->get_schema($EventData, $_eventcard);

				
				// CLASES - attribute
					$_eventClasses [] = 'eventon_list_event';
					$_eventClasses [] = 'evo_eventtop';
					$_eventClasses [] = $EventData['_status'];
					$_eventClasses [] = (isset($event_['event_past']) && $event_['event_past'] =='yes')? 'past_event':'';
					$_eventClasses [] = 'event';
					if($__month_long_event) $_eventClasses [] = 'month_long';
					if($__year_long_event) $_eventClasses [] = 'year_long';

					if( $EVO_Event->is_hide_endtime()) $_eventClasses[] = 'no_et';

					$_eventInClasses[] = $_event_date_HTML['class_daylength'];
					$_eventInClasses[] = 'evcal_list_a';
					if($EVO_Event->is_all_day()) $_eventInClasses[] = 'allday';


					$_eventInClasses_ = $this->helper->get_eventinclasses(array(
						'existing_classes'=>$_eventInClasses,
						'show_et_ft_img'=>(!empty($SC['show_et_ft_img'])?$SC['show_et_ft_img']:'no'),
						'img_thumb_src'=>$img_thumb_src,
						'event_type'=>	$event_type,
						'event_description_trigger'=>$event_description_trigger,
						'monthlong'=>	$__month_long_event,
						'yearlong'=>	$__year_long_event,
					));

					// show limit styles
					if( !empty($SC['show_limit']) && $SC['show_limit']=='yes' 
						&& !empty($SC['event_count']) 
						&& $SC['event_count']>0 
						&& $__count> $SC['event_count']
					){
						$_eventAttr['style'][] = "display:none; ";
						$_eventClasses[] = 'evSL';
					}


					$eventbefore = '';
					$p_elm_styles = array();
					
					// TILES STYLE
						if(!empty($SC['tiles']) && $SC['tiles'] =='yes'){
							// boxy event colors
							// if featured image exists for an event
							if(!empty($img_src) && $SC['tile_bg']==1){

								// background image size
								$image_size = isset($SC['tile_bg_size'])? $SC['tile_bg_size']: 'full';

								$image = $img_src;

								if($image_size =='med') $image = $img_med_src;
								if($image_size =='thumb') $image = $img_thumb_src;

								$_this_style = 'background-image: url('.$image.'); background-color:'.$EventData['color'].';';

								$p_elm_styles['background-image'] = 'url('.$image.')';
								$p_elm_styles['background-color'] = $EventData['color'];

								$_eventClasses[] = 'hasbgimg';
							}else{
								$_this_style = 'background-color: '.$EventData['color'].';';
								$p_elm_styles['background-color'] = $EventData['color'];
								$_eventClasses[] = 'noimg';

								if( $SC['tile_bg'] =='0'){ // color bg	
									// gradient color
										if( $ev_grad = $EVENT->get_gradient() ){
											$_eventInAttr['style'][] = 'background-image: '.$ev_grad.';';
										}
								}	

							}
							

							// support different tile style , with top box
							// top box tile
							if(!empty($SC['tile_style']) && $SC['tile_style'] !='0'){
								$topbox_topbox_height = $SC['tile_height']!= 0? ((int)$SC['tile_height']) -110: 150;
								$topbox_padding_top = $topbox_topbox_height+35;								
														

								// tile style = 1; details under color tile
								if( $SC['tile_style'] == 1){
									if( $SC['tile_bg'] =='0'){ // color bg		

										$_eventInAttr['style'][] = 'border-color:'.$EventData['color'].';';	
										//$_eventClasses [] = 'color';
									
									}else{ 
										$_eventInAttr['style'][] = 'background-color:'.$EventData['color'].';';	
										// gradient color
										if( $ev_grad = $EVENT->get_gradient() ){
											$_eventInAttr['style'][] = 'background-image: '.$ev_grad.';';
										}
									}

									// img bg
									if( $SC['tile_bg'] =='1'){
										$topbox_topbox_height = $topbox_topbox_height + 50;
										$topbox_padding_top = $topbox_topbox_height + 35;
										
										// no event image
										if(empty($img_src)){
											 $topbox_padding_top = 80;
											 $topbox_topbox_height = 0;
										}
									}
									
								}
								
								// tile style = 2 - details under clean tile
								if( $SC['tile_style'] == 2){

									// color background
									if( $SC['tile_bg'] == '0'){
										$topbox_topbox_height = 30;
										$topbox_padding_top = $topbox_topbox_height + 55;
									}

									// img bg
									if( $SC['tile_bg'] == '1'){
										// no image
										if( empty($img_src)){
											$topbox_topbox_height = 30;
											$topbox_padding_top = $topbox_topbox_height + 55;
										}
									}
									
									$_eventInAttr['style'][] = 'border-color:'.$EventData['color'].';';	
								}
								
								// complete items
								$eventbefore = '<div class="evo_boxtop" style="'.$_this_style.'height:'.$topbox_topbox_height.'px;"></div>';

								$_eventInAttr['style'][] = 'padding-top: '.$topbox_padding_top.'px;';
								$p_elm_styles = array();

							}else{
								//$_eventAttr['style'][] = $_this_style;
							}

							// tile height
							if($SC['tile_height']!=0){
								$_eventAttr['style'][] = 'min-height: '. (int)$SC['tile_height'].'px;';
							}

							// tile count
							if($SC['tile_count']!=2){}
						}else{

							// event top style
							if( isset($__shortC_arg['eventtop_style']) && 
								($__shortC_arg['eventtop_style'] == 1 || 
									$__shortC_arg['eventtop_style'] == 2 || 
									$__shortC_arg['eventtop_style'] == 3 ) 
							){
								$_eventInAttr['style'][] = 'background-color: '.$EventData['color'].';';
								
								// gradient color
								if( $ev_grad = $EVENT->get_gradient() ){
									$_eventInAttr['style'][] = 'background-image: '.$ev_grad.';';
								}
							}else{
								$_eventInAttr['style'][] = 'border-color: '.$EventData['color'].';';
							}	
						}



					// Unique repeating event class name
						$_eventClasses[] = 'event_'.$EVENT->ID.'_'.$EVENT->ri;

				$_eventAttr['id'] = 'event_'.$EVENT->ID .'_'. $EVENT->ri;
				$_eventAttr['class'] = $this->helper->implode( apply_filters('evo_event_etop_class_names', $_eventClasses, $EVENT, $this ) );
				$_eventAttr['data-event_id'] = $event_id;
				$_eventAttr['data-ri'] = $EVENT->ri.'r';
				$_eventAttr['data-time'] = $EVENT->start_unix.'-'.$EVENT->end_unix;
				$_eventAttr['data-colr'] = $EventData['color'];
				$_eventAttr['rest'][] = $__scheme_attributes;

				$atts = $this->helper->get_attrs( apply_filters('evo_cal_eventtop_attrs', $_eventAttr, $EVENT));

				$_eventInAttr['id']=$unique_id;
				$_eventInAttr['class']=$_eventInClasses_;
				$_eventInAttr['data-ux_val'] = $event_ux_val;
				$_eventInAttr['data-ux_val_mob'] = isset($SC['ux_val_mob']) ? $SC['ux_val_mob']: '-';
				if( $EventData['cancelled'] )  $_eventInAttr['data-text'] = evo_lang('Cancelled');
				$_eventInAttr['data-j'] = apply_filters('evo_event_json_data', array(), $event_id);
				$_eventInAttr['data-runjs'] = apply_filters('evo_event_run_json_onclick', false, $EVENT);
				
				// for ux val 4 show href value for <a>
					if($event_ux_val == '4') $_eventInAttr['href'] = $EVENT->get_permalink();

				// if event is linking to external site
					if($event_ux_val == '2'){
						$_eventInAttr['href'] = $EventData['event_permalink'];

						// open in new window
						if($EVENT->check_yn('_evcal_exlink_target')){
							$_eventInAttr['target'] = '_blank';
						}						
					}


				$attsIn = $this->helper->get_attrs( apply_filters('evo_cal_eventtop_in_attrs',$_eventInAttr, $EVENT->ID, $EVENT));

				// event item html
					$p_styles = '';
					foreach( apply_filters('evo_event_desc_trig_outter_styles', $p_elm_styles, $this, $EVENT) as $c=>$n){
						$p_styles .= $c .':'. $n .';';
					}

					$html_tag_start = ($html_tag=='a')?'p class="desc_trig_outter" style="'. $p_styles .'"><a': $html_tag;

					$html_tag_end = ($html_tag=='a')?'a></p': $html_tag;

				// build the event HTML
				$event_html_code = "<div {$atts}>{$__scheme_data}{$eventbefore}
				<{$html_tag_start} {$attsIn} >{$html_info_line}</{$html_tag_end}>".$html_event_detail_card."<div class='clear end'></div></div>";


				// prepare output
				$months_event_array[]=array(
					'event_id'=>$event_id,
					'srow'=>$event_start_unix,
					'erow'=>$event_end_unix,
					'content'=>$event_html_code
				);

			endforeach;

			}else{// if event list is not an array
				$months_event_array;
			}


			return $months_event_array;
		}

	// convert url based on ssl
		function _convert_ssl_url($url){

			if( strpos($url, '://') !== false){

				//Correct protocol for https connections
			  	list($protocol, $uri) = explode('://', $url, 2);

			  	if(is_ssl()) {
			    	if('http' == $protocol)	$protocol = 'https';
			  	} else {
			    	if('https' == $protocol) $protocol = 'http';
			  	}

			  	return $protocol.'://'.$uri;
			}else{
				return $url;
			}
		}

	// convert wp query event data into readable event data array
	// @+ 2.8
		function _convert_to_readable_eventdata($event_data, $EVENT){
			$A = array();

			
			foreach($event_data as $F=>$V){
				$A[$F] = $V;
			}

			// add true/false values
			foreach(array(
				'year_long'=>'evo_year_long',
				'month_long'=>'month_long_event',
				'featured'=>'_featured',
			) as $F=>$V){
				$A[ $F ] = $EVENT->check_yn( $F );
				
			}

			$SC = $this->shortcode_args;
			$calendar_defaults = $this->helper->get_calendar_defaults();	

			// other event post values u 4.5.8
			$A['timezone_offset'] = $EVENT->utcoff;
			$A['gmt'] = $EVENT->gmt;

			// unique event ID with repeat interval
			$A['_ID'] = $event_data['ID'].'_'. $event_data['ri'];

			// event color as hex			
			if( $color = $EVENT->get_hex()){
				$A['hex_color'] = $color;
			}else{
				$A['hex_color'] = $calendar_defaults['color'];
			}

			if(isset($SC['etc_override']) && $SC['etc_override'] =='yes'){
				$tax = $EVENT->get_tax_ids();

				//print_r($tax['event_type']);
				if(isset($tax['event_type']) ){

					$C = '';
					foreach($tax['event_type'] as $et){
						if( !isset($et['et_color'])) continue;
						if( !empty($C)) continue;
						$C = $et['et_color'];
					}

					if(!empty($C)) $A['hex_color'] = $C;
				}	
			}

			return $A;
		}

	// generate events data via wp_query -- @4.0.2
		public function get_events_from_wp_query($args = array() ){
			$defaults = array(
				'wp_args'=>array(),
				'hide_past'=> 'no',
			);
			$args = array_merge($defaults, $args);

			$wp_args= array(
				'posts_per_page'=>-1,
				'post_type' => 'ajde_events',
				'post_status'=>'any'			
			);
			$wp_args = (isset($args['wp_args']))? array_merge($wp_args,$args['wp_args']): $wp_args;
			
			$wp_args = $this->filtering->apply_evo_filters_to_wp_argument( $wp_args );

			$events = new WP_Query($wp_args);

			return ($events->have_posts()) ? $events: false;
		}

	// generate event data for all eventON events
	// @updated 4.5.6
		public function get_all_event_data($args = array()){
			
			$evo_opt = EVO()->cal->get_op('evcal_1');

			$events = $this->get_events_from_wp_query($args);

			if( !$events) return array();			
			
			$designated_meta_fields = array(
				'publish_status'=>'publish_status',
				'evcal_event_color'=>'color',
				'evcal_subtitle'=>'event_subtitle',
				'evcal_lmlink'=>'learnmore_link',
				'_featured'=>'featured',
			);
			

			$output = array();
			if($events->have_posts()):
				while($events->have_posts()): $events->the_post();

					$EVENT = new EVO_Event($events->post->ID, '','', true, $events->post);

					$event_id = $events->post->ID;
					$ev_vals = $EVENT->get_data();

					$output[$event_id]['post_status'] = $EVENT->post_status;
					$output[$event_id]['content'] = $EVENT->content;
					
					// event name
						$output[$event_id]['name'] = $EVENT->get_title();

					// permalink 
						$output[$event_id]['permalink'] = $EVENT->is_virtual() ? $EVENT->virtual_url() : $EVENT->get_permalink();

					// date times
						$output[$event_id]['start']= $row_start = $EVENT->start_unix;
						$output[$event_id]['end']= $row_end = $EVENT->start_unix + $EVENT->duration;

					// if hide past skip those
						if( isset($args['hide_past']) && $args['hide_past'] =='yes' && $this->current_time < $row_end) continue;

					// details
						$output[$event_id]['details'] = EVO()->frontend->filter_evo_content( $EVENT->content); 

					// repeating event
						if( $EVENT->is_repeating_event() )
							$output[$event_id]['repeats'] = $EVENT->get_repeats();

					// Event timezone
						if( !empty($ev_vals['evo_event_timezone']))
							$output[$event_id]['event_timezone'] = $EVENT->get_prop('evo_event_timezone');

					// designated meta fields
						foreach($designated_meta_fields as $field=>$name){
							if(!empty($ev_vals[$field]))
								$output[$event_id][$name] = $ev_vals[$field][0];
						}

					// time ext type
						$output[$event_id]['year_long_event'] = ( $EVENT->is_year_long() ) ? 'yes':'no';
						$output[$event_id]['month_long_event'] = ( $EVENT->is_month_long() ) ? 'yes':'no';
						$output[$event_id]['all_day_event'] = ( $EVENT->is_all_day() ) ? 'yes':'no';

					// image
						if(has_post_thumbnail()){
							$img_id =get_post_thumbnail_id($event_id);
							$img_src = wp_get_attachment_image_src($img_id,'full');
							if($img_src) $output[$event_id]['image_url'] = $img_src[0];
						}

					// location
						$location_terms = wp_get_post_terms($event_id, 'event_location');
						if ( $location_terms && ! is_wp_error( $location_terms ) ){
							$location_tax_id =  $location_terms[0]->term_id;

							//$LocTermMeta = get_option( "taxonomy_$location_tax_id");
							$LocTermMeta = evo_get_term_meta('event_location',$location_tax_id);

							// location taxonomy id
								$output[$event_id]['location_tax'] = $location_tax_id;

							$output[$event_id]['location_name'] = $location_terms[0]->name;

							// location address
							if(!empty( $LocTermMeta['location_address']))
								$output[$event_id]['location_address'] = $LocTermMeta['location_address']; 

							// Lat Long
							if( !empty( $LocTermMeta['location_lat']) && !empty( $LocTermMeta['location_lon']) ){
								$output[$event_id]['location_lat'] = $LocTermMeta['location_lat'];
								$output[$event_id]['location_lon'] = $LocTermMeta['location_lon'];
							}	

							// location link
							if(!empty( $LocTermMeta['evcal_location_link']))
								$output[$event_id]['location_link'] = $LocTermMeta['evcal_location_link']; 

							// location image
							if(!empty( $LocTermMeta['evo_loc_img']))
								$output[$event_id]['location_img'] = $LocTermMeta['evo_loc_img']; 

							// location description 
							$output[$event_id]['location_desc'] = $location_terms[0]->description;							
						}

					// Organizer
						$organizer_terms = wp_get_post_terms($event_id, 'event_organizer');
						if ( $organizer_terms && ! is_wp_error( $organizer_terms ) ){
							$organizer_term_id =  $organizer_terms[0]->term_id;

							// /$orgTermMeta = get_option( "taxonomy_$organizer_term_id");
							$orgTermMeta = evo_get_term_meta('event_organizer',$organizer_term_id);

							// organizer initial
								$output[$event_id]['organizer_tax'] = $organizer_term_id;
								$output[$event_id]['organier_name'] = $organizer_terms[0]->name;
								
							// organizer address
							if(!empty( $orgTermMeta['evcal_org_address']))
								$output[$event_id]['organizer_address'] = stripslashes($orgTermMeta['evcal_org_address']); 

							// organizer contact
							if(!empty( $orgTermMeta['evcal_org_contact']))
								$output[$event_id]['organizer_contact'] = stripslashes($orgTermMeta['evcal_org_contact']);

							// organizer link
							if(!empty( $orgTermMeta['evcal_org_exlink']))
								$output[$event_id]['organizer_link'] = stripslashes($orgTermMeta['evcal_org_exlink']);

							// organizer image
							if(!empty( $orgTermMeta['evo_org_img']))
								$output[$event_id]['organizer_img'] = stripslashes($orgTermMeta['evo_org_img']);

							// organizer description 
							$output[$event_id]['organizer_desc'] = $organizer_terms[0]->description;
						}

					// Custom fields
						$_cmf_count = evo_retrieve_cmd_count($evo_opt);
						for($x =1; $x<$_cmf_count+1; $x++){
							if( !empty($evo_opt['evcal_ec_f'.$x.'a1']) && !empty($evo_opt['evcal__fai_00c'.$x])	&& !empty($ev_vals["_evcal_ec_f".$x."a1_cus"])	){

								// check if hide this from eventCard set to yes
								if(empty($evo_opt['evcal_ec_f'.$x.'a3']) || $evo_opt['evcal_ec_f'.$x.'a3']=='no'){
								
									$output[$event_id]['customfield_'.$x] =  array(
										'x'=>$x,
										'value'=>$ev_vals["_evcal_ec_f".$x."a1_cus"][0],
										'valueL'=>( (!empty($ev_vals["_evcal_ec_f".$x."a1_cusL"]))?
											$ev_vals["_evcal_ec_f".$x."a1_cusL"][0]:null ),
										'_target'=>( (!empty($ev_vals["_evcal_ec_f".$x."_onw"]))?
											$ev_vals["_evcal_ec_f".$x."_onw"][0]:null ),
										'type'=>$evo_opt['evcal_ec_f'.$x.'a2'],
										'visibility_type'=> (!empty($evo_opt['evcal_ec_f'.$x.'a4'])? $evo_opt['evcal_ec_f'.$x.'a4']: 'all')
									);
								}
							}
						}

					// event types
						for($y=1; $y<=evo_get_ett_count($evo_opt);  $y++){
							$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
							$terms = get_the_terms( $event_id, $_ett_name );

							if ( $terms && ! is_wp_error( $terms ) ){
								foreach ( $terms as $term ) {
									$output[$event_id][$_ett_name][$term->term_id] = $term->name;
								}
							}
						}

					// all meta values
						$output[$event_id]['pmv'] = $ev_vals;
					
				endwhile;
				wp_reset_postdata();
			endif;

			return $output;
		}


		//return tranlated language
		function lang($field, $default){
			$lang = !empty($this->shortcode_args['lang'])? $this->shortcode_args['lang']: 'L1';
			return eventon_get_custom_language($this->evopt2, $field,$default, $lang);
		}

	/**
	 * Deprecated functions since 2.2.22
	 */
		// load scripts
		function load_evo_files(){
			EVO()->frontend->load_evo_scripts_styles();
		}
		// SHORT CODE variables
		function get_supported_shortcode_atts(){
			return $this->shell->get_supported_shortcode_atts();
		}
		// ABOVE calendar header
		public function cal_above_header($args){
			return $this->body->cal_above_header($args);
		}
		// HEADER
		public function calendar_shell_header($arg){
			return $this->body->calendar_shell_header($arg);
		}
		// FOOTER
		public function calendar_shell_footer(){
			return $this->body->calendar_shell_footer();
		}		
		// the reused variables and other things within the calendar
		function reused(){
			$this->shell->reused();
		}
		// SORT event list array
		public function evo_sort_events_array($events_array, $args=''){
			return $this->shell->evo_sort_events_array($events_array);
		}
		// Apply filters to events lists array -- DEP v2.8
		function _filter_events_list($EL, $args){
			return $this->filtering->apply_filters_to_event_list($EL,'event_count');					
		}
		function separate_eventlist_to_months($EL){
			$O = '';
			$H = $this->generate_event_data($EL);
			foreach($H as $event){
				$O.= $event['content'];
			}
			return $O. '<span style="color:red">Notice: EventON addons need updated!</span>';
		}
		public function eventon_generate_events($args=''){
			 return $this->_generate_events(  'html');
		}
		function prefilter_events($EL){
			return $this->shell->move_important_events_up( $EL);
		}
		public function evo_process_event_list_data($EL, $args=''){
			return $this->filtering->no_more_events_add( $EL);
		}
		// DEP 2.8
		public function eventon_generate_calendar($args=''){
			
			if(!$this->calendar_pre_check()) return;

			$this->_cal_reset();

			// PROCESS & extract the variable values
			$A = $this->shortcode_args;
			extract($A);

			// Before beginning the eventON calendar Action
			do_action('eventon_cal_variable_action', $A);
			
			$content = '';
			$content.= $this->body->get_calendar_header(array(
				'focused_month_num'=>$fixed_month,
				'focused_year'=>$fixed_year
				)
			);						
			$this->reused();

			$content .= $this->_generate_events( 'html');			
			$content .= $this->body->calendar_shell_footer();

			// action to perform at the end of the calendar
			do_action('eventon_cal_end');

			$this->_cal_reset('end');
			return  $content;
		}

} // class EVO_generator
?>