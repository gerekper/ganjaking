<?php
/**
 * FullCal front-end
 * @version 	1.1.2
 */

class evofc_frontend{

	public $day_names = array();
	public $focus_day_data= array();
	public $shortcode_args;

	function __construct(){
		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action( 'wp_footer', array( $this, 'print_fc_scripts' ) ,15);

		// evo ajax
		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);
		add_filter('evo_global_data', array($this, 'global_data'), 10, 1);
		add_filter('evo_init_ajax_data', array($this, 'init_ajax_data'), 10, 2);	

		// Widget		
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );	

		add_action('evo_cal_view_switcher_end', array($this, 'view_switcher'),10,1);
	}

	// MAIN CAL
		function getCAL($atts){
			if(!is_array($atts)) $atts = array();

			// initiate
			EVOFC()->is_running_fc = true;
			EVOFC()->load_script = true;
			$this->only_fc_actions();
			add_filter('eventon_shortcode_defaults', array(EVOFC()->shortcodes,'add_shortcode_defaults'), 10, 1);
			
			$atts['calendar_type'] = 'fullcal';
			$atts['number_of_months'] = 1;

			// RUN CAL
			$O = EVO()->calendar->_get_initial_calendar( $atts );	

			if(!empty($atts['grid_ux']) && $atts['grid_ux']=='2')
				add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);


			// close
			$this->remove_only_fc_actions();
			EVOFC()->is_running_fc = false;
			remove_filter('eventon_shortcode_defaults', array(EVOFC()->shortcodes,'add_shortcode_defaults'));

			return $O;
		}
		
	// BEFORE INIT CAL
		function evo_init_ajax_before($atts){
			$SC = EVO()->calendar->shortcode_args;

			if($SC['calendar_type'] != 'fullcal') return;

			$SC = EVO()->calendar->shortcode_args;

			// process shortcode defaults
			if(empty($atts['fixed_day'])){
				$DD = EVO()->calendar->DD;
				$DD->setTimestamp( EVO()->calendar->current_time );
				EVO()->calendar->_update_sc_args( 'fixed_day', $DD->format('j'));
			}else{
				// fixed month and year will already be set
				//EVO()->calendar->_update_sc_args( 'fixed_day', (int)$atts['fixed_day'] );
			}

			// day increment on init
			if($SC['day_incre'] += '0'){
				$DD = EVO()->calendar->DD;
				$DD->setTimestamp( EVO()->calendar->current_time );
				$DD->modify( $SC['day_incre'].'days');
				EVO()->calendar->_update_sc_args( 'fixed_day', $DD->format('j') );
			}

		}

	// INIT EVO
		function global_data($A){
			// tell the page dv is on page to load dv specific codes
			if(EVOFC()->load_script) $A['calendars'][] = 'EVOFC';
			return $A;
		}
		function init_ajax_data($A, $G){
			if(isset($G['calendars']) && in_array('EVOFC', $G['calendars'])){
				ob_start(); ?><div class='evofc_month_grid evoADDS eventon_fullcal' style='display:none' data-d=''>
					<div class='evoFC_tip' style='display:none'></div>
					<div class='evofc_title_tip' style='display:none'>
						<span class='evofc_ttle_cnt'>3</span><ul class='evofc_ttle_events'><li style='border-left-color:#FBAD61'>Event Name</li></ul>
					</div>
					<div class='evofc_months_strip{{months_strip_classes}}'></div><div class='clear'></div>
				</div>
				<?php
				$A['temp']['evofc_base'] = ob_get_clean();

				ob_start();?><div class='evofc_month m_{{month}}'>
					<div class='eventon_fc_daynames'>{{#each day_names}}<p class='evofc_day_name evo_fc_day' data-d='{{@key}}'>{{this}}</p>{{/each}}<div class='clear'></div></div>
					<div class='eventon_fc_days'>
						{{{forAdds blanks "<p class='evo_fc_day evo_fc_empty'>-</p>"}}}
						{{#each days}}
							<p class='evofc_day evo_fc_day {{cls}} d_{{@key}}' data-su='{{su}}' data-d='{{@key}}'>{{@key}}<span></span></p>
						{{/each}}
					<div class='clear'></div></div></div><?php 
				$A['temp']['evofc_grid'] = ob_get_clean();

				$A['txt']['more'] = eventon_get_custom_language('', 'evo_lang_more','More' );
			}
			return $A;
		}

		

	// Other Additions
		function view_switcher($A){
			if($A['view_switcher'] == 'yes'){
				EVOFC()->load_script = true;
				echo "<span class='evo_vSW evofc ". ($A['calendar_type']=='fullcal'?'focus':'')."'>Month</span>";
			}
		}


	// STYLES
		public function register_styles_scripts(){
			if(is_admin()) return false;
						
			wp_register_style( 'evo_fc_styles',EVOFC()->addon_data['plugin_url'].'/assets/fc_styles.css','',EVOFC()->version);
			wp_register_script('evo_fc_script',EVOFC()->addon_data['plugin_url'].'/assets/fc_script.js', array('jquery'), EVOFC()->version, true );				
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts_(){					
			wp_enqueue_script('evo_fc_script');	
		}
		function print_styles(){
			wp_enqueue_style( 'evo_fc_styles');	
		}
		function print_fc_scripts(){	
			if(EVOFC()->load_script) $this->print_scripts_();
		}
	
	// other supported functions		
		public function only_fc_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);		
		}
		public function remove_only_fc_actions(){	
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));			
		}
		// add class name to calendar header
		function eventon_cal_class($name){
			$SC = EVO()->calendar->shortcode_args;

			if(!empty($SC['nexttogrid']) && $SC['nexttogrid']=='yes' && $SC['grid_ux']==0)
				$name[]='evoFC_nextto';

			// if grid UX is lightbox and not showing all events on load
			if( isset($SC['grid_ux']) && $SC['grid_ux'] == '2' && $SC['load_fullmonth']=='no')
				$name[] = 'evofc_nolist';

			$name[]='evoFC';
			return $name;
		}
		// remove class name to calendar header for EM
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoFC', $name)) !== false) unset($name[$key]);
			if(($key = array_search('evoFC_nextto', $name)) !== false) unset($name[$key]);
			return $name;
		}

		// Lightbox calling
		function ligthbox($array){
			$array['evofc_lightbox']= array(
				'id'=>'evofc_lightbox',
				'CLclosebtn'=> 'evolbclose_fc',
				'CLin'=>'evofc_lightbox_body evo_pop_body eventon_events_list evcal_eventcard'
			);return $array;
		}
		function register_widgets() {
			// Include - no need to use autoload as WP loads them anyway
			include_once( EVOFC()->addon_data['plugin_path'].'/includes/class-evo-fc-widget.php' );			
			// Register widgets
			register_widget( 'evoFC_Widget' );
		}
}
