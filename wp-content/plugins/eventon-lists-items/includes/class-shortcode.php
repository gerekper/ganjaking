<?php
/**
 * Event Lists Items shortcode
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-LI/Functions/shortcode
 * @version     0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class evoli_shortcode{	
	function __construct(){
		add_shortcode('add_eventon_li', array($this,'lists_items'));
		add_filter('eventon_shortcode_popup',array($this,'add_shortcode_options'), 10, 1);
			
	}

	/**	Shortcode processing */	
		function lists_items($atts){
			EVOLI()->load_scripts = true;
			add_filter('eventon_shortcode_defaults',array($this,  'update_shortcode_default'), 10, 1);
			add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);	
			
			if(!is_array($atts)) $atts = array();
			
			// Shortcode processing
			if(!isset($atts['number_of_months'])) $atts['number_of_months'] = 12;

			ob_start();				
			
			echo EVOLI()->frontend->get_list_items($atts);	

			remove_filter('eventon_shortcode_defaults',array($this,  'update_shortcode_default'));	
			remove_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'));		
			return ob_get_clean();					
		}
		function update_shortcode_default($arr){
			return array_merge($arr, array(
				'ux_val'=>3
			));
		}

	// add new default shortcode arguments
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				'cat_type'=>'event_type',
				'li_type'=>'li',
				'li_title'=>'',
				'it_id'=>'',
				'it_stop'=>'no',
				'sep_month'=>'no',
				'it_hide_desc'=>'no',
				'el_type'=>'ue',
				'li_layout'=>'def',
				'show_empty'=>'no',
			));			
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/ 
		function add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_li',
					'name'=>'Event Lists & Items',
					'code'=>'add_eventon_li',
					'variables'=>array(
						array(
							'name'=>'Optional Title Field',
							'type'=>'text',
							'var'=>'li_title'							
						),array(
							'name'=>'Category Type',
							'type'=>'select',
							'var'=>'cat_type',
							'options'=> apply_filters('evoli_shortcodegen_cat_type', array(
								'event_type'=>'Event Type #1',
								'event_type_2'=>'Event Type #2',
								'event_type_3'=>'Event Type #3',
								'event_type_4'=>'Event Type #4',
								'event_type_5'=>'Event Type #5',
								'event_location'=>'Event Locations',
								'event_organizer'=>'Event Organizers',
							))
						),
						array(
							'name'=>'Type of events to show',
							'type'=>'select',
							'var'=>'el_type',
							'options'=>array(
								'ue'=> __('Upcoming Events','evoli'),
								'pe'=> __('Past Events','evoli'),
							)
						),
						array(
							'name'=>'List or Item',
							'type'=>'select',
							'var'=>'li_type',
							'options'=>array(
								'li'=>'List of Categories',
								'it'=>'Single item from category',
							)
						),						
						array(
							'name'=>'Number of Months to Show',
							'type'=>'text',
							'guide'=>'How many months of events to consider when finding events per item',
							'var'=>'number_of_months',
							'placeholder'=>'12',							
						),$this->event_opening_array(),
						array(
							'name'=>'Hide item descriptions (if available)',
							'type'=>'YN','default'=>'no',
							'guide'=>'If the items have descriptions, you can hide those with this shortcode option',
							'var'=>'it_hide_desc',															
						),array(
							'name'=>'Stop showing events upon item click',
							'type'=>'YN','default'=>'no',
							'guide'=>'Setting this to yes will stop showing events when clicked on item box',
							'var'=>'it_stop',															
						),
						array('name'=>'<b>// ONLY Lists Options</b>','type'=>'note')
							,array(
								'name'=>'Separate Events by Month Name (if applicable)',
								'type'=>'YN',
								'var'=>'sep_month',
								'guide'=>'For list of categories, you can use this to separate events by months',
								'default'=>'no',							
							),
							array(
								'name'=>'Show empty terms in the list',
								'type'=>'YN',
								'var'=>'show_empty',
								'guide'=>'This will show terms for category, that do not have events assigned to.',
								'default'=>'no',							
							),
							array(
								'name'=>'List Layout',
								'type'=>'select',
								'var'=>'li_layout',
								'options'=>array(
									'def'=>'Default List',
									'boxes'=>'Boxes',
								)
							),
						array('name'=>'<b>// ONLY Single Item Options</b>','type'=>'note'),
							array(
								'name'=>'Item ID (One one ID)',
								'type'=>'text',
								'var'=>'it_id',
								'placeholder'=>'eg. 23'
							)					
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}
		function event_opening_array(){
			//if( is_plugin_active('eventon-single-events/eventon-single-event.php')){
			return array(
				'name'=>'Open events as',
				'type'=>'select',
				'var'=>'ux_val',
				'default'=>'3',
				'options'=>apply_filters('eventon_uix_shortcode_opts',array(
					'3'=>__('Lightbox popup window','eventon'),
					'X'=>__('Do not interact','eventon'),					
				))
			);			
		}
}
?>