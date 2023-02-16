<?php
/**
 * Shortcode Connections to countdown addon
 * @version  0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class evocd_shortcode{
	function __construct(){
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'add_fields_to_eventon_basic_cal'), 10, 1);
	}

	function calhead_args($array, $arg=''){
		if(!empty($arg['hide_countdown']))
			$array['hide_countdown'] = $arg['hide_countdown'];
		return $array;
	}
	// add new default shortcode arguments
		function add_shortcode_defaults($arr){	
			return array_merge($arr, array(
				'hide_countdown'=>'no',
			));	
		}
	// Add user IDs field to shordcode basic cal version
		function add_fields_to_eventon_basic_cal($array){
			
			$field = array(
					'name'=>'Hide all event countdowns',
					'type'=>'YN',
					'guide'=>'This will hide all countdowns for events in this calendar',
					'var'=>'hide_countdown',
					'default'=>'no'
				);
			$array[] = $field;
			
			return $array; 			
		}

}
new evocd_shortcode();