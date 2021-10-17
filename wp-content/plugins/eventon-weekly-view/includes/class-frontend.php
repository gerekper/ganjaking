<?php
/**
 * Weeklyview Front-end
 * @version 1.0.11
 */
class EVOWV_frontend{
	public $events_list;		
	public $current_time;		
	public $start_of_week;
	public $focus_data = array(); // updated data for focus time
	public $focus_month;
	private $shortcode_args;
	private $time_format;

	public $week_start_date;
	public $atts = array();


	public function __construct(){
		global $eventon;
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);
		add_action( 'wp_footer', array( $this, 'print_wv_scripts' ) ,15);
		add_action('evo_addon_styles', array($this, 'styles') );

		add_action('evo_calendar_defaults', array($this, 'calendar_def'), 10, 3);

		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);
		add_filter('evo_global_data', array($this, 'global_data'), 10, 1);
		add_filter('evo_init_ajax_data', array($this, 'init_ajax_data'), 10, 2);	
		add_action('evo_cal_view_switcher_end', array($this, 'view_switcher'),10,1);

		$this->current_time = EVO()->calendar->current_time;
		$this->time_format = get_option('time_format');
		$this->start_of_week = get_option('start_of_week');		
	}
		
	// MAIN CAL
		function getCAL($atts){

			// INIT
			EVOWV()->is_running_wv = true;
			EVOWV()->load_script = true;
			$this->only_wv_actions('new');	
			
			add_filter('eventon_shortcode_defaults', array(EVOWV()->shortcodes,'add_shortcode_defaults'), 10, 1);	
			
			$atts['calendar_type'] = 'weekly';
			$atts['number_of_months'] = 1;

			// shortcode modification override slide down uxval passed via cal shortcode
				if( isset($atts['week_style']) && $atts['week_style'] == '1' && isset($atts['ux_val']) && $atts['ux_val'] == '1'){
					$atts['ux_val'] = '3';
				}

			$header_args = array('date_header'=>false);
			// hide events on load
			if(isset($atts['hide_events_onload']) && $atts['hide_events_onload']=='yes'){
				$header_args['_classes_evcal_list'] = 'evo_hide';
			}


			$this->atts = $atts;
			
			$O = EVO()->calendar->_get_initial_calendar( $atts , $header_args);	

			// CLOSE
			EVOWV()->is_running_wv = false;
			$this->remove_only_wv_actions('new');
			remove_filter('eventon_shortcode_defaults', array(EVOWV()->shortcodes,'add_shortcode_defaults'));
			return $O;	
		}

	// ALL Calendar Def additions
		function calendar_def($defaults, $options, $SC){

			if(isset($options['evowv_range_timeformat'])){
				$defaults['wv_range_format'] = explode('/', $options['evowv_range_timeformat']);
			} 
			return $defaults;
		}
	// BEFORE INIT
		function evo_init_ajax_before($atts){
			$SC = EVO()->calendar->shortcode_args;

			if($SC['calendar_type'] != 'weekly') return;			

			$this->set_week_unix_date_range($atts);
		}

	// INIT
		function global_data($A){
			// tell the page dv is on page to load dv specific codes
			if(EVOWV()->load_script) $A['calendars'][] = 'EVOWV';
			return $A;
		}
		function init_ajax_data($A, $global){
			if(isset($global['calendars']) && in_array('EVOWV', $global['calendars'])){
				ob_start();
			?>
			<div class='EVOWV_content evoADDS tb_{{table_style}} wk_{{week_style}}'>
				<div class='EVOWV_dates'>
					{{#ifEQ disable_week_switch 'no'}}
						<a class='evowv_prev evowv_arrow' data-dir='prev' data-week='-1'><i class='fa fa-angle-left'></i></a>
					{{/ifEQ}}
					<p class='EVOWV_thisdates_range {{#ifEQ disable_week_switch "no"}}range_switch{{/ifEQ}}'></p>
					{{#ifEQ disable_week_switch 'no'}}
						<div class='EVOWV_change'><i class='fa fa-sort'></i>
							<div class='EVOWV_ranger'>
								<a class='EVOWV_range_mover up'><i class='fa fa-angle-up'></i></a>
								<div class='EVOWV_ranger_handle'>
									<ul class='EVOWV_date_ranges' style=''>
									</ul>
								</div>
								<a class='EVOWV_range_mover down'><i class='fa fa-angle-down'></i></a>
							</div>
						</div>
					{{/ifEQ}}
					{{#ifEQ disable_week_switch 'no'}}
						<a class='evowv_next evowv_arrow' data-dir='next' data-week='+1'><i class='fa fa-angle-right'></i></a>
					{{/ifEQ}}
				</div>
				<div class='EVOWV_grid'></div>
			</div>
			<?php			
			$A['temp']['evowv_top'] = ob_get_clean();
			ob_start();
			?>{{#each days}}
					<div class='evo_wv_day {{today}}{{#ifNEQ newmo "no"}} newmo{{/ifNEQ}}' data-su='{{SU}}'>
						<div class='evowv_daybox'>
							{{#ifNEQ newmo "no"}}<span class='mo_name'>{{GetDMnames newmo "m3"}}</span>{{/ifNEQ}}
							<span class='day_name'>{{GetDMnames DN "d3"}}</span>
							<span class='day_num'>{{D}}</span>
							<span class='day_events'></span>
						</div>
						{{#ifEQ ../week_style "1"}}
							<div class='evowv_col_events'></div>
						{{/ifEQ}}
					</div>
					
				{{/each}}
				<div class='clear'></div>						
			<?php
			$A['temp']['evowv_week'] = ob_get_clean();

			$A['txt']['this_week'] = evo_lang('This Week');
			}
			return $A;
		}

	// SET week range unix values
		function set_week_unix_date_range(  $atts= array() ){
			$SC = EVO()->calendar->shortcode_args;

			// current month data
				$current_timestamp =  EVO()->calendar->current_time;
				$a_week_seconds = (7*24*3600) -1;


			// get date range initially for calendar	
				if( !empty($SC['fixed_month']) && !empty($SC['fixed_year']) && !empty($atts['fixed_week'])){
					$DD = new DateTime($SC['fixed_year'] .'-'. $SC['fixed_month'].'-1');	

					$SC['fixed_week'] = $atts['fixed_week'];
					if(!empty($SC['fixed_week']) && $SC['fixed_week'] > 1 )	$DD->modify( '+'.($SC['fixed_week'] -1).'weeks');	

				}else{
					// set date to first of month, first week of month
					$DD = new DateTime( date('Y-n-d', $current_timestamp));
				}	

				$DD->setTimezone( EVO()->calendar->timezone0 );	

								
				// ajax new week
				if(isset($SC['_in_ws']) && $SC['_in_ws'] >0){

					$DD->setTimestamp( (int)$SC['_in_ws'] );
					if( $SC['week_incre'] != '0' ){
						$week_incre = str_replace('+', '', $SC['week_incre']);
						$week_incre = $week_incre >0 ? '+'. $week_incre: $week_incre;
						$DD->modify( $week_incre.'weeks');
					}
				}else{ // initial adjust focus week based on fixed week and week incre					
					// week incre
					if( $SC['week_incre'] != '0' ){
						$week_incre = str_replace('+', '', $SC['week_incre']);
						$week_incre = $week_incre >0 ? '+'. $week_incre: $week_incre;
						$DD->modify( $week_incre.'weeks');
						$SC['week_incre'] = 0;
					}					
				}


				
				// general start of the week day num
					$start_ow = $this->start_of_week; // sun = 0, mon = 1, sat = 6
					$today_day = $DD->format('w');

					if( $start_ow >1) $dayDif = $today_day -( $start_ow-1);
					if( $today_day > $start_ow ) $dayDif = $today_day - $start_ow;
					if( $today_day == $start_ow ) $dayDif = 0;
					if( $start_ow > $today_day) $dayDif = 7 - $start_ow;

					// week start
					if($dayDif != 0) $DD->modify('-'. $dayDif .'days');	
					
				$SC['focus_start_date_range'] = $DD->format('U');	


				$SC['fixed_year']= $DD->format('Y');
				$SC['fixed_month']= $DD->format('n');

				if(empty($SC['_in_ws'])) $SC['_in_ws'] = $DD->format('U');

				// week end
				$DD->modify( '+'. $a_week_seconds .'seconds');
				$SC['focus_end_date_range'] = $DD->format('U');	

			EVO()->calendar->update_shortcode_arguments($SC);					

			return $SC;
		}

	// Other Additions
		function view_switcher($A){
			if($A['view_switcher'] == 'yes'){
				EVOWV()->load_script = true;
				echo "<span class='evo_vSW evowv ". ($A['calendar_type']=='weekly'?'focus':'')."'>Week</span>";
			}
		}
	
	// STYLES	
		public function register_styles_scripts(){	
			if(is_admin()) return;

			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'evo_wv_styles',
					EVOWV()->addon_data['plugin_url'].'/assets/wv_styles.css',
					array(), EVOWV()->version
				);
			wp_register_script('evo_wv_script',EVOWV()->addon_data['plugin_url'].'/assets/wv_script.js', array('jquery'), EVOWV()->version, true );

			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
		}
		public function print_scripts(){			
			wp_enqueue_script('evo_wv_script');	
		}
		function print_styles(){
			wp_enqueue_style( 'evo_wv_styles');	
		}
		function print_wv_scripts(){
			if(EVOWV()->load_script)	$this->print_scripts();			
		}
		function styles(){
			ob_start();
			include_once(EVOWV()->plugin_path.'/assets/wv_styles.css');
			echo ob_get_clean();
		}
		
	// ONLY for WV calendar actions 
		public function only_wv_actions($type=''){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_wv_actions($type=''){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));			
		}
	// add class name to calendar header for EM
		function eventon_cal_class($name){
			$name[]='evoWV'; 

			$week_style = EVO()->calendar->_get_sc('week_style');
			if( $week_style == '1')	$name[] = 'evoWV_tb';
			return $name;
		}
	// remove class name to calendar header for EM
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoWV', $name)) !== false) {
			    unset($name[$key]);
			}return $name;
		}

	// get the week of the month from week start date
		function _get_week_of_month($start__unix){
			$firstOfMonth = strtotime(date("Y-m-01", $start__unix));
			return intval(date("W", $start__unix)) - intval(date("W", $firstOfMonth)) + 1;
		}

	// three letter day array
		function set_three_letter_day_names($lang=''){
			
			// Build 3 letter day name array to use in the weeklyview from custom language
			for($x=0; $x<=6; $x++){
				$z = $x+1;
				$y = ($z>6)? $z-7: $z;		
				$evcal_day_is[$y] =eventon_return_timely_names_('day_num_to_name',$z, 'three', $lang);				
			}	
			//print_r($evcal_day_is);
			$this->day_names = $evcal_day_is;
		}
	
		function days_in_month($month, $year) { 
			return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
		}

}