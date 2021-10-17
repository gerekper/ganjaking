<?php
/**
 * AJAX for EventON YV
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOYV_ajax{
	public function __construct(){	

		// pass date range unix to get only events for the selected date
		add_filter('eventon_ajax_arguments',array($this,'main_ajax_arg_adds'), 10, 2);
		add_filter('evo_ajax_query_returns', array($this, 'main_ajax_return'), 10,3);
		add_filter('evo_init_ajax_cals', array($this, 'init_ajax_cals'), 10,1);
	}

	// INIT AJAX
		function init_ajax_cals($CALS){
			foreach($CALS as $calid=>$CD){
				if(!isset($CD['sc'])) continue;
				if( $CD['sc']['calendar_type'] != 'yearly') continue;
			}

			return $CALS;
		}

	// ADD to main ajax return
		function main_ajax_return( $A,  $SC, $events_data){
			if($SC['calendar_type'] == 'yearly'){
				$A['cal_month_title'] = $SC['fixed_year'];					
			}
			return $A;
		}

	// alter calendar SC
	// Before generating events via ajax
		function main_ajax_arg_adds($SC){			
			extract($SC);

			if($SC['calendar_type'] == 'yearly'  ){
				
				// switching month in oneday style
				$direction = $_POST['direction'];

				// if derection present = chaning months
				if($direction != 'none' && !empty($fixed_month) && !empty($fixed_year) ){
					
					$_SC = isset($_POST['shortcode']) ? $_POST['shortcode']: array();

					$SC['fixed_month'] = $_SC['fixed_month'];
					if($direction == 'next'){
						$fixed_year = (int)$_SC['fixed_year'] +1;
					}else{
						$fixed_year = (int)$_SC['fixed_year'] -1;
					}													
				}

				$SC['fixed_year'] = $fixed_year;
				$DD = EVO()->calendar->DD;
				$DD->setDate($fixed_year,1, 1 );
				$DD->setTime(0,0,0);

				$SC['focus_start_date_range'] = $DD->format('U');
				$DD->setDate($fixed_year,12, 31 );
				$DD->setTime(23,59,59);
				$SC['focus_end_date_range'] = $DD->format('U');	
				
			}
			return $SC;
		}
}
new EVOYV_ajax();
?>