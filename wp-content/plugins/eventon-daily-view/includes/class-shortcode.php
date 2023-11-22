<?php
/**
 * EventON dailyView shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	dailyView/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evo_dv_shortcode{

	static $add_script;

	function __construct(){
		add_shortcode('add_eventon_dv', array($this,'EVODV_calendar'));
		add_filter('eventon_shortcode_popup',array($this,'evoDV_add_shortcode_options'), 11, 1);		
	}

	//	Shortcode processing
		function EVODV_calendar($atts){
			if( !is_array($atts)) $atts = array();	
			return EVODV()->frontend->getCAL($atts);					
		}
		
	// add new default shortcode arguments
			function add_shortcode_defaults($arr){				
				return array_merge($arr, array(
					'fixed_day'=>0,
					'day_incre'=>0,
					'hide_sort_options'=>'no',
					'mo1st'=>'',
					'header_title'=>'',
					'dv_view_style'=>'def',
					'dv_scroll_type'=>'',
					'dv_scroll_style'=>'def',
				));	
			}

	// ADD shortcode buttons to eventON shortcode popup
		function evoDV_add_shortcode_options($shortcode_array){
			
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_DV',
					'name'=>'DailyView',
					'code'=>'add_eventon_dv',
					'variables'=>array(
						$evo_shortcode_box->shortcode_default_field('cal_id'),
						array(
							'name'=>'View Style',
							'type'=>'select',
							'options'=>array(
								'def'=>'Month strip + Day box',
								'defless'=>'Month strip only',
								'oneday'=>'One day events',
								'onedayplus'=>'One day events + Day box',
							),
							'var'=>'dv_view_style',
							'default'=>'def'
						),
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
						$evo_shortcode_box->shortcode_default_field('ft_event_priority'),
						array(
							'name'=>'Day Increment',
							'type'=>'text',
							'placeholder'=>'eg. +1',
							'guide'=>'Change starting date (eg. +1)',
							'var'=>'day_incre',
							'default'=>'0'
						),$evo_shortcode_box->shortcode_default_field('month_incre'),
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						array(
							'name'=>'Fixed Day',
							'type'=>'text',
							'guide'=>'Set fixed day as calendar focused day (integer)',
							'var'=>'fixed_day',
							'guide'=>'Both fixed month and year should be set for this to work',
							'default'=>'0',
							'placeholder'=>'eg. 10'
						),
						$evo_shortcode_box->shortcode_default_field('fixed_month'),
						$evo_shortcode_box->shortcode_default_field('fixed_year'),
						$evo_shortcode_box->shortcode_default_field('etc_override'),
						$evo_shortcode_box->shortcode_default_field('evc_open'),						
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('lang'),						
						$evo_shortcode_box->shortcode_default_field('jumper'),
						array(
							'name'=>'Day scrolling style at start & end of month',
							'type'=>'select',
							'options'=>array(
								'def'=>'Default, using previous methods',
								'continuous'=>'Continuous scrolling',
								'firstday'=>'Always go to 1st of month',
								'lastday'=>'Always go to last day of month',
							),
							'var'=>'dv_scroll_style',
							'default'=>'def',
							'guide'=>'Define how to scroll to next or previous date at the end and start of the month. Continuous scrolling will go from 31 > 1 and 1 to 31. This will override Switch to first of month value.',
						),
						array(
							'name'=>'Switch to first of month (Deprecating)',
							'type'=>'YN',
							'guide'=>'Yes = when switching month focus day will go to 1st of new month. This is deprecating. Please use Day scrolling type instead.',
							'var'=>'mo1st',
							'default'=>'no'
						)
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}	
}
?>