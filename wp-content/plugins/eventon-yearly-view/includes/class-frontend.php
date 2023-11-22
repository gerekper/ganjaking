<?php
/**
 * YV front-end
 * @version 	0.1
 * @updated 	2019
 */

class evoyv_frontend{

	public $print_scripts_on;
	public $day_names = array();
	public $focus_day_data= array();
	public $shortcode_args;
	public $atts;

	public $events_list = array();

	function __construct(){
		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action( 'wp_footer', array( $this, 'print_this_scripts' ) ,15);

		add_filter('evo_init_ajax_data', array($this, 'load_init_templates'), 10, 2);
		add_filter('evo_init_ajax_data', array($this, 'init_text_strings'), 10, 2);
		add_action('eventon_calendar_header_content', array($this, 'cal_header_content'), 10, 1);
		add_filter('evo_global_data', array($this, 'global_data'), 10, 1);

		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);

		add_action('evo_cal_view_switcher_end', array($this, 'view_switcher'),10,1);

		// user interaction overrides
		add_filter('evo_one_event_ux_val', array($this, 'ux_override'), 10,2);
	}


	// MAIN CAL
		function getCAL($atts){
			if( !is_array($atts)) $atts = array();

			// initiate dv
			EVOYV()->is_running_yv=true;
			EVOYV()->load_script =true;	
			EVOYV()->frontend->only_yv_actions();			
			add_filter('eventon_shortcode_defaults', array(EVOYV()->shortcodes,'add_shortcode_defaults'), 10, 1);		

			$atts['calendar_type'] = 'yearly';
			$atts['number_of_months'] = 12;
			$atts['hide_mult_occur'] = 'yes';
			$atts['show_repeats'] = 'yes';
			if( isset($atts['ux_val']) && $atts['ux_val'] != 4) $atts['ux_val'] = 3;

			// light box
				add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);

			// set year date range
				$DD = EVO()->calendar->DD;
				$DD->setTimestamp( EVO()->calendar->current_time );
				if(empty($atts['fixed_day']))	$atts['fixed_day']= $DD->format('d');
				if(empty($atts['fixed_month']))	$atts['fixed_month']= $DD->format('n');


				$fixed_year = $atts['fixed_year'] = (empty($atts['fixed_year']) )? $DD->format('Y'): (int)$atts['fixed_year'];	
				$DD->setDate($fixed_year,1,1);	$DD->setTime(0,0,0);
				$atts['focus_start_date_range'] = $DD->format('U');
				$DD->setDate($fixed_year,12,31);	$DD->setTime(23,59,59);
				$atts['focus_end_date_range'] = $DD->format('U');
				

			$O = EVO()->calendar->_get_initial_calendar( $atts , array(
				'date_header'=>false
			));	
			
			
			EVOYV()->frontend->remove_yv_only_actions();
			EVOYV()->is_running_yv=false;
			remove_filter('eventon_shortcode_defaults', array(EVOYV()->shortcodes,'add_shortcode_defaults'));
			return $O;
		}

	// BEFORE INIT
		function evo_init_ajax_before($atts){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'yearly') return;
		}

	// Add year nav header
		function cal_header_content($A){
			if($A['calendar_type'] != 'yearly') return;

			echo "<p id='evcal_cur' class='evo_month_title'>". $A['fixed_year']."</p>";
			echo EVO()->calendar->body->cal_parts_arrows();
		}

	// UX override
		function ux_override( $event_ux_val, $event_ux_val_raw){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'yearly') return $event_ux_val;
			
			if($event_ux_val_raw ==2 ) return 2;
			return $event_ux_val;
		}

	// INIT EVO
		function global_data($A){
			// tell the page dv is on page to load dv specific codes
			if(EVOYV()->load_script) $A['calendars'][] = 'EVOYV';
			return $A;
		}
		function load_init_templates($A, $global){
			if(isset($global['calendars']) && in_array('EVOYV', $global['calendars'])){
				ob_start();
				?><div class='evoyv_year_grid evoADDS' style='display:none' data-d=''>
					
					<div class='evoyv_tip' style='display:none'></div>
					<div class='evoyv_title_tip' style='display:none'>
						<span class='evoyv_ttle_cnt'>3</span><ul class='evoyv_ttle_events'><li style='border-left-color:#FBAD61'>Event Name</li></ul>
					</div>

					{{#each months}}
					<div class='evoyv_month m_{{@key}}' data-m='{{@key}}'>
						<div class='evoyv_month_in'>
							<span class='month_title'>{{GetDMnames @key "m"}}</span>
							<span class='day_names'>
								{{#each day_names}}<span class='day_box'>{{this}}</span>{{/each}}<span class='clear'></span>
							</span>
							<span class='month_box' data-m='{{@key}}'>
								{{{forAdds blanks "<span class='day_box'></span>"}}}
								{{#each days}}
									<span class='evoyv_day day_box d_{{@key}} {{he}}' data-d='{{@key}}' data-su='{{su}}'>
										<span class='day_box_in'>{{@key}}<span class='day_box_color'></span></span>
									</span>
								{{/each}}
								<span class='clear'></span>
							</span>
						</div>
					</div>
					{{/each}}
				</div><?php
				$A['temp']['evoyv_grid'] = ob_get_clean();
					ob_start();
					?><div class='evoyv_lb_header'>
						<span class=''>{{GetDMnames month "m"}} {{day}}</span>
					</div>
					<div class='evoyv_events'>{{{html}}}</div>
					<?php
				$A['temp']['evoyv_lb'] = ob_get_clean();
			}
			return $A;
		}
		function init_text_strings($A, $G){
			if(isset($G['calendars']) && in_array('EVOYV', $G['calendars'])){
				$A['txt']['more'] = evo_lang('More' );
			}
			return $A;
		}

	// STYLES	
		public function register_styles_scripts(){			
			// Load dailyview styles conditionally
			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'evo_yv_styles',EVOYV()->assets_path.'yv_styles.css');

			wp_register_script('evo_yv_mousewheel',EVOYV()->assets_path.'jquery.mousewheel.min.js', array('jquery'), EVOYV()->version, true );
			wp_register_script('evo_yv_script',EVOYV()->assets_path.'yv_script.js', array('jquery'), EVOYV()->version, true );	
			
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
				
		}
		public function print_scripts(){
			wp_enqueue_script('evo_yv_mousewheel');
			wp_enqueue_script('evo_yv_script');	
		}

		function print_styles(){
			wp_enqueue_style( 'evo_yv_styles');	
		}
		function print_this_scripts(){	
			if(EVOYV()->load_script)	$this->print_scripts();
		}
	// Other Additions
		function view_switcher($A){
			if($A['view_switcher'] == 'yes'){
				EVOYV()->load_script = true;
				echo "<span class='evo_vSW evoyv ". ($A['calendar_type']=='yearly'?'focus':'')."'>Year</span>";
				add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);
			}
		}

	// Lightbox calling
		function ligthbox($array){
			$array['evoyv_lightbox']= array(
				'id'=>'evoyv_lightbox',
				'CLclosebtn'=> 'evolbclose_yv',
				'CLin'=>'evoyv_lightbox_body evo_pop_body eventon_events_list evcal_eventcard'
			);return $array;
		}

	// other supported functions
		public function only_yv_actions(){
			$SC = EVO()->calendar->shortcode_args;
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);			
		}

		public function remove_yv_only_actions(){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));	
		}

		// add class name to calendar header 
			function eventon_cal_class($name){
				$name[]='evoYV';	return $name;
			}
		function days_in_month($month, $year) { 
			return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
		}
}
