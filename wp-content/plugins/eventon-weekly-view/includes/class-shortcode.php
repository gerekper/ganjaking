<?php
/**
 * EventON WeeklyView Ajax Handlers
 *
 * Handles shortcode functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-WV/shortcode/
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOWV_shortcode{

	static $add_script;

	function __construct(){
		add_shortcode('add_eventon_wv', array($this,'EVOWV_calendar'));
		add_action('ajde_shortcode_box_interpret_fwmy',array($this,'fixed_wmy'), 10,2);		
		add_filter('eventon_shortcode_popup',array($this,'evoWV_add_shortcode_options'), 11, 1);	
	}

	// Shortcode processing
		function EVOWV_calendar($atts){
			if( !is_array($atts)) $atts = array();
			return EVOWV()->frontend->getCAL($atts);					
		}

	// add new default shortcode arguments
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				'fixed_week'=>1,		
				'week_incre'=>0,		
				'always_first_week'=>'no',		
				'hide_events_onload'=>'no',	
				'disable_week_switch'=>'no',	
				'week_style'=>0,	
				'table_style'=>0,//0-def or 1-solid
				'_in_ws'=>0
			));			
		}

		function fixed_wmy($var, $guide){
			$line_class[]='fieldline';
			$line_class[]='fwmy';
			echo 
			"<div class='".implode(' ', $line_class)."'>
				<p class='label'>
					<input class='ajdePOSH_input short shorter' type='text' codevar='fixed_week' placeholder='eg. 3' title='Date'/><input class='ajdePOSH_input short shorter' type='text' codevar='fixed_month' placeholder='eg. 11' title='Month'/><input class='ajdePOSH_input short shorter' type='text' codevar='fixed_year' placeholder='eg. 2014' title='Year'/> ".$var['name']."".$guide."</p>
			</div>";
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/
		function evoWV_add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_WV',
					'name'=>'WeeklyView',
					'code'=>'add_eventon_wv',
					'variables'=>array(
						
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
						$evo_shortcode_box->shortcode_default_field('ft_event_priority'),	
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('event_location'),
						$evo_shortcode_box->shortcode_default_field('event_organizer'),
						array(
							'name'=>__('Fixed Week/Month/Year','eventon'),
							'type'=>'fwmy',
							'guide'=>__('Set fixed month and year value (Both values required)(integer)','eventon'),
							'var'=>'fixed_wmy',
						),
						array(
							'name'=>__('Week Increment','eventon'),
							'type'=>'text',
							'guide'=>'Set + or - week increment from current week of the month to show at first',
							'var'=>'week_incre',
							'default'=>'',
							'placeholder'=>'eg. +1'
						),array(
							'name'=>__('Hide events on first load','eventon'),
							'type'=>'YN',
							'guide'=>'This will hide calendar events until a date on the week strip is clicked by the visitor',
							'var'=>'hide_events_onload',
							'default'=>'no',
						),
						array(
							'name'=>__('Disable Week Switching','eventon'),
							'type'=>'YN',
							'guide'=>'Disable the user ability to switch weeks',
							'var'=>'disable_week_switch',
							'default'=>'no',
						),array(
							'name'=>__('WeekView Layout Style','eventon'),
							'type'=>'select',
							'options'=> array(
								'0'=>'Default',
								'1'=>'Table'
							),
							'guide'=>'Weekly view styles',
							'var'=>'week_style',
							'default'=>'0',
						),array(
							'name'=>__('Table Layout Style','eventon'),
							'type'=>'select',
							'options'=> array(
								'0'=>'Default',
								'1'=>'Solid'
							),
							'guide'=>'Only work with weekly view table style',
							'var'=>'table_style',
							'default'=>'0',
						),				
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('lang'),					
						$evo_shortcode_box->shortcode_default_field('UIX'),					
						$evo_shortcode_box->shortcode_default_field('evc_open'),
						$evo_shortcode_box->shortcode_default_field('etc_override'),
						$evo_shortcode_box->shortcode_default_field('hide_sortO'),
						$evo_shortcode_box->shortcode_default_field('expand_sortO'),
						$evo_shortcode_box->shortcode_default_field('filter_type'),
						$evo_shortcode_box->shortcode_default_field('members_only'),

					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}
}
?>