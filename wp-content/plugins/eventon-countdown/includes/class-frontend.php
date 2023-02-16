<?php
/** 
 * Frontend Class for countdown
 *
 * @author 		AJDE
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evocd_front{
	public $print_scripts_on;
	public $evoOpt;
	public $lang;
	
	function __construct(){

		$this->evoOpt = get_option('evcal_options_evcal_1');
		$this->evoOpt2 = get_option('evcal_options_evcal_2');
		$this->evoOpt_sb = get_option('evcal_options_evcal_sb');

		add_action( 'init', array( $this, 'register_frontend_scripts' ) ,15);
		add_action( 'eventon_enqueue_scripts', array( $this, 'enqueue_script' ) ,15);

		//add_action( 'wp_head', array( $this, 'print_scripts' ) ,15);

		add_filter('evo_eventtop_adds', array($this, 'eventtop_adds'), 10, 1);
		add_filter('eventon_eventtop_evocd', array($this, 'eventtop_content'), 10, 3);

		
	}

	// event top inclusion
		public function eventtop_content($OT, $object,  $EVENT){
			
			// countdown is hidden via shortcode
			if(!empty( EVO()->evo_generator->shortcode_args['hide_countdown']) &&  EVO()->evo_generator->shortcode_args['hide_countdown']=='yes')
				return $OT;

			$output = '';
			
			// countdown is disabled
			if( !$EVENT->check_yn('_evocd_countdown') )	return $OT;

			date_default_timezone_set('UTC');
			$rightnow =current_time('timestamp');
			
			// ending time
				$endat = $EVENT->get_prop('_evocd_countdown_end') == 'end'? 'end':'start';

			$endtime = ($endat=='end')? $EVENT->get_end_time() : $EVENT->get_start_time();
			$repeat_num = 0;

			// Repeating events
			if($EVENT->is_repeating_event()){
				$repeat_intervals = $EVENT->get_repeats();

				$this_ri = $object->ri;
				
				$endtime = ($endat=='end')? 
					(($repeat_intervals && !empty($this_ri))? 
					$repeat_intervals[$this_ri][1]:$endtime):
					(($repeat_intervals && !empty($this_ri))? 
					$repeat_intervals[$this_ri][0]:$endtime);

				$repeat_num = $this_ri;
			}

			// when event expire
			$ex_ux = $EVENT->get_prop_val('_evocd_countdown_ux','0');

			$different = $endtime - $rightnow;

			// If custom offset time is set
				$offset = ($EVENT->get_prop('_evocd_custom_time'))? (int)$EVENT->get_prop('_evocd_custom_time'):0;

				$different = $different - ($offset*60);

			// hook for expiration action
				if($different<0) do_action('ecocd_timer_expired', $ex_ux,$object->evvals );

			// pass time data values to calendar
				$time_json = json_encode(array(
					'yr'=> EVOCD()->lang( 'evocd_001','Yr'),
					'o'=> EVOCD()->lang( 'evocd_002','Mo'),
					'w'=> EVOCD()->lang( 'evocd_003','Wk'),
					'd'=> EVOCD()->lang( 'evocd_004','Dy'),
					'h'=> EVOCD()->lang( 'evocd_005','Hr'),
					'm'=> EVOCD()->lang( 'evocd_006','Mn'),
					's'=> EVOCD()->lang( 'evocd_007','Sc'),
				));

			// text translations
				$_evocd_tx1 = $EVENT->get_prop_val( '_evocd_tx1', evo_lang('This event ends in..') );
				$_evocd_tx2 = $EVENT->get_prop_val('_evocd_tx2', evo_lang('Time has ran out! Better luck next time!') );


			if($different>0){
				$unique_id = 'event_cd_'.$EVENT->ID.'_'.$repeat_num.'_'.(rand(1,10));

				$output .= "<span class='evocd_timer evoet_b1'>";
					$output .= "<span class='evocd_text' data-ex_tx='". $_evocd_tx2 ."'>". $_evocd_tx1 ."</span>";
					$output .= "<span id='".$unique_id."' class='evocd_time' data-et='".$different."' data-ex_ux='{$ex_ux}' data-timetx='".$time_json."'>";
						//$output .= '<span id="noDays" class="countdown is-countdown"><span class="countdown-row countdown-show3">
						//<span class="countdown-section">
						//<span class="countdown-amount">112</span>
						//<span class="countdown-period">Hours</span>
						//</span><span class="countdown-section"><span class="countdown-amount">33</span><span class="countdown-period">Minutes</span></span><span class="countdown-section"><span class="countdown-amount">31</span><span class="countdown-period">Seconds</span></span></span></span>';
					$output .= "</span>";
					
					$output .= "<em class='clear'></em>
				</span>";

			}else{
				$output .= "<span class='evocd_timer evoet_b1'>";
				$output .= "<span class='evocd_text timeexpired' data-ex_tx='". $_evocd_tx2 ."'>". $_evocd_tx2 ."</span></span>";
			}

			return $output;
		}

		// event card inclusion functions		
			function eventtop_adds($array){
				$array['evocd'] = __('Countdown Timer','evocd');
				return $array;
			}

	

	// front end styles and scripts
		function register_frontend_scripts(){
			wp_register_style( 'evocd_styles', EVOCD()->assets_path.'evocd_styles.css');
			wp_register_script( 'evocd_timer_plugin', EVOCD()->assets_path.'jquery.plugin.min.js', array('jquery'));
			wp_register_script( 'evocd_timer', EVOCD()->assets_path.'jquery.countdown.js', array('jquery'));
			wp_register_script( 'evocd_script', EVOCD()->assets_path.'evocd_script.js');
		}
		function print_scripts(){
			//if(!$this->print_scripts_on) return;

			$this->print_front_end_scripts();
		}
		function print_front_end_scripts(){
			wp_enqueue_style('evocd_styles');			
		}
		function enqueue_script(){
			wp_enqueue_style('evocd_styles');			
			wp_enqueue_script('evocd_timer_plugin');
			wp_enqueue_script('evocd_timer');
			wp_enqueue_script('evocd_script');
		}

}