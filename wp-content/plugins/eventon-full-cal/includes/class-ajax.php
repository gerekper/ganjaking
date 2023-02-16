<?php
/**
 * EventON FullCal Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 * @author 		AJDE
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOFC_Ajax{
	function __construct(){
		add_filter('eventon_ajax_arguments',array($this,'main_ajax_arg_adds'), 10, 2);
	}	

	// alter calendar SC
	// Before generating events via ajax
		function main_ajax_arg_adds($SC){			
			extract($SC);

			if($SC['calendar_type'] != 'fullcal') return $SC;

			if(!empty($mo1st) && $mo1st == 'yes')	$SC['fixed_day'] = 1;

			return $SC;
		}
}

new EVOFC_Ajax();
	
?>