<?php
/**
 * EventON Event lists shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-EL/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_el_shortcode{
	
	function __construct(){
		add_shortcode('add_eventon_el', array($this,'EVOEL_Calendar'));
		add_filter('eventon_shortcode_popup',array($this,'shortcode_additions'), 8, 1);	
	}

	/**	Shortcode processing */	
		function EVOEL_Calendar($atts){			
			if(empty($atts)) $atts = array();
			return EVOEL()->frontend->getCAL($atts);	
		}

	// add new default shortcode arguments
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				//'mobreaks'=>'no',
				'cal_type'=>'',
				'el_type'=>'ue',
				'el_title'=>'',
				'sep_month'=>'yes', 		// separate events by months
				'start_range'=>0,
				'end_range'=>0,
				'event_count_list'=> 'no',
				'month_order'=> 'ASC',
			));			
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/
		function shortcode_additions($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_el',
					'name'=>'Event Lists: Extended',
					'code'=>'add_eventon_el',
					'variables'=>array(
						array(
							'name'=>'Custom Calendar title',
							'type'=>'text',
							'var'=>'el_title',	
						),array(
							'name'=>'Select Event List Type',
							'type'=>'select_step',
							'guide'=>'Type of event list you want to show.',
							'var'=>'el_type',
							'options'=>array(
								'ue'=>'Upcoming Events',
								'pe'=>'Past Events',
								'dr'=>'Date Range'
							)
						)	
							,array('type'=>'open_select_steps','id'=>'ue')
							,array(	'type'=>'close_select_step')
							,array('type'=>'open_select_steps','id'=>'pe')
							,array(	'type'=>'close_select_step')
							,array('type'=>'open_select_steps','id'=>'dr')
								,array(
									'name'=>'Start Date Range',
									'type'=>'text',
									'var'=>'start_range',
									'default'=>'0',
									'guide'=>'Date value MUST be in yyyy/mm/dd format. ALSO supported values: today, rightnow, +/-{x} days, +/-{x} months',
									'placeholder'=>'eg. 2017/12/30'
								),array(
									'name'=>'End Date Range',
									'type'=>'text',
									'var'=>'end_range',
									'default'=>'0',
									'guide'=>'Date value MUST be in yyyy/mm/dd format. ALSO supported values: today, rightnow, +/-{x} days, +/-{x} months',
									'placeholder'=>'eg. 2017/12/30'
								)
							,array(	'type'=>'close_select_step')
							
						,array(
							'name'=>'Event Cut-off',
							'type'=>'select_step',
							'guide'=>'Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am',
							'var'=>'pec',
							'default'=>'Current Time',
							'options'=>array( 
								'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
								'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
								'ft'=>'Fixed Time'
							)
						)
						
							,array('type'=>'open_select_steps','id'=>'ct')
							,array(	'type'=>'close_select_step')
							,array('type'=>'open_select_steps','id'=>'cd')
							,array(	'type'=>'close_select_step')
							,array('type'=>'open_select_steps','id'=>'ft')
								,$evo_shortcode_box->shortcode_default_field('fixed_d_m_y')
								
							,array(	'type'=>'close_select_step')

						,
						$evo_shortcode_box->shortcode_default_field('number_of_months'),	
						$evo_shortcode_box->shortcode_default_field('event_count'),	
						array(
							'name'=>'Apply Event Count to Whole Events List',
							'type'=>'YN',
							'guide'=>'Event Count limit will be applied to the entire events list instead of per each month, by default event count is applied to each month',
							'var'=>'event_count_list',
							'default'=>'no'
						),		

						array(
							'name'=>__('Show load more events button','eventon'),
							'type'=>'YN',
							'guide'=>__('Require "event count limit" to work, then this will add a button to show rest of the events for calendar in increments','eventon'),
							'var'=>'show_limit',
							'default'=>'no',
							'afterstatement'=>'show_limit'
						),
							array(
								'name'=>__('Redirect load more events button','eventon'),
								'type'=>'text',
								'guide'=>__('http:// URL the load more events button will redirect to instead of loading more events on the same calendar.','eventon'),
								'var'=>'show_limit_redir',
								'default'=>'no',
							),
							array('name'=>'Load more events via AJAX only support when "Separate events by month" is disabled AND "Apply Event Count to Whole Events List" is enabled','type'=>'note')
							,array(
								'name'=>__('Load more events via AJAX','eventon'),
								'type'=>'YN',
								'guide'=>__('This will load more events via AJAX as oppose to loading all events onLoad.','eventon'),
								'var'=>'show_limit_ajax',
								'default'=>'no',
							)
							,array(
								'name'=>'Custom Code','type'=>'customcode', 'value'=>'',
								'closestatement'=>'show_limit'
							),
						array(
							'name'=>'Separate events by month',
							'type'=>'YN',
							'guide'=>'This will separate events into months similar to basic event list',
							'var'=>'sep_month',
							'default'=>'no'	
						),					
						$evo_shortcode_box->shortcode_default_field('hide_mult_occur'),				
						$evo_shortcode_box->shortcode_default_field('show_repeats')	,			
						$evo_shortcode_box->shortcode_default_field('hide_empty_months'),				
						$evo_shortcode_box->shortcode_default_field('show_year'),	
						$evo_shortcode_box->shortcode_default_field('event_order'),
						array(
							'name'=>'Month Order for List',
							'type'=>'select',
							'guide'=>'How to order months in the events list where there are more than one month',
							'var'=>'month_order',
							'default'=>'ASC',
							'options'=>array( 
								'ASC'=>'ASC: Nov, Dec',
								'DESC'=>'DESC: Dec, Nov'
							)
						),
						

						array('name'=>'Sorting & Filtering Options','type'=>'collapsable','closed'=>true),
							$evo_shortcode_box->shortcode_default_field('event_past_future'),
							$evo_shortcode_box->shortcode_default_field('hide_past_by'),
							array('name'=>'You can also use NOT-, NOT-all for event type filter values','type'=>'note')	,			
							$evo_shortcode_box->shortcode_default_field('event_type'),
							$evo_shortcode_box->shortcode_default_field('event_type_2'),
							$evo_shortcode_box->shortcode_default_field('event_type_3'),
							$evo_shortcode_box->shortcode_default_field('event_type_4'),
							$evo_shortcode_box->shortcode_default_field('event_type_5')	,						
							$evo_shortcode_box->shortcode_default_field('event_location'),
							$evo_shortcode_box->shortcode_default_field('event_organizer'),
							array('type'=>'close_div'),

						array('name'=>'Other Additional Options','type'=>'subheader'),							
							$evo_shortcode_box->shortcode_default_field('etc_override'),
							$evo_shortcode_box->shortcode_default_field('evc_open'),
							$evo_shortcode_box->shortcode_default_field('hide_sortO'),
							$evo_shortcode_box->shortcode_default_field('expand_sortO'),
							$evo_shortcode_box->shortcode_default_field('ft_event_priority'),
							$evo_shortcode_box->shortcode_default_field('only_ft'),
							$evo_shortcode_box->shortcode_default_field('UIX'),
							$evo_shortcode_box->shortcode_default_field('accord'),
							$evo_shortcode_box->shortcode_default_field('show_et_ft_img'),
							$evo_shortcode_box->shortcode_default_field('hide_end_time'),
						
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}
}
?>