<?php
/** 
 * Shortcodes
 */

class EVOAD_Shortcode{
	function __construct(){
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'shortcode_fields'), 10, 1);

		add_filter('eventon_wp_query_args', array($this, 'wp_query_meta_additions'), 10,3);
		add_filter('evofc_shortcode_array', array($this, 'shortcode_fields_fc'), 10,3);
	}

// Inclusions
	function calhead_args($array, $arg=''){
		if(!empty($arg['advent_events']))	$array['advent_events'] = $arg['advent_events'];
		return $array;
	}
	function add_shortcode_defaults($arr){
		return array_merge($arr, array(
			'advent_events'=>'no',
		));	
	}
	function shortcode_fields($array){

		$array[] = array(
			'name'=>'Show only Advent Events',
			'type'=>'YN',
			'guide'=>'This will show only advent events',
			'var'=>'advent_events',
			'default'=>'no'
		);
		return $array; 			
	}

	// fullCal integration
	public function shortcode_fields_fc($A){
		$A[] = array(
			'name'=>'Show only Advent Events',
			'type'=>'YN',
			'guide'=>'This will show only advent events',
			'var'=>'advent_events',
			'default'=>'no'
		);
		return $A;
	}

// query
	function wp_query_meta_additions($wp_arguments, $filters, $ecv){

		if(isset($ecv['advent_events']) && $ecv['advent_events'] != 'yes') return $wp_arguments;

		$wp_arguments['meta_query'] = array(
			array(
				'key'=>'_evo_advent_event',
				'value'=>'yes'
			)
		);

		return $wp_arguments;
	}

}

new EVOAD_Shortcode();