<?php
/**
 * EventON YV shortcode
 *
 * Handles all shortcode related functions
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evoyv_shortcode{

	static $add_script;

	function __construct(){
		add_shortcode('add_eventon_yv', array($this,'_calendar'));
		add_filter('eventon_shortcode_popup',array($this,'_add_shortcode_options'), 11, 1);		
	}

	//	Shortcode processing
		function _calendar($atts){	
			return EVOYV()->frontend->getCAL($atts);					
		}
		
	// add new default shortcode arguments
		function add_shortcode_defaults($arr){				
			return array_merge($arr, array(
				'load_as_clicked'=>'no',
				'loading_animation'=>'no',
				'heat_circles'=>'no',
				'hover_style'=>'0',
			));	
		}

	// ADD shortcode buttons to eventON shortcode popup
		function _add_shortcode_options($shortcode_array){
			
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_YV',
					'name'=>'YearlyView',
					'code'=>'add_eventon_yv',
					'variables'=>array(
						$evo_shortcode_box->shortcode_default_field('cal_id'),
						$evo_shortcode_box->shortcode_default_field('fixed_year'),						
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
						$evo_shortcode_box->shortcode_default_field('ft_event_priority'),
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('etc_override'),					
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('lang'),	
						array(
							'name'=>__('Show loading events animation','eventon'),
							'type'=>'YN',
							'guide'=>'This will highlight dates with events in an animation, it may take sometimes if there are events on every date.',
							'var'=>'loading_animation',
							'default'=>'no',
						),array(
							'name'=>__('Heat style date circle coloring','eventon'),
							'type'=>'YN',
							'guide'=>'Date circles with more events will have a darker color and lighter color for less events.',
							'var'=>'heat_circles',
							'default'=>'no',
						),
						array(
							'name'=>__('Date Circle Hover Style','eventon'),
							'type'=>'select',
							'options'=> array(
								'0'=>'Nothing',
								'1'=>'Number of Events',
								'2'=>'Number + First 3 Events',
								'3'=>'First 3 Events',
							),
							'guide'=>'Various styles in which the event information is shown when hovered over a date circle',
							'var'=>'hover_style',
							'default'=>'0',
						),
					)
					
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}	
}
?>