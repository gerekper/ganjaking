<?php
/**
 * EventON eventMap shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	eventMap/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_em_shortcode{

	static $add_script;

	function __construct(){
		add_shortcode('add_eventon_evmap', array($this,'generate_calendar'));
		add_filter('eventon_shortcode_popup',array($this,'shortcode_optinos'), 10, 1);
	}

	// generate calendar
		function generate_calendar($atts){

			if(!is_array($atts)) $atts = array();		
			EVOEM()->is_running_em = true;	
			EVOEM()->load_script = true;		
			
			// add additional default values to accepted shortcode variables
			add_filter('eventon_shortcode_defaults', array($this,'add_shortcode_defaults'), 10, 1);
			
			$atts['calendar_type'] = 'map';		

			ob_start();	

			EVOEM()->frontend->atts = $atts;
			echo EVOEM()->frontend->generate_evo_em($atts);
				
			// pipe down
			EVOEM()->is_running_em = false;	
			remove_filter('eventon_shortcode_defaults', array($this,'add_shortcode_defaults'));

			return ob_get_clean();
					
		}
	// add new default shortcode arguments
		function add_shortcode_defaults($arr){
			
			return array_merge($arr, array(
				'map_height'=>400,
				'show_alle'=>'no',
				'lightbox'=>'no',
				'loc_page'=>'no',
				'focusmap'=>'no',
				'map_type'=>'monthly',
				'map_title'=>''
			));			
		}

	//	ADD shortcode buttons to eventON shortcode popup
		function shortcode_optinos($shortcode_array){
			
			global $evo_shortcode_box;
			$eventloclink = get_admin_url().'edit-tags.php?taxonomy=event_location&post_type=ajde_events';
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_EM',
					'name'=>'Event Map',
					'code'=>'add_eventon_evmap',
					'variables'=>array(
						array(
							'name'=>'<i>NOTE: EventMap will only generate markers for event locations that have latitude and longitude values saved already. If a location is missing those, check if it has correct latlng saved in <a href="'.$eventloclink.'">event locations</a>.</i>',
							'placeholder'=>'eg. 400',
							'type'=>'note',
							'guide'=>'Height of the google map box in pixels',
						),
						$evo_shortcode_box->shortcode_default_field('show_et_ft_img')
						,array(
							'name'=>'Event Map Type',
							'type'=>'select',
							'options'=>array(
								'monthly'=>'Month Navigational Map',
								'upcoming'=>'All Upcoming Events'
							),
							'var'=>'map_type',
							'default'=>'monthly'
						)						
						,$evo_shortcode_box->shortcode_default_field('fixed_month')
						,$evo_shortcode_box->shortcode_default_field('fixed_year')
						,array(
							'name'=>'Number of months *',
							'placeholder'=>'eg. 12',
							'type'=>'text',
							'guide'=>'ONLY for All upcoming events map',
							'var'=>'number_of_months',
							'default'=>'12'
						),array(
							'name'=>'All Events Map Title *',
							'type'=>'text',
							'guide'=>'ONLY for All upcoming events map',
							'var'=>'map_title',
						)
						,$evo_shortcode_box->shortcode_default_field('hide_past')
						,$evo_shortcode_box->shortcode_default_field('hide_sortO')
						,array(
							'name'=>'Google Map Height (px)',
							'placeholder'=>'eg. 400',
							'type'=>'text',
							'guide'=>'Height of the google map box in pixels',
							'var'=>'map_height',
							'default'=>'400'
						),array(
							'name'=>'Show all events on load',
							'type'=>'YN',
							'guide'=>'This will show all events without having to click on markers in the map',
							'var'=>'show_alle',
							'default'=>'no'
						),array(
							'name'=>'Add link to location page on info window',
							'type'=>'YN',
							'guide'=>'Add a link in the marker info window to location page',
							'var'=>'loc_page',
							'default'=>'no'
						),array(
							'name'=>'Display events as lightbox',
							'type'=>'YN',
							'guide'=>'Clicking on the marker to load events as lightbox',
							'var'=>'lightbox',
							'default'=>'no'
						),$evo_shortcode_box->shortcode_default_field('UIX')
						,array(
							'name'=>'Show "All Map" button on calendar header',
							'type'=>'YN',
							'guide'=>'This button will allow users to go back to view all events on the map once they have zoomed in on an event.',
							'var'=>'focusmap',
							'default'=>'no'
						)
						,$evo_shortcode_box->shortcode_default_field('event_count')
						,$evo_shortcode_box->shortcode_default_field('month_incre')
						,$evo_shortcode_box->shortcode_default_field('event_type')
						,$evo_shortcode_box->shortcode_default_field('event_type_2')
						,$evo_shortcode_box->shortcode_default_field('lang')
						,
					)
				)
			);
			return array_merge($shortcode_array, $new_shortcode_array);
		}
}
?>