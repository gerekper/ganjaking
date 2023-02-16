<?php
/**
 * Shortcode Connections to Subscriber addon
 * @version  0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class evosub_shortcode{
	function __construct(){
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'add_fields_to_eventon_basic_cal'), 10, 1);
	}

	function calhead_args($array, $arg=''){
		if(!empty($arg['subscriber']))
			$array['subscriber'] = $arg['subscriber'];
		return $array;
	}
	// add new default shortcode arguments
		function add_shortcode_defaults($arr){	
			return array_merge($arr, array(
				'subscriber'=>'no',
			));	
		}
	// Add user IDs field to shordcode basic cal version
		function add_fields_to_eventon_basic_cal($array){
			
			$field = array(
					'name'=>'Show Subscriber button',
					'type'=>'YN',
					'guide'=>'This will add Subscriber button to bottom of your calendar',
					'var'=>'subscriber',
					'default'=>'no'
				);
			$array[] = $field;
			
			return $array; 			
		}

}
new evosub_shortcode();