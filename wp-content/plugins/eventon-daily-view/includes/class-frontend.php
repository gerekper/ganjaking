<?php
/**
 * DailyView front-end
 * @version 	1.0.5
 * @updated 	2019
 */

class evodv_frontend{

	public $print_scripts_on;
	public $day_names = array();
	public $focus_day_data= array();
	public $shortcode_args;
	public $atts;

	public $events_list = array();

	function __construct(){
		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action( 'wp_footer', array( $this, 'print_dv_scripts' ) ,15);
		add_action('evo_addon_styles', array($this, 'styles') );

		add_filter('evo_global_data', array($this, 'global_data'), 10, 1);
		add_filter('evo_init_ajax_data', array($this, 'init_ajax_data'), 10, 2);	

		add_action('eventon_below_sorts', array($this, 'add_loading'), 10, 2);

		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);
		add_action('evo_view_switcher_items', array($this, 'view_switcher'),10,2);

	}

	// CAL loading
		public function add_loading($content, $args){

			if($args['calendar_type'] != 'daily') return;
			?>
			<div class='evo_ajax_load_events evodv_pre_loader'>
				<?php echo ($args['dv_view_style']!= 'def') ? "<span style='height: 230px'></span>":'';?>
				<?php echo ($args['dv_view_style']!= 'oneday') ? "<span style='height: 100px'></span>":'';?>
			</div>
			<?php
		}

	// MAIN CAL
		function getCAL($atts){
			if( !is_array($atts)) $atts = array();

			// initiate 
			EVODV()->is_running_dv=true;
			EVODV()->load_script =true;	
			EVODV()->frontend->only_dv_actions();			
			add_filter('eventon_shortcode_defaults', array(EVODV()->shortcodes,'add_shortcode_defaults'), 10, 1);		

			$atts['calendar_type'] = 'daily';
			$atts['number_of_months'] = 1;
			$this->atts = $atts;

			$header_args = array();

			// compromise for unused shortcode
			if(isset($atts['today']) && $atts['today'] == 'yes'){
				$atts['dv_view_style'] = 'oneday';
			}

			// just one day
			if( isset($atts['dv_view_style']) && $atts['dv_view_style']=='oneday'){
				$header_args['date_header'] = false;
			}

			$O = EVO()->calendar->_get_initial_calendar( $atts , $header_args );	
						
			// close
			$this->remove_dv_only_actions();
			EVODV()->is_running_dv=false;
			remove_filter('eventon_shortcode_defaults', array(EVODV()->shortcodes,'add_shortcode_defaults'));
			return $O;
		}

	// BEFORE INIT
		function evo_init_ajax_before($atts){
			$SC = EVO()->calendar->shortcode_args;

			if($SC['calendar_type'] != 'daily') return;

			// if fixed day is not set, use current date
			if(empty($atts['fixed_day'])){
				$DD = EVO()->calendar->DD;

				$DD->setTimestamp( EVO()->calendar->current_time );

				EVO()->calendar->_update_sc_args( 'fixed_day', $DD->format('d'));
				EVO()->calendar->_update_sc_args( 'fixed_month', $DD->format('n'));
				EVO()->calendar->_update_sc_args( 'fixed_year', $DD->format('Y'));
			}


			$this->set_day_unix_range( $SC['dv_view_style'] == 'oneday'? 'day':'month');			
		}

	// INIT EVO
		function global_data($A){
			// tell the page dv is on page to load dv specific codes
			if(EVODV()->load_script) $A['calendars'][] = 'EVODV';
			return $A;
		}
		function init_ajax_data($A, $global){
			if(isset($global['calendars']) && in_array('EVODV', $global['calendars'])){
				
				ob_start();
				?><div class='evodv_CD evoADDS evodv_current_day dv_vs_{{dv_view_style}}' style='display:none'>
					<p class='evodv_dayname'>{{fixed_day_name}}</p>
					<p class='evodv_daynum'><span class='evodv_daynum_switch prev' data-dir='prev'><i class='fa fa-angle-left'></i></span><b class='evodv_current_fixed_day'>{{fixed_day}}</b><span class='evodv_daynum_switch next' data-dir='next'><i class='fa fa-angle-right'></i></span></p>
					<p class='evodv_events' style='display:none'></p>
				</div><?php
				$A['temp']['evodv_cd'] = ob_get_clean();

				
				ob_start();?>
				<div class='evodv_DL evoADDS eventon_daily_list {{#Cal_def_check hide_arrows}}dvlist_noarrows{{/Cal_def_check}} dv_vs_{{dv_view_style}}' style='display:none'>
					<div class='eventon_dv_outter'>
						<span class='evodv_action prev'></span>
						<div class='eventon_daily_in' data-left='' style='width:{{width}}px; margin-left:{{marginLeft}}px'>
							{{#each days}}
							<p class="evo_dv_day evo_day {{evo_dv_day_classes}}" data-date='{{@key}}' data-events='{{COUNT e}}' data-unix='{{su}}'>
								<span class='evo_day_name'>{{GetDMnames dn "d3"}}</span>
								<span class='evo_day_num'>{{@key}}</span>
								<span class='evoday_events'></span>
							</p>
							{{/each}}
						</div>
						<span class='evodv_action next'></span>
					</div>
				</div><?php
				$A['temp']['evodv_list'] = ob_get_clean();
				
				// TEXT strings
				$A['txt']['events'] = eventon_get_custom_language('', 'evcal_lang_events','Events' );
				$A['txt']['event'] = evo_lang('Event' );
			}
			return $A;
		}

	// process calendar range and initial values
		function set_day_unix_range($type='day'){

			$SC = EVO()->calendar->shortcode_args;
			$atts = $this->atts;
			extract($SC);

			$DD = EVO()->calendar->DD;		


			if(!empty($atts['fixed_day']) && $atts['fixed_day']!=0 && $SC['fixed_month']!=0 && $SC['fixed_year']!=0){
				$DD->setDate($SC['fixed_year'], $SC['fixed_month'], $SC['fixed_day']);
			}else{
				$DD->setTimestamp( EVO()->calendar->current_time );	
				$SC['fixed_day'] = (int)$DD->format('d');	
			}	

			$DD->setTime(0,0,0);		

			// month and year
			if($month_incre != 0){
				if( strpos($month_incre, '+') === false  && strpos($month_incre, '-') === false) $month_incre = '+'.$month_incre;

				if( strpos($month_incre, '+') !== false) $month_incre = '+'. (int)$month_incre;
				if( strpos($month_incre, '-') !== false) $month_incre = '-'. (int)$month_incre;

				$DD->modify($month_incre.'month');
			}

			// day increment
			if($day_incre != 0 ){
				if( strpos($month_incre, '+') === false  && strpos($month_incre, '-') === false) $month_incre = '+'.$month_incre;
				$DD->modify( $day_incre.'days');
				$SC['fixed_day'] = (int)$DD->format('d');
			}

			// month range type
			if($type == 'month'){
				$DD->modify('first day of this month');			
			}

			$SC['focus_start_date_range'] = $DD->format('U');
			$SC['fixed_month'] = $DD->format('n');
			$SC['fixed_year'] = $DD->format('Y');
			
			if($type == 'day'){
				$SC['focus_end_date_range'] = $DD->format('U') + 86399;
			}else{
				$DD->modify('last day of this month');	
				$DD->setTime(23,59,59);			
				$SC['focus_end_date_range'] = $DD->format('U');	
			}
			

			EVO()->calendar->update_shortcode_arguments($SC);		
		}

	// Other Additions
		function view_switcher($A, $args){
			if($args['view_switcher'] == 'yes'){
				$DATA = array();

				$DATA['c'] = 'evoDV';
				$DATA['el_visibility'] = 'hide_events';

				EVODV()->load_script = true;
				$A['evodv'] = array($DATA, 'daily', evo_lang('Day'));
			}

			return $A;
		}

	// STYLES
		function styles(){
			ob_start();
			include_once(EVODV()->assets_path.'dv_styles.css');
			echo ob_get_clean();
		}	
		public function register_styles_scripts(){			
			// Load dailyview styles conditionally
			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'evo_dv_styles',EVODV()->assets_path.'dv_styles.css');

			wp_register_script('evo_dv_mousewheel',EVODV()->assets_path.'jquery.mousewheel.min.js', array('jquery'), EVODV()->version, true );
			wp_register_script('evo_dv_script',EVODV()->assets_path.'dv_script.js', array('jquery'), EVODV()->version, true );	
			
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){
			wp_enqueue_script('evo_dv_mousewheel');
			wp_enqueue_script('evo_dv_script');	
		}
		function print_styles(){	wp_enqueue_style( 'evo_dv_styles');		}
		function print_dv_scripts(){	
			if(EVODV()->load_script)	$this->print_scripts();
		}

	// other supported functions
		public function only_dv_actions(){
			$SC = EVO()->calendar->shortcode_args;
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);			
		}

		public function remove_dv_only_actions(){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));	
		}

		// add class name to calendar header for DV
			function eventon_cal_class($name){
				$name[]='evoDV';
				return $name;
			}

		// three letter day array
			function set_three_letter_day_names($lang=''){				
				// Build 3 letter day name array to use in the fullcal from custom language
				for($x=1; $x<8; $x++){			
					$evcal_day_is[$x] =eventon_return_timely_names_('day_num_to_name',$x, 'three', $lang);
				}					
				$this->day_names = $evcal_day_is;
			}

		// full length day array
			function set_full_day_names($lang=''){				
				// Build 3 letter day name array to use in the fullcal from custom language
				for($x=1; $x<8; $x++){			
					$evcal_full_day_is[$x] =eventon_return_timely_names_('day_num_to_name',$x, 'full', $lang);		
				}					
				$this->full_day_names = $evcal_full_day_is;
			}

		// full length day array
			function get_full_day_names($dayofweekN, $lang=''){						
				return eventon_return_timely_names_('day_num_to_name',$dayofweekN, 'full', $lang);	
			}
		function days_in_month($month, $year) { 
			return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
		}
}
