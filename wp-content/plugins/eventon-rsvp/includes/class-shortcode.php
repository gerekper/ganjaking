<?php
/**
 * RSVP Events shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-RS/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_rs_shortcode{

	static $add_script;

	function __construct(){		
		add_filter('eventon_shortcode_defaults', array($this,'evoRS_add_shortcode_defaults'), 10, 1);
		add_shortcode('evo_rsvp_manager',array($this, 'user_rsvp_manager'));
		add_filter('eventon_shortcode_popup',array($this, 'evors_add_shortcode_options'), 10, 1);
	}

	// add new default shortcode arguments
	function evoRS_add_shortcode_defaults($arr){		
		return array_merge($arr, array(
			'show_rsvp'=>'no',
		));		
	}

	// Event Manger for frontend
	function user_rsvp_manager($atts){
		global $eventon_rs;
		ob_start();		

		$manager = new evors_event_manager();
		echo $manager->user_rsvp_manager($atts);

		return ob_get_clean();
	}
	function evors_add_shortcode_options($shortcode_array){
		global $evo_shortcode_box;
		
		$new_shortcode_array = array(
			array(
				'id'=>'s_rs',
				'name'=>'RSVP - User RSVP Manager',
				'code'=>'evo_rsvp_manager',
				'variables'=>''
			)
		);
		return array_merge($shortcode_array, $new_shortcode_array);
	}
}
?>