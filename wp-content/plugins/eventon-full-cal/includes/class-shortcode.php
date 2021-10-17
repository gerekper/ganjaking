<?php
/**
 * EventON FullCal shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-FC/Functions/shortcode
 * @version     1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_fc_shortcode{

	static $add_script;

	function __construct(){
		add_shortcode('add_eventon_fc', array($this,'EVOFC_calendar'));	
		add_filter('eventon_shortcode_popup',array($this,'shortcode_structure'), 11, 1);
	}

	// SC
		function EVOFC_calendar($atts){			
			return EVOFC()->frontend->getCAL($atts);
		}

	// add new default shortcode arguments
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				'fixed_day'=>0,
				'day_incre'=>0,
				'hide_sort_options'=>'no',
				'mo1st'=>'',
				'grid_ux'=>0,
				'load_fullmonth'=>'no',
				'heat'=>'no',	// heat graph style grid background colors
				'style'=>'',	// styles for full cal 
				'hover'=>'number',	// information on hover
				'nexttogrid'=>'no', // whether event list show next to month grid
			));			
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/
		function shortcode_structure($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_FC',
					'name'=>'FullCal',
					'code'=>'add_eventon_fc',
					'variables'=>
					apply_filters('evofc_shortcode_array', array(
						$evo_shortcode_box->shortcode_default_field('cal_id'),
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
						$evo_shortcode_box->shortcode_default_field('ft_event_priority')
						,array(
							'name'=>'Month Grid Interaction',
							'type'=>'select',
							'guide'=>'Select the user interaction option when a user click on a date box inside the month grid. "Focus to Events" will scroll page and focus on events list.',
							'var'=>'grid_ux',
							'default'=>'0',
							'options'=>apply_filters('evofc_uix_shortcode_opts', array(
								'0'=>'Default',
								'1'=>'Focus to Events',
								'2'=>'Lightbox Events List'
							))
						),array(
							'name'=>'Show events next to grid*',
							'type'=>'YN',
							'guide'=>'Only works when month grid interaction is set to default. The events list will show as 50% width column on right side of month grid.',
							'var'=>'nexttogrid',
							'default'=>'no'
						),array(
							'name'=>'Show all events of the month below calendar',
							'type'=>'YN',
							'guide'=>'Yes = Show entire month of events on load & when switching months.',
							'var'=>'load_fullmonth',
							'default'=>'no'
						),
						array(
							'name'=>'Day Increment',
							'type'=>'text',
							'placeholder'=>'eg. +1',
							'guide'=>'Change starting date (eg. +1)',
							'var'=>'day_incre',
							'default'=>'0'
						),
						$evo_shortcode_box->shortcode_default_field('month_incre'),
						$evo_shortcode_box->shortcode_default_field('fixed_mo_yr'),
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('event_type_3'),
						$evo_shortcode_box->shortcode_default_field('event_type_4'),
						$evo_shortcode_box->shortcode_default_field('event_type_5'),
						array(
							'name'=>'Fixed Day',
							'type'=>'text',
							'guide'=>'Set fixed day as calendar focused day (integer)',
							'var'=>'fixed_day',
							'default'=>'0',
							'placeholder'=>'eg. 10'
						),
						$evo_shortcode_box->shortcode_default_field('etc_override'),
						array(
							'name'=>__('Open eventCards on load *'),
							'type'=>'YN',
							'guide'=>'Open eventcards when the calendar first load on the page by default. This will override the settings saved for default calendar. This does not apply if grid UX is set to lightbox **',
							'var'=>'evc_open',
							'default'=>'no'
						),
						//$evo_shortcode_box->shortcode_default_field('evc_open'),
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('lang'),
						$evo_shortcode_box->shortcode_default_field('jumper'),
						array(
							'name'=>'Switch to first of month',
							'type'=>'YN',
							'guide'=>'Yes = when switching month focus day will go to 1st of new month',
							'var'=>'mo1st',
							'default'=>'no'
						),array(
							'name'=>'Heat style box coloring',
							'type'=>'YN',
							'guide'=>'Boxes with more events will have darker color than boxes with fewer events.',
							'var'=>'heat',
							'default'=>'no'
						),array(
							'name'=>'Date hover information',
							'type'=>'select',
							'guide'=>'Select what information you want to display on the calendar when hover over calendar dates',
							'var'=>'hover',
							'default'=>'number',
							'options'=>apply_filters('evofc_date_hover', array('number'=>'Number of Events','numname'=>'Events + First 3 Names'))
						),array(
							'name'=>'FullCal Style',
							'type'=>'select',
							'guide'=>'Select different fullcal grid styles from available options.',
							'var'=>'style',
							'default'=>'0',
							'options'=>apply_filters('evofc_grid_styles', 
								array(
									'def'=>'Default',
									'nobox'=>'No Date Outline',
									'names'=>'2 Event Names',
									)
								)
						)
					))

				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}
}
?>