<?php
/**
 * EventON WeeklyView Ajax Handlers
 *
 * Handles ajax functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-WV/ajax/
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class EVOWV_ajax{
	public function __construct(){
		add_filter('evo_init_ajax_cals', array($this, 'init_ajax_cals'), 10,1);
		add_filter('eventon_ajax_arguments',array($this,'main_ajax_arg_adds'), 10, 3);
		add_filter('evo_ajax_query_returns', array($this, 'main_ajax_return'), 10,3);
	}

	// INIT AJAX
		function init_ajax_cals($CALS){
			foreach($CALS as $calid=>$CD){
				if(!isset($CD['sc'])) continue;
				if( $CD['sc']['calendar_type'] != 'weekly') continue;
			}
			return $CALS;
		}

	// MAIN AJAX CALL before get events
		function main_ajax_arg_adds($SC, $POST, $ajaxtype){			
			if($SC['calendar_type'] == 'weekly' && $ajaxtype == 'wv_newweek'){
				$SC = EVOWV()->frontend->set_week_unix_date_range($SC);	
			}
			return $SC;
		}
	// main calendar ajax return
		function main_ajax_return( $A,  $SC, $events_data){
			if($SC['calendar_type'] == 'weekly' && isset($SC['wv_days']) && $SC['wv_days'] == 'yes'){
				
			}
			return $A;
		}

}
new EVOWV_ajax();

?>